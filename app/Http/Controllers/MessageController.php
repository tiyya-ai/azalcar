<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Listing;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Inbox / Conversation List
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get all conversations where the user is either buyer or seller
        $conversations = Conversation::with(['listing.make', 'buyer', 'seller', 'latestMessage'])
            ->where('buyer_id', $user->id)
            ->orWhere('seller_id', $user->id)
            ->latest('updated_at')
            ->get();
            
        return view('dashboard.messages.index', compact('conversations'));
    }

    /**
     * Show a specific conversation
     */
    public function show(Conversation $conversation)
    {
        // Check if user is part of the conversation
        if ($conversation->buyer_id !== auth()->id() && $conversation->seller_id !== auth()->id()) {
            abort(403);
        }

        $conversation->load(['listing', 'buyer', 'seller', 'messages.sender']);
        
        // Mark messages as read
        Message::where('conversation_id', $conversation->id)
            ->where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('dashboard.messages.show', compact('conversation'));
    }

    /**
     * Send a message
     */
    public function store(Request $request)
    {
        $request->validate([
            'listing_id' => 'required|exists:listings,id',
            'message' => 'required|string|max:1000',
        ]);

        $listing = Listing::findOrFail($request->listing_id);
        
        // PRODUCTION HARDENING: Prevent messaging on inactive listings
        if ($listing->status !== 'active') {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'This listing is no longer active']);
            }
            return back()->with('error', 'This listing is no longer active');
        }

        // PRODUCTION HARDENING: Prevent self-messaging
        if (auth()->id() === $listing->user_id) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Cannot message yourself']);
            }
            return back()->with('error', 'Cannot message yourself');
        }

        try {
            // 1. Find or create conversation atomically
            // If unique constraint exists and another request creates it simultaneously,
            // we'll get the existing one
            $conversation = Conversation::firstOrCreate(
                [
                    'listing_id' => $listing->id,
                    'buyer_id' => auth()->id(),
                    'seller_id' => $listing->user_id,
                ],
                [
                    'listing_id' => $listing->id,
                    'buyer_id' => auth()->id(),
                    'seller_id' => $listing->user_id,
                ]
            );

            // 2. Create message
            // PRODUCTION HARDENING: Use conversation's seller_id instead of listing's user_id
            // This ensures message routing is correct even if listing is deleted
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => auth()->id(),
                'receiver_id' => $conversation->seller_id,  // Use conversation's seller_id
                'listing_id' => $listing->id,
                'message' => $request->message,
            ]);

            // 3. Update conversation timestamp
            $conversation->touch();

            // 4. Notify receiver (optional - commented out in original)
            if ($conversation->seller_id && ($seller = User::find($conversation->seller_id))) {
                try {
                    // $seller->notify(new \App\Notifications\NewMessageNotification($message));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('Failed to send message notification', [
                        'error' => $e->getMessage(),
                        'message_id' => $message->id
                    ]);
                }
            }

            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }

            return back()->with('success', 'Message sent successfully!');
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle unique constraint violations gracefully
            if (strpos($e->getMessage(), 'Duplicate entry') !== false || 
                strpos($e->getMessage(), 'UNIQUE constraint failed') !== false) {
                // Another request already created this conversation - retry
                $conversation = Conversation::where('listing_id', $listing->id)
                    ->where('buyer_id', auth()->id())
                    ->where('seller_id', $listing->user_id)
                    ->first();

                if ($conversation) {
                    $message = Message::create([
                        'conversation_id' => $conversation->id,
                        'sender_id' => auth()->id(),
                        'receiver_id' => $conversation->seller_id,
                        'listing_id' => $listing->id,
                        'message' => $request->message,
                    ]);
                    $conversation->touch();

                    if ($request->ajax()) {
                        return response()->json(['success' => true]);
                    }
                    return back()->with('success', 'Message sent successfully!');
                }
            }

            \Illuminate\Support\Facades\Log::error('Database error creating message', [
                'listing_id' => $listing->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to send message']);
            }
            return back()->with('error', 'Failed to send message');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Unexpected error creating message', [
                'listing_id' => $listing->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to send message']);
            }
            return back()->with('error', 'Failed to send message');
        }
    }

    /**
     * Reply to a message in a conversation
     */
    public function reply(Request $request, Conversation $conversation)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        $receiverId = ($conversation->buyer_id === auth()->id()) ? $conversation->seller_id : $conversation->buyer_id;

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'receiver_id' => $receiverId,
            'listing_id' => $conversation->listing_id,
            'message' => $request->message,
        ]);

        $conversation->touch();

        return back()->with('success', 'Reply sent!');
    }
}
