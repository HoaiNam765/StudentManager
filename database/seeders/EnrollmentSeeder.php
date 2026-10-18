<?php

namespace Database\Seeders;

use App\Modules\AcademicYear\Models\Term;
use App\Modules\Enrollment\Enums\PeriodStatus;
use App\Modules\Enrollment\Enums\RegistrationRound;
use App\Modules\Enrollment\Models\RegistrationPeriod;
use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Models\Cohort;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Student\Enums\StudentStatus;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Services\AdmissionService;
use App\Support\Exceptions\BusinessRuleException;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

/**
 * ENR (dữ liệu dùng thử): xác nhận nhập học 12 thí sinh khóa 2026 (có MSSV, tài khoản, CTĐT, lớp), mở đăng ký các lớp HK2
 * đã có giảng viên chính và lịch, và một đợt vòng 1 cho khóa 2026 đang mở từ hôm qua tới 30 ngày sau để thử ngay.
 */
class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $term = Term::query()->where('name', 'Học kỳ 2')->orderBy('start_date')->first();
        $cohort = Cohort::query()->where('start_year', 2026)->first();

        if ($term === null || $cohort === null) {
            return;
        }

        $admissions = app(AdmissionService::class);

        foreach (Student::query()->where('status', StudentStatus::Pending)->orderBy('id')->limit(12)->get() as $student) {
            try {
                $admissions->admit($student);
            } catch (BusinessRuleException) {
                // Thiếu CTĐT / lớp cho ngành đó thì bỏ qua
            }
        }

        CourseSection::query()
            ->where('term_id', $term->id)
            ->where('status', SectionStatus::Planned)
            ->whereHas('instructors', fn ($q) => $q->where('role', 'main'))
            ->whereExists(fn ($q) => $q->from('schedule_rules')->whereColumn('schedule_rules.course_section_id', 'course_sections.id')->whereNull('deleted_at'))
            ->get()
            ->each(fn (CourseSection $s) => $s->update(['status' => SectionStatus::RegistrationOpen]));

        $period = RegistrationPeriod::firstOrCreate(
            ['term_id' => $term->id, 'round' => RegistrationRound::Round1],
            [
                'name' => 'Đăng ký học phần HK2 2026–2027 — vòng 1',
                'opens_at' => CarbonImmutable::now('UTC')->subDay()->startOfHour(),
                'closes_at' => CarbonImmutable::now('UTC')->addDays(30)->startOfHour(),
                'status' => PeriodStatus::Published,
            ],
        );

        if (! $period->targets()->exists()) {
            $period->targets()->create(['cohort_id' => $cohort->id]);
        }
    }
}
