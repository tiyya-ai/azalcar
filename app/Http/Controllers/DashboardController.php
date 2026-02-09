<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get paginated listings by status
        $activeListings = Listing::where('user_id', $user->id)
            ->where('status', 'active')
            ->with(['make', 'vehicleModel', 'vehicleType'])
            ->latest()
            ->paginate(10);

        $archivedListings = Listing::where('user_id', $user->id)
            ->where('status', 'archived')
            ->with(['make', 'vehicleModel', 'vehicleType'])
            ->latest()
            ->paginate(10);

        $draftListings = Listing::where('user_id', $user->id)
            ->where('status', 'pending')
            ->with(['make', 'vehicleModel', 'vehicleType'])
            ->latest()
            ->paginate(10);

        // Aggregate Statistics for User Dashboard
        $allListings = Listing::where('user_id', $user->id)->get();
        $stats = [
            'total_listings' => $allListings->count(),
            'active_listings' => $allListings->where('status', 'active')->count(),
            'total_views' => $allListings->sum('views_count'),
            'total_leads' => $allListings->sum('calls_count') + $allListings->sum('whatsapp_count'),
            'unread_messages' => Message::where('receiver_id', $user->id)->where('is_read', false)->count(),
        ];

        return view('dashboard.index', compact('activeListings', 'archivedListings', 'draftListings', 'user', 'stats'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only('name', 'email', 'phone');

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $user->avatar));
            }
            
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = '/storage/' . $path;
        }

        $user->update($data);

        return back()->with('success', 'Profile updated successfully!');
    }
}
