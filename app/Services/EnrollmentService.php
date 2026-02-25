<?php

namespace App\Services;

use App\Models\Course;
use App\Models\EnrollmentStatusLog;
use App\Models\Student;
use App\Models\Timetable;
use App\Notifications\EnrollmentStatusUpdated;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

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
            $existingEnrollmentId = null;
            $existingStatus = null;
            $performedBy = (int) ($student->user_id ?? 0) ?: null;

            $existingEnrollment = DB::table('course_student')
                ->where('student_id', $student->id)
                ->where('course_id', $course->id)
                ->lockForUpdate()
                ->first();

            if ($existingEnrollment) {
                $status = $existingEnrollment->status;
                $existingEnrollmentId = (int) ($existingEnrollment->id ?? 0) ?: null;
                $existingStatus = is_string($status) ? $status : null;
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
                $reason = $this->scheduleConflictMessage($conflict);
                Log::info('enrollment.request_blocked', [
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'reason' => 'schedule_conflict',
                    'conflict_course_id' => $conflict['conflict_course_id'],
                ]);

                $this->logEnrollmentStatus([
                    'enrollment_id' => $existingEnrollmentId,
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'from_status' => $existingStatus,
                    'to_status' => $existingStatus,
                    'action' => 'request_blocked_conflict',
                    'reason' => $reason,
                    'performed_by' => $performedBy,
                    'meta' => [
                        'conflict_course_id' => $conflict['conflict_course_id'] ?? null,
                        'conflict_subject_code' => $conflict['conflict_subject_code'] ?? null,
                        'conflict_day_of_week' => $conflict['day_of_week'] ?? null,
                    ],
                ]);

                return $this->result(
                    'error',
                    $reason
                );
            }

            if ($reapplyAfterRejection) {
                $now = now();
                DB::table('course_student')
                    ->where('student_id', $student->id)
                    ->where('course_id', $course->id)
                    ->update(['status' => 'pending', 'updated_at' => $now]);

                Log::info('enrollment.request_submitted', [
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'mode' => 'resubmission_after_rejection',
                ]);

                $this->logEnrollmentStatus([
                    'enrollment_id' => $existingEnrollmentId,
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'from_status' => 'rejected',
                    'to_status' => 'pending',
                    'action' => 'request_resubmitted',
                    'reason' => 'Student resubmitted previously rejected enrollment.',
                    'performed_by' => $performedBy,
                ]);

                return $this->result(
                    'success',
                    "Enrollment request submitted for {$course->course_code} - {$course->title}. Waiting for admin approval."
                );
            }

            try {
                $enrollmentId = DB::table('course_student')->insertGetId([
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->logEnrollmentStatus([
                    'enrollment_id' => (int) $enrollmentId,
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'from_status' => null,
                    'to_status' => 'pending',
                    'action' => 'request_submitted',
                    'reason' => 'Student submitted enrollment request.',
                    'performed_by' => $performedBy,
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
        $enrollmentRow = DB::table('course_student')
            ->where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->first(['id', 'status']);

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

        $this->logEnrollmentStatus([
            'enrollment_id' => (int) ($enrollmentRow?->id ?? 0) ?: null,
            'student_id' => $student->id,
            'course_id' => $course->id,
            'from_status' => 'approved',
            'to_status' => 'withdrawal_pending',
            'action' => 'withdrawal_requested',
            'reason' => 'Student requested withdrawal.',
            'performed_by' => (int) ($student->user_id ?? 0) ?: null,
        ]);

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
            $performedBy = Auth::id();
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
                    $reason = $this->scheduleConflictMessage($conflict);
                    Log::info('enrollment.review_blocked', [
                        'enrollment_id' => $enrollmentId,
                        'student_id' => $enrollment->student_id,
                        'course_id' => $enrollment->course_id,
                        'reason' => 'schedule_conflict',
                        'conflict_course_id' => $conflict['conflict_course_id'],
                    ]);

                    $this->logEnrollmentStatus([
                        'enrollment_id' => (int) $enrollmentId,
                        'student_id' => (int) $enrollment->student_id,
                        'course_id' => (int) $enrollment->course_id,
                        'from_status' => 'pending',
                        'to_status' => 'pending',
                        'action' => 'review_blocked_conflict',
                        'reason' => $reason,
                        'performed_by' => $performedBy,
                        'meta' => [
                            'conflict_course_id' => $conflict['conflict_course_id'] ?? null,
                            'conflict_subject_code' => $conflict['conflict_subject_code'] ?? null,
                            'conflict_day_of_week' => $conflict['day_of_week'] ?? null,
                        ],
                    ]);

                    return $this->result(
                        'error',
                        "Cannot approve enrollment for {$this->studentLabel($student)} in {$this->courseLabel($course)}. ".$reason
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

            $this->logEnrollmentStatus([
                'enrollment_id' => (int) $enrollmentId,
                'student_id' => (int) $enrollment->student_id,
                'course_id' => (int) $enrollment->course_id,
                'from_status' => 'pending',
                'to_status' => 'approved',
                'action' => 'approved',
                'reason' => 'Enrollment approved by staff.',
                'performed_by' => $performedBy,
            ]);

            $this->notifyStudentEnrollmentDecision($student, $course, 'approved');

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
    public function rejectEnrollment(int|string $enrollmentId, ?string $reason = null): array
    {
        $reason = $this->normalizeReason($reason);

        return DB::transaction(function () use ($enrollmentId, $reason): array {
            $performedBy = Auth::id();
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

            $this->logEnrollmentStatus([
                'enrollment_id' => (int) $enrollmentId,
                'student_id' => (int) $enrollment->student_id,
                'course_id' => (int) $enrollment->course_id,
                'from_status' => 'pending',
                'to_status' => 'rejected',
                'action' => 'rejected',
                'reason' => $reason ?? 'Enrollment rejected by staff.',
                'performed_by' => $performedBy,
            ]);

            $this->notifyStudentEnrollmentDecision($student, $course, 'rejected', $reason);

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
            $performedBy = Auth::id();
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

            $this->logEnrollmentStatus([
                'enrollment_id' => (int) $enrollmentId,
                'student_id' => (int) $enrollment->student_id,
                'course_id' => (int) $enrollment->course_id,
                'from_status' => 'withdrawal_pending',
                'to_status' => 'withdrawn',
                'action' => 'withdrawal_approved',
                'reason' => 'Withdrawal approved by staff.',
                'performed_by' => $performedBy,
            ]);

            $this->notifyStudentEnrollmentDecision($student, $course, 'withdrawal_approved');

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
    public function rejectWithdrawal(int|string $enrollmentId, ?string $reason = null): array
    {
        $reason = $this->normalizeReason($reason);

        return DB::transaction(function () use ($enrollmentId, $reason): array {
            $performedBy = Auth::id();
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

            $this->logEnrollmentStatus([
                'enrollment_id' => (int) $enrollmentId,
                'student_id' => (int) $enrollment->student_id,
                'course_id' => (int) $enrollment->course_id,
                'from_status' => 'withdrawal_pending',
                'to_status' => 'approved',
                'action' => 'withdrawal_rejected',
                'reason' => $reason ?? 'Withdrawal rejected by staff.',
                'performed_by' => $performedBy,
            ]);

            $this->notifyStudentEnrollmentDecision($student, $course, 'withdrawal_rejected', $reason);

            return $this->result(
                'success',
                "Withdrawal rejected for {$this->studentLabel($student)} from {$this->courseLabel($course)}. Student remains enrolled."
            );
        });
    }

    /**
     * @param  array{
     *     enrollment_id?: int|null,
     *     student_id: int,
     *     course_id: int,
     *     from_status?: string|null,
     *     to_status?: string|null,
     *     action: string,
     *     reason?: string|null,
     *     performed_by?: int|null,
     *     meta?: array<string, mixed>|null
     * }  $payload
     */
    private function logEnrollmentStatus(array $payload): void
    {
        if (! $this->hasEnrollmentStatusLogTable()) {
            return;
        }

        try {
            EnrollmentStatusLog::create([
                'enrollment_id' => $payload['enrollment_id'] ?? null,
                'student_id' => $payload['student_id'],
                'course_id' => $payload['course_id'],
                'from_status' => $payload['from_status'] ?? null,
                'to_status' => $payload['to_status'] ?? null,
                'action' => $payload['action'],
                'reason' => $payload['reason'] ?? null,
                'performed_by' => $payload['performed_by'] ?? null,
                'meta' => $payload['meta'] ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::warning('enrollment.status_log_failed', [
                'student_id' => $payload['student_id'] ?? null,
                'course_id' => $payload['course_id'] ?? null,
                'action' => $payload['action'] ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function hasEnrollmentStatusLogTable(): bool
    {
        static $exists = null;

        if ($exists === null) {
            $exists = Schema::hasTable('enrollment_status_logs');
        }

        return (bool) $exists;
    }

    private function normalizeReason(?string $reason): ?string
    {
        if ($reason === null) {
            return null;
        }

        $cleaned = trim($reason);

        return $cleaned !== '' ? mb_substr($cleaned, 0, 1000) : null;
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
            ->with('subject:id,course_id,subject_code,title')
            ->whereHas('subject', fn ($q) => $q->where('course_id', $course->id))
            ->get(['subject_id', 'day_of_week', 'start_time', 'end_time']);

        if ($newCourseTimetables->isEmpty()) {
            return null;
        }

        $existingTimetables = Timetable::query()
            ->with([
                'subject:id,course_id,subject_code,title',
                'subject.course:id,course_code,title',
            ])
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
                        'requested_course_code' => $course->course_code,
                        'requested_course_title' => $course->title,
                        'requested_subject_code' => $newTimetable->subject?->subject_code,
                        'requested_subject_title' => $newTimetable->subject?->title,
                        'conflict_subject_code' => $existingTimetable->subject?->subject_code,
                        'conflict_subject_title' => $existingTimetable->subject?->title,
                        'day_of_week' => (string) $newTimetable->day_of_week,
                        'start_time' => (string) $newTimetable->start_time,
                        'end_time' => (string) $newTimetable->end_time,
                        'conflict_start_time' => (string) $existingTimetable->start_time,
                        'conflict_end_time' => (string) $existingTimetable->end_time,
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
        $requestedCourse = trim(sprintf(
            '%s - %s',
            (string) ($conflict['requested_course_code'] ?? ''),
            (string) ($conflict['requested_course_title'] ?? '')
        ));
        $requestedSubject = trim(sprintf(
            '%s%s',
            (string) ($conflict['requested_subject_code'] ?? ''),
            filled($conflict['requested_subject_title'] ?? null)
                ? ' ('.(string) $conflict['requested_subject_title'].')'
                : ''
        ));
        $conflictCourse = trim(sprintf(
            '%s - %s',
            (string) ($conflict['course_code'] ?? ''),
            (string) ($conflict['course_title'] ?? '')
        ));
        $conflictSubject = trim(sprintf(
            '%s%s',
            (string) ($conflict['conflict_subject_code'] ?? ''),
            filled($conflict['conflict_subject_title'] ?? null)
                ? ' ('.(string) $conflict['conflict_subject_title'].')'
                : ''
        ));
        $requestedRange = sprintf(
            '%s-%s',
            $this->formatTimeLabel((string) ($conflict['start_time'] ?? '')),
            $this->formatTimeLabel((string) ($conflict['end_time'] ?? ''))
        );
        $conflictRange = sprintf(
            '%s-%s',
            $this->formatTimeLabel((string) ($conflict['conflict_start_time'] ?? '')),
            $this->formatTimeLabel((string) ($conflict['conflict_end_time'] ?? ''))
        );

        return sprintf(
            'Schedule conflict on %s. Requested %s %s (%s) overlaps with enrolled %s %s (%s). Please choose a different course or contact administration.',
            (string) $conflict['day_of_week'],
            $requestedCourse !== '-' ? $requestedCourse : 'course',
            $requestedSubject !== '' ? $requestedSubject : '',
            $requestedRange,
            $conflictCourse !== '-' ? $conflictCourse : 'course',
            $conflictSubject !== '' ? $conflictSubject : '',
            $conflictRange
        );
    }

    private function formatTimeLabel(string $value): string
    {
        $raw = trim($value);
        if ($raw === '') {
            return '-';
        }

        if (preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $raw) === 1) {
            return substr($raw, 0, 5);
        }

        return $raw;
    }

    private function notifyStudentEnrollmentDecision(
        ?Student $student,
        ?Course $course,
        string $decision,
        ?string $reason = null
    ): void {
        $recipient = $student?->user;
        if (! $recipient) {
            return;
        }

        try {
            $recipient->notify(new EnrollmentStatusUpdated($course, $decision, $reason));
        } catch (\Throwable $e) {
            Log::warning('enrollment.notification_failed', [
                'student_id' => $student?->id,
                'course_id' => $course?->id,
                'decision' => $decision,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
