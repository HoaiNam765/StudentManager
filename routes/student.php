<?php

use App\Http\Controllers\DashboardController;
use App\Modules\Enrollment\Http\Controllers\RegistrationOptionsController;
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

    // Đăng ký học phần (ENR, FR-ENR-002): đợt đang mở của sinh viên và các lớp được chọn (JSON)
    Route::get('/registration/options', [RegistrationOptionsController::class, 'mine'])->middleware('permission:ENR.view')->name('registration.options');
});
