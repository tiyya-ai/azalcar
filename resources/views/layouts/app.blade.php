<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $global_meta_title ?? View::getSection('title', 'azal Cars') }}</title>
    <meta name="description" content="{{ $global_meta_description ?? 'The best car marketplace.' }}">
    <meta property="og:title" content="{{ $global_meta_title ?? View::getSection('title', 'azal Cars') }}">
    <meta property="og:description" content="{{ $global_meta_description ?? 'Discover thousands of cars.' }}">
    @if(isset($global_og_image))
    <meta property="og:image" content="{{ $global_og_image }}">
    @endif
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        window.userAuthenticated = {{ auth()->check() ? 'true' : 'false' }};
    </script>
    @stack('styles')
</head>
<body>
    @include('partials.header')

    <main class="main-content">
        @if(session('success'))
            <div class="container mt-4">
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container mt-4">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    @include('partials.footer')

    <!-- Bottom Navigation Bar (Mobile Only) -->
    <div class="bottom-nav">
        <a href="{{ url('/') }}" class="nav-item">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="{{ route('listings.search') }}" class="nav-item">
              <i class="fas fa-search"></i>
              <span>Search</span>
          </a>
        @auth
            <a href="{{ route('listings.create') }}" class="nav-item center-item">
                <div class="plus-btn">
                    <i class="fas fa-plus"></i>
                </div>
                <span>Post</span>
            </a>
            <a href="{{ route('messages.index') }}" class="nav-item">
                <i class="far fa-comment-dots"></i>
                <span>Messages</span>
            </a>
            <a href="{{ auth()->user()->is_admin ? route('admin.dashboard') : route('dashboard') }}" class="nav-item active">
                <i class="far fa-user"></i>
                <span>{{ auth()->user()->is_admin ? 'Admin' : 'Profile' }}</span>
            </a>
        @else
            <button type="button" class="nav-item center-item" onclick="document.getElementById('open-auth-modal').click()">
                <div class="plus-btn">
                    <i class="fas fa-plus"></i>
                </div>
                <span>Post</span>
            </button>
            <button type="button" class="nav-item" onclick="document.getElementById('open-auth-modal').click()">
                <i class="far fa-comment-dots"></i>
                <span>Messages</span>
            </button>
            <button type="button" class="nav-item active" onclick="document.getElementById('open-auth-modal').click()">
                <i class="far fa-user"></i>
                <span>Login</span>
            </button>
        @endauth
    </div>

    <script src="{{ asset('assets/js/script.js') }}"></script>
    @stack('scripts')
</body>
</html>
