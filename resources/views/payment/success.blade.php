@extends('layouts.blankLayout')

@section('title', 'Payment Successful')

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')
<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-4">
      <!-- Success -->
      <div class="card" style="max-width: 500px; margin: 0 auto;">
        <div class="card-body" style="padding: 2rem;">
          <div class="d-flex justify-content-center mb-6">
            <div class="avatar avatar-xl bg-success-subtle rounded">
              <div class="avatar-initial rounded-circle bg-success">
                <i class="icon-base ti tabler-check icon-28px"></i>
              </div>
            </div>
          </div>
          
          <h4 class="mb-1 text-center">Payment Successful!</h4>
          <p class="mb-6 text-center">Your payment has been processed successfully.</p>

          @if(isset($listing) && $listing)
          <div class="alert alert-success" role="alert">
            <h5 class="alert-heading mb-2">{{ $listing->title }}</h5>
            <p class="mb-0">
              Your listing has been upgraded to 
              @if($listing->is_premium)
                <strong>Premium</strong>
              @elseif($listing->is_featured)
                <strong>Featured</strong>
              @else
                <strong>Bump to Top</strong>
              @endif
              status.
            </p>
          </div>
          @endif

          @if(isset($session) && $session)
          <div class="text-muted small mb-6">
            <p class="mb-1">Transaction ID: {{ $session->id }}</p>
            <p class="mb-0">Amount Paid: ${{ number_format($session->amount_total / 100, 2) }}</p>
          </div>
          @endif

          <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('listings.show', $listing->slug ?? '#') }}" class="btn btn-primary">
              View Listing
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
              Go to Dashboard
            </a>
          </div>
        </div>
      </div>
      <!-- /Success -->
    </div>
  </div>
</div>
@endsection
