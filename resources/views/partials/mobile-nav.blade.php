<!-- Bottom Navigation Bar (Mobile Only) -->
<div class="bottom-nav">
    <a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
        <i class="fas fa-home"></i>
        <span>Home</span>
    </a>
    <a href="{{ route('listings.search') }}" class="nav-item {{ request()->routeIs('listings.search') ? 'active' : '' }}">
        <i class="fas fa-search"></i>
        <span>Search</span>
    </a>
    <a href="{{ route('listings.create') }}" class="nav-item center-item">
        <div class="plus-btn">
            <i class="fas fa-plus"></i>
        </div>
        <span>Post</span>
    </a>
    @auth
        <a href="{{ route('messages.index') }}" class="nav-item {{ request()->routeIs('messages.index') ? 'active' : '' }}">
            <i class="far fa-comment-dots"></i>
            <span>Messages</span>
        </a>
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="far fa-user"></i>
            <span>Profile</span>
        </a>
    @else
        <a href="#" class="nav-item" id="mobile-messages-login">
            <i class="far fa-comment-dots"></i>
            <span>Messages</span>
        </a>
        <a href="#" class="nav-item" id="mobile-profile-login">
            <i class="far fa-user"></i>
            <span>Profile</span>
        </a>
    @endauth
</div>
