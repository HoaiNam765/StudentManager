<?php

namespace App\Modules\Notification\Services;

use App\Models\User;
use App\Modules\Notification\Models\NotificationRecipient;
use App\Support\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trung tâm thông báo của người dùng (FR-NOT-001; BR-NOT-01, 06): số chưa đọc, danh sách (lọc chưa đọc, nhóm, từ khóa,
 * kho lưu thông báo đã hết hạn), đánh dấu đã đọc / đọc tất cả, mở tới đối tượng liên quan. Mọi truy vấn đi từ
 * `notification_recipients` của chính người dùng, nên không đọc được thông báo của người khác (trả 404).
 */
class NotificationCenter extends BaseService
{
    public function unreadCount(User $user): int
    {
        return $this->mine($user)->whereNull('notification_recipients.read_at')->count();
    }

    /** @param array{status?: string, category?: string, q?: string, archived?: bool} $filters */
    public function list(User $user, array $filters = []): LengthAwarePaginator
    {
        $archived = (bool) ($filters['archived'] ?? false);

        return $this->mine($user, $archived)
            ->when(($filters['status'] ?? 'all') === 'unread', fn (Builder $q) => $q->whereNull('notification_recipients.read_at'))
            ->when($filters['category'] ?? null, fn (Builder $q, string $c) => $q->where('notifications.category', $c))
            ->when($filters['q'] ?? null, fn (Builder $q, string $term) => $q->where(fn (Builder $w) => $w
                ->where('notifications.title', 'like', '%'.addcslashes($term, '%_\\').'%')
                ->orWhere('notifications.body', 'like', '%'.addcslashes($term, '%_\\').'%')))
            ->orderByDesc('notifications.pinned')
            ->orderByDesc('notifications.sent_at')
            ->orderByDesc('notification_recipients.id')
            ->select('notification_recipients.*')
            ->with('notification:id,source,event_code,category,title,body,link,attachment_name,priority,mandatory,pinned,sent_at,expires_at,sender_id', 'notification.sender:id,name')
            ->paginate((int) config('notifications.per_page', 20));
    }

    /** Đánh dấu đã đọc; trả về thông báo để giao diện mở liên kết. */
    public function markRead(User $user, int $notificationId): NotificationRecipient
    {
        $row = $this->mine($user, null)->where('notification_recipients.notification_id', $notificationId)
            ->select('notification_recipients.*')->first();

        abort_if($row === null, 404, 'Không tìm thấy thông báo.');

        if ($row->read_at === null) {
            $row->forceFill(['read_at' => now()])->save();
        }

        return $row->load('notification');
    }

    public function markAllRead(User $user): int
    {
        return NotificationRecipient::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->whereIn('notification_id', fn ($q) => $q->select('id')->from('notifications')->where('status', 'sent')->whereNull('deleted_at'))
            ->update(['read_at' => now()]);
    }

    /**
     * Dòng thông báo của người dùng. `$archived`: false = đang hiển thị, true = đã hết hạn (kho lưu), null = cả hai.
     * Thông báo bị thu hồi / chưa gửi không bao giờ hiện.
     */
    private function mine(User $user, ?bool $archived = false): Builder
    {
        return NotificationRecipient::query()
            ->join('notifications', 'notifications.id', '=', 'notification_recipients.notification_id')
            ->where('notification_recipients.user_id', $user->id)
            ->where('notifications.status', 'sent')
            ->whereNull('notifications.deleted_at')
            ->when($archived === false, fn (Builder $q) => $q->where(fn (Builder $w) => $w->whereNull('notifications.expires_at')->orWhere('notifications.expires_at', '>', now())))
            ->when($archived === true, fn (Builder $q) => $q->where('notifications.expires_at', '<=', now()));
    }
}
