@extends('layouts/contentNavbarLayout')

@section('title', 'Create Package - Admin')

@section('content')
<h4 class="py-3 mb-4"><span class="text-muted fw-light">Monetization /</span> Create Package</h4>

<div class="row">
  <div class="col-md-6">
    <div class="card mb-4">
      <h5 class="card-header">New Package Details</h5>
      <div class="card-body">
        <form action="{{ route('admin.packages.store') }}" method="POST">
          @csrf
          
          <div class="mb-3">
            <label class="form-label" for="name">Package Name</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="e.g. Premium Plan" required />
          </div>

          <div class="mb-3">
            <label class="form-label" for="price">Price (₽)</label>
            <input type="number" class="form-control" id="price" name="price" placeholder="0.00" step="0.01" required />
          </div>

          <div class="mb-3">
            <label class="form-label" for="duration_days">Duration (Days)</label>
            <input type="number" class="form-control" id="duration_days" name="duration_days" placeholder="30" required />
          </div>

          <div class="mb-3">
            <label class="form-label" for="limit_images">Max Images Allowed</label>
            <input type="number" class="form-control" id="limit_images" name="limit_images" placeholder="10" required />
          </div>

          <div class="mb-3 d-flex gap-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1">
              <label class="form-check-label" for="is_featured">Featured Badge</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_top" id="is_top" value="1">
              <label class="form-check-label" for="is_top">Top Ranking</label>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label" for="description">Description (Features)</label>
            <textarea class="form-control" id="description" name="description" rows="3" placeholder="List key features..."></textarea>
          </div>

          <button type="submit" class="btn btn-primary">Create Package</button>
          <a href="{{ route('admin.packages.index') }}" class="btn btn-label-secondary">Cancel</a>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
