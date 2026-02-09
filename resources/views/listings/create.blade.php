@extends('layouts.app')

@section('title', 'Place an Ad - azal Cars')

@section('content')
<div class="bg-gray-50 min-h-screen py-10 selection:bg-[#6041E0] selection:text-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-10 text-center">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight sm:text-4xl mb-2">Sell your car</h1>
            <p class="text-lg text-gray-500">Reach millions of buyers with a few easy steps.</p>
        </div>

        <form action="{{ route('listings.store') }}" method="POST" enctype="multipart/form-data" id="createListingForm">
            @csrf

            <!-- Form Container -->
            <div class="space-y-8">
                
                <!-- Vehicle Details Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Vehicle Details</h2>
                            <p class="text-sm text-gray-500 mt-1">Tell us the basics about your car.</p>
                        </div>
                        <div class="hidden sm:block">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-[#6041E0]/10 text-[#6041E0]">
                                Step 1 of 3
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-8 space-y-8">
                        <!-- Title -->
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-2">Ad Title <span class="text-red-500">*</span></label>
                            <input type="text" name="title" required 
                                class="w-full rounded-lg border border-gray-300 focus:border-[#6041E0] focus:ring-2 focus:ring-[#6041E0]/20 text-base py-3 px-4 transition-all"
                                placeholder="e.g. 2023 Toyota Camry SE - Low Mileage">
                            <p class="text-xs text-gray-500 mt-2">A catchy title helps your ad stand out.</p>
                        </div>

                        <!-- Grid: Basic Specs -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Make -->
                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-2">Make <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select name="make_id" required 
                                        class="w-full appearance-none rounded-lg border border-gray-300 focus:border-[#6041E0] focus:ring-2 focus:ring-[#6041E0]/20 text-sm py-3 px-4 pr-8 bg-white cursor-pointer transition-all">
                                        <option value="">Select Make</option>
                                        @foreach($makes as $make)
                                            <option value="{{ $make->id }}">{{ $make->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                        <i class="fas fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Model -->
                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-2">Model <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select name="vehicle_model_id" required 
                                        class="w-full appearance-none rounded-lg border border-gray-300 focus:border-[#6041E0] focus:ring-2 focus:ring-[#6041E0]/20 text-sm py-3 px-4 pr-8 bg-white cursor-pointer transition-all disabled:bg-gray-100 disabled:text-gray-400">
                                        <option value="">Select Make First</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                        <i class="fas fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Year -->
                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-2">Year <span class="text-red-500">*</span></label>
                                <input type="number" name="year" required min="1900" max="{{ date('Y') + 1 }}"
                                    class="w-full rounded-lg border border-gray-300 focus:border-[#6041E0] focus:ring-2 focus:ring-[#6041E0]/20 text-sm py-3 px-4 transition-all"
                                    placeholder="e.g. 2023">
                            </div>

                            <!-- Type -->
                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-2">Body Type <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select name="vehicle_type_id" required 
                                        class="w-full appearance-none rounded-lg border border-gray-300 focus:border-[#6041E0] focus:ring-2 focus:ring-[#6041E0]/20 text-sm py-3 px-4 pr-8 bg-white cursor-pointer transition-all">
                                        <option value="">Select Type</option>
                                        @foreach($types as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                        <i class="fas fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Mileage -->
                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-2">Mileage (km) <span class="text-red-500">*</span></label>
                                <input type="number" name="mileage" required min="0"
                                    class="w-full rounded-lg border border-gray-300 focus:border-[#6041E0] focus:ring-2 focus:ring-[#6041E0]/20 text-sm py-3 px-4 transition-all"
                                    placeholder="e.g. 50000">
                            </div>

                            <!-- Condition -->
                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-2">Condition <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select name="condition" required 
                                        class="w-full appearance-none rounded-lg border border-gray-300 focus:border-[#6041E0] focus:ring-2 focus:ring-[#6041E0]/20 text-sm py-3 px-4 pr-8 bg-white cursor-pointer transition-all">
                                        <option value="used">Used</option>
                                        <option value="new">New</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                        <i class="fas fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="border-gray-100">

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-2">Description</label>
                            
                            <!-- Quill Editor Container -->
                            <div id="editor-container" class="bg-white rounded-lg border border-gray-300" style="min-height: 200px;"></div>
                            
                            <!-- Hidden input to store editor content -->
                            <input type="hidden" name="description" id="description_hidden">
                        </div>

                        <!-- Technical Specs Expandable -->
                        <div x-data="{ expanded: false }">
                            <button type="button" @click="expanded = !expanded" class="flex items-center text-sm font-bold text-[#6041E0] hover:text-[#4a32b0] transition-colors focus:outline-none">
                                <span x-text="expanded ? 'Hide Technical Details' : 'Show Technical Details (Fuel, Engine, etc.)'"></span>
                                <i class="fas ml-2 transition-transform duration-200" :class="expanded ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                            </button>
                            
                            <div x-show="expanded" x-collapse class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <!-- Fuel -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Fuel Type</label>
                                    <select name="fuel_type" class="w-full rounded-lg border border-gray-300 text-sm py-2.5 px-4 bg-white">
                                        <option value="petrol">Petrol</option>
                                        <option value="diesel">Diesel</option>
                                        <option value="electric">Electric</option>
                                        <option value="hybrid">Hybrid</option>
                                    </select>
                                </div>
                                <!-- Trans -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Transmission</label>
                                    <select name="transmission" class="w-full rounded-lg border border-gray-300 text-sm py-2.5 px-4 bg-white">
                                        <option value="automatic">Automatic</option>
                                        <option value="manual">Manual</option>
                                    </select>
                                </div>
                                <!-- Drive -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Drivetrain</label>
                                    <select name="drivetrain" class="w-full rounded-lg border border-gray-300 text-sm py-2.5 px-4 bg-white">
                                        <option value="">Select</option>
                                        <option value="fwd">FWD</option>
                                        <option value="rwd">RWD</option>
                                        <option value="awd">AWD</option>
                                        <option value="4wd">4WD</option>
                                    </select>
                                </div>
                                <!-- Color -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Color</label>
                                    <input type="text" name="color" class="w-full rounded-lg border border-gray-300 text-sm py-2.5 px-4" placeholder="e.g. Silver">
                                </div>
                                <!-- Engine -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Engine Size</label>
                                    <input type="text" name="engine_size" class="w-full rounded-lg border border-gray-300 text-sm py-2.5 px-4" placeholder="e.g. 2.0L Turbo">
                                </div>
                                <!-- Location -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Review Location</label>
                                    <input type="text" name="location" class="w-full rounded-lg border border-gray-300 text-sm py-2.5 px-4" placeholder="City, State">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Photos Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50">
                        <h2 class="text-lg font-bold text-gray-900">Photos</h2>
                        <p class="text-sm text-gray-500 mt-1">High-quality photos increase your chances of a quick sale.</p>
                    </div>
                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Main Image -->
                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-3">Cover Photo <span class="text-red-500">*</span></label>
                                <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl hover:border-[#6041E0] hover:bg-[#6041E0]/5 transition-all text-center cursor-pointer group h-64 flex flex-col items-center justify-center relative overflow-hidden" onclick="document.getElementById('main_image_input').click()">
                                    <input type="file" name="main_image" id="main_image_input" accept="image/*" class="hidden" required>
                                    <img id="main_preview_img" class="absolute inset-0 w-full h-full object-cover hidden z-10">
                                    
                                    <div id="main_upload_placeholder" class="z-0">
                                        <div class="w-16 h-16 bg-white rounded-full shadow-sm flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                            <i class="fas fa-camera text-2xl text-[#6041E0]"></i>
                                        </div>
                                        <p class="text-sm font-bold text-gray-700 group-hover:text-[#6041E0]">Click to upload cover</p>
                                        <p class="text-xs text-gray-400 mt-1">First impression matters!</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Gallery -->
                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-3">Gallery Photos</label>
                                <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl hover:border-[#6041E0] hover:bg-[#6041E0]/5 transition-all text-center cursor-pointer h-64 overflow-y-auto custom-scrollbar p-2" onclick="document.getElementById('gallery_input').click()">
                                    <input type="file" name="gallery[]" id="gallery_input" multiple accept="image/*" class="hidden">
                                    
                                    <div id="gallery_placeholder" class="h-full flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 bg-white rounded-full shadow-sm flex items-center justify-center mx-auto mb-3">
                                            <i class="fas fa-images text-xl text-gray-400"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-600">Add multiple photos</p>
                                        <p class="text-xs text-gray-400 mt-1">Interior, exterior, details...</p>
                                    </div>

                                    <div id="gallery_previews" class="grid grid-cols-3 gap-2 hidden">
                                        <!-- Dynamic previews -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Price & Contact Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50">
                        <h2 class="text-lg font-bold text-gray-900">Price & Features</h2>
                    </div>
                    <div class="p-8 space-y-8">
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-2">Selling Price ({{ \App\Helpers\Helpers::getCurrencySymbol() }}) <span class="text-red-500">*</span></label>
                            <div class="relative max-w-sm">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-gray-500 font-bold text-lg">{{ \App\Helpers\Helpers::getCurrencySymbol() }}</span>
                                </div>
                                <input type="number" name="price" required min="0" step="0.01"
                                    class="w-full rounded-lg border border-gray-300 focus:border-[#6041E0] focus:ring-2 focus:ring-[#6041E0]/20 text-xl font-bold text-gray-900 py-3 pl-12 pr-4 transition-all"
                                    placeholder="0">
                            </div>
                        </div>

                        <!-- Features -->
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-2">Features</label>
                            <div class="flex gap-2 mb-3">
                                <input type="text" id="feature_input" 
                                    class="flex-1 rounded-lg border border-gray-300 focus:border-[#6041E0] focus:ring-2 focus:ring-[#6041E0]/20 text-sm py-3 px-4" 
                                    placeholder="Add feature (e.g. Leather Seats)">
                                <button type="button" id="add_feature_btn" 
                                    class="bg-gray-900 text-white px-6 py-3 rounded-lg text-sm font-bold hover:bg-gray-800 transition-colors">
                                    Add
                                </button>
                            </div>
                            <div id="features_list" class="flex flex-wrap gap-2"></div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-end gap-4 pt-4">
                    <a href="{{ route('home') }}" class="text-gray-600 font-bold hover:text-gray-900 text-sm">Cancel</a>
                    <button type="submit" class="w-full sm:w-auto bg-[#6041E0] hover:bg-[#4c30c4] text-white text-base font-bold py-4 px-10 rounded-xl shadow-lg shadow-[#6041E0]/30 hover:shadow-[#6041E0]/50 transition-all transform hover:-translate-y-0.5">
                        Post Ad Now
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

@push('scripts')
<!-- Alpine.js for collapse logic -->
<script src="//unpkg.com/alpinejs" defer></script>
<!-- Quill Editor CDN -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
    // Initialize Quill Editor
    var quill = new Quill('#editor-container', {
        theme: 'snow',
        placeholder: 'Describe the vehicle\'s condition, features, history, and any modifications...',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
                ['blockquote', 'code-block'],
                [{ 'header': 1 }, { 'header': 2 }],               // custom button values
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
                [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
                [{ 'direction': 'rtl' }],                         // text direction
                [{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
                [{ 'font': [] }],
                [{ 'align': [] }],
                ['clean']                                         // remove formatting button
            ]
        }
    });

    // Sync Editor content to hidden input
    var form = document.getElementById('createListingForm');
    form.onsubmit = function() {
        var description = document.querySelector('input[name=description]');
        description.value = quill.root.innerHTML;
    };

    // Models Loading
    const makeSelect = document.querySelector('select[name="make_id"]');
    const modelSelect = document.querySelector('select[name="vehicle_model_id"]');

    if(makeSelect && modelSelect) {
        makeSelect.addEventListener('change', function() {
             if (this.value) {
                modelSelect.disabled = true;
                modelSelect.innerHTML = '<option>Loading...</option>';
                
                fetch(`/api/models/${this.value}`)
                    .then(r => r.json())
                    .then(models => {
                         modelSelect.innerHTML = '<option value="">Select Model</option>';
                         models.forEach(m => {
                             const opt = document.createElement('option');
                             opt.value = m.id;
                             opt.innerText = m.name;
                             modelSelect.appendChild(opt);
                         });
                         modelSelect.disabled = false;
                    });
            } else {
                modelSelect.innerHTML = '<option value="">Select Make First</option>';
                modelSelect.disabled = true;
            }
        });
    }

    // Main Image Logic
    const mainInput = document.getElementById('main_image_input');
    const mainPreview = document.getElementById('main_preview_img');
    const mainPlaceholder = document.getElementById('main_upload_placeholder');
    
    if(mainInput) {
        mainInput.addEventListener('change', function(e) {
            if(e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(re) {
                    mainPreview.src = re.target.result;
                    mainPreview.classList.remove('hidden');
                    mainPlaceholder.classList.add('opacity-0'); // Hide placeholder text cleanly
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    }

    // Gallery Logic
    const galleryInput = document.getElementById('gallery_input');
    const galleryPreviews = document.getElementById('gallery_previews');
    const galleryHolder = document.getElementById('gallery_placeholder');
    
    if(galleryInput) {
        galleryInput.addEventListener('change', function(e) {
            galleryPreviews.innerHTML = '';
            
            if(e.target.files.length > 0) {
                galleryHolder.classList.add('hidden');
                galleryPreviews.classList.remove('hidden');
                
                Array.from(e.target.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(re) {
                        const div = document.createElement('div');
                        div.className = 'aspect-square rounded-lg overflow-hidden border border-gray-200 relative shadow-sm';
                        div.innerHTML = `<img src="${re.target.result}" class="w-full h-full object-cover">`;
                        galleryPreviews.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                });
            } else {
                galleryHolder.classList.remove('hidden');
                galleryPreviews.classList.add('hidden');
            }
        });
    }

    // Features Logic
    const featureInput = document.getElementById('feature_input');
    const addFeatureBtn = document.getElementById('add_feature_btn');
    const featuresList = document.getElementById('features_list');

    function addFeature() {
        const val = featureInput.value.trim();
        if (val) {
            const tag = document.createElement('div');
            tag.className = 'inline-flex items-center gap-2 px-4 py-2 bg-[#6041E0]/10 text-[#6041E0] rounded-full text-sm font-bold border border-[#6041E0]/20 animate-fade-in-up';
            tag.innerHTML = `
                <span>${val}</span>
                <input type="hidden" name="features[]" value="${val}">
                <button type="button" class="ml-1 text-[#6041E0]/60 hover:text-red-500 focus:outline-none transition-colors" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            `;
            featuresList.appendChild(tag);
            featureInput.value = '';
            featureInput.focus();
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
</script>

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.3s ease-out forwards;
    }
    /* Custom Scrollbar for gallery area */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1; 
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #d1d5db; 
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #9ca3af; 
    }
</style>
@endpush
@endsection
