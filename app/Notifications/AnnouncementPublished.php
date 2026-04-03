<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
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

        $channels = ['database'];
        if (($preferences['email_notifications'] ?? true) === true) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        [$subject, $intro] = match ($this->action) {
            'updated' => ['Announcement updated', 'An announcement has been updated and may need your attention.'],
            default => ['New announcement published', 'A new announcement has been published for you.'],
        };

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Hello '.($notifiable->name ?? 'User').',')
            ->line($intro)
            ->line((string) $this->announcement->title)
            ->action('View announcements', route('announcements.index'));
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
