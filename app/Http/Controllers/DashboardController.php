<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Modules\Report\Services\PortalOverview;
use App\Modules\SchoolClass\Models\Cohort;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tổng quan Quản lý Đào tạo theo vai trò (#118; FR-RPT-001, 002, 008). Dữ liệu thật do JavaScript gọi
     * `admin.dashboard.data`; mẫu giao diện cũ của nhóm FE vẫn ở `admin/admin-dashboard.blade.php`.
     */
    public function admin(): View
    {
        return view('admin.dashboard', ['pageData' => [
            'api' => ['data' => route('admin.dashboard.data')],
            'links' => array_filter([
                'gradeChanges' => Route::has('admin.pages.grade-changes') ? route('admin.pages.grade-changes') : null,
                'announcements' => Route::has('admin.pages.announcements') ? route('admin.pages.announcements') : null,
            ]),
        ]]);
    }

    /**
     * Tổng quan cổng sinh viên (FR-RPT-009) theo các biến view FE student/student-dashboard đang đọc (`$student`,
     * `$todayClasses`, `$recentGrades`), dữ liệu thật của chính sinh viên (PortalOverview).
     */
    public function student(Request $request, PortalOverview $overview): View
    {
        return view('student.student-dashboard', $this->studentView($request->user(), $overview));
    }

    /** Cổng Sinh viên trên điện thoại (view FE student/student-mobile), cùng dữ liệu thật với trang máy tính. */
    public function studentMobile(Request $request, PortalOverview $overview): View
    {
        $data = $this->studentView($request->user(), $overview);
        // View điện thoại định dạng điểm bằng number_format: chỉ lấy học phần có điểm số, truyền dạng số
        $data['recentGrades'] = array_values(array_map(
            fn (array $g) => ['score' => (float) $g['score10']] + $g,
            array_filter($data['recentGrades'], fn (array $g) => $g['score10'] !== null),
        ));

        return view('student.student-mobile', $data);
    }

    /**
     * Tài khoản chưa gắn hồ sơ sinh viên: truyền giá trị rỗng / 0 thay vì để view hiện số mẫu của FE.
     *
     * @return array{student: array<string, mixed>, todayClasses: list<array<string, mixed>>, recentGrades: list<array<string, mixed>>}
     */
    private function studentView(User $user, PortalOverview $overview): array
    {
        $o = $overview->student($user);

        if ($o === null) {
            return [
                'student' => [
                    'name' => $user->name, 'student_id' => '—', 'major' => '—', 'cohort' => '—', 'class' => 'Chưa liên kết hồ sơ',
                    'credits_earned' => 0, 'credits_total' => 0, 'credits_pct' => 0, 'credits_remaining' => 0,
                    'gpa_4' => 0.0, 'gpa_10' => 0.0, 'classification' => 'Chưa có kết quả',
                    'current_courses_count' => 0, 'current_credits' => 0,
                    'tuition_status' => 'Chưa có dữ liệu học phí', 'tuition_debt' => 0,
                ],
                'todayClasses' => [],
                'recentGrades' => [],
            ];
        }

        $student = [
            'name' => $o['profile']['name'],
            'student_id' => $o['profile']['code'],
            'major' => $o['profile']['major'] ?? '—',
            'cohort' => $o['profile']['cohort'] === null ? '—' : (Cohort::query()->where('start_year', $o['profile']['cohort'])->value('code') ?? Cohort::codeFor((int) $o['profile']['cohort'])),
            'class' => $o['profile']['class'] ?? '—',
            'credits_earned' => $o['credits']['earned'],
            'credits_total' => $o['credits']['total'],
            'credits_pct' => $o['credits']['pct'],
            'credits_remaining' => max(0, $o['credits']['total'] - $o['credits']['earned']),
            'gpa_4' => (float) ($o['cumulative_gpa'] ?? 0),
            'gpa_10' => (float) ($o['gpa10'] ?? 0),
            'classification' => $o['classification'] ?? 'Chưa xếp loại',
            'current_courses_count' => $o['current']['courses'],
            'current_credits' => $o['current']['credits'],
            // Học phí (FEE) chưa làm: không hiện số nợ giả
            'tuition_status' => 'Chưa có dữ liệu học phí',
            'tuition_debt' => 0,
        ];

        $todayClasses = array_map(fn (array $s) => $this->classCard($s), $o['today_schedule']);

        $recentGrades = array_map(fn (array $r) => [
            'course_name' => $r['subject'],
            'credits' => $r['credits'],
            'semester' => $r['term'],
            'score' => $r['score'] ?? '—',
            'score10' => $r['score10'],
            'letter' => $r['letter'] ?? '—',
        ], $o['recent_results']);

        return compact('student', 'todayClasses', 'recentGrades');
    }

    /** Một buổi học hôm nay theo khóa view FE: `status` ongoing / upcoming / done tính theo giờ hiện tại (UTC+7). */
    private function classCard(array $session): array
    {
        [$from, $to] = array_pad(explode('–', $session['time']), 2, null);
        $now = now(config('studentmanager.display_timezone'))->format('H:i');
        $off = ! in_array($session['status'], ['planned', 'makeup', 'taught'], true);
        $status = match (true) {
            $off => 'done',
            $from !== null && $to !== null && $now >= $from && $now <= $to => 'ongoing',
            $from !== null && $now < $from => 'upcoming',
            default => 'done',
        };

        return [
            'time' => str_replace('–', ' - ', $session['time']),
            'status' => $status,
            'status_label' => $off ? $session['status_label'] : ['ongoing' => 'Đang diễn ra', 'upcoming' => 'Sắp diễn ra', 'done' => 'Đã kết thúc'][$status],
            'course_name' => $session['subject'],
            'room' => $session['room'] ?? '—',
            'lecturer' => $session['lecturer'] ?? '—',
        ];
    }

    /**
     * Tổng quan cổng giảng viên (FR-RPT-010; FR-TCH-009): lịch dạy hôm nay, lớp học kỳ này và trạng thái bảng điểm, việc
     * cần xử lý, yêu cầu điều chỉnh điểm, hạn chót, thông báo — dữ liệu thật (PortalOverview).
     */
    public function lecturer(Request $request, PortalOverview $overview): View
    {
        return view('teacher.lecturer-dashboard', ['overview' => $overview->lecturer($request->user())]);
    }
}
