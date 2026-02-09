<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Exception;

class PaymentService
{
    public function __construct()
    {
        $stripeSecretKey = \App\Models\Setting::get('stripe_secret_key');
        if ($stripeSecretKey) {
            Stripe::setApiKey($stripeSecretKey);
        }
    }

    /**
     * Create a Stripe Payment Intent
     */
    public function createPaymentIntent($amount, $currency = 'rub', $metadata = [])
    {
        try {
            // Amount in cents
            $amountInCents = (int) ($amount * 100);

            $intent = PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => $currency,
                'metadata' => $metadata,
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            return [
                'success' => true,
                'client_secret' => $intent->client_secret,
                'id' => $intent->id
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Verify payment status
     */
    public function verifyPayment($paymentIntentId)
    {
        try {
            $intent = PaymentIntent::retrieve($paymentIntentId);
            return $intent->status === 'succeeded';
        } catch (Exception $e) {
            return false;
        }
    }
}
