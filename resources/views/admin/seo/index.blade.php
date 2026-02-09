@extends('layouts/contentNavbarLayout')

@section('title', 'Global SEO Settings - Admin')

@section('content')
<div class="card mb-6">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Add New SEO Route</h5>
    <small class="text-muted">Set specific meta tags for any URL path</small>
  </div>
  <div class="card-body">
    <form action="{{ route('admin.seo.store') }}" method="POST">
      @csrf
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label" for="path">Path (e.g. /brands)</label>
          <input type="text" id="path" name="path" class="form-control" placeholder="/example-page" required />
        </div>
        <div class="col-md-3">
          <label class="form-label" for="meta_title">Meta Title</label>
          <input type="text" id="meta_title" name="meta_title" class="form-control" placeholder="Page Title" required />
        </div>
        <div class="col-md-4">
          <label class="form-label" for="meta_description">Meta Description</label>
          <input type="text" id="meta_description" name="meta_description" class="form-control" placeholder="Brief summary for search engines" required />
        </div>
        <div class="col-md-2 d-flex align-items-end">
          <button type="submit" class="btn btn-primary w-100">Save</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <h5 class="card-header">Managed Routes</h5>
  <div class="table-responsive text-nowrap">
    <table class="table">
      <thead>
        <tr>
          <th>Path</th>
          <th>Meta Title</th>
          <th>Description</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody class="table-border-bottom-0">
        @foreach($seos as $seo)
        <tr>
          <td><span class="fw-medium">{{ $seo->path }}</span></td>
          <td>{{ $seo->meta_title }}</td>
          <td>{{ Str::limit($seo->meta_description, 50) }}</td>
          <td>
            <div class="dropdown">
              <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="icon-base ti tabler-dots-vertical"></i></button>
              <div class="dropdown-menu">
                <form action="{{ route('admin.seo.destroy', $seo) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="dropdown-item"><i class="icon-base ti tabler-trash me-1"></i> Delete</button>
                </form>
              </div>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
