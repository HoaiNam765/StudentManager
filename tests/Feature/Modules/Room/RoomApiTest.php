<?php

namespace Tests\Feature\Modules\Room;

use App\Modules\Room\Models\Building;
use App\Modules\Room\Models\Room;
use App\Modules\Room\Models\RoomType;
use Database\Seeders\RoomSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

class RoomApiTest extends TestCase
{
    use DatabaseMigrations;
    use InteractsWithRoles;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
        $this->seed(RoomSeeder::class);
    }

    public function test_seeder_co_50_phong_5_toa_va_chay_lap_lai_an_toan(): void
    {
        $this->seed(RoomSeeder::class);

        $this->assertSame(50, Room::count());
        $this->assertSame(5, RoomType::count());
        $this->assertSame(['A101', 'A110', 'E101', 'E110'], Room::query()->whereIn('code', ['A101', 'A110', 'E101', 'E110', 'A111', 'A201'])->orderBy('code')->pluck('code')->all());
        $this->assertTrue(Room::all()->every(fn (Room $room) => $room->exam_capacity <= $room->capacity && $room->capacity > 0));
    }

    public function test_can_bo_dao_tao_tao_sua_phong_va_len_lich_bao_tri(): void
    {
        $this->actingAs($this->userWithRoles('ACAD'));
        $building = Building::query()->where('code', 'B')->firstOrFail();
        $lab = RoomType::query()->where('code', RoomType::COMPUTER_LAB)->firstOrFail();

        $id = $this->postJson('/admin/rooms', [
            'building_id' => $building->id, 'room_type_id' => $lab->id, 'code' => 'b111 ',
            'floor' => 1, 'capacity' => 40, 'exam_capacity' => 40, 'equipment' => ['40 máy tính'],
        ])->assertCreated()->assertJsonPath('code', 'B111')->assertJsonPath('status', 'available')->json('id');

        $this->postJson('/admin/rooms', [
            'building_id' => $building->id, 'room_type_id' => $lab->id, 'code' => 'B112', 'capacity' => 40, 'exam_capacity' => 50,
        ])->assertStatus(422)->assertJsonPath('message', fn ($m) => str_contains($m, 'Sức chứa thi (50)'));

        $this->postJson("/admin/rooms/{$id}/maintenances", ['starts_on' => '2026-10-10', 'ends_on' => '2026-10-15', 'reason' => 'Nâng cấp máy'])->assertCreated();

        $this->getJson("/admin/rooms/{$id}/availability?from=2026-10-12")
            ->assertOk()
            ->assertJsonPath('schedulable', false)
            ->assertJsonPath('problems.0', fn ($p) => str_contains($p, 'bảo trì từ 10/10/2026'));

        $this->getJson("/admin/rooms/{$id}/availability?from=2026-10-16")->assertOk()->assertJsonPath('schedulable', true);

        $this->putJson("/admin/rooms/{$id}", ['status' => 'inactive'])->assertOk()->assertJsonPath('status_label', 'Ngừng sử dụng');
    }

    public function test_sinh_vien_va_giang_vien_chi_xem_va_loc_phong(): void
    {
        $this->actingAs($this->userWithRoles('STU'));

        $this->getJson('/admin/rooms?min_capacity=100')->assertOk()->assertJsonPath('total', 10);
        $this->getJson('/admin/rooms?room_type_id='.RoomType::query()->where('code', 'MT')->value('id'))->assertOk()->assertJsonPath('total', 10);
        $this->getJson('/admin/rooms?q=d10')->assertOk()->assertJsonPath('total', 9);
        $this->getJson('/admin/campuses')->assertOk()->assertJsonPath('0.buildings_count', 5);

        $roomId = Room::query()->value('id');
        $this->postJson('/admin/rooms', [])->assertForbidden();
        $this->putJson("/admin/rooms/{$roomId}", ['name' => 'X'])->assertForbidden();
        $this->deleteJson("/admin/rooms/{$roomId}")->assertForbidden();

        $this->actingAs($this->userWithRoles('LEC'));
        $this->getJson("/admin/rooms/{$roomId}")->assertOk()->assertJsonPath('campus.code', 'CS1');
        $this->postJson("/admin/rooms/{$roomId}/maintenances", ['starts_on' => '2026-11-01', 'ends_on' => '2026-11-02', 'reason' => 'X'])->assertForbidden();
    }

    public function test_quan_tri_xoa_phong_chua_dung_va_khong_xoa_toa_nha_con_phong(): void
    {
        $this->actingAs($this->userWithRoles('ADMIN'));
        $room = Room::query()->where('code', 'B110')->firstOrFail();

        $this->deleteJson("/admin/rooms/{$room->id}")->assertNoContent();
        $this->deleteJson('/admin/buildings/'.$room->building_id)->assertStatus(422)->assertJsonPath('message', fn ($m) => str_contains($m, 'đã có phòng'));

        $this->postJson('/admin/rooms', [
            'building_id' => $room->building_id, 'room_type_id' => $room->room_type_id, 'code' => 'B110', 'capacity' => 50,
        ])->assertStatus(422)->assertJsonPath('message', fn ($m) => str_contains($m, 'không được cấp lại'));
    }
}
