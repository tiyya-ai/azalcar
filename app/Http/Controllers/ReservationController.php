<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Reservation;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    /**
     * Show reservation form
     */
    public function create($slug)
    {
        $listing = Listing::where('slug', $slug)->firstOrFail();

        // Check if listing is available
        if ($listing->status !== 'active') {
            return redirect()->route('listings.show', $slug)
                ->with('error', 'This listing is not available for reservation.');
        }

        // Check if already reserved
        if ($listing->isReserved()) {
            return redirect()->route('listings.show', $slug)
                ->with('error', 'This listing is already reserved.');
        }

        // Check if user is trying to reserve their own listing
        if ($listing->user_id === auth()->id()) {
            return redirect()->route('listings.show', $slug)
                ->with('error', 'You cannot reserve your own listing.');
        }

        // Calculate required wallet balance (10% of listing price)
        $requiredBalance = ($listing->price * 10) / 100;
        $hasEnoughBalance = auth()->user()->balance >= $requiredBalance;

        // Get payment gateway settings
        $settings = \App\Models\Setting::all()->pluck('value', 'key');

        return view('reservations.create', compact('listing', 'requiredBalance', 'hasEnoughBalance', 'settings'));
    }

    /**
     * Store new reservation
     */
    public function store(Request $request, $slug)
    {
        $listing = Listing::where('slug', $slug)->firstOrFail();

        $request->validate([
            'deposit_percentage' => 'required|numeric|min:1|max:10',
            'duration_type' => 'required|in:24,72',
            'payment_method' => 'required|in:wallet,stripe,paypal,bank_transfer,cash_on_delivery',
        ]);

        // PRODUCTION HARDENING: Atomic reservation with pessimistic locking
        // This ensures that if two buyers click "Reserve" simultaneously, only one succeeds.
        // All operations (wallet deduction, reservation creation, listing update) happen
        // in a single transaction with row-level locks to prevent race conditions.

        DB::beginTransaction();
        try {
            // STEP 1: Lock the listing row to prevent concurrent reservations
            // lockForUpdate() acquires an exclusive lock on this row until transaction commits
            $lockedListing = Listing::where('id', $listing->id)
                ->lockForUpdate()
                ->first();

            if (!$lockedListing) {
                DB::rollBack();
                return back()->with('error', 'Listing not found.');
            }

            // STEP 2: Re-check availability under lock (prevents race condition)
            if ($lockedListing->status !== 'active') {
                DB::rollBack();
                return back()->with('error', 'This listing is not available for reservation.');
            }

            if ($lockedListing->isReserved()) {
                DB::rollBack();
                return back()->with('error', 'This listing is already reserved.');
            }

            if ($lockedListing->user_id === auth()->id()) {
                DB::rollBack();
                return back()->with('error', 'You cannot reserve your own listing.');
            }

            // STEP 3: Lock the user row to prevent concurrent wallet mutations
            $lockedUser = \App\Models\User::where('id', auth()->id())
                ->lockForUpdate()
                ->first();

            if (!$lockedUser) {
                DB::rollBack();
                return back()->with('error', 'User not found.');
            }

            // STEP 4: Calculate deposit and verify balance under lock
            $depositData = Reservation::calculateDeposit($lockedListing->price, $request->deposit_percentage);
            $paymentMethod = $request->payment_method;

            // STEP 5: Idempotency check - prevent duplicate reservations
            // Generate unique reference ID from user + listing + session
            $sessionToken = session()->getId();
            $referenceId = 'reservation_' . auth()->id() . '_' . $lockedListing->id . '_' . md5($sessionToken . $lockedListing->id);

            $existingReservation = Reservation::where('reference_id', $referenceId)
                ->where('user_id', auth()->id())
                ->lockForUpdate()
                ->first();

            if ($existingReservation) {
                DB::commit();
                \Illuminate\Support\Facades\Log::info('Duplicate reservation attempt blocked', [
                    'user_id' => auth()->id(),
                    'listing_id' => $lockedListing->id,
                    'reference_id' => $referenceId
                ]);
                return redirect()->route('reservations.show', $existingReservation->id)
                    ->with('info', 'This reservation was already created.');
            }

            // STEP 6: Process payment based on selected method
            $transaction = null;
            $paymentStatus = 'pending';
            
            if ($paymentMethod === 'wallet') {
                // Wallet payment: Duct balance immediately
                if ($lockedUser->balance < $depositData['deposit_amount']) {
                    DB::rollBack();
                    return back()->with('error', 'Insufficient balance for deposit. Please top up your wallet.');
                }

                $lockedUser->balance -= $depositData['deposit_amount'];
                if (!$lockedUser->save()) {
                    DB::rollBack();
                    \Illuminate\Support\Facades\Log::error('Failed to save user balance', [
                        'user_id' => auth()->id(),
                        'new_balance' => $lockedUser->balance
                    ]);
                    return back()->with('error', 'Failed to process wallet deduction.');
                }

                $paymentStatus = 'completed';
                $transaction = Transaction::create([
                    'user_id' => auth()->id(),
                    'listing_id' => $lockedListing->id,
                    'amount' => -$depositData['deposit_amount'],
                    'type' => 'reservation_deposit',
                    'description' => "Reservation deposit for {$lockedListing->title}",
                    'status' => 'completed',
                    'reference_id' => 'txn_' . $referenceId,
                    'currency' => 'RUB',
                    'payment_method' => 'wallet'
                ]);
            } elseif ($paymentMethod === 'stripe') {
                // Stripe: Create payment intent, don't deduct wallet
                $paymentService = new \App\Services\PaymentService();
                $paymentResult = $paymentService->createPaymentIntent(
                    $depositData['deposit_amount'],
                    'rub',
                    [
                        'reservation_reference' => $referenceId,
                        'listing_id' => $lockedListing->id,
                        'user_id' => auth()->id()
                    ]
                );

                if (!$paymentResult['success']) {
                    DB::rollBack();
                    return back()->with('error', 'Failed to initialize Stripe payment: ' . $paymentResult['message']);
                }

                // Store payment intent in session for confirmation
                session(['stripe_payment_intent_' . $referenceId => $paymentResult['client_secret']]);
                
                $transaction = Transaction::create([
                    'user_id' => auth()->id(),
                    'listing_id' => $lockedListing->id,
                    'amount' => -$depositData['deposit_amount'],
                    'type' => 'reservation_deposit',
                    'description' => "Reservation deposit for {$lockedListing->title} (Stripe)",
                    'status' => 'pending',
                    'reference_id' => 'txn_' . $referenceId,
                    'currency' => 'RUB',
                    'payment_method' => 'stripe',
                    'payment_intent_id' => $paymentResult['id']
                ]);
            } elseif ($paymentMethod === 'paypal') {
                // PayPal: Create pending transaction
                $transaction = Transaction::create([
                    'user_id' => auth()->id(),
                    'listing_id' => $lockedListing->id,
                    'amount' => -$depositData['deposit_amount'],
                    'type' => 'reservation_deposit',
                    'description' => "Reservation deposit for {$lockedListing->title} (PayPal)",
                    'status' => 'pending',
                    'reference_id' => 'txn_' . $referenceId,
                    'currency' => 'RUB',
                    'payment_method' => 'paypal'
                ]);
            } elseif ($paymentMethod === 'bank_transfer') {
                // Bank Transfer: Create pending transaction
                $transaction = Transaction::create([
                    'user_id' => auth()->id(),
                    'listing_id' => $lockedListing->id,
                    'amount' => -$depositData['deposit_amount'],
                    'type' => 'reservation_deposit',
                    'description' => "Reservation deposit for {$lockedListing->title} (Bank Transfer)",
                    'status' => 'pending',
                    'reference_id' => 'txn_' . $referenceId,
                    'currency' => 'RUB',
                    'payment_method' => 'bank_transfer'
                ]);
            } elseif ($paymentMethod === 'cash_on_delivery') {
                // Cash on Delivery: Create pending transaction
                $transaction = Transaction::create([
                    'user_id' => auth()->id(),
                    'listing_id' => $lockedListing->id,
                    'amount' => -$depositData['deposit_amount'],
                    'type' => 'reservation_deposit',
                    'description' => "Reservation deposit for {$lockedListing->title} (Cash on Delivery)",
                    'status' => 'pending',
                    'reference_id' => 'txn_' . $referenceId,
                    'currency' => 'RUB',
                    'payment_method' => 'cash_on_delivery'
                ]);
            }

            // STEP 7: Create reservation record
            $reservation = Reservation::create([
                'listing_id' => $lockedListing->id,
                'user_id' => auth()->id(),
                'seller_id' => $lockedListing->user_id,
                'listing_price' => $lockedListing->price,
                'deposit_percentage' => $depositData['deposit_percentage'],
                'deposit_amount' => $depositData['deposit_amount'],
                'reserved_at' => now(),
                'expires_at' => now()->addHours($request->duration_type),
                'duration_hours' => $request->duration_type,
                'status' => $paymentMethod === 'wallet' ? 'active' : 'pending_payment',
                'transaction_id' => $transaction ? $transaction->id : null,
                'reference_id' => $referenceId,
                'payment_method' => $paymentMethod,
            ]);

            // STEP 8: Update listing flags to mark as reserved (only for wallet payments)
            // For other payment methods, listing remains available until payment is confirmed
            if ($paymentMethod === 'wallet') {
                $lockedListing->is_reserved = true;
                $lockedListing->reserved_until = $reservation->expires_at;
                if (!$lockedListing->save()) {
                    DB::rollBack();
                    \Illuminate\Support\Facades\Log::error('Failed to update listing reserved status', [
                        'listing_id' => $lockedListing->id
                    ]);
                    return back()->with('error', 'Failed to reserve listing.');
                }
            }

            // STEP 9: Commit transaction - all operations succeed atomically
            DB::commit();

            // STEP 10: Handle post-commit actions based on payment method
            if ($paymentMethod === 'stripe') {
                // Redirect to Stripe payment page
                return redirect()->route('reservations.stripe.checkout', [
                    'reservation' => $reservation->id,
                    'client_secret' => session('stripe_payment_intent_' . $referenceId)
                ]);
            } elseif ($paymentMethod === 'paypal') {
                // Redirect to PayPal payment page
                return redirect()->route('reservations.paypal.checkout', ['reservation' => $reservation->id]);
            }

            // STEP 11: Send notification (outside transaction to avoid blocking)
            try {
                auth()->user()->notify(new \App\Notifications\ReservationCreated($reservation));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send reservation notification', [
                    'error' => $e->getMessage(),
                    'reservation_id' => $reservation->id
                ]);
                // Don't fail the entire request if notification fails
            }

            // Different success messages based on payment method
            $successMessage = match($paymentMethod) {
                'wallet' => 'Reservation created successfully! You have ' . $request->duration_type . ' hours to complete the purchase.',
                'bank_transfer' => 'Reservation pending! Please complete your bank transfer. Your reservation will be confirmed once payment is received.',
                'cash_on_delivery' => 'Reservation pending! You will pay the deposit when you meet with the seller.',
                default => 'Reservation created! Please complete your payment to confirm the reservation.'
            };

            return redirect()->route('reservations.show', $reservation->id)
                ->with('success', $successMessage);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            // Handle unique constraint violations (e.g., duplicate reference_id)
            if (strpos($e->getMessage(), 'Duplicate entry') !== false || strpos($e->getMessage(), 'UNIQUE constraint failed') !== false) {
                \Illuminate\Support\Facades\Log::warning('Duplicate reservation detected', [
                    'user_id' => auth()->id(),
                    'listing_id' => $listing->id,
                    'error' => $e->getMessage()
                ]);
                return back()->with('error', 'This reservation was already created.');
            }
            \Illuminate\Support\Facades\Log::error('Database error during reservation creation', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'listing_id' => $listing->id
            ]);
            return back()->with('error', 'Failed to create reservation. Please try again.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Unexpected error during reservation creation', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'listing_id' => $listing->id
            ]);
            return back()->with('error', 'Failed to create reservation. Please try again.');
        }
    }

    /**
     * Show user's reservations
     */
    public function index()
    {
        $reservations = Reservation::where('user_id', auth()->id())
            ->with(['listing', 'seller'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('reservations.index', compact('reservations'));
    }

    /**
     * Show specific reservation
     */
    public function show($id)
    {
        $reservation = Reservation::with(['listing', 'seller', 'transaction'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return view('reservations.show', compact('reservation'));
    }

    /**
     * Extend reservation
     */
    public function extend(Request $request, $id)
    {
        $reservation = Reservation::where('user_id', auth()->id())
            ->where('status', 'active')
            ->findOrFail($id);

        // Check if can extend
        if ($reservation->extension_count >= Reservation::MAX_EXTENSIONS) {
            return back()->with('error', 'Maximum extensions reached (3 extensions maximum).');
        }

        // Check if expired
        if ($reservation->isExpired()) {
            return back()->with('error', 'Cannot extend an expired reservation.');
        }

        // Quick pre-check before opening DB transaction
        if (auth()->user()->balance < $reservation->deposit_amount) {
            return back()->with('error', 'Insufficient balance for extension. Please top up your wallet.');
        }

        // IDEMPOTENCY: Generate unique reference for this extension
        $sessionToken = session()->getId();
        $extensionRefId = 'extension_' . $reservation->id . '_' . ($reservation->extension_count + 1) . '_' . md5($sessionToken);

        DB::beginTransaction();
        try {
            // IDEMPOTENCY CHECK: Check if this extension was already processed
            $existingTransaction = Transaction::where('reference_id', $extensionRefId)->first();
            if ($existingTransaction) {
                DB::commit();
                Log::info('Duplicate extension attempt blocked', [
                    'user_id' => auth()->id(),
                    'reservation_id' => $reservation->id,
                    'reference_id' => $extensionRefId
                ]);
                return back()->with('info', 'This extension was already processed.');
            }

            // Deduct extension fee (same as deposit amount)
            if (!auth()->user()->updateBalance($reservation->deposit_amount, 'subtract')) {
                DB::rollBack();
                return back()->with('error', 'Insufficient balance for extension. Please top up your wallet.');
            }

            // Create transaction with reference_id
            Transaction::create([
                'user_id' => auth()->id(),
                'listing_id' => $reservation->listing_id,
                'amount' => -$reservation->deposit_amount,
                'type' => 'reservation_extension',
                'description' => "Extension #" . ($reservation->extension_count + 1) . " for reservation #{$reservation->id}",
                'status' => 'completed',
                'reference_id' => $extensionRefId,
                'currency' => 'RUB',
                'payment_method' => 'wallet'
            ]);

            // Extend reservation
            $reservation->extend();

            // Update listing reserved_until
            $reservation->listing->update([
                'reserved_until' => $reservation->expires_at,
            ]);

            DB::commit();

            return back()->with('success', 'Reservation extended successfully! New expiry: ' . $reservation->expires_at->format('M d, Y H:i'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reservation extension failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'reservation_id' => $reservation->id
            ]);
            return back()->with('error', 'Failed to extend reservation. Please try again.');
        }
    }

    /**
     * Cancel reservation
     */
    public function cancel($id)
    {
        $reservation = Reservation::where('user_id', auth()->id())
            ->where('status', 'active')
            ->findOrFail($id);

        DB::beginTransaction();
        try {
            // Forfeit deposit (50/50 split)
            $forfeiture = $reservation->forfeitDeposit();

            // Credit seller's share
            if (!$reservation->seller->updateBalance($forfeiture['seller_share'], 'add')) {
                \Illuminate\Support\Facades\Log::error('Failed to credit seller share on reservation completion', [
                    'seller_id' => $reservation->seller->id,
                    'amount' => $forfeiture['seller_share'],
                    'reservation_id' => $reservation->id
                ]);
                DB::rollBack();
                return back()->with('error', 'Failed to process seller payment. Please contact support.');
            }

            // Website keeps the other 50%
            // Create transaction records
            Transaction::create([
                'user_id' => $reservation->seller_id,
                'listing_id' => $reservation->listing_id,
                'amount' => $forfeiture['seller_share'],
                'type' => 'reservation_forfeiture_seller',
                'description' => "Forfeiture share from cancelled reservation #{$reservation->id}",
                'status' => 'completed',
            ]);

            DB::commit();

            return redirect()->route('reservations.index')
                ->with('warning', 'Reservation cancelled. Deposit forfeited (50% to seller, 50% to platform).');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel reservation. Please try again.');
        }
    }

    /**
     * Complete reservation (purchase completed)
     * IDEMPOTENT: Safe to call multiple times
     */
    public function complete($id)
    {
        $reservation = Reservation::where('user_id', auth()->id())
            ->findOrFail($id);

        // Idempotency check: if already completed, return success
        if ($reservation->status !== 'active') {
            return redirect()->route('reservations.show', $reservation->id)
                ->with('info', 'This reservation was already completed.');
        }

        DB::beginTransaction();
        try {
            // Re-check status under transaction to prevent race conditions
            $reservation = Reservation::where('id', $reservation->id)
                ->lockForUpdate()
                ->first();
            
            if ($reservation->status !== 'active') {
                DB::commit();
                return redirect()->route('reservations.show', $reservation->id)
                    ->with('info', 'This reservation was already completed.');
            }

            // Mark reservation as completed
            $reservation->complete();

            // Update listing status to sold
            $reservation->listing->update([
                'status' => 'sold',
            ]);

            // Check if commission already exists (idempotency)
            $existingCommission = \App\Models\Commission::where('listing_id', $reservation->listing_id)
                ->where('seller_id', $reservation->seller_id)
                ->first();
            
            if (!$existingCommission) {
                // Create commission record only if it doesn't exist
                $commissionData = \App\Models\Commission::calculateCommission($reservation->listing_price);
                
                \App\Models\Commission::create([
                    'listing_id' => $reservation->listing_id,
                    'seller_id' => $reservation->seller_id,
                    'listing_price' => $reservation->listing_price,
                    'commission_percentage' => $commissionData['commission_percentage'],
                    'commission_amount' => $commissionData['commission_amount'],
                    'commission_cap' => $commissionData['commission_cap'],
                    'final_commission' => $commissionData['final_commission'],
                    'status' => 'pending',
                ]);
            }

            DB::commit();

            return redirect()->route('reservations.show', $reservation->id)
                ->with('success', 'Purchase completed! The listing has been marked as sold.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Reservation completion failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'reservation_id' => $id
            ]);
            return back()->with('error', 'Failed to complete purchase. Please try again.');
        }
    }
}
