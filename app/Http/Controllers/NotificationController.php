<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display a listing of user notifications (maximum 30 notifications).
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $notifications = $user->notifications()
            ->latest()
            ->limit(30)
            ->get();

        $unreadCount = $user->unreadNotifications()->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark a specific notification as read and redirect to its destination.
     */
    public function show(string $id): RedirectResponse
    {
        $user = Auth::user();

        $notification = $user->notifications()->where('id', $id)->firstOrFail();

        if ($notification->unread()) {
            $notification->markAsRead();
        }

        $conversationId = $notification->data['conversation_id'] ?? null;

        if ($conversationId) {
            return redirect()->route('chat.show', $conversationId);
        }

        return redirect()->route('notifications.index');
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead(): RedirectResponse
    {
        $user = Auth::user();

        $user->unreadNotifications()->update(['read_at' => now()]);

        return redirect()->route('notifications.index')
            ->with('success', 'Semua notifikasi telah ditandai sebagai telah dibaca.');
    }
}

