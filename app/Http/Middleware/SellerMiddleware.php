<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SellerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to access this area.');
        }

        $user = auth()->user();

        // Check if user is a vendor or an approved seller
        if ($user->role === 'vendor' || $user->seller_status === 'approved' || $user->role === 'admin') {
            return $next($request);
        }

        // If specific seller page but not approved yet
        if ($user->seller_status === 'pending') {
            return redirect()->route('dashboard')->with('info', 'Your seller application is pending approval.');
        }

        return redirect()->route('dashboard')->with('error', 'You must be a registered seller to access this area.');
    }
}
