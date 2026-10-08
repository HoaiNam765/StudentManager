<?php

namespace App\Modules\Room\Services;

use App\Modules\Room\Enums\RoomStatus;
use App\Modules\Room\Models\Room;
use App\Modules\Room\Models\RoomMaintenance;
use App\Support\Services\BaseService;
use Carbon\Carbon;

/**
 * Lịch bảo trì phòng (FR-ROM-002). Trong khoảng bảo trì phòng không được xếp lịch (RoomAvailability).
 *
 * - Không chồng lên một lịch bảo trì khác của cùng phòng.
 * - Lịch chưa bắt đầu thì sửa hoặc hủy được; đang diễn ra thì chỉ rút ngắn/kéo dài ngày kết thúc; đã xong thì giữ làm lịch sử.
 */
class RoomMaintenanceService extends BaseService
{
    /** @param  array{starts_on: string, ends_on: string, reason: string}  $data */
    public function schedule(Room $room, array $data): RoomMaintenance
    {
        if ($room->status === RoomStatus::Inactive) {
            $this->fail("Phòng {$room->code} đã ngừng sử dụng.", 'Không cần lên lịch bảo trì cho phòng đã ngừng; kích hoạt lại phòng nếu cần.');
        }

        $this->assertRange($data['starts_on'], $data['ends_on']);
        $this->assertNoOverlap($room, $data['starts_on'], $data['ends_on']);

        return $room->maintenances()->create($data);
    }

    /** @param  array{starts_on?: string, ends_on?: string, reason?: string}  $data */
    public function update(RoomMaintenance $maintenance, array $data): RoomMaintenance
    {
        $today = $this->today();

        if ($maintenance->ends_on->toDateString() < $today) {
            $this->fail('Lịch bảo trì đã kết thúc nên không sửa được.', 'Lịch đã qua được giữ làm lịch sử; tạo lịch bảo trì mới nếu cần.');
        }

        $started = $maintenance->starts_on->toDateString() <= $today;

        if ($started && isset($data['starts_on']) && $data['starts_on'] !== $maintenance->starts_on->toDateString()) {
            $this->fail('Lịch bảo trì đang diễn ra nên không đổi được ngày bắt đầu.', 'Chỉ sửa ngày kết thúc (kết thúc sớm hoặc kéo dài).');
        }

        $startsOn = $data['starts_on'] ?? $maintenance->starts_on->toDateString();
        $endsOn = $data['ends_on'] ?? $maintenance->ends_on->toDateString();

        if ($started && $endsOn < $today) {
            $this->fail('Ngày kết thúc mới đã qua.', 'Muốn kết thúc bảo trì ngay thì chọn ngày kết thúc là hôm nay.');
        }

        $this->assertRange($startsOn, $endsOn);
        $this->assertNoOverlap($maintenance->room, $startsOn, $endsOn, $maintenance->id);

        $maintenance->update(array_intersect_key($data, array_flip(['starts_on', 'ends_on', 'reason'])));

        return $maintenance->refresh();
    }

    public function cancel(RoomMaintenance $maintenance): void
    {
        if ($maintenance->starts_on->toDateString() <= $this->today()) {
            $this->fail('Lịch bảo trì đã bắt đầu nên không hủy được.', 'Muốn kết thúc sớm thì sửa ngày kết thúc thành hôm nay.');
        }

        $maintenance->delete();
    }

    private function assertRange(string $startsOn, string $endsOn): void
    {
        if (Carbon::parse($endsOn)->lt(Carbon::parse($startsOn))) {
            $this->fail('Ngày kết thúc bảo trì phải sau hoặc bằng ngày bắt đầu.', 'Chọn lại khoảng ngày bảo trì.');
        }
    }

    private function assertNoOverlap(Room $room, string $startsOn, string $endsOn, ?int $ignoreId = null): void
    {
        $clash = $room->maintenances()
            ->overlapping($startsOn, $endsOn)
            ->when($ignoreId !== null, fn ($q) => $q->whereKeyNot($ignoreId))
            ->first();

        if ($clash !== null) {
            $this->fail(
                "Phòng {$room->code} đã có lịch bảo trì từ ".$clash->starts_on->format('d/m/Y').' đến '.$clash->ends_on->format('d/m/Y').'.',
                'Sửa lịch bảo trì đã có thay vì tạo lịch chồng lên.'
            );
        }
    }

    private function today(): string
    {
        return now(config('studentmanager.display_timezone'))->toDateString();
    }
}
