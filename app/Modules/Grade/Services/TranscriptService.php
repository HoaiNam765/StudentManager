<?php

namespace App\Modules\Grade\Services;

use App\Modules\Enrollment\Enums\EnrollmentStatus;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grade\Enums\GradePublicationStatus;
use App\Modules\Grade\Models\CourseResult;
use App\Modules\Grade\Models\CumulativeGpa;
use App\Modules\Grade\Models\GradeEntry;
use App\Modules\Grade\Models\SectionGradeComponent;
use App\Modules\Grade\Models\SectionGradeWorkflow;
use App\Modules\Grade\Models\TermGpa;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Student\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

/**
 * Bảng điểm cá nhân và sổ điểm lớp học phần (FR-GRD-010, 011):
 * - `personal()`: chỉ học phần của lớp đã công bố / khóa, nhóm theo học kỳ, kèm GPA học kỳ / tích lũy đã lưu; tiếng Việt
 *   hoặc tiếng Anh (tên học phần tiếng Anh nếu có). Mã QR xác thực chờ module DOC (P2).
 * - `section()`: điểm thành phần, kết quả, trạng thái đặc biệt của từng sinh viên và thống kê (điểm trung bình, tỷ lệ đạt,
 *   phân bố điểm chữ); xuất PDF / Excel.
 */
class TranscriptService
{
    public function personal(Student $student, ?int $termId = null): array
    {
        $published = SectionGradeWorkflow::query()
            ->whereIn('status', [GradePublicationStatus::Published->value, GradePublicationStatus::Locked->value])
            ->select('course_section_id');

        $attempts = Enrollment::query()
            ->where('student_id', $student->id)
            ->whereNotIn('status', [EnrollmentStatus::Cancelled->value, EnrollmentStatus::Withdrawn->value])
            ->whereIn('course_section_id', $published)
            ->when($termId, fn ($q) => $q->where('term_id', $termId))
            ->with('subject:id,code,name,name_en,credits,counts_toward_gpa', 'term:id,name,academic_year_id,start_date', 'term.academicYear:id,name')
            ->get()
            ->sortBy(fn (Enrollment $e) => [$e->term?->start_date?->format('Y-m-d'), $e->id]);
        $results = CourseResult::query()->whereIn('enrollment_id', $attempts->pluck('id'))->get()->keyBy('enrollment_id');
        $termGpas = TermGpa::query()->where('student_id', $student->id)->get()->keyBy('term_id');
        $cumulative = CumulativeGpa::query()->where('student_id', $student->id)->first();

        $terms = $attempts->filter(fn (Enrollment $e) => $results->has($e->id))->groupBy('term_id')
            ->map(fn (Collection $group, $id) => [
                'term_id' => (int) $id,
                'name' => trim(($group->first()->term?->name ?? '').' – '.($group->first()->term?->academicYear?->name ?? ''), ' –'),
                'gpa' => $termGpas->get($id)?->only('gpa', 'credits_attempted', 'credits_earned', 'classification'),
                'rows' => $group->map(fn (Enrollment $e) => $this->row($e, $results->get($e->id)))->values()->all(),
            ])->values();

        return [
            'student' => $student->only('id', 'student_code', 'full_name', 'cohort'),
            'term_id' => $termId,
            'terms' => $terms->all(),
            'cumulative' => $cumulative?->only('gpa', 'credits_attempted', 'credits_earned', 'classification'),
            'verification_qr' => null, // Chờ DOC (P2); không tự tạo URL xác thực giả.
        ];
    }

    public function personalPdf(Student $student, ?int $termId, string $lang): string
    {
        return $this->pdf('pdf.grade-transcript', ['report' => $this->personal($student, $termId), 'lang' => $lang]);
    }

    public function section(CourseSection $section): array
    {
        $section->loadMissing('subject:id,code,name,credits', 'term:id,name,academic_year_id', 'term.academicYear:id,name');
        $components = SectionGradeComponent::query()->where('course_section_id', $section->id)->orderBy('sort_order')->get();
        $enrollments = Enrollment::query()->active()->where('course_section_id', $section->id)
            ->with('student:id,student_code,full_name')->get()
            ->sortBy(fn (Enrollment $e) => $e->student?->student_code)->values();
        $results = CourseResult::query()->whereIn('enrollment_id', $enrollments->pluck('id'))->get()->keyBy('enrollment_id');
        $entries = GradeEntry::query()->whereIn('enrollment_id', $enrollments->pluck('id'))->get()
            ->keyBy(fn (GradeEntry $e) => $e->enrollment_id.':'.$e->section_grade_component_id);

        $rows = $enrollments->map(fn (Enrollment $e) => [
            'student_code' => $e->student?->student_code,
            'student_name' => $e->student?->full_name,
            'scores' => $components->map(fn (SectionGradeComponent $c) => $entries->get($e->id.':'.$c->id)?->score)->all(),
        ] + $this->row($e, $results->get($e->id)));
        $graded = $rows->filter(fn (array $row) => $row['score10'] !== null);

        return [
            'section' => [
                'id' => $section->id, 'code' => $section->code,
                'subject' => $section->subject?->only('code', 'name', 'credits'),
                'term' => trim(($section->term?->name ?? '').' – '.($section->term?->academicYear?->name ?? ''), ' –'),
                'workflow' => SectionGradeWorkflow::query()->where('course_section_id', $section->id)->first()?->status?->label() ?? 'Nháp',
            ],
            'components' => $components->map(fn (SectionGradeComponent $c) => ['name' => $c->name, 'weight' => (float) $c->weight])->all(),
            'rows' => $rows->all(),
            'statistics' => [
                'students' => $rows->count(),
                'graded' => $graded->count(),
                'mean' => $graded->isEmpty() ? null : round($graded->avg(fn (array $r) => (float) $r['score10']), 2),
                'pass_rate' => $graded->isEmpty() ? null : round($graded->where('is_passed', true)->count() * 100 / $graded->count(), 1),
                'distribution' => $graded->groupBy('letter')->map->count()->sortKeys()->all(),
            ],
        ];
    }

    public function sectionPdf(CourseSection $section): string
    {
        return $this->pdf('pdf.grade-section', ['report' => $this->section($section)]);
    }

    /** @return array<string, mixed> một dòng kết quả học phần (ký hiệu trạng thái đặc biệt thay cho điểm khi loại trừ) */
    private function row(Enrollment $enrollment, ?CourseResult $result): array
    {
        $special = $result?->special_status !== null ? config("grade_special_statuses.{$result->special_status}") : null;

        return [
            'enrollment_id' => $enrollment->id,
            'subject_code' => $enrollment->subject?->code,
            'subject_name' => $enrollment->subject?->name,
            'subject_name_en' => $enrollment->subject?->name_en,
            'credits' => $enrollment->subject?->credits,
            'counts_toward_gpa' => (bool) $enrollment->subject?->counts_toward_gpa,
            'score10' => $result?->score10,
            'letter' => $result?->letter ?? ($special['symbol'] ?? null),
            'point4' => $result?->point4,
            'is_passed' => $result?->is_passed,
            'status' => $result?->status,
            'special_status' => $result?->special_status,
            'special_label' => $special['label'] ?? null,
        ];
    }

    private function pdf(string $view, array $data): string
    {
        $data['school'] = config('studentmanager.school.name');
        $data['font'] = config('studentmanager.export.pdf_font');
        $data['printed_at'] = now(config('studentmanager.display_timezone'))->format('H:i d/m/Y');

        return Pdf::loadView($view, $data)->setPaper('a4')->setOptions([
            'defaultFont' => $data['font'],
            'isRemoteEnabled' => false,
            'isPhpEnabled' => false,
        ])->output();
    }
}
