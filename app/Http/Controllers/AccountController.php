<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Các trang tài khoản của nhóm FE (issue #6). Đổi mật khẩu do module Auth xử lý
 * (route `password.change` và `password.update`), không làm lại ở đây.
 * Phiên đăng nhập do module Auth xử lý (`SessionController`, issue #84). Tùy chọn thông báo lưu qua module Notification (#116).
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

    /** Tùy chọn nhận thông báo (FR-NOT-007): dữ liệu do JavaScript gọi API `/notifications/preferences` */
    public function notifications(): View
    {
        return view('account.notifications');
    }
}
