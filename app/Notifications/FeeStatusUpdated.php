<?php

namespace App\Notifications;

use App\Models\Fee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class FeeStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Fee $fee) {}

    public function via(object $notifiable): array
    {
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
                ucfirst($this->fee->status)
            ),
        ];
    }
}
