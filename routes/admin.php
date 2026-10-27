<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Pages\Admin\AcademicYearPageController;
use App\Http\Controllers\Pages\Admin\CourseSectionDetailPageController;
use App\Http\Controllers\Pages\Admin\CourseSectionPageController;
use App\Http\Controllers\Pages\Admin\EnrollmentAdminPageController;
use App\Http\Controllers\Pages\Admin\GradeAdminActionController;
use App\Http\Controllers\Pages\Admin\GradeAdminDetailController;
use App\Http\Controllers\Pages\Admin\GradeAdminPageController;
use App\Http\Controllers\Pages\Admin\GradeChangePageController;
use App\Http\Controllers\Pages\Admin\MakeupPageController;
use App\Http\Controllers\Pages\Admin\StudentPageController;
use App\Http\Controllers\Pages\Admin\SubjectPageController;
use App\Http\Controllers\Pages\Admin\SystemOperationPageController;
use App\Http\Controllers\Pages\Admin\TimetablePageController;
use App\Http\Controllers\Pages\Admin\TrainingFrameworkPageController;
use App\Http\Controllers\Pages\AnnouncementPageController;
use App\Http\Controllers\Pages\AttendancePageController;
use App\Modules\AcademicYear\Http\Controllers\AcademicCalendarController;
use App\Modules\AcademicYear\Http\Controllers\AcademicYearController;
use App\Modules\Attendance\Http\Controllers\AttendanceAdminController;
use App\Modules\Attendance\Http\Controllers\AttendanceController;
use App\Modules\Auth\Http\Controllers\LoginHistoryController;
use App\Modules\Auth\Http\Controllers\RoleController;
use App\Modules\Auth\Http\Controllers\RolePermissionController;
use App\Modules\Auth\Http\Controllers\UserController;
use App\Modules\Auth\Http\Controllers\UserRoleController;
use App\Modules\Curriculum\Http\Controllers\CurriculumController;
use App\Modules\Curriculum\Http\Controllers\CurriculumVersionController;
use App\Modules\Enrollment\Http\Controllers\EnrollmentController;
use App\Modules\Enrollment\Http\Controllers\EnrollmentResultController;
use App\Modules\Enrollment\Http\Controllers\RegistrationOfferingController;
use App\Modules\Enrollment\Http\Controllers\RegistrationOptionsController;
use App\Modules\Enrollment\Http\Controllers\RegistrationPeriodController;
use App\Modules\Enrollment\Http\Controllers\SectionEnrollmentController;
use App\Modules\Enrollment\Http\Controllers\StaffEnrollmentController;
use App\Modules\Faculty\Http\Controllers\LeadershipController;
use App\Modules\Faculty\Http\Controllers\UnitController;
use App\Modules\Grade\Http\Controllers\GradeChangeController;
use App\Modules\Grade\Http\Controllers\GradeWorkflowController;
use App\Modules\Grade\Http\Controllers\StudentGpaController;
use App\Modules\Grade\Http\Controllers\TranscriptController;
use App\Modules\Notification\Http\Controllers\AnnouncementController;
use App\Modules\Notification\Http\Controllers\EventSubscriptionController;
use App\Modules\Report\Http\Controllers\DashboardDataController;
use App\Modules\Room\Http\Controllers\FacilityController;
use App\Modules\Room\Http\Controllers\RoomController;
use App\Modules\SchoolClass\Http\Controllers\AdminClassController;
use App\Modules\SchoolClass\Http\Controllers\AdminClassRosterController;
use App\Modules\SchoolClass\Http\Controllers\CourseSectionController;
use App\Modules\SchoolClass\Http\Controllers\TrainingFrameworkController;
use App\Modules\Student\Http\Controllers\AdmissionController;
use App\Modules\Student\Http\Controllers\StudentChangeRequestController;
use App\Modules\Student\Http\Controllers\StudentController;
use App\Modules\Student\Http\Controllers\StudentCurriculumController;
use App\Modules\Student\Http\Controllers\StudentStatusController;
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
use App\Modules\Timetable\Http\Controllers\ClassSessionController;
use App\Modules\Timetable\Http\Controllers\PeriodController;
use App\Modules\Timetable\Http\Controllers\ScheduleRuleController;
use App\Modules\Timetable\Http\Controllers\TimetableController;
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
    // Dữ liệu dashboard quản trị theo vai trò và phạm vi (RPT, FR-RPT-001, 002, 008)
    Route::get('/dashboard/data', [DashboardDataController::class, 'admin'])->middleware('permission:RPT.view')->name('dashboard.data');
    Route::get('van-hanh-he-thong', [SystemOperationPageController::class, 'index'])->middleware('permission:SYS.view')->name('pages.system-operations');
    Route::get('thoi-khoa-bieu/day-bu', [MakeupPageController::class, 'index'])->middleware('permission:TTB.view')->name('pages.makeup');
    Route::get('dang-ky-hoc-phan', [EnrollmentAdminPageController::class, 'index'])->middleware('permission:ENR.view')->name('pages.enrollment');
    Route::get('thoi-khoa-bieu', [TimetablePageController::class, 'index'])->middleware('permission:TTB.view')->name('pages.timetable');
    Route::get('hoc-phan', [SubjectPageController::class, 'index'])->middleware('permission:SUB.view')->name('pages.subjects');
    Route::get('lop-hoc-phan', [CourseSectionPageController::class, 'index'])->middleware('permission:CLS.view')->name('pages.course-sections');
    Route::get('lop-hoc-phan/{courseSection}', [CourseSectionDetailPageController::class, 'show'])->whereNumber('courseSection')->middleware('permission:CLS.view')->name('pages.course-sections.show');
    // Quản lý sinh viên (FE #16) và khóa – lớp hành chính (FE #15): trang HTML, dữ liệu do JavaScript gọi API STU / CLS / SYS
    Route::get('sinh-vien', [StudentPageController::class, 'index'])->middleware('permission:STU.view')->name('pages.students');
    Route::get('sinh-vien/them', [StudentPageController::class, 'create'])->middleware('permission:STU.create')->name('pages.students.create');
    Route::get('sinh-vien/nhap-file', [StudentPageController::class, 'import'])->middleware('permission:SYS.view')->name('pages.students.import');
    Route::get('sinh-vien/{student}', [StudentPageController::class, 'show'])->whereNumber('student')->middleware('permission:STU.view')->name('pages.students.show');
    Route::get('sinh-vien/{student}/sua', [StudentPageController::class, 'edit'])->whereNumber('student')->middleware('permission:STU.update')->name('pages.students.edit');
    Route::get('lop-hanh-chinh', [StudentPageController::class, 'classes'])->middleware('permission:CLS.view')->name('pages.admin-classes');
    Route::get('chuong-trinh-khung', [TrainingFrameworkPageController::class, 'index'])->middleware('permission:CLS.view')->name('pages.training-frameworks');

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
        Route::get('students/export', [StudentController::class, 'export'])->middleware('permission:STU.export')->name('students.export');
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

    // Trạng thái sinh viên theo quyết định (FR-STU-009, 010; BR-STU-05, 10): xem = STU.view, chuyển = STU.update trên hồ sơ,
    // khôi phục từ trạng thái cuối (special) = STU.approve toàn trường — kiểm tra trong StudentStatusController
    Route::get('students/{student}/status', [StudentStatusController::class, 'index'])->whereNumber('student')->middleware('permission:STU.view')->name('students.status.index');
    Route::post('students/{student}/status', [StudentStatusController::class, 'store'])->whereNumber('student')->middleware('permission:STU.update')->name('students.status.store');
    Route::get('student-status-histories/{history}/attachment', [StudentStatusController::class, 'attachment'])->middleware('permission:STU.view')->name('student-status-histories.attachment');

    // Yêu cầu sửa trường nhạy cảm của sinh viên (BR-STU-06): xem = STU.view theo phạm vi, duyệt / từ chối = STU.approve trên hồ sơ
    Route::get('student-change-requests', [StudentChangeRequestController::class, 'index'])->middleware('permission:STU.view')->name('student-change-requests.index');
    Route::get('student-change-requests/{studentChangeRequest}/evidence', [StudentChangeRequestController::class, 'evidence'])->middleware('permission:STU.view')->name('student-change-requests.evidence');
    Route::post('student-change-requests/{studentChangeRequest}/approve', [StudentChangeRequestController::class, 'approve'])->middleware('permission:STU.approve')->name('student-change-requests.approve');
    Route::post('student-change-requests/{studentChangeRequest}/reject', [StudentChangeRequestController::class, 'reject'])->middleware('permission:STU.approve')->name('student-change-requests.reject');

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

    // Thành viên và cố vấn lớp hành chính (FR-CLS-003, 004; BR-CLS-07, 08)
    Route::get('admin-classes/missing-advisors', [AdminClassRosterController::class, 'missingAdvisors'])->middleware('permission:CLS.view')->name('admin-classes.missing-advisors');
    Route::post('admin-classes/auto-placement/preview', [AdminClassRosterController::class, 'previewAutomaticPlacement'])->middleware('permission:CLS.update,ALL')->name('admin-classes.auto-placement.preview');
    Route::post('admin-classes/auto-placement', [AdminClassRosterController::class, 'assignAutomatically'])->middleware('permission:CLS.update,ALL')->name('admin-classes.auto-placement');
    Route::get('admin-classes/{adminClass}/members/export', [AdminClassRosterController::class, 'exportMembers'])->whereNumber('adminClass')->middleware('permission:CLS.export')->name('admin-classes.members.export');
    Route::get('admin-classes/{adminClass}/members', [AdminClassRosterController::class, 'members'])->whereNumber('adminClass')->middleware('permission:CLS.view')->name('admin-classes.members.index');
    Route::post('admin-classes/{adminClass}/members/preview', [AdminClassRosterController::class, 'previewAssignment'])->whereNumber('adminClass')->middleware('permission:CLS.update,ALL')->name('admin-classes.members.preview');
    Route::post('admin-classes/{adminClass}/members', [AdminClassRosterController::class, 'assign'])->whereNumber('adminClass')->middleware('permission:CLS.update,ALL')->name('admin-classes.members.store');
    Route::post('admin-classes/{adminClass}/members/remove-preview', [AdminClassRosterController::class, 'previewRemoval'])->whereNumber('adminClass')->middleware('permission:CLS.update,ALL')->name('admin-classes.members.remove-preview');
    Route::post('admin-classes/{adminClass}/members/remove', [AdminClassRosterController::class, 'remove'])->whereNumber('adminClass')->middleware('permission:CLS.update,ALL')->name('admin-classes.members.remove');
    Route::get('admin-classes/{adminClass}/advisors', [AdminClassRosterController::class, 'advisors'])->whereNumber('adminClass')->middleware('permission:CLS.view')->name('admin-classes.advisors.index');
    Route::post('admin-classes/{adminClass}/advisors/preview', [AdminClassRosterController::class, 'previewAdvisor'])->whereNumber('adminClass')->middleware('permission:CLS.view')->name('admin-classes.advisors.preview');
    Route::post('admin-classes/{adminClass}/advisors', [AdminClassRosterController::class, 'assignAdvisor'])->whereNumber('adminClass')->middleware('permission:CLS.view')->name('admin-classes.advisors.store');
    Route::post('advisor-assignments/{advisorAssignment}/end', [AdminClassRosterController::class, 'endAdvisor'])->whereNumber('advisorAssignment')->middleware('permission:CLS.view')->name('advisor-assignments.end');

    // Lớp học phần (CLS, FR-CLS-009..012): xem = CLS.view (DEAN đơn vị mình, LEC lớp được phân công); mở, sửa, xóa, đổi trạng thái = CLS toàn trường (ACAD);
    // phân công giảng viên = ACAD hoặc DEAN trong đơn vị (kiểm tra trong CourseSectionController).
    Route::middleware('permission:CLS.view')->group(function (): void {
        Route::get('course-sections', [CourseSectionController::class, 'index'])->name('course-sections.index');
        Route::get('course-sections/{courseSection}', [CourseSectionController::class, 'show'])->whereNumber('courseSection')->name('course-sections.show');
        Route::post('course-sections/{courseSection}/instructors', [CourseSectionController::class, 'assign'])->whereNumber('courseSection')->name('course-sections.instructors.store');
        Route::delete('section-instructors/{sectionInstructor}', [CourseSectionController::class, 'unassign'])->name('section-instructors.destroy');
    });

    Route::post('course-sections', [CourseSectionController::class, 'store'])->middleware('permission:CLS.create,ALL')->name('course-sections.store');
    Route::put('course-sections/{courseSection}', [CourseSectionController::class, 'update'])->whereNumber('courseSection')->middleware('permission:CLS.update,ALL')->name('course-sections.update');
    Route::delete('course-sections/{courseSection}', [CourseSectionController::class, 'destroy'])->whereNumber('courseSection')->middleware('permission:CLS.delete,ALL')->name('course-sections.destroy');
    // Lớp sau đăng ký (BR-CLS-05, 09; FR-CLS-014, 018): lớp thiếu sĩ số, gộp lớp, xuất danh sách, mở khóa ngoại lệ
    Route::get('terms/{term}/under-min-sections', [SectionEnrollmentController::class, 'underMin'])->middleware('permission:CLS.view')->name('terms.under-min-sections');
    Route::post('course-sections/{courseSection}/merge', [SectionEnrollmentController::class, 'merge'])->whereNumber('courseSection')->middleware('permission:CLS.update,ALL')->name('course-sections.merge');
    Route::get('course-sections/{courseSection}/enrollments/export', [SectionEnrollmentController::class, 'export'])->whereNumber('courseSection')->middleware('permission:ENR.export')->name('course-sections.enrollments.export');
    Route::post('course-sections/{courseSection}/unlock', [CourseSectionController::class, 'unlock'])->whereNumber('courseSection')->middleware('permission:CLS.approve,ALL')->name('course-sections.unlock');
    Route::post('course-sections/{courseSection}/status/{status}', [CourseSectionController::class, 'transition'])
        ->whereNumber('courseSection')->middleware('permission:CLS.update,ALL')->name('course-sections.status');

    // Chương trình khung theo khoa – khóa – học kỳ: xem = CLS.view (DEAN khoa mình); lập, gợi ý từ CTĐT, mở lớp hàng loạt = CLS
    // toàn trường (ACAD); gửi thông báo cho sinh viên khoa – khóa = NOT.create (phạm vi kiểm tra trong AudienceResolver).
    Route::middleware('permission:CLS.view')->group(function (): void {
        Route::get('training-frameworks', [TrainingFrameworkController::class, 'index'])->name('training-frameworks.index');
        Route::get('training-frameworks/{trainingFramework}', [TrainingFrameworkController::class, 'show'])->whereNumber('trainingFramework')->name('training-frameworks.show');
    });
    Route::middleware('permission:CLS.create,ALL')->group(function (): void {
        Route::post('training-frameworks', [TrainingFrameworkController::class, 'store'])->name('training-frameworks.store');
        Route::put('training-frameworks/{trainingFramework}', [TrainingFrameworkController::class, 'update'])->whereNumber('trainingFramework')->name('training-frameworks.update');
        Route::delete('training-frameworks/{trainingFramework}', [TrainingFrameworkController::class, 'destroy'])->whereNumber('trainingFramework')->name('training-frameworks.destroy');
        Route::post('training-frameworks/{trainingFramework}/items', [TrainingFrameworkController::class, 'storeItem'])->whereNumber('trainingFramework')->name('training-frameworks.items.store');
        Route::put('training-framework-items/{trainingFrameworkItem}', [TrainingFrameworkController::class, 'updateItem'])->name('training-framework-items.update');
        Route::delete('training-framework-items/{trainingFrameworkItem}', [TrainingFrameworkController::class, 'destroyItem'])->name('training-framework-items.destroy');
        Route::post('training-frameworks/{trainingFramework}/suggest', [TrainingFrameworkController::class, 'suggest'])->whereNumber('trainingFramework')->name('training-frameworks.suggest');
    });

    // Đợt đăng ký học phần (ENR, FR-ENR-001, 002): xem = ENR.view, cấu hình = ACAD (ENR toàn trường);
    // xem danh sách lớp một sinh viên được chọn = ENR.view + quyền xem hồ sơ sinh viên đó
    Route::get('registration-periods', [RegistrationPeriodController::class, 'index'])->middleware('permission:ENR.view')->name('registration-periods.index');
    Route::get('registration-periods/{registrationPeriod}', [RegistrationPeriodController::class, 'show'])->middleware('permission:ENR.view')->name('registration-periods.show');
    Route::post('registration-periods', [RegistrationPeriodController::class, 'store'])->middleware('permission:ENR.create,ALL')->name('registration-periods.store');
    Route::put('registration-periods/{registrationPeriod}', [RegistrationPeriodController::class, 'update'])->middleware('permission:ENR.update,ALL')->name('registration-periods.update');
    Route::post('registration-periods/{registrationPeriod}/publish', [RegistrationPeriodController::class, 'publish'])->middleware('permission:ENR.update,ALL')->name('registration-periods.publish');
    Route::post('registration-periods/{registrationPeriod}/open-early', [RegistrationPeriodController::class, 'openEarly'])->middleware('permission:ENR.update,ALL')->name('registration-periods.open-early');
    Route::prefix('registration-periods/{registrationPeriod}')->name('registration-periods.')->group(function (): void {
        $controller = RegistrationOfferingController::class;
        Route::get('offerings', [$controller, 'index'])->middleware('permission:ENR.view,ALL')->name('offerings.index');
        Route::post('lecturer-options', [$controller, 'lecturers'])->middleware('permission:ENR.view,ALL')->name('lecturers');
        Route::post('offerings', [$controller, 'store'])->middleware(['permission:ENR.update,ALL', 'permission:CLS.create,ALL', 'permission:TTB.create,ALL'])->name('offerings.store');
        Route::put('offerings/{courseSection}', [$controller, 'update'])->middleware(['permission:ENR.update,ALL', 'permission:CLS.update,ALL', 'permission:TTB.update,ALL'])->name('offerings.update');
        Route::delete('offerings/{courseSection}', [$controller, 'destroy'])->middleware(['permission:ENR.update,ALL', 'permission:CLS.update,ALL'])->name('offerings.destroy');
        Route::post('announce', [$controller, 'announce'])->middleware(['permission:ENR.update,ALL', 'permission:NOT.create'])->name('announce');
    });
    // Mở đăng ký từ trang Chương trình khung: tạo đợt nháp + lớp + giảng viên (lịch xếp sau ở trang Đăng ký học phần)
    Route::get('training-frameworks/{trainingFramework}/lecturer-options', [RegistrationOfferingController::class, 'frameworkLecturers'])
        ->whereNumber('trainingFramework')->middleware('permission:ENR.view,ALL')->name('training-frameworks.lecturer-options');
    Route::post('training-frameworks/{trainingFramework}/registration-periods', [RegistrationOfferingController::class, 'storeFromFramework'])
        ->whereNumber('trainingFramework')->middleware(['permission:ENR.create,ALL', 'permission:ENR.update,ALL', 'permission:CLS.create,ALL'])
        ->name('training-frameworks.registration-periods.store');
    Route::delete('registration-periods/{registrationPeriod}', [RegistrationPeriodController::class, 'destroy'])->middleware('permission:ENR.delete,ALL')->name('registration-periods.destroy');
    Route::get('course-sections/{courseSection}/enrollments', [EnrollmentController::class, 'roster'])->whereNumber('courseSection')->middleware('permission:ENR.view')->name('course-sections.enrollments');
    Route::get('students/{student}/registration-result', [EnrollmentResultController::class, 'forStudent'])->whereNumber('student')->middleware('permission:ENR.view')->name('students.registration-result');
    Route::get('students/{student}/registration-result/slip', [EnrollmentResultController::class, 'studentSlip'])->whereNumber('student')->middleware('permission:ENR.view')->name('students.registration-slip');
    Route::get('enrollment-stats', [EnrollmentResultController::class, 'stats'])->middleware('permission:ENR.view')->name('enrollment-stats');
    // Đăng ký / hủy thay, ngoại lệ, chốt đăng ký (FR-ENR-009, 012; BR-ENR-08): ENR toàn trường (ACAD)
    Route::post('students/{student}/enrollments', [StaffEnrollmentController::class, 'store'])->whereNumber('student')->middleware('permission:ENR.create,ALL')->name('students.enrollments.store');
    Route::post('enrollments/{enrollment}/cancel', [StaffEnrollmentController::class, 'cancel'])->middleware('permission:ENR.delete,ALL')->name('enrollments.cancel');
    Route::get('terms/{term}/registration/finalize', [StaffEnrollmentController::class, 'preview'])->middleware('permission:ENR.approve,ALL')->name('terms.registration.finalize-preview');
    Route::post('terms/{term}/registration/finalize', [StaffEnrollmentController::class, 'finalize'])->middleware('permission:ENR.approve,ALL')->name('terms.registration.finalize');
    Route::get('students/{student}/registration-options', [RegistrationOptionsController::class, 'forStudent'])->whereNumber('student')->middleware('permission:ENR.view')->name('students.registration-options');

    // Duyệt và công bố điểm (GRD, FR-GRD-004, 005): xem theo phạm vi GRD.view; duyệt khoa = DEAN, xác nhận / công bố /
    // khóa = ACAD, EXAM (GRD.approve toàn trường); trả lại = khoa hoặc phòng — kiểm tra từng bước trong GradeWorkflowService
    Route::get('grade-workflows', [GradeWorkflowController::class, 'index'])->middleware('permission:GRD.view')->name('grade-workflows.index');
    Route::get('course-sections/{courseSection}/grade-workflow', [GradeWorkflowController::class, 'show'])->whereNumber('courseSection')->middleware('permission:GRD.view')->name('course-sections.grade-workflow.show');
    Route::post('course-sections/{courseSection}/grade-workflow', [GradeWorkflowController::class, 'change'])->whereNumber('courseSection')->middleware('permission:GRD.approve')->name('course-sections.grade-workflow.change');

    // Thông báo thủ công (NOT, FR-NOT-006, 015): đối tượng trong phạm vi người gửi (BR-NOT-05), kiểm tra trong AudienceResolver
    Route::get('announcements/options', [AnnouncementController::class, 'options'])->middleware('permission:NOT.create')->name('announcements.options');
    Route::post('announcements/preview', [AnnouncementController::class, 'preview'])->middleware('permission:NOT.create')->name('announcements.preview');
    Route::get('announcements', [AnnouncementController::class, 'index'])->middleware('permission:NOT.create')->name('announcements.index');
    Route::post('announcements', [AnnouncementController::class, 'store'])->middleware('permission:NOT.create')->name('announcements.store');
    Route::post('announcements/{notification}/revoke', [AnnouncementController::class, 'revoke'])->whereNumber('notification')->middleware('permission:NOT.delete,ALL')->name('announcements.revoke');
    Route::post('recipient-groups', [AnnouncementController::class, 'saveGroup'])->middleware('permission:NOT.create')->name('recipient-groups.store');
    Route::put('recipient-groups/{recipientGroup}', [AnnouncementController::class, 'saveGroup'])->whereNumber('recipientGroup')->middleware('permission:NOT.create')->name('recipient-groups.update');
    Route::delete('recipient-groups/{recipientGroup}', [AnnouncementController::class, 'deleteGroup'])->whereNumber('recipientGroup')->middleware('permission:NOT.create')->name('recipient-groups.destroy');
    Route::get('soan-thong-bao', [AnnouncementPageController::class, 'index'])->middleware('permission:NOT.create')->name('pages.announcements');

    // Cấu hình thông báo theo sự kiện (NOT, FR-NOT-004; phụ lục A): chỉ ADMIN (NOT.update toàn trường)
    Route::get('notification-events', [EventSubscriptionController::class, 'index'])->middleware('permission:NOT.update,ALL')->name('notification-events.index');
    Route::put('notification-events/{code}', [EventSubscriptionController::class, 'update'])->where('code', 'EV-[A-Z]+-[0-9]{2}')->middleware('permission:NOT.update,ALL')->name('notification-events.update');

    // Điều chỉnh điểm sau công bố (FR-GRD-006): khoa duyệt (DEAN, phạm vi) → ACAD / EXAM xác nhận; từ chối có lý do
    Route::get('grade-change-requests', [GradeChangeController::class, 'index'])->middleware('permission:GRD.approve')->name('grade-change-requests.index');
    Route::post('grade-change-requests/{gradeChangeRequest}/approve', [GradeChangeController::class, 'approve'])->whereNumber('gradeChangeRequest')->middleware('permission:GRD.approve')->name('grade-change-requests.approve');
    Route::post('grade-change-requests/{gradeChangeRequest}/reject', [GradeChangeController::class, 'reject'])->whereNumber('gradeChangeRequest')->middleware('permission:GRD.approve')->name('grade-change-requests.reject');
    Route::get('grade-change-requests/{gradeChangeRequest}/evidence', [GradeChangeController::class, 'evidence'])->whereNumber('gradeChangeRequest')->middleware('permission:GRD.approve')->name('grade-change-requests.evidence');
    Route::get('enrollments/{enrollment}/grade-history', [GradeChangeController::class, 'history'])->whereNumber('enrollment')->middleware('permission:GRD.view')->name('enrollments.grade-history');
    // Sổ điểm lớp (FR-GRD-011), GPA và bảng điểm của một sinh viên trong phạm vi (FR-GRD-009, 010)
    Route::get('course-sections/{courseSection}/grade-report', [TranscriptController::class, 'gradebook'])->whereNumber('courseSection')->middleware('permission:GRD.view')->name('course-sections.grade-report');
    Route::get('students/{student}/gpa', [StudentGpaController::class, 'show'])->whereNumber('student')->middleware('permission:GRD.view')->name('students.gpa');
    Route::get('students/{student}/transcript', [TranscriptController::class, 'student'])->whereNumber('student')->middleware('permission:GRD.view')->name('students.transcript');
    // Trang duyệt và công bố điểm theo lớp (FR-GRD-004, 005); nút bước tiếp theo theo vai trò
    Route::get('quan-ly-diem', [GradeAdminPageController::class, 'index'])->middleware('permission:GRD.view')->name('pages.grades');
    // Trang và form HTML của giao diện FE (admin/grade/*): xem sổ điểm lớp, lịch sử điểm, tra GPA, minh chứng; chuyển bước,
    // duyệt / từ chối điều chỉnh điểm (redirect kèm thông báo, cùng kiểm tra quyền như API JSON)
    Route::get('quan-ly-diem/lop/{courseSection}', [GradeAdminDetailController::class, 'section'])->whereNumber('courseSection')->middleware('permission:GRD.view')->name('pages.grade-section');
    Route::get('quan-ly-diem/lich-su/{enrollment}', [GradeAdminDetailController::class, 'history'])->whereNumber('enrollment')->middleware('permission:GRD.view')->name('pages.grade-history');
    Route::get('quan-ly-diem/gpa', [GradeAdminDetailController::class, 'gpa'])->middleware('permission:GRD.view')->name('pages.grade-gpa');
    Route::get('quan-ly-diem/minh-chung/{gradeChangeRequest}', [GradeAdminDetailController::class, 'evidence'])->whereNumber('gradeChangeRequest')->middleware('permission:GRD.view')->name('pages.grade-evidence');
    Route::post('quan-ly-diem/lop/{courseSection}/chuyen-trang-thai', [GradeAdminActionController::class, 'workflow'])->whereNumber('courseSection')->middleware('permission:GRD.approve')->name('pages.grade-workflow.change');
    Route::post('quan-ly-diem/yeu-cau/{gradeChangeRequest}/duyet', [GradeAdminActionController::class, 'approve'])->whereNumber('gradeChangeRequest')->middleware('permission:GRD.approve')->name('pages.grade-changes.approve');
    Route::post('quan-ly-diem/yeu-cau/{gradeChangeRequest}/tu-choi', [GradeAdminActionController::class, 'reject'])->whereNumber('gradeChangeRequest')->middleware('permission:GRD.approve')->name('pages.grade-changes.reject');
    Route::get('course-sections/{courseSection}/gradebook/export', [TranscriptController::class, 'gradebook'])->whereNumber('courseSection')->middleware('permission:GRD.view')->name('course-sections.gradebook.export');
    Route::get('duyet-dieu-chinh-diem', [GradeChangePageController::class, 'index'])->middleware('permission:GRD.approve')->name('pages.grade-changes');

    // Thời khóa biểu (TTB, FR-TTB-001, 002): khung tiết xem = TTB.view, sửa = ACAD (TTB toàn trường);
    // mẫu lịch lớp học phần: xem theo phạm vi lớp (DEAN, LEC), thêm / sửa / xóa chỉ ACAD — kiểm tra trong ScheduleRuleController.
    Route::get('periods', [PeriodController::class, 'index'])->middleware('permission:TTB.view')->name('periods.index');
    Route::post('periods', [PeriodController::class, 'store'])->middleware('permission:TTB.create,ALL')->name('periods.store');
    Route::put('periods/{period}', [PeriodController::class, 'update'])->middleware('permission:TTB.update,ALL')->name('periods.update');
    Route::delete('periods/{period}', [PeriodController::class, 'destroy'])->middleware('permission:TTB.delete,ALL')->name('periods.destroy');
    Route::get('course-sections/{courseSection}/schedule', [ScheduleRuleController::class, 'index'])->whereNumber('courseSection')->name('course-sections.schedule.index');
    // Xem thời khóa biểu theo đối tượng (FR-TTB-004; phạm vi kiểm tra trong controller); nháp – công bố – khóa theo học kỳ
    // (FR-TTB-006): xem trạng thái = TTB.view, công bố / khóa = ACAD (TTB.approve toàn trường)
    Route::get('timetable', [TimetableController::class, 'show'])->middleware('permission:TTB.view')->name('timetable.show');
    Route::get('terms/{term}/timetable', [TimetableController::class, 'status'])->middleware('permission:TTB.view')->name('terms.timetable.status');
    Route::post('terms/{term}/timetable/publish', [TimetableController::class, 'publish'])->middleware('permission:TTB.approve,ALL')->name('terms.timetable.publish');
    Route::post('terms/{term}/timetable/lock', [TimetableController::class, 'lock'])->middleware('permission:TTB.approve,ALL')->name('terms.timetable.lock');

    // Buổi học cụ thể (FR-TTB-005, 009): xem theo phạm vi lớp; sinh lại, dạy bù, hủy buổi = ACAD
    Route::get('course-sections/{courseSection}/sessions', [ClassSessionController::class, 'index'])->whereNumber('courseSection')->middleware('permission:TTB.view')->name('course-sections.sessions.index');
    Route::post('course-sections/{courseSection}/sessions/generate', [ClassSessionController::class, 'generate'])->whereNumber('courseSection')->middleware('permission:TTB.update,ALL')->name('course-sections.sessions.generate');
    Route::post('class-sessions/{classSession}/makeup', [ClassSessionController::class, 'makeup'])->middleware('permission:TTB.create,ALL')->name('class-sessions.makeup');
    Route::post('class-sessions/{classSession}/cancel', [ClassSessionController::class, 'cancel'])->middleware('permission:TTB.update,ALL')->name('class-sessions.cancel');
    Route::get('course-sections/{courseSection}/schedule-conflicts', [ScheduleRuleController::class, 'conflicts'])->whereNumber('courseSection')->name('course-sections.schedule.conflicts');
    Route::post('course-sections/{courseSection}/schedule', [ScheduleRuleController::class, 'store'])->whereNumber('courseSection')->name('course-sections.schedule.store');
    Route::put('schedule-rules/{scheduleRule}', [ScheduleRuleController::class, 'update'])->name('schedule-rules.update');
    Route::delete('schedule-rules/{scheduleRule}', [ScheduleRuleController::class, 'destroy'])->name('schedule-rules.destroy');

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

    // FE #9: Năm học, học kỳ, lịch học vụ và ngày nghỉ.
    Route::get('nam-hoc', [AcademicYearPageController::class, 'index'])
        ->middleware('permission:ACY.view,ALL')
        ->name('pages.academic-years');

    // Điểm danh và chuyên cần (ATT, #142–#144): tổng hợp theo phạm vi, xuất, yêu cầu sửa quá hạn, khóa sổ, miễn cấm thi.
    // Phòng Đào tạo điều chỉnh phiếu điểm danh qua cùng API với giảng viên (AttendanceService kiểm tra ATT.approve).
    Route::get('attendance/summaries', [AttendanceAdminController::class, 'summaries'])->middleware('permission:ATT.view')->name('attendance.summaries');
    Route::get('attendance/summaries/export', [AttendanceAdminController::class, 'export'])->middleware('permission:ATT.export')->name('attendance.summaries.export');
    Route::post('attendance-summaries/{attendanceSummary}/exempt', [AttendanceAdminController::class, 'exempt'])->whereNumber('attendanceSummary')->middleware('permission:ATT.approve,ALL')->name('attendance.exempt');
    Route::get('attendance-change-requests', [AttendanceAdminController::class, 'changeRequests'])->middleware('permission:ATT.view')->name('attendance.change-requests');
    Route::post('attendance-change-requests/{attendanceChangeRequest}/approve', [AttendanceAdminController::class, 'approve'])->whereNumber('attendanceChangeRequest')->middleware('permission:ATT.approve,ALL')->name('attendance.change-requests.approve');
    Route::post('attendance-change-requests/{attendanceChangeRequest}/reject', [AttendanceAdminController::class, 'reject'])->whereNumber('attendanceChangeRequest')->middleware('permission:ATT.approve,ALL')->name('attendance.change-requests.reject');
    Route::post('course-sections/{courseSection}/attendance/lock', [AttendanceAdminController::class, 'lock'])->whereNumber('courseSection')->middleware('permission:ATT.approve,ALL')->name('attendance.lock');
    Route::post('course-sections/{courseSection}/attendance/unlock', [AttendanceAdminController::class, 'unlock'])->whereNumber('courseSection')->middleware('permission:ATT.approve,ALL')->name('attendance.unlock');
    Route::get('course-sections/{courseSection}/attendance/sessions', [AttendanceController::class, 'sessions'])->whereNumber('courseSection')->middleware('permission:ATT.view')->name('attendance.sessions');
    Route::get('course-sections/{courseSection}/attendance/summary', [AttendanceController::class, 'summary'])->whereNumber('courseSection')->middleware('permission:ATT.view')->name('attendance.summary');
    Route::get('class-sessions/{classSession}/attendance', [AttendanceController::class, 'sheet'])->whereNumber('classSession')->middleware('permission:ATT.view')->name('attendance.sheet');
    Route::put('class-sessions/{classSession}/attendance', [AttendanceController::class, 'record'])->whereNumber('classSession')->middleware('permission:ATT.approve,ALL')->name('attendance.record');

    // Trang điểm danh / chuyên cần (FE #38, #42, #43)
    Route::get('diem-danh', [AttendancePageController::class, 'admin'])->middleware('permission:ATT.view')->name('pages.attendance');
});
