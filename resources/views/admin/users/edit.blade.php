@extends('layouts/contentNavbarLayout')

@section('title', 'Edit User - Admin')

@section('content')
<div class="row">
    <!-- Edit User Sidebar -->
    <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
        <!-- User Card -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="user-avatar-section">
                    <div class="d-flex align-items-center flex-column">
                        <div class="avatar avatar-xl mb-3">
                            @if($user->profile_photo_path)
                                <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="rounded-circle" />
                            @else
                                <span class="avatar-initial rounded-circle bg-label-secondary fs-2">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                            @endif
                        </div>
                        <div class="user-info text-center">
                            <h4 class="mb-2">{{ $user->name }}</h4>
                            <span class="badge bg-label-{{ $user->role === 'admin' ? 'primary' : ($user->role === 'vendor' ? 'info' : 'secondary') }} mt-1">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-center pt-3 gap-2">
                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-label-primary w-100">
                        <i class="ti tabler-eye me-1"></i> View Details
                    </a>
                </div>
                
                <div class="info-container mt-4 pt-3 border-top">
                     <div class="d-flex justify-content-between">
                         <span class="fw-medium">Joined:</span>
                         <span>{{ $user->created_at->format('d M Y') }}</span>
                     </div>
                     <div class="d-flex justify-content-between mt-2">
                         <span class="fw-medium">Listings:</span>
                         <span class="badge bg-label-primary">{{ $user->listings->count() }}</span>
                     </div>
                     <div class="d-flex justify-content-between mt-2">
                         <span class="fw-medium">Balance:</span>
                         <span class="fw-bold text-success">₽{{ number_format($user->balance, 2) }}</span>
                     </div>
                </div>
            </div>
        </div>
        <!-- /User Card -->
    </div>
    <!--/ Edit User Sidebar -->

    <!-- Edit User Form -->
    <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Edit User Details</h5>
                <a href="{{ route('admin.listings.create', ['user_id' => $user->id]) }}" class="btn btn-outline-primary btn-sm">
                    <i class="ti tabler-plus me-1"></i> Create Listing
                </a>
            </div>
            <div class="card-body">
                <form id="formAccountSettings" method="POST" action="{{ route('admin.users.update', $user->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                         <div class="col-12">
                            <label class="form-label">Profile Photo</label>
                            <div class="d-flex align-items-start align-items-sm-center gap-4">
                                <img src="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : asset('assets/img/avatars/1.png') }}"
                                    alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar" style="object-fit: cover" />
                                <div class="button-wrapper">
                                    <label for="upload" class="btn btn-primary me-2 mb-3" tabindex="0">
                                        <span class="d-none d-sm-block">Upload new photo</span>
                                        <i class="ti tabler-upload d-block d-sm-none"></i>
                                        <input type="file" id="upload" class="account-file-input" hidden name="profile_photo" accept="image/png, image/jpeg" />
                                    </label>
                                    <button type="button" class="btn btn-label-secondary account-image-reset mb-3">
                                        <i class="ti tabler-refresh-dot d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block">Reset</span>
                                    </button>
                                    <div class="text-muted">Allowed JPG, GIF or PNG. Max size of 2MB</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="name" class="form-label">Full Name</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti tabler-user"></i></span>
                                <input class="form-control" type="text" id="name" name="name" value="{{ old('name', $user->name) }}" autofocus placeholder="John Doe" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">E-mail</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti tabler-mail"></i></span>
                                <input class="form-control" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" placeholder="john@example.com" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="phone">Phone Number</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti tabler-phone"></i></span>
                                <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="+1 (234) 567-890" />
                            </div>
                        </div>
                       
                        <div class="col-md-6">
                            <label class="form-label" for="role">Role</label>
                            <select id="role" name="role" class="select2 form-select">
                                <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User - Standard</option>
                                <option value="vendor" {{ $user->role == 'vendor' ? 'selected' : '' }}>Seller (Vendor)</option>
                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Administrator</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="status">Account Status</label>
                            <select id="status" name="status" class="select2 form-select">
                                <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="banned" {{ $user->status == 'banned' ? 'selected' : '' }}>Banned</option>
                            </select>
                        </div>
                        
                        @if($user->role == 'vendor' || $user->seller_status != 'none')
                        <div class="col-12">
                            <div class="divider">
                                <div class="divider-text">Seller Information</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Company / Dealership Name</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti tabler-building-store"></i></span>
                                <input type="text" class="form-control" value="{{ $user->seller_company }}" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Seller Application Status</label>
                            <input type="text" class="form-control" value="{{ ucfirst($user->seller_status) }}" disabled>
                        </div>
                        @endif

                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary me-2">Save Changes</button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-label-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--/ Edit User Form -->
</div>

<script>
    document.addEventListener('DOMContentLoaded', function (e) {
      (function () {
        const uploadedAvatar = document.getElementById('uploadedAvatar');
        const fileInput = document.getElementById('upload');
        const resetFileInput = document.querySelector('.account-image-reset');
    
        if (uploadedAvatar) {
          const fileInputImage = uploadedAvatar.src;
    
          if (fileInput) {
            fileInput.onchange = () => {
              if (fileInput.files[0]) {
                uploadedAvatar.src = window.URL.createObjectURL(fileInput.files[0]);
              }
            };
          }
          if (resetFileInput) {
            resetFileInput.onclick = () => {
              fileInput.value = '';
              uploadedAvatar.src = fileInputImage;
            };
          }
        }
      })();
    });
</script>
@endsection
