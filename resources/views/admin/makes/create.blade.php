@extends('layouts/contentNavbarLayout')

@section('title', 'Add Make')

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">Attributes / Makes /</span> Add
</h4>

<div class="row">
  <div class="col-xxl">
    <div class="card mb-4">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">Add New Make</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.makes.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label" for="make-name">Name</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="make-name" name="name" placeholder="Toyota" required />
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label" for="make-image">Logo/Image</label>
            <div class="col-sm-10">
              <input class="form-control" type="file" id="make-image" name="image" />
            </div>
          </div>
          <div class="row justify-content-end">
            <div class="col-sm-10">
              <button type="submit" class="btn btn-primary">Save</button>
              <a href="{{ route('admin.makes.index') }}" class="btn btn-label-secondary">Cancel</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
