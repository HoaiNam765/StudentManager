<?php

use App\Http\Controllers\DashboardController;
use App\Modules\Grade\Http\Controllers\GradebookController;
use App\Modules\SchoolClass\Http\Controllers\TeacherClassRosterController;
use App\Modules\Teacher\Http\Controllers\LecturerProfileController;
use App\Modules\Teacher\Http\Controllers\LecturerScheduleController;
use App\Modules\Timetable\Http\Controllers\TimetableController;
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

    // Lịch dạy của tôi theo buổi học cụ thể (FR-TTB-004): ?view=week|month|term&date=&term_id=; chỉ khi đã công bố
    Route::get('timetable', [TimetableController::class, 'mineLecturer'])->middleware('permission:TTB.view')->name('timetable');

    // Danh sách lớp đang dạy và lớp được cố vấn; roster chỉ gồm sinh viên thuộc phạm vi được phân công.
    Route::middleware('permission:CLS.view')->group(function (): void {
        Route::get('course-sections', [TeacherClassRosterController::class, 'sections'])->name('course-sections.index');
        Route::get('course-sections/{courseSection}/students', [TeacherClassRosterController::class, 'sectionStudents'])->whereNumber('courseSection')->name('course-sections.students.index');
        Route::get('admin-classes', [TeacherClassRosterController::class, 'adviseeClasses'])->name('admin-classes.index');
        Route::get('admin-classes/{adminClass}/students', [TeacherClassRosterController::class, 'adviseeStudents'])->whereNumber('adminClass')->name('admin-classes.students.index');
    });

    // Lịch bận, nguyện vọng học phần kỳ tới, nghỉ phép / công tác của chính giảng viên (FR-TCH-008, 012; phạm vi OWN)
    Route::get('availability', [LecturerScheduleController::class, 'myAvailability'])->name('availability.index');
    Route::post('availability', [LecturerScheduleController::class, 'storeAvailability'])->name('availability.store');
    Route::delete('availability/{lecturerAvailability}', [LecturerScheduleController::class, 'destroyAvailability'])->name('availability.destroy');
    Route::get('preferences', [LecturerScheduleController::class, 'myPreferences'])->name('preferences.show');
    Route::put('preferences', [LecturerScheduleController::class, 'updatePreferences'])->name('preferences.update');
    Route::get('leaves', [LecturerScheduleController::class, 'myLeaves'])->name('leaves.index');
    Route::post('leaves', [LecturerScheduleController::class, 'storeLeave'])->name('leaves.store');
    Route::post('leaves/{lecturerLeave}/cancel', [LecturerScheduleController::class, 'cancelLeave'])->name('leaves.cancel');

    Route::prefix('course-sections/{courseSection}')
        ->name('gradebook.')
        ->group(function (): void {
            Route::get(
                'gradebook',
                [GradebookController::class, 'show']
            )
                ->middleware('permission:GRD.view')
                ->name('show');

            Route::post(
                'grade-components/initialize',
                [GradebookController::class, 'initialize']
            )
                ->middleware('permission:GRD.update')
                ->name('initialize');

            Route::put(
                'grade-components',
                [GradebookController::class, 'replaceStructure']
            )
                ->middleware('permission:GRD.update')
                ->name('structure.update');

            Route::post(
                'grades',
                [GradebookController::class, 'saveDraft']
            )
                ->middleware('permission:GRD.update')
                ->name('draft.save');

            Route::get(
                'grades/template',
                [GradebookController::class, 'template']
            )
                ->middleware('permission:GRD.view')
                ->name('template');

            Route::post(
                'grades/import',
                [GradebookController::class, 'import']
            )
                ->middleware('permission:GRD.update')
                ->name('import');
        });
});
