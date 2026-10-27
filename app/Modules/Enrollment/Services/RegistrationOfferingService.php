<?php

namespace App\Modules\Enrollment\Services;

use App\Models\User;
use App\Modules\Enrollment\Enums\PeriodStatus;
use App\Modules\Enrollment\Models\RegistrationPeriod;
use App\Modules\Notification\Services\AnnouncementService;
use App\Modules\Room\Models\Room;
use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\SectionInstructor;
use App\Modules\SchoolClass\Services\CourseSectionService;
use App\Modules\Teacher\Models\Lecturer;
use App\Modules\Timetable\Models\ScheduleRule;
use App\Modules\Timetable\Services\ScheduleRuleService;
use App\Modules\Timetable\Services\SectionStartDates;
use App\Support\Services\BaseService;

/** Lớp của từng đợt độc lập với danh mục học phần của chương trình khung. */
class RegistrationOfferingService extends BaseService
{
    public function __construct(
        private readonly CourseSectionService $sections,
        private readonly ScheduleRuleService $schedules,
        private readonly OfferingLecturers $lecturers,
        private readonly AnnouncementService $announcements,
    ) {}

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
            }
            // Khóa cùng các tài nguyên khi ghi để hai yêu cầu mở lớp không cùng chiếm một lịch.
            $lecturer = Lecturer::query()->lockForUpdate()->findOrFail($data['lecturer_id']);
            Room::query()->whereKey($data['schedule']['room_id'])->lockForUpdate()->firstOrFail();
            $this->assertLecturer($period, $lecturer, $data['schedule'], $section?->id);
            $attributes = [
                'min_size' => $data['min_size'], 'max_size' => $data['max_size'],
                'room_id' => $data['schedule']['room_id'], 'section_type' => $data['section_type'] ?? 'theory',
                'delivery_mode' => 'in_person',
            ];
            if ($section === null) {
                $item = $period->framework->items()->where('subject_id', $data['subject_id'])->first();
                $section = $this->sections->open($attributes + [
                    'subject_id' => $data['subject_id'], 'term_id' => $period->term_id,
                    'registration_period_id' => $period->id, 'training_framework_item_id' => $item?->id,
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

    public function assertReady(RegistrationPeriod $period): void
    {
        if ($period->training_framework_id === null) {
            return; // Giữ các đợt cũ hoạt động, không tự chuyển dữ liệu đang dùng.
        }
        $sections = $period->sections()->where('status', '!=', SectionStatus::Cancelled)->get();
        if ($sections->isEmpty()) {
            $this->fail('Đợt chưa có lớp học phần.', 'Thêm học phần từ chương trình khung, chọn giảng viên và lịch học trước.');
        }
        foreach ($sections as $section) {
            $rules = ScheduleRule::query()->where('course_section_id', $section->id)->get();
            if (! $section->hasMainInstructor() || $rules->isEmpty()) {
                $this->fail("Lớp {$section->code} thiếu giảng viên hoặc lịch học.", 'Hoàn tất phân công và xếp lịch trước khi thông báo / công bố.');
            }
            foreach ($section->instructors()->with('lecturer')->get() as $instructor) {
                foreach ($rules as $rule) {
                    $this->assertLecturer($period, $instructor->lecturer, $rule->getAttributes(), $section->id);
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
        if ($period->training_framework_id === null) {
            return;
        }
        $framework = $period->framework;
        $tz = config('studentmanager.display_timezone');
        $body = $preview
            ? 'Danh sách học phần, giảng viên và lịch học đã có để xem trước. Chưa được đăng ký; thời gian dưới đây là dự kiến cho đến khi Phòng Đào tạo công bố.'
            : 'Đã công bố mở đăng ký. Bạn chỉ được đăng ký trong thời gian mở đợt; môn đã đạt không được đăng ký thường.';
        $body .= "\n{$framework->faculty->name} – khóa {$framework->cohort->code}.\n"
            .'Mở: '.$period->opens_at->setTimezone($tz)->format('H:i d/m/Y').'; đóng: '.$period->closes_at->setTimezone($tz)->format('H:i d/m/Y').'.';
        $this->announcements->create($actor, [
            'title' => ($preview ? 'Xem trước học phần: ' : 'Công bố đăng ký: ').$period->name,
            'body' => $body,
            'audience' => ['targets' => [['type' => 'faculty', 'id' => $framework->faculty_id, 'cohort_id' => $framework->cohort_id]]],
            'link' => route('student.pages.registration', ['period_id' => $period->id], absolute: false),
        ]);
    }

    private function assertDraft(RegistrationPeriod $period): void
    {
        if ($period->status !== PeriodStatus::Draft || $period->training_framework_id === null) {
            $this->fail('Chỉ sửa danh sách học phần của đợt nháp có chương trình khung.', 'Chọn chương trình khung khi tạo đợt. Sau công bố, hủy lớp thay vì xóa.');
        }
    }

    private function assertLecturer(RegistrationPeriod $period, Lecturer $lecturer, array $schedule, ?int $ignore): void
    {
        if (! $this->lecturers->inFaculty($period->framework->faculty_id)->whereKey($lecturer->id)->exists()) {
            $this->fail('Giảng viên phải đang công tác trong khoa của chương trình khung.', 'Chọn giảng viên của khoa trong danh sách.');
        }
        if ($reason = $this->lecturers->conflict($lecturer, $period->term, $schedule, $ignore)) {
            $this->fail($reason, 'Chọn giảng viên hoặc lịch học khác.');
        }
    }
}
