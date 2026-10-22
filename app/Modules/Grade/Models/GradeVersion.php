<?php

namespace App\Modules\Grade\Models;

use App\Support\Models\StandardModel;
use LogicException;

/** Immutable history: no modification and no deletion (including soft deletion). */
class GradeVersion extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['snapshot' => 'array'];
    }

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Lịch sử điểm là bất biến.'));
        static::deleting(fn () => throw new LogicException('Không được xóa phiên bản điểm.'));
    }
}
