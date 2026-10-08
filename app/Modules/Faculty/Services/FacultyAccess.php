<?php

namespace App\Modules\Faculty\Services;

use App\Models\User;
use App\Modules\Faculty\Models\Department;
use App\Modules\Faculty\Models\LeadershipTerm;

/**
 * Đơn vị mà người dùng quản lý, dùng cho phạm vi dữ liệu FACULTY (docs/BA.md mục 4.3) của mọi module:
 *
 *     DataScope::Faculty => $query->whereIn('faculty_id', app(FacultyAccess::class)->facultyIds($user)),
 *
 * Nguồn là nhiệm kỳ lãnh đạo đơn vị đang hiệu lực (BR-FAC-06): trưởng/phó khoa quản lý khoa và mọi bộ môn của khoa;
 * trưởng/phó bộ môn chỉ quản lý bộ môn đó. Kết quả nhớ trong một yêu cầu (đăng ký `scoped`); đổi nhiệm kỳ thì
 * LeadershipService gọi flush().
 */
class FacultyAccess
{
    /** @var array<string, list<int>> */
    private array $cache = [];

    /** @return list<int> các khoa người dùng đang quản lý */
    public function facultyIds(User $user, ?string $day = null): array
    {
        $day ??= $this->today();

        return $this->cache["faculty|{$user->id}|{$day}"] ??= $this->unitIds($user, 'faculty', $day);
    }

    /** @return list<int> các bộ môn người dùng đang quản lý (kể cả mọi bộ môn của khoa họ quản lý) */
    public function departmentIds(User $user, ?string $day = null): array
    {
        $day ??= $this->today();

        return $this->cache["department|{$user->id}|{$day}"] ??= collect($this->unitIds($user, 'department', $day))
            ->merge(Department::query()->whereIn('faculty_id', $this->facultyIds($user, $day))->pluck('id'))
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    public function flush(): void
    {
        $this->cache = [];
    }

    /** @return list<int> */
    private function unitIds(User $user, string $type, string $day): array
    {
        return LeadershipTerm::query()
            ->where('user_id', $user->id)
            ->where('unit_type', $type)
            ->effectiveOn($day)
            ->pluck('unit_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    private function today(): string
    {
        return now(config('studentmanager.display_timezone'))->toDateString();
    }
}
