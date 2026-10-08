<?php

namespace Tests\Feature\Modules\System;

use App\Modules\System\Enums\AdministrativeLevel;
use App\Modules\System\Enums\AdministrativeScheme;
use App\Modules\System\Models\AdministrativeUnit;
use App\Modules\System\Models\LookupCategory;
use App\Modules\System\Models\LookupValue;
use App\Modules\System\Services\AdministrativeUnitService;
use App\Modules\System\Services\LookupService;
use App\Support\Exceptions\BusinessRuleException;
use App\Support\References\ReferenceRegistry;
use Database\Seeders\LookupSeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LookupServiceTest extends TestCase
{
    use DatabaseMigrations;

    private LookupService $lookups;

    private AdministrativeUnitService $units;

    protected function setUp(): void
    {
        parent::setUp();

        $this->lookups = app(LookupService::class);
        $this->units = app(AdministrativeUnitService::class);
    }

    private function category(string $code = 'GENDER'): LookupCategory
    {
        return LookupCategory::create(['code' => $code, 'name' => 'Giới tính', 'is_system' => true]);
    }

    /** Chạy $callback, bắt lỗi nghiệp vụ và trả về để kiểm tra thông báo + cách khắc phục (GC-04). */
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

    public function test_ma_on_dinh_khong_doi_duoc_va_khong_cap_lai_ke_ca_khi_da_xoa(): void
    {
        $value = $this->lookups->createValue($this->category(), ['code' => '1', 'name' => 'Nam']);

        $e = $this->rejected(fn () => $this->lookups->updateValue($value, ['code' => 'M']));
        $this->assertStringContainsString('Không đổi được mã', $e->getMessage());

        // Đổi tên thì được
        $this->assertSame('Nam giới', $this->lookups->updateValue($value, ['name' => 'Nam giới'])->name);

        // Chưa ai dùng nên xóa được, nhưng mã không được cấp lại
        $this->lookups->deleteValue($value);
        $this->assertSoftDeleted($value);

        $e = $this->rejected(fn () => $this->lookups->createValue($value->category, ['code' => '1', 'name' => 'Nam']));
        $this->assertStringContainsString('đã có trong danh mục', $e->getMessage());
    }

    public function test_gia_tri_dang_duoc_tham_chieu_chi_ngung_khong_xoa(): void
    {
        Schema::create('test_people', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gender_id');
        });
        app(ReferenceRegistry::class)->register(LookupValue::class, 'test_people', 'gender_id', 'người');

        $male = $this->lookups->createValue($this->category(), ['code' => '1', 'name' => 'Nam']);
        DB::table('test_people')->insert([['gender_id' => $male->id], ['gender_id' => $male->id]]);

        $e = $this->rejected(fn () => $this->lookups->deleteValue($male));
        $this->assertStringContainsString('đang được dùng bởi 2 người', $e->getMessage());
        $this->assertStringContainsString('ngừng sử dụng', $e->hint());
        $this->assertNotSoftDeleted($male);

        // Ngừng sử dụng thì không còn trong danh sách lựa chọn, dữ liệu cũ vẫn trỏ đúng
        $this->lookups->updateValue($male, ['status' => 'inactive']);
        $this->assertSame([], $this->lookups->options('GENDER')->pluck('code')->all());
        $this->assertSame('Nam', LookupValue::find(DB::table('test_people')->value('gender_id'))->name);
    }

    public function test_danh_sach_lua_chon_chi_gom_gia_tri_dang_hoat_dong_theo_thu_tu(): void
    {
        $category = $this->category('RELIGION');
        $this->lookups->createValue($category, ['code' => '02', 'name' => 'Công giáo', 'sort_order' => 2]);
        $this->lookups->createValue($category, ['code' => '00', 'name' => 'Không', 'sort_order' => 0]);
        $buddhism = $this->lookups->createValue($category, ['code' => '01', 'name' => 'Phật giáo', 'sort_order' => 1]);
        $this->lookups->updateValue($buddhism, ['status' => 'inactive']);

        $this->assertSame(['00', '02'], $this->lookups->options('RELIGION')->pluck('code')->all());

        // Cả danh mục ngừng thì không có lựa chọn nào và không thêm giá trị được
        $this->lookups->updateCategory($category, ['status' => 'inactive']);
        $this->assertCount(0, $this->lookups->options('RELIGION'));
        $this->rejected(fn () => $this->lookups->createValue($category->refresh(), ['code' => '03', 'name' => 'Tin lành']));
    }

    public function test_khong_xoa_danh_muc_he_thong_hoac_da_co_gia_tri(): void
    {
        $system = $this->category();
        $this->rejected(fn () => $this->lookups->deleteCategory($system));

        $custom = $this->lookups->createCategory(['code' => 'BLOOD_TYPE', 'name' => 'Nhóm máu']);
        $this->lookups->createValue($custom, ['code' => 'O', 'name' => 'Nhóm O']);
        $this->rejected(fn () => $this->lookups->deleteCategory($custom));

        $empty = $this->lookups->createCategory(['code' => 'EMPTY', 'name' => 'Trống']);
        $this->lookups->deleteCategory($empty);
        $this->assertSoftDeleted($empty);
        $this->rejected(fn () => $this->lookups->createCategory(['code' => 'EMPTY', 'name' => 'Lại']));
    }

    public function test_don_vi_hai_cap_khong_co_cap_huyen_va_xa_phai_thuoc_tinh(): void
    {
        $province = $this->units->create(['scheme' => 'two_level_2025', 'level' => 'province', 'code' => '01', 'name' => 'Thành phố Hà Nội', 'unit_type' => 'Thành phố']);
        $ward = $this->units->create(['scheme' => 'two_level_2025', 'level' => 'commune', 'code' => '00004', 'name' => 'Phường Ba Đình', 'unit_type' => 'Phường', 'parent_id' => $province->id]);

        $this->assertSame('Phường Ba Đình, Thành phố Hà Nội', $ward->fullName());

        $e = $this->rejected(fn () => $this->units->create(['scheme' => 'two_level_2025', 'level' => 'district', 'code' => '001', 'name' => 'Quận Ba Đình', 'unit_type' => 'Quận', 'parent_id' => $province->id]));
        $this->assertStringContainsString('không có cấp', $e->getMessage());

        // Xã không có tỉnh, hoặc trỏ tới một xã khác
        $this->rejected(fn () => $this->units->create(['scheme' => 'two_level_2025', 'level' => 'commune', 'code' => '00007', 'name' => 'Phường X', 'unit_type' => 'Phường']));
        $this->rejected(fn () => $this->units->create(['scheme' => 'two_level_2025', 'level' => 'commune', 'code' => '00007', 'name' => 'Phường X', 'unit_type' => 'Phường', 'parent_id' => $ward->id]));

        // Tỉnh không có cấp trên
        $this->rejected(fn () => $this->units->create(['scheme' => 'two_level_2025', 'level' => 'province', 'code' => '04', 'name' => 'Tỉnh Cao Bằng', 'unit_type' => 'Tỉnh', 'parent_id' => $province->id]));
    }

    public function test_du_lieu_ba_cap_cu_van_tra_cuu_duoc_va_tro_toi_don_vi_moi(): void
    {
        $newHanoi = $this->units->create(['scheme' => 'two_level_2025', 'level' => 'province', 'code' => '01', 'name' => 'Thành phố Hà Nội', 'unit_type' => 'Thành phố']);

        // Cùng mã 01 ở hai mô hình khác nhau là hợp lệ
        $oldHanoi = $this->units->create(['scheme' => 'three_level_legacy', 'level' => 'province', 'code' => '01', 'name' => 'Thành phố Hà Nội', 'unit_type' => 'Thành phố', 'successor_id' => $newHanoi->id]);
        $baDinh = $this->units->create(['scheme' => 'three_level_legacy', 'level' => 'district', 'code' => '001', 'name' => 'Quận Ba Đình', 'unit_type' => 'Quận', 'parent_id' => $oldHanoi->id]);

        // Ba cấp cũ: xã phải thuộc huyện, không thuộc thẳng tỉnh
        $this->rejected(fn () => $this->units->create(['scheme' => 'three_level_legacy', 'level' => 'commune', 'code' => '00001', 'name' => 'Phường Phúc Xá', 'unit_type' => 'Phường', 'parent_id' => $oldHanoi->id]));
        $phucXa = $this->units->create(['scheme' => 'three_level_legacy', 'level' => 'commune', 'code' => '00001', 'name' => 'Phường Phúc Xá', 'unit_type' => 'Phường', 'parent_id' => $baDinh->id]);

        $this->assertSame('Phường Phúc Xá, Quận Ba Đình, Thành phố Hà Nội', $phucXa->fullName());
        $this->assertSame($newHanoi->id, $oldHanoi->successor->id);

        // Tra cứu: mặc định chỉ mô hình mới; mô hình cũ phải chọn rõ
        $this->assertSame([$newHanoi->id], $this->units->options(AdministrativeScheme::TwoLevel2025)->pluck('id')->all());
        $this->assertSame([$phucXa->id], $this->units->options(AdministrativeScheme::ThreeLevelLegacy, term: 'phuc xa', level: AdministrativeLevel::Commune)->pluck('id')->all());

        // Đơn vị thay thế phải thuộc mô hình mới; đơn vị mới thì không có đơn vị thay thế
        $this->rejected(fn () => $this->units->update($baDinh, ['successor_id' => $oldHanoi->id]));
        $this->rejected(fn () => $this->units->update($newHanoi, ['successor_id' => $newHanoi->id]));

        // Đơn vị mới đang được dữ liệu cũ trỏ tới thì không xóa được
        $this->rejected(fn () => $this->units->delete($newHanoi));
    }

    public function test_don_vi_hanh_chinh_khong_doi_ma_cap_tren_va_chi_ngung_khi_het_don_vi_con_hoat_dong(): void
    {
        $province = $this->units->create(['scheme' => 'two_level_2025', 'level' => 'province', 'code' => '01', 'name' => 'Thành phố Hà Nội', 'unit_type' => 'Thành phố']);
        $ward = $this->units->create(['scheme' => 'two_level_2025', 'level' => 'commune', 'code' => '00004', 'name' => 'Phường Ba Đình', 'unit_type' => 'Phường', 'parent_id' => $province->id]);

        $this->rejected(fn () => $this->units->update($ward, ['code' => '00005']));
        $this->rejected(fn () => $this->units->create(['scheme' => 'two_level_2025', 'level' => 'commune', 'code' => '00004', 'name' => 'Trùng mã', 'unit_type' => 'Phường', 'parent_id' => $province->id]));

        $e = $this->rejected(fn () => $this->units->update($province, ['status' => 'inactive']));
        $this->assertStringContainsString('1 đơn vị trực thuộc đang hoạt động', $e->getMessage());
        $this->rejected(fn () => $this->units->delete($province));

        $this->units->update($ward, ['status' => 'inactive']);
        $this->units->update($province, ['status' => 'inactive']);
        $this->assertFalse($province->refresh()->isActive());

        // Không kích hoạt xã khi tỉnh đang ngừng
        $this->rejected(fn () => $this->units->update($ward, ['status' => 'active']));

        // Xã chưa ai dùng, không có đơn vị con thì xóa được
        $this->units->delete($ward);
        $this->assertSoftDeleted($ward);
    }

    public function test_seeder_chay_lap_lai_an_toan_va_du_du_lieu_chuan(): void
    {
        $this->seed(LookupSeeder::class);
        $this->seed(LookupSeeder::class);

        $this->assertCount(54, $this->lookups->options(LookupCategory::ETHNICITY));
        $this->assertSame('Kinh', $this->lookups->options(LookupCategory::ETHNICITY)->first()->name);
        $this->assertSame(['1', '2'], $this->lookups->options(LookupCategory::GENDER)->pluck('code')->all());

        $provinces = AdministrativeUnit::query()->scheme(AdministrativeScheme::TwoLevel2025)->get();
        $this->assertCount(34, $provinces);
        $this->assertCount(6, $provinces->where('unit_type', 'Thành phố'));
        $this->assertSame(0, $provinces->where('level', AdministrativeLevel::District)->count());

        $phucXa = AdministrativeUnit::query()->scheme(AdministrativeScheme::ThreeLevelLegacy)->where('code', '00001')->first();
        $this->assertSame('Phường Phúc Xá, Quận Ba Đình, Thành phố Hà Nội', $phucXa->fullName());
        $this->assertTrue(LookupCategory::query()->where('is_system', false)->doesntExist());
    }
}
