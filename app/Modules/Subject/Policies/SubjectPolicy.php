<?php

namespace App\Modules\Subject\Policies;

use App\Modules\Auth\Policies\ModulePolicy;

/**
 * Học phần: theo ma trận phân quyền, mục "SUB" (ACAD quản lý toàn trường; DEAN thêm, sửa học phần của bộ môn
 * mình quản lý; LEC xem, sửa trong phạm vi lớp được phân công; các vai trò khác xem).
 */
class SubjectPolicy extends ModulePolicy
{
    protected string $module = 'SUB';
}
