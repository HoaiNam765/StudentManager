<?php

namespace App\Modules\AcademicYear\Models;

use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Ngày nghỉ từ ngày – đến ngày (FR-ACY-005). `term_id` trống là áp dụng toàn trường. */
class Holiday extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['type' => HolidayType::class, 'starts_on' => 'date', 'ends_on' => 'date'];
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    /** Ngày nghỉ giao với khoảng [$from, $to]. */
    public function scopeOverlapping(Builder $query, string $from, string $to): Builder
    {
        return $query->whereDate('starts_on', '<=', $to)->whereDate('ends_on', '>=', $from);
    }

    /** Ngày nghỉ toàn trường cộng ngày nghỉ riêng của học kỳ $termId. */
    public function scopeApplicableTo(Builder $query, ?int $termId): Builder
    {
        return $query->where(fn (Builder $q) => $q->whereNull('term_id')->when($termId !== null, fn (Builder $q) => $q->orWhere('term_id', $termId)));
    }
}
