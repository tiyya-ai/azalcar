@extends('layouts.app')

@section('title', 'Account Settings - Azal Cars')

@push('styles')
<style>
    /* Dashboard specific overrides */
    body { background-color: #F8F9FB !important; overflow: hidden; height: 100vh; width: 100vw; margin: 0; padding: 0; }
    .main-content { margin-top: 0 !important; height: 100vh !important; width: 100vw !important; max-width: 100vw !important; padding: 0 !important; }
    .modern-dashboard-layout { 
        display: grid; 
        grid-template-columns: 260px 1fr; 
        gap: 0; 
        height: 100vh; 
        width: 100vw; 
        overflow: hidden; 
    }
    .modern-dashboard-content { 
        padding: 40px; 
        background: #F8F9FB; 
        overflow-y: auto; 
        height: 100vh; 
        position: relative;
    }
    .navbar { display: none !important; }
    .footer { display: none !important; }
    


    .form-group {
        margin-bottom: 24px;
    }
    .form-group label {
        display: block;
        font-weight: 700;
        font-size: 14px;
        color: #1a1a1a;
        margin-bottom: 8px;
    }
    .form-control-modern {
        width: 100%;
        padding: 12px 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        font-size: 14px;
        color: #1a1a1a;
        transition: all 0.2s;
    }
    .form-control-modern:focus {
        background: white;
        border-color: #6041E0;
        box-shadow: 0 0 0 4px rgba(96, 65, 224, 0.1);
        outline: none;
    }
    .settings-section {
        margin-bottom: 40px;
    }
</style>
@endpush

@section('content')
<div class="modern-dashboard-layout">
    <!-- Left Sidebar -->
    @include('dashboard.sidebar')

    <!-- Main Content Area -->
    <div class="modern-dashboard-content">
        <!-- Modern Header -->
        <div class="modern-welcome-section">
            <div class="modern-welcome-text">
                <h1>Settings</h1>
                <p>Manage your account preferences and security.</p>
            </div>
            <div class="modern-date-badge">
                <span>{{ date('d M, Y') }}</span>
                <i class="far fa-calendar-alt"></i>
            </div>
        </div>

        @if(session('success'))
            <div style="background: #f0fdf4; color: #15803d; padding: 16px; border-radius: 12px; margin-bottom: 24px; font-weight: 600; border: 1px solid #dcfce7;">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif

        <div class="dashboard-card" style="border-radius: 20px; border: 1px solid #e2e8f0; padding: 40px;">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="settings-section">
                    <h3 style="font-size: 18px; font-weight: 800; color: #1a1a1a; margin-bottom: 24px;">Profile Information</h3>
                    
                    <div style="display: flex; align-items: center; gap: 32px; margin-bottom: 32px;">
                        <img src="{{ auth()->user()->avatar ?? 'https://i.pravatar.cc/150?u=' . auth()->id() }}" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 4px solid #f8fafc; box-shadow: 0 4px 12px rgba(0,0,0,0.05);" alt="Avatar">
                        <div>
                            <label style="display: block; font-weight: 700; font-size: 14px; margin-bottom: 8px;">Profile Photo</label>
                            <input type="file" name="avatar" class="form-control-modern" style="padding: 8px;">
                            <p style="font-size: 12px; color: #94a3b8; margin-top: 8px;">Allowed formats: JPG, PNG. Max size: 2MB.</p>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" class="form-control-modern" value="{{ old('name', auth()->user()->name) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" class="form-control-modern" value="{{ old('email', auth()->user()->email) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="text" id="phone" name="phone" class="form-control-modern" value="{{ old('phone', auth()->user()->phone) }}" placeholder="+1 (555) 000-0000">
                        </div>
                    </div>
                </div>

                <div class="settings-section" style="border-top: 1px solid #f1f5f9; padding-top: 32px;">
                    <h3 style="font-size: 18px; font-weight: 800; color: #1a1a1a; margin-bottom: 24px;">Change Password</h3>
                    <p style="font-size: 13px; color: #64748b; margin-bottom: 24px;">Leave these fields empty if you don't want to change your password.</p>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" id="password" name="password" class="form-control-modern">
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control-modern">
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 16px; margin-top: 40px;">
                    <button type="reset" style="padding: 12px 24px; border-radius: 12px; font-weight: 700; border: 1px solid #e2e8f0; background: white; color: #4b5563; cursor: pointer;">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background: #6041E0; border-radius: 12px; padding: 12px 40px; font-weight: 700;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>


</div>
@endsection
