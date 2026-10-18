<?php

namespace App\Modules\Student;

use App\Modules\Curriculum\Models\Curriculum;
use App\Modules\Faculty\Models\Major;
use App\Modules\Faculty\Models\Specialization;
use App\Modules\Faculty\Models\TrainingType;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Models\StudentContact;
use App\Modules\Student\Policies\StudentPolicy;
use App\Modules\System\Models\AdministrativeUnit;
use App\Modules\System\Models\LookupValue;
use App\Support\References\ReferenceRegistry;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Đăng ký của module Student (STU).
 *
 * - Danh mục dùng chung, đơn vị hành chính, ngành, chuyên ngành, hệ đào tạo, CTĐT mà hồ sơ sinh viên đang dùng thì
 *   không xóa / không đổi mã (BR-SYS-09, BR-FAC-01).
 * - Ngành, chuyên ngành, hệ đào tạo còn sinh viên đang học (chờ nhập học, đang học, bảo lưu) thì không ngừng được cho tới
 *   khi chuyển hết (BR-FAC-05).
 */
class StudentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(Student::class, StudentPolicy::class);
        Gate::policy(StudentContact::class, StudentPolicy::class);

        $references = app(ReferenceRegistry::class);
        $enrolled = fn ($q) => $q->whereIn('status', ['pending', 'studying', 'suspended'])->whereNull('deleted_at');

        foreach (['gender_id', 'ethnicity_id', 'religion_id', 'nationality_id', 'priority_group_id', 'priority_area_id'] as $column) {
            $references->register(LookupValue::class, 'students', $column, 'sinh viên');
        }

        $references->register(AdministrativeUnit::class, 'students', 'permanent_unit_id', 'sinh viên (địa chỉ thường trú)');
        $references->register(AdministrativeUnit::class, 'students', 'temporary_unit_id', 'sinh viên (địa chỉ tạm trú)');
        $references->register(Major::class, 'students', 'major_id', 'sinh viên', active: $enrolled);
        $references->register(Specialization::class, 'students', 'specialization_id', 'sinh viên', active: $enrolled);
        $references->register(TrainingType::class, 'students', 'training_type_id', 'sinh viên', active: $enrolled);
        $references->register(Curriculum::class, 'students', 'curriculum_id', 'sinh viên', active: $enrolled);
        $references->register(Curriculum::class, 'student_curricula', 'curriculum_id', 'lịch sử gán CTĐT của sinh viên');
    }
}
