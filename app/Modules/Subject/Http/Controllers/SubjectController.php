<?php

namespace App\Modules\Subject\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Faculty\Services\FacultyAccess;
use App\Modules\Subject\Enums\RelationType;
use App\Modules\Subject\Http\Requests\SubjectRequest;
use App\Modules\Subject\Models\Subject;
use App\Modules\Subject\Models\SubjectRelation;
use App\Modules\Subject\Services\SubjectService;
use App\Support\Exports\ExportColumn;
use App\Support\Services\ExportService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * API danh mục học phần (FR-SUB-001..003; tìm kiếm, lọc, xuất). Quyền: middleware `permission:SUB.*` chặn cả route;
 * Policy + AccessControl kiểm tra phạm vi trên từng bản ghi và lọc danh sách (DEAN chỉ thấy, chỉ quản lý
 * học phần của bộ môn mình phụ trách).
 */
class SubjectController extends Controller
{
    public function __construct(
        private readonly SubjectService $subjects,
        private readonly AccessControl $access,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $list = $this->access->constrain($this->listQuery($request), $request->user(), 'SUB', PermissionAction::View)
            ->paginate(min(100, max(1, $request->integer('per_page', 50))))->withQueryString();
        // Học phần tiên quyết / học trước đang hiệu lực cho cột "Học phần tiên quyết" của trang danh sách (mock admin/hoc-phan)
        $requires = SubjectRelation::query()->with('relatedSubject:id,code,name')
            ->whereIn('subject_id', $list->getCollection()->pluck('id'))
            ->whereIn('type', [RelationType::Prerequisite->value, RelationType::Prior->value])
            ->effectiveOn(now(config('studentmanager.display_timezone'))->toDateString())
            ->get()->groupBy('subject_id');

        return response()->json($list->through(fn (Subject $subject) => $this->present($subject) + [
            'prerequisites' => ($requires->get($subject->id) ?? collect())->map(fn (SubjectRelation $r) => [
                'type' => $r->type->value,
                'type_label' => $r->type->label(),
                'code' => $r->relatedSubject?->code,
                'name' => $r->relatedSubject?->name,
            ])->values(),
        ]));
    }

    public function show(Subject $subject): JsonResponse
    {
        Gate::authorize('view', $subject);

        return response()->json($this->present($subject->load('department.faculty')) + [
            'description' => $subject->description,
            'is_pass_fail_only' => $subject->isPassFailOnly(),
        ]);
    }

    public function store(SubjectRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->assertDepartmentInScope($request->user(), (int) $data['department_id'], PermissionAction::Create);

        return response()->json($this->present($this->subjects->create($data)->load('department.faculty')), 201);
    }

    public function update(SubjectRequest $request, Subject $subject): JsonResponse
    {
        Gate::authorize('update', $subject);
        $data = $request->validated();

        if (isset($data['department_id'])) {
            $this->assertDepartmentInScope($request->user(), (int) $data['department_id'], PermissionAction::Update);
        }

        return response()->json($this->present($this->subjects->update($subject, $data)->load('department.faculty')));
    }

    public function destroy(Subject $subject): Response
    {
        Gate::authorize('delete', $subject);
        $this->subjects->delete($subject);

        return response()->noContent();
    }

    /** Xuất danh sách đang lọc ra Excel/CSV/PDF; cần quyền SUB.export, dữ liệu lọc theo phạm vi xuất. */
    public function export(Request $request, ExportService $exports): SymfonyResponse
    {
        $result = $exports->export(
            $this->listQuery($request),
            [
                new ExportColumn('code', 'Mã học phần'),
                new ExportColumn('name', 'Tên học phần'),
                new ExportColumn('name_en', 'Tên tiếng Anh'),
                new ExportColumn('department.code', 'Mã bộ môn'),
                new ExportColumn('department.name', 'Bộ môn quản lý'),
                new ExportColumn('credits', 'Tín chỉ'),
                new ExportColumn('theory_credits', 'TC lý thuyết'),
                new ExportColumn('practice_credits', 'TC thực hành'),
                new ExportColumn('theory_periods', 'Tiết LT'),
                new ExportColumn('practice_periods', 'Tiết TH'),
                new ExportColumn('self_study_hours', 'Giờ tự học'),
                new ExportColumn('requirement_label', 'Loại'),
                new ExportColumn('knowledge_block_label', 'Khối kiến thức'),
                new ExportColumn('counts_toward_gpa_label', 'Tính GPA'),
                new ExportColumn('graduation_requirement_label', 'Điều kiện tốt nghiệp'),
                new ExportColumn('teaching_language_label', 'Ngôn ngữ'),
                new ExportColumn('status_label', 'Trạng thái'),
            ],
            'SUB',
            $request->user(),
            $request->string('format', 'xlsx')->toString(),
            'danh-muc-hoc-phan',
        );

        return $result->isQueued()
            ? response()->json(['id' => $result->requestId, 'row_count' => $result->rowCount], 202)
            : $result->download;
    }

    // ---------------------------------------------------------------------

    private function listQuery(Request $request): Builder
    {
        return Subject::query()
            ->with('department:id,code,name,faculty_id', 'department.faculty:id,code,name')
            ->search($request->string('q')->toString())
            ->when($request->filled('department_id'), fn (Builder $q) => $q->where('department_id', $request->integer('department_id')))
            ->when($request->filled('faculty_id'), fn (Builder $q) => $q->whereHas('department', fn (Builder $d) => $d->where('faculty_id', $request->integer('faculty_id'))))
            ->when($request->filled('knowledge_block'), fn (Builder $q) => $q->where('knowledge_block', $request->string('knowledge_block')->toString()))
            ->when($request->filled('requirement'), fn (Builder $q) => $q->where('requirement', $request->string('requirement')->toString()))
            ->when($request->filled('status'), fn (Builder $q) => $q->where('status', $request->string('status')->toString()))
            ->when($request->filled('credits'), fn (Builder $q) => $q->where('credits', $request->integer('credits')))
            ->when($request->filled('counts_toward_gpa'), fn (Builder $q) => $q->where('counts_toward_gpa', $request->boolean('counts_toward_gpa')))
            ->when($request->filled('is_graduation_requirement'), fn (Builder $q) => $q->where('is_graduation_requirement', $request->boolean('is_graduation_requirement')))
            ->orderBy('code');
    }

    /** Người chỉ có quyền trong phạm vi (DEAN) chỉ giao học phần cho bộ môn mình quản lý. */
    private function assertDepartmentInScope(User $user, int $departmentId, PermissionAction $action): void
    {
        if ($this->access->allowsGlobally($user, 'SUB', $action)) {
            return;
        }

        abort_unless(
            in_array($departmentId, array_map(intval(...), app(FacultyAccess::class)->departmentIds($user)), true),
            403,
            'Chỉ được quản lý học phần của bộ môn thuộc đơn vị mình phụ trách.'
        );
    }

    /** @return array<string, mixed> */
    private function present(Subject $subject): array
    {
        return [
            'id' => $subject->id,
            'code' => $subject->code,
            'name' => $subject->name,
            'name_en' => $subject->name_en,
            'department_id' => $subject->department_id,
            'department' => $subject->department?->only('id', 'code', 'name'),
            'faculty' => $subject->department?->faculty?->only('id', 'code', 'name'),
            'teaching_language' => $subject->teaching_language->value,
            'teaching_language_label' => $subject->teaching_language_label,
            'credits' => $subject->credits,
            'theory_credits' => $subject->theory_credits,
            'practice_credits' => $subject->practice_credits,
            'theory_periods' => $subject->theory_periods,
            'practice_periods' => $subject->practice_periods,
            'self_study_hours' => $subject->self_study_hours,
            'requirement' => $subject->requirement->value,
            'requirement_label' => $subject->requirement_label,
            'knowledge_block' => $subject->knowledge_block->value,
            'knowledge_block_label' => $subject->knowledge_block_label,
            'counts_toward_gpa' => $subject->counts_toward_gpa,
            'is_graduation_requirement' => $subject->is_graduation_requirement,
            'status' => $subject->status->value,
            'status_label' => $subject->status_label,
        ];
    }
}
