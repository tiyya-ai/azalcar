<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $user = auth()->user();
        $transactions = Transaction::where('user_id', $user->id)->latest()->get();
        return view('dashboard.wallet', compact('user', 'transactions'));
    }

    public function topUp(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10|max:10000',
        ]);

        $user = auth()->user();
        $amount = $request->amount;

        // Ensure Stripe is properly configured
        if (!class_exists('Stripe\Stripe')) {
            \Illuminate\Support\Facades\Log::error('Stripe not configured for wallet topup', [
                'user_id' => $user->id,
                'amount' => $amount
            ]);
            return back()->with('error', 'Payment system is currently unavailable. Please try again later.');
        }

        // Real Stripe Integration
        $result = $this->paymentService->createPaymentIntent($amount, 'rub', [
            'user_id' => $user->id,
            'type' => 'wallet_topup'
        ]);

        if (!$result['success']) {
            return back()->with('error', 'Payment initialization failed: ' . $result['message']);
        }

        return view('dashboard.wallet.checkout', [
            'clientSecret' => $result['client_secret'],
            'amount' => $amount
        ]);
    }

    public function verifyTopUp(Request $request)
    {
        $paymentIntentId = $request->query('payment_intent');
        
        if (!$paymentIntentId) {
            return redirect()->route('wallet.index')->with('error', 'Invalid payment verification.');
        }

        // UI-only verification: do not perform authoritative ledger changes here.
        // The authoritative payment processing and balance crediting is handled
        // by the Stripe webhook (`/webhook/stripe`) which verifies signature
        // and performs idempotent balance updates.

        if ($this->paymentService->verifyPayment($paymentIntentId)) {
            return redirect()->route('wallet.index')->with('success', 'Payment verified — finalizing in background.');
        }

        return redirect()->route('wallet.index')->with('error', 'Payment verification failed.');
    }
}
