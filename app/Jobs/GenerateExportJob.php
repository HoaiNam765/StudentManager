<?php

namespace App\Jobs;

use App\Models\User;
use App\Modules\Auth\Enums\DataScope;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\System\Models\ExportRequest;
use App\Support\Exports\ExportColumn;
use App\Support\Exports\ExportFormat;
use App\Support\Exports\ExportRenderer;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class GenerateExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout;

    /**
     * @param  array<int, mixed>  $bindings
     * @param  list<array{key: string, label: string, sensitive: bool}>  $columns
     * @param  list<string>  $exportScopes
     * @param  list<string>  $viewScopes
     */
    public function __construct(
        public readonly string $requestId,
        public readonly string $databaseConnection,
        public readonly string $sql,
        public readonly array $bindings,
        public readonly array $columns,
        public readonly string $module,
        public readonly int|string $userId,
        public readonly string $format,
        public readonly array $exportScopes,
        public readonly array $viewScopes,
        public readonly string $modelClass,
    ) {
        $this->timeout = (int) config('studentmanager.export.job_timeout_seconds');
    }

    public function handle(ExportRenderer $renderer, AccessControl $access): void
    {
        $request = ExportRequest::query()->findOrFail($this->requestId);
        $user = User::query()->find($this->userId);
        $exportScopes = $user === null ? [] : $this->scopes($access, $user, PermissionAction::Export);
        $viewScopes = $user === null ? [] : $this->scopes($access, $user, PermissionAction::View);

        if (
            $user === null
            || $exportScopes !== $this->exportScopes
            || $viewScopes !== $this->viewScopes
            || $exportScopes === []
        ) {
            throw new AuthorizationException('Quyền xuất dữ liệu đã thay đổi trước khi tác vụ được xử lý.');
        }

        if (
            collect($this->columns)->contains(fn (array $column) => $column['sensitive'])
            && $viewScopes !== [DataScope::All->value]
        ) {
            throw new AuthorizationException('Không còn quyền xuất dữ liệu nhạy cảm.');
        }

        $request->update(['status' => 'running']);
        $format = ExportFormat::fromInput($this->format);
        $columns = array_map(ExportColumn::fromArray(...), $this->columns);
        $tempPath = tempnam(sys_get_temp_dir(), 'studentmanager-export-');

        if ($tempPath === false) {
            throw new RuntimeException('Không thể tạo tệp tạm để xuất dữ liệu.');
        }

        try {
            $rawRows = DB::connection($this->databaseConnection)->cursor($this->sql, $this->bindings);
            $model = new $this->modelClass;

            if (! $model instanceof Model) {
                throw new RuntimeException('Mô hình truy vấn xuất dữ liệu không hợp lệ.');
            }

            $rows = $this->hydrateRows($rawRows, $model);

            match ($format) {
                ExportFormat::Xlsx => $renderer->writeXlsx($rows, $columns, $tempPath),
                ExportFormat::Pdf => $renderer->writePdf($rows, $columns, $tempPath),
                ExportFormat::Csv => $renderer->writeCsv($rows, $columns, $tempPath),
            };

            $relativePath = 'exports/'.$this->requestId.'.'.$format->value;
            $stream = fopen($tempPath, 'rb');

            if ($stream === false) {
                throw new RuntimeException('Không thể mở tệp xuất để lưu trữ.');
            }

            try {
                $stored = Storage::disk(config('studentmanager.export.disk'))->writeStream($relativePath, $stream);
            } finally {
                fclose($stream);
            }

            if (! $stored) {
                throw new RuntimeException('Không thể lưu tệp xuất vào vùng lưu trữ.');
            }

            // Tệp có thể chứa dữ liệu nhạy cảm nên chỉ giữ theo hạn cấu hình; lệnh exports:prune dọn sau đó
            $request->update([
                'status' => 'completed',
                'path' => $relativePath,
                'expires_at' => now()->addDays((int) config('studentmanager.export.retention_days')),
            ]);
        } finally {
            if (is_file($tempPath)) {
                unlink($tempPath);
            }
        }
    }

    public function failed(Throwable $exception): void
    {
        Storage::disk(config('studentmanager.export.disk'))
            ->delete('exports/'.$this->requestId.'.'.$this->format);

        // Lỗi quyền có thông báo dành cho người dùng nên nêu đúng lý do; lỗi khác không để lộ chi tiết kỹ thuật
        $message = $exception instanceof AuthorizationException
            ? $exception->getMessage().' Hãy gửi yêu cầu xuất mới.'
            : 'Tác vụ xuất thất bại. Hãy gửi yêu cầu mới hoặc liên hệ quản trị viên.';

        ExportRequest::query()
            ->whereKey($this->requestId)
            ->update(['status' => 'failed', 'error_message' => $message]);
    }

    /** @return list<string> */
    private function scopes(AccessControl $access, User $user, PermissionAction $action): array
    {
        return array_map(
            fn (DataScope $scope) => $scope->value,
            $access->scopesFor($user, $this->module, $action)
        );
    }

    /**
     * @param  iterable<object>  $rows
     * @return iterable<Model>
     */
    private function hydrateRows(iterable $rows, Model $model): iterable
    {
        foreach ($rows as $row) {
            yield $model->newFromBuilder((array) $row, $this->databaseConnection);
        }
    }
}
