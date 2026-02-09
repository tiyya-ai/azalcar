@extends('layouts.app')

@section('title', 'My Favorites - Azal Cars')

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
                <h1>My Favorites</h1>
                <p>Manage your saved car listings.</p>
            </div>
            <div class="modern-date-badge">
                <span>{{ date('d M, Y') }}</span>
                <i class="far fa-calendar-alt"></i>
            </div>
        </div>

        <div class="dashboard-card" style="padding: 0; overflow: hidden; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
            @if($favorites->isEmpty())
            <div style="padding: 80px 20px; text-align: center;">
                <i class="far fa-heart" style="font-size: 50px; color: #e2e8f0; display: block; margin-bottom: 20px;"></i>
                <p style="color: #64748b; font-size: 16px; margin-bottom: 24px;">No favorites yet. Save ads to view them later.</p>
                <a href="{{ route('listings.search') }}" class="btn btn-primary" style="background: #6041E0; border-radius: 12px; padding: 12px 30px;">
                    <i class="fas fa-search me-2"></i> Browse ads
                </a>
            </div>
            @else
            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Car Details</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($favorites as $favorite)
                        @php $listing = $favorite->listing; @endphp
                        @if($listing)
                        <tr>
                            <td width="100">
                                <div style="width: 80px; height: 60px; border-radius: 10px; overflow: hidden; background: #f1f5f9;">
                                    <img src="{{ $listing->main_image ? asset('storage/' . $listing->main_image) : 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=200' }}" 
                                         alt="" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-weight: 700; font-size: 15px; color: #1a1a1a; margin-bottom: 4px;">{{ $listing->title }}</span>
                                    <span style="font-size: 12px; color: #94a3b8;">
                                        <i class="fas fa-map-marker-alt me-1"></i> {{ $listing->location ?? 'Global' }}
                                        <span class="ms-3"><i class="far fa-calendar-alt me-1"></i> {{ $listing->year }}</span>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span style="font-weight: 800; color: #1a1a1a; font-size: 16px;">{!! \App\Helpers\Helpers::formatPrice($listing->price) !!}</span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <a href="{{ route('listings.show', $listing->slug) }}" class="modern-action-btn" title="View">
                                        <i class="far fa-eye"></i>
                                    </a>
                                    <form action="{{ route('favorites.toggle', $listing->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="modern-action-btn" style="color: #ef4444;" title="Remove">
                                            <i class="fas fa-heart-broken"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>


</div>
@endsection
