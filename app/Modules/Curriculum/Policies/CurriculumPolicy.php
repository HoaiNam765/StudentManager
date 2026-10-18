<?php

namespace App\Modules\Curriculum\Policies;

use App\Modules\Auth\Policies\ModulePolicy;

/**
 * Chương trình đào tạo: theo ma trận phân quyền, mục "CUR" (ACAD quản lý và ban hành toàn trường; DEAN xây dựng CTĐT của
 * ngành thuộc khoa mình; các vai trò khác xem).
 */
class CurriculumPolicy extends ModulePolicy
{
    protected string $module = 'CUR';
}
