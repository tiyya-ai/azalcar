@extends('layouts/contentNavbarLayout')

@section('title', 'Backup Settings')

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
          <i class="icon-base ti tabler-database me-2"></i>
          Backup Settings
        </h5>
        <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary btn-sm">
          <i class="icon-base ti tabler-arrow-left me-1"></i>
          Back to Settings
        </a>
      </div>

      <div class="card-body">
        <form action="{{ route('admin.settings.backup.update') }}" method="POST">
          @csrf
          @method('PUT')

          <!-- Backup Configuration -->
          <div class="row">
            <div class="col-12">
              <h6 class="text-primary mb-3">
                <i class="icon-base ti tabler-settings me-2"></i>
                Backup Configuration
              </h6>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="backup_frequency" class="form-label">Backup Frequency <span class="text-danger">*</span></label>
              <select class="form-select" id="backup_frequency" name="backup_frequency" required>
                <option value="daily" {{ ($settings['backup_frequency'] ?? 'weekly') == 'daily' ? 'selected' : '' }}>Daily</option>
                <option value="weekly" {{ ($settings['backup_frequency'] ?? 'weekly') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                <option value="monthly" {{ ($settings['backup_frequency'] ?? 'weekly') == 'monthly' ? 'selected' : '' }}>Monthly</option>
              </select>
              <div class="form-text">How often automatic backups should be created</div>
            </div>

            <div class="col-md-6 mb-3 d-flex align-items-end">
              <div class="alert alert-info mb-0">
                <small>
                  <strong>Current Schedule:</strong><br>
                  @if(($settings['backup_frequency'] ?? 'weekly') == 'daily')
                    Daily at 2:00 AM
                  @elseif(($settings['backup_frequency'] ?? 'weekly') == 'weekly')
                    Weekly on Sunday at 2:00 AM
                  @else
                    Monthly on the 1st at 2:00 AM
                  @endif
                </small>
              </div>
            </div>
          </div>

          <hr class="my-4">

          <!-- Backup Information -->
          <div class="row">
            <div class="col-12">
              <h6 class="text-primary mb-3">
                <i class="icon-base ti tabler-info-circle me-2"></i>
                Backup Information
              </h6>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="alert alert-warning">
                <h6 class="alert-heading mb-2">
                  <i class="icon-base ti tabler-alert-triangle me-2"></i>
                  Important Notes
                </h6>
                <ul class="mb-0">
                  <li>Backups include database and uploaded files</li>
                  <li>Backup files are stored securely on the server</li>
                  <li>Old backups are automatically cleaned up after 30 days</li>
                  <li>Manual backups can be created anytime from the Backup Management page</li>
                  <li>Consider downloading important backups to external storage</li>
                </ul>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <button type="submit" class="btn btn-primary">
                <i class="icon-base ti tabler-device-floppy me-1"></i>
                Save Backup Settings
              </button>
              <a href="{{ route('admin.backups.index') }}" class="btn btn-outline-secondary ms-2">
                <i class="icon-base ti tabler-folder me-1"></i>
                Manage Backups
              </a>
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
