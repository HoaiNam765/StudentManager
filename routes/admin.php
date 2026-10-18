<?php

use App\Http\Controllers\DashboardController;
use App\Modules\AcademicYear\Http\Controllers\AcademicCalendarController;
use App\Modules\AcademicYear\Http\Controllers\AcademicYearController;
use App\Modules\Auth\Http\Controllers\LoginHistoryController;
use App\Modules\Auth\Http\Controllers\RoleController;
use App\Modules\Auth\Http\Controllers\RolePermissionController;
use App\Modules\Auth\Http\Controllers\UserController;
use App\Modules\Auth\Http\Controllers\UserRoleController;
use App\Modules\Curriculum\Http\Controllers\CurriculumController;
use App\Modules\Curriculum\Http\Controllers\CurriculumVersionController;
use App\Modules\Faculty\Http\Controllers\LeadershipController;
use App\Modules\Faculty\Http\Controllers\UnitController;
use App\Modules\Room\Http\Controllers\FacilityController;
use App\Modules\Room\Http\Controllers\RoomController;
use App\Modules\SchoolClass\Http\Controllers\AdminClassController;
use App\Modules\Student\Http\Controllers\AdmissionController;
use App\Modules\Student\Http\Controllers\StudentController;
use App\Modules\Student\Http\Controllers\StudentCurriculumController;
use App\Modules\Subject\Http\Controllers\GradeStructureController;
use App\Modules\Subject\Http\Controllers\SubjectController;
use App\Modules\Subject\Http\Controllers\SubjectRelationController;
use App\Modules\Subject\Http\Controllers\SyllabusController;
use App\Modules\System\Http\Controllers\AdministrativeUnitController;
use App\Modules\System\Http\Controllers\ImportController;
use App\Modules\System\Http\Controllers\LookupController;
use App\Modules\System\Http\Controllers\PolicySetController;
use App\Modules\System\Http\Controllers\SettingController;
use App\Modules\Teacher\Http\Controllers\LecturerController;
use App\Modules\Teacher\Http\Controllers\LecturerScheduleController;
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
        // Lịch sử đăng nhập toàn trường (FR-AUTH-016)
        Route::get('login-histories', [LoginHistoryController::class, 'index'])->name('login-histories.index');
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

    // Chương trình đào tạo (CUR, FR-CUR-001..003): xem = CUR.view, thêm = CUR.create, sửa nháp (khối, nhóm tự chọn, học phần) = CUR.update,
    // xóa nháp = CUR.delete, ban hành = CUR.approve toàn trường (ACAD). Phạm vi FACULTY (DEAN) kiểm tra tiếp trong CurriculumController.
    Route::middleware('permission:CUR.view')->group(function (): void {
        Route::get('curricula', [CurriculumController::class, 'index'])->name('curricula.index');
        Route::get('curricula/{curriculum}', [CurriculumController::class, 'show'])->name('curricula.show');
        Route::get('curricula/{curriculum}/check', [CurriculumController::class, 'check'])->name('curricula.check');
        Route::get('curricula/{curriculum}/versions', [CurriculumVersionController::class, 'index'])->name('curricula.versions.index');
        Route::get('curricula/{curriculum}/graduation-requirements', [CurriculumVersionController::class, 'showRequirements'])->name('curricula.graduation-requirements.show');
    });

    Route::post('curricula', [CurriculumController::class, 'store'])->middleware('permission:CUR.create')->name('curricula.store');
    Route::post('curricula/{curriculum}/versions', [CurriculumVersionController::class, 'store'])->middleware('permission:CUR.create')->name('curricula.versions.store');
    Route::delete('curricula/{curriculum}', [CurriculumController::class, 'destroy'])->middleware('permission:CUR.delete')->name('curricula.destroy');
    Route::post('curricula/{curriculum}/publish', [CurriculumController::class, 'publish'])->middleware('permission:CUR.approve,ALL')->name('curricula.publish');

    Route::middleware('permission:CUR.update')->group(function (): void {
        Route::put('curricula/{curriculum}', [CurriculumController::class, 'update'])->name('curricula.update');
        Route::put('curricula/{curriculum}/graduation-requirements', [CurriculumVersionController::class, 'updateRequirements'])->name('curricula.graduation-requirements.update');
        Route::post('curricula/{curriculum}/blocks', [CurriculumController::class, 'storeBlock'])->name('curricula.blocks.store');
        Route::put('curriculum-blocks/{curriculumBlock}', [CurriculumController::class, 'updateBlock'])->name('curriculum-blocks.update');
        Route::delete('curriculum-blocks/{curriculumBlock}', [CurriculumController::class, 'destroyBlock'])->name('curriculum-blocks.destroy');
        Route::post('curricula/{curriculum}/elective-groups', [CurriculumController::class, 'storeGroup'])->name('curricula.elective-groups.store');
        Route::put('curriculum-elective-groups/{electiveGroup}', [CurriculumController::class, 'updateGroup'])->name('curriculum-elective-groups.update');
        Route::delete('curriculum-elective-groups/{electiveGroup}', [CurriculumController::class, 'destroyGroup'])->name('curriculum-elective-groups.destroy');
        Route::post('curricula/{curriculum}/subjects', [CurriculumController::class, 'storeSubject'])->name('curricula.subjects.store');
        Route::put('curriculum-subjects/{curriculumSubject}', [CurriculumController::class, 'updateSubject'])->name('curriculum-subjects.update');
        Route::delete('curriculum-subjects/{curriculumSubject}', [CurriculumController::class, 'destroySubject'])->name('curriculum-subjects.destroy');
    });

    // Hồ sơ sinh viên (STU, FR-STU-001, 004, 005, 013): xem = STU.view (DEAN theo khoa; LEC, ADV khi có lớp), thêm = STU.create,
    // sửa / người thân / xem đầy đủ trường nhạy cảm = STU.update, xóa hồ sơ nhập sai = STU.delete. Phạm vi kiểm tra tiếp trong StudentController.
    Route::middleware('permission:STU.view')->group(function (): void {
        Route::get('students', [StudentController::class, 'index'])->name('students.index');
        Route::get('students/{student}', [StudentController::class, 'show'])->whereNumber('student')->name('students.show');
        Route::get('students/{student}/change-logs', [StudentController::class, 'changeLogs'])->whereNumber('student')->name('students.change-logs');
    });

    Route::post('students', [StudentController::class, 'store'])->middleware('permission:STU.create')->name('students.store');
    Route::delete('students/{student}', [StudentController::class, 'destroy'])->whereNumber('student')->middleware('permission:STU.delete')->name('students.destroy');

    Route::middleware('permission:STU.update')->group(function (): void {
        Route::put('students/{student}', [StudentController::class, 'update'])->whereNumber('student')->name('students.update');
        Route::get('students/{student}/sensitive', [StudentController::class, 'sensitive'])->whereNumber('student')->name('students.sensitive');
        Route::post('students/{student}/contacts', [StudentController::class, 'storeContact'])->whereNumber('student')->name('students.contacts.store');
        Route::put('student-contacts/{studentContact}', [StudentController::class, 'updateContact'])->name('student-contacts.update');
        Route::delete('student-contacts/{studentContact}', [StudentController::class, 'destroyContact'])->name('student-contacts.destroy');
    });

    // Gán CTĐT cho sinh viên (CUR, FR-CUR-006): xem lịch sử = STU.view; gán theo ngành – khóa hoặc đổi CTĐT = STU.update (kiểm tra phạm vi trên hồ sơ)
    Route::get('students/{student}/curricula', [StudentCurriculumController::class, 'index'])->whereNumber('student')->middleware('permission:STU.view')->name('students.curricula.index');
    Route::post('students/{student}/curricula', [StudentCurriculumController::class, 'store'])->whereNumber('student')->middleware('permission:STU.update')->name('students.curricula.store');

    // Xác nhận / hủy nhập học (STU, FR-STU-002): STU.approve (ACAD, CTSV). Cấp MSSV, email, tài khoản, CTĐT, lớp hành chính trong một giao dịch.
    Route::middleware('permission:STU.approve')->group(function (): void {
        Route::post('students/{student}/admit', [AdmissionController::class, 'admit'])->whereNumber('student')->name('students.admit');
        Route::post('students/{student}/cancel-admission', [AdmissionController::class, 'cancel'])->whereNumber('student')->name('students.cancel-admission');
    });

    // Hồ sơ giảng viên (TCH, FR-TCH-001, 003): xem = TCH.view (LEC, ADV chỉ hồ sơ của mình), thêm = TCH.create,
    // sửa / tình trạng / kiêm nhiệm / cấp tài khoản / xác nhận đề nghị = TCH.update. Phạm vi kiểm tra tiếp trong LecturerController.
    Route::middleware('permission:TCH.view')->group(function (): void {
        Route::get('lecturers', [LecturerController::class, 'index'])->name('lecturers.index');
        Route::get('lecturers/export', [LecturerController::class, 'export'])->middleware('permission:TCH.export')->name('lecturers.export');
        Route::get('lecturers/{lecturer}', [LecturerController::class, 'show'])->name('lecturers.show');
        Route::get('lecturers/{lecturer}/units', [LecturerController::class, 'units'])->name('lecturers.units.index');
        Route::get('lecturer-change-requests', [LecturerController::class, 'changeRequests'])->name('lecturer-change-requests.index');
        // Lịch bận, nguyện vọng, nghỉ phép (FR-TCH-008, 012)
        Route::get('lecturers/{lecturer}/availability', [LecturerScheduleController::class, 'availabilityOfLecturer'])->name('lecturers.availability');
        Route::get('teaching-preferences', [LecturerScheduleController::class, 'preferencesForTerm'])->name('teaching-preferences.index');
        Route::get('lecturer-leaves', [LecturerScheduleController::class, 'leaves'])->name('lecturer-leaves.index');
    });

    Route::post('lecturers', [LecturerController::class, 'store'])->middleware('permission:TCH.create')->name('lecturers.store');

    Route::middleware('permission:TCH.update')->group(function (): void {
        Route::put('lecturers/{lecturer}', [LecturerController::class, 'update'])->name('lecturers.update');
        Route::get('lecturers/{lecturer}/handover', [LecturerController::class, 'handover'])->name('lecturers.handover');
        Route::post('lecturers/{lecturer}/status', [LecturerController::class, 'changeStatus'])->name('lecturers.status');
        Route::post('lecturers/{lecturer}/account', [LecturerController::class, 'createAccount'])->name('lecturers.account');
        Route::post('lecturers/{lecturer}/units', [LecturerController::class, 'storeUnit'])->name('lecturers.units.store');
        Route::post('lecturer-units/{lecturerUnit}/end', [LecturerController::class, 'endUnit'])->name('lecturer-units.end');
        Route::delete('lecturer-units/{lecturerUnit}', [LecturerController::class, 'destroyUnit'])->name('lecturer-units.destroy');
        Route::post('lecturer-change-requests/{lecturerChangeRequest}/approve', [LecturerController::class, 'approve'])->name('lecturer-change-requests.approve');
        Route::post('lecturer-change-requests/{lecturerChangeRequest}/reject', [LecturerController::class, 'reject'])->name('lecturer-change-requests.reject');
        Route::post('lecturer-leaves/{lecturerLeave}/approve', [LecturerScheduleController::class, 'approveLeave'])->name('lecturer-leaves.approve');
        Route::post('lecturer-leaves/{lecturerLeave}/reject', [LecturerScheduleController::class, 'rejectLeave'])->name('lecturer-leaves.reject');
    });

    // Đề cương học phần (SUB, FR-SUB-006, BR-SUB-08): xem = SUB.view; soạn, sửa, đính tệp, gửi duyệt = SUB.update (LEC học phần phụ trách);
    // duyệt / trả lại = SUB.approve (DEAN, ACAD). Phạm vi kiểm tra trên học phần trong SyllabusController.
    Route::middleware('permission:SUB.view')->group(function (): void {
        Route::get('subjects/{subject}/syllabi', [SyllabusController::class, 'index'])->whereNumber('subject')->name('subjects.syllabi.index');
        Route::get('syllabi/{syllabus}', [SyllabusController::class, 'show'])->name('syllabi.show');
        Route::get('syllabi/{syllabus}/file', [SyllabusController::class, 'downloadFile'])->name('syllabi.file.download');
    });

    Route::middleware('permission:SUB.update')->group(function (): void {
        Route::post('subjects/{subject}/syllabi', [SyllabusController::class, 'store'])->whereNumber('subject')->name('subjects.syllabi.store');
        Route::put('syllabi/{syllabus}', [SyllabusController::class, 'update'])->name('syllabi.update');
        Route::post('syllabi/{syllabus}/file', [SyllabusController::class, 'uploadFile'])->name('syllabi.file.upload');
        Route::post('syllabi/{syllabus}/submit', [SyllabusController::class, 'submit'])->name('syllabi.submit');
        Route::delete('syllabi/{syllabus}', [SyllabusController::class, 'destroy'])->name('syllabi.destroy');
    });

    Route::middleware('permission:SUB.approve')->group(function (): void {
        Route::post('syllabi/{syllabus}/approve', [SyllabusController::class, 'approve'])->name('syllabi.approve');
        Route::post('syllabi/{syllabus}/reject', [SyllabusController::class, 'reject'])->name('syllabi.reject');
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

    // Khóa tuyển sinh và lớp hành chính (CLS, FR-CLS-001, 002): xem = CLS.view (DEAN theo khoa); khóa: thêm, sửa, xóa = CLS.* toàn trường (ACAD);
    // lớp: thêm = CLS.create, sửa / đóng lớp = CLS.update, xóa = CLS.delete. Phạm vi kiểm tra tiếp trong AdminClassController.
    Route::middleware('permission:CLS.view')->group(function (): void {
        Route::get('cohorts', [AdminClassController::class, 'cohorts'])->name('cohorts.index');
        Route::get('admin-classes', [AdminClassController::class, 'index'])->name('admin-classes.index');
        Route::get('admin-classes/{adminClass}', [AdminClassController::class, 'show'])->whereNumber('adminClass')->name('admin-classes.show');
    });

    Route::post('cohorts', [AdminClassController::class, 'storeCohort'])->middleware('permission:CLS.create,ALL')->name('cohorts.store');
    Route::put('cohorts/{cohort}', [AdminClassController::class, 'updateCohort'])->middleware('permission:CLS.update,ALL')->name('cohorts.update');
    Route::delete('cohorts/{cohort}', [AdminClassController::class, 'destroyCohort'])->middleware('permission:CLS.delete,ALL')->name('cohorts.destroy');
    Route::post('admin-classes', [AdminClassController::class, 'store'])->middleware('permission:CLS.create')->name('admin-classes.store');
    Route::put('admin-classes/{adminClass}', [AdminClassController::class, 'update'])->whereNumber('adminClass')->middleware('permission:CLS.update')->name('admin-classes.update');
    Route::delete('admin-classes/{adminClass}', [AdminClassController::class, 'destroy'])->whereNumber('adminClass')->middleware('permission:CLS.delete')->name('admin-classes.destroy');

    // Học phần (SUB, FR-SUB-001..003): xem = SUB.view (DEAN, LEC theo phạm vi), thêm = SUB.create, sửa / ngừng = SUB.update,
    // xóa = SUB.delete, xuất = SUB.export. Phạm vi FACULTY (DEAN) kiểm tra tiếp trên từng bản ghi trong SubjectController.
    Route::middleware('permission:SUB.view')->group(function (): void {
        Route::get('subjects', [SubjectController::class, 'index'])->name('subjects.index');
        Route::get('subjects/{subject}', [SubjectController::class, 'show'])->whereNumber('subject')->name('subjects.show');
        Route::get('subjects/{subject}/relations', [SubjectRelationController::class, 'index'])->name('subjects.relations.index');
        Route::get('subjects/{subject}/prerequisites', [SubjectRelationController::class, 'prerequisites'])->name('subjects.prerequisites');
    });

    Route::get('subjects/export', [SubjectController::class, 'export'])->middleware('permission:SUB.export')->name('subjects.export');
    Route::post('subjects', [SubjectController::class, 'store'])->middleware('permission:SUB.create')->name('subjects.store');
    Route::get('subjects/{subject}/grade-components', [GradeStructureController::class, 'show'])->whereNumber('subject')->middleware('permission:SUB.view')->name('subjects.grade-components.show');
    Route::put('subjects/{subject}/grade-components', [GradeStructureController::class, 'update'])->whereNumber('subject')->middleware('permission:SUB.update')->name('subjects.grade-components.update');
    Route::put('subjects/{subject}', [SubjectController::class, 'update'])->whereNumber('subject')->middleware('permission:SUB.update')->name('subjects.update');
    Route::delete('subjects/{subject}', [SubjectController::class, 'destroy'])->whereNumber('subject')->middleware('permission:SUB.delete')->name('subjects.destroy');

    // Quan hệ học phần (FR-SUB-004): thêm, kết thúc, xóa = SUB.update trên học phần của quan hệ
    Route::middleware('permission:SUB.update')->group(function (): void {
        Route::post('subjects/{subject}/relations', [SubjectRelationController::class, 'store'])->name('subjects.relations.store');
        Route::post('subject-relations/{subjectRelation}/end', [SubjectRelationController::class, 'end'])->name('subject-relations.end');
        Route::delete('subject-relations/{subjectRelation}', [SubjectRelationController::class, 'destroy'])->name('subject-relations.destroy');
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
