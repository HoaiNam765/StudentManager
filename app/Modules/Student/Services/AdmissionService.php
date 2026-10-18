<?php

namespace App\Modules\Student\Services;

use App\Models\User;
use App\Modules\Auth\Enums\ProfileType;
use App\Modules\Auth\Services\UserService;
use App\Modules\SchoolClass\Models\AdminClass;
use App\Modules\SchoolClass\Models\AdminClassMember;
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
            $class = $this->pickClass($student, $adminClassId);
            $code = $this->nextStudentCode($student);
            $email = $this->schoolEmail($code);

            $student->update([
                'student_code' => $code,
                'school_email' => $email,
                'admission_date' => $date,
                'status' => StudentStatus::Studying,
            ]);

            $this->curricula->assignDefault($student, $date);

            AdminClassMember::create([
                'admin_class_id' => $class->id,
                'student_id' => $student->id,
                'effective_from' => $date,
                'reason' => 'Xếp lớp khi nhập học',
            ]);

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

    /** Lớp hành chính cùng khóa – ngành – hệ, đang hoạt động, còn chỗ (BR-STU-04, BR-CLS-01). */
    private function pickClass(Student $student, ?int $adminClassId): AdminClass
    {
        $candidates = AdminClass::query()
            ->active()
            ->where('major_id', $student->major_id)
            ->where('training_type_id', $student->training_type_id)
            ->whereHas('cohort', fn ($q) => $q->where('start_year', $student->cohort))
            ->when($adminClassId !== null, fn ($q) => $q->whereKey($adminClassId))
            ->orderBy('code')
            ->lockForUpdate()
            ->get();

        foreach ($candidates as $class) {
            $members = AdminClassMember::query()->where('admin_class_id', $class->id)->current()->count();

            if ($members < $class->max_size) {
                return $class;
            }
        }

        $this->fail(
            $adminClassId !== null
                ? 'Lớp hành chính đã chọn không cùng khóa – ngành – hệ với sinh viên, đã đóng hoặc đã đủ sĩ số.'
                : "Không còn lớp hành chính khóa {$student->cohort} ngành này còn chỗ.",
            'Mở thêm lớp hành chính cho khóa – ngành (CLS) hoặc tăng sĩ số tối đa, rồi xác nhận lại.'
        );
    }

    private function today(): string
    {
        return now(config('studentmanager.display_timezone'))->toDateString();
    }
}
