<?php

namespace App\Services;

use App\Models\Fee;
use App\Models\StripeWebhookEvent;
use App\Models\Student;
use App\Notifications\FeeStatusUpdated;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Webhook;

class PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createCheckoutSession(Fee $fee, Student $student): Session
    {
        return Session::create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'gbp',
                    'product_data' => [
                        'name' => $fee->description ?? 'Fee Payment',
                    ],
                    'unit_amount' => (int) ($fee->amount * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'payment_intent_data' => [
                'metadata' => [
                    'fee_id' => $fee->id,
                    'student_id' => $student->id,
                ],
            ],
            'success_url' => route('payment.success', $fee).'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payment.cancel', $fee),
            'customer_email' => $student->email,
        ]);
    }

    public function markCheckoutStarted(Fee $fee, string $paymentIntentId, int $studentId): void
    {
        $fee->markAsPaymentPending($paymentIntentId);

        Log::info('payment.checkout_started', [
            'fee_id' => $fee->id,
            'student_id' => $studentId,
            'payment_intent_id' => $paymentIntentId,
        ]);
    }

    /**
     * @throws ApiErrorException
     */
    public function confirmPaymentFromSession(Fee $fee, string $sessionId): bool
    {
        $session = Session::retrieve($sessionId);

        if ($session->payment_status === 'paid' && $session->payment_intent) {
            $paymentIntent = PaymentIntent::retrieve($session->payment_intent);
            if ($paymentIntent->status === 'succeeded') {
                $this->handlePaymentSuccess($paymentIntent);

                return true;
            }
        }

        if ($session->payment_status !== 'paid' && $fee->status === Fee::STATUS_PAYMENT_PENDING) {
            $fee->markAsPending();
        }

        return false;
    }

    public function resetPendingPayment(Fee $fee): bool
    {
        if ($fee->status !== Fee::STATUS_PAYMENT_PENDING || ! $fee->payment_intent_id) {
            return false;
        }

        $fee->markAsPending();

        return true;
    }

    /**
     * @throws \Exception
     */
    public function constructWebhookEvent(string $payload, ?string $signatureHeader, string $endpointSecret): object
    {
        return Webhook::constructEvent($payload, (string) $signatureHeader, $endpointSecret);
    }

    /**
     * Record webhook event and return null when duplicate delivery is detected.
     */
    public function recordWebhookEvent(string $eventId, string $eventType, array $payload): ?StripeWebhookEvent
    {
        try {
            return StripeWebhookEvent::create([
                'event_id' => $eventId,
                'event_type' => $eventType,
                'payload' => $payload,
            ]);
        } catch (QueryException $e) {
            $message = strtolower($e->getMessage());

            if ((string) $e->getCode() === '23000' || str_contains($message, 'unique')) {
                Log::info('Duplicate Stripe webhook event ignored', [
                    'event_id' => $eventId,
                    'event_type' => $eventType,
                ]);

                return null;
            }

            throw $e;
        }
    }

    public function processWebhookEvent(object $event): void
    {
        DB::transaction(function () use ($event): void {
            switch ($event->type) {
                case 'checkout.session.completed':
                    $this->handleCheckoutCompleted($event->data->object);
                    break;
                case 'payment_intent.succeeded':
                    $this->handlePaymentSuccess($event->data->object);
                    break;
                case 'payment_intent.payment_failed':
                    $this->handlePaymentFailure($event->data->object);
                    break;
                default:
                    Log::info('Unhandled Stripe webhook event', [
                        'event_type' => $event->type,
                    ]);
            }
        });
    }

    /**
     * Handle successful payment from webhook.
     */
    private function handlePaymentSuccess(object $paymentIntent): void
    {
        $feeId = $paymentIntent->metadata->fee_id ?? null;

        if (! $feeId) {
            Log::warning('Payment Intent missing fee_id metadata', ['payment_intent_id' => $paymentIntent->id]);

            return;
        }

        $paymentIntentId = $paymentIntent->id ?? null;

        $result = DB::transaction(function () use ($feeId, $paymentIntent, $paymentIntentId) {
            $fee = Fee::query()->lockForUpdate()->find($feeId);

            if (! $fee) {
                Log::warning('Fee not found for payment', [
                    'fee_id' => $feeId,
                    'payment_intent_id' => $paymentIntentId,
                ]);

                return null;
            }

            if ($fee->payment_intent_id && $fee->payment_intent_id !== $paymentIntentId) {
                Log::warning('Payment intent mismatch on success handler, aborting update', [
                    'fee_id' => $fee->id,
                    'existing_payment_intent_id' => $fee->payment_intent_id,
                    'incoming_payment_intent_id' => $paymentIntentId,
                ]);

                return null;
            }

            if ($fee->status === Fee::STATUS_PAID) {
                return [
                    'newly_paid' => false,
                    'fee' => $fee->fresh(['student.user']),
                ];
            }

            $fee->markAsPaid(
                $paymentIntent->payment_method_types[0] ?? 'card',
                $paymentIntentId
            );

            return [
                'newly_paid' => true,
                'fee' => $fee->fresh(['student.user']),
            ];
        });

        if (! $result) {
            return;
        }

        /** @var \App\Models\Fee $fee */
        $fee = $result['fee'];

        if (! $result['newly_paid']) {
            Log::info('Payment already processed, skipping duplicate success handler', [
                'fee_id' => $fee->id,
                'payment_intent_id' => $paymentIntentId,
            ]);

            return;
        }

        if ($fee->student?->user) {
            $fee->student->user->notify(new FeeStatusUpdated($fee));
        }

        Log::info('Payment processed successfully via webhook', [
            'fee_id' => $fee->id,
            'payment_intent_id' => $paymentIntentId,
        ]);
    }

    /**
     * Handle failed payment from webhook.
     */
    private function handlePaymentFailure(object $paymentIntent): void
    {
        $feeId = $paymentIntent->metadata->fee_id ?? null;

        if (! $feeId) {
            return;
        }

        $updated = DB::transaction(function () use ($feeId, $paymentIntent): bool {
            $fee = Fee::query()->lockForUpdate()->find($feeId);

            if (! $fee || $fee->status === Fee::STATUS_PAID) {
                return false;
            }

            if ($fee->payment_intent_id && $fee->payment_intent_id !== $paymentIntent->id) {
                Log::warning('Payment failure webhook ignored due to payment intent mismatch', [
                    'fee_id' => $fee->id,
                    'stored_payment_intent_id' => $fee->payment_intent_id,
                    'incoming_payment_intent_id' => $paymentIntent->id,
                ]);

                return false;
            }

            if ($fee->status !== Fee::STATUS_PAYMENT_PENDING) {
                return false;
            }

            $fee->markAsPending();

            return true;
        });

        if ($updated) {
            Log::info('Payment failed via webhook', [
                'fee_id' => $feeId,
                'payment_intent_id' => $paymentIntent->id,
            ]);
        }
    }

    /**
     * Handle checkout.session.completed webhook.
     */
    private function handleCheckoutCompleted(object $session): void
    {
        $paymentIntentId = $session->payment_intent ?? null;

        if (! $paymentIntentId) {
            Log::warning('Checkout completed without payment_intent id', ['session_id' => $session->id]);

            return;
        }

        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            if ($paymentIntent->status === 'succeeded') {
                $this->handlePaymentSuccess($paymentIntent);
            } elseif ($paymentIntent->status === 'requires_payment_method') {
                $this->handlePaymentFailure($paymentIntent);
            }
        } catch (ApiErrorException $e) {
            Log::error('Failed to retrieve payment intent after checkout completion', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
