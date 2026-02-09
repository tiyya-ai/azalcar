@extends('layouts.contentLayoutMaster')

@section('title', 'Commission Details #' . $commission->id)

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <a href="{{ route('admin.commissions.index') }}" class="btn btn-outline-secondary">
            <i class="feather icon-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<div class="row">
    <!-- Main Details -->
    <div class="col-md-8 col-12">
        <div class="card">
            <div class="card-header border-bottom">
                <h4 class="card-title">Commission Breakdown</h4>
                <div class="heading-elements">
                    @if($commission->status === 'paid')
                        <span class="badge badge-success font-medium-1 px-2 py-1">Paid on {{ $commission->paid_at ? $commission->paid_at->format('M d, Y') : 'N/A' }}</span>
                    @elseif($commission->status === 'pending')
                        <span class="badge badge-warning font-medium-1 px-2 py-1">Pending Payment</span>
                    @else
                        <span class="badge badge-secondary font-medium-1 px-2 py-1">Waived</span>
                    @endif
                </div>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <div class="row mb-5">
                        <div class="col-md-6 col-12">
                            <h6 class="text-muted">Status</h6>
                            <p class="font-weight-bold text-uppercase">{{ $commission->status }}</p>
                        </div>
                        <div class="col-md-6 col-12">
                            <h6 class="text-muted">Created Date</h6>
                            <p class="font-weight-bold">{{ $commission->created_at->format('F d, Y H:i A') }}</p>
                        </div>
                    </div>

                    <h5 class="mb-2">Calculations</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td class="font-weight-bold">Listing Sale Price</td>
                                    <td class="text-right">₩{{ number_format($commission->listing_price) }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Commission Rate</td>
                                    <td class="text-right">{{ $commission->commission_percentage }}%</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Calculated Amount</td>
                                    <td class="text-right">₩{{ number_format($commission->commission_amount) }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Cap Limit (Max)</td>
                                    <td class="text-right text-danger">₩{{ number_format($commission->commission_cap) }}</td>
                                </tr>
                                <tr class="bg-light">
                                    <td class="font-weight-bold font-large-1">Final Commission</td>
                                    <td class="text-right font-weight-bold font-large-1 text-primary">₩{{ number_format($commission->final_commission) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if($commission->notes)
                        <div class="mt-4">
                            <h5 class="mb-2">Notes</h5>
                            <div class="alert alert-secondary mb-0">
                                {!! nl2br(e($commission->notes)) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Related Transaction -->
        @if($commission->transaction)
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Related Transaction</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <span>Transaction ID:</span>
                            <span class="font-weight-bold">#{{ $commission->transaction->id }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span>Amount:</span>
                            <span class="font-weight-bold">₩{{ number_format($commission->transaction->amount) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span>Date:</span>
                            <span class="font-weight-bold">{{ $commission->transaction->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Sidebar Actions -->
    <div class="col-md-4 col-12">
        <!-- Seller Info -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Seller Information</h4>
            </div>
            <div class="card-content">
                <div class="card-body">
                    @if($commission->seller)
                        <div class="d-flex justify-content-start align-items-center mb-1">
                            <div class="avatar mr-1">
                                <span class="avatar-content">{{ substr($commission->seller->name, 0, 2) }}</span>
                            </div>
                            <div class="user-page-info">
                                <h6 class="mb-0">{{ $commission->seller->name }}</h6>
                                <span class="font-small-2">{{ $commission->seller->email }}</span>
                            </div>
                        </div>
                        <a href="{{ route('admin.users.show', $commission->seller_id) }}" class="btn btn-outline-primary btn-block mt-2">View User Profile</a>
                    @else
                        <div class="alert alert-warning mb-0">User Deleted</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Listing Info -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Listing Information</h4>
            </div>
            <div class="card-content">
                <div class="card-body">
                    @if($commission->listing)
                        @if($commission->listing->main_image)
                            <img src="{{ asset('storage/' . $commission->listing->main_image) }}" class="img-fluid rounded mb-2" alt="Car Image">
                        @endif
                        <h6>{{ $commission->listing->title }}</h6>
                        <ul class="list-unstyled">
                            <li><i class="feather icon-calendar mr-50"></i> {{ $commission->listing->year }}</li>
                            <li><i class="feather icon-check-square mr-50"></i> {{ $commission->listing->status }}</li>
                        </ul>
                        <a href="{{ route('listings.show', $commission->listing->slug) }}" target="_blank" class="btn btn-outline-info btn-block mt-2">View Listing</a>
                    @else
                        <div class="alert alert-warning mb-0">Listing Deleted</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Admin Actions -->
        @if($commission->status === 'pending')
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title text-white">Admin Actions</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <form action="{{ route('admin.commissions.mark-paid', $commission->id) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Are you sure you want to mark this as PAID manually?')">
                                <i class="feather icon-check"></i> Mark as Paid
                            </button>
                        </form>

                        <form action="{{ route('admin.commissions.waive', $commission->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-block" onclick="return confirm('Are you sure you want to WAIVE this commission? This cannot be undone.')">
                                <i class="feather icon-slash"></i> Waive Commission
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
