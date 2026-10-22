<?php

namespace App\Modules\Notification\Services;

use App\Models\User;
use App\Modules\Auth\Models\Role;
use App\Modules\Notification\Models\Notification;
use App\Modules\Notification\Models\RecipientGroup;
use App\Support\Services\BaseService;
use Carbon\CarbonImmutable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Thông báo thủ công (FR-NOT-006, 009, 015; BR-NOT-05, 08):
 * - `preview()`: số người nhận trước khi gửi; đối tượng ngoài phạm vi bị từ chối (AudienceResolver).
 * - `create()`: gửi ngay, hoặc hẹn giờ (`status` = scheduled, lệnh `notifications:send-scheduled` chạy mỗi phút nên bắt
 *   đầu gửi trong vòng 5 phút sau giờ hẹn). Người nhận tính lại lúc gửi theo phạm vi của người gửi. Có ghim, hạn hiển
 *   thị, khẩn, tệp đính kèm (disk riêng tư). Ghi người gửi và đối tượng đã chọn (BR-NOT-08).
 * - `revoke()`: chỉ ADMIN; thông báo biến mất khỏi trung tâm thông báo của người nhận, vẫn lưu lịch sử.
 * - Nhóm người nhận: lưu bộ chọn đối tượng của chính người gửi để dùng lại.
 */
class AnnouncementService extends BaseService
{
    public function __construct(
        private readonly AudienceResolver $audiences,
        private readonly NotificationDispatcher $dispatcher,
    ) {}

    /** @return array{students: int, labels: list<string>} */
    public function preview(User $actor, array $audience): array
    {
        $resolved = $this->audiences->resolve($actor, $audience);

        return ['students' => $resolved['students'], 'labels' => $resolved['labels']];
    }

    /**
     * @param  array{title: string, body: string, audience: array, scheduled_at?: ?string, expires_at?: ?string, pinned?: bool, urgent?: bool, link?: ?string}  $data
     */
    public function create(User $actor, array $data, ?UploadedFile $attachment = null): Notification
    {
        $resolved = $this->audiences->resolve($actor, $data['audience']);
        $schedule = filled($data['scheduled_at'] ?? null) ? $this->localTime($data['scheduled_at']) : null;
        $expires = filled($data['expires_at'] ?? null) ? $this->localTime($data['expires_at']) : null;

        if ($schedule !== null && ! $schedule->isFuture()) {
            $this->fail('Giờ hẹn gửi phải ở tương lai.', 'Chọn thời điểm sau hiện tại, hoặc bỏ giờ hẹn để gửi ngay.');
        }

        if ($expires !== null && $expires->lte($schedule ?? CarbonImmutable::now())) {
            $this->fail('Hạn hiển thị phải sau thời điểm gửi.', 'Chọn hạn hiển thị muộn hơn, hoặc để trống nếu không giới hạn.');
        }

        if ($resolved['students'] === 0) {
            $this->fail('Đối tượng đã chọn chưa có sinh viên nào có tài khoản.', 'Kiểm tra lại lớp / khóa / danh sách MSSV đã chọn.');
        }

        $path = $attachment?->store('notification-attachments', 'local');

        return $this->transaction(function () use ($actor, $data, $resolved, $schedule, $expires, $path, $attachment): Notification {
            $notification = Notification::query()->create([
                'source' => 'manual',
                'category' => 'announcement',
                'title' => $data['title'],
                'body' => $data['body'],
                'link' => $data['link'] ?? null,
                'priority' => ! empty($data['urgent']) ? 'urgent' : 'normal',
                'pinned' => (bool) ($data['pinned'] ?? false),
                'sender_id' => $actor->id,
                'audience' => $resolved['audience'] + ['labels' => $resolved['labels']],
                'scheduled_at' => $schedule?->utc(),
                'expires_at' => $expires?->utc(),
                'attachment_path' => $path,
                'attachment_name' => $attachment?->getClientOriginalName(),
                'status' => $schedule !== null ? 'scheduled' : 'sent',
                'sent_at' => $schedule !== null ? null : now(),
            ]);

            if ($schedule === null) {
                $this->dispatcher->deliver($notification, $resolved['user_ids']);
            }

            return $notification->refresh();
        });
    }

    /** Gửi các thông báo hẹn giờ đã tới giờ (lệnh mỗi phút). @return int số thông báo đã gửi */
    public function sendScheduled(): int
    {
        $count = 0;

        Notification::query()->where('status', 'scheduled')->where('scheduled_at', '<=', now())->with('sender')
            ->chunkById(50, function ($rows) use (&$count): void {
                foreach ($rows as $notification) {
                    $this->transaction(function () use ($notification, &$count): void {
                        $locked = Notification::query()->lockForUpdate()->find($notification->id);

                        if ($locked?->status !== 'scheduled' || $notification->sender === null) {
                            return;
                        }

                        // Tính lại người nhận theo phạm vi hiện tại của người gửi
                        $resolved = $this->audiences->resolve($notification->sender, $locked->audience ?? []);
                        $locked->forceFill(['status' => 'sent', 'sent_at' => now()])->save();
                        $this->dispatcher->deliver($locked, $resolved['user_ids']);
                        $count++;
                    });
                }
            });

        return $count;
    }

    /** ADMIN thu hồi thông báo thủ công trong ứng dụng (BR-NOT-08); hẹn giờ chưa gửi thì hủy luôn. */
    public function revoke(User $actor, Notification $notification, string $reason): Notification
    {
        if (! $actor->hasRole(Role::ADMIN)) {
            abort(403, 'Chỉ quản trị hệ thống (ADMIN) được thu hồi thông báo.');
        }

        if ($notification->source !== 'manual') {
            $this->fail('Chỉ thu hồi được thông báo thủ công.', 'Thông báo tự động theo sự kiện không thu hồi; tắt sự kiện ở cấu hình thông báo nếu cần.');
        }

        if ($notification->status === 'revoked') {
            $this->fail('Thông báo này đã bị thu hồi.', 'Tải lại lịch sử thông báo.');
        }

        $notification->forceFill(['status' => 'revoked', 'revoked_at' => now(), 'revoked_by' => $actor->id, 'revoke_reason' => $reason])->save();

        return $notification;
    }

    /** Lịch sử đã gửi kèm tỷ lệ đã đọc (FR-NOT-009): người gửi thấy của mình, ADMIN thấy tất cả. */
    public function history(User $actor, int $perPage = 20)
    {
        return Notification::query()->where('source', 'manual')
            ->when(! $actor->hasRole(Role::ADMIN), fn ($q) => $q->where('sender_id', $actor->id))
            ->withCount(['recipients as read_count' => fn ($q) => $q->whereNotNull('read_at')])
            ->with('sender:id,name')
            ->latest('id')
            ->paginate($perPage);
    }

    public function attachmentFor(User $user, Notification $notification): string
    {
        $allowed = $notification->sender_id === $user->id || $user->hasRole(Role::ADMIN)
            || ($notification->status === 'sent' && $notification->recipients()->where('user_id', $user->id)->exists());

        abort_unless($allowed, 404, 'Không tìm thấy tệp đính kèm.');
        abort_if($notification->attachment_path === null || ! Storage::disk('local')->exists($notification->attachment_path), 404, 'Thông báo không có tệp đính kèm.');

        return $notification->attachment_path;
    }

    public function saveGroup(User $actor, string $name, array $audience, ?RecipientGroup $group = null): RecipientGroup
    {
        $resolved = $this->audiences->resolve($actor, $audience); // kiểm tra phạm vi trước khi lưu

        if (RecipientGroup::query()->where('owner_id', $actor->id)->where('name', $name)->when($group, fn ($q) => $q->whereKeyNot($group->id))->exists()) {
            $this->fail("Bạn đã có nhóm tên \"{$name}\".", 'Đặt tên khác cho nhóm người nhận.');
        }

        $group ??= new RecipientGroup(['owner_id' => $actor->id]);
        $group->fill(['name' => $name, 'audience' => ['targets' => $resolved['audience']['targets'], 'student_codes' => $resolved['audience']['student_codes']]])->save();

        return $group;
    }

    private function localTime(string $value): CarbonImmutable
    {
        return CarbonImmutable::parse($value, config('studentmanager.display_timezone'));
    }
}
