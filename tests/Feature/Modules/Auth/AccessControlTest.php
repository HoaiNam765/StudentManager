<?php

namespace Tests\Feature\Modules\Auth;

use App\Models\User;
use App\Modules\Auth\Enums\DataScope;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Models\Role;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Auth\Services\RoleService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\Support\DemoSection;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use DatabaseMigrations; // tự tạo bảng (DDL) nên không dùng được RefreshDatabase trên MySQL
    use InteractsWithRoles;

    private AccessControl $access;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
        $this->access = app(AccessControl::class);

        Schema::dropIfExists('demo_sections');
        Schema::create('demo_sections', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('lecturer_id')->nullable();
            $table->unsignedBigInteger('advisor_id')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('demo_sections');

        parent::tearDown();
    }

    public function test_pham_vi_theo_vai_tro_va_hop_cua_nhieu_vai_tro(): void
    {
        $lecturer = $this->userWithRoles('LEC');
        $lecturerAdvisor = $this->userWithRoles('LEC', 'ADV');
        $acad = $this->userWithRoles('ACAD');
        $acadAndLecturer = $this->userWithRoles('ACAD', 'LEC');

        $this->assertSame([DataScope::Section], $this->access->scopesFor($lecturer, 'GRD', PermissionAction::Update));
        $this->assertSame([DataScope::All], $this->access->scopesFor($acad, 'STU', PermissionAction::View));
        $this->assertEqualsCanonicalizing(
            [DataScope::Section, DataScope::Advisee],
            $this->access->scopesFor($lecturerAdvisor, 'STU', PermissionAction::View),
            'Quyền là hợp của các vai trò (BR-AUTH-04)'
        );
        $this->assertSame([DataScope::All], $this->access->scopesFor($acadAndLecturer, 'GRD', PermissionAction::View), 'Có ALL thì chỉ còn ALL');
        $this->assertTrue($this->access->allowsGlobally($acad, 'STU', PermissionAction::Export));
        $this->assertFalse($this->access->allowsGlobally($lecturer, 'GRD', PermissionAction::View));
    }

    public function test_mac_dinh_tu_choi(): void
    {
        $noRole = User::factory()->create();

        $this->assertSame([], $this->access->scopesFor($noRole, 'STU', PermissionAction::View));
        $this->assertSame([], $this->access->scopesFor(null, 'STU', PermissionAction::View), 'Khách chưa đăng nhập');
        $this->assertFalse($this->access->allows($this->userWithRoles('LEC'), 'FEE', PermissionAction::View), 'Ô "—" trong ma trận');
        $this->assertFalse($this->access->allows($this->userWithRoles('ADMIN'), 'GRD', PermissionAction::Update), 'ADMIN không sửa dữ liệu nghiệp vụ');
    }

    public function test_vai_tro_chi_co_hieu_luc_trong_khoang_thoi_gian_va_khi_dang_hoat_dong(): void
    {
        $service = app(RoleService::class);
        $lec = Role::where('code', 'LEC')->first();
        $today = now(config('studentmanager.display_timezone'))->startOfDay();

        $future = User::factory()->create();
        $service->assign($future, $lec, $today->copy()->addDay()->toDateString());
        $expired = User::factory()->create();
        DB::table('user_roles')->insert([
            'user_id' => $expired->id, 'role_id' => $lec->id,
            'valid_from' => $today->copy()->subMonth()->toDateString(), 'valid_to' => $today->copy()->subDay()->toDateString(),
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $current = $this->userWithRoles('LEC');

        $this->assertFalse($this->access->allows($future, 'GRD', PermissionAction::View), 'Chưa tới ngày bắt đầu');
        $this->assertFalse($this->access->allows($expired, 'GRD', PermissionAction::View), 'Đã quá ngày kết thúc');
        $this->assertTrue($this->access->allows($current, 'GRD', PermissionAction::View));

        $service->deactivate($lec);

        $this->assertFalse($this->access->allows($current, 'GRD', PermissionAction::View), 'Vai trò ngừng hoạt động thì mất quyền ngay');
    }

    public function test_thay_doi_ma_tran_co_hieu_luc_ngay(): void
    {
        $lecturer = $this->userWithRoles('LEC');
        $this->assertFalse($this->access->allows($lecturer, 'FEE', PermissionAction::View));

        $role = Role::where('code', 'LEC')->first();
        app(RoleService::class)->syncPermissions($role, $role->permissionMatrix() + ['FEE.view' => 'ALL']);

        $this->assertTrue($this->access->allows($lecturer, 'FEE', PermissionAction::View));
    }

    public function test_loc_danh_sach_theo_pham_vi_du_lieu(): void
    {
        $lecturerA = $this->userWithRoles('LEC');
        $lecturerB = $this->userWithRoles('LEC');
        $advisor = $this->userWithRoles('LEC', 'ADV');
        $acad = $this->userWithRoles('ACAD');
        $noRole = User::factory()->create();

        $l1 = DemoSection::create(['name' => 'L1', 'lecturer_id' => $lecturerA->id]);
        $l2 = DemoSection::create(['name' => 'L2', 'lecturer_id' => $lecturerB->id, 'advisor_id' => $advisor->id]);
        $l3 = DemoSection::create(['name' => 'L3', 'lecturer_id' => $advisor->id]);

        $visible = fn (?User $user, string $module = 'GRD') => $this->access
            ->constrain(DemoSection::query(), $user, $module, PermissionAction::View)
            ->orderBy('id')->pluck('name')->all();

        $this->assertSame(['L1'], $visible($lecturerA), 'Giảng viên chỉ thấy lớp mình dạy');
        $this->assertSame(['L1', 'L2', 'L3'], $visible($acad), 'Phòng đào tạo thấy toàn trường');
        $this->assertSame([], $visible($noRole));
        $this->assertSame(['L2', 'L3'], $visible($advisor, 'STU'), 'Hợp phạm vi: lớp mình dạy và lớp mình cố vấn');

        $this->assertTrue($this->access->allowsOn($lecturerA, 'GRD', PermissionAction::Update, $l1));
        $this->assertFalse($this->access->allowsOn($lecturerA, 'GRD', PermissionAction::Update, $l2), 'Chống IDOR');
        $this->assertFalse($this->access->allowsOn($lecturerA, 'GRD', PermissionAction::Approve, $l1), 'LEC không có quyền duyệt điểm');
        $this->assertTrue($this->access->allowsOn($acad, 'GRD', PermissionAction::Approve, $l3));
    }

    public function test_model_khong_khai_bao_pham_vi_thi_tu_choi(): void
    {
        // DEAN có GRD.view với phạm vi FACULTY, nhưng DemoSection không xử lý FACULTY
        $dean = $this->userWithRoles('DEAN');
        $section = DemoSection::create(['name' => 'L1']);

        $this->assertSame([DataScope::Faculty], $this->access->scopesFor($dean, 'GRD', PermissionAction::View));
        $this->assertSame(0, $this->access->constrain(DemoSection::query(), $dean, 'GRD', PermissionAction::View)->count());
        $this->assertFalse($this->access->allowsOn($dean, 'GRD', PermissionAction::View, $section));

        // Model không cài HasDataScope: chỉ người có phạm vi ALL mới qua được
        $this->assertSame(0, $this->access->constrain(User::query(), $dean, 'TCH', PermissionAction::View)->count());
        $this->assertGreaterThan(0, $this->access->constrain(User::query(), $this->userWithRoles('ACAD'), 'TCH', PermissionAction::View)->count());
    }
}
