@extends('layouts/blankLayout')

@section('title', 'Reset Password')

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
      <!-- Reset Password -->
      <div class="card" style="max-width: 450px; margin: 0 auto;">
        <div class="card-body" style="padding: 2rem;">
          <!-- Logo -->
          <div class="app-brand justify-content-center mb-6">
            <a href="{{ url('/') }}" class="app-brand-link">
              <span class="app-brand-logo demo">
                <div class="logo">
                  <div class="logo-dots">
                    <div class="dot dot-green"></div>
                    <div class="dot dot-purple"></div>
                    <div class="dot dot-blue"></div>
                    <div class="dot dot-orange"></div>
                  </div>
                </div>
              </span>
            </a>
          </div>
          <!-- /Logo -->
          <h4 class="mb-1">Reset Password</h4>
          <p class="mb-6">Enter your new password</p>

          @if ($errors->any())
            <div class="alert alert-danger" role="alert">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form id="formAuthentication" class="mb-4" action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <input type="hidden" name="email" value="{{ $request->email ?? old('email') }}">

            <div class="mb-6 form-control-validation">
              <label for="email" class="form-label">Email Address</label>
              <input type="email" class="form-control" id="email" name="email"
                placeholder="Enter your email" value="{{ old('email', $request->email ?? '') }}" readonly />
            </div>
            <div class="mb-6 form-password-toggle form-control-validation">
              <label class="form-label" for="password">New Password</label>
              <div class="input-group input-group-merge">
                <input type="password" id="password" class="form-control" name="password"
                  placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                  aria-describedby="password" required />
                <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
              </div>
            </div>
            <div class="mb-6 form-password-toggle form-control-validation">
              <label class="form-label" for="password_confirmation">Confirm New Password</label>
              <div class="input-group input-group-merge">
                <input type="password" id="password_confirmation" class="form-control" name="password_confirmation"
                  placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                  aria-describedby="password_confirmation" required />
                <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
              </div>
            </div>
            <div class="mb-6">
              <button class="btn btn-primary d-grid w-100" type="submit">Reset Password</button>
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
      <!-- /Reset Password -->
    </div>
  </div>
</div>
@endsection
