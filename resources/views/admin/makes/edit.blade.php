@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Car Make')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <h4 class="fw-bold py-3 mb-4">
                <span class="text-muted fw-light">Attributes / Makes /</span> Edit Make
            </h4>
        </div>
        <div class="col-sm-6 col-xl-9 text-sm-end">
            <a href="{{ route('admin.makes.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back to List
            </a>
        </div>
    </div>

    <form action="{{ route('admin.makes.update', $make->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row center-form-wrapper justify-content-center">
            <!-- Main Column -->
            <div class="col-xl-8">
                <div class="card mb-4 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary"><i class="bx bx-car me-2"></i>Edit Make Details</h5>
                        <small class="text-muted float-end">ID: {{ $make->id }}</small>
                    </div>
                    <div class="card-body">
                        
                        <!-- Header / Active Status -->
                        <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-lighter rounded">
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $make->name }}</h6>
                                <small class="text-muted">Total Models: {{ $make->models()->count() }}</small>
                            </div>
                            <div class="form-check form-switch custom-switch-lg">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ $make->is_active ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="is_active">Active Status</label>
                            </div>
                        </div>

                        <div class="row g-3">
                            <!-- Name -->
                            <div class="col-md-6">
                                <label class="form-label" for="make-name">Make Name</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-tag"></i></span>
                                    <input type="text" class="form-control" id="make-name" name="name" value="{{ old('name', $make->name) }}" placeholder="e.g. Toyota" required />
                                </div>
                                @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <!-- Slug (Readonly) -->
                            <div class="col-md-6">
                                <label class="form-label" for="make-slug">Slug (Auto-generated)</label>
                                <input type="text" class="form-control bg-light" id="make-slug" value="{{ $make->slug }}" readonly />
                                <div class="form-text">Used in URLs. Updates automatically with name.</div>
                            </div>

                            <!-- NHTSA ID -->
                            <div class="col-md-6">
                                <label class="form-label" for="nhtsa_id">NHTSA ID (Official)</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-hash"></i></span>
                                    <input type="number" class="form-control" id="nhtsa_id" name="nhtsa_id" value="{{ old('nhtsa_id', $make->nhtsa_id) }}" placeholder="e.g. 442" />
                                </div>
                                <div class="form-text">Official ID from National Highway Traffic Safety Administration.</div>
                            </div>
                        </div>

                        <div class="mt-5 pt-3 border-top d-flex justify-content-between">
                            <button type="button" class="btn btn-label-danger" onclick="if(confirm('Delete this make?')) document.getElementById('delete-form').submit();">
                                <i class="bx bx-trash me-1"></i> Delete
                            </button>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bx bx-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Side Info Card -->
            <div class="col-xl-4">
                <!-- Brand Image Card -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header pb-0">
                        <h5 class="card-title mb-0">Brand Logo</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-center bg-lighter border rounded mb-3 p-2" style="height: 200px; position: relative; overflow: hidden;">
                             @if($make->image)
                                <img src="{{ asset('storage/' . $make->image) }}" alt="Current Image" class="img-fluid rounded" id="imagePreview" style="max-height: 100%; width: auto; object-fit: contain;">
                            @else
                                <div id="placeholder-icon" class="text-center">
                                    <i class="bx bx-image-alt text-muted" style="font-size: 3rem;"></i>
                                    <p class="mt-2 text-muted mb-0 small">No Logo Uploaded</p>
                                </div>
                                <img src="" id="imagePreview" class="d-none img-fluid rounded" style="max-height: 100%; width: auto; object-fit: contain;">
                            @endif
                        </div>
                        
                        <div class="d-grid gap-2">
                             <label for="make-image" class="btn btn-outline-primary" tabindex="0">
                                <span class="d-none d-sm-block">Upload New Logo</span>
                                <i class="bx bx-upload d-block d-sm-none"></i>
                                <input type="file" id="make-image" name="image" class="account-file-input" hidden accept="image/png, image/jpeg, image/gif" onchange="previewImage(this)"/>
                            </label>
                            <p class="text-muted small mb-0 text-center">Allowed JPG, GIF or PNG. Max 2MB.</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Quick Stats</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                Associated Models
                                <span class="badge bg-primary rounded-pill">{{ $make->models()->count() }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                Active Listings
                                <span class="badge bg-success rounded-pill">{{ $make->listings()->where('status', 'active')->count() ?? 0 }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                Created At
                                <small class="text-muted">{{ $make->created_at->format('M d, Y') }}</small>
                            </li>
                        </ul>
                    </div>
                </div>
                
                 <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title text-white">NHTSA Integration</h5>
                        <p class="card-text">If this make is synced with NHTSA, model data can be automatically populated.</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
    <form id="delete-form" action="{{ route('admin.makes.destroy', $make->id) }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                let img = document.getElementById('imagePreview');
                let placeholder = document.getElementById('placeholder-icon');
                
                img.src = e.target.result;
                img.classList.remove('d-none');
                
                if(placeholder) placeholder.classList.add('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<style>
    .bg-lighter { background-color: #f8f9fa; }
    .center-form-wrapper { max-width: 1400px; margin: 0 auto; }
</style>
@endsection
