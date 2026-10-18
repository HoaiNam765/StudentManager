<?php

namespace App\Modules\Enrollment\Services;

use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Timetable\Contracts\SectionStudents;
use Illuminate\Support\Collection;

/** Cài đặt thật của SectionStudents (TTB) từ bảng `enrollments`: chỉ tính đăng ký còn hiệu lực. */
class EnrollmentSectionStudents implements SectionStudents
{
    public function studentIdsOf(CourseSection $section): array
    {
        return Enrollment::query()->active()->where('course_section_id', $section->id)->pluck('student_id')->all();
    }

    public function otherSectionsOf(int $studentId, CourseSection $section): Collection
    {
        return CourseSection::query()
            ->whereIn('id', Enrollment::query()->active()
                ->where('student_id', $studentId)
                ->where('term_id', $section->term_id)
                ->where('course_section_id', '!=', $section->id)
                ->select('course_section_id'))
            ->get();
    }
}
