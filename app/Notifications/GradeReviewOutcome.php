<?php

namespace App\Notifications;

use App\Models\Grade;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class GradeReviewOutcome extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Grade $grade,
        protected string $decision,
        protected ?string $reason = null
    ) {}

    public function via(object $notifiable): array
    {
        $preferences = is_array($notifiable->preferences ?? null) ? $notifiable->preferences : [];
        if (($preferences['notify_grade_review'] ?? true) === false) {
            return [];
        }

        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $studentName = $this->grade->student?->full_name ?? 'Unknown student';
        $studentNo = $this->grade->student?->student_no ?? 'N/A';
        $subjectCode = $this->grade->subject?->subject_code ?? 'Unknown subject';
        $subjectTitle = $this->grade->subject?->title ?? '';
        $statusLabel = $this->decision === 'approved' ? 'approved' : 'rejected';
        $message = sprintf(
            'Your submitted grade for %s (%s) in %s %s was %s.',
            $studentName,
            $studentNo,
            $subjectCode,
            $subjectTitle !== '' ? '- '.$subjectTitle : '',
            $statusLabel
        );

        if ($this->decision === 'rejected' && $this->reason) {
            $message .= ' Reason: '.$this->reason;
        }

        return [
            'type' => 'grade_review_result',
            'title' => $this->decision === 'approved' ? 'Grade approved by staff' : 'Grade rejected by staff',
            'message' => $message,
            'decision' => $this->decision,
            'reason' => $this->reason,
            'grade_id' => $this->grade->id,
            'subject_id' => $this->grade->subject_id,
            'student_id' => $this->grade->student_id,
            'url' => route('teacher.grades.show', $this->grade->subject_id),
        ];
    }
}
