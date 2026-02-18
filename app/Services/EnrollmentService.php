<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Student;
use App\Models\Timetable;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EnrollmentService
{
    /**
     * Request enrollment in a course. Returns a flash payload.
     *
     * @return array{level: string, message: string}
     */
    public function requestEnrollment(Student $student, Course $course): array
    {
        return DB::transaction(function () use ($student, $course): array {
            $reapplyAfterRejection = false;

            $existingEnrollment = DB::table('course_student')
                ->where('student_id', $student->id)
                ->where('course_id', $course->id)
                ->lockForUpdate()
                ->first();

            if ($existingEnrollment) {
                $status = $existingEnrollment->status;
                if ($status === 'approved') {
                    Log::info('enrollment.request_blocked', [
                        'student_id' => $student->id,
                        'course_id' => $course->id,
                        'reason' => 'already_approved',
                    ]);

                    return $this->result('error', 'You are already enrolled in this course.');
                }

                if ($status === 'pending') {
                    Log::info('enrollment.request_blocked', [
                        'student_id' => $student->id,
                        'course_id' => $course->id,
                        'reason' => 'already_pending',
                    ]);

                    return $this->result('error', 'You already have a pending enrollment request for this course.');
                }

                if ($status === 'withdrawal_pending') {
                    Log::info('enrollment.request_blocked', [
                        'student_id' => $student->id,
                        'course_id' => $course->id,
                        'reason' => 'withdrawal_pending',
                    ]);

                    return $this->result(
                        'error',
                        "You have a pending withdrawal request for {$course->course_code} - {$course->title}. Please wait for admin approval before registering again."
                    );
                }

                if ($status === 'rejected') {
                    $reapplyAfterRejection = true;
                }
            }

            $approvedCourseIds = DB::table('course_student')
                ->where('student_id', $student->id)
                ->where('status', 'approved')
                ->lockForUpdate()
                ->pluck('course_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $conflict = $this->findScheduleConflict($course, $approvedCourseIds);
            if ($conflict !== null) {
                Log::info('enrollment.request_blocked', [
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'reason' => 'schedule_conflict',
                    'conflict_course_id' => $conflict['conflict_course_id'],
                ]);

                return $this->result(
                    'error',
                    $this->scheduleConflictMessage($conflict)
                );
            }

            if ($reapplyAfterRejection) {
                DB::table('course_student')
                    ->where('student_id', $student->id)
                    ->where('course_id', $course->id)
                    ->update(['status' => 'pending', 'updated_at' => now()]);

                Log::info('enrollment.request_submitted', [
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'mode' => 'resubmission_after_rejection',
                ]);

                return $this->result(
                    'success',
                    "Enrollment request submitted for {$course->course_code} - {$course->title}. Waiting for admin approval."
                );
            }

            try {
                DB::table('course_student')->insert([
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (QueryException $e) {
                if ((string) $e->getCode() === '23000') {
                    $raceEnrollment = DB::table('course_student')
                        ->where('student_id', $student->id)
                        ->where('course_id', $course->id)
                        ->first();

                    if ($raceEnrollment) {
                        if ($raceEnrollment->status === 'approved') {
                            return $this->result('error', 'You are already enrolled in this course.');
                        }
                        if ($raceEnrollment->status === 'pending') {
                            return $this->result('error', 'You already have a pending enrollment request for this course.');
                        }
                        if ($raceEnrollment->status === 'withdrawal_pending') {
                            return $this->result(
                                'error',
                                "You have a pending withdrawal request for {$course->course_code} - {$course->title}. Please wait for admin approval before registering again."
                            );
                        }
                    }

                    return $this->result(
                        'error',
                        'An error occurred while processing your enrollment request. Please try again.'
                    );
                }

                throw $e;
            }

            Log::info('enrollment.request_submitted', [
                'student_id' => $student->id,
                'course_id' => $course->id,
                'mode' => 'new_request',
            ]);

            return $this->result(
                'success',
                "Enrollment request submitted for {$course->course_code} - {$course->title}. Waiting for admin approval."
            );
        });
    }

    /**
     * Request course withdrawal. Returns a flash payload.
     *
     * @return array{level: string, message: string}
     */
    public function requestWithdrawal(Student $student, Course $course): array
    {
        $enrollment = $student->courses()
            ->where('course_id', $course->id)
            ->first();

        if (! $enrollment) {
            return $this->result('error', 'You are not enrolled in this course.');
        }

        $status = $enrollment->pivot->status;
        if ($status !== 'approved') {
            if ($status === 'withdrawal_pending') {
                return $this->result('error', 'You already have a pending withdrawal request for this course.');
            }

            return $this->result('error', 'You are not enrolled in this course.');
        }

        $student->courses()->updateExistingPivot($course->id, ['status' => 'withdrawal_pending']);

        Log::info('enrollment.withdrawal_requested', [
            'student_id' => $student->id,
            'course_id' => $course->id,
        ]);

        return $this->result(
            'success',
            "Withdrawal request submitted for {$course->course_code} - {$course->title}. Waiting for admin approval."
        );
    }

    /**
     * Approve a pending enrollment request.
     *
     * @param  int|string  $enrollmentId
     * @return array{level: string, message: string}
     */
    public function approveEnrollment(int|string $enrollmentId): array
    {
        return DB::transaction(function () use ($enrollmentId): array {
            $enrollment = DB::table('course_student')
                ->where('id', $enrollmentId)
                ->lockForUpdate()
                ->first();

            if (! $enrollment || $enrollment->status !== 'pending') {
                return $this->result('error', 'Enrollment not found or already processed.');
            }

            // Lock all enrollment rows for this student so concurrent approvals
            // for the same student are serialized.
            $studentEnrollments = DB::table('course_student')
                ->where('student_id', $enrollment->student_id)
                ->lockForUpdate()
                ->get(['id', 'course_id', 'status']);

            $approvedCourseIds = $studentEnrollments
                ->where('status', 'approved')
                ->pluck('course_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $course = Course::find($enrollment->course_id);
            $student = Student::find($enrollment->student_id);

            if ($course !== null) {
                $conflict = $this->findScheduleConflict($course, $approvedCourseIds);
                if ($conflict !== null) {
                    Log::info('enrollment.review_blocked', [
                        'enrollment_id' => $enrollmentId,
                        'student_id' => $enrollment->student_id,
                        'course_id' => $enrollment->course_id,
                        'reason' => 'schedule_conflict',
                        'conflict_course_id' => $conflict['conflict_course_id'],
                    ]);

                    return $this->result(
                        'error',
                        "Cannot approve enrollment for {$this->studentLabel($student)} in {$this->courseLabel($course)}. ".$this->scheduleConflictMessage($conflict)
                    );
                }
            }

            DB::table('course_student')
                ->where('id', $enrollmentId)
                ->update([
                    'status' => 'approved',
                    'updated_at' => now(),
                ]);

            Log::info('enrollment.review_decision', [
                'enrollment_id' => $enrollmentId,
                'student_id' => $enrollment->student_id,
                'course_id' => $enrollment->course_id,
                'decision' => 'approved',
            ]);

            return $this->result(
                'success',
                "Enrollment approved for {$this->studentLabel($student)} in {$this->courseLabel($course)}."
            );
        });
    }

    /**
     * Reject a pending enrollment request.
     *
     * @param  int|string  $enrollmentId
     * @return array{level: string, message: string}
     */
    public function rejectEnrollment(int|string $enrollmentId): array
    {
        return DB::transaction(function () use ($enrollmentId): array {
            $enrollment = DB::table('course_student')
                ->where('id', $enrollmentId)
                ->lockForUpdate()
                ->first();

            if (! $enrollment || $enrollment->status !== 'pending') {
                return $this->result('error', 'Enrollment not found or already processed.');
            }

            DB::table('course_student')
                ->where('id', $enrollmentId)
                ->update([
                    'status' => 'rejected',
                    'updated_at' => now(),
                ]);

            $course = Course::find($enrollment->course_id);
            $student = Student::find($enrollment->student_id);

            Log::info('enrollment.review_decision', [
                'enrollment_id' => $enrollmentId,
                'student_id' => $enrollment->student_id,
                'course_id' => $enrollment->course_id,
                'decision' => 'rejected',
            ]);

            return $this->result(
                'success',
                "Enrollment rejected for {$this->studentLabel($student)} in {$this->courseLabel($course)}."
            );
        });
    }

    /**
     * Approve a pending withdrawal request by deleting the enrollment row.
     *
     * @param  int|string  $enrollmentId
     * @return array{level: string, message: string}
     */
    public function approveWithdrawal(int|string $enrollmentId): array
    {
        return DB::transaction(function () use ($enrollmentId): array {
            $enrollment = DB::table('course_student')
                ->where('id', $enrollmentId)
                ->lockForUpdate()
                ->first();

            if (! $enrollment || $enrollment->status !== 'withdrawal_pending') {
                return $this->result('error', 'Withdrawal request not found or already processed.');
            }

            DB::table('course_student')
                ->where('id', $enrollmentId)
                ->delete();

            $course = Course::find($enrollment->course_id);
            $student = Student::find($enrollment->student_id);

            Log::info('enrollment.withdrawal_decision', [
                'enrollment_id' => $enrollmentId,
                'student_id' => $enrollment->student_id,
                'course_id' => $enrollment->course_id,
                'decision' => 'approved',
            ]);

            return $this->result(
                'success',
                "Withdrawal approved for {$this->studentLabel($student)} from {$this->courseLabel($course)}."
            );
        });
    }

    /**
     * Reject a pending withdrawal request by restoring approved status.
     *
     * @param  int|string  $enrollmentId
     * @return array{level: string, message: string}
     */
    public function rejectWithdrawal(int|string $enrollmentId): array
    {
        return DB::transaction(function () use ($enrollmentId): array {
            $enrollment = DB::table('course_student')
                ->where('id', $enrollmentId)
                ->lockForUpdate()
                ->first();

            if (! $enrollment || $enrollment->status !== 'withdrawal_pending') {
                return $this->result('error', 'Withdrawal request not found or already processed.');
            }

            DB::table('course_student')
                ->where('id', $enrollmentId)
                ->update([
                    'status' => 'approved',
                    'updated_at' => now(),
                ]);

            $course = Course::find($enrollment->course_id);
            $student = Student::find($enrollment->student_id);

            Log::info('enrollment.withdrawal_decision', [
                'enrollment_id' => $enrollmentId,
                'student_id' => $enrollment->student_id,
                'course_id' => $enrollment->course_id,
                'decision' => 'rejected',
            ]);

            return $this->result(
                'success',
                "Withdrawal rejected for {$this->studentLabel($student)} from {$this->courseLabel($course)}. Student remains enrolled."
            );
        });
    }

    /**
     * @return array{level: string, message: string}
     */
    private function result(string $level, string $message): array
    {
        return [
            'level' => $level,
            'message' => $message,
        ];
    }

    private function studentLabel(?Student $student): string
    {
        return $student?->full_name ?? 'student';
    }

    private function courseLabel(?Course $course): string
    {
        if (! $course) {
            return 'the course';
        }

        return "{$course->course_code} - {$course->title}";
    }

    /**
     * @param  array<int, int>  $approvedCourseIds
     * @return array<string, mixed>|null
     */
    private function findScheduleConflict(Course $course, array $approvedCourseIds): ?array
    {
        if ($approvedCourseIds === []) {
            return null;
        }

        $newCourseTimetables = Timetable::query()
            ->whereHas('subject', fn ($q) => $q->where('course_id', $course->id))
            ->get(['subject_id', 'day_of_week', 'start_time', 'end_time']);

        if ($newCourseTimetables->isEmpty()) {
            return null;
        }

        $existingTimetables = Timetable::query()
            ->with('subject.course:id,course_code,title')
            ->whereHas('subject', fn ($q) => $q->whereIn('course_id', $approvedCourseIds))
            ->get(['subject_id', 'day_of_week', 'start_time', 'end_time']);

        foreach ($newCourseTimetables as $newTimetable) {
            foreach ($existingTimetables as $existingTimetable) {
                if ($newTimetable->day_of_week !== $existingTimetable->day_of_week) {
                    continue;
                }

                $newStart = strtotime((string) $newTimetable->start_time);
                $newEnd = strtotime((string) $newTimetable->end_time);
                $existingStart = strtotime((string) $existingTimetable->start_time);
                $existingEnd = strtotime((string) $existingTimetable->end_time);

                if (
                    $newStart === false ||
                    $newEnd === false ||
                    $existingStart === false ||
                    $existingEnd === false
                ) {
                    continue;
                }

                if ($newStart < $existingEnd && $newEnd > $existingStart) {
                    $conflictingCourse = $existingTimetable->subject?->course;

                    return [
                        'course_code' => $conflictingCourse?->course_code ?? 'UNKNOWN',
                        'course_title' => $conflictingCourse?->title ?? 'Unknown Course',
                        'conflict_course_id' => $conflictingCourse?->id,
                        'day_of_week' => (string) $newTimetable->day_of_week,
                        'start_time' => (string) $newTimetable->start_time,
                        'end_time' => (string) $newTimetable->end_time,
                    ];
                }
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $conflict
     */
    private function scheduleConflictMessage(array $conflict): string
    {
        return sprintf(
            'Schedule conflict detected with %s - %s on %s (%s - %s). Please choose a different course or contact administration.',
            (string) $conflict['course_code'],
            (string) $conflict['course_title'],
            (string) $conflict['day_of_week'],
            (string) $conflict['start_time'],
            (string) $conflict['end_time']
        );
    }
}
