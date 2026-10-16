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
 * Bản mặc định (AnyUserIsEligible) không chặn ai; module TCH (#88) bind lớp thật `TeacherLeaderEligibility`
 * trong TeacherServiceProvider.
 */
interface LeaderEligibility
{
    /**
     * Lý do không đủ điều kiện (tiếng Việt), hoặc null nếu đủ điều kiện. $startsOn, $endsOn (Y-m-d) là thời gian nhiệm kỳ
     * dự kiến, dùng cho quy tắc mỗi người chỉ giữ chức vụ trưởng ở một đơn vị tại một thời điểm (BR-TCH-06).
     */
    public function reasonIneligible(User $user, Faculty|Department $unit, LeadershipPosition $position, ?string $startsOn = null, ?string $endsOn = null): ?string;
}
