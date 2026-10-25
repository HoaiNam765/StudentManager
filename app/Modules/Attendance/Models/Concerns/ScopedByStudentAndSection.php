<?php

namespace App\Modules\Attendance\Models\Concerns;

use App\Models\User;
use App\Modules\Auth\Enums\DataScope;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Student\Models\Student;
use Illuminate\Database\Eloquent\Builder;

/**
 * Phạm vi dữ liệu điểm danh (BR-ATT-08): sinh viên chỉ thấy của mình (OWN), cố vấn thấy sinh viên lớp mình cố vấn
 * (ADVISEE), giảng viên thấy lớp mình dạy (SECTION), lãnh đạo khoa theo học phần của khoa (FACULTY). Bảng dùng trait cần
 * cột `student_id` và `course_section_id`.
 */
trait ScopedByStudentAndSection
{
    public function applyDataScope(Builder $query, DataScope $scope, User $user): void
    {
        match ($scope) {
            DataScope::Own, DataScope::Advisee => $query->whereIn(
                $this->qualifyColumn('student_id'),
                tap(Student::query()->select('students.id'), fn (Builder $q) => (new Student)->applyDataScope($q, $scope, $user)),
            ),
            default => $query->whereIn(
                $this->qualifyColumn('course_section_id'),
                tap(CourseSection::query()->select('id'), fn (Builder $q) => (new CourseSection)->applyDataScope($q, $scope, $user)),
            ),
        };
    }
}
