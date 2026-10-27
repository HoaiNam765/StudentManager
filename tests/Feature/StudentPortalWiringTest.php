<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\Curriculum\Models\Curriculum;
use App\Modules\Curriculum\Models\CurriculumBlock;
use App\Modules\Curriculum\Models\CurriculumSubject;
use App\Modules\Curriculum\Models\ElectiveGroup;
use App\Modules\Faculty\Models\Department;
use App\Modules\Grade\Models\CumulativeGpa;
use App\Modules\Grade\Services\TranscriptService;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Models\StudentChangeRequest;
use App\Modules\Student\Models\StudentContact;
use App\Modules\Student\Models\StudentCurriculum;
use App\Modules\Subject\Models\Subject;
use App\Modules\Subject\Models\SubjectRelation;
use Carbon\CarbonImmutable;
use Database\Seeders\AcademicYearSeeder;
use Database\Seeders\FacultySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

class StudentPortalWiringTest extends TestCase
{
    use InteractsWithRoles, RefreshDatabase;

    private User $user;

    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
        $this->seed([FacultySeeder::class, AcademicYearSeeder::class]);
        $this->user = $this->userWithRoles('STU');
        $this->student = Student::factory()->create([
            'user_id' => $this->user->id, 'student_code' => 'PORTAL0001', 'full_name' => 'Sinh Viên Kiểm Tra',
            'permanent_address' => 'Địa chỉ phải giữ nguyên',
        ]);
        $this->actingAs($this->user);
    }

    public function test_dashboard_renders_own_data_and_real_navigation(): void
    {
        CumulativeGpa::create(['student_id' => $this->student->id, 'gpa' => 2.75, 'credits_earned' => 21, 'classification' => 'Khá']);
        Student::factory()->create(['full_name' => 'Hồ Sơ Không Được Hiện']);

        // Dashboard dùng view của nhóm FE (student-dashboard, student-mobile); BE chỉ truyền dữ liệu thật
        foreach (['student.home', 'student.mobile'] as $route) {
            $this->get(route($route))->assertOk()->assertSee('2.75')
                ->assertDontSee('Nguyễn Văn An')->assertDontSee('Hồ Sơ Không Được Hiện');
        }
        $this->get(route('student.mobile'))->assertSee('Sinh Viên Kiểm Tra')->assertSee('PORTAL0001');
        // Menu cổng (PortalMenu) dẫn tới các trang thật
        $this->get(route('student.home'))->assertSee(route('student.pages.registration'), false)
            ->assertSee(route('student.pages.grades'), false)->assertSee(route('student.pages.timetable'), false)
            ->assertSee(route('student.pages.profile'), false)->assertSee(route('student.pages.curriculum'), false);
    }

    public function test_pages_use_actual_profile_and_handle_unpublished_or_missing_data(): void
    {
        $this->get(route('student.pages.profile'))->assertOk()->assertSee('PORTAL0001')->assertDontSee('079203001234');
        $this->get(route('student.pages.curriculum'))->assertOk()->assertSee('Chưa được gán chương trình đào tạo');
        $this->get(route('student.pages.timetable', ['date' => '2026-10-22']))->assertOk()
            ->assertSee('chưa công bố')->assertDontSee('241_71ITEC3201_01');
        $this->get(route('student.pages.timetable', ['date' => 'invalid']))->assertSessionHasErrors('date');
    }

    public function test_timetable_term_selector_opens_first_week_of_chosen_term(): void
    {
        $today = now(config('studentmanager.display_timezone'))->toDateString();
        $other = Term::query()->with('academicYear')->where(fn ($q) => $q->where('start_date', '>', $today)->orWhere('end_date', '<', $today))
            ->orderBy('start_date')->firstOrFail();

        $this->get(route('student.pages.timetable', ['term_id' => $other->id]))->assertOk()
            ->assertSee('name="term_id"', false)
            ->assertSee('value="'.$other->id.'" selected', false)
            ->assertSee($other->name.' · '.$other->academicYear->name)
            ->assertSee(CarbonImmutable::parse($other->start_date)->startOfWeek()->format('d/m/Y'));
    }

    public function test_profile_form_persists_only_own_validated_fields_and_preserves_masked_address(): void
    {
        $other = Student::factory()->create(['phone' => '0901111222']);
        $this->from(route('student.pages.profile'))->put(route('student.pages.profile.update'), [
            'phone' => '0901234567', 'personal_email' => 'updated@example.test', 'permanent_address' => '',
            'student_id' => $other->id,
        ])->assertRedirect(route('student.pages.profile'))->assertSessionHas('status');
        $this->assertSame('0901234567', $this->student->fresh()->phone);
        $this->assertSame('Địa chỉ phải giữ nguyên', $this->student->fresh()->permanent_address);
        $this->assertSame('0901111222', $other->fresh()->phone);
        $this->put(route('student.pages.profile.update'), ['phone' => 'not a phone'])->assertSessionHasErrors('phone');
        $this->put(route('student.pages.profile.update'), ['full_name' => 'Không được sửa trực tiếp'])->assertSessionHasErrors('full_name');
    }

    public function test_change_request_uses_evidence_and_does_not_change_profile_until_approved(): void
    {
        Storage::fake(config('studentmanager.student.decision_disk', 'local'));
        $this->post(route('student.pages.profile.request'), [
            'full_name' => 'Tên Đề Nghị', 'date_of_birth' => '', 'national_id' => '', 'major_id' => '',
            'reason' => 'Đối chiếu giấy khai sinh', 'evidence' => UploadedFile::fake()->create('evidence.pdf', 10, 'application/pdf'),
        ])->assertRedirect()->assertSessionHas('status');
        $change = StudentChangeRequest::where('student_id', $this->student->id)->firstOrFail();
        $this->assertSame('Sinh Viên Kiểm Tra', $this->student->fresh()->full_name);
        $this->assertSame('pending', $change->status);
        $this->get(route('student.pages.profile'))->assertOk()->assertSee('Tên Đề Nghị');
        $this->post(route('student.pages.profile.cancel', $change))->assertRedirect()->assertSessionHas('status');
        $this->assertSame('cancelled', $change->fresh()->status);
    }

    public function test_contact_mutations_cannot_touch_another_student(): void
    {
        $other = Student::factory()->create();
        $contact = StudentContact::create(['student_id' => $other->id, 'full_name' => 'Liên hệ riêng', 'relationship' => 'Mẹ']);
        $this->delete(route('student.pages.profile.contacts.destroy', $contact))->assertNotFound();
        $this->put(route('student.pages.profile.contacts.update', $contact), ['phone' => '0901234567'])->assertNotFound();
        $this->post(route('student.pages.profile.contacts.store'), ['full_name' => 'Người thân', 'relationship' => 'Cha', 'phone' => '0901234567'])
            ->assertRedirect()->assertSessionHas('status');
        $this->assertDatabaseHas('student_contacts', ['student_id' => $this->student->id, 'full_name' => 'Người thân']);
    }

    public function test_account_without_profile_does_not_get_sample_records(): void
    {
        $this->actingAs($this->userWithRoles('STU'))->get(route('student.home'))->assertOk()
            ->assertSee('Chưa liên kết hồ sơ')->assertDontSee('Nguyễn Văn An')->assertDontSee('3.42');
    }

    public function test_curriculum_displays_assigned_subjects_without_fake_completion(): void
    {
        $curriculum = Curriculum::create([
            'code' => 'PORTAL-CTDT', 'name' => 'Chương trình kiểm thử', 'major_id' => $this->student->major_id,
            'training_type_id' => $this->student->training_type_id, 'cohort_from' => 2026,
            'total_credits' => 3, 'standard_terms' => 8, 'status' => 'published',
        ]);
        $block = CurriculumBlock::create(['curriculum_id' => $curriculum->id, 'name' => 'Khối kiểm thử', 'block_type' => 'general', 'required_credits' => 3]);
        $subject = Subject::create([
            'code' => 'PORTAL101', 'name' => 'Học phần từ cơ sở dữ liệu', 'department_id' => Department::firstOrFail()->id,
            'credits' => 3, 'theory_credits' => 3, 'knowledge_block' => 'general',
        ]);
        CurriculumSubject::create(['curriculum_id' => $curriculum->id, 'curriculum_block_id' => $block->id, 'subject_id' => $subject->id, 'planned_term' => 1]);
        StudentCurriculum::create(['student_id' => $this->student->id, 'curriculum_id' => $curriculum->id, 'effective_from' => now()->toDateString()]);

        $this->get(route('student.pages.curriculum'))->assertOk()->assertSee('PORTAL101')
            ->assertSee('Học phần từ cơ sở dữ liệu')->assertSee('0 / 3 TC đã đạt')->assertDontSee('Hoàn thành 100%');
    }

    public function test_curriculum_term_view_groups_subjects_by_planned_term(): void
    {
        $curriculum = Curriculum::create([
            'code' => 'PORTAL-HK', 'name' => 'Chương trình theo học kỳ', 'major_id' => $this->student->major_id,
            'training_type_id' => $this->student->training_type_id, 'cohort_from' => 2026,
            'total_credits' => 9, 'standard_terms' => 3, 'status' => 'published',
        ]);
        $block = CurriculumBlock::create(['curriculum_id' => $curriculum->id, 'name' => 'Khối kiểm thử', 'block_type' => 'general', 'required_credits' => 9]);
        $group = ElectiveGroup::create(['curriculum_id' => $curriculum->id, 'curriculum_block_id' => $block->id, 'name' => 'Nhóm kiểm thử', 'min_credits' => 2]);
        $subjects = collect([
            ['PORTAL201', 3, 30, 15, 1, null], ['PORTAL202', 2, 30, 0, 1, $group->id], ['PORTAL203', 2, 30, 0, 1, $group->id],
            ['PORTAL204', 4, 45, 30, 2, null],
        ])->mapWithKeys(function ($s) use ($curriculum, $block) {
            $subject = Subject::create([
                'code' => $s[0], 'name' => 'Học phần '.$s[0], 'department_id' => Department::firstOrFail()->id, 'credits' => $s[1],
                'theory_credits' => $s[1], 'theory_periods' => $s[2], 'practice_periods' => $s[3], 'knowledge_block' => 'general',
            ]);
            CurriculumSubject::create(['curriculum_id' => $curriculum->id, 'curriculum_block_id' => $block->id, 'subject_id' => $subject->id,
                'planned_term' => $s[4], 'curriculum_elective_group_id' => $s[5]]);

            return [$s[0] => $subject];
        });
        SubjectRelation::create(['subject_id' => $subjects['PORTAL204']->id, 'related_subject_id' => $subjects['PORTAL201']->id, 'type' => 'prerequisite']);
        StudentCurriculum::create(['student_id' => $this->student->id, 'curriculum_id' => $curriculum->id, 'effective_from' => now()->toDateString()]);
        // Kết quả học tập lấy từ bảng điểm đã công bố; giả lập một học phần đã đạt
        $this->mock(TranscriptService::class)->shouldReceive('personal')->andReturn(['terms' => [['name' => 'HK1 – 2026-2027', 'rows' => [
            ['subject_code' => 'PORTAL201', 'subject_name' => 'Học phần PORTAL201', 'credits' => 3, 'is_passed' => true, 'score10' => 8.0, 'letter' => 'B+'],
        ]]]]);

        // Tổng HK1 = 3 bắt buộc + 2 tối thiểu của nhóm (tính một lần, không cộng từng học phần tự chọn)
        $this->get(route('student.pages.curriculum'))->assertOk()
            ->assertSee('Theo học kỳ')->assertSee('✓', false)
            ->assertSeeInOrder(['Học kỳ 1', '(Tổng: 5 TC)', 'Học phần bắt buộc', 'PORTAL201', 'Học phần tự chọn',
                'Nhóm tự chọn: Nhóm kiểm thử', 'chọn tối thiểu 2 TC', 'PORTAL202', 'PORTAL203',
                'Học kỳ 2', '(Tổng: 4 TC)', 'PORTAL204', 'PORTAL201', '(tiên quyết)', 'Học kỳ 3', '(Tổng: 0 TC)'])
            ->assertSee('3 / 5 TC đã đạt')
            ->assertSee('không lấy học phần nhóm này thay cho nhóm khác');
    }

    public function test_curriculum_term_view_without_profile_or_curriculum(): void
    {
        // "Học kỳ 1" còn có ở nhãn học kỳ hiện tại trên đầu trang nên kiểm tra khối học kỳ bằng thuộc tính data-term
        $this->get(route('student.pages.curriculum'))->assertOk()->assertDontSee('data-term=', false);
        $this->actingAs($this->userWithRoles('STU'))->get(route('student.pages.curriculum'))->assertOk()
            ->assertSee('Chưa được gán chương trình đào tạo')->assertDontSee('data-term=', false);
    }
}
