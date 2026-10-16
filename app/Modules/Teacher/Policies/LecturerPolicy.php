<?php

namespace App\Modules\Teacher\Policies;

use App\Modules\Auth\Policies\ModulePolicy;

/**
 * Hồ sơ giảng viên: theo ma trận phân quyền, mục "TCH" (ACAD quản lý toàn trường; DEAN quản lý giảng viên thuộc đơn vị
 * mình; LEC, ADV xem và sửa hồ sơ của chính mình trong giới hạn BR-TCH-07; các vai trò khác xem thông tin cơ bản).
 */
class LecturerPolicy extends ModulePolicy
{
    protected string $module = 'TCH';
}
