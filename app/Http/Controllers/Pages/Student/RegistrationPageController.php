<?php

namespace App\Http\Controllers\Pages\Student;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Trang giao diện đăng ký học phần của sinh viên (FR-ENR-002, 003, 005..007, 010). Chỉ trả view; dữ liệu do
 * JavaScript gọi API của module ENR (routes/student.php). Cờ quyền chỉ dùng để ẩn nút, API vẫn kiểm tra lại.
 * Đối chiếu mock: docs/fe-be-mapping.md, nhóm (a).
 */
class RegistrationPageController extends Controller
{
    public function __construct(private readonly AccessControl $access) {}

    /** Đăng ký học phần (gộp mock student/dang-ky-hoc-phan và student/dang-ky-hoc-phan-mobile thành một trang). */
    public function index(Request $request): View
    {
        return view('student.enrollment.register', ['pageData' => [
            'api' => $this->api(),
            'links' => ['result' => route('student.pages.registration-result')],
            // Giờ API trả theo múi giờ hiển thị; đồng hồ đếm ngược cần độ lệch để đổi sang thời điểm tuyệt đối
            'tzOffset' => now(config('studentmanager.display_timezone'))->format('P'),
            'can' => [
                'enroll' => $this->access->allows($request->user(), 'ENR', PermissionAction::Create),
                'cancel' => $this->access->allows($request->user(), 'ENR', PermissionAction::Delete),
            ],
        ]]);
    }

    /** Kết quả đăng ký, thời khóa biểu tuần và phiếu đăng ký PDF (mock student/ket-qua-dang-ky). */
    public function result(Request $request): View
    {
        return view('student.enrollment.result', ['pageData' => [
            // Danh sách năm học – học kỳ để chọn học kỳ xem kết quả (ACY.view toàn trường: mọi vai trò có)
            'api' => $this->api() + ['terms' => route('admin.academic-years.index')],
            'links' => ['register' => route('student.pages.registration')],
            'termId' => $request->integer('term_id') ?: null,
            'school' => config('studentmanager.school.name'),
            'can' => [
                'cancel' => $this->access->allows($request->user(), 'ENR', PermissionAction::Delete),
            ],
        ]]);
    }

    /** Địa chỉ API cho JavaScript (không viết cứng URL trong mã JS); `__ID__` được thay bằng mã đăng ký. */
    private function api(): array
    {
        return [
            'options' => route('student.registration.options'),
            'enrollments' => route('student.registration.enrollments'),
            'enroll' => route('student.registration.enroll'),
            'cancel' => route('student.registration.cancel', ['enrollment' => '__ID__']),
            'withdraw' => route('student.registration.withdraw', ['enrollment' => '__ID__']),
            'change' => route('student.registration.change', ['enrollment' => '__ID__']),
            'result' => route('student.registration.result'),
            'slip' => route('student.registration.slip'),
        ];
    }
}
