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

// Trang gốc: đã đăng nhập thì về trang chủ theo vai trò, chưa thì về trang đăng nhập
Route::get('/', function (Request $request, PortalResolver $portals) {
    $home = $request->user() !== null ? $portals->homeRouteFor($request->user()) : null;

    return redirect()->route($home ?? 'login');
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
