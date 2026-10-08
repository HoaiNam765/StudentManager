<?php

namespace Database\Seeders;

use App\Modules\Room\Models\Building;
use App\Modules\Room\Models\Campus;
use App\Modules\Room\Models\Room;
use App\Modules\Room\Models\RoomType;
use Illuminate\Database\Seeder;

/**
 * Dữ liệu mẫu phòng học (docs/BA.md mục 11.2: 20 phòng). Chỉ là dữ liệu giả; chạy lặp lại an toàn theo mã.
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

    /** [mã tòa nhà, mã phòng, loại, tầng, sức chứa học, sức chứa thi, thiết bị] */
    private const ROOMS = [
        ['A', 'A101', 'LT', 1, 60, 30, ['Máy chiếu', 'Loa']],
        ['A', 'A102', 'LT', 1, 60, 30, ['Máy chiếu', 'Loa']],
        ['A', 'A103', 'LT', 1, 60, 30, ['Máy chiếu']],
        ['A', 'A104', 'LT', 1, 60, 30, ['Máy chiếu']],
        ['A', 'A201', 'LT', 2, 80, 40, ['Máy chiếu', 'Loa', 'Điều hòa']],
        ['A', 'A202', 'LT', 2, 80, 40, ['Máy chiếu', 'Loa', 'Điều hòa']],
        ['A', 'A203', 'LT', 2, 80, 40, ['Máy chiếu', 'Điều hòa']],
        ['A', 'A204', 'LT', 2, 80, 40, ['Máy chiếu', 'Điều hòa']],
        ['A', 'A301', 'HT', 3, 200, 100, ['Máy chiếu', 'Âm thanh hội trường', 'Điều hòa']],
        ['A', 'A302', 'HT', 3, 150, 75, ['Máy chiếu', 'Âm thanh hội trường', 'Điều hòa']],
        ['A', 'A401', 'PT', 4, 40, 40, ['Camera giám sát']],
        ['A', 'A402', 'PT', 4, 40, 40, ['Camera giám sát']],
        ['B', 'B101', 'MT', 1, 45, 45, ['45 máy tính', 'Máy chiếu', 'Điều hòa']],
        ['B', 'B102', 'MT', 1, 45, 45, ['45 máy tính', 'Máy chiếu', 'Điều hòa']],
        ['B', 'B103', 'MT', 1, 40, 40, ['40 máy tính', 'Máy chiếu', 'Điều hòa']],
        ['B', 'B201', 'TN', 2, 30, 0, ['Bàn thí nghiệm', 'Tủ hút']],
        ['B', 'B202', 'TN', 2, 30, 0, ['Bàn thí nghiệm']],
        ['B', 'B301', 'LT', 3, 50, 25, ['Máy chiếu']],
        ['B', 'B302', 'LT', 3, 50, 25, ['Máy chiếu']],
        ['B', 'B303', 'LT', 3, 50, 25, ['Máy chiếu']],
    ];

    public function run(): void
    {
        $types = [];

        foreach (self::TYPES as $code => $name) {
            $types[$code] = RoomType::firstOrCreate(['code' => $code], ['name' => $name]);
        }

        $campus = Campus::firstOrCreate(['code' => 'CS1'], ['name' => 'Cơ sở chính', 'address' => 'Địa chỉ mẫu']);

        $buildings = [
            'A' => Building::firstOrCreate(['code' => 'A'], ['campus_id' => $campus->id, 'name' => 'Tòa nhà A', 'floors' => 5]),
            'B' => Building::firstOrCreate(['code' => 'B'], ['campus_id' => $campus->id, 'name' => 'Tòa nhà B', 'floors' => 4]),
        ];

        foreach (self::ROOMS as [$building, $code, $type, $floor, $capacity, $examCapacity, $equipment]) {
            Room::firstOrCreate(['code' => $code], [
                'building_id' => $buildings[$building]->id,
                'room_type_id' => $types[$type]->id,
                'name' => "Phòng {$code}",
                'floor' => $floor,
                'capacity' => $capacity,
                'exam_capacity' => $examCapacity,
                'equipment' => $equipment,
            ]);
        }
    }
}
