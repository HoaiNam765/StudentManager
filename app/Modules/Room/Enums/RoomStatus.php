<?php

namespace App\Modules\Room\Enums;

/** Tình trạng phòng (FR-ROM-002). */
enum RoomStatus: string
{
    /** Sử dụng được */
    case Available = 'available';

    /** Đang bảo trì không thời hạn; bảo trì có thời hạn thì dùng lịch bảo trì */
    case Maintenance = 'maintenance';

    /** Ngừng sử dụng (thay cho xóa, BR-ROM-04) */
    case Inactive = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Sử dụng được',
            self::Maintenance => 'Bảo trì',
            self::Inactive => 'Ngừng sử dụng',
        };
    }
}
