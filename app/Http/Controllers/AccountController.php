<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Các trang tài khoản của nhóm FE (issue #6). Đổi mật khẩu do module Auth xử lý
 * (route `password.change` và `password.update`), không làm lại ở đây.
 * Phiên đăng nhập do module Auth xử lý (`SessionController`, issue #84). Tùy chọn thông báo đang là dữ liệu mẫu, chưa lưu gì.
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
