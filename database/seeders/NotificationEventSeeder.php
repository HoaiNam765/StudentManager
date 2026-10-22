<?php

namespace Database\Seeders;

use App\Modules\Notification\Models\EventSubscription;
use App\Modules\Notification\Support\EventCatalog;
use Illuminate\Database\Seeder;

/**
 * Nạp danh mục sự kiện thông báo của phụ lục A (FR-NOT-004; BR-NOT-02). Chạy lại được: chỉ cập nhật thông tin từ BA (tên,
 * module, người nhận, bắt buộc, đã nối); mẫu, kênh và bật / tắt mà ADMIN đã sửa thì giữ nguyên.
 */
class NotificationEventSeeder extends Seeder
{
    public function run(): void
    {
        foreach (EventCatalog::EVENTS as $code => $event) {
            $row = EventSubscription::query()->firstOrNew(['event_code' => $code]);

            if (! $row->exists) {
                $row->fill([
                    'channels' => $event['channels'],
                    'title_template' => $event['title'],
                    'body_template' => $event['body'],
                    'link_template' => $event['link'] ?? null,
                    'enabled' => true,
                ]);
            }

            $row->fill([
                'name' => $event['name'],
                'module' => $event['module'],
                'category' => $event['category'],
                'recipients' => $event['recipients'],
                'mandatory' => $event['mandatory'],
                'wired' => $event['wired'] ?? false,
            ])->save();
        }
    }
}
