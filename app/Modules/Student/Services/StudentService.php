<?php

namespace App\Modules\Student\Services;

use App\Models\User;
use App\Modules\Faculty\Models\Major;
use App\Modules\Faculty\Models\Specialization;
use App\Modules\Faculty\Models\TrainingType;
use App\Modules\Student\Enums\IdType;
use App\Modules\Student\Enums\StudentStatus;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Models\StudentChangeLog;
use App\Modules\Student\Models\StudentContact;
use App\Modules\System\Models\LookupCategory;
use App\Modules\System\Models\LookupValue;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLogger;
use App\Support\References\ReferenceRegistry;
use App\Support\Services\BaseService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Gate;

/**
 * Hồ sơ sinh viên (FR-STU-001, 004, 005, 013; BR-STU-02, 03, 06, 07, 11).
 *
 * - Tạo hồ sơ ở trạng thái Chờ nhập học. MSSV, email trường, tài khoản cấp khi xác nhận nhập học (#102).
 * - Số giấy tờ và email trường duy nhất, tính cả hồ sơ đã xóa mềm (BR-STU-02). Thông báo trùng nêu hồ sơ đã có chỉ khi
 *   người thao tác được xem hồ sơ đó; nếu không thì chỉ báo "đã có trong hệ thống".
 * - Ngày sinh không ở tương lai, tuổi khi nhập học trong khoảng cấu hình (mặc định 16–60); điện thoại, email đúng định dạng
 *   (BR-STU-03, kiểm tra định dạng ở FormRequest).
 * - Mọi lần sửa ghi lịch sử từng trường (trước – sau, người sửa, thời điểm, lý do). Trường nhạy cảm (họ tên, ngày sinh,
 *   giấy tờ, ngành, chuyên ngành) bắt buộc có lý do (BR-STU-06).
 * - Hồ sơ đã có MSSV, đã qua Chờ nhập học hoặc đã có dữ liệu liên quan thì không xóa, chỉ chuyển trạng thái theo quyết
 *   định (#114); hồ sơ nhập sai chưa phát sinh dữ liệu thì xóa mềm (BR-STU-07).
 */
class StudentService extends BaseService
{
    /** Cột lookup → mã danh mục và nhãn để báo lỗi. */
    private const LOOKUPS = [
        'gender_id' => [LookupCategory::GENDER, 'giới tính'],
        'ethnicity_id' => [LookupCategory::ETHNICITY, 'dân tộc'],
        'religion_id' => [LookupCategory::RELIGION, 'tôn giáo'],
        'nationality_id' => [LookupCategory::NATIONALITY, 'quốc tịch'],
        'priority_group_id' => [LookupCategory::PRIORITY_GROUP, 'đối tượng ưu tiên'],
        'priority_area_id' => [LookupCategory::PRIORITY_AREA, 'khu vực ưu tiên'],
    ];

    /** Tên trường hiển thị trong lịch sử thay đổi. */
    public const FIELD_LABELS = [
        'full_name' => 'Họ và tên',
        'gender_id' => 'Giới tính',
        'date_of_birth' => 'Ngày sinh',
        'place_of_birth' => 'Nơi sinh',
        'ethnicity_id' => 'Dân tộc',
        'religion_id' => 'Tôn giáo',
        'nationality_id' => 'Quốc tịch',
        'id_type' => 'Loại giấy tờ',
        'national_id' => 'Số giấy tờ',
        'national_id_issued_on' => 'Ngày cấp',
        'national_id_issued_by' => 'Nơi cấp',
        'school_email' => 'Email trường',
        'personal_email' => 'Email cá nhân',
        'phone' => 'Điện thoại',
        'permanent_address' => 'Địa chỉ thường trú',
        'permanent_unit_id' => 'Xã / phường thường trú',
        'temporary_address' => 'Địa chỉ tạm trú',
        'temporary_unit_id' => 'Xã / phường tạm trú',
        'major_id' => 'Ngành',
        'specialization_id' => 'Chuyên ngành',
        'training_type_id' => 'Hệ đào tạo',
        'cohort' => 'Khóa',
        'admission_date' => 'Ngày nhập học',
        'priority_group_id' => 'Đối tượng ưu tiên',
        'priority_area_id' => 'Khu vực ưu tiên',
        'note' => 'Ghi chú',
    ];

    public function __construct(
        private readonly AuditLogger $audit,
        private readonly ReferenceRegistry $references,
    ) {}

    /** @param  array<string, mixed>  $data */
    public function create(array $data, ?User $actor = null): Student
    {
        $data = $this->normalize($data);
        $this->assertAcademicPlacement($data);
        $this->assertLookups($data);
        $this->assertAge($data['date_of_birth'], $data['admission_date'] ?? null);
        $this->assertIdentityFree($data['national_id'] ?? null, $actor);
        $this->assertSchoolEmailFree($data['school_email'] ?? null, $actor);

        return $this->transaction(function () use ($data): Student {
            $student = Student::create($data + [
                'id_type' => IdType::NationalId->value,
                'status' => StudentStatus::Pending->value,
                'national_id_hash' => IdentityNumber::hash($data['national_id'] ?? null),
            ]);

            return $student->refresh();
        });
    }

    /**
     * Cán bộ sửa hồ sơ (FR-STU-005). Không đổi MSSV, trạng thái, tài khoản ở đây.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Student $student, array $data, ?string $reason = null, ?User $actor = null): Student
    {
        $data = $this->normalize($data);

        foreach (['student_code', 'status', 'user_id', 'curriculum_id'] as $field) {
            if (array_key_exists($field, $data)) {
                $this->fail(
                    'Không sửa '.match ($field) {
                        'student_code' => 'MSSV',
                        'status' => 'trạng thái',
                        'user_id' => 'tài khoản',
                        'curriculum_id' => 'CTĐT',
                    }.' ở biểu mẫu hồ sơ.',
                    match ($field) {
                        'student_code' => 'MSSV cấp một lần khi xác nhận nhập học và không đổi (BR-STU-01).',
                        'status' => 'Đổi trạng thái theo quyết định ở thao tác chuyển trạng thái (BR-STU-05).',
                        'user_id' => 'Tài khoản được cấp khi xác nhận nhập học.',
                        'curriculum_id' => 'Dùng thao tác gán CTĐT.',
                    },
                );
            }
        }

        $changes = $this->changedFields($student, $data);

        if ($changes === []) {
            return $student;
        }

        $sensitive = array_values(array_intersect(array_keys($changes), Student::SENSITIVE_FIELDS));

        if ($sensitive !== [] && trim((string) $reason) === '') {
            $labels = implode(', ', array_map(fn (string $f) => self::FIELD_LABELS[$f] ?? $f, $sensitive));
            $this->fail("Cần ghi lý do khi sửa trường nhạy cảm: {$labels}.", 'Nhập lý do và số văn bản / minh chứng kèm theo (BR-STU-06).');
        }

        $merged = array_merge($this->current($student), $data);
        $this->assertAcademicPlacement($merged);
        $this->assertLookups(array_intersect_key($data, self::LOOKUPS));

        if (isset($changes['date_of_birth']) || isset($changes['admission_date'])) {
            $this->assertAge($merged['date_of_birth'], $merged['admission_date'] ?? null);
        }

        if (array_key_exists('national_id', $changes)) {
            $this->assertIdentityFree($data['national_id'], $actor, $student);
            $data['national_id_hash'] = IdentityNumber::hash($data['national_id']);
        }

        if (array_key_exists('school_email', $changes)) {
            $this->assertSchoolEmailFree($data['school_email'], $actor, $student);
        }

        return $this->audit->withReason($reason ?: 'Cập nhật hồ sơ sinh viên', function () use ($student, $data, $changes, $reason, $actor): Student {
            return $this->transaction(function () use ($student, $data, $changes, $reason, $actor): Student {
                $student->update(array_intersect_key($data, $changes + ['national_id_hash' => true]));
                $this->logChanges($student, $changes, $reason, $actor);

                return $student->refresh();
            });
        });
    }

    /** Xóa mềm hồ sơ nhập sai chưa phát sinh dữ liệu (BR-STU-07). */
    public function delete(Student $student, ?string $reason = null): void
    {
        $blocking = [];

        if ($student->status !== StudentStatus::Pending) {
            $blocking[] = "đang ở trạng thái {$student->status->label()}";
        }

        if ($student->student_code !== null) {
            $blocking[] = "đã cấp MSSV {$student->student_code}";
        }

        foreach ($this->references->usages($student) as $label => $count) {
            $blocking[] = "{$count} {$label}";
        }

        if ($blocking !== []) {
            $this->fail(
                "Không xóa được hồ sơ của {$student->full_name}: ".implode('; ', $blocking).'.',
                'Hồ sơ đã phát sinh dữ liệu chỉ ngừng bằng quyết định chuyển trạng thái (thôi học, chuyển trường…), không xóa (BR-STU-07).'
            );
        }

        $this->audit->withReason($reason ?: 'Xóa hồ sơ nhập sai', fn () => $student->delete());
    }

    /**
     * Xem đầy đủ trường nhạy cảm (số giấy tờ, địa chỉ); mỗi lần xem ghi nhật ký (BR-STU-11).
     *
     * @return array<string, mixed>
     */
    public function reveal(Student $student): array
    {
        $this->audit->record(AuditEvent::ViewSensitive, $student, reason: 'Xem đầy đủ hồ sơ sinh viên (giấy tờ, địa chỉ)');

        return [
            'id_type' => $student->id_type->value,
            'national_id' => $student->national_id,
            'national_id_issued_on' => $student->national_id_issued_on?->toDateString(),
            'national_id_issued_by' => $student->national_id_issued_by,
            'permanent_address' => $student->permanent_address,
            'temporary_address' => $student->temporary_address,
        ];
    }

    // ---------------------------------------------------------------------
    // Người thân, liên hệ khẩn cấp (FR-STU-013)
    // ---------------------------------------------------------------------

    /** @param  array<string, mixed>  $data */
    public function addContact(Student $student, array $data): StudentContact
    {
        $this->assertContactReachable($data);

        return $student->contacts()->create($data)->refresh();
    }

    /** @param  array<string, mixed>  $data */
    public function updateContact(StudentContact $contact, array $data): StudentContact
    {
        $this->assertContactReachable(array_merge($contact->only('phone', 'email'), $data));
        $contact->update($data);

        return $contact->refresh();
    }

    public function removeContact(StudentContact $contact): void
    {
        $contact->delete();
    }

    // ---------------------------------------------------------------------

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalize(array $data): array
    {
        if (array_key_exists('national_id', $data)) {
            $data['national_id'] = IdentityNumber::normalize($data['national_id']);
        }

        foreach (['school_email', 'personal_email'] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = mb_strtolower(trim($data[$field]));
            }
        }

        foreach (['date_of_birth', 'admission_date', 'national_id_issued_on'] as $field) {
            if (! empty($data[$field])) {
                $data[$field] = CarbonImmutable::parse($data[$field])->toDateString();
            }
        }

        return $data;
    }

    /** @return array<string, mixed> */
    private function current(Student $student): array
    {
        $values = [];

        foreach (array_keys(self::FIELD_LABELS) as $field) {
            $value = $student->getAttribute($field);
            $values[$field] = match (true) {
                $value instanceof \DateTimeInterface => $value->format('Y-m-d'),
                $value instanceof \BackedEnum => $value->value,
                default => $value,
            };
        }

        return $values;
    }

    /**
     * Các trường thực sự thay đổi: tên trường => [trước, sau].
     *
     * @param  array<string, mixed>  $data
     * @return array<string, array{0: mixed, 1: mixed}>
     */
    private function changedFields(Student $student, array $data): array
    {
        $current = $this->current($student);
        $changes = [];

        foreach ($data as $field => $value) {
            if (! array_key_exists($field, self::FIELD_LABELS)) {
                continue;
            }

            $old = $current[$field] ?? null;

            if ((string) $old !== (string) $value) {
                $changes[$field] = [$old, $value];
            }
        }

        return $changes;
    }

    /** @param  array<string, array{0: mixed, 1: mixed}>  $changes */
    private function logChanges(Student $student, array $changes, ?string $reason, ?User $actor): void
    {
        $now = now();

        foreach ($changes as $field => [$old, $new]) {
            $sensitive = in_array($field, Student::SENSITIVE_FIELDS, true);

            if ($field === 'national_id') {
                [$old, $new] = [IdentityNumber::mask($old), IdentityNumber::mask($new)];
            }

            if (in_array($field, ['permanent_address', 'temporary_address'], true)) {
                [$old, $new] = [IdentityNumber::maskAddress($old), IdentityNumber::maskAddress($new)];
            }

            StudentChangeLog::create([
                'student_id' => $student->id,
                'field' => $field,
                'old_value' => $old === null ? null : (string) $old,
                'new_value' => $new === null ? null : (string) $new,
                'sensitive' => $sensitive,
                'reason' => $reason,
                'changed_by' => $actor?->id ?? auth()->id(),
                'changed_at' => $now,
            ]);
        }
    }

    /** Ngành đang hoạt động, chuyên ngành thuộc ngành, hệ đào tạo đang hoạt động, khóa hợp lệ. */
    private function assertAcademicPlacement(array $data): void
    {
        $major = Major::query()->findOrFail($data['major_id']);

        if (! $major->isActive()) {
            $this->fail("Ngành {$major->code} đang ngừng tuyển sinh / ngừng hoạt động.", 'Chọn ngành đang hoạt động.');
        }

        if (! empty($data['specialization_id'])) {
            $specialization = Specialization::query()->findOrFail($data['specialization_id']);

            if ($specialization->major_id !== $major->id) {
                $this->fail("Chuyên ngành {$specialization->code} không thuộc ngành {$major->code}.", 'Chọn chuyên ngành của đúng ngành, hoặc để trống.');
            }

            if (! $specialization->isActive()) {
                $this->fail("Chuyên ngành {$specialization->code} đang ngừng hoạt động.", 'Chọn chuyên ngành khác hoặc để trống.');
            }
        }

        $type = TrainingType::query()->findOrFail($data['training_type_id']);

        if (! $type->isActive()) {
            $this->fail("Hệ đào tạo {$type->code} đang ngừng hoạt động.", 'Chọn hệ đào tạo đang hoạt động.');
        }
    }

    /** Mỗi mã lookup phải thuộc đúng danh mục và đang sử dụng. */
    private function assertLookups(array $data): void
    {
        foreach (self::LOOKUPS as $field => [$category, $label]) {
            if (empty($data[$field])) {
                continue;
            }

            $value = LookupValue::query()->with('category')->find($data[$field]);

            if ($value === null || $value->category?->code !== $category) {
                $this->fail("Giá trị {$label} không hợp lệ.", "Chọn {$label} trong danh mục dùng chung.");
            }

            if (! $value->isActive()) {
                $this->fail("Giá trị {$label} \"{$value->name}\" đã ngừng sử dụng.", "Chọn {$label} khác đang sử dụng.");
            }
        }
    }

    /** Ngày sinh không ở tương lai; tuổi tại ngày nhập học (hoặc hôm nay) trong khoảng cấu hình (BR-STU-03). */
    private function assertAge(string $dateOfBirth, ?string $admissionDate): void
    {
        $birth = CarbonImmutable::parse($dateOfBirth);
        $reference = CarbonImmutable::parse($admissionDate ?? now(config('studentmanager.display_timezone'))->toDateString());

        if ($birth->isAfter(now())) {
            $this->fail('Ngày sinh ở tương lai.', 'Kiểm tra lại ngày sinh theo giấy tờ tùy thân.');
        }

        $age = (int) floor($birth->diffInYears($reference));
        $min = (int) config('studentmanager.student.min_age', 16);
        $max = (int) config('studentmanager.student.max_age', 60);

        if ($age < $min || $age > $max) {
            $this->fail(
                "Tuổi khi nhập học là {$age}, ngoài khoảng cho phép {$min}–{$max}.",
                'Kiểm tra lại ngày sinh và ngày nhập học; khoảng tuổi đổi ở tham số hệ thống (studentmanager.student).'
            );
        }
    }

    private function assertIdentityFree(?string $nationalId, ?User $actor, ?Student $except = null): void
    {
        $hash = IdentityNumber::hash($nationalId);

        if ($hash === null) {
            return;
        }

        $existing = Student::withTrashed()
            ->where('national_id_hash', $hash)
            ->when($except !== null, fn ($q) => $q->whereKeyNot($except->id))
            ->first();

        if ($existing !== null) {
            $this->fail(
                'Số giấy tờ này đã có trong hồ sơ '.$this->describeFor($existing, $actor).'.',
                'Mỗi người chỉ có một hồ sơ sinh viên (BR-STU-02). Kiểm tra lại số giấy tờ, hoặc mở hồ sơ đã có thay vì tạo mới.'
            );
        }
    }

    private function assertSchoolEmailFree(?string $email, ?User $actor, ?Student $except = null): void
    {
        if (empty($email)) {
            return;
        }

        $existing = Student::withTrashed()
            ->where('school_email', $email)
            ->when($except !== null, fn ($q) => $q->whereKeyNot($except->id))
            ->first();

        if ($existing !== null) {
            $this->fail(
                "Email trường {$email} đã được dùng cho hồ sơ ".$this->describeFor($existing, $actor).'.',
                'Email trường là duy nhất (BR-STU-02); chọn email khác hoặc để hệ thống cấp khi xác nhận nhập học.'
            );
        }
    }

    /** Mô tả hồ sơ trùng theo quyền của người xem (BR-STU-02): chỉ nêu tên, MSSV khi người đó được xem hồ sơ. */
    private function describeFor(Student $existing, ?User $actor): string
    {
        if ($actor !== null && ! $existing->trashed() && Gate::forUser($actor)->allows('view', $existing)) {
            return "sinh viên {$existing->full_name}".($existing->student_code ? " (MSSV {$existing->student_code})" : " (mã hồ sơ {$existing->id})");
        }

        return 'khác đã có trong hệ thống';
    }

    private function assertContactReachable(array $data): void
    {
        if (empty($data['phone']) && empty($data['email'])) {
            $this->fail('Người liên hệ cần có số điện thoại hoặc email.', 'Nhập ít nhất một cách liên lạc.');
        }
    }
}
