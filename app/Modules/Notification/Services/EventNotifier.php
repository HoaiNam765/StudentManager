<?php

namespace App\Modules\Notification\Services;

use App\Modules\Notification\Models\EventSubscription;
use App\Modules\Notification\Models\Notification;
use App\Modules\Notification\Models\NotificationPreference;

/**
 * Gửi thông báo tự động theo sự kiện (FR-NOT-004, 007; BR-NOT-02, 03, 09): tra `event_subscriptions` (tắt hoặc không có
 * kênh trong ứng dụng thì bỏ qua), bỏ người đã tắt loại thông báo này (trừ loại bắt buộc), điền mẫu `{{bien}}` rồi gửi
 * qua `NotificationDispatcher` với `event_key` để mỗi thay đổi trạng thái chỉ báo một lần.
 *
 * Biến truyền vào chỉ là tên học phần, mã lớp, MSSV, lý do… — **không** truyền điểm, số nợ, CCCD (BR-NOT-03).
 */
class EventNotifier
{
    public function __construct(private readonly NotificationDispatcher $dispatcher) {}

    /**
     * @param  iterable<int>  $userIds
     * @param  array<string, scalar|null>  $vars
     * @param  string|null  $link  ghi đè liên kết của mẫu (ví dụ liên kết cổng giảng viên cho người nhận là giảng viên)
     */
    public function notify(string $eventCode, string $key, iterable $userIds, array $vars = [], ?string $link = null): ?Notification
    {
        $event = EventSubscription::query()->where('event_code', $eventCode)->first();

        if ($event === null || ! $event->enabled || ! $event->sendsInApp()) {
            return null;
        }

        $userIds = collect($userIds)->filter()->map(fn ($id) => (int) $id)->unique();

        if (! $event->mandatory && $userIds->isNotEmpty()) {
            $optedOut = NotificationPreference::query()->where('event_code', $eventCode)->where('in_app', false)
                ->whereIn('user_id', $userIds)->pluck('user_id')->map(fn ($id) => (int) $id);
            $userIds = $userIds->diff($optedOut);
        }

        return $this->dispatcher->send([
            'source' => 'event',
            'event_code' => $eventCode,
            'event_key' => "{$eventCode}:{$key}",
            'category' => $event->category,
            'mandatory' => $event->mandatory,
            'title' => $this->render($event->title_template, $vars),
            'body' => $this->render($event->body_template, $vars),
            'link' => $link ?? ($event->link_template !== null ? $this->render($event->link_template, $vars) : null),
        ], $userIds->values()->all());
    }

    /** Điền biến `{{ten}}`; biến không truyền thì để trống. */
    public function render(string $template, array $vars): string
    {
        $text = preg_replace_callback('/\{\{\s*([a-z0-9_]+)\s*\}\}/i', fn (array $m) => (string) ($vars[$m[1]] ?? ''), $template);

        return trim(preg_replace('/[ \t]{2,}/', ' ', (string) $text));
    }
}
