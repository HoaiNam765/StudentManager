<?php

namespace Tests\Feature\Modules\Auth;

use App\Models\User;
use App\Modules\Auth\Models\Permission;
use App\Modules\Auth\Models\Role;
use Database\Seeders\AuthSeeder;
use Database\Seeders\DevUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_nap_du_vai_tro_he_thong_va_danh_muc_quyen(): void
    {
        $this->seed(AuthSeeder::class);

        $this->assertSame(
            ['ACAD', 'ADMIN', 'ADV', 'CTSV', 'DEAN', 'EXAM', 'FIN', 'GUA', 'LEC', 'STU'],
            Role::orderBy('code')->pluck('code')->all()
        );
        $this->assertTrue(Role::where('is_system', false)->doesntExist(), 'Vai trò mặc định đều là vai trò hệ thống');
        $this->assertFalse(Role::where('code', 'GUA')->first()->isActive(), 'Phụ huynh là tùy chọn, mặc định ngừng');
        $this->assertSame(count(AuthSeeder::MODULES) * 6, Permission::count());
    }

    /** Đối chiếu một số ô tiêu biểu với docs/BA.md mục 4.2 và 4.3. */
    public function test_ma_tran_khop_voi_tai_lieu_ba(): void
    {
        $this->seed(AuthSeeder::class);
        $matrix = fn (string $code) => Role::where('code', $code)->first()->permissionMatrix();

        // ACAD | STU | CRUAX (toàn trường)
        $acad = $matrix('ACAD');
        foreach (['create', 'view', 'update', 'approve', 'export'] as $action) {
            $this->assertSame('ALL', $acad["STU.{$action}"]);
        }
        $this->assertArrayNotHasKey('STU.delete', $acad);

        // LEC | GRD | CRU* (lớp học phần được phân công); LEC | FEE | — ; LEC | TCH | RU* (hồ sơ của mình)
        $lec = $matrix('LEC');
        $this->assertSame(['GRD.create' => 'SECTION', 'GRD.update' => 'SECTION', 'GRD.view' => 'SECTION'], array_intersect_key($lec, array_flip(['GRD.create', 'GRD.update', 'GRD.view', 'GRD.approve'])));
        $this->assertArrayNotHasKey('FEE.view', $lec);
        $this->assertSame('OWN', $lec['TCH.update']);
        $this->assertSame('ALL', $lec['FAC.view'], 'Ô không có * là toàn trường');

        // STU | ENR | CRD* ; mọi vai trò | AUTH | U* là tài khoản của chính mình
        $stu = $matrix('STU');
        $this->assertSame(['ENR.create' => 'OWN', 'ENR.delete' => 'OWN', 'ENR.view' => 'OWN'], array_intersect_key($stu, array_flip(['ENR.create', 'ENR.delete', 'ENR.view', 'ENR.update'])));
        $this->assertSame('OWN', $matrix('DEAN')['AUTH.update']);
        $this->assertSame('FACULTY', $matrix('DEAN')['TCH.approve']);
        $this->assertSame('ADVISEE', $matrix('ADV')['STU.update']);

        // ADMIN | AUTH | CRUDA ; ADMIN không sửa dữ liệu nghiệp vụ (ghi chú 1): GRD chỉ R
        $admin = $matrix('ADMIN');
        $this->assertSame('ALL', $admin['AUTH.approve']);
        $this->assertArrayNotHasKey('GRD.update', $admin);
        $this->assertSame(['AUDIT.export' => 'ALL', 'AUDIT.view' => 'ALL'], array_intersect_key($admin, array_flip(['AUDIT.view', 'AUDIT.export'])));
        $this->assertArrayNotHasKey('AUDIT.view', $acad, 'Chỉ ADMIN xem nhật ký kiểm toán');
    }

    public function test_chay_lai_khong_ghi_de_dieu_chinh_cua_quan_tri_vien(): void
    {
        $this->seed(AuthSeeder::class);
        $lec = Role::where('code', 'LEC')->first();
        $lec->permissions()->sync([Permission::where('module', 'GRD')->where('action', 'view')->value('id') => ['scope' => 'SECTION']]);

        $this->seed(AuthSeeder::class);

        $this->assertSame(['GRD.view' => 'SECTION'], $lec->fresh()->permissionMatrix());
        $this->assertSame(10, Role::count());
    }

    public function test_tai_khoan_dung_thu_duoc_gan_admin(): void
    {
        $this->seed(); // DatabaseSeeder: AuthSeeder rồi DevUserSeeder

        $this->assertTrue(User::where('email', DevUserSeeder::EMAIL)->first()->hasRole(Role::ADMIN));
    }
}
