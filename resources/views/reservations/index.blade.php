@extends('layouts.app')

@section('title', 'My Reservations - Azal Cars')

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
    


    .reservation-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 24px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .reservation-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.05);
    }
    .status-badge-modern {
        padding: 6px 12px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .status-active { background: #dcfce7; color: #166534; }
    .status-completed { background: #eff6ff; color: #1e40af; }
    .status-expired { background: #fee2e2; color: #991b1b; }
    .status-cancelled { background: #f8fafc; color: #64748b; }
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
                <h1>My Reservations</h1>
                <p>Track and manage your car deposits and bookings.</p>
            </div>
            <div class="modern-date-badge">
                <span>{{ date('d M, Y') }}</span>
                <i class="far fa-calendar-alt"></i>
            </div>
        </div>

        @if($reservations->isEmpty())
        <div class="dashboard-card" style="padding: 80px 20px; text-align: center; border-radius: 20px; border: 1px solid #e2e8f0;">
            <i class="fas fa-calendar-times" style="font-size: 50px; color: #e2e8f0; display: block; margin-bottom: 20px;"></i>
            <h3 style="font-size: 20px; font-weight: 800; color: #1a1a1a; margin-bottom: 12px;">No Reservations Yet</h3>
            <p style="color: #64748b; margin-bottom: 32px;">You haven't made any car reservations yet.</p>
            <a href="{{ route('listings.search') }}" class="btn btn-primary" style="background: #6041E0; border-radius: 12px; padding: 12px 30px;">
                <i class="fas fa-search me-2"></i> Browse Cars
            </a>
        </div>
        @else
            @foreach($reservations as $reservation)
            <div class="reservation-card">
                <div style="display: flex; flex-direction: column; md-flex-direction: row;">
                    <div style="display: flex; padding: 24px; gap: 24px;">
                        <div style="width: 180px; height: 135px; border-radius: 16px; overflow: hidden; background: #f1f5f9; flex-shrink: 0;">
                            <img src="{{ $reservation->listing->main_image ? asset('storage/' . $reservation->listing->main_image) : 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=400' }}" 
                                 alt="" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                                <div>
                                    <h3 style="font-size: 18px; font-weight: 800; color: #1a1a1a; margin-bottom: 4px;">
                                        <a href="{{ route('reservations.show', $reservation->id) }}" style="color: inherit; text-decoration: none;">{{ $reservation->listing->title }}</a>
                                    </h3>
                                    <div style="font-size: 13px; color: #94a3b8; font-weight: 600;">
                                        {{ $reservation->listing->year }} • {{ number_format($reservation->listing->mileage) }} km
                                    </div>
                                </div>
                                <span class="status-badge-modern status-{{ $reservation->status }}">
                                    {{ $reservation->status }}
                                </span>
                            </div>

                            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; background: #f8fafc; padding: 16px; border-radius: 12px;">
                                <div>
                                    <div style="font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px;">Deposit</div>
                                    <div style="font-size: 14px; font-weight: 800; color: #1a1a1a;">{{ \App\Helpers\Helpers::formatPrice($reservation->deposit_amount) }}</div>
                                </div>
                                <div>
                                    <div style="font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px;">Date</div>
                                    <div style="font-size: 14px; font-weight: 700; color: #1a1a1a;">{{ $reservation->reserved_at->format('M d, Y') }}</div>
                                </div>
                                <div>
                                    <div style="font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px;">Expires</div>
                                    <div style="font-size: 14px; font-weight: 700; color: {{ $reservation->status === 'active' ? '#f59e0b' : '#1a1a1a' }};">
                                        {{ $reservation->expires_at->format('M d, H:i') }}
                                    </div>
                                </div>
                                <div>
                                    <div style="font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px;">Extensions</div>
                                    <div style="font-size: 14px; font-weight: 700; color: #1a1a1a;">{{ $reservation->extension_count }}/3</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="padding: 16px 24px; background: #fcfcfd; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end; gap: 12px;">
                        <a href="{{ route('reservations.show', $reservation->id) }}" class="modern-action-btn" style="width: auto; padding: 0 20px; font-size: 12px; font-weight: 700;">
                            View Full Details
                        </a>
                        @if($reservation->status === 'active')
                            @if($reservation->extension_count < 3)
                            <form action="{{ route('reservations.extend', $reservation->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="modern-action-btn" style="width: auto; padding: 0 20px; background: #22c55e; color: white; border-color: #22c55e; font-size: 12px; font-weight: 700;">
                                    Extend Reservation
                                </button>
                            </form>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
            <div style="margin-top: 24px;">{{ $reservations->links() }}</div>
        @endif
    </div>


</div>

<script>
    @stack('scripts')
</script>
@endsection
