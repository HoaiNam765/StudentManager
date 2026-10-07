<?php

namespace Tests\Feature\Support;

use App\Jobs\GenerateExportJob;
use App\Models\User;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Auth\Services\RoleService;
use App\Modules\System\Models\ExportRequest;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLog;
use App\Support\Exceptions\BusinessRuleException;
use App\Support\Exports\ExportColumn;
use App\Support\Exports\ExportRenderer;
use App\Support\Exports\ExportResult;
use App\Support\Services\ExportService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

    /**
     * Xếp một tác vụ xuất nền (chưa chạy job) cho $user.
     *
     * @return array{0: ExportResult, 1: GenerateExportJob}
     */
    private function queueExport(User $user, string $format = 'xlsx', int $rows = 2): array
    {
        Storage::fake(config('studentmanager.export.disk'));
        Queue::fake();
        config(['studentmanager.export.sync_threshold' => 1]);

        for ($i = 1; $i <= $rows; $i++) {
            ExportFixture::query()->create(['owner_id' => $user->id, 'name' => "Dòng {$i}", 'sensitive_value' => 'A']);
        }

        $result = app(ExportService::class)->export(
            ExportFixture::query()->orderBy('id'),
            [new ExportColumn('name', 'Tên')],
            'SYS',
            $user,
            format: $format,
        );

        $this->assertTrue($result->isQueued());

        return [$result, Queue::pushed(GenerateExportJob::class)->first()];
    }

    private function runJob(GenerateExportJob $job): void
    {
        $job->handle(app(ExportRenderer::class), app(AccessControl::class));
    }

    /** Người dùng có quyền xuất/xem SYS ở phạm vi riêng, tách khỏi ma trận mặc định. */
    private function userWithExportScopes(string $export, string $view): array
    {
        $user = $this->userWithRoles('STU');
        $role = app(RoleService::class)->create(['code' => 'EXPORT_TEST', 'name' => 'Xuất thử']);
        app(RoleService::class)->syncPermissions($role, ['SYS.export' => $export, 'SYS.view' => $view]);
        app(RoleService::class)->assign($user, $role);
        app(AccessControl::class)->flush();

        return [$user, $role];
    }

    public function test_doi_quyen_sau_khi_xep_hang_bi_tu_choi_403_o_status_download_va_job(): void
    {
        [$user, $role] = $this->userWithExportScopes('ALL', 'ALL');
        [$result, $job] = $this->queueExport($user);
        $this->runJob($job);

        // Đang đủ quyền: xem trạng thái và tải bình thường
        $this->assertSame('completed', app(ExportService::class)->status($result->requestId, $user)['status']);

        // Phạm vi xuất bị thu hẹp sau khi xếp hàng
        app(RoleService::class)->syncPermissions($role, ['SYS.export' => 'OWN', 'SYS.view' => 'ALL']);
        app(AccessControl::class)->flush();

        foreach (['status', 'download'] as $method) {
            try {
                app(ExportService::class)->{$method}($result->requestId, $user);
                $this->fail("{$method}() phải bị từ chối khi quyền đã đổi.");
            } catch (HttpException $exception) {
                // 403 chứ không phải lỗi 500 "Class not found"
                $this->assertSame(403, $exception->getStatusCode(), $method);
                $this->assertStringContainsString('Quyền xuất dữ liệu đã thay đổi', $exception->getMessage());
            }
        }

        // Job được xếp hàng với phạm vi cũ nhưng chạy khi quyền đã đổi
        $request = ExportRequest::query()->findOrFail($result->requestId);
        $request->update(['status' => 'queued', 'path' => null]);

        try {
            $this->runJob($job);
            $this->fail('Job phải từ chối khi quyền đã đổi.');
        } catch (AuthorizationException $exception) {
            $job->failed($exception);
        }

        $request->refresh();
        $this->assertSame('failed', $request->status);
        $this->assertStringContainsString('Quyền xuất dữ liệu đã thay đổi trước khi tác vụ được xử lý', $request->error_message);
        $this->assertStringContainsString('gửi yêu cầu xuất mới', $request->error_message);
    }

    public function test_job_bao_dung_ly_do_khi_mat_quyen_va_khong_lo_chi_tiet_ky_thuat_o_loi_khac(): void
    {
        [$user, $role] = $this->userWithExportScopes('ALL', 'ALL');
        [$result, $job] = $this->queueExport($user);

        // Mất toàn bộ quyền xuất
        app(RoleService::class)->syncPermissions($role, []);
        app(AccessControl::class)->flush();

        try {
            $this->runJob($job);
            $this->fail('Mất quyền xuất thì job phải bị từ chối.');
        } catch (AuthorizationException $exception) {
            $this->assertStringContainsString('Quyền xuất dữ liệu đã thay đổi', $exception->getMessage());
        }

        $job->failed(new \RuntimeException('SQLSTATE[HY000] chi tiết nội bộ'));

        $message = ExportRequest::query()->findOrFail($result->requestId)->error_message;
        $this->assertStringNotContainsString('SQLSTATE', $message);
        $this->assertStringContainsString('liên hệ quản trị viên', $message);
    }

    public function test_pdf_co_nguong_dong_bo_rieng_va_chay_nen_khi_vuot_nguong(): void
    {
        $user = $this->userWithRoles('ADMIN');
        Queue::fake();
        config([
            'studentmanager.export.sync_threshold' => 50000,
            'studentmanager.export.pdf_sync_threshold' => 1,
        ]);
        ExportFixture::query()->insert([
            ['owner_id' => $user->id, 'name' => 'Một', 'sensitive_value' => 'A'],
            ['owner_id' => $user->id, 'name' => 'Hai', 'sensitive_value' => 'B'],
        ]);
        $columns = [new ExportColumn('name', 'Tên')];

        $pdf = app(ExportService::class)->export(ExportFixture::query(), $columns, 'SYS', $user, format: 'pdf');

        $this->assertTrue($pdf->isQueued(), 'PDF vượt ngưỡng PDF phải chạy nền dù chưa vượt ngưỡng chung');
        Queue::assertPushed(GenerateExportJob::class, fn ($job) => $job->format === 'pdf');

        // Cùng số dòng nhưng Excel vẫn đồng bộ (ngưỡng chung 50.000)
        $xlsx = app(ExportService::class)->export(ExportFixture::query(), $columns, 'SYS', $user, format: 'xlsx');

        $this->assertFalse($xlsx->isQueued());
        unlink($xlsx->download->getFile()->getPathname());
        Queue::assertPushed(GenerateExportJob::class, 1);
    }

    public function test_pdf_vuot_muc_tran_bi_tu_choi_kem_cach_khac_phuc_con_excel_csv_thi_khong(): void
    {
        $user = $this->userWithRoles('ADMIN');
        Queue::fake();
        config(['studentmanager.export.pdf_max_rows' => 1]);
        ExportFixture::query()->insert([
            ['owner_id' => $user->id, 'name' => 'Một', 'sensitive_value' => 'A'],
            ['owner_id' => $user->id, 'name' => 'Hai', 'sensitive_value' => 'B'],
        ]);
        $columns = [new ExportColumn('name', 'Tên')];

        try {
            app(ExportService::class)->export(ExportFixture::query(), $columns, 'SYS', $user, format: 'pdf');
            $this->fail('PDF vượt mức trần phải bị từ chối.');
        } catch (BusinessRuleException $exception) {
            $this->assertStringContainsString('PDF chỉ xuất tối đa 1 dòng, yêu cầu này có 2 dòng', $exception->getMessage());
            $this->assertStringContainsString('Excel (.xlsx) hoặc CSV', $exception->hint());
        }

        Queue::assertNothingPushed();

        foreach (['xlsx', 'csv'] as $format) {
            $result = app(ExportService::class)->export(ExportFixture::query(), $columns, 'SYS', $user, format: $format);
            $this->assertSame(2, $result->rowCount);
            unlink($result->download->getFile()->getPathname());
        }
    }

    public function test_tep_xuat_chi_duoc_giu_theo_han_het_han_thi_bao_ro_va_lenh_don_xoa_tep(): void
    {
        $user = $this->userWithRoles('ADMIN');
        config(['studentmanager.export.retention_days' => 3]);
        [$result, $job] = $this->queueExport($user);
        $this->runJob($job);

        $request = ExportRequest::query()->findOrFail($result->requestId);
        $disk = Storage::disk(config('studentmanager.export.disk'));

        $this->assertSame(now()->addDays(3)->toDateString(), $request->expires_at->toDateString());
        $this->assertSame($request->expires_at->toIso8601String(), app(ExportService::class)->status($request->id, $user)['expires_at']);
        $this->assertTrue($disk->exists($request->path));

        // Trong hạn: tải được, ghi sự kiện Downloaded và không ghi thêm Exported
        $this->assertSame(Response::HTTP_OK, app(ExportService::class)->download($request->id, $user)->getStatusCode());
        $this->assertSame(1, AuditLog::query()->where('event', AuditEvent::Exported)->count());
        $this->assertSame(1, AuditLog::query()->where('event', AuditEvent::Downloaded)->count());

        // Quá hạn nhưng lệnh dọn chưa chạy: vẫn bị chặn, tệp chưa xóa
        $this->travel(4)->days();
        $this->assertSame('expired', app(ExportService::class)->status($request->id, $user)['status']);

        try {
            app(ExportService::class)->download($request->id, $user);
            $this->fail('Tệp quá hạn không được tải.');
        } catch (BusinessRuleException $exception) {
            $this->assertStringContainsString('đã hết hạn', $exception->getMessage());
            $this->assertStringContainsString('3 ngày', $exception->hint());
        }

        $this->assertTrue($disk->exists($request->path));
        $this->assertSame(1, AuditLog::query()->where('event', AuditEvent::Downloaded)->count());

        // Lệnh dọn xóa tệp và chuyển yêu cầu sang expired
        $path = $request->path;
        $this->artisan('exports:prune')->expectsOutput('Đã dọn 1 tệp xuất hết hạn.')->assertSuccessful();

        $request->refresh();
        $this->assertSame('expired', $request->status);
        $this->assertNull($request->path);
        $this->assertFalse($disk->exists($path));
        $this->assertTrue(AuditLog::query()->where('event', AuditEvent::Deleted)->where('reason', 'Dọn tệp xuất dữ liệu hết hạn.')->exists());

        // Chạy lại không làm gì thêm
        $this->artisan('exports:prune')->expectsOutput('Đã dọn 0 tệp xuất hết hạn.')->assertSuccessful();

        try {
            app(ExportService::class)->download($request->id, $user);
            $this->fail('Yêu cầu đã expired không được tải.');
        } catch (BusinessRuleException $exception) {
            $this->assertStringContainsString('đã hết hạn', $exception->getMessage());
        }
    }

    public function test_lenh_don_chi_dong_vao_tep_da_hoan_tat_va_qua_han(): void
    {
        Storage::fake(config('studentmanager.export.disk'));
        $user = $this->userWithRoles('ADMIN');
        $disk = Storage::disk(config('studentmanager.export.disk'));

        $make = fn (string $status, ?\DateTimeInterface $expiresAt, ?string $path) => ExportRequest::query()->create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'module' => 'SYS',
            'format' => 'xlsx',
            'filename' => 'thu',
            'status' => $status,
            'row_count' => 1,
            'columns' => [],
            'export_scopes' => ['ALL'],
            'view_scopes' => ['ALL'],
            'path' => $path,
            'expires_at' => $expiresAt,
        ]);

        $disk->put('exports/con-han.xlsx', 'x');
        $disk->put('exports/da-het-han.xlsx', 'x');
        $stillValid = $make('completed', now()->addDay(), 'exports/con-han.xlsx');
        $expired = $make('completed', now()->subMinute(), 'exports/da-het-han.xlsx');
        $running = $make('running', now()->subDay(), null);
        $failed = $make('failed', now()->subDay(), null);

        $this->assertSame(1, app(ExportService::class)->pruneExpired());

        $this->assertSame('completed', $stillValid->refresh()->status);
        $this->assertTrue($disk->exists('exports/con-han.xlsx'));
        $this->assertSame('expired', $expired->refresh()->status);
        $this->assertFalse($disk->exists('exports/da-het-han.xlsx'));
        $this->assertSame('running', $running->refresh()->status);
        $this->assertSame('failed', $failed->refresh()->status);
    }

    public function test_lenh_don_duoc_len_lich_hang_ngay(): void
    {
        $this->artisan('schedule:list')->expectsOutputToContain('exports:prune')->assertSuccessful();
    }
}
