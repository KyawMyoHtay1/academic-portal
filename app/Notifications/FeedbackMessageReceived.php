<?php

namespace App\Notifications;

use App\Models\FeedbackMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class FeedbackMessageReceived extends Notification
{
    use Queueable;

    public function __construct(protected FeedbackMessage $feedbackMessage) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $type = trim((string) $this->feedbackMessage->type);
        $bodyPreview = Str::limit(trim((string) $this->feedbackMessage->message), 120);

        return [
            'type' => 'feedback',
            'title' => 'New feedback message',
            'message' => sprintf(
                '%s (%s) sent %s feedback: %s',
                (string) $this->feedbackMessage->name,
                (string) $this->feedbackMessage->email,
                $type !== '' ? $type : 'general',
                $bodyPreview
            ),
            'feedback_message_id' => $this->feedbackMessage->id,
            'url' => route('admin.feedback-messages.index', ['status' => 'unread']),
        ];
    }
}
