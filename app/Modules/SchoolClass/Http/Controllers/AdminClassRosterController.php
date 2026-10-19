<?php

namespace App\Modules\SchoolClass\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\SchoolClass\Models\AdminClass;
use App\Modules\SchoolClass\Models\AdminClassMember;
use App\Modules\SchoolClass\Models\AdvisorAssignment;
use App\Modules\SchoolClass\Services\AdvisorAssignmentService;
use App\Modules\SchoolClass\Services\ClassDirectoryService;
use App\Modules\SchoolClass\Services\ClassRosterService;
use App\Modules\Teacher\Models\Lecturer;
use App\Support\Exports\ExportColumn;
use App\Support\Services\ExportService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

/** API lịch sử thành viên và cố vấn lớp hành chính (FR-CLS-003, 004; GC-07). */
class AdminClassRosterController extends Controller
{
    public function __construct(
        private readonly ClassRosterService $roster,
        private readonly ClassDirectoryService $directory,
        private readonly AdvisorAssignmentService $advisors,
        private readonly AccessControl $access,
    ) {}

    public function members(Request $request, AdminClass $adminClass): JsonResponse
    {
        abort_unless(
            $this->access->allowsOn($request->user(), 'CLS', PermissionAction::View, $adminClass),
            403,
            'Bạn không có quyền xem danh sách sinh viên của lớp này. Hãy chọn lớp thuộc phạm vi của bạn hoặc liên hệ cán bộ Đào tạo.'
        );
        $members = $this->directory
            ->studentsInAdminClass($request->user(), $adminClass, $request->boolean('history'))
            ->paginate(50);

        return response()->json($members->through(fn (AdminClassMember $member) => [
            'id' => $member->id,
            'student' => $this->directory->presentStudent($member->student, $adminClass),
            'admin_class' => $adminClass->only('id', 'code', 'name'),
            'effective_from' => $member->effective_from->toDateString(),
            'effective_to' => $member->effective_to?->toDateString(),
            'reason' => $member->reason,
            'end_reason' => $member->end_reason,
        ]));
    }

    public function exportMembers(Request $request, AdminClass $adminClass, ExportService $exports): Response|JsonResponse
    {
        abort_unless(
            $this->access->allowsOn($request->user(), 'CLS', PermissionAction::View, $adminClass),
            403,
            'Bạn không có quyền xem danh sách sinh viên của lớp này. Hãy chọn lớp thuộc phạm vi của bạn hoặc liên hệ cán bộ Đào tạo.'
        );
        abort_unless(
            $this->access->allowsOn($request->user(), 'CLS', PermissionAction::Export, $adminClass),
            403,
            'Bạn không có quyền xuất danh sách lớp này. Hãy liên hệ cán bộ Đào tạo để được cấp quyền xuất dữ liệu.'
        );

        $result = $exports->export(
            $this->directory->studentsForExport($request->user(), $adminClass),
            [
                new ExportColumn('student_code', 'MSSV'),
                new ExportColumn('full_name', 'Họ và tên'),
                new ExportColumn('school_email', 'Email trường'),
            ],
            'CLS',
            $request->user(),
            $request->string('format', 'xlsx')->toString(),
            "danh-sach-lop-{$adminClass->code}"
        );

        return $result->isQueued()
            ? response()->json(['id' => $result->requestId, 'row_count' => $result->rowCount], 202)
            : $result->download;
    }

    public function previewAssignment(Request $request, AdminClass $adminClass): JsonResponse
    {
        $this->authorizeRosterUpdate($request, $adminClass);
        $data = $this->validateStudents($request, 'effective_from');

        return response()->json($this->roster->previewAssignment($adminClass, $data['student_ids'], $data['effective_from']));
    }

    public function assign(Request $request, AdminClass $adminClass): JsonResponse
    {
        $this->authorizeRosterUpdate($request, $adminClass);
        $data = $this->validateStudents($request, 'effective_from') + $request->validate([
            'reason' => ['sometimes', 'nullable', 'string', 'max:500'],
        ]);
        $members = $this->roster->assign($adminClass, $data['student_ids'], $data['effective_from'], $data['reason'] ?? null);

        return response()->json([
            'count' => count($members),
            'members' => collect($members)->map(fn (AdminClassMember $member) => $this->presentMember($member->load(['student:id,student_code,full_name', 'adminClass:id,code,name']))),
        ], 201);
    }

    public function previewRemoval(Request $request, AdminClass $adminClass): JsonResponse
    {
        $this->authorizeRosterUpdate($request, $adminClass);
        $data = $this->validateStudents($request, 'effective_to');

        return response()->json($this->roster->previewRemoval($adminClass, $data['student_ids'], $data['effective_to']));
    }

    public function remove(Request $request, AdminClass $adminClass): JsonResponse
    {
        $this->authorizeRosterUpdate($request, $adminClass);
        $data = $this->validateStudents($request, 'effective_to') + $request->validate([
            'reason' => ['sometimes', 'nullable', 'string', 'max:500'],
        ]);
        $members = $this->roster->remove($adminClass, $data['student_ids'], $data['effective_to'], $data['reason'] ?? null);

        return response()->json([
            'count' => count($members),
            'members' => collect($members)->map(fn (AdminClassMember $member) => $this->presentMember($member->load(['student:id,student_code,full_name', 'adminClass:id,code,name']))),
        ]);
    }

    public function previewAutomaticPlacement(Request $request): JsonResponse
    {
        $data = $this->validateStudents($request, 'effective_from');

        return response()->json($this->roster->previewAutomaticPlacement($data['student_ids'], $data['effective_from']));
    }

    public function assignAutomatically(Request $request): JsonResponse
    {
        $data = $this->validateStudents($request, 'effective_from') + $request->validate([
            'reason' => ['sometimes', 'nullable', 'string', 'max:500'],
        ]);
        $members = $this->roster->assignAutomatically($data['student_ids'], $data['effective_from'], $data['reason'] ?? null);

        return response()->json([
            'count' => count($members),
            'members' => collect($members)->map(fn (AdminClassMember $member) => $this->presentMember($member->load(['student:id,student_code,full_name', 'adminClass:id,code,name']))),
        ], 201);
    }

    public function advisors(Request $request, AdminClass $adminClass): JsonResponse
    {
        Gate::authorize('view', $adminClass);

        $assignments = AdvisorAssignment::query()
            ->with('lecturer:id,code,full_name,status')
            ->where('admin_class_id', $adminClass->id)
            ->when(! $request->boolean('history'), fn (Builder $query) => $query->current())
            ->orderByDesc('effective_from')
            ->get();

        return response()->json($assignments->map(fn (AdvisorAssignment $assignment) => $this->presentAdvisor($assignment)));
    }

    public function previewAdvisor(Request $request, AdminClass $adminClass): JsonResponse
    {
        $data = $this->validateAdvisor($request, $adminClass);

        return response()->json($this->advisors->preview($adminClass, $data['lecturer'], $data['effective_from']));
    }

    public function assignAdvisor(Request $request, AdminClass $adminClass): JsonResponse
    {
        $data = $this->validateAdvisor($request, $adminClass) + $request->validate([
            'reason' => ['sometimes', 'nullable', 'string', 'max:500'],
        ]);

        return response()->json($this->presentAdvisor(
            $this->advisors->assign($adminClass, $data['lecturer'], $data['effective_from'], $data['reason'] ?? null)
        ), 201);
    }

    public function endAdvisor(Request $request, AdvisorAssignment $advisorAssignment): JsonResponse
    {
        $advisorAssignment->loadMissing('adminClass');
        $this->authorizeAdvisorChange($request, $advisorAssignment->adminClass, $advisorAssignment->lecturer);
        $data = $request->validate([
            'effective_to' => ['required', 'date_format:Y-m-d'],
            'reason' => ['sometimes', 'nullable', 'string', 'max:500'],
        ], [], ['effective_to' => 'ngày kết thúc', 'reason' => 'lý do']);

        return response()->json($this->presentAdvisor(
            $this->advisors->end($advisorAssignment, $data['effective_to'], $data['reason'] ?? null)
        ));
    }

    public function missingAdvisors(Request $request): JsonResponse
    {
        $classes = $this->access->constrain(AdminClass::query(), $request->user(), 'CLS', PermissionAction::View)
            ->active()
            ->whereDoesntHave('advisorAssignments', fn (Builder $query) => $query->current())
            ->with(['cohort:id,code', 'major:id,code,name'])
            ->orderBy('code')
            ->paginate(50);

        return response()->json($classes->through(fn (AdminClass $class) => [
            'id' => $class->id,
            'code' => $class->code,
            'name' => $class->name,
            'cohort' => $class->cohort?->only('id', 'code'),
            'major' => $class->major?->only('id', 'code', 'name'),
            'warning' => "Lớp {$class->code} chưa có cố vấn học tập.",
        ]));
    }

    /** @return array<string, mixed> */
    private function validateStudents(Request $request, string $dateField): array
    {
        return $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['required', 'integer', 'distinct', 'exists:students,id'],
            $dateField => ['required', 'date_format:Y-m-d'],
        ], [], ['student_ids' => 'danh sách sinh viên', $dateField => $dateField === 'effective_from' ? 'ngày hiệu lực' : 'ngày kết thúc']);
    }

    /** @return array{lecturer: Lecturer, effective_from: string} */
    private function validateAdvisor(Request $request, AdminClass $adminClass): array
    {
        $this->authorizeAdvisorChange($request, $adminClass);
        $data = $request->validate([
            'lecturer_id' => ['required', 'integer', 'exists:lecturers,id'],
            'effective_from' => ['required', 'date_format:Y-m-d'],
        ], [], ['lecturer_id' => 'giảng viên', 'effective_from' => 'ngày hiệu lực']);
        $lecturer = Lecturer::query()->findOrFail($data['lecturer_id']);
        $this->authorizeAdvisorChange($request, $adminClass, $lecturer);

        return ['lecturer' => $lecturer, 'effective_from' => $data['effective_from']];
    }

    private function authorizeRosterUpdate(Request $request, AdminClass $adminClass): void
    {
        abort_unless($this->access->allowsGlobally($request->user(), 'CLS', PermissionAction::Update), 403, 'Chỉ cán bộ Đào tạo được thay đổi danh sách lớp hành chính.');
        Gate::authorize('update', $adminClass);
    }

    private function authorizeAdvisorChange(Request $request, AdminClass $adminClass, ?Lecturer $lecturer = null): void
    {
        abort_unless(Gate::any(['update', 'approve'], $adminClass), 403, 'Bạn không có quyền phân công cố vấn cho lớp này.');

        if ($lecturer !== null && ! $this->access->allowsGlobally($request->user(), 'CLS', PermissionAction::Update)) {
            abort_unless(
                $this->access->allowsOn($request->user(), 'TCH', PermissionAction::Update, $lecturer)
                    || $this->access->allowsOn($request->user(), 'TCH', PermissionAction::Approve, $lecturer),
                403,
                'Giảng viên được chọn nằm ngoài phạm vi quản lý của bạn.'
            );
        }
    }

    /** @return array<string, mixed> */
    private function presentMember(AdminClassMember $member): array
    {
        return [
            'id' => $member->id,
            'student' => $member->student?->only('id', 'student_code', 'full_name'),
            'admin_class' => $member->adminClass?->only('id', 'code', 'name'),
            'effective_from' => $member->effective_from->toDateString(),
            'effective_to' => $member->effective_to?->toDateString(),
            'reason' => $member->reason,
        ];
    }

    /** @return array<string, mixed> */
    private function presentAdvisor(AdvisorAssignment $assignment): array
    {
        return [
            'id' => $assignment->id,
            'lecturer' => $assignment->lecturer?->only('id', 'code', 'full_name', 'status'),
            'effective_from' => $assignment->effective_from->toDateString(),
            'effective_to' => $assignment->effective_to?->toDateString(),
            'reason' => $assignment->reason,
            'end_reason' => $assignment->end_reason,
        ];
    }
}
