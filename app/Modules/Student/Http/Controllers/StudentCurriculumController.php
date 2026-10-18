<?php

namespace App\Modules\Student\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Curriculum\Models\Curriculum;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Models\StudentCurriculum;
use App\Modules\Student\Services\StudentCurriculumService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/** API gán CTĐT cho sinh viên (FR-CUR-006): lịch sử, gán theo ngành – khóa, đổi CTĐT từ một ngày. */
class StudentCurriculumController extends Controller
{
    public function __construct(private readonly StudentCurriculumService $curricula) {}

    public function index(Student $student): JsonResponse
    {
        Gate::authorize('view', $student);

        return response()->json([
            'current' => $student->curriculum?->only('id', 'code', 'name', 'version'),
            'history' => StudentCurriculum::query()
                ->with('curriculum:id,code,name,version')
                ->where('student_id', $student->id)
                ->orderByDesc('effective_from')
                ->get()
                ->map(fn (StudentCurriculum $a) => $this->present($a)),
        ]);
    }

    /** Không gửi `curriculum_id` thì gán CTĐT đang áp dụng cho ngành – khóa – hệ của sinh viên. */
    public function store(Request $request, Student $student): JsonResponse
    {
        Gate::authorize('update', $student);
        $data = $request->validate([
            'curriculum_id' => ['sometimes', 'nullable', 'integer', 'exists:curricula,id'],
            'effective_from' => ['sometimes', 'nullable', 'date_format:Y-m-d'],
            'reason' => ['sometimes', 'nullable', 'string', 'max:500'],
        ], [], ['curriculum_id' => 'CTĐT', 'effective_from' => 'ngày hiệu lực', 'reason' => 'lý do']);

        $assignment = empty($data['curriculum_id'])
            ? $this->curricula->assignDefault($student, $data['effective_from'] ?? null)
            : $this->curricula->change($student, Curriculum::query()->findOrFail($data['curriculum_id']), $data['effective_from'] ?? null, $data['reason'] ?? null);

        return response()->json($this->present($assignment->load('curriculum')), 201);
    }

    /** @return array<string, mixed> */
    private function present(StudentCurriculum $assignment): array
    {
        return [
            'id' => $assignment->id,
            'curriculum' => $assignment->curriculum?->only('id', 'code', 'name', 'version'),
            'effective_from' => $assignment->effective_from->toDateString(),
            'effective_to' => $assignment->effective_to?->toDateString(),
            'reason' => $assignment->reason,
        ];
    }
}
