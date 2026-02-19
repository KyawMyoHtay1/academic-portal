<?php

namespace App\Notifications;

use App\Models\Fee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class FeePaymentReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Fee $fee,
        protected int $daysOverdue
    ) {}

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
            'title' => 'Overdue fee reminder',
            'message' => sprintf(
                'Your fee "%s" is overdue by %d day(s). Please submit payment confirmation as soon as possible.',
                $this->fee->description ?? 'Tuition fee',
                $this->daysOverdue
            ),
            'fee_id' => $this->fee->id,
            'days_overdue' => $this->daysOverdue,
            'url' => route('student.fees.index'),
        ];
    }
}
