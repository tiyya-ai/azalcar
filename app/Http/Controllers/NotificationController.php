<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return redirect()->route('login');
            }
            
            $notifications = $user->notifications()->paginate(20);
            $user->unreadNotifications->markAsRead();

            return view('dashboard.notifications', compact('notifications'));
        } catch (\Exception $e) {
            Log::error('Notification index error: ' . $e->getMessage());
            return view('dashboard.notifications', ['notifications' => collect()]);
        }
    }

    public function destroy($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->delete();
        return back()->with('success', 'Notification removed.');
    }

    public function readAll()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'All notifications marked as read.');
    }

    public function check(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['count' => 0, 'unread_messages_count' => 0]);
            }

            $unreadCount = $user->unreadNotifications()->count();
            $unreadMessagesCount = \App\Models\Message::where('receiver_id', $user->id)->where('is_read', false)->count();
            $latest = $user->unreadNotifications()->first();

            return response()->json([
                'count' => $unreadCount,
                'unread_messages_count' => $unreadMessagesCount,
                'latest' => $latest ? [
                    'id' => $latest->id,
                    'title' => $latest->data['title'] ?? 'New Notification',
                    'message' => $latest->data['message'] ?? '',
                    'url' => $latest->data['url'] ?? '#'
                ] : null
            ]);
        } catch (\Exception $e) {
            Log::error('Notification check error: ' . $e->getMessage());
            return response()->json(['count' => 0, 'unread_messages_count' => 0]);
        }
    }
}
