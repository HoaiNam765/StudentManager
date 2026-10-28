<?php

namespace App\Modules\Enrollment\Services;

use App\Models\User;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\Enrollment\Enums\PeriodStatus;
use App\Modules\Enrollment\Models\RegistrationPeriod;
use App\Modules\Faculty\Models\Faculty;
use App\Modules\Notification\Services\AnnouncementService;
use App\Modules\Room\Models\Room;
use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Models\Cohort;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\SectionInstructor;
use App\Modules\SchoolClass\Models\TrainingFramework;
use App\Modules\SchoolClass\Services\CourseSectionService;
use App\Modules\SchoolClass\Services\TrainingFrameworkService;
use App\Modules\Teacher\Models\Lecturer;
use App\Modules\Timetable\Models\ScheduleRule;
use App\Modules\Timetable\Services\ScheduleRuleService;
use App\Modules\Timetable\Services\SectionStartDates;
use App\Support\Exceptions\BusinessRuleException;
use App\Support\Services\BaseService;

/**
 * Đợt đăng ký theo chương trình khung cho cả học kỳ: bảng Khoa × Khóa (mỗi ô một khung, kỳ CTĐT do Phòng Đào tạo chọn),
 * lớp + giảng viên của khoa + lịch học mở trong đợt (độc lập với danh mục học phần của khung). Công bố khi mọi khoa – khóa
 * đang có sinh viên đều có khung, mỗi khung có lớp, mọi lớp có giảng viên chính và lịch.
 */
class RegistrationOfferingService extends BaseService
{
    public function __construct(
        private readonly CourseSectionService $sections,
        private readonly ScheduleRuleService $schedules,
        private readonly OfferingLecturers $lecturers,
        private readonly AnnouncementService $announcements,
        private readonly TrainingFrameworkService $frameworks,
    ) {}

    /**
     * Bảng Khoa × Khóa của đợt: dòng là khoa có ngành, cột là khóa đang có sinh viên; ô có khi khoa – khóa có sinh viên.
     *
     * @return array{faculties: list<Faculty>, cohorts: list<Cohort>, cells: list<array<string, mixed>>}
     */
    public function matrix(RegistrationPeriod $period): array
    {
        $period->loadMissing(['term.academicYear', 'frameworks']);
        $attached = $period->frameworks->keyBy(fn (TrainingFramework $f) => "{$f->faculty_id}-{$f->cohort_id}");
        $faculties = Faculty::query()->whereHas('majors')->orderBy('code')->get();
        $cohorts = Cohort::query()->orderByDesc('start_year')->get();
        $sections = $period->sections()->with('trainingFrameworkItem:id,training_framework_id')->where('status', '!=', SectionStatus::Cancelled)->get();
        $scheduled = ScheduleRule::query()->whereIn('course_section_id', $sections->pluck('id'))->pluck('course_section_id')->unique()->flip();
        $cells = [];

        foreach ($faculties as $faculty) {
            foreach ($cohorts as $cohort) {
                $students = $this->frameworks->studentsOf($faculty->id, $cohort);
                $framework = $attached->get("{$faculty->id}-{$cohort->id}");

                if ($students === 0 && $framework === null) {
                    continue;
                }

                $own = $framework === null ? collect() : $sections->filter(fn (CourseSection $s) => $s->trainingFrameworkItem?->training_framework_id === $framework->id);
                $cells[] = [
                    'faculty' => $faculty, 'cohort' => $cohort, 'students' => $students,
                    'suggested_term' => $this->frameworks->cohortTermNumber($cohort, $period->term),
                    'framework' => $framework,
                    'planned_term' => $framework === null ? null : $this->frameworks->plannedTermOf($framework),
                    'items' => $framework?->items()->count() ?? 0,
                    'sections' => $own->count(),
                    'unscheduled' => $own->reject(fn (CourseSection $s) => $scheduled->has($s->id))->count(),
                ];
            }
        }

        $used = collect($cells)->pluck('cohort.id')->unique();

        return [
            'faculties' => $faculties->filter(fn (Faculty $f) => collect($cells)->contains(fn ($c) => $c['faculty']->id === $f->id))->values()->all(),
            'cohorts' => $cohorts->filter(fn (Cohort $c) => $used->contains($c->id))->values()->all(),
            'cells' => $cells,
        ];
    }

    /**
     * Chọn kỳ CTĐT cho ô khoa – khóa: dùng / tạo khung (khoa, khóa, học kỳ của đợt) với kỳ đó, môn lấy theo CTĐT, gắn vào
     * đợt. Đổi kỳ của khung đã có lớp thì chặn.
     */
    public function setTerm(RegistrationPeriod $period, int $facultyId, int $cohortId, int $plannedTerm): TrainingFramework
    {
        return $this->transaction(function () use ($period, $facultyId, $cohortId, $plannedTerm): TrainingFramework {
            $period = RegistrationPeriod::query()->lockForUpdate()->findOrFail($period->id);
            $this->assertDraft($period);
            $framework = TrainingFramework::query()->lockForUpdate()
                ->where(['faculty_id' => $facultyId, 'cohort_id' => $cohortId, 'term_id' => $period->term_id])->first();

            if ($framework === null) {
                $framework = $this->frameworks->create(['faculty_id' => $facultyId, 'cohort_id' => $cohortId, 'term_id' => $period->term_id]);
                $framework->update(['planned_term' => $plannedTerm]);
                $this->frameworks->suggestFromCurriculum($framework->refresh());
            } elseif ($this->frameworks->plannedTermOf($framework) !== $plannedTerm) {
                if (CourseSection::query()->whereIn('training_framework_item_id', $framework->items()->select('id'))->where('status', '!=', SectionStatus::Cancelled)->exists()) {
                    $this->fail(
                        "Khung {$framework->faculty->name} – {$framework->cohort->code} đã có lớp học phần nên không đổi kỳ được.",
                        'Bỏ các lớp của khung khỏi đợt trước, rồi chọn lại kỳ.'
                    );
                }
                $framework->items()->get()->each->forceDelete();
                $framework->update(['planned_term' => $plannedTerm]);
                $this->frameworks->suggestFromCurriculum($framework->refresh());
            }

            $period->frameworks()->syncWithoutDetaching([$framework->id]);

            return $framework->refresh();
        });
    }

    /** Bỏ khung khỏi đợt (khung giữ nguyên ở trang Chương trình khung); chỉ khi đợt chưa có lớp nào của khung. */
    public function detach(RegistrationPeriod $period, TrainingFramework $framework): void
    {
        $this->transaction(function () use ($period, $framework): void {
            $period = RegistrationPeriod::query()->lockForUpdate()->findOrFail($period->id);
            $this->assertDraft($period);

            if ($period->sections()->whereIn('training_framework_item_id', $framework->items()->select('id'))->where('status', '!=', SectionStatus::Cancelled)->exists()) {
                $this->fail('Khung đã có lớp trong đợt nên không bỏ được.', 'Bỏ các lớp của khung khỏi đợt trước.');
            }

            $period->frameworks()->detach($framework->id);
        });
    }

    /**
     * Mở lớp cho các môn của một khung trong đợt: số lớp, sĩ số, giảng viên chính của khoa từng lớp, trong một giao dịch.
     * Lịch học xếp sau. Số lớp dự kiến của từng môn trong khung cập nhật theo số lớp đang mở.
     *
     * @param  list<array{item_id: int, lecturer_id: int, max_size?: ?int}>  $classes
     * @return list<CourseSection>
     */
    public function addClasses(RegistrationPeriod $period, TrainingFramework $framework, array $classes, bool $confirmOverload = false): array
    {
        if ($classes === []) {
            $this->fail('Chưa có lớp nào để mở.', 'Nhập số lớp ít nhất 1 cho một học phần và chọn giảng viên cho từng lớp.');
        }

        return $this->transaction(function () use ($period, $framework, $classes, $confirmOverload): array {
            $period = RegistrationPeriod::query()->lockForUpdate()->findOrFail($period->id);
            $this->assertDraft($period);
            $this->assertAttached($period, $framework);
            $items = $framework->items()->with('subject')->get()->keyBy('id');
            $defaultMin = (int) config('studentmanager.course_section.default_min_size', 15);
            $defaultMax = (int) config('studentmanager.course_section.default_max_size', 60);
            $counts = [];
            $opened = [];

            foreach ($classes as $class) {
                $item = $items->get((int) $class['item_id'])
                    ?? $this->fail('Học phần không thuộc chương trình khung này.', 'Tải lại trang rồi chọn lại học phần.');
                $counts[$item->id] = ($counts[$item->id] ?? 0) + 1;
                $max = (int) ($class['max_size'] ?? $item->max_size ?? $defaultMax);

                try {
                    $section = $this->sections->open([
                        'subject_id' => $item->subject_id, 'term_id' => $period->term_id,
                        'registration_period_id' => $period->id, 'training_framework_item_id' => $item->id,
                        'min_size' => min($defaultMin, $max), 'max_size' => $max,
                        'section_type' => 'theory', 'delivery_mode' => 'in_person',
                    ]);
                    // assign kiểm tra giảng viên thuộc khoa của khung của lớp
                    $this->sections->assign($section, Lecturer::query()->findOrFail($class['lecturer_id']), SectionInstructor::MAIN, $confirmOverload);
                    $opened[] = $section;
                } catch (BusinessRuleException $e) {
                    $this->fail("{$item->subject->code} – lớp thứ {$counts[$item->id]}: {$e->getMessage()}", $e->hint());
                }
            }

            foreach (array_keys($counts) as $itemId) {
                $item = $items->get($itemId);
                $item->update(['planned_sections' => max(1, $item->sections()->where('status', '!=', SectionStatus::Cancelled)->count())]);
            }

            return $opened;
        });
    }

    /**
     * Thêm / sửa một lớp kèm giảng viên và lịch. Lớp mới: `framework_id` (khung trong đợt) + học phần; học phần chưa có trong
     * khung thì được thêm vào khung ("Thêm học phần khác").
     */
    public function save(RegistrationPeriod $period, array $data, ?CourseSection $section = null): CourseSection
    {
        return $this->transaction(function () use ($period, $data, $section): CourseSection {
            $period = RegistrationPeriod::query()->lockForUpdate()->findOrFail($period->id);
            $this->assertDraft($period);
            if ($section !== null) {
                $section = CourseSection::query()->lockForUpdate()->findOrFail($section->id);
                abort_unless($section->registration_period_id === $period->id, 404);
                if ($section->status !== SectionStatus::Planned || $section->subject_id !== (int) $data['subject_id']) {
                    $this->fail('Không đổi học phần hoặc lớp đã mở đăng ký.', 'Xóa lớp dự kiến và thêm học phần khác; sau công bố hãy hủy lớp kèm lý do.');
                }
                if (ScheduleRule::query()->where('course_section_id', $section->id)->count() > 1 || $section->instructors()->count() > 1) {
                    $this->fail('Lớp có nhiều lịch học hoặc nhiều giảng viên.', 'Sửa từng lịch tại trang thời khóa biểu và phân công tại trang lớp học phần để giữ nguyên các lịch, giảng viên khác.');
                }
                $framework = $this->frameworkOf($section) ?? $this->fail('Lớp không thuộc khung nào của đợt.', 'Bỏ lớp khỏi đợt rồi thêm lại từ khung.');
            } else {
                $framework = $period->frameworks()->whereKey($data['framework_id'] ?? 0)->first()
                    ?? $this->fail('Chưa chọn khung (khoa – khóa) của lớp.', 'Chọn khoa – khóa trong bảng khung của đợt rồi thêm lớp.');
            }
            // Khóa cùng các tài nguyên khi ghi để hai yêu cầu mở lớp không cùng chiếm một lịch.
            $lecturer = Lecturer::query()->lockForUpdate()->findOrFail($data['lecturer_id']);
            Room::query()->whereKey($data['schedule']['room_id'])->lockForUpdate()->firstOrFail();
            $this->assertLecturer($framework->faculty_id, $period->term, $lecturer, $data['schedule'], $section?->id);
            $attributes = [
                'min_size' => $data['min_size'], 'max_size' => $data['max_size'],
                'room_id' => $data['schedule']['room_id'], 'section_type' => $data['section_type'] ?? 'theory',
                'delivery_mode' => 'in_person',
            ];
            if ($section === null) {
                $item = $framework->items()->where('subject_id', $data['subject_id'])->first()
                    ?? $this->frameworks->addItem($framework, ['subject_id' => $data['subject_id'], 'compulsory' => false]);
                $section = $this->sections->open($attributes + [
                    'subject_id' => $data['subject_id'], 'term_id' => $period->term_id,
                    'registration_period_id' => $period->id, 'training_framework_item_id' => $item->id,
                ]);
            } else {
                // Xóa lịch cũ qua service để giữ kiểm tra công bố TKB và nhật ký.
                foreach (ScheduleRule::query()->where('course_section_id', $section->id)->get() as $rule) {
                    $this->schedules->delete($rule, ['reason' => 'Sửa kế hoạch mở học phần']);
                }
                foreach ($section->instructors()->get() as $instructor) {
                    $this->sections->unassign($instructor);
                }
                $section = $this->sections->update($section, $attributes);
            }
            $this->sections->assign($section, $lecturer, SectionInstructor::MAIN, $data['confirm_overload'] ?? false);
            $this->schedules->create($section, $data['schedule'] + ['reason' => 'Xếp lịch mở học phần']);

            return $section->refresh();
        });
    }

    public function delete(RegistrationPeriod $period, CourseSection $section): void
    {
        $this->transaction(function () use ($period, $section): void {
            $period = RegistrationPeriod::query()->lockForUpdate()->findOrFail($period->id);
            $this->assertDraft($period);
            $section = CourseSection::query()->lockForUpdate()->findOrFail($section->id);
            abort_unless($section->registration_period_id === $period->id, 404);
            $this->sections->cancel($section, 'Bỏ học phần khỏi kế hoạch đăng ký trước công bố');
        });
    }

    /** Thông báo xem trước (đợt nháp): sinh viên các khoa – khóa của đợt xem được lớp, chưa đăng ký. */
    public function announce(RegistrationPeriod $period, User $actor): RegistrationPeriod
    {
        return $this->transaction(function () use ($period, $actor): RegistrationPeriod {
            $period = RegistrationPeriod::query()->lockForUpdate()->findOrFail($period->id);
            $this->assertDraft($period);
            $this->assertReady($period);
            $this->notify($period, $actor, true);
            $period->update(['preview_at' => now()]);

            return $period->refresh();
        });
    }

    /**
     * Sẵn sàng công bố: mọi khoa – khóa đang có sinh viên đều có khung trong đợt, mỗi khung có lớp; mọi lớp có giảng viên
     * chính của khoa (không trùng lịch) và lịch học bắt đầu sau ngày đóng đăng ký.
     */
    public function assertReady(RegistrationPeriod $period): void
    {
        if (! $period->usesFrameworks()) {
            return; // Đợt theo đối tượng (cũ) giữ nguyên cách công bố.
        }

        $matrix = $this->matrix($period);
        $missing = collect($matrix['cells'])->filter(fn ($c) => $c['framework'] === null)
            ->map(fn ($c) => "{$c['faculty']->name} – {$c['cohort']->code}")->values();
        if ($missing->isNotEmpty()) {
            $this->fail('Chưa chọn kỳ cho: '.$missing->implode(', ').'.', 'Chọn kỳ CTĐT cho mọi ô khoa – khóa trong bảng khung của đợt (hoặc bấm "Điền theo gợi ý").');
        }
        $empty = collect($matrix['cells'])->filter(fn ($c) => $c['sections'] === 0)
            ->map(fn ($c) => "{$c['faculty']->name} – {$c['cohort']->code}")->values();
        if ($empty->isNotEmpty()) {
            $this->fail('Khung chưa có lớp nào: '.$empty->implode(', ').'.', 'Bấm "Lớp & giảng viên" ở khung đó để mở lớp.');
        }

        $sections = $period->sections()->with('trainingFrameworkItem.framework')->where('status', '!=', SectionStatus::Cancelled)->get();
        foreach ($sections as $section) {
            $rules = ScheduleRule::query()->where('course_section_id', $section->id)->get();
            if (! $section->hasMainInstructor() || $rules->isEmpty()) {
                $this->fail("Lớp {$section->code} thiếu giảng viên hoặc lịch học.", 'Hoàn tất phân công và xếp lịch trước khi công bố.');
            }
            $facultyId = $this->frameworkOf($section)?->faculty_id;
            foreach ($section->instructors()->with('lecturer')->get() as $instructor) {
                foreach ($rules as $rule) {
                    $this->assertLecturer($facultyId, $period->term, $instructor->lecturer, $rule->getAttributes(), $section->id);
                }
            }
            $start = app(SectionStartDates::class)->of($section->id);
            if ($start === null || $start->toDateString() <= $period->closes_at->setTimezone(config('studentmanager.display_timezone'))->toDateString()) {
                $this->fail("Lớp {$section->code} phải bắt đầu học sau ngày đóng đăng ký.", 'Sửa ngày đóng đợt hoặc lịch học trước khi công bố.');
            }
        }
    }

    public function activateSections(RegistrationPeriod $period): void
    {
        $this->assertReady($period);
        foreach ($period->sections()->where('status', SectionStatus::Planned)->get() as $section) {
            $this->sections->advance($section, SectionStatus::RegistrationOpen);
        }
    }

    public function notify(RegistrationPeriod $period, User $actor, bool $preview = false): void
    {
        if (! $period->usesFrameworks()) {
            return;
        }
        $period->loadMissing('frameworks.faculty', 'frameworks.cohort');
        if ($period->frameworks->isEmpty()) {
            return;
        }
        $tz = config('studentmanager.display_timezone');
        $body = $preview
            ? 'Danh sách học phần, giảng viên và lịch học đã có để xem trước. Chưa được đăng ký; thời gian dưới đây là dự kiến cho đến khi Phòng Đào tạo công bố.'
            : 'Đã công bố danh sách lớp. Nút đăng ký hiện khi tới giờ mở đăng ký (hoặc khi Phòng Đào tạo mở sớm); môn đã đạt không hiện.';
        $body .= "\n".$period->frameworks->map(fn (TrainingFramework $f) => "{$f->faculty->name} – khóa {$f->cohort->code}")->implode('; ').".\n"
            .'Mở: '.$period->opens_at->setTimezone($tz)->format('H:i d/m/Y').'; đóng: '.$period->closes_at->setTimezone($tz)->format('H:i d/m/Y').'.';
        $this->announcements->create($actor, [
            'title' => ($preview ? 'Xem trước học phần: ' : 'Công bố đăng ký: ').$period->name,
            'body' => $body,
            'audience' => ['targets' => $period->frameworks->map(fn (TrainingFramework $f) => ['type' => 'faculty', 'id' => $f->faculty_id, 'cohort_id' => $f->cohort_id])->values()->all()],
            'link' => route('student.pages.registration', ['period_id' => $period->id], absolute: false),
        ]);
    }

    /** Khung (khoa – khóa) của lớp trong đợt theo khung. */
    public function frameworkOf(CourseSection $section): ?TrainingFramework
    {
        return $section->loadMissing('trainingFrameworkItem.framework')->trainingFrameworkItem?->framework;
    }

    private function assertAttached(RegistrationPeriod $period, TrainingFramework $framework): void
    {
        if (! $period->frameworks()->whereKey($framework->id)->exists()) {
            $this->fail('Khung chưa thuộc đợt đăng ký này.', 'Chọn kỳ cho ô khoa – khóa đó trong bảng khung của đợt trước.');
        }
    }

    private function assertDraft(RegistrationPeriod $period): void
    {
        if ($period->status !== PeriodStatus::Draft || ! $period->usesFrameworks()) {
            $this->fail('Chỉ sửa khung và lớp của đợt nháp theo chương trình khung.', 'Tạo đợt loại "Theo chương trình khung". Sau công bố, hủy lớp thay vì xóa.');
        }
    }

    private function assertLecturer(?int $facultyId, Term $term, Lecturer $lecturer, array $schedule, ?int $ignore): void
    {
        if ($facultyId === null || ! $this->lecturers->inFaculty($facultyId)->whereKey($lecturer->id)->exists()) {
            $this->fail('Giảng viên phải đang công tác trong khoa của chương trình khung.', 'Chọn giảng viên của khoa trong danh sách.');
        }
        if ($reason = $this->lecturers->conflict($lecturer, $term, $schedule, $ignore)) {
            $this->fail($reason, 'Chọn giảng viên hoặc lịch học khác.');
        }
    }
}
