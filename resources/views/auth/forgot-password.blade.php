@extends('layouts/blankLayout')

@section('title', 'Forgot Password')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

@section('page-script')
@vite(['resources/assets/js/pages-auth.js'])
@endsection

@section('content')
<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-4">
      <!-- Forgot Password -->
      <div class="card" style="max-width: 400px; margin: 0 auto;">
        <div class="card-body" style="padding: 2rem;">
          <!-- Logo -->
          <div class="app-brand justify-content-center mb-6">
            <a href="{{ url('/') }}" class="app-brand-link">
              <img src="{{ asset('assets/images/logo.png') }}" alt="AzalCars" style="height: 48px; width: auto;">
            </a>
          </div>
          <!-- /Logo -->
          <h4 class="mb-1">Forgot Password?</h4>
          <p class="mb-6">Enter your email and we'll send you instructions to reset your password</p>

          @if ($errors->any())
            <div class="alert alert-danger" role="alert">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          @if (session('status'))
            <div class="alert alert-success" role="alert">
              {{ session('status') }}
            </div>
          @endif

          <form id="formAuthentication" class="mb-4" action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="mb-6 form-control-validation">
              <label for="email" class="form-label">Email Address</label>
              <input type="email" class="form-control" id="email" name="email"
                placeholder="Enter your email" value="{{ old('email') }}" required autofocus />
            </div>
            <div class="mb-6">
              <button class="btn btn-primary d-grid w-100" type="submit">Send Reset Link</button>
            </div>
          </form>

          <div class="text-center">
            <a href="{{ route('login') }}">
              <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl"></i>
              Back to login
            </a>
          </div>
        </div>
      </div>
      <!-- /Forgot Password -->
    </div>
  </div>
</div>
@endsection
