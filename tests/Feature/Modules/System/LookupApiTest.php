<?php

namespace Tests\Feature\Modules\System;

use App\Modules\System\Models\LookupValue;
use Database\Seeders\LookupSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

class LookupApiTest extends TestCase
{
    use DatabaseMigrations;
    use InteractsWithRoles;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
        $this->seed(LookupSeeder::class);
    }

    public function test_quan_tri_tao_danh_muc_va_gia_tri_ma_khong_doi_duoc(): void
    {
        $this->actingAs($this->userWithRoles('ADMIN'));

        $this->postJson('/admin/lookups', ['code' => 'blood_type', 'name' => 'Nhóm máu'])
            ->assertCreated()
            ->assertJsonPath('code', 'BLOOD_TYPE')
            ->assertJsonPath('is_system', false);

        $id = $this->postJson('/admin/lookups/BLOOD_TYPE/values', ['code' => 'O', 'name' => 'Nhóm O'])
            ->assertCreated()
            ->json('id');

        $this->putJson("/admin/lookup-values/{$id}", ['code' => 'O+', 'name' => 'Nhóm O'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['code' => 'Không đổi được mã']);

        $this->putJson("/admin/lookup-values/{$id}", ['name' => 'Nhóm máu O', 'status' => 'inactive'])
            ->assertOk()
            ->assertJsonPath('name', 'Nhóm máu O')
            ->assertJsonPath('status', 'inactive');

        $this->getJson('/admin/lookups/BLOOD_TYPE/values?status=inactive')->assertOk()->assertJsonPath('data.0.code', 'O');

        // Trùng mã trong danh mục: lỗi nghiệp vụ 422 kèm cách khắc phục
        $this->postJson('/admin/lookups/BLOOD_TYPE/values', ['code' => 'O', 'name' => 'Lại'])
            ->assertStatus(422)
            ->assertJsonPath('message', fn ($m) => str_contains($m, 'đã có trong danh mục') && str_contains($m, 'không được cấp lại'));

        // Danh mục hệ thống không xóa được
        $this->deleteJson('/admin/lookups/GENDER')->assertStatus(422);
    }

    public function test_can_bo_dao_tao_xem_va_sua_nhung_khong_them_xoa_theo_ma_tran(): void
    {
        $this->actingAs($this->userWithRoles('ACAD')); // SYS của ACAD: RU
        $kinh = LookupValue::query()->where('code', '01')->whereHas('category', fn ($q) => $q->where('code', 'ETHNICITY'))->firstOrFail();

        $this->getJson('/admin/lookups')->assertOk()->assertJsonPath('total', 7);
        $this->getJson('/admin/lookups/ETHNICITY/values')->assertOk()->assertJsonPath('total', 54);
        $this->putJson("/admin/lookup-values/{$kinh->id}", ['name' => 'Kinh'])->assertOk();

        $this->postJson('/admin/lookups/ETHNICITY/values', ['code' => '55', 'name' => 'Thử'])->assertForbidden();
        $this->deleteJson("/admin/lookup-values/{$kinh->id}")->assertForbidden();
        $this->postJson('/admin/administrative-units', [])->assertForbidden();
    }

    public function test_sinh_vien_khong_vao_quan_tri_nhung_doc_duoc_danh_sach_lua_chon(): void
    {
        $this->actingAs($this->userWithRoles('STU'));

        $this->getJson('/admin/lookups')->assertForbidden();

        $female = LookupValue::query()->where('code', '2')->whereHas('category', fn ($q) => $q->where('code', 'GENDER'))->firstOrFail();
        $female->deactivate();

        $this->getJson('/danh-muc/GENDER')
            ->assertOk()
            ->assertExactJson([['id' => LookupValue::query()->where('code', '1')->whereHas('category', fn ($q) => $q->where('code', 'GENDER'))->value('id'), 'code' => '1', 'name' => 'Nam']]);

        $this->getJson('/danh-muc/KHONG_CO')->assertOk()->assertExactJson([]);

        // Địa chỉ: mặc định 34 tỉnh/thành mô hình hai cấp; tìm không dấu
        $this->getJson('/danh-muc/don-vi-hanh-chinh')->assertOk()->assertJsonCount(34);
        $this->getJson('/danh-muc/don-vi-hanh-chinh?q=ho chi minh')->assertOk()->assertJsonPath('0.code', '79');
        $this->getJson('/danh-muc/don-vi-hanh-chinh?scheme=three_level_legacy&level=commune&q=phuc xa')->assertOk()->assertJsonPath('0.code', '00001');
    }

    public function test_khach_chua_dang_nhap_bi_chan(): void
    {
        $this->getJson('/danh-muc/GENDER')->assertUnauthorized();
        $this->getJson('/admin/lookups')->assertUnauthorized();
    }

    public function test_quan_tri_don_vi_hanh_chinh_qua_api(): void
    {
        $this->actingAs($this->userWithRoles('ADMIN'));
        $hanoi = $this->getJson('/admin/administrative-units?q=ha noi')->assertOk()->json('data.0');

        $wardId = $this->postJson('/admin/administrative-units', [
            'scheme' => 'two_level_2025', 'level' => 'commune', 'code' => '00004',
            'name' => 'Phường Ba Đình', 'unit_type' => 'Phường', 'parent_id' => $hanoi['id'],
        ])->assertCreated()->json('id');

        $this->getJson("/admin/administrative-units/{$wardId}")
            ->assertOk()
            ->assertJsonPath('full_name', 'Phường Ba Đình, Thành phố Hà Nội')
            ->assertJsonPath('parent.code', '01');

        $this->putJson("/admin/administrative-units/{$wardId}", ['parent_id' => 999])
            ->assertStatus(422)
            ->assertJsonValidationErrors('parent_id');

        $this->putJson("/admin/administrative-units/{$hanoi['id']}", ['status' => 'inactive'])
            ->assertStatus(422)
            ->assertJsonPath('message', fn ($m) => str_contains($m, 'đơn vị trực thuộc đang hoạt động'));

        $this->deleteJson("/admin/administrative-units/{$wardId}")->assertNoContent();
    }
}
