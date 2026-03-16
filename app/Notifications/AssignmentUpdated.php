<?php

namespace App\Notifications;

use App\Models\Assignment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AssignmentUpdated extends Notification
{
    use Queueable;

    public function __construct(
        protected Assignment $assignment,
        protected string $action = 'updated'
    ) {}

    public function via(object $notifiable): array
    {
        $preferences = is_array($notifiable->preferences ?? null) ? $notifiable->preferences : [];
        if (($preferences['notify_assignments'] ?? true) === false) {
            return [];
        }

        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $this->assignment->loadMissing('subject.course');

        $subjectCode = $this->assignment->subject?->subject_code ?? 'subject';
        $courseCode = $this->assignment->course?->course_code ?? 'course';
        $dueDate = $this->assignment->due_date?->format('Y-m-d') ?? 'N/A';
        $dueTimeRaw = $this->assignment->due_time;
        $dueTime = is_string($dueTimeRaw) ? substr($dueTimeRaw, 0, 5) : null;

        [$title, $statusLabel] = match ($this->action) {
            'created' => ['New assignment available', 'created'],
            'published' => ['Assignment published', 'published'],
            default => ['Assignment updated', 'updated'],
        };

        return [
            'type' => 'assignment',
            'title' => $title,
            'message' => sprintf(
                '%s (%s/%s) was %s. Due %s%s.',
                $this->assignment->title,
                $subjectCode,
                $courseCode,
                $statusLabel,
                $dueDate,
                $dueTime ? " {$dueTime}" : ''
            ),
            'assignment_id' => $this->assignment->id,
            'subject_id' => $this->assignment->subject_id,
            'course_id' => $this->assignment->course_id,
            'action' => $this->action,
            'url' => $this->resolveUrl($notifiable),
        ];
    }

    private function resolveUrl(object $notifiable): ?string
    {
        $role = (string) ($notifiable->role ?? '');

        if ($role === 'student') {
            return route('student.assignments.show', $this->assignment->id);
        }

        if ($role === 'teacher' && $this->assignment->subject_id) {
            return route('teacher.assignments.show', $this->assignment->subject_id);
        }

        return null;
    }
}
