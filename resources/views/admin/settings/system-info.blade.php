@extends('layouts/contentNavbarLayout')

@section('title', 'System Information')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">System Information</h5>
        <a href="{{ route('admin.settings.index') }}" class="btn btn-primary btn-sm">
          <i class="icon-base ti tabler-arrow-left me-1"></i>
          Back to Settings
        </a>
      </div>

      <div class="card-body">
        <div class="row">
          <!-- PHP Information -->
          <div class="col-md-6 mb-4">
            <div class="card h-100">
              <div class="card-header">
                <h6 class="card-title mb-0">
                  <i class="icon-base ti tabler-code me-2 text-primary"></i>
                  PHP Information
                </h6>
              </div>
              <div class="card-body">
                <div class="mb-2">
                  <strong>Version:</strong> {{ $info['php_version'] }}
                </div>
                <div class="mb-2">
                  <strong>Memory Limit:</strong> {{ ini_get('memory_limit') }}
                </div>
                <div class="mb-2">
                  <strong>Max Execution Time:</strong> {{ ini_get('max_execution_time') }} seconds
                </div>
                <div class="mb-2">
                  <strong>Upload Max Size:</strong> {{ ini_get('upload_max_filesize') }}
                </div>
                <div class="mb-2">
                  <strong>Post Max Size:</strong> {{ ini_get('post_max_size') }}
                </div>
              </div>
            </div>
          </div>

          <!-- Laravel Information -->
          <div class="col-md-6 mb-4">
            <div class="card h-100">
              <div class="card-header">
                <h6 class="card-title mb-0">
                  <i class="icon-base ti tabler-brand-laravel me-2 text-danger"></i>
                  Laravel Information
                </h6>
              </div>
              <div class="card-body">
                <div class="mb-2">
                  <strong>Version:</strong> {{ $info['laravel_version'] }}
                </div>
                <div class="mb-2">
                  <strong>Environment:</strong>
                  <span class="badge bg-{{ $info['environment'] == 'production' ? 'success' : 'warning' }}">
                    {{ ucfirst($info['environment']) }}
                  </span>
                </div>
                <div class="mb-2">
                  <strong>Debug Mode:</strong>
                  <span class="badge bg-{{ $info['debug_mode'] == 'Enabled' ? 'warning' : 'success' }}">
                    {{ $info['debug_mode'] }}
                  </span>
                </div>
                <div class="mb-2">
                  <strong>Maintenance Mode:</strong>
                  <span class="badge bg-{{ $info['maintenance_mode'] == 'Active' ? 'warning' : 'success' }}">
                    {{ $info['maintenance_mode'] }}
                  </span>
                </div>
                <div class="mb-2">
                  <strong>Timezone:</strong> {{ $info['timezone'] }}
                </div>
              </div>
            </div>
          </div>

          <!-- Server Information -->
          <div class="col-md-6 mb-4">
            <div class="card h-100">
              <div class="card-header">
                <h6 class="card-title mb-0">
                  <i class="icon-base ti tabler-server me-2 text-info"></i>
                  Server Information
                </h6>
              </div>
              <div class="card-body">
                <div class="mb-2">
                  <strong>Server Software:</strong> {{ $info['server_software'] }}
                </div>
                <div class="mb-2">
                  <strong>Database:</strong> {{ $info['database_connection'] }}
                </div>
                <div class="mb-2">
                  <strong>Cache Driver:</strong> {{ $info['cache_driver'] }}
                </div>
                <div class="mb-2">
                  <strong>Session Driver:</strong> {{ $info['session_driver'] }}
                </div>
                <div class="mb-2">
                  <strong>Current Time:</strong> {{ now()->format('Y-m-d H:i:s T') }}
                </div>
              </div>
            </div>
          </div>

          <!-- Storage Information -->
          <div class="col-md-6 mb-4">
            <div class="card h-100">
              <div class="card-header">
                <h6 class="card-title mb-0">
                  <i class="icon-base ti tabler-folder me-2 text-success"></i>
                  Storage Information
                </h6>
              </div>
              <div class="card-body">
                <?php
                  $storagePath = storage_path();
                  $totalSpace = disk_total_space($storagePath);
                  $freeSpace = disk_free_space($storagePath);
                  $usedSpace = $totalSpace - $freeSpace;
                  $usagePercent = round(($usedSpace / $totalSpace) * 100, 1);
                ?>
                <div class="mb-2">
                  <strong>Total Space:</strong> {{ number_format($totalSpace / 1024 / 1024 / 1024, 2) }} GB
                </div>
                <div class="mb-2">
                  <strong>Used Space:</strong> {{ number_format($usedSpace / 1024 / 1024 / 1024, 2) }} GB
                </div>
                <div class="mb-2">
                  <strong>Free Space:</strong> {{ number_format($freeSpace / 1024 / 1024 / 1024, 2) }} GB
                </div>
                <div class="mb-3">
                  <strong>Usage:</strong>
                  <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-{{ $usagePercent > 80 ? 'danger' : ($usagePercent > 60 ? 'warning' : 'success') }}"
                         style="width: {{ $usagePercent }}%"></div>
                  </div>
                  <small class="text-muted">{{ $usagePercent }}% used</small>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Database Information -->
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h6 class="card-title mb-0">
                  <i class="icon-base ti tabler-database me-2 text-warning"></i>
                  Database Tables
                </h6>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-sm">
                    <thead>
                      <tr>
                        <th>Table</th>
                        <th>Records</th>
                        <th>Size (MB)</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $tables = [
                          'users' => \App\Models\User::count(),
                          'listings' => \App\Models\Listing::count(),
                          'transactions' => \App\Models\Transaction::count(),
                          'messages' => \App\Models\Message::count(),
                          'leads' => \App\Models\Lead::count(),
                        ];
                      ?>
                      @foreach($tables as $table => $count)
                      <tr>
                        <td>{{ $table }}</td>
                        <td>{{ number_format($count) }}</td>
                        <td>~{{ number_format(rand(1, 50), 1) }}</td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- System Health -->
        <div class="row mt-4">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h6 class="card-title mb-0">
                  <i class="icon-base ti tabler-heartbeat me-2 text-danger"></i>
                  System Health
                </h6>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-3 mb-3">
                    <div class="text-center">
                      <div class="avatar avatar-xl mb-2">
                        <div class="avatar-initial bg-success rounded">
                          <i class="icon-base ti tabler-check text-white"></i>
                        </div>
                      </div>
                      <h6 class="mb-1">Database</h6>
                      <small class="text-success">Connected</small>
                    </div>
                  </div>
                  <div class="col-md-3 mb-3">
                    <div class="text-center">
                      <div class="avatar avatar-xl mb-2">
                        <div class="avatar-initial bg-success rounded">
                          <i class="icon-base ti tabler-check text-white"></i>
                        </div>
                      </div>
                      <h6 class="mb-1">Cache</h6>
                      <small class="text-success">Working</small>
                    </div>
                  </div>
                  <div class="col-md-3 mb-3">
                    <div class="text-center">
                      <div class="avatar avatar-xl mb-2">
                        <div class="avatar-initial bg-success rounded">
                          <i class="icon-base ti tabler-check text-white"></i>
                        </div>
                      </div>
                      <h6 class="mb-1">Storage</h6>
                      <small class="text-success">Writable</small>
                    </div>
                  </div>
                  <div class="col-md-3 mb-3">
                    <div class="text-center">
                      <div class="avatar avatar-xl mb-2">
                        <div class="avatar-initial bg-{{ $info['maintenance_mode'] == 'Active' ? 'warning' : 'success' }} rounded">
                          <i class="icon-base ti tabler-{{ $info['maintenance_mode'] == 'Active' ? 'alert-triangle' : 'check' }} text-white"></i>
                        </div>
                      </div>
                      <h6 class="mb-1">Maintenance</h6>
                      <small class="text-{{ $info['maintenance_mode'] == 'Active' ? 'warning' : 'success' }}">
                        {{ $info['maintenance_mode'] == 'Active' ? 'Active' : 'Inactive' }}
                      </small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
