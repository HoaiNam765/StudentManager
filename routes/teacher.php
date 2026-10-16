<?php

use App\Http\Controllers\DashboardController;
use App\Modules\Teacher\Http\Controllers\LecturerProfileController;
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
    // Dashboard dùng dữ liệu mẫu của nhóm FE; thay bằng dữ liệu thật khi có (FR-RPT-010, FR-TCH-009)
    Route::get('/', [DashboardController::class, 'lecturer'])->name('home');

    // Hồ sơ của chính giảng viên (BR-TCH-07): liên hệ, ảnh sửa ngay; họ tên, chức danh, học vị gửi đề nghị chờ xác nhận
    Route::get('profile', [LecturerProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [LecturerProfileController::class, 'update'])->name('profile.update');
    Route::post('profile/photo', [LecturerProfileController::class, 'photo'])->name('profile.photo');
    Route::delete('profile/change-requests/{lecturerChangeRequest}', [LecturerProfileController::class, 'cancelRequest'])->name('profile.change-requests.cancel');
});
