<?php

namespace App\Modules\SchoolClass\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\SchoolClass\Models\AdminClass;
use App\Modules\SchoolClass\Models\Cohort;
use App\Modules\SchoolClass\Services\AdminClassService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

/**
 * API khóa tuyển sinh và lớp hành chính (FR-CLS-001, 002). Quyền: middleware `permission:CLS.*` (thêm, sửa khóa cần
 * phạm vi toàn trường); Policy kiểm tra phạm vi trên từng lớp (DEAN: ngành thuộc khoa mình).
 */
class AdminClassController extends Controller
{
    private const CODE_RULE = 'regex:/^[A-Z0-9][A-Z0-9._\-]*$/';

    public function __construct(
        private readonly AdminClassService $classes,
        private readonly AccessControl $access,
    ) {}

    // ---------------------------------------------------------------------
    // Khóa
    // ---------------------------------------------------------------------

    public function cohorts(Request $request): JsonResponse
    {
        $list = Cohort::query()
            ->with('policySet:id,code,version,name')
            ->withCount(['classes' => fn (Builder $q) => $q->active()])
            ->search($request->string('q')->toString())
            ->when($request->filled('status'), fn (Builder $q) => $q->where('status', $request->string('status')->toString()))
            ->orderByDesc('start_year')
            ->get();

        return response()->json($list->map(fn (Cohort $c) => $this->presentCohort($c)));
    }

    public function storeCohort(Request $request): JsonResponse
    {
        return response()->json($this->presentCohort($this->classes->createCohort($this->validateCohort($request, true))), 201);
    }

    public function updateCohort(Request $request, Cohort $cohort): JsonResponse
    {
        return response()->json($this->presentCohort($this->classes->updateCohort($cohort, $this->validateCohort($request, false))));
    }

    public function destroyCohort(Cohort $cohort): Response
    {
        $this->classes->deleteCohort($cohort);

        return response()->noContent();
    }

    // ---------------------------------------------------------------------
    // Lớp hành chính
    // ---------------------------------------------------------------------

    public function index(Request $request): JsonResponse
    {
        $list = $this->access->constrain(AdminClass::query(), $request->user(), 'CLS', PermissionAction::View)
            ->with([
                'cohort:id,code,start_year',
                'major:id,code,name,faculty_id',
                'specialization:id,code,name',
                'trainingType:id,code,name',
                'advisorAssignments' => fn (Builder $q) => $q->current()->with('lecturer:id,code,full_name'),
            ])
            ->withCount(['members as current_students_count' => fn (Builder $q) => $q->current()])
            ->search($request->string('q')->toString())
            ->when($request->filled('cohort_id'), fn (Builder $q) => $q->where('cohort_id', $request->integer('cohort_id')))
            ->when($request->filled('major_id'), fn (Builder $q) => $q->where('major_id', $request->integer('major_id')))
            ->when($request->filled('faculty_id'), fn (Builder $q) => $q->whereHas('major', fn (Builder $m) => $m->where('faculty_id', $request->integer('faculty_id'))))
            ->when($request->filled('status'), fn (Builder $q) => $q->where('status', $request->string('status')->toString()))
            ->orderBy('code')
            ->paginate(50);

        return response()->json($list->through(fn (AdminClass $c) => $this->presentClass($c)));
    }

    public function show(AdminClass $adminClass): JsonResponse
    {
        Gate::authorize('view', $adminClass);

        return response()->json($this->presentClass($adminClass->load([
            'cohort',
            'major',
            'specialization',
            'trainingType',
            'advisorAssignments' => fn (Builder $q) => $q->current()->with('lecturer:id,code,full_name'),
        ])->loadCount(['members as current_students_count' => fn (Builder $q) => $q->current()])));
    }

    public function store(Request $request): JsonResponse
    {
        $class = $this->classes->createClass($this->validateClass($request, true));

        return response()->json($this->presentClass($class->load(['cohort', 'major', 'specialization', 'trainingType'])), 201);
    }

    public function update(Request $request, AdminClass $adminClass): JsonResponse
    {
        Gate::authorize('update', $adminClass);
        $class = $this->classes->updateClass($adminClass, $this->validateClass($request, false));

        return response()->json($this->presentClass($class->load(['cohort', 'major', 'specialization', 'trainingType'])));
    }

    public function destroy(AdminClass $adminClass): Response
    {
        Gate::authorize('delete', $adminClass);
        $this->classes->deleteClass($adminClass);

        return response()->noContent();
    }

    // ---------------------------------------------------------------------

    /** @return array<string, mixed> */
    private function validateCohort(Request $request, bool $creating): array
    {
        if (is_string($request->input('code'))) {
            $request->merge(['code' => Str::upper(trim($request->input('code')))]);
        }

        $required = $creating ? ['required'] : ['sometimes', 'required'];

        return $request->validate([
            'code' => ['sometimes', 'required', 'string', 'max:20', self::CODE_RULE],
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'start_year' => [...$required, 'integer', 'between:1990,2100'],
            'expected_end_year' => ['sometimes', 'integer', 'between:1990,2110'],
            'policy_set_id' => ['sometimes', 'nullable', 'integer', 'exists:policy_sets,id'],
            'status' => [$creating ? 'prohibited' : 'sometimes', 'in:active,inactive'],
        ], ['code.regex' => 'Mã khóa chỉ gồm chữ in hoa không dấu, số, dấu chấm, gạch ngang, gạch dưới (ví dụ K2026).'], [
            'code' => 'mã khóa',
            'name' => 'tên khóa',
            'start_year' => 'năm bắt đầu',
            'expected_end_year' => 'năm kết thúc dự kiến',
            'policy_set_id' => 'bộ quy chế',
            'status' => 'trạng thái',
        ]);
    }

    /** @return array<string, mixed> */
    private function validateClass(Request $request, bool $creating): array
    {
        if (is_string($request->input('code'))) {
            $request->merge(['code' => Str::upper(trim($request->input('code')))]);
        }

        $required = $creating ? ['required'] : ['sometimes', 'required'];

        return $request->validate([
            'code' => [...$required, 'string', 'max:30', self::CODE_RULE],
            'name' => [...$required, 'string', 'max:255'],
            'cohort_id' => [...$required, 'integer', 'exists:cohorts,id'],
            'major_id' => [...$required, 'integer', 'exists:majors,id'],
            'specialization_id' => ['sometimes', 'nullable', 'integer', 'exists:specializations,id'],
            'training_type_id' => ['sometimes', 'integer', 'exists:training_types,id'],
            'max_size' => ['sometimes', 'integer', 'min:1'],
            'status' => [$creating ? 'prohibited' : 'sometimes', 'in:active,inactive'],
        ], ['code.regex' => 'Mã lớp chỉ gồm chữ in hoa không dấu, số, dấu chấm, gạch ngang, gạch dưới (ví dụ 26DHTH01).'], [
            'code' => 'mã lớp',
            'name' => 'tên lớp',
            'cohort_id' => 'khóa',
            'major_id' => 'ngành',
            'specialization_id' => 'chuyên ngành',
            'training_type_id' => 'hệ đào tạo',
            'max_size' => 'sĩ số tối đa',
            'status' => 'trạng thái',
        ]);
    }

    /** @return array<string, mixed> */
    private function presentCohort(Cohort $cohort): array
    {
        $effective = rescue(fn () => $cohort->effectivePolicySet(), null, false);

        return [
            'id' => $cohort->id,
            'code' => $cohort->code,
            'name' => $cohort->name,
            'start_year' => $cohort->start_year,
            'expected_end_year' => $cohort->expected_end_year,
            'policy_set' => $cohort->policySet?->only('id', 'code', 'version', 'name'),
            'effective_policy_set' => $effective?->only('id', 'code', 'version', 'name'),
            'active_classes_count' => $cohort->classes_count ?? $cohort->classes()->active()->count(),
            'status' => $cohort->status->value,
            'status_label' => $cohort->status_label,
        ];
    }

    /** @return array<string, mixed> */
    private function presentClass(AdminClass $class): array
    {
        $advisor = $class->relationLoaded('advisorAssignments')
            ? $class->advisorAssignments->first()?->lecturer
            : $class->advisorAssignments()->current()->with('lecturer')->first()?->lecturer;

        return [
            'id' => $class->id,
            'code' => $class->code,
            'name' => $class->name,
            'cohort' => $class->cohort?->only('id', 'code', 'start_year'),
            'major' => $class->major?->only('id', 'code', 'name'),
            'specialization' => $class->specialization?->only('id', 'code', 'name'),
            'training_type' => $class->trainingType?->only('id', 'code', 'name'),
            'max_size' => $class->max_size,
            'current_students_count' => $class->current_students_count ?? $class->members()->current()->count(),
            'advisor' => $advisor?->only('id', 'code', 'full_name'),
            'advisor_warning' => $advisor === null,
            'status' => $class->status->value,
            'status_label' => $class->status_label,
        ];
    }
}
