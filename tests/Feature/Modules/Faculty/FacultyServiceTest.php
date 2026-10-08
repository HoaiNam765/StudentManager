<?php

namespace Tests\Feature\Modules\Faculty;

use App\Modules\Faculty\Models\Department;
use App\Modules\Faculty\Models\Faculty;
use App\Modules\Faculty\Models\Major;
use App\Modules\Faculty\Models\TrainingType;
use App\Modules\Faculty\Services\FacultyService;
use App\Support\Exceptions\BusinessRuleException;
use App\Support\References\ReferenceRegistry;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FacultyServiceTest extends TestCase
{
    use DatabaseMigrations;

    private FacultyService $units;

    private Faculty $faculty;

    protected function setUp(): void
    {
        parent::setUp();

        $this->units = app(FacultyService::class);
        $this->faculty = $this->units->createFaculty(['code' => 'CNTT', 'name' => 'Khoa Công nghệ thông tin']);
    }

    private function major(string $code = '7480201', ?Faculty $faculty = null): Major
    {
        return $this->units->createMajor([
            'faculty_id' => ($faculty ?? $this->faculty)->id, 'code' => $code, 'name' => 'Công nghệ thông tin',
            'total_credits' => 150, 'standard_terms' => 8,
        ]);
    }

    private function department(string $code = 'BM-KTPM'): Department
    {
        return $this->units->createDepartment(['faculty_id' => $this->faculty->id, 'code' => $code, 'name' => 'Bộ môn Kỹ thuật phần mềm']);
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

    public function test_ngung_khoa_con_bo_mon_hoac_nganh_dang_hoat_dong_bi_tu_choi_va_liet_ke_du_lieu_can_chuyen(): void
    {
        $this->department('BM-KTPM');
        $this->department('BM-HTTT');
        $major = $this->major();

        $e = $this->rejected(fn () => $this->units->update($this->faculty, ['status' => 'inactive']));
        $this->assertStringContainsString('bộ môn đang hoạt động: BM-HTTT, BM-KTPM', $e->getMessage());
        $this->assertStringContainsString('ngành đang hoạt động: 7480201', $e->getMessage());
        $this->assertStringContainsString('BR-FAC-05', $e->hint());
        $this->assertTrue($this->faculty->refresh()->isActive());

        // Ngừng hết đơn vị con thì ngừng được khoa
        Department::all()->each(fn (Department $d) => $this->units->update($d, ['status' => 'inactive']));
        $this->units->update($major, ['status' => 'inactive']);
        $this->assertFalse($this->units->update($this->faculty, ['status' => 'inactive'])->isActive());

        // Khoa đã ngừng: không thêm, không kích hoạt đơn vị con
        $this->rejected(fn () => $this->department('BM-MOI'));
        $this->rejected(fn () => $this->units->update($major, ['status' => 'active']));
    }

    public function test_ma_nganh_da_co_chuong_trinh_dao_tao_thi_khong_doi_duoc(): void
    {
        Schema::create('test_curricula', function (Blueprint $table) {
            $table->id();
            $table->foreignId('major_id');
        });
        app(ReferenceRegistry::class)->register(Major::class, 'test_curricula', 'major_id', 'chương trình đào tạo');

        $major = $this->major();
        DB::table('test_curricula')->insert(['major_id' => $major->id]);

        $e = $this->rejected(fn () => $this->units->update($major, ['code' => '7480202']));
        $this->assertStringContainsString('1 chương trình đào tạo', $e->getMessage());
        $this->assertStringContainsString('BR-FAC-01', $e->hint());

        // Đổi tên thì được
        $this->assertSame('Công nghệ thông tin (mới)', $this->units->update($major, ['name' => 'Công nghệ thông tin (mới)'])->name);

        // Khoa đã có ngành, bộ môn thì cũng không đổi mã được
        $this->rejected(fn () => $this->units->update($this->faculty, ['code' => 'IT']));
    }

    public function test_ma_duy_nhat_va_khong_cap_lai_don_vi_chua_co_du_lieu_thi_doi_ma_va_xoa_duoc(): void
    {
        $department = $this->department('BM-TAM');

        $this->rejected(fn () => $this->department('BM-TAM'));
        $this->assertSame('BM-TAM2', $this->units->update($department, ['code' => 'BM-TAM2'])->code);

        $this->units->delete($department->refresh());
        $this->assertSoftDeleted($department);
        $this->rejected(fn () => $this->department('BM-TAM2'));

        // Khoa còn bộ môn (kể cả đã xóa mềm) thì không xóa được, chỉ ngừng
        $e = $this->rejected(fn () => $this->units->delete($this->faculty));
        $this->assertStringContainsString('ngừng hoạt động', $e->hint());
    }

    public function test_tham_chieu_con_hoat_dong_cua_module_khac_chan_ngung_bo_mon(): void
    {
        Schema::create('test_teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id');
            $table->string('status');
        });
        app(ReferenceRegistry::class)->register(
            Department::class, 'test_teachers', 'department_id', 'giảng viên đang làm việc',
            active: fn ($q) => $q->where('status', 'working'),
        );

        $department = $this->department();
        DB::table('test_teachers')->insert([
            ['department_id' => $department->id, 'status' => 'working'],
            ['department_id' => $department->id, 'status' => 'working'],
            ['department_id' => $department->id, 'status' => 'retired'],
        ]);

        $e = $this->rejected(fn () => $this->units->update($department, ['status' => 'inactive']));
        $this->assertStringContainsString('2 giảng viên đang làm việc', $e->getMessage());

        DB::table('test_teachers')->update(['status' => 'retired']);
        $this->assertFalse($this->units->update($department, ['status' => 'inactive'])->isActive());

        // Đã có giảng viên (kể cả đã nghỉ) thì vẫn không xóa được
        $this->rejected(fn () => $this->units->delete($department));
    }

    public function test_he_dao_tao_luon_co_dung_mot_he_mac_dinh(): void
    {
        $regular = $this->units->createTrainingType(['code' => 'CQ', 'name' => 'Chính quy']);
        $this->assertTrue($regular->is_default, 'Hệ đầu tiên tự làm mặc định');

        $bridging = $this->units->createTrainingType(['code' => 'LT', 'name' => 'Liên thông', 'is_default' => true]);
        $this->assertTrue($bridging->is_default);
        $this->assertFalse($regular->refresh()->is_default);
        $this->assertSame(1, TrainingType::query()->where('is_default', true)->count());

        $this->rejected(fn () => $this->units->updateTrainingType($bridging, ['status' => 'inactive']));
        $this->units->updateTrainingType($regular, ['is_default' => true]);
        $this->assertFalse($this->units->updateTrainingType($bridging->refresh(), ['status' => 'inactive'])->isActive());
    }

    public function test_khoi_luong_trung_binh_moi_hoc_ky_tu_thoi_gian_dao_tao_chuan(): void
    {
        $this->assertEquals(18.75, $this->major()->averageCreditsPerTerm());
    }
}
