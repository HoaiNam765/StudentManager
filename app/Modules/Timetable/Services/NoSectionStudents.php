<?php

namespace App\Modules\Timetable\Services;

use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Timetable\Contracts\SectionStudents;
use Illuminate\Support\Collection;

/** Cài đặt tạm của SectionStudents khi chưa có ENR: chưa lớp nào có sinh viên. */
class NoSectionStudents implements SectionStudents
{
    public function studentIdsOf(CourseSection $section): array
    {
        return [];
    }

    public function otherSectionsOf(int $studentId, CourseSection $section): Collection
    {
        return collect();
    }

    public function sectionIdsOfStudents(array $studentIds, int $termId): array
    {
        return [];
    }
}
