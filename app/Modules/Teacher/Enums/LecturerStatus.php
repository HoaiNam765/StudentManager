<?php

namespace App\Modules\Teacher\Enums;

/**
 * Tình trạng công tác (BR-TCH-02). Chỉ giảng viên đang làm việc mới được phân công mới; nghỉ việc, nghỉ hưu
 * giữ nguyên lịch sử. Nghỉ phép, công tác ngắn ngày khai báo riêng (FR-TCH-012), không đổi tình trạng.
 */
enum LecturerStatus: string
{
    case Working = 'working';
    case Resigned = 'resigned';
    case Retired = 'retired';

    public function label(): string
    {
        return match ($this) {
            self::Working => 'Đang làm việc',
            self::Resigned => 'Nghỉ việc',
            self::Retired => 'Nghỉ hưu',
        };
    }

    public function isActive(): bool
    {
        return $this === self::Working;
    }
}
