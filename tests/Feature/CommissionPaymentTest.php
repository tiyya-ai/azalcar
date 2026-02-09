<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Commission;
use App\Models\Listing;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class CommissionPaymentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test commission payment idempotency
     */
    public function test_commission_payment_is_idempotent()
    {
        // Create admin user
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Create seller with balance
        $seller = User::factory()->create([
            'role' => 'vendor',
            'balance' => 500000 // 500k RUB
        ]);
        
        // Create listing
        $listing = Listing::factory()->create([
            'user_id' => $seller->id,
            'price' => 10000000 // 10M RUB
        ]);
        
        // Create commission (3% = 300k RUB)
        $commission = Commission::create([
            'listing_id' => $listing->id,
            'seller_id' => $seller->id,
            'listing_price' => 10000000,
            'commission_percentage' => 3.00,
            'commission_amount' => 300000,
            'commission_cap' => 900000,
            'final_commission' => 300000,
            'status' => 'pending',
        ]);
        
        $this->actingAs($admin);
        
        // First payment
        $result1 = $commission->markAsPaid();
        $this->assertTrue($result1);
        
        // Verify seller balance was deducted
        $seller->refresh();
        $this->assertEquals(200000, $seller->balance); // 500k - 300k = 200k
        
        // Verify transaction was created
        $this->assertDatabaseHas('transactions', [
            'user_id' => $seller->id,
            'type' => 'commission_payment',
            'amount' => -300000,
        ]);
        
        // Attempt second payment (should be idempotent)
        $result2 = $commission->markAsPaid();
        $this->assertFalse($result2); // Should return false (already paid)
        
        // Verify balance wasn't deducted again
        $seller->refresh();
        $this->assertEquals(200000, $seller->balance); // Still 200k
        
        // Verify only ONE transaction exists
        $transactionCount = Transaction::where('user_id', $seller->id)
            ->where('type', 'commission_payment')
            ->count();
        $this->assertEquals(1, $transactionCount);
    }

    /**
     * Test commission payment fails if seller has insufficient balance
     */
    public function test_commission_payment_fails_with_insufficient_balance()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $seller = User::factory()->create([
            'role' => 'vendor',
            'balance' => 100000 // Only 100k RUB
        ]);
        
        $listing = Listing::factory()->create(['user_id' => $seller->id]);
        
        $commission = Commission::create([
            'listing_id' => $listing->id,
            'seller_id' => $seller->id,
            'listing_price' => 10000000,
            'commission_percentage' => 3.00,
            'commission_amount' => 300000,
            'commission_cap' => 900000,
            'final_commission' => 300000, // Needs 300k but only has 100k
            'status' => 'pending',
        ]);
        
        $this->actingAs($admin);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('insufficient balance');
        
        $commission->markAsPaid();
        
        // Verify balance unchanged
        $seller->refresh();
        $this->assertEquals(100000, $seller->balance);
        
        // Verify commission still pending
        $commission->refresh();
        $this->assertEquals('pending', $commission->status);
    }

    /**
     * Test admin audit log is created
     */
    public function test_commission_payment_creates_audit_log()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $seller = User::factory()->create([
            'role' => 'vendor',
            'balance' => 500000
        ]);
        
        $listing = Listing::factory()->create(['user_id' => $seller->id]);
        
        $commission = Commission::create([
            'listing_id' => $listing->id,
            'seller_id' => $seller->id,
            'listing_price' => 10000000,
            'commission_percentage' => 3.00,
            'commission_amount' => 300000,
            'commission_cap' => 900000,
            'final_commission' => 300000,
            'status' => 'pending',
        ]);
        
        $this->actingAs($admin);
        
        $commission->markAsPaid();
        
        // Verify audit log was created
        $this->assertDatabaseHas('admin_audit_logs', [
            'admin_id' => $admin->id,
            'action_type' => 'commission_marked_paid',
            'target_type' => 'Commission',
            'target_id' => $commission->id,
        ]);
    }
}
