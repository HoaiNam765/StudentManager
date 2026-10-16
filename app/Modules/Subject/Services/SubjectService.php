<?php

namespace App\Modules\Subject\Services;

use App\Modules\Faculty\Models\Department;
use App\Modules\Subject\Models\Subject;
use App\Support\References\ReferenceRegistry;
use App\Support\Services\BaseService;

/**
 * Danh mục học phần (FR-SUB-001, 002, 003; BR-SUB-01, 02, 05, 06; GC-05).
 *
 * - Mã học phần duy nhất, không cấp lại (kể cả đã xóa) và không đổi khi đã có dữ liệu liên quan
 *   (lớp học phần, điểm, CTĐT… do các module đó đăng ký với ReferenceRegistry) (BR-SUB-01).
 * - Tín chỉ là số nguyên không âm; lý thuyết + thực hành = tổng; chỉ học phần điều kiện (không tính GPA)
 *   mới được 0 tín chỉ (BR-SUB-02). Số tiết bỏ trống thì tính theo định mức trong cấu hình.
 * - Học phần đã có dữ liệu liên quan: không xóa, chỉ ngừng; không đổi tín chỉ hay cách tính GPA
 *   (phải tạo phiên bản mới, FR-SUB-007) (BR-SUB-05).
 * - Học phần do một bộ môn đang hoạt động quản lý.
 */
class SubjectService extends BaseService
{
    /** Trường quyết định cách tính điểm, GPA: không đổi khi học phần đã có dữ liệu (BR-SUB-05). */
    public const LOCKED_WHEN_USED = ['credits', 'theory_credits', 'practice_credits', 'counts_toward_gpa'];

    private const LABELS = [
        'credits' => 'tổng tín chỉ',
        'theory_credits' => 'tín chỉ lý thuyết',
        'practice_credits' => 'tín chỉ thực hành',
        'counts_toward_gpa' => 'cờ tính vào GPA',
    ];

    public function __construct(private readonly ReferenceRegistry $references) {}

    /** @param  array<string, mixed>  $data */
    public function create(array $data): Subject
    {
        $this->assertDepartmentActive(Department::query()->findOrFail($data['department_id']));
        $this->assertCodeFree($data['code']);

        // Gán sẵn mặc định để model trả về có đủ giá trị (không đọc lại mặc định của CSDL)
        $data += ['teaching_language' => 'vi', 'requirement' => 'compulsory', 'counts_toward_gpa' => true, 'is_graduation_requirement' => false];

        return Subject::create($this->normalizeCredits($data, fillPeriods: true));
    }

    /**
     * Sửa học phần; `status` = active / inactive để kích hoạt lại hoặc ngừng giảng dạy.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Subject $subject, array $data): Subject
    {
        $status = $data['status'] ?? null;
        unset($data['status']);

        if (isset($data['code']) && $data['code'] !== $subject->code) {
            $this->assertNotUsed($subject, "Không đổi được mã học phần {$subject->code}", 'Đổi tên thì được; mã giữ nguyên vì lớp học phần, điểm và CTĐT đang tham chiếu mã này (BR-SUB-01).');
            $this->assertCodeFree($data['code']);
        }

        $changedLocked = array_keys(array_filter(
            array_intersect_key($data, array_flip(self::LOCKED_WHEN_USED)),
            fn ($value, string $field) => $value != $subject->{$field},
            ARRAY_FILTER_USE_BOTH,
        ));

        if ($changedLocked !== []) {
            $fields = implode(', ', array_map(fn (string $field) => self::LABELS[$field], $changedLocked));

            $this->assertNotUsed(
                $subject,
                "Không đổi được {$fields} của học phần {$subject->code}",
                'Kết quả đã phát sinh phải giữ nguyên; thay đổi tín chỉ hoặc cách tính GPA áp dụng qua phiên bản học phần mới từ học kỳ chưa có dữ liệu (BR-SUB-05).'
            );
        }

        if (isset($data['department_id']) && (int) $data['department_id'] !== $subject->department_id) {
            $this->assertDepartmentActive(Department::query()->findOrFail($data['department_id']));
        }

        $current = $subject->only(['credits', 'theory_credits', 'practice_credits', 'counts_toward_gpa', 'theory_periods', 'practice_periods']);

        if (array_intersect_key($data, array_flip(['credits', 'theory_credits', 'practice_credits', 'counts_toward_gpa'])) !== []) {
            $merged = $data + $current;

            // Phần không gửi kèm thì tính lại từ tổng: chỉ đổi tổng thì giữ phần thực hành, tính lại lý thuyết;
            // chỉ gửi một phần thì phần kia là phần còn lại
            $sentTheory = array_key_exists('theory_credits', $data);
            $sentPractice = array_key_exists('practice_credits', $data);

            if ($sentTheory xor $sentPractice) {
                unset($merged[$sentTheory ? 'practice_credits' : 'theory_credits']);
            } elseif (! $sentTheory && array_key_exists('credits', $data)) {
                unset($merged['theory_credits']);
            }

            $normalized = $this->normalizeCredits($merged, fillPeriods: false);
            $data = array_merge($data, array_intersect_key($normalized, array_flip(['credits', 'theory_credits', 'practice_credits'])));
        }

        $this->assertPeriodsMatchCredits(array_merge($current, $data));

        $this->transaction(function () use ($subject, $data, $status): void {
            $subject->update($data);

            match ($status) {
                'inactive' => $this->deactivate($subject),
                'active' => $this->activate($subject),
                default => null,
            };
        });

        return $subject->refresh();
    }

    /** Xóa (mềm) học phần chưa có dữ liệu liên quan; đã có thì chỉ ngừng (BR-SUB-05, GC-05). */
    public function delete(Subject $subject): void
    {
        $this->assertNotUsed($subject, "Không xóa được học phần {$subject->code}", 'Hãy ngừng giảng dạy học phần thay cho việc xóa; dữ liệu lịch sử vẫn hiển thị đúng (BR-SUB-05).');

        $subject->delete();
    }

    // ---------------------------------------------------------------------

    /**
     * Chuẩn hóa và kiểm tra tín chỉ, số tiết (BR-SUB-02, FR-SUB-002).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizeCredits(array $data, bool $fillPeriods): array
    {
        $credits = (int) $data['credits'];
        $max = (int) config('studentmanager.subject.max_credits');

        if ($credits < 0 || $credits > $max) {
            $this->fail("Tổng tín chỉ phải từ 0 đến {$max}.", 'Nhập số nguyên trong khoảng cho phép; giới hạn trên đặt ở cấu hình SUBJECT_MAX_CREDITS.');
        }

        $theory = $data['theory_credits'] ?? null;
        $practice = $data['practice_credits'] ?? null;

        // Bỏ trống một phần thì phần đó là phần còn lại; bỏ trống cả hai thì toàn bộ là lý thuyết
        $theory = $theory === null ? ($practice === null ? $credits : $credits - (int) $practice) : (int) $theory;
        $practice = $practice === null ? $credits - $theory : (int) $practice;

        if ($theory < 0 || $practice < 0 || $theory + $practice !== $credits) {
            $this->fail(
                "Tín chỉ lý thuyết ({$theory}) cộng tín chỉ thực hành ({$practice}) phải bằng tổng tín chỉ ({$credits}).",
                'Sửa lại số tín chỉ lý thuyết hoặc thực hành cho khớp với tổng (BR-SUB-02).'
            );
        }

        if ($credits === 0 && ($data['counts_toward_gpa'] ?? true)) {
            $this->fail(
                'Học phần 0 tín chỉ chỉ dành cho học phần điều kiện, không tính vào GPA.',
                'Nhập số tín chỉ lớn hơn 0, hoặc bỏ cờ "Tính vào GPA" nếu đây là học phần điều kiện (BR-SUB-02).'
            );
        }

        $data['credits'] = $credits;
        $data['theory_credits'] = $theory;
        $data['practice_credits'] = $practice;

        if ($fillPeriods) {
            $perCredit = config('studentmanager.subject.periods_per_credit');
            $data['theory_periods'] ??= $theory * (int) $perCredit['theory'];
            $data['practice_periods'] ??= $practice * (int) $perCredit['practice'];
            $data['self_study_hours'] ??= $credits * (int) config('studentmanager.subject.self_study_hours_per_credit');
        }

        $this->assertPeriodsMatchCredits($data);

        return $data;
    }

    /**
     * Không có tiết lý thuyết (thực hành) khi phần đó không có tín chỉ. Học phần điều kiện 0 tín chỉ
     * (ví dụ giáo dục quốc phòng tính theo tiết) không bị ràng buộc.
     *
     * @param  array<string, mixed>  $data
     */
    private function assertPeriodsMatchCredits(array $data): void
    {
        if ((int) ($data['credits'] ?? 0) === 0) {
            return;
        }

        foreach (['theory' => 'lý thuyết', 'practice' => 'thực hành'] as $part => $label) {
            if ((int) ($data["{$part}_credits"] ?? 0) === 0 && (int) ($data["{$part}_periods"] ?? 0) > 0) {
                $this->fail(
                    "Học phần không có tín chỉ {$label} nhưng lại có {$data["{$part}_periods"]} tiết {$label}.",
                    "Đặt số tiết {$label} bằng 0, hoặc chia lại tín chỉ cho phần {$label} (FR-SUB-002)."
                );
            }
        }
    }

    private function deactivate(Subject $subject): void
    {
        $usages = $this->references->activeUsages($subject);

        if ($usages !== []) {
            $this->fail(
                "Không thể ngừng học phần {$subject->code} vì còn ".ReferenceRegistry::describe($usages).'.',
                'Kết thúc hoặc chuyển các dữ liệu này trước, rồi ngừng học phần.'
            );
        }

        $subject->deactivate();
    }

    private function activate(Subject $subject): void
    {
        $this->assertDepartmentActive($subject->department()->firstOrFail());
        $subject->activate();
    }

    private function assertNotUsed(Subject $subject, string $what, string $hint): void
    {
        $usages = $this->references->usages($subject);

        if ($usages !== []) {
            $this->fail("{$what} vì đã có dữ liệu liên quan (".ReferenceRegistry::describe($usages).').', $hint);
        }
    }

    private function assertDepartmentActive(Department $department): void
    {
        if (! $department->isActive()) {
            $this->fail(
                "Bộ môn {$department->code} đang ngừng hoạt động nên không quản lý thêm học phần được.",
                'Chọn bộ môn đang hoạt động hoặc kích hoạt lại bộ môn này.'
            );
        }
    }

    private function assertCodeFree(string $code): void
    {
        if (Subject::withTrashed()->where('code', $code)->exists()) {
            $this->fail("Mã học phần {$code} đã được dùng.", 'Mã học phần không được cấp lại, kể cả khi học phần cũ đã xóa; hãy chọn mã khác (BR-SUB-01).');
        }
    }
}
