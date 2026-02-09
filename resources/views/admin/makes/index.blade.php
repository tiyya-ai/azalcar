@extends('layouts/contentNavbarLayout')

@section('title', 'Manage Makes')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
@endsection

@section('content')
<div class="row g-6 mb-6">
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Total Makes</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $makes->count() }}</h4>
              <p class="text-success mb-0">(+5%)</p>
            </div>
            <small class="mb-0">Car brands</small>
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
            <span class="text-heading">Active Makes</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $makes->count() }}</h4>
              <p class="text-success mb-0">(+2%)</p>
            </div>
            <small class="mb-0">Available brands</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-success">
              <i class="icon-base ti tabler-check icon-26px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Makes List Table -->
<div class="card">
  <div class="card-header border-bottom">
    <h5 class="card-title mb-0">Makes Management</h5>
  </div>
  <div class="card-datatable">
    <table class="datatables-users table">
      <thead class="border-top">
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Slug</th>
          <th>Image</th>
          <th>Created</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($makes as $make)
        <tr>
          <td>{{ $make->id }}</td>
          <td>{{ $make->name }}</td>
          <td>{{ $make->slug }}</td>
          <td>
            @if($make->image)
                <img src="{{ asset('storage/' . $make->image) }}" alt="{{ $make->name }}" width="40" class="rounded-circle">
            @else
                <span class="badge bg-label-secondary">No Image</span>
            @endif
          </td>
          <td>{{ $make->created_at->format('M d, Y') }}</td>
          <td>
            <div class="dropdown">
              <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                <i class="icon-base ti tabler-dots-vertical icon-22px"></i>
              </button>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="{{ route('admin.makes.edit', $make) }}">
                  <i class="icon-base ti tabler-pencil me-1"></i> Edit
                </a>
                <a class="dropdown-item" href="#" onclick="event.preventDefault(); if(confirm('Are you sure?')) { document.getElementById('delete-form-{{ $make->id }}').submit(); }">
                  <i class="icon-base ti tabler-trash me-1"></i> Delete
                </a>
                <form id="delete-form-{{ $make->id }}" action="{{ route('admin.makes.destroy', $make) }}" method="POST" style="display: none;">
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
</div>
@endsection

@section('page-script')
<script src="{{ asset('assets/js/app-user-list.js') }}"></script>
@endsection
