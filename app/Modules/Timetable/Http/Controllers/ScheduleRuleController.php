<?php

namespace App\Modules\Timetable\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Timetable\Enums\WeekParity;
use App\Modules\Timetable\Models\ScheduleRule;
use App\Modules\Timetable\Services\ScheduleConflictService;
use App\Modules\Timetable\Services\ScheduleRuleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

/**
 * API mẫu lịch lớp học phần (FR-TTB-002). Xem = TTB.view theo phạm vi lớp (DEAN khoa mình, LEC lớp mình dạy);
 * thêm / sửa / xóa cần TTB.create / update / delete toàn trường (ACAD): chữ C của DEAN, LEC ở ma trận TTB là đề xuất
 * chờ ACAD duyệt (BA 4.2 ghi chú 3, dạy bù ở FR-TTB-008), không phải tự xếp lịch.
 */
class ScheduleRuleController extends Controller
{
    public function __construct(
        private readonly ScheduleRuleService $rules,
        private readonly AccessControl $access,
    ) {}

    public function index(Request $request, CourseSection $courseSection): JsonResponse
    {
        $this->authorizeOn($request, PermissionAction::View, $courseSection);
        $summary = $this->rules->summary($courseSection);

        return response()->json([
            'section' => $courseSection->only('id', 'code', 'max_size', 'delivery_mode'),
            'required_periods' => $summary['required'],
            'scheduled_periods' => $summary['scheduled'],
            'warnings' => $summary['warnings'],
            'data' => ScheduleRule::query()
                ->with('room:id,code,capacity')
                ->where('course_section_id', $courseSection->id)
                ->orderBy('day_of_week')->orderBy('period_from')
                ->get()
                ->map(fn (ScheduleRule $r) => $this->present($r, $courseSection)),
        ]);
    }

    /** Xung đột hiện có của lớp với các lớp khác cùng học kỳ (FR-TTB-003). */
    public function conflicts(Request $request, CourseSection $courseSection, ScheduleConflictService $conflicts): JsonResponse
    {
        $this->authorizeOn($request, PermissionAction::View, $courseSection);

        return response()->json($conflicts->sectionConflicts($courseSection));
    }

    public function store(Request $request, CourseSection $courseSection): JsonResponse
    {
        $this->authorizeOn($request, PermissionAction::Create, $courseSection);
        $result = $this->rules->create($courseSection, $this->validated($request, true));

        return response()->json(['data' => $this->present($result['rule'], $courseSection), 'warnings' => $result['warnings']], 201);
    }

    public function update(Request $request, ScheduleRule $scheduleRule): JsonResponse
    {
        $this->authorizeOn($request, PermissionAction::Update, $scheduleRule->section);
        $result = $this->rules->update($scheduleRule, $this->validated($request, false));

        return response()->json(['data' => $this->present($result['rule'], $scheduleRule->section), 'warnings' => $result['warnings']]);
    }

    public function destroy(Request $request, ScheduleRule $scheduleRule): Response
    {
        $this->authorizeOn($request, PermissionAction::Delete, $scheduleRule->section);
        $this->rules->delete($scheduleRule);

        return response()->noContent();
    }

    private function authorizeOn(Request $request, PermissionAction $action, CourseSection $section): void
    {
        $allowed = $action === PermissionAction::View
            ? $this->access->allowsOn($request->user(), 'TTB', $action, $section)
            : $this->access->allowsGlobally($request->user(), 'TTB', $action);

        abort_unless($allowed, 403, 'Bạn không có quyền thực hiện thao tác này.');
    }

    private function validated(Request $request, bool $creating): array
    {
        $required = $creating ? 'required' : 'sometimes';

        return $request->validate([
            'day_of_week' => [$required, 'integer', 'between:1,7'],
            'period_from' => [$required, 'integer', 'min:1'],
            'period_to' => [$required, 'integer', 'min:1'],
            'room_id' => ['sometimes', 'nullable', 'integer', 'exists:rooms,id'],
            'week_from' => [$required, 'integer', 'min:1'],
            'week_to' => [$required, 'integer', 'min:1'],
            'week_parity' => ['sometimes', Rule::enum(WeekParity::class)],
            'note' => ['sometimes', 'nullable', 'string', 'max:500'],
        ], [], [
            'day_of_week' => 'thứ', 'period_from' => 'tiết bắt đầu', 'period_to' => 'tiết kết thúc', 'room_id' => 'phòng',
            'week_from' => 'tuần bắt đầu', 'week_to' => 'tuần kết thúc', 'week_parity' => 'mẫu tuần', 'note' => 'ghi chú',
        ]);
    }

    /** @return array<string, mixed> */
    private function present(ScheduleRule $rule, CourseSection $section): array
    {
        $section->loadMissing('term');
        $weeks = $rule->weeks();

        return [
            'id' => $rule->id,
            'day_of_week' => $rule->day_of_week,
            'day_label' => ScheduleRule::DAYS[$rule->day_of_week],
            'period_from' => $rule->period_from,
            'period_to' => $rule->period_to,
            'room' => $rule->room?->only('id', 'code', 'capacity'),
            'week_from' => $rule->week_from,
            'week_to' => $rule->week_to,
            'week_parity' => $rule->week_parity->value,
            'session_count' => count($weeks),
            'first_date' => $this->rules->dateOf($section->term, $weeks[0], $rule->day_of_week)->toDateString(),
            'last_date' => $this->rules->dateOf($section->term, end($weeks), $rule->day_of_week)->toDateString(),
            'description' => $rule->describe(),
            'note' => $rule->note,
        ];
    }
}
