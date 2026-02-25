<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewMessageReceived extends Notification
{
    use Queueable;

    public function __construct(
        protected int $messageId,
        protected int $senderId,
        protected string $senderName,
        protected string $body
    ) {}

    public function via(object $notifiable): array
    {
        $preferences = is_array($notifiable->preferences ?? null) ? $notifiable->preferences : [];
        if (($preferences['notify_messages'] ?? true) === false) {
            return [];
        }

        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $snippet = Str::limit(trim($this->body), 120);

        return [
            'type' => 'message',
            'title' => 'New message received',
            'message' => "{$this->senderName}: {$snippet}",
            'message_id' => $this->messageId,
            'sender_id' => $this->senderId,
            'url' => route('messages.index', ['with_user' => $this->senderId]),
        ];
    }
}
