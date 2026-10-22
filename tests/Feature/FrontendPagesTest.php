<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Auth\Models\Role;
use App\Modules\Auth\Services\RoleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

/** Giao diện của nhóm FE (issue #2, #6, #32, #33, #34): đăng nhập, dashboard 3 cổng, trang tài khoản. */
class FrontendPagesTest extends TestCase
{
    use InteractsWithRoles;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    private function userWithRole(string $code): User
    {
        $user = User::factory()->create(['username' => fake()->unique()->userName()]);
        app(RoleService::class)->assign($user, Role::where('code', $code)->firstOrFail());

        return $user;
    }

    public function test_form_dang_nhap_gui_duoc_den_be(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('action="'.route('login.store').'"', false)
            ->assertSee('name="_token"', false)
            ->assertSee('name="login"', false)
            ->assertSee('name="remember"', false);
    }

    public function test_dang_nhap_sai_hien_thong_bao_loi(): void
    {
        $this->from('/login')->post('/login', ['login' => 'khong-co', 'password' => 'sai']);

        $this->followingRedirects()->get('/login')->assertSee('role="alert"', false);
    }

    public function test_dashboard_ba_cong_can_dang_nhap(): void
    {
        foreach (['/admin', '/student', '/teacher', '/student/mobile'] as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }

        foreach (['/admin/dashboard', '/sinh-vien/tong-quan', '/giang-vien/tong-quan'] as $url) {
            $this->get($url)->assertNotFound();
        }
    }

    public function test_dashboard_hien_thi_theo_cong(): void
    {
        $this->actingAs($this->userWithRole('ADMIN'))->get('/admin')->assertOk()->assertSee('Tổng quan Quản lý Đào tạo');
        $this->actingAs($this->userWithRole('STU'))->get('/student')->assertOk()->assertSee('Cổng thông tin Sinh viên');
        $this->actingAs($this->userWithRole('STU'))->get('/student/mobile')->assertOk()->assertSee('Lịch học hôm nay');
        $this->actingAs($this->userWithRole('LEC'))->get('/teacher')->assertOk()->assertSee('Cổng Giảng viên');
    }

    public function test_dashboard_co_nut_dang_xuat_dang_form_post(): void
    {
        $this->actingAs($this->userWithRole('ADMIN'))
            ->get('/admin')
            ->assertSee('action="'.route('logout').'"', false)
            ->assertSee('aria-label="Đăng xuất"', false)
            ->assertSee('id="app-sidebar"', false);
    }

    public function test_trang_tai_khoan_hien_thi_cho_nguoi_da_dang_nhap(): void
    {
        $this->get('/tai-khoan/cai-dat')->assertRedirect(route('login'));

        $this->actingAs($this->userWithRole('STU'));

        $this->get('/tai-khoan/cai-dat')->assertOk()->assertSee('Thông tin tài khoản');
        $this->get('/tai-khoan/phien-dang-nhap')->assertOk()->assertSee('Phiên đăng nhập');
        $this->get('/tai-khoan/thong-bao')->assertOk()->assertSee('Tùy chọn thông báo');
    }

    public function test_trang_doi_mat_khau_dung_truong_cua_be_va_khong_dien_san_mat_khau(): void
    {
        $this->actingAs($this->userWithRole('STU'))
            ->get(route('password.change'))
            ->assertOk()
            ->assertSee('action="'.route('password.update').'"', false)
            ->assertSee('name="_method" value="PUT"', false)
            ->assertSee('name="current_password"', false)
            ->assertSee('name="password"', false)
            ->assertSee('name="password_confirmation"', false)
            ->assertDontSee('HuitSecure@2026');
    }

    public function test_khong_con_route_trung_ten_voi_be(): void
    {
        $this->assertSame('/password/change', route('password.change', absolute: false));
        $this->assertSame('/password', route('password.update', absolute: false));
        $this->assertSame('/', route('root', absolute: false));
    }
}
