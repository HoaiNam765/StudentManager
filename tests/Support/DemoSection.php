<?php

namespace Tests\Support;

use App\Models\User;
use App\Modules\Auth\Contracts\HasDataScope;
use App\Modules\Auth\Enums\DataScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Ví dụ "lớp học phần": giảng viên chỉ thấy lớp mình dạy (SECTION), cố vấn thấy lớp mình cố vấn (ADVISEE).
 * Cố ý không xử lý FACULTY để kiểm tra AccessControl từ chối khi model không khai báo phạm vi.
 */
class DemoSection extends Model implements HasDataScope
{
    protected $table = 'demo_sections';

    protected $guarded = [];

    public function applyDataScope(Builder $query, DataScope $scope, User $user): void
    {
        match ($scope) {
            DataScope::Section => $query->where('lecturer_id', $user->id),
            DataScope::Advisee => $query->where('advisor_id', $user->id),
            default => null,
        };
    }
}
