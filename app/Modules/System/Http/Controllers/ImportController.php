<?php

namespace App\Modules\System\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\System\Http\Requests\SaveImportRequest;
use App\Modules\System\Http\Requests\UploadImportRequest;
use App\Modules\System\Models\ImportBatch;
use App\Modules\System\Models\ImportRow;
use App\Modules\System\Services\ImportRegistry;
use App\Modules\System\Services\ImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * API Trung tâm Import (FR-SYS-007).
 * Quy trình: upload → validate → preview → save → [rollback].
 *
 * Phân quyền được kiểm tra ở tầng Policy (ImportBatchPolicy).
 */
class ImportController extends Controller
{
    public function __construct(
        private readonly ImportService $importService,
        private readonly ImportRegistry $registry,
    ) {}

    /**
     * Danh sách Importer đã đăng ký (để FE hiển thị lựa chọn).
     *
     * GET /admin/imports/importers
     */
    public function importers(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ImportBatch::class);

        $all = $this->registry->all();

        $result = collect($all)->map(fn ($importer) => [
            'key' => $importer->key(),
            'label' => $importer->label(),
            'description' => $importer->description(),
            'template_url' => $importer->templateUrl(),
        ])->values();

        return response()->json($result);
    }

    /**
     * Danh sách lô import (lịch sử).
     *
     * GET /admin/imports
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ImportBatch::class);

        $batches = ImportBatch::query()
            ->with('createdBy:id,name')
            ->when($request->filled('importer'), fn ($q) => $q->where('importer', $request->input('importer')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->orderByDesc('id')
            ->paginate(20);

        return response()->json($batches->through(fn (ImportBatch $batch) => $this->presentBatch($batch)));
    }

    /**
     * Chi tiết lô import + danh sách dòng có lỗi.
     *
     * GET /admin/imports/{batch}
     */
    public function show(Request $request, ImportBatch $batch): JsonResponse
    {
        $this->authorize('view', $batch);

        $errorRows = $batch->invalidRows()
            ->orderBy('row_number')
            ->paginate(100);

        return response()->json([
            'batch' => $this->presentBatch($batch),
            'error_rows' => $errorRows->through(fn (ImportRow $row) => $this->presentRow($row)),
        ]);
    }

    /**
     * Bước 1: Tải file lên và tạo lô.
     *
     * POST /admin/imports
     */
    public function upload(UploadImportRequest $request): JsonResponse
    {
        $batch = $this->importService->upload(
            $request->input('importer'),
            $request->file('file'),
            $request->user(),
        );

        return response()->json($this->presentBatch($batch), 201);
    }

    /**
     * Bước 2: Kích hoạt kiểm tra dữ liệu từng dòng.
     *
     * POST /admin/imports/{batch}/validate
     */
    public function validateBatch(Request $request, ImportBatch $batch): JsonResponse
    {
        $this->authorize('validate', $batch);

        $batch = $this->importService->validate($batch);

        return response()->json($this->presentBatch($batch));
    }

    /**
     * Bước 3: Xem trước kết quả kiểm tra.
     *
     * GET /admin/imports/{batch}/preview
     */
    public function preview(Request $request, ImportBatch $batch): JsonResponse
    {
        $this->authorize('preview', $batch);

        ['batch' => $batch, 'error_rows' => $errorRows] = $this->importService->preview($batch);

        return response()->json([
            'batch' => $this->presentBatch($batch),
            'error_rows' => $errorRows->map(fn (ImportRow $row) => $this->presentRow($row))->values(),
        ]);
    }

    /**
     * Bước 4: Xác nhận lưu lô.
     *
     * POST /admin/imports/{batch}/save
     * Body: { "mode": "all_valid" | "all" }
     */
    public function save(SaveImportRequest $request, ImportBatch $batch): JsonResponse
    {
        $batch = $this->importService->save($batch, $request->input('mode'), $request->user());

        return response()->json($this->presentBatch($batch));
    }

    /**
     * Bước 5: Hoàn tác lô (nếu chưa có dữ liệu phụ thuộc).
     *
     * POST /admin/imports/{batch}/rollback
     */
    public function rollback(Request $request, ImportBatch $batch): JsonResponse
    {
        $this->authorize('rollback', $batch);

        $batch = $this->importService->rollback($batch, $request->user());

        return response()->json($this->presentBatch($batch));
    }

    /**
     * Xóa mềm lô import.
     *
     * DELETE /admin/imports/{batch}
     */
    public function destroy(Request $request, ImportBatch $batch): Response
    {
        $this->authorize('delete', $batch);
        $batch->delete();

        return response()->noContent();
    }

    /**
     * Kiểm tra tiến trình của lô (dùng cho polling khi chạy nền).
     *
     * GET /admin/imports/{batch}/progress
     */
    public function progress(Request $request, ImportBatch $batch): JsonResponse
    {
        $this->authorize('view', $batch);

        return response()->json([
            'id' => $batch->id,
            'code' => $batch->code,
            'status' => $batch->status->value,
            'status_label' => $batch->status->label(),
            'progress' => $batch->progress,
            'total_rows' => $batch->total_rows,
            'valid_rows' => $batch->valid_rows,
            'invalid_rows' => $batch->invalid_rows,
            'saved_rows' => $batch->saved_rows,
            'error_summary' => $batch->error_summary,
        ]);
    }

    // -------------------------------------------------------------------------
    // Presenters
    // -------------------------------------------------------------------------

    /** @return array<string, mixed> */
    private function presentBatch(ImportBatch $batch): array
    {
        return [
            'id' => $batch->id,
            'code' => $batch->code,
            'importer' => $batch->importer,
            'original_filename' => $batch->original_filename,
            'status' => $batch->status->value,
            'status_label' => $batch->status->label(),
            'total_rows' => $batch->total_rows,
            'valid_rows' => $batch->valid_rows,
            'invalid_rows' => $batch->invalid_rows,
            'saved_rows' => $batch->saved_rows,
            'save_mode' => $batch->save_mode,
            'progress' => $batch->progress,
            'error_summary' => $batch->error_summary,
            'can_rollback' => $batch->canRollback(),
            'started_at' => $batch->started_at?->toIso8601String(),
            'finished_at' => $batch->finished_at?->toIso8601String(),
            'created_at' => $batch->created_at?->toIso8601String(),
            'created_by' => $batch->created_by,
        ];
    }

    /** @return array<string, mixed> */
    private function presentRow(ImportRow $row): array
    {
        return [
            'id' => $row->id,
            'row_number' => $row->row_number,
            'raw_data' => $row->raw_data,
            'status' => $row->status->value,
            'status_label' => $row->status->label(),
            'errors' => $row->errors ?? [],
            'error_count' => $row->errorCount(),
        ];
    }
}
