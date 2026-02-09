<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class EmailSettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings.email', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer|min:1|max:65535',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_encryption' => 'nullable|in:tls,ssl',
        ]);

        foreach ($validated as $key => $value) {
            $type = $key === 'smtp_port' ? 'number' : 'string';
            Setting::set($key, $value, $type);
        }

        return back()->with('success', 'Email settings updated successfully.');
    }

    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            \Illuminate\Support\Facades\Mail::raw('This is a test email from your azalcars admin panel.', function ($message) use ($request) {
                $message->to($request->test_email)
                         ->subject('Test Email from azalcars Admin');
            });

            return back()->with('success', 'Test email sent successfully to ' . $request->test_email);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }
}