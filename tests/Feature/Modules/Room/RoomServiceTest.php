<?php

namespace Tests\Feature\Modules\Room;

use App\Modules\Room\Enums\RoomStatus;
use App\Modules\Room\Models\Building;
use App\Modules\Room\Models\Campus;
use App\Modules\Room\Models\Room;
use App\Modules\Room\Models\RoomType;
use App\Modules\Room\Services\RoomAvailability;
use App\Modules\Room\Services\RoomMaintenanceService;
use App\Modules\Room\Services\RoomService;
use App\Support\Exceptions\BusinessRuleException;
use App\Support\References\ReferenceRegistry;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RoomServiceTest extends TestCase
{
    use DatabaseMigrations;

    private RoomService $rooms;

    private RoomMaintenanceService $maintenances;

    private RoomAvailability $availability;

    private Building $building;

    private RoomType $lecture;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rooms = app(RoomService::class);
        $this->maintenances = app(RoomMaintenanceService::class);
        $this->availability = app(RoomAvailability::class);

        $campus = $this->rooms->createCampus(['code' => 'CS1', 'name' => 'Cơ sở chính']);
        $this->building = $this->rooms->createBuilding(['campus_id' => $campus->id, 'code' => 'A', 'name' => 'Tòa A', 'floors' => 3]);
        $this->lecture = $this->rooms->createRoomType(['code' => 'LT', 'name' => 'Lý thuyết']);
    }

    private function room(string $code = 'A101', int $capacity = 60, int $examCapacity = 30): Room
    {
        return $this->rooms->createRoom([
            'building_id' => $this->building->id, 'room_type_id' => $this->lecture->id,
            'code' => $code, 'floor' => 1, 'capacity' => $capacity, 'exam_capacity' => $examCapacity,
        ]);
    }

    private function day(int $offset): string
    {
        return now(config('studentmanager.display_timezone'))->addDays($offset)->toDateString();
    }

    private function rejected(callable $callback): BusinessRuleException
    {
        try {
            $callback();
        } catch (BusinessRuleException $exception) {
            $this->assertNotEmpty($exception->hint(), 'Lỗi nghiệp vụ phải kèm cách khắc phục');

            return $exception;
        }

        $this->fail('Thao tác phải bị từ chối.');
    }

    public function test_ma_phong_duy_nhat_suc_chua_duong_va_suc_chua_thi_khong_vuot_suc_chua_hoc(): void
    {
        $room = $this->room();

        $this->rejected(fn () => $this->room('A102', 0, 0));
        $e = $this->rejected(fn () => $this->room('A102', 40, 41));
        $this->assertStringContainsString('Sức chứa thi (41)', $e->getMessage());
        $this->rejected(fn () => $this->room('A101'));
        $this->rejected(fn () => $this->rooms->updateRoom($room, ['exam_capacity' => 61]));

        // Tầng không vượt số tầng của tòa nhà
        $this->rejected(fn () => $this->rooms->updateRoom($room, ['floor' => 4]));

        // Sức chứa thi 0 là phòng không dùng để thi: hợp lệ
        $this->assertSame(0, $this->room('A102', 30, 0)->exam_capacity);
    }

    public function test_phong_bao_tri_tu_10_den_15_thang_10_thi_xep_lich_ngay_12_bi_chan(): void
    {
        $room = $this->room();
        $this->maintenances->schedule($room, ['starts_on' => '2026-10-10', 'ends_on' => '2026-10-15', 'reason' => 'Sửa điều hòa']);

        $e = $this->rejected(fn () => $this->availability->assertSchedulable($room, '2026-10-12'));
        $this->assertStringContainsString('bảo trì từ 10/10/2026 đến 15/10/2026 (Sửa điều hòa)', $e->getMessage());

        $this->assertTrue($this->availability->isSchedulable($room, '2026-10-16'));
        $this->assertTrue($this->availability->isSchedulable($room, '2026-10-01', '2026-10-09'));
        $this->assertFalse($this->availability->isSchedulable($room, '2026-10-01', '2026-10-10'));
    }

    public function test_chi_phong_su_dung_duoc_va_toa_nha_con_hoat_dong_moi_xep_lich_duoc(): void
    {
        $room = $this->room();
        $this->rooms->updateRoom($room, ['status' => 'maintenance']);
        $this->assertStringContainsString('Bảo trì', $this->availability->problems($room->refresh(), '2026-10-12')[0]);

        $this->rooms->updateRoom($room, ['status' => 'available']);
        $this->assertTrue($this->availability->isSchedulable($room->refresh(), '2026-10-12'));

        // Ngừng tòa nhà phải ngừng hết phòng trước
        $this->rejected(fn () => $this->rooms->updateBuilding($this->building, ['status' => 'inactive']));
        $this->rooms->updateRoom($room, ['status' => 'inactive']);
        $this->rooms->updateBuilding($this->building, ['status' => 'inactive']);

        $this->assertFalse($this->availability->isSchedulable($room->refresh(), '2026-10-12'));
        $this->rejected(fn () => $this->room('A103'));
        $this->rejected(fn () => $this->rooms->updateRoom($room, ['status' => 'available']));
    }

    public function test_phong_da_co_lich_su_su_dung_chi_ngung_khong_xoa_khong_doi_ma(): void
    {
        Schema::create('test_class_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id');
        });
        app(ReferenceRegistry::class)->register(Room::class, 'test_class_sessions', 'room_id', 'buổi học');

        $used = $this->room('A101');
        $unused = $this->room('A102');
        DB::table('test_class_sessions')->insert(['room_id' => $used->id]);

        $e = $this->rejected(fn () => $this->rooms->deleteRoom($used));
        $this->assertStringContainsString('lịch sử sử dụng (1 buổi học)', $e->getMessage());
        $this->assertStringContainsString('Ngừng sử dụng', $e->hint());
        $this->rejected(fn () => $this->rooms->updateRoom($used, ['code' => 'A199']));

        $this->assertSame(RoomStatus::Inactive, $this->rooms->updateRoom($used, ['status' => 'inactive'])->status);

        // Phòng chưa dùng: đổi mã và xóa được, mã cũ không cấp lại
        $this->assertSame('A105', $this->rooms->updateRoom($unused, ['code' => 'A105'])->code);
        $this->rooms->deleteRoom($unused->refresh());
        $this->assertSoftDeleted($unused);
        $this->rejected(fn () => $this->room('A105'));
    }

    public function test_lich_bao_tri_khong_chong_nhau_va_chi_huy_khi_chua_bat_dau(): void
    {
        $room = $this->room();
        $future = $this->maintenances->schedule($room, ['starts_on' => $this->day(5), 'ends_on' => $this->day(7), 'reason' => 'Sơn tường']);

        $this->rejected(fn () => $this->maintenances->schedule($room, ['starts_on' => $this->day(7), 'ends_on' => $this->day(9), 'reason' => 'Trùng']));
        $this->rejected(fn () => $this->maintenances->schedule($room, ['starts_on' => $this->day(3), 'ends_on' => $this->day(2), 'reason' => 'Ngược']));

        // Đang diễn ra: không hủy, không đổi ngày bắt đầu, được kéo dài hoặc kết thúc sớm
        $running = $this->maintenances->schedule($room, ['starts_on' => $this->day(-1), 'ends_on' => $this->day(1), 'reason' => 'Thay đèn']);
        $this->rejected(fn () => $this->maintenances->cancel($running));
        $this->rejected(fn () => $this->maintenances->update($running, ['starts_on' => $this->day(0)]));
        $this->assertSame($this->day(0), $this->maintenances->update($running, ['ends_on' => $this->day(0)])->ends_on->toDateString());

        // Đã kết thúc thì giữ làm lịch sử
        $past = $this->maintenances->schedule($room, ['starts_on' => $this->day(-10), 'ends_on' => $this->day(-8), 'reason' => 'Cũ']);
        $this->rejected(fn () => $this->maintenances->update($past, ['reason' => 'Sửa']));

        $this->maintenances->cancel($future);
        $this->assertSoftDeleted($future);
    }

    public function test_khong_ngung_co_so_con_toa_nha_hoat_dong_va_khong_giam_tang_duoi_phong_dang_co(): void
    {
        $campus = Campus::query()->firstOrFail();
        $room = $this->room();
        $this->rooms->updateRoom($room, ['floor' => 3]);

        $this->rejected(fn () => $this->rooms->updateBuilding($this->building, ['floors' => 2]));
        $this->rejected(fn () => $this->rooms->updateCampus($campus, ['status' => 'inactive']));
        $this->rejected(fn () => $this->rooms->deleteCampus($campus));
        $this->rejected(fn () => $this->rooms->updateBuilding($this->building, ['code' => 'AA']));

        // Loại phòng còn phòng đang dùng thì không ngừng
        $this->rejected(fn () => $this->rooms->updateRoomType($this->lecture, ['status' => 'inactive']));
    }
}
