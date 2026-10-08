<?php

namespace App\Modules\Room\Models;

use App\Support\Concerns\HasActiveStatus;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Loại phòng (FR-ROM-001): lý thuyết, máy tính, thí nghiệm, hội trường, phòng thi.
 * TTB dùng mã loại để kiểm tra BR-ROM-03 (buổi thực hành cần phòng máy hoặc phòng thí nghiệm).
 */
class RoomType extends StandardModel
{
    use HasActiveStatus;

    public const LECTURE = 'LT';

    public const COMPUTER_LAB = 'MT';

    public const LABORATORY = 'TN';

    public const HALL = 'HT';

    public const EXAM = 'PT';

    protected $guarded = [];

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
