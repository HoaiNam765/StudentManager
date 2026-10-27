<?php

namespace Tests\Feature;

use App\Modules\Subject\Models\SubjectRelation;
use Database\Seeders\FacultySeeder;
use Database\Seeders\SubjectRelationSeeder;
use Database\Seeders\SubjectSeeder;
use Database\Seeders\SystemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

/** Trang quản lý học phần (FE #10, mock admin/hoc-phan): trang theo quyền, cột học phần tiên quyết trong API danh sách. */
class SubjectPageTest extends TestCase
{
    use InteractsWithRoles;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
        $this->seed([SystemSeeder::class, FacultySeeder::class, SubjectSeeder::class, SubjectRelationSeeder::class]);
    }

    public function test_phong_dao_tao_mo_trang_va_thay_hoc_phan_tien_quyet(): void
    {
        $this->actingAs($this->userWithRoles('ACAD'));
        $relation = SubjectRelation::query()->where('type', 'prerequisite')->whereNull('effective_to')->firstOrFail();

        $this->get('/admin/hoc-phan')->assertOk()->assertSee('data-page="admin-subjects"', false);
        $row = collect($this->getJson('/admin/subjects?per_page=100&department_id='.$relation->subject->department_id)->assertOk()->json('data'))
            ->firstWhere('id', $relation->subject_id);

        $this->assertContains($relation->relatedSubject->code, collect($row['prerequisites'])->pluck('code')->all());
    }

    public function test_sinh_vien_khong_vao_duoc_trang_quan_ly_hoc_phan(): void
    {
        $this->actingAs($this->userWithRoles('STU'))->get('/admin/hoc-phan')->assertRedirect(route('student.home'));
    }
}
