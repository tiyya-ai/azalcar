<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\User;

class StripeWebhookController extends Controller
{
    /**
     * Entrypoint for Stripe webhooks. Verifies signature and handles events.
     */
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook.secret');

        try {
            if (class_exists('\\Stripe\\Webhook')) {
                $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $secret);
            } else {
                // Fallback verification if stripe/stripe-php not installed (use HMAC SHA256)
                $valid = $this->verifyStripeSignatureFallback($payload, $sigHeader, $secret);
                if (!$valid) {
                    Log::warning('Stripe webhook fallback signature verification failed');
                    return response('Invalid signature', 400);
                }
                $event = json_decode($payload);
            }
        } catch (\Exception $e) {
            Log::warning('Stripe webhook signature verification failed', ['error' => $e->getMessage()]);
            return response('Invalid signature', 400);
        }

        if (empty($event->type)) {
            return response('Ignored', 200);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $intent = $event->data->object;
                $this->processPaymentIntent($intent);
                break;

            // Handle other relevant events here if needed
            default:
                Log::info('Unhandled stripe event', ['type' => $event->type]);
        }

        return response('OK', 200);
    }

    /**
     * Process a PaymentIntent object (array/object) for idempotent crediting.
     * This method is public so tests can call it without needing actual Stripe signatures.
     */
    public function processPaymentIntent($intent): bool
    {
        // Normalize to array/object access
        $paymentIntentId = $intent->id ?? ($intent['id'] ?? null);
        $amountCents = $intent->amount ?? ($intent['amount'] ?? 0);
        $currency = strtoupper($intent->currency ?? ($intent['currency'] ?? '')); 
            $metadata = [];
            if (isset($intent->metadata)) {
                // Stripe SDK may return a StripeObject; normalize via JSON round-trip to get an associative array
                if (is_array($intent->metadata)) {
                    $metadata = (array) $intent->metadata;
                } else {
                    $metadata = json_decode(json_encode($intent->metadata), true) ?? [];
                }
            } elseif (isset($intent['metadata'])) {
                $metadata = (array) $intent['metadata'];
            }

        $amount = ((float) $amountCents) / 100.0;

        if (!$paymentIntentId) {
            Log::warning('payment_intent.succeeded missing id', ['intent' => $intent]);
            return false;
        }

        // Idempotent processing: ignore if transaction already exists
        if (Transaction::where('reference_id', $paymentIntentId)->exists()) {
            Log::info('Duplicate payment_intent received, ignoring', ['reference_id' => $paymentIntentId]);
            return true;
        }

        // Attempt to persist transaction and credit user atomically
        try {
            DB::beginTransaction();

            // Re-check under transaction
            if (Transaction::where('reference_id', $paymentIntentId)->lockForUpdate()->exists()) {
                DB::commit();
                Log::info('Duplicate detected under lock, ignoring', ['reference_id' => $paymentIntentId]);
                return true;
            }

            // Determine user id from metadata if provided
            $userId = $metadata['user_id'] ?? null;
            $listingId = $metadata['listing_id'] ?? null;

            // Create transaction record
            $tx = Transaction::create([
                'user_id' => $userId,
                'listing_id' => $listingId,
                'amount' => $amount,
                'currency' => $currency,
                'type' => 'credit',
                'description' => 'Wallet top-up via Stripe (webhook)',
                'reference_id' => $paymentIntentId,
                'payment_method' => 'stripe',
                'status' => 'completed'
            ]);

            // If user exists, credit balance using atomic method
            if ($userId && ($user = User::find($userId))) {
                if (!$user->updateBalance($amount, 'add')) {
                    // Mark transaction failed and rollback
                    $tx->update(['status' => 'failed']);
                    DB::rollBack();
                    Log::error('Failed to credit user balance after payment intent', ['user_id' => $userId, 'amount' => $amount, 'reference_id' => $paymentIntentId]);
                    return false;
                }
            } else {
                Log::warning('Stripe payment intent has no valid user metadata', ['reference_id' => $paymentIntentId, 'metadata' => $metadata]);
            }

            DB::commit();
            Log::info('Processed payment_intent.succeeded', ['reference_id' => $paymentIntentId, 'amount' => $amount, 'currency' => $currency]);
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            // Handle unique constraint races — treat as idempotent
            Log::warning('QueryException processing payment intent (possible duplicate)', ['error' => $e->getMessage(), 'reference_id' => $paymentIntentId]);
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Exception processing payment intent', ['error' => $e->getMessage(), 'reference_id' => $paymentIntentId]);
            return false;
        }
    }

    /**
     * Fallback signature verification using HMAC SHA256 for tests/edge environments.
     */
    protected function verifyStripeSignatureFallback(string $payload, ?string $sigHeader, ?string $secret): bool
    {
        if (empty($sigHeader) || empty($secret)) {
            return false;
        }

        // Expect header like: t=timestamp,v1=signature
        $parts = explode(',', $sigHeader);
        $t = null;
        $v1 = null;
        foreach ($parts as $p) {
            [$k, $v] = array_map('trim', explode('=', $p, 2) + [1 => null]);
            if ($k === 't') $t = $v;
            if ($k === 'v1') $v1 = $v;
        }
        if (!$t || !$v1) return false;

        $signedPayload = $t . '.' . $payload;
        $expected = hash_hmac('sha256', $signedPayload, $secret);

        // Use hash_equals to avoid timing attacks
        return hash_equals($expected, $v1);
    }
}
