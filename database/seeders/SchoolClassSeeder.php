<?php

namespace Database\Seeders;

use App\Modules\AcademicYear\Models\Term;
use App\Modules\Curriculum\Models\Curriculum;
use App\Modules\Curriculum\Models\CurriculumSubject;
use App\Modules\Faculty\Models\Major;
use App\Modules\Faculty\Models\TrainingType;
use App\Modules\SchoolClass\Models\AdminClass;
use App\Modules\SchoolClass\Models\Cohort;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\SectionInstructor;
use App\Modules\Subject\Models\Subject;
use App\Modules\Teacher\Models\Lecturer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Khóa và lớp hành chính mẫu (CLS): khóa 14DH–16DH (nhập học 2024–2026, `Cohort::codeFor`); khóa 16DH mỗi ngành một lớp
 * hệ chính quy, mã = mã khóa + mã khoa + số thứ tự ngành trong khoa (`adminClass`, ví dụ 16DHCNTT01 ngành Công nghệ thông
 * tin, 16DHCNTT02 ngành Kỹ thuật phần mềm). Lớp học phần mẫu (#107): học kỳ 2 năm 2026–2027, mỗi học phần học kỳ 2 của các CTĐT
 * đang áp dụng một lớp Dự kiến, giảng viên chính là giảng viên đầu tiên của bộ môn quản lý (bộ môn chưa có giảng viên thì
 * lần lượt giảng viên toàn trường). Tạo thẳng bằng model
 * (bỏ qua kiểm tra đề cương vì dữ liệu mẫu chưa có đề cương đã duyệt). Chạy lặp lại an toàn.
 */
class SchoolClassSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([2024, 2025, 2026] as $year) {
            Cohort::firstOrCreate(['start_year' => $year], [
                'code' => Cohort::codeFor($year),
                'name' => 'Khóa '.($year - Cohort::NUMBER_OFFSET)." ({$year}–".($year + 4).')',
                'expected_end_year' => $year + 4,
            ]);
        }

        $cohort = Cohort::query()->where('start_year', 2026)->first();
        $type = TrainingType::query()->where('code', 'CQ')->first();

        if ($cohort === null || $type === null) {
            return;
        }

        foreach (Major::query()->orderBy('code')->get() as $major) {
            AdminClass::firstOrCreate(['code' => self::adminClassCode($cohort, $major)], [
                'name' => "Lớp {$major->name} {$cohort->code}",
                'cohort_id' => $cohort->id,
                'major_id' => $major->id,
                'training_type_id' => $type->id,
                'max_size' => 60,
            ]);
        }

        $this->seedSections();
    }

    /**
     * Mã lớp hành chính mẫu: mã khóa + mã khoa + số thứ tự của ngành trong khoa (theo tên ngành), ví dụ 16DHCNTT01
     * (Công nghệ thông tin), 16DHCNTT02 (Kỹ thuật phần mềm), 16DHNN01 (Ngôn ngữ Anh).
     */
    public static function adminClassCode(Cohort $cohort, Major $major): string
    {
        $major->loadMissing('faculty');
        $order = Major::query()->where('faculty_id', $major->faculty_id)->get()
            ->sortBy(fn (Major $m) => Str::ascii($m->name))->pluck('id')->search($major->id);

        return sprintf('%s%s%02d', $cohort->code, $major->faculty->code, $order + 1);
    }

    private function seedSections(): void
    {
        $term = Term::query()->where('name', 'Học kỳ 2')->orderBy('start_date')->first();

        if ($term === null) {
            return;
        }

        $subjectIds = CurriculumSubject::query()
            ->whereIn('curriculum_id', Curriculum::query()->inForce()->select('id'))
            ->where('planned_term', 2)
            ->distinct()
            ->pluck('subject_id');

        $yy = sprintf('%02d%d', ((int) $term->start_date->format('Y') - 1) % 100, 2);
        // Bộ môn chưa có giảng viên (khoa Khoa học cơ bản): lần lượt giảng viên toàn trường theo mã, như DemoDataSeeder
        $schoolWide = Lecturer::query()->working()->where('code', '!=', DevRoleUsersSeeder::ADVISOR_CODE)->orderBy('code')->get()->values();
        $turn = 0;

        foreach (Subject::query()->whereIn('id', $subjectIds)->orderBy('code')->get() as $subject) {
            $section = CourseSection::firstOrCreate(['subject_id' => $subject->id, 'term_id' => $term->id, 'sequence' => 1], [
                'code' => "{$subject->code}.{$yy}.01",
                'min_size' => 15,
                'max_size' => 60,
                'language' => 'vi',
                'status' => 'planned',
            ]);

            $lecturer = Lecturer::query()->where('department_id', $subject->department_id)->working()->orderBy('code')->first()
                ?? ($schoolWide->isEmpty() ? null : $schoolWide[$turn++ % $schoolWide->count()]);

            if ($lecturer !== null) {
                SectionInstructor::firstOrCreate(['course_section_id' => $section->id, 'lecturer_id' => $lecturer->id], ['role' => 'main']);
            }
        }
    }
}
