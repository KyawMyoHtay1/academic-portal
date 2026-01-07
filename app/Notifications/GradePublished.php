<?php

namespace App\Notifications;

use App\Models\Grade;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GradePublished extends Notification
{
    use Queueable;

    public function __construct(protected Grade $grade)
    {
    }

    public function via(object $notifiable): array
    {
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
        ];
    }
}


