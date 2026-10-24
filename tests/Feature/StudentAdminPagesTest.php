<?php

namespace Tests\Feature;

use App\Modules\SchoolClass\Models\AdminClass;
use App\Modules\SchoolClass\Models\AdminClassMember;
use App\Modules\Student\Models\Student;
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
use Illuminate\Support\Facades\DB;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

/**
 * Trang quản lý sinh viên (FE #16) và khóa – lớp hành chính (FE #15): trang HTML theo quyền, danh sách sinh viên có lớp
 * hành chính hiện tại và GPA tích lũy cho cột của mock admin/sinh-vien.
 */
class StudentAdminPagesTest extends TestCase
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

    public function test_phong_dao_tao_mo_duoc_cac_trang(): void
    {
        $student = Student::factory()->create();
        $this->actingAs($this->userWithRoles('ACAD'));

        foreach (['/admin/sinh-vien', '/admin/sinh-vien/them', '/admin/sinh-vien/'.$student->id, '/admin/sinh-vien/'.$student->id.'/sua', '/admin/sinh-vien/nhap-file', '/admin/lop-hanh-chinh'] as $url) {
            $this->get($url)->assertOk()->assertSee('data-page="admin-', false);
        }
    }

    public function test_danh_sach_sinh_vien_co_lop_hanh_chinh_va_gpa(): void
    {
        $student = Student::factory()->create();
        $class = AdminClass::firstOrFail();
        AdminClassMember::create(['student_id' => $student->id, 'admin_class_id' => $class->id, 'effective_from' => now()->subMonth()->toDateString()]);
        DB::table('cumulative_gpas')->insert(['student_id' => $student->id, 'gpa' => 3.25, 'credits_attempted' => 30, 'credits_earned' => 28, 'classification' => 'Giỏi', 'created_at' => now(), 'updated_at' => now()]);
        $this->actingAs($this->userWithRoles('ACAD'));

        $row = collect($this->getJson('/admin/students?per_page=100')->assertOk()->json('data'))->firstWhere('id', $student->id);

        $this->assertSame($class->code, $row['admin_class']['code']);
        $this->assertSame(3.25, $row['gpa']['gpa']);
        $this->assertSame(28, $row['gpa']['credits_earned']);
    }

    public function test_sinh_vien_khong_vao_duoc_trang_quan_ly(): void
    {
        $this->actingAs($this->userWithRoles('STU'));

        $this->get('/admin/sinh-vien')->assertRedirect(route('student.home'));
        $this->get('/admin/lop-hanh-chinh')->assertRedirect(route('student.home'));
    }
}
