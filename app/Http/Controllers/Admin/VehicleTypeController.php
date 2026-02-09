<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VehicleTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $types = VehicleType::all();
        return view('admin.vehicle_types.index', compact('types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.vehicle_types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:vehicle_types,name',
        ]);

        $slug = Str::slug($request->name);

        VehicleType::create([
            'name' => $request->name,
            'slug' => $slug,
        ]);

        return redirect()->route('admin.vehicle_types.index')->with('success', 'Vehicle Type created successfully.');
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
    public function edit(VehicleType $vehicleType)
    {
         return view('admin.vehicle_types.edit', compact('vehicleType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VehicleType $vehicleType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:vehicle_types,name,' . $vehicleType->id,
        ]);

        $slug = Str::slug($request->name);

        $vehicleType->update([
            'name' => $request->name,
            'slug' => $slug,
        ]);

        return redirect()->route('admin.vehicle_types.index')->with('success', 'Vehicle Type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VehicleType $vehicleType)
    {
        $vehicleType->delete();
        return redirect()->route('admin.vehicle_types.index')->with('success', 'Vehicle Type deleted successfully.');
    }
}
