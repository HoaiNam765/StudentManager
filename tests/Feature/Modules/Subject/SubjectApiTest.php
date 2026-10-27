<?php

namespace Tests\Feature\Modules\Subject;

use App\Models\User;
use App\Modules\Faculty\Models\Department;
use App\Modules\Faculty\Services\LeadershipService;
use App\Modules\Subject\Models\Subject;
use Database\Seeders\FacultySeeder;
use Database\Seeders\SubjectSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use OpenSpout\Reader\XLSX\Reader;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

class SubjectApiTest extends TestCase
{
    use DatabaseMigrations;
    use InteractsWithRoles;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
        $this->seed([FacultySeeder::class, SubjectSeeder::class]);
    }

    private function department(string $code): Department
    {
        return Department::query()->where('code', $code)->firstOrFail();
    }

    /** Số học phần mẫu của SubjectSeeder thỏa điều kiện (tuple [bộ môn, mã, tên, TC, TC thực hành, khối, tự chọn?]). */
    private function seeded(callable $filter): int
    {
        return count(array_filter(SubjectSeeder::SUBJECTS, $filter));
    }

    /** Trưởng bộ môn (vai trò DEAN có hiệu lực theo nhiệm kỳ, phạm vi FACULTY = bộ môn đó). */
    private function headOf(string $departmentCode): User
    {
        $user = User::factory()->create();

        app(LeadershipService::class)->assign([
            'unit_type' => 'department', 'unit_id' => $this->department($departmentCode)->id, 'user_id' => $user->id,
            'position' => 'head', 'starts_on' => now(config('studentmanager.display_timezone'))->toDateString(),
        ]);

        return $user;
    }

    public function test_seeder_tao_du_hoc_phan_mau_dung_quy_tac_va_chay_lap_lai_an_toan(): void
    {
        $this->seed(SubjectSeeder::class);

        $this->assertSame(count(SubjectSeeder::SUBJECTS), Subject::count());
        $this->assertTrue(Subject::all()->every(fn (Subject $s) => $s->theory_credits + $s->practice_credits === $s->credits));

        $pe = Subject::query()->where('code', 'GDTC101')->firstOrFail();
        $this->assertFalse($pe->counts_toward_gpa);
        $this->assertTrue($pe->is_graduation_requirement);
        $this->assertSame(0, Subject::query()->where('code', 'SHCD101')->value('credits'));
    }

    public function test_can_bo_dao_tao_tao_sua_ngung_va_xoa_hoc_phan(): void
    {
        $this->actingAs($this->userWithRoles('ACAD'));
        $department = $this->department('BM-KTPM');

        $id = $this->postJson('/admin/subjects', [
            'department_id' => $department->id, 'code' => ' int3101 ', 'name' => 'Kiến trúc phần mềm', 'name_en' => 'Software Architecture',
            'credits' => 3, 'practice_credits' => 1, 'knowledge_block' => 'specialized', 'requirement' => 'elective',
        ])
            ->assertCreated()
            ->assertJsonPath('code', 'INT3101')
            ->assertJsonPath('theory_credits', 2)
            ->assertJsonPath('practice_periods', 30)
            ->assertJsonPath('requirement_label', 'Tự chọn')
            ->assertJsonPath('knowledge_block_label', 'Chuyên ngành')
            ->assertJsonPath('faculty.code', 'CNTT')
            ->json('id');

        $this->postJson('/admin/subjects', [
            'department_id' => $department->id, 'code' => 'INT3102', 'name' => 'Sai tín chỉ',
            'credits' => 3, 'theory_credits' => 3, 'practice_credits' => 1, 'knowledge_block' => 'specialized',
        ])->assertStatus(422)->assertJsonPath('message', fn ($m) => str_contains($m, 'phải bằng tổng tín chỉ'));

        $this->postJson('/admin/subjects', ['department_id' => $department->id, 'code' => 'INT3101', 'name' => 'Trùng', 'credits' => 3, 'knowledge_block' => 'general'])
            ->assertStatus(422);

        $this->postJson('/admin/subjects', ['code' => 'X1', 'name' => 'Thiếu', 'credits' => 3, 'knowledge_block' => 'abc'])
            ->assertStatus(422)->assertJsonValidationErrors(['department_id', 'knowledge_block']);

        $this->putJson("/admin/subjects/{$id}", ['name' => 'Kiến trúc và thiết kế phần mềm', 'credits' => 4])
            ->assertOk()->assertJsonPath('credits', 4)->assertJsonPath('theory_credits', 3);

        $this->putJson("/admin/subjects/{$id}", ['status' => 'inactive'])->assertOk()->assertJsonPath('status_label', 'Ngừng hoạt động');

        $this->deleteJson("/admin/subjects/{$id}")->assertNoContent();
        $this->assertSoftDeleted('subjects', ['id' => $id]);
    }

    public function test_tim_khong_dau_va_loc_theo_khoa_khoi_kien_thuc_co_gpa(): void
    {
        $this->actingAs($this->userWithRoles('STU'));

        $this->getJson('/admin/subjects?q=cau truc du lieu')->assertOk()->assertJsonPath('total', 1)->assertJsonPath('data.0.code', 'INT201');
        $this->getJson('/admin/subjects?q=giao duc the chat')->assertOk()->assertJsonPath('total', 3);
        $this->getJson('/admin/subjects?counts_toward_gpa=0')->assertOk()->assertJsonPath('total', count(SubjectSeeder::CONDITION_SUBJECTS));
        $this->getJson('/admin/subjects?is_graduation_requirement=1')->assertOk()->assertJsonPath('total', count(SubjectSeeder::CONDITION_SUBJECTS));
        $this->getJson('/admin/subjects?knowledge_block=thesis')->assertOk()->assertJsonPath('total', $this->seeded(fn (array $s) => $s[5] === 'T'));
        $this->getJson('/admin/subjects?requirement=elective&faculty_id='.$this->department('BM-KTPM')->faculty_id)->assertOk()
            ->assertJsonPath('total', $this->seeded(fn (array $s) => $s[6] && in_array($s[0], ['BM-KTPM', 'BM-HTTT', 'BM-MMT'], true)));

        $id = Subject::query()->where('code', 'INT201')->value('id');
        $this->getJson("/admin/subjects/{$id}")->assertOk()->assertJsonPath('department.code', 'BM-KTPM')->assertJsonPath('is_pass_fail_only', false);
    }

    public function test_sinh_vien_va_can_bo_khac_chi_xem(): void
    {
        $id = Subject::query()->where('code', 'INT201')->value('id');

        foreach (['STU', 'EXAM', 'ADMIN'] as $role) {
            $this->actingAs($this->userWithRoles($role));

            $this->getJson('/admin/subjects')->assertOk()->assertJsonPath('total', count(SubjectSeeder::SUBJECTS));
            $this->postJson('/admin/subjects', [])->assertForbidden();
            $this->putJson("/admin/subjects/{$id}", ['name' => 'X'])->assertForbidden();
            $this->deleteJson("/admin/subjects/{$id}")->assertForbidden();
        }
    }

    public function test_giang_vien_chua_duoc_phan_cong_lop_thi_khong_thay_hoc_phan_nao(): void
    {
        $this->actingAs($this->userWithRoles('LEC'));

        $this->getJson('/admin/subjects')->assertOk()->assertJsonPath('total', 0);
        $this->getJson('/admin/subjects/'.Subject::query()->value('id'))->assertForbidden();
    }

    public function test_truong_bo_mon_chi_quan_ly_hoc_phan_cua_bo_mon_minh(): void
    {
        $this->actingAs($this->headOf('BM-KTPM'));
        $own = Subject::query()->where('code', 'INT301')->firstOrFail();
        $other = Subject::query()->where('code', 'INT203')->firstOrFail(); // BM-HTTT

        $this->getJson('/admin/subjects')->assertOk()->assertJsonPath('total', $this->seeded(fn (array $s) => $s[0] === 'BM-KTPM'));
        $this->getJson("/admin/subjects/{$other->id}")->assertForbidden();

        $this->putJson("/admin/subjects/{$own->id}", ['description' => 'Cập nhật bởi trưởng bộ môn'])->assertOk();
        $this->putJson("/admin/subjects/{$other->id}", ['description' => 'X'])->assertForbidden();

        // Không chuyển học phần sang bộ môn ngoài phạm vi, không tạo học phần cho bộ môn khác
        $this->putJson("/admin/subjects/{$own->id}", ['department_id' => $other->department_id])->assertForbidden();
        $this->postJson('/admin/subjects', [
            'department_id' => $other->department_id, 'code' => 'INT3201', 'name' => 'Ngoài phạm vi', 'credits' => 3, 'knowledge_block' => 'specialized',
        ])->assertForbidden();

        $this->postJson('/admin/subjects', [
            'department_id' => $own->department_id, 'code' => 'INT3202', 'name' => 'Mẫu thiết kế', 'credits' => 3, 'knowledge_block' => 'specialized',
        ])->assertCreated();

        // DEAN không có quyền xóa học phần theo ma trận (CRUA*)
        $this->deleteJson("/admin/subjects/{$own->id}")->assertForbidden();
    }

    public function test_xuat_excel_theo_bo_loc_va_chi_nguoi_co_quyen_xuat(): void
    {
        $this->actingAs($this->userWithRoles('STU'));
        $this->get('/admin/subjects/export')->assertForbidden();

        $this->actingAs($this->userWithRoles('ACAD'));
        $response = $this->get('/admin/subjects/export?knowledge_block=thesis');
        $response->assertOk();

        $reader = new Reader;
        $reader->open($response->getFile()->getPathname());
        $rows = [];

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $rows[] = array_map(fn ($cell) => $cell->getValue(), $row->getCells());
            }
        }

        $reader->close();

        $this->assertSame('Mã học phần', $rows[0][0]);
        $this->assertSame(['ACC402', 'BUS402', 'CHN402', 'ENG402', 'INT402'], array_column(array_slice($rows, 1), 0));
        $this->assertSame('Đồ án / khóa luận', $rows[1][12]);
    }
}
