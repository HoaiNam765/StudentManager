<?php

namespace Tests\Support;

use App\Models\User;
use App\Modules\Auth\Contracts\HasDataScope;
use App\Modules\Auth\Enums\DataScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** Ví dụ "hồ sơ sinh viên": sinh viên chỉ thấy hồ sơ của chính mình (OWN). */
class DemoProfile extends Model implements HasDataScope
{
    protected $table = 'demo_profiles';

    protected $guarded = [];

    public function applyDataScope(Builder $query, DataScope $scope, User $user): void
    {
        match ($scope) {
            DataScope::Own => $query->where('user_id', $user->id),
            default => $query->whereRaw('1 = 0'),
        };
    }
}
