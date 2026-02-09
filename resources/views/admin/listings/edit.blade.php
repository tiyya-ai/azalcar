@extends('layouts.layoutMaster')

@section('title', 'Edit Listing')

@section('content')
<form action="{{ route('admin.listings.update', $listing->id) }}" method="POST" enctype="multipart/form-data" id="editListingForm">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Basic Information</h5>
                    <span class="badge bg-label-primary">ID: {{ $listing->id }}</span>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Listing Title</label>
                            <input type="text" name="title" class="form-control form-control-lg" value="{{ $listing->title }}" required placeholder="e.g., 2020 Toyota Camry Highline">
                            @error('title')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Make</label>
                            <select name="make_id" class="form-select select2" id="make_id">
                                @foreach($makes as $make)
                                <option value="{{ $make->id }}" {{ $listing->make_id == $make->id ? 'selected' : '' }}>{{ $make->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Model</label>
                            <select name="vehicle_model_id" class="form-select select2" id="vehicle_model_id">
                                @foreach($models as $model)
                                <option value="{{ $model->id }}" {{ $listing->vehicle_model_id == $model->id ? 'selected' : '' }}>{{ $model->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Vehicle Type</label>
                            <select name="vehicle_type_id" class="form-select select2">
                                @foreach($types as $type)
                                <option value="{{ $type->id }}" {{ $listing->vehicle_type_id == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Price ($)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="price" class="form-control" value="{{ $listing->price }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Year</label>
                            <input type="number" name="year" class="form-control" value="{{ $listing->year }}" required min="1900" max="{{ date('Y')+1 }}">
                        </div>

                        <!-- Technical Specs -->
                        <div class="col-md-4">
                            <label class="form-label">Mileage (km)</label>
                            <input type="number" name="mileage" class="form-control" value="{{ $listing->mileage }}">
                        </div>
                        <div class="col-md-4">
                             <label class="form-label">Fuel Type</label>
                             <select name="fuel_type" class="form-select select2">
                                <option value="Petrol" {{ $listing->fuel_type == 'Petrol' ? 'selected' : '' }}>Petrol</option>
                                <option value="Diesel" {{ $listing->fuel_type == 'Diesel' ? 'selected' : '' }}>Diesel</option>
                                <option value="Electric" {{ $listing->fuel_type == 'Electric' ? 'selected' : '' }}>Electric</option>
                                <option value="Hybrid" {{ $listing->fuel_type == 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Transmission</label>
                            <select name="transmission" class="form-select select2">
                                <option value="Automatic" {{ $listing->transmission == 'Automatic' ? 'selected' : '' }}>Automatic</option>
                                <option value="Manual" {{ $listing->transmission == 'Manual' ? 'selected' : '' }}>Manual</option>
                                <option value="CVT" {{ $listing->transmission == 'CVT' ? 'selected' : '' }}>CVT</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Condition</label>
                            <select name="condition" class="form-select select2" required>
                                <option value="used" {{ $listing->condition == 'used' ? 'selected' : '' }}>Used</option>
                                <option value="new" {{ $listing->condition == 'new' ? 'selected' : '' }}>New</option>
                            </select>
                            @error('condition')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Drivetrain</label>
                            <select name="drivetrain" class="form-select select2">
                                <option value="">Select Drivetrain</option>
                                <option value="FWD" {{ $listing->drivetrain == 'FWD' ? 'selected' : '' }}>FWD</option>
                                <option value="RWD" {{ $listing->drivetrain == 'RWD' ? 'selected' : '' }}>RWD</option>
                                <option value="AWD" {{ $listing->drivetrain == 'AWD' ? 'selected' : '' }}>AWD</option>
                                <option value="4WD" {{ $listing->drivetrain == '4WD' ? 'selected' : '' }}>4WD</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Color</label>
                            <input type="text" name="color" class="form-control" value="{{ $listing->color }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Engine Size</label>
                            <input type="text" name="engine_size" class="form-control" value="{{ $listing->engine_size }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control" value="{{ $listing->location }}" required placeholder="e.g., Dubai, UAE">
                            @error('location')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" id="description_editor" class="form-control" rows="6" placeholder="Describe the vehicle condition, features, etc.">{{ $listing->description }}</textarea>
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
                                    @php
                                        $features = is_array($listing->features) ? $listing->features : json_decode($listing->features ?? '[]', true);
                                    @endphp
                                    @if($features)
                                        @foreach($features as $feature)
                                            <div class="feature-tag badge bg-label-primary d-flex align-items-center gap-2 p-2">
                                                <span>{{ $feature }}</span>
                                                <input type="hidden" name="features[]" value="{{ $feature }}">
                                                <i class="ti tabler-x cursor-pointer feature-remove" onclick="this.parentElement.remove()"></i>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="form-text mt-2">Add specific features or options to make your listing stand out.</div>
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
                    <div class="col-12" id="video_url_container">
                        <label class="form-label fw-bold">Video Link (YouTube/Vimeo)</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="ti tabler-brand-youtube text-danger"></i></span>
                            <input type="url" name="video_url" class="form-control" value="{{ $listing->video_url }}" placeholder="https://www.youtube.com/watch?v=...">
                        </div>
                        <div class="form-text">Paste a video link to showcase the car in action.</div>
                    </div>

                    <!-- 360 Section (URL Only for now) -->
                    <div class="col-12">
                        <label class="form-label fw-bold">360° View / Virtual Tour URL</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="ti tabler-3d-rotate text-info"></i></span>
                            <input type="url" name="v360_url" class="form-control" value="{{ $listing->v360_url }}" placeholder="https://kuula.co/post/...">
                        </div>
                        <div class="form-text">Support for Matterport, Kuula, or custom 360° viewers.</div>
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
                <div class="mb-4">
                    <label class="form-label fw-bold mb-2">Cover Photo</label>
                    @error('main_image')
                        <div class="text-danger small mb-2">{{ $message }}</div>
                    @enderror
                    <div class="media-upload-wrapper-sm" id="main_image_upload_container">
                        <input type="file" name="main_image" id="main_image_input" accept="image/*" class="d-none">
                        @if($listing->main_image)
                            <div class="image-preview-card-sm active">
                                <img src="{{ asset($listing->main_image) }}" alt="Main Image" id="main_image_preview">
                                <div class="image-actions-sm">
                                    <button type="button" class="btn btn-icon btn-danger btn-xs" onclick="removeMainImage()">
                                        <i class="ti tabler-trash"></i>
                                    </button>
                                    <button type="button" class="btn btn-icon btn-primary btn-xs" onclick="document.getElementById('main_image_input').click()">
                                        <i class="ti tabler-pencil"></i>
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="upload-dropzone-sidebar" onclick="document.getElementById('main_image_input').click()">
                                <div class="dz-message-sm">
                                    <i class="ti tabler-upload icon-24px text-muted mb-1"></i>
                                    <p class="mb-0 small fw-bold">Main Image</p>
                                    <span class="text-muted" style="font-size: 0.7rem;">Click to upload</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Gallery -->
                <div>
                    <label class="form-label fw-bold mb-2">Gallery Photos</label>
                    <div class="gallery-management-sidebar">
                        <div class="gallery-grid-sidebar mb-3" id="gallery_preview_container">
                            @if($listing->images && count($listing->images) > 0)
                                @foreach($listing->images as $index => $image)
                                    <div class="gallery-tile-sidebar" data-path="{{ $image }}">
                                        <img src="{{ asset($image) }}" alt="Gallery Image">
                                        <button type="button" class="tile-remove-xs" onclick="removeGalleryImage(this, '{{ $image }}')">
                                            <i class="ti tabler-x"></i>
                                        </button>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <input type="file" name="gallery[]" id="gallery_input" accept="image/*" multiple class="d-none">
                        <input type="hidden" name="delete_gallery_images" id="delete_gallery_images" value="">
                        <div class="upload-dropzone-xs" onclick="document.getElementById('gallery_input').click()">
                            <i class="ti tabler-camera-plus me-1"></i> Add Photos
                        </div>
                        <div id="new_gallery_previews" class="gallery-grid-sidebar mt-2"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 sticky-top" style="top: 100px; z-index: 10;">
            <div class="card-header border-bottom">
                <h5 class="mb-0">Publishing Status</h5>
            </div>
            <div class="card-body pt-4">
                <div class="mb-4">
                    <label class="form-label fw-bold">Visibility Status</label>
                    <select name="status" id="visibilityStatus" class="form-select">
                        <option value="active" {{ $listing->status == 'active' ? 'selected' : '' }} data-icon="ti-circle-check" data-color="text-success">Active</option>
                        <option value="pending" {{ $listing->status == 'pending' ? 'selected' : '' }} data-icon="ti-clock" data-color="text-warning">Pending</option>
                        <option value="sold" {{ $listing->status == 'sold' ? 'selected' : '' }} data-icon="ti-discount-check" data-color="text-info">Sold</option>
                        <option value="inactive" {{ $listing->status == 'inactive' ? 'selected' : '' }} data-icon="ti-circle-x" data-color="text-danger">Inactive</option>
                    </select>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" form="editListingForm" class="btn btn-primary btn-lg shadow">
                        <i class="ti tabler-device-floppy me-2"></i> Save Changes
                    </button>
                    <a href="{{ route('admin.listings.index') }}" class="btn btn-label-secondary">
                        Discard
                    </a>
                </div>
            </div>
            <div class="card-footer bg-lighter border-top">
                <div class="d-flex align-items-center justify-content-between">
                    <small class="text-muted">Last updated</small>
                    <small class="fw-bold">{{ $listing->updated_at->diffForHumans() }}</small>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Quick Actions</h6>
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('listings.show', $listing->slug) }}" target="_blank" class="btn btn-sm btn-label-primary">
                        <i class="ti tabler-eye me-1"></i> Preview Listing
                    </a>
                    @if($listing->status == 'pending')
                    <button type="button" class="btn btn-sm btn-label-success" onclick="document.getElementById('approve-form').submit()">
                        <i class="ti tabler-check me-1"></i> Quick Approve
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</form>

<form id="approve-form" action="{{ route('admin.listings.approve', $listing) }}" method="POST" class="d-none">@csrf</form>
@endsection

@section('page-style')
<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
<style>
    /* Modern Form Styling */
    .media-upload-wrapper-sm {
        width: 100%;
        position: relative;
    }

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
        transition: all 0.2s;
    }

    .upload-dropzone-xs:hover {
        border-color: #7367f0;
        color: #7367f0;
        background: #f2f0ff;
    }

    .image-preview-card-sm {
        border-radius: 8px;
        overflow: hidden;
        position: relative;
        height: 160px;
        box-shadow: 0 2px 10px rgba(75, 70, 92, 0.1);
    }

    .image-preview-card-sm img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .image-actions-sm {
        position: absolute;
        bottom: 8px;
        right: 8px;
        display: flex;
        gap: 4px;
        background: rgba(255,255,255,0.85);
        padding: 4px;
        border-radius: 6px;
        backdrop-filter: blur(2px);
    }

    .btn-xs {
        padding: 2px;
        font-size: 0.75rem;
        width: 24px;
        height: 24px;
    }

    /* Gallery Grid Sidebar */
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
        background: #f8f7fa;
    }

    .gallery-tile-sidebar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .tile-remove-xs {
        position: absolute;
        top: 2px;
        right: 2px;
        background: rgba(234, 84, 85, 0.9);
        color: white;
        border: none;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 10px;
        opacity: 0.8;
    }

    .tile-remove-xs:hover {
        opacity: 1;
        background: #ea5455;
    }

    /* Status Selector Styling */
    .custom-option {
        transition: all 0.2s linear;
    }
    .custom-option-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
    }
</style>
@endsection

@section('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Main Image Upload Handling
        const mainInput = document.getElementById('main_image_input');
        const mainContainer = document.getElementById('main_image_upload_container');

        mainInput.addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(re) {
                    // Find or create preview container (but keep the file input intact!)
                    let previewDiv = mainContainer.querySelector('.image-preview-card-sm');
                    if (!previewDiv) {
                        previewDiv = document.createElement('div');
                        previewDiv.className = 'image-preview-card-sm active';
                        mainContainer.innerHTML = '';
                        mainContainer.appendChild(mainInput);
                        mainContainer.appendChild(previewDiv);
                    }
                    
                    previewDiv.innerHTML = `
                        <img src="${re.target.result}" id="main_image_preview">
                        <div class="image-actions-sm">
                            <button type="button" class="btn btn-icon btn-danger btn-xs" onclick="removeMainImage()">
                                <i class="ti tabler-trash"></i>
                            </button>
                            <button type="button" class="btn btn-icon btn-primary btn-xs" onclick="document.getElementById('main_image_input').click()">
                                <i class="ti tabler-pencil"></i>
                            </button>
                        </div>
                    `;
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });

        // Form submit handler to verify file is included
        const form = document.getElementById('editListingForm');
        form.addEventListener('submit', function(e) {
            const mainImageInput = document.getElementById('main_image_input');
            console.log('=== FORM SUBMIT DEBUG ===');
            console.log('Main image input element:', mainImageInput);
            console.log('Main image files count:', mainImageInput.files.length);
            console.log('Main image input value:', mainImageInput.value);
            console.log('Main image input name:', mainImageInput.name);
            
            if (mainImageInput.files.length > 0) {
                console.log('Main image file:', mainImageInput.files[0].name);
                console.log('File size:', mainImageInput.files[0].size);
            } else {
                console.log('NO FILES SELECTED IN INPUT');
            }
            
            // Check FormData
            const formData = new FormData(form);
            console.log('FormData keys:', Array.from(formData.keys()));
            console.log('FormData has main_image:', formData.has('main_image'));
        });

        // Gallery Upload Previews
        const galleryInput = document.getElementById('gallery_input');
        const galleryPreview = document.getElementById('new_gallery_previews');

        galleryInput.addEventListener('change', function(e) {
            Array.from(e.target.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(re) {
                    const div = document.createElement('div');
                    div.className = 'gallery-tile-sidebar';
                    div.innerHTML = `
                        <img src="${re.target.result}">
                        <button type="button" class="tile-remove-xs" onclick="this.parentElement.remove()">
                            <i class="ti tabler-x"></i>
                        </button>
                    `;
                    galleryPreview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });

        // Models Dynamic Loading
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
                        // Always use numeric id to satisfy validation rules
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

    function removeMainImage() {
        if (!confirm('Remove cover photo?')) return;

        const container = document.getElementById('main_image_upload_container');
        const mainInput = document.getElementById('main_image_input');

        // Clear the file input
        mainInput.value = '';

        // Add hidden input to mark for deletion
        let deleteInput = document.querySelector('input[name="delete_main_image"]');
        if (!deleteInput) {
            deleteInput = document.createElement('input');
            deleteInput.type = 'hidden';
            deleteInput.name = 'delete_main_image';
            deleteInput.value = '1';
            document.getElementById('editListingForm').appendChild(deleteInput);
        }

        container.innerHTML = `
            <div class="upload-dropzone-sidebar" onclick="document.getElementById('main_image_input').click()">
                <div class="dz-message-sm">
                    <i class="ti tabler-upload icon-24px text-muted mb-1"></i>
                    <p class="mb-0 small fw-bold">Main Image</p>
                    <span class="text-muted" style="font-size: 0.7rem;">Click to upload</span>
                </div>
            </div>
        `;
    }

    function removeGalleryImage(btn, path) {
        if (!confirm('Remove this photo from gallery?')) return;

        // Track for backend deletion
        const hidden = document.getElementById('delete_gallery_images');
        let vals = hidden.value ? hidden.value.split(',') : [];
        // Avoid duplicates
        if (!vals.includes(path)) {
            vals.push(path);
            hidden.value = vals.filter(v => v && v.trim()).join(',');
        }

        // Remove tile from UI
        const tile = btn.closest('.gallery-tile-sidebar');
        if (tile) tile.remove();
    }
</script>
@endsection
