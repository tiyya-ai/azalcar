<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\TestDrive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestDriveController extends Controller
{
    public function store(Request $request, $listingSlug)
    {
        $listing = Listing::where('slug', $listingSlug)->firstOrFail();

        $request->validate([
            'buyer_name' => 'required|string|max:255',
            'buyer_email' => 'required|email',
            'buyer_phone' => 'required|string|max:20',
            'scheduled_at' => 'required|date_format:Y-m-d H:i|after:now',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $testDrive = TestDrive::create([
                'listing_id' => $listing->id,
                'buyer_id' => auth()->id(),
                'buyer_name' => $request->buyer_name,
                'buyer_email' => $request->buyer_email,
                'buyer_phone' => $request->buyer_phone,
                'scheduled_at' => $request->scheduled_at,
                'notes' => $request->notes,
                'status' => 'pending',
            ]);

            // Send email to seller
            try {
                Mail::send('emails.test-drive-request', [
                    'listing' => $listing,
                    'testDrive' => $testDrive,
                ], function ($message) use ($listing) {
                    $message->to($listing->user->email)
                        ->subject("Test Drive Request: {$listing->title}");
                });
            } catch (\Exception $e) {
                Log::warning('Failed to send seller email', ['error' => $e->getMessage()]);
            }

            // Send confirmation to buyer
            try {
                Mail::send('emails.test-drive-confirmation', [
                    'listing' => $listing,
                    'testDrive' => $testDrive,
                ], function ($message) use ($request) {
                    $message->to($request->buyer_email)
                        ->subject("Test Drive Scheduled: {$listing->title}");
                });
            } catch (\Exception $e) {
                Log::warning('Failed to send buyer email', ['error' => $e->getMessage()]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Test drive scheduled successfully!',
            ]);
        } catch (\Exception $e) {
            Log::error('Test drive booking error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to schedule test drive',
            ], 500);
        }
    }
}
