<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grade\Models\GradeEntry;
use App\Modules\Grade\Services\GradebookService;
use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\SectionInstructor;
use App\Modules\Student\Models\Student;
use Database\Seeders\AcademicYearSeeder;
use Database\Seeders\CurriculumSeeder;
use Database\Seeders\FacultySeeder;
use Database\Seeders\GradeStructureSeeder;
use Database\Seeders\NotificationEventSeeder;
use Database\Seeders\RoomSeeder;
use Database\Seeders\SchoolClassSeeder;
use Database\Seeders\SubjectSeeder;
use Database\Seeders\SystemSeeder;
use Database\Seeders\TeacherSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Writer\XLSX\Writer;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

/**
 * Sổ điểm Excel của giảng viên (yêu cầu người dùng 28/10): tải về có đủ sinh viên, điểm đã có và cột kết quả bằng công
 * thức; nhập lại nhận mọi ô điểm, ô trống = chưa có điểm, lỗi báo theo dòng và không lưu gì.
 */
class GradebookSpreadsheetTest extends TestCase
{
    use InteractsWithRoles;
    use RefreshDatabase;

    private CourseSection $section;

    private User $lecturer;

    /** @var list<Enrollment> */
    private array $enrollments = [];

    /** @var list<array<string, mixed>> */
    private array $components = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
        $this->seed([
            SystemSeeder::class, NotificationEventSeeder::class, FacultySeeder::class, AcademicYearSeeder::class, RoomSeeder::class,
            SubjectSeeder::class, CurriculumSeeder::class, TeacherSeeder::class, GradeStructureSeeder::class, SchoolClassSeeder::class,
        ]);
        $this->section = CourseSection::query()->whereHas('instructors')->orderBy('id')->firstOrFail();
        $this->lecturer = SectionInstructor::query()->where('course_section_id', $this->section->id)->firstOrFail()->lecturer->user;
        $this->lecturer->forceFill(['must_change_password' => false, 'temporary_password_expires_at' => null, 'password_changed_at' => now()])->save();
        app(GradebookService::class)->initialize($this->section, $this->lecturer);
        $this->section->forceFill(['status' => SectionStatus::InProgress])->save();

        foreach (range(1, 3) as $i) {
            $student = Student::factory()->create(['cohort' => 2026, 'student_code' => '26990'.$i]);
            $this->enrollments[] = Enrollment::create([
                'student_id' => $student->id, 'course_section_id' => $this->section->id, 'term_id' => $this->section->term_id,
                'subject_id' => $this->section->subject_id, 'status' => 'registered', 'registered_at' => now(),
            ]);
        }
        $this->components = app(GradebookService::class)->gradebook($this->section, $this->lecturer)['components']->values()->all();
        $this->actingAs($this->lecturer);
    }

    /** Tệp .xlsx tự dựng: dòng tiêu đề MSSV + tên thành phần, mỗi sinh viên một dòng; sheet ẩn _ma với mã lớp. */
    private function workbook(array $rows, ?int $sectionId = null, array $header = []): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'grd').'.xlsx';
        $writer = new Writer;
        $writer->openToFile($path);
        $writer->addRow(Row::fromValues(['Sổ điểm thử']));
        $writer->addRow(Row::fromValues($header ?: ['STT', 'MSSV', 'Họ và tên', ...array_map(fn ($c) => "{$c['name']} ({$c['weight']}%)", $this->components), 'Tổng kết']));
        foreach ($rows as $i => [$code, $scores]) {
            $writer->addRow(new Row([Cell::fromValue($i + 1), Cell::fromValue($code), Cell::fromValue('SV'),
                ...array_map(fn ($v) => Cell::fromValue($v), $scores), Cell::fromValue('=1+1')]));
        }
        $writer->addNewSheetAndMakeItCurrent()->setName('_ma');
        $writer->addRow(Row::fromValues(['course_section_id', $sectionId ?? $this->section->id]));
        $writer->close();

        return new UploadedFile($path, 'so-diem.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
    }

    private function import(UploadedFile $file)
    {
        return $this->post(route('teacher.gradebook.import', $this->section), ['file' => $file], ['Accept' => 'application/json']);
    }

    private function score(int $student, int $component): ?float
    {
        $score = GradeEntry::query()->where('enrollment_id', $this->enrollments[$student]->id)
            ->where('section_grade_component_id', $this->components[$component]['id'])->value('score');

        return $score === null ? null : (float) $score;
    }

    public function test_tai_so_diem_co_du_sinh_vien_diem_da_co_va_cong_thuc(): void
    {
        app(GradebookService::class)->saveDraft($this->section, $this->lecturer, [
            ['enrollment_id' => $this->enrollments[0]->id, 'component_id' => $this->components[0]['id'], 'score' => 8.5],
        ]);

        $response = $this->get(route('teacher.gradebook.template', $this->section))->assertOk();
        $path = $response->baseResponse->getFile()->getPathname();
        $reader = new Reader;
        $reader->open($path);
        $rows = [];
        $sheets = [];
        foreach ($reader->getSheetIterator() as $sheet) {
            $sheets[$sheet->getName()] = $sheet->isVisible();
            foreach ($sheet->getRowIterator() as $i => $row) {
                $rows[$sheet->getName()][$i] = array_map(fn ($c) => $c->getValue(), $row->getCells());
            }
        }
        $reader->close();

        $this->assertSame(['Bảng điểm' => true, '_ma' => false], $sheets);
        $header = $rows['Bảng điểm'][4];
        $this->assertSame(['STT', 'MSSV', 'Họ và tên'], array_slice($header, 0, 3));
        $this->assertSame(['Tổng kết', 'Điểm chữ', 'Hệ 4', 'Kết quả'], array_slice($header, -4));
        $this->assertCount(3, array_slice($rows['Bảng điểm'], 4));
        $first = $rows['Bảng điểm'][5];
        $this->assertSame('269901', (string) $first[1]);
        $this->assertEquals(8.5, $first[3]);
        $this->assertStringStartsWith('=IF(', $first[3 + count($this->components)]);
        $this->assertStringContainsString('LOOKUP(', $first[4 + count($this->components)]);
        $this->assertSame($this->section->id, (int) $rows['_ma'][1][1]);
    }

    public function test_nhap_lai_nhan_moi_o_o_trong_la_chua_co_diem(): void
    {
        $n = count($this->components);
        app(GradebookService::class)->saveDraft($this->section, $this->lecturer, [
            ['enrollment_id' => $this->enrollments[1]->id, 'component_id' => $this->components[0]['id'], 'score' => 5],
        ]);

        $this->import($this->workbook([
            ['269901', array_fill(0, $n, 7.5)],
            ['269902', [null, ...array_fill(0, $n - 1, '8,5')]], // ô trống xóa điểm 5 đã có; dấu phẩy thập phân
            ['269903', array_fill(0, $n, null)],
        ]))->assertOk()->assertJsonPath('saved_count', 3 * $n);

        $this->assertSame(7.5, $this->score(0, $n - 1));
        $this->assertNull($this->score(1, 0));
        $this->assertSame(8.5, $this->score(1, 1));
        $this->assertNull($this->score(2, 0));
    }

    public function test_file_loi_bao_theo_dong_va_khong_luu_gi(): void
    {
        $n = count($this->components);

        $this->import($this->workbook([
            ['269901', array_fill(0, $n, 11)],
            ['999999', array_fill(0, $n, 5)],
            ['269903', ['abc', ...array_fill(0, $n - 1, 5)]],
        ]))->assertUnprocessable()->assertJsonPath('message', fn ($m) => str_contains($m, 'Dòng 3') && str_contains($m, '999999') && str_contains($m, '"abc"'));
        $this->assertSame(0, GradeEntry::query()->whereIn('enrollment_id', collect($this->enrollments)->pluck('id'))->count());

        // File của lớp khác
        $other = CourseSection::query()->whereKeyNot($this->section->id)->value('id');
        $this->import($this->workbook([['269901', array_fill(0, $n, 5)]], $other))
            ->assertUnprocessable()->assertJsonPath('message', fn ($m) => str_starts_with($m, 'File Excel là sổ điểm của lớp khác.'));

        // Thiếu cột của một thành phần
        $this->import($this->workbook([['269901', [5]]], null, ['MSSV', 'Không phải thành phần']))
            ->assertUnprocessable()->assertJsonPath('message', fn ($m) => str_contains($m, 'thiếu cột điểm'));
    }
}
