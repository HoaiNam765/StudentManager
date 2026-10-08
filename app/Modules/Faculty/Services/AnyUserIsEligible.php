<?php

namespace App\Modules\Faculty\Services;

use App\Models\User;
use App\Modules\Faculty\Contracts\LeaderEligibility;
use App\Modules\Faculty\Enums\LeadershipPosition;
use App\Modules\Faculty\Models\Department;
use App\Modules\Faculty\Models\Faculty;

/**
 * Bản mặc định khi chưa có hồ sơ giảng viên (TCH): chưa kiểm tra người được giao có thuộc đơn vị hay không.
 * TCH (#88) thay bằng lớp kiểm tra thật (BR-FAC-04).
 */
class AnyUserIsEligible implements LeaderEligibility
{
    public function reasonIneligible(User $user, Faculty|Department $unit, LeadershipPosition $position): ?string
    {
        return null;
    }
}
