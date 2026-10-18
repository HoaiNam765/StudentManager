<?php

namespace App\Modules\Timetable\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Timetable\Enums\Shift;
use App\Modules\Timetable\Models\Period;
use App\Modules\Timetable\Services\PeriodService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

/** API khung tiết học (FR-TTB-001): xem = TTB.view; thêm, sửa, xóa = ACAD (TTB toàn trường). */
class PeriodController extends Controller
{
    public function __construct(private readonly PeriodService $periods) {}

    /** Không gửi `campus_id`: khung chung; có `campus_id`: khung áp dụng cho cơ sở đó (riêng nếu có, không thì chung). */
    public function index(Request $request): JsonResponse
    {
        $campusId = $request->filled('campus_id') ? $request->integer('campus_id') : null;

        return response()->json([
            'latest_end' => config('studentmanager.timetable.latest_end'),
            'data' => $this->periods->periodsFor($campusId)->values()->map(fn (Period $p) => $this->present($p)),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        return response()->json($this->present($this->periods->create($this->validated($request, true))), 201);
    }

    public function update(Request $request, Period $period): JsonResponse
    {
        return response()->json($this->present($this->periods->update($period, $this->validated($request, false))));
    }

    public function destroy(Period $period): Response
    {
        $this->periods->delete($period);

        return response()->noContent();
    }

    private function validated(Request $request, bool $creating): array
    {
        $required = $creating ? 'required' : 'sometimes';

        return $request->validate([
            'campus_id' => ['sometimes', 'nullable', 'integer', 'exists:campuses,id'],
            'number' => [$required, 'integer', 'min:1', 'max:30'],
            'starts_at' => [$required, 'date_format:H:i'],
            'ends_at' => [$required, 'date_format:H:i'],
            'shift' => [$required, Rule::enum(Shift::class)],
        ], [], ['campus_id' => 'cơ sở', 'number' => 'số tiết', 'starts_at' => 'giờ bắt đầu', 'ends_at' => 'giờ kết thúc', 'shift' => 'ca']);
    }

    /** @return array<string, mixed> */
    private function present(Period $period): array
    {
        return [
            'id' => $period->id,
            'campus_id' => $period->campus_id,
            'number' => $period->number,
            'starts_at' => $period->startTime(),
            'ends_at' => $period->endTime(),
            'shift' => $period->shift->value,
            'shift_label' => $period->shift->label(),
        ];
    }
}
