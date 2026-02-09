@extends('layouts/contentNavbarLayout')

@section('title', 'Payment Settings')

@section('vendor-style')
@endsection

@section('page-style')
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Payment Settings</h5>
        <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary btn-sm">
          <i class="icon-base ti tabler-arrow-left me-1"></i>
          Back to Settings
        </a>
      </div>

      <form action="{{ route('admin.settings.payments.update') }}" method="POST">
        @csrf
        <div class="card-body">
          <div class="row">
            <!-- Stripe Settings -->
            <div class="col-md-6 mb-4">
              <div class="card h-100 border-primary">
                <div class="card-header bg-primary text-white">
                  <h6 class="card-title mb-0">
                    <i class="icon-base ti tabler-credit-card me-2"></i>
                    Stripe Payment Gateway
                  </h6>
                </div>
                <div class="card-body">
                  <div class="mb-3">
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" id="payment_gateway_stripe" name="payment_gateway_stripe" value="1" {{ $settings['payment_gateway_stripe'] ?? false ? 'checked' : '' }}>
                      <label class="form-check-label" for="payment_gateway_stripe">
                        Enable Stripe Payments
                      </label>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label for="stripe_publishable_key" class="form-label">Publishable Key</label>
                    <input type="text" class="form-control" id="stripe_publishable_key" name="stripe_publishable_key" value="{{ $settings['stripe_publishable_key'] ?? '' }}" placeholder="pk_test_...">
                  </div>

                  <div class="mb-3">
                    <label for="stripe_secret_key" class="form-label">Secret Key</label>
                    <input type="password" class="form-control" id="stripe_secret_key" name="stripe_secret_key" value="{{ $settings['stripe_secret_key'] ?? '' }}" placeholder="sk_test_...">
                  </div>

                  <div class="mb-3">
                    <label for="stripe_webhook_secret" class="form-label">Webhook Secret</label>
                    <input type="password" class="form-control" id="stripe_webhook_secret" name="stripe_webhook_secret" value="{{ $settings['stripe_webhook_secret'] ?? '' }}" placeholder="whsec_...">
                  </div>
                </div>
              </div>
            </div>

            <!-- PayPal Settings -->
            <div class="col-md-6 mb-4">
              <div class="card h-100 border-info">
                <div class="card-header bg-info text-white">
                  <h6 class="card-title mb-0">
                    <i class="icon-base ti tabler-brand-paypal me-2"></i>
                    PayPal Payment Gateway
                  </h6>
                </div>
                <div class="card-body">
                  <div class="mb-3">
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" id="payment_gateway_paypal" name="payment_gateway_paypal" value="1" {{ $settings['payment_gateway_paypal'] ?? false ? 'checked' : '' }}>
                      <label class="form-check-label" for="payment_gateway_paypal">
                        Enable PayPal Payments
                      </label>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label for="paypal_client_id" class="form-label">Client ID</label>
                    <input type="text" class="form-control" id="paypal_client_id" name="paypal_client_id" value="{{ $settings['paypal_client_id'] ?? '' }}" placeholder="Your PayPal Client ID">
                  </div>

                  <div class="mb-3">
                    <label for="paypal_client_secret" class="form-label">Client Secret</label>
                    <input type="password" class="form-control" id="paypal_client_secret" name="paypal_client_secret" value="{{ $settings['paypal_client_secret'] ?? '' }}" placeholder="Your PayPal Client Secret">
                  </div>

                  <div class="mb-3">
                    <label for="paypal_mode" class="form-label">Mode</label>
                    <select class="form-select" id="paypal_mode" name="paypal_mode">
                      <option value="sandbox" {{ ($settings['paypal_mode'] ?? '') == 'sandbox' ? 'selected' : '' }}>Sandbox (Testing)</option>
                      <option value="live" {{ ($settings['paypal_mode'] ?? '') == 'live' ? 'selected' : '' }}>Live (Production)</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <!-- Bank Transfer Settings -->
            <div class="col-md-6 mb-4">
              <div class="card h-100 border-success">
                <div class="card-header bg-success text-white">
                  <h6 class="card-title mb-0">
                    <i class="icon-base ti tabler-building-bank me-2"></i>
                    Bank Transfer
                  </h6>
                </div>
                <div class="card-body">
                  <div class="mb-3">
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" id="payment_gateway_bank_transfer" name="payment_gateway_bank_transfer" value="1" {{ $settings['payment_gateway_bank_transfer'] ?? false ? 'checked' : '' }}>
                      <label class="form-check-label" for="payment_gateway_bank_transfer">
                        Enable Bank Transfer
                      </label>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label for="bank_transfer_instructions" class="form-label">Payment Instructions</label>
                    <textarea class="form-control" id="bank_transfer_instructions" name="bank_transfer_instructions" rows="4" placeholder="Enter bank transfer instructions...">{{ $settings['bank_transfer_instructions'] ?? '' }}</textarea>
                    <div class="form-text">Instructions shown to customers for bank transfer payments.</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Cash on Delivery Settings -->
            <div class="col-md-6 mb-4">
              <div class="card h-100 border-warning">
                <div class="card-header bg-warning text-white">
                  <h6 class="card-title mb-0">
                    <i class="icon-base ti tabler-cash me-2"></i>
                    Cash on Delivery
                  </h6>
                </div>
                <div class="card-body">
                  <div class="mb-3">
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" id="payment_gateway_cash_on_delivery" name="payment_gateway_cash_on_delivery" value="1" {{ $settings['payment_gateway_cash_on_delivery'] ?? false ? 'checked' : '' }}>
                      <label class="form-check-label" for="payment_gateway_cash_on_delivery">
                        Enable Cash on Delivery
                      </label>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label for="cash_on_delivery_instructions" class="form-label">Payment Instructions</label>
                    <textarea class="form-control" id="cash_on_delivery_instructions" name="cash_on_delivery_instructions" rows="4" placeholder="Enter cash on delivery instructions...">{{ $settings['cash_on_delivery_instructions'] ?? '' }}</textarea>
                    <div class="form-text">Instructions shown to customers for cash on delivery payments.</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="card-footer">
          <button type="submit" class="btn btn-primary">
            <i class="icon-base ti tabler-device-floppy me-1"></i>
            Update Payment Settings
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add any client-side validation or enhancements here
    console.log('Payment settings page loaded');
});
</script>
@endsection
