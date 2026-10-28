<?php

namespace Tests\Feature\Modules\Teacher;

use App\Models\User;
use App\Modules\Auth\Enums\ProfileType;
use App\Modules\Auth\Notifications\TemporaryPasswordIssued;
use App\Modules\Faculty\Models\Department;
use App\Modules\Faculty\Models\Faculty;
use App\Modules\Faculty\Services\FacultyService;
use App\Modules\Faculty\Services\LeadershipService;
use App\Modules\Teacher\Contracts\TeachingAssignments;
use App\Modules\Teacher\Enums\LecturerStatus;
use App\Modules\Teacher\Models\Lecturer;
use App\Modules\Teacher\Services\LecturerService;
use App\Support\Exceptions\BusinessRuleException;
use Carbon\CarbonImmutable;
use Database\Seeders\FacultySeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

class LecturerServiceTest extends TestCase
{
    use DatabaseMigrations;
    use InteractsWithRoles;

    private LecturerService $lecturers;

    private Department $ktpm;

    private Department $httt;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        $this->seedRoles();
        $this->seed(FacultySeeder::class);

        $this->lecturers = app(LecturerService::class);
        $this->ktpm = Department::query()->where('code', 'BM-KTPM')->firstOrFail();
        $this->httt = Department::query()->where('code', 'BM-HTTT')->firstOrFail();
    }

    /** @param  array<string, mixed>  $data */
    private function lecturer(array $data = []): Lecturer
    {
        return $this->lecturers->create($data + [
            'code' => 'GV1001',
            'full_name' => 'Nguyễn Văn Thử',
            'email' => 'gv1001@studentmanager.test',
            'department_id' => $this->ktpm->id,
        ]);
    }

    private function day(int $offset = 0): string
    {
        return CarbonImmutable::now(config('studentmanager.display_timezone'))->addDays($offset)->toDateString();
    }

    private function rejected(callable $callback): BusinessRuleException
    {
        try {
            $callback();
        } catch (BusinessRuleException $e) {
            $this->assertNotEmpty($e->hint(), 'Thông báo từ chối phải kèm cách khắc phục (GC-04).');

            return $e;
        }

        $this->fail('Thao tác lẽ ra bị từ chối.');
    }

    private function leadership(array $data): void
    {
        app(LeadershipService::class)->assign($data + ['starts_on' => $this->day()]);
    }

    public function test_tao_ho_so_cap_luon_tai_khoan_vai_tro_lec_va_mat_khau_tam(): void
    {
        $lecturer = $this->lecturer();
        $user = $lecturer->user;

        $this->assertNotNull($user);
        $this->assertSame('GV1001', $user->username);
        $this->assertSame('Nguyễn Văn Thử', $user->name);
        $this->assertSame(ProfileType::Teacher, $user->profile_type);
        $this->assertSame($lecturer->id, $user->profile_id);
        $this->assertTrue($user->hasRole('LEC'));
        $this->assertTrue($user->must_change_password);
        $this->assertSame(LecturerStatus::Working, $lecturer->status);
        $this->assertSame('lecturer', $lecturer->academic_title->value);
        Notification::assertSentTo($user, TemporaryPasswordIssued::class);
    }

    public function test_cap_tai_khoan_can_email_hoac_chon_chua_cap(): void
    {
        $e = $this->rejected(fn () => $this->lecturer(['email' => null]));
        $this->assertStringContainsString('Cần email', $e->getMessage());

        $lecturer = $this->lecturer(['email' => null, 'account' => 'none']);
        $this->assertNull($lecturer->user_id);

        $this->rejected(fn () => $this->lecturers->createAccount($lecturer));
        $withAccount = $this->lecturers->createAccount($lecturer, email: 'gv1001@studentmanager.test');
        $this->assertSame('GV1001', $withAccount->user->username);

        // Mỗi hồ sơ tối đa một tài khoản
        $this->rejected(fn () => $this->lecturers->createAccount($withAccount));
    }

    public function test_gan_tai_khoan_co_san_va_bo_sung_vai_tro_lec(): void
    {
        $staff = $this->userWithRoles('ACAD');
        $lecturer = $this->lecturer(['account' => 'link', 'user_id' => $staff->id]);

        $this->assertSame($staff->id, $lecturer->user_id);
        $this->assertTrue($staff->refresh()->hasRole('LEC'));
        $this->assertTrue($staff->hasRole('ACAD'));

        // Tài khoản đã gắn hồ sơ khác thì không gắn thêm
        $e = $this->rejected(fn () => $this->lecturer(['code' => 'GV1002', 'account' => 'link', 'user_id' => $staff->id]));
        $this->assertStringContainsString('đã gắn', $e->getMessage());
    }

    public function test_ma_giang_vien_va_cccd_duy_nhat_bo_mon_phai_hoat_dong(): void
    {
        $lecturer = $this->lecturer(['national_id' => '001099000001']);

        $this->rejected(fn () => $this->lecturer(['email' => 'khac@studentmanager.test']));
        $e = $this->rejected(fn () => $this->lecturer(['code' => 'GV1002', 'email' => 'gv1002@studentmanager.test', 'national_id' => '001099000001']));
        $this->assertStringContainsString('GV1001', $e->getMessage());

        $this->rejected(fn () => $this->lecturers->update($lecturer, ['code' => 'GV9999']));

        $this->httt->deactivate();
        $this->rejected(fn () => $this->lecturer(['code' => 'GV1003', 'email' => 'gv1003@studentmanager.test', 'department_id' => $this->httt->id]));
    }

    public function test_doi_ho_ten_dong_bo_ten_tai_khoan_va_chuyen_don_vi_chinh(): void
    {
        $lecturer = $this->lecturers->update($this->lecturer(), ['full_name' => 'Nguyễn Văn Mới', 'department_id' => $this->httt->id]);

        $this->assertSame('Nguyễn Văn Mới', $lecturer->user->name);
        $this->assertSame($this->httt->id, $lecturer->department_id);
    }

    public function test_kiem_nhiem_don_vi_khac_voi_don_vi_chinh_va_khong_trung_thoi_gian(): void
    {
        $lecturer = $this->lecturer();
        $cntt = Faculty::query()->where('code', 'CNTT')->firstOrFail();
        $kinhTe = Faculty::query()->where('code', 'KT')->firstOrFail();

        $this->rejected(fn () => $this->lecturers->addUnit($lecturer, ['unit_type' => 'department', 'unit_id' => $this->ktpm->id]));
        $this->rejected(fn () => $this->lecturers->addUnit($lecturer, ['unit_type' => 'faculty', 'unit_id' => $cntt->id]));

        $unit = $this->lecturers->addUnit($lecturer, ['unit_type' => 'department', 'unit_id' => $this->httt->id, 'note' => 'Dạy học phần cơ sở dữ liệu']);
        $this->lecturers->addUnit($lecturer, ['unit_type' => 'faculty', 'unit_id' => $kinhTe->id]);

        $e = $this->rejected(fn () => $this->lecturers->addUnit($lecturer, ['unit_type' => 'department', 'unit_id' => $this->httt->id, 'starts_on' => $this->day(10)]));
        $this->assertStringContainsString('đã kiêm nhiệm', $e->getMessage());

        // Đang kiêm nhiệm ở bộ môn nào thì chưa chuyển đơn vị chính sang bộ môn đó
        $this->rejected(fn () => $this->lecturers->update($lecturer, ['department_id' => $this->httt->id]));

        $this->lecturers->endUnit($unit, $this->day());
        $this->rejected(fn () => $this->lecturers->cancelUnit($unit));

        $future = $this->lecturers->addUnit($lecturer, ['unit_type' => 'department', 'unit_id' => $this->httt->id, 'starts_on' => $this->day(30)]);
        $this->lecturers->cancelUnit($future);
        $this->assertSoftDeleted($future);
    }

    public function test_nghi_viec_can_ly_do_va_chuyen_het_lop_dang_day(): void
    {
        $lecturer = $this->lecturer();

        $this->app->instance(TeachingAssignments::class, new class implements TeachingAssignments
        {
            public function activeSections(Lecturer $lecturer): array
            {
                return ['INT201.01 – Cấu trúc dữ liệu', 'INT201.02 – Cấu trúc dữ liệu', 'INT303.01 – Phát triển ứng dụng web'];
            }

            public function termLoads(array $lecturerIds, int $termId): array
            {
                return [];
            }
        });
        $service = app(LecturerService::class);

        $this->rejected(fn () => $service->changeStatus($lecturer, LecturerStatus::Resigned));

        $e = $this->rejected(fn () => $service->changeStatus($lecturer, LecturerStatus::Resigned, reason: 'Xin thôi việc'));
        $this->assertStringContainsString('3 lớp đang dạy', $e->getMessage());
        $this->assertStringContainsString('INT303.01', $e->getMessage());
        $this->assertSame(LecturerStatus::Working, $lecturer->refresh()->status);
    }

    public function test_nghi_viec_khi_con_chuc_vu_quan_ly_bi_chan(): void
    {
        $lecturer = $this->lecturer();
        $this->leadership(['unit_type' => 'department', 'unit_id' => $this->ktpm->id, 'user_id' => $lecturer->user_id, 'position' => 'head']);

        $e = $this->rejected(fn () => $this->lecturers->changeStatus($lecturer, LecturerStatus::Retired, reason: 'Đủ tuổi nghỉ hưu'));
        $this->assertStringContainsString('Trưởng bộ môn BM-KTPM', $e->getMessage());
    }

    public function test_nghi_viec_giu_lich_su_ket_thuc_kiem_nhiem_va_khoa_phan_cong_moi(): void
    {
        $lecturer = $this->lecturer();
        $unit = $this->lecturers->addUnit($lecturer, ['unit_type' => 'department', 'unit_id' => $this->httt->id, 'starts_on' => $this->day(-30)]);
        $future = $this->lecturers->addUnit($lecturer, ['unit_type' => 'faculty', 'unit_id' => Faculty::query()->where('code', 'KT')->value('id'), 'starts_on' => $this->day(30)]);

        $left = $this->lecturers->changeStatus($lecturer, LecturerStatus::Resigned, $this->day(5), 'Chuyển công tác');

        $this->assertSame(LecturerStatus::Resigned, $left->status);
        $this->assertSame($this->day(5), $left->left_on->toDateString());
        $this->assertFalse($left->isAssignable());
        $this->assertSame($this->day(5), $unit->refresh()->ends_on->toDateString());
        $this->assertSoftDeleted($future);
        $this->assertDatabaseHas('audit_logs', ['auditable_id' => $lecturer->id, 'event' => 'updated', 'reason' => 'Nghỉ việc: Chuyển công tác']);

        $this->rejected(fn () => $this->lecturers->addUnit($left, ['unit_type' => 'department', 'unit_id' => $this->httt->id]));

        $back = $this->lecturers->changeStatus($left, LecturerStatus::Working);
        $this->assertTrue($back->isAssignable());
        $this->assertNull($back->left_on);
    }

    public function test_bo_mon_con_giang_vien_dang_lam_viec_thi_khong_ngung_duoc(): void
    {
        $this->lecturer();

        $e = $this->rejected(fn () => app(FacultyService::class)->update($this->ktpm, ['status' => 'inactive']));
        $this->assertStringContainsString('1 giảng viên', $e->getMessage());
    }

    public function test_giao_chuc_vu_chi_cho_giang_vien_dang_lam_viec_thuoc_don_vi(): void
    {
        $lecturer = $this->lecturer();
        $head = ['unit_type' => 'department', 'unit_id' => $this->httt->id, 'user_id' => $lecturer->user_id, 'position' => 'head'];

        // Không thuộc BM-HTTT
        $e = $this->rejected(fn () => $this->leadership($head));
        $this->assertStringContainsString('không thuộc BM-HTTT', $e->getMessage());

        // Kiêm nhiệm ở BM-HTTT thì được
        $this->lecturers->addUnit($lecturer, ['unit_type' => 'department', 'unit_id' => $this->httt->id]);
        $this->leadership($head);

        // Trưởng khoa CNTT: bộ môn chính BM-KTPM thuộc khoa nên đủ điều kiện về đơn vị, nhưng đang là trưởng BM-HTTT (BR-TCH-06)
        $cntt = Faculty::query()->where('code', 'CNTT')->firstOrFail();
        $e = $this->rejected(fn () => $this->leadership(['unit_type' => 'faculty', 'unit_id' => $cntt->id, 'user_id' => $lecturer->user_id, 'position' => 'dean']));
        $this->assertStringContainsString('BR-TCH-06', $e->getMessage());

        // Phó trưởng khoa thì không vướng BR-TCH-06
        $this->leadership(['unit_type' => 'faculty', 'unit_id' => $cntt->id, 'user_id' => $lecturer->user_id, 'position' => 'vice_dean']);
    }

    public function test_giang_vien_da_nghi_va_tai_khoan_sinh_vien_khong_duoc_giao_chuc_vu(): void
    {
        $lecturer = $this->lecturers->changeStatus($this->lecturer(), LecturerStatus::Retired, reason: 'Nghỉ hưu');

        $e = $this->rejected(fn () => $this->leadership(['unit_type' => 'department', 'unit_id' => $this->ktpm->id, 'user_id' => $lecturer->user_id, 'position' => 'head']));
        $this->assertStringContainsString('Nghỉ hưu', $e->getMessage());

        $student = User::factory()->create();
        $student->forceFill(['profile_type' => ProfileType::Student, 'profile_id' => 1])->save();
        $this->rejected(fn () => $this->leadership(['unit_type' => 'department', 'unit_id' => $this->ktpm->id, 'user_id' => $student->id, 'position' => 'vice_head']));
    }

    public function test_cau_hinh_cho_phep_giao_chuc_vu_ngoai_don_vi(): void
    {
        config(['studentmanager.faculty.allow_leader_outside_unit' => true]);
        $lecturer = $this->lecturer();

        $this->leadership(['unit_type' => 'department', 'unit_id' => $this->httt->id, 'user_id' => $lecturer->user_id, 'position' => 'vice_head']);
        $this->assertDatabaseHas('leadership_terms', ['user_id' => $lecturer->user_id, 'unit_id' => $this->httt->id]);
    }
}
