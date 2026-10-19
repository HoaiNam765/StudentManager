<?php

namespace App\Modules\Student\Services;

use App\Models\User;
use App\Modules\Auth\Enums\StudentAccountEvent;
use App\Modules\Auth\Services\UserService;
use App\Modules\Student\Enums\StudentStatus;
use App\Modules\Student\Events\StudentStatusChanged;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Models\StudentStatusHistory;
use App\Support\Audit\AuditLogger;
use App\Support\Services\BaseService;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Chuyển trạng thái sinh viên theo quyết định (FR-STU-009, 010; BR-STU-05, 10; BR-AUTH-07; EV-STU-02).
 *
 * - Chỉ theo sơ đồ BA 6.2: Đang học → Bảo lưu / Thôi học / Buộc thôi học / Chuyển trường / Đã tốt nghiệp;
 *   Bảo lưu → Đang học / Thôi học / Buộc thôi học. Chờ nhập học → Đang học là xác nhận nhập học (#102), không đi qua đây.
 * - Trạng thái cuối (Thôi học, Buộc thôi học, Chuyển trường, Đã tốt nghiệp) chỉ khôi phục về Đang học bằng quyết định
 *   đặc biệt (`special`, người có STU.approve toàn trường — ACAD), bắt buộc lý do, nhật ký ghi rõ.
 * - Một giao dịch: đổi trạng thái, ghi `student_status_histories` (số quyết định, ngày hiệu lực, lý do, tệp), phát
 *   `StudentStatusChanged` (EV-STU-02) — ENR hủy đăng ký chưa chốt của kỳ chưa bắt đầu (BR-STU-10); gọi AUTH:
 *   thôi học / buộc thôi học / chuyển trường → hẹn ngừng tài khoản, tốt nghiệp → chỉ đọc, khôi phục → hoạt động (BR-AUTH-07).
 *   Chặn đăng ký mới do ENR chỉ cho sinh viên Đang học (BR-ENR-01).
 */
class StudentStatusService extends BaseService
{
    private const ALLOWED = [
        'studying' => ['suspended', 'withdrawn', 'expelled', 'transferred', 'graduated'],
        'suspended' => ['studying', 'withdrawn', 'expelled'],
    ];

    public function __construct(
        private readonly UserService $users,
        private readonly AuditLogger $audit,
    ) {}

    /** @return list<StudentStatus> trạng thái được chuyển tới từ trạng thái hiện tại (không tính khôi phục đặc biệt) */
    public function nextStatuses(Student $student): array
    {
        return array_map(fn (string $s) => StudentStatus::from($s), self::ALLOWED[$student->status->value] ?? []);
    }

    /**
     * @param  array{decision_no: string, effective_date: string, reason: string, special?: bool}  $data
     */
    public function change(Student $student, StudentStatus $to, array $data, ?UploadedFile $attachment = null, ?User $actor = null): StudentStatusHistory
    {
        $from = $student->status;
        $special = (bool) ($data['special'] ?? false);

        if ($special) {
            if (! $from->isFinal() || $to !== StudentStatus::Studying) {
                $this->fail(
                    'Quyết định đặc biệt chỉ dùng để khôi phục sinh viên từ trạng thái cuối về Đang học.',
                    'Chuyển trạng thái thường theo sơ đồ (BR-STU-05) thì bỏ chọn "quyết định đặc biệt".'
                );
            }
        } elseif (! in_array($to->value, self::ALLOWED[$from->value] ?? [], true)) {
            $allowed = implode(', ', array_map(fn (StudentStatus $s) => $s->label(), $this->nextStatuses($student)));
            $this->fail(
                "Không chuyển được từ {$from->label()} sang {$to->label()}.",
                $from->isFinal()
                    ? 'Trạng thái cuối chỉ khôi phục bằng quyết định đặc biệt của Phòng Đào tạo (BR-STU-05).'
                    : ($allowed !== '' ? "Từ {$from->label()} chỉ chuyển được sang: {$allowed} (BR-STU-05)." : 'Xác nhận nhập học trước (FR-STU-002).')
            );
        }

        $history = $this->transaction(function () use ($student, $from, $to, $data, $special, $attachment): StudentStatusHistory {
            $student = Student::query()->lockForUpdate()->findOrFail($student->id);

            if ($student->status !== $from) {
                $this->fail('Trạng thái sinh viên vừa được người khác thay đổi.', 'Tải lại hồ sơ rồi thao tác lại.');
            }

            $history = StudentStatusHistory::create([
                'student_id' => $student->id,
                'from_status' => $from,
                'to_status' => $to,
                'decision_no' => trim($data['decision_no']),
                'effective_date' => $data['effective_date'],
                'reason' => trim($data['reason']),
                'special' => $special,
            ]);

            if ($attachment !== null) {
                $path = $attachment->store("student-decisions/{$student->id}", config('studentmanager.student.decision_disk', 'local'));
                $history->update(['attachment_path' => $path, 'attachment_name' => $attachment->getClientOriginalName()]);
            }

            $note = ($special ? 'Quyết định đặc biệt ' : 'Quyết định ').$history->decision_no.": {$history->reason}";
            $this->audit->withReason($note, fn () => $student->update(['status' => $to]));

            StudentStatusChanged::dispatch($student, $from, $to, $history);

            if ($student->user !== null && ($event = $this->accountEvent($from, $to)) !== null) {
                $this->users->applyStudentStatus($student->user, $event);
            }

            return $history;
        });

        return $history->load('student');
    }

    private function accountEvent(StudentStatus $from, StudentStatus $to): ?StudentAccountEvent
    {
        return match ($to) {
            StudentStatus::Withdrawn => StudentAccountEvent::Dropped,
            StudentStatus::Expelled => StudentAccountEvent::Expelled,
            StudentStatus::Transferred => StudentAccountEvent::Transferred,
            StudentStatus::Graduated => StudentAccountEvent::Graduated,
            StudentStatus::Studying => $from->isFinal() ? StudentAccountEvent::Reinstated : null,
            default => null,
        };
    }

    public function attachmentDisk(): Filesystem
    {
        return Storage::disk(config('studentmanager.student.decision_disk', 'local'));
    }
}
