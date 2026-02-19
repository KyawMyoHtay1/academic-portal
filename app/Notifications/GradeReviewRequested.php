<?php

namespace App\Notifications;

use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class GradeReviewRequested extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Grade $grade,
        protected Student $student,
        protected Subject $subject
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
        return [
            'type' => 'grade_review',
            'title' => 'Grade submitted for review',
            'message' => sprintf(
                'A grade for %s (%s) was submitted by %s for student %s (%s).',
                $this->subject->title,
                $this->subject->subject_code,
                $this->grade->grader?->name ?? 'Teacher',
                $this->student->full_name,
                $this->student->student_no
            ),
            'subject_id' => $this->subject->id,
            'grade_id' => $this->grade->id,
            'student_id' => $this->student->id,
            'course_id' => $this->subject->course_id,
            'url' => route('admin.grades.show', $this->subject->id),
        ];
    }
}
