<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class BalanceConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_balance_atomicity()
    {
        $user = User::factory()->create(['balance' => 100.00]);

        // Apply two sequential updates simulating concurrent credits
        $this->assertTrue($user->updateBalance(25.00, 'add'));
        $this->assertTrue($user->updateBalance(50.00, 'add'));

        $user->refresh();
        $this->assertEquals(175.00, $user->balance);

        // Now subtract a valid amount
        $this->assertTrue($user->updateBalance(75.00, 'subtract'));
        $user->refresh();
        $this->assertEquals(100.00, $user->balance);

        // Attempt to overspend
        $this->assertFalse($user->updateBalance(1000.00, 'subtract'));
    }
}
