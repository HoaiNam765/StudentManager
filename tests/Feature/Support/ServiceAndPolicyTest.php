<?php

namespace Tests\Feature\Support;

use App\Models\User;
use App\Support\Exceptions\BusinessRuleException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\Support\DemoItem;
use Tests\Support\DemoItemPolicy;
use Tests\Support\DemoItemService;
use Tests\TestCase;

class ServiceAndPolicyTest extends TestCase
{
    use DatabaseMigrations; // các test này tự tạo bảng (DDL) nên không dùng được RefreshDatabase trên MySQL

    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('demo_items');
        Schema::create('demo_items', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->activeStatus();
            $table->searchText();
            $table->standardColumns();
        });

        Gate::policy(DemoItem::class, DemoItemPolicy::class);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('demo_items');

        parent::tearDown();
    }

    // ---- Service ----

    public function test_service_tu_choi_theo_quy_tac_nghiep_vu_kem_cach_khac_phuc(): void
    {
        $item = DemoItem::create(['code' => 'CORE', 'name' => 'Mục hệ thống']);

        try {
            app(DemoItemService::class)->deactivate($item);
            $this->fail('Phải ném BusinessRuleException');
        } catch (BusinessRuleException $e) {
            $this->assertSame('Không thể ngừng hoạt động mục hệ thống.', $e->getMessage());
            $this->assertSame(
                'Không thể ngừng hoạt động mục hệ thống. Hãy chọn mục khác hoặc liên hệ quản trị viên.',
                $e->userMessage()
            );
        }

        $this->assertTrue($item->fresh()->isActive(), 'Bị từ chối thì dữ liệu không đổi');
    }

    public function test_service_thuc_hien_khi_hop_le(): void
    {
        $item = DemoItem::create(['code' => 'A1', 'name' => 'Khoa Toán']);

        app(DemoItemService::class)->deactivate($item);

        $this->assertFalse($item->fresh()->isActive());
    }

    public function test_giao_dich_hoan_tac_khi_gap_loi(): void
    {
        $item = DemoItem::create(['code' => 'A1', 'name' => 'Khoa Toán']);

        try {
            app(DemoItemService::class)->renameThenFail($item, 'Tên mới');
        } catch (BusinessRuleException) {
            // dự kiến
        }

        $this->assertSame('Khoa Toán', $item->fresh()->name, 'Giao dịch phải hoàn tác việc sửa tên');
    }

    // ---- Xử lý lỗi nghiệp vụ ở tầng HTTP ----

    public function test_loi_nghiep_vu_tra_ve_422_dang_json(): void
    {
        $this->registerFailingRoute();

        $this->getJson('/_test/loi-nghiep-vu')
            ->assertStatus(422)
            ->assertExactJson(['message' => 'Thiếu học phần tiên quyết: Giải tích 1. Hãy hoàn thành học phần này trước.']);
    }

    public function test_loi_nghiep_vu_dang_trang_quay_lai_kem_thong_bao(): void
    {
        $this->registerFailingRoute();

        $this->from('/truoc-do')
            ->get('/_test/loi-nghiep-vu')
            ->assertRedirect('/truoc-do')
            ->assertSessionHasErrors(['business_rule' => 'Thiếu học phần tiên quyết: Giải tích 1. Hãy hoàn thành học phần này trước.']);
    }

    private function registerFailingRoute(): void
    {
        Route::middleware('web')->get('/_test/loi-nghiep-vu', function (): void {
            throw new BusinessRuleException(
                'Thiếu học phần tiên quyết: Giải tích 1.',
                'Hãy hoàn thành học phần này trước.'
            );
        });
    }

    // ---- Policy ----

    public function test_policy_mac_dinh_tu_choi_moi_thao_tac(): void
    {
        $user = User::factory()->create();
        $item = DemoItem::create(['code' => 'A1', 'name' => 'Khoa Toán']);

        foreach (['view', 'update', 'delete', 'restore', 'forceDelete'] as $ability) {
            $this->assertTrue(Gate::forUser($user)->denies($ability, $item), "Phải từ chối `$ability` theo mặc định");
        }

        $this->assertTrue(Gate::forUser($user)->denies('create', DemoItem::class));
    }

    public function test_policy_cua_module_chi_mo_quyen_can_thiet(): void
    {
        $user = User::factory()->create();

        $this->assertTrue(Gate::forUser($user)->allows('viewAny', DemoItem::class));
        $this->assertTrue(Gate::forUser(null)->denies('viewAny', DemoItem::class), 'Khách chưa đăng nhập bị từ chối');
    }
}
