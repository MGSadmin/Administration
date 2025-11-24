<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display all notifications (toutes les applications)
     */
    public function index()
    {
        // Afficher toutes les notifications, peu importe l'application
        $notifications = Auth::user()->notifications()->paginate(20);
        
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        return redirect()->back()->with('success', 'Toutes les notifications ont été marquées comme lues');
    }

    /**
     * Check for new notifications
     */
    public function checkNew(Request $request)
    {
        $count = Auth::user()->unreadNotifications()->count();
        $hasNew = $count > ($request->session()->get('last_notification_count', 0));
        
        $request->session()->put('last_notification_count', $count);
        
        return response()->json([
            'hasNew' => $hasNew,
            'count' => $count,
            'message' => $hasNew ? 'Vous avez de nouvelles notifications' : null
        ]);
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();
        
        return redirect()->back()->with('success', 'Notification supprimée');
    }
}
