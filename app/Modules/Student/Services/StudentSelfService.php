<?php

namespace App\Modules\Student\Services;

use App\Models\User;
use App\Modules\Faculty\Models\Major;
use App\Modules\Student\Events\StudentChangeRequestDecided;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Models\StudentChangeRequest;
use App\Support\Services\BaseService;
use Carbon\CarbonImmutable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Sinh viên tự cập nhật hồ sơ (FR-STU-006; BR-STU-06; EV-STU-03).
 *
 * - Lưu ngay (qua StudentService::update, có lịch sử trước – sau): điện thoại, email cá nhân, địa chỉ thường trú / tạm
 *   trú, ảnh, người liên hệ khẩn cấp.
 * - Họ tên, ngày sinh, số CCCD, ngành: chỉ qua yêu cầu kèm minh chứng; cán bộ khác (không phải người gửi) duyệt hoặc từ
 *   chối có lý do. Duyệt thì áp dụng bằng StudentService::update (đủ kiểm tra trùng CCCD, tuổi, ngành) và ghi lịch sử;
 *   phát `StudentChangeRequestDecided` (EV-STU-03). Mỗi sinh viên tối đa một yêu cầu đang chờ; người gửi hủy được.
 */
class StudentSelfService extends BaseService
{
    public const SELF_FIELDS = ['phone', 'personal_email', 'permanent_address', 'permanent_unit_id', 'temporary_address', 'temporary_unit_id'];

    public const REQUEST_FIELDS = ['full_name', 'date_of_birth', 'national_id', 'major_id'];

    public function __construct(private readonly StudentService $students) {}

    /** @param array<string, mixed> $data */
    public function updateSelf(Student $student, array $data, User $actor): Student
    {
        $sensitive = array_intersect(array_keys($data), [...Student::SENSITIVE_FIELDS, 'major_id']);

        if ($sensitive !== []) {
            $this->fail('Họ tên, ngày sinh, số CCCD, ngành không sửa trực tiếp được.', 'Gửi yêu cầu chỉnh sửa kèm minh chứng để Phòng Công tác sinh viên duyệt (BR-STU-06).');
        }

        return $this->students->update($student, array_intersect_key($data, array_flip(self::SELF_FIELDS)), 'Sinh viên tự cập nhật', $actor);
    }

    public function updatePhoto(Student $student, UploadedFile $photo): Student
    {
        $old = $student->photo_path;
        $path = $photo->storeAs('students', ($student->student_code ?? "hs{$student->id}").'-'.now()->format('YmdHis').'.'.$photo->extension(), 'public');
        $student->update(['photo_path' => $path]);

        if ($old !== null) {
            Storage::disk('public')->delete($old);
        }

        return $student->refresh();
    }

    /** @param array<string, mixed> $values giá trị đề nghị (chỉ các trường REQUEST_FIELDS) */
    public function request(Student $student, array $values, string $reason, UploadedFile $evidence, User $actor): StudentChangeRequest
    {
        if (StudentChangeRequest::query()->where('student_id', $student->id)->where('status', StudentChangeRequest::PENDING)->exists()) {
            $this->fail('Bạn đã có một yêu cầu chỉnh sửa đang chờ duyệt.', 'Chờ yêu cầu đó được xử lý, hoặc hủy nó rồi gửi lại.');
        }

        $changes = [];

        foreach (array_intersect_key($values, array_flip(self::REQUEST_FIELDS)) as $field => $new) {
            $new = $field === 'national_id' ? IdentityNumber::normalize($new) : (is_string($new) ? trim($new) : $new);
            $old = match ($field) {
                'date_of_birth' => $student->date_of_birth?->toDateString(),
                default => $student->{$field},
            };

            if ($new !== null && $new !== '' && (string) $new !== (string) $old) {
                $changes[$field] = ['old' => $old, 'new' => $new];
            }
        }

        if ($changes === []) {
            $this->fail('Không có giá trị nào khác với hồ sơ hiện tại.', 'Nhập giá trị mới cho ít nhất một trường: họ tên, ngày sinh, số CCCD, ngành.');
        }

        if (isset($changes['date_of_birth']) && CarbonImmutable::parse($changes['date_of_birth']['new'])->isFuture()) {
            $this->fail('Ngày sinh ở tương lai.', 'Kiểm tra lại ngày sinh theo giấy tờ.');
        }

        if (isset($changes['major_id']) && ! Major::query()->whereKey($changes['major_id']['new'])->exists()) {
            $this->fail('Ngành đề nghị không tồn tại.', 'Chọn ngành trong danh mục.');
        }

        $path = $evidence->store("student-change-requests/{$student->id}", config('studentmanager.student.decision_disk', 'local'));

        return StudentChangeRequest::create([
            'student_id' => $student->id,
            'requested_by' => $actor->id,
            'changes' => $changes,
            'reason' => trim($reason),
            'evidence_path' => $path,
            'evidence_name' => $evidence->getClientOriginalName(),
            'status' => StudentChangeRequest::PENDING,
        ]);
    }

    public function approve(StudentChangeRequest $request, User $approver, ?string $note = null): StudentChangeRequest
    {
        $this->assertDecidable($request, $approver);

        return $this->transaction(function () use ($request, $approver, $note): StudentChangeRequest {
            $new = array_map(fn (array $c) => $c['new'], $request->changes);
            $reason = "Duyệt yêu cầu chỉnh sửa #{$request->id}: {$request->reason}".($note ? " ({$note})" : '');

            // StudentService kiểm tra trùng CCCD, tuổi, ngành và ghi lịch sử trước – sau (BR-STU-06)
            $this->students->update($request->student, $new, $reason, $approver);

            $request->update(['status' => StudentChangeRequest::APPROVED, 'decided_by' => $approver->id, 'decided_at' => now(), 'decision_note' => $note]);
            StudentChangeRequestDecided::dispatch($request);

            return $request->refresh();
        });
    }

    public function reject(StudentChangeRequest $request, User $approver, string $note): StudentChangeRequest
    {
        $this->assertDecidable($request, $approver);
        $request->update(['status' => StudentChangeRequest::REJECTED, 'decided_by' => $approver->id, 'decided_at' => now(), 'decision_note' => $note]);
        StudentChangeRequestDecided::dispatch($request);

        return $request->refresh();
    }

    public function cancel(StudentChangeRequest $request, User $actor): StudentChangeRequest
    {
        if ($request->status !== StudentChangeRequest::PENDING) {
            $this->fail('Yêu cầu này đã được xử lý.', 'Chỉ hủy được yêu cầu đang chờ duyệt.');
        }

        if ($request->requested_by !== $actor->id) {
            $this->fail('Chỉ người gửi yêu cầu mới hủy được.', 'Cán bộ xử lý dùng thao tác từ chối kèm lý do.');
        }

        $request->update(['status' => StudentChangeRequest::CANCELLED]);

        return $request->refresh();
    }

    private function assertDecidable(StudentChangeRequest $request, User $approver): void
    {
        if ($request->status !== StudentChangeRequest::PENDING) {
            $this->fail('Yêu cầu này đã được xử lý.', 'Tải lại danh sách để xem trạng thái mới nhất.');
        }

        if ($request->requested_by === $approver->id || $request->student->user_id === $approver->id) {
            $this->fail('Không tự duyệt yêu cầu chỉnh sửa hồ sơ của chính mình.', 'Yêu cầu phải do một cán bộ khác duyệt (BR-STU-06).');
        }
    }
}
