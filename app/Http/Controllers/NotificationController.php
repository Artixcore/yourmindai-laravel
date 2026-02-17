<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Get unread notification count.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }
            $count = $user->unreadNotifications()->count();
            return response()->json(['success' => true, 'count' => $count]);
        } catch (\Throwable $e) {
            Log::error('NotificationController::unreadCount failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Something went wrong'], 500);
        }
    }

    /**
     * Get latest unread notifications (limit 10).
     */
    public function unread(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }
            $notifications = $user->unreadNotifications()
                ->limit(10)
                ->get()
                ->map(function ($n) {
                    $data = $n->data;
                    return [
                        'id' => $n->id,
                        'type' => $data['type'] ?? 'unknown',
                        'title' => $data['title'] ?? 'Notification',
                        'message' => $data['message'] ?? '',
                        'url' => $data['url'] ?? '#',
                        'created_at' => $n->created_at->diffForHumans(),
                    ];
                });
            return response()->json(['success' => true, 'notifications' => $notifications]);
        } catch (\Throwable $e) {
            Log::error('NotificationController::unread failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Something went wrong'], 500);
        }
    }

    /**
     * Mark a notification as read.
     */
    public function markRead(Request $request, string $id): JsonResponse
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }
            $notification = $user->notifications()->where('id', $id)->firstOrFail();
            $notification->markAsRead();
            return response()->json(['success' => true]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
        } catch (\Throwable $e) {
            Log::error('NotificationController::markRead failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Something went wrong'], 500);
        }
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }
            $user->unreadNotifications->markAsRead();
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('NotificationController::markAllRead failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Something went wrong'], 500);
        }
    }
}
