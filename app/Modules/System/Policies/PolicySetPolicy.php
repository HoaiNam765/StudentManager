<?php

namespace App\Modules\System\Policies;

use App\Modules\Auth\Policies\ModulePolicy;

/**
 * Bộ quy chế đào tạo: theo ma trận phân quyền, mục "SYS" (ADMIN đủ quyền; ACAD xem và sửa, nên soạn và ban hành
 * được, đúng FR-SYS-003). Bộ đã ban hành không sửa được ở bất kỳ vai trò nào (PolicySetService).
 */
class PolicySetPolicy extends ModulePolicy
{
    protected string $module = 'SYS';
}
