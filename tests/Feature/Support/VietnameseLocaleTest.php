<?php

namespace Tests\Feature\Support;

use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class VietnameseLocaleTest extends TestCase
{
    public function test_ngon_ngu_mac_dinh_la_tieng_viet(): void
    {
        $this->assertSame('vi', app()->getLocale());
    }

    public function test_thong_bao_xac_thuc_bang_tieng_viet(): void
    {
        $errors = Validator::make(
            ['email' => 'khong-phai-email', 'name' => ''],
            ['email' => 'required|email', 'name' => 'required']
        )->errors();

        $this->assertStringContainsString('không được bỏ trống', $errors->first('name'));
        $this->assertStringContainsString('email', $errors->first('email'));
        $this->assertDoesNotMatchRegularExpression('/must be|is required|valid email/i', $errors->first('email').$errors->first('name'));
    }

    public function test_thong_bao_dang_nhap_va_phan_trang_bang_tieng_viet(): void
    {
        $this->assertNotSame('auth.failed', __('auth.failed'));
        $this->assertNotSame('pagination.next', __('pagination.next'));
        $this->assertDoesNotMatchRegularExpression('/credentials|Next/i', __('auth.failed').__('pagination.next'));
    }
}
