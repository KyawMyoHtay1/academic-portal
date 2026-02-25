<?php

namespace Tests\Feature;

use App\Models\Fee;
use App\Models\Student;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class PaymentWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.stripe.webhook.secret', 'whsec_test_secret');
    }

    public function test_payment_intent_succeeded_is_idempotent_for_duplicate_event_delivery(): void
    {
        [$user, $student] = $this->createStudentUser();

        $fee = Fee::create([
            'student_id' => $student->id,
            'amount' => 1500,
            'description' => 'Semester fee',
            'status' => 'payment_pending',
            'due_date' => now()->toDateString(),
            'payment_intent_id' => 'pi_test_123',
        ]);

        $event = [
            'id' => 'evt_payment_success_001',
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'id' => 'pi_test_123',
                    'status' => 'succeeded',
                    'metadata' => [
                        'fee_id' => (string) $fee->id,
                    ],
                    'payment_method_types' => ['card'],
                ],
            ],
        ];

        $first = $this->postStripeWebhookEvent($event);
        $first->assertOk();

        $second = $this->postStripeWebhookEvent($event);
        $second->assertOk();

        $this->assertDatabaseHas('fees', [
            'id' => $fee->id,
            'status' => 'paid',
            'payment_intent_id' => 'pi_test_123',
        ]);
        $this->assertDatabaseCount('stripe_webhook_events', 1);
        $this->assertDatabaseCount('notifications', 1);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $user->id,
            'notifiable_type' => User::class,
        ]);
        $this->assertNotNull($fee->fresh()->payment_processed_at);
    }

    public function test_payment_failed_webhook_does_not_revert_already_paid_fee(): void
    {
        [, $student] = $this->createStudentUser();

        $fee = Fee::create([
            'student_id' => $student->id,
            'amount' => 800,
            'description' => 'Lab fee',
            'status' => 'paid',
            'due_date' => now()->toDateString(),
            'paid_date' => now()->toDateString(),
            'payment_intent_id' => 'pi_paid_001',
            'payment_processed_at' => now(),
        ]);

        $event = [
            'id' => 'evt_payment_failed_001',
            'type' => 'payment_intent.payment_failed',
            'data' => [
                'object' => [
                    'id' => 'pi_paid_001',
                    'status' => 'requires_payment_method',
                    'metadata' => [
                        'fee_id' => (string) $fee->id,
                    ],
                ],
            ],
        ];

        $response = $this->postStripeWebhookEvent($event);
        $response->assertOk();

        $this->assertDatabaseHas('fees', [
            'id' => $fee->id,
            'status' => 'paid',
            'payment_intent_id' => 'pi_paid_001',
        ]);
        $this->assertDatabaseCount('stripe_webhook_events', 1);
    }

    public function test_reset_pending_payment_reverts_to_pending_even_without_payment_intent_id(): void
    {
        [, $student] = $this->createStudentUser();

        $fee = Fee::create([
            'student_id' => $student->id,
            'amount' => 550,
            'description' => 'Library fee',
            'status' => 'payment_pending',
            'due_date' => now()->toDateString(),
            'payment_intent_id' => null,
        ]);

        $updated = app(PaymentService::class)->resetPendingPayment($fee);

        $this->assertTrue($updated);
        $this->assertDatabaseHas('fees', [
            'id' => $fee->id,
            'status' => 'pending',
            'payment_intent_id' => null,
        ]);
    }

    public function test_invalid_webhook_signature_is_rejected(): void
    {
        $event = [
            'id' => 'evt_invalid_sig',
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'id' => 'pi_any',
                    'metadata' => ['fee_id' => '1'],
                ],
            ],
        ];

        $payload = json_encode($event, JSON_THROW_ON_ERROR);

        $response = $this->call(
            'POST',
            route('stripe.webhook'),
            [],
            [],
            [],
            [
                'HTTP_STRIPE_SIGNATURE' => 't=1,v1=invalid',
                'CONTENT_TYPE' => 'application/json',
            ],
            $payload
        );

        $response->assertStatus(400);
        $this->assertDatabaseCount('stripe_webhook_events', 0);
    }

    private function postStripeWebhookEvent(array $event): TestResponse
    {
        $payload = json_encode($event, JSON_THROW_ON_ERROR);
        $timestamp = time();
        $secret = (string) config('services.stripe.webhook.secret');
        $signature = hash_hmac('sha256', $timestamp.'.'.$payload, $secret);
        $signatureHeader = "t={$timestamp},v1={$signature}";

        return $this->call(
            'POST',
            route('stripe.webhook'),
            [],
            [],
            [],
            [
                'HTTP_STRIPE_SIGNATURE' => $signatureHeader,
                'CONTENT_TYPE' => 'application/json',
            ],
            $payload
        );
    }

    /**
     * @return array{0: \App\Models\User, 1: \App\Models\Student}
     */
    private function createStudentUser(): array
    {
        $user = User::factory()->create([
            'role' => 'student',
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'student_no' => 'STU'.str_pad((string) $user->id, 6, '0', STR_PAD_LEFT),
            'full_name' => $user->name,
            'email' => $user->email,
            'programme' => 'BSc Computing',
            'intake_year' => '2026',
        ]);

        return [$user, $student];
    }
}
