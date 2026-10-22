<?php

namespace App\Modules\Grade\Services;

use App\Modules\Enrollment\Enums\EnrollmentStatus;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grade\Enums\GradePublicationStatus;
use App\Modules\Grade\Models\CourseResult;
use App\Modules\Grade\Models\CumulativeGpa;
use App\Modules\Grade\Models\SectionGradeWorkflow;
use App\Modules\Grade\Models\TermGpa;
use App\Modules\Student\Models\Student;
use App\Modules\System\Services\PolicyResolver;
use App\Support\Services\BaseService;
use Illuminate\Support\Collection;

/**
 * GPA học kỳ và tích lũy, xếp loại học lực (FR-GRD-008, 009, 013; BR-GRD-04).
 *
 * - Chỉ tính lớp đã công bố / khóa điểm; không tính học phần điều kiện (`counts_toward_gpa` = false), học phần rút,
 *   học phần Đạt / Không đạt (không có điểm hệ 4), trạng thái loại trừ (miễn, công nhận, hoãn thi…). Vắng thi không lý
 *   do / cấm thi tính như điểm đã ghi (thi cuối kỳ = 0).
 * - GPA học kỳ = Σ(hệ 4 × tín chỉ) ÷ Σ tín chỉ của các học phần đăng ký trong kỳ (kể cả F). GPA tích lũy lấy kết quả
 *   cao nhất của mỗi học phần (mặc định) hoặc lần gần nhất (`grade_gpa.retake_policy`). Tín chỉ tích lũy = tín chỉ các
 *   học phần đạt (kể cả học phần Đạt / Không đạt) hoặc được miễn / công nhận, mỗi học phần tính một lần.
 * - Xếp loại theo `classification.bands` của bộ quy chế áp dụng cho khóa sinh viên.
 * Lưu `term_gpas`, `cumulative_gpas`; chạy lại khi công bố / điều chỉnh điểm (listener) hoặc lệnh `grades:recalculate-gpa`.
 */
class GpaService extends BaseService
{
    public function __construct(private readonly PolicyResolver $policies) {}

    /** @return array{terms: list<TermGpa>, cumulative: CumulativeGpa} */
    public function recalculate(Student $student): array
    {
        return $this->transaction(function () use ($student): array {
            $attempts = $this->publishedAttempts($student);
            $results = CourseResult::query()->whereIn('enrollment_id', $attempts->pluck('id'))->get()->keyBy('enrollment_id');
            $specials = (array) config('grade_special_statuses', []);

            // Lần học có điểm hệ 4 được tính vào GPA
            $counted = $attempts->filter(function (Enrollment $e) use ($results, $specials): bool {
                $result = $results->get($e->id);

                if ($result === null || ! $e->subject?->counts_toward_gpa) {
                    return false;
                }

                if ($result->special_status !== null && ($specials[$result->special_status]['gpa'] ?? 'exclude') === 'exclude') {
                    return false;
                }

                return $result->point4 !== null;
            });

            // Tín chỉ tích lũy: học phần đạt, hoặc miễn / công nhận (`earns_credit`)
            $earns = fn (Enrollment $e): bool => (bool) $results->get($e->id)?->is_passed
                || (bool) ($specials[$results->get($e->id)?->special_status ?? '']['earns_credit'] ?? false);

            $termRows = [];

            foreach ($attempts->groupBy('term_id') as $termId => $group) {
                $inTerm = $counted->where('term_id', $termId);
                $gpa = $this->weighted($inTerm, $results);
                $termRows[] = TermGpa::query()->updateOrCreate(['student_id' => $student->id, 'term_id' => $termId], [
                    'gpa' => $gpa,
                    'credits_attempted' => $inTerm->sum(fn (Enrollment $e) => (int) $e->subject->credits),
                    'credits_earned' => $group->filter($earns)->sum(fn (Enrollment $e) => (int) $e->subject->credits),
                    'classification' => $this->classification($gpa, $student),
                ]);
            }

            $selected = $this->selectAttempts($counted, $results);
            $cumulative = $this->weighted($selected, $results);
            $earned = $attempts->filter($earns)->unique('subject_id')->sum(fn (Enrollment $e) => (int) $e->subject->credits);

            $cgpa = CumulativeGpa::query()->updateOrCreate(['student_id' => $student->id], [
                'gpa' => $cumulative,
                'credits_attempted' => $selected->sum(fn (Enrollment $e) => (int) $e->subject->credits),
                'credits_earned' => $earned,
                'classification' => $this->classification($cumulative, $student),
            ]);

            return ['terms' => $termRows, 'cumulative' => $cgpa];
        });
    }

    /** @return Collection<int, Enrollment> lần học của lớp đã công bố / khóa điểm, bỏ đăng ký hủy / rút */
    private function publishedAttempts(Student $student): Collection
    {
        $published = SectionGradeWorkflow::query()
            ->whereIn('status', [GradePublicationStatus::Published->value, GradePublicationStatus::Locked->value])
            ->select('course_section_id');

        return Enrollment::query()
            ->with('subject:id,code,name,credits,counts_toward_gpa')
            ->where('student_id', $student->id)
            ->whereNotIn('status', [EnrollmentStatus::Cancelled->value, EnrollmentStatus::Withdrawn->value])
            ->whereIn('course_section_id', $published)
            ->orderBy('term_id')->orderBy('id')
            ->get();
    }

    /**
     * Mỗi học phần một lần học cho GPA tích lũy: điểm hệ 4 cao nhất (bằng nhau thì lần sau) hoặc lần gần nhất.
     *
     * @param  Collection<int, Enrollment>  $counted
     * @return Collection<int, Enrollment>
     */
    private function selectAttempts(Collection $counted, Collection $results): Collection
    {
        $policy = (string) config('grade_gpa.retake_policy', 'highest');

        if (! in_array($policy, ['highest', 'latest'], true)) {
            $this->fail("Cấu hình GRD_RETAKE_POLICY = \"{$policy}\" không hợp lệ.", 'Đặt GRD_RETAKE_POLICY là highest hoặc latest.');
        }

        return $counted->groupBy('subject_id')->map(fn (Collection $group) => $policy === 'latest'
            ? $group->sortBy(fn (Enrollment $e) => sprintf('%012d%012d', $e->term_id, $e->id))->last()
            : $group->sortBy(fn (Enrollment $e) => sprintf('%06.2f%012d', (float) $results->get($e->id)->point4, $e->id))->last()
        )->values();
    }

    private function weighted(Collection $attempts, Collection $results): ?float
    {
        $credits = $attempts->sum(fn (Enrollment $e) => (int) $e->subject->credits);

        if ($credits <= 0) {
            return null;
        }

        $points = $attempts->sum(fn (Enrollment $e) => (int) $e->subject->credits * (float) $results->get($e->id)->point4);

        return round($points / $credits, 2);
    }

    private function classification(?float $gpa, Student $student): ?string
    {
        if ($gpa === null) {
            return null;
        }

        $bands = collect((array) $this->policies->value('classification.bands', (int) $student->cohort))
            ->sortByDesc(fn (array $band) => (float) $band['min']);

        return $bands->first(fn (array $band) => $gpa >= (float) $band['min'])['label'] ?? null;
    }
}
