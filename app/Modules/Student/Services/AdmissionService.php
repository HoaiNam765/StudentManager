<?php

namespace App\Modules\Student\Services;

use App\Models\User;
use App\Modules\Auth\Enums\ProfileType;
use App\Modules\Auth\Services\UserService;
use App\Modules\SchoolClass\Models\AdminClass;
use App\Modules\SchoolClass\Services\ClassRosterService;
use App\Modules\Student\Enums\StudentStatus;
use App\Modules\Student\Events\StudentAdmitted;
use App\Modules\Student\Models\Student;
use App\Support\Audit\AuditLogger;
use App\Support\Services\BaseService;
use Carbon\CarbonImmutable;

/**
 * Xác nhận nhập học (FR-STU-002; BR-STU-01, 04; EV-STU-01; UAT-04).
 *
 * Trong **một giao dịch**: cấp MSSV (khóa + mã ngành + số thứ tự theo cấu hình, duy nhất, không tái sử dụng), email trường,
 * tài khoản (AUTH, vai trò STU, mật khẩu tạm gửi email), CTĐT (`StudentCurriculumService::assignDefault`), lớp hành chính
 * (chọn sẵn hoặc tự chọn lớp cùng khóa – ngành – hệ còn chỗ), chuyển sang Đang học. Bước nào lỗi thì hoàn tác toàn bộ.
 * Hủy nhập học: hồ sơ Chờ nhập học chưa phát sinh dữ liệu được xóa mềm kèm lý do.
 */
class AdmissionService extends BaseService
{
    public function __construct(
        private readonly UserService $users,
        private readonly StudentCurriculumService $curricula,
        private readonly StudentService $students,
        private readonly ClassRosterService $classRoster,
        private readonly AuditLogger $audit,
    ) {}

    public function admit(Student $student, ?int $adminClassId = null, ?string $admissionDate = null, ?User $actor = null): Student
    {
        if ($student->status !== StudentStatus::Pending) {
            $this->fail(
                "Hồ sơ {$student->full_name} đang ở trạng thái {$student->status->label()}.",
                'Chỉ xác nhận nhập học cho hồ sơ Chờ nhập học (FR-STU-002).'
            );
        }

        if ($student->student_code !== null) {
            $this->fail("Hồ sơ đã có MSSV {$student->student_code}.", 'MSSV chỉ cấp một lần (BR-STU-01).');
        }

        $date = CarbonImmutable::parse($admissionDate ?? $student->admission_date ?? $this->today())->toDateString();

        $student = $this->audit->withReason('Xác nhận nhập học', fn () => $this->transaction(function () use ($student, $adminClassId, $date, $actor): Student {
            $student = Student::query()->lockForUpdate()->findOrFail($student->id);
            $code = $this->nextStudentCode($student);
            $email = $this->schoolEmail($code);

            $student->update([
                'student_code' => $code,
                'school_email' => $email,
                'admission_date' => $date,
                'status' => StudentStatus::Studying,
            ]);

            $this->curricula->assignDefault($student, $date);

            if ($adminClassId === null) {
                $this->classRoster->assignForAdmission($student, $date);
            } else {
                $class = AdminClass::query()->findOrFail($adminClassId);
                $this->classRoster->assign($class, [$student->id], $date, 'Xếp lớp khi nhập học');
            }

            $user = $this->users->create([
                'username' => $code,
                'name' => $student->full_name,
                'email' => $email,
                'profile_type' => ProfileType::Student->value,
                'profile_id' => $student->id,
            ], $actor);

            $student->update(['user_id' => $user->id]);

            return $student->refresh();
        }));

        StudentAdmitted::dispatch($student);

        return $student;
    }

    /** Hủy nhập học: hồ sơ Chờ nhập học chưa phát sinh dữ liệu (BA 6.2, sơ đồ trạng thái). */
    public function cancel(Student $student, string $reason): void
    {
        if ($student->status !== StudentStatus::Pending) {
            $this->fail(
                "Hồ sơ {$student->full_name} đang ở trạng thái {$student->status->label()} nên không hủy nhập học được.",
                'Sinh viên đã nhập học thì dùng quyết định thôi học / chuyển trường (#114).'
            );
        }

        $this->students->delete($student, "Hủy nhập học: {$reason}");
    }

    /** MSSV kế tiếp: 2 số cuối của khóa + N số cuối mã ngành + số thứ tự trong khóa – ngành (tính cả hồ sơ đã xóa). */
    public function nextStudentCode(Student $student): string
    {
        $student->loadMissing('major');
        $majorDigits = (int) config('studentmanager.student.code_major_digits', 4);
        $sequenceDigits = (int) config('studentmanager.student.code_sequence_digits', 4);
        $prefix = sprintf('%02d', $student->cohort % 100).substr(preg_replace('/\D/', '', (string) $student->major->code), -$majorDigits);

        $last = Student::withTrashed()
            ->where('student_code', 'like', $prefix.'%')
            ->whereRaw('CHAR_LENGTH(student_code) = ?', [strlen($prefix) + $sequenceDigits])
            ->lockForUpdate()
            ->max('student_code');

        $sequence = $last === null ? 1 : ((int) substr($last, strlen($prefix))) + 1;

        if ($sequence >= 10 ** $sequenceDigits) {
            $this->fail("Đã hết số thứ tự MSSV cho khóa {$student->cohort}, ngành {$student->major->code}.", 'Tăng số chữ số của số thứ tự (STUDENT_CODE_SEQUENCE_DIGITS).');
        }

        return $prefix.str_pad((string) $sequence, $sequenceDigits, '0', STR_PAD_LEFT);
    }

    private function schoolEmail(string $code): string
    {
        $email = mb_strtolower($code.'@'.config('studentmanager.student.email_domain', 'student.studentmanager.test'));

        if (Student::withTrashed()->where('school_email', $email)->exists() || User::query()->where('email', $email)->exists()) {
            $this->fail("Email trường {$email} đã được dùng.", 'Kiểm tra dữ liệu trùng email trường (BR-STU-02) trước khi xác nhận nhập học.');
        }

        return $email;
    }

    private function today(): string
    {
        return now(config('studentmanager.display_timezone'))->toDateString();
    }
}
