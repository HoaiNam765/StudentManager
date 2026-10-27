<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\SchoolClass\Models\AdvisorAssignment;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Student\Models\Student;
use App\Modules\Teacher\Models\Lecturer;
use Database\Seeders\AcademicYearSeeder;
use Database\Seeders\CurriculumSeeder;
use Database\Seeders\DevRoleUsersSeeder;
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
 * Lỗi phát hiện khi mở mọi trang theo từng tài khoản demo (26/10): trang chỉ gọi API mà vai trò được phép, sinh viên chọn
 * được học kỳ ở trang kết quả đăng ký, tài khoản cố vấn mẫu có hồ sơ giảng viên.
 */
class DemoRoleAccountsTest extends TestCase
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

    public function test_tai_chinh_xem_duoc_lop_hoc_phan_ma_khong_goi_api_phong(): void
    {
        $this->actingAs($this->userWithRoles('FIN'));
        $page = $this->get('/admin/lop-hoc-phan')->assertOk()->viewData('pageData');

        $this->assertFalse($page['can']['viewRooms']);

        foreach (['terms', 'subjects', 'lecturers', 'list'] as $api) {
            $this->getJson($page['api'][$api])->assertOk();
        }

        $this->assertNotEmpty($this->getJson($page['api']['list'])->json('data'));
        // FIN vẫn không có ROM.view; trang không được gọi API này
        $this->getJson($page['api']['rooms'])->assertForbidden();

        $this->actingAs($this->userWithRoles('ACAD'));
        $this->assertTrue($this->get('/admin/lop-hoc-phan')->viewData('pageData')['can']['viewRooms']);
    }

    public function test_trang_lop_hanh_chinh_chi_tai_bo_quy_che_khi_sua_duoc_khoa_va_co_quyen_sys(): void
    {
        foreach (['EXAM', 'CTSV', 'FIN', 'DEAN'] as $role) {
            $this->actingAs($this->userWithRoles($role));
            $page = $this->get('/admin/lop-hanh-chinh')->assertOk()->viewData('pageData');
            $this->assertFalse($page['can']['policySets'], $role);
        }

        $this->actingAs($this->userWithRoles('ACAD'));
        $page = $this->get('/admin/lop-hanh-chinh')->assertOk()->viewData('pageData');
        $this->assertTrue($page['can']['policySets']);
        $this->getJson($page['api']['policySets'])->assertOk();
    }

    public function test_sinh_vien_lay_duoc_danh_sach_hoc_ky_co_dang_ky(): void
    {
        [$first, $second, $summer] = Term::query()->orderBy('start_date')->get()->all();
        $section = CourseSection::query()->where('term_id', $second->id)->firstOrFail();
        $earlier = $section->replicate(['search_text']);
        $earlier->forceFill(['term_id' => $first->id, 'code' => $section->code.'-HK1'])->save();

        $user = $this->userWithRoles('STU');
        $student = Student::factory()->create(['user_id' => $user->id]);
        $other = Student::factory()->create();
        $enroll = fn (Student $who, CourseSection $s, string $status) => Enrollment::create([
            'student_id' => $who->id, 'course_section_id' => $s->id, 'term_id' => $s->term_id,
            'subject_id' => $s->subject_id, 'status' => $status, 'registered_at' => now(),
        ]);
        $enroll($student, $section, 'registered');
        $enroll($student, $earlier, 'withdrawn');
        $enroll($other, $section, 'registered');

        $this->actingAs($user);
        $page = $this->get('/student/ket-qua-dang-ky')->assertOk()->viewData('pageData');
        $this->assertSame(route('student.registration.terms'), $page['api']['terms']);

        // Học kỳ có đăng ký (kể cả đã rút) và học kỳ mặc định (chưa có đợt đăng ký: học kỳ mới nhất), mới nhất trước
        $years = $this->getJson($page['api']['terms'])->assertOk()->json('data');
        $this->assertCount(1, $years);
        $this->assertSame('2026-2027', $years[0]['name']);
        $this->assertSame([$summer->id, $second->id, $first->id], array_column($years[0]['terms'], 'id'));

        // Sinh viên không gọi được API quản trị (#285) nên trang không dùng /admin/academic-years nữa
        $this->getJson(route('admin.academic-years.index'))->assertForbidden();
    }

    public function test_tai_khoan_co_van_mau_co_ho_so_giang_vien_va_lop_co_van(): void
    {
        $this->seed(DevRoleUsersSeeder::class);
        $this->seed(DevRoleUsersSeeder::class);

        $adv = User::query()->where('username', 'adv.test')->firstOrFail();
        $lecturer = Lecturer::query()->where('user_id', $adv->id)->firstOrFail();
        $this->assertSame(DevRoleUsersSeeder::ADVISOR_CODE, $lecturer->code);
        $this->assertSame(1, Lecturer::query()->where('code', DevRoleUsersSeeder::ADVISOR_CODE)->count());

        $assignment = AdvisorAssignment::query()->where('lecturer_id', $lecturer->id)->current()->sole();

        $this->actingAs($adv);
        $this->get('/teacher/lich-giang-day')->assertOk();
        $this->getJson('/teacher/timetable?view=week')->assertOk();
        $this->getJson('/teacher/dashboard/data')->assertOk()->assertJsonPath('lecturer.code', DevRoleUsersSeeder::ADVISOR_CODE);
        $this->assertContains($assignment->admin_class_id, array_column($this->getJson('/teacher/admin-classes')->assertOk()->json('data'), 'id'));
    }

    public function test_tai_khoan_chua_gan_ho_so_giang_vien_nhan_thong_bao_ro(): void
    {
        $this->actingAs($this->userWithRoles('LEC'));

        $this->getJson('/teacher/timetable?view=week')->assertNotFound()->assertJsonPath('message', 'Tài khoản chưa gắn với hồ sơ giảng viên.');
        $this->getJson('/teacher/dashboard/data')->assertNotFound()->assertJsonPath('message', 'Tài khoản chưa gắn với hồ sơ giảng viên.');
    }
}
