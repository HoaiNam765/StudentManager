<?php

namespace App\Modules\Timetable\Models;

use App\Modules\Room\Models\Campus;
use App\Modules\Timetable\Enums\Shift;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Một tiết học trong ngày (FR-TTB-001). Giờ lưu dạng `H:i:s`, trả ra `H:i`. */
class Period extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'number' => 'integer',
            'shift' => Shift::class,
        ];
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function startTime(): string
    {
        return substr((string) $this->starts_at, 0, 5);
    }

    public function endTime(): string
    {
        return substr((string) $this->ends_at, 0, 5);
    }
}
