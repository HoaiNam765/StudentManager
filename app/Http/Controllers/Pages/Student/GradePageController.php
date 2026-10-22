<?php

namespace App\Http\Controllers\Pages\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Trang kết quả học tập của sinh viên (FR-GRD-008..010; mock student/ket-qua-hoc-tap): GPA học kỳ / tích lũy, xếp loại,
 * điểm từng học phần theo học kỳ, tải bảng điểm PDF tiếng Việt / Anh. Chỉ trả view; dữ liệu do JavaScript gọi API GRD
 * (routes/student.php), chỉ gồm lớp đã công bố điểm.
 */
class GradePageController extends Controller
{
    public function index(Request $request): View
    {
        return view('student.grade.results', ['pageData' => [
            'api' => [
                'gpa' => route('student.gpa'),
                'transcript' => route('student.transcript'),
                'grades' => route('student.grades'),
            ],
        ]]);
    }
}
