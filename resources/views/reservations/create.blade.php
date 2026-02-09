@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('listings.show', $listing->slug) }}" class="text-[#440a67] hover:text-[#290540] font-semibold">
            <i class="fas fa-arrow-left"></i> Back to Listing
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-[#440a67] to-[#290540] text-white p-6">
            <h1 class="text-3xl font-bold">Reserve This Car</h1>
            <p class="mt-2 text-indigo-100">Secure your purchase with a refundable deposit</p>
        </div>

        @if(session('success'))
            <div class="m-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="m-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="m-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="p-6">
            <!-- Listing Summary -->
            <div class="mb-8 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex items-start gap-4">
                    <img src="{{ $listing->main_image ? (Str::startsWith($listing->main_image, 'http') ? $listing->main_image : $listing->main_image) : 'https://via.placeholder.com/128x96?text=No+Image' }}"
                         alt="{{ $listing->title }}"
                         class="w-32 h-24 object-cover rounded-md">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $listing->title }}</h2>
                        <div class="text-sm text-gray-500 mt-1">
                            <span>{{ $listing->year }}</span> • <span>{{ number_format($listing->mileage) }} km</span>
                        </div>
                        <div class="text-2xl font-bold text-[#440a67] mt-2">
                            {!! \App\Helpers\Helpers::formatPrice($listing->price) !!}
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('reservations.store', $listing->id) }}" method="POST" id="reservationForm">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Left Column: Settings -->
                    <div class="space-y-6">
                        
                        <!-- Deposit Amount Slider -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Deposit Percentage</label>
                            <input type="range" name="deposit_percentage" id="depositPercentage" 
                                   min="1" max="10" value="10" 
                                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-[#440a67]">
                            <div class="flex justify-between mt-2 text-sm text-gray-600">
                                <span>1%</span>
                                <span id="percentageDisplay" class="font-bold text-[#440a67]">10%</span>
                                <span>10%</span>
                            </div>
                        </div>

                        <!-- Duration Selection -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Reservation Duration</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="cursor-pointer">
                                    <input type="radio" name="duration_type" value="24" class="peer sr-only" checked>
                                    <div class="p-3 border-2 border-gray-200 rounded-lg text-center peer-checked:border-[#440a67] peer-checked:bg-purple-50 transition-all">
                                        <div class="font-bold text-gray-900">24 Hours</div>
                                        <div class="text-xs text-gray-500">Standard</div>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="duration_type" value="72" class="peer sr-only">
                                    <div class="p-3 border-2 border-gray-200 rounded-lg text-center peer-checked:border-[#440a67] peer-checked:bg-purple-50 transition-all">
                                        <div class="font-bold text-gray-900">72 Hours</div>
                                        <div class="text-xs text-gray-500">Extended</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Payment Method Selection -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Payment Method</label>
                            <div class="space-y-3">
                                <!-- Wallet -->
                                <label class="cursor-pointer block">
                                    <input type="radio" name="payment_method" value="wallet" class="peer sr-only" checked onchange="updatePaymentMethod()">
                                    <div class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-[#440a67] peer-checked:bg-purple-50 transition-all">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-[#440a67] rounded-full flex items-center justify-center text-white">
                                                <i class="fas fa-wallet"></i>
                                            </div>
                                            <div class="flex-1">
                                                <div class="font-bold text-gray-900">Wallet Balance</div>
                                                <div class="text-sm text-gray-500">Pay using your wallet (₽{{ number_format(auth()->user()->balance) }})</div>
                                            </div>
                                            <div class="w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-[#440a67] peer-checked:bg-[#440a67] flex items-center justify-center">
                                                <i class="fas fa-check text-white text-xs opacity-0 peer-checked:opacity-100"></i>
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                @if($settings['payment_gateway_stripe'] ?? false)
                                <!-- Stripe -->
                                <label class="cursor-pointer block">
                                    <input type="radio" name="payment_method" value="stripe" class="peer sr-only" onchange="updatePaymentMethod()">
                                    <div class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-[#440a67] peer-checked:bg-purple-50 transition-all">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center text-white">
                                                <i class="fab fa-cc-stripe"></i>
                                            </div>
                                            <div class="flex-1">
                                                <div class="font-bold text-gray-900">Credit/Debit Card</div>
                                                <div class="text-sm text-gray-500">Pay securely with Stripe</div>
                                            </div>
                                            <div class="w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-[#440a67] peer-checked:bg-[#440a67] flex items-center justify-center">
                                                <i class="fas fa-check text-white text-xs opacity-0 peer-checked:opacity-100"></i>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                                @endif

                                @if($settings['payment_gateway_paypal'] ?? false)
                                <!-- PayPal -->
                                <label class="cursor-pointer block">
                                    <input type="radio" name="payment_method" value="paypal" class="peer sr-only" onchange="updatePaymentMethod()">
                                    <div class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-[#440a67] peer-checked:bg-purple-50 transition-all">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white">
                                                <i class="fab fa-paypal"></i>
                                            </div>
                                            <div class="flex-1">
                                                <div class="font-bold text-gray-900">PayPal</div>
                                                <div class="text-sm text-gray-500">Pay with your PayPal account</div>
                                            </div>
                                            <div class="w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-[#440a67] peer-checked:bg-[#440a67] flex items-center justify-center">
                                                <i class="fas fa-check text-white text-xs opacity-0 peer-checked:opacity-100"></i>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                                @endif

                                @if($settings['payment_gateway_bank_transfer'] ?? false)
                                <!-- Bank Transfer -->
                                <label class="cursor-pointer block">
                                    <input type="radio" name="payment_method" value="bank_transfer" class="peer sr-only" onchange="updatePaymentMethod()">
                                    <div class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-[#440a67] peer-checked:bg-purple-50 transition-all">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center text-white">
                                                <i class="fas fa-university"></i>
                                            </div>
                                            <div class="flex-1">
                                                <div class="font-bold text-gray-900">Bank Transfer</div>
                                                <div class="text-sm text-gray-500">Pay via bank transfer</div>
                                            </div>
                                            <div class="w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-[#440a67] peer-checked:bg-[#440a67] flex items-center justify-center">
                                                <i class="fas fa-check text-white text-xs opacity-0 peer-checked:opacity-100"></i>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                                @endif

                                @if($settings['payment_gateway_cash_on_delivery'] ?? false)
                                <!-- Cash on Delivery -->
                                <label class="cursor-pointer block">
                                    <input type="radio" name="payment_method" value="cash_on_delivery" class="peer sr-only" onchange="updatePaymentMethod()">
                                    <div class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-[#440a67] peer-checked:bg-purple-50 transition-all">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-orange-600 rounded-full flex items-center justify-center text-white">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </div>
                                            <div class="flex-1">
                                                <div class="font-bold text-gray-900">Cash on Delivery</div>
                                                <div class="text-sm text-gray-500">Pay when you receive the car</div>
                                            </div>
                                            <div class="w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-[#440a67] peer-checked:bg-[#440a67] flex items-center justify-center">
                                                <i class="fas fa-check text-white text-xs opacity-0 peer-checked:opacity-100"></i>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                                @endif
                            </div>
                        </div>

                    </div>

                    <!-- Right Column: Summary & Payment -->
                    <div class="bg-gray-50 p-6 rounded-xl h-fit border border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Payment Summary</h3>
                        
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-gray-600">
                                <span>Listing Price</span>
                                <span>{!! \App\Helpers\Helpers::formatPrice($listing->price) !!}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Deposit Rate</span>
                                <span id="summaryPercentage">10%</span>
                            </div>
                            <div class="border-t border-gray-200 pt-3 flex justify-between items-center">
                                <span class="font-bold text-gray-900">Deposit Amount</span>
                                <span class="text-2xl font-bold text-[#440a67]" id="summaryAmount">
                                    {!! \App\Helpers\Helpers::formatPrice($listing->price * 0.1) !!}
                                </span>
                            </div>
                        </div>

                        <!-- Wallet Check (shown only when wallet is selected) -->
                        <div id="walletSection" class="mb-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-gray-700">Wallet Balance</span>
                                <span class="text-sm font-bold">₽{{ number_format(auth()->user()->balance) }}</span>
                            </div>
                            
                            @php
                                $requiredAmount = $listing->price * 0.1; // Default 10%
                                $hasBalance = auth()->user()->balance >= $requiredAmount;
                            @endphp

                            <div id="walletStatus" class="p-3 rounded-lg text-sm {{ $hasBalance ? 'bg-gray-200 text-gray-800' : 'bg-red-50 text-red-700 border border-red-200' }}">
                                @if($hasBalance)
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Sufficient balance available</span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <span>Insufficient balance</span>
                                    </div>
                                @endif
                            </div>

                            @if(!$hasBalance)
                                <button type="button" onclick="openTopUpModal()" class="block w-full text-center mt-2 text-sm text-[#440a67] hover:underline hover:text-[#290540] font-semibold bg-transparent border-none cursor-pointer">
                                    + Top Up Wallet
                                </button>
                            @endif
                        </div>

                        <!-- Payment Instructions (shown for non-wallet methods) -->
                        <div id="paymentInstructions" class="mb-6 hidden">
                            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                                    <div>
                                        <div class="font-semibold text-blue-900" id="instructionTitle">Payment Instructions</div>
                                        <div class="text-sm text-blue-700 mt-1" id="instructionText">
                                            You will be redirected to complete your payment after confirming the reservation.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" 
                                class="w-full py-4 rounded-xl text-white font-bold text-lg shadow-lg transition-transform transform active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed bg-[#440a67] hover:bg-[#290540]"
                                id="submitBtn"
                                {{ !$hasBalance ? 'disabled' : '' }}>
                            Confirm Reservation
                        </button>
                        
                        <p class="text-xs text-center text-gray-500 mt-4" id="submitHelpText">
                            By confirming, the deposit amount will be deducted from your wallet. 
                            You can cancel anytime within the duration period for a partial refund.
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@include('partials.topup-modal')

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const slider = document.getElementById('depositPercentage');
        const display = document.getElementById('percentageDisplay');
        const summaryPercentage = document.getElementById('summaryPercentage');
        const summaryAmount = document.getElementById('summaryAmount');
        const submitBtn = document.getElementById('submitBtn');
        const walletSection = document.getElementById('walletSection');
        const paymentInstructions = document.getElementById('paymentInstructions');
        const submitHelpText = document.getElementById('submitHelpText');
        const price = {{ $listing->price }};
        const userBalance = {{ auth()->user()->balance }};

        function updateValues() {
            const percent = slider.value;
            const amount = (price * percent) / 100;
            
            display.textContent = percent + '%';
            summaryPercentage.textContent = percent + '%';
            summaryAmount.textContent = '₽' + new Intl.NumberFormat().format(amount);

            // Only check balance if wallet is selected
            const selectedPayment = document.querySelector('input[name="payment_method"]:checked');
            if (selectedPayment && selectedPayment.value === 'wallet') {
                if (userBalance < amount) {
                    submitBtn.disabled = true;
                    document.getElementById('walletStatus').className = 'p-3 rounded-lg text-sm bg-red-50 text-red-700 border border-red-200';
                    document.getElementById('walletStatus').innerHTML = `
                        <div class="flex items-center gap-2">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>Insufficient balance (Need ₽${new Intl.NumberFormat().format(amount)})</span>
                        </div>
                    `;
                } else {
                    submitBtn.disabled = false;
                    document.getElementById('walletStatus').className = 'p-3 rounded-lg text-sm bg-gray-200 text-gray-800';
                    document.getElementById('walletStatus').innerHTML = `
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle"></i>
                            <span>Sufficient balance available</span>
                        </div>
                    `;
                }
            }
        }

        function updatePaymentMethod() {
            const selectedPayment = document.querySelector('input[name="payment_method"]:checked');
            if (!selectedPayment) return;

            const method = selectedPayment.value;
            
            if (method === 'wallet') {
                walletSection.classList.remove('hidden');
                paymentInstructions.classList.add('hidden');
                submitHelpText.textContent = 'By confirming, the deposit amount will be deducted from your wallet. You can cancel anytime within the duration period for a partial refund.';
                updateValues(); // Check balance
            } else {
                walletSection.classList.add('hidden');
                paymentInstructions.classList.remove('hidden');
                submitBtn.disabled = false; // Enable submit for other methods
                
                // Update instructions based on method
                const instructionTitle = document.getElementById('instructionTitle');
                const instructionText = document.getElementById('instructionText');
                
                switch(method) {
                    case 'stripe':
                        instructionTitle.textContent = 'Stripe Payment';
                        instructionText.textContent = 'You will be redirected to Stripe to complete your card payment securely.';
                        submitHelpText.textContent = 'You will be redirected to complete your payment via Stripe.';
                        break;
                    case 'paypal':
                        instructionTitle.textContent = 'PayPal Payment';
                        instructionText.textContent = 'You will be redirected to PayPal to complete your payment.';
                        submitHelpText.textContent = 'You will be redirected to complete your payment via PayPal.';
                        break;
                    case 'bank_transfer':
                        instructionTitle.textContent = 'Bank Transfer Instructions';
                        instructionText.textContent = '{{ $settings['bank_transfer_instructions'] ?? 'Please transfer the deposit amount to our bank account. Your reservation will be confirmed once payment is received.' }}';
                        submitHelpText.textContent = 'Your reservation will be pending until we receive your bank transfer.';
                        break;
                    case 'cash_on_delivery':
                        instructionTitle.textContent = 'Cash on Delivery';
                        instructionText.textContent = '{{ $settings['cash_on_delivery_instructions'] ?? 'You will pay the deposit in cash when you meet with the seller.' }}';
                        submitHelpText.textContent = 'You will pay the deposit amount when you meet with the seller.';
                        break;
                }
            }
        }

        slider.addEventListener('input', updateValues);
        
        // Initial check
        updateValues();
        updatePaymentMethod();
    });
    </script>
@endsection
