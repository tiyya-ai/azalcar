<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Http\Controllers\StripeWebhookController;
use App\Models\Transaction;

class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_replay_attempt_is_idempotent()
    {
        // Create a user
        $user = User::factory()->create(['balance' => 0]);

        $controller = new StripeWebhookController();

        $intent = [
            'id' => 'pi_test_123',
            'amount' => 5000,
            'currency' => 'rub',
            'metadata' => ['user_id' => $user->id]
        ];

        // First processing should create a transaction and credit balance
        $res1 = $controller->processPaymentIntent($intent);
        $this->assertTrue($res1);

        $this->assertDatabaseHas('transactions', ['reference_id' => 'pi_test_123']);

        $user->refresh();
        $this->assertEquals(50.00, $user->balance);

        // Replay processing should be ignored (idempotent)
        $res2 = $controller->processPaymentIntent($intent);
        $this->assertTrue($res2);

        // Ensure still only one transaction record
        $count = Transaction::where('reference_id', 'pi_test_123')->count();
        $this->assertEquals(1, $count);

        // Balance remains same
        $user->refresh();
        $this->assertEquals(50.00, $user->balance);
    }
}
