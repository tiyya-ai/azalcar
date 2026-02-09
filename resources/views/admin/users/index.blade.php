@extends('layouts/contentNavbarLayout')

@section('title', 'Users Management - Admin')

@section('content')
<div class="row g-6 mb-6">
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Total Users</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ \App\Models\User::count() }}</h4>
            </div>
            <small class="mb-0">All registered users</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-primary">
              <i class="ti tabler-users icon-26px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Sellers</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ \App\Models\User::where('role', 'vendor')->orWhere('seller_status', 'approved')->count() }}</h4>
            </div>
            <small class="mb-0">Active Sellers</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-success">
              <i class="ti tabler-building-store icon-26px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Pending Approvals</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $pendingSellersCount }}</h4>
            </div>
            <small class="mb-0">Seller requests</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-warning">
              <i class="ti tabler-user-exclamation icon-26px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Users List Table -->
<div class="card">
  <div class="card-header border-bottom">
    <h5 class="card-title mb-0">Search Filter</h5>
    <form method="GET" action="{{ route('admin.users.index') }}">
      <div class="d-flex justify-content-start align-items-center row pt-4 gap-4 gap-md-0">
        <div class="col-md-4 user_role">
            <select name="role" class="form-select text-capitalize" onchange="this.form.submit()">
              <option value=""> Select Role </option>
              <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
              <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
              <option value="seller" {{ request('role') == 'seller' ? 'selected' : '' }}>Seller (Approved)</option>
              <option value="seller_pending" {{ request('role') == 'seller_pending' ? 'selected' : '' }}>Seller (Pending)</option>
            </select>
        </div>
        <div class="col-md-4 user_search">
            <div class="input-group input-group-merge">
              <span class="input-group-text" id="basic-addon-search31"><i class="ti tabler-search"></i></span>
              <input type="text" name="search" class="form-control" placeholder="Search name or email..." value="{{ request('search') }}" aria-label="Search..." aria-describedby="basic-addon-search31">
            </div>
        </div>
        <div class="col-md-4 px-md-2">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-label-secondary ms-1">Reset</a>
        </div>
      </div>
    </form>
  </div>
  <div class="card-datatable table-responsive">
    <table class="datatables-users table">
      <thead class="border-top">
        <tr>
          <th>User</th>
          <th>Role</th>
          <th>Status</th>
          <th>Balance</th>
          <th>Joined</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
          @foreach($users as $user)
          <tr>
              <td>
                  <div class="d-flex justify-content-start align-items-center">
                      <div class="avatar-wrapper">
                          <div class="avatar me-2">
                              @if($user->profile_photo_path)
                                  <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Avatar" class="rounded">
                              @else
                                  <span class="avatar-initial rounded bg-label-secondary">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                              @endif
                          </div>
                      </div>
                      <div class="d-flex flex-column">
                          <a href="{{ route('admin.users.show', $user->id) }}" class="text-body text-truncate"><span class="fw-medium">{{ $user->name }}</span></a>
                          <small class="text-muted">{{ $user->email }}</small>
                      </div>
                  </div>
              </td>
              <td>
                  @if($user->role == 'admin')
                      <span class="badge bg-label-primary me-1">Admin</span>
                  @elseif($user->role == 'vendor')
                      <span class="badge bg-label-success me-1">Seller</span>
                  @else
                       <span class="badge bg-label-info me-1">User</span>
                       @if($user->seller_status == 'pending')
                          <span class="badge bg-label-warning">Pending</span>
                       @endif
                  @endif
              </td>
              <td>
                  @if($user->status == 'active')
                      <span class="badge bg-label-success">Active</span>
                  @else
                      <span class="badge bg-label-danger">Banned</span>
                  @endif
              </td>
              <td>{!! \App\Helpers\Helpers::formatPrice($user->balance) !!}</td>
              <td>{{ $user->created_at->format('M d, Y') }}</td>
              <td>
                  <div class="d-flex align-items-center">
                      <a href="{{ route('admin.users.edit', $user->id) }}" class="text-body"><i class="ti tabler-edit ti-sm me-2"></i></a>
                      
                      @if($user->seller_status == 'pending')
                          <form action="{{ route('admin.users.approve-seller', $user->id) }}" method="POST" class="d-inline">
                              @csrf
                              <button type="submit" class="btn btn-sm btn-icon btn-text-success rounded-pill waves-effect" title="Approve Seller">
                                  <i class="ti tabler-check"></i>
                              </button>
                          </form>
                          <form action="{{ route('admin.users.reject-seller', $user->id) }}" method="POST" class="d-inline">
                              @csrf
                              <button type="submit" class="btn btn-sm btn-icon btn-text-danger rounded-pill waves-effect" title="Reject Seller">
                                  <i class="ti tabler-x"></i>
                              </button>
                          </form>
                      @endif

                      <a href="javascript:;" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti tabler-dots-vertical ti-sm mx-1"></i></a>
                      <div class="dropdown-menu dropdown-menu-end m-0">
                          <a href="{{ route('admin.users.show', $user->id) }}" class="dropdown-item">View</a>
                          @if($user->status == 'active')
                             <a href="#" class="dropdown-item text-danger" onclick="event.preventDefault(); document.getElementById('ban-form-{{ $user->id }}').submit();">Ban User</a>
                          @endif
                          <a href="javascript:;" class="dropdown-item">Transactions</a>
                      </div>
                      
                      <form id="ban-form-{{ $user->id }}" action="{{ route('admin.users.ban', $user->id) }}" method="POST" style="display: none;">
                          @csrf
                          <input type="hidden" name="ban_reason" value="Admin Action">
                      </form>
                  </div>
              </td>
          </tr>
          @endforeach
      </tbody>
    </table>
    <div class="p-4">
        {{ $users->links() }}
    </div>
  </div>
</div>
@endsection
