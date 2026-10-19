<?php

namespace App\Modules\Timetable\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Timetable\Models\ClassSession;
use App\Modules\Timetable\Services\SessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API buổi học cụ thể (FR-TTB-005, 009): xem theo phạm vi lớp (TTB.view); sinh lại, dạy bù, hủy buổi = TTB toàn trường
 * (ACAD). Giảng viên đề xuất dạy bù (FR-TTB-008) ở P2.
 */
class ClassSessionController extends Controller
{
    public function __construct(
        private readonly SessionService $sessions,
        private readonly AccessControl $access,
    ) {}

    public function index(Request $request, CourseSection $courseSection): JsonResponse
    {
        abort_unless($this->access->allowsOn($request->user(), 'TTB', PermissionAction::View, $courseSection), 403, 'Bạn không có quyền thực hiện thao tác này.');

        $list = ClassSession::query()
            ->with('room:id,code')
            ->where('course_section_id', $courseSection->id)
            ->orderBy('date')->orderBy('period_from')
            ->get();

        return response()->json([
            'section' => $courseSection->only('id', 'code'),
            'total' => $list->count(),
            'counted' => $list->filter(fn (ClassSession $s) => $s->status->countsForAttendance())->count(),
            'needs_makeup' => $list->filter(fn (ClassSession $s) => in_array($s->status->value, ['off', 'cancelled'], true)
                && ! $list->contains('makeup_for_id', $s->id))->count(),
            'data' => $list->map(fn (ClassSession $s) => $this->present($s))->values(),
        ]);
    }

    public function generate(CourseSection $courseSection): JsonResponse
    {
        return response()->json($this->sessions->generate($courseSection));
    }

    public function makeup(Request $request, ClassSession $classSession): JsonResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
            'period_from' => ['required', 'integer', 'min:1'],
            'period_to' => ['required', 'integer', 'min:1'],
            'room_id' => ['sometimes', 'nullable', 'integer', 'exists:rooms,id'],
            'note' => ['sometimes', 'nullable', 'string', 'max:500'],
        ], [], ['date' => 'ngày dạy bù', 'period_from' => 'tiết bắt đầu', 'period_to' => 'tiết kết thúc', 'room_id' => 'phòng']);

        $session = $this->sessions->makeup($classSession, $data['date'], $data['period_from'], $data['period_to'], $data['room_id'] ?? null, $data['note'] ?? null);

        return response()->json($this->present($session->load('room')), 201);
    }

    public function cancel(Request $request, ClassSession $classSession): JsonResponse
    {
        $reason = $request->validate(['reason' => ['required', 'string', 'max:255']], [], ['reason' => 'lý do'])['reason'];

        return response()->json($this->present($this->sessions->cancel($classSession, $reason)->load('room')));
    }

    /** @return array<string, mixed> */
    private function present(ClassSession $session): array
    {
        return [
            'id' => $session->id,
            'date' => $session->date->toDateString(),
            'week' => $session->week,
            'period_from' => $session->period_from,
            'period_to' => $session->period_to,
            'room' => $session->room?->code,
            'status' => $session->status->value,
            'status_label' => $session->status->label(),
            'counts_for_attendance' => $session->status->countsForAttendance(),
            'off_reason' => $session->off_reason,
            'makeup_for_id' => $session->makeup_for_id,
            'note' => $session->note,
        ];
    }
}
