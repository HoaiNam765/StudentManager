<?php

use App\Http\Controllers\AccountController;
use App\Modules\Auth\Http\Controllers\LoginController;
use App\Modules\Auth\Http\Controllers\PasswordController;
use App\Modules\Auth\Http\Controllers\PasswordResetController;
use App\Modules\Auth\Services\PortalResolver;
use App\Modules\System\Http\Controllers\LookupOptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Trang công khai và đăng nhập
|--------------------------------------------------------------------------
| Ba cổng (sinh viên, giảng viên, quản trị) nằm ở routes/student.php, teacher.php, admin.php.
*/

// Trang gốc: đã đăng nhập thì vào trang chủ theo vai trò, chưa thì về trang đăng nhập
Route::get('/', function (Request $request, PortalResolver $portals) {
    $home = $request->user() !== null ? $portals->homeRouteFor($request->user()) : null;

    return redirect()->route($home ?? 'login');
})->name('root');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:login')->name('login.store');

    // Quên mật khẩu (FR-AUTH-003): liên kết đặt lại gửi qua email, dùng một lần, hết hạn sau 30 phút
    Route::get('/forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'store'])->middleware('throttle:password-reset')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->middleware('throttle:password-reset')->name('password.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/password/change', [PasswordController::class, 'edit'])->name('password.change');
    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');
});

// Trang tài khoản dùng chung cho mọi vai trò; đổi mật khẩu dùng route `password.change` ở trên
Route::middleware(['auth', 'password.changed'])->prefix('tai-khoan')->name('account.')->group(function (): void {
    Route::get('/cai-dat', [AccountController::class, 'settings'])->name('settings');
    Route::get('/phien-dang-nhap', [AccountController::class, 'sessions'])->name('sessions');
    Route::get('/thong-bao', [AccountController::class, 'notifications'])->name('notifications');
});

// Danh sách lựa chọn cho biểu mẫu (danh mục dùng chung, địa chỉ): mọi người dùng đã đăng nhập, chỉ giá trị đang hoạt động
Route::middleware(['auth', 'password.changed'])->prefix('danh-muc')->name('lookups.')->group(function (): void {
    Route::get('/don-vi-hanh-chinh', [LookupOptionController::class, 'administrativeUnits'])->name('administrative-units');
    Route::get('/{category}', [LookupOptionController::class, 'options'])->where('category', '[A-Z][A-Z0-9_]*')->name('options');
});

// Chỉ cho phép truy cập các màn UI mock trên môi trường local
if (app()->environment('local')) {
    require __DIR__.'/fe-preview.php';
}
