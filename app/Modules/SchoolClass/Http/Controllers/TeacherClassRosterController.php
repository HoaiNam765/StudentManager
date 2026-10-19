<?php

namespace App\Modules\SchoolClass\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\SchoolClass\Models\AdminClass;
use App\Modules\SchoolClass\Models\AdminClassMember;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Services\ClassDirectoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Danh sách lớp được giảng viên phụ trách và sinh viên thuộc lớp (FR-TCH-011, BR-TCH-04). */
class TeacherClassRosterController extends Controller
{
    public function __construct(
        private readonly AccessControl $access,
        private readonly ClassDirectoryService $directory,
    ) {}

    public function sections(Request $request): JsonResponse
    {
        $sections = $this->directory->taughtSections($request->user())->paginate(50);

        return response()->json($sections->through(fn (CourseSection $section) => [
            'id' => $section->id,
            'code' => $section->code,
            'status' => $section->status->value,
            'subject' => $section->subject?->only('id', 'code', 'name'),
            'term' => $section->term?->only('id', 'name'),
            'academic_year' => $section->term?->academicYear?->only('id', 'name'),
        ]));
    }

    public function sectionStudents(Request $request, CourseSection $courseSection): JsonResponse
    {
        abort_unless(
            $this->access->allowsOn($request->user(), 'CLS', PermissionAction::View, $courseSection),
            403,
            'Bạn không có quyền xem danh sách sinh viên của lớp học phần này. Hãy chọn lớp đang được phân công cho bạn hoặc liên hệ cán bộ Đào tạo.'
        );
        $students = $this->directory->studentsInSection($request->user(), $courseSection)->paginate(50);

        return response()->json($students->through(fn ($enrollment) => [
            'id' => $enrollment->student?->id,
            'student' => $this->directory->presentStudent($enrollment->student),
        ]));
    }

    public function adviseeClasses(Request $request): JsonResponse
    {
        $classes = $this->directory->adviseeClasses($request->user())->paginate(50);

        return response()->json($classes->through(fn (AdminClass $class) => [
            'id' => $class->id,
            'code' => $class->code,
            'name' => $class->name,
            'cohort' => $class->cohort?->only('id', 'code'),
            'major' => $class->major?->only('id', 'code', 'name'),
            'current_students_count' => $class->current_students_count,
        ]));
    }

    public function adviseeStudents(Request $request, AdminClass $adminClass): JsonResponse
    {
        abort_unless(
            $this->access->allowsOn($request->user(), 'CLS', PermissionAction::View, $adminClass),
            403,
            'Bạn không có quyền xem danh sách sinh viên của lớp này. Hãy chọn lớp bạn đang cố vấn hoặc liên hệ cán bộ Đào tạo.'
        );
        $members = $this->directory
            ->studentsInAdminClass($request->user(), $adminClass)
            ->paginate(50);

        return response()->json($members->through(fn (AdminClassMember $member) => [
            'id' => $member->student?->id,
            'student' => $this->directory->presentStudent($member->student, $adminClass),
        ]));
    }
}
