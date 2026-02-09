<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class ListingSettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings.listings', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'max_images_per_listing' => 'required|integer|min:1|max:20',
            'listing_expiry_days' => 'required|integer|min:7|max:365',
            'commission_percentage' => 'required|numeric|min:0|max:50',
            'max_file_size' => 'required|integer|min:1|max:50',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value, 'number');
        }

        return back()->with('success', 'Listing settings updated successfully.');
    }
}