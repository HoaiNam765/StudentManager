<?php

namespace Tests\Feature\Modules\Subject;

use App\Models\User;
use App\Modules\Faculty\Models\Department;
use App\Modules\Faculty\Services\LeadershipService;
use App\Modules\Subject\Enums\RelationType;
use App\Modules\Subject\Models\Subject;
use App\Modules\Subject\Models\SubjectRelation;
use App\Modules\Subject\Services\SubjectRelationService;
use App\Support\Exceptions\BusinessRuleException;
use Carbon\CarbonImmutable;
use Database\Seeders\FacultySeeder;
use Database\Seeders\SubjectRelationSeeder;
use Database\Seeders\SubjectSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

class SubjectRelationTest extends TestCase
{
    use DatabaseMigrations;
    use InteractsWithRoles;

    private SubjectRelationService $relations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
        $this->seed([FacultySeeder::class, SubjectSeeder::class]);
        $this->relations = app(SubjectRelationService::class);
    }

    private function subject(string $code): Subject
    {
        return Subject::query()->where('code', $code)->firstOrFail();
    }

    /** "$related là $type của $code". */
    private function relate(string $code, string $related, RelationType $type = RelationType::Prerequisite, array $data = []): SubjectRelation
    {
        return $this->relations->add($this->subject($code), $this->subject($related), $type, $data);
    }

    private function day(int $offset = 0): string
    {
        return CarbonImmutable::now(config('studentmanager.display_timezone'))->addDays($offset)->toDateString();
    }

    private function rejected(callable $callback): BusinessRuleException
    {
        try {
            $callback();
        } catch (BusinessRuleException $e) {
            $this->assertNotEmpty($e->hint(), 'Thông báo từ chối phải kèm cách khắc phục (GC-04).');

            return $e;
        }

        $this->fail('Thao tác lẽ ra bị từ chối.');
    }

    public function test_chan_vong_lap_truc_tiep_va_tu_tham_chieu(): void
    {
        $this->relate('INT102', 'INT101');

        // Tiêu chí nghiệm thu 6.5: A tiên quyết của B, đặt B tiên quyết của A thì bị chặn
        $e = $this->rejected(fn () => $this->relate('INT101', 'INT102'));
        $this->assertStringContainsString('vòng lặp INT101 → INT102 → INT101', $e->getMessage());

        $this->rejected(fn () => $this->relate('INT101', 'INT101'));
        $this->rejected(fn () => $this->relate('INT101', 'INT101', RelationType::Corequisite));
    }

    public function test_chan_vong_lap_gian_tiep_ke_ca_tron_tien_quyet_va_hoc_truoc(): void
    {
        $this->relate('INT102', 'INT101');
        $this->relate('INT201', 'INT102', RelationType::Prior);
        $this->relate('INT306', 'INT201');

        $e = $this->rejected(fn () => $this->relate('INT101', 'INT306', RelationType::Prior));
        $this->assertStringContainsString('INT101 → INT306 → INT201 → INT102 → INT101', $e->getMessage());
        $this->assertSame(3, SubjectRelation::count());
    }

    public function test_song_hanh_hai_chieu_hop_le_nhung_khong_duoc_mau_thuan_voi_tien_quyet(): void
    {
        // Học cùng kỳ: hai học phần song hành với nhau không mâu thuẫn
        $this->relate('INT201', 'TOAN104', RelationType::Corequisite);
        $this->relate('TOAN104', 'INT201', RelationType::Corequisite);

        // TOAN104 song hành (học trước hoặc cùng kỳ) với INT201 mà INT201 lại phải xong trước TOAN104: không thỏa được
        $this->relate('INT306', 'INT201');
        $this->rejected(fn () => $this->relate('TOAN104', 'INT306'));

        // Giữa hai học phần chỉ một quan hệ thứ tự
        $e = $this->rejected(fn () => $this->relate('INT201', 'TOAN104', RelationType::Prior));
        $this->assertStringContainsString('đã là học phần Song hành', $e->getMessage());
    }

    public function test_tap_tien_quyet_day_du_va_dieu_kien_truc_tiep_cho_dang_ky(): void
    {
        $this->seed(SubjectRelationSeeder::class);
        $this->seed(SubjectRelationSeeder::class);

        $this->assertSame(count(SubjectRelationSeeder::RELATIONS), SubjectRelation::count());

        // INT307 ← INT306 ← INT201 ← INT102 ← INT101, và INT307 ← TOAN103 (học trước); song hành không tính
        $all = $this->relations->allPrerequisites($this->subject('INT307'))->pluck('code')->all();
        $this->assertSame(['INT101', 'INT102', 'INT201', 'INT306', 'TOAN103'], $all);

        $direct = $this->relations->requirementsOf($this->subject('INT201'));
        $this->assertSame(['INT102'], $direct['prerequisite']->pluck('code')->all());
        $this->assertSame(['TOAN104'], $direct['corequisite']->pluck('code')->all());
        $this->assertSame([], $direct['prior']->all());
    }

    public function test_tuong_duong_co_hieu_luc_theo_ngay_va_dung_hai_chieu(): void
    {
        $relation = $this->relate('BUS202', 'ENG303', RelationType::Equivalent, ['effective_from' => $this->day(-10), 'decision_no' => '15/QĐ-KT']);

        $this->assertSame(['ENG303'], $this->relations->equivalentsOf($this->subject('BUS202'))->pluck('code')->all());
        $this->assertSame(['BUS202'], $this->relations->equivalentsOf($this->subject('ENG303'))->pluck('code')->all());
        $this->assertSame([], $this->relations->equivalentsOf($this->subject('BUS202'), $this->day(-11))->all());

        // Trùng thời gian (kể cả chiều ngược) bị chặn; đã hiệu lực thì không xóa, chỉ kết thúc
        $this->rejected(fn () => $this->relate('ENG303', 'BUS202', RelationType::Equivalent));
        $this->rejected(fn () => $this->relations->remove($relation));

        $this->relations->end($relation, $this->day(-1));
        $this->assertSame([], $this->relations->equivalentsOf($this->subject('BUS202'))->all());

        $this->relate('ENG303', 'BUS202', RelationType::Equivalent, ['effective_from' => $this->day(5)]);
        $this->assertSame(['BUS202'], $this->relations->equivalentsOf($this->subject('ENG303'), $this->day(6))->pluck('code')->all());
    }

    public function test_hoc_phan_ngung_hoat_dong_khong_them_quan_he_duoc(): void
    {
        $this->subject('INT101')->deactivate();

        $this->rejected(fn () => $this->relate('INT102', 'INT101'));
    }

    public function test_api_can_bo_dao_tao_them_xoa_quan_he_va_ghi_nhat_ky(): void
    {
        $this->actingAs($this->userWithRoles('ACAD'));
        $b = $this->subject('INT102');
        $a = $this->subject('INT101');

        $id = $this->postJson("/admin/subjects/{$b->id}/relations", ['related_subject_id' => $a->id, 'type' => 'prerequisite'])
            ->assertCreated()
            ->assertJsonPath('type_label', 'Tiên quyết')
            ->assertJsonPath('subject.code', 'INT101')
            ->json('id');

        $this->postJson("/admin/subjects/{$a->id}/relations", ['related_subject_id' => $b->id, 'type' => 'prior'])
            ->assertStatus(422)
            ->assertJsonPath('message', fn ($m) => str_contains($m, 'vòng lặp'));

        $this->getJson("/admin/subjects/{$b->id}/relations")->assertOk()->assertJsonPath('requires.0.subject.code', 'INT101');
        $this->getJson("/admin/subjects/{$a->id}/relations")->assertOk()->assertJsonPath('required_by.0.subject.code', 'INT102');
        $this->getJson("/admin/subjects/{$b->id}/prerequisites")->assertOk()->assertJsonPath('0.code', 'INT101');

        $this->deleteJson("/admin/subject-relations/{$id}")->assertNoContent();
        $this->assertDatabaseHas('audit_logs', ['auditable_type' => (new SubjectRelation)->getMorphClass(), 'auditable_id' => $id, 'event' => 'deleted']);
    }

    public function test_api_sinh_vien_chi_xem_truong_bo_mon_chi_sua_hoc_phan_cua_bo_mon_minh(): void
    {
        $own = $this->subject('INT303');     // BM-KTPM
        $other = $this->subject('INT305');   // BM-HTTT
        $sql = $this->subject('INT203');

        $this->actingAs($this->userWithRoles('STU'));
        $this->getJson("/admin/subjects/{$own->id}/relations")->assertOk();
        $this->postJson("/admin/subjects/{$own->id}/relations", ['related_subject_id' => $sql->id, 'type' => 'prerequisite'])->assertForbidden();

        $head = User::factory()->create();
        app(LeadershipService::class)->assign([
            'unit_type' => 'department', 'unit_id' => Department::query()->where('code', 'BM-KTPM')->value('id'),
            'user_id' => $head->id, 'position' => 'head', 'starts_on' => $this->day(),
        ]);

        $this->actingAs($head);
        $this->postJson("/admin/subjects/{$own->id}/relations", ['related_subject_id' => $sql->id, 'type' => 'prerequisite'])->assertCreated();
        $this->postJson("/admin/subjects/{$other->id}/relations", ['related_subject_id' => $sql->id, 'type' => 'prerequisite'])->assertForbidden();
    }
}
