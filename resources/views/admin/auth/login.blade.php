@extends('layouts/blankLayout')

@section('title', 'Admin Login - azal Cars')

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
    <div class="authentication-inner py-6">
      <!-- Admin Login -->
      <div class="card">
        <div class="card-body">
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
          <h4 class="mb-1">Admin Panel Access</h4>
          <p class="mb-6">Please sign-in to manage the website</p>

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

          <form id="formAuthentication" class="mb-4" action="{{ route('admin.login') }}" method="POST">
            @csrf
            <div class="mb-6 form-control-validation">
              <label for="email" class="form-label">Email Address</label>
              <input type="email" class="form-control" id="email" name="email"
                placeholder="Enter your admin email" value="{{ old('email') }}" required autofocus />
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
            <div class="mb-6">
              <button class="btn btn-primary d-grid w-100" type="submit">Sign In to Admin Panel</button>
            </div>
          </form>

          <div class="text-center">
            <a href="{{ route('home') }}">
              <i class="icon-base ti tabler-arrow-left scaleX-n1-rtl"></i>
              Back to website
            </a>
          </div>
        </div>
      </div>
      <!-- /Admin Login -->
    </div>
  </div>
</div>
@endsection
