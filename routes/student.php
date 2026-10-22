<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Pages\Student\GradePageController;
use App\Http\Controllers\Pages\Student\RegistrationPageController;
use App\Modules\Enrollment\Http\Controllers\EnrollmentController;
use App\Modules\Enrollment\Http\Controllers\EnrollmentResultController;
use App\Modules\Enrollment\Http\Controllers\RegistrationOptionsController;
use App\Modules\Grade\Http\Controllers\StudentGpaController;
use App\Modules\Grade\Http\Controllers\StudentGradesController;
use App\Modules\Grade\Http\Controllers\TranscriptController;
use App\Modules\Report\Http\Controllers\DashboardDataController;
use App\Modules\Student\Http\Controllers\StudentSelfController;
use App\Modules\Timetable\Http\Controllers\TimetableController;
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

    // Hồ sơ của tôi (FR-STU-006; BR-STU-06): xem = STU.view, tự sửa / gửi yêu cầu = STU.update (OWN)
    Route::get('/profile', [StudentSelfController::class, 'show'])->middleware('permission:STU.view')->name('profile.show');
    Route::middleware('permission:STU.update')->group(function (): void {
        Route::put('/profile', [StudentSelfController::class, 'update'])->name('profile.update');
        Route::post('/profile/photo', [StudentSelfController::class, 'photo'])->name('profile.photo');
        Route::post('/profile/contacts', [StudentSelfController::class, 'storeContact'])->name('profile.contacts.store');
        Route::put('/profile/contacts/{studentContact}', [StudentSelfController::class, 'updateContact'])->name('profile.contacts.update');
        Route::delete('/profile/contacts/{studentContact}', [StudentSelfController::class, 'destroyContact'])->name('profile.contacts.destroy');
        Route::post('/profile/change-requests', [StudentSelfController::class, 'storeRequest'])->name('profile.change-requests.store');
        Route::post('/profile/change-requests/{studentChangeRequest}/cancel', [StudentSelfController::class, 'cancelRequest'])->name('profile.change-requests.cancel');
    });

    // Đăng ký học phần (ENR, FR-ENR-002): đợt đang mở của sinh viên và các lớp được chọn (JSON)
    Route::get('/registration/options', [RegistrationOptionsController::class, 'mine'])->middleware('permission:ENR.view')->name('registration.options');

    // Đăng ký / hủy / rút / đổi lớp của chính mình (FR-ENR-003, 005..007): STU có C, D trên ENR (OWN)
    Route::get('/registration/enrollments', [EnrollmentController::class, 'mine'])->middleware('permission:ENR.view')->name('registration.enrollments');
    Route::post('/registration/enrollments', [EnrollmentController::class, 'store'])->middleware('permission:ENR.create')->name('registration.enroll');
    Route::post('/registration/enrollments/{enrollment}/cancel', [EnrollmentController::class, 'cancel'])->middleware('permission:ENR.delete')->name('registration.cancel');
    Route::post('/registration/enrollments/{enrollment}/withdraw', [EnrollmentController::class, 'withdraw'])->middleware('permission:ENR.delete')->name('registration.withdraw');
    Route::post('/registration/enrollments/{enrollment}/change', [EnrollmentController::class, 'change'])->middleware('permission:ENR.create')->name('registration.change');

    // Thời khóa biểu của tôi theo buổi học cụ thể (FR-TTB-004): ?view=week|month|term&date=&term_id=; chỉ khi đã công bố
    Route::get('/timetable', [TimetableController::class, 'mineStudent'])->middleware('permission:TTB.view')->name('timetable');

    // Kết quả đăng ký, thời khóa biểu cá nhân, phiếu đăng ký PDF (FR-ENR-010)
    Route::get('/registration/result', [EnrollmentResultController::class, 'mine'])->middleware('permission:ENR.view')->name('registration.result');
    Route::get('/registration/result/slip', [EnrollmentResultController::class, 'mineSlip'])->middleware('permission:ENR.view')->name('registration.slip');

    // Trang giao diện gọi các API ở trên (docs/fe-be-mapping.md, nhóm a); cùng quyền với API chính của trang
    Route::get('/dang-ky-hoc-phan', [RegistrationPageController::class, 'index'])->middleware('permission:ENR.view')->name('pages.registration');
    Route::get('/ket-qua-dang-ky', [RegistrationPageController::class, 'result'])->middleware('permission:ENR.view')->name('pages.registration-result');

    Route::get(
        '/dashboard/data',
        [DashboardDataController::class, 'student']
    )->name('dashboard.data');

    // Điểm của tôi (FR-GRD-005; BR-GRD-08): chỉ lớp đã công bố, kể cả điểm thành phần
    Route::get('/grades', [StudentGradesController::class, 'mine'])->middleware('permission:GRD.view')->name('grades');
    // GPA học kỳ / tích lũy, xếp loại (FR-GRD-008, 009) và bảng điểm cá nhân JSON / PDF tiếng Việt – Anh (FR-GRD-010)
    Route::get('/gpa', [StudentGpaController::class, 'mine'])->middleware('permission:GRD.view')->name('gpa');
    Route::get('/transcript', [TranscriptController::class, 'mine'])->middleware('permission:GRD.view')->name('transcript');
    Route::get('/transcript/pdf', [TranscriptController::class, 'minePdf'])->middleware('permission:GRD.view')->name('transcript.mine');
    Route::get('/ket-qua-hoc-tap', [GradePageController::class, 'index'])->middleware('permission:GRD.view')->name('pages.grades');
});
