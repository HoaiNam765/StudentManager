<?php

namespace App\Modules\Faculty\Enums;

/** Chức vụ lãnh đạo đơn vị (FR-FAC-006). Trưởng khoa, trưởng bộ môn: tối đa một người tại một thời điểm (BR-FAC-04). */
enum LeadershipPosition: string
{
    case Dean = 'dean';
    case ViceDean = 'vice_dean';
    case Head = 'head';
    case ViceHead = 'vice_head';

    public function label(): string
    {
        return match ($this) {
            self::Dean => 'Trưởng khoa',
            self::ViceDean => 'Phó trưởng khoa',
            self::Head => 'Trưởng bộ môn',
            self::ViceHead => 'Phó trưởng bộ môn',
        };
    }

    /** Loại đơn vị của chức vụ: faculty hoặc department. */
    public function unitType(): string
    {
        return in_array($this, [self::Dean, self::ViceDean], true) ? 'faculty' : 'department';
    }

    /** Chức vụ "trưởng": mỗi đơn vị tối đa một người tại một thời điểm. */
    public function isHead(): bool
    {
        return in_array($this, [self::Dean, self::Head], true);
    }
}
