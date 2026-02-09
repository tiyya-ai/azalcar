<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\User;
use App\Models\Listing;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Store a new review for a seller
     */
    public function store(Request $request)
    {
        $request->validate([
            'seller_id' => 'required|exists:users,id',
            'listing_id' => 'nullable|exists:listings,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Prevent self-review
        if ($request->seller_id == auth()->id()) {
            return back()->with('error', 'You cannot review yourself.');
        }

        // PRODUCTION HARDENING: Atomic review creation with uniqueness constraint
        // This prevents duplicate reviews from same user to same seller
        // even under concurrent requests
        
        try {
            // Use firstOrCreate for atomic deduplication
            // If unique constraint exists and another request creates it simultaneously,
            // we'll catch the exception and treat it as already reviewed
            $review = Review::firstOrCreate(
                [
                    'user_id' => auth()->id(),
                    'seller_id' => $request->seller_id,
                ],
                [
                    'user_id' => auth()->id(),
                    'seller_id' => $request->seller_id,
                    'listing_id' => $request->listing_id,
                    'rating' => $request->rating,
                    'comment' => $request->comment,
                ]
            );

            // Check if this is a new review or existing
            if ($review->wasRecentlyCreated) {
                return back()->with('success', 'Thank you for your review!');
            } else {
                return back()->with('error', 'You have already reviewed this seller.');
            }
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle unique constraint violations gracefully
            if (strpos($e->getMessage(), 'Duplicate entry') !== false || 
                strpos($e->getMessage(), 'UNIQUE constraint failed') !== false) {
                \Illuminate\Support\Facades\Log::info('Duplicate review attempt blocked', [
                    'user_id' => auth()->id(),
                    'seller_id' => $request->seller_id
                ]);
                return back()->with('error', 'You have already reviewed this seller.');
            }

            \Illuminate\Support\Facades\Log::error('Database error creating review', [
                'user_id' => auth()->id(),
                'seller_id' => $request->seller_id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to create review. Please try again.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Unexpected error creating review', [
                'user_id' => auth()->id(),
                'seller_id' => $request->seller_id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to create review. Please try again.');
        }
    }

    /**
     * Show all reviews for a seller
     */
    public function sellerReviews($sellerId)
    {
        $seller = User::findOrFail($sellerId);
        $reviews = Review::where('seller_id', $sellerId)
                        ->with(['user', 'listing'])
                        ->latest()
                        ->paginate(10);

        return view('reviews.seller', compact('seller', 'reviews'));
    }
}
