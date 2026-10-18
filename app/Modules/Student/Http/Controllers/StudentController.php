<?php

namespace App\Modules\Student\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Student\Http\Requests\StudentRequest;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Models\StudentChangeLog;
use App\Modules\Student\Models\StudentContact;
use App\Modules\Student\Services\IdentityNumber;
use App\Modules\Student\Services\StudentService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

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

    public function index(Request $request): JsonResponse
    {
        $q = $request->string('q')->trim()->toString();
        $identityHash = preg_match('/^[0-9A-Za-z]{6,20}$/', $q) && preg_match('/[0-9]{6,}/', $q) ? IdentityNumber::hash($q) : null;

        $list = $this->access->constrain(Student::query(), $request->user(), 'STU', PermissionAction::View)
            ->with(['major:id,code,name,faculty_id', 'trainingType:id,code,name'])
            ->when($q !== '', fn (Builder $query) => $query->where(fn (Builder $w) => $w
                ->where(fn (Builder $s) => $s->search($q))
                ->when($identityHash !== null, fn (Builder $s) => $s->orWhere('national_id_hash', $identityHash))))
            ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('major_id'), fn (Builder $query) => $query->where('major_id', $request->integer('major_id')))
            ->when($request->filled('faculty_id'), fn (Builder $query) => $query->whereHas('major', fn (Builder $m) => $m->where('faculty_id', $request->integer('faculty_id'))))
            ->when($request->filled('cohort'), fn (Builder $query) => $query->where('cohort', $request->integer('cohort')))
            ->when($request->filled('training_type_id'), fn (Builder $query) => $query->where('training_type_id', $request->integer('training_type_id')))
            ->orderByRaw('student_code IS NULL')
            ->orderBy('student_code')
            ->orderBy('full_name')
            ->paginate(50);

        return response()->json($list->through(fn (Student $s) => $this->summary($s)));
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
