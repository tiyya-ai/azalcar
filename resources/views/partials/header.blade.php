<header class="cars-header">
    <div class="header-container">
        <div class="header-logo-container">
            <a href="{{ route('home') }}">
                <img src="{{ asset('assets/images/logo.png') }}" alt="AzalCars" class="header-logo-img">
            </a>
        </div>
        
        <nav class="header-nav">
            <a href="{{ route('listings.search') }}">Cars for Sale</a>
            <a href="{{ route('listings.search', ['condition' => 'new']) }}">New Cars</a>
            <a href="{{ route('listings.search', ['condition' => 'used']) }}">Used Cars</a>
            <a href="{{ route('news.index') }}">News & Videos</a>
            <a href="{{ route('listings.create') }}">Sell Your Car</a>
        </nav>
        
        <div class="header-actions">
            @include('partials.currency-selector')

            @auth
                <a href="{{ route('favorites.index') }}" class="header-btn">
                    <i class="far fa-heart"></i>
                </a>

                <a href="{{ auth()->user()->is_admin ? route('admin.messages.index') : route('messages.index') }}" class="header-btn header-icon-badge-link auth-hide-mobile">
                    <i class="far fa-envelope"></i>
                    <span class="header-icon-badge message-badge" style="display: none;">0</span>
                </a>

                <div class="user-dropdown-container">
                    <a href="javascript:void(0)" class="header-btn user-dropdown-toggle" title="{{ auth()->user()->name }}">
                        <i class="far fa-user"></i>
                    </a>
                    
                    <div class="user-dropdown-menu">
                        <a href="{{ auth()->user()->is_admin ? route('admin.dashboard') : route('dashboard') }}">
                            <i class="fas fa-columns"></i> Dashboard
                        </a>
                        <a href="{{ auth()->user()->is_admin ? route('admin.messages.index') : route('messages.index') }}">
                            <i class="far fa-envelope"></i> Messages
                        </a>
                        @if(!auth()->user()->is_admin)
                            <a href="{{ route('wallet.index') }}">
                                <i class="fas fa-wallet"></i> My Wallet
                            </a>
                            <a href="{{ route('settings.index') }}">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                        @else
                             <a href="{{ route('admin.settings.index') }}">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                        @endif
                        
                        <div class="dropdown-divider"></div>
                        
                        <form action="{{ route('logout') }}" method="POST" class="logout-form">
                            @csrf
                            <button type="submit" class="dropdown-item-btn">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="header-btn" title="Sign In">
                    <i class="far fa-user"></i>
                </a>
            @endauth
        </div>
    </div>
</header>

<style>
    /* ========== HEADER ========== */
    .cars-header {
        height: 56px;
        background: #ffffff;
        border-bottom: 1px solid #e5e7eb;
        position: sticky;
        top: 0;
        z-index: 1000;
        display: flex;
        align-items: center;
        width: 100%;
    }
    
    .header-container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 24px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 100%;
    }
    
    .header-logo-container {
        display: flex;
        align-items: center;
        margin: 0;
        padding: 0;
    }
    
    .header-logo-img {
        height: 28px;
        width: auto;
        object-fit: contain;
        display: block;
    }
    
    .header-nav {
        display: flex;
        gap: 32px;
        align-items: center;
        flex: 1;
        padding-left: 48px;
    }
    
    .header-nav a {
        font-size: 14px;
        font-weight: 600;
        color: #141817;
        text-decoration: none;
        transition: color 0.2s;
    }
    
    .header-nav a:hover {
        color: #6041E0;
    }
    
    .header-actions {
        display: flex;
        gap: 24px;
        align-items: center;
    }
    
    .header-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 700;
        color: #000000;
        text-decoration: none;
        transition: color 0.2s;
    }
    
    .header-btn:hover {
        color: #6041E0;
    }
    
    .header-btn i {
        font-size: 18px;
    }

    .header-icon-badge-link {
        position: relative;
    }

    .header-icon-badge {
        position: absolute;
        top: -6px;
        right: -8px;
        background-color: #ef4444;
        color: white;
        font-size: 10px;
        font-weight: 700;
        min-width: 16px;
        height: 16px;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 4px;
        border: 2px solid white;
        pointer-events: none;
    }

    /* DROPDOWN STYLES */
    .user-dropdown-container {
        position: relative;
        padding-bottom: 20px;
        margin-bottom: -20px;
        z-index: 1001; 
        display: flex;
        align-items: center;
    }
    
    .user-dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        width: 200px;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        padding: 8px 0;
        margin-top: 10px;
        z-index: 1002;
    }
    
    .user-dropdown-container::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        height: 15px;
    }

    .user-dropdown-container:hover .user-dropdown-menu,
    .user-dropdown-container.active .user-dropdown-menu {
        display: block;
        animation: fadeIn 0.2s ease-out;
    }
    
    .user-dropdown-menu.show {
        display: block;
    }
    
    .user-dropdown-menu a, .dropdown-item-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 16px;
        font-size: 14px;
        color: #374151;
        text-decoration: none;
        transition: background-color 0.15s;
        width: 100%;
        text-align: left;
        background: none;
        border: none;
        font-family: inherit;
        font-weight: 500;
        cursor: pointer;
    }
    
    .user-dropdown-menu a:hover, .dropdown-item-btn:hover {
        background-color: #f3f4f6;
        color: #6041E0;
    }
    
    .user-dropdown-menu i {
        width: 16px;
        text-align: center;
        color: #9ca3af;
    }
    
    .user-dropdown-menu a:hover i, .dropdown-item-btn:hover i {
        color: #6041E0;
    }
    
    .dropdown-divider {
        height: 1px;
        background-color: #e5e7eb;
        margin: 8px 0;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
    }
  
    @media (max-width: 1024px) {
        .header-nav {
            display: none;
        }
        .header-actions {
            gap: 12px;
        }
        .header-container {
            padding: 0 16px;
        }
    }

    @media (max-width: 768px) {
        .auth-hide-mobile {
            display: none !important;
        }
        .header-actions {
            gap: 8px;
        }
        .header-btn {
            padding: 4px;
        }
        .header-btn i {
            font-size: 20px;
        }
    }
</style>

@include('partials.auth-modal')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // User dropdown toggle
        const userDropdownToggle = document.querySelector('.user-dropdown-toggle');
        const userDropdownContainer = document.querySelector('.user-dropdown-container');
        
        if (userDropdownToggle && userDropdownContainer) {
            userDropdownToggle.addEventListener('click', function(e) {
                e.preventDefault();
                userDropdownContainer.classList.toggle('active');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!userDropdownContainer.contains(e.target)) {
                    userDropdownContainer.classList.remove('active');
                }
            });
        }

        // Global logic to override sign-in links to open modal
        const signInLinks = document.querySelectorAll('.header-btn[href*="login"], a[href*="login"], .open-login-modal');
        signInLinks.forEach(link => {
            if (!window.location.pathname.includes('/login') && !window.location.pathname.includes('/register')) {
                link.addEventListener('click', function(e) {
                    if (window.openAuthModal) {
                        e.preventDefault();
                        window.openAuthModal('login');
                    }
                });
            }
        });

        const registerLinks = document.querySelectorAll('a[href*="register"], .open-register-modal');
        registerLinks.forEach(link => {
            if (!window.location.pathname.includes('/login') && !window.location.pathname.includes('/register')) {
                link.addEventListener('click', function(e) {
                    if (window.openAuthModal) {
                        e.preventDefault();
                        window.openAuthModal('register');
                    }
                });
            }
        });
    });
</script>
