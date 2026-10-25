<?php

namespace App\Modules\Attendance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Attendance\Enums\AttendanceStatus;
use App\Modules\Attendance\Models\AttendanceRecord;
use App\Modules\Attendance\Services\AttendanceReport;
use App\Modules\Attendance\Services\AttendanceService;
use App\Modules\Grade\Services\GradebookService;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Timetable\Models\ClassSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Điểm danh theo buổi (#142, #144): danh sách buổi của lớp, phiếu điểm danh, ghi / sửa, yêu cầu sửa quá hạn, sổ đầu bài,
 * tổng hợp chuyên cần lớp, chuyển điểm chuyên cần sang sổ điểm. Dùng cho cổng giảng viên và quản trị (cùng quy tắc ở
 * AttendanceService: chỉ giảng viên được phân công ghi; Phòng Đào tạo điều chỉnh có lý do).
 */
class AttendanceController extends Controller
{
    public function __construct(
        private readonly AttendanceService $attendance,
        private readonly AttendanceReport $report,
    ) {}

    public function sessions(Request $request, CourseSection $courseSection): JsonResponse
    {
        return response()->json([
            'section' => $courseSection->only('id', 'code', 'max_size') + ['subject' => $courseSection->subject?->only('id', 'code', 'name')],
            'locked' => $this->attendance->isLocked($courseSection),
            'sessions' => $this->attendance->sessions($courseSection, $request->user()),
        ]);
    }

    public function sheet(Request $request, ClassSession $classSession): JsonResponse
    {
        return response()->json($this->attendance->sheet($classSession, $request->user()));
    }

    public function record(Request $request, ClassSession $classSession): JsonResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.enrollment_id' => ['required', 'integer', 'distinct'],
            'items.*.status' => ['required', Rule::in(AttendanceStatus::values())],
            'items.*.minutes_late' => ['nullable', 'integer', 'min:0', 'max:300'],
            'items.*.note' => ['nullable', 'string', 'max:500'],
            'reason' => ['nullable', 'string', 'max:500'],
        ], [], ['items' => 'danh sách điểm danh', 'items.*.status' => 'trạng thái', 'items.*.minutes_late' => 'số phút đi muộn', 'reason' => 'lý do']);

        $count = $this->attendance->record($classSession, $request->user(), $data['items'], $data['reason'] ?? null);

        return response()->json(['message' => "Đã lưu điểm danh ({$count} thay đổi).", 'changed' => $count] + $this->attendance->sheet($classSession, $request->user()));
    }

    public function requestChange(Request $request, AttendanceRecord $attendanceRecord): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(AttendanceStatus::values())],
            'minutes_late' => ['nullable', 'integer', 'min:0', 'max:300'],
            'reason' => ['required', 'string', 'max:1000'],
        ], [], ['status' => 'trạng thái đề nghị', 'minutes_late' => 'số phút đi muộn', 'reason' => 'lý do']);

        $change = $this->attendance->requestChange($attendanceRecord, $request->user(), $data['status'], $data['minutes_late'] ?? null, $data['reason']);

        return response()->json(['id' => $change->id, 'message' => 'Đã gửi yêu cầu sửa điểm danh cho Phòng Đào tạo.'], 201);
    }

    public function teachingLog(Request $request, ClassSession $classSession): JsonResponse
    {
        $data = $request->validate([
            'periods' => ['required', 'integer', 'min:1', 'max:20'],
            'content' => ['required', 'string', 'max:2000'],
        ], [], ['periods' => 'số tiết thực dạy', 'content' => 'nội dung buổi dạy']);

        $log = $this->attendance->confirmTeaching($classSession, $request->user(), $data['periods'], $data['content']);

        return response()->json($log->only('class_session_id', 'periods', 'content') + ['message' => 'Đã ghi sổ đầu bài.']);
    }

    /** Tổng hợp chuyên cần từng sinh viên của lớp (FR-ATT-010). */
    public function summary(Request $request, CourseSection $courseSection): JsonResponse
    {
        $this->attendance->authorizeView($courseSection, $request->user());

        return response()->json([
            'section' => $courseSection->only('id', 'code'),
            'locked' => $this->attendance->isLocked($courseSection),
            'data' => $this->report->scoped($request->user(), ['course_section_id' => $courseSection->id])->get()
                ->sortBy(fn ($s) => $s->student?->student_code)->values()->map(fn ($s) => AttendanceReport::present($s)),
        ]);
    }

    public function pushScores(Request $request, CourseSection $courseSection, GradebookService $gradebook): JsonResponse
    {
        $count = $this->attendance->pushScores($courseSection, $request->user(), $gradebook);

        return response()->json(['message' => "Đã chuyển {$count} điểm chuyên cần vào sổ điểm (bản nháp).", 'saved_count' => $count]);
    }
}
