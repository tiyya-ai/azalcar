<?php

namespace App\Http\Middleware;

use App\Models\AdsSeo;
use Illuminate\Support\Facades\Schema;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class GlobalSeoMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $path = '/' . ltrim($request->path(), '/');
        
        // If the table doesn't exist (e.g. during some test setups), skip SEO lookup
        if (!Schema::hasTable('ads_seo')) {
            return $next($request);
        }

        $seo = AdsSeo::where('path', $path)->first();
        
        if ($seo) {
            View::share('global_meta_title', $seo->meta_title);
            View::share('global_meta_description', $seo->meta_description);
            View::share('global_og_image', $seo->og_image);
        }

        return $next($request);
    }
}
