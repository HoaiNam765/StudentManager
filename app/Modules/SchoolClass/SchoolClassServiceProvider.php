<?php

namespace App\Modules\SchoolClass;

use App\Modules\Faculty\Models\Major;
use App\Modules\Faculty\Models\Specialization;
use App\Modules\Faculty\Models\TrainingType;
use App\Modules\SchoolClass\Models\AdminClass;
use App\Modules\SchoolClass\Models\Cohort;
use App\Modules\SchoolClass\Policies\SchoolClassPolicy;
use App\Modules\System\Models\PolicySet;
use App\Support\References\ReferenceRegistry;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Đăng ký của module SchoolClass (CLS).
 *
 * - Ngành, chuyên ngành, hệ đào tạo còn lớp hành chính đang hoạt động thì không ngừng được; đã có lớp thì không xóa,
 *   không đổi mã (BR-FAC-01, 05).
 * - Bộ quy chế đã gắn cho khóa thì không xóa.
 */
class SchoolClassServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(Cohort::class, SchoolClassPolicy::class);
        Gate::policy(AdminClass::class, SchoolClassPolicy::class);

        $references = app(ReferenceRegistry::class);
        $activeClass = fn ($q) => $q->where('status', 'active')->whereNull('deleted_at');

        $references->register(Major::class, 'admin_classes', 'major_id', 'lớp hành chính', active: $activeClass);
        $references->register(Specialization::class, 'admin_classes', 'specialization_id', 'lớp hành chính', active: $activeClass);
        $references->register(TrainingType::class, 'admin_classes', 'training_type_id', 'lớp hành chính', active: $activeClass);
        $references->register(PolicySet::class, 'cohorts', 'policy_set_id', 'khóa tuyển sinh');
        $references->register(AdminClass::class, 'admin_class_members', 'admin_class_id', 'sinh viên trong lớp', active: fn ($q) => $q->whereNull('effective_to')->whereNull('deleted_at'));
    }
}
