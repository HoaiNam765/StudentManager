<?php

namespace Tests\Feature\Modules\Auth;

use App\Models\User;
use App\Modules\Auth\Models\Role;
use App\Modules\Auth\Services\PasswordService;
use App\Modules\Auth\Services\RoleService;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

/** UAT-01: đăng nhập, khóa tạm sau nhiều lần sai, mật khẩu tạm (FR-AUTH-001, 002, 006; BR-AUTH-06, 09, 10). */
class LoginTest extends TestCase
{
    use InteractsWithRoles;
    use RefreshDatabase;

    private const PASSWORD = 'MatKhau@123';

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    private function account(string $username, string ...$roles): User
    {
        $user = User::factory()->create(['username' => $username, 'password' => self::PASSWORD]);

        foreach ($roles as $code) {
            app(RoleService::class)->assign($user, Role::where('code', $code)->firstOrFail());
        }

        return $user;
    }

    private function login(string $login, string $password = self::PASSWORD)
    {
        return $this->from('/login')->post('/login', ['login' => $login, 'password' => $password]);
    }

    public function test_dang_nhap_bang_ten_dang_nhap_hoac_email_va_vao_dung_cong(): void
    {
        $lecturer = $this->account('gv001', 'LEC');
        $student = $this->account('2001230535', 'STU');
        $admin = $this->account('qt01', 'ADMIN');
        $dean = $this->account('tk01', 'LEC', 'DEAN');

        $this->login('gv001')->assertRedirect('/teacher');
        $this->assertAuthenticatedAs($lecturer);
        $this->post('/logout');

        $this->login($student->email)->assertRedirect('/student');
        $this->post('/logout');

        $this->login('qt01')->assertRedirect('/admin');
        $this->post('/logout');

        // Nhiều vai trò: ưu tiên Cổng Quản trị / Văn phòng
        $this->login('tk01')->assertRedirect('/admin');

        $this->assertNotNull($admin->fresh()->last_login_at);
        $this->assertSame(4, AuditLog::where('event', AuditEvent::Login)->count());
        $this->assertSame($dean->id, AuditLog::where('event', AuditEvent::Login)->latest('id')->first()->user_id);
    }

    public function test_sai_mat_khau_va_tai_khoan_khong_ton_tai_nhan_cung_mot_thong_bao(): void
    {
        $this->account('gv001', 'LEC');

        $wrongPassword = $this->login('gv001', 'SaiMatKhau1')->assertSessionHasErrors('login');
        $wrongMessage = session('errors')->first('login');
        $this->post('/logout');

        $this->login('khong-ton-tai')->assertSessionHasErrors('login');
        $unknownMessage = session('errors')->first('login');

        $this->assertGuest();
        $this->assertSame($wrongMessage, $unknownMessage, 'Không được lộ tên đăng nhập có tồn tại hay không (BR-AUTH-09)');
        $this->assertStringNotContainsString('khóa', mb_strtolower(explode('.', $wrongMessage)[0]), 'Câu đầu chỉ nói sai thông tin');

        $failed = AuditLog::where('event', AuditEvent::LoginFailed)->orderBy('id')->get();
        $this->assertSame(['Sai mật khẩu', 'Tài khoản không tồn tại'], $failed->pluck('reason')->all());
        $this->assertSame(['login' => 'khong-ton-tai'], $failed[1]->new_values);
    }

    public function test_khoa_tam_sau_5_lan_sai_ke_ca_khi_nhap_dung_mat_khau(): void
    {
        $user = $this->account('gv001', 'LEC');
        $this->travelTo(now()->startOfMinute());

        foreach (range(1, 5) as $attempt) {
            $this->login('gv001', 'SaiMatKhau1')->assertSessionHasErrors('login');
        }

        $this->assertTrue($user->fresh()->isLockedOut());
        $this->assertSame(1, AuditLog::where('event', AuditEvent::Locked)->where('auditable_id', $user->id)->count());

        $this->login('gv001')->assertSessionHasErrors('login');
        $this->assertGuest();
        $this->assertSame('Tài khoản đang tạm khóa', AuditLog::where('event', AuditEvent::LoginFailed)->latest('id')->first()->reason);

        $this->travel(16)->minutes();

        $this->login('gv001')->assertRedirect('/teacher');
        $this->assertFalse($user->fresh()->isLockedOut());
        $this->assertSame(0, $user->fresh()->failed_login_count);
    }

    public function test_nhap_sai_rai_rac_ngoai_khoang_15_phut_thi_khong_bi_khoa(): void
    {
        $user = $this->account('gv001', 'LEC');

        foreach (range(1, 4) as $attempt) {
            $this->login('gv001', 'SaiMatKhau1');
        }
        $this->travel(16)->minutes();
        $this->login('gv001', 'SaiMatKhau1');

        $this->assertFalse($user->fresh()->isLockedOut());
        $this->assertSame(1, $user->fresh()->failed_login_count, 'Bộ đếm bắt đầu lại khi lần sai đầu tiên đã quá 15 phút');
    }

    public function test_tai_khoan_chua_co_vai_tro_khong_dang_nhap_duoc(): void
    {
        $this->account('moi01');

        $this->login('moi01')->assertSessionHasErrors(['login' => 'Tài khoản chưa được cấp quyền truy cập. Liên hệ phòng Đào tạo hoặc quản trị viên.']);
        $this->assertGuest();
    }

    public function test_mat_khau_tam_buoc_doi_va_het_han_sau_7_ngay(): void
    {
        $user = $this->account('2001230535', 'STU');
        app(PasswordService::class)->setTemporaryPassword($user, 'Tam@12345');

        $this->login('2001230535', 'Tam@12345')->assertRedirect(route('password.change'));
        $this->get('/student')->assertRedirect(route('password.change'));
        $this->getJson('/student')->assertStatus(428)->assertJsonPath('message', 'Bạn cần đổi mật khẩu trước khi tiếp tục.');
        $this->post('/logout');

        $this->travel(8)->days();

        $this->login('2001230535', 'Tam@12345')->assertSessionHasErrors('login');
        $this->assertStringContainsString('Mật khẩu tạm đã hết hạn', session('errors')->first('login'));
        $this->assertGuest();
    }

    public function test_dang_xuat_huy_phien_va_ghi_nhat_ky(): void
    {
        $user = $this->account('gv001', 'LEC');
        $this->login('gv001');

        $this->post('/logout')->assertRedirect(route('login'));

        $this->assertGuest();
        $this->get('/teacher')->assertRedirect(route('login'));
        $this->assertSame($user->id, AuditLog::where('event', AuditEvent::Logout)->sole()->user_id);
    }

    public function test_gioi_han_tan_suat_gui_bieu_mau_dang_nhap(): void
    {
        $limit = config('studentmanager.auth.throttle_per_minute');

        foreach (range(1, $limit) as $attempt) {
            $this->postJson('/login', ['login' => 'khong-ton-tai', 'password' => 'x'])->assertUnprocessable();
        }

        $this->postJson('/login', ['login' => 'khong-ton-tai', 'password' => 'x'])
            ->assertStatus(429)
            ->assertJsonPath('message', fn (string $message) => str_starts_with($message, 'Bạn gửi yêu cầu đăng nhập quá nhiều lần.'));
    }

    public function test_chuyen_huong_khach_va_nguoi_da_dang_nhap(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
        $this->get('/')->assertRedirect(route('login'));

        $this->actingAs($this->account('gv001', 'LEC'));
        $this->get('/login')->assertRedirect(route('root'));
        $this->get('/')->assertRedirect(route('teacher.home'));
    }

    public function test_mat_khau_bam_mot_chieu_va_cookie_phien_an_toan(): void
    {
        $user = $this->account('gv001', 'LEC');

        $this->assertNotSame(self::PASSWORD, $user->getRawOriginal('password'));
        $this->assertContains(Hash::info($user->password)['algoName'], ['bcrypt', 'argon2i', 'argon2id'], 'BR-AUTH-02');
        $this->assertTrue(config('session.http_only'), 'Cookie phiên HttpOnly (NFR-SEC-01)');
        $this->assertContains(config('session.same_site'), ['lax', 'strict'], 'Cookie phiên có SameSite');
    }
}
