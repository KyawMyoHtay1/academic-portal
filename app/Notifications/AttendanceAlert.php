<?php

namespace App\Notifications;

use App\Models\Attendance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AttendanceAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Attendance $attendance) {}

    public function viaConnections(): array
    {
        return [
            'mail' => 'database',
        ];
    }

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
        $message = (new MailMessage)
            ->subject('Attendance recorded')
            ->greeting('Hello '.($notifiable->name ?? 'Student').',')
            ->line(sprintf(
                'Your attendance for %s (%s) on %s is marked as %s.',
                $this->attendance->course->title,
                $this->attendance->course->course_code,
                $this->attendance->date->format('Y-m-d'),
                ucfirst($this->attendance->status)
            ));

        $url = $this->resolveUrl($notifiable);
        if ($url) {
            $message->action('View attendance', $url);
        }

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'attendance',
            'title' => 'Attendance recorded',
            'message' => sprintf(
                'Your attendance for %s (%s) on %s is marked as %s.',
                $this->attendance->course->title,
                $this->attendance->course->course_code,
                $this->attendance->date->format('Y-m-d'),
                ucfirst($this->attendance->status)
            ),
            'attendance_id' => $this->attendance->id,
            'subject_id' => $this->attendance->subject_id,
            'student_id' => $this->attendance->student_id,
            'url' => $this->resolveUrl($notifiable),
        ];
    }

    private function resolveUrl(object $notifiable): ?string
    {
        $role = $notifiable->role ?? null;

        if ($role === 'student') {
            return route('student.attendance.index');
        }

        if ($role === 'teacher') {
            return route('teacher.attendance.index');
        }

        if (in_array($role, ['staff', 'admin'], true)) {
            return route('admin.attendance.report');
        }

        return null;
    }
}
