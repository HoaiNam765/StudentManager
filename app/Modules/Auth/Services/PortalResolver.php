<?php

namespace App\Modules\Auth\Services;

use App\Models\User;

/**
 * Chọn trang chủ theo vai trò sau khi đăng nhập (FR-AUTH-001; các cổng ở docs/BA.md mục 8.1).
 * Có nhiều vai trò thì ưu tiên Cổng Quản trị / Văn phòng, rồi Cổng Giảng viên, rồi Cổng Sinh viên.
 * Vai trò tự tạo (không thuộc nhóm giảng viên hay sinh viên) được coi là cán bộ văn phòng.
 */
class PortalResolver
{
    public const TEACHER_ROLES = ['LEC', 'ADV'];

    public const STUDENT_ROLES = ['STU', 'GUA'];

    /** Tên route trang chủ; null nếu người dùng chưa có vai trò nào đang hiệu lực. */
    public function homeRouteFor(User $user): ?string
    {
        $codes = $user->activeRoles()->pluck('roles.code')->unique()->all();

        return match (true) {
            array_diff($codes, self::TEACHER_ROLES, self::STUDENT_ROLES) !== [] => 'admin.home',
            array_intersect($codes, self::TEACHER_ROLES) !== [] => 'teacher.home',
            array_intersect($codes, self::STUDENT_ROLES) !== [] => 'student.home',
            default => null,
        };
    }
}
