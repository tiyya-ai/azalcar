<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Listing;

class PaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create checkout session for premium listing
     */
    public function createCheckoutSession(Request $request)
    {
        $request->validate([
            'listing_id' => 'required|exists:listings,id',
            'type' => 'required|in:premium,featured,bump',
            'duration' => 'nullable|in:7,14,30',
        ]);

        $listing = Listing::findOrFail($request->listing_id);
        $user = Auth::user();

        // Pricing based on type and duration
        $prices = [
            'premium' => [
                '7' => 1000,    // $10.00
                '14' => 1500,   // $15.00
                '30' => 2500,   // $25.00
            ],
            'featured' => [
                '7' => 2000,    // $20.00
                '14' => 3500,   // $35.00
                '30' => 5000,   // $50.00
            ],
            'bump' => [
                '7' => 500,     // $5.00
            ],
        ];

        $duration = $request->duration ?? '30';
        $amount = $prices[$request->type][$duration] ?? 2500;

        $productNames = [
            'premium' => 'Premium Listing',
            'featured' => 'Featured Listing',
            'bump' => 'Bump to Top',
        ];

        try {
            // Create Stripe customer if doesn't exist
            if (!$user->stripe_id) {
                $customer = \Stripe\Customer::create([
                    'email' => $user->email,
                    'name' => $user->name,
                ]);
                $user->stripe_id = $customer->id;
                $user->save();
            }

            $session = Session::create([
                'customer' => $user->stripe_id,
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => config('services.stripe.currency', 'usd'),
                        'product_data' => [
                            'name' => $productNames[$request->type] . ' - ' . $listing->title,
                            'description' => ucfirst($request->type) . ' placement for ' . $duration . ' days',
                        ],
                        'unit_amount' => $amount,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}&listing_id=' . $listing->id,
                'cancel_url' => route('payment.cancel') . '?listing_id=' . $listing->id,
                'metadata' => [
                    'user_id' => $user->id,
                    'listing_id' => $listing->id,
                    'type' => $request->type,
                    'duration' => $duration,
                ],
            ]);

            return redirect($session->url);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Payment initialization failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Handle successful payment
     */
    public function success(Request $request)
    {
        $request->validate([
            'session_id' => 'required',
            'listing_id' => 'required|exists:listings,id',
        ]);

        try {
            $session = Session::retrieve($request->session_id);

            if ($session->payment_status !== 'paid') {
                return redirect()->route('home')
                    ->withErrors(['error' => 'Payment was not successful']);
            }

            $listing = Listing::findOrFail($request->listing_id);
            $metadata = $session->metadata;

            // Update listing based on payment type
            switch ($metadata->type) {
                case 'premium':
                    $listing->update([
                        'is_premium' => true,
                        'premium_until' => now()->addDays((int)$metadata->duration),
                    ]);
                    break;
                case 'featured':
                    $listing->update([
                        'is_featured' => true,
                        'featured_until' => now()->addDays((int)$metadata->duration),
                    ]);
                    break;
                case 'bump':
                    $listing->update([
                        'bumped_at' => now(),
                    ]);
                    break;
            }

            // Record transaction
            $user = Auth::user();
            $user->transactions()->create([
                'stripe_session_id' => $session->id,
                'amount' => $session->amount_total / 100,
                'type' => 'payment',
                'description' => ucfirst($metadata->type) . ' listing upgrade',
                'status' => 'completed',
            ]);

            return view('payment.success', compact('listing', 'session'));

        } catch (\Exception $e) {
            return redirect()->route('home')
                ->withErrors(['error' => 'Failed to process payment: ' . $e->getMessage()]);
        }
    }

    /**
     * Handle cancelled payment
     */
    public function cancel(Request $request)
    {
        return view('payment.cancel');
    }

    /**
     * Stripe webhook handler
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig = $request->header('stripe-signature');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig,
                config('services.stripe.webhook.secret')
            );

            switch ($event->type) {
                case 'checkout.session.completed':
                    $session = $event->data->object;
                    // Handle successful payment
                    \Log::info('Payment successful', ['session' => $session->id]);
                    break;

                case 'invoice.payment_succeeded':
                    // Handle subscription payment
                    \Log::info('Invoice payment succeeded', ['invoice' => $event->data->object->id]);
                    break;

                case 'invoice.payment_failed':
                    // Handle failed payment
                    \Log::warning('Invoice payment failed', ['invoice' => $event->data->object->id]);
                    break;
            }

            return response()->json(['received' => true]);

        } catch (\Exception $e) {
            \Log::error('Stripe webhook error', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
