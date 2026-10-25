<?php

namespace App\Modules\Notification\Listeners;

use App\Modules\Attendance\Events\AttendanceThresholdReached;
use App\Modules\Auth\Events\AccountSecurityAlert;
use App\Modules\Auth\Events\UserRolesChanged;
use App\Modules\Enrollment\Events\EnrollmentChanged;
use App\Modules\Enrollment\Events\EnrollmentsCancelledByClass;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grade\Events\GradeAdjusted;
use App\Modules\Grade\Events\GradesPublished;
use App\Modules\Grade\Models\SectionGradeWorkflow;
use App\Modules\Notification\Services\EventNotifier;
use App\Modules\Notification\Services\RecipientResolver;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Student\Events\StudentAdmitted;
use App\Modules\Student\Events\StudentChangeRequestDecided;
use App\Modules\Student\Events\StudentStatusChanged;
use App\Modules\Timetable\Events\ScheduleChanged;
use App\Modules\Timetable\Events\TimetablePublished;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\DB;

/**
 * Nối sự kiện của các module P1 với thông báo (FR-NOT-004; phụ lục A). Mỗi hàm chọn người nhận, biến cho mẫu và khóa
 * chống trùng (`event_key`, BR-NOT-09). Biến chỉ là tên học phần, mã lớp, MSSV, lý do — không có điểm, số nợ, CCCD
 * (BR-NOT-03). Chạy đồng bộ trong yêu cầu phát sự kiện; người nhận được ghi theo lô.
 */
class EventNotificationSubscriber
{
    public function __construct(
        private readonly EventNotifier $notifier,
        private readonly RecipientResolver $recipients,
    ) {}

    /** @return array<class-string, string> */
    public function subscribe(Dispatcher $events): array
    {
        return [
            AccountSecurityAlert::class => 'accountSecurity',
            UserRolesChanged::class => 'rolesChanged',
            StudentAdmitted::class => 'studentAdmitted',
            StudentStatusChanged::class => 'studentStatusChanged',
            StudentChangeRequestDecided::class => 'changeRequestDecided',
            EnrollmentChanged::class => 'enrollmentChanged',
            EnrollmentsCancelledByClass::class => 'sectionCancelled',
            TimetablePublished::class => 'timetablePublished',
            ScheduleChanged::class => 'scheduleChanged',
            GradesPublished::class => 'gradesPublished',
            GradeAdjusted::class => 'gradeAdjusted',
            AttendanceThresholdReached::class => 'attendanceThreshold',
        ];
    }

    /** EV-AUTH-02: chủ tài khoản và ADMIN. */
    public function accountSecurity(AccountSecurityAlert $event): void
    {
        $text = $event->type === 'locked'
            ? "Tài khoản {$event->user->username} bị khóa tạm do nhập sai mật khẩu nhiều lần."
            : "Tài khoản {$event->user->username} vừa đăng nhập từ thiết bị / địa chỉ chưa từng dùng.";

        $this->notifier->notify('EV-AUTH-02', "user:{$event->user->id}:{$event->key}", [$event->user->id], ['noi_dung' => $text]);
        $this->notifier->notify('EV-AUTH-02', "user:{$event->user->id}:{$event->key}:admin", array_diff($this->recipients->admins(), [$event->user->id]), ['noi_dung' => $text], '/admin');
    }

    /** EV-AUTH-03: người được gán / gỡ vai trò. */
    public function rolesChanged(UserRolesChanged $event): void
    {
        $this->notifier->notify('EV-AUTH-03', "user:{$event->user->id}:".now()->format('YmdHisu'), [$event->user->id], [
            'thay_doi' => $event->change === 'assigned' ? 'gán' : 'gỡ',
            'vai_tro' => $event->roleName,
        ]);
    }

    /** EV-STU-01: sinh viên vừa nhập học. */
    public function studentAdmitted(StudentAdmitted $event): void
    {
        $this->notifier->notify('EV-STU-01', "student:{$event->student->id}", $this->recipients->students([$event->student->id]), ['mssv' => $event->student->student_code]);
    }

    /** EV-STU-02: sinh viên và cố vấn học tập. */
    public function studentStatusChanged(StudentStatusChanged $event): void
    {
        $vars = [
            'ho_ten' => $event->student->full_name,
            'mssv' => $event->student->student_code,
            'trang_thai' => $event->to->label(),
            'so_quyet_dinh' => $event->history->decision_no,
        ];

        $this->notifier->notify('EV-STU-02', "history:{$event->history->id}", $this->recipients->students([$event->student->id]), $vars, '/student');
        $this->notifier->notify('EV-STU-02', "history:{$event->history->id}:advisor", $this->recipients->advisorsOf($event->student->id), $vars, '/teacher');
    }

    /** EV-STU-03: kết quả duyệt yêu cầu sửa hồ sơ. */
    public function changeRequestDecided(StudentChangeRequestDecided $event): void
    {
        $status = $event->request->status instanceof \BackedEnum ? $event->request->status->value : (string) $event->request->status;

        $this->notifier->notify('EV-STU-03', "request:{$event->request->id}:{$status}", $this->recipients->students([$event->request->student_id]), [
            'ket_qua' => $status === 'approved' ? 'duyệt' : 'từ chối',
        ]);
    }

    /** EV-ENR-02: xác nhận đăng ký / hủy / rút / đổi lớp cho sinh viên. */
    public function enrollmentChanged(EnrollmentChanged $event): void
    {
        $enrollment = $event->enrollment->loadMissing('section:id,code,subject_id', 'subject:id,code,name');

        $this->notifier->notify('EV-ENR-02', "enrollment:{$enrollment->id}:{$event->action}", $this->recipients->students([$enrollment->student_id]), [
            'thao_tac' => match ($event->action) {
                'register' => 'đăng ký',
                'cancel' => 'hủy',
                'withdraw' => 'rút',
                'change' => 'đổi sang',
                default => $event->action,
            },
            'hoc_phan' => $enrollment->subject?->name,
            'lop' => $enrollment->section?->code,
        ]);
    }

    /** EV-ENR-03: lớp bị hủy — sinh viên đã đăng ký và giảng viên của lớp. */
    public function sectionCancelled(EnrollmentsCancelledByClass $event): void
    {
        $section = $event->section->loadMissing('subject:id,code,name');
        $vars = ['lop' => $section->code, 'hoc_phan' => $section->subject?->name, 'ly_do' => $event->reason];

        $this->notifier->notify('EV-ENR-03', "section:{$section->id}", $this->recipients->students($event->studentIds), $vars);
        $this->notifier->notify('EV-ENR-03', "section:{$section->id}:lecturer", $this->recipients->sectionLecturers($section->id), $vars, '/teacher');
    }

    /** EV-TTB-01: sinh viên và giảng viên của học kỳ. */
    public function timetablePublished(TimetablePublished $event): void
    {
        $term = $event->term->loadMissing('academicYear:id,name');
        $published = DB::table('term_timetables')->where('term_id', $term->id)->value('published_at');
        $vars = ['hoc_ky' => trim($term->name.' – '.($term->academicYear?->name ?? ''), ' –')];
        $key = "term:{$term->id}:".($published ?? now()->toDateTimeString());

        $this->notifier->notify('EV-TTB-01', $key, $this->recipients->termStudents($term->id), $vars, '/student/ket-qua-dang-ky');
        $this->notifier->notify('EV-TTB-01', "{$key}:lecturer", $this->recipients->termLecturers($term->id), $vars, '/teacher');
    }

    /** EV-TTB-02: sinh viên và giảng viên của lớp đổi lịch. */
    public function scheduleChanged(ScheduleChanged $event): void
    {
        $section = $event->section->loadMissing('subject:id,code,name');
        $vars = ['lop' => $section->code, 'hoc_phan' => $section->subject?->name, 'ly_do' => $event->reason];
        $key = "section:{$section->id}:".now()->format('YmdHi').':'.md5($event->reason);

        $this->notifier->notify('EV-TTB-02', $key, $this->recipients->sectionStudents($section->id), $vars, '/student/ket-qua-dang-ky');
        $this->notifier->notify('EV-TTB-02', "{$key}:lecturer", $this->recipients->sectionLecturers($section->id), $vars, '/teacher');
    }

    /** EV-GRD-01: mỗi sinh viên của lớp một thông báo, không kèm điểm (tiêu chí nghiệm thu 6.12). */
    public function gradesPublished(GradesPublished $event): void
    {
        $section = CourseSection::query()->with('subject:id,code,name')->find($event->courseSectionId);

        if ($section === null) {
            return;
        }

        $published = SectionGradeWorkflow::query()->where('course_section_id', $section->id)->value('published_at');

        $this->notifier->notify('EV-GRD-01', "section:{$section->id}:".($published ?? now()->toDateTimeString()), $this->recipients->sectionStudents($section->id), [
            'hoc_phan' => $section->subject?->name,
            'lop' => $section->code,
        ]);
    }

    /** EV-GRD-02: sinh viên và giảng viên của lớp. */
    public function gradeAdjusted(GradeAdjusted $event): void
    {
        $enrollment = Enrollment::query()->with('section:id,code,subject_id', 'subject:id,name', 'student:id,student_code,user_id')->find($event->enrollmentId);

        if ($enrollment === null) {
            return;
        }

        $vars = ['hoc_phan' => $enrollment->subject?->name, 'lop' => $enrollment->section?->code, 'mssv' => $enrollment->student?->student_code];

        $this->notifier->notify('EV-GRD-02', "request:{$event->changeRequestId}", $this->recipients->students([$enrollment->student_id]), $vars);
        $this->notifier->notify('EV-GRD-02', "request:{$event->changeRequestId}:lecturer", $this->recipients->sectionLecturers($enrollment->course_section_id), $vars, '/teacher');
    }

    /** EV-ATT-01 (cảnh báo vắng): sinh viên, cố vấn; EV-ATT-02 (cấm thi): thêm Phòng Đào tạo. Không kèm tỷ lệ vắng trong nội dung gửi email. */
    public function attendanceThreshold(AttendanceThresholdReached $event): void
    {
        $summary = $event->summary->loadMissing('student:id,student_code,full_name', 'section:id,code,subject_id', 'section.subject:id,name');
        $banned = $event->level === AttendanceThresholdReached::BANNED;
        $code = $banned ? 'EV-ATT-02' : 'EV-ATT-01';
        $text = $banned
            ? "Sinh viên {$summary->student?->student_code} vắng vượt ngưỡng cấm thi học phần {$summary->section?->subject?->name} (lớp {$summary->section?->code}), không đủ điều kiện dự thi."
            : "Sinh viên {$summary->student?->student_code} vắng chạm ngưỡng cảnh báo học phần {$summary->section?->subject?->name} (lớp {$summary->section?->code}).";
        $key = "attendance:{$summary->id}:{$event->level}";

        $this->notifier->notify($code, $key, $this->recipients->students([$summary->student_id]), ['noi_dung' => $text], '/student/chuyen-can');
        $this->notifier->notify($code, "{$key}:advisor", $this->recipients->advisorsOf($summary->student_id), ['noi_dung' => $text], '/teacher/lop-co-van');

        if ($banned) {
            $this->notifier->notify($code, "{$key}:office", $this->recipients->withRole('ACAD'), ['noi_dung' => $text], '/admin/diem-danh');
        }
    }
}
