<?php

namespace Tests\Feature\Modules\System;

use App\Models\User;
use App\Modules\System\Services\AuditLogSearch;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLog;
use App\Support\Audit\AuditLogger;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

class AuditLogSearchTest extends TestCase
{
    use InteractsWithRoles;
    use RefreshDatabase;

    private AuditLogSearch $search;

    protected function setUp(): void
    {
        parent::setUp();

        $this->search = new AuditLogSearch;
    }

    /** Ghi một dòng nhật ký tại thời điểm UTC cho trước. */
    private function logAt(string $utc, AuditEvent $event, ?User $actor = null, ?User $subject = null, array $new = []): AuditLog
    {
        $this->travelTo(CarbonImmutable::parse($utc, 'UTC'));
        $actor === null ? Auth::logout() : $this->actingAs($actor);

        return app(AuditLogger::class)->record($event, $subject, [], $new);
    }

    public function test_loc_theo_nguoi_hanh_dong_va_doi_tuong(): void
    {
        [$admin, $other, $target] = User::factory()->count(3)->create();

        $a = $this->logAt('2026-10-01 01:00:00', AuditEvent::Updated, $admin, $target);
        $b = $this->logAt('2026-10-01 02:00:00', AuditEvent::Login, $admin);
        $c = $this->logAt('2026-10-01 03:00:00', AuditEvent::Updated, $other, $admin);

        $ids = fn(array $filters) => $this->search->query($filters)->pluck('id')->all();

        $this->assertSame([$b->id, $a->id], $ids(['user_id' => $admin->id]), 'Theo người, mới nhất trước');
        $this->assertSame([$c->id, $a->id], $ids(['event' => AuditEvent::Updated]));
        $this->assertSame([$c->id, $a->id], $ids(['event' => 'updated']), 'Nhận cả chuỗi');
        $this->assertSame([$a->id], $ids(['auditable_type' => User::class, 'auditable_id' => $target->id]));
        $this->assertSame([$c->id, $b->id, $a->id], $ids([]), 'Không lọc thì lấy tất cả');
    }

    public function test_loc_khoang_ngay_theo_gio_viet_nam(): void
    {
        // 16:59 UTC ngày 04/10 = 23:59 ngày 04/10 giờ Việt Nam; 18:00 UTC ngày 04/10 = 01:00 ngày 05/10
        $late = $this->logAt('2026-10-04 16:59:00', AuditEvent::Login);
        $nextDay = $this->logAt('2026-10-04 18:00:00', AuditEvent::Login);

        $ids = fn(array $filters) => $this->search->query($filters)->pluck('id')->all();

        $this->assertSame([$late->id], $ids(['from' => '2026-10-04', 'to' => '2026-10-04']));
        $this->assertSame([$nextDay->id], $ids(['from' => '2026-10-05', 'to' => '2026-10-05']));
        $this->assertSame([$nextDay->id], $ids(['from' => '2026-10-05']));
        $this->assertSame([$late->id], $ids(['to' => '2026-10-04']));
    }

    public function test_du_lieu_xuat_file_co_tieu_de_va_dinh_dang_viet_nam(): void
    {
        $admin = User::factory()->create(['name' => 'Quản trị viên']);
        $target = User::factory()->create();
        $this->logAt('2026-10-04 18:00:00', AuditEvent::Updated, $admin, $target, ['name' => 'Đặng Hoài Nam']);

        $rows = iterator_to_array($this->search->exportRows($this->search->query()), false);

        $this->assertCount(2, $rows);
        $this->assertSame('Thời điểm', $rows[0][0]);
        $this->assertSame(
            ['05/10/2026 01:00', 'Quản trị viên', 'Cập nhật', 'User', (string) $target->id, '', '{"name":"Đặng Hoài Nam"}'],
            array_slice($rows[1], 0, 7)
        );
    }

    public function test_chi_admin_xem_va_xuat_duoc_khong_ai_sua_xoa_duoc_nhat_ky(): void
    {
        $this->seedRoles();
        $admin = $this->userWithRoles('ADMIN');
        $acad = $this->userWithRoles('ACAD');

        $log = $this->logAt(
            '2026-10-04 18:00:00',
            AuditEvent::Login,
            $acad
        );

        $this->travelTo(CarbonImmutable::parse('2026-10-06 12:00:00', 'UTC'));

        $this->assertTrue(Gate::forUser($admin)->allows('viewAny', AuditLog::class));

        $this->assertTrue(Gate::forUser($admin)->allows('view', $log));
        $this->assertTrue(Gate::forUser($admin)->allows('export', AuditLog::class));

        foreach ([$acad, User::factory()->create()] as $user) {
            $this->assertTrue(Gate::forUser($user)->denies('viewAny', AuditLog::class), 'Chỉ ADMIN xem nhật ký (FR-SYS-004)');
            $this->assertTrue(Gate::forUser($user)->denies('export', AuditLog::class));
        }

        foreach (['update', 'delete', 'restore', 'forceDelete'] as $ability) {
            $this->assertTrue(Gate::forUser($admin)->denies($ability, $log), "Kể cả ADMIN cũng không được {$ability} nhật ký");
        }
    }
}
