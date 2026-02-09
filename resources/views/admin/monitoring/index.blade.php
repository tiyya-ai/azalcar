@extends('layouts/contentNavbarLayout')

@section('title', 'Abuse Detection & Monitoring')

@section('vendor-style')
@endsection

@section('page-style')
@endsection

@section('content')
<div class="row">
  <!-- Suspicious Users Alert -->
  <div class="col-12 mb-4">
    <div class="alert alert-warning" role="alert">
      <h6 class="alert-heading mb-2">
        <i class="icon-base ti tabler-alert-triangle me-2"></i>
        Abuse Detection Dashboard
      </h6>
      <p class="mb-0">Monitor suspicious activity, lead spikes, and manage user bans to maintain platform integrity.</p>
    </div>
  </div>
</div>

<div class="row">
  <!-- Suspicious Users -->
  <div class="col-lg-6 mb-4">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0">Suspicious Users</h5>
        <a href="{{ route('admin.monitoring.suspicious') }}" class="btn btn-primary btn-sm">View All</a>
      </div>
      <div class="card-body">
        @if($suspiciousUsers->count() > 0)
          <div class="table-responsive">
            <table class="table table-borderless">
              <thead>
                <tr>
                  <th>User</th>
                  <th>Leads (24h)</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($suspiciousUsers as $user)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar-sm me-3">
                        <span class="avatar-initial rounded-circle bg-label-primary">
                          {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                      </div>
                      <div>
                        <h6 class="mb-0">{{ $user->name }}</h6>
                        <small class="text-muted">{{ $user->email }}</small>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="badge bg-label-danger">{{ $user->listings_count }}</span>
                  </td>
                  <td>
                    <span class="badge bg-label-{{ $user->status == 'active' ? 'success' : 'danger' }}">
                      {{ ucfirst($user->status) }}
                    </span>
                  </td>
                  <td>
                    @if($user->status != 'banned')
                      <button class="btn btn-sm btn-outline-danger" onclick="banUser({{ $user->id }}, '{{ $user->name }}')">
                        Ban
                      </button>
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="text-center py-4">
            <i class="icon-base ti tabler-shield-check icon-48px text-success mb-2"></i>
            <p class="text-muted mb-0">No suspicious activity detected</p>
          </div>
        @endif
      </div>
    </div>
  </div>

  <!-- Lead Spikes -->
  <div class="col-lg-6 mb-4">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0">Lead Spikes</h5>
        <a href="{{ route('admin.monitoring.spikes') }}" class="btn btn-primary btn-sm">View All</a>
      </div>
      <div class="card-body">
        @if($leadSpikes->count() > 0)
          <div class="table-responsive">
            <table class="table table-borderless">
              <thead>
                <tr>
                  <th>Time</th>
                  <th>Leads</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($leadSpikes as $spike)
                <tr>
                  <td>{{ \Carbon\Carbon::parse($spike->hour)->format('M d, H:i') }}</td>
                  <td>
                    <span class="badge bg-label-{{ $spike->count > 100 ? 'danger' : 'warning' }}">
                      {{ $spike->count }}
                    </span>
                  </td>
                  <td>
                    @if($spike->count > 100)
                      <span class="badge bg-danger">Critical</span>
                    @elseif($spike->count > 50)
                      <span class="badge bg-warning">High</span>
                    @else
                      <span class="badge bg-info">Moderate</span>
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="text-center py-4">
            <i class="icon-base ti tabler-trending-up icon-48px text-success mb-2"></i>
            <p class="text-muted mb-0">No unusual lead spikes detected</p>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- IP Abuse -->
  <div class="col-lg-6 mb-4">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="card-title m-0">IP Address Abuse</h5>
      </div>
      <div class="card-body">
        @if($ipAbuse->count() > 0)
          <div class="table-responsive">
            <table class="table table-borderless">
              <thead>
                <tr>
                  <th>IP Address</th>
                  <th>Users</th>
                  <th>Leads</th>
                  <th>Risk</th>
                </tr>
              </thead>
              <tbody>
                @foreach($ipAbuse as $abuse)
                <tr>
                  <td>
                    <code>{{ $abuse->ip_address }}</code>
                  </td>
                  <td>{{ $abuse->user_count }}</td>
                  <td>{{ $abuse->lead_count }}</td>
                  <td>
                    @if($abuse->lead_count > 100)
                      <span class="badge bg-danger">High</span>
                    @elseif($abuse->lead_count > 50)
                      <span class="badge bg-warning">Medium</span>
                    @else
                      <span class="badge bg-info">Low</span>
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="text-center py-4">
            <i class="icon-base ti tabler-network icon-48px text-success mb-2"></i>
            <p class="text-muted mb-0">No IP abuse detected</p>
          </div>
        @endif
      </div>
    </div>
  </div>

  <!-- Recent Bans -->
  <div class="col-lg-6 mb-4">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="card-title m-0">Recent Bans</h5>
      </div>
      <div class="card-body">
        @if($recentBans->count() > 0)
          @foreach($recentBans as $user)
          <div class="d-flex align-items-center mb-3">
            <div class="avatar avatar-sm me-3">
              <span class="avatar-initial rounded-circle bg-label-danger">
                {{ strtoupper(substr($user->name, 0, 1)) }}
              </span>
            </div>
            <div class="flex-grow-1">
              <h6 class="mb-0">{{ $user->name }}</h6>
              <small class="text-muted">{{ $user->email }}</small>
              @if($user->ban_reason)
                <br><small class="text-danger">Reason: {{ $user->ban_reason }}</small>
              @endif
            </div>
            <div class="text-end">
              <small class="text-muted">{{ $user->updated_at->diffForHumans() }}</small>
            </div>
          </div>
          @endforeach
        @else
          <div class="text-center py-4">
            <i class="icon-base ti tabler-user-x icon-48px text-muted mb-2"></i>
            <p class="text-muted mb-0">No recent bans</p>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Ban User Modal -->
<div class="modal fade" id="banUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ban User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="banUserForm" method="POST">
        @csrf
        <div class="modal-body">
          <p>Are you sure you want to ban <strong id="banUserName"></strong>?</p>
          <div class="mb-3">
            <label for="ban_reason" class="form-label">Ban Reason</label>
            <textarea class="form-control" id="ban_reason" name="reason" rows="3" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Ban User</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('vendor-script')
@endsection

@section('page-script')
<script>
function banUser(userId, userName) {
    document.getElementById('banUserName').textContent = userName;
    document.getElementById('banUserForm').action = '{{ route("admin.monitoring.ban", ":user") }}'.replace(':user', userId);
    new bootstrap.Modal(document.getElementById('banUserModal')).show();
}
</script>
@endsection