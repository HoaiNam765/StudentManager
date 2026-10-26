<?php

namespace App\Modules\SchoolClass;

use App\Modules\AcademicYear\Models\Term;
use App\Modules\Faculty\Models\Faculty;
use App\Modules\Faculty\Models\Major;
use App\Modules\Faculty\Models\Specialization;
use App\Modules\Faculty\Models\TrainingType;
use App\Modules\Room\Models\Room;
use App\Modules\SchoolClass\Models\AdminClass;
use App\Modules\SchoolClass\Models\Cohort;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\SectionInstructor;
use App\Modules\SchoolClass\Models\TrainingFramework;
use App\Modules\SchoolClass\Models\TrainingFrameworkItem;
use App\Modules\SchoolClass\Policies\SchoolClassPolicy;
use App\Modules\SchoolClass\Services\SectionSubjectAssignments;
use App\Modules\SchoolClass\Services\SectionTeachingAssignments;
use App\Modules\Subject\Contracts\SubjectAssignments;
use App\Modules\Subject\Models\Subject;
use App\Modules\System\Models\PolicySet;
use App\Modules\Teacher\Contracts\TeachingAssignments;
use App\Modules\Teacher\Models\Lecturer;
use App\Support\References\ReferenceRegistry;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Đăng ký của module SchoolClass (CLS).
 *
 * - Ngành, chuyên ngành, hệ đào tạo còn lớp hành chính đang hoạt động thì không ngừng được; đã có lớp thì không xóa,
 *   không đổi mã (BR-FAC-01, 05).
 * - Bộ quy chế đã gắn cho khóa thì không xóa.
 * - Lớp học phần (#107): cài `TeachingAssignments` (giảng viên còn lớp thì chưa nghỉ việc được, BR-TCH-02) và
 *   `SubjectAssignments` (phạm vi SECTION của giảng viên). Học phần, học kỳ, phòng, giảng viên đã có lớp thì không xóa.
 */
class SchoolClassServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TeachingAssignments::class, SectionTeachingAssignments::class);
        $this->app->bind(SubjectAssignments::class, SectionSubjectAssignments::class);
    }

    public function boot(): void
    {
        foreach ([Cohort::class, AdminClass::class, CourseSection::class, SectionInstructor::class, TrainingFramework::class, TrainingFrameworkItem::class] as $model) {
            Gate::policy($model, SchoolClassPolicy::class);
        }

        $references = app(ReferenceRegistry::class);
        $activeClass = fn ($q) => $q->where('status', 'active')->whereNull('deleted_at');

        $references->register(Major::class, 'admin_classes', 'major_id', 'lớp hành chính', active: $activeClass);
        $references->register(Specialization::class, 'admin_classes', 'specialization_id', 'lớp hành chính', active: $activeClass);
        $references->register(TrainingType::class, 'admin_classes', 'training_type_id', 'lớp hành chính', active: $activeClass);
        $references->register(PolicySet::class, 'cohorts', 'policy_set_id', 'khóa tuyển sinh');
        $activePeriod = function ($q, ?string $on = null): void {
            $date = $on ?? now(config('studentmanager.display_timezone'))->toDateString();
            $q->whereNull('deleted_at')->whereDate('effective_from', '<=', $date)
                ->where(fn ($period) => $period->whereNull('effective_to')->orWhereDate('effective_to', '>=', $date));
        };
        $references->register(AdminClass::class, 'admin_class_members', 'admin_class_id', 'sinh viên trong lớp', active: $activePeriod);
        $references->register(AdminClass::class, 'advisor_assignments', 'admin_class_id', 'cố vấn lớp', active: $activePeriod);
        $references->register(Lecturer::class, 'advisor_assignments', 'lecturer_id', 'lớp đang cố vấn', active: $activePeriod);

        $activeSection = fn ($q) => $q->whereIn('status', ['planned', 'registration_open', 'registration_closed', 'in_progress'])->whereNull('deleted_at');
        $references->register(Subject::class, 'course_sections', 'subject_id', 'lớp học phần', active: $activeSection);
        $references->register(Term::class, 'course_sections', 'term_id', 'lớp học phần');
        $references->register(Room::class, 'course_sections', 'room_id', 'lớp học phần');
        $references->register(Lecturer::class, 'section_instructors', 'lecturer_id', 'phân công lớp học phần');

        // Chương trình khung theo khoa – khóa – học kỳ
        $references->register(Faculty::class, 'training_frameworks', 'faculty_id', 'chương trình khung');
        $references->register(Cohort::class, 'training_frameworks', 'cohort_id', 'chương trình khung');
        $references->register(Term::class, 'training_frameworks', 'term_id', 'chương trình khung');
        $references->register(Subject::class, 'training_framework_items', 'subject_id', 'chương trình khung');
    }
}
