@extends('layouts.contentLayoutMaster')

@section('title', 'Reservations Management')

@section('content')
<div class="row">
    <!-- Stats Cards -->
    <div class="col-xl-3 col-md-4 col-sm-6">
        <div class="card text-center">
            <div class="card-body">
                <div class="avatar bg-light-primary p-50 mb-1">
                    <div class="avatar-content">
                        <i class="feather icon-clock font-medium-5"></i>
                    </div>
                </div>
                <h2 class="text-bold-700">{{ $stats['active'] }}</h2>
                <p class="text-muted mb-0">Active</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-4 col-sm-6">
        <div class="card text-center">
            <div class="card-body">
                <div class="avatar bg-light-success p-50 mb-1">
                    <div class="avatar-content">
                        <i class="feather icon-check-circle font-medium-5"></i>
                    </div>
                </div>
                <h2 class="text-bold-700">{{ $stats['completed'] }}</h2>
                <p class="text-muted mb-0">Completed</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-4 col-sm-6">
        <div class="card text-center">
            <div class="card-body">
                <div class="avatar bg-light-danger p-50 mb-1">
                    <div class="avatar-content">
                        <i class="feather icon-alert-circle font-medium-5"></i>
                    </div>
                </div>
                <h2 class="text-bold-700">{{ $stats['expired'] }}</h2>
                <p class="text-muted mb-0">Expired</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-4 col-sm-6">
        <div class="card text-center">
            <div class="card-body">
                <div class="avatar bg-light-secondary p-50 mb-1">
                    <div class="avatar-content">
                        <i class="feather icon-x-circle font-medium-5"></i>
                    </div>
                </div>
                <h2 class="text-bold-700">{{ $stats['cancelled'] }}</h2>
                <p class="text-muted mb-0">Cancelled</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Reservations List</h4>
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" id="filterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="feather icon-filter"></i> Filter: {{ ucfirst(request('status', 'All')) }}
                    </button>
                    <div class="dropdown-menu" aria-labelledby="filterDropdown">
                        <a class="dropdown-item" href="{{ route('admin.reservations.index', ['status' => 'all']) }}">All</a>
                        <a class="dropdown-item" href="{{ route('admin.reservations.index', ['status' => 'active']) }}">Active</a>
                        <a class="dropdown-item" href="{{ route('admin.reservations.index', ['status' => 'completed']) }}">Completed</a>
                        <a class="dropdown-item" href="{{ route('admin.reservations.index', ['status' => 'expired']) }}">Expired</a>
                        <a class="dropdown-item" href="{{ route('admin.reservations.index', ['status' => 'cancelled']) }}">Cancelled</a>
                    </div>
                </div>
            </div>
            <div class="card-content">
                <div class="card-body card-dashboard">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Listing</th>
                                    <th>Buyer</th>
                                    <th>Seller</th>
                                    <th>Deposit</th>
                                    <th>Status</th>
                                    <th>Expires/End</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reservations as $reservation)
                                    <tr>
                                        <td>#{{ $reservation->id }}</td>
                                            @if($reservation->listing)
                                                <a href="{{ route('admin.listings.show', $reservation->listing_id) }}">
                                                    {{ \Illuminate\Support\Str::limit($reservation->listing->title, 20) }}
                                                </a>
                                            @else
                                                <span class="text-muted">Deleted Listing</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($reservation->user)
                                                {{ $reservation->user->name }}
                                            @else
                                                <span class="text-muted">Unknown</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($reservation->seller)
                                                {{ $reservation->seller->name }}
                                            @else
                                                <span class="text-muted">Unknown</span>
                                            @endif
                                        </td>
                                        <td class="font-weight-bold">{{ \App\Helpers\Helpers::formatPrice($reservation->deposit_amount) }}</td>
                                        <td>
                                            @if($reservation->status === 'active')
                                                <div class="badge badge-primary">Active</div>
                                            @elseif($reservation->status === 'completed')
                                                <div class="badge badge-success">Completed</div>
                                            @elseif($reservation->status === 'expired')
                                                <div class="badge badge-danger">Expired</div>
                                            @else
                                                <div class="badge badge-secondary">Cancelled</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($reservation->status === 'active')
                                                <span class="text-warning">{{ $reservation->expires_at ? $reservation->expires_at->diffForHumans() : 'N/A' }}</span>
                                            @else
                                                {{ $reservation->updated_at->format('M d, Y') }}
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.reservations.show', $reservation->id) }}" class="btn btn-sm btn-primary">
                                                <i class="feather icon-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <i class="feather icon-calendar text-muted font-large-3"></i>
                                            <p class="mt-2 text-muted">No reservations found.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $reservations->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
