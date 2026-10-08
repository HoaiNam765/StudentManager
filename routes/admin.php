<?php

use App\Http\Controllers\DashboardController;
use App\Modules\AcademicYear\Http\Controllers\AcademicYearController;
use App\Modules\Auth\Http\Controllers\RoleController;
use App\Modules\Auth\Http\Controllers\RolePermissionController;
use App\Modules\Auth\Http\Controllers\UserRoleController;
use App\Modules\System\Http\Controllers\AdministrativeUnitController;
use App\Modules\System\Http\Controllers\ImportController;
use App\Modules\System\Http\Controllers\LookupController;
use App\Modules\System\Http\Controllers\PolicySetController;
use App\Modules\System\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Cổng Quản trị / Văn phòng
|--------------------------------------------------------------------------
| Tiền tố URL: /admin   ·   Tiền tố tên route: admin.
| Danh sách màn hình: docs/BA.md mục 8.2.
| Mọi route ở đây cần đăng nhập; quyền kiểm tra bằng middleware `permission` và Policy.
*/

Route::middleware('auth')->group(function (): void {
    // Dashboard dùng dữ liệu mẫu của nhóm FE; thay bằng dữ liệu thật khi có (FR-RPT-001)
    Route::get('/', [DashboardController::class, 'admin'])->name('home');

    // Phân quyền (AUTH): chỉ người có quyền trên toàn trường, mặc định là ADMIN
    Route::middleware('permission:AUTH.view,ALL')->group(function (): void {
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show');
        Route::get('permissions', [RolePermissionController::class, 'index'])->name('permissions.index');
        Route::get('users/{user}/roles', [UserRoleController::class, 'index'])->name('users.roles.index');
    });

    Route::post('roles', [RoleController::class, 'store'])
        ->middleware('permission:AUTH.create,ALL')->name('roles.store');
    Route::put('roles/{role}', [RoleController::class, 'update'])
        ->middleware('permission:AUTH.update,ALL')->name('roles.update');
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])
        ->middleware('permission:AUTH.delete,ALL')->name('roles.destroy');
    Route::put('roles/{role}/permissions', [RolePermissionController::class, 'update'])
        ->middleware('permission:AUTH.update,ALL')->name('roles.permissions.update');
    Route::post('users/{user}/roles', [UserRoleController::class, 'store'])
        ->middleware('permission:AUTH.update,ALL')->name('users.roles.store');
    Route::delete('users/{user}/roles/{role}', [UserRoleController::class, 'destroy'])
        ->middleware('permission:AUTH.update,ALL')->name('users.roles.destroy');

    Route::prefix('academic-years')
        ->name('academic-years.')
        ->group(function (): void {

            Route::get('/', [
                AcademicYearController::class,
                'index',
            ])
                ->middleware('permission:ACY.view,ALL')
                ->name('index');

            Route::get('/current-term', [
                AcademicYearController::class,
                'currentTerm',
            ])
                ->middleware('permission:ACY.view,ALL')
                ->name('current-term');

            Route::get('/{academicYear}', [
                AcademicYearController::class,
                'show',
            ])
                ->middleware('permission:ACY.view,ALL')
                ->name('show');

            Route::post('/', [
                AcademicYearController::class,
                'store',
            ])
                ->middleware('permission:ACY.create,ALL')
                ->name('store');

            Route::put('/{academicYear}', [
                AcademicYearController::class,
                'update',
            ])
                ->middleware('permission:ACY.update,ALL')
                ->name('update');

            Route::post('/terms', [
                AcademicYearController::class,
                'storeTerm',
            ])
                ->middleware('permission:ACY.create,ALL')
                ->name('terms.store');

            Route::post('/terms/{term}/current', [
                AcademicYearController::class,
                'setCurrentTerm',
            ])
                ->middleware('permission:ACY.approve,ALL')
                ->name('terms.current');

            Route::post('/terms/{term}/status/{status}', [
                AcademicYearController::class,
                'changeStatus',
            ])
                ->middleware('permission:ACY.approve,ALL')
                ->name('terms.status');
        });

    // Trung tâm import (SYS, FR-SYS-007): xem = SYS.view, tải lên/kiểm tra/lưu = SYS.create, hoàn tác/xóa = SYS.delete.
    // Quyền trên từng lô (phạm vi dữ liệu) kiểm tra tiếp bằng ImportBatchPolicy.
    Route::prefix('imports')
        ->name('imports.')
        ->group(function (): void {
            Route::middleware('permission:SYS.view')->group(function (): void {
                Route::get('importers', [ImportController::class, 'importers'])->name('importers');
                Route::get('/', [ImportController::class, 'index'])->name('index');
                Route::get('{batch}', [ImportController::class, 'show'])->whereNumber('batch')->name('show');
                Route::get('{batch}/preview', [ImportController::class, 'preview'])->whereNumber('batch')->name('preview');
                Route::get('{batch}/progress', [ImportController::class, 'progress'])->whereNumber('batch')->name('progress');
            });

            Route::middleware('permission:SYS.create')->group(function (): void {
                Route::post('/', [ImportController::class, 'upload'])->name('upload');
                Route::post('{batch}/validate', [ImportController::class, 'validateBatch'])->whereNumber('batch')->name('validate');
                Route::post('{batch}/save', [ImportController::class, 'save'])->whereNumber('batch')->name('save');
            });

            Route::middleware('permission:SYS.delete')->group(function (): void {
                Route::post('{batch}/rollback', [ImportController::class, 'rollback'])->whereNumber('batch')->name('rollback');
                Route::delete('{batch}', [ImportController::class, 'destroy'])->whereNumber('batch')->name('destroy');
            });
        });

    // Danh mục dùng chung và đơn vị hành chính (SYS, FR-SYS-002): xem = SYS.view, thêm = SYS.create, sửa/ngừng = SYS.update, xóa = SYS.delete
    Route::middleware('permission:SYS.view')->group(function (): void {
        Route::get('lookups', [LookupController::class, 'categories'])->name('lookups.index');
        Route::get('lookups/{lookupCategory:code}/values', [LookupController::class, 'values'])->name('lookups.values.index');
        Route::get('administrative-units', [AdministrativeUnitController::class, 'index'])->name('administrative-units.index');
        Route::get('administrative-units/{administrativeUnit}', [AdministrativeUnitController::class, 'show'])->name('administrative-units.show');
    });

    Route::middleware('permission:SYS.create')->group(function (): void {
        Route::post('lookups', [LookupController::class, 'storeCategory'])->name('lookups.store');
        Route::post('lookups/{lookupCategory:code}/values', [LookupController::class, 'storeValue'])->name('lookups.values.store');
        Route::post('administrative-units', [AdministrativeUnitController::class, 'store'])->name('administrative-units.store');
    });

    Route::middleware('permission:SYS.update')->group(function (): void {
        Route::put('lookups/{lookupCategory:code}', [LookupController::class, 'updateCategory'])->name('lookups.update');
        Route::put('lookup-values/{lookupValue}', [LookupController::class, 'updateValue'])->name('lookups.values.update');
        Route::put('administrative-units/{administrativeUnit}', [AdministrativeUnitController::class, 'update'])->name('administrative-units.update');
    });

    // Tham số hệ thống và bộ quy chế đào tạo (SYS, FR-SYS-001, FR-SYS-003): xem = SYS.view; sửa, soạn, ban hành = SYS.update
    Route::middleware('permission:SYS.view')->group(function (): void {
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::get('policy-sets', [PolicySetController::class, 'index'])->name('policy-sets.index');
        Route::get('policy-sets/definitions', [PolicySetController::class, 'definitions'])->name('policy-sets.definitions');
        Route::get('policy-sets/resolve', [PolicySetController::class, 'resolve'])->name('policy-sets.resolve');
        Route::get('policy-sets/{policySet}', [PolicySetController::class, 'show'])->whereNumber('policySet')->name('policy-sets.show');
    });

    Route::middleware('permission:SYS.update')->group(function (): void {
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('settings/logo', [SettingController::class, 'uploadLogo'])->name('settings.logo');
        Route::post('policy-sets', [PolicySetController::class, 'store'])->name('policy-sets.store');
        Route::put('policy-sets/{policySet}', [PolicySetController::class, 'update'])->whereNumber('policySet')->name('policy-sets.update');
        Route::put('policy-sets/{policySet}/items', [PolicySetController::class, 'updateItems'])->whereNumber('policySet')->name('policy-sets.items');
        Route::post('policy-sets/{policySet}/publish', [PolicySetController::class, 'publish'])->whereNumber('policySet')->name('policy-sets.publish');
        Route::delete('policy-sets/{policySet}', [PolicySetController::class, 'destroy'])->whereNumber('policySet')->name('policy-sets.destroy');
    });

    Route::middleware('permission:SYS.delete')->group(function (): void {
        Route::delete('lookups/{lookupCategory:code}', [LookupController::class, 'destroyCategory'])->name('lookups.destroy');
        Route::delete('lookup-values/{lookupValue}', [LookupController::class, 'destroyValue'])->name('lookups.values.destroy');
        Route::delete('administrative-units/{administrativeUnit}', [AdministrativeUnitController::class, 'destroy'])->name('administrative-units.destroy');
    });
});
