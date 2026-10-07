<?php

namespace Tests\Unit\Support;

use App\Models\User;
use App\Support\Exceptions\BusinessRuleException;
use App\Support\Exports\ExportColumn;
use App\Support\Services\ExportService;
use Tests\TestCase;

class ExportServiceTest extends TestCase
{
    public function test_export_requires_at_least_one_column_and_explains_how_to_fix_it(): void
    {
        try {
            app(ExportService::class)->export(
                User::query(),
                [],
                'SYS',
                User::factory()->make(),
            );
            $this->fail('Danh sách cột trống phải bị từ chối.');
        } catch (BusinessRuleException $exception) {
            $this->assertStringContainsString('Hãy chọn ít nhất một cột', $exception->userMessage());
        }
    }

    public function test_export_rejects_unsupported_format_with_remediation(): void
    {
        try {
            app(ExportService::class)->export(
                User::query(),
                [new ExportColumn('name', 'Họ và tên')],
                'SYS',
                User::factory()->make(),
                format: 'docx',
            );
            $this->fail('Định dạng không hỗ trợ phải bị từ chối.');
        } catch (BusinessRuleException $exception) {
            $this->assertStringContainsString('Excel (.xlsx), CSV hoặc PDF', $exception->userMessage());
        }
    }
}
