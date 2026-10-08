<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Các trang tài khoản của nhóm FE (issue #6). Đổi mật khẩu do module Auth xử lý
 * (route `password.change` và `password.update`), không làm lại ở đây.
 * Phiên đăng nhập và tùy chọn thông báo đang là dữ liệu mẫu, chưa lưu gì.
 */
class AccountController extends Controller
{
    /** Cài đặt tài khoản */
    public function settings(Request $request): View
    {
        $user = $request->user();

        return view('account.settings', [
            'user' => $user,
            'roles' => $user->activeRoles()->pluck('roles.name'),
        ]);
    }

    /** Phiên đăng nhập đang hoạt động (dữ liệu mẫu) */
    public function sessions(): View
    {
        $sessions = [
            [
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

        return view('account.sessions', compact('sessions'));
    }

    /** Tùy chọn thông báo học vụ (dữ liệu mẫu) */
    public function notifications(): View
    {
        $channels = [
            'email' => ['label' => 'Email', 'description' => 'Gửi thông báo tới hộp thư của tài khoản', 'icon' => 'mail', 'enabled' => true],
            'sms' => ['label' => 'Tin nhắn SMS', 'description' => 'Chỉ dùng cho thông báo khẩn', 'icon' => 'sms', 'enabled' => false],
            'in_app' => ['label' => 'Trong hệ thống', 'description' => 'Hiện ở chuông thông báo trên thanh trên cùng', 'icon' => 'notifications', 'enabled' => true],
            'push' => ['label' => 'Thông báo đẩy', 'description' => 'Nhận trên điện thoại khi cài ứng dụng', 'icon' => 'smartphone', 'enabled' => false],
        ];

        return view('account.notifications', compact('channels'));
    }
}
