<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\AcademicYear\Models\TermStatus;
use App\Modules\AcademicYear\Models\TermType;
use App\Modules\Curriculum\Models\Curriculum;
use App\Modules\Curriculum\Models\CurriculumSubject;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Faculty\Models\Major;
use App\Modules\Faculty\Models\TrainingType;
use App\Modules\Grade\Models\SectionGradeComponent;
use App\Modules\Grade\Models\SectionGradeWorkflow;
use App\Modules\Grade\Services\CourseGradeService;
use App\Modules\Grade\Services\GpaService;
use App\Modules\SchoolClass\Models\AdminClass;
use App\Modules\SchoolClass\Models\Cohort;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\SectionInstructor;
use App\Modules\Student\Enums\StudentStatus;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Services\AdmissionService;
use App\Modules\Student\Services\StudentStatusService;
use App\Modules\Subject\Models\GradeComponentTemplate;
use App\Modules\Subject\Models\Subject;
use App\Modules\Teacher\Models\Lecturer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Dữ liệu demo trọn vòng đời học kỳ (#122; BA 11.2, FR-SYS-016, BR-SYS-10, NFR-DAT-02). Chạy bằng `php artisan demo:seed`
 * sau `php artisan migrate --seed`; **từ chối ở môi trường production**. Chạy lại thì bỏ qua (đã có học kỳ demo).
 *
 * Kết quả (cộng với dữ liệu mẫu của các module): 3 khoa, 6 ngành, 60 học phần, 20 giảng viên, 20 phòng; 300 sinh viên
 * (khóa 2025 và 2026, mỗi ngành 25 / khóa, họ tên Faker vi_VN, CCCD / email giả); năm học 2025–2026 hai học kỳ **đã khóa**
 * (lớp học phần theo CTĐT, điểm đã công bố + khóa, GPA đã tính) và học kỳ 1 2026–2027 **đang diễn ra** (đăng ký đã chốt,
 * đang nhập điểm, hai lớp đã nộp); học kỳ 2 2026–2027 đang mở đăng ký (từ EnrollmentSeeder).
 * Tình huống đặc biệt: sinh viên học lại (trượt học kỳ 1, học lại ở học kỳ 2), sinh viên học lực yếu (bị cảnh báo theo
 * GPA), vắng thi không lý do, hai sinh viên bảo lưu từ học kỳ 1 2026–2027. Nợ học phí, vắng quá ngưỡng chờ FEE, ATT.
 * Mật khẩu tài khoản sinh viên demo = DevRoleUsersSeeder::PASSWORD (đăng nhập bằng MSSV).
 */
class DemoDataSeeder extends Seeder
{
    public const PER_MAJOR = 25;

    public const SECTION_MAX = 80;

    /** @var array<int, float> năng lực giả của từng sinh viên (mức điểm trung bình) */
    private array $ability = [];

    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command?->error('Không chạy dữ liệu demo trên môi trường production (BR-SYS-10).');

            return;
        }

        if (AcademicYear::query()->where('name', '2025-2026')->exists()) {
            $this->command?->info('Dữ liệu demo đã có (năm học 2025-2026), bỏ qua.');

            return;
        }

        mt_srand(2026);

        // Một giao dịch: lỗi giữa chừng thì không để lại dữ liệu demo dở dang
        DB::transaction(fn () => $this->seed());
        // Điểm danh các buổi đã qua của học kỳ đang diễn ra (ATT)
        $this->call(AttendanceDemoSeeder::class);

        $this->command?->info(sprintf('Dữ liệu demo: %d sinh viên, %d lớp học phần, %d lượt đăng ký.',
            Student::query()->whereNotNull('student_code')->count(), CourseSection::query()->count(), Enrollment::query()->count()));
    }

    private function seed(): void
    {

        // CTĐT mẫu áp dụng từ khóa 2025 để khóa 2025 nhập học được (chỉ trong dữ liệu demo)
        Curriculum::query()->where('cohort_from', 2026)->get()->each(fn (Curriculum $c) => $c->update(['cohort_from' => 2025]));

        [$hk1Past, $hk2Past, $hk1Now] = $this->terms();
        $this->adminClasses(2025);
        $k2025 = $this->admitCohort(2025);
        $k2026 = $this->admitCohort(2026);
        $this->demoPasswords($k2025->merge($k2026));

        foreach ($k2025->merge($k2026) as $student) {
            $this->ability[$student->id] = round(min(9.3, max(4.2, $this->normal(6.8, 1.1))), 2);
        }

        // Ba sinh viên học lực yếu (sẽ có GPA thấp, nhiều học phần F)
        foreach ($k2025->take(3) as $weak) {
            $this->ability[$weak->id] = 3.2;
        }

        // Năm học 2025–2026: khóa 2025 học kỳ 1, 2 theo CTĐT; trượt học kỳ 1 thì học lại ở học kỳ 2
        $failed = $this->pastTerm($hk1Past, $k2025, 1);
        $this->pastTerm($hk2Past, $k2025, 2, $failed);
        $hk1Past->update(['status' => TermStatus::LOCKED]);
        $hk2Past->update(['status' => TermStatus::LOCKED]);

        // Hai sinh viên bảo lưu từ học kỳ 1 2026–2027 (theo quyết định, có lịch sử)
        $statuses = app(StudentStatusService::class);
        foreach ($k2025->slice(10, 2) as $student) {
            $statuses->change($student->refresh(), StudentStatus::Suspended, [
                'decision_no' => 'QĐ-DEMO-'.$student->student_code,
                'effective_date' => '2026-08-20',
                'reason' => 'Bảo lưu kết quả học tập (dữ liệu demo)',
            ]);
        }

        // Học kỳ 1 2026–2027 đang diễn ra: khóa 2026 học kỳ 1, khóa 2025 học kỳ 3 của CTĐT
        $this->currentTerm($hk1Now, $k2026, 1, $k2025->filter(fn (Student $s) => $s->refresh()->status === StudentStatus::Studying), 3);
        $hk1Now->update(['status' => TermStatus::IN_PROGRESS]);

        $gpa = app(GpaService::class);
        $k2025->each(fn (Student $s) => $gpa->recalculate($s->refresh()));
    }

    /** @return array{0: Term, 1: Term, 2: Term} */
    private function terms(): array
    {
        $year = AcademicYear::query()->create(['name' => '2025-2026', 'start_date' => '2025-08-15', 'end_date' => '2026-08-14']);
        $hk1 = Term::query()->create(['academic_year_id' => $year->id, 'name' => 'Học kỳ 1', 'type' => TermType::MAIN, 'start_date' => '2025-09-01', 'end_date' => '2026-01-10', 'weeks' => 18, 'status' => TermStatus::COMPLETED]);
        $hk2 = Term::query()->create(['academic_year_id' => $year->id, 'name' => 'Học kỳ 2', 'type' => TermType::MAIN, 'start_date' => '2026-01-19', 'end_date' => '2026-06-14', 'weeks' => 20, 'status' => TermStatus::COMPLETED]);
        $now = Term::query()->whereHas('academicYear', fn ($q) => $q->where('name', '2026-2027'))->where('name', 'Học kỳ 1')->firstOrFail();

        return [$hk1, $hk2, $now];
    }

    private function adminClasses(int $year): void
    {
        $cohort = Cohort::query()->where('start_year', $year)->firstOrFail();
        $type = TrainingType::query()->where('code', 'CQ')->firstOrFail();

        foreach (Major::query()->orderBy('code')->get() as $major) {
            $initials = collect(preg_split('/\s+/u', Str::ascii($major->name)) ?: [])->filter()->map(fn (string $w) => Str::upper(Str::substr($w, 0, 1)))->implode('');

            AdminClass::query()->firstOrCreate(['code' => ($year % 100)."DH{$initials}01"], [
                'name' => "Lớp {$major->name} K{$year} - 01",
                'cohort_id' => $cohort->id,
                'major_id' => $major->id,
                'training_type_id' => $type->id,
                'max_size' => 60,
            ]);
        }
    }

    /** Đủ PER_MAJOR thí sinh mỗi ngành của khóa (Faker vi_VN) rồi xác nhận nhập học qua AdmissionService. @return Collection<int, Student> */
    private function admitCohort(int $year): Collection
    {
        $admissions = app(AdmissionService::class);

        foreach (Major::query()->orderBy('code')->get() as $major) {
            $have = Student::query()->where('cohort', $year)->where('major_id', $major->id)->count();

            if ($have < self::PER_MAJOR) {
                Student::factory()->cohort($year)->count(self::PER_MAJOR - $have)->create(['major_id' => $major->id]);
            }
        }

        Student::query()->where('cohort', $year)->where('status', StudentStatus::Pending)->orderBy('id')->get()
            ->each(fn (Student $s) => $admissions->admit($s, null, "{$year}-09-05"));

        return Student::query()->where('cohort', $year)->where('status', StudentStatus::Studying)->orderBy('student_code')->get();
    }

    private function demoPasswords(Collection $students): void
    {
        // Sinh viên và giảng viên demo (tài khoản giảng viên tạo bởi TeacherSeeder còn mật khẩu tạm ngẫu nhiên)
        $lecturers = Lecturer::query()->whereNotNull('user_id')->pluck('user_id');
        User::query()->whereIn('id', $students->pluck('user_id')->merge($lecturers))->where('must_change_password', true)->get()
            ->each(fn (User $u) => $u->forceFill([
                'password' => DevRoleUsersSeeder::PASSWORD,
                'must_change_password' => false,
                'temporary_password_expires_at' => null,
                'password_changed_at' => now(),
            ])->save());
    }

    /**
     * Học kỳ đã khóa: mở lớp cho các học phần học kỳ `$planned` của CTĐT, đăng ký sinh viên (Hoàn thành), nhập đủ điểm,
     * tính kết quả, công bố + khóa bảng điểm. `$retakes`: sinh viên => học phần trượt cần học lại trong học kỳ này.
     *
     * @return array<int, list<int>> sinh viên => học phần bị F trong học kỳ
     */
    private function pastTerm(Term $term, Collection $students, int $planned, array $retakes = []): array
    {
        $plan = $this->plan($students, $planned);

        foreach ($retakes as $studentId => $subjectIds) {
            foreach ($subjectIds as $subjectId) {
                $plan[$subjectId][] = ['student_id' => $studentId, 'type' => 'retake'];
            }
        }

        $failed = [];

        foreach ($plan as $subjectId => $subjectRows) {
            foreach ($this->split($subjectRows) as $sequence => $rows) {
                $section = $this->section($term, $subjectId, 'locked', $sequence);
                $enrollments = $this->enroll($section, $rows, 'completed', $term->start_date->toDateString());
                $components = $this->components($section);

                foreach ($enrollments as $enrollment) {
                    $this->scores($enrollment, $components, all: true);
                }

                // Một sinh viên vắng thi không lý do ở mỗi lớp học kỳ 1
                if ($planned === 1 && $enrollments->count() > 5) {
                    $absent = $enrollments->get(5);
                    $final = $components->firstWhere('kind.value', 'final_exam');
                    DB::table('grade_entries')->where('enrollment_id', $absent->id)->where('section_grade_component_id', $final?->id)->update(['score' => 0]);
                    DB::table('course_results')->updateOrInsert(['enrollment_id' => $absent->id], ['special_status' => 'absent_unexcused', 'special_reason' => 'Vắng thi không lý do (dữ liệu demo)', 'status' => 'incomplete', 'created_at' => now(), 'updated_at' => now()]);
                }

                $calculator = app(CourseGradeService::class);

                foreach ($enrollments as $enrollment) {
                    $result = $calculator->save($enrollment);

                    if ($result['is_passed'] === false) {
                        $failed[$enrollment->student_id][] = $subjectId;
                    }
                }

                SectionGradeWorkflow::query()->create([
                    'course_section_id' => $section->id,
                    'status' => 'locked',
                    'published_at' => $term->end_date->copy()->subDays(3),
                ]);
            }
        }

        return $failed;
    }

    /** Học kỳ đang diễn ra: đăng ký đã chốt, đã nhập chuyên cần / giữa kỳ; hai lớp đầu đủ điểm và đã nộp. */
    private function currentTerm(Term $term, Collection $first, int $firstPlanned, Collection $second, int $secondPlanned): void
    {
        $plan = $this->plan($first, $firstPlanned);

        foreach ($this->plan($second, $secondPlanned) as $subjectId => $rows) {
            $plan[$subjectId] = array_merge($plan[$subjectId] ?? [], $rows);
        }

        $calculator = app(CourseGradeService::class);
        $index = 0;

        foreach ($plan as $subjectId => $subjectRows) {
            foreach ($this->split($subjectRows) as $sequence => $rows) {
                $section = $this->section($term, $subjectId, 'in_progress', $sequence);
                $enrollments = $this->enroll($section, $rows, 'confirmed', '2026-08-20');
                $components = $this->components($section);
                $full = $index++ < 2;

                foreach ($enrollments as $enrollment) {
                    $this->scores($enrollment, $components, all: $full);
                }

                if ($full) {
                    $enrollments->each(fn (Enrollment $e) => $calculator->save($e));
                    SectionGradeWorkflow::query()->create(['course_section_id' => $section->id, 'status' => 'submitted']);
                }
            }
        }
    }

    /** @return array<int, list<array{student_id: int, type: string}>> học phần => sinh viên học theo CTĐT */
    private function plan(Collection $students, int $planned): array
    {
        $plan = [];

        foreach ($students as $student) {
            $curriculumId = DB::table('student_curricula')->where('student_id', $student->id)->orderByDesc('effective_from')->value('curriculum_id');
            $subjects = CurriculumSubject::query()->where('curriculum_id', $curriculumId)->where('planned_term', $planned)->pluck('subject_id');

            foreach ($subjects as $subjectId) {
                $plan[$subjectId][] = ['student_id' => $student->id, 'type' => 'normal'];
            }
        }

        ksort($plan);

        return $plan;
    }

    /**
     * Chia sinh viên của một học phần thành các lớp không quá SECTION_MAX, sĩ số đều nhau.
     *
     * @return array<int, list<array{student_id: int, type: string}>> số thứ tự lớp (từ 1) => sinh viên
     */
    private function split(array $rows): array
    {
        $size = (int) ceil(count($rows) / max(1, (int) ceil(count($rows) / self::SECTION_MAX)));

        return array_combine(range(1, (int) ceil(count($rows) / $size)), array_chunk($rows, $size));
    }

    private function section(Term $term, int $subjectId, string $status, int $sequence = 1): CourseSection
    {
        $subject = Subject::query()->findOrFail($subjectId);
        $code = sprintf('%02d%d', $term->academicYear->start_date->year % 100, $term->name === 'Học kỳ 1' ? 1 : 2);
        $section = CourseSection::query()->create([
            'subject_id' => $subject->id,
            'term_id' => $term->id,
            'sequence' => $sequence,
            'code' => sprintf('%s.%s.%02d', $subject->code, $code, $sequence),
            'min_size' => 15,
            'max_size' => self::SECTION_MAX,
            'language' => 'vi',
            'status' => $status,
        ]);
        $lecturer = Lecturer::query()->where('department_id', $subject->department_id)->working()->inRandomOrder()->first()
            ?? Lecturer::query()->working()->inRandomOrder()->first();

        if ($lecturer !== null) {
            SectionInstructor::query()->create(['course_section_id' => $section->id, 'lecturer_id' => $lecturer->id, 'role' => 'main']);
        }

        return $section;
    }

    /** @return Collection<int, Enrollment> */
    private function enroll(CourseSection $section, array $rows, string $status, string $registeredAt): Collection
    {
        $now = now();

        DB::table('enrollments')->insert(array_map(fn (array $row) => [
            'student_id' => $row['student_id'],
            'course_section_id' => $section->id,
            'term_id' => $section->term_id,
            'subject_id' => $section->subject_id,
            'type' => $row['type'],
            'status' => $status,
            'source' => 'staff',
            'registered_at' => $registeredAt.' 08:00:00',
            'created_at' => $now,
            'updated_at' => $now,
        ], $rows));

        return Enrollment::query()->where('course_section_id', $section->id)->orderBy('id')->get();
    }

    /** Sao cơ cấu điểm của học phần sang lớp (giống GradebookService::initialize, không qua kiểm tra quyền). */
    private function components(CourseSection $section): Collection
    {
        foreach (GradeComponentTemplate::query()->where('subject_id', $section->subject_id)->orderBy('sort_order')->get()->values() as $i => $template) {
            SectionGradeComponent::query()->create(array_merge($template->toComponent(), [
                'course_section_id' => $section->id,
                'grade_component_template_id' => $template->id,
                'version' => 1,
                'sort_order' => $i + 1,
            ]));
        }

        return SectionGradeComponent::query()->where('course_section_id', $section->id)->orderBy('sort_order')->get();
    }

    /** Điểm thành phần giả quanh năng lực của sinh viên; `$all` = false thì chưa có điểm thi cuối kỳ. */
    private function scores(Enrollment $enrollment, Collection $components, bool $all): void
    {
        $ability = $this->ability[$enrollment->student_id] ?? 6.5;
        $rows = [];

        foreach ($components as $component) {
            $final = $component->kind->value === 'final_exam';

            if ($final && ! $all) {
                continue;
            }

            $score = round(min(10, max(0, $this->normal($ability + ($component->kind->value === 'attendance' ? 1.5 : 0), $final ? 1.4 : 1.0))), 1);
            $rows[] = ['enrollment_id' => $enrollment->id, 'section_grade_component_id' => $component->id, 'score' => $score, 'status' => 'draft', 'created_at' => now(), 'updated_at' => now()];
        }

        DB::table('grade_entries')->insert($rows);
    }

    private function normal(float $mean, float $sd): float
    {
        $u = max(1e-9, mt_rand() / mt_getrandmax());
        $v = mt_rand() / mt_getrandmax();

        return $mean + $sd * sqrt(-2 * log($u)) * cos(2 * M_PI * $v);
    }
}
