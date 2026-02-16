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
            }

            $enrolledCourseIds = $student->courses()
                ->wherePivot('status', 'approved')
                ->pluck('courses.id')
                ->toArray();

            if (! empty($enrolledCourseIds)) {
                $newCourseTimetables = Timetable::with('subject.course')
                    ->whereHas('subject', fn ($q) => $q->where('course_id', $course->id))
                    ->get();

                $existingTimetables = Timetable::with('subject.course')
                    ->whereHas('subject', fn ($q) => $q->whereIn('course_id', $enrolledCourseIds))
                    ->get();

                foreach ($newCourseTimetables as $newTimetable) {
                    foreach ($existingTimetables as $existingTimetable) {
                        if ($newTimetable->day_of_week !== $existingTimetable->day_of_week) {
                            continue;
                        }

                        $newStart = strtotime($newTimetable->start_time);
                        $newEnd = strtotime($newTimetable->end_time);
                        $existingStart = strtotime($existingTimetable->start_time);
                        $existingEnd = strtotime($existingTimetable->end_time);

                        if ($newStart < $existingEnd && $newEnd > $existingStart) {
                            $conflictingCourse = $existingTimetable->subject->course;

                            Log::info('enrollment.request_blocked', [
                                'student_id' => $student->id,
                                'course_id' => $course->id,
                                'reason' => 'schedule_conflict',
                                'conflict_course_id' => $conflictingCourse?->id,
                            ]);

                            return $this->result(
                                'error',
                                "Schedule conflict detected! This course conflicts with {$conflictingCourse->course_code} on {$newTimetable->day_of_week} ({$newTimetable->start_time} - {$newTimetable->end_time}). Please choose a different course or contact administration."
                            );
                        }
                    }
                }
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

            DB::table('course_student')
                ->where('id', $enrollmentId)
                ->update([
                    'status' => 'approved',
                    'updated_at' => now(),
                ]);

            $course = Course::find($enrollment->course_id);
            $student = Student::find($enrollment->student_id);

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
}
