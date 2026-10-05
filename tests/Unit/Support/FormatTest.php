<?php

namespace Tests\Unit\Support;

use App\Support\Format;
use Carbon\CarbonImmutable;
use Tests\TestCase;

class FormatTest extends TestCase
{
    public function test_gio_luu_utc_hien_thi_theo_gio_viet_nam(): void
    {
        // 20:00 UTC ngày 04/10 là 03:00 ngày 05/10 theo UTC+7
        $this->assertSame('05/10/2026', Format::date('2026-10-04 20:00:00'));
        $this->assertSame('05/10/2026 03:00', Format::dateTime('2026-10-04 20:00:00'));
    }

    public function test_nhan_doi_tuong_carbon(): void
    {
        $value = CarbonImmutable::parse('2026-01-15 10:30:00', 'UTC');

        $this->assertSame('15/01/2026 17:30', Format::dateTime($value));
    }

    public function test_gia_tri_rong_tra_ve_chuoi_rong(): void
    {
        $this->assertSame('', Format::date(null));
        $this->assertSame('', Format::dateTime(''));
        $this->assertSame('', Format::money(null));
    }

    public function test_tien_vnd_khong_co_phan_thap_phan(): void
    {
        $this->assertSame('4.050.000 ₫', Format::money(4050000));
        $this->assertSame('4.050.000 ₫', Format::money('4050000'));
        $this->assertSame('4.050.000 ₫', Format::money(4050000.4));
        $this->assertSame('0 ₫', Format::money(0));
    }
}
