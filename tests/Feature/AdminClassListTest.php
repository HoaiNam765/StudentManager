<?php

namespace Tests\Feature;

use App\Modules\SchoolClass\Models\AdminClass;
use Database\Seeders\AcademicYearSeeder;
use Database\Seeders\CurriculumSeeder;
use Database\Seeders\FacultySeeder;
use Database\Seeders\GradeStructureSeeder;
use Database\Seeders\RoomSeeder;
use Database\Seeders\SchoolClassSeeder;
use Database\Seeders\SubjectSeeder;
use Database\Seeders\SystemSeeder;
use Database\Seeders\TeacherSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

/**
 * Danh sách / chi tiết lớp hành chính (FR-CLS-002). Hồi quy: closure ràng buộc quan hệ trong `with()` / `load()` nhận
 * đối tượng quan hệ (HasMany), khai kiểu `Builder` làm API trả 500.
 */
class AdminClassListTest extends TestCase
{
    use InteractsWithRoles;
    use RefreshDatabase;

    public function test_danh_sach_va_chi_tiet_lop_hanh_chinh(): void
    {
        $this->seedRoles();
        $this->seed([
            SystemSeeder::class, FacultySeeder::class, AcademicYearSeeder::class, RoomSeeder::class, SubjectSeeder::class,
            CurriculumSeeder::class, TeacherSeeder::class, GradeStructureSeeder::class, SchoolClassSeeder::class,
        ]);
        $this->actingAs($this->userWithRoles('ACAD'));

        $this->getJson('/admin/admin-classes')->assertOk()->assertJsonPath('total', AdminClass::count());
        $this->getJson('/admin/admin-classes/'.AdminClass::firstOrFail()->id)->assertOk();
        $this->getJson('/admin/cohorts')->assertOk();
    }
}
