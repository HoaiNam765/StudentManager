<?php

namespace App\Modules\Student\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Student\Enums\StudentStatus;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Models\StudentStatusHistory;
use App\Modules\Student\Services\StudentStatusService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * API trạng thái sinh viên (FR-STU-009, 010): xem lịch sử = STU.view theo phạm vi; chuyển trạng thái = STU.update trên
 * hồ sơ; khôi phục từ trạng thái cuối (`special`) = STU.approve toàn trường (ACAD) (BR-STU-05).
 */
class StudentStatusController extends Controller
{
    public function __construct(
        private readonly StudentStatusService $statuses,
        private readonly AccessControl $access,
    ) {}

    public function index(Student $student): JsonResponse
    {
        Gate::authorize('view', $student);

        return response()->json([
            'status' => $student->status->value,
            'status_label' => $student->status->label(),
            'next' => array_map(fn (StudentStatus $s) => ['value' => $s->value, 'label' => $s->label()], $this->statuses->nextStatuses($student)),
            'can_restore' => $student->status->isFinal(),
            'history' => StudentStatusHistory::query()->where('student_id', $student->id)->orderByDesc('id')->get()
                ->map(fn (StudentStatusHistory $h) => $this->present($h)),
        ]);
    }

    public function store(Request $request, Student $student): JsonResponse
    {
        Gate::authorize('update', $student);
        $data = $request->validate([
            'status' => ['required', Rule::enum(StudentStatus::class)],
            'decision_no' => ['required', 'string', 'max:100'],
            'effective_date' => ['required', 'date_format:Y-m-d'],
            'reason' => ['required', 'string', 'max:1000'],
            'special' => ['sometimes', 'boolean'],
            'attachment' => ['sometimes', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:'.(int) config('studentmanager.student.decision_max_kb', 5120)],
        ], [], ['status' => 'trạng thái mới', 'decision_no' => 'số quyết định', 'effective_date' => 'ngày hiệu lực', 'reason' => 'lý do', 'attachment' => 'tệp quyết định']);

        if ($request->boolean('special')) {
            abort_unless($this->access->allowsGlobally($request->user(), 'STU', PermissionAction::Approve), 403, 'Chỉ Phòng Đào tạo được khôi phục trạng thái bằng quyết định đặc biệt.');
        }

        $history = $this->statuses->change($student, StudentStatus::from($data['status']), $data, $request->file('attachment'), $request->user());

        return response()->json($this->present($history), 201);
    }

    public function attachment(StudentStatusHistory $history): StreamedResponse
    {
        Gate::authorize('view', $history->student);
        abort_if($history->attachment_path === null, 404);

        return $this->statuses->attachmentDisk()->download($history->attachment_path, $history->attachment_name ?? 'quyet-dinh');
    }

    /** @return array<string, mixed> */
    private function present(StudentStatusHistory $h): array
    {
        return [
            'id' => $h->id,
            'from' => $h->from_status->label(),
            'to' => $h->to_status->label(),
            'to_status' => $h->to_status->value,
            'decision_no' => $h->decision_no,
            'effective_date' => $h->effective_date->toDateString(),
            'reason' => $h->reason,
            'special' => $h->special,
            'has_attachment' => $h->attachment_path !== null,
            'created_by' => $h->created_by,
            'created_at' => $h->created_at?->setTimezone(config('studentmanager.display_timezone'))->format('Y-m-d H:i'),
        ];
    }
}
