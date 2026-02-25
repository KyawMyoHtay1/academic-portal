<?php

namespace App\Notifications;

use App\Models\Fee;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FeePaymentPendingReview extends Notification
{
    use Queueable;

    public function __construct(
        protected Fee $fee,
        protected string $source = 'student_submission'
    ) {}

    public function via(object $notifiable): array
    {
        $preferences = is_array($notifiable->preferences ?? null) ? $notifiable->preferences : [];
        if (($preferences['notify_fees'] ?? true) === false) {
            return [];
        }

        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $studentName = $this->fee->student?->full_name ?? 'A student';
        $studentNo = $this->fee->student?->student_no;
        $studentLabel = $studentNo ? "{$studentName} ({$studentNo})" : $studentName;
        $feeDescription = $this->fee->description ?? 'Tuition fee';
        $amountLabel = number_format((float) $this->fee->amount, 2);

        [$title, $message] = match ($this->source) {
            'checkout_started' => [
                'Payment started',
                "{$studentLabel} started online payment for \"{$feeDescription}\" (GBP {$amountLabel}).",
            ],
            default => [
                'Payment confirmation submitted',
                "{$studentLabel} submitted payment confirmation for \"{$feeDescription}\" (GBP {$amountLabel}).",
            ],
        };

        return [
            'type' => 'fee',
            'title' => $title,
            'message' => $message,
            'fee_id' => $this->fee->id,
            'student_id' => $this->fee->student_id,
            'status' => $this->fee->status,
            'source' => $this->source,
            'url' => route('admin.fees.index', ['status' => Fee::STATUS_PAYMENT_PENDING]),
        ];
    }
}
