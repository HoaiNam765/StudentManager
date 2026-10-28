<?php

namespace Tests\Feature;

use App\Modules\Room\Models\Room;
use App\Modules\Room\Models\RoomType;
use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Timetable\Models\ScheduleRule;
use App\Modules\Timetable\Services\ScheduleRuleService;
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
use Database\Seeders\TimetableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

/**
 * Yêu cầu người dùng 28/10: lớp thực hành học tối thiểu `studentmanager.timetable.practice_min_periods` (5) tiết mỗi buổi;
 * lớp lý thuyết không bị ràng buộc này.
 */
class PracticeSessionLengthTest extends TestCase
{
    use InteractsWithRoles;
    use RefreshDatabase;

    private CourseSection $section;

    private Room $lab;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
        $this->seed([
            SystemSeeder::class, FacultySeeder::class, AcademicYearSeeder::class, RoomSeeder::class, SubjectSeeder::class,
            CurriculumSeeder::class, TeacherSeeder::class, GradeStructureSeeder::class, SchoolClassSeeder::class, TimetableSeeder::class,
        ]);

        $this->section = CourseSection::query()->where('status', SectionStatus::Planned)
            ->whereHas('instructors', fn ($q) => $q->where('role', 'main'))->orderBy('id')->firstOrFail();
        ScheduleRule::query()->where('course_section_id', $this->section->id)->delete();
        $this->lab = Room::query()->where('status', 'available')
            ->whereHas('type', fn ($q) => $q->where('code', RoomType::COMPUTER_LAB))->orderByDesc('capacity')->firstOrFail();
        // Phòng máy mẫu nhỏ hơn lớp lý thuyết: giảm sĩ số tối đa cho vừa phòng
        $this->section->update(['min_size' => min($this->section->min_size, $this->lab->capacity), 'max_size' => $this->lab->capacity]);
    }

    /** Chủ nhật: lịch mẫu chỉ xếp thứ 2–7 nên không trùng phòng / giảng viên. */
    private function schedule(int $from, int $to): array
    {
        return ['day_of_week' => 7, 'period_from' => $from, 'period_to' => $to, 'room_id' => $this->lab->id, 'week_from' => 1, 'week_to' => 15];
    }

    public function test_lop_thuc_hanh_duoi_5_tiet_moi_buoi_bi_chan(): void
    {
        $this->section->update(['section_type' => 'practice']);

        try {
            app(ScheduleRuleService::class)->create($this->section->refresh(), $this->schedule(1, 3));
            $this->fail('Lớp thực hành 3 tiết / buổi phải bị chặn.');
        } catch (BusinessRuleException $e) {
            $this->assertStringContainsString('cần tối thiểu 5 tiết', $e->getMessage());
        }

        $rule = app(ScheduleRuleService::class)->create($this->section, $this->schedule(1, 5))['rule'];
        $this->assertSame(5, $rule->periodCount());
    }

    public function test_lop_ly_thuyet_3_tiet_moi_buoi_van_xep_duoc(): void
    {
        $this->section->update(['section_type' => 'theory']);

        $rule = app(ScheduleRuleService::class)->create($this->section->refresh(), $this->schedule(1, 3))['rule'];

        $this->assertSame(3, $rule->periodCount());
    }
}
