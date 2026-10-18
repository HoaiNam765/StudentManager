<?php

namespace App\Modules\Subject;

use App\Modules\Faculty\Models\Department;
use App\Modules\Subject\Contracts\SubjectAssignments;
use App\Modules\Subject\Models\Subject;
use App\Modules\Subject\Policies\SubjectPolicy;
use App\Modules\Subject\Services\NoSubjectAssignments;
use App\Support\References\ReferenceRegistry;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Đăng ký của module Subject (SUB).
 *
 * Module lưu khóa ngoại tới `subjects` (CTĐT, lớp học phần, điểm…) đăng ký với ReferenceRegistry trong ServiceProvider
 * của mình; khi đó học phần đã được dùng không xóa, không đổi mã, không đổi tín chỉ (BR-SUB-01, BR-SUB-05):
 *
 *     app(ReferenceRegistry::class)->register(Subject::class, 'course_sections', 'subject_id', 'lớp học phần',
 *         active: fn ($q) => $q->whereIn('status', ['open', 'running']));
 */
class SubjectServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Phạm vi SECTION của giảng viên: TCH cài tạm theo bộ môn, CLS cài theo lớp được phân công
        $this->app->bindIf(SubjectAssignments::class, NoSubjectAssignments::class);
    }

    public function boot(): void
    {
        Gate::policy(Subject::class, SubjectPolicy::class);

        // Bộ môn còn quản lý học phần đang hoạt động thì không ngừng, không xóa, không đổi mã được (BR-FAC-01, 05)
        app(ReferenceRegistry::class)->register(Department::class, 'subjects', 'department_id', 'học phần',
            active: fn ($query) => $query->where('status', 'active')->whereNull('deleted_at'));
    }
}
