<?php

namespace App\Modules\Faculty\Services;

use App\Models\User;

/**
 * Đơn vị mà người dùng quản lý, dùng cho phạm vi dữ liệu FACULTY (docs/BA.md mục 4.3) của mọi module:
 *
 *     DataScope::Faculty => $query->whereIn('faculty_id', app(FacultyAccess::class)->facultyIds($user)),
 *
 * Nguồn dữ liệu là nhiệm kỳ lãnh đạo đơn vị (issue #74). Chưa có nhiệm kỳ thì không quản lý đơn vị nào,
 * nên phạm vi FACULTY chưa thấy dữ liệu nào (an toàn mặc định).
 */
class FacultyAccess
{
    /** @return list<int> các khoa người dùng đang quản lý */
    public function facultyIds(User $user): array
    {
        return [];
    }

    /** @return list<int> các bộ môn người dùng đang quản lý (kể cả mọi bộ môn của khoa họ quản lý) */
    public function departmentIds(User $user): array
    {
        return [];
    }
}
