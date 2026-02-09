@extends('layouts.app')

@section('title', 'Reservation Details - Azal Cars')

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

    .reservation-detail-card {
        background: white;
        border-radius: 24px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        margin-bottom: 32px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
    }
    .st-badge {
        padding: 8px 16px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
    }
    .st-active { background: #dcfce7; color: #166534; }
    .st-completed { background: #eff6ff; color: #1e40af; }
    .st-expired { background: #fee2e2; color: #991b1b; }
    .st-cancelled { background: #f8fafc; color: #64748b; }
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
                <a href="{{ route('reservations.index') }}" style="color: #6041E0; text-decoration: none; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 12px;">
                    <i class="fas fa-arrow-left"></i> Back to Reservations
                </a>
                <h1>Reservation #{{ $reservation->id }}</h1>
                <p>Detailed overview of your car booking.</p>
            </div>
            <div class="modern-date-badge">
                <span class="st-badge st-{{ $reservation->status }}">{{ $reservation->status }}</span>
            </div>
        </div>

        <div class="reservation-detail-card">
            <!-- Header Info -->
            <div style="padding: 32px; border-bottom: 1px solid #f1f5f9; display: flex; gap: 32px;">
                <div style="width: 240px; height: 160px; border-radius: 16px; overflow: hidden; background: #f1f5f9; flex-shrink: 0;">
                    <img src="{{ $reservation->listing->main_image ? asset('storage/' . $reservation->listing->main_image) : 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=600' }}" 
                         alt="" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div style="flex: 1;">
                    <h2 style="font-size: 24px; font-weight: 800; color: #1a1a1a; margin-bottom: 8px;">{{ $reservation->listing->title }}</h2>
                    <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                        <span style="font-size: 14px; font-weight: 600; color: #94a3b8;"><i class="fas fa-calendar-alt me-2"></i> {{ $reservation->listing->year }}</span>
                        <span style="font-size: 14px; font-weight: 600; color: #94a3b8;"><i class="fas fa-tachometer-alt me-2"></i> {{ number_format($reservation->listing->mileage) }} km</span>
                        <span style="font-size: 14px; font-weight: 600; color: #94a3b8;"><i class="fas fa-map-marker-alt me-2"></i> {{ $reservation->listing->location }}</span>
                    </div>
                    <div style="font-size: 32px; font-weight: 800; color: #6041E0;">{{ \App\Helpers\Helpers::formatPrice($reservation->listing_price) }}</div>
                </div>
            </div>

            <!-- Dashboard Style Info Grid -->
            <div style="padding: 32px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px; background: #fbfbfc;">
                <div style="background: white; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0;">
                    <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 8px;">Deposit Amount</div>
                    <div style="font-size: 18px; font-weight: 800; color: #1a1a1a;">{{ \App\Helpers\Helpers::formatPrice($reservation->deposit_amount) }}</div>
                    <div style="font-size: 12px; color: #6041E0; margin-top: 4px; font-weight: 600;">{{ $reservation->deposit_percentage }}% of price</div>
                </div>
                <div style="background: white; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0;">
                    <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 8px;">Active Until</div>
                    <div style="font-size: 18px; font-weight: 800; color: #1a1a1a;">{{ $reservation->expires_at->format('M d, H:i') }}</div>
                    <div style="font-size: 12px; color: #f97316; margin-top: 4px; font-weight: 600;">
                        @if($reservation->status === 'active')
                            {{ $reservation->expires_at->diffForHumans() }}
                        @else
                            {{ ucfirst($reservation->status) }}
                        @endif
                    </div>
                </div>
                <div style="background: white; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0;">
                    <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 8px;">Extensions Used</div>
                    <div style="font-size: 18px; font-weight: 800; color: #1a1a1a;">{{ $reservation->extension_count }} / 3</div>
                    <div style="font-size: 12px; color: #94a3b8; margin-top: 4px; font-weight: 600;">{{ 3 - $reservation->extension_count }} remaining</div>
                </div>
            </div>

            <!-- Seller & Additional Info -->
            <div style="padding: 32px; display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center; gap: 16px;">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: #6041E0; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 18px;">
                        {{ substr($reservation->seller->name, 0, 1) }}
                    </div>
                    <div>
                        <div style="font-size: 15px; font-weight: 800; color: #1a1a1a;">{{ $reservation->seller->name }}</div>
                        <div style="font-size: 12px; color: #94a3b8;">Private Seller</div>
                    </div>
                </div>
                <a href="{{ route('messages.index') }}" class="modern-action-btn" style="width: auto; padding: 0 24px; font-weight: 700; background: #6041E0; color: white; border-color: #6041E0;">
                    Message Seller
                </a>
            </div>
        </div>
    </div>


</div>

<script>
    @stack('scripts')
</script>
@endsection
