<?php

namespace App\Modules\Student\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\SchoolClass\Models\AdminClassMember;
use App\Modules\Student\Http\Requests\StudentRequest;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Models\StudentChangeLog;
use App\Modules\Student\Models\StudentContact;
use App\Modules\Student\Services\IdentityNumber;
use App\Modules\Student\Services\StudentService;
use App\Support\Exports\ExportColumn;
use App\Support\Services\ExportService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * API hồ sơ sinh viên (FR-STU-001, 004, 005, 013). Quyền: middleware `permission:STU.*` chặn cả route; Policy kiểm tra
 * phạm vi trên từng hồ sơ (DEAN: ngành thuộc khoa mình). Số giấy tờ và địa chỉ luôn che; xem đầy đủ qua `/sensitive`
 * và được ghi nhật ký (BR-STU-11). Danh sách đầy đủ, bộ lọc, xuất Excel thuộc #94.
 */
class StudentController extends Controller
{
    public function __construct(
        private readonly StudentService $students,
        private readonly AccessControl $access,
    ) {}

    /**
     * Danh sách sinh viên (FR-STU-003): tìm theo MSSV, họ tên (không dấu), số CCCD đầy đủ (so bằng hash); lọc khoa, ngành,
     * khóa, hệ, lớp hành chính hiện tại, trạng thái; sắp xếp `sort` (student_code | full_name | cohort | status) và `dir`;
     * `per_page` tối đa 100. Kết quả lọc theo phạm vi xem của vai trò (khoa, cố vấn, lớp giảng dạy, của mình).
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min(100, max(1, $request->integer('per_page', 50)));
        $list = $this->filtered($request, $this->access->constrain(Student::query(), $request->user(), 'STU', PermissionAction::View))
            ->with(['major:id,code,name,faculty_id', 'trainingType:id,code,name'])
            ->paginate($perPage)
            ->withQueryString();
        $ids = $list->getCollection()->pluck('id');
        // Lớp hành chính hiện tại và GPA tích lũy cho cột "Lớp hành chính", "ĐTB tích lũy / TC" của trang danh sách
        $classes = AdminClassMember::query()->current()->whereIn('student_id', $ids)->with('adminClass:id,code,name')->get()->keyBy('student_id');
        $gpas = DB::table('cumulative_gpas')->whereIn('student_id', $ids)->get(['student_id', 'gpa', 'credits_earned', 'classification'])->keyBy('student_id');

        return response()->json($list->through(fn (Student $s) => $this->summary($s) + [
            'admin_class' => $classes->get($s->id)?->adminClass?->only('id', 'code', 'name'),
            'gpa' => ($g = $gpas->get($s->id)) === null ? null : [
                'gpa' => $g->gpa === null ? null : (float) $g->gpa,
                'credits_earned' => (int) $g->credits_earned,
                'classification' => $g->classification,
            ],
        ]));
    }

    /**
     * Xuất danh sách theo bộ lọc (FR-STU-008): Excel / CSV / PDF qua ExportService (quyền STU.export, lọc theo phạm vi
     * xuất, nhật ký `exported`). `include_sensitive=1` thêm số CCCD và địa chỉ đầy đủ — chỉ người xem STU toàn trường.
     */
    public function export(Request $request, ExportService $exports): SymfonyResponse
    {
        $request->validate([
            'format' => ['sometimes', Rule::in(['xlsx', 'csv', 'pdf'])],
            'include_sensitive' => ['sometimes', 'boolean'],
        ]);
        $sensitive = $request->boolean('include_sensitive');

        $columns = [
            new ExportColumn('student_code', 'MSSV'),
            new ExportColumn('full_name', 'Họ và tên'),
            new ExportColumn('date_of_birth', 'Ngày sinh'),
            new ExportColumn('major.name', 'Ngành'),
            new ExportColumn('cohort', 'Khóa'),
            new ExportColumn('school_email', 'Email trường'),
            new ExportColumn('phone', 'Điện thoại'),
            new ExportColumn('status_label', 'Trạng thái'),
            ...($sensitive
                ? [new ExportColumn('national_id', 'Số CCCD', sensitive: true), new ExportColumn('permanent_address', 'Địa chỉ thường trú', sensitive: true)]
                : [new ExportColumn('national_id_masked', 'Số CCCD (che)')]),
        ];

        $result = $exports->export(
            $this->filtered($request, Student::query()->with('major:id,name')),
            $columns,
            'STU',
            $request->user(),
            $request->string('format', 'xlsx')->toString(),
            'danh-sach-sinh-vien',
        );

        return $result->isQueued()
            ? response()->json(['id' => $result->requestId, 'row_count' => $result->rowCount], 202)
            : $result->download;
    }

    /** Bộ lọc và sắp xếp dùng chung cho danh sách và xuất. */
    private function filtered(Request $request, Builder $query): Builder
    {
        $request->validate([
            'sort' => ['sometimes', Rule::in(['student_code', 'full_name', 'cohort', 'status'])],
            'dir' => ['sometimes', Rule::in(['asc', 'desc'])],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);
        $q = $request->string('q')->trim()->toString();
        $identityHash = preg_match('/^[0-9A-Za-z]{6,20}$/', $q) && preg_match('/[0-9]{6,}/', $q) ? IdentityNumber::hash($q) : null;
        $sort = $request->string('sort', 'student_code')->toString();
        $dir = $request->string('dir', 'asc')->toString();

        return $query
            ->when($q !== '', fn (Builder $query) => $query->where(fn (Builder $w) => $w
                ->where(fn (Builder $s) => $s->search($q))
                ->when($identityHash !== null, fn (Builder $s) => $s->orWhere('national_id_hash', $identityHash))))
            ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('major_id'), fn (Builder $query) => $query->where('major_id', $request->integer('major_id')))
            ->when($request->filled('faculty_id'), fn (Builder $query) => $query->whereHas('major', fn (Builder $m) => $m->where('faculty_id', $request->integer('faculty_id'))))
            ->when($request->filled('cohort'), fn (Builder $query) => $query->where('cohort', $request->integer('cohort')))
            ->when($request->filled('training_type_id'), fn (Builder $query) => $query->where('training_type_id', $request->integer('training_type_id')))
            ->when($request->filled('admin_class_id'), fn (Builder $query) => $query->whereHas('adminClassMembers', fn (Builder $m) => $m
                ->current()->where('admin_class_id', $request->integer('admin_class_id'))))
            ->when($sort === 'student_code', fn (Builder $query) => $query->orderByRaw('student_code IS NULL'))
            ->when($sort === 'full_name', fn (Builder $query) => $query->orderByRaw('full_name COLLATE utf8mb4_vietnamese_ci '.$dir), fn (Builder $query) => $query->orderBy($sort, $dir))
            ->orderBy('id');
    }

    /** Hồ sơ chi tiết (FR-STU-004): thông tin cá nhân, liên hệ, học tập, người thân. Trường nhạy cảm đã che. */
    public function show(Student $student): JsonResponse
    {
        Gate::authorize('view', $student);

        return response()->json($this->detail($student));
    }

    public function store(StudentRequest $request): JsonResponse
    {
        $student = $this->students->create($request->profile(), $request->user());

        return response()->json($this->detail($student), 201);
    }

    public function update(StudentRequest $request, Student $student): JsonResponse
    {
        Gate::authorize('update', $student);
        $student = $this->students->update($student, $request->profile(), $request->validated('reason'), $request->user());

        return response()->json($this->detail($student));
    }

    public function destroy(Request $request, Student $student): Response
    {
        Gate::authorize('delete', $student);
        $reason = $request->validate(['reason' => ['nullable', 'string', 'max:500']], [], ['reason' => 'lý do'])['reason'] ?? null;
        $this->students->delete($student, $reason);

        return response()->noContent();
    }

    /** Xem đầy đủ số giấy tờ, địa chỉ: cần quyền sửa hồ sơ, mỗi lần xem ghi nhật ký (BR-STU-11). */
    public function sensitive(Student $student): JsonResponse
    {
        Gate::authorize('update', $student);

        return response()->json($this->students->reveal($student));
    }

    /** Lịch sử thay đổi hồ sơ (FR-STU-005). */
    public function changeLogs(Student $student): JsonResponse
    {
        Gate::authorize('view', $student);

        return response()->json($student->changeLogs()->with('changer:id,name,username')->paginate(50)
            ->through(fn (StudentChangeLog $log) => [
                'id' => $log->id,
                'field' => $log->field,
                'field_label' => StudentService::FIELD_LABELS[$log->field] ?? $log->field,
                'old_value' => $log->old_value,
                'new_value' => $log->new_value,
                'sensitive' => $log->sensitive,
                'reason' => $log->reason,
                'changed_by' => $log->changer?->only('id', 'name', 'username'),
                'changed_at' => $log->changed_at->toIso8601String(),
            ]));
    }

    // ---------------------------------------------------------------------
    // Người thân, liên hệ khẩn cấp (FR-STU-013)
    // ---------------------------------------------------------------------

    public function storeContact(Request $request, Student $student): JsonResponse
    {
        Gate::authorize('update', $student);

        return response()->json($this->presentContact($this->students->addContact($student, $request->validate($this->contactRules(true), [], $this->contactLabels()))), 201);
    }

    public function updateContact(Request $request, StudentContact $studentContact): JsonResponse
    {
        Gate::authorize('update', $studentContact->student);

        return response()->json($this->presentContact($this->students->updateContact($studentContact, $request->validate($this->contactRules(false), [], $this->contactLabels()))));
    }

    public function destroyContact(StudentContact $studentContact): Response
    {
        Gate::authorize('update', $studentContact->student);
        $this->students->removeContact($studentContact);

        return response()->noContent();
    }

    // ---------------------------------------------------------------------

    /** @return array<string, mixed> */
    private function summary(Student $student): array
    {
        return [
            'id' => $student->id,
            'student_code' => $student->student_code,
            'full_name' => $student->full_name,
            'date_of_birth' => $student->date_of_birth?->toDateString(),
            'major' => $student->major?->only('id', 'code', 'name'),
            'training_type' => $student->trainingType?->only('id', 'code', 'name'),
            'cohort' => $student->cohort,
            'school_email' => $student->school_email,
            'national_id_masked' => $student->national_id_masked,
            'status' => $student->status->value,
            'status_label' => $student->status_label,
        ];
    }

    /** @return array<string, mixed> */
    private function detail(Student $student): array
    {
        $student->loadMissing(['major.faculty', 'specialization', 'trainingType', 'curriculum', 'gender', 'ethnicity', 'religion',
            'nationality', 'permanentUnit.parent', 'temporaryUnit.parent', 'contacts', 'user']);

        return $this->summary($student) + [
            'personal' => [
                'gender' => $student->gender?->only('id', 'code', 'name'),
                'place_of_birth' => $student->place_of_birth,
                'ethnicity' => $student->ethnicity?->only('id', 'code', 'name'),
                'religion' => $student->religion?->only('id', 'code', 'name'),
                'nationality' => $student->nationality?->only('id', 'code', 'name'),
                'id_type' => $student->id_type->value,
                'id_type_label' => $student->id_type->label(),
                'national_id_masked' => IdentityNumber::mask($student->national_id),
                'priority_group_id' => $student->priority_group_id,
                'priority_area_id' => $student->priority_area_id,
            ],
            'contact' => [
                'personal_email' => $student->personal_email,
                'phone' => $student->phone,
                'permanent_address_masked' => IdentityNumber::maskAddress($student->permanent_address),
                'permanent_unit' => $student->permanentUnit === null ? null : ['id' => $student->permanentUnit->id, 'name' => $student->permanentUnit->fullName()],
                'temporary_address_masked' => IdentityNumber::maskAddress($student->temporary_address),
                'temporary_unit' => $student->temporaryUnit === null ? null : ['id' => $student->temporaryUnit->id, 'name' => $student->temporaryUnit->fullName()],
            ],
            'academic' => [
                'faculty' => $student->major?->faculty?->only('id', 'code', 'name'),
                'specialization' => $student->specialization?->only('id', 'code', 'name'),
                'curriculum' => $student->curriculum?->only('id', 'code', 'name', 'version'),
                'admission_date' => $student->admission_date?->toDateString(),
                'username' => $student->user?->username,
            ],
            'contacts' => $student->contacts->map(fn (StudentContact $c) => $this->presentContact($c))->values(),
            'note' => $student->note,
            'photo_url' => $student->photo_path === null ? null : asset('storage/'.$student->photo_path),
        ];
    }

    /** @return array<string, mixed> */
    private function presentContact(StudentContact $contact): array
    {
        return $contact->only('id', 'full_name', 'relationship', 'phone', 'email', 'is_emergency', 'receives_notifications');
    }

    /** @return array<string, list<string>> */
    private function contactRules(bool $creating): array
    {
        $required = $creating ? ['required'] : ['sometimes', 'required'];

        return [
            'full_name' => [...$required, 'string', 'max:255'],
            'relationship' => [...$required, 'string', 'max:50'],
            'phone' => ['sometimes', 'nullable', 'string', StudentRequest::PHONE_RULE],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'is_emergency' => ['sometimes', 'boolean'],
            'receives_notifications' => ['sometimes', 'boolean'],
        ];
    }

    /** @return array<string, string> */
    private function contactLabels(): array
    {
        return [
            'full_name' => 'họ tên người liên hệ',
            'relationship' => 'quan hệ',
            'phone' => 'số điện thoại',
            'email' => 'email',
            'is_emergency' => 'liên hệ khẩn cấp',
            'receives_notifications' => 'đồng ý nhận thông báo',
        ];
    }
}
