<?php

namespace Database\Seeders;

use App\Modules\Room\Models\Building;
use App\Modules\Room\Models\Campus;
use App\Modules\Room\Models\Room;
use App\Modules\Room\Models\RoomType;
use Illuminate\Database\Seeder;

/**
 * Dữ liệu mẫu phòng học: 5 tòa nhà A–E, mỗi tòa 10 phòng tầng 1 (A101–A110 … E101–E110). Tòa A, B: phòng lý thuyết;
 * C: phòng máy tính; D: phòng thí nghiệm (D101–D105) và phòng thi (D106–D110); E: hội trường (E101, E102) và giảng
 * đường lớn. Chỉ là dữ liệu giả; chạy lặp lại an toàn theo mã.
 */
class RoomSeeder extends Seeder
{
    /** Mã => tên loại phòng (FR-ROM-001). */
    public const TYPES = [
        RoomType::LECTURE => 'Phòng lý thuyết',
        RoomType::COMPUTER_LAB => 'Phòng máy tính',
        RoomType::LABORATORY => 'Phòng thí nghiệm',
        RoomType::HALL => 'Hội trường',
        RoomType::EXAM => 'Phòng thi',
    ];

    /** Mã tòa nhà => tên. */
    public const BUILDINGS = ['A' => 'Tòa nhà A', 'B' => 'Tòa nhà B', 'C' => 'Tòa nhà C', 'D' => 'Tòa nhà D', 'E' => 'Tòa nhà E'];

    public function run(): void
    {
        $types = [];

        foreach (self::TYPES as $code => $name) {
            $types[$code] = RoomType::firstOrCreate(['code' => $code], ['name' => $name]);
        }

        $campus = Campus::firstOrCreate(['code' => 'CS1'], ['name' => 'Cơ sở chính', 'address' => 'Địa chỉ mẫu']);

        foreach (self::BUILDINGS as $code => $name) {
            $building = Building::firstOrCreate(['code' => $code], ['campus_id' => $campus->id, 'name' => $name, 'floors' => 5]);

            foreach (range(1, 10) as $number) {
                $roomCode = sprintf('%s1%02d', $code, $number);
                [$type, $capacity, $examCapacity, $equipment] = self::profile($code, $number);
                Room::firstOrCreate(['code' => $roomCode], [
                    'building_id' => $building->id,
                    'room_type_id' => $types[$type]->id,
                    'name' => "Phòng {$roomCode}",
                    'floor' => 1,
                    'capacity' => $capacity,
                    'exam_capacity' => $examCapacity,
                    'equipment' => $equipment,
                ]);
            }
        }
    }

    /** @return array{0: string, 1: int, 2: int, 3: list<string>} [loại, sức chứa học, sức chứa thi, thiết bị] */
    public static function profile(string $building, int $number): array
    {
        return match (true) {
            $building === 'A' => [RoomType::LECTURE, 80, 40, ['Máy chiếu', 'Loa', 'Điều hòa']],
            $building === 'B' => [RoomType::LECTURE, 60, 30, ['Máy chiếu', 'Loa']],
            $building === 'C' => [RoomType::COMPUTER_LAB, 45, 45, ['45 máy tính', 'Máy chiếu', 'Điều hòa']],
            $building === 'D' && $number <= 5 => [RoomType::LABORATORY, 30, 0, ['Bàn thí nghiệm', 'Tủ hút']],
            $building === 'D' => [RoomType::EXAM, 40, 40, ['Camera giám sát']],
            $number <= 2 => [RoomType::HALL, $number === 1 ? 200 : 150, $number === 1 ? 100 : 75, ['Máy chiếu', 'Âm thanh hội trường', 'Điều hòa']],
            default => [RoomType::LECTURE, 120, 60, ['Máy chiếu', 'Âm thanh', 'Điều hòa']],
        };
    }
}
