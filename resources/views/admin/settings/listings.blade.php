@extends('layouts/contentNavbarLayout')

@section('title', 'Listing Settings')

@section('vendor-style')
@endsection

@section('page-style')
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
          <i class="icon-base ti tabler-car me-2"></i>
          Listing Settings
        </h5>
        <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary btn-sm">
          <i class="icon-base ti tabler-arrow-left me-1"></i>
          Back to Settings
        </a>
      </div>

      <div class="card-body">
        <form action="{{ route('admin.settings.listings.update') }}" method="POST">
          @csrf
          @method('PUT')

          <!-- Listing Configuration -->
          <div class="row">
            <div class="col-12">
              <h6 class="text-primary mb-3">
                <i class="icon-base ti tabler-settings me-2"></i>
                Listing Configuration
              </h6>
            </div>

            <div class="col-md-6 mb-3">
              <label for="max_images_per_listing" class="form-label">Max Images Per Listing <span class="text-danger">*</span></label>
              <input type="number" class="form-control" id="max_images_per_listing" name="max_images_per_listing"
                     value="{{ old('max_images_per_listing', $settings['max_images_per_listing'] ?? 10) }}"
                     min="1" max="20" required>
              <div class="form-text">Maximum number of images allowed per listing (1-20)</div>
            </div>

            <div class="col-md-6 mb-3">
              <label for="listing_expiry_days" class="form-label">Listing Expiry (Days) <span class="text-danger">*</span></label>
              <input type="number" class="form-control" id="listing_expiry_days" name="listing_expiry_days"
                     value="{{ old('listing_expiry_days', $settings['listing_expiry_days'] ?? 30) }}"
                     min="7" max="365" required>
              <div class="form-text">Number of days before listings expire (7-365)</div>
            </div>

            <div class="col-md-6 mb-3">
              <label for="commission_percentage" class="form-label">Commission Percentage <span class="text-danger">*</span></label>
              <input type="number" class="form-control" id="commission_percentage" name="commission_percentage"
                     value="{{ old('commission_percentage', $settings['commission_percentage'] ?? 5) }}"
                     min="0" max="50" step="0.1" required>
              <div class="form-text">Percentage taken from each sale (0-50%)</div>
            </div>

            <div class="col-md-6 mb-3">
              <label for="max_file_size" class="form-label">Max File Size (MB) <span class="text-danger">*</span></label>
              <input type="number" class="form-control" id="max_file_size" name="max_file_size"
                     value="{{ old('max_file_size', $settings['max_file_size'] ?? 5) }}"
                     min="1" max="50" required>
              <div class="form-text">Maximum file size for uploads in MB (1-50)</div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <button type="submit" class="btn btn-primary">
                <i class="icon-base ti tabler-device-floppy me-1"></i>
                Save Listing Settings
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-script')
@endsection
