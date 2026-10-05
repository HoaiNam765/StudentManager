<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Cổng Giảng viên
|--------------------------------------------------------------------------
| Tiền tố URL: /teacher   ·   Tiền tố tên route: teacher.
| Danh sách màn hình: docs/BA.md mục 8.2.
| Mọi route cần đăng nhập; quyền kiểm tra bằng middleware `permission` và Policy.
*/

Route::middleware('auth')->group(function (): void {
    // Trang chủ tạm; thay bằng dashboard giảng viên (FR-RPT-010, FR-TCH-009) khi có
    Route::get('/', fn () => view('portal-home', ['portal' => 'Cổng Giảng viên']))->name('home');
});
