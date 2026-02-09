@extends('layouts/contentNavbarLayout')

@section('title', 'Create News Article')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/quill/typography.scss',
  'resources/assets/vendor/libs/quill/katex.scss',
  'resources/assets/vendor/libs/quill/editor.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/quill/katex.js',
  'resources/assets/vendor/libs/quill/quill.js'
])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <h4 class="fw-bold py-3 mb-4">
                <span class="text-muted fw-light">Management / News /</span> Create Article
            </h4>
        </div>
        <div class="col-sm-6 col-xl-9 text-sm-end">
            <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Basic Layout -->
    <form id="createNewsForm" action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row center-form-wrapper justify-content-center">
            <div class="col-xl-8">
                <div class="card mb-4 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary"><i class="bx bx-news me-2"></i>New Article Content</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Title -->
                            <div class="col-12">
                                <label class="form-label" for="title">Article Title</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-heading"></i></span>
                                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" placeholder="e.g. 2026 Model X Review" required />
                                </div>
                                @error('title') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <!-- Excerpt -->
                            <div class="col-12">
                                <label class="form-label" for="excerpt">Short Excerpt</label>
                                <textarea class="form-control" id="excerpt" name="excerpt" rows="2" maxlength="500" placeholder="Brief summary for listing cards...">{{ old('excerpt') }}</textarea>
                                <div class="form-text">Max 500 characters.</div>
                            </div>

                            <!-- Content -->
                            <div class="col-12">
                                <label class="form-label" for="content">Full Content</label>
                                <div id="quill-editor" style="height: 400px;">
                                    {!! old('content') !!}
                                </div>
                                <input type="hidden" name="content" id="content-hidden">
                                @error('content') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mt-5 pt-3 border-top text-end">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bx bx-save me-1"></i> Create Article
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-xl-4">
                 <!-- Featured Image Card -->
                 <div class="card mb-4 shadow-sm">
                    <div class="card-header pb-0">
                        <h5 class="card-title mb-0">Featured Image</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-center bg-lighter border rounded mb-3 p-2" style="height: 200px; position: relative; overflow: hidden;">
                            <div id="placeholder-icon" class="text-center">
                                <i class="bx bx-image text-muted" style="font-size: 3rem;"></i>
                                <p class="mt-2 text-muted mb-0 small">No Cover Image</p>
                            </div>
                            <img src="" id="imagePreview" class="d-none img-fluid rounded" style="max-height: 100%; width: auto; object-fit: contain;">
                        </div>
                        
                        <div class="d-grid gap-2">
                             <label for="image" class="btn btn-outline-primary" tabindex="0">
                                <span class="d-none d-sm-block">Upload Cover</span>
                                <i class="bx bx-upload d-block d-sm-none"></i>
                                <input type="file" id="image" name="image" class="account-file-input" hidden accept="image/png, image/jpeg, image/gif, image/webp" onchange="previewImage(this)"/>
                            </label>
                            <p class="text-muted small mb-0 text-center">Allowed JPG, GIF, PNG, WEBP. Max 2MB.</p>
                        </div>
                        @error('image') <div class="text-danger small mt-1 text-center">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Settings Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Publishing Settings</h5>
                    </div>
                    <div class="card-body">
                         <!-- Status -->
                         <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select text-uppercase fw-bold" id="status" name="status" required>
                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }} class="text-success">Published</option>
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }} class="text-secondary">Draft</option>
                                <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }} class="text-warning">Archived</option>
                            </select>
                        </div>

                         <!-- Category -->
                         <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="News" {{ old('category') == 'News' ? 'selected' : '' }}>News</option>
                                <option value="Review" {{ old('category') == 'Review' ? 'selected' : '' }}>Review</option>
                                <option value="Guide" {{ old('category') == 'Guide' ? 'selected' : '' }}>Guide</option>
                                <option value="Video" {{ old('category') == 'Video' ? 'selected' : '' }}>Video</option>
                                <option value="Press Release" {{ old('category') == 'Press Release' ? 'selected' : '' }}>Press Release</option>
                            </select>
                        </div>
                        
                        <!-- Publish Date -->
                        <div class="mb-3">
                            <label for="published_at" class="form-label">Publish Date & Time</label>
                            <input type="datetime-local" class="form-control" id="published_at" name="published_at" value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}">
                            <div class="form-text">Leave future date to schedule.</div>
                        </div>
                        
                        <hr class="my-3">
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Visiblity</span>
                            <span class="badge bg-label-primary">Public</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@section('page-script')
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

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Quill
        const quill = new Quill('#quill-editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'font': [] }, { 'size': [] }],
                    [ 'bold', 'italic', 'underline', 'strike' ],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'script': 'super' }, { 'script': 'sub' }],
                    [{ 'header': '1' }, { 'header': '2' }, 'blockquote', 'code-block' ],
                    [{ 'list': 'ordered' }, { 'list': 'bullet'}, { 'indent': '-1' }, { 'indent': '+1' }],
                    [ 'direction', { 'align': [] }],
                    [ 'link', 'image', 'video', 'formula' ],
                    [ 'clean' ]
                ]
            }
        });

        // Sync Content on Submit
        const form = document.querySelector('#createNewsForm');
        form.onsubmit = function() {
            // Populate hidden form on submit
            const contentInput = document.querySelector('#content-hidden');
            contentInput.value = quill.root.innerHTML;
        };
    });
</script>
@endsection

<style>
    .bg-lighter { background-color: #f8f9fa; }
    .center-form-wrapper { max-width: 1400px; margin: 0 auto; }
</style>
@endsection
