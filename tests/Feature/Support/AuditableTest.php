<?php

namespace Tests\Feature\Support;

use App\Models\User;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLog;
use App\Support\Audit\AuditLogger;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Tests\Support\DemoItem;
use Tests\Support\DemoSensitiveItem;
use Tests\TestCase;

class AuditableTest extends TestCase
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
            $table->string('id_number')->nullable();
            $table->string('secret_token')->nullable();
            $table->timestamp('viewed_at')->nullable();
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

    public function test_tao_moi_ghi_gia_tri_moi_va_nguoi_thuc_hien(): void
    {
        $user = User::factory()->create(['name' => 'Nguyễn Văn Cán Bộ']);
        $this->actingAs($user);

        $item = DemoItem::create(['code' => 'K01', 'name' => 'Khoa Toán']);

        $log = AuditLog::sole();
        $this->assertSame(AuditEvent::Created, $log->event);
        $this->assertSame(DemoItem::class, $log->auditable_type);
        $this->assertSame($item->id, $log->auditable_id);
        $this->assertSame($user->id, $log->user_id);
        $this->assertSame('Nguyễn Văn Cán Bộ', $log->user_name, 'Lưu tên tại thời điểm ghi');
        $this->assertNull($log->old_values);
        $this->assertSame('Khoa Toán', $log->new_values['name']);
        $this->assertSame('active', $log->new_values['status']);
        foreach (['created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'search_text'] as $column) {
            $this->assertArrayNotHasKey($column, $log->new_values, "Không ghi cột {$column}");
        }
    }

    public function test_sua_chi_ghi_cot_thay_doi_kem_gia_tri_cu_va_moi(): void
    {
        $item = DemoItem::create(['code' => 'K01', 'name' => 'Khoa Toán']);

        $item->update(['name' => 'Khoa Toán – Tin']);

        $log = AuditLog::where('event', AuditEvent::Updated)->sole();
        $this->assertSame(['name' => 'Khoa Toán'], $log->old_values);
        $this->assertSame(['name' => 'Khoa Toán – Tin'], $log->new_values);
    }

    public function test_luu_ma_khong_doi_thi_khong_ghi_cap_nhat(): void
    {
        $item = DemoItem::create(['code' => 'K01', 'name' => 'Khoa Toán']);

        $item->update(['name' => 'Khoa Toán']);

        $this->assertSame(0, AuditLog::where('event', AuditEvent::Updated)->count());
    }

    public function test_xoa_mem_khoi_phuc_va_xoa_vinh_vien(): void
    {
        $item = DemoItem::create(['code' => 'K01', 'name' => 'Khoa Toán']);

        $item->delete();
        $item->restore();
        $item->forceDelete();

        $this->assertSame(
            [AuditEvent::Created, AuditEvent::Deleted, AuditEvent::Restored, AuditEvent::ForceDeleted],
            AuditLog::orderBy('id')->get()->pluck('event')->all(),
            'Khôi phục chỉ ghi "Khôi phục", không ghi thêm "Cập nhật"'
        );
        $this->assertSame('Khoa Toán', AuditLog::where('event', AuditEvent::ForceDeleted)->sole()->old_values['name']);
    }

    public function test_ly_do_duoc_ghi_kem_va_khong_lan_sang_thay_doi_sau(): void
    {
        $item = DemoItem::create(['code' => 'K01', 'name' => 'Khoa Toán']);

        app(AuditLogger::class)->withReason('Sửa sai chính tả theo đề nghị của khoa', function () use ($item): void {
            $item->update(['name' => 'Khoa Toán học']);
        });
        $item->update(['name' => 'Khoa Toán ứng dụng']);

        $logs = AuditLog::where('event', AuditEvent::Updated)->orderBy('id')->get();
        $this->assertSame('Sửa sai chính tả theo đề nghị của khoa', $logs[0]->reason);
        $this->assertNull($logs[1]->reason);
    }

    public function test_che_va_bo_qua_cot_nhay_cam(): void
    {
        DemoSensitiveItem::create([
            'code' => 'SV01',
            'name' => 'Đặng Hoài Nam',
            'id_number' => '012345678901',
            'secret_token' => 'abc123',
            'viewed_at' => now(),
        ]);

        $values = AuditLog::sole()->new_values;
        $this->assertSame(DemoSensitiveItem::AUDIT_MASK, $values['id_number'], 'Cột trong $auditMasked được che');
        $this->assertSame(DemoSensitiveItem::AUDIT_MASK, $values['secret_token'], 'Cột trong $hidden luôn được che');
        $this->assertArrayNotHasKey('viewed_at', $values, 'Cột trong $auditExclude không được ghi');
        $this->assertStringNotContainsString('012345678901', json_encode($values));
    }

    public function test_ghi_ip_thiet_bi_va_duong_dan_khi_thao_tac_qua_web(): void
    {
        Route::middleware('web')->post('/_test/tao-muc', function () {
            DemoItem::create(['code' => 'K01', 'name' => 'Khoa Toán']);

            return response()->noContent();
        });

        $this->actingAs(User::factory()->create())
            ->withServerVariables(['REMOTE_ADDR' => '10.0.0.5'])
            ->withHeader('User-Agent', 'Trình duyệt thử nghiệm')
            ->post('/_test/tao-muc')
            ->assertNoContent();

        $log = AuditLog::sole();
        $this->assertSame('10.0.0.5', $log->ip_address);
        $this->assertSame('Trình duyệt thử nghiệm', $log->user_agent);
        $this->assertStringEndsWith('/_test/tao-muc', $log->url);
    }

    public function test_ghi_thu_cong_hanh_dong_khong_phai_tao_sua_xoa(): void
    {
        $user = User::factory()->create();
        $item = DemoItem::create(['code' => 'K01', 'name' => 'Khoa Toán']);
        $logger = app(AuditLogger::class);

        $this->actingAs($user);
        $logger->record(AuditEvent::ViewSensitive, $item, reason: 'Xem đầy đủ số CCCD để đối chiếu hồ sơ');
        $logger->record(AuditEvent::AccessDenied, reason: 'Truy cập điểm lớp không được phân công');

        $view = AuditLog::where('event', AuditEvent::ViewSensitive)->sole();
        $this->assertSame($item->id, $view->auditable_id);
        $this->assertSame($user->id, $view->user_id);
        $this->assertSame('Xem đầy đủ số CCCD để đối chiếu hồ sơ', $view->reason);

        $denied = AuditLog::where('event', AuditEvent::AccessDenied)->sole();
        $this->assertNull($denied->auditable_type, 'Có thể ghi mà không gắn đối tượng');
        $this->assertSame('Từ chối truy cập', $denied->event->label());
    }

    public function test_giao_dich_hoan_tac_thi_nhat_ky_cung_hoan_tac(): void
    {
        try {
            DB::transaction(function (): void {
                DemoItem::create(['code' => 'K01', 'name' => 'Khoa Toán']);

                throw new RuntimeException('Lỗi giả lập');
            });
        } catch (RuntimeException) {
            // dự kiến
        }

        $this->assertSame(0, DemoItem::withTrashed()->count());
        $this->assertSame(0, AuditLog::count(), 'Không còn nhật ký cho thay đổi đã bị hoàn tác');
    }
}
