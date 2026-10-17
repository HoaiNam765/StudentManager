<?php

namespace App\Modules\Subject\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Subject\Enums\RelationType;
use App\Modules\Subject\Models\Subject;
use App\Modules\Subject\Models\SubjectRelation;
use App\Modules\Subject\Services\SubjectRelationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * API quan hệ giữa các học phần (FR-SUB-004). Xem = SUB.view trên học phần; thêm, kết thúc, xóa = SUB.update trên
 * học phần `subject_id` của quan hệ (DEAN chỉ với học phần thuộc bộ môn mình).
 */
class SubjectRelationController extends Controller
{
    public function __construct(private readonly SubjectRelationService $relations) {}

    /** Điều kiện của học phần, học phần nhận nó làm điều kiện, và tương đương. */
    public function index(Subject $subject): JsonResponse
    {
        Gate::authorize('view', $subject);

        $requires = SubjectRelation::query()->where('subject_id', $subject->id)->with('relatedSubject')->get();
        $requiredBy = SubjectRelation::query()
            ->where('related_subject_id', $subject->id)
            ->where('type', '!=', RelationType::Equivalent->value)
            ->with('subject')
            ->get();
        $equivalentOf = SubjectRelation::query()
            ->where('related_subject_id', $subject->id)
            ->where('type', RelationType::Equivalent->value)
            ->with('subject')
            ->get();

        return response()->json([
            'requires' => $requires->where('type', '!=', RelationType::Equivalent)->values()->map(fn (SubjectRelation $r) => $this->present($r, $r->relatedSubject)),
            'required_by' => $requiredBy->map(fn (SubjectRelation $r) => $this->present($r, $r->subject)),
            'equivalents' => $requires->where('type', RelationType::Equivalent)->values()->map(fn (SubjectRelation $r) => $this->present($r, $r->relatedSubject))
                ->merge($equivalentOf->map(fn (SubjectRelation $r) => $this->present($r, $r->subject))),
        ]);
    }

    /** Toàn bộ học phần phải học trước (kể cả gián tiếp). */
    public function prerequisites(Subject $subject): JsonResponse
    {
        Gate::authorize('view', $subject);

        return response()->json($this->relations->allPrerequisites($subject)->map(fn (Subject $s) => $s->only('id', 'code', 'name', 'credits')));
    }

    public function store(Request $request, Subject $subject): JsonResponse
    {
        Gate::authorize('update', $subject);

        $data = $request->validate([
            'related_subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'type' => ['required', Rule::enum(RelationType::class)],
            'effective_from' => ['nullable', 'date_format:Y-m-d'],
            'effective_to' => ['nullable', 'date_format:Y-m-d'],
            'decision_no' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:500'],
        ], [], [
            'related_subject_id' => 'học phần liên quan', 'type' => 'loại quan hệ', 'effective_from' => 'ngày hiệu lực',
            'effective_to' => 'ngày kết thúc', 'decision_no' => 'số quyết định', 'note' => 'ghi chú',
        ]);

        $relation = $this->relations->add(
            $subject,
            Subject::query()->findOrFail($data['related_subject_id']),
            RelationType::from($data['type']),
            $data,
        );

        return response()->json($this->present($relation, $relation->relatedSubject), 201);
    }

    public function end(Request $request, SubjectRelation $subjectRelation): JsonResponse
    {
        Gate::authorize('update', $subjectRelation->subject);
        $endsOn = $request->validate(['effective_to' => ['required', 'date_format:Y-m-d']], [], ['effective_to' => 'ngày kết thúc'])['effective_to'];

        $relation = $this->relations->end($subjectRelation, $endsOn);

        return response()->json($this->present($relation, $relation->relatedSubject));
    }

    public function destroy(SubjectRelation $subjectRelation): Response
    {
        Gate::authorize('update', $subjectRelation->subject);
        $this->relations->remove($subjectRelation);

        return response()->noContent();
    }

    /** @return array<string, mixed> */
    private function present(SubjectRelation $relation, ?Subject $other): array
    {
        return [
            'id' => $relation->id,
            'type' => $relation->type->value,
            'type_label' => $relation->type->label(),
            'subject_id' => $relation->subject_id,
            'related_subject_id' => $relation->related_subject_id,
            'subject' => $other?->only('id', 'code', 'name', 'credits'),
            'effective_from' => $relation->effective_from?->toDateString(),
            'effective_to' => $relation->effective_to?->toDateString(),
            'decision_no' => $relation->decision_no,
            'note' => $relation->note,
        ];
    }
}
