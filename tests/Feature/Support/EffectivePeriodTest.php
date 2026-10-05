<?php

namespace Tests\Feature\Support;

use App\Support\Exceptions\BusinessRuleException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Schema;
use Tests\Support\DemoRate;
use Tests\TestCase;

class EffectivePeriodTest extends TestCase
{
    use DatabaseMigrations; // các test này tự tạo bảng (DDL) nên không dùng được RefreshDatabase trên MySQL

    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('demo_rates');
        Schema::create('demo_rates', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 20);
            $table->unsignedInteger('amount');
            $table->effectivePeriod();
            $table->standardColumns();
            $table->unique(['code', 'effective_from']);
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('demo_rates');

        parent::tearDown();
    }

    public function test_chon_ban_ghi_co_hieu_luc_theo_ngay(): void
    {
        DemoRate::create(['code' => 'GIA', 'amount' => 450000, 'effective_from' => '2025-09-01', 'effective_to' => '2026-08-31']);
        DemoRate::create(['code' => 'GIA', 'amount' => 500000, 'effective_from' => '2026-09-01']);

        $this->assertSame(450000, DemoRate::effectiveOn('2026-03-15')->value('amount'));
        $this->assertSame(450000, DemoRate::effectiveOn('2026-08-31')->value('amount'), 'Ngày cuối vẫn còn hiệu lực');
        $this->assertSame(500000, DemoRate::effectiveOn('2026-09-01')->value('amount'));
        $this->assertSame(500000, DemoRate::effectiveOn('2030-01-01')->value('amount'), 'Không có ngày kết thúc: còn hiệu lực mãi');
        $this->assertNull(DemoRate::effectiveOn('2025-08-31')->value('amount'), 'Trước ngày hiệu lực đầu tiên');
    }

    public function test_thay_doi_khong_ghi_de_lich_su(): void
    {
        $old = DemoRate::create(['code' => 'GIA', 'amount' => 450000, 'effective_from' => '2025-09-01']);

        $new = $old->supersedeWith(['amount' => 500000], '2026-09-01');

        $this->assertSame(2, DemoRate::count(), 'Bản cũ vẫn còn');
        $this->assertSame('2026-08-31', $old->fresh()->effective_to->toDateString(), 'Bản cũ được đóng vào ngày hôm trước');
        $this->assertSame('2026-09-01', $new->effective_from->toDateString());
        $this->assertNull($new->effective_to);
        $this->assertSame(500000, $new->amount);
        $this->assertSame(450000, DemoRate::effectiveOn('2026-08-31')->value('amount'));
        $this->assertSame(500000, DemoRate::effectiveOn('2026-09-01')->value('amount'));
    }

    public function test_ngay_hieu_luc_moi_phai_sau_ban_hien_tai(): void
    {
        $old = DemoRate::create(['code' => 'GIA', 'amount' => 450000, 'effective_from' => '2026-09-01']);

        $this->expectException(BusinessRuleException::class);
        $this->expectExceptionMessage('Ngày hiệu lực mới phải sau ngày hiệu lực của bản hiện tại.');

        $old->supersedeWith(['amount' => 500000], '2026-09-01');
    }
}
