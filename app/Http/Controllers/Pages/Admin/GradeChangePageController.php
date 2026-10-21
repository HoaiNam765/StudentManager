<?php

namespace App\Http\Controllers\Pages\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Trang duyệt yêu cầu điều chỉnh điểm sau công bố (FR-GRD-006): lãnh đạo khoa duyệt yêu cầu chờ khoa trong phạm vi,
 * Phòng Đào tạo / Khảo thí xác nhận. Chỉ trả view; dữ liệu do JavaScript gọi API GRD (routes/admin.php), quyền từng bước
 * kiểm tra trong GradeChangeService.
 */
class GradeChangePageController extends Controller
{
    public function index(): View
    {
        return view('admin.grade.change-requests', ['pageData' => [
            'api' => [
                'list' => route('admin.grade-change-requests.index'),
                'approve' => route('admin.grade-change-requests.approve', ['gradeChangeRequest' => '__ID__']),
                'reject' => route('admin.grade-change-requests.reject', ['gradeChangeRequest' => '__ID__']),
                'evidence' => route('admin.grade-change-requests.evidence', ['gradeChangeRequest' => '__ID__']),
            ],
        ]]);
    }
}
