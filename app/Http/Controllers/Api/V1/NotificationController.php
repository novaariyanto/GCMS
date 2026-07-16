<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\NotificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NotificationController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $notifications = $request->user('api')
            ->notifications()
            ->latest()
            ->paginate((int) $request->integer('per_page', 15));

        return NotificationResource::collection($notifications);
    }

    public function markRead(Request $request, string $id): JsonResponse
    {
        $notification = $request->user('api')->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'message' => 'Notifikasi ditandai sudah dibaca.',
            'data' => new NotificationResource($notification->refresh()),
        ]);
    }
}
