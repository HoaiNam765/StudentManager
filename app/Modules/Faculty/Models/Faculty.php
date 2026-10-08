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
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Khoa (FR-FAC-001). Phạm vi FACULTY: chỉ khoa mà người dùng đang quản lý. */
class Faculty extends StandardModel implements HasDataScope
{
    use HasActiveStatus;
    use HasVietnameseSearch;

    protected $guarded = [];

    protected array $searchable = ['code', 'name', 'name_en'];

    protected function casts(): array
    {
        return ['founded_on' => 'date'];
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function majors(): HasMany
    {
        return $this->hasMany(Major::class);
    }

    public function applyDataScope(Builder $query, DataScope $scope, User $user): void
    {
        match ($scope) {
            DataScope::Faculty => $query->whereIn('id', app(FacultyAccess::class)->facultyIds($user)),
            default => $query->whereRaw('1 = 0'),
        };
    }
}
