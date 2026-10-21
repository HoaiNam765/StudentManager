<?php

namespace App\Modules\Notification\Services;

use App\Models\User;
use App\Modules\Auth\Enums\AccountStatus;
use App\Modules\Notification\Models\Notification;
use App\Support\Services\BaseService;
use Illuminate\Support\Facades\DB;

/**
 * Cổng phát thông báo duy nhất cho mọi module (FR-NOT-001; BR-NOT-01, 07, 09). Tạo một bản ghi nội dung và một dòng cho
 * từng người nhận (ghi theo lô `notifications.chunk_size`). Có `event_key` thì chỉ gửi một lần: gọi lại cùng khóa trả
 * về `null`, không tạo thông báo trùng. Không gửi cho tài khoản đã ngừng (tài khoản khóa tạm / chỉ đọc vẫn nhận).
 *
 * Ví dụ: `app(NotificationDispatcher::class)->send(['title' => …, 'body' => …, 'link' => '/student/…'], [$userId])`.
 * Nội dung không chứa dữ liệu nhạy cảm (điểm, số nợ, CCCD — BR-NOT-03), chỉ nhắc và liên kết yêu cầu đăng nhập.
 */
class NotificationDispatcher extends BaseService
{
    private const DEFAULTS = [
        'source' => 'system',
        'event_code' => null,
        'event_key' => null,
        'category' => 'general',
        'link' => null,
        'priority' => 'normal',
        'mandatory' => false,
        'pinned' => false,
        'sender_id' => null,
        'expires_at' => null,
    ];

    /**
     * @param  array{title: string, body: string, link?: ?string, category?: string, source?: string, event_code?: ?string, event_key?: ?string, priority?: string, mandatory?: bool, pinned?: bool, sender_id?: ?int, expires_at?: mixed}  $message
     * @param  iterable<int>  $userIds
     * @return Notification|null null khi đã gửi trước đó (cùng `event_key`) hoặc không còn người nhận
     */
    public function send(array $message, iterable $userIds): ?Notification
    {
        $message = array_merge(self::DEFAULTS, $message);

        if (blank($message['title'] ?? null) || blank($message['body'] ?? null)) {
            $this->fail('Thông báo phải có tiêu đề và nội dung.', 'Nhập tiêu đề và nội dung trước khi gửi.');
        }

        if ($message['link'] !== null && (! str_starts_with($message['link'], '/') || str_starts_with($message['link'], '//'))) {
            $this->fail('Liên kết của thông báo phải là đường dẫn trong hệ thống.', 'Dùng đường dẫn tương đối bắt đầu bằng "/", ví dụ /student/ket-qua-hoc-tap.');
        }

        if ($message['event_key'] !== null && Notification::query()->withTrashed()->where('event_key', $message['event_key'])->exists()) {
            return null; // BR-NOT-09: đã phát cho thay đổi trạng thái này
        }

        $userIds = $this->activeUsers($userIds);

        if ($userIds === []) {
            return null;
        }

        return $this->transaction(function () use ($message, $userIds): Notification {
            $notification = Notification::query()->create($message + [
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            $this->deliver($notification, $userIds);

            return $notification;
        });
    }

    /**
     * Ghi người nhận cho một thông báo đã có (gửi ngay hoặc tới giờ hẹn). Bỏ người đã nhận rồi.
     *
     * @param  list<int>  $userIds
     */
    public function deliver(Notification $notification, array $userIds): int
    {
        $now = now();
        $count = 0;

        foreach (array_chunk(array_values(array_unique($userIds)), max(1, (int) config('notifications.chunk_size', 500))) as $chunk) {
            $count += DB::table('notification_recipients')->insertOrIgnore(array_map(fn (int $id) => [
                'notification_id' => $notification->id,
                'user_id' => $id,
                'created_at' => $now,
            ], $chunk));
        }

        $notification->forceFill(['recipient_count' => $notification->recipients()->count()])->saveQuietly();

        return $count;
    }

    /** @return list<int> */
    private function activeUsers(iterable $userIds): array
    {
        $ids = collect($userIds)->filter()->map(fn ($id) => (int) $id)->unique()->values();

        if ($ids->isEmpty()) {
            return [];
        }

        return User::query()->whereIn('id', $ids)->where('status', '!=', AccountStatus::Inactive->value)->pluck('id')->map(fn ($id) => (int) $id)->all();
    }
}
