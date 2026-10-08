<?php

namespace App\Modules\Faculty;

use App\Modules\Faculty\Contracts\LeaderEligibility;
use App\Modules\Faculty\Models\Department;
use App\Modules\Faculty\Models\Faculty;
use App\Modules\Faculty\Models\LeadershipTerm;
use App\Modules\Faculty\Models\Major;
use App\Modules\Faculty\Models\Specialization;
use App\Modules\Faculty\Models\TrainingType;
use App\Modules\Faculty\Policies\UnitPolicy;
use App\Modules\Faculty\Services\AnyUserIsEligible;
use App\Modules\Faculty\Services\FacultyAccess;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Đăng ký của module Faculty (FAC). Module khác lưu khóa ngoại tới khoa, bộ môn, ngành, chuyên ngành, hệ đào tạo
 * thì đăng ký với ReferenceRegistry trong ServiceProvider của mình; khai báo thêm điều kiện "còn hoạt động"
 * để chặn ngừng đơn vị khi chưa chuyển hết dữ liệu (BR-FAC-05), ví dụ trong TeacherServiceProvider:
 *
 *     app(ReferenceRegistry::class)->register(Department::class, 'teachers', 'department_id', 'giảng viên',
 *         active: fn ($q) => $q->where('status', 'working'));
 */
class FacultyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Đơn vị người dùng quản lý được nhớ trong một yêu cầu
        $this->app->scoped(FacultyAccess::class);

        // Chưa có module TCH: chưa kiểm tra người được giao chức vụ có thuộc đơn vị không (TCH #88 bind lại)
        $this->app->bindIf(LeaderEligibility::class, AnyUserIsEligible::class);
    }

    public function boot(): void
    {
        foreach ([Faculty::class, Department::class, Major::class, Specialization::class, TrainingType::class, LeadershipTerm::class] as $model) {
            Gate::policy($model, UnitPolicy::class);
        }
    }
}
