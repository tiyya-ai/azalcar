<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class BackupSettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings.backup', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'backup_frequency' => 'required|in:daily,weekly,monthly',
        ]);

        Setting::set('backup_frequency', $validated['backup_frequency'], 'string');

        return back()->with('success', 'Backup settings updated successfully.');
    }
}