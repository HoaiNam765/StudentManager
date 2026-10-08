<?php

use App\Modules\Auth\Http\Controllers\LoginController;
use App\Modules\Auth\Http\Controllers\PasswordController;
use App\Modules\Auth\Services\PortalResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Trang công khai và đăng nhập
|--------------------------------------------------------------------------
| Ba cổng (sinh viên, giảng viên, quản trị) nằm ở routes/student.php, teacher.php, admin.php.
*/

// Trang gốc: tạm cho phép truy cập mẫu test giao diện không cần đăng nhập
// Route::get('/', function (Request $request, PortalResolver $portals) {
//     $home = $request->user() !== null ? $portals->homeRouteFor($request->user()) : null;
//
//     return redirect()->route($home ?? 'login');
// })->name('root');

Route::get('/', function () {
    return view('app'); // Hiển thị layout mẫu test (resources/views/app.blade.php)
})->name('root');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:login')->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/password/change', [PasswordController::class, 'edit'])->name('password.change');
    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');
});


//Mẫu test giao diện
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AccountController;



// 1. Phân hệ Admin / Phòng Đào tạo
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
});

// 2. Phân hệ Sinh viên
Route::prefix('sinh-vien')->name('student.')->group(function () {
    Route::get('/tong-quan', [DashboardController::class, 'student'])->name('dashboard');
    Route::get('/mobile', [DashboardController::class, 'studentMobile'])->name('mobile');
});

// 3. Phân hệ Giảng viên
Route::prefix('giang-vien')->name('lecturer.')->group(function () {
    Route::get('/tong-quan', [DashboardController::class, 'lecturer'])->name('dashboard');
});

// 4. Quản lý Tài khoản & Bảo mật
Route::prefix('tai-khoan')->group(function () {
    Route::get('/cai-dat', [AccountController::class, 'settings'])->name('student.settings');
    Route::get('/doi-mat-khau', [AccountController::class, 'changePassword'])->name('password.change');
    Route::post('/doi-mat-khau', [AccountController::class, 'updatePassword'])->name('password.update');
    Route::get('/phien-dang-nhap', [AccountController::class, 'sessions'])->name('sessions.index');
    Route::post('/phien-dang-nhap/dang-xuat-khac', [AccountController::class, 'logoutOtherDevices'])->name('sessions.logout-others');
    Route::get('/thong-bao', [AccountController::class, 'notifications'])->name('notifications.settings');
    Route::post('/thong-bao', [AccountController::class, 'updateNotifications'])->name('notifications.update');
});