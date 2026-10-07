<?php

namespace Tests\Feature\Support;

use App\Jobs\GenerateExportJob;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Auth\Services\RoleService;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLog;
use App\Support\Exceptions\BusinessRuleException;
use App\Support\Exports\ExportColumn;
use App\Support\Exports\ExportRenderer;
use App\Support\Services\ExportService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Reader\XLSX\Reader;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\Support\ExportFixture;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

class ExportServiceTest extends TestCase
{
    use DatabaseMigrations;
    use InteractsWithRoles;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('export_fixture', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id');
            $table->string('name');
            $table->text('sensitive_value');
        });

        $this->seedRoles();
    }

    public function test_xlsx_export_preserves_vietnamese_content_and_writes_audit_log(): void
    {
        $user = $this->userWithRoles('ADMIN');
        Auth::login($user);
        ExportFixture::query()->create([
            'owner_id' => $user->id,
            'name' => 'Nguyễn Thị Ánh',
            'sensitive_value' => '123456789',
        ]);

        $result = app(ExportService::class)->export(
            ExportFixture::query(),
            [new ExportColumn('name', 'Họ và tên')],
            'SYS',
            $user,
            filename: 'sinh-vien',
        );

        $this->assertFalse($result->isQueued());
        $this->assertSame(1, $result->rowCount);
        $this->assertSame('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $result->download->headers->get('content-type'));

        $path = $result->download->getFile()->getPathname();
        $reader = new Reader;
        $reader->open($path);
        $values = [];

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $values[] = array_map(fn ($cell) => $cell->getValue(), $row->getCells());
            }
        }

        $reader->close();
        unlink($path);

        $this->assertSame([['Họ và tên'], ['Nguyễn Thị Ánh']], $values);
        $this->assertDatabaseHas('audit_logs', [
            'event' => AuditEvent::Exported->value,
            'user_id' => $user->id,
        ]);
        $this->assertSame(1, AuditLog::query()->where('event', AuditEvent::Exported)->firstOrFail()->new_values['row_count']);
    }

    public function test_pdf_embeds_unicode_font_and_preserves_vietnamese_content(): void
    {
        $user = $this->userWithRoles('ADMIN');
        Auth::login($user);
        ExportFixture::query()->create([
            'owner_id' => $user->id,
            'name' => 'Đại học Bách khoa',
            'sensitive_value' => 'không xuất',
        ]);

        $result = app(ExportService::class)->export(
            ExportFixture::query(),
            [new ExportColumn('name', 'Tên trường')],
            'SYS',
            $user,
            format: 'pdf',
        );

        $this->assertSame(Response::HTTP_OK, $result->download->getStatusCode());
        $contents = file_get_contents($result->download->getFile()->getPathname());
        $this->assertStringStartsWith('%PDF-', $contents);
        $this->assertStringContainsString('/FontFile2', $contents);
        unlink($result->download->getFile()->getPathname());
    }

    public function test_csv_is_utf8_compatible_and_neutralizes_formula_values(): void
    {
        $user = $this->userWithRoles('ADMIN');
        ExportFixture::query()->create([
            'owner_id' => $user->id,
            'name' => '=HYPERLINK("https://example.test")',
            'sensitive_value' => 'không xuất',
        ]);

        $result = app(ExportService::class)->export(
            ExportFixture::query(),
            [new ExportColumn('name', 'Họ và tên')],
            'SYS',
            $user,
            format: 'csv',
        );

        $contents = file_get_contents($result->download->getFile()->getPathname());
        $this->assertStringStartsWith("\xEF\xBB\xBF", $contents);
        $this->assertStringContainsString("'=HYPERLINK", $contents);
        unlink($result->download->getFile()->getPathname());
    }

    public function test_sensitive_eloquent_cast_is_decrypted_for_authorized_export(): void
    {
        $user = $this->userWithRoles('ADMIN');
        ExportFixture::query()->create([
            'owner_id' => $user->id,
            'name' => 'Có dữ liệu riêng tư',
            'sensitive_value' => 'gia-tri-da-ma-hoa',
        ]);

        $result = app(ExportService::class)->export(
            ExportFixture::query(),
            [new ExportColumn('sensitive_value', 'Thông tin riêng tư', sensitive: true)],
            'SYS',
            $user,
        );

        $path = $result->download->getFile()->getPathname();
        $reader = new Reader;
        $reader->open($path);
        $values = [];

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $values[] = $row->getCells()[0]->getValue();
            }
        }

        $reader->close();
        unlink($path);

        $this->assertSame(['Thông tin riêng tư', 'gia-tri-da-ma-hoa'], $values);
        $this->assertDatabaseHas('audit_logs', ['event' => AuditEvent::ViewSensitive->value]);
    }

    public function test_export_is_denied_without_export_permission_and_sensitive_columns_require_global_view(): void
    {
        $student = $this->userWithRoles('STU');

        try {
            app(ExportService::class)->export(
                ExportFixture::query(),
                [new ExportColumn('name', 'Họ tên')],
                'SYS',
                $student,
            );
            $this->fail('Người dùng không có quyền xuất phải bị từ chối.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }

        $role = app(RoleService::class)->create(['code' => 'SCOPED_EXPORT', 'name' => 'Xuất giới hạn']);
        app(RoleService::class)->syncPermissions($role, [
            'SYS.export' => 'OWN',
            'SYS.view' => 'OWN',
        ]);
        app(RoleService::class)->assign($student, $role);
        app(AccessControl::class)->flush();

        try {
            app(ExportService::class)->export(
                ExportFixture::query(),
                [new ExportColumn('sensitive_value', 'Dữ liệu nhạy cảm', sensitive: true)],
                'SYS',
                $student,
            );
            $this->fail('Cột nhạy cảm phải bị từ chối với quyền xem chỉ trong phạm vi OWN.');
        } catch (HttpException $sensitiveException) {
            $this->assertSame(403, $sensitiveException->getStatusCode());
        }
    }

    public function test_export_query_is_constrained_to_the_users_data_scope(): void
    {
        $student = $this->userWithRoles('STU');
        $role = app(RoleService::class)->create(['code' => 'SCOPED_EXPORT', 'name' => 'Xuất giới hạn']);
        app(RoleService::class)->syncPermissions($role, [
            'SYS.export' => 'OWN',
            'SYS.view' => 'OWN',
        ]);
        app(RoleService::class)->assign($student, $role);
        app(AccessControl::class)->flush();

        ExportFixture::query()->insert([
            ['owner_id' => $student->id, 'name' => 'Của tôi', 'sensitive_value' => 'A'],
            ['owner_id' => $student->id + 1000, 'name' => 'Của người khác', 'sensitive_value' => 'B'],
        ]);

        $result = app(ExportService::class)->export(
            ExportFixture::query(),
            [new ExportColumn('name', 'Tên')],
            'SYS',
            $student,
        );

        $path = $result->download->getFile()->getPathname();
        $reader = new Reader;
        $reader->open($path);
        $values = [];

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $values[] = $row->getCells()[0]->getValue();
            }
        }

        $reader->close();
        unlink($path);

        $this->assertSame(1, $result->rowCount);
        $this->assertSame(['Tên', 'Của tôi'], $values);
    }

    public function test_exports_over_configured_threshold_are_queued_and_owner_can_check_and_download(): void
    {
        $user = $this->userWithRoles('ADMIN');
        Auth::login($user);
        ExportFixture::query()->insert([
            ['owner_id' => $user->id, 'name' => 'Một', 'sensitive_value' => 'A'],
            ['owner_id' => $user->id, 'name' => 'Hai', 'sensitive_value' => 'B'],
        ]);
        config(['studentmanager.export.sync_threshold' => 1]);
        Queue::fake();

        $result = app(ExportService::class)->export(
            ExportFixture::query()->orderBy('id'),
            [new ExportColumn('name', 'Tên')],
            'SYS',
            $user,
        );

        $this->assertTrue($result->isQueued());
        $this->assertSame(2, $result->rowCount);
        Queue::assertPushed(GenerateExportJob::class);
        $job = Queue::pushed(GenerateExportJob::class)->first();
        $job->handle(app(ExportRenderer::class), app(AccessControl::class));

        $this->assertSame('completed', app(ExportService::class)->status($result->requestId, $user)['status']);
        $this->assertSame(Response::HTTP_OK, app(ExportService::class)->download($result->requestId, $user)->getStatusCode());

        $otherUser = $this->userWithRoles('ADMIN');
        try {
            app(ExportService::class)->status($result->requestId, $otherUser);
            $this->fail('Người khác không được xem yêu cầu xuất.');
        } catch (NotFoundHttpException $exception) {
            $this->assertSame(404, $exception->getStatusCode());
        }
        Storage::disk(config('studentmanager.export.disk'))->delete('exports/'.$result->requestId.'.xlsx');
    }

    public function test_ten_thousand_xlsx_rows_export_within_fifteen_seconds(): void
    {
        $user = $this->userWithRoles('ADMIN');
        $rows = [];

        for ($index = 1; $index <= 10000; $index++) {
            $rows[] = ['owner_id' => $user->id, 'name' => 'Sinh viên '.$index, 'sensitive_value' => 'ẩn'];
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('export_fixture')->insert($chunk);
        }

        $startedAt = hrtime(true);
        $result = app(ExportService::class)->export(
            ExportFixture::query()->orderBy('id'),
            [new ExportColumn('name', 'Tên sinh viên')],
            'SYS',
            $user,
        );
        $elapsedSeconds = (hrtime(true) - $startedAt) / 1_000_000_000;

        $this->assertFalse($result->isQueued());
        $this->assertSame(10000, $result->rowCount);
        $this->assertLessThan(15, $elapsedSeconds, 'Xuất 10.000 dòng phải hoàn tất trong tối đa 15 giây.');
        unlink($result->download->getFile()->getPathname());
    }

    public function test_empty_or_duplicate_column_lists_are_rejected_with_vietnamese_remediation(): void
    {
        $user = $this->userWithRoles('ADMIN');

        try {
            app(ExportService::class)->export(ExportFixture::query(), [], 'SYS', $user);
            $this->fail('Danh sách cột trống phải bị từ chối.');
        } catch (BusinessRuleException $exception) {
            $this->assertStringContainsString('ít nhất một cột', $exception->userMessage());
        }

        try {
            app(ExportService::class)->export(
                ExportFixture::query(),
                [new ExportColumn('name', 'Tên'), new ExportColumn('name', 'Tên khác')],
                'SYS',
                $user,
            );
            $this->fail('Cột trùng phải bị từ chối.');
        } catch (BusinessRuleException $exception) {
            $this->assertStringContainsString('chỉ định mỗi cột một lần', $exception->userMessage());
        }
    }
}
