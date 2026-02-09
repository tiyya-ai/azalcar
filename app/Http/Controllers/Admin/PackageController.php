<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::all();
        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.packages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'duration_days' => 'required|integer',
            'limit_images' => 'required|integer',
            'features' => 'nullable|string', // JSON or Comma separated
        ]);

        $data = $request->all();
        $data['is_featured'] = $request->has('is_featured');
        $data['is_top'] = $request->has('is_top');
        // Handle features if needed (assuming array or simple string)

        Package::create($data);

        return redirect()->route('admin.packages.index')->with('success', 'Package created successfully!');
    }

    public function edit(Package $package)
    {
        return view('admin.packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'duration_days' => 'required|integer',
            'listings_limit' => 'required|integer',
        ]);

        $data = $request->all();
        $data['is_featured'] = $request->has('is_featured');
        $data['is_top'] = $request->has('is_top');

        $package->update($data);

        return redirect()->route('admin.packages.index')->with('success', 'Package updated!');
    }

    public function destroy(Package $package)
    {
        $package->delete();
        return back()->with('success', 'Package deleted successfully.');
    }
}
