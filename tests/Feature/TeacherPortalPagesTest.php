<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\SectionInstructor;
use App\Modules\Timetable\Models\ClassSession;
use App\Modules\Timetable\Models\Period;
use App\Modules\Timetable\Services\TimetablePublication;
use Carbon\CarbonImmutable;
use Database\Seeders\AcademicYearSeeder;
use Database\Seeders\CurriculumSeeder;
use Database\Seeders\FacultySeeder;
use Database\Seeders\GradeStructureSeeder;
use Database\Seeders\RoomSeeder;
use Database\Seeders\SchoolClassSeeder;
use Database\Seeders\SubjectSeeder;
use Database\Seeders\SystemSeeder;
use Database\Seeders\TeacherSeeder;
use Database\Seeders\TimetableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

/**
 * Cổng giảng viên (FE #24, #25): lịch giảng dạy, lớp học phần, lớp cố vấn; chi tiết lớp học phần phía quản trị (FE #18).
 * API lớp của giảng viên có tín chỉ, sĩ số, lịch học, trạng thái điểm cho thẻ lớp của mock teacher/lop-hoc-phan.
 */
class TeacherPortalPagesTest extends TestCase
{
    use InteractsWithRoles;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
        $this->seed([
            SystemSeeder::class, FacultySeeder::class, AcademicYearSeeder::class, RoomSeeder::class, SubjectSeeder::class,
            CurriculumSeeder::class, TeacherSeeder::class, GradeStructureSeeder::class, SchoolClassSeeder::class, TimetableSeeder::class,
        ]);
    }

    private function lecturerOf(CourseSection $section): User
    {
        $user = SectionInstructor::query()->where('course_section_id', $section->id)->firstOrFail()->lecturer->user;
        // Tài khoản giảng viên tạo bởi seeder còn mật khẩu tạm (phải đổi trước khi dùng cổng)
        $user->forceFill(['must_change_password' => false, 'temporary_password_expires_at' => null, 'password_changed_at' => now()])->save();

        return $user;
    }

    public function test_giang_vien_mo_duoc_cac_trang_va_thay_lop_cua_minh(): void
    {
        $section = CourseSection::query()->whereIn('id', DB::table('schedule_rules')->whereNull('deleted_at')->select('course_section_id'))->firstOrFail();
        $this->actingAs($this->lecturerOf($section));

        foreach (['/teacher/lich-giang-day', '/teacher/lop-hoc-phan', '/teacher/lop-co-van'] as $url) {
            $this->get($url)->assertOk()->assertSee('data-page="teacher-', false);
        }

        $row = collect($this->getJson('/teacher/course-sections')->assertOk()->json('data'))->firstWhere('id', $section->id);
        $this->assertNotNull($row['subject']['credits']);
        $this->assertArrayHasKey('students_count', $row);
        $this->assertNotEmpty($row['schedule']);
        $this->assertSame('draft', $row['grade_status']['value']);
    }

    public function test_tong_quan_giang_vien_dung_lich_day_that_va_mo_dung_buoi_diem_danh(): void
    {
        $section = CourseSection::query()->whereIn('id', DB::table('schedule_rules')->whereNull('deleted_at')->select('course_section_id'))->with('term')->firstOrFail();
        $user = $this->lecturerOf($section);
        $session = ClassSession::query()->where('course_section_id', $section->id)->where('status', 'planned')->with('room')->orderBy('date')->firstOrFail();
        $start = Period::query()->whereNull('campus_id')->where('number', $session->period_from)->value('starts_at');
        $section->update(['status' => 'in_progress']);

        // Chưa công bố thời khóa biểu: không hiện lịch, báo rõ; không còn số liệu mẫu của bản thiết kế
        $this->travelTo(CarbonImmutable::parse($session->date->toDateString().' '.$start, config('studentmanager.display_timezone'))->addMinutes(10));
        $this->actingAs($user)->get('/teacher')->assertOk()->assertSee('chưa công bố')->assertDontSee('IT302')->assertSee($section->code);

        app(TimetablePublication::class)->publish($section->term);
        $this->get('/teacher')->assertOk()
            ->assertSee('Lịch giảng dạy hôm nay')->assertSee('Đang diễn ra')->assertSee($session->room->code)
            ->assertSee(route('teacher.pages.attendance').'?section='.$section->id.'&session='.$session->id, false);
    }

    public function test_quan_tri_xem_chi_tiet_lop_hoc_phan(): void
    {
        $section = CourseSection::query()->firstOrFail();
        $this->actingAs($this->userWithRoles('ACAD'));

        $this->get('/admin/lop-hoc-phan/'.$section->id)->assertOk()->assertSee($section->code);
        $this->getJson('/admin/course-sections/'.$section->id.'/enrollments')->assertOk()->assertJsonStructure(['data', 'count']);
    }

    public function test_giang_vien_khong_vao_duoc_trang_quan_tri(): void
    {
        $section = CourseSection::query()->firstOrFail();
        $this->actingAs($this->lecturerOf($section));

        $this->get('/admin/lop-hoc-phan/'.$section->id)->assertRedirect(route('teacher.home'));
    }
}
