<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VendorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('home')->with('open_login', true)->with('error', 'Please login to continue.');
        }
        
        // Allow both vendors and admins to access vendor routes
        if (!in_array(auth()->user()->role, ['vendor', 'admin'])) {
            abort(403, 'This area is only accessible to sellers.');
        }

        return $next($request);
    }
}
