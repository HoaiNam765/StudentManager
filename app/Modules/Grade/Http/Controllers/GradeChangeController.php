<?php

namespace App\Modules\Grade\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grade\Models\GradeChangeRequest;
use App\Modules\Grade\Models\GradeVersion;
use App\Modules\Grade\Services\GradebookService;
use App\Modules\Grade\Services\GradeChangeService;
use App\Modules\Grade\Services\SpecialStatusService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Yêu cầu điều chỉnh điểm sau công bố (FR-GRD-006), lịch sử phiên bản điểm, trạng thái điểm đặc biệt (FR-GRD-012). */
class GradeChangeController extends Controller
{
    public function __construct(
        private readonly GradeChangeService $service,
        private readonly GradebookService $gradebook,
    ) {}

    /** Cán bộ: yêu cầu mình được duyệt (mặc định đang chờ); giảng viên: yêu cầu mình đã gửi (mặc định tất cả). */
    public function index(Request $request): JsonResponse
    {
        $data = $request->validate(['status' => ['nullable', 'in:pending,pending_faculty,pending_confirmation,approved,rejected,all']]);
        $user = $request->user();
        $mine = $request->routeIs('teacher.*');
        $status = $data['status'] ?? ($mine ? 'all' : 'pending');

        $requests = GradeChangeRequest::query()
            ->with('enrollment.student:id,student_code,full_name', 'enrollment.section', 'enrollment.subject:id,code,name')
            ->when($mine, fn ($q) => $q->where('requested_by', $user->id))
            ->when($status === 'pending', fn ($q) => $q->whereIn('status', GradeChangeService::PENDING))
            ->when(! in_array($status, ['pending', 'all'], true), fn ($q) => $q->where('status', $status))
            ->latest('id')
            ->limit(200)
            ->get()
            ->filter(fn (GradeChangeRequest $r) => $mine || $this->service->canReview($user, $r))
            ->map(fn (GradeChangeRequest $r) => $this->present($r) + ['can_act' => ! $mine && $this->service->canAct($user, $r)])
            ->values();

        return response()->json(['data' => $requests]);
    }

    public function store(Request $request, Enrollment $enrollment): JsonResponse
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:2000'],
            'evidence' => ['nullable', 'file', 'max:5120', 'mimes:pdf,png,jpg,jpeg'],
            'scores' => ['required', 'array', 'min:1'],
            'scores.*.component_id' => ['required', 'integer'],
            'scores.*.score' => ['required', 'numeric'],
        ]);
        $evidence = $request->hasFile('evidence') ? $request->file('evidence')->store('grade-evidence', 'local') : null;

        return response()->json($this->present($this->service->request($enrollment, $request->user(), $data['scores'], $data['reason'], $evidence)), 201);
    }

    public function approve(Request $request, GradeChangeRequest $gradeChangeRequest): JsonResponse
    {
        return response()->json($this->present($this->service->decide($gradeChangeRequest, $request->user(), true)));
    }

    public function reject(Request $request, GradeChangeRequest $gradeChangeRequest): JsonResponse
    {
        $data = $request->validate(['reason' => ['required', 'string', 'max:2000']]);

        return response()->json($this->present($this->service->decide($gradeChangeRequest, $request->user(), false, $data['reason'])));
    }

    /** Minh chứng (disk riêng tư): người gửi hoặc người duyệt được xem. */
    public function evidence(Request $request, GradeChangeRequest $gradeChangeRequest): StreamedResponse
    {
        $user = $request->user();
        abort_unless($gradeChangeRequest->requested_by === $user->id || $this->service->canReview($user, $gradeChangeRequest), 403,
            'Bạn không có quyền xem minh chứng của yêu cầu này.');
        abort_if($gradeChangeRequest->evidence_path === null || ! Storage::disk('local')->exists($gradeChangeRequest->evidence_path), 404,
            'Yêu cầu này không có tệp minh chứng.');

        return Storage::disk('local')->download($gradeChangeRequest->evidence_path, 'minh-chung-'.$gradeChangeRequest->id.'.'.pathinfo($gradeChangeRequest->evidence_path, PATHINFO_EXTENSION));
    }

    /** Lịch sử phiên bản điểm của một lần học (bất biến, BR-GRD-06). */
    public function history(Request $request, Enrollment $enrollment): JsonResponse
    {
        $this->gradebook->authorizeView($enrollment->section, $request->user());

        return response()->json([
            'versions' => GradeVersion::query()->where('enrollment_id', $enrollment->id)->orderByDesc('version')->get(),
            'requests' => GradeChangeRequest::query()->where('enrollment_id', $enrollment->id)->latest('id')->get()->map(fn ($r) => $this->present($r)),
        ]);
    }

    /** Giảng viên ghi / bỏ trạng thái điểm đặc biệt (`status` = null để bỏ). */
    public function special(Request $request, Enrollment $enrollment, SpecialStatusService $special): JsonResponse
    {
        $data = $request->validate([
            'status' => ['nullable', 'string', 'max:24'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        return response()->json($special->set($enrollment, $request->user(), $data['status'] ?? null, $data['reason'] ?? null));
    }

    /** @return array<string, mixed> */
    private function present(GradeChangeRequest $request): array
    {
        $request->loadMissing('enrollment.student:id,student_code,full_name', 'enrollment.section', 'enrollment.subject:id,code,name');

        return [
            'id' => $request->id,
            'enrollment_id' => $request->enrollment_id,
            'student' => $request->enrollment?->student?->only('id', 'student_code', 'full_name'),
            'section' => $request->enrollment?->section?->only('id', 'code'),
            'subject' => $request->enrollment?->subject?->only('code', 'name'),
            'status' => $request->status,
            'status_label' => match ($request->status) {
                'pending_faculty' => 'Chờ khoa duyệt',
                'pending_confirmation' => 'Chờ Phòng Đào tạo xác nhận',
                'approved' => 'Đã áp dụng',
                'rejected' => 'Bị từ chối',
                default => $request->status,
            },
            'reason' => $request->reason,
            'proposed_scores' => $request->proposed_scores,
            'has_evidence' => $request->evidence_path !== null,
            'rejection_reason' => $request->rejection_reason,
            'requested_by' => $request->requested_by,
            'created_at' => $request->created_at?->toIso8601String(),
        ];
    }
}
