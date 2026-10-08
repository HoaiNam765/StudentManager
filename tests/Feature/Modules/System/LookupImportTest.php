<?php

namespace Tests\Feature\Modules\System;

use App\Models\User;
use App\Modules\System\Enums\AdministrativeScheme;
use App\Modules\System\Enums\ImportStatus;
use App\Modules\System\Models\AdministrativeUnit;
use App\Modules\System\Models\ImportBatch;
use App\Modules\System\Models\LookupCategory;
use App\Modules\System\Models\LookupValue;
use App\Modules\System\Services\ImportService;
use App\Support\Exceptions\BusinessRuleException;
use App\Support\References\ReferenceRegistry;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use Tests\TestCase;

/** Nhập danh mục chuẩn qua trung tâm import (FR-SYS-002 + FR-SYS-007). */
class LookupImportTest extends TestCase
{
    use DatabaseMigrations;

    private ImportService $imports;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        $this->imports = app(ImportService::class);
        $this->admin = User::factory()->create();
        $this->actingAs($this->admin);
    }

    /** File .xlsx thật (OpenSpout) để kiểm tra đường đọc Excel, cột mã ở dạng chuỗi. */
    private function xlsx(array $rows, string $name = 'danh-muc.xlsx'): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'imp').'.xlsx';
        $writer = new Writer;
        $writer->openToFile($path);

        foreach ($rows as $row) {
            $writer->addRow(Row::fromValues($row));
        }

        $writer->close();

        return new UploadedFile($path, $name, null, null, true);
    }

    private function csv(array $rows, string $name = 'danh-muc.csv'): UploadedFile
    {
        $content = "\u{FEFF}".implode("\n", array_map(fn ($row) => implode(',', $row), $rows))."\n";

        return UploadedFile::fake()->createWithContent($name, $content);
    }

    private function uploadAndValidate(string $importer, UploadedFile $file): ImportBatch
    {
        $batch = $this->imports->upload($importer, $file, $this->admin);

        return $this->imports->validate($batch, $this->admin);
    }

    public function test_nhap_gia_tri_danh_muc_tu_excel_chi_luu_dong_hop_le_va_hoan_tac_duoc(): void
    {
        LookupCategory::create(['code' => 'RELIGION', 'name' => 'Tôn giáo', 'is_system' => true])
            ->values()->create(['code' => '01', 'name' => 'Phật giáo', 'sort_order' => 1]);

        $batch = $this->uploadAndValidate('lookup_values', $this->xlsx([
            ['category_code', 'code', 'name', 'sort_order'],
            ['RELIGION', '02', 'Công giáo', '2'],
            ['RELIGION', '03', 'Tin lành', ''],
            ['RELIGION', '01', 'Trùng mã đã có', ''],
            ['KHONG_CO', '01', 'Danh mục không tồn tại', ''],
            ['', '', '', ''], // dòng trống bị bỏ qua
            ['RELIGION', '04', 'Cao Đài', 'x'],
        ]));

        $this->assertSame(ImportStatus::Validated, $batch->status);
        $this->assertSame(5, $batch->total_rows);
        $this->assertSame(2, $batch->valid_rows);

        $errors = $batch->invalidRows()->orderBy('row_number')->get()->mapWithKeys(fn ($row) => [$row->row_number => $row->errors[0]['column']])->all();
        $this->assertSame([4 => 'code', 5 => 'category_code', 7 => 'sort_order'], $errors);

        $batch = $this->imports->save($batch, 'all_valid', $this->admin);
        $this->assertSame(2, $batch->saved_rows);
        $this->assertSame(['01', '02', '03'], LookupValue::query()->orderBy('code')->pluck('code')->all());
        $this->assertSame(3, LookupValue::query()->where('code', '03')->value('sort_order'));

        // Hoàn tác xóa hẳn các giá trị do lô tạo, mã nhập lại được
        $this->imports->rollback($batch, $this->admin);
        $this->assertSame(['01'], LookupValue::withTrashed()->pluck('code')->all());
    }

    public function test_khong_hoan_tac_duoc_khi_gia_tri_da_duoc_dung(): void
    {
        LookupCategory::create(['code' => 'GENDER', 'name' => 'Giới tính', 'is_system' => true]);
        Schema::create('test_people', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gender_id');
        });
        app(ReferenceRegistry::class)->register(LookupValue::class, 'test_people', 'gender_id', 'người');

        $batch = $this->uploadAndValidate('lookup_values', $this->csv([['category_code', 'code', 'name'], ['GENDER', '1', 'Nam'], ['GENDER', '2', 'Nữ']]));
        $batch = $this->imports->save($batch, 'all', $this->admin);
        DB::table('test_people')->insert(['gender_id' => LookupValue::query()->where('code', '1')->value('id')]);

        $this->expectException(BusinessRuleException::class);
        $this->expectExceptionMessage('dữ liệu phụ thuộc');

        $this->imports->rollback($batch, $this->admin);
    }

    public function test_thieu_cot_bat_buoc_thi_lo_that_bai_va_neu_ro_cot_thieu(): void
    {
        $batch = $this->imports->upload('lookup_values', $this->csv([['code', 'name'], ['1', 'Nam']]), $this->admin);

        try {
            $this->imports->validate($batch, $this->admin);
            $this->fail('Thiếu cột phải bị từ chối.');
        } catch (BusinessRuleException $exception) {
            $this->assertStringContainsString('File thiếu cột: category_code', $exception->getMessage());
        }

        $this->assertSame(ImportStatus::Failed, $batch->refresh()->status);
        $this->assertStringContainsString('category_code', $batch->error_summary);
    }

    public function test_file_xls_cu_bi_tu_choi_kem_huong_dan_luu_lai(): void
    {
        $this->expectException(BusinessRuleException::class);
        $this->expectExceptionMessage('Chỉ đọc được file Excel .xlsx hoặc CSV');

        $this->imports->upload('lookup_values', UploadedFile::fake()->create('cu.xls', 5), $this->admin);
    }

    public function test_nhap_tinh_truoc_roi_nhap_xa_o_lo_sau(): void
    {
        $provinces = $this->uploadAndValidate('administrative_units', $this->xlsx([
            ['scheme', 'level', 'code', 'name', 'unit_type', 'parent_code', 'successor_code'],
            ['', 'province', '01', 'Thành phố Hà Nội', 'Thành phố', '', ''],
            ['two_level_2025', 'district', '001', 'Quận Ba Đình', 'Quận', '01', ''],
            ['two_level_2025', 'commune', '00004', 'Phường Ba Đình', 'Phường', '01', ''], // tỉnh chưa có trong hệ thống
        ]));

        $this->assertSame(1, $provinces->valid_rows);
        $this->assertSame(['level', 'parent_code'], $provinces->invalidRows()->orderBy('row_number')->get()->map(fn ($row) => $row->errors[0]['column'])->all());
        $this->imports->save($provinces, 'all_valid', $this->admin);

        $communes = $this->uploadAndValidate('administrative_units', $this->csv([
            ['level', 'code', 'name', 'unit_type', 'parent_code'],
            ['commune', '00004', 'Phường Ba Đình', 'Phường', '01'],
            ['commune', '00008', 'Phường Ngọc Hà', 'Phường', '01'],
        ]));

        $this->assertSame(2, $communes->valid_rows);
        $this->imports->save($communes, 'all', $this->admin);

        $hanoi = AdministrativeUnit::query()->scheme(AdministrativeScheme::TwoLevel2025)->where('code', '01')->first();
        $this->assertSame(['00004', '00008'], $hanoi->children()->orderBy('code')->pluck('code')->all());

        // Tỉnh đã có xã trực thuộc thì lô nhập tỉnh không hoàn tác được nữa
        $this->expectException(BusinessRuleException::class);
        $this->imports->rollback($provinces->refresh(), $this->admin);
    }
}
