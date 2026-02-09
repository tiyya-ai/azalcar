@php
    $routeName = request()->route()->getName();
@endphp

<aside class="profile-sidebar" style="background: #2F3349; height: 100vh; width: 260px; min-width: 260px; position: sticky; top: 0; display: flex; flex-direction: column; color: #B6BEE3;">
    <div style="padding: 24px; text-align: center;">
        <a href="{{ route('home') }}" style="display: block;">
            <img src="{{ asset('assets/images/logo-footer.png') }}" alt="AzalCars" style="max-height: 25px; width: auto;">
        </a>
    </div>

    <nav class="profile-menu" style="flex: 1; padding: 0 16px; background: transparent !important; border: none !important; box-shadow: none !important;">
        <a href="{{ route('dashboard') }}" class="menu-item {{ $routeName == 'dashboard' ? 'active' : '' }}" style="display: flex; align-items: center; gap: 12px; padding: 10px 16px; border-radius: 8px; margin-bottom: 4px; font-weight: 500; color: #B6BEE3; transition: all 0.2s;">
            <i class="fas fa-th-large" style="width: 20px; color: {{ $routeName == 'dashboard' ? 'inherit' : '#7367F0' }};"></i> Dashboard
        </a>
        <a href="{{ route('messages.index') }}" class="menu-item {{ str_contains($routeName, 'messages') ? 'active' : '' }}" style="display: flex; align-items: center; gap: 12px; padding: 10px 16px; border-radius: 8px; margin-bottom: 4px; font-weight: 500; color: #B6BEE3; transition: all 0.2s;">
            <i class="fas fa-comment-alt" style="width: 20px; color: {{ str_contains($routeName, 'messages') ? 'inherit' : '#28C76F' }};"></i> Messages
        </a>
        <a href="{{ route('favorites.index') }}" class="menu-item {{ $routeName == 'favorites.index' ? 'active' : '' }}" style="display: flex; align-items: center; gap: 12px; padding: 10px 16px; border-radius: 8px; margin-bottom: 4px; font-weight: 500; color: #B6BEE3; transition: all 0.2s;">
            <i class="fas fa-heart" style="width: 20px; color: {{ $routeName == 'favorites.index' ? 'inherit' : '#EA5455' }};"></i> Favorites
        </a>
        <a href="{{ route('notifications.index') }}" class="menu-item {{ $routeName == 'notifications.index' ? 'active' : '' }}" style="display: flex; align-items: center; gap: 12px; padding: 10px 16px; border-radius: 8px; margin-bottom: 4px; font-weight: 500; color: #B6BEE3; transition: all 0.2s;">
            <i class="fas fa-bell" style="width: 20px; color: {{ $routeName == 'notifications.index' ? 'inherit' : '#FF9F43' }};"></i> Notifications
        </a>
        <a href="{{ route('wallet.index') }}" class="menu-item {{ str_contains($routeName, 'wallet') ? 'active' : '' }}" style="display: flex; align-items: center; gap: 12px; padding: 10px 16px; border-radius: 8px; margin-bottom: 4px; font-weight: 500; color: #B6BEE3; transition: all 0.2s;">
            <i class="fas fa-wallet" style="width: 20px; color: {{ str_contains($routeName, 'wallet') ? 'inherit' : '#00CFE8' }};"></i> Wallet
        </a>
        <a href="{{ route('reservations.index') }}" class="menu-item {{ str_contains($routeName, 'reservations') ? 'active' : '' }}" style="display: flex; align-items: center; gap: 12px; padding: 10px 16px; border-radius: 8px; margin-bottom: 4px; font-weight: 500; color: #B6BEE3; transition: all 0.2s;">
            <i class="fas fa-calendar-check" style="width: 20px; color: {{ str_contains($routeName, 'reservations') ? 'inherit' : '#1ABCFE' }};"></i> Reservations
        </a>
        
        <div style="height: 1px; background: rgba(226, 232, 240, 0.1); margin: 20px 16px;"></div>
        
        <a href="{{ route('settings.index') }}" class="menu-item {{ $routeName == 'settings.index' ? 'active' : '' }}" style="display: flex; align-items: center; gap: 12px; padding: 10px 16px; border-radius: 8px; margin-bottom: 4px; font-weight: 500; color: #B6BEE3; transition: all 0.2s;">
            <i class="fas fa-cog" style="width: 20px; color: {{ $routeName == 'settings.index' ? 'inherit' : '#A8AAAE' }};"></i> Settings
        </a>
    </nav>

    <div class="modern-upgrade-card" style="margin: 20px; padding: 20px; border-radius: 12px; background: rgba(115, 103, 240, 0.12); border: 1px solid rgba(115, 103, 240, 0.2);">
        <h4 style="color: white; font-size: 15px; font-weight: 700; margin-bottom: 8px;">Upgrade to Pro</h4>
        <p style="color: #B6BEE3; font-size: 12px; line-height: 1.5; margin-bottom: 15px;">Unlock premium features & analytics</p>
        <a href="#" class="btn-modern-upgrade" style="display: block; width: 100%; text-align: center; background: #7367F0; color: white; padding: 8px; border-radius: 6px; font-size: 13px; font-weight: 600; text-decoration: none;">Upgrade Now</a>
    </div>

    <div style="padding: 20px; border-top: 1px solid rgba(226, 232, 240, 0.05);">
        <form action="{{ route('logout') }}" method="POST" id="sidebar-logout-form" class="d-none">
            @csrf
        </form>
        <a href="#" onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();" class="menu-item" style="display: flex; align-items: center; gap: 12px; padding: 10px 16px; border-radius: 8px; font-weight: 500; color: #EA5455; transition: all 0.2s;">
            <i class="fas fa-sign-out-alt" style="width: 20px;"></i> Log out
        </a>
    </div>
</aside>

<style>
    .profile-sidebar .menu-item:hover:not(.active) {
        background: rgba(255, 255, 255, 0.05);
        color: white !important;
    }
    .profile-sidebar .menu-item.active {
        background: linear-gradient(72.47deg, #7367f0 22.16%, rgba(115, 103, 240, 0.7) 76.47%);
        box-shadow: 0px 2px 6px rgba(115, 103, 240, 0.48);
        color: white !important;
    }
    .profile-sidebar .menu-item.active i {
        color: white !important;
    }
</style>
