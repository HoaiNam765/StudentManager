<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AccountController extends Controller
{
    /**
     * Trang tổng hợp Cài đặt tài khoản & Bảo mật
     */
    public function settings(): View
    {
        $user = [
            'name' => 'Nguyễn Văn An',
            'student_id' => '2001210123',
            'email' => 'an.nv@huit.edu.vn',
            'phone' => '0987 654 321',
            'faculty' => 'Khoa Công nghệ Thông tin',
            'specialization' => 'Kỹ thuật Phần mềm (Khóa 12)',
            'status' => 'Sinh viên đang học',
        ];

        return view('student-settings', compact('user'));
    }

    /**
     * Trang Đổi mật khẩu
     */
    public function changePassword(): View
    {
        return view('change-password');
    }

    /**
     * Xử lý xác thực và cập nhật mật khẩu mới theo chuẩn HUIT
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => [
                'required',
                'string',
                'min:8',
                'max:32',
                'regex:/[A-Z]/',      // ít nhất 1 chữ hoa
                'regex:/[a-z]/',      // ít nhất 1 chữ thường
                'regex:/[0-9]/',      // ít nhất 1 chữ số
                'regex:/[@$!%*#?&]/', // ít nhất 1 ký tự đặc biệt
                'different:current_password',
            ],
            'confirm_password' => ['required', 'same:new_password'],
        ], [
            'new_password.min' => 'Mật khẩu phải có tối thiểu 8 ký tự.',
            'new_password.regex' => 'Mật khẩu phải chứa ít nhất 1 chữ hoa, 1 chữ thường, 1 số và 1 ký tự đặc biệt.',
            'new_password.different' => 'Mật khẩu mới không được trùng với mật khẩu cũ.',
            'confirm_password.same' => 'Mật khẩu xác nhận không trùng khớp.',
        ]);

        return redirect()->back()->with('success', 'Mật khẩu đã được cập nhật thành công!');
    }

    /**
     * Quản lý Phiên đăng nhập đang hoạt động
     */
    public function sessions(): View
    {
        $sessions = [
            [
                'id' => 'sess_1',
                'device_name' => 'Google Chrome trên Windows 11',
                'device_type' => 'desktop',
                'is_current' => true,
                'location' => 'TP. Hồ Chí Minh, Việt Nam',
                'ip_address' => '115.78.23.45',
                'network' => 'Mạng HUIT',
                'login_time' => 'Hôm nay lúc 08:15 (Đang online)',
                'browser' => 'Chrome 124.0 • Windows 64-bit',
            ],
            [
                'id' => 'sess_2',
                'device_name' => 'Safari trên iPhone 14 Pro',
                'device_type' => 'mobile',
                'is_current' => false,
                'location' => 'TP. Hồ Chí Minh, Việt Nam',
                'ip_address' => '14.241.12.89',
                'network' => 'Viettel 4G/5G',
                'login_time' => '2 giờ trước (14:30 - 14/03/2026)',
                'browser' => 'Mobile Safari 17.4 • iOS 17.4.1',
            ],
            [
                'id' => 'sess_3',
                'device_name' => 'Google Chrome trên Samsung Galaxy Tab S9',
                'device_type' => 'tablet',
                'is_current' => false,
                'location' => 'Hà Nội, Việt Nam',
                'ip_address' => '118.69.182.202',
                'network' => 'FPT Telecom',
                'login_time' => '3 ngày trước (11/03/2026)',
                'browser' => 'Chrome Mobile 123.0 • Android 14',
            ],
        ];

        return view('active-sessions', compact('sessions'));
    }

    /**
     * Đăng xuất khỏi tất cả các thiết bị khác
     */
    public function logoutOtherDevices(Request $request): RedirectResponse
    {
        return redirect()->back()->with('success', 'Đã đăng xuất khỏi tất cả các thiết bị khác thành công!');
    }

    /**
     * Tùy chọn thông báo học vụ
     */
    public function notifications(): View
    {
        $channels = ['email' => true, 'sms' => false, 'in_app' => true, 'push' => false];
        $frequency = 'daily';

        return view('notification-preferences', compact('channels', 'frequency'));
    }

    /**
     * Cập nhật tùy chọn thông báo
     */
    public function updateNotifications(Request $request): RedirectResponse
    {
        return redirect()->back()->with('success', 'Đã lưu cấu hình thông báo thành công!');
    }
}