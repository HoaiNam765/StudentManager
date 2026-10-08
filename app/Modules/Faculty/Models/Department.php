<?php

namespace App\Modules\Faculty\Models;

use App\Models\User;
use App\Modules\Auth\Contracts\HasDataScope;
use App\Modules\Auth\Enums\DataScope;
use App\Modules\Faculty\Services\FacultyAccess;
use App\Support\Concerns\HasActiveStatus;
use App\Support\Concerns\HasVietnameseSearch;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Bộ môn, thuộc đúng một khoa (FR-FAC-002, BR-FAC-02). */
class Department extends StandardModel implements HasDataScope
{
    use HasActiveStatus;
    use HasVietnameseSearch;

    protected $guarded = [];

    protected array $searchable = ['code', 'name', 'name_en'];

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function applyDataScope(Builder $query, DataScope $scope, User $user): void
    {
        match ($scope) {
            DataScope::Faculty => $query->whereIn('id', app(FacultyAccess::class)->departmentIds($user)),
            default => $query->whereRaw('1 = 0'),
        };
    }
}
