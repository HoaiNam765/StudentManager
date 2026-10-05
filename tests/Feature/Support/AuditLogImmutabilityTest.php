<?php

namespace Tests\Feature\Support;

use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLog;
use App\Support\Audit\AuditLogger;
use App\Support\Audit\AuditLogImmutableException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/** BR-SYS-01: nhật ký kiểm toán chỉ được ghi thêm, không ai sửa hoặc xóa được. */
class AuditLogImmutabilityTest extends TestCase
{
    use RefreshDatabase;

    private AuditLog $log;

    protected function setUp(): void
    {
        parent::setUp();

        $this->log = app(AuditLogger::class)->record(AuditEvent::Login, reason: 'Ban đầu');
    }

    public function test_khong_sua_duoc_bang_eloquent(): void
    {
        try {
            $this->log->update(['reason' => 'Đã bị sửa']);
            $this->fail('Phải chặn sửa nhật ký');
        } catch (AuditLogImmutableException $e) {
            $this->assertStringContainsString('chỉ được ghi thêm', $e->getMessage());
        }

        $this->assertSame('Ban đầu', $this->log->fresh()->reason);
    }

    public function test_khong_xoa_duoc_bang_eloquent(): void
    {
        $this->expectException(AuditLogImmutableException::class);

        $this->log->delete();
    }

    public function test_khong_sua_duoc_bang_query_builder(): void
    {
        $this->skipUnlessMysql();

        try {
            AuditLog::query()->update(['reason' => 'Đã bị sửa']);
            $this->fail('Trigger CSDL phải chặn sửa');
        } catch (QueryException $e) {
            $this->assertStringContainsString('append-only', $e->getMessage());
        }

        $this->assertSame('Ban đầu', $this->log->fresh()->reason);
    }

    public function test_khong_xoa_duoc_bang_query_builder_hay_sql_truc_tiep(): void
    {
        $this->skipUnlessMysql();

        foreach ([
            fn () => DB::table('audit_logs')->delete(),
            fn () => DB::statement('DELETE FROM audit_logs WHERE id = ?', [$this->log->id]),
        ] as $attempt) {
            try {
                $attempt();
                $this->fail('Trigger CSDL phải chặn xóa');
            } catch (QueryException $e) {
                $this->assertStringContainsString('append-only', $e->getMessage());
            }
        }

        $this->assertSame(1, AuditLog::count());
    }

    private function skipUnlessMysql(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            $this->markTestSkipped('Trigger chặn sửa/xóa chỉ được tạo trên MySQL.');
        }
    }
}
