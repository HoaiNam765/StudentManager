<?php

namespace App\Modules\Enrollment;

use App\Modules\AcademicYear\Models\Term;
use App\Modules\Enrollment\Contracts\AcademicRecord;
use App\Modules\Enrollment\Contracts\TuitionStatus;
use App\Modules\Enrollment\Enums\EnrollmentStatus;
use App\Modules\Enrollment\Listeners\CancelEnrollmentsOfSection;
use App\Modules\Enrollment\Listeners\CancelEnrollmentsOnStatusChange;
use App\Modules\Enrollment\Models\RegistrationPeriod;
use App\Modules\Enrollment\Policies\EnrollmentPolicy;
use App\Modules\Enrollment\Services\EmptyAcademicRecord;
use App\Modules\Enrollment\Services\EnrollmentSectionStudents;
use App\Modules\Enrollment\Services\NoTuitionDebt;
use App\Modules\Faculty\Models\Major;
use App\Modules\SchoolClass\Events\CourseSectionCancelled;
use App\Modules\SchoolClass\Models\AdminClass;
use App\Modules\SchoolClass\Models\Cohort;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Student\Events\StudentStatusChanged;
use App\Modules\Student\Models\Student;
use App\Modules\Timetable\Contracts\SectionStudents;
use App\Support\References\ReferenceRegistry;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/** Đăng ký học phần (ENR): đợt đăng ký, điều kiện, đăng ký / hủy / rút / đổi lớp. */
class EnrollmentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // GRD, FEE bind cài đặt thật khi có
        $this->app->bindIf(AcademicRecord::class, EmptyAcademicRecord::class);
        $this->app->bindIf(TuitionStatus::class, NoTuitionDebt::class);

        // Thay cài đặt tạm của TTB: sinh viên của lớp lấy từ bảng enrollments (BR-TTB-08, sĩ số, trùng lịch)
        $this->app->bind(SectionStudents::class, EnrollmentSectionStudents::class);
    }

    public function boot(): void
    {
        Gate::policy(RegistrationPeriod::class, EnrollmentPolicy::class);
        Event::listen(CourseSectionCancelled::class, CancelEnrollmentsOfSection::class);
        Event::listen(StudentStatusChanged::class, CancelEnrollmentsOnStatusChange::class);

        $references = app(ReferenceRegistry::class);
        $references->register(Term::class, 'registration_periods', 'term_id', 'đợt đăng ký học phần');
        $references->register(Cohort::class, 'registration_period_targets', 'cohort_id', 'đối tượng đợt đăng ký');
        $references->register(Major::class, 'registration_period_targets', 'major_id', 'đối tượng đợt đăng ký');
        $references->register(AdminClass::class, 'registration_period_targets', 'admin_class_id', 'đối tượng đợt đăng ký');
        $references->register(CourseSection::class, 'enrollments', 'course_section_id', 'lượt đăng ký', active: fn ($q) => $q->whereIn('status', EnrollmentStatus::activeValues()));
        $references->register(Student::class, 'enrollments', 'student_id', 'lượt đăng ký học phần');
    }
}
