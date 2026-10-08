<?php

namespace App\Modules\Faculty\Models;

use App\Support\Concerns\HasActiveStatus;
use App\Support\Models\StandardModel;

/** Hệ / loại hình đào tạo (FR-FAC-005): chính quy, liên thông…; đúng một hệ mặc định (chính quy). */
class TrainingType extends StandardModel
{
    use HasActiveStatus;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }

    public static function default(): ?self
    {
        return self::query()->where('is_default', true)->first();
    }
}
