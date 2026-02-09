@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Vehicle Model')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <h4 class="fw-bold py-3 mb-4">
                <span class="text-muted fw-light">Attributes / Vehicle Models /</span> Edit Model
            </h4>
        </div>
        <div class="col-sm-6 col-xl-9 text-sm-end">
            <a href="{{ route('admin.vehicle_models.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Basic Layout -->
    <div class="row center-form-wrapper justify-content-center">
        <div class="col-xl-8">
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bx bx-car me-2"></i>Edit Model Details</h5>
                    <small class="text-muted float-end">ID: {{ $vehicleModel->id }}</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.vehicle_models.update', $vehicleModel->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Header / Active Status -->
                        <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-lighter rounded">
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $vehicleModel->name }}</h6>
                                <small class="text-muted">Parent Make: {{ $vehicleModel->make->name ?? 'N/A' }}</small>
                            </div>
                            <div class="form-check form-switch custom-switch-lg">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ $vehicleModel->is_active ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="is_active">Active Status</label>
                            </div>
                        </div>

                        <div class="row g-3">
                            <!-- Parent Make Selection -->
                            <div class="col-12">
                                <label class="form-label" for="model-make">Parent Manufacturer</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-building"></i></span>
                                    <select id="model-make" name="make_id" class="form-select" required>
                                        <option value="">Select Make</option>
                                        @foreach($makes as $make)
                                            <option value="{{ $make->id }}" {{ $vehicleModel->make_id == $make->id ? 'selected' : '' }}>{{ $make->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Name -->
                            <div class="col-md-6">
                                <label class="form-label" for="model-name">Model Name</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-tag"></i></span>
                                    <input type="text" class="form-control" id="model-name" name="name" value="{{ $vehicleModel->name }}" placeholder="e.g. Camry" required />
                                </div>
                                @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <!-- Slug (Readonly) -->
                            <div class="col-md-6">
                                <label class="form-label" for="model-slug">Slug (Auto-generated)</label>
                                <input type="text" class="form-control bg-light" id="model-slug" value="{{ $vehicleModel->slug }}" readonly />
                            </div>

                            <!-- NHTSA ID -->
                            <div class="col-12">
                                <label class="form-label" for="nhtsa_id">NHTSA ID (Official)</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-hash"></i></span>
                                    <input type="number" class="form-control" id="nhtsa_id" name="nhtsa_id" value="{{ old('nhtsa_id', $vehicleModel->nhtsa_id) }}" placeholder="e.g. 1861" />
                                </div>
                                <div class="form-text">Model ID from National Highway Traffic Safety Administration database.</div>
                            </div>
                        </div>

                        <div class="mt-5 pt-3 border-top d-flex justify-content-between">
                            <button type="button" class="btn btn-label-danger" onclick="if(confirm('Delete this model?')) document.getElementById('delete-form').submit();">
                                <i class="bx bx-trash me-1"></i> Delete
                            </button>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bx bx-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>

                    <form id="delete-form" action="{{ route('admin.vehicle_models.destroy', $vehicleModel->id) }}" method="POST" class="d-none">
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
                    <h5 class="mb-0">Quick Stats</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Active Listings
                            <span class="badge bg-success rounded-pill">{{ \App\Models\Listing::where('vehicle_model_id', $vehicleModel->id)->where('status', 'active')->count() }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Created At
                            <small class="text-muted">{{ $vehicleModel->created_at->format('M d, Y') }}</small>
                        </li>
                    </ul>
                </div>
            </div>
            
             <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title text-white">Data Hierarchy</h5>
                    <p class="card-text">Check the manufacturer before saving to ensure correct data categorization.</p>
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
