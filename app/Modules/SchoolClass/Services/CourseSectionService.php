<?php

namespace App\Modules\SchoolClass\Services;

use App\Modules\AcademicYear\Models\Term;
use App\Modules\AcademicYear\Models\TermStatus;
use App\Modules\Curriculum\Services\CurriculumCatalog;
use App\Modules\Enrollment\Services\OfferingLecturers;
use App\Modules\Room\Models\Room;
use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Events\CourseSectionCancelled;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\SectionInstructor;
use App\Modules\Subject\Models\Subject;
use App\Modules\Subject\Services\SyllabusService;
use App\Modules\Teacher\Models\Lecturer;
use App\Modules\Timetable\Contracts\SectionStudents;
use App\Modules\Timetable\Models\ScheduleRule;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLogger;
use App\Support\References\ReferenceRegistry;
use App\Support\Services\BaseService;
use Illuminate\Support\Facades\DB;

/**
 * Mở lớp học phần, phân công giảng viên và vòng đời lớp (FR-CLS-009..012; BR-CLS-02, 03, 04; UAT-05).
 *
 * - Chỉ mở lớp cho học phần đang hoạt động, thuộc ít nhất một CTĐT đang áp dụng (BR-CLS-03, `CurriculumCatalog`) và có
 *   đề cương áp dụng được (BR-SUB-08, theo cấu hình). Học kỳ chưa kết thúc / chưa khóa.
 * - Mã tự sinh `<mã học phần>.<năm><thứ tự học kỳ>.<số thứ tự>` (ví dụ INT101.261.01), duy nhất (FR-CLS-010).
 * - Sĩ số tối thiểu ≤ tối đa; tối đa không vượt mức cấu hình và sức chứa phòng được xếp (BR-CLS-02).
 * - Giảng viên đang làm việc; vượt định mức tiết trong học kỳ thì cảnh báo và phải xác nhận (`confirm_overload`).
 * - Phải có ít nhất một giảng viên chính trước khi Mở đăng ký (BR-CLS-04). Lớp trực tiếp / kết hợp còn phải có mẫu lịch
 *   (TTB xếp lịch trước khi mở đăng ký — luồng CLS bước 3, FR-TTB-002); lớp trực tuyến không bắt buộc. Trạng thái chỉ đi
 *   tới bước kế tiếp; hủy được ở ba bước đầu và phải có lý do. Xóa hẳn chỉ lớp Dự kiến chưa có dữ liệu liên quan (BR-CLS-06).
 */
class CourseSectionService extends BaseService
{
    public function __construct(
        private readonly CurriculumCatalog $catalog,
        private readonly SyllabusService $syllabi,
        private readonly ReferenceRegistry $references,
        private readonly AuditLogger $audit,
    ) {}

    /** @param  array<string, mixed>  $data */
    public function open(array $data): CourseSection
    {
        $subject = Subject::query()->findOrFail($data['subject_id']);
        $term = Term::query()->with('academicYear')->findOrFail($data['term_id']);

        if (! $subject->isActive()) {
            $this->fail("Học phần {$subject->code} đang ngừng hoạt động.", 'Chỉ mở lớp cho học phần đang hoạt động (BR-CLS-03).');
        }

        if (! $this->catalog->isInForce($subject)) {
            $this->fail(
                "Học phần {$subject->code} không thuộc CTĐT nào đang áp dụng.",
                'Đưa học phần vào CTĐT đã ban hành trước, hoặc chọn học phần khác (BR-CLS-03).'
            );
        }

        $this->assertTermOpen($term);
        $this->syllabi->assertUsableForNewSection($subject);

        $data['min_size'] ??= (int) config('studentmanager.course_section.default_min_size', 15);
        $data['max_size'] ??= (int) config('studentmanager.course_section.default_max_size', 60);
        $data['language'] ??= $subject->teaching_language === 'en' ? 'en' : 'vi';
        $this->assertSizes((int) $data['min_size'], (int) $data['max_size'], $data['room_id'] ?? null);

        return $this->transaction(function () use ($data, $subject, $term): CourseSection {
            // Khóa các lớp cùng học phần – học kỳ để số thứ tự không trùng khi mở đồng thời
            $last = CourseSection::withTrashed()
                ->where('subject_id', $subject->id)
                ->where('term_id', $term->id)
                ->lockForUpdate()
                ->max('sequence');
            $sequence = ((int) $last) + 1;

            $section = CourseSection::create($data + [
                'code' => sprintf('%s.%s.%02d', $subject->code, $this->termKey($term), $sequence),
                'sequence' => $sequence,
                'status' => SectionStatus::Planned->value,
            ]);

            return $section->refresh();
        });
    }

    /** @param  array<string, mixed>  $data */
    public function update(CourseSection $section, array $data): CourseSection
    {
        if (! $section->status->isEditable()) {
            $this->fail(
                "Lớp {$section->code} đang ở trạng thái {$section->status->label()} nên không sửa cấu hình được.",
                'Chỉ sửa sĩ số, hình thức, ngôn ngữ khi lớp còn Dự kiến hoặc đang Mở đăng ký.'
            );
        }

        $this->assertSizes(
            (int) ($data['min_size'] ?? $section->min_size),
            (int) ($data['max_size'] ?? $section->max_size),
            array_key_exists('room_id', $data) ? $data['room_id'] : $section->room_id,
        );

        $section->update($data);

        return $section->refresh();
    }

    /**
     * Phân công giảng viên (FR-CLS-011). Trả về cảnh báo vượt định mức (nếu đã xác nhận).
     *
     * @return array{instructor: SectionInstructor, warnings: list<string>}
     */
    public function assign(CourseSection $section, Lecturer $lecturer, string $role, bool $confirmOverload = false): array
    {
        // Lớp mở trong đợt theo khung: giảng viên phải thuộc khoa của khung (khoa – khóa) của lớp
        $framework = $section->registrationPeriod?->usesFrameworks() ? $section->loadMissing('trainingFrameworkItem.framework')->trainingFrameworkItem?->framework : null;
        if ($framework !== null) {
            $availability = app(OfferingLecturers::class);
            if (! $availability->inFaculty($framework->faculty_id)->whereKey($lecturer->id)->exists()) {
                $this->fail('Giảng viên không thuộc khoa của chương trình khung của lớp.', 'Chọn giảng viên đang công tác trong khoa của chương trình khung.');
            }
            foreach (ScheduleRule::query()->where('course_section_id', $section->id)->get() as $rule) {
                if ($reason = $availability->conflict($lecturer, $section->term, $rule->getAttributes(), $section->id)) {
                    $this->fail($reason, 'Chọn giảng viên hoặc lịch học khác.');
                }
            }
        }
        if (! $section->status->isActive()) {
            $this->fail("Lớp {$section->code} đã {$section->status->label()}.", 'Chỉ phân công giảng viên cho lớp còn hoạt động.');
        }

        if (! $lecturer->isAssignable()) {
            $this->fail("Giảng viên {$lecturer->code} đang {$lecturer->status_label}.", 'Chỉ phân công giảng viên đang làm việc (BR-TCH-02).');
        }

        if ($section->instructors()->where('lecturer_id', $lecturer->id)->exists()) {
            $this->fail("Giảng viên {$lecturer->code} đã có trong lớp {$section->code}.", 'Đổi vai trò bằng cách gỡ rồi phân công lại.');
        }

        $warnings = [];
        $periods = $this->periodsOf($section->subject);
        $load = $this->loadInTerm($lecturer, $section->term_id);
        $limit = (int) config('studentmanager.course_section.max_periods_per_term', 400);

        if ($load + $periods > $limit) {
            $message = "Giảng viên {$lecturer->code} đã có {$load} tiết trong học kỳ; thêm lớp {$section->code} ({$periods} tiết) sẽ vượt định mức {$limit} tiết.";

            if (! $confirmOverload) {
                $this->fail($message, 'Chọn giảng viên khác, hoặc gửi lại kèm confirm_overload = true nếu vẫn phân công (FR-CLS-011).');
            }

            $warnings[] = $message;
        }

        $instructor = $this->audit->withReason(
            $warnings === [] ? 'Phân công giảng viên' : 'Phân công giảng viên (đã xác nhận vượt định mức)',
            function () use ($section, $lecturer, $role): SectionInstructor {
                $instructor = SectionInstructor::withTrashed()->firstOrNew([
                    'course_section_id' => $section->id, 'lecturer_id' => $lecturer->id,
                ]);
                if ($instructor->exists && $instructor->trashed()) {
                    $instructor->restore();
                }
                $instructor->fill(['role' => $role])->save();

                return $instructor;
            },
        );

        return ['instructor' => $instructor->load('lecturer'), 'warnings' => $warnings];
    }

    public function unassign(SectionInstructor $instructor): void
    {
        $section = $instructor->section;

        if (! $section->status->isActive()) {
            $this->fail("Lớp {$section->code} đã {$section->status->label()}.", 'Không đổi giảng viên của lớp đã kết thúc, khóa hoặc hủy.');
        }

        $isLastMain = $instructor->role === SectionInstructor::MAIN
            && $section->instructors()->where('role', SectionInstructor::MAIN)->count() === 1;

        if ($isLastMain && $section->status !== SectionStatus::Planned) {
            $this->fail(
                "Lớp {$section->code} đang {$section->status->label()} nên phải luôn có giảng viên chính.",
                'Phân công giảng viên chính khác trước, rồi gỡ giảng viên này (BR-CLS-04).'
            );
        }

        $instructor->delete();
    }

    /** Chuyển sang bước kế tiếp của vòng đời (FR-CLS-012). */
    public function advance(CourseSection $section, SectionStatus $to): CourseSection
    {
        if ($section->status->next() !== $to) {
            $expected = $section->status->next();
            $this->fail(
                "Lớp {$section->code} đang {$section->status->label()}, không chuyển thẳng sang {$to->label()} được.",
                $expected === null ? 'Lớp đã ở bước cuối của vòng đời.' : "Bước tiếp theo hợp lệ là {$expected->label()} (FR-CLS-012)."
            );
        }

        if ($to === SectionStatus::RegistrationOpen && ! $section->hasMainInstructor()) {
            $this->fail(
                "Lớp {$section->code} chưa có giảng viên chính nên chưa mở đăng ký được.",
                'Phân công ít nhất một giảng viên chính rồi mở đăng ký lại (BR-CLS-04).'
            );
        }

        // Không có lịch thì sinh viên không thấy giờ học, ngày bắt đầu học và không kiểm tra được trùng lịch / ngày học sau
        // ngày đóng đăng ký. Lớp trực tuyến có thể học không đồng bộ nên cho qua.
        if ($to === SectionStatus::RegistrationOpen && $section->delivery_mode !== 'online'
            && ! ScheduleRule::query()->where('course_section_id', $section->id)->exists()) {
            $this->fail(
                "Lớp {$section->code} chưa có lịch học nên chưa mở đăng ký được.",
                'Xếp lịch ở trang Xếp thời khóa biểu trước khi mở đăng ký (FR-TTB-002); chỉ lớp trực tuyến được mở khi chưa có lịch.'
            );
        }

        $section->update(['status' => $to]);

        return $section->refresh();
    }

    public function cancel(CourseSection $section, string $reason): CourseSection
    {
        if (! $section->status->cancellable()) {
            $this->fail(
                "Lớp {$section->code} đang {$section->status->label()} nên không hủy được.",
                'Chỉ hủy lớp ở bước Dự kiến, Mở đăng ký hoặc Đóng đăng ký (FR-CLS-012).'
            );
        }

        // Một giao dịch: lớp Hủy + sự kiện nghe đồng bộ (ENR hủy đăng ký và phát EV-ENR-03, TTB hủy buổi học; BR-CLS-09)
        return $this->audit->withReason("Hủy lớp: {$reason}", fn (): CourseSection => $this->transaction(function () use ($section, $reason): CourseSection {
            $section = CourseSection::query()->lockForUpdate()->findOrFail($section->id);
            $section->update(['status' => SectionStatus::Cancelled, 'cancel_reason' => $reason]);
            CourseSectionCancelled::dispatch($section, $reason);

            return $section->refresh();
        }));
    }

    /**
     * Lớp dưới sĩ số tối thiểu của học kỳ (BR-CLS-05): lớp đang Mở / Đóng đăng ký có số đăng ký < tối thiểu, kèm đề xuất
     * gộp vào lớp khác cùng học phần còn đủ chỗ hoặc hủy. Phải xử lý hết trước khi chốt đăng ký (#113).
     *
     * @return list<array<string, mixed>>
     */
    public function underMinSections(Term $term): array
    {
        $students = app(SectionStudents::class);
        $sections = CourseSection::query()
            ->with('subject:id,code,name')
            ->where('term_id', $term->id)
            ->whereIn('status', [SectionStatus::RegistrationOpen, SectionStatus::RegistrationClosed])
            ->orderBy('code')
            ->get();
        $counts = $sections->mapWithKeys(fn (CourseSection $s) => [$s->id => count($students->studentIdsOf($s))]);
        $rows = [];

        foreach ($sections as $section) {
            $enrolled = $counts[$section->id];

            if ($enrolled >= $section->min_size) {
                continue;
            }

            $targets = $sections
                ->filter(fn (CourseSection $t) => $t->id !== $section->id && $t->subject_id === $section->subject_id
                    && $t->max_size - $counts[$t->id] >= $enrolled)
                ->map(fn (CourseSection $t) => ['id' => $t->id, 'code' => $t->code, 'enrolled' => $counts[$t->id], 'max_size' => $t->max_size])
                ->values();

            $rows[] = [
                'id' => $section->id,
                'code' => $section->code,
                'subject' => $section->subject->only('code', 'name'),
                'status_label' => $section->status->label(),
                'enrolled' => $enrolled,
                'min_size' => $section->min_size,
                'suggestion' => $enrolled === 0 ? 'cancel' : ($targets->isNotEmpty() ? 'merge' : 'cancel_or_keep'),
                'merge_targets' => $targets,
            ];
        }

        return $rows;
    }

    /**
     * Mở khóa lớp đã khóa (sau công bố điểm) theo quy trình ngoại lệ (FR-CLS-018): bắt buộc lý do, người có quyền
     * duyệt CLS; lớp quay về Kết thúc, nhật ký ghi `unlocked` kèm lý do.
     */
    public function unlock(CourseSection $section, string $reason): CourseSection
    {
        if ($section->status !== SectionStatus::Locked) {
            $this->fail("Lớp {$section->code} đang {$section->status->label()}, không phải Đã khóa.", 'Chỉ mở khóa lớp đã khóa.');
        }

        $old = ['status' => $section->status->value];
        $section->update(['status' => SectionStatus::Finished]);
        $this->audit->record(AuditEvent::Unlocked, $section, $old, ['status' => SectionStatus::Finished->value], $reason);

        return $section->refresh();
    }

    /** Xóa lớp nhập nhầm: chỉ lớp Dự kiến chưa có dữ liệu liên quan (BR-CLS-06). */
    public function delete(CourseSection $section): void
    {
        if ($section->status !== SectionStatus::Planned) {
            $this->fail("Lớp {$section->code} đang {$section->status->label()}.", 'Không xóa lớp đã mở đăng ký; hãy hủy lớp kèm lý do (BR-CLS-06).');
        }

        $usages = $this->references->usages($section);

        if ($usages !== []) {
            $text = implode(', ', array_map(fn ($l, $c) => "{$c} {$l}", array_keys($usages), $usages));
            $this->fail("Lớp {$section->code} đã có {$text}.", 'Hủy lớp kèm lý do thay vì xóa (BR-CLS-06).');
        }

        $this->transaction(function () use ($section): void {
            $section->instructors()->get()->each->delete();
            $section->delete();
        });
    }

    // ---------------------------------------------------------------------

    /** Số tiết của một lớp: tổng tiết lý thuyết và thực hành của học phần. */
    public function periodsOf(Subject $subject): int
    {
        return (int) $subject->theory_periods + (int) $subject->practice_periods;
    }

    /** Tổng số tiết giảng viên đang được phân công trong học kỳ (các lớp còn hoạt động). */
    public function loadInTerm(Lecturer $lecturer, int $termId): int
    {
        return (int) SectionInstructor::query()
            ->join('course_sections', 'course_sections.id', '=', 'section_instructors.course_section_id')
            ->join('subjects', 'subjects.id', '=', 'course_sections.subject_id')
            ->where('section_instructors.lecturer_id', $lecturer->id)
            ->whereNull('section_instructors.deleted_at')
            ->whereNull('course_sections.deleted_at')
            ->where('course_sections.term_id', $termId)
            ->whereIn('course_sections.status', SectionStatus::activeValues())
            ->sum(DB::raw('subjects.theory_periods + subjects.practice_periods'));
    }

    /** Phần học kỳ trong mã lớp: 2 số cuối năm bắt đầu năm học + thứ tự học kỳ trong năm (theo ngày bắt đầu). */
    private function termKey(Term $term): string
    {
        $year = (int) substr((string) $term->academicYear?->name, 0, 4) ?: (int) $term->start_date->format('Y');
        $order = Term::query()
            ->where('academic_year_id', $term->academic_year_id)
            ->where('start_date', '<=', $term->start_date)
            ->count();

        return sprintf('%02d%d', $year % 100, max(1, $order));
    }

    private function assertTermOpen(Term $term): void
    {
        if (in_array($term->status, [TermStatus::COMPLETED, TermStatus::LOCKED], true)) {
            $this->fail("Học kỳ {$term->name} đã kết thúc hoặc đã khóa.", 'Chọn học kỳ chưa kết thúc để mở lớp.');
        }
    }

    private function assertSizes(int $min, int $max, ?int $roomId): void
    {
        if ($min < 1 || $min > $max) {
            $this->fail("Sĩ số tối thiểu {$min} không hợp lệ so với tối đa {$max}.", 'Sĩ số tối thiểu phải từ 1 và không lớn hơn sĩ số tối đa.');
        }

        $limit = (int) config('studentmanager.course_section.max_size', 200);

        if ($max > $limit) {
            $this->fail("Sĩ số tối đa {$max} vượt mức cấu hình {$limit}.", 'Giảm sĩ số hoặc tách thành nhiều lớp (BR-CLS-02).');
        }

        if ($roomId !== null) {
            $room = Room::query()->findOrFail($roomId);

            if ($max > $room->capacity) {
                $this->fail(
                    "Sĩ số tối đa {$max} vượt sức chứa phòng {$room->code} ({$room->capacity} chỗ).",
                    'Giảm sĩ số tối đa hoặc xếp phòng lớn hơn (BR-CLS-02).'
                );
            }
        }
    }
}
