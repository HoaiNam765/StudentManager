<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_trang_goc_dua_khach_ve_trang_dang_nhap(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_trang_dang_nhap_hien_thi(): void
    {
        $this->get('/login')->assertOk()->assertSee('Đăng nhập');
    }
}
