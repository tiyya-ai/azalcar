@extends('layouts/contentNavbarLayout')

@section('title', 'Suspicious Activity')

@section('vendor-style')
@endsection

@section('page-style')
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0">Suspicious User Activity</h5>
        <a href="{{ route('admin.monitoring.index') }}" class="btn btn-secondary btn-sm">
          <i class="icon-base ti tabler-arrow-left me-1"></i>Back to Dashboard
        </a>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>User</th>
                <th>Email</th>
                <th>Leads (24h)</th>
                <th>Status</th>
                <th>Joined</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($suspiciousUsers as $user)
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
                    </div>
                  </div>
                </td>
                <td>{{ $user->email }}</td>
                <td>
                  <span class="badge bg-label-danger">{{ $user->listings_count }}</span>
                </td>
                <td>
                  <span class="badge bg-label-{{ $user->status == 'active' ? 'success' : 'danger' }}">
                    {{ ucfirst($user->status) }}
                  </span>
                </td>
                <td>{{ $user->created_at->format('M d, Y') }}</td>
                <td>
                  <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                      Actions
                    </button>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="{{ route('admin.users.show', $user) }}">
                        <i class="icon-base ti tabler-eye me-2"></i>View Details
                      </a></li>
                      @if($user->status != 'banned')
                      <li><a class="dropdown-item text-danger" href="#" onclick="banUser({{ $user->id }}, '{{ $user->name }}')">
                        <i class="icon-base ti tabler-ban me-2"></i>Ban User
                      </a></li>
                      @endif
                    </ul>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center py-4">
                  <i class="icon-base ti tabler-shield-check icon-48px text-success mb-2"></i>
                  <p class="text-muted mb-0">No suspicious activity detected</p>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if($suspiciousUsers->hasPages())
        <div class="d-flex justify-content-center mt-4">
          {{ $suspiciousUsers->links() }}
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