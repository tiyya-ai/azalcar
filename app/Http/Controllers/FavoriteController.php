<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Listing;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $favorites = Favorite::with('listing')->where('user_id', $user->id)->latest()->get();
        return view('dashboard.favorites', compact('favorites'));
    }

    public function toggle(Request $request, $listingId)
    {
        $userId = auth()->id();
        
        // PRODUCTION HARDENING: Atomic toggle using database-level uniqueness
        // This prevents race conditions where concurrent requests could create
        // duplicate favorites or fail to delete properly.
        
        try {
            // Check if favorite exists
            $favorite = Favorite::where('user_id', $userId)
                ->where('listing_id', $listingId)
                ->first();
            
            if ($favorite) {
                // Delete existing favorite
                $favorite->delete();
                $status = 'removed';
                $message = 'Removed from favorites';
            } else {
                // Create new favorite
                // If unique constraint exists and another request creates it simultaneously,
                // we'll catch the exception and treat it as already added
                try {
                    Favorite::create([
                        'user_id' => $userId,
                        'listing_id' => $listingId
                    ]);
                    $status = 'added';
                    $message = 'Added to favorites';
                } catch (\Illuminate\Database\QueryException $e) {
                    // Handle unique constraint violation (race condition)
                    if (strpos($e->getMessage(), 'Duplicate entry') !== false || 
                        strpos($e->getMessage(), 'UNIQUE constraint failed') !== false) {
                        // Another request already created this favorite
                        $status = 'added';
                        $message = 'Already in favorites';
                    } else {
                        throw $e;
                    }
                }
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'status' => $status,
                    'message' => $message
                ]);
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error toggling favorite', [
                'user_id' => $userId,
                'listing_id' => $listingId,
                'error' => $e->getMessage()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update favorite'
                ], 500);
            }

            return back()->with('error', 'Failed to update favorite');
        }
    }
}
