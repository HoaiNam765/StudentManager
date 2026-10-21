<?php

namespace App\Modules\Grade\Listeners;

use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grade\Events\GradeAdjusted;
use App\Modules\Grade\Events\GradesPublished;
use App\Modules\Grade\Services\GpaService;
use App\Modules\Student\Models\Student;

/** Công bố điểm lớp (EV-GRD-01) / điều chỉnh điểm (EV-GRD-02) → tính lại GPA học kỳ, tích lũy của sinh viên liên quan. */
class RecalculateGpa
{
    public function __construct(private readonly GpaService $gpa) {}

    public function handle(GradesPublished|GradeAdjusted $event): void
    {
        $studentIds = $event instanceof GradesPublished
            ? Enrollment::query()->active()->where('course_section_id', $event->courseSectionId)->pluck('student_id')
            : Enrollment::query()->whereKey($event->enrollmentId)->pluck('student_id');

        Student::query()->whereIn('id', $studentIds->unique())->each(fn (Student $student) => $this->gpa->recalculate($student));
    }
}
