<?php

namespace Tests\Feature\Modules\AcademicYear;

use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\AcademicYear\Models\TermStatus;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

class AcademicYearTest extends TestCase
{
    use DatabaseMigrations;
    use InteractsWithRoles;

    public function test_tao_nam_hoc_thanh_cong(): void
    {
        $this->seedRoles();

        $user = $this->userWithRoles('ACAD');

        $this->actingAs($user);

        $response = $this->postJson(
            '/admin/academic-years',
            [
                'name' => '2026-2027',
                'start_date' => '2026-09-01',
                'end_date' => '2027-08-31',
            ]
        );

        $response->assertCreated();

        $this->assertDatabaseHas('academic_years', [
            'name' => '2026-2027',
        ]);
    }

    public function test_tao_hoc_ky_thanh_cong(): void
    {
        $this->seedRoles();

        $user = $this->userWithRoles('ACAD');

        $this->actingAs($user);

        $academicYear = AcademicYear::create([
            'name' => '2026-2027',
            'start_date' => '2026-09-01',
            'end_date' => '2027-08-31',
        ]);

        $response = $this->postJson(
            '/admin/academic-years/terms',
            [
                'academic_year_id' => $academicYear->id,
                'name' => 'Học kỳ 1',
                'type' => 'main',
                'start_date' => '2026-09-01',
                'end_date' => '2027-01-10',
                'weeks' => 18,
            ]
        );

        $response->assertCreated();

        $this->assertDatabaseHas('terms', [
            'academic_year_id' => $academicYear->id,
            'name' => 'Học kỳ 1',
        ]);
    }

    public function test_khong_cho_hoc_ky_chong_thoi_gian(): void
    {
        $this->seedRoles();

        $user = $this->userWithRoles('ACAD');

        $this->actingAs($user);

        $academicYear = AcademicYear::create([
            'name' => '2026-2027',
            'start_date' => '2026-09-01',
            'end_date' => '2027-08-31',
        ]);

        Term::create([
            'academic_year_id' => $academicYear->id,
            'name' => 'Học kỳ 1',
            'type' => 'main',
            'start_date' => '2026-09-01',
            'end_date' => '2027-01-10',
            'weeks' => 18,
            'status' => TermStatus::PLANNED,
        ]);

        $response = $this->postJson(
            '/admin/academic-years/terms',
            [
                'academic_year_id' => $academicYear->id,
                'name' => 'Học kỳ 2',
                'type' => 'main',
                'start_date' => '2027-01-05',
                'end_date' => '2027-05-30',
                'weeks' => 18,
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJsonPath(
                'message',
                fn ($message) => str_contains(
                    $message,
                    'chồng'
                )
            );
    }

    public function test_lay_hoc_ky_hien_hanh(): void
    {
        $this->seedRoles();

        $user = $this->userWithRoles('ACAD');

        $this->actingAs($user);

        $academicYear = AcademicYear::create([
            'name' => '2026-2027',
            'start_date' => '2026-09-01',
            'end_date' => '2027-08-31',
        ]);

        $term = Term::create([
            'academic_year_id' => $academicYear->id,
            'name' => 'Học kỳ 1',
            'type' => 'main',
            'start_date' => '2026-09-01',
            'end_date' => '2027-01-10',
            'weeks' => 18,
            'status' => TermStatus::IN_PROGRESS,
        ]);

        $response = $this->getJson(
            '/admin/academic-years/current-term'
        );

        $response
            ->assertOk()
            ->assertJsonPath('id', $term->id);
    }

    public function test_user_khong_co_quyen_create_acy_bi_tu_choi(): void
    {
        $this->seedRoles();

        $user = $this->userWithRoles('STU');

        $response = $this
            ->actingAs($user)
            ->postJson('/admin/academic-years', [
                'name' => '2026-2027',
                'start_date' => '2026-09-01',
                'end_date' => '2027-08-31',
            ]);

        $response->assertForbidden();
    }
}
