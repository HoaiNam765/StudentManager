<?php

namespace App\Modules\Faculty\Contracts;

use App\Models\User;
use App\Modules\Faculty\Enums\LeadershipPosition;
use App\Modules\Faculty\Models\Department;
use App\Modules\Faculty\Models\Faculty;

/**
 * Người được giao chức vụ có đủ điều kiện không (BR-FAC-04: phải là giảng viên hoặc cán bộ đang hoạt động thuộc
 * đơn vị đó; cho phép ngoại lệ bằng cấu hình `studentmanager.faculty.allow_leader_outside_unit`).
 *
 * Chưa có module TCH nên bản mặc định (AnyUserIsEligible) không chặn ai; issue hồ sơ giảng viên (#88) cài lớp thật
 * và bind trong ServiceProvider của TCH:
 *
 *     $this->app->bind(LeaderEligibility::class, TeacherLeaderEligibility::class);
 */
interface LeaderEligibility
{
    /** Lý do không đủ điều kiện (tiếng Việt), hoặc null nếu đủ điều kiện. */
    public function reasonIneligible(User $user, Faculty|Department $unit, LeadershipPosition $position): ?string;
}
