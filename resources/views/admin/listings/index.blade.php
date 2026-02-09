@extends('layouts/contentNavbarLayout')

@section('title', 'Listings - Admin')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
@endsection

@section('page-script')
<script src="{{ asset('assets/js/app-user-list.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('select-all');
    const selectItems = document.querySelectorAll('.select-item');
    const deleteSelectedBtn = document.getElementById('delete-selected');
    const bulkDeleteForm = document.getElementById('bulk-delete-form');
    const bulkDeleteIdsDiv = document.getElementById('bulk-delete-ids');

    function updateDeleteButtonVisibility() {
        const checkedItems = document.querySelectorAll('.select-item:checked');
        if (checkedItems.length > 0) {
            deleteSelectedBtn.style.display = 'block';
        } else {
            deleteSelectedBtn.style.display = 'none';
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            const selectItems = document.querySelectorAll('.select-item');
            selectItems.forEach(item => {
                item.checked = selectAll.checked;
            });
            updateDeleteButtonVisibility();
        });
    }

    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('select-item')) {
            const selectAll = document.getElementById('select-all');
            const selectItems = document.querySelectorAll('.select-item');
            if (!e.target.checked) {
                selectAll.checked = false;
            } else if (document.querySelectorAll('.select-item:checked').length === selectItems.length) {
                selectAll.checked = true;
            }
            updateDeleteButtonVisibility();
        }
    });

    if (deleteSelectedBtn) {
        deleteSelectedBtn.addEventListener('click', function () {
            if (confirm('Are you sure you want to delete the selected listings?')) {
                bulkDeleteIdsDiv.innerHTML = '';
                document.querySelectorAll('.select-item:checked').forEach(item => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = item.value;
                    bulkDeleteIdsDiv.appendChild(input);
                });
                bulkDeleteForm.submit();
            }
        });
    }
});
</script>
@endsection

@section('content')
<div class="row g-6 mb-6">
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Total Listings</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $listings->total() }}</h4>
              <p class="text-success mb-0">(+10%)</p>
            </div>
            <small class="mb-0">All listings</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-primary">
              <i class="icon-base ti tabler-car icon-26px"></i>
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
            <span class="text-heading">Active Listings</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $listings->where('status', 'active')->count() }}</h4>
              <p class="text-success mb-0">(+5%)</p>
            </div>
            <small class="mb-0">Published listings</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-success">
              <i class="icon-base ti tabler-car icon-26px"></i>
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
            <span class="text-heading">Pending Listings</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $listings->where('status', 'pending')->count() }}</h4>
              <p class="text-warning mb-0">(+2%)</p>
            </div>
            <small class="mb-0">Awaiting approval</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-warning">
              <i class="icon-base ti tabler-clock icon-26px"></i>
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
            <span class="text-heading">Sold Listings</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $listings->where('status', 'sold')->count() }}</h4>
              <p class="text-info mb-0">(+8%)</p>
            </div>
            <small class="mb-0">Completed sales</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-info">
              <i class="icon-base ti tabler-check icon-26px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Filter Listings section -->
<div class="card mb-6">
  <div class="card-header border-bottom">
    <h5 class="card-title mb-0">Search Filters</h5>
  </div>
  <div class="card-body pt-6">
    <form action="{{ route('admin.listings.index') }}" method="GET">
      <div class="row g-6">
        <div class="col-md-4">
          <label class="form-label">Search</label>
          <input type="text" name="search" class="form-control" placeholder="Search by ID or Title..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <option value="">All Statuses</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Sold</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Make</label>
          <select name="make_id" class="form-select">
            <option value="">All Makes</option>
            @foreach($makes as $make)
              <option value="{{ $make->id }}" {{ request('make_id') == $make->id ? 'selected' : '' }}>{{ $make->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
          <div class="d-flex gap-2 w-100">
            <button type="submit" class="btn btn-primary flex-grow-1">
              <i class="ti tabler-search me-1"></i> Filter
            </button>
            <a href="{{ route('admin.listings.index') }}" class="btn btn-label-secondary">
              <i class="ti tabler-refresh"></i>
            </a>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Listings List Table -->
<div class="card">
  <div class="card-header border-bottom d-flex justify-content-between align-items-center">
    <h5 class="card-title mb-0">Listings Management</h5>
    <div class="d-flex gap-2">
      <button type="button" id="delete-selected" class="btn btn-danger" style="display: none;">
        <i class="ti tabler-trash me-1"></i> Delete Selected
      </button>
      <a href="{{ route('admin.listings.create') }}" class="btn btn-primary">
        <i class="ti tabler-plus me-1"></i> Add New Listing
      </a>
    </div>
  </div>
  <div class="card-datatable">
    <table class="datatables-users table">
      <thead class="border-top">
        <tr>
          <th>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="select-all">
            </div>
          </th>
          <th>Listing</th>
          <th>Vehicle</th>
          <th>Price</th>
          <th>Status</th>
          <th>User</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($listings as $listing)
        <tr>
          <td>
            <div class="form-check">
              <input class="form-check-input select-item" type="checkbox" value="{{ $listing->id }}">
            </div>
          </td>
          <td>
            <div class="d-flex justify-content-start align-items-center listing-name text-nowrap">
              <div class="avatar-wrapper">
                <div class="avatar me-3" style="width: 50px; height: 50px;">
                  @if($listing->main_image)
                    <img src="{{ asset($listing->main_image) }}" alt="Thumbnail" class="rounded" style="object-fit: cover; width: 100%; height: 100%;">
                  @else
                    <span class="avatar-initial rounded bg-label-secondary"><i class="ti tabler-car"></i></span>
                  @endif
                </div>
              </div>
              <div class="d-flex flex-column">
                <span class="text-heading text-wrap fw-medium" style="max-width: 250px;">{{ $listing->title }}</span>
                <small class="text-muted">ID: #{{ $listing->id }}</small>
              </div>
            </div>
          </td>
          <td>
            <div class="d-flex flex-column">
              <span class="text-heading fw-medium">{{ $listing->make->name ?? 'N/A' }}</span>
              <small>{{ $listing->vehicleModel->name ?? 'N/A' }}</small>
            </div>
          </td>
          <td>
            <span class="text-heading fw-medium">${{ number_format($listing->price) }}</span>
          </td>
          <td>
            <span class="badge bg-label-{{ $listing->status == 'active' ? 'success' : ($listing->status == 'pending' ? 'warning' : ($listing->status == 'sold' ? 'info' : 'danger')) }} text-uppercase">
              {{ $listing->status }}
            </span>
          </td>
          <td>
            <div class="d-flex flex-column">
              <span class="text-heading fw-medium">{{ $listing->user->name ?? 'N/A' }}</span>
              <small class="text-muted">{{ $listing->created_at->format('M d, Y') }}</small>
            </div>
          </td>
          <td>
            <div class="dropdown">
              <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                <i class="icon-base ti tabler-dots-vertical icon-22px"></i>
              </button>
              <div class="dropdown-menu">
                @if($listing->status == 'pending')
                <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('approve-form-{{ $listing->id }}').submit();">
                  <i class="icon-base ti tabler-check me-1"></i> Approve
                </a>
                <form id="approve-form-{{ $listing->id }}" action="{{ route('admin.listings.approve', $listing) }}" method="POST" style="display: none;">@csrf</form>
                
                <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('reject-form-{{ $listing->id }}').submit();">
                  <i class="icon-base ti tabler-x me-1"></i> Reject
                </a>
                <form id="reject-form-{{ $listing->id }}" action="{{ route('admin.listings.reject', $listing) }}" method="POST" style="display: none;">@csrf</form>
                @endif

                <a class="dropdown-item" href="{{ route('admin.listings.edit', $listing) }}">
                  <i class="icon-base ti tabler-pencil me-1"></i> Edit
                </a>
                <a class="dropdown-item" href="#" onclick="event.preventDefault(); if(confirm('Are you sure?')) { document.getElementById('delete-form-{{ $listing->id }}').submit(); }">
                  <i class="icon-base ti tabler-trash me-1"></i> Delete
                </a>
                <form id="delete-form-{{ $listing->id }}" action="{{ route('admin.listings.destroy', $listing) }}" method="POST" style="display: none;">
                  @csrf
                  @method('DELETE')
                </form>
              </div>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="card-footer d-flex justify-content-center">
    {{ $listings->links() }}
  </div>
</div>

<form id="bulk-delete-form" action="{{ route('admin.listings.bulk-delete') }}" method="POST" style="display: none;">
  @csrf
  <div id="bulk-delete-ids"></div>
</form>

@endsection
