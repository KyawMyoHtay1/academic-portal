<?php

namespace App\Notifications;

use App\Models\Attendance;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AttendanceAlert extends Notification
{
    use Queueable;

    public function __construct(protected Attendance $attendance)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
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
        ];
    }
}


