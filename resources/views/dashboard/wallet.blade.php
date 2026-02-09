@extends('layouts.app')

@section('title', 'My Wallet - Azal Cars')

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
    


    .wallet-card-custom {
        background: linear-gradient(135deg, #6041E0 0%, #452276 100%);
        border-radius: 24px;
        padding: 40px;
        color: white;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(96, 65, 224, 0.2);
    }
    .wallet-card-custom::after {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
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
                <h1>My Wallet</h1>
                <p>Manage your funds and transaction history.</p>
            </div>
            <div class="modern-date-badge">
                <span>{{ date('d M, Y') }}</span>
                <i class="far fa-calendar-alt"></i>
            </div>
        </div>

        <div class="wallet-card-custom" style="margin-bottom: 40px;">
            <div style="font-size: 14px; font-weight: 500; opacity: 0.8; margin-bottom: 12px;">Total Balance</div>
            <div style="font-size: 48px; font-weight: 800; margin-bottom: 32px;">{{ \App\Helpers\Helpers::formatPrice(auth()->user()->wallet_balance ?? 0) }}</div>
            
            <div style="display: flex; gap: 16px;">
                <button class="btn" onclick="openTopUpModal()" style="background: white; color: #6041E0; border-radius: 12px; padding: 12px 30px; font-weight: 700; border: none; cursor: pointer;">
                    <i class="fas fa-plus me-2"></i> Top Up
                </button>
                <button class="btn" style="background: rgba(255,255,255,0.1); color: white; border-radius: 12px; padding: 12px 30px; font-weight: 700; border: 1px solid rgba(255,255,255,0.2);">
                    <i class="fas fa-arrow-up me-2"></i> Withdraw
                </button>
            </div>
        </div>

        <div class="modern-section-header">
            <h3 class="modern-section-title">Recent Transactions</h3>
            <div class="modern-period-select">
                <span>Last 30 days</span>
                <i class="fas fa-chevron-down"></i>
            </div>
        </div>

        <div class="dashboard-card" style="padding: 0; overflow: hidden; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
            <div style="padding: 60px 20px; text-align: center; color: #94a3b8;">
                <i class="fas fa-receipt" style="font-size: 50px; color: #e2e8f0; display: block; margin-bottom: 20px;"></i>
                <p style="font-weight: 600;">No transactions yet.</p>
            </div>
        </div>
    </div>
</div>

@include('partials.topup-modal')
@endsection
