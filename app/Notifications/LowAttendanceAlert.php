<?php

namespace App\Notifications;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowAttendanceAlert extends Notification
{
    use Queueable;

    public function __construct(
        protected Student $student,
        protected float $rate,
        protected float $threshold,
    ) {}

    public function via(object $notifiable): array
    {
        $preferences = is_array($notifiable->preferences ?? null) ? $notifiable->preferences : [];
        if (($preferences['notify_attendance'] ?? true) === false) {
            return [];
        }

        $channels = ['database'];
        if (($preferences['email_notifications'] ?? true) === true) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $rateText = rtrim(rtrim(number_format($this->rate, 2, '.', ''), '0'), '.');
        $thresholdText = rtrim(rtrim(number_format($this->threshold, 2, '.', ''), '0'), '.');

        return (new MailMessage)
            ->subject('Low attendance alert')
            ->greeting('Hello '.($notifiable->name ?? 'Student').',')
            ->line(sprintf(
                'Your attendance is currently %s%%, which is below the required threshold of %s%%.',
                $rateText,
                $thresholdText
            ))
            ->action('View attendance report', url('/student/attendance'))
            ->line('If you believe this is incorrect, please contact your course administrator.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'attendance',
            'title' => 'Low attendance alert',
            'message' => sprintf(
                'Your attendance is %.2f%%, which is below the required %.2f%% threshold.',
                $this->rate,
                $this->threshold
            ),
            'rate' => round($this->rate, 2),
            'threshold' => round($this->threshold, 2),
            'student_id' => $this->student->id,
            'url' => route('student.attendance.index'),
        ];
    }
}
