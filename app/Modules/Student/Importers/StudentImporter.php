<?php

namespace App\Modules\Student\Importers;

use App\Modules\Faculty\Models\Major;
use App\Modules\Faculty\Models\Specialization;
use App\Modules\Faculty\Models\TrainingType;
use App\Modules\Student\Enums\StudentStatus;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Services\IdentityNumber;
use App\Modules\Student\Services\StudentService;
use App\Modules\System\Importers\SpreadsheetImporter;
use App\Modules\System\Models\LookupValue;
use App\Support\Exceptions\BusinessRuleException;
use App\Support\References\ReferenceRegistry;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use Throwable;

/**
 * Nhập hồ sơ thí sinh trúng tuyển / sinh viên từ Excel, CSV qua trung tâm import (FR-STU-007, GC-07): xem trước, báo lỗi
 * theo dòng và cột, chọn lưu toàn bộ hoặc chỉ dòng hợp lệ, hoàn tác theo lô. Hồ sơ tạo ở trạng thái **Chờ nhập học**
 * (chưa MSSV, chưa tài khoản — cấp khi xác nhận nhập học, #102). Kiểm tra bằng chính quy tắc của StudentService
 * (ngành, chuyên ngành, hệ, danh mục, tuổi, trùng CCCD và email) và trùng trong cùng file.
 */
class StudentImporter extends SpreadsheetImporter
{
    public const KEY = 'students';

    public const COLUMNS = [
        'required' => ['full_name', 'date_of_birth', 'major_code', 'training_type_code', 'cohort'],
        'optional' => ['gender', 'place_of_birth', 'ethnicity', 'national_id', 'national_id_issued_on', 'national_id_issued_by',
            'personal_email', 'phone', 'permanent_address', 'specialization_code', 'admission_date', 'note'],
    ];

    /** Số CCCD / email đã gặp trong file đang kiểm tra (chặn trùng trong cùng file). */
    private array $seen = [];

    public function key(): string
    {
        return self::KEY;
    }

    public function label(): string
    {
        return 'Hồ sơ sinh viên (thí sinh trúng tuyển)';
    }

    public function description(): string
    {
        return 'Cột bắt buộc: full_name, date_of_birth (dd/mm/yyyy hoặc yyyy-mm-dd), major_code (mã ngành, ví dụ 7480201), '
            .'training_type_code (CQ, LT, VB2, VLVH), cohort (năm nhập học, ví dụ 2026). Tùy chọn: gender (Nam / Nữ), place_of_birth, '
            .'ethnicity (tên dân tộc, ví dụ Kinh), national_id (12 chữ số), national_id_issued_on, national_id_issued_by, personal_email, '
            .'phone, permanent_address, specialization_code, admission_date, note. Hồ sơ tạo ở trạng thái Chờ nhập học.';
    }

    protected function columns(): array
    {
        return self::COLUMNS;
    }

    public function validateRow(int $rowNumber, array $rawRow): array
    {
        if ($rowNumber <= 2) {
            $this->seen = [];
        }

        $errors = [];
        $data = $this->data($rawRow, $errors);

        // Trùng trong cùng file
        foreach (['national_id' => IdentityNumber::normalize($data['national_id'] ?? null), 'personal_email' => $data['personal_email'] ?? null] as $column => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if (isset($this->seen[$column][$value])) {
                $errors[] = ['column' => $column, 'message' => 'Trùng với dòng '.$this->seen[$column][$value].' trong cùng file.'];
            } else {
                $this->seen[$column][$value] = $rowNumber;
            }
        }

        if ($errors !== []) {
            return $errors;
        }

        // Quy tắc nghiệp vụ dùng chung với tạo hồ sơ (BR-STU-02, 03; ngành / chuyên ngành / hệ)
        try {
            app(StudentService::class)->assertCreatable($data);
        } catch (BusinessRuleException $e) {
            $errors[] = ['column' => $this->columnFor($e->getMessage()), 'message' => $e->userMessage()];
        }

        return $errors;
    }

    public function saveRow(int $rowNumber, array $rawRow): array
    {
        $errors = [];
        $student = app(StudentService::class)->create($this->data($rawRow, $errors));

        return ['type' => 'student', 'id' => $student->id];
    }

    /** Chỉ hoàn tác hồ sơ còn Chờ nhập học, chưa MSSV, chưa phát sinh dữ liệu. */
    public function canRollbackRow(string $modelType, int|string $modelId): bool
    {
        $student = Student::withTrashed()->find($modelId);

        return $student === null
            || ($student->status === StudentStatus::Pending && $student->student_code === null && app(ReferenceRegistry::class)->usages($student) === []);
    }

    public function rollbackRow(string $modelType, int|string $modelId): bool
    {
        $student = Student::withTrashed()->find($modelId);
        $student?->contacts()->withTrashed()->forceDelete();
        $student?->changeLogs()->delete();
        $student?->forceDelete();

        return true;
    }

    /**
     * Chuyển một dòng file thành dữ liệu hồ sơ; lỗi định dạng / mã không tồn tại thêm vào `$errors`.
     *
     * @param  list<array{column: string, message: string}>  $errors
     * @return array<string, mixed>
     */
    private function data(array $rawRow, array &$errors): array
    {
        $value = fn (string $column): string => trim((string) ($rawRow[$column] ?? ''));
        $data = [];

        if ($value('full_name') === '') {
            $errors[] = ['column' => 'full_name', 'message' => 'Họ và tên bắt buộc.'];
        } else {
            $data['full_name'] = preg_replace('/\s+/u', ' ', $value('full_name'));
        }

        foreach (['date_of_birth' => 'Ngày sinh', 'national_id_issued_on' => 'Ngày cấp', 'admission_date' => 'Ngày nhập học'] as $column => $label) {
            if ($value($column) === '') {
                if ($column === 'date_of_birth') {
                    $errors[] = ['column' => $column, 'message' => 'Ngày sinh bắt buộc.'];
                }

                continue;
            }

            $date = $this->date($value($column));
            $date === null
                ? $errors[] = ['column' => $column, 'message' => "{$label} không hợp lệ (dd/mm/yyyy hoặc yyyy-mm-dd)."]
                : $data[$column] = $date;
        }

        $major = Major::query()->where('code', $value('major_code'))->first();
        $major === null
            ? $errors[] = ['column' => 'major_code', 'message' => 'Không có ngành mã "'.$value('major_code').'".']
            : $data['major_id'] = $major->id;

        $type = TrainingType::query()->where('code', Str::upper($value('training_type_code')))->first();
        $type === null
            ? $errors[] = ['column' => 'training_type_code', 'message' => 'Không có hệ đào tạo mã "'.$value('training_type_code').'" (CQ, LT, VB2, VLVH).']
            : $data['training_type_id'] = $type->id;

        if (preg_match('/^(19|20)[0-9]{2}$/', $value('cohort')) !== 1) {
            $errors[] = ['column' => 'cohort', 'message' => 'Khóa là năm nhập học 4 chữ số, ví dụ 2026.'];
        } else {
            $data['cohort'] = (int) $value('cohort');
        }

        if ($value('specialization_code') !== '') {
            $specialization = Specialization::query()->where('code', Str::upper($value('specialization_code')))->first();
            $specialization === null
                ? $errors[] = ['column' => 'specialization_code', 'message' => 'Không có chuyên ngành mã "'.$value('specialization_code').'".']
                : $data['specialization_id'] = $specialization->id;
        }

        foreach (['gender' => ['GENDER', 'gender_id', 'Giới tính'], 'ethnicity' => ['ETHNICITY', 'ethnicity_id', 'Dân tộc']] as $column => [$category, $field, $label]) {
            if ($value($column) === '') {
                continue;
            }

            $id = LookupValue::query()
                ->whereHas('category', fn ($q) => $q->where('code', $category))
                ->where(fn ($q) => $q->where('name', $value($column))->orWhere('code', $value($column)))
                ->value('id');
            $id === null
                ? $errors[] = ['column' => $column, 'message' => "{$label} \"{$value($column)}\" không có trong danh mục."]
                : $data[$field] = $id;
        }

        $nationalId = preg_replace('/[\s.\-]/', '', $value('national_id'));

        if ($nationalId !== '') {
            preg_match('/^[0-9]{12}$/', $nationalId) === 1
                ? $data['national_id'] = $nationalId
                : $errors[] = ['column' => 'national_id', 'message' => 'Số CCCD gồm đúng 12 chữ số.'];
        }

        if ($value('personal_email') !== '') {
            filter_var($value('personal_email'), FILTER_VALIDATE_EMAIL) === false
                ? $errors[] = ['column' => 'personal_email', 'message' => 'Email cá nhân không hợp lệ.']
                : $data['personal_email'] = Str::lower($value('personal_email'));
        }

        $phone = preg_replace('/[\s.\-]/', '', $value('phone'));

        if ($phone !== '') {
            preg_match('/^(0|\+84)[0-9]{9,10}$/', $phone) === 1
                ? $data['phone'] = $phone
                : $errors[] = ['column' => 'phone', 'message' => 'Số điện thoại không hợp lệ (10–11 số, bắt đầu bằng 0 hoặc +84).'];
        }

        foreach (['place_of_birth' => 255, 'national_id_issued_by' => 255, 'permanent_address' => 500, 'note' => 1000] as $column => $max) {
            if ($value($column) === '') {
                continue;
            }

            mb_strlen($value($column)) > $max
                ? $errors[] = ['column' => $column, 'message' => "Tối đa {$max} ký tự."]
                : $data[$column] = $value($column);
        }

        return $data;
    }

    /** Đoán cột cho lỗi nghiệp vụ để báo đúng chỗ. */
    private function columnFor(string $message): string
    {
        return match (true) {
            str_contains($message, 'Tuổi') || str_contains($message, 'Ngày sinh') => 'date_of_birth',
            str_contains($message, 'giấy tờ') || str_contains($message, 'CCCD') => 'national_id',
            str_contains($message, 'Chuyên ngành') => 'specialization_code',
            str_contains($message, 'Hệ đào tạo') => 'training_type_code',
            str_contains($message, 'Ngành') => 'major_code',
            str_contains($message, 'email') => 'personal_email',
            default => '*',
        };
    }

    private function date(string $value): ?string
    {
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
