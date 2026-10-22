<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Auth\Models\Role;
use App\Modules\Auth\Services\RoleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

/** Mỗi cổng chỉ cho vai trò của cổng đó (docs/BA.md mục 8.1): sinh viên không mở được Cổng Giảng viên / Quản trị. */
class PortalAccessTest extends TestCase
{
    use InteractsWithRoles;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    private function userWithRoles(string ...$codes): User
    {
        $user = User::factory()->create(['username' => fake()->unique()->userName()]);

        foreach ($codes as $code) {
            app(RoleService::class)->assign($user, Role::where('code', $code)->firstOrFail());
        }

        return $user;
    }

    public function test_sinh_vien_khong_vao_duoc_cong_giang_vien_va_quan_tri(): void
    {
        $student = $this->userWithRoles('STU');

        $this->actingAs($student)->get('/teacher')->assertRedirect(route('student.home'))->assertSessionHas('error');
        $this->actingAs($student)->get('/admin')->assertRedirect(route('student.home'));
        $this->actingAs($student)->get('/admin/quan-ly-diem')->assertRedirect(route('student.home'));
        $this->actingAs($student)->getJson('/admin/dashboard/data')->assertForbidden();
        $this->actingAs($student)->get('/student')->assertOk();
    }

    public function test_giang_vien_khong_vao_duoc_cong_sinh_vien_va_quan_tri(): void
    {
        $lecturer = $this->userWithRoles('LEC');

        $this->actingAs($lecturer)->get('/student')->assertRedirect(route('teacher.home'));
        $this->actingAs($lecturer)->get('/admin')->assertRedirect(route('teacher.home'));
        $this->actingAs($lecturer)->get('/teacher')->assertOk();
    }

    public function test_can_bo_kiem_giang_vien_vao_duoc_ca_hai_cong(): void
    {
        $dean = $this->userWithRoles('DEAN', 'LEC');

        $this->actingAs($dean)->get('/admin')->assertOk();
        $this->actingAs($dean)->get('/teacher')->assertOk();
        $this->actingAs($dean)->get('/student')->assertRedirect(route('admin.home'));
    }
}
