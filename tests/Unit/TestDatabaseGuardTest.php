<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tests\Support\TestDatabaseGuard;

class TestDatabaseGuardTest extends TestCase
{
    public function test_cho_phep_csdl_co_hau_to_test_va_sqlite(): void
    {
        TestDatabaseGuard::assertSafe('mysql', 'student_manager_test');
        TestDatabaseGuard::assertSafe('sqlite', ':memory:');

        $this->addToAssertionCount(2);
    }

    public function test_tu_choi_csdl_that_de_khong_xoa_nham_du_lieu(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Từ chối chạy test trên CSDL 'student_manager'");

        TestDatabaseGuard::assertSafe('mysql', 'student_manager');
    }

    public function test_hau_to_phai_nam_o_cuoi_ten(): void
    {
        $this->expectException(RuntimeException::class);

        TestDatabaseGuard::assertSafe('mysql', 'student_manager_test_backup');
    }
}
