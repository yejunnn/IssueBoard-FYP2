<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }
        
        $notification->markAsRead();
        
        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications()->update(['is_read' => true]);
        
        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    public function getUnreadCount()
    {
        $user = Auth::user();
        $count = $user->unreadNotifications()->count();
        
        return response()->json(['count' => $count]);
    }

    public function delete(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }
        
        $notification->delete();
        
        return redirect()->back()->with('success', 'Notification deleted.');
    }

    public function deleteAll()
    {
        $user = Auth::user();
        $deletedCount = $user->notifications()->count();
        $user->notifications()->delete();
        
        return redirect()->back()->with('success', "All {$deletedCount} notifications deleted successfully.");
    }
}
