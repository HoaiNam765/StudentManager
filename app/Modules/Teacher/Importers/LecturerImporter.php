<?php

namespace App\Modules\Teacher\Importers;

use App\Modules\Faculty\Models\Department;
use App\Modules\Faculty\Models\LeadershipTerm;
use App\Modules\System\Importers\SpreadsheetImporter;
use App\Modules\Teacher\Enums\AcademicDegree;
use App\Modules\Teacher\Enums\AcademicTitle;
use App\Modules\Teacher\Models\Lecturer;
use App\Modules\Teacher\Services\LecturerService;
use App\Support\References\ReferenceRegistry;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

/**
 * Nhập danh sách giảng viên từ Excel / CSV qua trung tâm import (FR-TCH-013, GC-07): xem trước, báo lỗi theo dòng và
 * cột, chọn lưu, hoàn tác theo lô. Mỗi dòng tạo hồ sơ bằng LecturerService; có email thì cấp luôn tài khoản (tên đăng
 * nhập = mã giảng viên, vai trò LEC, mật khẩu tạm gửi email), không có email thì chưa cấp tài khoản (BR-TCH-01).
 */
class LecturerImporter extends SpreadsheetImporter
{
    public const KEY = 'lecturers';

    public const COLUMNS = [
        'required' => ['code', 'full_name', 'department_code'],
        'optional' => ['email', 'phone', 'academic_title', 'academic_degree', 'expertise', 'hired_on', 'date_of_birth', 'national_id'],
    ];

    public function key(): string
    {
        return self::KEY;
    }

    public function label(): string
    {
        return 'Danh sách giảng viên';
    }

    public function description(): string
    {
        return 'Cột bắt buộc: code (mã giảng viên), full_name, department_code (mã bộ môn chính, ví dụ BM-KTPM). Tùy chọn: email (có thì cấp tài khoản), phone, '
            .'academic_title (lecturer | senior_lecturer | principal_lecturer | associate_professor | professor, hoặc tên tiếng Việt như "Giảng viên chính"), '
            .'academic_degree (bachelor | engineer | master | doctor, hoặc "Thạc sĩ"…), expertise, hired_on và date_of_birth (dd/mm/yyyy hoặc yyyy-mm-dd), national_id (12 chữ số).';
    }

    protected function columns(): array
    {
        return self::COLUMNS;
    }

    public function validateRow(int $rowNumber, array $rawRow): array
    {
        $errors = [];
        $code = Str::upper(trim($rawRow['code'] ?? ''));

        if (preg_match('/^[A-Z0-9][A-Z0-9._\-]{0,19}$/', $code) !== 1) {
            $errors[] = ['column' => 'code', 'message' => 'Mã giảng viên bắt buộc, tối đa 20 ký tự, chỉ gồm chữ không dấu, số, dấu chấm, gạch ngang, gạch dưới.'];
        } elseif (Lecturer::withTrashed()->where('code', $code)->exists()) {
            $errors[] = ['column' => 'code', 'message' => "Mã giảng viên {$code} đã được dùng."];
        }

        if (trim($rawRow['full_name'] ?? '') === '') {
            $errors[] = ['column' => 'full_name', 'message' => 'Họ và tên bắt buộc.'];
        }

        $department = Department::query()->where('code', Str::upper(trim($rawRow['department_code'] ?? '')))->first();

        if ($department === null) {
            $errors[] = ['column' => 'department_code', 'message' => 'Không có bộ môn mã "'.($rawRow['department_code'] ?? '').'".'];
        } elseif (! $department->isActive()) {
            $errors[] = ['column' => 'department_code', 'message' => "Bộ môn {$department->code} đang ngừng hoạt động."];
        }

        $email = trim($rawRow['email'] ?? '');

        if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors[] = ['column' => 'email', 'message' => 'Email không hợp lệ.'];
        } elseif ($email !== '' && DB::table('users')->where('email', Str::lower($email))->exists()) {
            $errors[] = ['column' => 'email', 'message' => "Email {$email} đã gắn với một tài khoản khác."];
        }

        if (($rawRow['academic_title'] ?? '') !== '' && $this->title($rawRow['academic_title']) === null) {
            $errors[] = ['column' => 'academic_title', 'message' => 'Chức danh không hợp lệ (ví dụ: lecturer hoặc "Giảng viên chính").'];
        }

        if (($rawRow['academic_degree'] ?? '') !== '' && $this->degree($rawRow['academic_degree']) === null) {
            $errors[] = ['column' => 'academic_degree', 'message' => 'Học vị không hợp lệ (ví dụ: master hoặc "Thạc sĩ").'];
        }

        foreach (['hired_on' => 'Ngày vào làm', 'date_of_birth' => 'Ngày sinh'] as $column => $label) {
            if (($rawRow[$column] ?? '') !== '' && $this->date($rawRow[$column]) === null) {
                $errors[] = ['column' => $column, 'message' => "{$label} không hợp lệ (dd/mm/yyyy hoặc yyyy-mm-dd)."];
            }
        }

        $nationalId = trim($rawRow['national_id'] ?? '');

        if ($nationalId !== '' && preg_match('/^[0-9]{12}$/', $nationalId) !== 1) {
            $errors[] = ['column' => 'national_id', 'message' => 'Số CCCD gồm đúng 12 chữ số.'];
        } elseif ($nationalId !== '' && Lecturer::withTrashed()->where('national_id', $nationalId)->exists()) {
            $errors[] = ['column' => 'national_id', 'message' => 'Số CCCD đã có trong hồ sơ giảng viên khác.'];
        }

        return $errors;
    }

    public function saveRow(int $rowNumber, array $rawRow): array
    {
        $email = Str::lower(trim($rawRow['email'] ?? ''));

        $lecturer = app(LecturerService::class)->create(array_filter([
            'code' => Str::upper(trim($rawRow['code'])),
            'full_name' => trim($rawRow['full_name']),
            'department_id' => Department::query()->where('code', Str::upper(trim($rawRow['department_code'])))->value('id'),
            'email' => $email !== '' ? $email : null,
            'phone' => trim($rawRow['phone'] ?? '') ?: null,
            'academic_title' => $this->title($rawRow['academic_title'] ?? '')?->value,
            'academic_degree' => $this->degree($rawRow['academic_degree'] ?? '')?->value,
            'expertise' => trim($rawRow['expertise'] ?? '') ?: null,
            'hired_on' => $this->date($rawRow['hired_on'] ?? ''),
            'date_of_birth' => $this->date($rawRow['date_of_birth'] ?? ''),
            'national_id' => trim($rawRow['national_id'] ?? '') ?: null,
        ], fn ($value) => $value !== null) + ['account' => $email !== '' ? 'new' : 'none']);

        return ['type' => 'lecturer', 'id' => $lecturer->id];
    }

    /** Chỉ hoàn tác hồ sơ chưa phát sinh dữ liệu: chưa kiêm nhiệm, chưa có đề nghị, chưa giữ chức vụ, tài khoản chưa đăng nhập. */
    public function canRollbackRow(string $modelType, int|string $modelId): bool
    {
        $lecturer = Lecturer::withTrashed()->with('user')->find($modelId);

        if ($lecturer === null) {
            return true;
        }

        if ($lecturer->units()->withTrashed()->exists() || $lecturer->changeRequests()->withTrashed()->exists()) {
            return false;
        }

        if (app(ReferenceRegistry::class)->usages($lecturer) !== []) {
            return false;
        }

        $user = $lecturer->user;

        return $user === null
            || ($user->last_login_at === null && ! LeadershipTerm::withTrashed()->where('user_id', $user->id)->exists());
    }

    /** Xóa hẳn hồ sơ và tài khoản do chính lô này tạo (mã được dùng lại khi nhập lại). */
    public function rollbackRow(string $modelType, int|string $modelId): bool
    {
        $lecturer = Lecturer::withTrashed()->with('user')->find($modelId);

        if ($lecturer === null) {
            return true;
        }

        $user = $lecturer->user;
        $lecturer->forceDelete();

        if ($user !== null) {
            DB::table('user_roles')->where('user_id', $user->id)->delete();
            $user->passwordHistories()->delete();
            $user->delete();
        }

        return true;
    }

    private function title(string $value): ?AcademicTitle
    {
        return $this->enum(AcademicTitle::class, $value);
    }

    private function degree(string $value): ?AcademicDegree
    {
        return $this->enum(AcademicDegree::class, $value);
    }

    /**
     * Nhận cả mã (senior_lecturer) lẫn tên tiếng Việt ("Giảng viên chính"), không phân biệt hoa thường.
     *
     * @template T of AcademicTitle|AcademicDegree
     *
     * @param  class-string<T>  $enum
     * @return T|null
     */
    private function enum(string $enum, string $value): AcademicTitle|AcademicDegree|null
    {
        $value = mb_strtolower(trim($value));

        if ($value === '') {
            return null;
        }

        foreach ($enum::cases() as $case) {
            if ($case->value === $value || mb_strtolower($case->label()) === $value) {
                return $case;
            }
        }

        return null;
    }

    private function date(string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        foreach (['d/m/Y', 'Y-m-d'] as $format) {
            try {
                $date = CarbonImmutable::createFromFormat('!'.$format, $value);

                if ($date !== null && $date->format($format) === $value) {
                    return $date->toDateString();
                }
            } catch (Throwable) {
                // thử định dạng khác
            }
        }

        return null;
    }
}
