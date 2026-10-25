<?php

namespace Tests\Unit\Modules\Attendance;

use App\Modules\Attendance\Services\AttendanceCalculator;
use PHPUnit\Framework\TestCase;

/** Phép tính tỷ lệ vắng, cảnh báo / cấm thi, điểm chuyên cần (#143; BR-ATT-02, 03, 04, 06; UAT-09). */
class AttendanceCalculatorTest extends TestCase
{
    private const CONFIG = ['excused_weight' => 1.0, 'lates_per_absence' => 3, 'score_deduction_per_absence' => 2.0];

    public function test_vang_3_tren_10_buoi_vuot_nguong_20_phan_tram_thi_bi_cam_thi(): void
    {
        $r = AttendanceCalculator::compute(10, ['absent' => 3], self::CONFIG, 10, 20);

        $this->assertSame(30.0, $r['absence_rate']);
        $this->assertTrue($r['warning']);
        $this->assertTrue($r['banned']);
        $this->assertSame(0.0, $r['attendance_score']);
    }

    public function test_cham_nguong_canh_bao_nhung_chua_cam_thi(): void
    {
        $r = AttendanceCalculator::compute(10, ['absent' => 1], self::CONFIG, 10, 20);

        $this->assertTrue($r['warning']);
        $this->assertFalse($r['banned']);
        $this->assertSame(8.0, $r['attendance_score']);
    }

    public function test_vang_dung_bang_nguong_cam_thi_van_du_dieu_kien(): void
    {
        $r = AttendanceCalculator::compute(10, ['absent' => 2], self::CONFIG, 10, 20);

        $this->assertSame(20.0, $r['absence_rate']);
        $this->assertFalse($r['banned']);
    }

    public function test_vang_co_phep_theo_he_so_va_di_muon_quy_doi(): void
    {
        $config = ['excused_weight' => 0.5] + self::CONFIG;
        // 2 vắng có phép × 0,5 = 1; 1 lần muộn quá giờ = 1; 4 lần muộn ngắn ÷ 3 = 1 → 3 buổi
        $r = AttendanceCalculator::compute(15, ['excused' => 2, 'late_long' => 1, 'late_short' => 4], $config, 10, 20);

        $this->assertSame(3.0, $r['absences']);
        $this->assertSame(20.0, $r['absence_rate']);
    }

    public function test_duoc_mien_thi_khong_bi_cam_thi(): void
    {
        $r = AttendanceCalculator::compute(10, ['absent' => 5], self::CONFIG, 10, 20, exempted: true);

        $this->assertFalse($r['banned']);
        $this->assertTrue($r['warning']);
    }

    public function test_chua_co_buoi_nao_thi_ty_le_bang_0(): void
    {
        $r = AttendanceCalculator::compute(0, [], self::CONFIG, 10, 20);

        $this->assertSame(0.0, $r['absence_rate']);
        $this->assertFalse($r['warning']);
        $this->assertSame(10.0, $r['attendance_score']);
    }
}
