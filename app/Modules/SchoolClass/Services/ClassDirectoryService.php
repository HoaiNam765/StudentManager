<?php

namespace App\Modules\SchoolClass\Services;

use App\Models\User;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Models\AdminClass;
use App\Modules\SchoolClass\Models\AdminClassMember;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Student\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

/** Các truy vấn danh sách lớp và sinh viên, luôn giới hạn theo quyền CLS/STU của người gọi. */
class ClassDirectoryService
{
    public function __construct(private readonly AccessControl $access) {}

    /** @return Builder<CourseSection> */
    public function taughtSections(User $user): Builder
    {
        return $this->access->constrain(
            CourseSection::query()
                ->with(['subject:id,code,name,credits', 'term:id,name,academic_year_id', 'term.academicYear:id,name'])
                ->whereIn('status', SectionStatus::activeValues())
                ->orderBy('code'),
            $user,
            'CLS',
            PermissionAction::View
        );
    }

    /** @return Builder<AdminClass> */
    public function adviseeClasses(User $user): Builder
    {
        return $this->access->constrain(
            AdminClass::query()
                ->active()
                ->with(['cohort:id,code', 'major:id,code,name'])
                ->withCount(['members as current_students_count' => fn (Builder $query) => $query->current()])
                ->orderBy('code'),
            $user,
            'CLS',
            PermissionAction::View
        );
    }

    /** @return Builder<Enrollment> */
    public function studentsInSection(User $user, CourseSection $section): Builder
    {
        return $this->access->constrain(
            Enrollment::query()
                ->with([
                    'student:id,student_code,full_name,photo_path,school_email,status,date_of_birth',
                    'student.adminClassMembers' => fn (Relation $query) => $query->current()->with('adminClass:id,code,name'),
                ])
                ->where('course_section_id', $section->id)
                ->active()
                ->join('students', 'students.id', '=', 'enrollments.student_id')
                ->orderBy('students.student_code')
                ->select('enrollments.*'),
            $user,
            'CLS',
            PermissionAction::View
        );
    }

    /** @return Builder<AdminClassMember> */
    public function studentsInAdminClass(User $user, AdminClass $class, bool $history = false): Builder
    {
        $visibleClasses = $this->access
            ->constrain(AdminClass::query(), $user, 'CLS', PermissionAction::View)
            ->select('admin_classes.id');
        $visibleStudents = $this->access
            ->constrain(Student::query(), $user, 'STU', PermissionAction::View)
            ->select('students.id');

        return AdminClassMember::query()
            ->with('student:id,student_code,full_name,photo_path,school_email,status,date_of_birth')
            ->where('admin_class_id', $class->id)
            ->whereIn('admin_class_id', $visibleClasses)
            ->whereIn('student_id', $visibleStudents)
            ->when(! $history, fn (Builder $query) => $query->current())
            ->orderByDesc('effective_from')
            ->orderBy('student_id');
    }

    /** @return Builder<Student> */
    public function studentsForExport(User $user, AdminClass $class): Builder
    {
        $exportableClasses = $this->access
            ->constrain(AdminClass::query(), $user, 'CLS', PermissionAction::Export)
            ->select('admin_classes.id');

        return $this->access
            ->constrain(Student::query(), $user, 'CLS', PermissionAction::Export)
            ->whereHas('adminClassMembers', fn (Builder $query) => $query
                ->current()
                ->where('admin_class_id', $class->id)
                ->whereIn('admin_class_id', $exportableClasses))
            ->orderBy('student_code');
    }

    /** @return array<string, mixed>|null */
    public function presentStudent(?Student $student, ?AdminClass $class = null): ?array
    {
        if ($student === null) {
            return null;
        }

        $class ??= $student->adminClassMembers->first()?->adminClass;

        return [
            'id' => $student->id,
            'student_code' => $student->student_code,
            'full_name' => $student->full_name,
            'photo_url' => $student->photo_path === null ? null : asset('storage/'.$student->photo_path),
            'admin_class' => $class?->only('id', 'code', 'name'),
            'school_email' => $student->school_email,
            'date_of_birth' => $student->date_of_birth?->toDateString(),
            'status' => $student->status?->value,
        ];
    }
}
