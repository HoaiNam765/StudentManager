<?php

namespace App\Modules\System\Policies;

use App\Modules\Auth\Policies\ModulePolicy;

/**
 * Danh mục dùng chung và đơn vị hành chính: theo ma trận phân quyền, mục "SYS"
 * (mặc định ADMIN tạo/sửa/xóa, ACAD xem và sửa). Mọi người dùng đã đăng nhập đọc được danh sách lựa chọn
 * đang hoạt động qua route `lookups.options` và `lookups.administrative-units` (không qua Policy này).
 */
class LookupPolicy extends ModulePolicy
{
    protected string $module = 'SYS';
}
