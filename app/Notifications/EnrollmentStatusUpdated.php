<?php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EnrollmentStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(
        protected ?Course $course,
        protected string $decision,
        protected ?string $reason = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $courseCode = $this->course?->course_code ?? 'course';
        $courseTitle = $this->course?->title ?? 'selected course';
        $courseLabel = trim("{$courseCode} - {$courseTitle}", ' -');

        [$title, $message] = match ($this->decision) {
            'approved' => [
                'Enrollment approved',
                "Your enrollment request for {$courseLabel} was approved.",
            ],
            'rejected' => [
                'Enrollment rejected',
                $this->reason
                    ? "Your enrollment request for {$courseLabel} was rejected. Reason: {$this->reason}"
                    : "Your enrollment request for {$courseLabel} was rejected.",
            ],
            'withdrawal_approved' => [
                'Withdrawal approved',
                "Your withdrawal request for {$courseLabel} was approved.",
            ],
            'withdrawal_rejected' => [
                'Withdrawal rejected',
                $this->reason
                    ? "Your withdrawal request for {$courseLabel} was rejected. Reason: {$this->reason}"
                    : "Your withdrawal request for {$courseLabel} was rejected. You remain enrolled.",
            ],
            default => [
                'Enrollment update',
                "Your enrollment status for {$courseLabel} was updated.",
            ],
        };

        return [
            'type' => 'enrollment',
            'title' => $title,
            'message' => $message,
            'decision' => $this->decision,
            'course_id' => $this->course?->id,
            'course_code' => $this->course?->course_code,
            'url' => $this->resolveUrl($notifiable),
        ];
    }

    private function resolveUrl(object $notifiable): ?string
    {
        $role = (string) ($notifiable->role ?? '');

        if ($role === 'student') {
            return route('my-courses.index');
        }

        if (in_array($role, ['staff', 'admin'], true)) {
            return route('admin.enrollments.index');
        }

        return null;
    }
}

