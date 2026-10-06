<?php

namespace Tests\Unit\Modules\System;

use App\Models\User;
use App\Modules\System\Enums\ImportRowStatus;
use App\Modules\System\Enums\ImportStatus;
use App\Modules\System\Jobs\SaveImportBatchJob;
use App\Modules\System\Jobs\ValidateImportBatchJob;
use App\Modules\System\Models\ImportBatch;
use App\Modules\System\Models\ImportRow;
use App\Modules\System\Services\ImportRegistry;
use App\Modules\System\Services\ImportService;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLog;
use App\Support\Exceptions\BusinessRuleException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\Support\FakeImporter;
use Tests\TestCase;

class ImportServiceTest extends TestCase
{
    use DatabaseMigrations;

    private ImportService $service;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        FakeImporter::createTable();

        app(ImportRegistry::class)->register(new FakeImporter);

        $this->service = app(ImportService::class);
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @param  list<array{string, string}>  $rows */
    private function upload(array $rows): ImportBatch
    {
        $file = UploadedFile::fake()->createWithContent('du-lieu.csv', FakeImporter::csv($rows));

        return $this->service->upload('fake', $file, $this->user);
    }

    /** @return list<array{string, string}> */
    private function rows(int $count, array $badAt = []): array
    {
        $rows = [];

        for ($i = 1; $i <= $count; $i++) {
            $rows[] = ["MA{$i}", in_array($i, $badAt, true) ? 'abc' : '5'];
        }

        return $rows;
    }

    private function validated(array $rows): ImportBatch
    {
        return $this->service->validate($this->upload($rows), $this->user);
    }

    private function saved(array $rows): ImportBatch
    {
        return $this->service->save($this->validated($rows), 'all_valid', $this->user);
    }

    public function test_file_100_dong_co_5_dong_loi_chi_luu_95_dong_khi_chon_chi_luu_dong_hop_le(): void
    {
        $batch = $this->validated($this->rows(100, [10, 20, 30, 40, 50]));

        $this->assertSame(ImportStatus::Validated, $batch->status);
        $this->assertSame(100, $batch->total_rows);
        $this->assertSame(95, $batch->valid_rows);
        $this->assertSame(5, $batch->invalid_rows);

        $batch = $this->service->save($batch, 'all_valid', $this->user);

        $this->assertSame(ImportStatus::Saved, $batch->status);
        $this->assertSame(95, $batch->saved_rows);
        $this->assertSame(95, DB::table(FakeImporter::TABLE)->count());
        $this->assertSame(5, $batch->rows()->where('status', ImportRowStatus::Skipped->value)->count());
        $this->assertSame(95, $batch->savedRows()->count());
    }

    public function test_bao_loi_theo_dong_va_cot(): void
    {
        $batch = $this->validated([['MA1', '5'], ['', 'x']]);

        $row = $batch->invalidRows()->first();

        $this->assertSame(3, $row->row_number); // dòng 1 là tiêu đề
        $this->assertSame(['code', 'qty'], array_column($row->errors, 'column'));
        $this->assertSame(2, $row->errorCount());
    }

    public function test_luu_toan_bo_bi_tu_choi_khi_con_dong_loi_va_khong_luu_gi(): void
    {
        $batch = $this->validated($this->rows(10, [3]));

        try {
            $this->service->save($batch, 'all', $this->user);
            $this->fail('Phải từ chối lưu toàn bộ khi có dòng lỗi.');
        } catch (BusinessRuleException $e) {
            $this->assertStringContainsString('1 dòng lỗi', $e->getMessage());
            $this->assertNotEmpty($e->hint());
        }

        $this->assertSame(0, DB::table(FakeImporter::TABLE)->count());
        $this->assertSame(ImportStatus::Validated, $batch->refresh()->status);
    }

    public function test_khong_luu_khi_khong_co_dong_hop_le(): void
    {
        $batch = $this->validated([['', 'x']]);

        $this->expectException(BusinessRuleException::class);

        $this->service->save($batch, 'all_valid', $this->user);
    }

    public function test_file_rong_dat_lo_sang_that_bai_chu_khong_ket_o_dang_kiem_tra(): void
    {
        $batch = $this->upload([]); // chỉ có dòng tiêu đề

        try {
            $this->service->validate($batch, $this->user);
            $this->fail('File rỗng phải bị từ chối.');
        } catch (BusinessRuleException $e) {
            $this->assertStringContainsString('không có dữ liệu', $e->getMessage());
            $this->assertNotEmpty($e->hint());
        }

        $batch->refresh();

        $this->assertSame(ImportStatus::Failed, $batch->status);
        $this->assertSame('File không có dữ liệu.', $batch->error_summary);
        $this->assertNotNull($batch->finished_at);
    }

    public function test_khong_kiem_tra_hai_lan_cung_mot_lo(): void
    {
        $batch = $this->validated($this->rows(3));

        $this->expectException(BusinessRuleException::class);

        $this->service->validate($batch, $this->user);
    }

    public function test_chiem_lo_co_dieu_kien_yeu_cau_dong_thoi_bi_tu_choi(): void
    {
        Bus::fake();
        config(['studentmanager.import.async_threshold' => 2]);

        $stale = $this->upload($this->rows(5));  // bản đọc cũ, vẫn thấy "pending"
        $current = ImportBatch::find($stale->id);

        // Yêu cầu thứ nhất thắng: lô sang "Đang kiểm tra" và job được đẩy đúng một lần
        $this->service->validate($current, $this->user);
        Bus::assertDispatchedTimes(ValidateImportBatchJob::class, 1);

        // Yêu cầu thứ hai còn cầm bản cũ: cập nhật có điều kiện không khớp nên bị từ chối.
        // Đồng hồ nhích về sau để một UPDATE không điều kiện chắc chắn đổi dữ liệu (không bị coi là 0 dòng thay đổi).
        $this->travel(10)->seconds();
        $stale->status = ImportStatus::Pending;

        try {
            $this->service->validate($stale, $this->user);
            $this->fail('Yêu cầu trùng phải bị từ chối.');
        } catch (BusinessRuleException $e) {
            $this->assertStringContainsString('yêu cầu khác', $e->getMessage());
        }

        Bus::assertDispatchedTimes(ValidateImportBatchJob::class, 1);
    }

    public function test_hoan_tac_xoa_ban_ghi_da_luu_va_dua_lo_ve_da_hoan_tac(): void
    {
        $batch = $this->saved($this->rows(10));
        $this->assertSame(10, DB::table(FakeImporter::TABLE)->count());

        $batch = $this->service->rollback($batch, $this->user);

        $this->assertSame(ImportStatus::RolledBack, $batch->status);
        $this->assertSame(0, $batch->saved_rows);
        $this->assertSame(0, DB::table(FakeImporter::TABLE)->count());
        $this->assertSame(0, $batch->savedRows()->count());
        $this->assertSame(10, $batch->rows()->whereNull('saved_model_id')->count());
    }

    public function test_hoan_tac_bi_tu_choi_va_khong_xoa_dong_nao_khi_mot_dong_co_du_lieu_phu_thuoc(): void
    {
        // Dòng 3 (LOCK3) có dữ liệu phụ thuộc; dòng 1-2 hoàn tác được nhưng KHÔNG được xóa dở dang
        $batch = $this->saved([['MA1', '5'], ['MA2', '5'], ['LOCK3', '5'], ['MA4', '5']]);

        try {
            $this->service->rollback($batch, $this->user);
            $this->fail('Phải từ chối hoàn tác.');
        } catch (BusinessRuleException $e) {
            $this->assertStringContainsString('1 dòng đã có dữ liệu phụ thuộc (dòng 4)', $e->getMessage());
            $this->assertNotEmpty($e->hint());
        }

        $this->assertSame(4, DB::table(FakeImporter::TABLE)->count());
        $this->assertSame(ImportStatus::Saved, $batch->refresh()->status);
        $this->assertSame(4, $batch->savedRows()->count());
    }

    public function test_hoan_tac_giu_nguyen_ca_lo_neu_xoa_dong_that_bai_giua_chung(): void
    {
        $batch = $this->saved($this->rows(4));

        // Một bản ghi bị xóa mất sau bước kiểm tra trước nên rollbackRow trả false giữa chừng
        $second = $batch->savedRows()->orderBy('id')->skip(2)->first();
        DB::table(FakeImporter::TABLE)->where('id', $second->saved_model_id)->delete();

        try {
            $this->service->rollback($batch, $this->user);
            $this->fail('Phải từ chối hoàn tác.');
        } catch (BusinessRuleException $e) {
            $this->assertStringContainsString('Không thể hoàn tác dòng', $e->getMessage());
        }

        // 3 dòng còn lại phải còn nguyên, lô vẫn "Đã lưu"
        $this->assertSame(3, DB::table(FakeImporter::TABLE)->count());
        $this->assertSame(ImportStatus::Saved, $batch->refresh()->status);
        $this->assertSame(4, $batch->savedRows()->count());
    }

    public function test_chi_hoan_tac_duoc_lo_da_luu(): void
    {
        $batch = $this->validated($this->rows(2));

        $this->expectException(BusinessRuleException::class);

        $this->service->rollback($batch, $this->user);
    }

    public function test_dong_hop_le_nhung_loi_luc_luu_chi_ghi_loi_rieng_dong_do_o_che_do_chi_luu_hop_le(): void
    {
        $batch = $this->validated([['MA1', '5'], ['TRUNG', '5'], ['MA3', '5'], ['BUSINESS4', '5']]);

        // Sau bước kiểm tra, mã TRUNG bị người khác tạo mất nên lúc lưu trùng khóa
        DB::table(FakeImporter::TABLE)->insert(['code' => 'TRUNG', 'qty' => 1]);

        $batch = $this->service->save($batch, 'all_valid', $this->user);

        $this->assertSame(ImportStatus::Saved, $batch->status);
        $this->assertSame(2, $batch->saved_rows); // MA1 và MA3
        $this->assertSame(2, $batch->valid_rows);
        $this->assertSame(2, $batch->invalid_rows);
        $this->assertStringContainsString('2 dòng hợp lệ nhưng không lưu được', $batch->error_summary);
        $this->assertSame(3, DB::table(FakeImporter::TABLE)->count()); // TRUNG có sẵn + MA1 + MA3

        $errors = $batch->invalidRows()->orderBy('row_number')->get();
        $this->assertSame([3, 5], $errors->pluck('row_number')->all());
        $this->assertStringContainsString('trùng khóa', $errors[0]->errors[0]['message']);
        $this->assertStringContainsString('Dùng mã khác', $errors[1]->errors[0]['message']);
    }

    public function test_che_do_luu_toan_bo_that_bai_thi_khong_dong_nao_duoc_luu(): void
    {
        $batch = $this->validated([['MA1', '5'], ['TRUNG', '5'], ['MA3', '5']]);
        DB::table(FakeImporter::TABLE)->insert(['code' => 'TRUNG', 'qty' => 1]);

        try {
            $this->service->save($batch, 'all', $this->user);
            $this->fail('Phải lỗi do trùng khóa.');
        } catch (\Throwable) {
        }

        $batch->refresh();

        $this->assertSame(ImportStatus::Failed, $batch->status);
        $this->assertStringContainsString('Không có dòng nào được lưu', $batch->error_summary);
        $this->assertSame(1, DB::table(FakeImporter::TABLE)->count()); // chỉ bản ghi có sẵn
        $this->assertSame(0, $batch->savedRows()->count());
    }

    public function test_ma_lo_khong_trung_ke_ca_khi_lo_cuoi_da_bi_xoa_mem(): void
    {
        $first = $this->upload($this->rows(1));
        $second = $this->upload($this->rows(1));

        $this->assertNotSame($first->code, $second->code);
        $this->assertStringEndsWith('-0001', $first->code);
        $this->assertStringEndsWith('-0002', $second->code);

        $this->service->delete($second, $this->user);

        $third = $this->upload($this->rows(1));

        $this->assertStringEndsWith('-0003', $third->code);
    }

    public function test_file_lon_chay_nen_va_ghi_nhat_ky_dung_nguoi_thuc_hien(): void
    {
        config(['studentmanager.import.async_threshold' => 10, 'studentmanager.import.chunk_size' => 4]);
        Bus::fake();

        $batch = $this->upload($this->rows(25, [7]));
        $batch = $this->service->validate($batch, $this->user);

        // Chưa chạy: lô ở "Đang kiểm tra" và job được đẩy kèm người thực hiện
        $this->assertSame(ImportStatus::Validating, $batch->status);
        Bus::assertDispatched(ValidateImportBatchJob::class, fn ($job) => $job->batchId === $batch->id && $job->userId === $this->user->id);

        // Worker chạy job: không có phiên đăng nhập, nhật ký vẫn ghi đúng người
        auth()->forgetUser();
        (new ValidateImportBatchJob($batch->id, $this->user->id))->handle($this->service);

        $batch->refresh();
        $this->assertSame(ImportStatus::Validated, $batch->status);
        $this->assertSame(25, $batch->total_rows);
        $this->assertSame(24, $batch->valid_rows);
        $this->assertSame(50, $batch->progress);
        $this->assertSame(25, $batch->rows()->count());

        $log = AuditLog::query()->where('event', AuditEvent::Updated->value)->latest('id')->first();
        $this->assertSame($this->user->id, $log->user_id);
        $this->assertSame('validated', $log->new_values['status']);

        // Lưu cũng chạy nền, cũng ghi đúng người và không lưu hai lần khi job chạy trùng
        $this->actingAs($this->user);
        $batch = $this->service->save($batch, 'all_valid', $this->user);
        $this->assertSame(ImportStatus::Saving, $batch->status);
        Bus::assertDispatched(SaveImportBatchJob::class, fn ($job) => $job->mode === 'all_valid' && $job->userId === $this->user->id);

        auth()->forgetUser();
        $job = new SaveImportBatchJob($batch->id, 'all_valid', $this->user->id);
        $job->handle($this->service);
        $job->handle($this->service);

        $batch->refresh();
        $this->assertSame(ImportStatus::Saved, $batch->status);
        $this->assertSame(24, $batch->saved_rows);
        $this->assertSame(24, DB::table(FakeImporter::TABLE)->count());

        $imported = AuditLog::query()->where('event', AuditEvent::Imported->value)->get();
        $this->assertCount(1, $imported);
        $this->assertSame($this->user->id, $imported->first()->user_id);
        $this->assertSame(24, $imported->first()->new_values['saved_rows']);
    }

    public function test_job_that_bai_dat_lo_sang_that_bai_nhung_khong_ghi_de_lo_da_xong(): void
    {
        $validated = $this->validated($this->rows(3));

        (new ValidateImportBatchJob($validated->id, $this->user->id))->failed(new \RuntimeException('boom'));
        $this->assertSame(ImportStatus::Validated, $validated->refresh()->status, 'Lô đã kiểm tra xong không bị đánh dấu lỗi');

        $running = $this->upload($this->rows(3));
        $running->update(['status' => ImportStatus::Validating]);

        (new ValidateImportBatchJob($running->id, $this->user->id))->failed(new \RuntimeException('boom'));
        $running->refresh();
        $this->assertSame(ImportStatus::Failed, $running->status);
        $this->assertStringContainsString('boom', $running->error_summary);
    }

    public function test_importer_loi_khi_doc_file_dat_lo_sang_that_bai(): void
    {
        $batch = $this->upload($this->rows(3));
        Storage::disk('local')->delete($batch->path); // file biến mất trước khi kiểm tra

        try {
            $this->service->validate($batch, $this->user);
            $this->fail('Phải lỗi khi không đọc được file.');
        } catch (\Throwable) {
        }

        $this->assertSame(ImportStatus::Failed, $batch->refresh()->status);
        $this->assertStringContainsString('Không đọc được file', $batch->error_summary);
    }

    public function test_xoa_lo_ghi_nhat_ky_va_khong_xoa_duoc_lo_dang_chay_hoac_da_luu(): void
    {
        $pending = $this->upload($this->rows(1));
        $this->service->delete($pending, $this->user);

        $this->assertSoftDeleted('import_batches', ['id' => $pending->id]);
        $this->assertTrue(AuditLog::query()->where('event', AuditEvent::Deleted->value)->where('auditable_id', $pending->id)->exists());

        $saved = $this->saved($this->rows(2));

        try {
            $this->service->delete($saved, $this->user);
            $this->fail('Không được xóa lô đã lưu.');
        } catch (BusinessRuleException $e) {
            $this->assertStringContainsString('Hãy hoàn tác', $e->hint());
        }

        $running = $this->upload($this->rows(1));
        $running->update(['status' => ImportStatus::Validating]);

        $this->expectException(BusinessRuleException::class);
        $this->service->delete($running, $this->user);
    }

    public function test_upload_ghi_nhat_ky_tao_lo(): void
    {
        $batch = $this->upload($this->rows(1));

        $log = AuditLog::query()->where('event', AuditEvent::Created->value)->where('auditable_id', $batch->id)->first();

        $this->assertNotNull($log);
        $this->assertSame($this->user->id, $log->user_id);
    }

    public function test_5000_dong_kiem_tra_va_luu_trong_60_giay(): void
    {
        config(['studentmanager.import.async_threshold' => 100000]); // chạy đồng bộ để đo trọn vẹn

        $batch = $this->upload($this->rows(5000));

        $start = microtime(true);
        $batch = $this->service->validate($batch, $this->user);
        $validateSeconds = microtime(true) - $start;

        $start = microtime(true);
        $batch = $this->service->save($batch, 'all_valid', $this->user);
        $saveSeconds = microtime(true) - $start;

        fwrite(STDERR, sprintf("\n[NFR-PERF-04] 5.000 dòng: kiểm tra %.2fs, lưu %.2fs\n", $validateSeconds, $saveSeconds));

        $this->assertSame(5000, $batch->saved_rows);
        $this->assertSame(5000, ImportRow::query()->where('import_batch_id', $batch->id)->count());
        $this->assertLessThan(60, $validateSeconds + $saveSeconds, 'NFR-PERF-04: 5.000 dòng không quá 60 giây');
    }
}
