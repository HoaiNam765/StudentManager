<?php

namespace App\Modules\Teacher\Services;

use App\Models\User;
use App\Modules\Teacher\Models\Lecturer;
use App\Modules\Teacher\Models\LecturerChangeRequest;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLogger;
use App\Support\Services\BaseService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Giảng viên tự cập nhật hồ sơ (BR-TCH-07).
 *
 * - Tự sửa ngay: thông tin liên hệ (điện thoại, email) và ảnh.
 * - Họ tên, chức danh, học vị: tạo đề nghị, chờ cán bộ có thẩm quyền (khác người đề nghị) xác nhận rồi mới áp dụng.
 *   Mỗi hồ sơ có tối đa một đề nghị đang chờ.
 */
class LecturerProfileService extends BaseService
{
    /** Trường giảng viên tự sửa được. */
    public const SELF_EDITABLE = ['phone', 'email'];

    /** Trường cần cán bộ xác nhận. */
    public const NEEDS_APPROVAL = ['full_name', 'academic_title', 'academic_degree'];

    public function __construct(
        private readonly AuditLogger $audit,
        private readonly LecturerService $lecturers,
    ) {}

    /**
     * Lưu ngay các trường tự sửa được; các trường cần xác nhận (nếu khác giá trị hiện tại) gộp thành một đề nghị.
     *
     * @param  array<string, mixed>  $data
     * @return array{lecturer: Lecturer, request: ?LecturerChangeRequest}
     */
    public function update(Lecturer $lecturer, array $data, User $actor, ?string $reason = null): array
    {
        $forbidden = array_diff(array_keys($data), [...self::SELF_EDITABLE, ...self::NEEDS_APPROVAL]);

        if ($forbidden !== []) {
            $this->fail(
                'Giảng viên không tự sửa được: '.implode(', ', $forbidden).'.',
                'Liên hệ phòng Đào tạo hoặc lãnh đạo đơn vị để cập nhật các thông tin này (BR-TCH-07).'
            );
        }

        $changes = [];

        foreach (array_intersect_key($data, array_flip(self::NEEDS_APPROVAL)) as $field => $value) {
            $old = $this->plain($lecturer->{$field});

            if ($old !== $this->plain($value)) {
                $changes[$field] = ['old' => $old, 'new' => $this->plain($value)];
            }
        }

        if ($changes !== [] && $lecturer->changeRequests()->where('status', LecturerChangeRequest::PENDING)->exists()) {
            $this->fail('Bạn đã có một đề nghị thay đổi thông tin đang chờ xác nhận.', 'Chờ đề nghị đó được xử lý, hoặc hủy đề nghị đó rồi gửi lại.');
        }

        return $this->transaction(function () use ($lecturer, $data, $actor, $changes, $reason): array {
            $direct = array_intersect_key($data, array_flip(self::SELF_EDITABLE));

            if ($direct !== []) {
                $lecturer->update($direct);
            }

            $request = $changes === [] ? null : LecturerChangeRequest::create([
                'lecturer_id' => $lecturer->id,
                'requested_by' => $actor->id,
                'changes' => $changes,
                'reason' => $reason,
                'status' => LecturerChangeRequest::PENDING,
            ]);

            return ['lecturer' => $lecturer->refresh(), 'request' => $request];
        });
    }

    /** Ảnh hồ sơ (lưu ở disk public, thay ảnh cũ). */
    public function updatePhoto(Lecturer $lecturer, UploadedFile $photo): Lecturer
    {
        $old = $lecturer->photo_path;
        $path = $photo->storeAs('lecturers', "{$lecturer->code}-".now()->format('YmdHis').'.'.$photo->extension(), 'public');

        $lecturer->update(['photo_path' => $path]);

        if ($old !== null && $old !== $path) {
            Storage::disk('public')->delete($old);
        }

        return $lecturer->refresh();
    }

    public function approve(LecturerChangeRequest $request, User $approver, ?string $note = null): LecturerChangeRequest
    {
        $this->assertDecidable($request, $approver);
        $lecturer = $request->lecturer;

        $this->transaction(function () use ($request, $approver, $note, $lecturer): void {
            $values = array_map(fn (array $change) => $change['new'], $request->changes);
            $reason = "Xác nhận đề nghị thay đổi thông tin #{$request->id}. Người đề nghị: {$request->requester->name}. Người xác nhận: {$approver->name}.";

            $this->audit->withReason($reason, function () use ($lecturer, $values): void {
                $lecturer->update($values);
                $this->lecturers->syncAccountName($lecturer);
            });

            $request->update([
                'status' => LecturerChangeRequest::APPROVED,
                'decided_by' => $approver->id,
                'decided_at' => now(),
                'decision_note' => $note,
            ]);

            $this->audit->record(AuditEvent::Approved, $request, reason: $reason);
        });

        return $request->refresh();
    }

    public function reject(LecturerChangeRequest $request, User $approver, string $note): LecturerChangeRequest
    {
        $this->assertDecidable($request, $approver);

        $request->update([
            'status' => LecturerChangeRequest::REJECTED,
            'decided_by' => $approver->id,
            'decided_at' => now(),
            'decision_note' => $note,
        ]);

        $this->audit->record(AuditEvent::Rejected, $request, reason: $note);

        return $request->refresh();
    }

    /** Người đề nghị tự hủy đề nghị đang chờ. */
    public function cancel(LecturerChangeRequest $request, User $actor): LecturerChangeRequest
    {
        if (! $request->isPending()) {
            $this->fail('Đề nghị này đã được xử lý.', 'Chỉ hủy được đề nghị đang chờ xác nhận.');
        }

        if ($request->requested_by !== $actor->id) {
            $this->fail('Chỉ người gửi đề nghị mới hủy được.', 'Cán bộ xử lý dùng thao tác từ chối kèm lý do.');
        }

        $request->update(['status' => LecturerChangeRequest::CANCELLED, 'decided_at' => now()]);

        return $request->refresh();
    }

    private function assertDecidable(LecturerChangeRequest $request, User $approver): void
    {
        if (! $request->isPending()) {
            $this->fail('Đề nghị này đã được xử lý.', 'Tải lại danh sách để xem trạng thái mới nhất.');
        }

        if ($request->requested_by === $approver->id || $request->lecturer->user_id === $approver->id) {
            $this->fail('Không tự xác nhận thay đổi thông tin của chính mình.', 'Đề nghị phải do một cán bộ có thẩm quyền khác xác nhận (BR-TCH-07).');
        }
    }

    private function plain(mixed $value): mixed
    {
        return $value instanceof \BackedEnum ? $value->value : $value;
    }
}
