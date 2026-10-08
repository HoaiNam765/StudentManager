<?php

namespace App\Modules\Room\Services;

use App\Modules\Room\Enums\RoomStatus;
use App\Modules\Room\Models\Room;
use App\Support\Exceptions\BusinessRuleException;
use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * Phòng có xếp lịch được không (BR-ROM-02): chỉ phòng "Sử dụng được", tòa nhà và cơ sở còn hoạt động,
 * và không trùng lịch bảo trì. TTB (xếp lớp) và EXM (xếp phòng thi) gọi trước khi lưu:
 *
 *     app(RoomAvailability::class)->assertSchedulable($room, '2026-10-12');
 *     app(RoomAvailability::class)->problems($room, $tuNgay, $denNgay);   // danh sách lý do, rỗng là được
 *
 * Việc kiểm tra trùng lịch học, lịch thi là của TTB/EXM (P2: tìm phòng trống).
 */
class RoomAvailability
{
    /** @return list<string> lý do không xếp được, rỗng là xếp được */
    public function problems(Room $room, CarbonInterface|string $from, CarbonInterface|string|null $to = null): array
    {
        $start = Carbon::parse($from)->toDateString();
        $end = $to === null ? $start : Carbon::parse($to)->toDateString();
        $problems = [];

        if ($room->status !== RoomStatus::Available) {
            $problems[] = "Phòng {$room->code} đang ở tình trạng \"{$room->status->label()}\".";
        }

        $building = $room->building()->with('campus')->first();

        if ($building !== null && (! $building->isActive() || ! $building->campus->isActive())) {
            $problems[] = "Tòa nhà {$building->code} hoặc cơ sở của nó đang ngừng sử dụng.";
        }

        foreach ($room->maintenances()->overlapping($start, $end)->orderBy('starts_on')->get() as $maintenance) {
            $problems[] = "Phòng {$room->code} bảo trì từ ".$maintenance->starts_on->format('d/m/Y')
                .' đến '.$maintenance->ends_on->format('d/m/Y')." ({$maintenance->reason}).";
        }

        return $problems;
    }

    public function isSchedulable(Room $room, CarbonInterface|string $from, CarbonInterface|string|null $to = null): bool
    {
        return $this->problems($room, $from, $to) === [];
    }

    /** @throws BusinessRuleException kèm lý do và cách khắc phục */
    public function assertSchedulable(Room $room, CarbonInterface|string $from, CarbonInterface|string|null $to = null): void
    {
        $problems = $this->problems($room, $from, $to);

        if ($problems !== []) {
            throw new BusinessRuleException(implode(' ', $problems), 'Chọn phòng khác hoặc thời gian khác (BR-ROM-02).');
        }
    }
}
