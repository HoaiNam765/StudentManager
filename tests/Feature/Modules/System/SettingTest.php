<?php

namespace Tests\Feature\Modules\System;

use App\Modules\System\Notifications\ImportantConfigurationChanged;
use App\Modules\System\Services\SettingService;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLog;
use Database\Seeders\PolicySeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use DatabaseMigrations;
    use InteractsWithRoles;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        $this->seedRoles();
    }

    public function test_tham_so_chua_luu_dung_mac_dinh_luu_roi_ghi_nhat_ky_truoc_sau_va_ap_dung_vao_config(): void
    {
        $admin = $this->userWithRoles('ADMIN');
        $settings = app(SettingService::class);
        $defaultLifetime = (int) config('session.lifetime');

        $this->assertSame($defaultLifetime, $settings->get('session.idle_minutes'));
        $this->assertTrue($settings->all()['session.idle_minutes']['is_default']);

        $changed = $settings->set(['session.idle_minutes' => '45', 'school.name' => $settings->get('school.name')], $admin, 'Siết thời gian phiên');

        // school.name không đổi giá trị nên không lưu, không ghi nhật ký
        $this->assertSame(['session.idle_minutes'], $changed);
        $this->assertSame(45, $settings->get('session.idle_minutes'));
        $this->assertSame(45, config('session.lifetime'));

        $log = AuditLog::query()->where('event', AuditEvent::Updated)->latest('id')->firstOrFail();
        $this->assertSame(['session.idle_minutes' => $defaultLifetime], $log->old_values);
        $this->assertSame(['session.idle_minutes' => 45], $log->new_values);
        $this->assertSame('Siết thời gian phiên', $log->reason);

        // Định dạng ngày và múi giờ cũng ghi đè config dùng chung
        $settings->set(['locale.date_format' => 'Y-m-d', 'locale.timezone' => 'Asia/Bangkok'], $admin);
        $this->assertSame('Y-m-d H:i', config('studentmanager.formats.datetime'));
        $this->assertSame('Asia/Bangkok', config('studentmanager.display_timezone'));
    }

    public function test_thay_doi_quan_trong_bao_cac_admin_khac_thay_doi_thuong_thi_khong(): void
    {
        $admin = $this->userWithRoles('ADMIN');
        $otherAdmin = $this->userWithRoles('ADMIN');
        $settings = app(SettingService::class);

        $settings->set(['school.name' => 'Trường Đại học Thử nghiệm'], $admin);
        Notification::assertNothingSent();

        $settings->set(['session.remember_days' => 7], $admin);
        Notification::assertSentTo($otherAdmin, ImportantConfigurationChanged::class, fn ($n) => str_contains($n->lines[0], '→ 7'));
        Notification::assertNotSentTo($admin, ImportantConfigurationChanged::class);
    }

    public function test_api_tham_so_kiem_tra_tung_gia_tri_va_phan_quyen(): void
    {
        $this->actingAs($this->userWithRoles('ADMIN'));

        $this->getJson('/admin/settings')->assertOk()->assertJsonFragment(['key' => 'locale.timezone', 'is_default' => true]);

        $this->putJson('/admin/settings', ['values' => ['locale.timezone' => 'Sao/Hoa', 'session.idle_minutes' => 1, 'khong.co' => 1]])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['values.khong.co']);

        $this->putJson('/admin/settings', ['values' => ['locale.timezone' => 'Sao/Hoa', 'session.idle_minutes' => 1]])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['values.locale.timezone', 'values.session.idle_minutes']);

        $this->putJson('/admin/settings', ['values' => ['school.email' => 'daotao@truong.edu.vn', 'school.phone' => '']])
            ->assertOk()
            ->assertJsonPath('changed', ['school.email']);

        $this->actingAs($this->userWithRoles('STU'));
        $this->getJson('/admin/settings')->assertForbidden();
        $this->putJson('/admin/settings', ['values' => ['school.name' => 'X']])->assertForbidden();
    }

    public function test_tai_logo_luu_disk_public_xoa_logo_cu_va_tu_choi_svg(): void
    {
        Storage::fake('public');
        $this->actingAs($this->userWithRoles('ADMIN'));

        $first = $this->postJson('/admin/settings/logo', ['logo' => UploadedFile::fake()->image('logo.png', 200, 200)])->assertOk()->json('path');
        Storage::disk('public')->assertExists($first);

        $second = $this->postJson('/admin/settings/logo', ['logo' => UploadedFile::fake()->image('logo2.jpg', 200, 200)])->assertOk()->json('path');
        Storage::disk('public')->assertExists($second);
        Storage::disk('public')->assertMissing($first);
        $this->assertSame($second, app(SettingService::class)->get('school.logo_path'));

        $svg = UploadedFile::fake()->createWithContent('logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>');
        $this->postJson('/admin/settings/logo', ['logo' => $svg])->assertStatus(422)->assertJsonValidationErrors('logo');
    }

    public function test_api_bo_quy_che_soan_sua_ban_hanh_va_tra_cuu_theo_khoa(): void
    {
        $this->seed(PolicySeeder::class);
        $this->actingAs($this->userWithRoles('ACAD')); // FR-SYS-003: phòng Đào tạo soạn và ban hành quy chế

        $this->getJson('/admin/policy-sets/definitions')->assertOk()->assertJsonFragment(['key' => 'attendance.ban_percent', 'default' => 20]);

        $effective = now(config('studentmanager.display_timezone'))->addDays(2)->toDateString();
        $created = $this->postJson('/admin/policy-sets', ['code' => 'qc-k2026', 'name' => 'K2026', 'cohort_from' => 2026, 'effective_from' => $effective])
            ->assertCreated()
            ->assertJsonPath('code', 'QC-K2026')
            ->assertJsonPath('status', 'draft');
        // Khóa tham số có dấu chấm nên đọc mảng trực tiếp thay cho assertJsonPath
        $this->assertSame(20, $created->json('items')['attendance.ban_percent']);
        $id = $created->json('id');

        $this->putJson("/admin/policy-sets/{$id}/items", ['items' => ['attendance.ban_percent' => 'x']])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['items.attendance.ban_percent']);

        $this->putJson("/admin/policy-sets/{$id}/items", ['items' => ['attendance.ban_percent' => 25]])->assertOk();
        $this->postJson("/admin/policy-sets/{$id}/publish")->assertOk()->assertJsonPath('status', 'published');
        $this->putJson("/admin/policy-sets/{$id}", ['name' => 'Sửa'])->assertStatus(422);

        $resolved = $this->getJson("/admin/policy-sets/resolve?cohort=2026&date={$effective}")
            ->assertOk()
            ->assertJsonPath('policy_set.code', 'QC-K2026');
        $this->assertSame(25, $resolved->json('values')['attendance.ban_percent']);
        $this->getJson("/admin/policy-sets/resolve?cohort=2025&date={$effective}")
            ->assertOk()
            ->assertJsonPath('policy_set.code', PolicySeeder::CODE);

        $this->actingAs($this->userWithRoles('LEC'));
        $this->getJson('/admin/policy-sets')->assertForbidden();
    }
}
