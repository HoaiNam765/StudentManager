<?php

namespace Tests\Feature\Modules\Auth;

use App\Models\User;
use App\Modules\Auth\Enums\AccountStatus;
use App\Modules\Auth\Enums\ProfileType;
use App\Modules\Auth\Enums\StudentAccountEvent;
use App\Modules\Auth\Notifications\TemporaryPasswordIssued;
use App\Modules\Auth\Services\LoginService;
use App\Modules\Auth\Services\UserService;
use App\Modules\System\Services\ImportService;
use App\Support\Exceptions\BusinessRuleException;
use Database\Seeders\DevRoleUsersSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use DatabaseMigrations;
    use InteractsWithRoles;

    private UserService $users;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        $this->seedRoles();
        $this->users = app(UserService::class);
    }

    private function student(string $username = 'SV2026001'): User
    {
        return $this->users->create(['username' => $username, 'name' => 'Nguyễn Văn An', 'email' => strtolower($username).'@sv.test', 'profile_type' => 'student']);
    }

    /** Mật khẩu tạm gửi trong email gần nhất cho $user. */
    private function temporaryPasswordOf(User $user): string
    {
        $sent = Notification::sent($user, TemporaryPasswordIssued::class);

        return $sent->last()->temporaryPassword;
    }

    private function loginError(string $login, string $password): ?string
    {
        try {
            app(LoginService::class)->attempt($login, $password);
            Auth::logout();

            return null;
        } catch (ValidationException $exception) {
            return $exception->errors()['login'][0];
        }
    }

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

    public function test_tao_tai_khoan_gan_vai_tro_mac_dinh_mat_khau_tam_buoc_doi_va_gui_email(): void
    {
        $student = $this->student();

        $this->assertSame(['STU'], $student->activeRoles()->pluck('roles.code')->all());
        $this->assertSame(ProfileType::Student, $student->profile_type);
        $this->assertTrue($student->must_change_password);

        $temporary = $this->temporaryPasswordOf($student);
        $this->assertMatchesRegularExpression('/[A-Z]/', $temporary);
        $this->assertMatchesRegularExpression('/[a-z]/', $temporary);
        $this->assertMatchesRegularExpression('/\d/', $temporary);
        $this->assertNull($this->loginError('SV2026001', $temporary), 'Đăng nhập được bằng mật khẩu tạm (rồi bị buộc đổi)');

        $lecturer = $this->users->create(['username' => 'GV001', 'name' => 'Trần Thị Bình', 'email' => 'gv001@truong.test', 'profile_type' => 'teacher', 'roles' => ['ADV']]);
        $this->assertEqualsCanonicalizing(['ADV', 'LEC'], $lecturer->activeRoles()->pluck('roles.code')->all());
    }

    public function test_ten_dang_nhap_duy_nhat_khong_tai_su_dung_va_moi_ho_so_mot_tai_khoan(): void
    {
        $admin = $this->userWithRoles('ADMIN');
        $student = $this->student();

        $this->rejected(fn () => $this->student());
        $this->users->deactivate($student, 'Thôi học', $admin);
        $e = $this->rejected(fn () => $this->student());
        $this->assertStringContainsString('không được tái sử dụng', $e->hint());

        $this->rejected(fn () => $this->users->create(['username' => 'CB01', 'name' => 'Cán bộ', 'email' => 'cb01@truong.test', 'profile_type' => 'staff']));
        $this->rejected(fn () => $this->users->create(['username' => 'CB01', 'name' => 'Cán bộ', 'email' => 'cb01@truong.test', 'profile_type' => 'staff', 'roles' => ['KHONG_CO']]));

        // Một hồ sơ chỉ gắn một tài khoản; tài khoản không đổi loại hồ sơ
        $other = $this->student('SV2026002');
        $this->users->linkProfile($other, ProfileType::Student, 501);
        $this->rejected(fn () => $this->users->linkProfile($this->student('SV2026003'), ProfileType::Student, 501));
        $this->rejected(fn () => $this->users->linkProfile($other, ProfileType::Teacher, 9));
    }

    public function test_khoa_ngung_chan_dang_nhap_huy_phien_va_khong_lam_mat_admin_cuoi(): void
    {
        $admin = $this->userWithRoles('ADMIN');
        $student = $this->student();
        $student->forceFill(['password' => 'MatKhau123', 'must_change_password' => false])->save();
        config(['session.driver' => 'database']); // như môi trường thật; phpunit.xml dùng driver array
        DB::table('sessions')->insert(['id' => 'phien-1', 'user_id' => $student->id, 'payload' => '', 'last_activity' => time()]);

        $this->users->lock($student, 'Nghi bị chiếm tài khoản', $admin);
        $this->assertSame(0, DB::table('sessions')->where('user_id', $student->id)->count(), 'Khóa thì hủy mọi phiên');
        $this->assertStringContainsString('đã bị khóa', $this->loginError('SV2026001', 'MatKhau123'));
        $this->assertStringContainsString('Tên đăng nhập hoặc mật khẩu không đúng', $this->loginError('SV2026001', 'sai'), 'Sai mật khẩu thì vẫn không lộ trạng thái');

        $this->users->unlock($student->refresh(), $admin);
        $this->assertNull($this->loginError('SV2026001', 'MatKhau123'));

        $this->users->deactivate($student->refresh(), 'Nghỉ', $admin);
        $this->assertStringContainsString('ngừng hoạt động', $this->loginError('SV2026001', 'MatKhau123'));

        // BR-AUTH-05 và không tự khóa mình
        $this->rejected(fn () => $this->users->lock($admin, 'Thử', $admin));
        $other = $this->userWithRoles('ADMIN');
        $this->users->lock($other, 'Bàn giao', $admin);
        $this->rejected(fn () => $this->users->lock($admin, 'Admin cuối', $this->userWithRoles('ACAD')));
    }

    public function test_sinh_vien_thoi_hoc_ngung_sau_thoi_gian_cau_hinh_tot_nghiep_chuyen_chi_doc(): void
    {
        config(['studentmanager.auth.student_deactivate_after_days' => 30]);
        $dropped = $this->student('SV1');
        $graduate = $this->student('SV2');

        $this->users->applyStudentStatus($dropped, StudentAccountEvent::Dropped);
        $this->assertTrue($dropped->refresh()->canSignIn(), 'Trong thời gian chờ vẫn đăng nhập được');
        $this->assertEqualsWithDelta(now()->addDays(30)->timestamp, $dropped->deactivate_at->timestamp, 5);

        $this->travel(31)->days();
        $this->assertFalse($dropped->refresh()->canSignIn(), 'Quá hạn thì chặn ngay cả khi lệnh chưa chạy');
        $this->artisan('users:apply-deactivations')->expectsOutputToContain('Đã ngừng 1 tài khoản')->assertSuccessful();
        $this->assertSame(AccountStatus::Inactive, $dropped->refresh()->status);
        $this->travelBack();

        // Tốt nghiệp: đăng nhập được, chỉ đọc
        $this->users->applyStudentStatus($graduate, StudentAccountEvent::Graduated);
        $graduate->refresh()->forceFill(['must_change_password' => false])->save();
        $this->assertTrue($graduate->canSignIn());
        $this->assertTrue($graduate->isReadOnly());

        Route::middleware(['web', 'auth'])->post('/_thu-ghi', fn () => 'ok');
        Route::middleware(['web', 'auth'])->get('/_thu-doc', fn () => 'ok');

        $this->actingAs($graduate);
        $this->getJson('/_thu-doc')->assertOk();
        $this->postJson('/_thu-ghi')->assertForbidden()->assertJsonPath('message', fn ($m) => str_contains($m, 'chỉ đọc'));
        $this->postJson('/logout')->assertOk();

        $this->rejected(fn () => $this->users->applyStudentStatus($this->userWithRoles('LEC'), StudentAccountEvent::Graduated));
    }

    public function test_tao_tai_khoan_hang_loat_qua_trung_tam_import_va_hoan_tac(): void
    {
        Storage::fake('local');
        $admin = $this->userWithRoles('ADMIN');
        $this->student('SV9');
        $imports = app(ImportService::class);

        $csv = "username,name,email,profile_type,roles\n"
            ."SV10,Lê Văn C,sv10@sv.test,student,\n"
            ."GV10,Phạm Thị D,gv10@truong.test,teacher,ADV\n"
            ."SV9,Trùng,trung@sv.test,student,\n"
            ."CB10,Cán bộ,cb10@truong.test,staff,\n";

        $batch = $imports->upload('users', UploadedFile::fake()->createWithContent('tai-khoan.csv', $csv), $admin);
        $batch = $imports->validate($batch, $admin);

        $this->assertSame(2, $batch->valid_rows);
        $this->assertSame(['username', 'roles'], $batch->invalidRows()->orderBy('row_number')->get()->map(fn ($row) => $row->errors[0]['column'])->all());

        $imports->save($batch, 'all_valid', $admin);
        $created = User::query()->whereIn('username', ['SV10', 'GV10'])->get();
        $this->assertCount(2, $created);
        Notification::assertSentTo($created, TemporaryPasswordIssued::class);

        $imports->rollback($batch->refresh(), $admin);
        $this->assertSame(0, User::query()->whereIn('username', ['SV10', 'GV10'])->count());
    }

    public function test_api_quan_ly_nguoi_dung_chi_admin(): void
    {
        $this->actingAs($this->userWithRoles('ACAD'));
        $this->getJson('/admin/users')->assertForbidden();
        $this->postJson('/admin/users', [])->assertForbidden();

        $this->actingAs($this->userWithRoles('ADMIN'));
        $id = $this->postJson('/admin/users', ['username' => 'CB20', 'name' => 'Cán bộ Hai', 'email' => 'cb20@truong.test', 'profile_type' => 'staff', 'roles' => ['FIN']])
            ->assertCreated()
            ->assertJsonPath('roles', ['FIN'])
            ->assertJsonMissingPath('password')
            ->json('id');

        $this->putJson("/admin/users/{$id}", ['username' => 'CB21'])->assertStatus(422)->assertJsonValidationErrors('username');
        $this->getJson('/admin/users?role=FIN')->assertOk()->assertJsonPath('total', 1);
        $this->postJson("/admin/users/{$id}/lock", ['reason' => 'Nghỉ phép dài'])->assertOk()->assertJsonPath('status', 'locked');
        $this->postJson("/admin/users/{$id}/unlock")->assertOk()->assertJsonPath('status', 'active');
        $this->postJson("/admin/users/{$id}/temporary-password")->assertOk()->assertJsonMissingPath('password');
        $this->postJson("/admin/users/{$id}/deactivate", ['reason' => 'Nghỉ việc'])->assertOk()->assertJsonPath('status_label', 'Ngừng hoạt động');
    }

    public function test_seeder_tai_khoan_mau_moi_vai_tro(): void
    {
        $this->seed(DevRoleUsersSeeder::class);
        $this->seed(DevRoleUsersSeeder::class);

        foreach (array_keys(DevRoleUsersSeeder::ACCOUNTS) as $code) {
            $this->assertTrue(User::query()->where('username', strtolower($code).'.test')->firstOrFail()->hasRole($code), $code);
        }
    }
}
