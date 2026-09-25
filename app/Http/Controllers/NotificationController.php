<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $limit = min(30, max(5, (int) $request->integer('limit', 15)));

        $notifications = $user->notifications()
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn ($notification) => $this->formatNotification($notification));

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

        $notification->markAsRead();

        return response()->json([
            'ok' => true,
            'unread_count' => $request->user()->unreadNotifications()->count(),
            'notification' => $this->formatNotification($notification->fresh()),
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json([
            'ok' => true,
            'unread_count' => 0,
        ]);
    }

    protected function formatNotification($notification): array
    {
        $data = $notification->data ?? [];

        return [
            'id' => $notification->id,
            'title' => $data['title'] ?? 'Notificación',
            'body' => $data['body'] ?? '',
            'url' => $data['url'] ?? '#',
            'type' => $data['type'] ?? null,
            'order_id' => $data['order_id'] ?? null,
            'read_at' => optional($notification->read_at)?->toIso8601String(),
            'created_at' => optional($notification->created_at)?->toIso8601String(),
            'created_at_human' => optional($notification->created_at)?->diffForHumans(),
        ];
    }
}
