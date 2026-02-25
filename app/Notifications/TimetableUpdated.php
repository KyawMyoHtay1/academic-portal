<?php

namespace App\Notifications;

use App\Models\Timetable;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TimetableUpdated extends Notification
{
    use Queueable;

    public function __construct(protected Timetable $timetable, protected string $action = 'updated') {}

    public function via(object $notifiable): array
    {
        $preferences = is_array($notifiable->preferences ?? null) ? $notifiable->preferences : [];
        if (($preferences['notify_timetable'] ?? true) === false) {
            return [];
        }

        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'timetable',
            'title' => 'Timetable '.$this->action,
            'message' => sprintf(
                'Timetable for %s (%s) on %s at %s-%s has been %s.',
                $this->timetable->course->title,
                $this->timetable->course->course_code,
                $this->timetable->day_of_week,
                $this->timetable->start_time,
                $this->timetable->end_time,
                $this->action
            ),
            'timetable_id' => $this->timetable->id,
            'subject_id' => $this->timetable->subject_id,
            'course_id' => $this->timetable->subject?->course_id,
            'url' => $this->resolveUrl($notifiable),
        ];
    }

    private function resolveUrl(object $notifiable): ?string
    {
        $role = $notifiable->role ?? null;

        if ($role === 'teacher') {
            return route('teacher.timetable.index');
        }

        if ($role === 'student') {
            return route('student.timetable.index');
        }

        if (in_array($role, ['staff', 'admin'], true)) {
            return route('admin.timetables.index');
        }

        return null;
    }
}
