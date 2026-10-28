<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

/**
 * Chế độ ngày / đêm (yêu cầu người dùng 28/10): layout chung đặt chế độ trong <head> trước khi trang hiện ra và có nút
 * chuyển ở header (resources/js/theme.js đổi chế độ, resources/css/app.css đổi màu).
 */
class ThemeToggleTest extends TestCase
{
    use InteractsWithRoles;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    public function test_trang_cong_va_trang_tai_khoan_co_nut_va_dat_che_do_truoc_khi_hien(): void
    {
        $this->actingAs($this->userWithRoles('ACAD'));

        foreach (['/admin/nam-hoc', '/tai-khoan/cai-dat'] as $url) {
            $html = $this->get($url)->assertOk()->getContent();

            $this->assertStringContainsString('data-theme-toggle', $html, $url);
            $this->assertStringContainsString("localStorage.getItem('sm-theme')", $html, $url);
            // Đặt chế độ ngay trong <head> để không nháy trắng
            $this->assertLessThan(strpos($html, '</head>'), strpos($html, "localStorage.getItem('sm-theme')"), $url);
        }
    }
}
