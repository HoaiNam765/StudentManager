<?php

namespace Tests\Feature\Modules\Teacher;

use App\Models\User;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Faculty\Models\Department;
use App\Modules\Faculty\Services\LeadershipService;
use App\Modules\Teacher\Models\Lecturer;
use App\Modules\Teacher\Models\LecturerChangeRequest;
use Database\Seeders\FacultySeeder;
use Database\Seeders\TeacherSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

class LecturerApiTest extends TestCase
{
    use DatabaseMigrations;
    use InteractsWithRoles;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        $this->seedRoles();
        $this->seed([FacultySeeder::class, TeacherSeeder::class]);

        // Tài khoản giảng viên mới có mật khẩu tạm (buộc đổi ở lần đầu, JSON trả 428); test coi như đã đổi
        User::query()->where('profile_type', 'teacher')->update(['must_change_password' => false, 'temporary_password_expires_at' => null]);
    }

    private function department(string $code): Department
    {
        return Department::query()->where('code', $code)->firstOrFail();
    }

    private function lecturer(string $code): Lecturer
    {
        return Lecturer::query()->where('code', $code)->firstOrFail();
    }

    /** Trưởng bộ môn: một giảng viên của bộ môn được giao nhiệm kỳ trưởng (vai trò DEAN theo nhiệm kỳ). */
    private function headOf(string $departmentCode, string $lecturerCode): User
    {
        $user = $this->lecturer($lecturerCode)->user;

        app(LeadershipService::class)->assign([
            'unit_type' => 'department', 'unit_id' => $this->department($departmentCode)->id, 'user_id' => $user->id,
            'position' => 'head', 'starts_on' => now(config('studentmanager.display_timezone'))->toDateString(),
        ]);

        return $user->refresh();
    }

    public function test_seeder_co_20_giang_vien_moi_nguoi_mot_tai_khoan_va_chay_lap_lai_an_toan(): void
    {
        $this->seed(TeacherSeeder::class);

        $this->assertSame(20, Lecturer::count());
        $this->assertSame(20, Lecturer::query()->whereNotNull('user_id')->distinct()->count('user_id'));
        $this->assertTrue($this->lecturer('GV0001')->user->hasRole('LEC'));
    }

    public function test_can_bo_dao_tao_tao_ho_so_kem_tai_khoan_tim_va_loc(): void
    {
        $this->actingAs($this->userWithRoles('ACAD'));

        $this->postJson('/admin/lecturers', [
            'code' => ' gv0100 ', 'full_name' => 'Đinh Thị Xuân', 'email' => 'GV0100@StudentManager.test',
            'department_id' => $this->department('BM-MMT')->id, 'academic_title' => 'lecturer', 'academic_degree' => 'master',
            'national_id' => '001199000123', 'date_of_birth' => '1990-05-20',
        ])
            ->assertCreated()
            ->assertJsonPath('code', 'GV0100')
            ->assertJsonPath('username', 'GV0100')
            ->assertJsonPath('email', 'gv0100@studentmanager.test')
            ->assertJsonPath('faculty.code', 'CNTT')
            ->assertJsonPath('academic_degree_label', 'Thạc sĩ');

        $this->postJson('/admin/lecturers', ['code' => 'GV0101', 'full_name' => 'Thiếu email', 'department_id' => $this->department('BM-MMT')->id])
            ->assertStatus(422)->assertJsonPath('message', fn ($m) => str_contains($m, 'Cần email'));

        $this->postJson('/admin/lecturers', ['code' => 'GV0102', 'full_name' => 'Sai CCCD', 'department_id' => $this->department('BM-MMT')->id, 'national_id' => '123', 'account' => 'none'])
            ->assertStatus(422)->assertJsonValidationErrors('national_id');

        $this->getJson('/admin/lecturers?q=dinh thi xuan')->assertOk()->assertJsonPath('total', 1);
        $this->getJson('/admin/lecturers?department_id='.$this->department('BM-KTPM')->id)->assertOk()->assertJsonPath('total', 4);
        $this->getJson('/admin/lecturers?academic_degree=doctor')->assertOk()->assertJsonPath('total', 7);
    }

    public function test_sinh_vien_chi_xem_thong_tin_co_ban_khong_thay_cccd(): void
    {
        $id = $this->lecturer('GV0001')->id;
        $this->lecturer('GV0001')->update(['national_id' => '001099000999', 'phone' => '0900000001']);

        $this->actingAs($this->userWithRoles('STU'));

        $response = $this->getJson("/admin/lecturers/{$id}")->assertOk()->assertJsonPath('code', 'GV0001');
        $this->assertArrayNotHasKey('national_id', $response->json());
        $this->assertArrayNotHasKey('phone', $response->json());

        $this->postJson('/admin/lecturers', [])->assertForbidden();
        $this->putJson("/admin/lecturers/{$id}", ['full_name' => 'X'])->assertForbidden();
    }

    public function test_can_bo_xem_cccd_duoc_ghi_nhat_ky_xem_du_lieu_nhay_cam(): void
    {
        $lecturer = $this->lecturer('GV0001');
        $lecturer->update(['national_id' => '001099000999']);

        $this->actingAs($this->userWithRoles('ACAD'));
        $this->getJson("/admin/lecturers/{$lecturer->id}")->assertOk()->assertJsonPath('national_id', '001099000999');

        $this->assertDatabaseHas('audit_logs', ['event' => 'view_sensitive', 'auditable_id' => $lecturer->id]);
    }

    public function test_giang_vien_chi_xem_va_tu_sua_lien_he_cua_minh(): void
    {
        $me = $this->lecturer('GV0002');
        $other = $this->lecturer('GV0003');
        $this->actingAs($me->user);

        $this->getJson('/admin/lecturers')->assertOk()->assertJsonPath('total', 1)->assertJsonPath('data.0.code', 'GV0002');
        $this->getJson("/admin/lecturers/{$other->id}")->assertForbidden();
        $this->putJson("/admin/lecturers/{$other->id}", ['phone' => '1'])->assertForbidden();

        $this->getJson('/teacher/profile')->assertOk()->assertJsonPath('code', 'GV0002')->assertJsonPath('pending_request', null);

        $this->putJson('/teacher/profile', ['phone' => '0912345678'])->assertOk()->assertJsonPath('phone', '0912345678')->assertJsonPath('change_request', null);

        // Đơn vị, mã, CCCD: giảng viên không tự sửa (kể cả gọi API quản trị)
        $this->putJson('/teacher/profile', ['department_id' => $this->department('BM-HTTT')->id])
            ->assertStatus(422)->assertJsonPath('message', fn ($m) => str_contains($m, 'department_id'));
        $this->putJson("/admin/lecturers/{$me->id}", ['national_id' => '001099000111'])->assertStatus(422);
        $this->postJson("/admin/lecturers/{$me->id}/status", ['status' => 'resigned', 'reason' => 'X'])->assertForbidden();
    }

    public function test_doi_ho_ten_hoc_vi_qua_de_nghi_cho_can_bo_khac_xac_nhan(): void
    {
        $me = $this->lecturer('GV0002');
        $this->actingAs($me->user);

        $requestId = $this->putJson('/teacher/profile', ['full_name' => 'Trần Thị Bình An', 'academic_degree' => 'doctor', 'reason' => 'Bảo vệ luận án tiến sĩ'])
            ->assertOk()
            ->assertJsonPath('full_name', 'Trần Thị Bình')
            ->assertJsonPath('change_request.status', 'pending')
            ->assertJsonPath('change_request.changes.academic_degree.new', 'doctor')
            ->json('change_request.id');

        $this->putJson('/teacher/profile', ['academic_title' => 'senior_lecturer'])->assertStatus(422);

        // Giảng viên không có quyền xác nhận
        $this->postJson("/admin/lecturer-change-requests/{$requestId}/approve")->assertForbidden();

        $this->actingAs($this->userWithRoles('ACAD'));
        $this->getJson('/admin/lecturer-change-requests')->assertOk()->assertJsonPath('total', 1);
        $this->postJson("/admin/lecturer-change-requests/{$requestId}/approve", ['note' => 'Đã kiểm tra bằng'])->assertOk()->assertJsonPath('status', 'approved');

        $me->refresh();
        $this->assertSame('Trần Thị Bình An', $me->full_name);
        $this->assertSame('doctor', $me->academic_degree->value);
        $this->assertSame('Trần Thị Bình An', $me->user->name);
        $this->postJson("/admin/lecturer-change-requests/{$requestId}/reject", ['note' => 'X'])->assertStatus(422);
    }

    public function test_giang_vien_huy_de_nghi_va_truong_bo_mon_tu_choi_de_nghi(): void
    {
        $me = $this->lecturer('GV0003');
        $this->actingAs($me->user);

        $first = $this->putJson('/teacher/profile', ['full_name' => 'Lê Hoàng Cường A'])->json('change_request.id');
        $this->deleteJson("/teacher/profile/change-requests/{$first}")->assertOk()->assertJsonPath('status', 'cancelled');

        $second = $this->putJson('/teacher/profile', ['full_name' => 'Lê Hoàng Cường B'])->json('change_request.id');

        $head = $this->headOf('BM-KTPM', 'GV0001');
        $this->actingAs($head);
        $this->postJson("/admin/lecturer-change-requests/{$second}/reject")->assertStatus(422)->assertJsonValidationErrors('note');
        $this->postJson("/admin/lecturer-change-requests/{$second}/reject", ['note' => 'Thiếu giấy tờ'])->assertOk()->assertJsonPath('status', 'rejected');

        $this->assertSame('Lê Hoàng Cường', $me->refresh()->full_name);
        $this->assertSame(LecturerChangeRequest::REJECTED, LecturerChangeRequest::query()->find($second)->status);
    }

    public function test_truong_bo_mon_khong_tu_xac_nhan_de_nghi_cua_chinh_minh(): void
    {
        $head = $this->headOf('BM-KTPM', 'GV0001');
        $this->actingAs($head);

        $id = $this->putJson('/teacher/profile', ['academic_title' => 'associate_professor'])->assertOk()->json('change_request.id');

        $this->postJson("/admin/lecturer-change-requests/{$id}/approve")
            ->assertStatus(422)
            ->assertJsonPath('message', fn ($m) => str_contains($m, 'chính mình'));
    }

    public function test_truong_bo_mon_chi_quan_ly_giang_vien_thuoc_don_vi_minh(): void
    {
        $head = $this->headOf('BM-KTPM', 'GV0001');
        $this->actingAs($head);

        $this->getJson('/admin/lecturers')->assertOk()->assertJsonPath('total', 4);

        $own = $this->lecturer('GV0002');
        $other = $this->lecturer('GV0004'); // BM-HTTT
        $this->putJson("/admin/lecturers/{$own->id}", ['expertise' => 'Kiểm thử tự động'])->assertOk()->assertJsonPath('expertise', 'Kiểm thử tự động');
        $this->getJson("/admin/lecturers/{$other->id}")->assertForbidden();

        $this->postJson('/admin/lecturers', [
            'code' => 'GV0200', 'full_name' => 'Ngoài đơn vị', 'department_id' => $this->department('BM-HTTT')->id, 'account' => 'none',
        ])->assertForbidden();
        $this->postJson('/admin/lecturers', [
            'code' => 'GV0201', 'full_name' => 'Trong đơn vị', 'department_id' => $this->department('BM-KTPM')->id, 'account' => 'none',
        ])->assertCreated();

        // Giảng viên bộ môn khác kiêm nhiệm ở BM-KTPM thì trưởng BM-KTPM thấy được
        $this->actingAs($this->userWithRoles('ACAD'));
        $this->postJson("/admin/lecturers/{$other->id}/units", ['unit_type' => 'department', 'unit_id' => $this->department('BM-KTPM')->id])->assertCreated();

        $this->actingAs($head);
        app(AccessControl::class)->flush();
        $this->getJson("/admin/lecturers/{$other->id}")->assertOk();
    }

    public function test_ghi_nhan_nghi_viec_qua_api_va_xem_truoc_viec_can_chuyen(): void
    {
        $lecturer = $this->lecturer('GV0005');
        $this->actingAs($this->userWithRoles('ACAD'));

        $this->getJson("/admin/lecturers/{$lecturer->id}/handover")->assertOk()->assertJsonPath('items', []);
        $this->postJson("/admin/lecturers/{$lecturer->id}/status", ['status' => 'resigned'])->assertStatus(422);
        $this->postJson("/admin/lecturers/{$lecturer->id}/status", ['status' => 'resigned', 'reason' => 'Quyết định số 12/QĐ'])
            ->assertOk()->assertJsonPath('status_label', 'Nghỉ việc');

        $this->getJson('/admin/lecturers?status=working')->assertOk()->assertJsonPath('total', 19);
    }

    public function test_giang_vien_doi_anh_ho_so(): void
    {
        Storage::fake('public');
        $me = $this->lecturer('GV0006');
        $this->actingAs($me->user);

        $this->postJson('/teacher/profile/photo', ['photo' => UploadedFile::fake()->create('cv.pdf', 10, 'application/pdf')])->assertStatus(422);
        $this->postJson('/teacher/profile/photo', ['photo' => UploadedFile::fake()->image('anh.jpg', 300, 400)])->assertOk();

        Storage::disk('public')->assertExists($me->refresh()->photo_path);
    }

    public function test_tai_khoan_moi_phai_doi_mat_khau_tam_truoc_khi_dung_cong_giang_vien(): void
    {
        $this->actingAs($this->userWithRoles('ACAD'));
        $this->postJson('/admin/lecturers', [
            'code' => 'GV0300', 'full_name' => 'Giảng viên mới', 'email' => 'gv0300@studentmanager.test', 'department_id' => $this->department('BM-TA')->id,
        ])->assertCreated();

        $this->actingAs($this->lecturer('GV0300')->user);
        $this->getJson('/teacher/profile')->assertStatus(428);
    }

    public function test_tai_khoan_khong_co_ho_so_giang_vien_nhan_404_o_cong_giang_vien(): void
    {
        $this->actingAs($this->userWithRoles('LEC'));

        $this->getJson('/teacher/profile')->assertNotFound();
    }
}
