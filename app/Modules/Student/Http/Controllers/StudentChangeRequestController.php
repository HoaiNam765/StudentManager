<?php

namespace App\Modules\Student\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Student\Models\StudentChangeRequest;
use App\Modules\Student\Services\IdentityNumber;
use App\Modules\Student\Services\StudentSelfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Cán bộ xử lý yêu cầu sửa trường nhạy cảm (BR-STU-06): xem = STU.view theo phạm vi hồ sơ; duyệt / từ chối = STU.approve
 * trên hồ sơ (CTSV, ACAD). Số CCCD trong yêu cầu hiển thị đã che.
 */
class StudentChangeRequestController extends Controller
{
    public function __construct(
        private readonly StudentSelfService $self,
        private readonly AccessControl $access,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $list = $this->access->constrain(StudentChangeRequest::query(), $request->user(), 'STU', PermissionAction::View)
            ->with('student:id,student_code,full_name')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()), fn ($q) => $q->where('status', StudentChangeRequest::PENDING))
            ->orderBy('id')
            ->paginate(50);

        return response()->json($list->through(fn (StudentChangeRequest $r) => self::present($r)));
    }

    public function approve(Request $request, StudentChangeRequest $studentChangeRequest): JsonResponse
    {
        $this->authorizeDecision($request, $studentChangeRequest);
        $note = $request->validate(['note' => ['sometimes', 'nullable', 'string', 'max:1000']])['note'] ?? null;

        return response()->json(self::present($this->self->approve($studentChangeRequest, $request->user(), $note)));
    }

    public function reject(Request $request, StudentChangeRequest $studentChangeRequest): JsonResponse
    {
        $this->authorizeDecision($request, $studentChangeRequest);
        $note = $request->validate(['note' => ['required', 'string', 'max:1000']], [], ['note' => 'lý do từ chối'])['note'];

        return response()->json(self::present($this->self->reject($studentChangeRequest, $request->user(), $note)));
    }

    public function evidence(StudentChangeRequest $studentChangeRequest): StreamedResponse
    {
        Gate::authorize('view', $studentChangeRequest->student);

        return Storage::disk(config('studentmanager.student.decision_disk', 'local'))
            ->download($studentChangeRequest->evidence_path, $studentChangeRequest->evidence_name ?? 'minh-chung');
    }

    /** @return array<string, mixed> */
    public static function present(StudentChangeRequest $r): array
    {
        $r->loadMissing('student:id,student_code,full_name');
        $changes = [];

        foreach ($r->changes as $field => $change) {
            $changes[$field] = $field === 'national_id'
                ? ['old' => IdentityNumber::mask($change['old']), 'new' => IdentityNumber::mask($change['new'])]
                : $change;
        }

        return [
            'id' => $r->id,
            'student' => $r->student?->only('id', 'student_code', 'full_name'),
            'changes' => $changes,
            'reason' => $r->reason,
            'evidence_name' => $r->evidence_name,
            'status' => $r->status,
            'status_label' => StudentChangeRequest::STATUS_LABELS[$r->status] ?? $r->status,
            'decision_note' => $r->decision_note,
            'decided_at' => $r->decided_at?->setTimezone(config('studentmanager.display_timezone'))->format('Y-m-d H:i'),
            'created_at' => $r->created_at?->setTimezone(config('studentmanager.display_timezone'))->format('Y-m-d H:i'),
        ];
    }

    private function authorizeDecision(Request $request, StudentChangeRequest $changeRequest): void
    {
        abort_unless($this->access->allowsOn($request->user(), 'STU', PermissionAction::Approve, $changeRequest->student), 403, 'Bạn không có quyền duyệt yêu cầu này.');
    }
}
