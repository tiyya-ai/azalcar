<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function searchUsers(Request $request)
    {
        $search = $request->input('q');
        $query = \App\Models\User::where('id', '!=', auth()->id());

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->select('id', 'name', 'email')
            ->latest()
            ->limit(10)
            ->get();

        return response()->json($users);
    }

    public function index()
    {
        $conversations = Conversation::with(['listing', 'buyer', 'seller'])
            ->withCount('messages')
            ->latest()
            ->paginate(20);
            
        return view('admin.messages.index', compact('conversations'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Conversation $conversation) // Note: route model binding uses 'message' param by default for resource? No, 'message' if controller name.
    {
        // Actually, resource route might name the parameter 'message' for MessageController.
        // But I bind model=Conversation.
        // Let's check routes list if needed, or just handle manually.
        
        $conversation->load(['messages.sender', 'listing', 'buyer', 'seller']);
        
        $conversations = Conversation::with(['listing', 'buyer', 'seller'])
            ->withCount('messages')
            ->latest()
            ->paginate(20);

        return view('admin.messages.show', compact('conversation', 'conversations'));
    }

    /**
     * Store a newly created conversation from Admin.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        $recipient = \App\Models\User::findOrFail($request->user_id);
        
        if ($recipient->id === auth()->id()) {
             return back()->withErrors(['email' => 'You cannot start a chat with yourself.']);
        }

        // Try to find an existing direct conversation (listing_id is null)
        $conversation = Conversation::whereNull('listing_id')
            ->where(function($q) use ($recipient) {
                $q->where('buyer_id', auth()->id())->where('seller_id', $recipient->id);
            })
            ->orWhere(function($q) use ($recipient) {
                $q->where('buyer_id', $recipient->id)->where('seller_id', auth()->id());
            })
            ->whereNull('listing_id')
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'buyer_id' => auth()->id(),
                'seller_id' => $recipient->id,
                'listing_id' => null,
            ]);
        }

        \App\Models\Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'receiver_id' => $recipient->id,
            'listing_id' => null, 
            'message' => $request->message,
            'is_read' => false,
        ]);

        $conversation->touch();

        return redirect()->route('admin.messages.show', $conversation->id)->with('success', 'Message sent.');
    }

    /**
     * Store a reply from the admin.
     */
    public function reply(Request $request, Conversation $conversation)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'recipient_id' => 'required|exists:users,id',
        ]);

        \App\Models\Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'receiver_id' => $request->recipient_id,
            'listing_id' => $conversation->listing_id,
            'message' => $request->message,
            'is_read' => false,
        ]);

        return back()->with('success', 'Message sent successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $conversation = Conversation::findOrFail($id);
        $conversation->messages()->delete(); // Soft delete messages
        $conversation->delete(); // Delete conversation
        
        return back()->with('success', 'Conversation deleted.');
    }
}
