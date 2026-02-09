@extends('layouts/contentNavbarLayout')

@section('title', 'System Settings')

@section('vendor-style')
@endsection

@section('page-style')
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">System Settings</h5>
        <div>
          <a href="{{ route('admin.settings.system-info') }}" class="btn btn-outline-info btn-sm">
            <i class="icon-base ti tabler-info-circle me-1"></i>
            System Info
          </a>
          <button type="button" class="btn btn-outline-warning btn-sm" onclick="clearCache()">
            <i class="icon-base ti tabler-refresh me-1"></i>
            Clear Cache
          </button>
        </div>
      </div>

      <div class="card-body">
        <div class="row">
          <!-- General Settings -->
          <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 border-primary">
              <div class="card-body text-center">
                <div class="avatar avatar-xl mb-3 mx-auto">
                  <div class="avatar-initial rounded bg-label-primary">
                    <i class="icon-base ti tabler-settings icon-26px text-primary"></i>
                  </div>
                </div>
                <h6 class="card-title mb-2">General Settings</h6>
                <p class="card-text text-muted small mb-3">Site name, contact info, security settings</p>
                <a href="{{ route('admin.settings.general') }}" class="btn btn-primary btn-sm">
                  <i class="icon-base ti tabler-edit me-1"></i>
                  Configure
                </a>
              </div>
            </div>
          </div>

          <!-- Listing Settings -->
          <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 border-success">
              <div class="card-body text-center">
                <div class="avatar avatar-xl mb-3 mx-auto">
                  <div class="avatar-initial rounded bg-label-success">
                    <i class="icon-base ti tabler-car icon-26px text-success"></i>
                  </div>
                </div>
                <h6 class="card-title mb-2">Listing Settings</h6>
                <p class="card-text text-muted small mb-3">Images, expiry, commissions, file sizes</p>
                <a href="{{ route('admin.settings.listings') }}" class="btn btn-success btn-sm">
                  <i class="icon-base ti tabler-edit me-1"></i>
                  Configure
                </a>
              </div>
            </div>
          </div>

          <!-- Email Settings -->
          <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 border-info">
              <div class="card-body text-center">
                <div class="avatar avatar-xl mb-3 mx-auto">
                  <div class="avatar-initial rounded bg-label-info">
                    <i class="icon-base ti tabler-mail icon-26px text-info"></i>
                  </div>
                </div>
                <h6 class="card-title mb-2">Email Settings</h6>
                <p class="card-text text-muted small mb-3">SMTP configuration and email testing</p>
                <a href="{{ route('admin.settings.email') }}" class="btn btn-info btn-sm">
                  <i class="icon-base ti tabler-edit me-1"></i>
                  Configure
                </a>
              </div>
            </div>
          </div>

          <!-- Analytics Settings -->
          <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 border-warning">
              <div class="card-body text-center">
                <div class="avatar avatar-xl mb-3 mx-auto">
                  <div class="avatar-initial rounded bg-label-warning">
                    <i class="icon-base ti tabler-chart-bar icon-26px text-warning"></i>
                  </div>
                </div>
                <h6 class="card-title mb-2">Analytics Settings</h6>
                <p class="card-text text-muted small mb-3">Google Analytics, Facebook Pixel</p>
                <a href="{{ route('admin.settings.analytics') }}" class="btn btn-warning btn-sm">
                  <i class="icon-base ti tabler-edit me-1"></i>
                  Configure
                </a>
              </div>
            </div>
          </div>

          <!-- Payment Settings -->
          <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 border-primary">
              <div class="card-body text-center">
                <div class="avatar avatar-xl mb-3 mx-auto">
                  <div class="avatar-initial rounded bg-label-primary">
                    <i class="icon-base ti tabler-credit-card icon-26px text-primary"></i>
                  </div>
                </div>
                <h6 class="card-title mb-2">Payment Settings</h6>
                <p class="card-text text-muted small mb-3">Stripe, PayPal, Bank Transfer, Cash on Delivery</p>
                <a href="{{ route('admin.settings.payments') }}" class="btn btn-primary btn-sm">
                  <i class="icon-base ti tabler-edit me-1"></i>
                  Configure
                </a>
              </div>
            </div>
          </div>

          <!-- Backup Settings -->
          <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 border-danger">
              <div class="card-body text-center">
                <div class="avatar avatar-xl mb-3 mx-auto">
                  <div class="avatar-initial rounded bg-label-danger">
                    <i class="icon-base ti tabler-database icon-26px text-danger"></i>
                  </div>
                </div>
                <h6 class="card-title mb-2">Backup Settings</h6>
                <p class="card-text text-muted small mb-3">Backup frequency and management</p>
                <a href="{{ route('admin.settings.backup') }}" class="btn btn-danger btn-sm">
                  <i class="icon-base ti tabler-edit me-1"></i>
                  Configure
                </a>
              </div>
            </div>
          </div>

          <!-- Backup Management -->
          <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 border-secondary">
              <div class="card-body text-center">
                <div class="avatar avatar-xl mb-3 mx-auto">
                  <div class="avatar-initial rounded bg-label-secondary">
                    <i class="icon-base ti tabler-file-zip icon-26px text-secondary"></i>
                  </div>
                </div>
                <h6 class="card-title mb-2">Backup Management</h6>
                <p class="card-text text-muted small mb-3">Create and manage backups</p>
                <a href="{{ route('admin.backups.index') }}" class="btn btn-secondary btn-sm">
                  <i class="icon-base ti tabler-folder me-1"></i>
                  Manage
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script>
function clearCache() {
    if (confirm('Are you sure you want to clear the application cache? This will temporarily slow down the application.')) {
        fetch('{{ route("admin.settings.clear-cache") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Cache cleared successfully!');
                location.reload();
            } else {
                alert('Error clearing cache');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error clearing cache');
        });
    }
}
</script>
@endsection
