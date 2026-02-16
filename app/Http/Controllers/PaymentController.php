<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\StripeWebhookEvent;
use App\Notifications\FeeStatusUpdated;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Stripe\Webhook;

class PaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create a Stripe checkout session for a fee payment.
     */
    public function checkout(Request $request, Fee $fee)
    {
        $user = Auth::user();
        $student = $user->student;
    
        if (!$student || $fee->student_id !== $student->id) {
            return redirect()
                ->route('student.fees.index')
                ->with('error', 'Unauthorized access.');
        }
    
        if ($fee->status === 'paid') {
            return redirect()
                ->route('student.fees.index')
                ->with('error', 'This fee has already been paid.');
        }
    
        try {
            $checkoutSession = Session::create([
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
                'success_url' => route('payment.success', $fee) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.cancel', $fee),
                'customer_email' => $student->email,
            ]);
    
            $fee->update([
                'payment_intent_id' => $checkoutSession->payment_intent,
                'status' => 'payment_pending',
            ]);
    
            return Inertia::location($checkoutSession->url);
    
        } catch (ApiErrorException $e) {
            Log::error('Stripe Checkout Session creation failed', [
                'error' => $e->getMessage(),
            ]);
    
            return redirect()
                ->route('student.fees.index')
                ->with('error', 'Payment initialization failed.');
        }
    }
    


    /**
     * Handle successful payment.
     */
    public function success(Request $request, Fee $fee): RedirectResponse
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student || $fee->student_id !== $student->id) {
            abort(403, 'Unauthorized');
        }

        $sessionId = $request->query('session_id');

        // Verify payment via webhook or session
        if ($sessionId) {
            try {
                $session = Session::retrieve($sessionId);
                
                // If Stripe confirms payment, verify the intent one more time
                if ($session->payment_status === 'paid' && $session->payment_intent) {
                    $paymentIntent = PaymentIntent::retrieve($session->payment_intent);
                    if ($paymentIntent->status === 'succeeded') {
                        $this->handlePaymentSuccess($paymentIntent);

                        return redirect()
                            ->route('student.fees.index')
                            ->with('success', "Payment of £{$fee->amount} completed successfully!");
                    }
                }

                // If user left checkout without paying, reset the fee so they can try again
                if ($session->payment_status !== 'paid' && $fee->status === 'payment_pending') {
                    $fee->update([
                        'status' => 'pending',
                        'payment_intent_id' => null,
                    ]);
                }
            } catch (ApiErrorException $e) {
                Log::error('Stripe Session verification failed: ' . $e->getMessage());
            }
        }

        // If webhook already processed, just show success
        if ($fee->status === 'paid') {
            return redirect()
                ->route('student.fees.index')
                ->with('success', "Payment of £{$fee->amount} completed successfully!");
        }

        return redirect()
            ->route('student.fees.index')
            ->with('info', 'Payment is being processed. You will be notified once confirmed.');
    }

    /**
     * Handle cancelled payment.
     */
    public function cancel(Request $request, Fee $fee): RedirectResponse
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student || $fee->student_id !== $student->id) {
            abort(403, 'Unauthorized');
        }

        // Reset payment intent if payment was cancelled
        if ($fee->status === 'payment_pending' && $fee->payment_intent_id) {
            $fee->update([
                'status' => 'pending',
                'payment_intent_id' => null,
            ]);
        }

        return redirect()
            ->route('student.fees.index')
            ->with('info', 'Payment was cancelled. You can try again anytime.');
    }

    /**
     * Handle Stripe webhook for payment confirmation.
     */
    public function webhook(Request $request): \Illuminate\Http\Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook.secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\Exception $e) {
            Log::error('Stripe webhook signature verification failed: ' . $e->getMessage());
            return response('Webhook signature verification failed', 400);
        }

        $eventId = $event->id ?? null;
        if (!$eventId) {
            Log::warning('Stripe webhook payload missing event id');
            return response('Webhook event id missing', 400);
        }

        try {
            $webhookEvent = StripeWebhookEvent::create([
                'event_id' => $eventId,
                'event_type' => $event->type,
                'payload' => json_decode($payload, true),
            ]);
        } catch (QueryException $e) {
            $message = strtolower($e->getMessage());

            if ((string) $e->getCode() === '23000' || str_contains($message, 'unique')) {
                Log::info('Duplicate Stripe webhook event ignored', [
                    'event_id' => $eventId,
                    'event_type' => $event->type,
                ]);

                return response('Webhook already processed', 200);
            }

            throw $e;
        }

        try {
            DB::transaction(function () use ($event) {
                // Handle the event
                switch ($event->type) {
                    case 'checkout.session.completed':
                        $session = $event->data->object;
                        $this->handleCheckoutCompleted($session);
                        break;

                    case 'payment_intent.succeeded':
                        $paymentIntent = $event->data->object;
                        $this->handlePaymentSuccess($paymentIntent);
                        break;

                    case 'payment_intent.payment_failed':
                        $paymentIntent = $event->data->object;
                        $this->handlePaymentFailure($paymentIntent);
                        break;

                    default:
                        Log::info('Unhandled Stripe webhook event', [
                            'event_type' => $event->type,
                        ]);
                }
            });
        } catch (\Throwable $e) {
            Log::error('Stripe webhook processing failed', [
                'event_id' => $eventId,
                'event_type' => $event->type,
                'error' => $e->getMessage(),
            ]);

            return response('Webhook processing failed', 500);
        }

        $webhookEvent->update(['processed_at' => now()]);

        return response('Webhook received', 200);
    }

    /**
     * Handle successful payment from webhook.
     */
    private function handlePaymentSuccess($paymentIntent): void
    {
        $feeId = $paymentIntent->metadata->fee_id ?? null;

        if (!$feeId) {
            Log::warning('Payment Intent missing fee_id metadata', ['payment_intent_id' => $paymentIntent->id]);
            return;
        }

        $paymentIntentId = $paymentIntent->id ?? null;

        $result = DB::transaction(function () use ($feeId, $paymentIntent, $paymentIntentId) {
            $fee = Fee::query()->lockForUpdate()->find($feeId);

            if (!$fee) {
                Log::warning('Fee not found for payment', [
                    'fee_id' => $feeId,
                    'payment_intent_id' => $paymentIntentId,
                ]);

                return null;
            }

            // Safety guard: if a different intent is already linked, do not overwrite.
            if ($fee->payment_intent_id && $fee->payment_intent_id !== $paymentIntentId) {
                Log::warning('Payment intent mismatch on success handler, aborting update', [
                    'fee_id' => $fee->id,
                    'existing_payment_intent_id' => $fee->payment_intent_id,
                    'incoming_payment_intent_id' => $paymentIntentId,
                ]);

                return null;
            }

            // Idempotency guard: if already paid, no further changes or notifications.
            if ($fee->status === 'paid') {
                return [
                    'newly_paid' => false,
                    'fee' => $fee->fresh(['student.user']),
                ];
            }

            $fee->update([
                'status' => 'paid',
                'paid_date' => now(),
                'payment_method' => $paymentIntent->payment_method_types[0] ?? 'card',
                'payment_processed_at' => now(),
                'payment_intent_id' => $paymentIntentId,
            ]);

            return [
                'newly_paid' => true,
                'fee' => $fee->fresh(['student.user']),
            ];
        });

        if (!$result) {
            return;
        }

        /** @var \App\Models\Fee $fee */
        $fee = $result['fee'];

        if (!$result['newly_paid']) {
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
    private function handlePaymentFailure($paymentIntent): void
    {
        $feeId = $paymentIntent->metadata->fee_id ?? null;
        
        if (!$feeId) {
            return;
        }

        $updated = DB::transaction(function () use ($feeId, $paymentIntent) {
            $fee = Fee::query()->lockForUpdate()->find($feeId);

            if (!$fee || $fee->status === 'paid') {
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

            if ($fee->status !== 'payment_pending') {
                return false;
            }

            $fee->update([
                'status' => 'pending',
                'payment_intent_id' => null,
            ]);

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
    private function handleCheckoutCompleted($session): void
    {
        // When a checkout session completes, Stripe includes the payment_intent id
        $paymentIntentId = $session->payment_intent ?? null;

        if (!$paymentIntentId) {
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
