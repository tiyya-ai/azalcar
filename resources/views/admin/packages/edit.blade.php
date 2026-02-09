@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Package - Admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <h4 class="fw-bold py-3 mb-4">
                <span class="text-muted fw-light">Monetization /</span> Edit Package
            </h4>
        </div>
        <div class="col-sm-6 col-xl-9 text-sm-end">
            <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Basic Layout -->
    <div class="row center-form-wrapper justify-content-center">
        <div class="col-xl-8">
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bx bx-package me-2"></i>Edit Package Details</h5>
                    <small class="text-muted float-end">ID: {{ $package->id }}</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.packages.update', $package) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Header / Colors -->
                        <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-lighter rounded">
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $package->name }}</h6>
                                <small class="text-muted">Slug: {{ $package->slug ?? Str::slug($package->name) }}</small>
                            </div>
                            <span class="badge {{ $package->is_featured ? 'bg-primary' : 'bg-label-secondary' }}">
                                {{ $package->is_featured ? 'FEATURED' : 'Standard' }}
                            </span>
                        </div>

                        <div class="row g-3">
                            <!-- Name -->
                            <div class="col-md-12">
                                <label class="form-label" for="name">Package Name</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-tag"></i></span>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ $package->name }}" placeholder="e.g. Premium Plan" required />
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="col-md-6">
                                <label class="form-label" for="price">Price (₽)</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-ruble"></i></span>
                                    <input type="number" class="form-control" id="price" name="price" value="{{ $package->price }}" placeholder="0.00" required />
                                </div>
                                <div class="form-text">Set to 0 for free packages.</div>
                            </div>

                            <!-- Duration -->
                            <div class="col-md-6">
                                <label class="form-label" for="duration_days">Duration (Days)</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-time"></i></span>
                                    <input type="number" class="form-control" id="duration_days" name="duration_days" value="{{ $package->duration_days }}" placeholder="30" required />
                                </div>
                            </div>
                            
                            <div class="col-12"><hr class="my-2"></div>

                            <!-- Image Limit -->
                            <div class="col-md-6">
                                <label class="form-label" for="limit_images">Max Images Allowed</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-image"></i></span>
                                    <input type="number" class="form-control" id="limit_images" name="limit_images" value="{{ $package->limit_images }}" placeholder="10" required />
                                </div>
                            </div>

                            <!-- Features Toggles -->
                            <div class="col-md-6">
                                <label class="form-label d-block mb-2">Premium Features</label>
                                <div class="d-flex flex-column gap-2">
                                    <div class="form-check form-switch custom-switch-primary">
                                        <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ $package->is_featured ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_featured">
                                            Featured Badge 
                                            <small class="text-muted d-block" style="font-size: 0.75rem">Listings get a 'Featured' flag</small>
                                        </label>
                                    </div>
                                    <div class="form-check form-switch custom-switch-success mt-2">
                                        <input class="form-check-input" type="checkbox" name="is_top" id="is_top" value="1" {{ $package->is_top ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_top">
                                            Top Ranking
                                            <small class="text-muted d-block" style="font-size: 0.75rem">Always appears at the top of search</small>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label class="form-label" for="description">Public Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Describe the benefits of this package...">{{ $package->description }}</textarea>
                            </div>
                        </div>

                        <div class="mt-5 pt-3 border-top d-flex justify-content-between">
                            <button type="button" class="btn btn-label-danger" onclick="if(confirm('Delete this package?')) document.getElementById('delete-form').submit();">
                                <i class="bx bx-trash me-1"></i> Delete
                            </button>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bx bx-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>

                    <form id="delete-form" action="{{ route('admin.packages.destroy', $package->id) }}" method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Side Info Card -->
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Package Impact</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Active Subscriptions
                            <span class="badge bg-primary rounded-pill">{{ \App\Models\Listing::where('package_id', $package->id)->where('status', 'active')->count() }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Total Revenue (Est.)
                            <span class="badge bg-success rounded-pill">₽ {{ number_format(\App\Models\Listing::where('package_id', $package->id)->count() * $package->price) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Created At
                            <small class="text-muted">{{ $package->created_at->format('M d, Y') }}</small>
                        </li>
                    </ul>
                </div>
            </div>
            
             <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title text-white">Pricing Strategy</h5>
                    <p class="card-text">Consider creating tiered packages (Basic, Pro, Elite) to maximize revenue.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-lighter { background-color: #f8f9fa; }
    .center-form-wrapper { max-width: 1400px; margin: 0 auto; }
</style>
@endsection
