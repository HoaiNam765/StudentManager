<?php

namespace App\Modules\Enrollment\Services;

use App\Modules\Enrollment\Contracts\AcademicRecord;
use App\Modules\Enrollment\Contracts\TuitionStatus;
use App\Modules\Enrollment\Models\RegistrationPeriod;
use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Student\Models\Student;
use App\Modules\Subject\Models\Subject;
use App\Modules\Subject\Services\SubjectRelationService;
use App\Modules\Timetable\Contracts\SectionStudents;
use App\Modules\Timetable\Services\ScheduleConflictService;
use App\Modules\Timetable\Services\SectionStartDates;
use Illuminate\Support\Collection;

/**
 * Kiểm tra điều kiện đăng ký một lớp học phần (FR-ENR-004). Trả danh sách lý do từ chối, mỗi lý do nêu cụ thể và cách
 * khắc phục (UX-09); rỗng là đăng ký được. #105 gọi trong giao dịch trước khi ghi nhận; danh sách lớp (#103) gọi để đánh dấu.
 *
 * Thứ tự: đợt và sinh viên (BR-ENR-01) → lớp (mở đăng ký, còn chỗ, đúng học kỳ của đợt) → học phần đã đạt (BR-ENR-07)
 * → cùng học phần hai lớp, trùng lịch (BR-ENR-04) → tiên quyết, học trước, song hành (BR-ENR-03) → tín chỉ tối đa
 * (BR-ENR-02) → công nợ quá hạn (BR-ENR-09, khi bật).
 *
 * "Đã đăng ký" của sinh viên lấy qua `SectionStudents::otherSectionsOf()` (ENR bind ở #105). Đổi lớp truyền `$replacing`
 * để bỏ lớp cũ khỏi các phép so.
 */
class EnrollmentRules
{
    public const TYPE_NORMAL = 'normal';

    public const TYPE_RETAKE = 'retake';

    public const TYPE_IMPROVE = 'improve';

    public function __construct(
        private readonly RegistrationAccess $access,
        private readonly SectionStudents $students,
        private readonly AcademicRecord $record,
        private readonly TuitionStatus $tuition,
        private readonly SubjectRelationService $relations,
        private readonly ScheduleConflictService $conflicts,
        private readonly CreditLimits $limits,
        private readonly SectionStartDates $startDates,
    ) {}

    /** @var array<string, array<int, array{backlog: bool, planned_term: ?int}>> lớp trong phạm vi theo sinh viên – đợt */
    private array $scopes = [];

    /** @return array<int, array{backlog: bool, planned_term: ?int}> */
    public function scopeFor(Student $student, RegistrationPeriod $period): array
    {
        return $this->scopes["{$student->id}-{$period->id}"] ??= app(FrameworkPeriodScope::class)->sectionsFor($student, $period);
    }

    /**
     * @param  list<string>  $skip  mã quy tắc được bỏ qua khi cán bộ đăng ký thay có phê duyệt (FR-ENR-009, #113):
     *                              period, capacity, prerequisite, schedule, credits, debt
     * @return list<string>
     */
    public function check(
        Student $student,
        CourseSection $section,
        RegistrationPeriod $period,
        string $type = self::TYPE_NORMAL,
        ?CourseSection $replacing = null,
        array $skip = [],
    ): array {
        $section->loadMissing('subject');
        $reasons = [];

        // Không cho dùng đợt khác cùng học kỳ để vượt giới hạn khóa hoặc thời gian của lớp.
        if (($section->registration_period_id !== null && $section->registration_period_id !== $period->id)
            || ($period->usesFrameworks() && $section->registration_period_id !== $period->id)) {
            $reasons[] = 'Lớp học phần không thuộc danh sách được mở trong đợt đăng ký này.';
        } elseif ($period->usesFrameworks() && ! in_array('period', $skip, true)
            && ! in_array($section->subject_id, $this->record->passedSubjectIds($student), true)
            && ! array_key_exists($section->id, $this->scopeFor($student, $period))) {
            // Đợt theo khung: chỉ lớp của khung khoa – khóa mình và lớp học bù môn CTĐT kỳ trước chưa đạt
            $reasons[] = 'Lớp không dành cho khoa – khóa của bạn. Chỉ đăng ký lớp của khung khoa – khóa mình hoặc môn kỳ trước chưa học / chưa đạt.';
        }

        if (! in_array('period', $skip, true)) {
            $reasons = [...$reasons, ...$this->access->problems($period, $student)];
        }

        $reasons = [...$reasons, ...$this->sectionReasons($section, $period, ! in_array('capacity', $skip, true), in_array('period', $skip, true))];

        $current = $this->currentSections($student, $section, $replacing);
        $passed = $this->record->passedSubjectIds($student);

        // Học phần đã đạt: chỉ học cải thiện (BR-ENR-07)
        if (in_array($section->subject_id, $passed, true) && $type !== self::TYPE_IMPROVE) {
            $reasons[] = "Bạn đã đạt học phần {$section->subject->name}. Muốn nâng điểm thì đăng ký loại \"học cải thiện\".";
        }

        if ($type === self::TYPE_IMPROVE && ! in_array($section->subject_id, $passed, true)) {
            $reasons[] = "Học phần {$section->subject->name} chưa đạt nên không đăng ký học cải thiện được; đăng ký học lần đầu hoặc học lại.";
        }

        // Cùng học phần hai lớp (BR-ENR-04)
        $same = $current->firstWhere('subject_id', $section->subject_id);

        if ($same !== null && ! config('studentmanager.enrollment.allow_same_subject_twice')) {
            $reasons[] = "Bạn đã đăng ký lớp {$same->code} của học phần {$section->subject->name}. Dùng chức năng đổi lớp thay vì đăng ký thêm.";
        }

        // Trùng lịch (BR-ENR-04, BR-TTB-01)
        if (! in_array('schedule', $skip, true)) {
            foreach ($current as $other) {
                $clash = $this->conflicts->sectionsClash($section, $other);

                if ($clash !== null) {
                    $reasons[] = "Trùng lịch: {$clash}. Chọn lớp khác của học phần hoặc hủy lớp đang trùng.";
                }
            }
        }

        // Tiên quyết, học trước, song hành (BR-ENR-03)
        if (! in_array('prerequisite', $skip, true)) {
            $reasons = [...$reasons, ...$this->requirementReasons($student, $section->subject, $current, $passed)];
        }

        // Tín chỉ tối đa (BR-ENR-02)
        if (! in_array('credits', $skip, true)) {
            $limits = $this->limits->for($student, $period);
            $total = $current->sum(fn (CourseSection $s) => (int) $s->subject->credits) + (int) $section->subject->credits;

            if ($total > $limits['max']) {
                $reasons[] = "Vượt khối lượng tối đa: sau khi đăng ký là {$total} tín chỉ, tối đa {$limits['max']} tín chỉ ({$limits['basis']}). Hủy bớt học phần khác trước.";
            }
        }

        // Công nợ quá hạn (BR-ENR-09, bật bằng cấu hình)
        if (! in_array('debt', $skip, true) && config('studentmanager.enrollment.block_on_debt')) {
            $overdue = $this->tuition->overdueAmount($student);

            if ($overdue > (int) config('studentmanager.enrollment.debt_threshold')) {
                $reasons[] = 'Bạn còn nợ học phí quá hạn '.number_format($overdue, 0, ',', '.').' đồng. Hoàn thành học phí rồi đăng ký lại.';
            }
        }

        return array_values(array_unique($reasons));
    }

    /**
     * Lý do thuộc về lớp (không phụ thuộc sinh viên): trạng thái, học kỳ, sĩ số (BR-ENR-05). `$outsidePeriod` = cán bộ
     * đăng ký thay ngoài đợt (bỏ qua "period", FR-ENR-009): nhận cả lớp Đóng đăng ký / Đang học.
     */
    public function sectionReasons(CourseSection $section, RegistrationPeriod $period, bool $checkCapacity = true, bool $outsidePeriod = false): array
    {
        $reasons = [];

        if ($section->term_id !== $period->term_id) {
            $reasons[] = "Lớp {$section->code} không thuộc học kỳ của đợt {$period->name}.";
        }

        $accepted = $outsidePeriod
            ? [SectionStatus::RegistrationOpen, SectionStatus::RegistrationClosed, SectionStatus::InProgress]
            : [SectionStatus::RegistrationOpen];

        if (! in_array($section->status, $accepted, true)) {
            $reasons[] = "Lớp {$section->code} đang ở trạng thái {$section->status->label()}, chưa / không còn mở đăng ký. Chọn lớp khác của học phần.";
        }

        if ($checkCapacity && $this->remaining($section) <= 0) {
            $reasons[] = "Lớp {$section->code} đã đủ {$section->max_size} sinh viên. Chọn lớp khác của học phần.";
        }

        // Ngày bắt đầu học phải sau ngày đóng đăng ký của đợt (cán bộ đăng ký thay ngoài đợt thì bỏ qua)
        if (! $outsidePeriod) {
            $starts = $this->startDates->of($section->id);
            $closes = $period->closes_at->setTimezone(config('studentmanager.display_timezone'))->startOfDay();

            if ($starts !== null && $starts->toDateString() <= $closes->toDateString()) {
                $reasons[] = "Lớp {$section->code} bắt đầu học ngày {$starts->format('d/m/Y')}, không sau ngày đóng đăng ký ({$closes->format('d/m/Y')}). "
                    .'Chọn lớp khác của học phần, hoặc liên hệ Phòng Đào tạo để dời lịch học / thời gian đăng ký.';
            }
        }

        return $reasons;
    }

    public function remaining(CourseSection $section): int
    {
        return max(0, $section->max_size - count($this->students->studentIdsOf($section)));
    }

    /** @return Collection<int, CourseSection> lớp sinh viên đã đăng ký cùng học kỳ, bỏ lớp đang xét và lớp sắp đổi */
    public function currentSections(Student $student, CourseSection $section, ?CourseSection $replacing = null): Collection
    {
        return $this->students->otherSectionsOf($student->id, $section)
            ->reject(fn (CourseSection $s) => $s->id === $section->id || $s->id === $replacing?->id)
            ->each(fn (CourseSection $s) => $s->loadMissing('subject'))
            ->values();
    }

    /**
     * @param  Collection<int, CourseSection>  $current
     * @param  list<int>  $passed
     * @return list<string>
     */
    private function requirementReasons(Student $student, Subject $subject, Collection $current, array $passed): array
    {
        $requirements = $this->relations->requirementsOf($subject);
        $attempted = $this->record->attemptedSubjectIds($student);
        $registered = $current->pluck('subject_id')->all();
        $reasons = [];

        $missing = $requirements['prerequisite']->reject(fn (Subject $s) => in_array($s->id, $passed, true));

        if ($missing->isNotEmpty()) {
            $reasons[] = 'Thiếu học phần tiên quyết: '.$missing->map(fn (Subject $s) => "{$s->name} ({$s->code})")->implode(', ')
                .'. Phải đạt các học phần này trước (BR-ENR-03).';
        }

        $notTaken = $requirements['prior']->reject(fn (Subject $s) => in_array($s->id, $attempted, true) || in_array($s->id, $passed, true));

        if ($notTaken->isNotEmpty()) {
            $reasons[] = 'Chưa học trước: '.$notTaken->map(fn (Subject $s) => "{$s->name} ({$s->code})")->implode(', ')
                .'. Phải học (không cần đạt) các học phần này trước (BR-ENR-03).';
        }

        $coMissing = $requirements['corequisite']->reject(fn (Subject $s) => in_array($s->id, $passed, true) || in_array($s->id, $attempted, true) || in_array($s->id, $registered, true));

        if ($coMissing->isNotEmpty()) {
            $reasons[] = 'Học phần song hành chưa đăng ký: '.$coMissing->map(fn (Subject $s) => "{$s->name} ({$s->code})")->implode(', ')
                .'. Đăng ký học phần song hành trước hoặc cùng lúc (BR-ENR-03).';
        }

        return $reasons;
    }
}
