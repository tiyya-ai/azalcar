<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Make;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MakeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $makes = Make::all();
        return view('admin.makes.index', compact('makes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.makes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:makes,name',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'nhtsa_id' => 'nullable|integer',
            'is_active' => 'boolean'
        ]);

        $slug = Str::slug($request->name);
        $imagePath = null;

        if ($request->hasFile('image')) {
             $imagePath = $request->file('image')->store('makes', 'public');
        }

        Make::create([
            'name' => $request->name,
            'slug' => $slug,
            'image' => $imagePath,
            'nhtsa_id' => $request->nhtsa_id,
            'is_active' => $request->has('is_active') ? $request->is_active : true, // Default true if not sent but form toggle sends 0/1 usually
        ]);

        return redirect()->route('admin.makes.index')->with('success', 'Make created successfully.');
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
    public function edit(Make $make)
    {
         return view('admin.makes.edit', compact('make'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Make $make)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:makes,name,' . $make->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'nhtsa_id' => 'nullable|integer',
            'is_active' => 'boolean'
        ]);

        $slug = Str::slug($request->name);
        $data = [
            'name' => $request->name,
            'slug' => $slug,
            'nhtsa_id' => $request->nhtsa_id,
            'is_active' => $request->has('is_active') ? $request->is_active : false, // Checkbox behavior
        ];

        if ($request->hasFile('image')) {
             $data['image'] = $request->file('image')->store('makes', 'public');
        }

        $make->update($data);

        return redirect()->route('admin.makes.index')->with('success', 'Make updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Make $make)
    {
        $make->delete();
        return redirect()->route('admin.makes.index')->with('success', 'Make deleted successfully.');
    }
}
