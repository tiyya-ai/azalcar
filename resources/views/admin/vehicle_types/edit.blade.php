@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Vehicle Type')

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">Attributes / Vehicle Types /</span> Edit
</h4>

<div class="row">
  <div class="col-xxl">
    <div class="card mb-4">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">Edit Vehicle Type: {{ $vehicleType->name }}</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.vehicle_types.update', $vehicleType->id) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label" for="type-name">Name</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="type-name" name="name" value="{{ $vehicleType->name }}" required />
            </div>
          </div>
          <div class="row justify-content-end">
            <div class="col-sm-10">
              <button type="submit" class="btn btn-primary">Update</button>
              <a href="{{ route('admin.vehicle_types.index') }}" class="btn btn-label-secondary">Cancel</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
