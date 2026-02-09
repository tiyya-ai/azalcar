<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Vite::useStyleTagAttributes(function (?string $src, string $url, ?array $chunk, ?array $manifest) {
            if ($src !== null) {
                return [
                    'class' => preg_match('/(resources\/assets\/vendor\/scss\/(rtl\/)?core)-?.*/i', $src)
                        ? 'template-customizer-core-css'
                        : (preg_match('/(resources\/assets\/vendor\/scss\/(rtl\/)?theme)-?.*/i', $src)
                            ? 'template-customizer-theme-css'
                            : '')
                ];
            }

            return [];
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useBootstrapFive();
        // Force HTTPS in production only (disabled for development)
        // if ($this->app->environment('production')) {
        //     \Illuminate\Support\Facades\URL::forceScheme('https');
        // }

        // Register Policies
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Listing::class, \App\Policies\ListingPolicy::class);

        // Rate Limiting
        \Illuminate\Support\Facades\RateLimiter::for('api', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('leads', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->ip()); // Strict limit for leads
        });

        \Illuminate\Support\Facades\RateLimiter::for('messages', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('login', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->ip()); // Limit login attempts
        });

        \Illuminate\Support\Facades\RateLimiter::for('admin_login', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(3)->by($request->ip()); // Very strict for admin
        });

        // Additional security headers
        \Illuminate\Support\Facades\Response::macro('secureHeaders', function () {
            return $this->header('X-Frame-Options', 'SAMEORIGIN')
                       ->header('X-Content-Type-Options', 'nosniff')
                       ->header('Referrer-Policy', 'strict-origin-when-cross-origin');
        });

        $verticalMenuJson = file_get_contents(base_path('resources/menu/verticalMenu.json'));
        $verticalMenuData = json_decode($verticalMenuJson);
        $horizontalMenuJson = file_get_contents(base_path('resources/menu/horizontalMenu.json'));
        $horizontalMenuData = json_decode($horizontalMenuJson);

        // Share all menuData to all the views
        View::share('menuData', [$verticalMenuData, $horizontalMenuData]);

        // Production safety checks: warn about insecure deployments (do not abort)
        if ($this->app->environment('production')) {
            $adminEmail = env('ADMIN_EMAIL');
            $adminPassword = env('ADMIN_PASSWORD');

            if ($adminEmail === 'admin@example.com' || $adminPassword === 'password' || config('app.debug') === true) {
                \Illuminate\Support\Facades\Log::warning('Potentially unsafe production configuration detected: default admin credentials or APP_DEBUG enabled. Please review environment settings.');
            }
        }
    }
}
