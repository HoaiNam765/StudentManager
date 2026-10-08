<?php

namespace App\Modules\Room\Policies;

use App\Modules\Auth\Policies\ModulePolicy;

/** Phòng học và cơ sở vật chất: theo ma trận phân quyền, mục "ROM" (ADMIN, ACAD quản lý; các vai trò khác xem). */
class RoomPolicy extends ModulePolicy
{
    protected string $module = 'ROM';
}
