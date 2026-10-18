<?php

namespace Tests\Feature\Modules\Subject;

use App\Models\User;
use App\Modules\Faculty\Models\Department;
use App\Modules\Faculty\Services\LeadershipService;
use App\Modules\Subject\Models\Subject;
use App\Modules\Subject\Models\Syllabus;
use App\Modules\Subject\Services\SyllabusService;
use App\Modules\Teacher\Models\Lecturer;
use App\Support\Exceptions\BusinessRuleException;
use Database\Seeders\FacultySeeder;
use Database\Seeders\SubjectSeeder;
use Database\Seeders\TeacherSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

class SyllabusTest extends TestCase
{
    use DatabaseMigrations;
    use InteractsWithRoles;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        Storage::fake('local');
        $this->seedRoles();
        $this->seed([FacultySeeder::class, SubjectSeeder::class, TeacherSeeder::class]);
        User::query()->where('profile_type', 'teacher')->update(['must_change_password' => false, 'temporary_password_expires_at' => null]);
    }

    private function subject(string $code = 'INT301'): Subject
    {
        return Subject::query()->where('code', $code)->firstOrFail();
    }

    private function lecturerUser(string $code = 'GV0002'): User
    {
        return Lecturer::query()->where('code', $code)->firstOrFail()->user;
    }

    private function headOf(string $departmentCode, string $lecturerCode): User
    {
        $user = $this->lecturerUser($lecturerCode);
        app(LeadershipService::class)->assign([
            'unit_type' => 'department', 'unit_id' => Department::query()->where('code', $departmentCode)->value('id'),
            'user_id' => $user->id, 'position' => 'head', 'starts_on' => now(config('studentmanager.display_timezone'))->toDateString(),
        ]);

        return $user->refresh();
    }

    /** @return array<string, mixed> */
    private function content(): array
    {
        return [
            'objectives' => 'Nắm quy trình phát triển phần mềm.',
            'learning_outcomes' => [['code' => 'CLO1', 'description' => 'Phân tích yêu cầu'], ['code' => 'CLO2', 'description' => 'Thiết kế hệ thống']],
            'content' => [['unit' => 'Tuần 1', 'topic' => 'Tổng quan', 'hours' => 3], ['unit' => 'Tuần 2', 'topic' => 'Yêu cầu', 'hours' => 3]],
            'teaching_methods' => 'Thuyết giảng, thảo luận, đồ án nhóm.',
            'materials' => [['title' => 'Software Engineering (Sommerville)', 'kind' => 'main']],
        ];
    }

    private function pdf(): UploadedFile
    {
        return UploadedFile::fake()->createWithContent('de-cuong.pdf', "%PDF-1.4\n1 0 obj << /Type /Catalog >> endobj\ntrailer << /Root 1 0 R >>\n%%EOF");
    }

    public function test_giang_vien_bo_mon_soan_gui_duyet_va_truong_bo_mon_duyet(): void
    {
        $subject = $this->subject();   // BM-KTPM
        $this->actingAs($this->lecturerUser('GV0002'));

        $id = $this->postJson("/admin/subjects/{$subject->id}/syllabi")->assertCreated()->assertJsonPath('version', 1)->json('id');
        $this->postJson("/admin/subjects/{$subject->id}/syllabi")->assertStatus(422);

        // Chưa đủ nội dung thì chưa gửi duyệt được
        $this->postJson("/admin/syllabi/{$id}/submit")->assertStatus(422);

        $this->putJson("/admin/syllabi/{$id}", $this->content())->assertOk()->assertJsonPath('learning_outcomes.1.code', 'CLO2');
        $this->postJson("/admin/syllabi/{$id}/file", ['file' => $this->pdf()])->assertOk()->assertJsonPath('has_file', true);
        $this->postJson("/admin/syllabi/{$id}/submit")->assertOk()->assertJsonPath('status', 'submitted');
        $this->putJson("/admin/syllabi/{$id}", ['objectives' => 'Sửa sau khi gửi'])->assertStatus(422);

        // Giảng viên không có quyền duyệt
        $this->postJson("/admin/syllabi/{$id}/approve")->assertForbidden();

        $this->actingAs($this->headOf('BM-KTPM', 'GV0001'));
        $this->postJson("/admin/syllabi/{$id}/approve", ['note' => 'Đạt'])->assertOk()->assertJsonPath('status', 'approved');

        $this->assertSame(1, app(SyllabusService::class)->applicableFor($subject)?->version);
        $this->assertDatabaseHas('audit_logs', ['event' => 'approved', 'auditable_id' => $id]);
    }

    public function test_giang_vien_chi_soan_de_cuong_hoc_phan_bo_mon_minh(): void
    {
        $this->actingAs($this->lecturerUser('GV0002'));   // BM-KTPM

        $this->postJson('/admin/subjects/'.$this->subject('INT203')->id.'/syllabi')->assertForbidden();   // BM-HTTT
        $this->getJson('/admin/subjects')->assertOk()->assertJsonPath('total', 10);
    }

    public function test_tra_lai_sua_va_gui_lai_khong_tu_duyet(): void
    {
        $subject = $this->subject();
        $head = $this->headOf('BM-KTPM', 'GV0001');

        // Trưởng bộ môn tự soạn thì không tự duyệt được
        $this->actingAs($head);
        $own = $this->postJson("/admin/subjects/{$subject->id}/syllabi")->json('id');
        $this->putJson("/admin/syllabi/{$own}", $this->content())->assertOk();
        $this->postJson("/admin/syllabi/{$own}/submit")->assertOk();
        $this->postJson("/admin/syllabi/{$own}/approve")->assertStatus(422)->assertJsonPath('message', fn ($m) => str_contains($m, 'chính mình'));

        $this->actingAs($this->userWithRoles('ACAD'));
        $this->postJson("/admin/syllabi/{$own}/reject")->assertStatus(422)->assertJsonValidationErrors('note');
        $this->postJson("/admin/syllabi/{$own}/reject", ['note' => 'Thiếu chuẩn đầu ra kỹ năng'])->assertOk()->assertJsonPath('status', 'rejected');

        $this->actingAs($head);
        $this->putJson("/admin/syllabi/{$own}", ['objectives' => 'Bổ sung'])->assertOk()->assertJsonPath('status', 'draft');
        $this->postJson("/admin/syllabi/{$own}/submit")->assertOk();

        $this->actingAs($this->userWithRoles('ACAD'));
        $this->postJson("/admin/syllabi/{$own}/approve")->assertOk();
    }

    public function test_phien_ban_moi_chep_noi_dung_va_ban_duyet_moi_thay_ban_cu(): void
    {
        $service = app(SyllabusService::class);
        $subject = $this->subject();
        $acad = $this->userWithRoles('ACAD');

        $this->actingAs($this->lecturerUser('GV0002'));
        $v1 = $service->createVersion($subject);
        $service->update($v1, $this->content());
        $service->submit($v1);
        $service->approve($v1, $acad);

        $v2 = $service->createVersion($subject);
        $this->assertSame(2, $v2->version);
        $this->assertSame('CLO1', $v2->learning_outcomes[0]['code']);
        $service->update($v2, ['objectives' => 'Mục tiêu mới']);
        $service->submit($v2);

        // Bản 2 chưa duyệt: lớp mới vẫn dùng bản 1
        $this->assertSame(1, $service->applicableFor($subject)->version);
        $service->approve($v2, $acad);

        $this->assertSame(2, $service->applicableFor($subject)->version);
        $this->assertSame('superseded', $v1->refresh()->status->value);
    }

    public function test_yeu_cau_duyet_truoc_khi_mo_lop_bat_tat_bang_cau_hinh(): void
    {
        $service = app(SyllabusService::class);
        $subject = $this->subject();

        try {
            $service->assertUsableForNewSection($subject);
            $this->fail('Lẽ ra phải từ chối.');
        } catch (BusinessRuleException $e) {
            $this->assertStringContainsString('chưa có đề cương được duyệt', $e->getMessage());
        }

        $this->actingAs($this->lecturerUser('GV0002'));
        $draft = $service->createVersion($subject);
        $service->update($draft, $this->content());
        $service->submit($draft);
        $this->assertNull($service->applicableFor($subject));

        config(['studentmanager.subject.syllabus_requires_approval' => false]);
        $this->assertSame($draft->id, $service->assertUsableForNewSection($subject)->id);
    }

    public function test_tep_phai_la_pdf_that_luu_rieng_tu_va_tai_theo_quyen(): void
    {
        $subject = $this->subject();
        $this->actingAs($this->lecturerUser('GV0002'));
        $id = $this->postJson("/admin/subjects/{$subject->id}/syllabi")->json('id');

        // Đổi đuôi tệp khác thành .pdf: bị chặn
        $fake = UploadedFile::fake()->createWithContent('de-cuong.pdf', "<?php echo 'x';");
        $this->postJson("/admin/syllabi/{$id}/file", ['file' => $fake])->assertStatus(422);
        $this->postJson("/admin/syllabi/{$id}/file", ['file' => UploadedFile::fake()->create('a.docx', 10)])->assertStatus(422);

        $this->postJson("/admin/syllabi/{$id}/file", ['file' => $this->pdf()])->assertOk();
        $path = Syllabus::query()->find($id)->file_path;
        Storage::disk('local')->assertExists($path);
        $this->assertStringStartsWith("syllabi/{$subject->id}/", $path);
        $this->assertStringNotContainsString('de-cuong', $path);

        $this->get("/admin/syllabi/{$id}/file")->assertOk()->assertDownload('de-cuong.pdf');

        // Sinh viên không thấy bản nháp
        $this->actingAs($this->userWithRoles('STU'));
        $this->get("/admin/syllabi/{$id}/file")->assertNotFound();
        $this->getJson("/admin/subjects/{$subject->id}/syllabi")->assertOk()->assertJsonPath('versions', [])->assertJsonPath('can_edit', false);
    }

    public function test_xoa_ban_nhap_chua_gui(): void
    {
        $subject = $this->subject();
        $this->actingAs($this->lecturerUser('GV0002'));
        $id = $this->postJson("/admin/subjects/{$subject->id}/syllabi")->json('id');

        $this->deleteJson("/admin/syllabi/{$id}")->assertNoContent();
        $this->assertDatabaseMissing('syllabi', ['id' => $id]);
    }
}
