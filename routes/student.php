<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Cổng Sinh viên
|--------------------------------------------------------------------------
| Tiền tố URL: /student   ·   Tiền tố tên route: student.
| Danh sách màn hình: docs/BA.md mục 8.2.
| Mọi route cần đăng nhập; quyền kiểm tra bằng middleware `permission` và Policy.
*/

Route::middleware('auth')->group(function (): void {
    // Dashboard dùng dữ liệu mẫu của nhóm FE; thay bằng dữ liệu thật khi có (FR-RPT-009)
    Route::get('/', [DashboardController::class, 'student'])->name('home');
    Route::get('/mobile', [DashboardController::class, 'studentMobile'])->name('mobile');
});
