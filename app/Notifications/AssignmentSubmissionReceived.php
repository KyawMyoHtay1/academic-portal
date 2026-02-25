<?php

namespace App\Notifications;

use App\Models\Assignment;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AssignmentSubmissionReceived extends Notification
{
    use Queueable;

    public function __construct(
        protected Assignment $assignment,
        protected Student $student,
        protected bool $isResubmission = false
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
        $action = $this->isResubmission ? 'resubmitted' : 'submitted';
        $studentName = (string) ($this->student->full_name ?? 'Student');
        $studentNo = (string) ($this->student->student_no ?? 'N/A');

        return [
            'type' => 'assignment',
            'title' => 'Assignment submission received',
            'message' => sprintf(
                '%s (%s) %s "%s".',
                $studentName,
                $studentNo,
                $action,
                (string) $this->assignment->title
            ),
            'assignment_id' => $this->assignment->id,
            'student_id' => $this->student->id,
            'url' => route('teacher.assignments.submissions', $this->assignment->id),
        ];
    }
}
