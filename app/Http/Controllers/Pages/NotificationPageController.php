<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Trung tâm thông báo (FR-NOT-001; mock FE #29) dùng chung cho cả ba cổng. Chỉ trả view; dữ liệu do JavaScript gọi API
 * `/notifications` (chỉ thông báo của người đang đăng nhập).
 */
class NotificationPageController extends Controller
{
    public function index(): View
    {
        return view('notification.center', ['pageData' => [
            'api' => [
                'list' => route('notifications.index'),
                'read' => route('notifications.read', ['notification' => '__ID__']),
                'readAll' => route('notifications.read-all'),
            ],
        ]]);
    }
}
