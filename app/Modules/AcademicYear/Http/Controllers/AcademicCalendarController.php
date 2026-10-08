<?php

namespace App\Modules\AcademicYear\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AcademicYear\Http\Requests\HolidayRequest;
use App\Modules\AcademicYear\Http\Requests\SaveMilestonesRequest;
use App\Modules\AcademicYear\Models\Holiday;
use App\Modules\AcademicYear\Models\MilestoneChangeRequest;
use App\Modules\AcademicYear\Models\MilestoneType;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\AcademicYear\Services\HolidayService;
use App\Modules\AcademicYear\Services\MilestoneService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * API lịch học vụ (các mốc) và ngày nghỉ (FR-ACY-004, 005, 007; BR-ACY-02, 05).
 * Quyền: middleware `permission:ACY.*` ở routes/admin.php (ACAD quản lý; các vai trò khác xem).
 */
class AcademicCalendarController extends Controller
{
    public function __construct(
        private readonly MilestoneService $milestones,
        private readonly HolidayService $holidays,
    ) {}

    public function milestones(Term $term): JsonResponse
    {
        return response()->json($this->presentCalendar($term));
    }

    /** Học kỳ chưa bắt đầu: lưu ngay (200). Đã bắt đầu: tạo đề nghị chờ duyệt (202). */
    public function saveMilestones(SaveMilestonesRequest $request, Term $term): JsonResponse
    {
        $result = $this->milestones->save($term, $request->input('milestones'), $request->user(), $request->input('reason'));

        return $result instanceof MilestoneChangeRequest
            ? response()->json(['message' => 'Học kỳ đã bắt đầu: đề nghị sửa lịch học vụ đang chờ duyệt.', 'request' => $this->presentRequest($result)], 202)
            : response()->json($this->presentCalendar($term));
    }

    /** Sao chép lịch học vụ từ học kỳ khác (FR-ACY-007). */
    public function copyMilestones(Request $request, Term $term): JsonResponse
    {
        $data = $request->validate(['from_term_id' => ['required', 'integer', 'exists:terms,id']], [], ['from_term_id' => 'học kỳ nguồn']);
        $this->milestones->copyFrom(Term::query()->findOrFail($data['from_term_id']), $term, $request->user());

        return response()->json($this->presentCalendar($term));
    }

    public function changeRequests(Request $request): JsonResponse
    {
        $requests = MilestoneChangeRequest::query()
            ->with(['term:id,name', 'requester:id,name', 'decider:id,name'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()))
            ->when($request->filled('term_id'), fn ($q) => $q->where('term_id', $request->integer('term_id')))
            ->latest('id')
            ->paginate(20);

        return response()->json($requests->through(fn (MilestoneChangeRequest $r) => $this->presentRequest($r)));
    }

    public function approve(Request $request, MilestoneChangeRequest $milestoneChangeRequest): JsonResponse
    {
        $note = $request->validate(['note' => ['nullable', 'string', 'max:1000']], [], ['note' => 'ghi chú'])['note'] ?? null;

        return response()->json($this->presentRequest($this->milestones->approve($milestoneChangeRequest, $request->user(), $note)));
    }

    public function reject(Request $request, MilestoneChangeRequest $milestoneChangeRequest): JsonResponse
    {
        $note = $request->validate(['note' => ['required', 'string', 'max:1000']], [], ['note' => 'lý do từ chối'])['note'];

        return response()->json($this->presentRequest($this->milestones->reject($milestoneChangeRequest, $request->user(), $note)));
    }

    public function holidays(Request $request): JsonResponse
    {
        $holidays = Holiday::query()
            ->when($request->filled('from') && $request->filled('to'), fn ($q) => $q->overlapping($request->string('from')->toString(), $request->string('to')->toString()))
            ->when($request->filled('term_id'), fn ($q) => $q->applicableTo($request->integer('term_id')))
            ->orderBy('starts_on')
            ->get();

        return response()->json($holidays->map(fn (Holiday $holiday) => $this->presentHoliday($holiday)));
    }

    /** Danh sách ngày nghỉ trong khoảng (TTB dùng để loại trừ buổi học). */
    public function daysOff(Request $request): JsonResponse
    {
        $data = $request->validate([
            'from' => ['required', 'date_format:Y-m-d'],
            'to' => ['required', 'date_format:Y-m-d', 'after_or_equal:from'],
            'term_id' => ['nullable', 'integer', 'exists:terms,id'],
        ], [], ['from' => 'từ ngày', 'to' => 'đến ngày', 'term_id' => 'học kỳ']);

        $term = isset($data['term_id']) ? Term::query()->find($data['term_id']) : null;

        return response()->json(['days_off' => $this->holidays->daysOff($data['from'], $data['to'], $term)]);
    }

    public function storeHoliday(HolidayRequest $request): JsonResponse
    {
        return response()->json($this->presentHoliday($this->holidays->create($request->validated())), 201);
    }

    public function updateHoliday(HolidayRequest $request, Holiday $holiday): JsonResponse
    {
        return response()->json($this->presentHoliday($this->holidays->update($holiday, $request->validated())));
    }

    public function destroyHoliday(Holiday $holiday): Response
    {
        $this->holidays->delete($holiday);

        return response()->noContent();
    }

    /** @return array<string, mixed> */
    private function presentCalendar(Term $term): array
    {
        $dates = $this->milestones->milestones($term);

        return [
            'term' => ['id' => $term->id, 'name' => $term->name, 'start_date' => $term->start_date->toDateString(), 'end_date' => $term->end_date->toDateString()],
            'started' => $this->milestones->hasStarted($term),
            'milestones' => array_map(fn (MilestoneType $type) => [
                'type' => $type->value,
                'label' => $type->label(),
                'date' => $dates[$type->value] ?? null,
            ], MilestoneType::cases()),
            'pending_requests' => MilestoneChangeRequest::query()->where('term_id', $term->id)->where('status', MilestoneChangeRequest::PENDING)->count(),
        ];
    }

    /** @return array<string, mixed> */
    private function presentRequest(MilestoneChangeRequest $request): array
    {
        return [
            'id' => $request->id,
            'term' => $request->term?->only('id', 'name'),
            'changes' => $request->changes,
            'reason' => $request->reason,
            'status' => $request->status,
            'requested_by' => $request->requester?->only('id', 'name'),
            'decided_by' => $request->decider?->only('id', 'name'),
            'decided_at' => $request->decided_at?->toIso8601String(),
            'decision_note' => $request->decision_note,
        ];
    }

    /** @return array<string, mixed> */
    private function presentHoliday(Holiday $holiday): array
    {
        return [
            'id' => $holiday->id,
            'name' => $holiday->name,
            'type' => $holiday->type->value,
            'type_label' => $holiday->type->label(),
            'starts_on' => $holiday->starts_on->toDateString(),
            'ends_on' => $holiday->ends_on->toDateString(),
            'term_id' => $holiday->term_id,
            'note' => $holiday->note,
        ];
    }
}
