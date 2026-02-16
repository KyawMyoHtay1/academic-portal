<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Stripe\Exception\ApiErrorException;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentService $paymentService) {}

    /**
     * Create a Stripe checkout session for a fee payment.
     */
    public function checkout(Fee $fee)
    {
        $this->authorize('checkout', $fee);

        $user = Auth::user();
        $student = $user->student;

        if (! $student || $fee->student_id !== $student->id) {
            return redirect()
                ->route('student.fees.index')
                ->with('error', 'Unauthorized access.');
        }

        if ($fee->status === Fee::STATUS_PAID) {
            return redirect()
                ->route('student.fees.index')
                ->with('error', 'This fee has already been paid.');
        }

        try {
            $checkoutSession = $this->paymentService->createCheckoutSession($fee, $student);
            $this->paymentService->markCheckoutStarted($fee, (string) $checkoutSession->payment_intent, $student->id);

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
        $this->authorize('view', $fee);

        $user = Auth::user();
        $student = $user->student;

        if (! $student || $fee->student_id !== $student->id) {
            abort(403, 'Unauthorized');
        }

        $sessionId = $request->query('session_id');

        if ($sessionId) {
            try {
                $paid = $this->paymentService->confirmPaymentFromSession($fee, (string) $sessionId);
                if ($paid) {
                    Log::info('payment.success_redirect_confirmed', [
                        'fee_id' => $fee->id,
                        'student_id' => $student->id,
                        'payment_intent_id' => $fee->fresh()->payment_intent_id,
                    ]);

                    return redirect()
                        ->route('student.fees.index')
                        ->with('success', "Payment of GBP {$fee->amount} completed successfully!");
                }
            } catch (ApiErrorException $e) {
                Log::error('Stripe Session verification failed: '.$e->getMessage());
            }
        }

        if ($fee->fresh()->status === Fee::STATUS_PAID) {
            return redirect()
                ->route('student.fees.index')
                ->with('success', "Payment of GBP {$fee->amount} completed successfully!");
        }

        return redirect()
            ->route('student.fees.index')
            ->with('info', 'Payment is being processed. You will be notified once confirmed.');
    }

    /**
     * Handle cancelled payment.
     */
    public function cancel(Fee $fee): RedirectResponse
    {
        $this->authorize('view', $fee);

        $user = Auth::user();
        $student = $user->student;

        if (! $student || $fee->student_id !== $student->id) {
            abort(403, 'Unauthorized');
        }

        if ($this->paymentService->resetPendingPayment($fee)) {
            Log::info('payment.checkout_cancelled', [
                'fee_id' => $fee->id,
                'student_id' => $student->id,
            ]);
        }

        return redirect()
            ->route('student.fees.index')
            ->with('info', 'Payment was cancelled. You can try again anytime.');
    }

    /**
     * Handle Stripe webhook for payment confirmation.
     */
    public function webhook(Request $request): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = (string) config('services.stripe.webhook.secret');

        try {
            $event = $this->paymentService->constructWebhookEvent($payload, $sigHeader, $endpointSecret);
        } catch (\Exception $e) {
            Log::error('Stripe webhook signature verification failed: '.$e->getMessage());

            return response('Webhook signature verification failed', 400);
        }

        $eventId = $event->id ?? null;
        if (! $eventId) {
            Log::warning('Stripe webhook payload missing event id');

            return response('Webhook event id missing', 400);
        }

        $decodedPayload = json_decode($payload, true) ?? [];

        try {
            $webhookEvent = $this->paymentService->recordWebhookEvent(
                $eventId,
                (string) $event->type,
                $decodedPayload
            );
            if (! $webhookEvent) {
                return response('Webhook already processed', 200);
            }
        } catch (\Throwable $e) {
            Log::error('Stripe webhook event logging failed', [
                'event_id' => $eventId,
                'event_type' => $event->type,
                'error' => $e->getMessage(),
            ]);

            return response('Webhook event logging failed', 500);
        }

        try {
            $this->paymentService->processWebhookEvent($event);
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
}
