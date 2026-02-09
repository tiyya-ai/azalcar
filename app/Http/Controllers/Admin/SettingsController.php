<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'contact_email' => 'required|email',
            'contact_phone' => 'nullable|string|max:20',
            'currency' => 'required|string|size:3',
            'timezone' => 'required|string',
            'maintenance_mode' => 'boolean',
            'allow_registration' => 'boolean',
            'require_email_verification' => 'boolean',
            'max_images_per_listing' => 'required|integer|min:1|max:20',
            'listing_expiry_days' => 'required|integer|min:7|max:365',
            'commission_percentage' => 'required|numeric|min:0|max:50',
            'google_analytics_id' => 'nullable|string|max:20',
            'facebook_pixel_id' => 'nullable|string|max:20',
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer|min:1|max:65535',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_encryption' => 'nullable|in:tls,ssl',
            'backup_frequency' => 'required|in:daily,weekly,monthly',
            'max_file_size' => 'required|integer|min:1|max:50', // MB
        ]);

        foreach ($validated as $key => $value) {
            $type = $this->getSettingType($key);
            Setting::set($key, $value, $type);
        }

        return back()->with('success', 'Settings updated successfully.');
    }

    /**
     * Display the payment settings page.
     */
    public function payments()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings.payments', compact('settings'));
    }

    /**
     * Update payment settings.
     */
    public function updatePayments(Request $request)
    {
        $validated = $request->validate([
            'payment_gateway_stripe' => 'boolean',
            'stripe_publishable_key' => 'nullable|string|max:255',
            'stripe_secret_key' => 'nullable|string|max:255',
            'stripe_webhook_secret' => 'nullable|string|max:255',
            'payment_gateway_paypal' => 'boolean',
            'paypal_client_id' => 'nullable|string|max:255',
            'paypal_client_secret' => 'nullable|string|max:255',
            'paypal_mode' => 'nullable|in:sandbox,live',
            'payment_gateway_bank_transfer' => 'boolean',
            'bank_transfer_instructions' => 'nullable|string|max:1000',
            'payment_gateway_cash_on_delivery' => 'boolean',
            'cash_on_delivery_instructions' => 'nullable|string|max:1000',
        ]);

        foreach ($validated as $key => $value) {
            $type = $this->getSettingType($key);
            Setting::set($key, $value, $type);
        }

        return back()->with('success', 'Payment settings updated successfully.');
    }

    /**
     * Get the appropriate type for a setting key.
     */
    private function getSettingType($key)
    {
        $booleanFields = [
            'maintenance_mode',
            'allow_registration',
            'require_email_verification',
            'payment_gateway_stripe',
            'payment_gateway_paypal',
            'payment_gateway_bank_transfer',
            'payment_gateway_cash_on_delivery'
        ];

        $numberFields = [
            'max_images_per_listing',
            'listing_expiry_days',
            'commission_percentage',
            'smtp_port',
            'max_file_size'
        ];

        if (in_array($key, $booleanFields)) {
            return 'boolean';
        }

        if (in_array($key, $numberFields)) {
            return 'number';
        }

        return 'string';
    }

    /**
     * Clear application cache.
     */
    public function clearCache()
    {
        \Illuminate\Support\Facades\Cache::flush();
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return back()->with('success', 'Cache cleared successfully.');
    }

    /**
     * Get system information.
     */
    public function systemInfo()
    {
        $info = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database_connection' => config('database.default'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'timezone' => config('app.timezone'),
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug') ? 'Enabled' : 'Disabled',
            'maintenance_mode' => app()->isDownForMaintenance() ? 'Active' : 'Inactive',
        ];

        return view('admin.settings.system-info', compact('info'));
    }
}