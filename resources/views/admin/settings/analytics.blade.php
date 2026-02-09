@extends('layouts/contentNavbarLayout')

@section('title', 'Analytics Settings')

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
          <i class="icon-base ti tabler-chart-bar me-2"></i>
          Analytics & Tracking
        </h5>
        <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary btn-sm">
          <i class="icon-base ti tabler-arrow-left me-1"></i>
          Back to Settings
        </a>
      </div>

      <div class="card-body">
        <form action="{{ route('admin.settings.analytics.update') }}" method="POST">
          @csrf
          @method('PUT')

          <!-- Google Analytics -->
          <div class="row">
            <div class="col-12">
              <h6 class="text-primary mb-3">
                <i class="icon-base ti tabler-brand-google me-2"></i>
                Google Analytics
              </h6>
            </div>
          </div>

          <div class="row">
            <div class="col-md-8 mb-3">
              <label for="google_analytics_id" class="form-label">Google Analytics ID</label>
              <input type="text" class="form-control" id="google_analytics_id" name="google_analytics_id"
                     value="{{ old('google_analytics_id', $settings['google_analytics_id'] ?? '') }}"
                     placeholder="GA-XXXXXXXXXX">
              <div class="form-text">Your Google Analytics tracking ID (e.g., GA-123456789)</div>
            </div>

            <div class="col-md-4 mb-3 d-flex align-items-end">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="ga_enabled" disabled
                       {{ !empty($settings['google_analytics_id']) ? 'checked' : '' }}>
                <label class="form-check-label" for="ga_enabled">
                  Enabled
                </label>
              </div>
            </div>
          </div>

          <hr class="my-4">

          <!-- Facebook Pixel -->
          <div class="row">
            <div class="col-12">
              <h6 class="text-primary mb-3">
                <i class="icon-base ti tabler-brand-facebook me-2"></i>
                Facebook Pixel
              </h6>
            </div>
          </div>

          <div class="row">
            <div class="col-md-8 mb-3">
              <label for="facebook_pixel_id" class="form-label">Facebook Pixel ID</label>
              <input type="text" class="form-control" id="facebook_pixel_id" name="facebook_pixel_id"
                     value="{{ old('facebook_pixel_id', $settings['facebook_pixel_id'] ?? '') }}"
                     placeholder="123456789012345">
              <div class="form-text">Your Facebook Pixel ID for tracking conversions and events</div>
            </div>

            <div class="col-md-4 mb-3 d-flex align-items-end">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="fb_enabled" disabled
                       {{ !empty($settings['facebook_pixel_id']) ? 'checked' : '' }}>
                <label class="form-check-label" for="fb_enabled">
                  Enabled
                </label>
              </div>
            </div>
          </div>

          <hr class="my-4">

          <!-- Implementation Notes -->
          <div class="row">
            <div class="col-12">
              <div class="alert alert-info">
                <h6 class="alert-heading mb-2">
                  <i class="icon-base ti tabler-info-circle me-2"></i>
                  Implementation Notes
                </h6>
                <ul class="mb-0">
                  <li>Google Analytics tracking code will be automatically added to all pages</li>
                  <li>Facebook Pixel will track page views, conversions, and custom events</li>
                  <li>Both services require proper privacy policy compliance</li>
                  <li>Test your implementation using the respective platform's debug tools</li>
                </ul>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <button type="submit" class="btn btn-primary">
                <i class="icon-base ti tabler-device-floppy me-1"></i>
                Save Analytics Settings
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
