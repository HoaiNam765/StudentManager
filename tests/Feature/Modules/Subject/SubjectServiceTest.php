<?php

namespace Tests\Feature\Modules\Subject;

use App\Modules\Faculty\Models\Department;
use App\Modules\Faculty\Services\FacultyService;
use App\Modules\Subject\Enums\KnowledgeBlock;
use App\Modules\Subject\Models\Subject;
use App\Modules\Subject\Services\SubjectService;
use App\Support\Exceptions\BusinessRuleException;
use App\Support\References\ReferenceRegistry;
use Database\Seeders\FacultySeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SubjectServiceTest extends TestCase
{
    use DatabaseMigrations;

    private SubjectService $subjects;

    private Department $department;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(FacultySeeder::class);
        $this->subjects = app(SubjectService::class);
        $this->department = Department::query()->where('code', 'BM-KTPM')->firstOrFail();
    }

    /** @param  array<string, mixed>  $data */
    private function subject(array $data = []): Subject
    {
        return $this->subjects->create($data + [
            'department_id' => $this->department->id,
            'code' => 'INT1001',
            'name' => 'Lập trình cơ bản',
            'credits' => 3,
            'knowledge_block' => 'foundation',
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

    /** Tạo bảng giả lập "lớp học phần" tham chiếu tới học phần (CLS chưa có). */
    private function referencedBySections(Subject $subject, bool $openSection = false): void
    {
        if (! Schema::hasTable('test_course_sections')) {
            Schema::create('test_course_sections', function (Blueprint $table) {
                $table->id();
                $table->foreignId('subject_id');
                $table->string('status')->default('closed');
            });

            app(ReferenceRegistry::class)->register(Subject::class, 'test_course_sections', 'subject_id', 'lớp học phần',
                active: fn ($q) => $q->where('status', 'open'));
        }

        DB::table('test_course_sections')->insert(['subject_id' => $subject->id, 'status' => $openSection ? 'open' : 'closed']);
    }

    public function test_bo_trong_phan_tin_chi_thi_tinh_tu_tong_va_dien_so_tiet_theo_dinh_muc(): void
    {
        $theory = $this->subject();

        $this->assertSame([3, 3, 0], [$theory->credits, $theory->theory_credits, $theory->practice_credits]);
        $this->assertSame([45, 0, 90], [$theory->theory_periods, $theory->practice_periods, $theory->self_study_hours]);
        $this->assertSame(KnowledgeBlock::Foundation, $theory->knowledge_block);
        $this->assertTrue($theory->counts_toward_gpa);
        $this->assertFalse($theory->is_graduation_requirement);
        $this->assertSame('compulsory', $theory->refresh()->requirement->value);

        $mixed = $this->subject(['code' => 'INT1002', 'credits' => 4, 'practice_credits' => 1]);
        $this->assertSame([3, 1, 45, 30], [$mixed->theory_credits, $mixed->practice_credits, $mixed->theory_periods, $mixed->practice_periods]);

        // Nhập tay số tiết thì giữ nguyên
        $custom = $this->subject(['code' => 'INT1003', 'credits' => 2, 'theory_credits' => 1, 'theory_periods' => 20, 'practice_periods' => 40]);
        $this->assertSame([1, 1, 20, 40], [$custom->theory_credits, $custom->practice_credits, $custom->theory_periods, $custom->practice_periods]);
    }

    public function test_ly_thuyet_cong_thuc_hanh_phai_bang_tong_tin_chi(): void
    {
        $e = $this->rejected(fn () => $this->subject(['credits' => 3, 'theory_credits' => 2, 'practice_credits' => 2]));
        $this->assertStringContainsString('phải bằng tổng tín chỉ (3)', $e->getMessage());

        $this->rejected(fn () => $this->subject(['credits' => 2, 'practice_credits' => 3]));
        $this->assertSame(0, Subject::count());
    }

    public function test_chi_hoc_phan_dieu_kien_moi_duoc_0_tin_chi(): void
    {
        $e = $this->rejected(fn () => $this->subject(['credits' => 0]));
        $this->assertStringContainsString('học phần điều kiện', $e->getMessage());

        $condition = $this->subject([
            'code' => 'SHCD1', 'credits' => 0, 'counts_toward_gpa' => false, 'is_graduation_requirement' => true,
            'knowledge_block' => 'general', 'theory_periods' => 30,
        ]);

        $this->assertSame(0, $condition->credits);
        $this->assertSame(30, $condition->theory_periods);
        $this->assertTrue($condition->isPassFailOnly());
    }

    public function test_tin_chi_vuot_gioi_han_cau_hinh_bi_tu_choi(): void
    {
        config(['studentmanager.subject.max_credits' => 10]);

        $e = $this->rejected(fn () => $this->subject(['credits' => 12, 'practice_credits' => 12]));
        $this->assertStringContainsString('từ 0 đến 10', $e->getMessage());
    }

    public function test_khong_co_tin_chi_thuc_hanh_thi_khong_co_tiet_thuc_hanh(): void
    {
        $e = $this->rejected(fn () => $this->subject(['credits' => 3, 'practice_periods' => 30]));
        $this->assertStringContainsString('không có tín chỉ thực hành', $e->getMessage());

        $subject = $this->subject(['credits' => 3, 'practice_credits' => 1]);
        $this->rejected(fn () => $this->subjects->update($subject, ['practice_credits' => 0]));

        // Gửi kèm số tiết mới thì được
        $updated = $this->subjects->update($subject, ['practice_credits' => 0, 'practice_periods' => 0, 'theory_periods' => 45]);
        $this->assertSame([3, 0, 45, 0], [$updated->theory_credits, $updated->practice_credits, $updated->theory_periods, $updated->practice_periods]);
    }

    public function test_ma_hoc_phan_duy_nhat_va_khong_cap_lai_ke_ca_da_xoa(): void
    {
        $subject = $this->subject();

        $this->rejected(fn () => $this->subject(['name' => 'Trùng mã']));

        $this->subjects->delete($subject);
        $e = $this->rejected(fn () => $this->subject(['name' => 'Dùng lại mã đã xóa']));
        $this->assertStringContainsString('INT1001 đã được dùng', $e->getMessage());
    }

    public function test_bo_mon_ngung_hoat_dong_khong_quan_ly_them_hoc_phan(): void
    {
        $this->department->deactivate();

        $e = $this->rejected(fn () => $this->subject());
        $this->assertStringContainsString('BM-KTPM đang ngừng hoạt động', $e->getMessage());
    }

    public function test_chua_co_du_lieu_lien_quan_thi_doi_ma_doi_tin_chi_va_xoa_duoc(): void
    {
        $subject = $this->subject(['credits' => 3, 'practice_credits' => 1]);

        $updated = $this->subjects->update($subject, ['code' => 'INT1009', 'credits' => 4]);

        // Đổi tổng mà không gửi kèm phần lý thuyết: phần thực hành giữ, lý thuyết tính lại
        $this->assertSame(['INT1009', 4, 3, 1], [$updated->code, $updated->credits, $updated->theory_credits, $updated->practice_credits]);

        $this->subjects->delete($updated);
        $this->assertSoftDeleted($updated);
    }

    public function test_da_co_lop_thi_khong_doi_ma_khong_doi_tin_chi_khong_xoa_chi_ngung(): void
    {
        $subject = $this->subject();
        $this->referencedBySections($subject);

        $e = $this->rejected(fn () => $this->subjects->update($subject, ['code' => 'INT9999']));
        $this->assertStringContainsString('1 lớp học phần', $e->getMessage());

        $e = $this->rejected(fn () => $this->subjects->update($subject, ['credits' => 4]));
        $this->assertStringContainsString('tổng tín chỉ', $e->getMessage());
        $this->assertStringContainsString('phiên bản', $e->hint());

        $this->rejected(fn () => $this->subjects->update($subject, ['counts_toward_gpa' => false]));
        $this->rejected(fn () => $this->subjects->delete($subject));

        // Đổi tên, mô tả, gửi lại đúng số tín chỉ cũ: được
        $renamed = $this->subjects->update($subject, ['name' => 'Lập trình căn bản', 'credits' => 3, 'description' => 'Mô tả mới']);
        $this->assertSame('Lập trình căn bản', $renamed->name);

        $stopped = $this->subjects->update($subject, ['status' => 'inactive']);
        $this->assertFalse($stopped->isActive());
    }

    public function test_con_lop_dang_mo_thi_chua_ngung_duoc(): void
    {
        $subject = $this->subject();
        $this->referencedBySections($subject, openSection: true);

        $e = $this->rejected(fn () => $this->subjects->update($subject, ['status' => 'inactive']));
        $this->assertStringContainsString('còn 1 lớp học phần', $e->getMessage());
    }

    public function test_kich_hoat_lai_can_bo_mon_dang_hoat_dong(): void
    {
        $subject = $this->subjects->update($this->subject(), ['status' => 'inactive']);
        $this->department->deactivate();

        $this->rejected(fn () => $this->subjects->update($subject, ['status' => 'active']));

        $this->department->activate();
        $this->assertTrue($this->subjects->update($subject, ['status' => 'active'])->isActive());
    }

    public function test_bo_mon_con_hoc_phan_thi_khong_xoa_khong_ngung_duoc(): void
    {
        $this->subject();
        $faculty = app(FacultyService::class);

        $e = $this->rejected(fn () => $faculty->delete($this->department));
        $this->assertStringContainsString('1 học phần', $e->getMessage());

        $e = $this->rejected(fn () => $faculty->update($this->department, ['status' => 'inactive']));
        $this->assertStringContainsString('học phần', $e->getMessage());
    }

    public function test_tao_sua_xoa_hoc_phan_ghi_nhat_ky_kiem_toan(): void
    {
        $subject = $this->subject();
        $this->subjects->update($subject, ['name' => 'Tên mới']);

        $this->assertDatabaseHas('audit_logs', ['auditable_type' => $subject->getMorphClass(), 'auditable_id' => $subject->id, 'event' => 'created']);
        $this->assertDatabaseHas('audit_logs', ['auditable_type' => $subject->getMorphClass(), 'auditable_id' => $subject->id, 'event' => 'updated']);
    }
}
