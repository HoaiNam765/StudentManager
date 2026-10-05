<?php

namespace Tests\Support;

use App\Support\Services\BaseService;

/** Ví dụ Service của một module: quy tắc nghiệp vụ nằm ở đây, không nằm trong Controller. */
class DemoItemService extends BaseService
{
    public function deactivate(DemoItem $item): void
    {
        if ($item->code === 'CORE') {
            $this->fail(
                'Không thể ngừng hoạt động mục hệ thống.',
                'Hãy chọn mục khác hoặc liên hệ quản trị viên.'
            );
        }

        $this->transaction(fn () => $item->deactivate());
    }

    /** Dùng để kiểm tra giao dịch: sửa tên rồi gặp lỗi thì phải hoàn tác. */
    public function renameThenFail(DemoItem $item, string $name): void
    {
        $this->transaction(function () use ($item, $name): void {
            $item->update(['name' => $name]);

            $this->fail('Lỗi giả lập sau khi sửa.');
        });
    }
}
