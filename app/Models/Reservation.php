<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Reservation extends Model
{
    protected $fillable = [
        'listing_id',
        'user_id',
        'seller_id',
        'listing_price',
        'deposit_percentage',
        'deposit_amount',
        'reserved_at',
        'expires_at',
        'extension_count',
        'duration_hours',
        'status',
        'deposit_forfeited',
        'forfeiture_amount',
        'forfeited_at',
        'transaction_id',
        'notes',
        'reference_id', // For idempotency
    ];

    protected $casts = [
        'reserved_at' => 'datetime',
        'expires_at' => 'datetime',
        'forfeited_at' => 'datetime',
        'deposit_forfeited' => 'boolean',
    ];

    const MIN_DEPOSIT = 100000; // 100,000 won
    const MAX_EXTENSIONS = 3;
    const STANDARD_DURATION = 24; // hours
    const EXTENDED_DURATION = 72; // hours (3 days)

    /**
     * Calculate deposit amount
     * Minimum 1%, Maximum 10%, Minimum amount 100,000 won
     */
    public static function calculateDeposit($listingPrice, $percentage)
    {
        // Ensure percentage is between 1-10%
        $percentage = max(1, min(10, $percentage));
        
        $depositAmount = ($listingPrice * $percentage) / 100;
        
        // Ensure minimum deposit of 100,000 won
        $depositAmount = max(self::MIN_DEPOSIT, $depositAmount);
        
        return [
            'deposit_percentage' => $percentage,
            'deposit_amount' => $depositAmount,
        ];
    }

    /**
     * Check if user has sufficient wallet balance (10% of listing price)
     */
    public static function checkWalletBalance($user, $listingPrice)
    {
        $requiredBalance = ($listingPrice * 10) / 100;
        return $user->balance >= $requiredBalance;
    }

    /**
     * Create a new reservation
     */
    public static function createReservation($listing, $user, $depositPercentage, $durationHours = 24)
    {
        $depositData = self::calculateDeposit($listing->price, $depositPercentage);
        
        return self::create([
            'listing_id' => $listing->id,
            'user_id' => $user->id,
            'seller_id' => $listing->user_id,
            'listing_price' => $listing->price,
            'deposit_percentage' => $depositData['deposit_percentage'],
            'deposit_amount' => $depositData['deposit_amount'],
            'reserved_at' => now(),
            'expires_at' => now()->addHours($durationHours),
            'duration_hours' => $durationHours,
            'status' => 'active',
        ]);
    }

    /**
     * Extend reservation (max 3 times)
     */
    public function extend()
    {
        if ($this->extension_count >= self::MAX_EXTENSIONS) {
            return false;
        }

        $this->update([
            'expires_at' => $this->expires_at->addHours(self::STANDARD_DURATION),
            'extension_count' => $this->extension_count + 1,
        ]);

        return true;
    }

    /**
     * Check if reservation is expired
     */
    public function isExpired()
    {
        return $this->expires_at->isPast() && $this->status === 'active';
    }

    /**
     * Forfeit deposit (50/50 split between website and seller)
     * ATOMIC: Wrapped in transaction with balance updates and transaction records
     */
    public function forfeitDeposit()
    {
        return \Illuminate\Support\Facades\DB::transaction(function () {
            // Lock reservation to prevent race conditions
            $reservation = static::where('id', $this->id)->lockForUpdate()->first();
            
            if (!$reservation) {
                throw new \Exception('Reservation not found');
            }
            
            // Update reservation status
            $reservation->update([
                'status' => 'expired',
                'deposit_forfeited' => true,
                'forfeiture_amount' => $reservation->deposit_amount,
                'forfeited_at' => now(),
            ]);

            // Split 50/50
            $sellerShare = $reservation->deposit_amount / 2;
            $websiteShare = $reservation->deposit_amount / 2;

            // Credit seller's share to their balance
            $seller = $reservation->seller;
            if (!$seller) {
                throw new \Exception('Seller not found for reservation #' . $reservation->id);
            }
            
            if (!$seller->updateBalance($sellerShare, 'add')) {
                throw new \Exception('Failed to credit seller balance for forfeiture');
            }

            // Create transaction record for seller's share
            \App\Models\Transaction::create([
                'user_id' => $reservation->seller_id,
                'listing_id' => $reservation->listing_id,
                'amount' => $sellerShare,
                'type' => 'reservation_forfeiture_seller',
                'description' => "Forfeiture share (50%) from cancelled reservation #{$reservation->id}",
                'status' => 'completed',
                'currency' => 'RUB',
                'payment_method' => 'forfeiture'
            ]);

            // Create transaction record for website's share (for accounting)
            \App\Models\Transaction::create([
                'user_id' => null, // Platform transaction
                'listing_id' => $reservation->listing_id,
                'amount' => $websiteShare,
                'type' => 'reservation_forfeiture_platform',
                'description' => "Forfeiture share (50%) from cancelled reservation #{$reservation->id} - Platform revenue",
                'status' => 'completed',
                'currency' => 'RUB',
                'payment_method' => 'forfeiture'
            ]);

            // Release the listing
            if ($reservation->listing) {
                $reservation->listing->update([
                    'is_reserved' => false,
                    'reserved_until' => null,
                ]);
            }

            \Illuminate\Support\Facades\Log::info('Reservation deposit forfeited', [
                'reservation_id' => $reservation->id,
                'seller_id' => $reservation->seller_id,
                'seller_share' => $sellerShare,
                'platform_share' => $websiteShare,
                'total_forfeited' => $reservation->deposit_amount
            ]);

            return [
                'seller_share' => $sellerShare,
                'website_share' => $websiteShare,
            ];
        });
    }

    /**
     * Complete reservation (purchase completed)
     */
    public function complete()
    {
        $this->update([
            'status' => 'completed',
        ]);

        // Update listing
        $this->listing->update([
            'is_reserved' => false,
            'reserved_until' => null,
        ]);
    }

    /**
     * Cancel reservation
     */
    public function cancel()
    {
        $this->update([
            'status' => 'cancelled',
        ]);

        // Update listing
        $this->listing->update([
            'is_reserved' => false,
            'reserved_until' => null,
        ]);
    }

    /**
     * Relationships
     */
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'active')
                     ->where('expires_at', '<', now());
    }
}
