<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AnnouncementReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Announcement $announcement) {}

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
        return (new MailMessage)
            ->subject('Reminder: Announcement requires your attention')
            ->greeting('Hello '.($notifiable->name ?? 'User').',')
            ->line('There is an announcement that still needs your attention.')
            ->line($this->announcement->title)
            ->action('View announcements', route('announcements.index'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'announcement',
            'title' => 'Announcement reminder',
            'message' => sprintf(
                'Reminder: "%s" is still awaiting your %s.',
                $this->announcement->title,
                $this->announcement->require_ack ? 'acknowledgement' : 'read confirmation'
            ),
            'announcement_id' => $this->announcement->id,
            'url' => route('announcements.index'),
        ];
    }
}
