<?php

namespace App\Http\Controllers\Api\Notification;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\NotificationLog;
use App\Models\NotificationTemplate;
use App\Services\Notification\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(private readonly NotificationService $notificationService) {}

    /** GET /api/notifications/templates */
    public function templates(Request $request): JsonResponse
    {
        $templates = NotificationTemplate::where('active', true)
            ->orderBy('event_key')
            ->get();

        return response()->json($templates);
    }

    /** PUT /api/notifications/templates/{template} */
    public function updateTemplate(Request $request, NotificationTemplate $template): JsonResponse
    {
        $validated = $request->validate([
            'name'   => 'sometimes|string|max:255',
            'body'   => 'sometimes|string',
            'active' => 'sometimes|boolean',
        ]);

        $template->update($validated);

        return response()->json($template->fresh());
    }

    /** POST /api/notifications/send/member/{member} */
    public function sendToMember(Request $request, Member $member): JsonResponse
    {
        $validated = $request->validate([
            'event_key'  => 'required|string',
            'variables'  => 'nullable|array',
        ]);

        $log = $this->notificationService->sendToMember(
            $member,
            $validated['event_key'],
            $validated['variables'] ?? []
        );

        return response()->json($log, 201);
    }

    /** GET /api/notifications/logs */
    public function logs(Request $request): JsonResponse
    {
        $query = NotificationLog::with('member')
            ->when($request->member_id, fn($q, $id) => $q->where('member_id', $id))
            ->when($request->event_key, fn($q, $k) => $q->where('event_key', $k))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->orderByDesc('created_at');

        return response()->json($query->paginate($request->per_page ?? 20));
    }
}
