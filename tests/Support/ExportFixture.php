<?php

namespace Tests\Support;

use App\Models\User;
use App\Modules\Auth\Contracts\HasDataScope;
use App\Modules\Auth\Enums\DataScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ExportFixture extends Model implements HasDataScope
{
    protected $table = 'export_fixture';

    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['sensitive_value' => 'encrypted'];
    }

    public function applyDataScope(Builder $query, DataScope $scope, User $user): void
    {
        match ($scope) {
            DataScope::Own => $query->where('owner_id', $user->getKey()),
            default => $query->whereRaw('1 = 0'),
        };
    }
}
