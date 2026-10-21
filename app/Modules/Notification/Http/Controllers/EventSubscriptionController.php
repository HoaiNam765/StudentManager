<?php

namespace App\Modules\Notification\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Notification\Models\EventSubscription;
use App\Modules\Notification\Services\EventSubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Cấu hình thông báo theo sự kiện (FR-NOT-004) cho ADMIN; tùy chọn nhận của người dùng (FR-NOT-007). */
class EventSubscriptionController extends Controller
{
    public function __construct(private readonly EventSubscriptionService $service) {}

    public function index(): JsonResponse
    {
        return response()->json(['data' => EventSubscription::query()->orderBy('event_code')->get()]);
    }

    public function update(Request $request, string $code): JsonResponse
    {
        $event = EventSubscription::query()->where('event_code', $code)->firstOrFail();
        $data = $request->validate([
            'enabled' => ['sometimes', 'boolean'],
            'channels' => ['sometimes', 'array'],
            'channels.*' => ['in:in_app,email'],
            'title_template' => ['sometimes', 'string', 'max:200'],
            'body_template' => ['sometimes', 'string', 'max:2000'],
            'link_template' => ['sometimes', 'nullable', 'string', 'max:300'],
        ]);

        return response()->json($this->service->update($event, $data));
    }

    public function preferences(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->service->preferences($request->user())]);
    }

    public function savePreferences(Request $request): JsonResponse
    {
        $data = $request->validate(['preferences' => ['required', 'array'], 'preferences.*' => ['boolean']]);

        return response()->json(['data' => $this->service->savePreferences($request->user(), $data['preferences'])]);
    }
}
