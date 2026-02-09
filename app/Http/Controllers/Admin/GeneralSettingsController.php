<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class GeneralSettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings.general', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'contact_email' => 'required|email',
            'contact_phone' => 'nullable|string|max:20',
            'currency' => 'required|string|size:3',
            'timezone' => 'required|string',
        ]);

        // Handle boolean fields
        $validated['maintenance_mode'] = $request->boolean('maintenance_mode');
        $validated['allow_registration'] = $request->boolean('allow_registration');
        $validated['require_email_verification'] = $request->boolean('require_email_verification');

        foreach ($validated as $key => $value) {
            $type = $this->getSettingType($key);
            Setting::set($key, $value, $type);
        }

        return back()->with('success', 'General settings updated successfully.');
    }

    private function getSettingType($key)
    {
        $booleanFields = ['maintenance_mode', 'allow_registration', 'require_email_verification'];
        return in_array($key, $booleanFields) ? 'boolean' : 'string';
    }
}