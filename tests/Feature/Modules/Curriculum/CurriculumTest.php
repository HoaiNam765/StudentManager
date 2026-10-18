<?php

namespace Tests\Feature\Modules\Curriculum;

use App\Models\User;
use App\Modules\Curriculum\Enums\CurriculumStatus;
use App\Modules\Curriculum\Models\Curriculum;
use App\Modules\Curriculum\Models\CurriculumBlock;
use App\Modules\Curriculum\Services\CurriculumCatalog;
use App\Modules\Curriculum\Services\CurriculumService;
use App\Modules\Faculty\Models\Faculty;
use App\Modules\Faculty\Models\Major;
use App\Modules\Faculty\Models\TrainingType;
use App\Modules\Faculty\Services\LeadershipService;
use App\Modules\Subject\Models\Subject;
use App\Modules\Subject\Services\SubjectService;
use App\Support\Exceptions\BusinessRuleException;
use Database\Seeders\CurriculumSeeder;
use Database\Seeders\FacultySeeder;
use Database\Seeders\SubjectRelationSeeder;
use Database\Seeders\SubjectSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

class CurriculumTest extends TestCase
{
    use DatabaseMigrations;
    use InteractsWithRoles;

    private CurriculumService $curricula;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
        $this->seed([FacultySeeder::class, SubjectSeeder::class, SubjectRelationSeeder::class]);
        $this->curricula = app(CurriculumService::class);
    }

    private function subject(string $code): Subject
    {
        return Subject::query()->where('code', $code)->firstOrFail();
    }

    private function major(string $code = '7480201'): Major
    {
        return Major::query()->where('code', $code)->firstOrFail();
    }

    /** CTĐT nháp nhỏ: một khối "Cơ sở ngành". */
    private function draft(int $total = 10, int $blockRequired = 10, array $data = []): array
    {
        $curriculum = $this->curricula->create($data + [
            'code' => 'CTDT-TEST',
            'name' => 'CTĐT thử nghiệm',
            'major_id' => $this->major()->id,
            'training_type_id' => TrainingType::default()->id,
            'cohort_from' => 2027,
            'total_credits' => $total,
        ]);

        $block = $this->curricula->addBlock($curriculum, ['name' => 'Cơ sở ngành', 'block_type' => 'foundation', 'required_credits' => $blockRequired]);

        return [$curriculum, $block];
    }

    private function add(Curriculum $curriculum, CurriculumBlock $block, string $code, int $term, array $extra = []): array
    {
        return $this->curricula->addSubject($curriculum, $extra + [
            'subject_id' => $this->subject($code)->id,
            'curriculum_block_id' => $block->id,
            'planned_term' => $term,
        ]);
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

    public function test_seeder_moi_nganh_mot_ctdt_da_ban_hanh_va_chay_lap_lai_an_toan(): void
    {
        $this->seed(CurriculumSeeder::class);
        $this->seed(CurriculumSeeder::class);

        $this->assertSame(6, Curriculum::query()->where('status', 'published')->count());
        $this->assertSame(6, Curriculum::query()->distinct()->count('major_id'));
    }

    public function test_cac_khoi_cong_lai_thieu_tin_chi_thi_khong_ban_hanh_duoc_va_neu_ro_thieu_bao_nhieu(): void
    {
        // Tiêu chí nghiệm thu 6.18 (thu nhỏ): CTĐT yêu cầu 10 tín chỉ nhưng các khối cộng lại chỉ 8
        [$curriculum, $block] = $this->draft(total: 10, blockRequired: 8);
        $this->add($curriculum, $block, 'INT101', 1);   // 3
        $this->add($curriculum, $block, 'INT206', 2);   // 3
        $this->add($curriculum, $block, 'TOAN104', 2);  // 3 -> 9 tín chỉ

        $e = $this->rejected(fn () => $this->curricula->publish($curriculum));
        $this->assertStringContainsString('các khối cộng lại chỉ yêu cầu 8/10 tín chỉ, thiếu 2 tín chỉ', $e->getMessage());
        $this->assertStringContainsString('mới đủ 9/10 tín chỉ, thiếu 1 tín chỉ', $e->getMessage());

        $this->curricula->updateBlock($block, ['required_credits' => 10]);
        $e = $this->rejected(fn () => $this->curricula->publish($curriculum->refresh()));
        $this->assertStringContainsString('khối "Cơ sở ngành" mới có 9/10 tín chỉ, thiếu 1', $e->getMessage());
        $this->assertSame(CurriculumStatus::Draft, $curriculum->refresh()->status);
    }

    public function test_hoc_phan_dieu_kien_khong_cong_vao_tong_tin_chi(): void
    {
        [$curriculum, $block] = $this->draft(total: 3, blockRequired: 3);
        $this->add($curriculum, $block, 'INT101', 1);
        $this->add($curriculum, $block, 'GDQP101', 1);   // 3 tín chỉ nhưng không tính GPA

        $published = $this->curricula->publish($curriculum);
        $this->assertSame(CurriculumStatus::Published, $published->status);
    }

    public function test_moi_hoc_phan_mot_lan_va_canh_bao_tien_quyet(): void
    {
        [$curriculum, $block] = $this->draft(total: 6, blockRequired: 6);

        // INT102 có tiên quyết INT101 chưa có trong CTĐT: lưu được nhưng có cảnh báo
        $result = $this->add($curriculum, $block, 'INT102', 2);
        $this->assertSame(['INT102 (HK2): học phần tiên quyết INT101 không thuộc CTĐT'], $result['warnings']);

        // INT101 đặt cùng học kỳ: vẫn cảnh báo vì phải sớm hơn
        $first = $this->add($curriculum, $block, 'INT101', 2);
        $this->assertSame(['INT102 (HK2): học phần tiên quyết INT101 đang ở HK2, phải sớm hơn'], $first['warnings']);

        $e = $this->rejected(fn () => $this->add($curriculum, $block, 'INT101', 1));
        $this->assertStringContainsString('đã có trong CTĐT', $e->getMessage());

        $e = $this->rejected(fn () => $this->curricula->publish($curriculum));
        $this->assertStringContainsString('phải sớm hơn', $e->getMessage());

        $fixed = $this->curricula->updateSubject($first['item'], ['planned_term' => 1]);
        $this->assertSame([], $fixed['warnings']);
        $this->assertSame(CurriculumStatus::Published, $this->curricula->publish($curriculum)->status);
    }

    public function test_tien_quyet_ngoai_ctdt_duoc_xac_nhan_thi_het_canh_bao(): void
    {
        [$curriculum, $block] = $this->draft(total: 3, blockRequired: 3);

        $result = $this->add($curriculum, $block, 'INT102', 2, ['outside_prerequisites_confirmed' => true]);
        $this->assertSame([], $result['warnings']);
        $this->curricula->publish($curriculum);
    }

    public function test_nhom_tu_chon_cung_khoi_va_du_hoc_phan_de_chon(): void
    {
        [$curriculum, $block] = $this->draft(total: 9, blockRequired: 9);
        $other = $this->curricula->addBlock($curriculum, ['name' => 'Chuyên ngành', 'block_type' => 'specialized', 'required_credits' => 0]);
        $group = $this->curricula->addGroup($curriculum, ['curriculum_block_id' => $block->id, 'name' => 'Tự chọn', 'min_credits' => 6]);

        $this->rejected(fn () => $this->add($curriculum, $other, 'INT206', 3, ['curriculum_elective_group_id' => $group->id]));

        $this->add($curriculum, $block, 'INT101', 1);
        $this->add($curriculum, $block, 'INT206', 3, ['curriculum_elective_group_id' => $group->id]);

        $e = $this->rejected(fn () => $this->curricula->publish($curriculum));
        $this->assertStringContainsString('yêu cầu chọn 6 tín chỉ nhưng chỉ có 3', $e->getMessage());

        $this->add($curriculum, $block, 'TOAN104', 2, ['curriculum_elective_group_id' => $group->id]);
        $this->curricula->publish($curriculum);
    }

    public function test_thoi_gian_chuan_khop_voi_nganh_va_hoc_ky_du_kien_trong_pham_vi(): void
    {
        $e = $this->rejected(fn () => $this->draft(data: ['standard_terms' => 9]));
        $this->assertStringContainsString('khác với ngành 7480201 (8 học kỳ)', $e->getMessage());

        [$curriculum, $block] = $this->draft();
        $this->rejected(fn () => $this->add($curriculum, $block, 'INT101', 9));
    }

    public function test_ctdt_da_ban_hanh_khong_sua_truc_tiep_va_khoa_hoc_phan(): void
    {
        [$curriculum, $block] = $this->draft(total: 3, blockRequired: 3);
        $this->add($curriculum, $block, 'INT101', 1);

        // Nháp chưa khóa học phần
        app(SubjectService::class)->update($this->subject('INT101'), ['name' => 'Nhập môn lập trình (sửa)']);
        $this->curricula->publish($curriculum);

        $e = $this->rejected(fn () => $this->curricula->update($curriculum->refresh(), ['name' => 'Tên mới']));
        $this->assertStringContainsString('phiên bản mới', $e->hint());
        $this->rejected(fn () => $this->add($curriculum, $block, 'INT206', 1));
        $this->rejected(fn () => $this->curricula->delete($curriculum));

        // Học phần nằm trong CTĐT đã ban hành: không đổi tín chỉ, không ngừng (BR-SUB-05, BR-CUR-07)
        $e = $this->rejected(fn () => app(SubjectService::class)->update($this->subject('INT101'), ['credits' => 4]));
        $this->assertStringContainsString('CTĐT đã ban hành', $e->getMessage());
        $this->rejected(fn () => app(SubjectService::class)->update($this->subject('INT101'), ['status' => 'inactive']));
    }

    public function test_hoc_phan_thuoc_ctdt_dang_ap_dung_moi_mo_lop_duoc(): void
    {
        [$curriculum, $block] = $this->draft(total: 3, blockRequired: 3);
        $this->add($curriculum, $block, 'INT101', 1);
        $catalog = app(CurriculumCatalog::class);

        $this->assertFalse($catalog->isInForce($this->subject('INT101')));

        $this->curricula->publish($curriculum);

        $this->assertTrue($catalog->isInForce($this->subject('INT101')));
        $this->assertFalse($catalog->isInForce($this->subject('INT206')));
        $this->assertSame([$this->subject('INT101')->id], $catalog->subjectIdsInForce()->all());
        $this->assertSame($curriculum->id, $catalog->forStudent($this->major()->id, 2027, TrainingType::default()->id)?->id);
        $this->assertNull($catalog->forStudent($this->major()->id, 2026, TrainingType::default()->id));
    }

    public function test_khong_ban_hanh_hai_ctdt_cung_nganh_cung_khoa(): void
    {
        [$first, $block] = $this->draft(total: 3, blockRequired: 3);
        $this->add($first, $block, 'INT101', 1);
        $this->curricula->publish($first);

        [$second, $block2] = $this->draft(total: 3, blockRequired: 3, data: ['code' => 'CTDT-TEST-2', 'cohort_from' => 2028]);
        $this->add($second, $block2, 'INT101', 1);

        $e = $this->rejected(fn () => $this->curricula->publish($second));
        $this->assertStringContainsString('CTDT-TEST đang áp dụng', $e->getMessage());

        // Giới hạn khóa của CTĐT cũ thì ban hành được
        Curriculum::query()->whereKey($first->id)->update(['cohort_to' => 2027]);
        $this->curricula->publish($second);
    }

    public function test_api_can_bo_dao_tao_xay_dung_va_ban_hanh(): void
    {
        $this->actingAs($this->userWithRoles('ACAD'));

        $id = $this->postJson('/admin/curricula', [
            'code' => 'ctdt-api', 'name' => 'CTĐT qua API', 'major_id' => $this->major()->id,
            'training_type_id' => TrainingType::default()->id, 'cohort_from' => 2027, 'total_credits' => 6,
        ])->assertCreated()->assertJsonPath('code', 'CTDT-API')->assertJsonPath('standard_terms', 8)->assertJsonPath('status', 'draft')->json('id');

        $blockId = $this->postJson("/admin/curricula/{$id}/blocks", ['name' => 'Cơ sở ngành', 'block_type' => 'foundation', 'required_credits' => 6])->assertCreated()->json('id');

        $this->postJson("/admin/curricula/{$id}/subjects", ['subject_id' => $this->subject('INT102')->id, 'curriculum_block_id' => $blockId, 'planned_term' => 2])
            ->assertCreated()
            ->assertJsonPath('warnings.0', 'INT102 (HK2): học phần tiên quyết INT101 không thuộc CTĐT');

        $this->getJson("/admin/curricula/{$id}/check")->assertOk()->assertJsonPath('publishable', false);
        $this->postJson("/admin/curricula/{$id}/publish")->assertStatus(422);

        $this->postJson("/admin/curricula/{$id}/subjects", ['subject_id' => $this->subject('INT101')->id, 'curriculum_block_id' => $blockId, 'planned_term' => 1])
            ->assertCreated()->assertJsonPath('warnings', []);

        $this->getJson("/admin/curricula/{$id}")->assertOk()->assertJsonPath('summary.provided', 6)->assertJsonPath('subjects.0.subject.code', 'INT101');
        $this->postJson("/admin/curricula/{$id}/publish")->assertOk()->assertJsonPath('status_label', 'Đã ban hành');
    }

    public function test_api_lanh_dao_khoa_chi_xay_dung_ctdt_nganh_cua_khoa_minh_va_khong_ban_hanh(): void
    {
        $dean = User::factory()->create();
        app(LeadershipService::class)->assign([
            'unit_type' => 'faculty', 'unit_id' => Faculty::query()->where('code', 'CNTT')->value('id'), 'user_id' => $dean->id,
            'position' => 'dean', 'starts_on' => now(config('studentmanager.display_timezone'))->toDateString(),
        ]);
        $this->actingAs($dean);

        $payload = ['name' => 'CTĐT', 'training_type_id' => TrainingType::default()->id, 'cohort_from' => 2027, 'total_credits' => 3];
        $id = $this->postJson('/admin/curricula', $payload + ['code' => 'CTDT-CNTT', 'major_id' => $this->major('7480201')->id])->assertCreated()->json('id');
        $this->postJson('/admin/curricula', $payload + ['code' => 'CTDT-QTKD', 'major_id' => $this->major('7340101')->id])->assertForbidden();

        $this->postJson("/admin/curricula/{$id}/publish")->assertForbidden();

        $this->actingAs($this->userWithRoles('ACAD'));
        $other = $this->postJson('/admin/curricula', $payload + ['code' => 'CTDT-QTKD', 'major_id' => $this->major('7340101')->id])->json('id');

        $this->actingAs($dean);
        $this->getJson('/admin/curricula')->assertOk()->assertJsonPath('total', 1);
        $this->getJson("/admin/curricula/{$other}")->assertForbidden();
    }

    public function test_api_giang_vien_xem_sinh_vien_chua_co_ctdt_thi_khong_thay(): void
    {
        $this->seed(CurriculumSeeder::class);

        $this->actingAs($this->userWithRoles('LEC'));
        $this->getJson('/admin/curricula')->assertOk()->assertJsonPath('total', 6);
        $this->postJson('/admin/curricula', [])->assertForbidden();

        // STU: phạm vi OWN, chờ STU gán CTĐT cho sinh viên
        $this->actingAs($this->userWithRoles('STU'));
        $this->getJson('/admin/curricula')->assertOk()->assertJsonPath('total', 0);
    }
}
