<?php

namespace App\Notifications;

use App\Models\AssignmentSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AssignmentSubmissionGraded extends Notification
{
    use Queueable;

    public function __construct(protected AssignmentSubmission $submission) {}

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
        $assignment = $this->submission->assignment;
        $score = $this->submission->score !== null
            ? (string) $this->submission->score
            : 'N/A';

        return [
            'type' => 'assignment',
            'title' => 'Assignment graded',
            'message' => sprintf(
                'Your submission for "%s" was graded. Score: %s.',
                (string) ($assignment?->title ?? 'assignment'),
                $score
            ),
            'assignment_id' => $assignment?->id,
            'submission_id' => $this->submission->id,
            'url' => $assignment?->id
                ? route('student.assignments.show', $assignment->id)
                : route('student.assignments.index'),
        ];
    }
}
