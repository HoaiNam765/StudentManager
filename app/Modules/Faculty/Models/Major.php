<?php

namespace App\Modules\Faculty\Models;

use App\Models\User;
use App\Modules\Auth\Contracts\HasDataScope;
use App\Modules\Auth\Enums\DataScope;
use App\Modules\Faculty\Enums\EducationLevel;
use App\Modules\Faculty\Services\FacultyAccess;
use App\Support\Concerns\HasActiveStatus;
use App\Support\Concerns\HasVietnameseSearch;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Ngành đào tạo, thuộc đúng một khoa quản lý (FR-FAC-003, BR-FAC-02).
 * `standard_terms` (thời gian đào tạo chuẩn) là căn cứ tính thời gian học tối đa (STU) và khối lượng
 * học tập trung bình mỗi học kỳ (ENR) (BR-FAC-07): `averageCreditsPerTerm()`.
 */
class Major extends StandardModel implements HasDataScope
{
    use HasActiveStatus;
    use HasVietnameseSearch;

    protected $guarded = [];

    protected array $searchable = ['code', 'name', 'name_en'];

    protected function casts(): array
    {
        return [
            'education_level' => EducationLevel::class,
            'total_credits' => 'integer',
            'standard_terms' => 'integer',
        ];
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function specializations(): HasMany
    {
        return $this->hasMany(Specialization::class);
    }

    /** Nhãn tiếng Việt của trình độ (`$major->education_level_label`), dùng khi xuất Excel. */
    public function getEducationLevelLabelAttribute(): string
    {
        return $this->education_level->label();
    }

    /** Khối lượng học tập trung bình một học kỳ của chương trình chuẩn (BR-FAC-07). */
    public function averageCreditsPerTerm(): float
    {
        return round($this->total_credits / max(1, $this->standard_terms), 2);
    }

    public function applyDataScope(Builder $query, DataScope $scope, User $user): void
    {
        match ($scope) {
            DataScope::Faculty => $query->whereIn('faculty_id', app(FacultyAccess::class)->facultyIds($user)),
            default => $query->whereRaw('1 = 0'),
        };
    }
}
