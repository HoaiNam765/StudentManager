<?php

namespace App\Modules\Enrollment\Services;

use App\Modules\AcademicYear\Models\Term;
use App\Modules\AcademicYear\Models\TermType;
use App\Modules\Curriculum\Models\Curriculum;
use App\Modules\Enrollment\Contracts\AcademicRecord;
use App\Modules\Enrollment\Models\RegistrationPeriod;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Services\StudentCurriculumService;
use App\Modules\System\Services\PolicyResolver;

/**
 * Khung tín chỉ một học kỳ của sinh viên (BR-ENR-02).
 *
 * Khung thường: trung bình một học kỳ của CTĐT (tổng tín chỉ / số học kỳ chuẩn) nhân tỷ lệ `registration.load_min_ratio`,
 * `registration.load_max_ratio` trong bộ quy chế của khóa (mặc định 2/3 và 3/2, làm tròn). Khung riêng (cấu hình
 * `studentmanager.enrollment`): cảnh báo học vụ → tối đa `probation_max_credits`; học kỳ cuối của CTĐT → tối đa
 * `final_term_max_credits`; học lực khá trở lên → cộng `good_standing_extra_credits`. Đợt có khung riêng thì dùng khung đợt.
 * Mức tối đa kiểm tra ở mỗi lần đăng ký; mức tối thiểu kiểm tra khi chốt (#113).
 */
class CreditLimits
{
    public function __construct(
        private readonly StudentCurriculumService $curricula,
        private readonly PolicyResolver $policies,
        private readonly AcademicRecord $record,
    ) {}

    /** @return array{min: int, max: int, basis: string} */
    public function for(Student $student, RegistrationPeriod $period): array
    {
        $curriculum = $this->curricula->current($student);
        $average = $curriculum !== null && $curriculum->standard_terms > 0 ? $curriculum->total_credits / $curriculum->standard_terms : 15;
        $min = (int) round($average * (float) $this->policies->value('registration.load_min_ratio', $student->cohort));
        $max = (int) round($average * (float) $this->policies->value('registration.load_max_ratio', $student->cohort));
        $basis = 'khung thường theo quy chế khóa';
        $config = config('studentmanager.enrollment');

        if ($this->record->onProbation($student)) {
            $max = min($max, (int) $config['probation_max_credits']);
            $basis = 'đang bị cảnh báo học vụ';
        } elseif ($curriculum !== null && $this->isFinalTerm($student, $curriculum, $period->term)) {
            $max = max($max, (int) $config['final_term_max_credits']);
            $basis = 'học kỳ cuối của CTĐT';
        } elseif ($this->record->goodStanding($student)) {
            $max += (int) $config['good_standing_extra_credits'];
            $basis = 'học lực khá trở lên';
        }

        if ($period->min_credits !== null) {
            $min = $period->min_credits;
        }

        if ($period->max_credits !== null) {
            $max = $period->max_credits;
            $basis = "khung riêng của đợt {$period->name}";
        }

        return ['min' => $min, 'max' => $max, 'basis' => $basis];
    }

    /** Học kỳ chính thứ mấy kể từ khi nhập học đã tới số học kỳ chuẩn của CTĐT. */
    private function isFinalTerm(Student $student, Curriculum $curriculum, Term $term): bool
    {
        if ($term->type !== TermType::MAIN) {
            return false;
        }

        $term->loadMissing('academicYear');
        $yearStart = (int) substr((string) $term->academicYear?->name, 0, 4);
        $earlier = Term::query()
            ->where('academic_year_id', $term->academic_year_id)
            ->where('type', TermType::MAIN)
            ->where('start_date', '<', $term->start_date)
            ->count();
        $ordinal = ($yearStart - $student->cohort) * 2 + $earlier + 1;

        return $ordinal >= $curriculum->standard_terms;
    }
}
