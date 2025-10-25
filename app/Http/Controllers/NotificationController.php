<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse as Redirect;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    public function show(Notification $notification, Request $request)
    {
        $user = $request->user();

        if ($notification->user_id !== $user->id) {
            abort(403);
        }

        // Mark as read if not already read
        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        // If notification has an associated appointment, redirect to appropriate appointment show route
        if ($notification->appointment) {
            if ($user->isCounselor() || $user->isAssistant()) {
                return redirect()->route('appointments.show', [$notification->appointment, 'back' => 'notifications']);
            } else {
                return redirect()->route('student.appointments.show', [$notification->appointment, 'back' => 'notifications']);
            }
        }

        return view('appointments.show', compact('notification'));
    }

    public function markAsRead(Notification $notification, Request $request): RedirectResponse
    {
        $user = $request->user();
        
        if ($notification->user_id !== $user->id) {
            abort(403);
        }

        $notification->markAsRead();

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        $user->notifications()->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(Notification $notification, Request $request): RedirectResponse
    {
        $user = $request->user();
        
        if ($notification->user_id !== $user->id) {
            abort(403);
        }

        $notification->delete();

        return back()->with('success', 'Notification deleted.');
    }

    /**
     * Send official notification (Counselor only). 
     */
    public function sendNotification(Request $request): RedirectResponse
    {
        if (!Auth::user()->canSendOfficialNotifications()) {
            return redirect()->route('notifications.index')
                ->with('error', 'You do not have permission to send official notifications.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'type' => 'required|in:general,appointment,urgent,system'
        ]);

        $targetUser = \App\Models\User::findOrFail($request->user_id);

        $targetUser->notifications()->create([
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'read_at' => null,
        ]);

        return redirect()->route('notifications.index')
            ->with('success', 'Official notification sent successfully.');
    }
} 