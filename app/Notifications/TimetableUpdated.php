<?php

namespace App\Notifications;

use App\Models\Timetable;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TimetableUpdated extends Notification
{
    use Queueable;

    public function __construct(protected Timetable $timetable, protected string $action = 'updated')
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'timetable',
            'title' => 'Timetable ' . $this->action,
            'message' => sprintf(
                'Timetable for %s (%s) on %s at %s-%s has been %s.',
                $this->timetable->course->title,
                $this->timetable->course->course_code,
                $this->timetable->day_of_week,
                $this->timetable->start_time,
                $this->timetable->end_time,
                $this->action
            ),
        ];
    }
}



