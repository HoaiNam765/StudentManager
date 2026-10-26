<?php

namespace App\Modules\SchoolClass\Models;

use App\Models\User;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\Auth\Contracts\HasDataScope;
use App\Modules\Auth\Enums\DataScope;
use App\Modules\Faculty\Models\Faculty;
use App\Modules\Faculty\Services\FacultyAccess;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Chương trình khung của một khoa cho một khóa trong một học kỳ: các học phần dự kiến mở và số lớp.
 * Phạm vi dữ liệu theo quyền CLS: DEAN xem khung của khoa mình (FACULTY).
 */
class TrainingFramework extends StandardModel implements HasDataScope
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['notified_at' => 'immutable_datetime'];
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TrainingFrameworkItem::class);
    }

    public function applyDataScope(Builder $query, DataScope $scope, User $user): void
    {
        match ($scope) {
            DataScope::Faculty => $query->whereIn($this->qualifyColumn('faculty_id'), app(FacultyAccess::class)->facultyIds($user)),
            default => $query->whereRaw('1 = 0'),
        };
    }
}
