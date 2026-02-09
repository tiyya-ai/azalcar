@extends('layouts/contentNavbarLayout')

@section('title', 'Manage Packages - Admin')

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-0">
          <span class="text-muted fw-light">Monetization /</span> Packages
        </h4>
        <a href="{{ route('admin.packages.create') }}" class="btn btn-primary"><i class="ti tabler-plus me-1"></i> Add New Package</a>
    </div>
</div>

<div class="row g-4">
    @forelse($packages as $package)
    <div class="col-xl-4 col-lg-6 col-md-6">
        <div class="card h-100 border-{{ $package->is_featured ? 'primary shadow-sm' : 'secondary' }}">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center {{ $package->is_featured ? 'bg-label-primary' : '' }}">
                <h5 class="card-title mb-0 {{ $package->is_featured ? 'text-primary' : '' }}">{{ $package->name }}</h5>
                <div class="dropdown">
                  <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                    <i class="ti tabler-dots-vertical text-muted"></i>
                  </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="{{ route('admin.packages.edit', $package) }}">
                          <i class="ti tabler-edit me-1"></i> Edit
                        </a>
                        <form action="{{ route('admin.packages.destroy', $package) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this package?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger">
                                 <i class="ti tabler-trash me-1"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body pt-4">
                <div class="d-flex justify-content-center mb-4">
                   <div class="display-6 fw-bold">₽ {{ number_format($package->price, 0) }}</div>
                   <sub class="ms-1 align-self-end text-muted">/ {{ $package->duration_days }} days</sub>
                </div>
                
                <ul class="list-unstyled my-4">
                    <li class="mb-3 d-flex align-items-center">
                         <span class="badge bg-label-secondary p-1 rounded me-2"><i class="ti tabler-photo ti-xs"></i></span>
                         <span class="fw-medium">{{ $package->limit_images }}</span> <span class="text-muted ms-1">Max Images</span>
                    </li>
                    <li class="mb-3 d-flex align-items-center">
                        @if($package->is_featured)
                           <span class="badge bg-label-success p-1 rounded me-2"><i class="ti tabler-check ti-xs"></i></span>
                           <span class="text-heading">Featured Listings Included</span>
                        @else
                           <span class="badge bg-label-danger p-1 rounded me-2"><i class="ti tabler-x ti-xs"></i></span>
                           <span class="text-muted text-decoration-line-through">Featured Listings</span>
                        @endif
                    </li>
                     <li class="mb-3 d-flex align-items-center">
                        @if($package->is_top)
                           <span class="badge bg-label-success p-1 rounded me-2"><i class="ti tabler-check ti-xs"></i></span>
                           <span class="text-heading">Top Placement</span>
                        @else
                           <span class="badge bg-label-danger p-1 rounded me-2"><i class="ti tabler-x ti-xs"></i></span>
                           <span class="text-muted text-decoration-line-through">Top Placement</span>
                        @endif
                    </li>
                </ul>

                <a href="{{ route('admin.packages.edit', $package) }}" class="btn btn-label-primary d-grid w-100">Edit Package</a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center p-5">
                <i class="ti tabler-package-off fs-1 text-muted mb-3"></i>
                <h4 class="mb-2">No Packages Found</h4>
                <p class="mb-4">Create your first subscription package to start monetizing your platform.</p>
                <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">
                    <i class="ti tabler-plus me-1"></i> Create Package
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>
@endsection
