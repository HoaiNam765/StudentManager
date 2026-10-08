<?php

namespace Tests\Feature\Modules\Faculty;

use App\Modules\Auth\Models\Role;
use App\Modules\Auth\Services\RoleService;
use App\Modules\Faculty\Models\Faculty;
use App\Modules\Faculty\Models\Major;
use App\Modules\Faculty\Models\Specialization;
use App\Modules\Faculty\Models\TrainingType;
use Database\Seeders\FacultySeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use OpenSpout\Reader\XLSX\Reader;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

class FacultyApiTest extends TestCase
{
    use DatabaseMigrations;
    use InteractsWithRoles;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
        $this->seed(FacultySeeder::class);
    }

    public function test_seeder_3_khoa_6_nganh_va_he_chinh_quy_mac_dinh(): void
    {
        $this->seed(FacultySeeder::class);

        $this->assertSame(3, Faculty::count());
        $this->assertSame(6, Major::count());
        $this->assertSame('CQ', TrainingType::default()->code);
    }

    public function test_can_bo_dao_tao_tao_sua_tim_kiem_va_xem_cay_to_chuc(): void
    {
        $this->actingAs($this->userWithRoles('ACAD'));
        $cntt = Faculty::query()->where('code', 'CNTT')->firstOrFail();

        $this->postJson('/admin/departments', ['faculty_id' => $cntt->id, 'code' => 'bm-ktmt', 'name' => 'Bộ môn Kỹ thuật máy tính'])
            ->assertCreated()
            ->assertJsonPath('code', 'BM-KTMT')
            ->assertJsonPath('faculty.code', 'CNTT');

        $majorId = $this->postJson('/admin/majors', ['faculty_id' => $cntt->id, 'code' => '7480102', 'name' => 'Mạng máy tính và truyền thông dữ liệu', 'total_credits' => 150, 'standard_terms' => 8])
            ->assertCreated()
            ->assertJsonPath('education_level_label', 'Đại học')
            ->assertJsonPath('average_credits_per_term', 18.75)
            ->json('id');

        // Không chuyển ngành sang khoa khác bằng thao tác sửa (BR-FAC-02)
        $this->putJson("/admin/majors/{$majorId}", ['faculty_id' => Faculty::query()->where('code', 'KT')->value('id')])
            ->assertStatus(422)
            ->assertJsonValidationErrors('faculty_id');

        // Tìm không dấu và lọc theo khoa
        $this->getJson('/admin/majors?q=ngon ngu')->assertOk()->assertJsonPath('total', 2);
        $this->getJson("/admin/departments?faculty_id={$cntt->id}")->assertOk()->assertJsonPath('total', 4);

        $this->getJson("/admin/faculties/{$cntt->id}")
            ->assertOk()
            ->assertJsonCount(4, 'departments')
            ->assertJsonCount(3, 'majors')
            ->assertJsonPath('majors.0.code', '7480102');

        // Ngừng khoa còn đơn vị con: 422 kèm danh sách
        $this->putJson("/admin/faculties/{$cntt->id}", ['status' => 'inactive'])
            ->assertStatus(422)
            ->assertJsonPath('message', fn ($m) => str_contains($m, 'bộ môn đang hoạt động: BM-HTTT, BM-KTMT, BM-KTPM, BM-MMT'));
    }

    public function test_sinh_vien_chi_xem_khong_tao_sua_xoa(): void
    {
        $this->actingAs($this->userWithRoles('STU'));
        $faculty = Faculty::query()->firstOrFail();

        $this->getJson('/admin/faculties')->assertOk()->assertJsonPath('total', 3);
        $this->getJson('/admin/specializations')->assertOk()->assertJsonPath('total', Specialization::count());
        $this->postJson('/admin/faculties', ['code' => 'X', 'name' => 'X'])->assertForbidden();
        $this->putJson("/admin/faculties/{$faculty->id}", ['name' => 'X'])->assertForbidden();
        $this->deleteJson("/admin/faculties/{$faculty->id}")->assertForbidden();
        $this->getJson('/admin/faculties/export')->assertForbidden();
    }

    public function test_lanh_dao_don_vi_chua_co_nhiem_ky_thi_khong_thay_don_vi_nao(): void
    {
        // Phạm vi FACULTY lấy từ nhiệm kỳ lãnh đạo (issue #74); chưa có nhiệm kỳ thì an toàn mặc định: không thấy gì
        $this->actingAs($this->userWithRoles('DEAN'));
        $faculty = Faculty::query()->firstOrFail();

        $this->getJson('/admin/faculties')->assertOk()->assertJsonPath('total', 0);
        $this->getJson("/admin/faculties/{$faculty->id}")->assertForbidden();
        $this->putJson("/admin/faculties/{$faculty->id}", ['description' => 'Giới thiệu'])->assertForbidden();
    }

    public function test_xuat_excel_can_quyen_xuat_va_dung_bo_loc(): void
    {
        // Ma trận BA 4.2 chưa cấp quyền X cho FAC: mặc định không ai xuất được
        $this->actingAs($this->userWithRoles('ACAD'));
        $this->getJson('/admin/majors/export')->assertForbidden();

        $acad = Role::query()->where('code', 'ACAD')->firstOrFail();
        app(RoleService::class)->syncPermissions($acad, $acad->permissionMatrix() + ['FAC.export' => 'ALL']);

        $response = $this->get('/admin/majors/export?q=ngon ngu');
        $response->assertOk();

        $reader = new Reader;
        $reader->open($response->getFile()->getPathname());
        $rows = [];

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $rows[] = array_map(fn ($cell) => $cell->getValue(), $row->getCells());
            }
        }

        $reader->close();

        $this->assertSame(['Mã', 'Tên', 'Tên tiếng Anh', 'Khoa quản lý', 'Trình độ', 'Tổng tín chỉ', 'Số học kỳ chuẩn', 'Trạng thái'], $rows[0]);
        $this->assertCount(3, $rows);
        $this->assertSame(['7220201', 'Ngôn ngữ Anh', '', 'Khoa Ngoại ngữ', 'Đại học', 130, 8, 'Hoạt động'], array_map(fn ($v) => $v ?? '', $rows[1]));
    }
}
