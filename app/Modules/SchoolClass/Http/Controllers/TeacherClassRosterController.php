<?php

namespace App\Modules\SchoolClass\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Enrollment\Enums\EnrollmentStatus;
use App\Modules\Grade\Enums\GradePublicationStatus;
use App\Modules\SchoolClass\Models\AdminClass;
use App\Modules\SchoolClass\Models\AdminClassMember;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Services\ClassDirectoryService;
use App\Modules\Timetable\Models\ScheduleRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Danh sách lớp được giảng viên phụ trách và sinh viên thuộc lớp (FR-TCH-011, BR-TCH-04). */
class TeacherClassRosterController extends Controller
{
    public function __construct(
        private readonly AccessControl $access,
        private readonly ClassDirectoryService $directory,
    ) {}

    public function sections(Request $request): JsonResponse
    {
        $sections = $this->directory->taughtSections($request->user())->paginate(50);
        $ids = $sections->getCollection()->pluck('id');
        // Sĩ số, lịch học, trạng thái bảng điểm cho thẻ lớp của trang giảng viên (mock teacher/lop-hoc-phan)
        $sizes = DB::table('enrollments')->whereIn('course_section_id', $ids)->whereIn('status', EnrollmentStatus::activeValues())
            ->whereNull('deleted_at')->groupBy('course_section_id')->selectRaw('course_section_id, count(*) as n')->pluck('n', 'course_section_id');
        $rules = DB::table('schedule_rules')->leftJoin('rooms', 'rooms.id', '=', 'schedule_rules.room_id')
            ->whereIn('schedule_rules.course_section_id', $ids)->whereNull('schedule_rules.deleted_at')
            ->orderBy('schedule_rules.day_of_week')->orderBy('schedule_rules.period_from')
            ->get(['schedule_rules.course_section_id', 'day_of_week', 'period_from', 'period_to', 'week_from', 'week_to', 'rooms.code as room'])
            ->groupBy('course_section_id');
        $workflows = DB::table('section_grade_workflows')->whereIn('course_section_id', $ids)->whereNull('deleted_at')->pluck('status', 'course_section_id');

        return response()->json($sections->through(fn (CourseSection $section) => [
            'id' => $section->id,
            'code' => $section->code,
            'status' => $section->status->value,
            'status_label' => $section->status->label(),
            'subject' => $section->subject?->only('id', 'code', 'name', 'credits'),
            'term' => $section->term?->only('id', 'name'),
            'academic_year' => $section->term?->academicYear?->only('id', 'name'),
            'max_size' => $section->max_size,
            'students_count' => (int) ($sizes[$section->id] ?? 0),
            'schedule' => ($rules->get($section->id) ?? collect())->map(fn ($r) => [
                'day_of_week' => (int) $r->day_of_week,
                'day_label' => ScheduleRule::DAYS[(int) $r->day_of_week] ?? '',
                'period_from' => (int) $r->period_from,
                'period_to' => (int) $r->period_to,
                'week_from' => (int) $r->week_from,
                'week_to' => (int) $r->week_to,
                'room' => $r->room,
            ])->values(),
            'grade_status' => ($status = GradePublicationStatus::tryFrom($workflows[$section->id] ?? 'draft')) === null ? null : [
                'value' => $status->value,
                'label' => $status->label(),
                'editable' => $status->editable(),
            ],
        ]));
    }

    public function sectionStudents(Request $request, CourseSection $courseSection): JsonResponse
    {
        abort_unless(
            $this->access->allowsOn($request->user(), 'CLS', PermissionAction::View, $courseSection),
            403,
            'Bạn không có quyền xem danh sách sinh viên của lớp học phần này. Hãy chọn lớp đang được phân công cho bạn hoặc liên hệ cán bộ Đào tạo.'
        );
        $students = $this->directory->studentsInSection($request->user(), $courseSection)->paginate(50);

        return response()->json($students->through(fn ($enrollment) => [
            'id' => $enrollment->student?->id,
            'student' => $this->directory->presentStudent($enrollment->student),
        ]));
    }

    public function adviseeClasses(Request $request): JsonResponse
    {
        $classes = $this->directory->adviseeClasses($request->user())->paginate(50);

        return response()->json($classes->through(fn (AdminClass $class) => [
            'id' => $class->id,
            'code' => $class->code,
            'name' => $class->name,
            'cohort' => $class->cohort?->only('id', 'code'),
            'major' => $class->major?->only('id', 'code', 'name'),
            'current_students_count' => $class->current_students_count,
        ]));
    }

    public function adviseeStudents(Request $request, AdminClass $adminClass): JsonResponse
    {
        abort_unless(
            $this->access->allowsOn($request->user(), 'CLS', PermissionAction::View, $adminClass),
            403,
            'Bạn không có quyền xem danh sách sinh viên của lớp này. Hãy chọn lớp bạn đang cố vấn hoặc liên hệ cán bộ Đào tạo.'
        );
        $members = $this->directory
            ->studentsInAdminClass($request->user(), $adminClass)
            ->paginate(50);
        // GPA, tín chỉ tích lũy cho cột của mock teacher/lop-co-van-hoc-tap
        $gpas = DB::table('cumulative_gpas')->whereIn('student_id', $members->getCollection()->pluck('student_id'))->whereNull('deleted_at')
            ->get(['student_id', 'gpa', 'credits_earned', 'classification'])->keyBy('student_id');

        return response()->json($members->through(fn (AdminClassMember $member) => [
            'id' => $member->student?->id,
            'student' => $this->directory->presentStudent($member->student, $adminClass),
            'gpa' => ($g = $gpas->get($member->student_id)) === null ? null : [
                'gpa' => $g->gpa === null ? null : (float) $g->gpa,
                'credits_earned' => (int) $g->credits_earned,
                'classification' => $g->classification,
            ],
        ]));
    }
}
