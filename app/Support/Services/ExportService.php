<?php

namespace App\Support\Services;

use App\Jobs\GenerateExportJob;
use App\Models\User;
use App\Modules\Auth\Enums\DataScope;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\System\Models\ExportRequest;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLogger;
use App\Support\Exceptions\BusinessRuleException;
use App\Support\Exports\ExportColumn;
use App\Support\Exports\ExportFormat;
use App\Support\Exports\ExportRenderer;
use App\Support\Exports\ExportResult;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ExportService extends BaseService
{
    public function __construct(
        private readonly AccessControl $access,
        private readonly AuditLogger $audit,
        private readonly ExportRenderer $renderer,
    ) {}

    /**
     * @param  Builder<Model>  $query
     * @param  list<ExportColumn>  $columns
     */
    public function export(
        Builder $query,
        array $columns,
        string $module,
        User $user,
        string $format = 'xlsx',
        string $filename = 'danh-sach',
    ): ExportResult {
        $exportFormat = ExportFormat::fromInput($format);
        $this->validateColumns($columns);
        $this->authorizeExport($user, $module, $columns);

        $scopedQuery = $this->access->constrain(
            clone $query,
            $user,
            $module,
            PermissionAction::Export
        );
        $baseQuery = $scopedQuery->toBase();
        $rowCount = DB::connection($baseQuery->getConnection()->getName())
            ->query()
            ->fromSub(clone $baseQuery, 'export_rows')
            ->count();

        $safeFilename = Str::slug(pathinfo($filename, PATHINFO_FILENAME)) ?: 'danh-sach';
        $syncThreshold = $this->syncThresholdFor($exportFormat);
        $this->assertPdfWithinLimit($exportFormat, $rowCount);

        if ($rowCount > $syncThreshold) {
            return $this->queue(
                $baseQuery,
                $columns,
                $module,
                $user,
                $exportFormat,
                $safeFilename,
                $rowCount,
                $scopedQuery->getModel()::class
            );
        }

        $path = $this->temporaryPath();

        try {
            $rows = $scopedQuery->cursor();
            $this->render($rows, $columns, $exportFormat, $path);

            $response = response()->download(
                $path,
                $safeFilename.'.'.$exportFormat->value,
                ['Content-Type' => $this->contentType($exportFormat)]
            )->deleteFileAfterSend(true);

            $this->recordExport($user, null, $module, $exportFormat, $columns, $rowCount, 'Hoàn tất đồng bộ.');

            return new ExportResult($rowCount, $exportFormat, $response);
        } catch (Throwable $exception) {
            if (is_file($path)) {
                unlink($path);
            }

            throw $exception;
        }
    }

    /** @return array{id: string, status: string, format: string, filename: string, row_count: int, error_message: ?string, expires_at: ?string} */
    public function status(string $requestId, User $user): array
    {
        $request = $this->ownedRequest($requestId, $user);
        $columns = array_map(ExportColumn::fromArray(...), $request->columns);
        $this->authorizeExport($user, $request->module, $columns);
        $this->assertSameScopes($user, $request->module, $request->export_scopes, $request->view_scopes);

        return [
            'id' => $request->id,
            'status' => $request->isExpired() ? 'expired' : $request->status,
            'format' => $request->format,
            'filename' => $request->filename,
            'row_count' => $request->row_count,
            'error_message' => $request->error_message,
            'expires_at' => $request->expires_at?->toIso8601String(),
        ];
    }

    public function download(string $requestId, User $user): Response
    {
        $request = $this->ownedRequest($requestId, $user);
        $columns = array_map(ExportColumn::fromArray(...), $request->columns);
        $this->authorizeExport($user, $request->module, $columns);
        $this->assertSameScopes($user, $request->module, $request->export_scopes, $request->view_scopes);

        if ($request->isExpired()) {
            throw new BusinessRuleException(
                'Tệp xuất đã hết hạn và đã bị xóa.',
                'Tệp chỉ được giữ '.(int) config('studentmanager.export.retention_days').' ngày. Hãy gửi yêu cầu xuất dữ liệu mới.'
            );
        }

        if ($request->status !== 'completed') {
            throw new BusinessRuleException(
                $request->status === 'failed' ? 'Tác vụ xuất dữ liệu đã thất bại.' : 'Tệp xuất chưa sẵn sàng.',
                $request->status === 'failed'
                    ? ($request->error_message ?? 'Hãy gửi yêu cầu xuất mới hoặc liên hệ quản trị viên.')
                    : 'Hãy kiểm tra lại trạng thái yêu cầu; nếu tác vụ thất bại, hãy gửi yêu cầu xuất mới.'
            );
        }

        if (! Storage::disk(config('studentmanager.export.disk'))->exists($request->path)) {
            throw new BusinessRuleException(
                'Tệp xuất không còn tồn tại.',
                'Hãy gửi yêu cầu xuất dữ liệu mới.'
            );
        }

        // Sự kiện riêng (Downloaded) để báo cáo không đếm đôi với lần xuất (Exported)
        $this->audit->record(
            AuditEvent::Downloaded,
            newValues: [
                'request_id' => $request->id,
                'module' => $request->module,
                'format' => $request->format,
                'row_count' => $request->row_count,
            ],
            reason: 'Tải tệp xuất dữ liệu đã hoàn tất.'
        );

        return Storage::disk(config('studentmanager.export.disk'))
            ->download($request->path, $request->filename.'.'.$request->format);
    }

    /**
     * Xóa tệp xuất đã quá hạn giữ và chuyển yêu cầu sang `expired` (chạy hằng ngày bằng lệnh `exports:prune`).
     * Không đổi trạng thái nếu xóa tệp thất bại, để lần chạy sau thử lại.
     *
     * @return int số yêu cầu đã dọn
     */
    public function pruneExpired(): int
    {
        $disk = Storage::disk(config('studentmanager.export.disk'));
        $pruned = 0;

        ExportRequest::query()
            ->where('status', 'completed')
            ->where('expires_at', '<=', now())
            ->chunkById(100, function ($requests) use ($disk, &$pruned): void {
                foreach ($requests as $request) {
                    if ($request->path !== null && ! $disk->delete($request->path)) {
                        report(new \RuntimeException("Không xóa được tệp xuất hết hạn {$request->path}."));

                        continue;
                    }

                    $request->update(['status' => 'expired', 'path' => null]);
                    $pruned++;
                }
            });

        if ($pruned > 0) {
            $this->audit->record(
                AuditEvent::Deleted,
                newValues: ['pruned' => $pruned],
                reason: 'Dọn tệp xuất dữ liệu hết hạn.'
            );
        }

        return $pruned;
    }

    /** PDF có ngưỡng đồng bộ riêng (thấp hơn) vì dựng cả bảng trong bộ nhớ. */
    private function syncThresholdFor(ExportFormat $format): int
    {
        $threshold = (int) config('studentmanager.export.sync_threshold');

        return $format === ExportFormat::Pdf
            ? min($threshold, (int) config('studentmanager.export.pdf_sync_threshold'))
            : $threshold;
    }

    private function assertPdfWithinLimit(ExportFormat $format, int $rowCount): void
    {
        $max = (int) config('studentmanager.export.pdf_max_rows');

        if ($format === ExportFormat::Pdf && $max > 0 && $rowCount > $max) {
            $this->fail(
                'PDF chỉ xuất tối đa '.number_format($max, 0, ',', '.').' dòng, yêu cầu này có '.number_format($rowCount, 0, ',', '.').' dòng.',
                'Hãy xuất Excel (.xlsx) hoặc CSV cho danh sách lớn, hoặc lọc bớt dữ liệu rồi xuất lại PDF.'
            );
        }
    }

    /**
     * @param  list<ExportColumn>  $columns
     */
    private function authorizeExport(User $user, string $module, array $columns): void
    {
        abort_unless(
            $this->access->allows($user, $module, PermissionAction::Export),
            403,
            'Bạn không có quyền xuất dữ liệu của module này.'
        );

        $hasSensitiveColumns = collect($columns)->contains(fn (ExportColumn $column) => $column->sensitive);

        abort_unless(
            ! $hasSensitiveColumns || $this->access->allowsGlobally($user, $module, PermissionAction::View),
            403,
            'Bạn không có quyền xuất dữ liệu nhạy cảm của module này.'
        );
    }

    /**
     * @param  list<ExportColumn>  $columns
     */
    private function validateColumns(array $columns): void
    {
        if ($columns === []) {
            $this->fail('Danh sách cột xuất đang trống.', 'Hãy chọn ít nhất một cột để xuất.');
        }

        foreach ($columns as $column) {
            if (! $column instanceof ExportColumn) {
                $this->fail('Cấu hình cột xuất không hợp lệ.', 'Hãy truyền các cột dưới dạng ExportColumn.');
            }
        }

        $keys = array_map(fn (ExportColumn $column) => $column->key, $columns);

        if (count($keys) !== count(array_unique($keys))) {
            $this->fail('Danh sách cột xuất bị trùng.', 'Hãy chỉ định mỗi cột một lần.');
        }
    }

    /**
     * @param  list<ExportColumn>  $columns
     */
    private function queue(
        QueryBuilder $query,
        array $columns,
        string $module,
        User $user,
        ExportFormat $format,
        string $filename,
        int $rowCount,
        string $modelClass,
    ): ExportResult {
        $exportScopes = $this->scopeValues($user, $module, PermissionAction::Export);
        $viewScopes = $this->scopeValues($user, $module, PermissionAction::View);
        $id = (string) Str::uuid();
        $extension = $format->value;
        $request = ExportRequest::query()->create([
            'id' => $id,
            'user_id' => $user->getKey(),
            'module' => $module,
            'format' => $extension,
            'filename' => $filename,
            'status' => 'queued',
            'row_count' => $rowCount,
            'columns' => array_map(fn (ExportColumn $column) => $column->toArray(), $columns),
            'export_scopes' => $exportScopes,
            'view_scopes' => $viewScopes,
        ]);

        try {
            GenerateExportJob::dispatch(
                $id,
                $query->getConnection()->getName(),
                $query->toSql(),
                $query->getBindings(),
                array_map(fn (ExportColumn $column) => $column->toArray(), $columns),
                $module,
                $user->getKey(),
                $format->value,
                $exportScopes,
                $viewScopes,
                $modelClass,
            );
        } catch (Throwable $exception) {
            $request->delete();

            throw $exception;
        }

        $this->recordExport($user, $request, $module, $format, $columns, $rowCount, 'Đã gửi tác vụ xuất nền.');

        return new ExportResult($rowCount, $format, requestId: $id);
    }

    private function ownedRequest(string $requestId, User $user): ExportRequest
    {
        $request = ExportRequest::query()
            ->where('user_id', $user->getKey())
            ->find($requestId);

        if ($request === null) {
            throw new NotFoundHttpException('Không tìm thấy yêu cầu xuất dữ liệu.');
        }

        return $request;
    }

    /**
     * @param  list<ExportColumn>  $columns
     */
    private function recordExport(
        User $user,
        ?ExportRequest $request,
        string $module,
        ExportFormat $format,
        array $columns,
        int $rowCount,
        string $status,
    ): void {
        $metadata = [
            'request_id' => $request?->id,
            'module' => $module,
            'format' => $format->value,
            'row_count' => $rowCount,
            'columns' => array_map(fn (ExportColumn $column) => $column->key, $columns),
            'status' => $status,
        ];

        $this->audit->record(AuditEvent::Exported, newValues: $metadata, reason: 'Xuất dữ liệu.');

        if (collect($columns)->contains(fn (ExportColumn $column) => $column->sensitive)) {
            $this->audit->record(
                AuditEvent::ViewSensitive,
                newValues: ['module' => $module, 'columns' => $metadata['columns']],
                reason: 'Xuất các cột dữ liệu nhạy cảm.'
            );
        }
    }

    /**
     * @param  iterable<object>  $rows
     * @param  list<ExportColumn>  $columns
     */
    private function render(iterable $rows, array $columns, ExportFormat $format, string $path): void
    {
        match ($format) {
            ExportFormat::Xlsx => $this->renderer->writeXlsx($rows, $columns, $path),
            ExportFormat::Pdf => $this->renderer->writePdf($rows, $columns, $path),
            ExportFormat::Csv => $this->renderer->writeCsv($rows, $columns, $path),
        };
    }

    /** @return list<string> */
    private function scopeValues(User $user, string $module, PermissionAction $action): array
    {
        return array_map(
            fn (DataScope $scope) => $scope->value,
            $this->access->scopesFor($user, $module, $action)
        );
    }

    /**
     * @param  list<string>  $exportScopes
     * @param  list<string>  $viewScopes
     */
    private function assertSameScopes(User $user, string $module, array $exportScopes, array $viewScopes): void
    {
        if (
            $this->scopeValues($user, $module, PermissionAction::Export) !== $exportScopes
            || $this->scopeValues($user, $module, PermissionAction::View) !== $viewScopes
        ) {
            // abort(403) để đi qua bộ xử lý lỗi 403 chung (ghi nhật ký từ chối truy cập, trả 403 chứ không phải 500)
            abort(403, 'Quyền xuất dữ liệu đã thay đổi; hãy gửi yêu cầu xuất mới.');
        }
    }

    private function temporaryPath(): string
    {
        $path = tempnam(sys_get_temp_dir(), 'studentmanager-export-');

        if ($path === false) {
            throw new \RuntimeException('Không thể tạo tệp tạm để xuất dữ liệu.');
        }

        return $path;
    }

    private function contentType(ExportFormat $format): string
    {
        return match ($format) {
            ExportFormat::Xlsx => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ExportFormat::Pdf => 'application/pdf',
            ExportFormat::Csv => 'text/csv; charset=UTF-8',
        };
    }
}
