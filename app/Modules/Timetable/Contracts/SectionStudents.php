<?php

namespace App\Modules\Timetable\Contracts;

use App\Modules\SchoolClass\Models\CourseSection;
use Illuminate\Support\Collection;

/**
 * Sinh viên đã đăng ký lớp học phần, để TTB kiểm tra lại trùng lịch khi đổi lịch (BR-TTB-08).
 * Mặc định chưa có ai (NoSectionStudents); ENR (#105) bind cài đặt thật.
 */
interface SectionStudents
{
    /** @return list<int> id sinh viên đang đăng ký lớp */
    public function studentIdsOf(CourseSection $section): array;

    /** @return Collection<int, CourseSection> các lớp khác cùng học kỳ sinh viên đang đăng ký */
    public function otherSectionsOf(int $studentId, CourseSection $section): Collection;
}
