<?php

namespace App\Modules\Enrollment\Services;

use App\Models\User;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\Enrollment\Enums\EnrollmentStatus;
use App\Modules\Enrollment\Enums\PeriodStatus;
use App\Modules\Enrollment\Events\RegistrationFinalized;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Enrollment\Models\RegistrationPeriod;
use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Services\CourseSectionService;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLogger;
use App\Support\Services\BaseService;

/**
 * Chốt đăng ký học kỳ (FR-ENR-012; BR-CLS-05, BR-ENR-02, 08).
 *
 * Xem trước (`preview`): lớp còn dưới sĩ số tối thiểu (phải hủy / gộp hết trước, #108), sinh viên dưới mức tín chỉ tối
 * thiểu (phải xác nhận đã biết — `acknowledge_under_min` — vì có thể là trường hợp được phép), số đăng ký sẽ chốt.
 * Chốt: mọi đăng ký Đã đăng ký của học kỳ → Đã chốt, các đợt → Đã chốt, lớp Mở đăng ký → Đóng đăng ký; phát
 * `RegistrationFinalized` để FEE phát sinh công nợ. Sau đó sinh viên không tự hủy; cán bộ xử lý kèm lý do (BR-ENR-08).
 */
class RegistrationFinalizer extends BaseService
{
    public function __construct(
        private readonly CourseSectionService $sections,
        private readonly EnrollmentReport $report,
        private readonly AuditLogger $audit,
    ) {}

    /** @return array<string, mixed> */
    public function preview(Term $term): array
    {
        $stats = $this->report->stats($term);

        return [
            'term' => $term->only('id', 'name'),
            'already_finalized' => RegistrationPeriod::query()->where('term_id', $term->id)->where('status', PeriodStatus::Finalized)->exists(),
            'open_periods' => RegistrationPeriod::query()->where('term_id', $term->id)->openAt(now())->pluck('name'),
            'under_min_sections' => $this->sections->underMinSections($term),
            'students_under_min_credits' => $stats['students_under_min_credits'],
            'students_not_registered' => count($stats['students_not_registered']),
            'to_confirm' => Enrollment::query()->where('term_id', $term->id)->where('status', EnrollmentStatus::Registered)->count(),
        ];
    }

    /** @return array{confirmed: int, periods: int, sections_closed: int} */
    public function finalize(Term $term, bool $acknowledgeUnderMin = false, ?User $actor = null): array
    {
        $preview = $this->preview($term);

        if ($preview['already_finalized']) {
            $this->fail("Đăng ký {$term->name} đã chốt.", 'Sau khi chốt, thay đổi do Phòng Đào tạo đăng ký / hủy thay từng trường hợp (BR-ENR-08).');
        }

        if ($preview['open_periods']->isNotEmpty()) {
            $this->fail('Còn đợt đăng ký đang mở: '.$preview['open_periods']->implode(', ').'.', 'Chờ đợt đóng hoặc sửa thời gian đóng của đợt rồi mới chốt.');
        }

        if ($preview['under_min_sections'] !== []) {
            $codes = collect($preview['under_min_sections'])->pluck('code')->implode(', ');
            $this->fail("Còn lớp dưới sĩ số tối thiểu: {$codes}.", 'Hủy hoặc gộp các lớp này trước khi chốt (BR-CLS-05).');
        }

        if ($preview['students_under_min_credits'] !== [] && ! $acknowledgeUnderMin) {
            $this->fail(
                count($preview['students_under_min_credits']).' sinh viên đăng ký dưới mức tín chỉ tối thiểu.',
                'Đăng ký thay cho các sinh viên này, hoặc gửi acknowledge_under_min = true nếu đã xem và chấp nhận (BR-ENR-02).'
            );
        }

        $result = $this->transaction(function () use ($term): array {
            $confirmed = Enrollment::query()->where('term_id', $term->id)->where('status', EnrollmentStatus::Registered)->lockForUpdate()->get();
            $confirmed->each(fn (Enrollment $e) => $e->update(['status' => EnrollmentStatus::Confirmed]));

            $periods = RegistrationPeriod::query()->where('term_id', $term->id)->where('status', PeriodStatus::Published)->get();
            $periods->each(fn (RegistrationPeriod $p) => $p->update(['status' => PeriodStatus::Finalized]));

            $sections = CourseSection::query()->where('term_id', $term->id)->where('status', SectionStatus::RegistrationOpen)->get();
            $sections->each(fn (CourseSection $s) => $this->sections->advance($s, SectionStatus::RegistrationClosed));

            return ['confirmed' => $confirmed->count(), 'periods' => $periods->count(), 'sections_closed' => $sections->count()];
        });

        $this->audit->record(AuditEvent::Locked, $term, [], ['registration' => 'finalized', ...$result], 'Chốt đăng ký học phần');
        RegistrationFinalized::dispatch($term, $result['confirmed']);

        return $result;
    }
}
