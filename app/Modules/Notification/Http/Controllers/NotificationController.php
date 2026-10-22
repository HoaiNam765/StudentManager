<?php

namespace App\Modules\Notification\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Notification\Models\NotificationRecipient;
use App\Modules\Notification\Services\NotificationCenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** API trung tâm thông báo của người đang đăng nhập (FR-NOT-001; BR-NOT-01). */
class NotificationController extends Controller
{
    public function __construct(private readonly NotificationCenter $center) {}

    public function index(Request $request): JsonResponse
    {
        $data = $request->validate([
            'status' => ['nullable', 'in:all,unread'],
            'category' => ['nullable', 'string', 'max:30'],
            'q' => ['nullable', 'string', 'max:100'],
            'archived' => ['nullable', 'boolean'],
        ]);
        $page = $this->center->list($request->user(), $data);

        return response()->json([
            'data' => collect($page->items())->map(fn (NotificationRecipient $r) => $this->present($r))->values(),
            'meta' => ['current_page' => $page->currentPage(), 'last_page' => $page->lastPage(), 'total' => $page->total()],
            'unread' => $this->center->unreadCount($request->user()),
            'categories' => config('notifications.categories'),
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json(['unread' => $this->center->unreadCount($request->user())]);
    }

    public function read(Request $request, int $notification): JsonResponse
    {
        return response()->json($this->present($this->center->markRead($request->user(), $notification)));
    }

    public function readAll(Request $request): JsonResponse
    {
        return response()->json(['marked' => $this->center->markAllRead($request->user()), 'unread' => 0]);
    }

    /** @return array<string, mixed> */
    private function present(NotificationRecipient $row): array
    {
        $n = $row->notification;

        return [
            'id' => $n->id,
            'title' => $n->title,
            'body' => $n->body,
            'link' => $n->link,
            'category' => $n->category,
            'category_label' => config("notifications.categories.{$n->category}", 'Khác'),
            'source' => $n->source,
            'event_code' => $n->event_code,
            'priority' => $n->priority,
            'mandatory' => $n->mandatory,
            'pinned' => $n->pinned,
            'sender' => $n->sender?->name,
            'sent_at' => $n->sent_at?->toIso8601String(),
            'expires_at' => $n->expires_at?->toIso8601String(),
            'read_at' => $row->read_at?->toIso8601String(),
            'attachment' => $n->attachment_name ? ['name' => $n->attachment_name, 'url' => route('notifications.attachment', $n->id)] : null,
        ];
    }
}
