<?php

namespace Tests\Feature\Modules\Teacher;

use App\Models\User;
use App\Modules\Faculty\Models\Department;
use App\Modules\Faculty\Models\Faculty;
use App\Modules\System\Enums\ImportStatus;
use App\Modules\System\Models\ImportBatch;
use App\Modules\System\Services\ImportService;
use App\Modules\Teacher\Models\Lecturer;
use App\Modules\Teacher\Services\LecturerService;
use App\Support\Exceptions\BusinessRuleException;
use Database\Seeders\FacultySeeder;
use Database\Seeders\TeacherSeeder;
use DateTimeImmutable;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Writer\XLSX\Writer;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

/** Nhập và xuất danh sách giảng viên (FR-TCH-013, GC-07). */
class LecturerImportExportTest extends TestCase
{
    use DatabaseMigrations;
    use InteractsWithRoles;

    private ImportService $imports;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        Storage::fake('local');
        $this->seedRoles();
        $this->seed(FacultySeeder::class);
        $this->imports = app(ImportService::class);
        $this->admin = $this->userWithRoles('ADMIN');
        $this->actingAs($this->admin);
    }

    /** File .xlsx thật; ô kiểu DateTimeImmutable được ghi thành ô ngày như khi người dùng gõ ngày trong Excel. */
    private function xlsx(array $rows): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'gv').'.xlsx';
        $writer = new Writer;
        $writer->openToFile($path);
        $dateStyle = (new Style)->setFormat('dd/mm/yyyy');

        foreach ($rows as $row) {
            $writer->addRow(new Row(array_map(
                fn ($value) => $value instanceof DateTimeImmutable ? Cell::fromValue($value, $dateStyle) : Cell::fromValue($value),
                $row,
            )));
        }

        $writer->close();

        return new UploadedFile($path, 'giang-vien.xlsx', null, null, true);
    }

    private function validated(UploadedFile $file): ImportBatch
    {
        return $this->imports->validate($this->imports->upload('lecturers', $file, $this->admin), $this->admin);
    }

    public function test_nhap_giang_vien_bao_loi_theo_dong_cot_va_cap_tai_khoan(): void
    {
        app(LecturerService::class)->create(['code' => 'GV0900', 'full_name' => 'Đã có', 'department_id' => Department::query()->value('id'), 'account' => 'none']);

        $batch = $this->validated($this->xlsx([
            ['code', 'full_name', 'department_code', 'email', 'academic_title', 'academic_degree', 'hired_on', 'date_of_birth', 'national_id'],
            ['gv0901', 'Nguyễn Thị Hoa', 'BM-KTPM', 'gv0901@studentmanager.test', 'Giảng viên chính', 'Tiến sĩ', '01/09/2015', new DateTimeImmutable('1985-03-20'), '001185000901'],
            ['GV0902', 'Trần Văn Bảo', 'BM-HTTT', '', 'lecturer', 'master', '2020-09-01', '', ''],
            ['GV0900', 'Trùng mã', 'BM-KTPM', '', '', '', '', '', ''],
            ['GV0903', 'Sai bộ môn', 'BM-KHONGCO', '', '', '', '', '', ''],
            ['GV0904', 'Sai chức danh, ngày', 'BM-MMT', 'khong-phai-email', 'Trưởng khoa', '', '31/02/2020', '', '123'],
        ]));

        $this->assertSame(ImportStatus::Validated, $batch->status);
        $this->assertSame(5, $batch->total_rows);
        $this->assertSame(2, $batch->valid_rows);

        $errors = $batch->invalidRows()->orderBy('row_number')->get()
            ->mapWithKeys(fn ($row) => [$row->row_number => array_column($row->errors, 'column')])->all();
        $this->assertSame([4 => ['code'], 5 => ['department_code'], 6 => ['email', 'academic_title', 'hired_on', 'national_id']], $errors);

        $batch = $this->imports->save($batch, 'all_valid', $this->admin);
        $this->assertSame(2, $batch->saved_rows);

        $hoa = Lecturer::query()->where('code', 'GV0901')->firstOrFail();
        $this->assertSame('senior_lecturer', $hoa->academic_title->value);
        $this->assertSame('doctor', $hoa->academic_degree->value);
        $this->assertSame('2015-09-01', $hoa->hired_on->toDateString());
        $this->assertSame('1985-03-20', $hoa->date_of_birth->toDateString());   // ô ngày của Excel
        $this->assertSame('GV0901', $hoa->user->username);
        $this->assertTrue($hoa->user->hasRole('LEC'));

        $bao = Lecturer::query()->where('code', 'GV0902')->firstOrFail();
        $this->assertNull($bao->user_id);   // không có email: chưa cấp tài khoản
    }

    public function test_hoan_tac_xoa_han_ho_so_va_tai_khoan_cua_lo(): void
    {
        $batch = $this->validated($this->xlsx([
            ['code', 'full_name', 'department_code', 'email'],
            ['GV0911', 'Lê Văn Một', 'BM-KTPM', 'gv0911@studentmanager.test'],
            ['GV0912', 'Lê Văn Hai', 'BM-KTPM', ''],
        ]));
        $batch = $this->imports->save($batch, 'all', $this->admin);
        $userId = Lecturer::query()->where('code', 'GV0911')->value('user_id');

        $this->imports->rollback($batch, $this->admin);

        $this->assertSame(0, Lecturer::withTrashed()->whereIn('code', ['GV0911', 'GV0912'])->count());
        $this->assertNull(User::query()->find($userId));

        // Mã được nhập lại sau khi hoàn tác
        $again = $this->validated($this->xlsx([['code', 'full_name', 'department_code'], ['GV0911', 'Lê Văn Một', 'BM-KTPM']]));
        $this->assertSame(1, $again->valid_rows);
    }

    public function test_khong_hoan_tac_khi_giang_vien_da_phat_sinh_du_lieu(): void
    {
        $batch = $this->imports->save($this->validated($this->xlsx([
            ['code', 'full_name', 'department_code'],
            ['GV0921', 'Phạm Văn Ba', 'BM-KTPM'],
        ])), 'all', $this->admin);

        $lecturer = Lecturer::query()->where('code', 'GV0921')->firstOrFail();
        app(LecturerService::class)->addUnit($lecturer, ['unit_type' => 'department', 'unit_id' => Department::query()->where('code', 'BM-HTTT')->value('id')]);

        $this->expectException(BusinessRuleException::class);
        $this->imports->rollback($batch, $this->admin);
    }

    public function test_xuat_theo_khoa_va_chuc_danh_chi_nguoi_co_quyen_xuat(): void
    {
        $this->seed(TeacherSeeder::class);

        $this->actingAs($this->userWithRoles('LEC'));
        $this->get('/admin/lecturers/export')->assertForbidden();

        $this->actingAs($this->userWithRoles('ACAD'));
        $cntt = Faculty::query()->where('code', 'CNTT')->value('id');
        $response = $this->get("/admin/lecturers/export?faculty_id={$cntt}&academic_title=senior_lecturer");
        $response->assertOk();

        $reader = new Reader;
        $reader->open($response->getFile()->getPathname());
        $rows = [];

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $rows[] = array_map(fn ($cell) => $cell->getValue(), $row->getCells());
            }
        }

        $reader->close();

        $this->assertSame(['Mã giảng viên', 'Họ và tên', 'Mã bộ môn', 'Bộ môn', 'Khoa'], array_slice($rows[0], 0, 5));
        $this->assertSame(['GV0001', 'GV0007'], array_column(array_slice($rows, 1), 0));
        $this->assertSame('Khoa Công nghệ thông tin', $rows[1][4]);
        $this->assertSame('Giảng viên chính', $rows[1][5]);
    }
}
