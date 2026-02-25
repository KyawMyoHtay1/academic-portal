<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AnnouncementPublished extends Notification
{
    use Queueable;

    public function __construct(
        protected Announcement $announcement,
        protected string $action = 'published'
    ) {}

    public function via(object $notifiable): array
    {
        $preferences = is_array($notifiable->preferences ?? null) ? $notifiable->preferences : [];
        if (($preferences['notify_announcements'] ?? true) === false) {
            return [];
        }

        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        [$title, $prefix] = match ($this->action) {
            'updated' => ['Announcement updated', 'Updated announcement'],
            default => ['New announcement', 'New announcement'],
        };

        return [
            'type' => 'announcement',
            'title' => $title,
            'message' => sprintf('%s: "%s"', $prefix, (string) $this->announcement->title),
            'announcement_id' => $this->announcement->id,
            'action' => $this->action,
            'url' => route('announcements.index'),
        ];
    }
}
