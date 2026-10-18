<?php

namespace App\Modules\Teacher;

use App\Modules\Faculty\Contracts\LeaderEligibility;
use App\Modules\Faculty\Models\Department;
use App\Modules\Faculty\Models\Faculty;
use App\Modules\Subject\Contracts\SubjectAssignments;
use App\Modules\System\Models\LookupValue;
use App\Modules\System\Services\ImportRegistry;
use App\Modules\Teacher\Contracts\TeachingAssignments;
use App\Modules\Teacher\Importers\LecturerImporter;
use App\Modules\Teacher\Models\Lecturer;
use App\Modules\Teacher\Models\LecturerChangeRequest;
use App\Modules\Teacher\Models\LecturerUnit;
use App\Modules\Teacher\Policies\LecturerPolicy;
use App\Modules\Teacher\Services\DepartmentSubjectAssignments;
use App\Modules\Teacher\Services\NoTeachingAssignments;
use App\Modules\Teacher\Services\TeacherLeaderEligibility;
use App\Support\References\ReferenceRegistry;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Đăng ký của module Teacher (TCH).
 *
 * - Giao chức vụ lãnh đạo đơn vị kiểm tra theo hồ sơ giảng viên (BR-FAC-04, BR-TCH-06).
 * - Lớp đang dạy cần chuyển khi giảng viên nghỉ (BR-TCH-02): CLS bind `TeachingAssignments` bằng lớp thật.
 * - Bộ môn, khoa còn giảng viên đang làm việc / kiêm nhiệm thì không ngừng, không xóa được (BR-FAC-05).
 */
class TeacherServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LeaderEligibility::class, TeacherLeaderEligibility::class);
        $this->app->bindIf(TeachingAssignments::class, NoTeachingAssignments::class);

        // Học phần giảng viên phụ trách (phạm vi SECTION): tạm theo bộ môn, chờ CLS (#107)
        $this->app->bind(SubjectAssignments::class, DepartmentSubjectAssignments::class);
    }

    public function boot(): void
    {
        foreach ([Lecturer::class, LecturerUnit::class, LecturerChangeRequest::class] as $model) {
            Gate::policy($model, LecturerPolicy::class);
        }

        // Nhập danh sách giảng viên qua trung tâm import (FR-TCH-013)
        $this->app->make(ImportRegistry::class)->registerLazy(LecturerImporter::KEY, fn () => $this->app->make(LecturerImporter::class));

        $references = app(ReferenceRegistry::class);
        $working = fn ($q) => $q->where('status', 'working')->whereNull('deleted_at');
        $current = fn ($q) => $q->whereNull('deleted_at')
            ->where(fn ($w) => $w->whereNull('ends_on')->orWhereDate('ends_on', '>=', now(config('studentmanager.display_timezone'))->toDateString()));

        $references->register(Department::class, 'lecturers', 'department_id', 'giảng viên', active: $working);
        $references->register(Department::class, 'lecturer_units', 'unit_id', 'giảng viên kiêm nhiệm',
            constraint: fn ($q) => $q->where('unit_type', 'department'), active: $current);
        $references->register(Faculty::class, 'lecturer_units', 'unit_id', 'giảng viên kiêm nhiệm',
            constraint: fn ($q) => $q->where('unit_type', 'faculty'), active: $current);
        $references->register(LookupValue::class, 'lecturers', 'gender_id', 'giảng viên');
    }
}
