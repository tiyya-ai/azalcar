<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $fillable = [
        'listing_id',
        'seller_id',
        'listing_price',
        'commission_percentage',
        'commission_amount',
        'commission_cap',
        'final_commission',
        'status',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    /**
     * Calculate commission based on listing price
     * 3% of listing price, capped at 900,000 won (3% of 30M won)
     * VALIDATES: Listing price must be positive and within reasonable bounds
     */
    public static function calculateCommission($listingPrice)
    {
        // Validate listing price
        if (!is_numeric($listingPrice) || $listingPrice <= 0) {
            throw new \InvalidArgumentException('Listing price must be a positive number');
        }
        
        if ($listingPrice > 1000000000) { // 1 billion RUB max
            throw new \InvalidArgumentException('Listing price exceeds maximum allowed value (1 billion RUB)');
        }
        
        $percentage = 3.00; // 3%
        $cap = 900000.00; // Maximum commission (30M won * 3%)
        
        $commissionAmount = ($listingPrice * $percentage) / 100;
        $finalCommission = min($commissionAmount, $cap);
        
        return [
            'commission_percentage' => $percentage,
            'commission_amount' => $commissionAmount,
            'commission_cap' => $cap,
            'final_commission' => $finalCommission,
        ];
    }

    /**
     * Get the listing that this commission is for
     */
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    /**
     * Get the seller who owes this commission
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Get the transaction for this commission payment
     */
    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    /**
     * Mark commission as paid - PRODUCTION SECURE VERSION
     * - Deducts from seller balance
     * - Creates transaction record
     * - Idempotent (prevents double execution)
     * - Logs admin action
     */
    public function markAsPaid()
    {
        return \Illuminate\Support\Facades\DB::transaction(function () {
            // Idempotency check - reload with lock to prevent race conditions
            $commission = static::where('id', $this->id)->lockForUpdate()->first();
            
            if (!$commission) {
                throw new \Exception('Commission not found');
            }
            
            if ($commission->status === 'paid') {
                \Illuminate\Support\Facades\Log::warning('Attempted to mark already-paid commission', [
                    'commission_id' => $this->id,
                    'admin_id' => auth()->id()
                ]);
                return false; // Already paid, idempotent return
            }
            
            // Deduct commission from seller balance
            $seller = $commission->seller;
            if (!$seller) {
                throw new \Exception('Seller not found for commission #' . $this->id);
            }
            
            if (!$seller->updateBalance($commission->final_commission, 'subtract')) {
                throw new \Exception('Seller has insufficient balance to pay commission. Balance: ' . $seller->balance . ', Required: ' . $commission->final_commission);
            }
            
            // Create transaction record with unique reference
            $referenceId = 'commission_payment_' . $commission->id . '_' . now()->timestamp;
            
            Transaction::create([
                'user_id' => $commission->seller_id,
                'listing_id' => $commission->listing_id,
                'amount' => -$commission->final_commission,
                'currency' => 'RUB',
                'type' => 'commission_payment',
                'description' => "Commission payment for listing #{$commission->listing_id} (Sale price: " . number_format($commission->listing_price) . " RUB)",
                'status' => 'completed',
                'reference_id' => $referenceId,
                'payment_method' => 'balance_deduction'
            ]);
            
            // Update commission status
            $commission->update([
                'status' => 'paid',
                'paid_at' => now(),
                'notes' => ($commission->notes ?? '') . "\nPaid by admin #" . auth()->id() . " on " . now()->toDateTimeString()
            ]);
            
            // Log admin action in audit trail
            if (class_exists('App\\Models\\AdminAuditLog')) {
                \App\Models\AdminAuditLog::create([
                    'admin_id' => auth()->id(),
                    'action_type' => 'commission_marked_paid',
                    'target_type' => 'Commission',
                    'target_id' => $commission->id,
                    'description' => "Marked commission #{$commission->id} as paid. Amount: {$commission->final_commission} RUB. Seller: #{$commission->seller_id}",
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'metadata' => json_encode([
                        'commission_id' => $commission->id,
                        'seller_id' => $commission->seller_id,
                        'listing_id' => $commission->listing_id,
                        'amount' => $commission->final_commission,
                        'reference_id' => $referenceId
                    ])
                ]);
            }
            
            \Illuminate\Support\Facades\Log::info('Commission marked as paid', [
                'commission_id' => $commission->id,
                'seller_id' => $commission->seller_id,
                'amount' => $commission->final_commission,
                'admin_id' => auth()->id()
            ]);
            
            return true;
        });
    }

    /**
     * Scope to get pending commissions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get paid commissions
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
}
