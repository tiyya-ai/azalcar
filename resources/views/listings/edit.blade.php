@extends('layouts.app')

@section('title', 'Edit Ad - ' . $listing->title)

@section('content')
<div class="container py-48">
    <div class="form-container-card mx-auto" style="max-width: 900px; background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
        <div class="form-header mb-32 text-center">
            <h1 style="font-size: 28px; font-weight: 800; color: #2c3e50;">Update your advertisement</h1>
            <p style="color: #7f8c8d;">Modify the details of your car listing</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger mb-24" style="background: #fee; color: #c33; padding: 15px; border-radius: 8px; border: 1px solid #fcc;">
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('listings.frontend.update', $listing->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Section: Basic Info -->
            <div style="margin-bottom: 40px; padding-bottom: 20px; border-bottom: 1px solid #f0f0f0;">
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 20px; color: #34495e;"><i class="fas fa-info-circle me-10"></i> Basic Information</h3>

                <div class="mb-24">
                    <label class="form-label d-block mb-8">Title</label>
                    <input type="text" name="title" class="search-input w-full" value="{{ $listing->title }}" required>
                </div>

                <div class="mb-24">
                    <label class="form-label d-block mb-8">Condition</label>
                    <div style="display: flex; gap: 20px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="radio" name="condition" value="new" {{ $listing->condition == 'new' ? 'checked' : '' }} required>
                            New
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="radio" name="condition" value="used" {{ $listing->condition == 'used' ? 'checked' : '' }} required>
                            Used
                        </label>
                    </div>
                </div>

                <div class="row mb-24" style="display: flex; gap: 20px;">
                    <div style="flex: 1;">
                        <label class="form-label d-block mb-8">Make</label>
                        <select name="make_id" id="make_id" class="search-input w-full" required>
                            @foreach($makes as $make)
                            <option value="{{ $make->id }}" {{ $listing->make_id == $make->id ? 'selected' : '' }}>{{ $make->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="flex: 1;">
                        <label class="form-label d-block mb-8">Model</label>
                        <select name="vehicle_model_id" id="vehicle_model_id" class="search-input w-full" required>
                            <option value="{{ $listing->vehicle_model_id }}">{{ $listing->vehicleModel->name }}</option>
                        </select>
                    </div>
                    <div style="flex: 1;">
                        <label class="form-label d-block mb-8">Vehicle Type</label>
                        <select name="vehicle_type_id" class="search-input w-full" required>
                            @foreach($types as $type)
                            <option value="{{ $type->id }}" {{ $listing->vehicle_type_id == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section: Tech Specs -->
            <div style="margin-bottom: 40px; padding-bottom: 20px; border-bottom: 1px solid #f0f0f0;">
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 20px; color: #34495e;"><i class="fas fa-cogs me-10"></i> Technical Specifications</h3>
                
                <div class="row mb-24" style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 200px;">
                        <input type="number" name="year" class="search-input w-full" value="{{ $listing->year }}" min="1900" max="{{ date('Y') + 1 }}" required>
                    </div>
                    <div style="flex: 1; min-width: 200px;">
                        <label class="form-label d-block mb-8">Mileage (km)</label>
                        <input type="number" name="mileage" class="search-input w-full" value="{{ $listing->mileage }}" required>
                    </div>
                    <div style="flex: 1; min-width: 200px;">
                        <label class="form-label d-block mb-8">Transmission</label>
                        <select name="transmission" class="search-input w-full" required>
                            <option value="Automatic" {{ $listing->transmission == 'Automatic' ? 'selected' : '' }}>Automatic</option>
                            <option value="Manual" {{ $listing->transmission == 'Manual' ? 'selected' : '' }}>Manual</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-24" style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 200px;">
                        <label class="form-label d-block mb-8">Fuel Type</label>
                        <select name="fuel_type" class="search-input w-full" required>
                            <option value="Petrol" {{ $listing->fuel_type == 'Petrol' ? 'selected' : '' }}>Petrol</option>
                            <option value="Diesel" {{ $listing->fuel_type == 'Diesel' ? 'selected' : '' }}>Diesel</option>
                            <option value="Electric" {{ $listing->fuel_type == 'Electric' ? 'selected' : '' }}>Electric</option>
                            <option value="Hybrid" {{ $listing->fuel_type == 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                        </select>
                    </div>
                    <div style="flex: 1; min-width: 200px;">
                        <label class="form-label d-block mb-8">Engine Size</label>
                        <input type="text" name="engine_size" class="search-input w-full" value="{{ $listing->engine_size }}">
                    </div>
                    <div style="flex: 1; min-width: 200px;">
                        <label class="form-label d-block mb-8">Color</label>
                        <input type="text" name="color" class="search-input w-full" value="{{ $listing->color }}">
                    </div>
                    <div style="flex: 1; min-width: 200px;">
                        <label class="form-label d-block mb-8">Drivetrain</label>
                        <select name="drivetrain" class="search-input w-full">
                            <option value="">Select Drivetrain</option>
                            <option value="FWD" {{ $listing->drivetrain == 'FWD' ? 'selected' : '' }}>FWD</option>
                            <option value="RWD" {{ $listing->drivetrain == 'RWD' ? 'selected' : '' }}>RWD</option>
                            <option value="AWD" {{ $listing->drivetrain == 'AWD' ? 'selected' : '' }}>AWD</option>
                            <option value="4WD" {{ $listing->drivetrain == '4WD' ? 'selected' : '' }}>4WD</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section: Price & Location -->
            <div style="margin-bottom: 40px; padding-bottom: 20px; border-bottom: 1px solid #f0f0f0;">
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 20px; color: #34495e;"><i class="fas fa-tag me-10"></i> Price & Location</h3>
                
                <div class="row mb-24" style="display: flex; gap: 20px;">
                    <div style="flex: 1;">
                        <label class="form-label d-block mb-8">Price (₽)</label>
                        <input type="number" name="price" class="search-input w-full" value="{{ $listing->price }}" required>
                    </div>
                    <div style="flex: 1;">
                        <label class="form-label d-block mb-8">Location (City)</label>
                        <input type="text" name="location" class="search-input w-full" value="{{ $listing->location }}" required>
                    </div>
                </div>
            </div>

            <!-- Section: Photos -->
            <div style="margin-bottom: 40px; padding-bottom: 20px; border-bottom: 1px solid #f0f0f0;">
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 20px; color: #34495e;"><i class="fas fa-camera me-10"></i> Photos</h3>

                <div class="mb-24">
                    <label class="form-label d-block mb-8">Cover Image</label>
                    <input type="file" name="main_image" accept="image/*" class="search-input w-full">
                    @if($listing->main_image)
                        <div style="margin-top: 10px;">
                            <img src="{{ asset($listing->main_image) }}?t={{ time() }}" alt="Current Cover" style="max-width: 200px; border-radius: 8px;">
                        </div>
                    @endif
                    @error('main_image') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-24">
                    <label class="form-label d-block mb-8">Gallery Images (Optional)</label>
                    <input type="file" name="gallery[]" accept="image/*" multiple class="search-input w-full">
                    @if($listing->images && count($listing->images) > 0)
                        <div id="gallery-images" style="margin-top: 10px; display: flex; flex-wrap: wrap; gap: 10px;">
                            @foreach($listing->images as $index => $image)
                                <div style="position: relative;" data-index="{{ $index }}">
                                    <img src="{{ $image }}?t={{ time() }}" alt="Gallery" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                                    <button type="button" onclick="removeGalleryImage(this)" style="position: absolute; top: -5px; right: -5px; background: red; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer; font-size: 12px;">×</button>
                                    <input type="hidden" name="existing_images[]" value="{{ $image }}">
                                </div>
                            @endforeach
                        </div>
                        <div id="removed-images" style="display: none;"></div>
                    @endif
                    @error('gallery') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            <!-- Section: Additional Options -->
            <div style="margin-bottom: 40px; padding-bottom: 20px; border-bottom: 1px solid #f0f0f0;">
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 20px; color: #34495e;"><i class="fas fa-plus-circle me-10"></i> Additional Options / Features</h3>
                
                <div class="features-management-card" style="border: 1px solid #f0f0f0; border-radius: 12px; padding: 20px; background: #fafafa;">
                    <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                        <input type="text" id="feature_input" class="search-input" style="flex: 1;" placeholder="Add a feature (e.g., Sunroof, Leather Seats)...">
                        <button class="btn btn-primary" type="button" id="add_feature_btn" style="padding: 10px 24px; border-radius: 8px;">
                            Add
                        </button>
                    </div>
                    
                    <div id="features_list" class="d-flex flex-wrap gap-10" style="display: flex; flex-wrap: wrap; gap: 10px;">
                        @php
                            $selectedFeatures = is_array($listing->features) ? $listing->features : json_decode($listing->features ?? '[]', true);
                        @endphp
                        @if($selectedFeatures)
                            @foreach($selectedFeatures as $feature)
                                <div class="feature-tag badge" style="background: #6041E0; color: white; padding: 6px 12px; border-radius: 20px; display: flex; align-items: center; gap: 8px; font-size: 13px;">
                                    <span>{{ $feature }}</span>
                                    <input type="hidden" name="features[]" value="{{ $feature }}">
                                    <i class="fas fa-times cursor-pointer" style="cursor: pointer; opacity: 0.8;" onclick="this.parentElement.remove()"></i>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <!-- Section: Description -->
            <div style="margin-bottom: 40px;">
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 20px; color: #34495e;"><i class="fas fa-edit me-10"></i> Description</h3>
                <div class="mb-32">
                    <textarea name="description" id="description_editor" class="search-input w-full" style="height: 180px; padding: 16px; resize: vertical; line-height: 1.6;" required>{{ $listing->description }}</textarea>
                </div>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary px-48 py-16" style="font-size: 18px; font-weight: 700; border-radius: 12px; transition: 0.3s;">
                    Update Advertisement
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function removeGalleryImage(button) {
        const container = button.parentElement;
        const imageSrc = container.querySelector('img').src;
        const removedImagesDiv = document.getElementById('removed-images');

        // Add to removed images hidden input
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'removed_images[]';
        hiddenInput.value = imageSrc.replace(window.location.origin, '');
        removedImagesDiv.appendChild(hiddenInput);

        // Remove the image container
        container.remove();
    }

    // Dynamic models
    const currentModelId = '{{ $listing->vehicle_model_id }}';
    const makeSelect = document.getElementById('make_id');
    const modelSelect = document.getElementById('vehicle_model_id');

    function loadModels(makeId) {
        if (!makeId) return;
        
        fetch(`/api/models/${makeId}`)
            .then(response => response.json())
            .then(data => {
                modelSelect.innerHTML = '<option value="">Select model</option>';
                data.forEach(model => {
                    const option = document.createElement('option');
                    option.value = model.id;
                    option.textContent = model.name;
                    if (model.id == currentModelId) {
                        option.selected = true;
                    }
                    modelSelect.appendChild(option);
                });
            });
    }

    makeSelect.addEventListener('change', function() {
        loadModels(this.value);
    });

    // Load models on page load if make is already selected
    if (makeSelect.value) {
        loadModels(makeSelect.value);
    }
</script>

<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // CKEditor Initialization
        ClassicEditor
            .create(document.querySelector('#description_editor'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo'],
            })
            .catch(error => {
                console.error(error);
            });

        // Features Logic
        const featureInput = document.getElementById('feature_input');
        const addFeatureBtn = document.getElementById('add_feature_btn');
        const featuresList = document.getElementById('features_list');

        function addFeature() {
            const val = featureInput.value.trim();
            if (val) {
                const tag = document.createElement('div');
                tag.className = 'feature-tag badge';
                tag.style.cssText = 'background: #6041E0; color: white; padding: 6px 12px; border-radius: 20px; display: flex; align-items: center; gap: 8px; font-size: 13px;';
                tag.innerHTML = `
                    <span>${val}</span>
                    <input type="hidden" name="features[]" value="${val}">
                    <i class="fas fa-times cursor-pointer" style="cursor: pointer; opacity: 0.8;" onclick="this.parentElement.remove()"></i>
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
    });
</script>
@endsection
