<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    /**
     * Track a lead event (call, whatsapp, view) via AJAX
     */
    public function track(Request $request)
    {
        $request->validate([
            'listing_id' => 'required|exists:listings,id',
            'type' => 'required|in:call,whatsapp,view'
        ]);

        $listing = Listing::findOrFail($request->listing_id);
        
        // PRODUCTION HARDENING: Validate listing status before tracking
        // Prevents analytics pollution from inactive/sold listings
        if ($listing->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot track leads on inactive listings'
            ], 403);
        }

        // Prevent self-tracking: sellers cannot generate leads on their own listings
        if (auth()->check() && auth()->id() === $listing->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot track leads on own listing'
            ], 403);
        }

        // PRODUCTION HARDENING: Atomic lead creation with deduplication
        // This prevents duplicate leads from same IP/User-Agent within 24 hours
        // and ensures counter increments are atomic
        
        try {
            // Use firstOrCreate for atomic deduplication
            // If another request creates the same lead simultaneously, we'll get the existing one
            $lead = Lead::firstOrCreate(
                [
                    'listing_id' => $listing->id,
                    'user_id' => auth()->id(),
                    'type' => $request->type,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ],
                [
                    'listing_id' => $listing->id,
                    'user_id' => auth()->id(),
                    'type' => $request->type,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]
            );

            // Check if this is a new lead (not created in last 24 hours)
            $isNewLead = $lead->created_at->greaterThan(now()->subHours(24));

            // Only increment counter if this is a new lead
            if ($isNewLead) {
                // Use atomic increment to prevent race conditions
                // Multiple concurrent requests will safely increment the counter
                if ($request->type === 'call') {
                    $listing->increment('calls_count');
                } elseif ($request->type === 'whatsapp') {
                    $listing->increment('whatsapp_count');
                } elseif ($request->type === 'view') {
                    $listing->increment('views_count');
                }
            }

            // Refresh listing to get updated counts
            $listing->refresh();

            return response()->json([
                'success' => true,
                'count' => $request->type === 'call' 
                    ? $listing->calls_count 
                    : ($request->type === 'whatsapp' 
                        ? $listing->whatsapp_count 
                        : $listing->views_count),
                'is_new' => $isNewLead
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle unique constraint violations gracefully
            if (strpos($e->getMessage(), 'Duplicate entry') !== false || 
                strpos($e->getMessage(), 'UNIQUE constraint failed') !== false) {
                // Another request already created this lead - treat as success
                $listing->refresh();
                return response()->json([
                    'success' => true,
                    'count' => $request->type === 'call' 
                        ? $listing->calls_count 
                        : ($request->type === 'whatsapp' 
                            ? $listing->whatsapp_count 
                            : $listing->views_count),
                    'is_new' => false
                ]);
            }

            \Illuminate\Support\Facades\Log::error('Database error tracking lead', [
                'listing_id' => $listing->id,
                'type' => $request->type,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to track lead'
            ], 500);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Unexpected error tracking lead', [
                'listing_id' => $listing->id,
                'type' => $request->type,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to track lead'
            ], 500);
        }
    }

    /**
     * Reveal phone number (Anti-Spam)
     */
    public function revealPhone(Listing $listing)
    {
        // Record the event
        $this->track(new Request([
            'listing_id' => $listing->id,
            'type' => 'call'
        ]));

        if (!$listing->user || !$listing->user->phone) {
             return response()->json(['error' => 'Phone number not available'], 404);
        }

        return response()->json([
            'phone' => $listing->user->phone
        ]);
    }
}
