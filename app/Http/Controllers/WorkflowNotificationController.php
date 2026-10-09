<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;

class WorkflowNotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $notifications = $user->notifications()
            ->latest()
            ->paginate(25);

        return view('admin.notifications.index', compact('notifications'));
    }

public function feed(Request $request)
{
    $user = $request->user();

    // Only unread notifications appear in the navigation dropdown.
    $notifications = $user->unreadNotifications()
        ->latest()
        ->limit(8)
        ->get()
        ->map(function ($notification) {
            $data = $notification->data ?? [];

            return [
                'id' => $notification->id,
                'title' => $data['title'] ?? 'Notification',
                'message' => $data['message'] ?? '',
                'url' => $data['url'] ?? null,
                'icon' => $data['icon'] ?? 'bell',
                'priority' => $data['priority'] ?? 'normal',
                'read' => false,
                'created_at' => $notification->created_at?->toIso8601String(),
                'time' => $notification->created_at?->diffForHumans(),
            ];
        });

    return response()->json([
        'unread_count' => $user->unreadNotifications()->count(),
        'notifications' => $notifications,
    ]);
}



    public function markAsRead(Request $request, string $id)
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        return response()->json([
            'success' => true,
            'unread_count' => $request->user()
                ->unreadNotifications()
                ->count(),
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        $user = $request->user();

        DB::transaction(function () use ($user) {
            $user->unreadNotifications()
                ->update(['read_at' => now()]);
        });

        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }
}