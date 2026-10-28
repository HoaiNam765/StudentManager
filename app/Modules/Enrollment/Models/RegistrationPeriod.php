<?php

namespace App\Modules\Enrollment\Models;

use App\Modules\AcademicYear\Models\Term;
use App\Modules\Enrollment\Enums\PeriodStatus;
use App\Modules\Enrollment\Enums\RegistrationRound;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\TrainingFramework;
use App\Modules\Student\Models\Student;
use App\Support\Models\StandardModel;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Đợt đăng ký học phần (FR-ENR-001). Thời gian lưu UTC, so với giờ máy chủ (BR-ENR-11). `uses_frameworks`: đợt theo
 * chương trình khung cho cả học kỳ (khung của mọi khoa – khóa, lớp mở trong đợt); ngược lại là đợt theo đối tượng (cũ).
 */
class RegistrationPeriod extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'round' => RegistrationRound::class,
            'status' => PeriodStatus::class,
            'opens_at' => 'immutable_datetime',
            'closes_at' => 'immutable_datetime',
            'preview_at' => 'immutable_datetime',
            'uses_frameworks' => 'boolean',
            'min_credits' => 'integer',
            'max_credits' => 'integer',
        ];
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function targets(): HasMany
    {
        return $this->hasMany(RegistrationPeriodTarget::class);
    }

    /** Khung của các khoa – khóa trong đợt theo chương trình khung (cả học kỳ). */
    public function frameworks(): BelongsToMany
    {
        return $this->belongsToMany(TrainingFramework::class, 'registration_period_frameworks')->withTimestamps();
    }

    public function usesFrameworks(): bool
    {
        return (bool) $this->uses_frameworks;
    }

    /** Khung của đợt đúng khoa (theo ngành) và khóa của sinh viên; null khi sinh viên không thuộc đợt. */
    public function frameworkFor(Student $student): ?TrainingFramework
    {
        if (! $this->usesFrameworks() || $student->major === null) {
            return null;
        }

        return $this->frameworks->first(fn (TrainingFramework $f) => $f->faculty_id === $student->major->faculty_id
            && $f->cohort?->start_year === (int) $student->cohort);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(CourseSection::class);
    }

    /** Đợt đã công bố và đang trong cửa sổ thời gian tại $at (giờ máy chủ). */
    public function scopeOpenAt(Builder $query, CarbonInterface $at): Builder
    {
        return $query->where('status', PeriodStatus::Published)
            ->where('opens_at', '<=', $at)
            ->where('closes_at', '>', $at);
    }

    public function isOpenAt(CarbonInterface $at): bool
    {
        return $this->status === PeriodStatus::Published && $this->opens_at->lte($at) && $this->closes_at->gt($at);
    }
}
