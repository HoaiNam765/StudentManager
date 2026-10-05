<?php

namespace Tests\Feature\Modules\Auth;

use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLog;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\Support\DemoProfile;
use Tests\Support\DemoProfilePolicy;
use Tests\Support\DemoSection;
use Tests\Support\DemoSectionPolicy;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

/** UAT-02: phân quyền và phạm vi dữ liệu, truy cập trái quyền bị từ chối và ghi nhật ký (BR-AUTH-04, NFR-SEC-04). */
class DataScopeAccessTest extends TestCase
{
    use DatabaseMigrations; // tự tạo bảng (DDL) nên không dùng được RefreshDatabase trên MySQL
    use InteractsWithRoles;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();

        Schema::dropIfExists('demo_sections');
        Schema::create('demo_sections', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('lecturer_id')->nullable();
            $table->unsignedBigInteger('advisor_id')->nullable();
            $table->timestamps();
        });
        Schema::dropIfExists('demo_profiles');
        Schema::create('demo_profiles', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->timestamps();
        });

        Gate::policy(DemoSection::class, DemoSectionPolicy::class);
        Gate::policy(DemoProfile::class, DemoProfilePolicy::class);

        Route::middleware(['web', 'auth'])->group(function (): void {
            Route::get('/_test/lop/{section}/diem', function (DemoSection $section) {
                Gate::authorize('view', $section);

                return ['lop' => $section->name];
            });
            Route::get('/_test/ho-so/{profile}', function (DemoProfile $profile) {
                Gate::authorize('view', $profile);

                return ['ho_ten' => $profile->name];
            });
            Route::put('/_test/lop/{section}/diem', fn (DemoSection $section) => response()->noContent())
                ->middleware('permission:GRD.update');
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('demo_sections');
        Schema::dropIfExists('demo_profiles');

        parent::tearDown();
    }

    public function test_giang_vien_khong_xem_duoc_diem_lop_cua_giang_vien_khac(): void
    {
        $lecturerA = $this->userWithRoles('LEC');
        $lecturerB = $this->userWithRoles('LEC');
        $l1 = DemoSection::create(['name' => 'L1', 'lecturer_id' => $lecturerA->id]);
        $l2 = DemoSection::create(['name' => 'L2', 'lecturer_id' => $lecturerB->id]);

        $this->actingAs($lecturerA)->getJson("/_test/lop/{$l1->id}/diem")->assertOk()->assertJson(['lop' => 'L1']);
        $this->actingAs($lecturerA)->getJson("/_test/lop/{$l2->id}/diem")->assertForbidden();

        $log = AuditLog::where('event', AuditEvent::AccessDenied)->sole();
        $this->assertSame($lecturerA->id, $log->user_id, 'Nhật ký ghi đúng người truy cập trái quyền');
        $this->assertStringEndsWith("/_test/lop/{$l2->id}/diem", $log->url);
    }

    public function test_sinh_vien_khong_xem_duoc_ho_so_sinh_vien_khac(): void
    {
        $studentA = $this->userWithRoles('STU');
        $studentB = $this->userWithRoles('STU');
        $own = DemoProfile::create(['user_id' => $studentA->id, 'name' => 'Sinh viên A']);
        $other = DemoProfile::create(['user_id' => $studentB->id, 'name' => 'Sinh viên B']);

        $this->actingAs($studentA)->getJson("/_test/ho-so/{$own->id}")->assertOk();
        $this->actingAs($studentA)->getJson("/_test/ho-so/{$other->id}")->assertForbidden();

        $this->assertSame(1, AuditLog::where('event', AuditEvent::AccessDenied)->where('user_id', $studentA->id)->count());
    }

    public function test_phong_dao_tao_xem_duoc_moi_lop(): void
    {
        $section = DemoSection::create(['name' => 'L1', 'lecturer_id' => $this->userWithRoles('LEC')->id]);

        $this->actingAs($this->userWithRoles('ACAD'))->getJson("/_test/lop/{$section->id}/diem")->assertOk();
    }

    public function test_middleware_chan_nguoi_khong_co_quyen_va_khach(): void
    {
        $section = DemoSection::create(['name' => 'L1']);

        $this->actingAs($this->userWithRoles('STU'))->putJson("/_test/lop/{$section->id}/diem")->assertForbidden();
        $this->actingAs($this->userWithRoles('LEC'))->putJson("/_test/lop/{$section->id}/diem")->assertNoContent();

        auth()->logout();
        $this->putJson("/_test/lop/{$section->id}/diem")->assertUnauthorized();
    }
}
