<?php

namespace App\Modules\Student\Policies;

use App\Modules\Auth\Policies\ModulePolicy;

/**
 * Hồ sơ sinh viên: theo ma trận phân quyền, mục "STU" (CTSV, ACAD quản lý toàn trường; DEAN xem sinh viên ngành thuộc
 * khoa mình; STU hồ sơ của chính mình; LEC, ADV theo lớp dạy / lớp cố vấn khi có dữ liệu).
 */
class StudentPolicy extends ModulePolicy
{
    protected string $module = 'STU';
}
