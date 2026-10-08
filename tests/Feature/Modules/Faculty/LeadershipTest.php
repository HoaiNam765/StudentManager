<?php

namespace Tests\Feature\Modules\Faculty;

use App\Models\User;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Faculty\Models\Department;
use App\Modules\Faculty\Models\Faculty;
use App\Modules\Faculty\Models\LeadershipTerm;
use App\Modules\Faculty\Services\FacultyAccess;
use App\Modules\Faculty\Services\LeadershipService;
use App\Support\Exceptions\BusinessRuleException;
use Carbon\CarbonImmutable;
use Database\Seeders\FacultySeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

class LeadershipTest extends TestCase
{
    use DatabaseMigrations;
    use InteractsWithRoles;

    private LeadershipService $leadership;

    private Faculty $cntt;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
        $this->seed(FacultySeeder::class);
        $this->leadership = app(LeadershipService::class);
        $this->cntt = Faculty::query()->where('code', 'CNTT')->firstOrFail();
    }

    private function day(int $offset): string
    {
        return CarbonImmutable::now(config('studentmanager.display_timezone'))->addDays($offset)->toDateString();
    }

    private function assignDean(User $user, string $from, ?string $to = null, bool $replace = false): LeadershipTerm
    {
        return $this->leadership->assign([
            'unit_type' => 'faculty', 'unit_id' => $this->cntt->id, 'user_id' => $user->id,
            'position' => 'dean', 'starts_on' => $from, 'ends_on' => $to, 'replace_current' => $replace,
        ]);
    }

    /** Quyền và phạm vi được nhớ trong một yêu cầu; sau khi đổi ngày phải tính lại. */
    private function fresh(): void
    {
        app(AccessControl::class)->flush();
        app(FacultyAccess::class)->flush();
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

    public function test_truong_khoa_moi_co_hieu_luc_tu_ngay_d_nguoi_cu_mat_quyen_lich_su_van_day_du(): void
    {
        $old = User::factory()->create(['name' => 'Trưởng khoa cũ']);
        $new = User::factory()->create(['name' => 'Trưởng khoa mới']);
        $d = $this->day(10);

        $this->assignDean($old, $this->day(-365));
        $this->assignDean($new, $d, replace: true);

        // Trước ngày D: người cũ có DEAN và phạm vi khoa, người mới chưa có
        $this->assertTrue($old->hasRole('DEAN'));
        $this->assertSame([$this->cntt->id], app(FacultyAccess::class)->facultyIds($old));
        $this->assertFalse($new->hasRole('DEAN'));
        $this->assertSame([], app(FacultyAccess::class)->facultyIds($new));

        // Đến ngày D
        $this->travelTo(CarbonImmutable::parse($d.' 08:00', config('studentmanager.display_timezone')));
        $this->fresh();

        $this->assertFalse($old->hasRole('DEAN'), 'Quyền DEAN của người cũ bị thu hồi từ ngày D');
        $this->assertTrue($new->hasRole('DEAN'));
        $this->assertSame([], app(FacultyAccess::class)->facultyIds($old));
        $this->assertSame([$this->cntt->id], app(FacultyAccess::class)->facultyIds($new));

        // Người mới thấy và cập nhật được thông tin khoa mình, không đổi được mã
        $this->actingAs($new);
        $this->getJson('/admin/faculties')->assertOk()->assertJsonPath('total', 1)->assertJsonPath('data.0.code', 'CNTT');
        $this->getJson('/admin/departments')->assertOk()->assertJsonPath('total', 3);
        $this->putJson("/admin/faculties/{$this->cntt->id}", ['description' => 'Giới thiệu khoa'])->assertOk();
        $this->putJson("/admin/faculties/{$this->cntt->id}", ['code' => 'IT'])->assertForbidden();
        $other = Faculty::query()->where('code', 'KT')->firstOrFail();
        $this->getJson("/admin/faculties/{$other->id}")->assertForbidden();

        // Lịch sử nhiệm kỳ đầy đủ: người cũ kết thúc ngày D - 1
        $history = LeadershipTerm::query()->forUnit('faculty', $this->cntt->id)->orderBy('starts_on')->get();
        $this->assertCount(2, $history);
        $this->assertSame(CarbonImmutable::parse($d)->subDay()->toDateString(), $history[0]->ends_on->toDateString());
        $this->assertNull($history[1]->ends_on);
    }

    public function test_moi_don_vi_toi_da_mot_truong_tai_mot_thoi_diem_pho_thi_nhieu_nguoi(): void
    {
        $a = User::factory()->create(['name' => 'Nguyễn Văn A']);
        $b = User::factory()->create();
        $this->assignDean($a, $this->day(-10));

        $e = $this->rejected(fn () => $this->assignDean($b, $this->day(5)));
        $this->assertStringContainsString('đã có Trưởng khoa là Nguyễn Văn A', $e->getMessage());
        $this->assertStringContainsString('BR-FAC-04', $e->hint());

        // Hai phó trưởng khoa cùng lúc được
        foreach ([$b, User::factory()->create()] as $vice) {
            $this->leadership->assign(['unit_type' => 'faculty', 'unit_id' => $this->cntt->id, 'user_id' => $vice->id, 'position' => 'vice_dean', 'starts_on' => $this->day(0)]);
        }

        $this->assertSame(2, LeadershipTerm::query()->where('position', 'vice_dean')->count());

        // Chức vụ không khớp loại đơn vị, ngày kết thúc trước ngày bắt đầu
        $this->rejected(fn () => $this->leadership->assign(['unit_type' => 'faculty', 'unit_id' => $this->cntt->id, 'user_id' => $b->id, 'position' => 'head', 'starts_on' => $this->day(0)]));
        $this->rejected(fn () => $this->assignDean($b, $this->day(5), $this->day(1)));

        // Đơn vị đã ngừng không giao chức vụ được
        $department = Department::query()->where('code', 'BM-MMT')->firstOrFail();
        $department->deactivate();
        $this->rejected(fn () => $this->leadership->assign(['unit_type' => 'department', 'unit_id' => $department->id, 'user_id' => $b->id, 'position' => 'head', 'starts_on' => $this->day(0)]));
    }

    public function test_mot_nguoi_giu_hai_chuc_vu_ket_thuc_mot_chuc_van_con_dean_dung_pham_vi(): void
    {
        $person = User::factory()->create();
        $deanTerm = $this->assignDean($person, $this->day(-30));
        $accounting = Department::query()->where('code', 'BM-KTKT')->firstOrFail();
        $this->leadership->assign(['unit_type' => 'department', 'unit_id' => $accounting->id, 'user_id' => $person->id, 'position' => 'head', 'starts_on' => $this->day(-5)]);

        $access = app(FacultyAccess::class);
        $this->assertSame([$this->cntt->id], $access->facultyIds($person));
        $this->assertCount(4, $access->departmentIds($person)); // 3 bộ môn của khoa CNTT + bộ môn Kế toán – Kiểm toán

        // Kết thúc chức trưởng khoa từ hôm qua: vẫn là trưởng bộ môn nên vẫn có DEAN, nhưng chỉ còn bộ môn đó
        $this->leadership->end($deanTerm, $this->day(-1));
        $this->fresh();

        $this->assertTrue($person->hasRole('DEAN'));
        $this->assertSame([], $access->facultyIds($person));
        $this->assertSame([$accounting->id], $access->departmentIds($person));
    }

    public function test_chi_huy_duoc_nhiem_ky_chua_bat_dau(): void
    {
        $person = User::factory()->create();
        $future = $this->assignDean($person, $this->day(3));

        $this->leadership->cancel($future);
        $this->assertSoftDeleted($future);

        $this->travelTo(CarbonImmutable::parse($this->day(4).' 08:00', config('studentmanager.display_timezone')));
        $this->fresh();
        $this->assertFalse($person->hasRole('DEAN'), 'Nhiệm kỳ đã hủy không còn cấp DEAN');
        $this->travelBack();

        $started = $this->assignDean($person, $this->day(0));
        $e = $this->rejected(fn () => $this->leadership->cancel($started));
        $this->assertStringContainsString('đã bắt đầu', $e->getMessage());
    }

    public function test_lenh_dong_bo_sua_dong_vai_tro_bi_lech_va_ghi_nhat_ky_nhiem_ky_bat_dau_hom_nay(): void
    {
        $tampered = $this->assignDean(User::factory()->create(), $this->day(-20));
        DB::table('user_roles')->where('id', $tampered->user_role_id)->update(['valid_to' => $this->day(-10)]);

        $department = Department::query()->where('code', 'BM-KTPM')->firstOrFail();
        $this->leadership->assign(['unit_type' => 'department', 'unit_id' => $department->id, 'user_id' => User::factory()->create()->id, 'position' => 'head', 'starts_on' => $this->day(0)]);

        $this->artisan('faculty:sync-leadership')
            ->expectsOutputToContain('sửa 1 dòng vai trò, 1 nhiệm kỳ bắt đầu hôm nay')
            ->assertSuccessful();

        $this->assertNull(DB::table('user_roles')->where('id', $tampered->user_role_id)->value('valid_to'));
        $this->artisan('schedule:list')->expectsOutputToContain('faculty:sync-leadership')->assertSuccessful();
    }

    public function test_api_giao_nhiem_ky_can_quyen_toan_truong_va_xem_lich_su(): void
    {
        $person = User::factory()->create();

        $this->actingAs($this->userWithRoles('DEAN'));
        $this->postJson('/admin/leadership-terms', ['unit_type' => 'faculty', 'unit_id' => $this->cntt->id, 'user_id' => $person->id, 'position' => 'dean', 'starts_on' => $this->day(0)])
            ->assertForbidden();

        $this->actingAs($this->userWithRoles('ACAD'));
        $id = $this->postJson('/admin/leadership-terms', ['unit_type' => 'faculty', 'unit_id' => $this->cntt->id, 'user_id' => $person->id, 'position' => 'dean', 'starts_on' => $this->day(0)])
            ->assertCreated()
            ->assertJsonPath('position_label', 'Trưởng khoa')
            ->assertJsonPath('unit.code', 'CNTT')
            ->json('id');

        $this->postJson("/admin/leadership-terms/{$id}/end", ['ends_on' => $this->day(30)])->assertOk()->assertJsonPath('ends_on', $this->day(30));
        $this->getJson("/admin/leadership-terms?unit_type=faculty&unit_id={$this->cntt->id}&current=1")->assertOk()->assertJsonPath('total', 1);

        $this->actingAs($this->userWithRoles('LEC'));
        $this->getJson('/admin/leadership-terms')->assertOk();
        $this->deleteJson("/admin/leadership-terms/{$id}")->assertForbidden();
    }
}
