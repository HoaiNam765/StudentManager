<?php

namespace App\Modules\Grade\Services;

use App\Modules\Enrollment\Contracts\AcademicRecord;
use App\Modules\Enrollment\Enums\EnrollmentStatus;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grade\Enums\GradePublicationStatus;
use App\Modules\Grade\Models\SectionGradeWorkflow;
use App\Modules\Grade\Models\TermGpa;
use App\Modules\Student\Models\Student;
use App\Modules\System\Services\PolicyResolver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Kết quả học tập cho ENR (BR-ENR-02, 03, 07) từ điểm đã công bố / khóa: học phần đạt theo `course_results.is_passed`
 * (BR-GRD-03) hoặc được miễn / công nhận, học phần đã học (có kết quả, kể cả F), cảnh báo học vụ tạm theo ngưỡng GPA học kỳ gần nhất của bộ quy
 * chế (`academic_warning.term_gpa_first` / `term_gpa_other`), học lực khá trở lên theo GPA học kỳ gần nhất ≥ mức "Khá"
 * của `classification.bands`.
 */
class GradeAcademicRecord implements AcademicRecord
{
    public function __construct(private readonly PolicyResolver $policies) {}

    public function passedSubjectIds(Student $student): array
    {
        // Miễn / công nhận (`earns_credit`) coi như đã đạt
        $credited = array_keys(array_filter((array) config('grade_special_statuses', []), fn (array $s) => $s['earns_credit'] ?? false));

        return $this->results($student)
            ->where(fn ($q) => $q->where('course_results.is_passed', true)->orWhereIn('course_results.special_status', $credited))
            ->distinct()->pluck('enrollments.subject_id')->map(fn ($id) => (int) $id)->all();
    }

    public function attemptedSubjectIds(Student $student): array
    {
        return $this->results($student)
            ->where(fn ($q) => $q->whereNotNull('course_results.is_passed')->orWhereNotNull('course_results.special_status'))
            ->distinct()->pluck('enrollments.subject_id')->map(fn ($id) => (int) $id)->all();
    }

    public function onProbation(Student $student): bool
    {
        $terms = $this->termGpas($student);
        $latest = $terms->first();

        if ($latest === null || $latest->gpa === null) {
            return false;
        }

        $threshold = (float) $this->policies->value($terms->count() === 1 ? 'academic_warning.term_gpa_first' : 'academic_warning.term_gpa_other', (int) $student->cohort);

        return (float) $latest->gpa < $threshold;
    }

    public function goodStanding(Student $student): bool
    {
        $latest = $this->termGpas($student)->first();

        if ($latest === null || $latest->gpa === null) {
            return false;
        }

        $good = collect((array) $this->policies->value('classification.bands', (int) $student->cohort))
            ->first(fn (array $band) => mb_strtolower($band['label']) === 'khá');

        return (float) $latest->gpa >= (float) ($good['min'] ?? 2.5);
    }

    private function results(Student $student): Builder
    {
        $published = SectionGradeWorkflow::query()
            ->whereIn('status', [GradePublicationStatus::Published->value, GradePublicationStatus::Locked->value])
            ->select('course_section_id');

        return Enrollment::query()
            ->join('course_results', 'course_results.enrollment_id', '=', 'enrollments.id')
            ->where('enrollments.student_id', $student->id)
            ->whereNotIn('enrollments.status', [EnrollmentStatus::Cancelled->value, EnrollmentStatus::Withdrawn->value])
            ->whereIn('enrollments.course_section_id', $published);
    }

    /** @return Collection<int, TermGpa> học kỳ mới nhất trước */
    private function termGpas(Student $student)
    {
        return TermGpa::query()
            ->join('terms', 'terms.id', '=', 'term_gpas.term_id')
            ->where('term_gpas.student_id', $student->id)
            ->orderByDesc('terms.start_date')
            ->get(['term_gpas.*']);
    }
}
