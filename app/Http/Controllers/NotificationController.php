<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotificationRequest;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    /**
     * Store a new notification and log the activity
     */
    public function store(NotificationRequest $request): JsonResponse
    {
        // Get validated data
        $validated = $request->validated();

        // Create activity log with polymorphic relationship
        $activityLog = new ActivityLog();
        $activityLog->channel = $validated['channel'];
        $activityLog->message = $validated['message'];
        $activityLog->notifiable_type = $validated['notifiable_type'];
        $activityLog->notifiable_id = $validated['notifiable_id'];
        $activityLog->save();

        return response()->json([
            'success' => true,
            'message' => 'Notification logged successfully.',
            'data' => [
                'id' => $activityLog->id,
                'channel' => $activityLog->channel,
                'message' => $activityLog->message,
                'notifiable_type' => $activityLog->notifiable_type,
                'notifiable_id' => $activityLog->notifiable_id,
                'created_at' => $activityLog->created_at,
            ],
        ], 201);
    }
}
