<?php

namespace Tests\Feature\Modules\System;

use App\Models\User;
use App\Modules\Auth\Models\Permission;
use App\Modules\Auth\Models\Role;
use App\Modules\System\Enums\ImportStatus;
use App\Modules\System\Models\ImportBatch;
use App\Modules\System\Services\ImportRegistry;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\Support\FakeImporter;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

class ImportApiTest extends TestCase
{
    use DatabaseMigrations;
    use InteractsWithRoles;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        FakeImporter::createTable();
        $this->seedRoles();

        app(ImportRegistry::class)->register(new FakeImporter);
    }

    private function csvFile(array $rows, string $name = 'du-lieu.csv'): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, FakeImporter::csv($rows));
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

    private function uploadAs(User $user, array $rows): int
    {
        return $this->actingAs($user)
            ->postJson('/admin/imports', ['importer' => 'fake', 'file' => $this->csvFile($rows)])
            ->assertCreated()
            ->json('id');
    }

    public function test_khach_chua_dang_nhap_bi_chan(): void
    {
        $this->getJson('/admin/imports')->assertUnauthorized();
        $this->postJson('/admin/imports', [])->assertUnauthorized();
    }

    public function test_sinh_vien_bi_tu_choi_moi_thao_tac(): void
    {
        $batchId = $this->uploadAs($this->userWithRoles('ADMIN'), $this->rows(3));
        $student = $this->userWithRoles('STU');

        $this->actingAs($student);
        $this->getJson('/admin/imports')->assertForbidden();
        $this->getJson('/admin/imports/importers')->assertForbidden();
        $this->getJson("/admin/imports/{$batchId}")->assertForbidden();
        $this->postJson('/admin/imports', ['importer' => 'fake', 'file' => $this->csvFile($this->rows(1))])->assertForbidden();
        $this->postJson("/admin/imports/{$batchId}/validate")->assertForbidden();
        $this->postJson("/admin/imports/{$batchId}/save", ['mode' => 'all_valid'])->assertForbidden();
        $this->postJson("/admin/imports/{$batchId}/rollback")->assertForbidden();
        $this->deleteJson("/admin/imports/{$batchId}")->assertForbidden();

        $this->assertSame(ImportStatus::Pending, ImportBatch::find($batchId)->status);
    }

    public function test_can_bo_dao_tao_chi_xem_khong_tai_len_kiem_tra_luu_hoan_tac_hay_xoa_duoc(): void
    {
        $batchId = $this->uploadAs($this->userWithRoles('ADMIN'), $this->rows(3));
        $acad = $this->userWithRoles('ACAD'); // ma trận SYS của ACAD: chỉ Xem và Sửa

        $this->actingAs($acad);
        $this->getJson('/admin/imports')->assertOk()->assertJsonPath('data.0.id', $batchId);
        $this->getJson("/admin/imports/{$batchId}")->assertOk();
        $this->getJson("/admin/imports/{$batchId}/progress")->assertOk();

        $this->postJson('/admin/imports', ['importer' => 'fake', 'file' => $this->csvFile($this->rows(1))])->assertForbidden();
        $this->postJson("/admin/imports/{$batchId}/validate")->assertForbidden();
        $this->postJson("/admin/imports/{$batchId}/save", ['mode' => 'all_valid'])->assertForbidden();
        $this->postJson("/admin/imports/{$batchId}/rollback")->assertForbidden();
        $this->deleteJson("/admin/imports/{$batchId}")->assertForbidden();
    }

    public function test_quan_tri_chay_tron_luong_chinh_tai_len_kiem_tra_xem_truoc_luu_hoan_tac_xoa(): void
    {
        $admin = $this->userWithRoles('ADMIN');
        $this->actingAs($admin);

        $this->getJson('/admin/imports/importers')->assertOk()->assertJsonPath('0.key', 'fake');

        $id = $this->postJson('/admin/imports', [
            'importer' => 'fake',
            'file' => $this->csvFile($this->rows(20, [4, 9])),
        ])
            ->assertCreated()
            ->assertJsonPath('status', 'pending')
            ->assertJsonPath('original_filename', 'du-lieu.csv')
            ->json('id');

        $this->postJson("/admin/imports/{$id}/validate")
            ->assertOk()
            ->assertJsonPath('status', 'validated')
            ->assertJsonPath('total_rows', 20)
            ->assertJsonPath('valid_rows', 18)
            ->assertJsonPath('invalid_rows', 2);

        $this->getJson("/admin/imports/{$id}/preview")
            ->assertOk()
            ->assertJsonCount(2, 'error_rows')
            ->assertJsonPath('error_rows.0.row_number', 5)
            ->assertJsonPath('error_rows.0.errors.0.column', 'qty');

        $this->postJson("/admin/imports/{$id}/save", ['mode' => 'all_valid'])
            ->assertOk()
            ->assertJsonPath('status', 'saved')
            ->assertJsonPath('saved_rows', 18)
            ->assertJsonPath('can_rollback', true);

        $this->assertSame(18, DB::table(FakeImporter::TABLE)->count());

        $this->getJson('/admin/imports')->assertOk()->assertJsonPath('data.0.status', 'saved');
        $this->getJson("/admin/imports/{$id}/progress")->assertOk()->assertJsonPath('progress', 100);

        $this->postJson("/admin/imports/{$id}/rollback")
            ->assertOk()
            ->assertJsonPath('status', 'rolled_back');

        $this->assertSame(0, DB::table(FakeImporter::TABLE)->count());

        $this->deleteJson("/admin/imports/{$id}")->assertNoContent();
        $this->getJson("/admin/imports/{$id}")->assertNotFound();
    }

    public function test_loi_nghiep_vu_tra_422_kem_cach_khac_phuc(): void
    {
        $this->actingAs($this->userWithRoles('ADMIN'));

        $id = $this->postJson('/admin/imports', ['importer' => 'fake', 'file' => $this->csvFile($this->rows(5, [2]))])->json('id');

        // Lưu khi chưa kiểm tra
        $this->postJson("/admin/imports/{$id}/save", ['mode' => 'all_valid'])
            ->assertStatus(422)
            ->assertJsonPath('message', fn ($m) => str_contains($m, 'không thể thực hiện thao tác này') && str_contains($m, 'chỉ được phép khi lô ở trạng thái'));

        $this->postJson("/admin/imports/{$id}/validate")->assertOk();

        // Lưu toàn bộ khi còn dòng lỗi
        $this->postJson("/admin/imports/{$id}/save", ['mode' => 'all'])
            ->assertStatus(422)
            ->assertJsonPath('message', fn ($m) => str_contains($m, '1 dòng lỗi') && str_contains($m, 'Chỉ lưu dòng hợp lệ'));

        // Chế độ lưu sai bị từ chối ở tầng Request
        $this->postJson("/admin/imports/{$id}/save", ['mode' => 'xyz'])->assertStatus(422)->assertJsonValidationErrors('mode');

        // Hoàn tác khi chưa lưu
        $this->postJson("/admin/imports/{$id}/rollback")->assertStatus(422);

        // File rỗng
        $emptyId = $this->postJson('/admin/imports', ['importer' => 'fake', 'file' => $this->csvFile([])])->json('id');
        $this->postJson("/admin/imports/{$emptyId}/validate")
            ->assertStatus(422)
            ->assertJsonPath('message', fn ($m) => str_contains($m, 'File không có dữ liệu') && str_contains($m, 'tải file mẫu'));
        $this->getJson("/admin/imports/{$emptyId}")->assertOk()->assertJsonPath('batch.status', 'failed');
    }

    public function test_tai_len_sai_dinh_dang_hoac_importer_khong_ton_tai_bi_tu_choi(): void
    {
        $this->actingAs($this->userWithRoles('ADMIN'));

        $this->postJson('/admin/imports', ['importer' => 'fake', 'file' => UploadedFile::fake()->create('a.pdf', 10, 'application/pdf')])
            ->assertStatus(422)
            ->assertJsonValidationErrors('file');

        $this->postJson('/admin/imports', ['importer' => 'khong-co', 'file' => $this->csvFile($this->rows(1))])
            ->assertStatus(422)
            ->assertJsonPath('message', fn ($m) => str_contains($m, "Không tìm thấy Importer 'khong-co'"));

        $this->postJson('/admin/imports', ['importer' => 'fake'])->assertStatus(422)->assertJsonValidationErrors('file');
    }

    public function test_dung_luong_toi_da_lay_tu_cau_hinh(): void
    {
        config(['studentmanager.import.max_file_kb' => 1]);
        $this->actingAs($this->userWithRoles('ADMIN'));

        $this->postJson('/admin/imports', [
            'importer' => 'fake',
            'file' => UploadedFile::fake()->createWithContent('lon.csv', FakeImporter::csv($this->rows(500))),
        ])->assertStatus(422)->assertJsonValidationErrors('file');
    }

    public function test_pham_vi_own_chi_thao_tac_duoc_tren_lo_cua_chinh_minh(): void
    {
        $admin = $this->userWithRoles('ADMIN');
        $adminBatch = $this->uploadAs($admin, $this->rows(2));

        // Cho ACAD quyền Xem + Tạo trên SYS nhưng chỉ ở phạm vi OWN (dữ liệu của chính mình)
        $acadRole = Role::where('code', 'ACAD')->firstOrFail();

        foreach (['view', 'create'] as $action) {
            $permission = Permission::where(['module' => 'SYS', 'action' => $action])->firstOrFail();

            DB::table('role_permissions')->updateOrInsert(
                ['role_id' => $acadRole->id, 'permission_id' => $permission->id],
                ['scope' => 'OWN'],
            );
        }

        $acad = $this->userWithRoles('ACAD');
        $own = $this->uploadAs($acad, $this->rows(2));

        $this->actingAs($acad);

        // Danh sách chỉ có lô của mình
        $this->getJson('/admin/imports')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $own);

        // Lô của mình: xem, kiểm tra được
        $this->getJson("/admin/imports/{$own}")->assertOk();
        $this->postJson("/admin/imports/{$own}/validate")->assertOk()->assertJsonPath('status', 'validated');

        // Lô của người khác: bị từ chối ở mọi thao tác (chống IDOR)
        $this->getJson("/admin/imports/{$adminBatch}")->assertForbidden();
        $this->getJson("/admin/imports/{$adminBatch}/preview")->assertForbidden();
        $this->getJson("/admin/imports/{$adminBatch}/progress")->assertForbidden();
        $this->postJson("/admin/imports/{$adminBatch}/validate")->assertForbidden();
        $this->postJson("/admin/imports/{$adminBatch}/save", ['mode' => 'all_valid'])->assertForbidden();

        $this->assertSame(ImportStatus::Pending, ImportBatch::find($adminBatch)->status);
    }

    public function test_hoan_tac_bi_tu_choi_tra_422_khi_co_du_lieu_phu_thuoc(): void
    {
        $this->actingAs($this->userWithRoles('ADMIN'));

        $id = $this->postJson('/admin/imports', ['importer' => 'fake', 'file' => $this->csvFile([['MA1', '1'], ['LOCK2', '1']])])->json('id');
        $this->postJson("/admin/imports/{$id}/validate")->assertOk();
        $this->postJson("/admin/imports/{$id}/save", ['mode' => 'all'])->assertOk()->assertJsonPath('saved_rows', 2);

        $this->postJson("/admin/imports/{$id}/rollback")
            ->assertStatus(422)
            ->assertJsonPath('message', fn ($m) => str_contains($m, 'dữ liệu phụ thuộc') && str_contains($m, 'Hãy xử lý'));

        $this->assertSame(2, DB::table(FakeImporter::TABLE)->count());
    }
}
