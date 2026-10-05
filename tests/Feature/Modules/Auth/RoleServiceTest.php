<?php

namespace Tests\Feature\Modules\Auth;

use App\Models\User;
use App\Modules\Auth\Models\Role;
use App\Modules\Auth\Services\RoleService;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLog;
use App\Support\Exceptions\BusinessRuleException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

class RoleServiceTest extends TestCase
{
    use InteractsWithRoles;
    use RefreshDatabase;

    private RoleService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
        $this->service = app(RoleService::class);
    }

    private function role(string $code): Role
    {
        return Role::where('code', $code)->firstOrFail();
    }

    private function assertRejected(callable $action, string $message): void
    {
        try {
            $action();
            $this->fail("Phải từ chối: {$message}");
        } catch (BusinessRuleException $e) {
            $this->assertStringContainsString($message, $e->getMessage());
            $this->assertNotNull($e->hint(), 'Từ chối phải kèm cách khắc phục (GC-04)');
        }
    }

    // ---- Vai trò ----

    public function test_khong_xoa_duoc_vai_tro_he_thong_nhung_xoa_duoc_vai_tro_tu_tao(): void
    {
        $this->assertRejected(fn () => $this->service->delete($this->role('LEC')), 'Không thể xóa vai trò hệ thống');

        $custom = $this->service->create(['code' => 'thu_vien', 'name' => 'Cán bộ thư viện']);
        $this->assertSame('THU_VIEN', $custom->code);

        $this->service->delete($custom);
        $this->assertSoftDeleted('roles', ['code' => 'THU_VIEN']);
    }

    public function test_khong_xoa_vai_tro_dang_duoc_gan(): void
    {
        $custom = $this->service->create(['code' => 'TV', 'name' => 'Thư viện']);
        $this->service->assign(User::factory()->create(), $custom);

        $this->assertRejected(fn () => $this->service->delete($custom), 'đang được gán');
    }

    public function test_khong_ngung_duoc_vai_tro_admin(): void
    {
        $this->assertRejected(fn () => $this->service->deactivate($this->role('ADMIN')), 'Không thể ngừng vai trò ADMIN');
    }

    // ---- Ma trận quyền ----

    public function test_doi_ma_tran_kiem_tra_du_lieu_va_ghi_nhat_ky(): void
    {
        $lec = $this->role('LEC');

        $this->assertRejected(fn () => $this->service->syncPermissions($lec, ['XYZ.view' => 'ALL']), 'không có trong danh mục');
        $this->assertRejected(fn () => $this->service->syncPermissions($lec, ['GRD.view' => 'KHOA']), 'không hợp lệ');

        $this->service->syncPermissions($lec, ['GRD.view' => 'SECTION', 'FEE.view' => 'ALL']);

        $this->assertSame(['FEE.view' => 'ALL', 'GRD.view' => 'SECTION'], $lec->permissionMatrix());
        $log = AuditLog::where('auditable_type', Role::class)->where('auditable_id', $lec->id)->where('event', AuditEvent::Updated)->latest('id')->first();
        $this->assertSame(['FEE.view' => 'ALL'], $log->new_values['permissions'], 'Nhật ký ghi phần thêm vào');
        $this->assertArrayHasKey('GRD.update', $log->old_values['permissions'], 'Nhật ký ghi phần bị gỡ');
    }

    public function test_admin_phai_giu_quyen_quan_ly_phan_quyen_toan_truong(): void
    {
        $admin = $this->role('ADMIN');
        $matrix = $admin->permissionMatrix();

        $this->assertRejected(fn () => $this->service->syncPermissions($admin, array_diff_key($matrix, ['AUTH.update' => true])), 'Vai trò ADMIN phải giữ');
        $this->assertRejected(fn () => $this->service->syncPermissions($admin, ['AUTH.update' => 'OWN'] + $matrix), 'Vai trò ADMIN phải giữ');
    }

    // ---- Gán và gỡ vai trò ----

    public function test_gan_vai_tro_kiem_tra_va_ghi_nhat_ky(): void
    {
        $user = User::factory()->create();
        $gua = $this->role('GUA');

        $this->assertRejected(fn () => $this->service->assign($user, $gua), 'đang ngừng hoạt động');
        $this->assertRejected(fn () => $this->service->assign($user, $this->role('LEC'), '2026-12-31', '2026-01-01'), 'Ngày kết thúc');

        $this->service->assign($user, $this->role('LEC'));
        $this->assertRejected(fn () => $this->service->assign($user, $this->role('LEC')), 'đã có vai trò LEC');

        $this->assertTrue($user->hasRole('LEC'));
        $log = AuditLog::where('auditable_type', User::class)->where('auditable_id', $user->id)->sole();
        $this->assertSame([], $log->old_values['roles']);
        $this->assertSame(['LEC'], $log->new_values['roles']);
    }

    public function test_go_vai_tro_giu_lich_su_va_mat_quyen_ngay(): void
    {
        $user = $this->userWithRoles('LEC', 'ADV');

        $this->service->revoke($user, $this->role('ADV'));

        $this->assertFalse($user->hasRole('ADV'));
        $this->assertTrue($user->hasRole('LEC'));
        $this->assertSame(2, DB::table('user_roles')->where('user_id', $user->id)->count(), 'Không xóa dòng gán, chỉ đặt ngày kết thúc');
        $this->assertRejected(fn () => $this->service->revoke($user, $this->role('ADV')), 'không có vai trò ADV');

        $this->service->assign($user, $this->role('ADV'));
        $this->assertTrue($user->hasRole('ADV'), 'Gán lại được sau khi đã gỡ');
    }

    public function test_khong_go_duoc_admin_cuoi_cung(): void
    {
        $onlyAdmin = $this->userWithRoles('ADMIN');

        $this->assertRejected(fn () => $this->service->revoke($onlyAdmin, $this->role('ADMIN')), 'ADMIN cuối cùng');
        $this->assertRejected(fn () => $this->service->ensureNotLastAdmin($onlyAdmin), 'ADMIN cuối cùng');

        $this->userWithRoles('ADMIN');
        $this->service->revoke($onlyAdmin, $this->role('ADMIN'));

        $this->assertFalse($onlyAdmin->hasRole('ADMIN'), 'Còn ADMIN khác thì gỡ được');
    }
}
