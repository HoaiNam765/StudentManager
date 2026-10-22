<?php

namespace App\Http\Controllers;

use App\Modules\Report\Services\StudentPortalData;
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
     * Cổng thông tin Sinh viên (Desktop)
     */
    public function student(): View
    {
        return view('student.student-dashboard', $this->studentData());
    }

    private function studentData(): array
    {
        return app(StudentPortalData::class)->for(request()->user());
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
        return view('student.student-mobile', $this->studentData());
    }
}
