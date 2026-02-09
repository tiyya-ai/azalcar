@extends('layouts/contentNavbarLayout')

@section('title', 'User Details - Admin')

@section('content')
<div class="row g-4">
    <!-- User Sidebar -->
    <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
        <!-- User Card -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="user-avatar-section">
                    <div class="d-flex align-items-center flex-column">
                        <div class="avatar avatar-xl mb-3">
                            @if($user->profile_photo_path)
                                <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="rounded-circle" />
                            @else
                                <span class="avatar-initial rounded-circle bg-label-secondary fs-2">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                            @endif
                        </div>
                        <div class="user-info text-center">
                            <h4 class="mb-2">{{ $user->name }}</h4>
                            <span class="badge bg-label-{{ $user->role === 'admin' ? 'primary' : ($user->role === 'vendor' ? 'info' : 'secondary') }} mt-1">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between flex-wrap my-2 py-3">
                    <div class="d-flex align-items-center me-4 mt-3 gap-3">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-primary rounded">
                                <i class='ti tabler-checkbox ti-lg'></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $user->listings->count() }}</h5>
                            <span>Listings</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-3 gap-3">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-success rounded">
                                <i class='ti tabler-wallet ti-lg'></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-0">{!! \App\Helpers\Helpers::formatPrice($user->balance) !!}</h5>
                            <span>Balance</span>
                        </div>
                    </div>
                </div>
                
                <h5 class="pb-2 border-bottom mb-4">Details</h5>
                <div class="info-container">
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <span class="fw-medium text-heading me-2">Username:</span>
                            <span>{{ $user->name }}</span>
                        </li>
                        <li class="mb-3">
                            <span class="fw-medium text-heading me-2">Email:</span>
                            <span>{{ $user->email }}</span>
                        </li>
                        <li class="mb-3">
                            <span class="fw-medium text-heading me-2">Status:</span>
                            @if($user->status == 'active')
                                <span class="badge bg-label-success">Active</span>
                            @else
                                <span class="badge bg-label-danger">Banned</span>
                            @endif
                        </li>
                        <li class="mb-3">
                            <span class="fw-medium text-heading me-2">Role:</span>
                            <span>{{ ucfirst($user->role) }}</span>
                        </li>
                        <li class="mb-3">
                            <span class="fw-medium text-heading me-2">Contact:</span>
                            <span>{{ $user->phone ?? 'N/A' }}</span>
                        </li>
                        <li class="mb-3">
                            <span class="fw-medium text-heading me-2">Joined:</span>
                            <span>{{ $user->created_at->format('d M Y') }}</span>
                        </li>
                        @if($user->last_login_at)
                        <li class="mb-3">
                            <span class="fw-medium text-heading me-2">Last Login:</span>
                            <span>{{ $user->last_login_at->diffForHumans() }}</span>
                        </li>
                        @endif
                    </ul>
                    <div class="d-flex justify-content-center pt-3 gap-2 flex-wrap">
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary flex-grow-1">
                            <i class="ti tabler-edit me-1"></i> Edit
                        </a>
                        <button type="button" class="btn btn-label-info flex-grow-1" data-bs-toggle="modal" data-bs-target="#directMessageModal">
                            <i class="ti tabler-mail me-1"></i> Message
                        </button>
                        @if($user->status == 'active')
                             <a href="javascript:;" class="btn btn-label-danger flex-grow-1 suspend-user" onclick="document.getElementById('ban-form').submit()">
                                <i class="ti tabler-ban me-1"></i> Suspend
                             </a>
                        @else
                             <a href="javascript:;" class="btn btn-label-success flex-grow-1 suspend-user" onclick="document.getElementById('ban-form').submit()">
                                <i class="ti tabler-check me-1"></i> Activate
                             </a>
                        @endif
                    </div>
                    <form id="ban-form" action="{{ route('admin.users.ban', $user->id) }}" method="POST" style="display: none;">
                          @csrf
                          <input type="hidden" name="ban_reason" value="Admin Toggle">
                    </form>
                </div>
            </div>
        </div>
        <!-- /User Card -->
    </div>
    <!--/ User Sidebar -->

    <!-- User Content -->
    <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
        
        <!-- Stats Horizontal -->
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-4">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                      <span>Total Sales</span>
                      <div class="d-flex align-items-center my-1">
                        <h4 class="mb-0 me-2">{{ $user->listings->where('status', 'sold')->count() }}</h4>
                      </div>
                      <small class="text-success mb-0">+0%</small>
                    </div>
                    <span class="avatar p-2 rounded bg-label-info">
                        <i class="ti tabler-chart-pie-2 ti-sm"></i>
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-xl-4">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                      <span>Active Ads</span>
                      <div class="d-flex align-items-center my-1">
                        <h4 class="mb-0 me-2">{{ $user->listings->where('status', 'active')->count() }}</h4>
                      </div>
                      <small class="text-success mb-0">+0%</small>
                    </div>
                    <span class="avatar p-2 rounded bg-label-success">
                        <i class="ti tabler-car ti-sm"></i>
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-xl-4">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                      <span>Pending Ads</span>
                      <div class="d-flex align-items-center my-1">
                        <h4 class="mb-0 me-2">{{ $user->listings->where('status', 'pending')->count() }}</h4>
                      </div>
                      <small class="text-warning mb-0">Action needed</small>
                    </div>
                    <span class="avatar p-2 rounded bg-label-warning">
                        <i class="ti tabler-alert-circle ti-sm"></i>
                    </span>
                  </div>
                </div>
              </div>
            </div>
        </div>
        <!-- /Stats Horizontal -->

        <!-- Tabs -->
        <div class="nav-align-top mb-4">
            <ul class="nav nav-pills mb-3" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-listings" aria-controls="navs-pills-listings" aria-selected="true">
                        <i class="ti tabler-car me-1"></i> Listings
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-transactions" aria-controls="navs-pills-transactions" aria-selected="false">
                        <i class="ti tabler-currency-dollar me-1"></i> Transactions
                    </button>
                </li>
            </ul>
            <div class="tab-content shadow-none p-0 bg-transparent">
                <!-- Listings Tab -->
                <div class="tab-pane fade show active" id="navs-pills-listings" role="tabpanel">
                    <div class="card">
                        <div class="table-responsive">
                            <table class="table border-top">
                                <thead>
                                    <tr>
                                        <th>Listing</th>
                                        <th>Status</th>
                                        <th>Price</th>
                                        <th>Views</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($listings as $listing)
                                    <tr>
                                        <td>
                                            <div class="d-flex justify-content-start align-items-center">
                                                <div class="avatar me-3">
                                                    @if($listing->main_image)
                                                        @php
                                                            $imageUrl = $listing->main_image;
                                                            if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                                                                $imageUrl = asset('storage/' . $imageUrl);
                                                            }
                                                        @endphp
                                                        <img src="{{ $imageUrl }}" alt="{{ $listing->title }}" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                                    @else
                                                        <span class="avatar-initial rounded bg-label-secondary">
                                                            <i class="ti tabler-photo"></i>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <a href="{{ route('listings.show', $listing->slug) }}" target="_blank" class="text-body text-truncate">
                                                        <span class="fw-medium">{{ $listing->title }}</span>
                                                    </a>
                                                    <small class="text-muted">{{ $listing->make->name ?? '' }} {{ $listing->vehicleModel->name ?? '' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $statusColor = match($listing->status) {
                                                    'active' => 'success',
                                                    'pending' => 'warning',
                                                    'sold' => 'info',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge bg-label-{{ $statusColor }}">{{ ucfirst($listing->status) }}</span>
                                        </td>
                                        <td>{!! \App\Helpers\Helpers::formatPrice($listing->price) !!}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti tabler-eye text-muted me-1"></i>
                                                {{ $listing->views_count }}
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.listings.edit', $listing->id) }}" class="btn btn-icon btn-text-secondary rounded-pill waves-effect">
                                                <i class="ti tabler-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <i class="ti tabler-car-off fs-1 text-muted mb-3"></i>
                                            <p class="mb-0">No listings found.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Transactions Tab -->
                <div class="tab-pane fade" id="navs-pills-transactions" role="tabpanel">
                     <div class="card">
                        <div class="table-responsive">
                            <table class="table border-top">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $transaction)
                                    <tr>
                                        <td><a href="#" class="text-body">#{{ $transaction->id }}</a></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="badge badge-center rounded-pill bg-label-{{ $transaction->type == 'credit' ? 'success' : 'danger' }} me-3 w-px-30 h-px-30">
                                                    <i class="ti tabler-{{ $transaction->type == 'credit' ? 'arrow-up' : 'arrow-down' }} ti-xs"></i>
                                                </span>
                                                <span>{{ ucfirst($transaction->type) }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $transaction->created_at->format('d M Y, H:i') }}</td>
                                        <td class="fw-medium {{ $transaction->type == 'credit' ? 'text-success' : 'text-danger' }}">
                                            {{ $transaction->type == 'credit' ? '+' : '-' }}{!! \App\Helpers\Helpers::formatPrice(abs($transaction->amount)) !!}
                                        </td>
                                        <td><span class="badge bg-label-secondary">Completed</span></td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <i class="ti tabler-receipt-off fs-1 text-muted mb-3"></i>
                                            <p class="mb-0">No transactions found.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                     </div>
                </div>
            </div>
        </div>
        <!-- /Tabs -->
    </div>
    <!--/ User Content -->
</div>

{{-- Direct Message Modal --}}
<div class="modal fade" id="directMessageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel1">Send Direct Message to {{ $user->name }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.messages.store') }}" method="POST">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">
        <div class="modal-body">
          <div class="row">
            <div class="col mb-3">
              <label for="message" class="form-label">Message</label>
              <textarea name="message" id="message" class="form-control" rows="5" placeholder="Type your message here..." required></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Send Message</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
