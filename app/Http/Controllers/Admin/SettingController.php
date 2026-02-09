<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        // Get all settings grouped by category
        $settings = [
            'general' => [
                'site_name' => Setting::get('site_name', 'Classified Cars'),
                'site_description' => Setting::get('site_description', 'Buy and sell cars online'),
                'contact_email' => Setting::get('contact_email', 'info@azalcars.com'),
                'contact_phone' => Setting::get('contact_phone', '+1 234 567 8900'),
            ],
            'seo' => [
                'meta_title' => Setting::get('meta_title', 'Classified Cars - Buy & Sell Cars'),
                'meta_description' => Setting::get('meta_description', 'Find your perfect car or sell your vehicle'),
                'meta_keywords' => Setting::get('meta_keywords', 'cars, buy cars, sell cars, used cars'),
            ],
            'payment' => [
                'stripe_enabled' => Setting::get('stripe_enabled', true),
                'paypal_enabled' => Setting::get('paypal_enabled', false),
                'currency' => Setting::get('currency', 'USD'),
                'currency_symbol' => Setting::get('currency_symbol', '$'),
            ],
            'features' => [
                'enable_reviews' => Setting::get('enable_reviews', true),
                'enable_messaging' => Setting::get('enable_messaging', true),
                'enable_favorites' => Setting::get('enable_favorites', true),
                'auto_approve_listings' => Setting::get('auto_approve_listings', false),
            ],
            'limits' => [
                'max_images_per_listing' => Setting::get('max_images_per_listing', 10),
                'max_listings_per_user' => Setting::get('max_listings_per_user', 50),
                'listing_expiry_days' => Setting::get('listing_expiry_days', 30),
            ],
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
        ]);

        foreach ($request->settings as $key => $value) {
            // Determine type
            $type = 'string';
            if (is_bool($value) || $value === 'true' || $value === 'false') {
                $type = 'boolean';
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            } elseif (is_numeric($value)) {
                $type = 'number';
            }

            Setting::set($key, $value, $type);
        }

        return back()->with('success', 'Settings updated successfully.');
    }
}
