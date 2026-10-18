<?php

namespace App\Modules\Enrollment\Policies;

use App\Modules\Auth\Policies\ModulePolicy;

/** Đợt đăng ký, đăng ký học phần: theo ma trận phân quyền mục "ENR" (ACAD quản lý; STU đăng ký của mình). */
class EnrollmentPolicy extends ModulePolicy
{
    protected string $module = 'ENR';
}
