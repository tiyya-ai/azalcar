<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index()
    {
        // Suspicious activity: Users with high lead counts in short time
        $suspiciousUsers = User::withCount(['listings' => function ($query) {
            $query->whereHas('leads', function ($q) {
                $q->where('created_at', '>=', now()->subHours(24));
            });
        }])
        ->having('listings_count', '>', 10) // Threshold for suspicious
        ->orderBy('listings_count', 'desc')
        ->take(10)
        ->get();

        // Lead spikes: High lead counts per hour in last 24 hours
        $leadSpikes = Lead::selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d %H:00:00") as hour, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(1))
            ->groupBy('hour')
            ->having('count', '>', 50) // Threshold for spike
            ->orderBy('count', 'desc')
            ->take(10)
            ->get();

        // Users with multiple leads from same IP
        $ipAbuse = Lead::selectRaw('ip_address, COUNT(DISTINCT user_id) as user_count, COUNT(*) as lead_count')
            ->where('ip_address', '!=', '')
            ->whereNotNull('ip_address')
            ->groupBy('ip_address')
            ->having('lead_count', '>', 20) // Threshold
            ->orderBy('lead_count', 'desc')
            ->take(10)
            ->get();

        // Recent banned users
        $recentBans = User::where('status', 'banned')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.monitoring.index', compact(
            'suspiciousUsers',
            'leadSpikes',
            'ipAbuse',
            'recentBans'
        ));
    }

    public function suspiciousActivity()
    {
        $suspiciousUsers = User::with(['listings.leads' => function ($query) {
            $query->where('created_at', '>=', now()->subHours(24));
        }])
        ->withCount(['listings' => function ($query) {
            $query->whereHas('leads', function ($q) {
                $q->where('created_at', '>=', now()->subHours(24));
            });
        }])
        ->having('listings_count', '>', 5)
        ->orderBy('listings_count', 'desc')
        ->paginate(15);

        return view('admin.monitoring.suspicious', compact('suspiciousUsers'));
    }

    public function leadSpikes()
    {
        $spikes = Lead::selectRaw('DATE(created_at) as date, HOUR(created_at) as hour, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date', 'hour')
            ->having('count', '>', 20)
            ->orderBy('date', 'desc')
            ->orderBy('hour', 'desc')
            ->paginate(20);

        return view('admin.monitoring.spikes', compact('spikes'));
    }

    public function banUser(Request $request, User $user)
    {
        $request->validate([
            'reason' => 'required|string|max:255'
        ]);

        $user->update([
            'status' => 'banned',
            'ban_reason' => $request->reason
        ]);

        return back()->with('success', 'User has been banned.');
    }
}