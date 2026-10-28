<?php

namespace Tests\Feature;

use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\SectionInstructor;
use App\Modules\Teacher\Models\Lecturer;
use Database\Seeders\AcademicYearSeeder;
use Database\Seeders\CurriculumSeeder;
use Database\Seeders\FacultySeeder;
use Database\Seeders\GradeStructureSeeder;
use Database\Seeders\RoomSeeder;
use Database\Seeders\SchoolClassSeeder;
use Database\Seeders\SubjectSeeder;
use Database\Seeders\SystemSeeder;
use Database\Seeders\TeacherSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

/**
 * Trang quản lý giảng viên cho Phòng Đào tạo / Quản trị (yêu cầu người dùng 28/10): mở được trang, có trên menu, danh
 * sách kèm số lớp / số tiết theo học kỳ, trang nhập file dùng importer `lecturers`.
 */
class LecturerAdminPagesTest extends TestCase
{
    use InteractsWithRoles;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
        $this->seed([
            SystemSeeder::class, FacultySeeder::class, AcademicYearSeeder::class, RoomSeeder::class, SubjectSeeder::class,
            CurriculumSeeder::class, TeacherSeeder::class, GradeStructureSeeder::class, SchoolClassSeeder::class,
        ]);
    }

    public function test_phong_dao_tao_mo_duoc_trang_giang_vien_va_thay_tren_menu(): void
    {
        $this->actingAs($this->userWithRoles('ACAD'));

        $response = $this->get(route('admin.pages.lecturers'))->assertOk()->assertSee('data-page="admin-lecturers"', false);
        $page = $response->viewData('pageData');
        $this->assertTrue($page['can']['create']);
        $this->assertTrue($page['can']['update']);
        foreach (['list', 'show', 'store', 'update', 'status', 'handover', 'account', 'export'] as $api) {
            $this->assertArrayHasKey($api, $page['api']);
        }
        // Menu bên có mục Giảng viên (cùng khung portal với các trang khác)
        $response->assertSee(route('admin.pages.lecturers'), false);

        $import = $this->get(route('admin.pages.lecturers.import'))->assertOk()->viewData('pageData');
        $this->assertSame('lecturers', $import['importer']);
        $this->assertTrue($import['available']);
    }

    public function test_danh_sach_kem_so_lop_va_so_tiet_theo_hoc_ky(): void
    {
        $this->actingAs($this->userWithRoles('ACAD'));
        $assignment = SectionInstructor::query()->where('role', SectionInstructor::MAIN)->firstOrFail();
        $section = CourseSection::query()->with('subject')->findOrFail($assignment->course_section_id);
        $lecturer = Lecturer::query()->findOrFail($assignment->lecturer_id);
        $expected = SectionInstructor::query()->where('lecturer_id', $lecturer->id)
            ->whereHas('section', fn ($q) => $q->where('term_id', $section->term_id)->where('status', '!=', 'cancelled'))->count();

        $row = collect($this->getJson(route('admin.lecturers.index', ['term_id' => $section->term_id, 'q' => $lecturer->code, 'per_page' => 100]))
            ->assertOk()->json('data'))->firstWhere('id', $lecturer->id);
        $this->assertSame($expected, $row['load']['sections']);
        $this->assertGreaterThanOrEqual((int) $section->subject->theory_periods + (int) $section->subject->practice_periods, $row['load']['periods']);
        $this->assertSame((int) config('studentmanager.course_section.max_periods_per_term'), $row['load']['limit']);

        // Không truyền học kỳ thì không kèm khối lượng
        $this->assertArrayNotHasKey('load', $this->getJson(route('admin.lecturers.index'))->assertOk()->json('data.0'));
    }

    public function test_sinh_vien_khong_vao_duoc_trang_giang_vien(): void
    {
        $this->actingAs($this->userWithRoles('STU'));

        // Cổng quản trị chuyển sinh viên về cổng của mình (như trang Sinh viên)
        $this->get(route('admin.pages.lecturers'))->assertRedirect(route('student.home'));
    }
}
