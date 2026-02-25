<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ManagementActivityNotification extends Notification
{
    use Queueable;

    /**
     * @param  array<string, mixed>  $meta
     */
    public function __construct(
        protected string $module,
        protected string $title,
        protected string $message,
        protected ?string $url = null,
        protected array $meta = []
    ) {}

    public function via(object $notifiable): array
    {
        $preferences = is_array($notifiable->preferences ?? null) ? $notifiable->preferences : [];
        if (($preferences['notify_management'] ?? true) === false) {
            return [];
        }

        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return array_merge([
            'type' => 'management',
            'module' => $this->module,
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
        ], $this->meta);
    }
}
