@extends('layouts/contentNavbarLayout')

@section('title', 'Add Vehicle Model')

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">Attributes / Vehicle Models /</span> Add
</h4>

<div class="row">
  <div class="col-xxl">
    <div class="card mb-4">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">Add New Vehicle Model</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.vehicle_models.store') }}" method="POST">
          @csrf
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label" for="model-make">Make</label>
            <div class="col-sm-10">
              <select id="model-make" name="make_id" class="form-select" required>
                <option value="">Select Make</option>
                @foreach($makes as $make)
                    <option value="{{ $make->id }}">{{ $make->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label" for="model-name">Name</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="model-name" name="name" placeholder="Corolla, Civic, etc." required />
            </div>
          </div>
          <div class="row justify-content-end">
            <div class="col-sm-10">
              <button type="submit" class="btn btn-primary">Save</button>
              <a href="{{ route('admin.vehicle_models.index') }}" class="btn btn-label-secondary">Cancel</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
