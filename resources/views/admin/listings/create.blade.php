@extends('layouts.layoutMaster')

@section('title', 'Create New Listing')

@section('content')
<form action="{{ route('admin.listings.store') }}" method="POST" enctype="multipart/form-data" id="createListingForm">
    @csrf
    @if(isset($target_user_id))
    <input type="hidden" name="user_id" value="{{ $target_user_id }}">
    @endif

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Basic Information</h5>
                    <span class="badge bg-label-info">New Listing</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Listing Title</label>
                            <input type="text" name="title" class="form-control form-control-lg" value="{{ old('title') }}" required placeholder="e.g., 2020 Toyota Camry Highline">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Make</label>
                            <select name="make_id" class="form-select select2" id="make_id" required>
                                <option value="">Select Make</option>
                                @foreach($makes as $make)
                                <option value="{{ $make->id }}">{{ $make->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Model</label>
                            <select name="vehicle_model_id" class="form-select select2" id="vehicle_model_id" required>
                                <option value="">Select model</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Vehicle Type</label>
                            <select name="vehicle_type_id" class="form-select select2" required>
                                <option value="">Select type</option>
                                @foreach($types as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Price</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="price" class="form-control" value="{{ old('price') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Year</label>
                            <input type="number" name="year" class="form-control" value="{{ old('year', date('Y')) }}" required min="1900" max="{{ date('Y')+1 }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Mileage (km/mi)</label>
                            <input type="number" name="mileage" class="form-control" value="{{ old('mileage', 0) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Condition</label>
                            <select name="condition" class="form-select">
                                <option value="used">Used</option>
                                <option value="new">New</option>
                            </select>
                        </div>

                         <div class="col-md-6">
                            <label class="form-label">Transmission</label>
                            <select name="transmission" class="form-select">
                                <option value="automatic">Automatic</option>
                                <option value="manual">Manual</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Fuel Type</label>
                            <input type="text" name="fuel_type" class="form-control" value="{{ old('fuel_type', 'Petrol') }}" required>
                        </div>

                         <div class="col-md-12">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control" value="{{ old('location') }}" required placeholder="e.g., Dubai, UAE">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" id="description_editor" class="form-control" rows="6" placeholder="Describe the vehicle condition, features, etc.">{{ old('description') }}</textarea>
                        </div>

                        <!-- Features Section -->
                        <div class="col-12 mt-4">
                            <label class="form-label fw-bold">Additional Options / Features</label>
                            <div class="features-management-card" style="border: 1px solid #dbdade; border-radius: 8px; padding: 16px; background: #fdfdfd;">
                                <div class="input-group mb-3">
                                    <input type="text" id="feature_input" class="form-control" placeholder="Add a feature (e.g., Sunroof, Leather Seats)...">
                                    <button class="btn btn-primary" type="button" id="add_feature_btn">
                                        <i class="ti tabler-plus me-1"></i> Add
                                    </button>
                                </div>
                                <div id="features_list" class="d-flex flex-wrap gap-2">
                                    {{-- Features will be added here dynamically --}}
                                </div>
                                <div class="form-text mt-2">Add specific features or options to make your listing stand out.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Media Management Section -->
        <div class="card mb-4">
            <div class="card-header border-bottom">
                <h5 class="mb-0">Extended Media</h5>
            </div>
            <div class="card-body pt-4">
                <div class="row g-4">
                    <!-- Video Section -->
                    <div class="col-12">
                        <label class="form-label fw-bold">Video Link (YouTube/Vimeo)</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="ti tabler-brand-youtube text-danger"></i></span>
                            <input type="url" name="video_url" class="form-control" value="{{ old('video_url') }}" placeholder="https://www.youtube.com/watch?v=...">
                        </div>
                    </div>

                    <!-- 360 Section -->
                    <div class="col-12">
                        <label class="form-label fw-bold">360° View / Virtual Tour URL</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="ti tabler-3d-rotate text-info"></i></span>
                            <input type="url" name="v360_url" class="form-control" value="{{ old('v360_url') }}" placeholder="https://kuula.co/post/...">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="col-12 col-lg-4">
        <!-- Image & Gallery Sidebar Section -->
        <div class="card mb-4">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Listing Media</h5>
                <i class="ti tabler-photo-video text-muted"></i>
            </div>
            <div class="card-body pt-4">
                <!-- Main Image -->
                <div class="mb-4 text-center">
                    <label class="form-label fw-bold mb-2">Cover Photo</label>
                    <div class="media-upload-wrapper-sm" id="main_image_upload_container">
                        <input type="file" name="main_image" id="main_image_input" accept="image/*" class="d-none">
                        <div class="upload-dropzone-sidebar" onclick="document.getElementById('main_image_input').click()">
                            <div class="dz-message-sm">
                                <i class="ti tabler-upload icon-24px text-muted mb-1"></i>
                                <p class="mb-0 small fw-bold">Main Image</p>
                                <span class="text-muted" style="font-size: 0.7rem;">Click to upload</span>
                            </div>
                        </div>
                    </div>
                    <div id="main_preview_area" class="mt-2 d-none">
                         <img src="" id="main_preview_img" class="img-fluid rounded border" style="max-height: 200px;">
                    </div>
                </div>

                <!-- Gallery -->
                <div>
                    <label class="form-label fw-bold mb-2">Gallery Photos</label>
                    <div class="gallery-management-sidebar">
                        <input type="file" name="gallery[]" id="gallery_input" accept="image/*" multiple class="d-none">
                        <div class="upload-dropzone-xs" onclick="document.getElementById('gallery_input').click()">
                            <i class="ti tabler-camera-plus me-1"></i> Add Photos
                        </div>
                        <div id="new_gallery_previews" class="gallery-grid-sidebar mt-2"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header border-bottom">
                <h5 class="mb-0">Publish</h5>
            </div>
            <div class="card-body pt-4">
                <div class="d-grid gap-2">
                    <button type="submit" form="createListingForm" class="btn btn-primary btn-lg shadow">
                        <i class="ti tabler-device-floppy me-2"></i> Create Listing
                    </button>
                    <a href="{{ route('admin.listings.index') }}" class="btn btn-label-secondary">
                        Discard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
@endsection

@section('page-style')
<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
<style>
    .upload-dropzone-sidebar {
        border: 2px dashed #dbdade;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 160px;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        background-color: #f8f7fa;
        text-align: center;
    }
    .upload-dropzone-sidebar:hover {
        border-color: #7367f0;
        background-color: #f2f0ff;
    }
    .upload-dropzone-xs {
        border: 2px dashed #dbdade;
        border-radius: 6px;
        padding: 8px;
        text-align: center;
        cursor: pointer;
        color: #6f6b7d;
        font-weight: 500;
        font-size: 0.85rem;
    }
    .gallery-grid-sidebar {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
    }
    .gallery-tile-sidebar {
        aspect-ratio: 1/1;
        border-radius: 6px;
        overflow: hidden;
        position: relative;
        border: 1px solid #dbdade;
    }
    .gallery-tile-sidebar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>
@endsection

@section('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Main Image Preview
        const mainInput = document.getElementById('main_image_input');
        const mainPreviewArea = document.getElementById('main_preview_area');
        const mainPreviewImg = document.getElementById('main_preview_img');

        mainInput.addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(re) {
                    mainPreviewImg.src = re.target.result;
                    mainPreviewArea.classList.remove('d-none');
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });

        // Gallery Previews
        const galleryInput = document.getElementById('gallery_input');
        const galleryPreview = document.getElementById('new_gallery_previews');

        galleryInput.addEventListener('change', function(e) {
            Array.from(e.target.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(re) {
                    const div = document.createElement('div');
                    div.className = 'gallery-tile-sidebar';
                    div.innerHTML = `<img src="${re.target.result}">`;
                    galleryPreview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });

        // Models Loading
        const makeSelect = document.getElementById('make_id');
        const modelSelect = document.getElementById('vehicle_model_id');

        makeSelect.addEventListener('change', function() {
            const makeId = this.value;
            modelSelect.innerHTML = '<option value="">Loading...</option>';
            
            fetch(`/api/models/${makeId}`)
                .then(r => r.json())
                .then(data => {
                    modelSelect.innerHTML = '<option value="">Select model</option>';
                    data.forEach(m => {
                        modelSelect.innerHTML += `<option value="${m.id}">${m.name}</option>`;
                    });
                });
        });

        // Features Logic
        const featureInput = document.getElementById('feature_input');
        const addFeatureBtn = document.getElementById('add_feature_btn');
        const featuresList = document.getElementById('features_list');

        function addFeature() {
            const val = featureInput.value.trim();
            if (val) {
                const tag = document.createElement('div');
                tag.className = 'feature-tag badge bg-label-primary d-flex align-items-center gap-2 p-2';
                tag.innerHTML = `
                    <span>${val}</span>
                    <input type="hidden" name="features[]" value="${val}">
                    <i class="ti tabler-x cursor-pointer feature-remove" onclick="this.parentElement.remove()"></i>
                `;
                featuresList.appendChild(tag);
                featureInput.value = '';
            }
        }

        if (addFeatureBtn) {
            addFeatureBtn.addEventListener('click', addFeature);
            featureInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    addFeature();
                }
            });
        }

        // CKEditor Initialization
        ClassicEditor
            .create(document.querySelector('#description_editor'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo'],
            })
            .catch(error => {
                console.error(error);
            });
    });
</script>
@endsection
