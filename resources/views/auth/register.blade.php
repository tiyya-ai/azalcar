@extends('layouts/blankLayout')

@section('title', 'Register')

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
      <!-- Register -->
      <div class="card" style="max-width: 450px; margin: 0 auto;">
        <div class="card-body" style="padding: 2rem;">
          <!-- Logo -->
          <div class="app-brand justify-content-center mb-6">
            <a href="{{ url('/') }}" class="app-brand-link">
              <img src="{{ asset('assets/images/logo.png') }}" alt="AzalCars" style="height: 48px; width: auto;">
            </a>
          </div>
          <!-- /Logo -->
          <h4 class="mb-1">Adventure starts here </h4>
          <p class="mb-6">Make your car buying & selling easy and fun!</p>

          @if ($errors->any())
            <div class="alert alert-danger" role="alert">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          @if (session('success'))
            <div class="alert alert-success" role="alert">
              {{ session('success') }}
            </div>
          @endif

          <form id="formAuthentication" class="mb-4" action="{{ route('register.post') }}" method="POST">
            @csrf
            <div class="mb-6 form-control-validation">
              <label for="name" class="form-label">Full Name</label>
              <input type="text" class="form-control" id="name" name="name"
                placeholder="Enter your full name" value="{{ old('name') }}" required />
            </div>
            <div class="mb-6 form-control-validation">
              <label for="email" class="form-label">Email Address</label>
              <input type="email" class="form-control" id="email" name="email"
                placeholder="Enter your email" value="{{ old('email') }}" required />
            </div>
            <div class="mb-6 form-password-toggle form-control-validation">
              <label class="form-label" for="password">Password</label>
              <div class="input-group input-group-merge">
                <input type="password" id="password" class="form-control" name="password"
                  placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                  aria-describedby="password" required />
                <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
              </div>
            </div>
            <div class="mb-6 form-password-toggle form-control-validation">
              <label class="form-label" for="password_confirmation">Confirm Password</label>
              <div class="input-group input-group-merge">
                <input type="password" id="password_confirmation" class="form-control" name="password_confirmation"
                  placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                  aria-describedby="password_confirmation" required />
                <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
              </div>
            </div>
            <div class="mb-6">
              <div class="form-check mb-0 ms-2">
                <input class="form-check-input" type="checkbox" id="terms" name="terms" required />
                <label class="form-check-label" for="terms">
                  I agree to the <a href="#" target="_blank">Terms & Conditions</a> and <a href="#" target="_blank">Privacy Policy</a>
                </label>
              </div>
            </div>
            <div class="mb-6">
              <button class="btn btn-primary d-grid w-100" type="submit">Create Account</button>
            </div>
          </form>

          <p class="text-center">
            <span>Already have an account?</span>
            <a href="{{ route('login') }}">
              <span>Sign in instead</span>
            </a>
          </p>

          <div class="divider my-6">
            <div class="divider-text">or</div>
          </div>

          <div class="d-flex justify-content-center">
            <a href="javascript:;" class="btn btn-icon rounded-circle btn-text-facebook me-1_5">
              <i class="icon-base ti tabler-brand-facebook-filled icon-20px"></i>
            </a>

            <a href="javascript:;" class="btn btn-icon rounded-circle btn-text-twitter me-1_5">
              <i class="icon-base ti tabler-brand-twitter-filled icon-20px"></i>
            </a>

            <a href="javascript:;" class="btn btn-icon rounded-circle btn-text-github me-1_5">
              <i class="icon-base ti tabler-brand-github-filled icon-20px"></i>
            </a>

            <a href="javascript:;" class="btn btn-icon rounded-circle btn-text-google-plus">
              <i class="icon-base ti tabler-brand-google-filled icon-20px"></i>
            </a>
          </div>
        </div>
      </div>
      <!-- /Register -->
    </div>
  </div>
</div>
@endsection
