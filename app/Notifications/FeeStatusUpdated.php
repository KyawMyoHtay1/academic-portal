<?php

namespace App\Notifications;

use App\Models\Fee;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FeeStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(protected Fee $fee) {}

    public function via(object $notifiable): array
    {
        $preferences = is_array($notifiable->preferences ?? null) ? $notifiable->preferences : [];
        if (($preferences['notify_fees'] ?? true) === false) {
            return [];
        }

        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'fee',
            'title' => 'Fee status updated',
            'message' => sprintf(
                'Your fee "%s" (%s) is now marked as %s.',
                $this->fee->description ?? 'Tuition fee',
                $this->fee->due_date->format('Y-m-d'),
                ucwords(str_replace('_', ' ', (string) $this->fee->status))
            ),
            'fee_id' => $this->fee->id,
            'url' => $this->resolveUrl($notifiable),
        ];
    }

    private function resolveUrl(object $notifiable): ?string
    {
        $role = $notifiable->role ?? null;

        if ($role === 'student') {
            return route('student.fees.index');
        }

        if (in_array($role, ['staff', 'admin'], true)) {
            return route('admin.fees.index');
        }

        return null;
    }
}
