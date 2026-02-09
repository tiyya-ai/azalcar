@extends('layouts/contentNavbarLayout')

@section('title', 'General Settings')

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
          <i class="icon-base ti tabler-settings me-2"></i>
          General Settings
        </h5>
        <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary btn-sm">
          <i class="icon-base ti tabler-arrow-left me-1"></i>
          Back to Settings
        </a>
      </div>

      <div class="card-body">
        <form action="{{ route('admin.settings.general.update') }}" method="POST">
          @csrf

          <!-- Site Information -->
          <div class="row">
            <div class="col-12">
              <h6 class="text-primary mb-3">
                <i class="icon-base ti tabler-building me-2"></i>
                Site Information
              </h6>
            </div>

            <div class="col-md-6 mb-3">
              <label for="site_name" class="form-label">Site Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="site_name" name="site_name"
                     value="{{ old('site_name', $settings['site_name'] ?? 'azalcar') }}" required>
            </div>

            <div class="col-md-6 mb-3">
              <label for="contact_email" class="form-label">Contact Email <span class="text-danger">*</span></label>
              <input type="email" class="form-control" id="contact_email" name="contact_email"
                     value="{{ old('contact_email', $settings['contact_email'] ?? 'admin@azalcars.com') }}" required>
            </div>

            <div class="col-md-6 mb-3">
              <label for="contact_phone" class="form-label">Contact Phone</label>
              <input type="text" class="form-control" id="contact_phone" name="contact_phone"
                     value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}">
            </div>

            <div class="col-md-6 mb-3">
              <label for="currency" class="form-label">Currency <span class="text-danger">*</span></label>
              <select class="form-select" id="currency" name="currency" required>
                <option value="USD" {{ ($settings['currency'] ?? 'USD') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                <option value="EUR" {{ ($settings['currency'] ?? 'USD') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                <option value="GBP" {{ ($settings['currency'] ?? 'USD') == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                <option value="RUB" {{ ($settings['currency'] ?? 'USD') == 'RUB' ? 'selected' : '' }}>RUB (₽)</option>
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <label for="timezone" class="form-label">Timezone <span class="text-danger">*</span></label>
              <select class="form-select" id="timezone" name="timezone" required>
                <option value="UTC" {{ ($settings['timezone'] ?? 'UTC') == 'UTC' ? 'selected' : '' }}>UTC</option>
                <option value="America/New_York" {{ ($settings['timezone'] ?? 'UTC') == 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                <option value="Europe/London" {{ ($settings['timezone'] ?? 'UTC') == 'Europe/London' ? 'selected' : '' }}>London</option>
                <option value="Asia/Dubai" {{ ($settings['timezone'] ?? 'UTC') == 'Asia/Dubai' ? 'selected' : '' }}>Dubai</option>
              </select>
            </div>

            <div class="col-12 mb-3">
              <label for="site_description" class="form-label">Site Description</label>
              <textarea class="form-control" id="site_description" name="site_description" rows="3"
                        maxlength="500">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
              <div class="form-text">Brief description of your website (max 500 characters)</div>
            </div>
          </div>

          <hr class="my-4">

          <!-- User Settings -->
          <div class="row">
            <div class="col-12">
              <h6 class="text-primary mb-3">
                <i class="icon-base ti tabler-users me-2"></i>
                User Settings
              </h6>
            </div>

            <div class="col-md-4 mb-3">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="allow_registration" name="allow_registration"
                       value="1" {{ ($settings['allow_registration'] ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="allow_registration">
                  Allow User Registration
                </label>
              </div>
            </div>

            <div class="col-md-4 mb-3">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="require_email_verification" name="require_email_verification"
                       value="1" {{ ($settings['require_email_verification'] ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="require_email_verification">
                  Require Email Verification
                </label>
              </div>
            </div>

            <div class="col-md-4 mb-3">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode"
                       value="1" {{ ($settings['maintenance_mode'] ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="maintenance_mode">
                  Maintenance Mode
                </label>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <button type="submit" class="btn btn-primary">
                <i class="icon-base ti tabler-device-floppy me-1"></i>
                Save General Settings
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
