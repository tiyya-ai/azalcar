@extends('layouts.blankLayout')

@section('title', 'Payment Cancelled')

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')
<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-4">
      <!-- Cancelled -->
      <div class="card" style="max-width: 500px; margin: 0 auto;">
        <div class="card-body" style="padding: 2rem;">
          <div class="d-flex justify-content-center mb-6">
            <div class="avatar avatar-xl bg-warning-subtle rounded">
              <div class="avatar-initial rounded-circle bg-warning">
                <i class="icon-base ti tabler-x icon-28px"></i>
              </div>
            </div>
          </div>
          
          <h4 class="mb-1 text-center">Payment Cancelled</h4>
          <p class="mb-6 text-center">Your payment was cancelled. No charges were made.</p>

          <div class="alert alert-warning" role="alert">
            <h5 class="alert-heading mb-2">What happened?</h5>
            <p class="mb-0">
              You may have closed the payment window or clicked the cancel button. 
              Your listing has not been upgraded yet.
            </p>
          </div>

          <div class="d-flex justify-content-center gap-3">
            <a href="{{ url()->previous() }}" class="btn btn-primary">
              Try Again
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
              Go to Dashboard
            </a>
          </div>
        </div>
      </div>
      <!-- /Cancelled -->
    </div>
  </div>
</div>
@endsection
