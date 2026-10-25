<?php

namespace App\Modules\Attendance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Attendance\Models\AttendanceChangeRequest;
use App\Modules\Attendance\Models\AttendanceRecord;
use App\Modules\Attendance\Models\AttendanceSummary;
use App\Modules\Attendance\Services\AttendanceReport;
use App\Modules\Attendance\Services\AttendanceService;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Support\Exports\ExportColumn;
use App\Support\Services\ExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Quản trị điểm danh (#143, #144; FR-ATT-005, 009, 010, 013): tổng hợp chuyên cần toàn trường theo phạm vi, xuất Excel,
 * yêu cầu sửa quá hạn (duyệt / từ chối), khóa / mở khóa sổ, miễn cấm thi có lý do.
 */
class AttendanceAdminController extends Controller
{
    public function __construct(
        private readonly AttendanceService $attendance,
        private readonly AttendanceReport $report,
        private readonly AccessControl $access,
    ) {}

    public function summaries(Request $request): JsonResponse
    {
        $filters = $this->filters($request);
        $page = $this->report->scoped($request->user(), $filters)->paginate(min(100, max(1, $request->integer('per_page', 50))))->withQueryString();
        $counts = $this->report->scoped($request->user(), array_diff_key($filters, ['flag' => 1]))
            ->toBase()->reorder()->selectRaw('count(*) as total, sum(warning and not banned) as warning, sum(banned) as banned, avg(absence_rate) as avg_rate')->first();

        return response()->json($page->through(fn (AttendanceSummary $s) => AttendanceReport::present($s))->toArray() + [
            'counts' => ['total' => (int) ($counts->total ?? 0), 'warning' => (int) ($counts->warning ?? 0), 'banned' => (int) ($counts->banned ?? 0), 'avg_rate' => round((float) ($counts->avg_rate ?? 0), 1)],
        ]);
    }

    public function export(Request $request, ExportService $exports): Response
    {
        $request->validate(['format' => ['sometimes', 'in:xlsx,csv,pdf']]);
        $result = $exports->export($this->report->summaries($this->filters($request)), [
            new ExportColumn('student.student_code', 'MSSV'),
            new ExportColumn('student.full_name', 'Họ và tên'),
            new ExportColumn('section.code', 'Lớp học phần'),
            new ExportColumn('section.subject.name', 'Học phần'),
            new ExportColumn('total_sessions', 'Số buổi'),
            new ExportColumn('present_count', 'Có mặt'),
            new ExportColumn('absent_count', 'Vắng không phép'),
            new ExportColumn('excused_count', 'Vắng có phép'),
            new ExportColumn('late_count', 'Đi muộn'),
            new ExportColumn('absence_rate', 'Tỷ lệ vắng (%)'),
            new ExportColumn('attendance_score', 'Điểm chuyên cần'),
        ], 'ATT', $request->user(), $request->string('format', 'xlsx')->toString(), 'chuyen-can');

        return $result->isQueued()
            ? response()->json(['id' => $result->requestId, 'row_count' => $result->rowCount], 202)
            : $result->download;
    }

    public function changeRequests(Request $request): JsonResponse
    {
        $list = AttendanceChangeRequest::query()
            ->whereIn('attendance_record_id', $this->access->constrain(AttendanceRecord::query()->select('attendance_records.id'), $request->user(), 'ATT', PermissionAction::View))
            ->where('status', $request->string('status', AttendanceChangeRequest::PENDING)->toString())
            ->with(['record.student:id,student_code,full_name', 'record.section:id,code', 'record.session:id,date,period_from,period_to', 'requester:id,name'])
            ->orderBy('id')->paginate(50);

        return response()->json($list->through(fn (AttendanceChangeRequest $r) => [
            'id' => $r->id,
            'student' => $r->record?->student?->only('id', 'student_code', 'full_name'),
            'section' => $r->record?->section?->only('id', 'code'),
            'session' => $r->record?->session === null ? null : ['date' => $r->record->session->date->toDateString(), 'periods' => "{$r->record->session->period_from}-{$r->record->session->period_to}"],
            'from_label' => $r->from_status->label(),
            'to_label' => $r->to_status->label(),
            'minutes_late' => $r->minutes_late,
            'reason' => $r->reason,
            'requested_by' => $r->requester?->name,
            'status' => $r->status,
            'status_label' => AttendanceChangeRequest::STATUS_LABELS[$r->status] ?? $r->status,
            'decision_note' => $r->decision_note,
            'created_at' => $r->created_at?->setTimezone(config('studentmanager.display_timezone'))->format('Y-m-d H:i'),
        ]));
    }

    public function approve(Request $request, AttendanceChangeRequest $attendanceChangeRequest): JsonResponse
    {
        $note = $request->validate(['note' => ['nullable', 'string', 'max:1000']])['note'] ?? null;
        $this->attendance->decideChange($attendanceChangeRequest, $request->user(), true, $note);

        return response()->json(['message' => 'Đã duyệt yêu cầu sửa điểm danh.']);
    }

    public function reject(Request $request, AttendanceChangeRequest $attendanceChangeRequest): JsonResponse
    {
        $note = $request->validate(['note' => ['required', 'string', 'max:1000']], [], ['note' => 'lý do từ chối'])['note'];
        $this->attendance->decideChange($attendanceChangeRequest, $request->user(), false, $note);

        return response()->json(['message' => 'Đã từ chối yêu cầu sửa điểm danh.']);
    }

    public function lock(Request $request, CourseSection $courseSection): JsonResponse
    {
        $this->attendance->lock($courseSection, $request->user());

        return response()->json(['message' => "Đã khóa sổ điểm danh lớp {$courseSection->code}.", 'locked' => true]);
    }

    public function unlock(Request $request, CourseSection $courseSection): JsonResponse
    {
        $reason = $request->validate(['reason' => ['required', 'string', 'max:500']], [], ['reason' => 'lý do mở khóa'])['reason'];
        $this->attendance->unlock($courseSection, $request->user(), $reason);

        return response()->json(['message' => "Đã mở khóa sổ điểm danh lớp {$courseSection->code}.", 'locked' => false]);
    }

    public function exempt(Request $request, AttendanceSummary $attendanceSummary): JsonResponse
    {
        $data = $request->validate([
            'exempted' => ['required', 'boolean'],
            'reason' => ['nullable', 'string', 'max:500'],
        ], [], ['exempted' => 'miễn cấm thi', 'reason' => 'lý do']);
        $summary = $this->attendance->exempt($attendanceSummary, $request->user(), (bool) $data['exempted'], $data['reason'] ?? null);

        return response()->json(AttendanceReport::present($summary->load('student', 'section.subject')));
    }

    /** @return array<string, mixed> */
    private function filters(Request $request): array
    {
        return $request->validate([
            'term_id' => ['nullable', 'integer'],
            'course_section_id' => ['nullable', 'integer'],
            'admin_class_id' => ['nullable', 'integer'],
            'faculty_id' => ['nullable', 'integer'],
            'flag' => ['nullable', 'in:warning,banned'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);
    }
}
