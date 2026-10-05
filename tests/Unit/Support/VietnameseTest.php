<?php

namespace Tests\Unit\Support;

use App\Support\Text\Vietnamese;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class VietnameseTest extends TestCase
{
    #[DataProvider('foldCases')]
    public function test_fold_bo_dau_ha_chu_thuong_va_gop_khoang_trang(string $input, string $expected): void
    {
        $this->assertSame($expected, Vietnamese::fold($input));
    }

    public static function foldCases(): array
    {
        return [
            'chữ đ' => ['Đặng  Hoài Nam', 'dang hoai nam'],
            'chữ hoa có dấu' => ['ĐỖ THỊ ƯỚT', 'do thi uot'],
            'có dấu và khoảng trắng đầu cuối' => ['  Nguyễn Văn Ăn ', 'nguyen van an'],
            'không dấu giữ nguyên' => ['Hello World', 'hello world'],
            'chuỗi rỗng' => ['', ''],
        ];
    }

    public function test_fold_nhan_null(): void
    {
        $this->assertSame('', Vietnamese::fold(null));
    }

    public function test_escape_like_thoat_ky_tu_dac_biet(): void
    {
        $this->assertSame('100!%!_a!!b', Vietnamese::escapeLike('100%_a!b'));
    }
}
