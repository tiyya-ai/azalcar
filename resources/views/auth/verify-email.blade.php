@extends('layouts/blankLayout')

@section('title', 'Email Verification')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')
<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-4">
      <!-- Email Verification -->
      <div class="card" style="max-width: 400px; margin: 0 auto;">
        <div class="card-body" style="padding: 2rem;">
          <!-- Logo -->
          <div class="app-brand justify-content-center mb-6">
            <a href="{{ url('/') }}" class="app-brand-link">
              <img src="{{ asset('assets/images/logo.png') }}" alt="AzalCars" style="height: 48px; width: auto;">
            </a>
          </div>
          <!-- /Logo -->
          <h4 class="mb-1">Verify Your Email Address</h4>
          <p class="mb-6">Before proceeding, please check your email for a verification link. If you did not receive the email, we will gladly send you another.</p>

          @if (session('resent'))
            <div class="alert alert-success" role="alert">
              A fresh verification link has been sent to your email address.
            </div>
          @endif

          <form class="mb-4" method="POST" action="{{ route('verification.send') }}">
            @csrf

            <button type="submit" class="btn btn-primary d-grid w-100">
              Resend Verification Email
            </button>
          </form>

          <p class="text-center">
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
              <span>Logout</span>
            </a>
          </p>

          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
          </form>
        </div>
      </div>
      <!-- /Email Verification -->
    </div>
  </div>
</div>
@endsection