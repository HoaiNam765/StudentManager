<?php

namespace App\Http\Controllers;

use App\Modules\Report\Services\PortalOverview;
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
     * `$todayClasses`, `$recentGrades`), dữ liệu thật của chính sinh viên (PortalOverview). Tài khoản chưa gắn hồ sơ
     * sinh viên thì không truyền biến (view tự hiện giá trị mặc định của FE).
     */
    public function student(Request $request, PortalOverview $overview): View
    {
        $o = $overview->student($request->user());

        if ($o === null) {
            return view('student.student-dashboard');
        }

        $student = [
            'name' => $o['profile']['name'],
            'student_id' => $o['profile']['code'],
            'major' => $o['profile']['major'] ?? '—',
            'cohort' => $o['profile']['cohort'] === null ? '—' : 'K'.$o['profile']['cohort'],
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
            'letter' => $r['letter'] ?? '—',
        ], $o['recent_results']);

        return view('student.student-dashboard', compact('student', 'todayClasses', 'recentGrades'));
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

    /** Dữ liệu mẫu cho dashboard sinh viên trên điện thoại (chưa nối dữ liệu thật) */
    private function studentData(): array
    {
        $student = [
            'name' => 'Nguyễn Văn An',
            'student_id' => '2001210123',
            'major' => 'Công nghệ Thông tin',
            'cohort' => '12 (2021–2025)',
            'class' => '12DHTT01',
            'credits_earned' => 84,
            'credits_total' => 132,
            'credits_pct' => 63.6,
            'credits_remaining' => 48,
            'gpa_4' => 3.42,
            'gpa_10' => 8.35,
            'classification' => 'Giỏi',
            'current_courses_count' => 6,
            'current_credits' => 18,
            'tuition_debt' => 0,
            'tuition_status' => 'Đã hoàn tất',
        ];

        $todayClasses = [
            [
                'time' => '07:30 - 09:10',
                'status' => 'ongoing',
                'status_label' => 'Đang diễn ra',
                'course_name' => 'Lập trình Web nâng cao',
                'room' => 'A3.04',
                'lecturer' => 'TS. Trần Hoàng Nam',
            ],
            [
                'time' => '09:30 - 11:10',
                'status' => 'upcoming',
                'status_label' => 'Sắp bắt đầu',
                'course_name' => 'Cơ sở dữ liệu phân tán',
                'room' => 'B1.02',
                'lecturer' => 'ThS. Lê Thị Bình',
            ],
            [
                'time' => '13:00 - 15:35',
                'status' => 'afternoon',
                'status_label' => 'Chiều nay • 3 Tiết',
                'course_name' => 'Thực hành Lập trình Web',
                'room' => 'PM04 (Phòng máy)',
                'lecturer' => 'TS. Trần Hoàng Nam',
            ],
        ];

        $recentGrades = [
            ['course_name' => 'Lập trình Web API', 'credits' => 3, 'semester' => 'HK trước', 'score' => 9.5, 'letter' => 'A+'],
            ['course_name' => 'Hệ quản trị CSDL SQL', 'credits' => 3, 'semester' => 'HK trước', 'score' => 8.8, 'letter' => 'A'],
            ['course_name' => 'Hệ điều hành máy tính', 'credits' => 3, 'semester' => 'HK trước', 'score' => 7.8, 'letter' => 'B+'],
            ['course_name' => 'Kiến trúc máy tính', 'credits' => 3, 'semester' => 'HK trước', 'score' => 8.5, 'letter' => 'A'],
        ];

        return compact('student', 'todayClasses', 'recentGrades');
    }

    /**
     * Tổng quan cổng giảng viên (FR-RPT-010; FR-TCH-009): lịch dạy hôm nay, lớp học kỳ này và trạng thái bảng điểm, việc
     * cần xử lý, yêu cầu điều chỉnh điểm, hạn chót, thông báo — dữ liệu thật (PortalOverview).
     */
    public function lecturer(Request $request, PortalOverview $overview): View
    {
        return view('teacher.lecturer-dashboard', ['overview' => $overview->lecturer($request->user())]);
    }

    /**
     * Cổng Sinh viên (Mobile Web)
     */
    public function studentMobile(): View
    {
        return view('student.student-mobile', $this->studentData());
    }
}
