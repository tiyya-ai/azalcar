@extends('layouts.app')

@section('title', 'Complete Payment')

@section('content')
<style>
    .checkout-container {
        min-height: 100vh;
        background: #F8F9FB;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .checkout-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        width: 100%;
        max-width: 500px;
        overflow: hidden;
    }
    .checkout-header {
        background: linear-gradient(135deg, #6041E0 0%, #452276 100%);
        padding: 30px;
        color: white;
        text-align: center;
    }
    .checkout-body {
        padding: 40px;
    }
    #submit {
        background: #6041E0;
        color: white;
        border: none;
        padding: 15px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 16px;
        cursor: pointer;
        width: 100%;
        transition: all 0.2s;
    }
    #submit:hover { background: #4c30c4; }
    #submit:disabled { opacity: 0.6; cursor: not-allowed; }
</style>

<div class="checkout-container">
    <div class="checkout-card">
        <div class="checkout-header">
            <h2 style="margin: 0; font-size: 24px;">Complete Your Top-up</h2>
            <p style="opacity: 0.8; margin: 10px 0 0;">Secure payment by Stripe</p>
        </div>
        <div class="checkout-body">
            <div style="text-align: center; margin-bottom: 30px;">
                <div style="font-size: 14px; color: #94a3b8; font-weight: 600; margin-bottom: 8px; text-transform: uppercase;">Amount to Pay</div>
                <div style="font-size: 36px; font-weight: 800; color: #1a1a1a;">{{ \App\Helpers\Helpers::formatPrice($amount) }}</div>
            </div>

            <form id="payment-form">
                <div id="payment-element" style="margin-bottom: 24px;">
                    <!-- Stripe Elements -->
                </div>
                <div id="error-message" style="display: none; padding: 15px; background: #fff1f2; color: #e11d48; border-radius: 8px; margin-bottom: 20px; font-size: 14px;"></div>
                
                <button id="submit">
                    <span id="button-text">Pay Now</span>
                </button>
            </form>

            <div style="text-align: center; margin-top: 25px;">
                <a href="{{ route('wallet.index') }}" style="color: #94a3b8; font-size: 14px; text-decoration: none; font-weight: 600;">
                    <i class="fas fa-arrow-left me-1"></i> Cancel and Return
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const stripeKey = "{{ \App\Models\Setting::get('stripe_publishable_key') }}";
        if (!stripeKey) {
            document.getElementById('error-message').textContent = "Stripe publishable key is not configured. Please contact administrator.";
            document.getElementById('error-message').classList.remove('d-none');
            return;
        }

        const stripe = Stripe(stripeKey);
        const clientSecret = "{{ $clientSecret }}";

        const elements = stripe.elements({
            clientSecret: clientSecret,
            appearance: { theme: 'stripe' }
        });

        const paymentElement = elements.create('payment');
        paymentElement.mount('#payment-element');

        const form = document.getElementById('payment-form');
        const submitBtn = document.getElementById('submit');
        const spinner = document.getElementById('spinner');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            setLoading(true);

            const { error } = await stripe.confirmPayment({
                elements,
                confirmParams: {
                    return_url: "{{ route('wallet.verify') }}",
                },
            });

            if (error) {
                const messageContainer = document.querySelector('#error-message');
                messageContainer.textContent = error.message;
                messageContainer.classList.remove('d-none');
                setLoading(false);
            } else {
                // Determine logic will handle the redirect
            }
        });

        function setLoading(isLoading) {
            if (isLoading) {
                submitBtn.disabled = true;
                spinner.classList.remove('d-none');
            } else {
                submitBtn.disabled = false;
                spinner.classList.add('d-none');
            }
        }
    });
</script>
@endpush
@endsection
