<?php

namespace Tests\Feature\Support;

use App\Models\User;
use App\Support\Enums\ActiveStatus;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\Support\DemoItem;
use Tests\TestCase;

class StandardModelTest extends TestCase
{
    use DatabaseMigrations; // các test này tự tạo bảng (DDL) nên không dùng được RefreshDatabase trên MySQL

    protected function setUp(): void
    {
        parent::setUp();

        // Bảng thử nghiệm dựng bằng các macro của migration
        Schema::dropIfExists('demo_items');
        Schema::create('demo_items', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->activeStatus();
            $table->searchText();
            $table->standardColumns();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('demo_items');

        parent::tearDown();
    }

    public function test_nguoi_tao_va_nguoi_sua_duoc_ghi_tu_dong(): void
    {
        $creator = User::factory()->create();
        $editor = User::factory()->create();

        $this->actingAs($creator);
        $item = DemoItem::create(['code' => 'A1', 'name' => 'Khoa Công nghệ thông tin']);

        $this->assertSame($creator->id, $item->created_by);
        $this->assertSame($creator->id, $item->updated_by);

        $this->actingAs($editor);
        $item->update(['name' => 'Khoa CNTT']);

        $item->refresh();
        $this->assertSame($creator->id, $item->created_by, 'Người tạo không đổi khi sửa');
        $this->assertSame($editor->id, $item->updated_by);
        $this->assertTrue($item->creator->is($creator));
        $this->assertTrue($item->updater->is($editor));
    }

    public function test_khong_dang_nhap_thi_nguoi_tao_de_trong(): void
    {
        $item = DemoItem::create(['code' => 'A1', 'name' => 'Khoa Toán']);

        $this->assertNull($item->created_by);
        $this->assertNull($item->updated_by);
    }

    public function test_xoa_mem_khong_xoa_vat_ly(): void
    {
        $item = DemoItem::create(['code' => 'A1', 'name' => 'Khoa Toán']);

        $item->delete();

        $this->assertSoftDeleted('demo_items', ['code' => 'A1']);
        $this->assertSame(0, DemoItem::count());
        $this->assertSame(1, DemoItem::withTrashed()->count());
    }

    public function test_trang_thai_mac_dinh_la_hoat_dong_va_co_the_ngung(): void
    {
        $item = DemoItem::create(['code' => 'A1', 'name' => 'Khoa Toán']);
        DemoItem::create(['code' => 'A2', 'name' => 'Khoa Lý'])->deactivate();

        $this->assertTrue($item->fresh()->isActive());
        $this->assertSame(['A1'], DemoItem::active()->pluck('code')->all());
        $this->assertSame(['A2'], DemoItem::inactive()->pluck('code')->all());
        $this->assertSame(ActiveStatus::Inactive, DemoItem::where('code', 'A2')->first()->status);
        $this->assertSame('Ngừng hoạt động', ActiveStatus::Inactive->label());
    }

    public function test_tim_kiem_khong_phan_biet_hoa_thuong_va_dau(): void
    {
        DemoItem::create(['code' => 'SV01', 'name' => 'Đặng Hoài Nam']);
        DemoItem::create(['code' => 'SV02', 'name' => 'Nguyễn Văn An']);
        DemoItem::create(['code' => 'SV03', 'name' => 'Trần Thị Bình']);

        $this->assertSame(['SV01'], DemoItem::search('dang hoai')->pluck('code')->all());
        $this->assertSame(['SV01'], DemoItem::search('ĐẶNG')->pluck('code')->all());
        $this->assertSame(['SV02'], DemoItem::search('NGUYEN an')->pluck('code')->all());
        $this->assertSame(['SV02'], DemoItem::search('sv02')->pluck('code')->all());
        $this->assertCount(3, DemoItem::search('')->get(), 'Từ khóa rỗng thì không lọc');
        $this->assertCount(0, DemoItem::search('%')->get(), 'Ký tự % không được hiểu là ký tự đại diện');
    }

    public function test_cot_tim_kiem_duoc_cap_nhat_khi_sua_ten(): void
    {
        $item = DemoItem::create(['code' => 'SV01', 'name' => 'Đặng Hoài Nam']);

        $item->update(['name' => 'Lê Văn Tám']);

        $this->assertCount(0, DemoItem::search('dang')->get());
        $this->assertCount(1, DemoItem::search('tam')->get());
    }

    public function test_sap_xep_theo_thu_tu_chu_cai_tieng_viet(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            $this->markTestSkipped('Sắp xếp tiếng Việt cần collation utf8mb4_vietnamese_ci của MySQL.');
        }

        foreach (['Đạt', 'Dũng', 'Dương', 'Anh', 'Ân', 'Ba', 'Đức', 'Em'] as $i => $name) {
            DemoItem::create(['code' => 'N'.$i, 'name' => $name]);
        }

        $this->assertSame(
            ['Anh', 'Ân', 'Ba', 'Dũng', 'Dương', 'Đạt', 'Đức', 'Em'],
            DemoItem::orderByVietnamese('name')->pluck('name')->all()
        );
        $this->assertSame(
            ['Em', 'Đức', 'Đạt', 'Dương', 'Dũng', 'Ba', 'Ân', 'Anh'],
            DemoItem::orderByVietnamese('name', 'desc')->pluck('name')->all()
        );
    }
}
