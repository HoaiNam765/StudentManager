<?php

namespace App\Modules\Curriculum\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Curriculum\Models\Curriculum;
use App\Modules\Curriculum\Services\CurriculumVersionService;
use App\Modules\Curriculum\Services\GraduationRequirementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Phiên bản CTĐT (FR-CUR-005) và điều kiện tốt nghiệp (FR-CUR-004). Xem = CUR.view; tạo phiên bản = CUR.create;
 * sửa điều kiện tốt nghiệp của bản nháp = CUR.update; phạm vi FACULTY (DEAN) kiểm tra trên CTĐT.
 */
class CurriculumVersionController extends Controller
{
    public function __construct(
        private readonly CurriculumVersionService $versions,
        private readonly GraduationRequirementService $graduation,
    ) {}

    public function index(Curriculum $curriculum): JsonResponse
    {
        Gate::authorize('view', $curriculum);

        return response()->json($this->versions->versionsOf($curriculum)->map(fn (Curriculum $c) => [
            'id' => $c->id,
            'version' => $c->version,
            'based_on_id' => $c->based_on_id,
            'cohort_from' => $c->cohort_from,
            'cohort_to' => $c->cohort_to,
            'effective_from' => $c->effective_from?->toDateString(),
            'status' => $c->status->value,
            'status_label' => $c->status->label(),
        ]));
    }

    public function store(Request $request, Curriculum $curriculum): JsonResponse
    {
        Gate::authorize('update', $curriculum);

        $data = $request->validate([
            'cohort_from' => ['required', 'integer', 'between:2000,2100'],
            'effective_from' => ['nullable', 'date_format:Y-m-d'],
        ], [], ['cohort_from' => 'khóa áp dụng từ', 'effective_from' => 'ngày hiệu lực']);

        $copy = $this->versions->newVersion($curriculum, (int) $data['cohort_from'], $data['effective_from'] ?? null);

        return response()->json([
            'id' => $copy->id,
            'code' => $copy->code,
            'version' => $copy->version,
            'cohort_from' => $copy->cohort_from,
            'status' => $copy->status->value,
        ], 201);
    }

    /** Điều kiện tốt nghiệp đã lưu (hoặc mặc định) và giá trị áp dụng cho khóa `cohort` (mặc định khóa bắt đầu). */
    public function showRequirements(Request $request, Curriculum $curriculum): JsonResponse
    {
        Gate::authorize('view', $curriculum);
        $cohort = $request->integer('cohort', $curriculum->cohort_from);
        $requirement = $this->graduation->requirementFor($curriculum);

        return response()->json([
            'stored' => $requirement->exists,
            'requirement' => $requirement->only([
                'min_credits', 'min_cgpa', 'foreign_language_standard', 'it_standard', 'requires_internship',
                'requires_thesis', 'allows_thesis_alternative', 'no_active_suspension', 'notes',
            ]),
            'effective' => ['cohort' => $cohort] + $this->graduation->effectiveFor($curriculum, $cohort),
        ]);
    }

    public function updateRequirements(Request $request, Curriculum $curriculum): JsonResponse
    {
        Gate::authorize('update', $curriculum);

        $data = $request->validate([
            'min_credits' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:400'],
            'min_cgpa' => ['sometimes', 'nullable', 'numeric', 'between:0,4'],
            'foreign_language_standard' => ['sometimes', 'nullable', 'string', 'max:255'],
            'it_standard' => ['sometimes', 'nullable', 'string', 'max:255'],
            'requires_internship' => ['sometimes', 'boolean'],
            'requires_thesis' => ['sometimes', 'boolean'],
            'allows_thesis_alternative' => ['sometimes', 'boolean'],
            'no_active_suspension' => ['sometimes', 'boolean'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:5000'],
        ], [], [
            'min_credits' => 'số tín chỉ tối thiểu', 'min_cgpa' => 'CGPA tối thiểu', 'foreign_language_standard' => 'chuẩn ngoại ngữ',
            'it_standard' => 'chuẩn tin học', 'requires_internship' => 'thực tập', 'requires_thesis' => 'khóa luận',
        ]);

        $this->graduation->save($curriculum, $data);

        return $this->showRequirements($request, $curriculum->refresh());
    }
}
