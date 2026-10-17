<?php

namespace App\Modules\Subject\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Subject\Enums\ComponentKind;
use App\Modules\Subject\Models\GradeComponentTemplate;
use App\Modules\Subject\Models\Subject;
use App\Modules\Subject\Services\GradeStructureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * API cơ cấu điểm của học phần (FR-SUB-005; BR-SUB-04). Xem = SUB.view; lưu (thay toàn bộ) = SUB.update trên học phần
 * (ACAD toàn trường, DEAN học phần của bộ môn mình).
 */
class GradeStructureController extends Controller
{
    public function __construct(private readonly GradeStructureService $structures) {}

    public function show(Subject $subject): JsonResponse
    {
        Gate::authorize('view', $subject);

        return response()->json($this->present($subject));
    }

    public function update(Request $request, Subject $subject): JsonResponse
    {
        Gate::authorize('update', $subject);

        $data = $request->validate([
            'components' => ['required', 'array', 'max:20'],
            'components.*.name' => ['required', 'string', 'max:100'],
            'components.*.kind' => ['required', Rule::enum(ComponentKind::class)],
            'components.*.weight' => ['required', 'numeric', 'min:0', 'max:100'],
            'components.*.min_score' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'components.*.is_required' => ['sometimes', 'boolean'],
            'components.*.is_remote' => ['sometimes', 'boolean'],
        ], [], [
            'components' => 'thành phần điểm',
            'components.*.name' => 'tên thành phần',
            'components.*.kind' => 'loại thành phần',
            'components.*.weight' => 'trọng số',
            'components.*.min_score' => 'điểm sàn',
            'components.*.is_required' => 'bắt buộc',
            'components.*.is_remote' => 'đánh giá từ xa',
        ]);

        $this->structures->replace($subject, $data['components']);

        return response()->json($this->present($subject));
    }

    /** @return array<string, mixed> */
    private function present(Subject $subject): array
    {
        return [
            'subject' => $subject->only('id', 'code', 'name'),
            'rules' => $this->structures->rules(),
            'components' => $this->structures->structureFor($subject)->map(fn (GradeComponentTemplate $c) => $c->toComponent() + [
                'id' => $c->id,
                'kind_label' => $c->kind->label(),
            ]),
        ];
    }
}
