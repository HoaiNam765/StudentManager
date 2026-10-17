<?php

namespace App\Modules\Teacher\Models;

use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Khung giờ bận lặp lại hằng tuần của giảng viên (FR-TCH-008, FR-TTB-010). */
class LecturerAvailability extends StandardModel
{
    public const DAYS = [1 => 'Thứ Hai', 2 => 'Thứ Ba', 3 => 'Thứ Tư', 4 => 'Thứ Năm', 5 => 'Thứ Sáu', 6 => 'Thứ Bảy', 7 => 'Chủ nhật'];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'period_from' => 'integer',
            'period_to' => 'integer',
            'starts_on' => 'date',
            'ends_on' => 'date',
        ];
    }

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }

    /** Khung bận vào thứ $day, giao với tiết [$periodFrom, $periodTo] và khoảng ngày [$from, $to]. */
    public function scopeClashing(Builder $query, int $day, int $periodFrom, int $periodTo, string $from, string $to): Builder
    {
        return $query->where('day_of_week', $day)
            ->where('period_from', '<=', $periodTo)
            ->where('period_to', '>=', $periodFrom)
            ->whereDate('starts_on', '<=', $to)
            ->whereDate('ends_on', '>=', $from);
    }

    public function describe(): string
    {
        return self::DAYS[$this->day_of_week]." tiết {$this->period_from}–{$this->period_to} (".$this->starts_on->format('d/m/Y').' – '.$this->ends_on->format('d/m/Y').')';
    }
}
