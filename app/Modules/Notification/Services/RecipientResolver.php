<?php

namespace App\Modules\Notification\Services;

use App\Modules\Auth\Models\Role;
use App\Modules\Enrollment\Enums\EnrollmentStatus;
use Illuminate\Support\Facades\DB;

/**
 * Tìm tài khoản người nhận cho thông báo theo sự kiện (phụ lục A, cột "Người nhận"): sinh viên, sinh viên của lớp học
 * phần / học kỳ, giảng viên của lớp, cố vấn học tập hiện hành của sinh viên, ADMIN. Chỉ trả `users.id`.
 */
class RecipientResolver
{
    /** @return list<int> */
    public function students(iterable $studentIds): array
    {
        return $this->ids(DB::table('students')->whereIn('id', collect($studentIds)->all())->whereNotNull('user_id')->pluck('user_id'));
    }

    /** @return list<int> sinh viên còn đăng ký (chưa hủy / rút) ở lớp học phần */
    public function sectionStudents(int $sectionId): array
    {
        return $this->ids(DB::table('enrollments')
            ->join('students', 'students.id', '=', 'enrollments.student_id')
            ->where('enrollments.course_section_id', $sectionId)
            ->whereIn('enrollments.status', EnrollmentStatus::activeValues())
            ->whereNull('enrollments.deleted_at')
            ->whereNotNull('students.user_id')
            ->pluck('students.user_id'));
    }

    /** @return list<int> giảng viên được phân công lớp học phần */
    public function sectionLecturers(int $sectionId): array
    {
        return $this->ids(DB::table('section_instructors')
            ->join('lecturers', 'lecturers.id', '=', 'section_instructors.lecturer_id')
            ->where('section_instructors.course_section_id', $sectionId)
            ->whereNull('section_instructors.deleted_at')
            ->whereNotNull('lecturers.user_id')
            ->pluck('lecturers.user_id'));
    }

    /** @return list<int> sinh viên còn đăng ký một lớp bất kỳ của học kỳ */
    public function termStudents(int $termId): array
    {
        return $this->ids(DB::table('enrollments')
            ->join('students', 'students.id', '=', 'enrollments.student_id')
            ->where('enrollments.term_id', $termId)
            ->whereIn('enrollments.status', EnrollmentStatus::activeValues())
            ->whereNull('enrollments.deleted_at')
            ->whereNotNull('students.user_id')
            ->pluck('students.user_id'));
    }

    /** @return list<int> giảng viên dạy một lớp chưa hủy của học kỳ */
    public function termLecturers(int $termId): array
    {
        return $this->ids(DB::table('section_instructors')
            ->join('course_sections', 'course_sections.id', '=', 'section_instructors.course_section_id')
            ->join('lecturers', 'lecturers.id', '=', 'section_instructors.lecturer_id')
            ->where('course_sections.term_id', $termId)
            ->where('course_sections.status', '!=', 'cancelled')
            ->whereNull('section_instructors.deleted_at')
            ->whereNotNull('lecturers.user_id')
            ->pluck('lecturers.user_id'));
    }

    /** @return list<int> cố vấn học tập hiện hành của lớp hành chính hiện tại của sinh viên */
    public function advisorsOf(int $studentId): array
    {
        $today = now(config('studentmanager.display_timezone'))->toDateString();

        return $this->ids(DB::table('admin_class_members as m')
            ->join('advisor_assignments as a', 'a.admin_class_id', '=', 'm.admin_class_id')
            ->join('lecturers as l', 'l.id', '=', 'a.lecturer_id')
            ->where('m.student_id', $studentId)
            ->whereNull('m.deleted_at')->whereNull('a.deleted_at')
            ->where('m.effective_from', '<=', $today)
            ->where(fn ($q) => $q->whereNull('m.effective_to')->orWhere('m.effective_to', '>=', $today))
            ->where('a.effective_from', '<=', $today)
            ->where(fn ($q) => $q->whereNull('a.effective_to')->orWhere('a.effective_to', '>=', $today))
            ->whereNotNull('l.user_id')
            ->pluck('l.user_id'));
    }

    /** @return list<int> tài khoản đang có vai trò ADMIN */
    public function admins(): array
    {
        $today = now(config('studentmanager.display_timezone'))->toDateString();

        return $this->ids(DB::table('user_roles')
            ->join('roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('roles.code', Role::ADMIN)
            ->where(fn ($q) => $q->whereNull('user_roles.valid_from')->orWhere('user_roles.valid_from', '<=', $today))
            ->where(fn ($q) => $q->whereNull('user_roles.valid_to')->orWhere('user_roles.valid_to', '>=', $today))
            ->pluck('user_roles.user_id'));
    }

    /** @return list<int> */
    private function ids(iterable $values): array
    {
        return collect($values)->filter()->map(fn ($id) => (int) $id)->unique()->values()->all();
    }
}
