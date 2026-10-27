<?php

namespace Tests\Feature;

use App\Modules\AcademicYear\Models\Term;
use App\Modules\AcademicYear\Models\TermStatus;
use App\Modules\Grade\Models\TermGpa;
use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\SectionInstructor;
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
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

/**
 * Trang điểm mở đúng học kỳ / lớp mặc định (phát hiện khi chạy kịch bản demo 27/10): không mở học kỳ chưa bắt đầu,
 * chưa có dữ liệu.
 */
class DefaultTermSelectionTest extends TestCase
{
    use InteractsWithRoles;
    use RefreshDatabase;

    private Term $current;

    private Term $next;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
        $this->seed([SystemSeeder::class, FacultySeeder::class, AcademicYearSeeder::class, RoomSeeder::class, SubjectSeeder::class,
            CurriculumSeeder::class, TeacherSeeder::class, GradeStructureSeeder::class, SchoolClassSeeder::class]);

        // Học kỳ 1 đang học, học kỳ 2 (bắt đầu sau) chưa tới
        $this->current = Term::query()->where('name', 'Học kỳ 1')->orderBy('start_date')->firstOrFail();
        $this->current->update(['status' => TermStatus::IN_PROGRESS]);
        $this->next = Term::query()->where('name', 'Học kỳ 2')->orderBy('start_date')->firstOrFail();
    }

    public function test_quan_ly_diem_mac_dinh_mo_hoc_ky_dang_hoc(): void
    {
        $this->actingAs($this->userWithRoles('ACAD'))
            ->get(route('admin.pages.grades'))
            ->assertOk()
            ->assertViewHas('selectedTermId', $this->current->id);
    }

    public function test_ket_qua_hoc_tap_mac_dinh_mo_hoc_ky_da_co_diem(): void
    {
        $user = $this->userWithRoles('STU');
        $student = Student::factory()->create(['user_id' => $user->id]);
        TermGpa::create(['student_id' => $student->id, 'term_id' => $this->current->id, 'gpa' => 3.2, 'credits_attempted' => 15, 'credits_earned' => 15]);

        $this->actingAs($user)->get(route('student.pages.grades'))->assertOk()->assertViewHas('selectedTermId', $this->current->id);
        // Chọn học kỳ khác thì giữ lựa chọn
        $this->get(route('student.pages.grades', ['term_id' => $this->next->id]))->assertViewHas('selectedTermId', $this->next->id);
    }

    public function test_nhap_diem_mac_dinh_mo_lop_dang_hoc(): void
    {
        // Lấy một giảng viên có từ hai lớp; lớp xếp sau theo mã chuyển sang Đang học
        $lecturerId = SectionInstructor::query()->selectRaw('lecturer_id, COUNT(*) c')->groupBy('lecturer_id')->havingRaw('COUNT(*) >= 2')->value('lecturer_id');
        $this->assertNotNull($lecturerId, 'Dữ liệu mẫu cần một giảng viên có từ hai lớp.');
        $sections = CourseSection::query()->whereIn('id', SectionInstructor::query()->where('lecturer_id', $lecturerId)->select('course_section_id'))->orderBy('code')->get();
        $teaching = $sections->last();
        $teaching->update(['status' => SectionStatus::InProgress]);

        $user = SectionInstructor::query()->where('lecturer_id', $lecturerId)->firstOrFail()->lecturer->user;
        $user->forceFill(['must_change_password' => false, 'temporary_password_expires_at' => null, 'password_changed_at' => now()])->save();

        $this->actingAs($user)->get(route('teacher.pages.gradebook'))->assertOk()
            ->assertViewHas('selectedSection', fn ($section) => $section?->id === $teaching->id);
    }
}
