<?php

namespace App\Modules\Enrollment;

use App\Modules\AcademicYear\Models\Term;
use App\Modules\Enrollment\Models\RegistrationPeriod;
use App\Modules\Enrollment\Policies\EnrollmentPolicy;
use App\Modules\Faculty\Models\Major;
use App\Modules\SchoolClass\Models\AdminClass;
use App\Modules\SchoolClass\Models\Cohort;
use App\Support\References\ReferenceRegistry;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/** Đăng ký học phần (ENR): đợt đăng ký, danh sách lớp được chọn. */
class EnrollmentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(RegistrationPeriod::class, EnrollmentPolicy::class);

        $references = app(ReferenceRegistry::class);
        $references->register(Term::class, 'registration_periods', 'term_id', 'đợt đăng ký học phần');
        $references->register(Cohort::class, 'registration_period_targets', 'cohort_id', 'đối tượng đợt đăng ký');
        $references->register(Major::class, 'registration_period_targets', 'major_id', 'đối tượng đợt đăng ký');
        $references->register(AdminClass::class, 'registration_period_targets', 'admin_class_id', 'đối tượng đợt đăng ký');
    }
}
