<?php

namespace App\Modules\Notification\Services;

use App\Models\User;
use App\Modules\Notification\Models\EventSubscription;
use App\Modules\Notification\Models\NotificationPreference;
use App\Support\Services\BaseService;
use Illuminate\Support\Collection;

/**
 * Cấu hình sự kiện thông báo (FR-NOT-004) và tùy chọn nhận của người dùng (FR-NOT-007; BR-NOT-02):
 * - ADMIN bật / tắt sự kiện, sửa kênh và mẫu; sự kiện bắt buộc không tắt được, mẫu không được trống.
 * - Người dùng tắt / bật từng loại thông báo **không bắt buộc**; loại bắt buộc luôn nhận.
 */
class EventSubscriptionService extends BaseService
{
    /** @param array{enabled?: bool, channels?: list<string>, title_template?: string, body_template?: string, link_template?: ?string} $data */
    public function update(EventSubscription $event, array $data): EventSubscription
    {
        if (array_key_exists('enabled', $data) && ! $data['enabled'] && $event->mandatory) {
            $this->fail("Sự kiện {$event->event_code} là loại bắt buộc nên không tắt được (BR-NOT-02).", 'Chỉ sửa mẫu nội dung; muốn ngừng phải sửa danh mục ở BA phụ lục A.');
        }

        if (isset($data['channels']) && $data['channels'] === []) {
            $this->fail('Sự kiện phải có ít nhất một kênh gửi.', 'Chọn "Trong ứng dụng" hoặc "Email".');
        }

        if (array_key_exists('link_template', $data) && $data['link_template'] !== null
            && (! str_starts_with($data['link_template'], '/') || str_starts_with($data['link_template'], '//'))) {
            $this->fail('Liên kết mẫu phải là đường dẫn trong hệ thống.', 'Dùng đường dẫn bắt đầu bằng "/", ví dụ /student/ket-qua-hoc-tap.');
        }

        $event->fill($data)->save();

        return $event;
    }

    /** @return Collection<int, array<string, mixed>> các loại thông báo người dùng chỉnh được (đang bật, có kênh trong ứng dụng) */
    public function preferences(User $user): Collection
    {
        $off = NotificationPreference::query()->where('user_id', $user->id)->where('in_app', false)->pluck('event_code')->all();

        return EventSubscription::query()->where('enabled', true)->orderBy('event_code')->get()
            ->filter(fn (EventSubscription $e) => $e->sendsInApp())
            ->map(fn (EventSubscription $e) => [
                'event_code' => $e->event_code,
                'name' => $e->name,
                'category' => $e->category,
                'mandatory' => $e->mandatory,
                'in_app' => $e->mandatory || ! in_array($e->event_code, $off, true),
            ])->values();
    }

    /** @param array<string, bool> $choices mã sự kiện => nhận trong ứng dụng */
    public function savePreferences(User $user, array $choices): Collection
    {
        $events = EventSubscription::query()->whereIn('event_code', array_keys($choices))->get()->keyBy('event_code');

        foreach ($choices as $code => $inApp) {
            $event = $events->get($code);

            if ($event === null) {
                $this->fail("Không có loại thông báo {$code}.", 'Tải lại trang tùy chọn thông báo.');
            }

            if ($event->mandatory && ! $inApp) {
                $this->fail("\"{$event->name}\" là thông báo bắt buộc nên không tắt được (BR-NOT-02).", 'Giữ bật loại thông báo này.');
            }

            NotificationPreference::query()->updateOrCreate(['user_id' => $user->id, 'event_code' => $code], ['in_app' => (bool) $inApp]);
        }

        return $this->preferences($user);
    }
}
