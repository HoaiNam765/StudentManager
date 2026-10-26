<?php

namespace App\Modules\Timetable\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Faculty\Services\FacultyAccess;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Student\Models\Student;
use App\Modules\Teacher\Models\Lecturer;
use App\Modules\Timetable\Models\TermTimetable;
use App\Modules\Timetable\Services\TimetablePublication;
use App\Modules\Timetable\Services\TimetableView;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Xem thời khóa biểu (FR-TTB-004) và nháp – công bố – khóa (FR-TTB-006).
 *
 * Phạm vi: cán bộ có TTB toàn trường xem mọi đối tượng, kể cả khi còn nháp; DEAN xem khoa mình và lớp học phần trong
 * phạm vi; sinh viên, giảng viên xem của chính mình ở cổng riêng và chỉ khi thời khóa biểu đã công bố.
 */
class TimetableController extends Controller
{
    public function __construct(
        private readonly TimetableView $view,
        private readonly TimetablePublication $publication,
        private readonly AccessControl $access,
        private readonly FacultyAccess $faculties,
    ) {}

    public function show(Request $request): JsonResponse
    {
        $data = $request->validate([
            'target' => ['required', Rule::in(TimetableView::TARGETS)],
            'id' => ['required', 'integer'],
        ], [], ['target' => 'đối tượng', 'id' => 'mã đối tượng']);

        $user = $request->user();
        $global = $this->access->allowsGlobally($user, 'TTB', PermissionAction::View);
        abort_unless($global || $this->allowedNarrow($user, $data['target'], (int) $data['id']), 403, 'Bạn không có quyền xem thời khóa biểu này.');

        return $this->respond($request, $data['target'], (int) $data['id'], $global);
    }

    public function mineStudent(Request $request): JsonResponse
    {
        $student = Student::query()->where('user_id', $request->user()->id)->firstOrFail();

        // Sinh viên luôn thấy buổi học của các lớp mình đang đăng ký (đăng ký xong là có lịch, hủy là mất), kể cả khi thời
        // khóa biểu học kỳ chưa công bố; `timetable_status` = draft cho biết lịch còn có thể thay đổi.
        return $this->respond($request, 'student', $student->id, true, true);
    }

    public function mineLecturer(Request $request): JsonResponse
    {
        $lecturer = Lecturer::query()->where('user_id', $request->user()->id)->firstOrFail();

        return $this->respond($request, 'lecturer', $lecturer->id, false);
    }

    public function status(Term $term): JsonResponse
    {
        $record = TermTimetable::query()->where('term_id', $term->id)->first();
        $status = $this->publication->status($term);

        return response()->json([
            'term' => $term->only('id', 'name'),
            'status' => $status->value,
            'status_label' => $status->label(),
            'published_at' => $record?->published_at?->setTimezone(config('studentmanager.display_timezone'))->format('Y-m-d H:i'),
            'locked_at' => $record?->locked_at?->setTimezone(config('studentmanager.display_timezone'))->format('Y-m-d H:i'),
            'change_notice_hours' => (int) config('studentmanager.timetable.change_notice_hours'),
        ]);
    }

    public function publish(Request $request, Term $term): JsonResponse
    {
        $this->publication->publish($term, $request->user());

        return $this->status($term);
    }

    public function lock(Term $term): JsonResponse
    {
        $this->publication->lock($term);

        return $this->status($term);
    }

    private function respond(Request $request, string $target, int $id, bool $seesDraft, bool $ownDraftNotice = false): JsonResponse
    {
        $request->validate([
            'view' => ['sometimes', Rule::in(['week', 'month', 'term'])],
            'date' => ['sometimes', 'date_format:Y-m-d'],
            'term_id' => ['sometimes', 'integer', 'exists:terms,id'],
        ], [], ['view' => 'dạng xem', 'date' => 'ngày', 'term_id' => 'học kỳ']);

        $term = $request->filled('term_id')
            ? Term::query()->findOrFail($request->integer('term_id'))
            : $this->termFor($request->input('date'));
        $view = $request->input('view', 'week');
        $range = $this->view->range($view, $request->input('date'), $term);
        $status = $this->publication->status($term);
        $published = $this->publication->isVisible($term);
        $visible = $seesDraft || $published;
        $message = match (true) {
            ! $visible => "Thời khóa biểu {$term->name} chưa công bố.",
            ! $published && $ownDraftNotice => "Thời khóa biểu {$term->name} chưa công bố: đây là lịch dự kiến của các lớp bạn đã đăng ký, có thể còn thay đổi.",
            default => null,
        };

        return response()->json([
            'target' => $target,
            'id' => $id,
            'term' => $term->only('id', 'name'),
            'timetable_status' => $status->value,
            'view' => $view,
            'from' => $range['from']->toDateString(),
            'to' => $range['to']->toDateString(),
            'message' => $message,
            'sessions' => $visible ? $this->view->sessions($target, $id, $term, $range['from'], $range['to']) : [],
        ]);
    }

    /** Học kỳ chứa ngày xem (mặc định hôm nay), không có thì học kỳ gần nhất sắp tới. */
    private function termFor(?string $date): Term
    {
        $day = $date ?? now(config('studentmanager.display_timezone'))->toDateString();

        return Term::query()->where('start_date', '<=', $day)->where('end_date', '>=', $day)->first()
            ?? Term::query()->where('start_date', '>', $day)->orderBy('start_date')->first()
            ?? abort(404, 'Không tìm thấy học kỳ.');
    }

    private function allowedNarrow(User $user, string $target, int $id): bool
    {
        return match ($target) {
            'faculty' => $this->access->allows($user, 'TTB', PermissionAction::View) && in_array($id, $this->faculties->facultyIds($user), true),
            'section' => ($section = CourseSection::query()->find($id)) !== null && $this->access->allowsOn($user, 'TTB', PermissionAction::View, $section),
            default => false,
        };
    }
}
