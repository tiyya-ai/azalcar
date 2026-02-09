<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleModel;
use App\Models\Make;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VehicleModelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $models = VehicleModel::with('make')->get();
        return view('admin.vehicle_models.index', compact('models'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $makes = Make::all();
        return view('admin.vehicle_models.create', compact('makes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'make_id' => 'required|exists:makes,id',
            'nhtsa_id' => 'nullable|integer',
            'is_active' => 'boolean'
        ]);

        $slug = Str::slug($request->name);

        VehicleModel::create([
            'name' => $request->name,
            'slug' => $slug,
            'make_id' => $request->make_id,
            'nhtsa_id' => $request->nhtsa_id,
            'is_active' => $request->has('is_active') ? $request->is_active : true, // Default true
        ]);

        return redirect()->route('admin.vehicle_models.index')->with('success', 'Vehicle Model created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VehicleModel $vehicleModel)
    {
         $makes = Make::all();
         return view('admin.vehicle_models.edit', compact('vehicleModel', 'makes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VehicleModel $vehicleModel)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'make_id' => 'required|exists:makes,id',
            'nhtsa_id' => 'nullable|integer',
            'is_active' => 'boolean'
        ]);

        $slug = Str::slug($request->name);

        $vehicleModel->update([
            'name' => $request->name,
            'slug' => $slug,
            'make_id' => $request->make_id,
            'nhtsa_id' => $request->nhtsa_id,
            'is_active' => $request->has('is_active') ? $request->is_active : false,
        ]);

        return redirect()->route('admin.vehicle_models.index')->with('success', 'Vehicle Model updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VehicleModel $vehicleModel)
    {
        $vehicleModel->delete();
        return redirect()->route('admin.vehicle_models.index')->with('success', 'Vehicle Model deleted successfully.');
    }
}
