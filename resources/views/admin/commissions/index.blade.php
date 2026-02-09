@extends('layouts.contentLayoutMaster')

@section('title', 'Commissions Management')

@section('content')
<div class="row">
    <!-- Stats Cards -->
    <div class="col-xl-4 col-md-6 col-12">
        <div class="card bg-warning text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 text-white">₩{{ number_format($totalPending) }}</h4>
                        <p class="mb-0">Pending Revenue</p>
                    </div>
                    <i class="feather icon-clock text-white opacity-50 font-large-2"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 col-12">
        <div class="card bg-success text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 text-white">₩{{ number_format($totalPaid) }}</h4>
                        <p class="mb-0">Collected Revenue</p>
                    </div>
                    <i class="feather icon-check-circle text-white opacity-50 font-large-2"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 col-12">
        <div class="card bg-secondary text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 text-white">₩{{ number_format($totalWaived) }}</h4>
                        <p class="mb-0">Waived Amount</p>
                    </div>
                    <i class="feather icon-slash text-white opacity-50 font-large-2"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Commissions List</h4>
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" id="filterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="feather icon-filter"></i> Filter: {{ ucfirst(request('status', 'All')) }}
                    </button>
                    <div class="dropdown-menu" aria-labelledby="filterDropdown">
                        <a class="dropdown-item" href="{{ route('admin.commissions.index', ['status' => 'all']) }}">All</a>
                        <a class="dropdown-item" href="{{ route('admin.commissions.index', ['status' => 'pending']) }}">Pending</a>
                        <a class="dropdown-item" href="{{ route('admin.commissions.index', ['status' => 'paid']) }}">Paid</a>
                        <a class="dropdown-item" href="{{ route('admin.commissions.index', ['status' => 'waived']) }}">Waived</a>
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
                                    <th>Seller</th>
                                    <th>Sale Price</th>
                                    <th>Commission</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($commissions as $commission)
                                    <tr>
                                        <td>#{{ $commission->id }}</td>
                                        <td>
                                            @if($commission->listing)
                                                <a href="{{ route('admin.listings.show', $commission->listing_id) }}">
                                                    {{ Str::limit($commission->listing->title, 30) }}
                                                </a>
                                            @else
                                                <span class="text-muted">Deleted Listing</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($commission->seller)
                                                <a href="{{ route('admin.users.show', $commission->seller_id) }}">
                                                    {{ $commission->seller->name }}
                                                </a>
                                            @else
                                                <span class="text-muted">Unknown User</span>
                                            @endif
                                        </td>
                                        <td>₩{{ number_format($commission->listing_price) }}</td>
                                        <td class="font-weight-bold">
                                            ₩{{ number_format($commission->final_commission) }}
                                            <small class="text-muted d-block">{{ $commission->commission_percentage }}%</small>
                                        </td>
                                        <td>
                                            @if($commission->status === 'paid')
                                                <div class="badge badge-success">Paid</div>
                                            @elseif($commission->status === 'pending')
                                                <div class="badge badge-warning">Pending</div>
                                            @else
                                                <div class="badge badge-secondary">Waived</div>
                                            @endif
                                        </td>
                                        <td>{{ $commission->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.commissions.show', $commission->id) }}" class="btn btn-sm btn-primary">
                                                <i class="feather icon-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <i class="feather icon-percent text-muted font-large-3"></i>
                                            <p class="mt-2 text-muted">No commissions found.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $commissions->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
