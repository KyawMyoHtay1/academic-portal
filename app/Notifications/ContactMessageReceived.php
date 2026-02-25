<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ContactMessageReceived extends Notification
{
    use Queueable;

    public function __construct(protected ContactMessage $contactMessage) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $senderName = trim(
            sprintf(
                '%s %s',
                (string) $this->contactMessage->first_name,
                (string) $this->contactMessage->last_name
            )
        );
        $subject = trim((string) $this->contactMessage->subject);
        $bodyPreview = Str::limit(trim((string) $this->contactMessage->message), 120);

        return [
            'type' => 'contact',
            'title' => 'New contact message',
            'message' => sprintf(
                '%s (%s) sent: %s%s',
                $senderName !== '' ? $senderName : 'A visitor',
                (string) $this->contactMessage->email,
                $subject !== '' ? "{$subject} - " : '',
                $bodyPreview
            ),
            'contact_message_id' => $this->contactMessage->id,
            'url' => route('admin.contact-messages.index', ['status' => 'unread']),
        ];
    }
}
