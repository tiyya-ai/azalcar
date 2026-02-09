<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Transaction;

class SignedStripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_signed_webhook_creates_transaction_idempotent()
    {
        $secret = 'whsec_testsecret';
        // Ensure the app config uses the same webhook secret for fallback verification
        config(['services.stripe.webhook.secret' => $secret]);
        // Prepare user
        $user = User::factory()->create(['balance' => 0]);

        $payload = json_encode([
            'id' => 'evt_test_1',
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'id' => 'pi_test_456',
                    'amount' => 2000,
                    'currency' => 'usd',
                    'metadata' => ['user_id' => $user->id]
                ]
            ]
        ]);

        // Build signature header. Prefer Stripe SDK helper when available.
        if (class_exists('\\Stripe\\Webhook') && method_exists('\\Stripe\\Webhook', 'generateTestHeaderString')) {
            $sigHeader = \Stripe\Webhook::generateTestHeaderString($payload, $secret);
        } else {
            $t = time();
            $signed = hash_hmac('sha256', $t . '.' . $payload, $secret);
            $sigHeader = "t={$t},v1={$signed}";
        }

        // First delivery
        $response = $this->withHeaders(['Stripe-Signature' => $sigHeader])
                 ->postJson('/webhook/stripe', json_decode($payload, true));

        $response->assertStatus(200);
        $this->assertDatabaseHas('transactions', ['reference_id' => 'pi_test_456']);
        $user->refresh();
        $this->assertEquals(20.00, $user->balance);

        // Replay attempt
        $response2 = $this->withHeaders(['Stripe-Signature' => $sigHeader])
                  ->postJson('/webhook/stripe', json_decode($payload, true));
        $response2->assertStatus(200);

        $count = Transaction::where('reference_id', 'pi_test_456')->count();
        $this->assertEquals(1, $count);

        $user->refresh();
        $this->assertEquals(20.00, $user->balance);
    }
}
