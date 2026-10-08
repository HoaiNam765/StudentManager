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

/** Chuyên ngành, thuộc đúng một ngành (FR-FAC-004, BR-FAC-02). */
class Specialization extends StandardModel implements HasDataScope
{
    use HasActiveStatus;
    use HasVietnameseSearch;

    protected $guarded = [];

    protected array $searchable = ['code', 'name', 'name_en'];

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    public function applyDataScope(Builder $query, DataScope $scope, User $user): void
    {
        match ($scope) {
            DataScope::Faculty => $query->whereIn('major_id', Major::query()->select('id')->whereIn('faculty_id', app(FacultyAccess::class)->facultyIds($user))),
            default => $query->whereRaw('1 = 0'),
        };
    }
}
