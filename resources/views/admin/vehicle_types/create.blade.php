@extends('layouts/contentNavbarLayout')

@section('title', 'Add Vehicle Type')

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">Attributes / Vehicle Types /</span> Add
</h4>

<div class="row">
  <div class="col-xxl">
    <div class="card mb-4">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">Add New Vehicle Type</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.vehicle_types.store') }}" method="POST">
          @csrf
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label" for="type-name">Name</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="type-name" name="name" placeholder="SUV, Sedan, etc." required />
            </div>
          </div>
          <div class="row justify-content-end">
            <div class="col-sm-10">
              <button type="submit" class="btn btn-primary">Save</button>
              <a href="{{ route('admin.vehicle_types.index') }}" class="btn btn-label-secondary">Cancel</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
