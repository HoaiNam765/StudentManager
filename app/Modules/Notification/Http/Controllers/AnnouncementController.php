<?php

namespace App\Modules\Notification\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Notification\Models\Notification;
use App\Modules\Notification\Models\RecipientGroup;
use App\Modules\Notification\Services\AnnouncementService;
use App\Modules\Notification\Services\AudienceResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Soạn, gửi / hẹn giờ, lịch sử, thu hồi thông báo thủ công và nhóm người nhận (FR-NOT-006, 009, 015; BR-NOT-05, 08). */
class AnnouncementController extends Controller
{
    public function __construct(
        private readonly AnnouncementService $service,
        private readonly AudienceResolver $audiences,
    ) {}

    /** Đối tượng người gửi được chọn và nhóm đã lưu. */
    public function options(Request $request): JsonResponse
    {
        return response()->json([
            'types' => AudienceResolver::TYPES,
            'options' => $this->audiences->options($request->user()),
            'groups' => RecipientGroup::query()->where('owner_id', $request->user()->id)->orderBy('name')->get(['id', 'name', 'audience']),
        ]);
    }

    public function preview(Request $request): JsonResponse
    {
        return response()->json($this->service->preview($request->user(), $this->audience($request)));
    }

    public function index(Request $request): JsonResponse
    {
        $page = $this->service->history($request->user());

        return response()->json([
            'data' => collect($page->items())->map(fn (Notification $n) => $this->present($n))->values(),
            'meta' => ['current_page' => $page->currentPage(), 'last_page' => $page->lastPage(), 'total' => $page->total()],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'body' => ['required', 'string', 'max:5000'],
            'link' => ['nullable', 'string', 'max:500', 'regex:#^/(?!/)#'],
            'scheduled_at' => ['nullable', 'date_format:Y-m-d H:i'],
            'expires_at' => ['nullable', 'date_format:Y-m-d H:i'],
            'pinned' => ['nullable', 'boolean'],
            'urgent' => ['nullable', 'boolean'],
            'attachment' => ['nullable', 'file', 'max:5120', 'mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg'],
        ]);
        $data['audience'] = $this->audience($request);

        return response()->json($this->present($this->service->create($request->user(), $data, $request->file('attachment'))), 201);
    }

    public function revoke(Request $request, Notification $notification): JsonResponse
    {
        $data = $request->validate(['reason' => ['required', 'string', 'max:500']]);

        return response()->json($this->present($this->service->revoke($request->user(), $notification, $data['reason'])));
    }

    public function attachment(Request $request, Notification $notification): StreamedResponse
    {
        return Storage::disk('local')->download($this->service->attachmentFor($request->user(), $notification), $notification->attachment_name ?? 'dinh-kem');
    }

    public function saveGroup(Request $request, ?RecipientGroup $recipientGroup = null): JsonResponse
    {
        abort_if($recipientGroup !== null && $recipientGroup->owner_id !== $request->user()->id, 404);
        $data = $request->validate(['name' => ['required', 'string', 'max:150']]);

        return response()->json($this->service->saveGroup($request->user(), $data['name'], $this->audience($request), $recipientGroup), $recipientGroup ? 200 : 201);
    }

    public function deleteGroup(Request $request, RecipientGroup $recipientGroup): JsonResponse
    {
        abort_if($recipientGroup->owner_id !== $request->user()->id, 404);
        $recipientGroup->delete();

        return response()->json(null, 204);
    }

    /** Bộ chọn đối tượng gửi kèm yêu cầu (JSON hoặc form có tệp: `audience` là chuỗi JSON). */
    private function audience(Request $request): array
    {
        $audience = $request->input('audience');

        if (is_string($audience)) {
            $audience = json_decode($audience, true);
        }

        abort_unless(is_array($audience), 422, 'Thiếu đối tượng nhận (audience).');

        return $audience;
    }

    /** @return array<string, mixed> */
    private function present(Notification $n): array
    {
        return [
            'id' => $n->id,
            'title' => $n->title,
            'body' => $n->body,
            'link' => $n->link,
            'status' => $n->status,
            'status_label' => ['sent' => 'Đã gửi', 'scheduled' => 'Hẹn giờ', 'revoked' => 'Đã thu hồi'][$n->status] ?? $n->status,
            'priority' => $n->priority,
            'pinned' => $n->pinned,
            'audience' => $n->audience['labels'] ?? [],
            'sender' => $n->sender?->name,
            'recipient_count' => $n->recipient_count,
            'read_count' => (int) ($n->read_count ?? $n->recipients()->whereNotNull('read_at')->count()),
            'attachment_name' => $n->attachment_name,
            'scheduled_at' => $n->scheduled_at?->toIso8601String(),
            'sent_at' => $n->sent_at?->toIso8601String(),
            'expires_at' => $n->expires_at?->toIso8601String(),
            'revoke_reason' => $n->revoke_reason,
        ];
    }
}
