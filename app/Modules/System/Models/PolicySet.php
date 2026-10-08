<?php

namespace App\Modules\System\Models;

use App\Modules\System\Enums\PolicySetStatus;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Bộ quy chế đào tạo (FR-SYS-003): có phiên bản, phạm vi khóa (năm nhập học) và ngày hiệu lực.
 * Tra cứu giá trị qua App\Modules\System\Services\PolicyResolver, không đọc bảng trực tiếp.
 */
class PolicySet extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'cohort_from' => 'integer',
            'cohort_to' => 'integer',
            'effective_from' => 'date',
            'status' => PolicySetStatus::class,
            'published_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(PolicyItem::class);
    }

    public function basedOn(): BelongsTo
    {
        return $this->belongsTo(self::class, 'based_on_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('status'), PolicySetStatus::Published->value);
    }

    /** Bộ quy chế có áp dụng cho khóa (năm nhập học) này không, chưa xét ngày. */
    public function scopeForCohort(Builder $query, int $cohort): Builder
    {
        return $query
            ->where(fn (Builder $q) => $q->whereNull('cohort_from')->orWhere('cohort_from', '<=', $cohort))
            ->where(fn (Builder $q) => $q->whereNull('cohort_to')->orWhere('cohort_to', '>=', $cohort));
    }

    public function isDraft(): bool
    {
        return $this->status === PolicySetStatus::Draft;
    }

    /** "K2025 trở về trước", "K2026 trở đi", "K2024–K2026", "Mọi khóa". */
    public function cohortLabel(): string
    {
        return match (true) {
            $this->cohort_from === null && $this->cohort_to === null => 'Mọi khóa',
            $this->cohort_from === null => "K{$this->cohort_to} trở về trước",
            $this->cohort_to === null => "K{$this->cohort_from} trở đi",
            default => "K{$this->cohort_from}–K{$this->cohort_to}",
        };
    }

    /** @return array<string, mixed> */
    public function values(): array
    {
        return $this->items->mapWithKeys(fn (PolicyItem $item) => [$item->key => $item->value])->all();
    }
}
