<?php

namespace App\Modules\Auth\Contracts;

use App\Models\User;
use App\Modules\Auth\Enums\DataScope;
use Illuminate\Database\Eloquent\Builder;

/**
 * Model khai báo cách lọc dữ liệu theo phạm vi của người dùng (docs/BA.md mục 4.3).
 * Phạm vi ALL không cần xử lý: AccessControl tự bỏ qua lọc.
 *
 * Ví dụ với lớp học phần:
 *
 *     public function applyDataScope(Builder $query, DataScope $scope, User $user): void
 *     {
 *         match ($scope) {
 *             DataScope::Section => $query->whereHas('instructors', fn ($q) => $q->where('user_id', $user->id)),
 *             DataScope::Faculty => $query->whereIn('faculty_id', FacultyAccess::idsFor($user)),
 *             default => $query->whereRaw('1 = 0'),  // phạm vi không áp dụng thì từ chối
 *         };
 *     }
 *
 * Nếu một phạm vi không thêm điều kiện nào, AccessControl coi như từ chối (an toàn mặc định).
 */
interface HasDataScope
{
    public function applyDataScope(Builder $query, DataScope $scope, User $user): void;
}
