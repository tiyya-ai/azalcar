@extends('layouts.app')

@section('title', 'Dashboard - azalcars')

@push('styles')
<style>
    /* Dashboard specific overrides */
    body { background-color: #F8F9FB !important; overflow: hidden; height: 100vh; width: 100vw; }
    .main-content { margin-top: 0; height: 100vh; }
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
    .navbar { display: none; }
    .footer { display: none; }
    

</style>
@endpush

@section('content')
<div class="modern-dashboard-layout">
    <!-- Left Sidebar -->
    @include('dashboard.sidebar')

    <!-- Main Content Area -->
    <div class="modern-dashboard-content">
        <!-- Modern Welcome Header -->
        <div class="modern-welcome-section">
            <div class="modern-welcome-text">
                <h1>Hello, {{ explode(' ', auth()->user()->name)[0] }}</h1>
                <p>Track your marketplace progress. You're close to your goal!</p>
            </div>
            <div class="modern-date-badge">
                <span>{{ date('d M, Y') }}</span>
                <i class="far fa-calendar-alt"></i>
            </div>
        </div>

        <!-- Modern Stats Horizontal Bar -->
        <div class="modern-stats-bar">
            <div class="modern-stat-item">
                <div class="modern-stat-icon icon-blue">
                    <i class="far fa-thumbs-up"></i>
                </div>
                <div class="modern-stat-info">
                    <span class="modern-stat-label">Finished</span>
                    <div class="modern-stat-main">
                        <span class="modern-stat-value">{{ $stats['active_listings'] }}</span>
                        <span class="modern-stat-growth growth-up">
                            <i class="fas fa-caret-up"></i> +{{ rand(1, 5) }} tasks
                        </span>
                    </div>
                </div>
            </div>

            <div class="modern-stat-item">
                <div class="modern-stat-icon icon-orange">
                    <i class="far fa-clock"></i>
                </div>
                <div class="modern-stat-info">
                    <span class="modern-stat-label">Tracked</span>
                    <div class="modern-stat-main">
                        <span class="modern-stat-value">{{ rand(20, 45) }}h</span>
                        <span class="modern-stat-growth growth-down">
                            <i class="fas fa-caret-down"></i> -6 hours
                        </span>
                    </div>
                </div>
            </div>

            <div class="modern-stat-item">
                <div class="modern-stat-icon icon-green">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="modern-stat-info">
                    <span class="modern-stat-label">Efficiency</span>
                    <div class="modern-stat-main">
                        <span class="modern-stat-value">93%</span>
                        <span class="modern-stat-growth growth-up">
                            <i class="fas fa-caret-up"></i> +12%
                        </span>
                    </div>
                </div>
            </div>
        </div>


        <!-- My Listings Section -->
        <div class="modern-section-header">
            <h3 class="modern-section-title">My Listings</h3>
            <div class="modern-period-select">
                <span>All Time</span>
                <i class="fas fa-chevron-down"></i>
            </div>
        </div>

        <div class="dashboard-card" style="padding: 0; overflow: hidden; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Listing Details</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Views</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeListings as $listing)
                        <tr>
                            <td width="80">
                                <div style="width: 60px; height: 45px; border-radius: 8px; overflow: hidden; background: #f1f5f9;">
                                    <img src="{{ $listing->main_image ? asset('storage/' . $listing->main_image) : 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=200' }}" 
                                         alt="" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-weight: 700; font-size: 14px; color: #1a1a1a; margin-bottom: 2px;">{{ $listing->title }}</span>
                                    <span style="font-size: 11px; color: #94a3b8;"><i class="fas fa-map-marker-alt" style="margin-right: 4px;"></i> {{ $listing->location ?? 'Global' }}</span>
                                </div>
                            </td>
                            <td>
                                <span style="font-weight: 800; color: #1a1a1a; font-size: 14px;">₽ {{ number_format($listing->price) }}</span>
                            </td>
                            <td>
                                <span class="status-badge {{ $listing->status == 'active' ? 'status-active' : 'status-pending' }}" style="font-size: 10px; padding: 4px 10px; font-weight: 700;">
                                    {{ strtoupper($listing->status) }}
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 6px; color: #64748b; font-size: 12px; font-weight: 600;">
                                    <i class="far fa-eye"></i> {{ $listing->views_count }}
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <a href="{{ route('listings.frontend.edit', $listing->slug) }}" class="modern-action-btn" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('listings.show', $listing->slug) }}" class="modern-action-btn" title="View">
                                        <i class="far fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="padding: 60px; text-align: center;">
                                <div style="color: #94a3b8; margin-bottom: 20px;">
                                    <i class="far fa-file-alt" style="font-size: 40px; margin-bottom: 15px; display: block;"></i>
                                    <p style="font-weight: 600;">No ads yet.</p>
                                </div>
                                <a href="{{ route('listings.create') }}" class="btn btn-primary" style="background: #6041E0; border-radius: 12px; padding: 10px 25px;">
                                    <i class="fas fa-plus me-2"></i> Post your first ad
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


</div>
@endsection
