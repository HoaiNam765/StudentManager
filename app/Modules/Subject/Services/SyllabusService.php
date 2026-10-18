<?php

namespace App\Modules\Subject\Services;

use App\Models\User;
use App\Modules\Subject\Enums\SyllabusStatus;
use App\Modules\Subject\Models\Subject;
use App\Modules\Subject\Models\Syllabus;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLogger;
use App\Support\Services\BaseService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Đề cương chi tiết học phần (FR-SUB-006; BR-SUB-08; NFR-SEC-07).
 *
 * - Mỗi học phần tối đa một phiên bản đang soạn (nháp, bị trả lại hoặc chờ duyệt); phiên bản mới chép nội dung bản
 *   gần nhất để sửa tiếp. Đã gửi duyệt thì không sửa; bị trả lại thì sửa rồi gửi lại.
 * - Duyệt / trả lại do người khác người soạn; duyệt bản mới thì bản đã duyệt trước chuyển "Đã thay thế".
 * - Tệp PDF: kiểm tra nội dung thật là PDF (chữ ký %PDF), dung lượng theo cấu hình, tên lưu ngẫu nhiên trên disk riêng tư.
 * - CLS gọi `applicableFor($subject)` khi mở lớp mới: khi bật `syllabus_requires_approval` (mặc định) chỉ bản đã duyệt
 *   mới áp dụng được (BR-SUB-08).
 */
class SyllabusService extends BaseService
{
    private const FIELDS = ['objectives', 'learning_outcomes', 'content', 'teaching_methods', 'assessment', 'materials'];

    public function __construct(private readonly AuditLogger $audit) {}

    /** Tạo phiên bản mới (chép nội dung bản gần nhất nếu có). */
    public function createVersion(Subject $subject): Syllabus
    {
        $open = $this->openVersion($subject);

        if ($open !== null) {
            $this->fail(
                "Học phần {$subject->code} đang có đề cương phiên bản {$open->version} ({$open->status->label()}).",
                'Sửa tiếp phiên bản đó, hoặc chờ phiên bản đó được duyệt / trả lại rồi mới tạo phiên bản mới.'
            );
        }

        $latest = Syllabus::query()->where('subject_id', $subject->id)->orderByDesc('version')->first();

        return Syllabus::create(array_merge(
            $latest?->only(self::FIELDS) ?? [],
            ['subject_id' => $subject->id, 'version' => ($latest?->version ?? 0) + 1, 'status' => SyllabusStatus::Draft],
        ));
    }

    /** @param  array<string, mixed>  $data */
    public function update(Syllabus $syllabus, array $data): Syllabus
    {
        $this->assertEditable($syllabus);
        $syllabus->update(array_intersect_key($data, array_flip(self::FIELDS)) + ($syllabus->status === SyllabusStatus::Rejected ? ['status' => SyllabusStatus::Draft] : []));

        return $syllabus->refresh();
    }

    /** Đính kèm tệp PDF (thay tệp cũ). */
    public function attachFile(Syllabus $syllabus, UploadedFile $file): Syllabus
    {
        $this->assertEditable($syllabus);

        $handle = fopen($file->getRealPath(), 'rb');
        $signature = $handle === false ? '' : (string) fread($handle, 5);

        if ($handle !== false) {
            fclose($handle);
        }

        if ($signature !== '%PDF-') {
            $this->fail('Tệp tải lên không phải tệp PDF hợp lệ.', 'Xuất đề cương ra PDF rồi tải lại; không đổi đuôi tệp khác thành .pdf (NFR-SEC-07).');
        }

        $disk = config('studentmanager.subject.syllabus_disk');
        $old = $syllabus->file_path;
        $path = $file->storeAs("syllabi/{$syllabus->subject_id}", Str::uuid()->toString().'.pdf', $disk);

        $syllabus->update([
            'file_path' => $path,
            'file_name' => Str::limit(basename($file->getClientOriginalName()), 200, ''),
            'file_size' => $file->getSize(),
        ]);

        if ($old !== null) {
            Storage::disk($disk)->delete($old);
        }

        return $syllabus->refresh();
    }

    public function submit(Syllabus $syllabus): Syllabus
    {
        $this->assertEditable($syllabus);

        if (empty($syllabus->objectives) || empty($syllabus->learning_outcomes) || empty($syllabus->content)) {
            $this->fail('Đề cương còn thiếu mục tiêu, chuẩn đầu ra hoặc nội dung giảng dạy.', 'Bổ sung đủ các mục trên trước khi gửi duyệt.');
        }

        $syllabus->update(['status' => SyllabusStatus::Submitted, 'submitted_at' => now()]);

        return $syllabus->refresh();
    }

    public function approve(Syllabus $syllabus, User $approver, ?string $note = null): Syllabus
    {
        $this->assertDecidable($syllabus, $approver);

        $this->transaction(function () use ($syllabus, $approver, $note): void {
            Syllabus::query()->where('subject_id', $syllabus->subject_id)->where('status', SyllabusStatus::Approved->value)
                ->get()->each(fn (Syllabus $old) => $old->update(['status' => SyllabusStatus::Superseded]));

            $syllabus->update(['status' => SyllabusStatus::Approved, 'decided_by' => $approver->id, 'decided_at' => now(), 'decision_note' => $note]);
            $this->audit->record(AuditEvent::Approved, $syllabus, reason: $note);
        });

        return $syllabus->refresh();
    }

    public function reject(Syllabus $syllabus, User $approver, string $note): Syllabus
    {
        $this->assertDecidable($syllabus, $approver);

        $syllabus->update(['status' => SyllabusStatus::Rejected, 'decided_by' => $approver->id, 'decided_at' => now(), 'decision_note' => $note]);
        $this->audit->record(AuditEvent::Rejected, $syllabus, reason: $note);

        return $syllabus->refresh();
    }

    /** Xóa phiên bản nháp chưa từng gửi duyệt. */
    public function delete(Syllabus $syllabus): void
    {
        if ($syllabus->status !== SyllabusStatus::Draft || $syllabus->submitted_at !== null) {
            $this->fail('Chỉ xóa được bản nháp chưa gửi duyệt.', 'Đề cương đã qua duyệt được giữ lại để tra cứu lịch sử.');
        }

        if ($syllabus->file_path !== null) {
            Storage::disk(config('studentmanager.subject.syllabus_disk'))->delete($syllabus->file_path);
        }

        $syllabus->forceDelete();
    }

    /**
     * Đề cương áp dụng cho lớp học phần mới (BR-SUB-08): bản đã duyệt mới nhất; nếu tắt yêu cầu duyệt thì lấy bản mới
     * nhất đã gửi (chờ duyệt) hoặc đã duyệt.
     */
    public function applicableFor(Subject $subject): ?Syllabus
    {
        $statuses = config('studentmanager.subject.syllabus_requires_approval')
            ? [SyllabusStatus::Approved->value]
            : [SyllabusStatus::Approved->value, SyllabusStatus::Submitted->value];

        return Syllabus::query()->where('subject_id', $subject->id)->whereIn('status', $statuses)->orderByDesc('version')->first();
    }

    /** CLS gọi trước khi mở lớp mới: chưa có đề cương áp dụng được thì từ chối (BR-SUB-08). */
    public function assertUsableForNewSection(Subject $subject): Syllabus
    {
        $syllabus = $this->applicableFor($subject);

        if ($syllabus === null) {
            $this->fail(
                "Học phần {$subject->code} chưa có đề cương được duyệt.",
                'Bộ môn cần soạn và gửi duyệt đề cương trước khi mở lớp mới (BR-SUB-08); có thể tắt yêu cầu này bằng SYLLABUS_REQUIRES_APPROVAL.'
            );
        }

        return $syllabus;
    }

    public function openVersion(Subject $subject): ?Syllabus
    {
        return Syllabus::query()->where('subject_id', $subject->id)
            ->whereIn('status', [SyllabusStatus::Draft->value, SyllabusStatus::Rejected->value, SyllabusStatus::Submitted->value])
            ->first();
    }

    // ---------------------------------------------------------------------

    private function assertEditable(Syllabus $syllabus): void
    {
        if (! $syllabus->status->isEditable()) {
            $this->fail(
                "Đề cương phiên bản {$syllabus->version} đang ở trạng thái {$syllabus->status->label()} nên không sửa được.",
                'Chỉ sửa bản nháp hoặc bản bị trả lại; muốn thay đổi bản đã duyệt thì tạo phiên bản mới.'
            );
        }
    }

    private function assertDecidable(Syllabus $syllabus, User $approver): void
    {
        if ($syllabus->status !== SyllabusStatus::Submitted) {
            $this->fail("Đề cương phiên bản {$syllabus->version} chưa được gửi duyệt hoặc đã xử lý.", 'Tải lại để xem trạng thái mới nhất.');
        }

        if ($syllabus->created_by === $approver->id || $syllabus->updated_by === $approver->id) {
            $this->fail('Không tự duyệt đề cương do chính mình soạn.', 'Đề cương phải do lãnh đạo bộ môn / khoa hoặc phòng Đào tạo khác người soạn duyệt (BR-SUB-08).');
        }
    }
}
