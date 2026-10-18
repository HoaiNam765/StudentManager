<?php

namespace App\Modules\Curriculum\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Curriculum\Models\Curriculum;
use App\Modules\Curriculum\Models\CurriculumBlock;
use App\Modules\Curriculum\Models\CurriculumSubject;
use App\Modules\Curriculum\Models\ElectiveGroup;
use App\Modules\Curriculum\Services\CurriculumAnalyzer;
use App\Modules\Curriculum\Services\CurriculumService;
use App\Modules\Faculty\Models\Major;
use App\Modules\Faculty\Services\FacultyAccess;
use App\Modules\Subject\Enums\KnowledgeBlock;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * API chương trình đào tạo (FR-CUR-001, 002, 003). Quyền: middleware `permission:CUR.*` chặn cả route; Policy kiểm tra
 * phạm vi trên từng CTĐT (DEAN: ngành thuộc khoa mình). Ban hành cần CUR.approve toàn trường (ACAD).
 */
class CurriculumController extends Controller
{
    public function __construct(
        private readonly CurriculumService $curricula,
        private readonly CurriculumAnalyzer $analyzer,
        private readonly AccessControl $access,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $list = $this->access->constrain(Curriculum::query(), $request->user(), 'CUR', PermissionAction::View)
            ->with(['major:id,code,name,faculty_id', 'specialization:id,code,name', 'trainingType:id,code,name'])
            ->search($request->string('q')->toString())
            ->when($request->filled('major_id'), fn (Builder $q) => $q->where('major_id', $request->integer('major_id')))
            ->when($request->filled('faculty_id'), fn (Builder $q) => $q->whereHas('major', fn (Builder $m) => $m->where('faculty_id', $request->integer('faculty_id'))))
            ->when($request->filled('status'), fn (Builder $q) => $q->where('status', $request->string('status')->toString()))
            ->when($request->filled('cohort'), fn (Builder $q) => $q->forCohort($request->integer('cohort')))
            ->orderBy('code')
            ->orderByDesc('version')
            ->paginate(50);

        return response()->json($list->through(fn (Curriculum $c) => $this->present($c)));
    }

    /** Chi tiết: khối, nhóm tự chọn, học phần theo học kỳ, tổng hợp tín chỉ và cảnh báo. */
    public function show(Curriculum $curriculum): JsonResponse
    {
        Gate::authorize('view', $curriculum);
        $curriculum->load(['major', 'specialization', 'trainingType', 'blocks', 'electiveGroups', 'items.subject']);

        return response()->json($this->present($curriculum) + [
            'description' => $curriculum->description,
            'blocks' => $curriculum->blocks->map(fn (CurriculumBlock $b) => [
                'id' => $b->id,
                'name' => $b->name,
                'block_type' => $b->block_type->value,
                'block_type_label' => $b->block_type->label(),
                'required_credits' => $b->required_credits,
                'elective_groups' => $curriculum->electiveGroups->where('curriculum_block_id', $b->id)->values()
                    ->map(fn (ElectiveGroup $g) => $g->only('id', 'name', 'min_credits')),
            ]),
            'subjects' => $curriculum->items->sortBy([['planned_term', 'asc'], ['sort_order', 'asc']])->values()
                ->map(fn (CurriculumSubject $i) => $this->presentItem($i)),
            'summary' => $this->analyzer->summary($curriculum),
            'warnings' => $this->analyzer->prerequisiteWarnings($curriculum),
        ]);
    }

    /** Kiểm tra trước khi ban hành: tổng hợp tín chỉ và các lý do chưa ban hành được. */
    public function check(Curriculum $curriculum): JsonResponse
    {
        Gate::authorize('view', $curriculum);
        $problems = $this->analyzer->publishProblems($curriculum);

        return response()->json(['publishable' => $problems === [], 'problems' => $problems, 'summary' => $this->analyzer->summary($curriculum)]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateCurriculum($request, creating: true);
        $this->assertMajorInScope($request->user(), (int) $data['major_id']);

        return response()->json($this->present($this->curricula->create($data)->load(['major', 'specialization', 'trainingType'])), 201);
    }

    public function update(Request $request, Curriculum $curriculum): JsonResponse
    {
        Gate::authorize('update', $curriculum);
        $data = $this->validateCurriculum($request, creating: false);

        if (isset($data['major_id'])) {
            $this->assertMajorInScope($request->user(), (int) $data['major_id']);
        }

        return response()->json($this->present($this->curricula->update($curriculum, $data)->load(['major', 'specialization', 'trainingType'])));
    }

    public function destroy(Curriculum $curriculum): Response
    {
        Gate::authorize('delete', $curriculum);
        $this->curricula->delete($curriculum);

        return response()->noContent();
    }

    public function publish(Request $request, Curriculum $curriculum): JsonResponse
    {
        return response()->json($this->present($this->curricula->publish($curriculum, $request->user())->load(['major', 'specialization', 'trainingType'])));
    }

    // ---------------------------------------------------------------------
    // Khối, nhóm tự chọn, học phần
    // ---------------------------------------------------------------------

    public function storeBlock(Request $request, Curriculum $curriculum): JsonResponse
    {
        Gate::authorize('update', $curriculum);
        $block = $this->curricula->addBlock($curriculum, $request->validate($this->blockRules(true), [], $this->labels()));

        return response()->json($block->only('id', 'name', 'required_credits', 'sort_order') + ['block_type' => $block->block_type->value], 201);
    }

    public function updateBlock(Request $request, CurriculumBlock $curriculumBlock): JsonResponse
    {
        Gate::authorize('update', $curriculumBlock->curriculum);
        $block = $this->curricula->updateBlock($curriculumBlock, $request->validate($this->blockRules(false), [], $this->labels()));

        return response()->json($block->only('id', 'name', 'required_credits', 'sort_order') + ['block_type' => $block->block_type->value]);
    }

    public function destroyBlock(CurriculumBlock $curriculumBlock): Response
    {
        Gate::authorize('update', $curriculumBlock->curriculum);
        $this->curricula->removeBlock($curriculumBlock);

        return response()->noContent();
    }

    public function storeGroup(Request $request, Curriculum $curriculum): JsonResponse
    {
        Gate::authorize('update', $curriculum);
        $data = $request->validate([
            'curriculum_block_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'min_credits' => ['required', 'integer', 'min:1', 'max:200'],
        ], [], $this->labels());

        return response()->json($this->curricula->addGroup($curriculum, $data)->only('id', 'curriculum_block_id', 'name', 'min_credits'), 201);
    }

    public function updateGroup(Request $request, ElectiveGroup $electiveGroup): JsonResponse
    {
        Gate::authorize('update', $electiveGroup->curriculum);
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'min_credits' => ['sometimes', 'integer', 'min:1', 'max:200'],
        ], [], $this->labels());

        return response()->json($this->curricula->updateGroup($electiveGroup, $data)->only('id', 'curriculum_block_id', 'name', 'min_credits'));
    }

    public function destroyGroup(ElectiveGroup $electiveGroup): Response
    {
        Gate::authorize('update', $electiveGroup->curriculum);
        $this->curricula->removeGroup($electiveGroup);

        return response()->noContent();
    }

    public function storeSubject(Request $request, Curriculum $curriculum): JsonResponse
    {
        Gate::authorize('update', $curriculum);
        $result = $this->curricula->addSubject($curriculum, $request->validate($this->itemRules(true), [], $this->labels()));

        return response()->json($this->presentItem($result['item']) + ['warnings' => $result['warnings']], 201);
    }

    public function updateSubject(Request $request, CurriculumSubject $curriculumSubject): JsonResponse
    {
        Gate::authorize('update', $curriculumSubject->curriculum);
        $result = $this->curricula->updateSubject($curriculumSubject, $request->validate($this->itemRules(false), [], $this->labels()));

        return response()->json($this->presentItem($result['item']) + ['warnings' => $result['warnings']]);
    }

    public function destroySubject(CurriculumSubject $curriculumSubject): Response
    {
        Gate::authorize('update', $curriculumSubject->curriculum);
        $this->curricula->removeSubject($curriculumSubject);

        return response()->noContent();
    }

    // ---------------------------------------------------------------------

    /** @return array<string, mixed> */
    private function validateCurriculum(Request $request, bool $creating): array
    {
        if (is_string($request->input('code'))) {
            $request->merge(['code' => Str::upper(trim($request->input('code')))]);
        }

        $required = $creating ? ['required'] : ['sometimes', 'required'];

        return $request->validate([
            'code' => [...$required, 'string', 'max:30', 'regex:/^[A-Z0-9][A-Z0-9._\-]*$/'],
            'name' => [...$required, 'string', 'max:255'],
            'major_id' => [...$required, 'integer', 'exists:majors,id'],
            'specialization_id' => ['sometimes', 'nullable', 'integer', 'exists:specializations,id'],
            'training_type_id' => [...$required, 'integer', 'exists:training_types,id'],
            'cohort_from' => [...$required, 'integer', 'between:2000,2100'],
            'cohort_to' => ['sometimes', 'nullable', 'integer', 'between:2000,2100'],
            'total_credits' => ['sometimes', 'integer', 'min:1', 'max:400'],
            'standard_terms' => ['sometimes', 'integer', 'min:1', 'max:20'],
            'description' => ['sometimes', 'nullable', 'string', 'max:5000'],
        ], ['code.regex' => 'Mã CTĐT chỉ gồm chữ in hoa không dấu, số, dấu chấm, gạch ngang và gạch dưới.'], $this->labels());
    }

    /** @return array<string, mixed> */
    private function blockRules(bool $creating): array
    {
        $required = $creating ? ['required'] : ['sometimes', 'required'];

        return [
            'name' => [...$required, 'string', 'max:255'],
            'block_type' => [...$required, Rule::enum(KnowledgeBlock::class)],
            'required_credits' => [...$required, 'integer', 'min:0', 'max:400'],
            'sort_order' => ['sometimes', 'integer', 'min:0', 'max:255'],
        ];
    }

    /** @return array<string, mixed> */
    private function itemRules(bool $creating): array
    {
        $required = $creating ? ['required'] : ['sometimes', 'required'];

        return [
            'subject_id' => [$creating ? 'required' : 'prohibited', 'integer', 'exists:subjects,id'],
            'curriculum_block_id' => [...$required, 'integer'],
            'curriculum_elective_group_id' => ['sometimes', 'nullable', 'integer'],
            'planned_term' => [...$required, 'integer', 'min:1', 'max:20'],
            'sort_order' => ['sometimes', 'integer', 'min:0', 'max:1000'],
            'outside_prerequisites_confirmed' => ['sometimes', 'boolean'],
        ];
    }

    /** @return array<string, string> */
    private function labels(): array
    {
        return [
            'code' => 'mã CTĐT', 'name' => 'tên', 'major_id' => 'ngành', 'specialization_id' => 'chuyên ngành',
            'training_type_id' => 'hệ đào tạo', 'cohort_from' => 'khóa áp dụng từ', 'cohort_to' => 'khóa áp dụng đến',
            'total_credits' => 'tổng tín chỉ', 'standard_terms' => 'số học kỳ chuẩn', 'description' => 'mô tả',
            'block_type' => 'loại khối', 'required_credits' => 'số tín chỉ yêu cầu', 'sort_order' => 'thứ tự',
            'curriculum_block_id' => 'khối kiến thức', 'min_credits' => 'số tín chỉ tối thiểu',
            'subject_id' => 'học phần', 'curriculum_elective_group_id' => 'nhóm tự chọn', 'planned_term' => 'học kỳ dự kiến',
            'outside_prerequisites_confirmed' => 'xác nhận tiên quyết ngoài CTĐT',
        ];
    }

    /** DEAN chỉ xây dựng CTĐT cho ngành thuộc khoa mình. */
    private function assertMajorInScope(User $user, int $majorId): void
    {
        if ($this->access->allowsGlobally($user, 'CUR', PermissionAction::Create)) {
            return;
        }

        $facultyId = Major::query()->whereKey($majorId)->value('faculty_id');

        abort_unless(
            in_array((int) $facultyId, array_map(intval(...), app(FacultyAccess::class)->facultyIds($user)), true),
            403,
            'Chỉ được xây dựng CTĐT cho ngành thuộc khoa mình quản lý.'
        );
    }

    /** @return array<string, mixed> */
    private function present(Curriculum $curriculum): array
    {
        return [
            'id' => $curriculum->id,
            'code' => $curriculum->code,
            'version' => $curriculum->version,
            'name' => $curriculum->name,
            'major' => $curriculum->major?->only('id', 'code', 'name'),
            'specialization' => $curriculum->specialization?->only('id', 'code', 'name'),
            'training_type' => $curriculum->trainingType?->only('id', 'code', 'name'),
            'cohort_from' => $curriculum->cohort_from,
            'cohort_to' => $curriculum->cohort_to,
            'total_credits' => $curriculum->total_credits,
            'standard_terms' => $curriculum->standard_terms,
            'status' => $curriculum->status->value,
            'status_label' => $curriculum->status->label(),
            'published_at' => $curriculum->published_at?->toIso8601String(),
        ];
    }

    /** @return array<string, mixed> */
    private function presentItem(CurriculumSubject $item): array
    {
        return [
            'id' => $item->id,
            'subject' => $item->subject?->only('id', 'code', 'name', 'credits'),
            'curriculum_block_id' => $item->curriculum_block_id,
            'curriculum_elective_group_id' => $item->curriculum_elective_group_id,
            'is_elective' => $item->isElective(),
            'planned_term' => $item->planned_term,
            'sort_order' => $item->sort_order,
            'outside_prerequisites_confirmed' => $item->outside_prerequisites_confirmed,
        ];
    }
}
