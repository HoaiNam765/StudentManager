<?php

namespace Tests\Feature\Modules\System;

use App\Models\User;
use App\Modules\System\Models\PolicySet;
use App\Modules\System\Notifications\ImportantConfigurationChanged;
use App\Modules\System\Services\PolicyResolver;
use App\Modules\System\Services\PolicySetService;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLog;
use App\Support\Exceptions\BusinessRuleException;
use Database\Seeders\PolicySeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

class PolicySetTest extends TestCase
{
    use DatabaseMigrations;
    use InteractsWithRoles;

    private PolicySetService $policies;

    private User $actor;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        $this->policies = app(PolicySetService::class);
        $this->actor = User::factory()->create();
    }

    private function day(int $offset): string
    {
        return now(config('studentmanager.display_timezone'))->addDays($offset)->toDateString();
    }

    private function resolver(): PolicyResolver
    {
        // Mỗi lần tra cứu dùng resolver mới để không dính bộ nhớ đệm của lần trước
        return new PolicyResolver;
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

    public function test_bo_quy_che_moi_cho_k2026_tu_ngay_d_thi_k2025_van_tinh_theo_bo_cu(): void
    {
        $this->seed(PolicySeeder::class);
        $d = $this->day(10);

        $draft = $this->policies->createDraft([
            'code' => 'QC-K2026', 'name' => 'Quy chế áp dụng từ K2026', 'cohort_from' => 2026, 'effective_from' => $d,
        ], PolicySet::query()->where('code', PolicySeeder::CODE)->first());
        $this->policies->setItems($draft, ['attendance.ban_percent' => 25, 'graduation.min_cgpa' => 2.2]);
        $this->policies->publish($draft, $this->actor);

        $after = $this->day(11);
        $before = $this->day(9);

        // Sau ngày D: K2026 theo bộ mới, K2025 vẫn theo bộ cũ
        $this->assertSame(25, $this->resolver()->value('attendance.ban_percent', 2026, $after));
        $this->assertEquals(2.2, $this->resolver()->value('graduation.min_cgpa', 2026, $after));
        $this->assertSame(20, $this->resolver()->value('attendance.ban_percent', 2025, $after));
        $this->assertSame(PolicySeeder::CODE, $this->resolver()->setFor(2025, $after)->code);

        // Trước ngày D: K2026 cũng vẫn theo bộ cũ (không hồi tố)
        $this->assertSame(20, $this->resolver()->value('attendance.ban_percent', 2026, $before));
        $this->assertSame('QC-K2026', $this->resolver()->setFor(2027, $d)->code);

        // Tham số không sửa vẫn sao chép đúng từ bộ cũ
        $this->assertSame(10, $this->resolver()->value('attendance.warning_percent', 2026, $after));
        $this->assertSame('A', $this->resolver()->value('grading.scale', 2026, $after)[0]['letter']);
    }

    public function test_bo_da_ban_hanh_khong_sua_khong_xoa_duoc_va_phien_ban_moi_sao_chep_tu_bo_cu(): void
    {
        $this->seed(PolicySeeder::class);
        $published = PolicySet::query()->where('code', PolicySeeder::CODE)->firstOrFail();

        $e = $this->rejected(fn () => $this->policies->setItems($published, ['attendance.ban_percent' => 30]));
        $this->assertStringContainsString('đã ban hành', $e->getMessage());
        $this->assertStringContainsString('phiên bản mới', $e->hint());
        $this->rejected(fn () => $this->policies->update($published, ['name' => 'Đổi tên']));
        $this->rejected(fn () => $this->policies->delete($published));

        $v2 = $this->policies->createDraft(['code' => PolicySeeder::CODE, 'name' => 'Bản sửa', 'effective_from' => $this->day(1)], $published);

        $this->assertSame(2, $v2->version);
        $expected = $published->values();
        $actual = $v2->values();
        ksort($expected);
        ksort($actual);
        $this->assertSame($expected, $actual);

        // Bản nháp xóa được
        $this->policies->delete($v2);
        $this->assertSoftDeleted($v2);
    }

    public function test_khong_ban_hanh_hoi_to(): void
    {
        $draft = $this->policies->createDraft(['code' => 'QC-CU', 'name' => 'Lùi ngày', 'effective_from' => $this->day(-1)]);

        $e = $this->rejected(fn () => $this->policies->publish($draft, $this->actor));
        $this->assertStringContainsString('đã qua', $e->getMessage());
        $this->assertStringContainsString('hồi tố', $e->hint());

        // Hiệu lực từ hôm nay thì được
        $this->policies->update($draft, ['effective_from' => $this->day(0)]);
        $this->assertTrue($this->policies->publish($draft->refresh(), $this->actor)->status->value === 'published');
    }

    public function test_tham_so_sai_bi_tu_choi_theo_tung_tham_so_va_rang_buoc_giua_cac_tham_so(): void
    {
        $draft = $this->policies->createDraft(['code' => 'QC-THU', 'name' => 'Thử', 'effective_from' => $this->day(1)]);

        try {
            $this->policies->setItems($draft, [
                'attendance.ban_percent' => 'abc',
                'grading.scale' => [['min' => 4, 'letter' => 'D', 'gpa' => 1, 'passed' => true], ['min' => 5, 'letter' => 'C', 'gpa' => 2, 'passed' => true]],
                'khong.co' => 1,
            ]);
            $this->fail('Giá trị sai phải bị từ chối.');
        } catch (ValidationException $exception) {
            $errors = $exception->errors();
            $this->assertArrayHasKey('items.attendance.ban_percent', $errors);
            $this->assertStringContainsString('giảm dần', $errors['items.grading.scale'][0]);
            $this->assertStringContainsString('Không có tham số', $errors['items.khong.co'][0]);
        }

        // Từng tham số hợp lệ nhưng mâu thuẫn nhau thì bị chặn lúc ban hành
        $this->policies->setItems($draft, ['attendance.warning_percent' => 30, 'attendance.ban_percent' => 20, 'grading.pass_score' => 5]);
        $e = $this->rejected(fn () => $this->policies->publish($draft->refresh(), $this->actor));
        $this->assertStringContainsString('Ngưỡng cảnh báo vắng phải nhỏ hơn ngưỡng cấm thi', $e->getMessage());
        $this->assertStringContainsString('Điểm đạt học phần (5)', $e->getMessage());
    }

    public function test_hai_bo_cung_pham_vi_cung_ngay_hieu_luc_bi_tu_choi(): void
    {
        $first = $this->policies->createDraft(['code' => 'QC-A', 'name' => 'A', 'cohort_from' => 2026, 'effective_from' => $this->day(5)]);
        $this->policies->publish($first, $this->actor);

        $second = $this->policies->createDraft(['code' => 'QC-B', 'name' => 'B', 'cohort_from' => 2026, 'effective_from' => $this->day(5)]);
        $this->rejected(fn () => $this->policies->publish($second, $this->actor));

        // Khóa bắt đầu lớn hơn khóa kết thúc
        $this->rejected(fn () => $this->policies->createDraft(['code' => 'QC-C', 'name' => 'C', 'cohort_from' => 2027, 'cohort_to' => 2026, 'effective_from' => $this->day(5)]));
    }

    public function test_chua_co_bo_quy_che_thi_bao_ro_cach_khac_phuc(): void
    {
        $e = $this->rejected(fn () => $this->resolver()->value('attendance.ban_percent', 2026));
        $this->assertStringContainsString('Chưa có bộ quy chế đào tạo áp dụng cho khóa K2026', $e->getMessage());

        $this->seed(PolicySeeder::class);
        $this->rejected(fn () => $this->resolver()->value('khong.co', 2026));
    }

    public function test_ban_hanh_ghi_nhat_ky_truoc_sau_va_bao_cac_admin_khac(): void
    {
        $this->seedRoles();
        $this->actor = $this->userWithRoles('ADMIN');
        $otherAdmin = $this->userWithRoles('ADMIN');
        $acad = $this->userWithRoles('ACAD');
        $this->actingAs($this->actor);

        $draft = $this->policies->createDraft(['code' => 'QC-MOI', 'name' => 'Mới', 'cohort_from' => 2026, 'effective_from' => $this->day(3)]);
        $this->policies->publish($draft, $this->actor);

        Notification::assertSentTo($otherAdmin, ImportantConfigurationChanged::class, fn ($n) => str_contains($n->summary, 'QC-MOI v1'));
        Notification::assertNotSentTo([$this->actor, $acad], ImportantConfigurationChanged::class);

        $log = AuditLog::query()->where('event', AuditEvent::Approved)->latest('id')->firstOrFail();
        $this->assertSame($this->actor->id, $log->user_id);
        $this->assertSame('K2026 trở đi', $log->new_values['cohorts']);
        $this->assertSame(20, $log->new_values['values']['attendance.ban_percent']);
    }
}
