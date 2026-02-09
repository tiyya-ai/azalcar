<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        // Defensive: check if core tables exist
        if (!Schema::hasTable('makes') || !Schema::hasTable('listings') || !Schema::hasTable('users')) {
            $stats = [
                'makes' => 0,
                'models' => 0,
                'types' => 0,
                'listings' => 0,
                'active_listings' => 0,
                'pending_listings' => 0,
                'users' => 0,
                'active_users' => 0,
                'sellers' => 0,
                'pending_sellers' => 0,
                'revenue' => 0,
                'total_leads' => 0,
                'today_leads' => 0,
                'total_views' => 0,
                'today_listings' => 0,
                'today_users' => 0,
                'today_revenue' => 0,
            ];
            $recentListings = collect();
            $topListings = collect();
            $recentUsers = collect();
            $revenueTrend = collect();
            $userGrowth = collect();
            return view('admin.dashboard', compact(
                'stats',
                'recentListings',
                'topListings',
                'recentUsers',
                'revenueTrend',
                'userGrowth'
            ));
        }

        // Basic counts
        try {
            $stats = [
                'makes' => \App\Models\Make::count(),
                'models' => \App\Models\VehicleModel::count(),
                'types' => \App\Models\VehicleType::count(),
                'listings' => \App\Models\Listing::count(),
                'active_listings' => \App\Models\Listing::where('status', 'active')->count(),
                'pending_listings' => \App\Models\Listing::where('status', 'pending')->count(),
                'users' => \App\Models\User::count(),
                'active_users' => \App\Models\User::where('status', 'active')->count(),
                'sellers' => \App\Models\User::where('role', 'vendor')->orWhere('seller_status', 'approved')->count(),
                'pending_sellers' => \App\Models\User::where('seller_status', 'pending')->count(),
                'revenue' => \App\Models\Transaction::where('type', 'debit')->sum('amount'),
                'total_leads' => \App\Models\Lead::count(),
                'today_leads' => \App\Models\Lead::whereDate('created_at', today())->count(),
                'total_views' => \App\Models\Listing::sum('views_count') ?? 0,
                'today_listings' => \App\Models\Listing::whereDate('created_at', today())->count(),
                'today_users' => \App\Models\User::whereDate('created_at', today())->count(),
                'today_revenue' => \App\Models\Transaction::where('type', 'debit')
                                                           ->whereDate('created_at', today())
                                                           ->sum('amount'),
            ];
        } catch (\Exception $e) {
            // If database error, return empty stats
            $stats = [
                'makes' => 0,
                'models' => 0,
                'types' => 0,
                'listings' => 0,
                'active_listings' => 0,
                'pending_listings' => 0,
                'users' => 0,
                'active_users' => 0,
                'sellers' => 0,
                'pending_sellers' => 0,
                'revenue' => 0,
                'total_leads' => 0,
                'today_leads' => 0,
                'total_views' => 0,
                'today_listings' => 0,
                'today_users' => 0,
                'today_revenue' => 0,
            ];
        }

        try {
            // Recent listings with more details
            $recentListings = \App\Models\Listing::with('user')
                                                 ->latest()
                                                 ->take(5)
                                                 ->get();

            // Top performing listings
            $topListings = \App\Models\Listing::where('status', 'active')
                                              ->orderBy('views_count', 'desc')
                                              ->take(5)
                                              ->get();

            // Recent user registrations
            $recentUsers = \App\Models\User::latest()->take(5)->get();

            // Revenue trend (last 7 days)
            $revenueTrend = \App\Models\Transaction::selectRaw('DATE(created_at) as date, SUM(amount) as total')
                                                   ->where('type', 'debit')
                                                   ->where('created_at', '>=', now()->subDays(7))
                                                   ->groupBy('date')
                                                   ->orderBy('date')
                                                   ->get()
                                                   ->pluck('total', 'date');

            // User growth (last 7 days)
            $userGrowth = \App\Models\User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                                          ->where('created_at', '>=', now()->subDays(7))
                                          ->groupBy('date')
                                          ->orderBy('date')
                                          ->get()
                                          ->pluck('count', 'date');
        } catch (\Exception $e) {
            $recentListings = collect();
            $topListings = collect();
            $recentUsers = collect();
            $revenueTrend = collect();
            $userGrowth = collect();
        }

        return view('admin.dashboard', compact(
            'stats',
            'recentListings',
            'topListings',
            'recentUsers',
            'revenueTrend',
            'userGrowth'
        ));
    }
}
