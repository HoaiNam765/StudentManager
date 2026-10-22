<?php

namespace App\Modules\Grade;

use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Support\References\ReferenceRegistry;
use Illuminate\Support\ServiceProvider;

/** Điểm (GRD): cơ cấu điểm lớp học phần, điểm thành phần. */
class GradeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Lớp đã có cơ cấu điểm / sinh viên đã có điểm thì không xóa (báo lỗi nghiệp vụ thay vì lỗi khóa ngoại)
        $references = app(ReferenceRegistry::class);
        $references->register(CourseSection::class, 'section_grade_components', 'course_section_id', 'thành phần điểm của lớp');
        $references->register(CourseSection::class, 'section_grade_workflows', 'course_section_id', 'tiến trình duyệt điểm');
        $references->register(Enrollment::class, 'grade_entries', 'enrollment_id', 'điểm thành phần');
        $references->register(Enrollment::class, 'course_results', 'enrollment_id', 'kết quả học phần');
        $references->register(Enrollment::class, 'grade_change_requests', 'enrollment_id', 'yêu cầu điều chỉnh điểm');
        $references->register(Enrollment::class, 'grade_versions', 'enrollment_id', 'lịch sử phiên bản điểm');
    }
}
