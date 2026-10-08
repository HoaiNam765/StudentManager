<?php
// Mẫu test Giao diện
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tổng quan Quản lý Đào tạo (Admin / Phòng Đào tạo)
     */
    public function admin(): View
    {
        $kpis = [
            'total_students' => 18450,
            'growth_rate' => '+3.2% so với năm 2023',
            'lecturers' => 680,
            'lecturers_assigned_pct' => 98,
            'course_classes' => 1240,
            'classes_roomed_pct' => 94,
            'pending_requests' => 28,
            'urgent_requests' => 12,
        ];

        $requests = [
            [
                'code' => 'ĐH-2024-0891',
                'student_name' => 'Nguyễn Văn Bảo',
                'student_id' => '2001210452',
                'faculty' => 'Khoa CNTT',
                'type' => 'ĐK học phần trễ',
                'submitted_at' => '15/10/2024',
                'status' => 'pending',
                'status_label' => 'Chờ duyệt',
            ],
            [
                'code' => 'ĐH-2024-0889',
                'student_name' => 'Trần Thị Mai Phương',
                'student_id' => '2001220114',
                'faculty' => 'Khoa Kế toán',
                'type' => 'Hoãn thi kết thúc HP',
                'submitted_at' => '15/10/2024',
                'status' => 'need_docs',
                'status_label' => 'Bổ sung minh chứng',
            ],
            [
                'code' => 'ĐH-2024-0885',
                'student_name' => 'Lê Hoàng Nam',
                'student_id' => '2001200876',
                'faculty' => 'Khoa CN Thực phẩm',
                'type' => 'Miễn HP Anh văn B1',
                'submitted_at' => '14/10/2024',
                'status' => 'verified',
                'status_label' => 'Đã xác thực',
            ],
            [
                'code' => 'ĐH-2024-0878',
                'student_name' => 'Phạm Quốc Huy',
                'student_id' => '2001211090',
                'faculty' => 'Khoa CNTT',
                'type' => 'ĐK học phần trễ',
                'submitted_at' => '14/10/2024',
                'status' => 'pending',
                'status_label' => 'Chờ duyệt',
            ],
            [
                'code' => 'ĐH-2024-0870',
                'student_name' => 'Đỗ Thu Hằng',
                'student_id' => '2001221532',
                'faculty' => 'Khoa QTKD',
                'type' => 'Xin thôi học tạm thời',
                'submitted_at' => '13/10/2024',
                'status' => 'faculty_review',
                'status_label' => 'Chờ Khoa duyệt',
            ],
        ];

        $registrationProgress = [
            'overall_pct' => 78,
            'completed_students' => 14200,
            'total_students' => 18450,
            'cohorts' => [
                ['name' => 'Khóa 2021 (Năm 4)', 'pct' => 98],
                ['name' => 'Khóa 2022 (Năm 3)', 'pct' => 92],
                ['name' => 'Khóa 2023 (Năm 2)', 'pct' => 81],
                ['name' => 'Khóa 2024 (Tân sinh viên)', 'pct' => 45],
            ]
        ];

        $announcements = [
            ['tag' => 'Khẩn', 'time' => 'Hôm nay 09:30', 'title' => 'Chốt sĩ số mở lớp HK1 năm học 2024-2025'],
            ['tag' => 'Đào tạo', 'time' => '14/10/2024', 'title' => 'Kế hoạch phân công phòng học giảng đường khu B'],
            ['tag' => 'Học vụ', 'time' => '12/10/2024', 'title' => 'Hướng dẫn xử lý hồ sơ sinh viên nợ học phí gia hạn'],
        ];

        $systemLogs = [
            ['time' => '10:45', 'actor' => 'Lê Thị Mai Hương', 'action' => 'đã duyệt mở thêm 02 lớp HP Lập trình Web.'],
            ['time' => '10:15', 'actor' => 'TS. Trần Anh Tuấn', 'action' => 'đã cập nhật bảng điểm quá trình Lớp 12DHTH01.'],
            ['time' => '09:30', 'actor' => 'Hệ thống tự động', 'action' => 'gạch nợ học phí cho 340 giao dịch ngân hàng.'],
        ];

        return view('admin.admin-dashboard', compact('kpis', 'requests', 'registrationProgress', 'announcements', 'systemLogs'));
    }

    /**
     * Cổng thông tin Sinh viên (Desktop)
     */
    public function student(): View
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

        return view('student.student-dashboard', compact('student', 'todayClasses', 'recentGrades'));
    }

    /**
     * Cổng Giảng viên
     */
    public function lecturer(): View
    {
        $lecturer = [
            'name' => 'TS. Trần Hoàng Nam',
            'faculty' => 'Khoa Công nghệ Thông tin',
            'role' => 'Giảng viên',
            'pending_tasks_count' => 3,
            'today_classes_count' => 3,
        ];

        $urgentTasks = [
            ['title' => 'Nộp điểm cuối kỳ HP Lập trình Java', 'deadline' => 'Hôm nay 15/10/2026', 'tag' => 'Khẩn cấp'],
            ['title' => 'Phê duyệt đơn xin nghỉ học của SV', 'deadline' => '3 đơn đang chờ duyệt', 'tag' => 'Cần xử lý'],
            ['title' => 'Cập nhật đề cương Công nghệ phần mềm', 'deadline' => 'Thời hạn: 20/10/2026', 'tag' => 'Cần xử lý'],
        ];

        return view('teacher.lecturer-dashboard', compact('lecturer', 'urgentTasks'));
    }

    /**
     * Cổng Sinh viên (Mobile Web)
     */
    public function studentMobile(): View
    {
        return view('student.student-mobile');
    }
}