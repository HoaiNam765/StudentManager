<?php

namespace App\Modules\Room\Services;

use App\Modules\Room\Enums\RoomStatus;
use App\Modules\Room\Models\Building;
use App\Modules\Room\Models\Campus;
use App\Modules\Room\Models\Room;
use App\Modules\Room\Models\RoomType;
use App\Support\References\ReferenceRegistry;
use App\Support\Services\BaseService;

/**
 * Danh mục cơ sở, tòa nhà, loại phòng, phòng học (FR-ROM-001; BR-ROM-01, BR-ROM-04, GC-05).
 *
 * - Mã duy nhất và không cấp lại; không đổi mã khi đã có dữ liệu bên dưới hoặc đã được tham chiếu.
 * - Sức chứa học là số nguyên dương; sức chứa thi không lớn hơn sức chứa học (0 = không dùng làm phòng thi).
 * - Phòng đã có lịch sử sử dụng (TTB, EXM đăng ký tham chiếu) thì chỉ ngừng sử dụng, không xóa.
 * - Không ngừng cơ sở / tòa nhà còn tòa nhà / phòng đang dùng; không tạo phòng trong tòa nhà đã ngừng.
 */
class RoomService extends BaseService
{
    public function __construct(private readonly ReferenceRegistry $references) {}

    // ---------------------------------------------------------------------
    // Cơ sở
    // ---------------------------------------------------------------------

    /** @param  array{code: string, name: string, address?: ?string}  $data */
    public function createCampus(array $data): Campus
    {
        $this->assertCodeFree(Campus::class, $data['code'], 'cơ sở');

        return Campus::create($data);
    }

    /** @param  array{code?: string, name?: string, address?: ?string, status?: string}  $data */
    public function updateCampus(Campus $campus, array $data): Campus
    {
        if (isset($data['code']) && $data['code'] !== $campus->code) {
            if ($campus->buildings()->withTrashed()->exists()) {
                $this->fail("Không đổi được mã cơ sở {$campus->code} vì đã có tòa nhà.", 'Đổi tên thì được; mã giữ nguyên để dữ liệu cũ tra cứu đúng.');
            }

            $this->assertCodeFree(Campus::class, $data['code'], 'cơ sở');
        }

        $this->transaction(function () use ($campus, $data): void {
            $campus->update(array_intersect_key($data, array_flip(['code', 'name', 'address'])));

            match ($data['status'] ?? null) {
                'active' => $campus->activate(),
                'inactive' => $this->deactivateCampus($campus),
                default => null,
            };
        });

        return $campus->refresh();
    }

    public function deleteCampus(Campus $campus): void
    {
        if ($campus->buildings()->withTrashed()->exists()) {
            $this->fail("Không xóa được cơ sở {$campus->code} vì đã có tòa nhà.", 'Hãy ngừng sử dụng cơ sở thay cho việc xóa.');
        }

        $campus->delete();
    }

    // ---------------------------------------------------------------------
    // Tòa nhà
    // ---------------------------------------------------------------------

    /** @param  array{campus_id: int, code: string, name: string, floors?: ?int}  $data */
    public function createBuilding(array $data): Building
    {
        $campus = Campus::query()->findOrFail($data['campus_id']);

        if (! $campus->isActive()) {
            $this->fail("Cơ sở {$campus->code} đang ngừng sử dụng.", 'Chọn cơ sở đang hoạt động hoặc kích hoạt lại cơ sở.');
        }

        $this->assertCodeFree(Building::class, $data['code'], 'tòa nhà');

        return Building::create($data);
    }

    /** @param  array{code?: string, name?: string, floors?: ?int, status?: string}  $data */
    public function updateBuilding(Building $building, array $data): Building
    {
        if (isset($data['code']) && $data['code'] !== $building->code) {
            if ($building->rooms()->withTrashed()->exists()) {
                $this->fail("Không đổi được mã tòa nhà {$building->code} vì đã có phòng.", 'Đổi tên thì được; mã giữ nguyên để dữ liệu cũ tra cứu đúng.');
            }

            $this->assertCodeFree(Building::class, $data['code'], 'tòa nhà');
        }

        if (array_key_exists('floors', $data) && $data['floors'] !== null) {
            $highest = (int) $building->rooms()->max('floor');

            if ($highest > $data['floors']) {
                $this->fail(
                    "Tòa nhà {$building->code} đang có phòng ở tầng {$highest}, không giảm số tầng xuống {$data['floors']} được.",
                    'Sửa tầng của các phòng đó trước, hoặc để trống số tầng.'
                );
            }
        }

        $this->transaction(function () use ($building, $data): void {
            $building->update(array_intersect_key($data, array_flip(['code', 'name', 'floors'])));

            match ($data['status'] ?? null) {
                'active' => $this->activateBuilding($building),
                'inactive' => $this->deactivateBuilding($building),
                default => null,
            };
        });

        return $building->refresh();
    }

    public function deleteBuilding(Building $building): void
    {
        if ($building->rooms()->withTrashed()->exists()) {
            $this->fail("Không xóa được tòa nhà {$building->code} vì đã có phòng.", 'Hãy ngừng sử dụng tòa nhà thay cho việc xóa.');
        }

        $building->delete();
    }

    // ---------------------------------------------------------------------
    // Loại phòng
    // ---------------------------------------------------------------------

    /** @param  array{code: string, name: string, description?: ?string}  $data */
    public function createRoomType(array $data): RoomType
    {
        $this->assertCodeFree(RoomType::class, $data['code'], 'loại phòng');

        return RoomType::create($data);
    }

    /** @param  array{name?: string, description?: ?string, status?: string}  $data */
    public function updateRoomType(RoomType $type, array $data): RoomType
    {
        $this->transaction(function () use ($type, $data): void {
            $type->update(array_intersect_key($data, array_flip(['name', 'description'])));

            if (($data['status'] ?? null) === 'inactive') {
                $inUse = $type->rooms()->where('status', '!=', RoomStatus::Inactive->value)->count();

                if ($inUse > 0) {
                    $this->fail("Còn {$inUse} phòng loại {$type->code} đang dùng.", 'Đổi loại hoặc ngừng các phòng đó trước khi ngừng loại phòng.');
                }

                $type->deactivate();
            } elseif (($data['status'] ?? null) === 'active') {
                $type->activate();
            }
        });

        return $type->refresh();
    }

    // ---------------------------------------------------------------------
    // Phòng
    // ---------------------------------------------------------------------

    /**
     * @param  array{building_id: int, room_type_id: int, code: string, name?: ?string, floor?: int, capacity: int, exam_capacity?: int, equipment?: ?list<string>, note?: ?string}  $data
     */
    public function createRoom(array $data): Room
    {
        $building = $this->usableBuilding($data['building_id']);
        $this->usableType($data['room_type_id']);
        $this->assertCodeFree(Room::class, $data['code'], 'phòng');

        $data['floor'] = (int) ($data['floor'] ?? 1);
        $data['capacity'] = (int) $data['capacity'];
        $data['exam_capacity'] = (int) ($data['exam_capacity'] ?? 0);
        $this->assertCapacities($data['capacity'], $data['exam_capacity']);
        $this->assertFloor($building, $data['floor']);

        return Room::create($data + ['status' => RoomStatus::Available]);
    }

    /**
     * @param  array{building_id?: int, room_type_id?: int, code?: string, name?: ?string, floor?: int, capacity?: int, exam_capacity?: int, equipment?: ?list<string>, note?: ?string, status?: string}  $data
     */
    public function updateRoom(Room $room, array $data): Room
    {
        if (isset($data['code']) && $data['code'] !== $room->code) {
            $usages = $this->references->usages($room);

            if ($usages !== []) {
                $this->fail(
                    "Không đổi được mã phòng {$room->code} vì đã có ".ReferenceRegistry::describe($usages).'.',
                    'Đổi tên phòng thì được; mã giữ nguyên để lịch học, lịch thi cũ tra cứu đúng.'
                );
            }

            $this->assertCodeFree(Room::class, $data['code'], 'phòng');
        }

        $building = isset($data['building_id']) && (int) $data['building_id'] !== $room->building_id
            ? $this->usableBuilding($data['building_id'])
            : $room->building;

        if (isset($data['room_type_id']) && (int) $data['room_type_id'] !== $room->room_type_id) {
            $this->usableType($data['room_type_id']);
        }

        $this->assertCapacities((int) ($data['capacity'] ?? $room->capacity), (int) ($data['exam_capacity'] ?? $room->exam_capacity));
        $this->assertFloor($building, (int) ($data['floor'] ?? $room->floor));

        $changes = array_intersect_key($data, array_flip(['building_id', 'room_type_id', 'code', 'name', 'floor', 'capacity', 'exam_capacity', 'equipment', 'note']));

        if (isset($data['status'])) {
            $status = RoomStatus::from($data['status']);

            if ($status !== RoomStatus::Inactive && ! $building->isActive()) {
                $this->fail("Tòa nhà {$building->code} đang ngừng sử dụng.", 'Kích hoạt lại tòa nhà trước khi đưa phòng vào sử dụng.');
            }

            $changes['status'] = $status;
        }

        $room->update($changes);

        return $room->refresh();
    }

    /** Xóa (mềm) phòng chưa từng được dùng; đã có lịch sử sử dụng thì chỉ ngừng (BR-ROM-04). */
    public function deleteRoom(Room $room): void
    {
        $usages = $this->references->usages($room);

        if ($usages !== []) {
            $this->fail(
                "Phòng {$room->code} đã có lịch sử sử dụng (".ReferenceRegistry::describe($usages).') nên không xóa được.',
                'Chuyển phòng sang "Ngừng sử dụng"; lịch sử vẫn tra cứu được.'
            );
        }

        $room->delete();
    }

    // ---------------------------------------------------------------------

    private function deactivateCampus(Campus $campus): void
    {
        $active = $campus->buildings()->active()->count();

        if ($active > 0) {
            $this->fail("Cơ sở {$campus->code} còn {$active} tòa nhà đang hoạt động.", 'Ngừng các tòa nhà trước, rồi mới ngừng cơ sở.');
        }

        $campus->deactivate();
    }

    private function activateBuilding(Building $building): void
    {
        if (! $building->campus->isActive()) {
            $this->fail("Cơ sở {$building->campus->code} đang ngừng sử dụng.", 'Kích hoạt lại cơ sở trước.');
        }

        $building->activate();
    }

    private function deactivateBuilding(Building $building): void
    {
        $inUse = $building->rooms()->where('status', '!=', RoomStatus::Inactive->value)->count();

        if ($inUse > 0) {
            $this->fail("Tòa nhà {$building->code} còn {$inUse} phòng chưa ngừng sử dụng.", 'Ngừng các phòng trước, rồi mới ngừng tòa nhà.');
        }

        $building->deactivate();
    }

    private function usableBuilding(int|string $id): Building
    {
        $building = Building::query()->with('campus')->findOrFail($id);

        if (! $building->isActive() || ! $building->campus->isActive()) {
            $this->fail("Tòa nhà {$building->code} hoặc cơ sở của nó đang ngừng sử dụng.", 'Chọn tòa nhà đang hoạt động.');
        }

        return $building;
    }

    private function usableType(int|string $id): RoomType
    {
        $type = RoomType::query()->findOrFail($id);

        if (! $type->isActive()) {
            $this->fail("Loại phòng {$type->code} đang ngừng sử dụng.", 'Chọn loại phòng đang hoạt động.');
        }

        return $type;
    }

    private function assertCapacities(int $capacity, int $examCapacity): void
    {
        if ($capacity < 1) {
            $this->fail('Sức chứa học phải là số nguyên dương.', 'Nhập số chỗ ngồi học thực tế của phòng (từ 1 trở lên).');
        }

        if ($examCapacity < 0 || $examCapacity > $capacity) {
            $this->fail(
                "Sức chứa thi ({$examCapacity}) phải từ 0 đến sức chứa học ({$capacity}).",
                'Sức chứa thi thường bằng khoảng một nửa sức chứa học; nhập 0 nếu phòng không dùng để thi (BR-ROM-01).'
            );
        }
    }

    private function assertFloor(Building $building, int $floor): void
    {
        if ($building->floors !== null && $floor > $building->floors) {
            $this->fail("Tòa nhà {$building->code} chỉ có {$building->floors} tầng.", 'Kiểm tra lại tầng của phòng hoặc số tầng của tòa nhà.');
        }
    }

    /** @param  class-string<Campus|Building|RoomType|Room>  $model */
    private function assertCodeFree(string $model, string $code, string $label): void
    {
        if ($model::withTrashed()->where('code', $code)->exists()) {
            $this->fail("Mã {$label} {$code} đã được dùng.", 'Mã không được cấp lại, kể cả khi bản ghi cũ đã xóa; hãy chọn mã khác.');
        }
    }
}
