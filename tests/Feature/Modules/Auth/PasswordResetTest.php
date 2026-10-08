<?php

namespace Tests\Feature\Modules\Auth;

use App\Models\User;
use App\Modules\Auth\Notifications\ResetPasswordLink;
use App\Modules\Auth\Services\UserService;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLog;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\TestResponse;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use DatabaseMigrations;
    use InteractsWithRoles;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        $this->seedRoles();
        $this->user = $this->userWithRoles('STU');
        $this->user->forceFill(['username' => 'SV01', 'email' => 'sv01@sv.test', 'password' => 'MatKhauCu1', 'must_change_password' => false])->save();
    }

    /** Gửi yêu cầu và lấy mã trong email gần nhất. */
    private function requestToken(): string
    {
        $this->post('/forgot-password', ['login' => 'SV01']);

        return Notification::sent($this->user, ResetPasswordLink::class)->last()->token;
    }

    private function reset(string $token, string $password = 'MatKhauMoi9', ?string $email = null): TestResponse
    {
        return $this->from('/reset-password/'.$token)->post('/reset-password', [
            'token' => $token, 'email' => $email ?? 'sv01@sv.test', 'password' => $password, 'password_confirmation' => $password,
        ]);
    }

    public function test_yeu_cau_lien_ket_tra_cung_mot_thong_bao_du_tai_khoan_co_ton_tai_hay_khong(): void
    {
        $this->from('/forgot-password')->post('/forgot-password', ['login' => 'SV01'])->assertRedirect('/forgot-password')->assertSessionHas('status');
        $message = session('status');

        $this->from('/forgot-password')->post('/forgot-password', ['login' => 'khong-ton-tai'])->assertRedirect('/forgot-password')->assertSessionHas('status', $message);
        $this->assertSame($this->postJson('/forgot-password', ['login' => 'sv01@sv.test'])->json('message'), $this->postJson('/forgot-password', ['login' => 'ai-do@x.test'])->json('message'));

        Notification::assertSentTo($this->user, ResetPasswordLink::class);
        $this->assertSame(2, AuditLog::query()->where('event', AuditEvent::PasswordResetRequested)->whereNull('auditable_id')->count());
    }

    public function test_dat_lai_mat_khau_dung_mot_lan_huy_moi_phien_va_mo_khoa_tam(): void
    {
        config(['session.driver' => 'database']); // như môi trường thật; phpunit.xml dùng driver array
        DB::table('sessions')->insert(['id' => 'phien-cu', 'user_id' => $this->user->id, 'payload' => '', 'last_activity' => time()]);
        $this->user->forceFill(['locked_until' => now()->addMinutes(10)])->save();
        $token = $this->requestToken();

        $this->reset($token)->assertRedirect(route('login'))->assertSessionHas('status');

        $this->user->refresh();
        $this->assertTrue(Hash::check('MatKhauMoi9', $this->user->password));
        $this->assertFalse($this->user->isLockedOut(), 'Đặt lại xong thì mở khóa tạm');
        $this->assertSame(0, DB::table('sessions')->where('user_id', $this->user->id)->count(), 'Mọi phiên đang mở bị hủy (BR-AUTH-03)');
        $this->assertTrue(AuditLog::query()->where('event', AuditEvent::PasswordChanged)->where('reason', 'Đặt lại mật khẩu bằng liên kết')->exists());

        // Dùng lại liên kết lần hai bị từ chối
        $this->reset($token, 'MatKhauKhac8')->assertSessionHasErrors('token');
        $this->assertTrue(Hash::check('MatKhauMoi9', $this->user->refresh()->password));
    }

    public function test_lien_ket_qua_30_phut_bi_tu_choi(): void
    {
        $this->assertSame(30, (int) config('auth.passwords.users.expire'));
        $token = $this->requestToken();

        $this->travel(31)->minutes();

        $this->reset($token)->assertSessionHasErrors(['token' => 'Liên kết đặt lại mật khẩu không hợp lệ, đã được dùng hoặc đã quá 30 phút. Hãy yêu cầu liên kết mới.']);
        $this->assertTrue(Hash::check('MatKhauCu1', $this->user->refresh()->password));
    }

    public function test_mat_khau_moi_theo_chinh_sach_va_khong_trung_mat_khau_gan_day(): void
    {
        $token = $this->requestToken();

        $this->reset($token, 'yeu')->assertSessionHasErrors('password');
        $this->reset($token, 'MatKhauCu1')->assertSessionHasErrors('password');
        $this->reset($token, 'MatKhauMoi9')->assertSessionHasNoErrors();
    }

    public function test_sai_email_trong_lien_ket_va_tai_khoan_bi_khoa_khong_dat_lai_duoc(): void
    {
        $token = $this->requestToken();
        $this->reset($token, email: 'nguoi-khac@sv.test')->assertSessionHasErrors('token');

        app(UserService::class)->lock($this->user, 'Nghi bị chiếm', $this->userWithRoles('ADMIN'));
        $this->reset($token)->assertSessionHasErrors('token');

        // Tài khoản đang khóa thì không gửi liên kết mới (thông báo vẫn như cũ)
        $before = Notification::sent($this->user, ResetPasswordLink::class)->count();
        $this->post('/forgot-password', ['login' => 'SV01'])->assertSessionHas('status');
        $this->assertSame($before, Notification::sent($this->user, ResetPasswordLink::class)->count());
    }

    public function test_gioi_han_tan_suat_yeu_cau(): void
    {
        foreach (range(1, 5) as $ignored) {
            $this->postJson('/forgot-password', ['login' => 'ai-do'])->assertOk();
        }

        $this->postJson('/forgot-password', ['login' => 'ai-do'])->assertStatus(429);
    }

    public function test_trang_quen_mat_khau_noi_tu_trang_dang_nhap(): void
    {
        $this->get('/login')->assertOk()->assertSee(route('password.request'), false);
        $this->get('/forgot-password')->assertOk()->assertSee('Quên mật khẩu');
        $this->get('/reset-password/ma-thu?email=sv01@sv.test')->assertOk()->assertSee('ma-thu', false);

        // Đã đăng nhập thì không vào các trang này
        $this->actingAs($this->user)->get('/forgot-password')->assertRedirect();
    }
}
