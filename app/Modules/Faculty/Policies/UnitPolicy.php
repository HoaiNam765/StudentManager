<?php

namespace App\Modules\Faculty\Policies;

use App\Modules\Auth\Policies\ModulePolicy;

/**
 * Khoa, bộ môn, ngành, chuyên ngành, hệ đào tạo: theo ma trận phân quyền, mục "FAC"
 * (ADMIN, ACAD quản lý toàn trường; DEAN xem và cập nhật đơn vị mình quản lý; các vai trò khác xem).
 */
class UnitPolicy extends ModulePolicy
{
    protected string $module = 'FAC';
}
