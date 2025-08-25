<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $userId = auth()->id();
            if (!$userId) {
                Log::error('Unauthenticated access to notifications');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated'
                ], 401);
            }

            $notifications = Notification::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'author_name' => $notification->author_name,
                        'action' => $notification->action,
                        'issue_id' => $notification->issue_id,
                        'post_id' => $notification->post_id,
                        'issue_description' => $notification->issue_description,
                        'is_read' => (bool) $notification->is_read,
                        'timestamp' => Carbon::parse($notification->created_at)->diffForHumans()
                    ];
                });

            return response()->json([
                'status' => 'success',
                'notifications' => $notifications
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to fetch notifications: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch notifications'
            ], 500);
        }
    }

    public function markAsRead(Request $request, $id)
    {
        try {
            $userId = auth()->id();
            if (!$userId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated'
                ], 401);
            }

            $notification = Notification::where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$notification) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Notification not found'
                ], 404);
            }

            $notification->update(['is_read' => true]);

            return response()->json([
                'status' => 'success',
                'message' => 'Notification marked as read'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to mark notification as read: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to mark notification as read'
            ], 500);
        }
    }

    public function markAllAsRead(Request $request)
    {
        try {
            $userId = auth()->id();
            if (!$userId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated'
                ], 401);
            }

            Notification::where('user_id', $userId)
                ->where('is_read', false)
                ->update(['is_read' => true]);

            return response()->json([
                'status' => 'success',
                'message' => 'All notifications marked as read'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to mark all notifications as read: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to mark all notifications as read'
            ], 500);
        }
    }
}
