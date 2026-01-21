<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Notifications\FeeStatusUpdated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                
                if ($session->payment_status === 'paid' && $fee->payment_intent_id) {
                    $paymentIntent = PaymentIntent::retrieve($fee->payment_intent_id);
                    
                    if ($paymentIntent->status === 'succeeded') {
                        // Update fee status if not already updated by webhook
                        if ($fee->status !== 'paid') {
                            $fee->update([
                                'status' => 'paid',
                                'paid_date' => now(),
                                'payment_method' => $paymentIntent->payment_method_types[0] ?? 'card',
                                'payment_processed_at' => now(),
                            ]);

                            // Notify student
                            if ($student->user) {
                                $student->user->notify(new FeeStatusUpdated($fee));
                            }
                        }

                        return redirect()
                            ->route('student.fees.index')
                            ->with('success', "Payment of £{$fee->amount} completed successfully!");
                    }
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
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\Exception $e) {
            Log::error('Stripe webhook signature verification failed: ' . $e->getMessage());
            return response('Webhook signature verification failed', 400);
        }

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
                Log::info('Unhandled Stripe webhook event: ' . $event->type);
        }

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

        $fee = Fee::find($feeId);
        if (!$fee) {
            Log::warning('Fee not found for payment', ['fee_id' => $feeId, 'payment_intent_id' => $paymentIntent->id]);
            return;
        }

        // Update fee status
        $fee->update([
            'status' => 'paid',
            'paid_date' => now(),
            'payment_method' => $paymentIntent->payment_method_types[0] ?? 'card',
            'payment_processed_at' => now(),
        ]);

        // Notify student
        if ($fee->student && $fee->student->user) {
            $fee->student->user->notify(new FeeStatusUpdated($fee));
        }

        Log::info('Payment processed successfully via webhook', [
            'fee_id' => $fee->id,
            'payment_intent_id' => $paymentIntent->id,
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

        $fee = Fee::find($feeId);
        if ($fee && $fee->status === 'payment_pending') {
            $fee->update([
                'status' => 'pending',
                'payment_intent_id' => null,
            ]);

            Log::info('Payment failed via webhook', [
                'fee_id' => $fee->id,
                'payment_intent_id' => $paymentIntent->id,
            ]);
        }
    }
}
