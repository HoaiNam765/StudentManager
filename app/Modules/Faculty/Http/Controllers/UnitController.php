<?php

namespace App\Modules\Faculty\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Faculty\Http\Requests\DepartmentRequest;
use App\Modules\Faculty\Http\Requests\FacultyRequest;
use App\Modules\Faculty\Http\Requests\MajorRequest;
use App\Modules\Faculty\Http\Requests\SpecializationRequest;
use App\Modules\Faculty\Http\Requests\TrainingTypeRequest;
use App\Modules\Faculty\Models\Department;
use App\Modules\Faculty\Models\Faculty;
use App\Modules\Faculty\Models\Major;
use App\Modules\Faculty\Models\Specialization;
use App\Modules\Faculty\Models\TrainingType;
use App\Modules\Faculty\Services\FacultyService;
use App\Support\Exports\ExportColumn;
use App\Support\Services\ExportService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * API cơ cấu tổ chức đào tạo (FR-FAC-001..005, 010): khoa, bộ môn, ngành, chuyên ngành (`{type}` trong đường dẫn)
 * và hệ đào tạo. Quyền: middleware `permission:FAC.*` chặn cả route; Policy + AccessControl kiểm tra phạm vi FACULTY
 * trên từng bản ghi và lọc danh sách (DEAN chỉ thấy, chỉ sửa đơn vị mình quản lý).
 */
class UnitController extends Controller
{
    /** type => [model, FormRequest, hàm tạo trong FacultyService] */
    public const TYPES = [
        'faculties' => [Faculty::class, FacultyRequest::class, 'createFaculty'],
        'departments' => [Department::class, DepartmentRequest::class, 'createDepartment'],
        'majors' => [Major::class, MajorRequest::class, 'createMajor'],
        'specializations' => [Specialization::class, SpecializationRequest::class, 'createSpecialization'],
    ];

    /** Trường người chỉ có quyền sửa trong phạm vi đơn vị (DEAN) được sửa: thông tin mô tả, liên hệ. */
    private const SCOPED_EDITABLE = ['name_en', 'founded_on', 'email', 'phone', 'description'];

    public function __construct(
        private readonly FacultyService $units,
        private readonly AccessControl $access,
    ) {}

    public function index(Request $request, string $type): JsonResponse
    {
        $list = $this->listQuery($request, $type)->paginate(50);

        return response()->json($list->through(fn (Model $unit) => $this->present($unit)));
    }

    /** Chi tiết; với khoa trả kèm cây bộ môn – ngành – chuyên ngành (FR-FAC-007). */
    public function show(string $type, int $id): JsonResponse
    {
        $unit = $this->find($type, $id);
        Gate::authorize('view', $unit);

        $data = $this->present($unit);

        if ($unit instanceof Faculty) {
            $data['departments'] = $unit->departments()->orderBy('code')->get()->map(fn (Department $d) => $this->present($d));
            $data['majors'] = $unit->majors()->with('specializations')->orderBy('code')->get()
                ->map(fn (Major $m) => $this->present($m) + ['specializations' => $m->specializations->sortBy('code')->values()->map(fn (Specialization $s) => $this->present($s))]);
        }

        if ($unit instanceof Major) {
            $data['specializations'] = $unit->specializations()->orderBy('code')->get()->map(fn (Specialization $s) => $this->present($s));
        }

        return response()->json($data);
    }

    public function store(string $type): JsonResponse
    {
        [, $requestClass, $create] = $this->type($type);
        $data = app($requestClass)->validated();

        return response()->json($this->present($this->units->{$create}($data)), 201);
    }

    public function update(Request $request, string $type, int $id): JsonResponse
    {
        [, $requestClass] = $this->type($type);
        $unit = $this->find($type, $id);
        Gate::authorize('update', $unit);
        $data = app($requestClass)->validated();

        // Người chỉ có quyền sửa trong phạm vi đơn vị mình (DEAN): chỉ thông tin mô tả, liên hệ
        if (! $this->access->allowsGlobally($request->user(), 'FAC', PermissionAction::Update)) {
            $forbidden = array_diff(array_keys($data), self::SCOPED_EDITABLE);

            abort_if($forbidden !== [], 403, 'Lãnh đạo đơn vị chỉ được sửa thông tin mô tả và liên hệ; đổi mã, tên, trạng thái do phòng Đào tạo thực hiện.');
        }

        return response()->json($this->present($this->units->update($unit, $data)));
    }

    public function destroy(string $type, int $id): Response
    {
        $unit = $this->find($type, $id);
        Gate::authorize('delete', $unit);
        $this->units->delete($unit);

        return response()->noContent();
    }

    /** Xuất danh sách đang lọc ra Excel/CSV/PDF (FR-FAC-010); cần quyền FAC.export, dữ liệu lọc theo phạm vi. */
    public function export(Request $request, string $type, ExportService $exports): SymfonyResponse
    {
        $columns = [
            new ExportColumn('code', 'Mã'),
            new ExportColumn('name', 'Tên'),
            new ExportColumn('name_en', 'Tên tiếng Anh'),
        ];

        $columns = [...$columns, ...match ($type) {
            'faculties' => [new ExportColumn('email', 'Email'), new ExportColumn('phone', 'Điện thoại')],
            'departments' => [new ExportColumn('faculty.code', 'Mã khoa'), new ExportColumn('faculty.name', 'Khoa')],
            'majors' => [
                new ExportColumn('faculty.name', 'Khoa quản lý'),
                new ExportColumn('education_level_label', 'Trình độ'),
                new ExportColumn('total_credits', 'Tổng tín chỉ'),
                new ExportColumn('standard_terms', 'Số học kỳ chuẩn'),
            ],
            'specializations' => [new ExportColumn('major.code', 'Mã ngành'), new ExportColumn('major.name', 'Ngành')],
            default => [],
        }, new ExportColumn('status_label', 'Trạng thái')];

        $result = $exports->export(
            $this->listQuery($request, $type, scoped: false),
            $columns,
            'FAC',
            $request->user(),
            $request->string('format', 'xlsx')->toString(),
            "danh-muc-{$type}",
        );

        return $result->isQueued()
            ? response()->json(['id' => $result->requestId, 'row_count' => $result->rowCount], 202)
            : $result->download;
    }

    public function trainingTypes(): JsonResponse
    {
        return response()->json(TrainingType::query()->orderByDesc('is_default')->orderBy('code')->get()->map(fn (TrainingType $t) => $this->presentType($t)));
    }

    public function storeTrainingType(TrainingTypeRequest $request): JsonResponse
    {
        return response()->json($this->presentType($this->units->createTrainingType($request->validated())), 201);
    }

    public function updateTrainingType(TrainingTypeRequest $request, TrainingType $trainingType): JsonResponse
    {
        return response()->json($this->presentType($this->units->updateTrainingType($trainingType, $request->validated())));
    }

    // ---------------------------------------------------------------------

    /** Danh sách theo bộ lọc; `$scoped` = lọc theo phạm vi xem (ExportService tự lọc theo quyền xuất nên không cần). */
    private function listQuery(Request $request, string $type, bool $scoped = true): Builder
    {
        [$model] = $this->type($type);

        $query = $model::query()
            ->search($request->string('q')->toString())
            ->when($request->filled('status'), fn (Builder $q) => $q->where('status', $request->string('status')->toString()))
            ->when($request->filled('faculty_id') && in_array($type, ['departments', 'majors'], true), fn (Builder $q) => $q->where('faculty_id', $request->integer('faculty_id')))
            ->when($request->filled('major_id') && $type === 'specializations', fn (Builder $q) => $q->where('major_id', $request->integer('major_id')))
            ->when($request->filled('education_level') && $type === 'majors', fn (Builder $q) => $q->where('education_level', $request->string('education_level')->toString()))
            ->with(match ($type) {
                'departments', 'majors' => ['faculty:id,code,name'],
                'specializations' => ['major:id,code,name,faculty_id'],
                default => [],
            })
            ->orderBy('code');

        return $scoped ? $this->access->constrain($query, $request->user(), 'FAC', PermissionAction::View) : $query;
    }

    /** @return array{0: class-string<Model>, 1: class-string, 2: string} */
    private function type(string $type): array
    {
        abort_unless(isset(self::TYPES[$type]), 404);

        return self::TYPES[$type];
    }

    private function find(string $type, int $id): Faculty|Department|Major|Specialization
    {
        [$model] = $this->type($type);

        return $model::query()->findOrFail($id);
    }

    /** @return array<string, mixed> */
    private function present(Model $unit): array
    {
        $data = [
            'id' => $unit->id,
            'code' => $unit->code,
            'name' => $unit->name,
            'name_en' => $unit->name_en,
            'status' => $unit->status->value,
            'status_label' => $unit->status_label,
        ];

        return $data + match (true) {
            $unit instanceof Faculty => [
                'founded_on' => $unit->founded_on?->toDateString(),
                'email' => $unit->email,
                'phone' => $unit->phone,
                'description' => $unit->description,
            ],
            $unit instanceof Department => ['faculty_id' => $unit->faculty_id, 'faculty' => $unit->faculty?->only('id', 'code', 'name'), 'description' => $unit->description],
            $unit instanceof Major => [
                'faculty_id' => $unit->faculty_id,
                'faculty' => $unit->faculty?->only('id', 'code', 'name'),
                'education_level' => $unit->education_level->value,
                'education_level_label' => $unit->education_level->label(),
                'total_credits' => $unit->total_credits,
                'standard_terms' => $unit->standard_terms,
                'average_credits_per_term' => $unit->averageCreditsPerTerm(),
            ],
            $unit instanceof Specialization => ['major_id' => $unit->major_id, 'major' => $unit->major?->only('id', 'code', 'name'), 'description' => $unit->description],
            default => [],
        };
    }

    /** @return array<string, mixed> */
    private function presentType(TrainingType $type): array
    {
        return [
            'id' => $type->id,
            'code' => $type->code,
            'name' => $type->name,
            'description' => $type->description,
            'is_default' => $type->is_default,
            'status' => $type->status->value,
            'status_label' => $type->status_label,
        ];
    }
}
