<?php

namespace Tests\Feature\Modules\Auth;

use App\Models\User;
use App\Modules\Auth\Models\Role;
use App\Modules\Auth\Services\PasswordService;
use App\Modules\Auth\Services\RoleService;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

/** Đổi mật khẩu và chính sách mật khẩu (FR-AUTH-004, FR-AUTH-005). */
class PasswordChangeTest extends TestCase
{
    use InteractsWithRoles;
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
        $this->user = User::factory()->create(['username' => 'gv001', 'password' => 'MatKhau@123']);
        app(RoleService::class)->assign($this->user, Role::where('code', 'LEC')->firstOrFail());
    }

    private function change(string $current, string $new)
    {
        return $this->actingAs($this->user)->from('/password/change')->put('/password', [
            'current_password' => $current,
            'password' => $new,
            'password_confirmation' => $new,
        ]);
    }

    public function test_doi_mat_khau_thanh_cong(): void
    {
        $oldRememberToken = $this->user->remember_token;
        $this->user->forceFill(['must_change_password' => true])->save();

        $this->change('MatKhau@123', 'MatKhauMoi9')->assertRedirect('/teacher');

        $fresh = $this->user->fresh();
        $this->assertTrue(Hash::check('MatKhauMoi9', $fresh->password));
        $this->assertFalse($fresh->must_change_password);
        $this->assertNotNull($fresh->password_changed_at);
        $this->assertNotSame($oldRememberToken, $fresh->remember_token, '"Ghi nhớ đăng nhập" trên thiết bị khác bị vô hiệu');
        $this->assertSame(1, $fresh->passwordHistories()->count());
        $this->assertSame(1, AuditLog::where('event', AuditEvent::PasswordChanged)->where('auditable_id', $this->user->id)->count());
        $this->assertStringNotContainsString('MatKhauMoi9', (string) json_encode(AuditLog::all()->toArray()), 'Không ghi mật khẩu vào nhật ký');
    }

    public function test_sai_mat_khau_hien_tai(): void
    {
        $this->change('Sai@12345', 'MatKhauMoi9')->assertSessionHasErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
    }

    public function test_mat_khau_moi_phai_dat_chinh_sach(): void
    {
        $this->change('MatKhau@123', 'ngan')->assertSessionHasErrors('password');
        $this->change('MatKhau@123', 'toanchuthuong1')->assertSessionHasErrors('password');
        $this->change('MatKhau@123', 'KhongCoSo')->assertSessionHasErrors('password');

        $this->assertMatchesRegularExpression('/[ăâđêôơưàáạảãèéẹẻẽìíịỉĩòóọỏõùúụủũỳýỵỷỹ]/u', session('errors')->first('password'), 'Thông báo bằng tiếng Việt');
        $this->assertTrue(Hash::check('MatKhau@123', $this->user->fresh()->password), 'Không đổi khi bị từ chối');
    }

    public function test_khong_dung_lai_mat_khau_gan_day(): void
    {
        $this->change('MatKhau@123', 'MatKhau@123')->assertSessionHasErrors('password');

        $this->change('MatKhau@123', 'MatKhauB12')->assertRedirect();
        $this->change('MatKhauB12', 'MatKhauC12')->assertRedirect();
        $this->change('MatKhauC12', 'MatKhauB12')->assertSessionHasErrors('password');

        $this->assertStringContainsString('Không được dùng lại', session('errors')->first('password'));
    }

    public function test_doi_mat_khau_dang_xuat_cac_phien_khac(): void
    {
        config(['session.driver' => 'database']);
        foreach (['phien-hien-tai', 'phien-may-khac'] as $id) {
            DB::table('sessions')->insert(['id' => $id, 'user_id' => $this->user->id, 'payload' => '', 'last_activity' => time()]);
        }

        app(PasswordService::class)->change($this->user, 'MatKhau@123', 'MatKhauMoi9', 'phien-hien-tai');

        $this->assertSame(['phien-hien-tai'], DB::table('sessions')->where('user_id', $this->user->id)->pluck('id')->all());
    }

    public function test_mat_khau_qua_han_thi_buoc_doi(): void
    {
        config(['studentmanager.auth.password.expires_days' => 90]);
        $this->user->forceFill(['password_changed_at' => now()->subDays(91)])->save();

        $this->actingAs($this->user)->get('/teacher')->assertRedirect(route('password.change'));

        $this->assertThrows(
            fn () => app(PasswordService::class)->change($this->user->fresh(), 'Sai@12345', 'MatKhauMoi9'),
            ValidationException::class
        );
    }
}
