<?php

namespace Tests\Feature\Modules\Auth;

use App\Models\User;
use App\Modules\Auth\Models\Role;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

class RoleApiTest extends TestCase
{
    use InteractsWithRoles;
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
        $this->admin = $this->userWithRoles('ADMIN');
    }

    public function test_khach_va_nguoi_khong_co_quyen_bi_chan(): void
    {
        $this->getJson('/admin/roles')->assertUnauthorized();

        $student = $this->userWithRoles('STU');
        $this->actingAs($student)->getJson('/admin/roles')->assertForbidden();
        $this->actingAs($student)->postJson('/admin/roles', ['code' => 'X', 'name' => 'X'])->assertForbidden();

        $this->assertSame(2, AuditLog::where('event', AuditEvent::AccessDenied)->where('user_id', $student->id)->count());
    }

    public function test_quyen_tu_quan_ly_tai_khoan_khong_du_de_quan_ly_phan_quyen(): void
    {
        // Mọi vai trò có AUTH.update phạm vi OWN (tự quản lý tài khoản); quản lý phân quyền cần phạm vi ALL
        $dean = $this->userWithRoles('DEAN');

        $this->actingAs($dean)->putJson('/admin/roles/'.Role::where('code', 'DEAN')->value('id').'/permissions', [
            'permissions' => ['SYS.update' => 'ALL'],
        ])->assertForbidden();
    }

    public function test_admin_xem_danh_sach_vai_tro_va_danh_muc_quyen(): void
    {
        $this->actingAs($this->admin)->getJson('/admin/roles')
            ->assertOk()
            ->assertJsonPath('total', 10)
            ->assertJsonFragment(['code' => 'LEC', 'name' => 'Giảng viên', 'status_label' => 'Hoạt động']);

        $this->actingAs($this->admin)->getJson('/admin/roles?q=giang vien')
            ->assertOk()->assertJsonPath('total', 1);

        $this->actingAs($this->admin)->getJson('/admin/permissions')
            ->assertOk()
            ->assertJsonFragment(['key' => 'STU.view', 'action_label' => 'Xem'])
            ->assertJsonFragment(['value' => 'SECTION', 'label' => 'Lớp học phần được phân công']);
    }

    public function test_admin_tao_sua_xoa_vai_tro(): void
    {
        $created = $this->actingAs($this->admin)->postJson('/admin/roles', ['code' => 'thu_vien', 'name' => 'Cán bộ thư viện'])
            ->assertCreated()
            ->assertJsonPath('code', 'THU_VIEN')
            ->json();

        $this->actingAs($this->admin)->postJson('/admin/roles', ['code' => 'THU_VIEN', 'name' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['code', 'name']);

        $this->actingAs($this->admin)->putJson("/admin/roles/{$created['id']}", ['name' => 'Thư viện', 'status' => 'inactive'])
            ->assertOk()
            ->assertJsonPath('status', 'inactive');

        $this->actingAs($this->admin)->deleteJson("/admin/roles/{$created['id']}")->assertNoContent();
    }

    public function test_tu_choi_nghiep_vu_tra_ve_422_kem_cach_khac_phuc(): void
    {
        $this->actingAs($this->admin)->deleteJson('/admin/roles/'.Role::where('code', 'LEC')->value('id'))
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Không thể xóa vai trò hệ thống mặc định LEC. Có thể ngừng sử dụng vai trò thay cho việc xóa.');
    }

    public function test_admin_cap_nhat_ma_tran_quyen(): void
    {
        $lecId = Role::where('code', 'LEC')->value('id');

        $this->actingAs($this->admin)->putJson("/admin/roles/{$lecId}/permissions", [
            'permissions' => ['GRD.view' => 'SECTION', 'GRD.update' => 'SECTION'],
        ])->assertOk()->assertExactJson([
            'code' => 'LEC',
            'permissions' => ['GRD.update' => 'SECTION', 'GRD.view' => 'SECTION'],
        ]);

        $this->actingAs($this->admin)->putJson("/admin/roles/{$lecId}/permissions", [
            'permissions' => ['GRD.view' => 'TOAN_TRUONG'],
        ])->assertUnprocessable()->assertJsonValidationErrors(['permissions.GRD.view']);
    }

    public function test_admin_gan_va_go_vai_tro_cho_nguoi_dung(): void
    {
        $user = User::factory()->create();
        $lecId = Role::where('code', 'LEC')->value('id');

        $this->actingAs($this->admin)->postJson("/admin/users/{$user->id}/roles", ['role_id' => $lecId])
            ->assertCreated()
            ->assertJsonPath('active', ['LEC']);

        $this->actingAs($this->admin)->deleteJson("/admin/users/{$user->id}/roles/{$lecId}")->assertNoContent();

        $this->actingAs($this->admin)->getJson("/admin/users/{$user->id}/roles")
            ->assertOk()
            ->assertJsonPath('active', [])
            ->assertJsonPath('history.0.code', 'LEC');
    }
}
