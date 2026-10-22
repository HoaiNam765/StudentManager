<?php

namespace App\Modules\Grade\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Grade\Enums\GradePublicationStatus;
use App\Modules\Grade\Models\SectionGradeWorkflow;
use App\Modules\Grade\Services\GradeWorkflowService;
use App\Modules\SchoolClass\Models\CourseSection;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * API duyệt và công bố điểm (FR-GRD-004, 005). Xem theo phạm vi GRD.view của lớp (giảng viên lớp mình dạy, khoa lớp của
 * khoa); quyền từng bước kiểm tra trong GradeWorkflowService.
 */
class GradeWorkflowController extends Controller
{
    public function __construct(
        private readonly GradeWorkflowService $workflow,
        private readonly AccessControl $access,
    ) {}

    /** Danh sách bảng điểm các lớp trong phạm vi: lọc `term_id`, `status` (lớp chưa có bảng điểm coi là Nháp). */
    public function index(Request $request): JsonResponse
    {
        $sections = $this->access->constrain(CourseSection::query(), $request->user(), 'GRD', PermissionAction::View)
            ->with(['subject:id,code,name', 'term:id,name'])
            ->when($request->filled('term_id'), fn ($q) => $q->where('term_id', $request->integer('term_id')))
            ->whereIn('status', ['in_progress', 'finished', 'locked'])
            ->orderBy('code')
            ->get();
        $workflows = SectionGradeWorkflow::query()->whereIn('course_section_id', $sections->pluck('id'))->get()->keyBy('course_section_id');

        $rows = $sections->map(function (CourseSection $s) use ($workflows): array {
            $workflow = $workflows->get($s->id);
            $status = $workflow?->status ?? GradePublicationStatus::Draft;

            return [
                'id' => $s->id,
                'code' => $s->code,
                'subject' => $s->subject?->only('code', 'name'),
                'term' => $s->term?->name,
                'status' => $status->value,
                'status_label' => $status->label(),
                'scheduled_at' => $workflow?->scheduled_at?->setTimezone(config('studentmanager.display_timezone'))->format('Y-m-d H:i'),
                'return_reason' => $workflow?->return_reason,
            ];
        })->when($request->filled('status'), fn ($c) => $c->where('status', $request->string('status')->toString()))->values();

        return response()->json(['data' => $rows]);
    }

    public function show(Request $request, CourseSection $courseSection): JsonResponse
    {
        abort_unless($this->access->allowsOn($request->user(), 'GRD', PermissionAction::View, $courseSection), 403, 'Bạn không có quyền xem bảng điểm lớp này.');

        return response()->json($this->present($this->workflow->current($courseSection)));
    }

    public function change(Request $request, CourseSection $courseSection): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::enum(GradePublicationStatus::class)],
            'reason' => ['nullable', 'string', 'max:2000'],
            'scheduled_at' => ['nullable', 'date_format:Y-m-d H:i'],
        ], [], ['status' => 'trạng thái', 'reason' => 'lý do', 'scheduled_at' => 'giờ hẹn công bố']);

        $schedule = isset($data['scheduled_at']) ? CarbonImmutable::parse($data['scheduled_at'], config('studentmanager.display_timezone'))->toIso8601String() : null;
        $workflow = $this->workflow->change($courseSection, $request->user(), GradePublicationStatus::from($data['status']), $data['reason'] ?? null, $schedule);

        return response()->json(['message' => 'Đã cập nhật trạng thái bảng điểm.', 'workflow' => $this->present($workflow)]);
    }

    /** @return array<string, mixed> */
    private function present(SectionGradeWorkflow $workflow): array
    {
        $tz = config('studentmanager.display_timezone');

        return [
            'course_section_id' => $workflow->course_section_id,
            'status' => $workflow->status->value,
            'status_label' => $workflow->status->label(),
            'return_reason' => $workflow->return_reason,
            'scheduled_at' => $workflow->scheduled_at?->setTimezone($tz)->format('Y-m-d H:i'),
            'published_at' => $workflow->published_at?->setTimezone($tz)->format('Y-m-d H:i'),
            'next' => array_values(array_map(fn (GradePublicationStatus $s) => ['value' => $s->value, 'label' => $s->label()],
                array_filter(GradePublicationStatus::cases(), fn (GradePublicationStatus $s) => $workflow->status->allows($s, (bool) config('grade_lifecycle.faculty_approval_required', true))))),
        ];
    }
}
