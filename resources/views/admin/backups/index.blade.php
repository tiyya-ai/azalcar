@extends('layouts/contentNavbarLayout')

@section('title', 'Backup Management')

@section('vendor-style')
@endsection

@section('page-style')
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Backup Management</h5>
        <div>
          <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#createBackupModal">
            <i class="icon-base ti tabler-plus me-1"></i>
            Create Backup
          </button>
        </div>
      </div>

      <div class="card-body">
        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Filename</th>
                <th>Size</th>
                <th>Created</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($backups as $backup)
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <i class="icon-base ti tabler-file-zip me-2 text-warning"></i>
                    <span>{{ $backup['name'] }}</span>
                  </div>
                </td>
                <td>{{ number_format($backup['size'] / 1024 / 1024, 2) }} MB</td>
                <td>{{ date('M d, Y H:i', $backup['created_at']) }}</td>
                <td>
                  <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                      Actions
                    </button>
                    <ul class="dropdown-menu">
                      <li>
                        <a class="dropdown-item" href="{{ $backup['url'] }}" target="_blank">
                          <i class="icon-base ti tabler-download me-2"></i>
                          Download
                        </a>
                      </li>
                      <li>
                        <hr class="dropdown-divider">
                      </li>
                      <li>
                        <button class="dropdown-item text-danger" onclick="deleteBackup('{{ $backup['name'] }}')">
                          <i class="icon-base ti tabler-trash me-2"></i>
                          Delete
                        </button>
                      </li>
                    </ul>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="4" class="text-center py-4">
                  <i class="icon-base ti tabler-file-zip text-muted" style="font-size: 48px;"></i>
                  <p class="text-muted mt-2 mb-0">No backups found</p>
                  <small class="text-muted">Create your first backup to get started</small>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Create Backup Modal -->
<div class="modal fade" id="createBackupModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create Backup</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('admin.backups.create') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label for="backup_type" class="form-label">Backup Type</label>
            <select class="form-select" id="backup_type" name="type" required>
              <option value="database">Database Only</option>
              <option value="files">Files Only</option>
              <option value="full">Full Backup (Database + Files)</option>
            </select>
          </div>
          <div class="alert alert-info">
            <i class="icon-base ti tabler-info-circle me-2"></i>
            <strong>Note:</strong> Full backups may take longer to create and will be larger in size.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">
            <i class="icon-base ti tabler-plus me-1"></i>
            Create Backup
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete Backup</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this backup? This action cannot be undone.</p>
        <p class="text-danger mb-0"><strong>This will permanently delete the backup file.</strong></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form id="deleteForm" method="POST" style="display: inline;">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">
            <i class="icon-base ti tabler-trash me-1"></i>
            Delete Backup
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script>
function deleteBackup(filename) {
    document.getElementById('deleteForm').action = '{{ route("admin.backups.index") }}/' + filename;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endsection
