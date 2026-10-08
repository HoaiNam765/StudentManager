<?php

use App\Http\Controllers\DashboardController;
use App\Modules\AcademicYear\Http\Controllers\AcademicCalendarController;
use App\Modules\AcademicYear\Http\Controllers\AcademicYearController;
use App\Modules\Auth\Http\Controllers\RoleController;
use App\Modules\Auth\Http\Controllers\RolePermissionController;
use App\Modules\Auth\Http\Controllers\UserController;
use App\Modules\Auth\Http\Controllers\UserRoleController;
use App\Modules\Faculty\Http\Controllers\LeadershipController;
use App\Modules\Faculty\Http\Controllers\UnitController;
use App\Modules\Room\Http\Controllers\FacilityController;
use App\Modules\Room\Http\Controllers\RoomController;
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

    // Quản lý người dùng (AUTH, FR-AUTH-008): mặc định chỉ ADMIN (quyền AUTH toàn trường).
    // Tạo hàng loạt (FR-AUTH-009) qua trung tâm import, Importer "users".
    Route::middleware('permission:AUTH.view,ALL')->group(function (): void {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    });
    Route::post('users', [UserController::class, 'store'])->middleware('permission:AUTH.create,ALL')->name('users.store');

    Route::middleware('permission:AUTH.update,ALL')->group(function (): void {
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::post('users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
        Route::post('users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
        Route::post('users/{user}/temporary-password', [UserController::class, 'issueTemporaryPassword'])->name('users.temporary-password');
    });

    Route::middleware('permission:AUTH.approve,ALL')->group(function (): void {
        Route::post('users/{user}/lock', [UserController::class, 'lock'])->name('users.lock');
        Route::post('users/{user}/unlock', [UserController::class, 'unlock'])->name('users.unlock');
    });

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

    // Lịch học vụ và ngày nghỉ (ACY, FR-ACY-004, 005, 007): xem = ACY.view (mọi vai trò), sửa = ACY.update,
    // duyệt sửa mốc của học kỳ đã bắt đầu = ACY.approve toàn trường (BR-ACY-05), ngày nghỉ thêm/xóa = ACY.create/delete
    Route::middleware('permission:ACY.view')->group(function (): void {
        Route::get('terms/{term}/milestones', [AcademicCalendarController::class, 'milestones'])->name('terms.milestones.index');
        Route::get('milestone-change-requests', [AcademicCalendarController::class, 'changeRequests'])->name('milestone-change-requests.index');
        Route::get('holidays', [AcademicCalendarController::class, 'holidays'])->name('holidays.index');
        Route::get('holidays/days-off', [AcademicCalendarController::class, 'daysOff'])->name('holidays.days-off');
    });

    Route::middleware('permission:ACY.update')->group(function (): void {
        Route::put('terms/{term}/milestones', [AcademicCalendarController::class, 'saveMilestones'])->name('terms.milestones.update');
        Route::post('terms/{term}/milestones/copy', [AcademicCalendarController::class, 'copyMilestones'])->name('terms.milestones.copy');
        Route::put('holidays/{holiday}', [AcademicCalendarController::class, 'updateHoliday'])->name('holidays.update');
    });

    Route::middleware('permission:ACY.approve,ALL')->group(function (): void {
        Route::post('milestone-change-requests/{milestoneChangeRequest}/approve', [AcademicCalendarController::class, 'approve'])->name('milestone-change-requests.approve');
        Route::post('milestone-change-requests/{milestoneChangeRequest}/reject', [AcademicCalendarController::class, 'reject'])->name('milestone-change-requests.reject');
    });

    Route::post('holidays', [AcademicCalendarController::class, 'storeHoliday'])->middleware('permission:ACY.create')->name('holidays.store');
    Route::delete('holidays/{holiday}', [AcademicCalendarController::class, 'destroyHoliday'])->middleware('permission:ACY.delete')->name('holidays.destroy');

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

    // Cơ sở, tòa nhà, phòng học, lịch bảo trì (ROM, FR-ROM-001, 002): xem = ROM.view (mọi vai trò theo ma trận),
    // thêm = ROM.create, sửa / đổi tình trạng / lịch bảo trì = ROM.update, xóa = ROM.delete
    Route::middleware('permission:ROM.view')->group(function (): void {
        Route::get('campuses', [FacilityController::class, 'campuses'])->name('campuses.index');
        Route::get('buildings', [FacilityController::class, 'buildings'])->name('buildings.index');
        Route::get('room-types', [FacilityController::class, 'roomTypes'])->name('room-types.index');
        Route::get('rooms', [RoomController::class, 'index'])->name('rooms.index');
        Route::get('rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
        Route::get('rooms/{room}/availability', [RoomController::class, 'availability'])->name('rooms.availability');
        Route::get('rooms/{room}/maintenances', [RoomController::class, 'maintenancesOf'])->name('rooms.maintenances.index');
    });

    Route::middleware('permission:ROM.create')->group(function (): void {
        Route::post('campuses', [FacilityController::class, 'storeCampus'])->name('campuses.store');
        Route::post('buildings', [FacilityController::class, 'storeBuilding'])->name('buildings.store');
        Route::post('room-types', [FacilityController::class, 'storeRoomType'])->name('room-types.store');
        Route::post('rooms', [RoomController::class, 'store'])->name('rooms.store');
    });

    Route::middleware('permission:ROM.update')->group(function (): void {
        Route::put('campuses/{campus}', [FacilityController::class, 'updateCampus'])->name('campuses.update');
        Route::put('buildings/{building}', [FacilityController::class, 'updateBuilding'])->name('buildings.update');
        Route::put('room-types/{roomType}', [FacilityController::class, 'updateRoomType'])->name('room-types.update');
        Route::put('rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
        Route::post('rooms/{room}/maintenances', [RoomController::class, 'storeMaintenance'])->name('rooms.maintenances.store');
        Route::put('room-maintenances/{roomMaintenance}', [RoomController::class, 'updateMaintenance'])->name('room-maintenances.update');
        Route::delete('room-maintenances/{roomMaintenance}', [RoomController::class, 'destroyMaintenance'])->name('room-maintenances.destroy');
    });

    Route::middleware('permission:ROM.delete')->group(function (): void {
        Route::delete('campuses/{campus}', [FacilityController::class, 'destroyCampus'])->name('campuses.destroy');
        Route::delete('buildings/{building}', [FacilityController::class, 'destroyBuilding'])->name('buildings.destroy');
        Route::delete('rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');
    });

    // Lãnh đạo đơn vị theo nhiệm kỳ (FAC, FR-FAC-006): xem = FAC.view; giao, kết thúc, hủy = FAC.update toàn trường (ACAD, ADMIN)
    Route::get('leadership-terms', [LeadershipController::class, 'index'])->middleware('permission:FAC.view')->name('leadership-terms.index');

    Route::middleware('permission:FAC.update,ALL')->group(function (): void {
        Route::post('leadership-terms', [LeadershipController::class, 'store'])->name('leadership-terms.store');
        Route::post('leadership-terms/{leadershipTerm}/end', [LeadershipController::class, 'end'])->name('leadership-terms.end');
        Route::delete('leadership-terms/{leadershipTerm}', [LeadershipController::class, 'destroy'])->name('leadership-terms.destroy');
    });

    // Khoa, bộ môn, ngành, chuyên ngành, hệ đào tạo (FAC, FR-FAC-001..005, 010): {unitType} là faculties | departments | majors | specializations.
    // Middleware chặn theo hành động; phạm vi FACULTY (DEAN) kiểm tra tiếp trên từng bản ghi trong UnitController.
    $unitTypes = array_keys(UnitController::TYPES);

    Route::middleware('permission:FAC.view')->group(function () use ($unitTypes): void {
        Route::get('training-types', [UnitController::class, 'trainingTypes'])->name('training-types.index');
        Route::get('{unitType}', [UnitController::class, 'index'])->whereIn('unitType', $unitTypes)->name('units.index');
        Route::get('{unitType}/{id}', [UnitController::class, 'show'])->whereIn('unitType', $unitTypes)->whereNumber('id')->name('units.show');
    });

    Route::get('{unitType}/export', [UnitController::class, 'export'])
        ->whereIn('unitType', $unitTypes)->middleware('permission:FAC.export')->name('units.export');

    Route::middleware('permission:FAC.create')->group(function () use ($unitTypes): void {
        Route::post('training-types', [UnitController::class, 'storeTrainingType'])->name('training-types.store');
        Route::post('{unitType}', [UnitController::class, 'store'])->whereIn('unitType', $unitTypes)->name('units.store');
    });

    Route::put('training-types/{trainingType}', [UnitController::class, 'updateTrainingType'])
        ->middleware('permission:FAC.update,ALL')->name('training-types.update');
    Route::put('{unitType}/{id}', [UnitController::class, 'update'])
        ->whereIn('unitType', $unitTypes)->whereNumber('id')->middleware('permission:FAC.update')->name('units.update');
    Route::delete('{unitType}/{id}', [UnitController::class, 'destroy'])
        ->whereIn('unitType', $unitTypes)->whereNumber('id')->middleware('permission:FAC.delete')->name('units.destroy');
});
