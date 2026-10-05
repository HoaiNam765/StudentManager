<?php

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
    // Trang chủ tạm; thay bằng dashboard sinh viên (FR-RPT-009) khi có
    Route::get('/', fn () => view('portal-home', ['portal' => 'Cổng Sinh viên']))->name('home');
});
