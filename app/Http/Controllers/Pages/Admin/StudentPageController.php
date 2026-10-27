<?php

namespace App\Http\Controllers\Pages\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\SchoolClass\Models\AdminClass;
use App\Modules\Student\Models\Student;
use App\Modules\System\Services\ImportRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Trang quản lý sinh viên (FE #16, mock admin/sinh-vien*) và khóa – lớp hành chính (FE #15, mock
 * admin/khoa-va-lop-hanh-chinh). Chỉ trả view + URL API + cờ quyền; dữ liệu do resources/js/pages/admin-student*.js,
 * admin-classes.js gọi API STU / CLS / SYS (cùng middleware quyền và Policy như API).
 */
class StudentPageController extends Controller
{
    public function __construct(private readonly AccessControl $access) {}

    /** Danh sách sinh viên, thẻ thống kê theo trạng thái, yêu cầu sửa hồ sơ chờ duyệt. */
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('admin.student.index', ['pageData' => [
            'api' => $this->lookupApis() + [
                'list' => route('admin.students.index'),
                'export' => route('admin.students.export'),
                'changeRequests' => route('admin.student-change-requests.index'),
                'approveRequest' => route('admin.student-change-requests.approve', ['studentChangeRequest' => '__ID__']),
                'rejectRequest' => route('admin.student-change-requests.reject', ['studentChangeRequest' => '__ID__']),
                'evidence' => route('admin.student-change-requests.evidence', ['studentChangeRequest' => '__ID__']),
            ],
            'links' => [
                'show' => route('admin.pages.students.show', ['student' => '__ID__']),
                'edit' => route('admin.pages.students.edit', ['student' => '__ID__']),
                'create' => route('admin.pages.students.create'),
                'import' => route('admin.pages.students.import'),
            ],
            'can' => [
                'create' => $this->access->allows($user, 'STU', PermissionAction::Create),
                'update' => $this->access->allows($user, 'STU', PermissionAction::Update),
                'approve' => $this->access->allows($user, 'STU', PermissionAction::Approve),
                'export' => $this->access->allows($user, 'STU', PermissionAction::Export),
                'import' => $this->access->allows($user, 'SYS', PermissionAction::Create),
            ],
        ]]);
    }

    /** Hồ sơ chi tiết: thông tin cá nhân (đã che), liên hệ, người thân, kết quả học tập, trạng thái, lịch sử thay đổi. */
    public function show(Request $request, Student $student): View
    {
        Gate::authorize('view', $student);
        $user = $request->user();
        $id = ['student' => $student->id];

        return view('admin.student.show', ['student' => $student, 'pageData' => [
            'api' => [
                'show' => route('admin.students.show', $id),
                'sensitive' => route('admin.students.sensitive', $id),
                'changeLogs' => route('admin.students.change-logs', $id),
                'status' => route('admin.students.status.index', $id),
                'changeStatus' => route('admin.students.status.store', $id),
                'attachment' => route('admin.student-status-histories.attachment', ['history' => '__ID__']),
                'contacts' => route('admin.students.contacts.store', $id),
                'contact' => route('admin.student-contacts.update', ['studentContact' => '__ID__']),
                'curricula' => route('admin.students.curricula.index', $id),
                'gpa' => route('admin.students.gpa', $id),
                'transcript' => route('admin.students.transcript', $id),
                'admit' => route('admin.students.admit', $id),
                'cancelAdmission' => route('admin.students.cancel-admission', $id),
                'adminClasses' => route('admin.admin-classes.index'),
                'destroy' => route('admin.students.destroy', $id),
            ],
            'links' => [
                'list' => route('admin.pages.students'),
                'edit' => route('admin.pages.students.edit', $id),
            ],
            'can' => [
                'update' => Gate::allows('update', $student),
                'approve' => $this->access->allowsOn($user, 'STU', PermissionAction::Approve, $student),
                'restore' => $this->access->allowsGlobally($user, 'STU', PermissionAction::Approve),
                'delete' => Gate::allows('delete', $student),
                'grades' => $this->access->allows($user, 'GRD', PermissionAction::View),
            ],
        ]]);
    }

    /** Thêm hồ sơ sinh viên (Chờ nhập học). */
    public function create(Request $request): View
    {
        return $this->form($request, null);
    }

    /** Sửa hồ sơ: trường nhạy cảm cần lý do (BR-STU-06), MSSV và trạng thái không sửa ở đây. */
    public function edit(Request $request, Student $student): View
    {
        Gate::authorize('update', $student);

        return $this->form($request, $student);
    }

    /** Nhập hồ sơ sinh viên từ Excel qua trung tâm import (importer `students`, SYS.create). */
    public function import(Request $request, ImportRegistry $importers): View
    {
        $user = $request->user();

        return view('admin.student.import', ['pageData' => [
            'importer' => 'students',
            'api' => [
                'importers' => route('admin.imports.importers'),
                'list' => route('admin.imports.index'),
                'upload' => route('admin.imports.upload'),
                'show' => route('admin.imports.show', ['batch' => '__ID__']),
                'validate' => route('admin.imports.validate', ['batch' => '__ID__']),
                'preview' => route('admin.imports.preview', ['batch' => '__ID__']),
                'save' => route('admin.imports.save', ['batch' => '__ID__']),
                'rollback' => route('admin.imports.rollback', ['batch' => '__ID__']),
            ],
            'links' => ['list' => route('admin.pages.students')],
            'can' => [
                'create' => $this->access->allows($user, 'SYS', PermissionAction::Create),
                'delete' => $this->access->allows($user, 'SYS', PermissionAction::Delete),
            ],
            'available' => $importers->has('students'),
        ]]);
    }

    /** Khóa tuyển sinh, lớp hành chính, sĩ số, cố vấn học tập, thành viên lớp. */
    public function classes(Request $request): View
    {
        $user = $request->user();
        $editCohort = $this->access->allows($user, 'CLS', PermissionAction::Create) || $this->access->allows($user, 'CLS', PermissionAction::Update);

        return view('admin.admin-class.index', ['pageData' => [
            'api' => $this->lookupApis() + [
                'cohortStore' => route('admin.cohorts.store'),
                'cohortUpdate' => route('admin.cohorts.update', ['cohort' => '__ID__']),
                'cohortDestroy' => route('admin.cohorts.destroy', ['cohort' => '__ID__']),
                'classStore' => route('admin.admin-classes.store'),
                'classUpdate' => route('admin.admin-classes.update', ['adminClass' => '__ID__']),
                'classDestroy' => route('admin.admin-classes.destroy', ['adminClass' => '__ID__']),
                'members' => route('admin.admin-classes.members.index', ['adminClass' => '__ID__']),
                'membersExport' => route('admin.admin-classes.members.export', ['adminClass' => '__ID__']),
                'membersPreview' => route('admin.admin-classes.members.preview', ['adminClass' => '__ID__']),
                'membersStore' => route('admin.admin-classes.members.store', ['adminClass' => '__ID__']),
                'membersRemovePreview' => route('admin.admin-classes.members.remove-preview', ['adminClass' => '__ID__']),
                'membersRemove' => route('admin.admin-classes.members.remove', ['adminClass' => '__ID__']),
                'advisors' => route('admin.admin-classes.advisors.index', ['adminClass' => '__ID__']),
                'advisorStore' => route('admin.admin-classes.advisors.store', ['adminClass' => '__ID__']),
                'advisorPreview' => route('admin.admin-classes.advisors.preview', ['adminClass' => '__ID__']),
                'advisorEnd' => route('admin.advisor-assignments.end', ['advisorAssignment' => '__ID__']),
                'missingAdvisors' => route('admin.admin-classes.missing-advisors'),
                'lecturers' => route('admin.lecturers.index'),
                'students' => route('admin.students.index'),
                'policySets' => route('admin.policy-sets.index'),
            ],
            'links' => ['student' => route('admin.pages.students.show', ['student' => '__ID__'])],
            'can' => [
                'createCohort' => $this->access->allows($user, 'CLS', PermissionAction::Create),
                'createClass' => $this->access->allows($user, 'CLS', PermissionAction::Create),
                'update' => $this->access->allows($user, 'CLS', PermissionAction::Update),
                'updateAll' => $this->access->allowsGlobally($user, 'CLS', PermissionAction::Update),
                'delete' => $this->access->allows($user, 'CLS', PermissionAction::Delete),
                'export' => $this->access->allows($user, 'CLS', PermissionAction::Export),
                // Bộ quy chế chỉ dùng trong form khóa tuyển sinh; DEAN, EXAM, CTSV, FIN không có SYS.view nên không gọi (tránh 403 ghi nhật ký)
                'policySets' => $editCohort && $this->access->allows($user, 'SYS', PermissionAction::View),
            ],
            'totalClasses' => AdminClass::query()->count(),
        ]]);
    }

    private function form(Request $request, ?Student $student): View
    {
        return view('admin.student.form', ['student' => $student, 'pageData' => [
            'mode' => $student === null ? 'create' : 'edit',
            'sensitiveFields' => Student::SENSITIVE_FIELDS,
            'api' => $this->lookupApis() + [
                'store' => route('admin.students.store'),
                'show' => $student === null ? null : route('admin.students.show', ['student' => $student->id]),
                'sensitive' => $student === null ? null : route('admin.students.sensitive', ['student' => $student->id]),
                'update' => $student === null ? null : route('admin.students.update', ['student' => $student->id]),
                'lookup' => route('lookups.options', ['category' => '__CODE__']),
                'units' => route('lookups.administrative-units'),
            ],
            'links' => [
                'list' => route('admin.pages.students'),
                'show' => route('admin.pages.students.show', ['student' => '__ID__']),
            ],
        ]]);
    }

    /** Danh mục cho ô lọc / biểu mẫu: khoa, ngành, chuyên ngành, hệ, khóa, lớp hành chính. */
    private function lookupApis(): array
    {
        return [
            'faculties' => route('admin.units.index', ['unitType' => 'faculties']),
            'majors' => route('admin.units.index', ['unitType' => 'majors']),
            'specializations' => route('admin.units.index', ['unitType' => 'specializations']),
            'trainingTypes' => route('admin.training-types.index'),
            'cohorts' => route('admin.cohorts.index'),
            'adminClasses' => route('admin.admin-classes.index'),
        ];
    }
}
