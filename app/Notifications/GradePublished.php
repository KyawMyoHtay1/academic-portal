<?php

namespace App\Notifications;

use App\Models\Grade;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GradePublished extends Notification
{
    use Queueable;

    public function __construct(protected Grade $grade) {}

    public function via(object $notifiable): array
    {
        $preferences = is_array($notifiable->preferences ?? null) ? $notifiable->preferences : [];
        if (($preferences['notify_grades'] ?? true) === false) {
            return [];
        }

        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'grade',
            'title' => 'New grade published',
            'message' => sprintf(
                'Your grade for %s (%s) has been updated to %s.',
                $this->grade->course->title,
                $this->grade->course->course_code,
                $this->grade->score ?? 'N/A'
            ),
            'grade_id' => $this->grade->id,
            'subject_id' => $this->grade->subject_id,
            'url' => $this->resolveUrl($notifiable),
        ];
    }

    private function resolveUrl(object $notifiable): ?string
    {
        $role = $notifiable->role ?? null;

        if (in_array($role, ['staff', 'admin'], true)) {
            return route('admin.grades.index');
        }

        if ($role === 'teacher' && $this->grade->subject_id) {
            return route('teacher.grades.show', $this->grade->subject_id);
        }

        if ($role === 'student') {
            return route('student.grades.index');
        }

        return null;
    }
}
