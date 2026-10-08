<?php

namespace Database\Seeders;

use App\Modules\System\Enums\PolicySetStatus;
use App\Modules\System\Models\PolicySet;
use App\Modules\System\Settings\PolicyDefinitions;
use Illuminate\Database\Seeder;

/**
 * Bộ quy chế mặc định theo docs/BA.md phụ lục C, áp dụng cho mọi khóa (FR-SYS-003).
 * Tạo thẳng ở trạng thái "Đã ban hành" với ngày hiệu lực trong quá khứ để mọi khóa đều có quy chế;
 * bộ quy chế thật của trường thì soạn và ban hành qua giao diện (không hồi tố). Chạy lặp lại an toàn.
 */
class PolicySeeder extends Seeder
{
    public const CODE = 'QC-MAC-DINH';

    public function run(): void
    {
        $set = PolicySet::firstOrCreate(
            ['code' => self::CODE, 'version' => 1],
            [
                'name' => 'Quy chế mặc định (phụ lục C, tham chiếu Thông tư 56/2026/TT-BGDĐT)',
                'description' => 'Giá trị mặc định từ tài liệu BA; cần đối chiếu quy chế của trường trước khi dùng thật.',
                'cohort_from' => null,
                'cohort_to' => null,
                'effective_from' => '2000-01-01',
                'status' => PolicySetStatus::Published,
                'published_at' => now(),
            ],
        );

        foreach (PolicyDefinitions::defaults() as $key => $value) {
            $set->items()->firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
