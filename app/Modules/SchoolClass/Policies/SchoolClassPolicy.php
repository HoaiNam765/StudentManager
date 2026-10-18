<?php

namespace App\Modules\SchoolClass\Policies;

use App\Modules\Auth\Policies\ModulePolicy;

/**
 * Khóa, lớp hành chính (và lớp học phần): theo ma trận phân quyền, mục "CLS" (ACAD quản lý toàn trường; CTSV xem, sửa;
 * DEAN theo khoa; LEC, ADV, STU xem theo phạm vi khi có dữ liệu).
 */
class SchoolClassPolicy extends ModulePolicy
{
    protected string $module = 'CLS';
}
