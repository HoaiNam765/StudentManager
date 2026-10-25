<?php

namespace App\Modules\Attendance\Services;

use App\Models\User;
use App\Modules\Attendance\Models\AttendanceRecord;
use App\Modules\Attendance\Models\AttendanceSummary;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Student\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Báo cáo chuyên cần (#144; FR-ATT-007, 010; BR-ATT-08): tổng hợp theo lớp học phần, lớp hành chính, khoa, sinh viên,
 * học kỳ, mức cảnh báo — luôn lọc theo phạm vi ATT.view của người xem; chuyên cần cá nhân của sinh viên.
 */
class AttendanceReport
{
    public function __construct(private readonly AccessControl $access) {}

    /**
     * Truy vấn tổng hợp theo bộ lọc: term_id, course_section_id, admin_class_id, faculty_id, flag (warning | banned), q.
     *
     * @param  array<string, mixed>  $filters
     */
    public function summaries(array $filters): Builder
    {
        $q = trim((string) ($filters['q'] ?? ''));

        return AttendanceSummary::query()
            ->with(['student:id,student_code,full_name,cohort', 'section:id,code,subject_id,term_id', 'section.subject:id,code,name'])
            ->whereIn('enrollment_id', Enrollment::query()->active()->select('id'))
            ->when($filters['term_id'] ?? null, fn (Builder $b, $v) => $b->whereIn('course_section_id', DB::table('course_sections')->select('id')->where('term_id', $v)))
            ->when($filters['course_section_id'] ?? null, fn (Builder $b, $v) => $b->where('course_section_id', $v))
            ->when($filters['faculty_id'] ?? null, fn (Builder $b, $v) => $b->whereIn('course_section_id', DB::table('course_sections')->select('course_sections.id')
                ->join('subjects', 'subjects.id', '=', 'course_sections.subject_id')
                ->join('departments', 'departments.id', '=', 'subjects.department_id')
                ->where('departments.faculty_id', $v)))
            ->when($filters['admin_class_id'] ?? null, fn (Builder $b, $v) => $b->whereIn('student_id', DB::table('admin_class_members')->select('student_id')
                ->where('admin_class_id', $v)->whereNull('deleted_at')->whereNull('effective_to')))
            ->when(($filters['flag'] ?? null) === 'warning', fn (Builder $b) => $b->where('warning', true)->where('banned', false))
            ->when(($filters['flag'] ?? null) === 'banned', fn (Builder $b) => $b->where('banned', true))
            ->when($q !== '', fn (Builder $b) => $b->whereIn('student_id', Student::query()->select('id')->search($q)))
            ->orderByDesc('absence_rate')->orderBy('id');
    }

    public function scoped(User $user, array $filters): Builder
    {
        return $this->access->constrain($this->summaries($filters), $user, 'ATT', PermissionAction::View);
    }

    /** @return array<string, mixed> */
    public static function present(AttendanceSummary $s): array
    {
        return [
            'id' => $s->id,
            'enrollment_id' => $s->enrollment_id,
            'student' => $s->student?->only('id', 'student_code', 'full_name', 'cohort'),
            'section' => $s->section === null ? null : ['id' => $s->section->id, 'code' => $s->section->code, 'subject' => $s->section->subject?->only('code', 'name')],
            'total_sessions' => $s->total_sessions,
            'recorded_sessions' => $s->recorded_sessions,
            'present_count' => $s->present_count,
            'absent_count' => $s->absent_count,
            'excused_count' => $s->excused_count,
            'late_count' => $s->late_count,
            'absences' => $s->absences,
            'absence_rate' => $s->absence_rate,
            'warning' => $s->warning,
            'banned' => $s->banned,
            'exempted' => $s->exempted,
            'exemption_reason' => $s->exemption_reason,
            'attendance_score' => $s->attendance_score,
            'level' => $s->banned ? 'banned' : ($s->warning ? 'warning' : 'normal'),
            'level_label' => $s->banned ? 'Không đủ điều kiện dự thi' : ($s->exempted ? 'Được miễn cấm thi' : ($s->warning ? 'Cảnh báo vắng' : 'Bình thường')),
        ];
    }

    /** Chuyên cần của chính sinh viên theo học phần và từng buổi (FR-ATT-007). */
    public function forStudent(Student $student, ?int $termId): array
    {
        $enrollments = Enrollment::query()->active()->where('student_id', $student->id)
            ->when($termId, fn (Builder $b) => $b->where('term_id', $termId))
            ->with(['section:id,code,subject_id,term_id', 'subject:id,code,name,credits'])->get();
        $summaries = AttendanceSummary::query()->whereIn('enrollment_id', $enrollments->pluck('id'))->with('section.subject')->get()->keyBy('enrollment_id');
        $records = AttendanceRecord::query()->whereIn('enrollment_id', $enrollments->pluck('id'))
            ->with('session:id,date,week,period_from,period_to,status')->get()->groupBy('enrollment_id');

        return $enrollments->map(fn (Enrollment $e) => [
            'enrollment_id' => $e->id,
            'section' => $e->section?->only('id', 'code'),
            'subject' => $e->subject?->only('code', 'name', 'credits'),
            'summary' => ($s = $summaries->get($e->id)) === null ? null : self::present($s),
            'records' => ($records->get($e->id) ?? collect())->sortBy(fn ($r) => $r->session?->date?->toDateString())->values()->map(fn (AttendanceRecord $r) => [
                'date' => $r->session?->date?->toDateString(),
                'week' => $r->session?->week,
                'periods' => $r->session ? "{$r->session->period_from}-{$r->session->period_to}" : null,
                'status' => $r->status->value,
                'status_label' => $r->status->label(),
                'minutes_late' => $r->minutes_late,
                'note' => $r->note,
            ])->all(),
        ])->values()->all();
    }
}
