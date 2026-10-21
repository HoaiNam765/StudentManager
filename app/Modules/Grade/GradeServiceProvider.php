<?php

namespace App\Modules\Grade;

use App\Modules\Enrollment\Contracts\AcademicRecord;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grade\Events\GradeAdjusted;
use App\Modules\Grade\Events\GradesPublished;
use App\Modules\Grade\Listeners\RecalculateGpa;
use App\Modules\Grade\Services\GradeAcademicRecord;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Support\References\ReferenceRegistry;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

/** Điểm (GRD): cơ cấu điểm lớp học phần, điểm thành phần, công bố, GPA, điều chỉnh điểm. */
class GradeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Kết quả học tập thật cho điều kiện đăng ký học phần (thay EmptyAcademicRecord của ENR)
        $this->app->bind(AcademicRecord::class, GradeAcademicRecord::class);
    }

    public function boot(): void
    {
        // Lớp đã có cơ cấu điểm / sinh viên đã có điểm thì không xóa (báo lỗi nghiệp vụ thay vì lỗi khóa ngoại)
        $references = app(ReferenceRegistry::class);
        $references->register(CourseSection::class, 'section_grade_components', 'course_section_id', 'thành phần điểm của lớp');
        $references->register(Enrollment::class, 'grade_entries', 'enrollment_id', 'điểm thành phần');

        Event::listen(GradesPublished::class, RecalculateGpa::class);
        Event::listen(GradeAdjusted::class, RecalculateGpa::class);
    }
}
