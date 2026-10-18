<?php

namespace App\Modules\Enrollment\Models;

use App\Modules\AcademicYear\Models\Term;
use App\Modules\Enrollment\Enums\PeriodStatus;
use App\Modules\Enrollment\Enums\RegistrationRound;
use App\Support\Models\StandardModel;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Đợt đăng ký học phần (FR-ENR-001). Thời gian lưu UTC, so với giờ máy chủ (BR-ENR-11). */
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
