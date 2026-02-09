@extends('layouts.contentLayoutMaster')

@section('title', 'Reservation Details #' . $reservation->id)

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <a href="{{ route('admin.reservations.index') }}" class="btn btn-outline-secondary">
            <i class="feather icon-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<div class="row">
    <!-- Main Info -->
    <div class="col-md-8 col-12">
        <div class="card">
            <div class="card-header border-bottom">
                <h4 class="card-title">Reservation Info</h4>
                <div class="heading-elements">
                    <span class="badge badge-primary font-medium-1 px-2 py-1">{{ strtoupper($reservation->status) }}</span>
                </div>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <div class="row mb-5">
                        <div class="col-md-6 col-12">
                            <h6 class="text-muted">Reserved At</h6>
                            <p class="font-weight-bold">{{ $reservation->reserved_at ? $reservation->reserved_at->format('F d, Y H:i A') : 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 col-12">
                            <h6 class="text-muted">Expires At</h6>
                            <p class="font-weight-bold {{ $reservation->status === 'active' ? 'text-warning' : '' }}">
                                {{ $reservation->expires_at ? $reservation->expires_at->format('F d, Y H:i A') : 'N/A' }}
                            </p>
                        </div>
                    </div>

                    <h5 class="mb-2">Financial Details</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td class="font-weight-bold">Listing Price</td>
                                    <td class="text-right">{{ \App\Helpers\Helpers::formatPrice($reservation->listing_price) }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Deposit Percentage</td>
                                    <td class="text-right">{{ $reservation->deposit_percentage }}%</td>
                                </tr>
                                <tr class="bg-light">
                                    <td class="font-weight-bold font-large-1">Deposit Amount</td>
                                    <td class="text-right font-weight-bold font-large-1 text-primary">{{ \App\Helpers\Helpers::formatPrice($reservation->deposit_amount) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if($reservation->status === 'cancelled' || $reservation->status === 'expired')
                        <div class="alert alert-secondary mt-4">
                            <h5 class="alert-heading"><i class="feather icon-info"></i> Forfeiture Info</h5>
                            <p class="mb-0">
                                Deposit Forfeited: {{ $reservation->deposit_forfeited ? 'Yes' : 'No' }}<br>
                                Amount: {{ \App\Helpers\Helpers::formatPrice($reservation->forfeiture_amount) }} ({{ \App\Helpers\Helpers::formatPrice($reservation->forfeiture_amount/2) }} to seller, {{ \App\Helpers\Helpers::formatPrice($reservation->forfeiture_amount/2) }} to platform)
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Transaction Record -->
        @if($reservation->transaction)
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Initial Deposit Transaction</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                         <div class="d-flex justify-content-between">
                            <span>Transaction ID:</span>
                            <span class="font-weight-bold">#{{ $reservation->transaction->id }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span>Amount Deducted:</span>
                            <span class="font-weight-bold text-danger">-{{ \App\Helpers\Helpers::formatPrice(abs($reservation->transaction->amount)) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="col-md-4 col-12">
        <!-- Participants -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Participants</h4>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <h6 class="mb-1 text-primary">Buyer</h6>
                    @if($reservation->user)
                        <div class="d-flex align-items-center mb-2">
                             <div class="avatar mr-1 bg-primary">
                                <span class="avatar-content">{{ substr($reservation->user->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="mb-0 font-weight-bold">{{ $reservation->user->name }}</p>
                                <small>{{ $reservation->user->email }}</small>
                            </div>
                        </div>
                        <a href="{{ route('admin.users.show', $reservation->user_id) }}" class="btn btn-sm btn-outline-primary mb-3">Profile</a>
                    @else
                         <p class="text-muted">Unknown User</p>
                    @endif

                    <div class="divider"></div>

                    <h6 class="mb-1 text-info">Seller</h6>
                     @if($reservation->seller)
                        <div class="d-flex align-items-center mb-2">
                             <div class="avatar mr-1 bg-info">
                                <span class="avatar-content">{{ substr($reservation->seller->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="mb-0 font-weight-bold">{{ $reservation->seller->name }}</p>
                                <small>{{ $reservation->seller->email }}</small>
                            </div>
                        </div>
                        <a href="{{ route('admin.users.show', $reservation->seller_id) }}" class="btn btn-sm btn-outline-info">Profile</a>
                    @else
                         <p class="text-muted">Unknown Seller</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Listing -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Listing</h4>
            </div>
            <div class="card-content">
                <div class="card-body">
                     @if($reservation->listing)
                        @if($reservation->listing->main_image)
                            <img src="{{ asset('storage/' . $reservation->listing->main_image) }}" class="img-fluid rounded mb-2" alt="Car Image">
                        @endif
                        <h6>{{ $reservation->listing->title }}</h6>
                        <a href="{{ route('listings.show', $reservation->listing->slug) }}" target="_blank" class="btn btn-outline-secondary btn-block mt-2">View Listing</a>
                    @else
                        <div class="alert alert-warning mb-0">Listing Deleted</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Admin Actions -->
        @if($reservation->status === 'active')
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h4 class="card-title text-white">Admin Overrides</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <p class="text-muted font-small-3">Warning: These actions cannot be undone and involve financial transactions.</p>
                        
                        <form action="{{ route('admin.reservations.expire', $reservation->id) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-block" onclick="return confirm('Force EXPIRE this reservation? Deposit will be forfeited.')">
                                <i class="feather icon-clock"></i> Force Expire
                            </button>
                        </form>

                        <form action="{{ route('admin.reservations.cancel', $reservation->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Force CANCEL this reservation? Deposit will be forfeited.')">
                                <i class="feather icon-x-circle"></i> Force Cancel
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
