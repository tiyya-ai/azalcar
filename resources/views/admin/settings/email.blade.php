@extends('layouts/contentNavbarLayout')

@section('title', 'Email Settings')

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
          <i class="icon-base ti tabler-mail me-2"></i>
          Email Settings
        </h5>
        <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary btn-sm">
          <i class="icon-base ti tabler-arrow-left me-1"></i>
          Back to Settings
        </a>
      </div>

      <div class="card-body">
        <!-- SMTP Configuration -->
        <div class="row">
          <div class="col-12">
            <h6 class="text-primary mb-3">
              <i class="icon-base ti tabler-server me-2"></i>
              SMTP Configuration
            </h6>
          </div>
        </div>

        <form action="{{ route('admin.settings.email.update') }}" method="POST" id="emailSettingsForm">
          @csrf
          @method('PUT')

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="smtp_host" class="form-label">SMTP Host</label>
              <input type="text" class="form-control" id="smtp_host" name="smtp_host"
                     value="{{ old('smtp_host', $settings['smtp_host'] ?? '') }}"
                     placeholder="smtp.gmail.com">
              <div class="form-text">Your SMTP server hostname</div>
            </div>

            <div class="col-md-6 mb-3">
              <label for="smtp_port" class="form-label">SMTP Port</label>
              <input type="number" class="form-control" id="smtp_port" name="smtp_port"
                     value="{{ old('smtp_port', $settings['smtp_port'] ?? 587) }}"
                     min="1" max="65535">
              <div class="form-text">Common ports: 587 (TLS), 465 (SSL), 25 (Plain)</div>
            </div>

            <div class="col-md-6 mb-3">
              <label for="smtp_username" class="form-label">SMTP Username</label>
              <input type="text" class="form-control" id="smtp_username" name="smtp_username"
                     value="{{ old('smtp_username', $settings['smtp_username'] ?? '') }}"
                     placeholder="your-email@gmail.com">
              <div class="form-text">Your SMTP authentication username</div>
            </div>

            <div class="col-md-6 mb-3">
              <label for="smtp_encryption" class="form-label">SMTP Encryption</label>
              <select class="form-select" id="smtp_encryption" name="smtp_encryption">
                <option value="" {{ ($settings['smtp_encryption'] ?? '') == '' ? 'selected' : '' }}>None</option>
                <option value="tls" {{ ($settings['smtp_encryption'] ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                <option value="ssl" {{ ($settings['smtp_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
              </select>
              <div class="form-text">Encryption method for SMTP connection</div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <button type="submit" class="btn btn-primary">
                <i class="icon-base ti tabler-device-floppy me-1"></i>
                Save Email Settings
              </button>
            </div>
          </div>
        </form>

        <hr class="my-4">

        <!-- Test Email -->
        <div class="row">
          <div class="col-12">
            <h6 class="text-primary mb-3">
              <i class="icon-base ti tabler-send me-2"></i>
              Test Email Configuration
            </h6>
          </div>
        </div>

        <form action="{{ route('admin.settings.email.test') }}" method="POST" id="testEmailForm">
          @csrf

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="test_email" class="form-label">Test Email Address <span class="text-danger">*</span></label>
              <input type="email" class="form-control" id="test_email" name="test_email"
                     value="{{ old('test_email') }}" required>
              <div class="form-text">Enter an email address to send a test message</div>
            </div>

            <div class="col-md-6 mb-3 d-flex align-items-end">
              <button type="submit" class="btn btn-info">
                <i class="icon-base ti tabler-send me-1"></i>
                Send Test Email
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
<script>
document.getElementById('testEmailForm').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="icon-base ti tabler-loader me-1"></i>Sending...';

    // Re-enable after 10 seconds in case of no response
    setTimeout(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }, 10000);
});
</script>
@endsection
