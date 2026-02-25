<?php

namespace App\Notifications;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EnrollmentRequestReceived extends Notification
{
    use Queueable;

    public function __construct(
        protected Student $student,
        protected Course $course,
        protected string $requestType = 'enrollment'
    ) {}

    public function via(object $notifiable): array
    {
        $preferences = is_array($notifiable->preferences ?? null) ? $notifiable->preferences : [];
        if (($preferences['notify_enrollment_requests'] ?? true) === false) {
            return [];
        }

        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $courseLabel = trim("{$this->course->course_code} - {$this->course->title}", ' -');
        $studentLabel = $this->student->full_name ?: 'A student';
        $isWithdrawal = $this->requestType === 'withdrawal';

        return [
            'type' => 'enrollment',
            'title' => $isWithdrawal ? 'New withdrawal request' : 'New enrollment request',
            'message' => $isWithdrawal
                ? "{$studentLabel} requested withdrawal from {$courseLabel}."
                : "{$studentLabel} requested enrollment in {$courseLabel}.",
            'request_type' => $this->requestType,
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
            'url' => route('admin.enrollments.index', [
                'status' => $isWithdrawal ? 'withdrawal_pending' : 'pending',
            ]),
        ];
    }
}
