<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/admin/login')->with('error', 'Please login to access the admin panel.');
        }
        
        if (!auth()->user()->is_admin) {
            return response('Unauthorized access to admin area.', 403);
        }

        // Allow access to 2FA setup routes without checking 2FA status (to prevent infinite redirects)
        if ($request->is('admin/2fa/*')) {
            return $next($request);
        }

        // Enforce admin 2FA only in production if explicitly enabled via env flag
        if (app()->environment('production') && env('REQUIRE_ADMIN_2FA') === 'true') {
            $user = auth()->user();
            if (empty($user->two_factor_enabled)) {
                // Soft enforcement: redirect admins to the 2FA setup page instead of hard aborting.
                // This prevents accidental lockouts during rollout while still prompting enrollment.
                return redirect('/admin/2fa/setup')->with('warning', 'Admin access requires two-factor authentication. Please enable 2FA for your account.');
            }
        }

        return $next($request);
    }
}
