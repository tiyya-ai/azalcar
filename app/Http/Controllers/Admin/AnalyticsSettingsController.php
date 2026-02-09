<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class AnalyticsSettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings.analytics', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'google_analytics_id' => 'nullable|string|max:20',
            'facebook_pixel_id' => 'nullable|string|max:20',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value, 'string');
        }

        return back()->with('success', 'Analytics settings updated successfully.');
    }
}