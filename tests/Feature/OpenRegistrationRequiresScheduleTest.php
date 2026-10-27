<?php

namespace Tests\Feature;

use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Services\CourseSectionService;
use App\Modules\Timetable\Models\ScheduleRule;
use App\Support\Exceptions\BusinessRuleException;
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

/** Mở đăng ký lớp học phần: lớp trực tiếp / kết hợp phải có mẫu lịch, lớp trực tuyến không bắt buộc (FR-CLS-012, FR-TTB-002). */
class OpenRegistrationRequiresScheduleTest extends TestCase
{
    use InteractsWithRoles;
    use RefreshDatabase;

    /** @var list<CourseSection> */
    private array $sections;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
        $this->seed([
            SystemSeeder::class, FacultySeeder::class, AcademicYearSeeder::class, RoomSeeder::class, SubjectSeeder::class,
            CurriculumSeeder::class, TeacherSeeder::class, GradeStructureSeeder::class, SchoolClassSeeder::class,
        ]);

        $this->sections = CourseSection::query()
            ->where('status', SectionStatus::Planned)
            ->whereHas('instructors', fn ($q) => $q->where('role', 'main'))
            ->orderBy('id')->limit(3)->get()->all();
        $this->assertCount(3, $this->sections);
        ScheduleRule::query()->whereIn('course_section_id', collect($this->sections)->pluck('id'))->delete();
    }

    private function rule(CourseSection $section): ScheduleRule
    {
        return ScheduleRule::create(['course_section_id' => $section->id, 'day_of_week' => 2, 'period_from' => 1, 'period_to' => 3, 'week_from' => 1, 'week_to' => 15, 'week_parity' => 'all']);
    }

    public function test_lop_truc_tiep_va_ket_hop_chua_co_lich_thi_khong_mo_dang_ky_duoc(): void
    {
        $service = app(CourseSectionService::class);

        foreach (['in_person', 'hybrid'] as $i => $mode) {
            $section = $this->sections[$i];
            $section->update(['delivery_mode' => $mode]);

            try {
                $service->advance($section, SectionStatus::RegistrationOpen);
                $this->fail("Lớp {$mode} chưa có lịch phải bị chặn.");
            } catch (BusinessRuleException $e) {
                $this->assertStringContainsString('chưa có lịch học', $e->getMessage());
                $this->assertStringContainsString('Xếp thời khóa biểu', (string) $e->hint());
            }

            $this->assertSame(SectionStatus::Planned, $section->refresh()->status);
        }
    }

    public function test_dong_lich_da_xoa_khong_tinh_xep_lich_xong_thi_mo_duoc(): void
    {
        $section = $this->sections[0];
        $section->update(['delivery_mode' => 'in_person']);
        $service = app(CourseSectionService::class);

        $this->rule($section)->delete();
        $this->expectRejected(fn () => $service->advance($section, SectionStatus::RegistrationOpen));

        $this->rule($section);
        $this->assertSame(SectionStatus::RegistrationOpen, $service->advance($section, SectionStatus::RegistrationOpen)->status);
    }

    public function test_lop_truc_tuyen_chua_co_lich_van_mo_dang_ky_duoc(): void
    {
        $section = $this->sections[2];
        $section->update(['delivery_mode' => 'online']);

        $this->assertSame(SectionStatus::RegistrationOpen, app(CourseSectionService::class)->advance($section, SectionStatus::RegistrationOpen)->status);
    }

    public function test_thieu_giang_vien_chinh_bao_truoc_loi_thieu_lich(): void
    {
        $section = $this->sections[0];
        $section->update(['delivery_mode' => 'in_person']);
        $section->instructors()->delete();

        $message = $this->expectRejected(fn () => app(CourseSectionService::class)->advance($section, SectionStatus::RegistrationOpen));
        $this->assertStringContainsString('giảng viên chính', $message);
    }

    public function test_api_tra_422_kem_cach_khac_phuc(): void
    {
        $section = $this->sections[0];
        $section->update(['delivery_mode' => 'in_person']);

        $message = $this->actingAs($this->userWithRoles('ACAD'))
            ->postJson("/admin/course-sections/{$section->id}/status/registration_open")
            ->assertStatus(422)
            ->json('message');

        $this->assertStringContainsString('chưa có lịch học', $message);
        $this->assertStringContainsString('Xếp lịch ở trang Xếp thời khóa biểu', $message);
    }

    private function expectRejected(callable $action): string
    {
        try {
            $action();
        } catch (BusinessRuleException $e) {
            return $e->getMessage();
        }

        $this->fail('Thao tác phải bị từ chối.');
    }
}
