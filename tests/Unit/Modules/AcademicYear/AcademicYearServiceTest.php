<?php

namespace Tests\Unit\Modules\AcademicYear;

use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\AcademicYear\Models\TermStatus;
use App\Modules\AcademicYear\Services\AcademicYearService;
use App\Support\Exceptions\BusinessRuleException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AcademicYearServiceTest extends TestCase
{
    use DatabaseMigrations;

    public function test_khong_cho_nam_hoc_co_ngay_ket_thuc_truoc_ngay_bat_dau(): void
    {
        $this->expectException(BusinessRuleException::class);

        app(AcademicYearService::class)->createAcademicYear([
            'name' => '2026-2027',
            'start_date' => '2027-01-01',
            'end_date' => '2026-09-01',
        ]);
    }

    public function test_tao_nam_hoc_thanh_cong(): void
    {
        $academicYear = app(AcademicYearService::class)
            ->createAcademicYear([
                'name' => '2026-2027',
                'start_date' => '2026-09-01',
                'end_date' => '2027-08-31',
            ]);

        $this->assertDatabaseHas('academic_years', [
            'id' => $academicYear->id,
            'name' => '2026-2027',
        ]);
    }

    public function test_tao_hoc_ky_nam_trong_nam_hoc(): void
    {
        $academicYear = AcademicYear::create([
            'name' => '2026-2027',
            'start_date' => '2026-09-01',
            'end_date' => '2027-08-31',
        ]);

        $term = app(AcademicYearService::class)
            ->createTerm([
                'academic_year_id' => $academicYear->id,
                'name' => 'Học kỳ 1',
                'type' => 'main',
                'start_date' => '2026-09-01',
                'end_date' => '2027-01-10',
                'weeks' => 18,
            ]);

        $this->assertSame(
            $academicYear->id,
            $term->academic_year_id
        );

        $this->assertSame(
            TermStatus::PLANNED,
            $term->status
        );
    }

    public function test_khong_cho_hoc_ky_chinh_chong_thoi_gian(): void
    {
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

        $this->expectException(BusinessRuleException::class);

        app(AcademicYearService::class)->createTerm([
            'academic_year_id' => $academicYear->id,
            'name' => 'Học kỳ 2',
            'type' => 'main',
            'start_date' => '2027-01-05',
            'end_date' => '2027-05-30',
            'weeks' => 18,
        ]);
    }

    public function test_hoc_ky_he_khong_duoc_chong_hoc_ky_chinh(): void
    {
        $academicYear = AcademicYear::create([
            'name' => '2026-2027',
            'start_date' => '2026-09-01',
            'end_date' => '2027-08-31',
        ]);

        Term::create([
            'academic_year_id' => $academicYear->id,
            'name' => 'Học kỳ 2',
            'type' => 'main',
            'start_date' => '2027-01-15',
            'end_date' => '2027-05-30',
            'weeks' => 18,
            'status' => TermStatus::PLANNED,
        ]);

        $this->expectException(BusinessRuleException::class);

        app(AcademicYearService::class)->createTerm([
            'academic_year_id' => $academicYear->id,
            'name' => 'Học kỳ hè',
            'type' => 'summer',
            'start_date' => '2027-05-01',
            'end_date' => '2027-07-15',
            'weeks' => 10,
        ]);
    }

    public function test_lay_duoc_hoc_ky_hien_hanh(): void
    {
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

        $current = app(AcademicYearService::class)
            ->getCurrentTerm();

        $this->assertNotNull($current);
        $this->assertSame($term->id, $current->id);
    }

    public function test_khong_cho_hai_hoc_ky_chinh_cung_dang_dien_ra(): void
    {
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
            'status' => TermStatus::IN_PROGRESS,
        ]);

        $term2 = Term::create([
            'academic_year_id' => $academicYear->id,
            'name' => 'Học kỳ 2',
            'type' => 'main',
            'start_date' => '2027-02-01',
            'end_date' => '2027-06-01',
            'weeks' => 18,
            'status' => TermStatus::REGISTRATION,
        ]);

        $this->expectException(BusinessRuleException::class);

        app(AcademicYearService::class)
            ->setCurrentTerm($term2);
    }

    public function test_state_machine_khong_cho_nhay_trang_thai(): void
    {
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
            'status' => TermStatus::PLANNED,
        ]);

        $this->expectException(BusinessRuleException::class);

        app(AcademicYearService::class)->changeStatus(
            $term,
            TermStatus::COMPLETED
        );
    }

    public function test_state_machine_chuyen_trang_thai_hop_le(): void
    {
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
            'status' => TermStatus::PLANNED,
        ]);

        $term = app(AcademicYearService::class)
            ->changeStatus(
                $term,
                TermStatus::REGISTRATION
            );

        $this->assertSame(
            TermStatus::REGISTRATION,
            $term->status
        );
    }

    public function test_khong_cho_acy_tu_y_khoa_hoc_ky(): void
    {
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
            'status' => TermStatus::COMPLETED,
        ]);

        $this->expectException(BusinessRuleException::class);

        app(AcademicYearService::class)->changeStatus(
            $term,
            TermStatus::LOCKED
        );
    }

    public function test_khong_xoa_nam_hoc_da_co_hoc_ky(): void
    {
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

        $this->expectException(BusinessRuleException::class);

        app(AcademicYearService::class)
            ->deleteAcademicYear($academicYear);
    }

    public function test_change_status_khong_duoc_chuyen_registration_sang_in_progress(): void
    {
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
            'status' => TermStatus::REGISTRATION,
        ]);

        $this->expectException(BusinessRuleException::class);

        app(AcademicYearService::class)->changeStatus(
            $term,
            TermStatus::IN_PROGRESS
        );
    }

    public function test_khong_duoc_chuyen_sang_exam_grading_truoc_ngay_ket_thuc(): void
    {
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
            'end_date' => '2099-01-10',
            'weeks' => 18,
            'status' => TermStatus::IN_PROGRESS,
        ]);

        $this->expectException(BusinessRuleException::class);

        app(AcademicYearService::class)->changeStatus(
            $term,
            TermStatus::EXAM_GRADING
        );
    }
}
