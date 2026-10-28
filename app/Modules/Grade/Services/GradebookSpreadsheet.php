<?php

namespace App\Modules\Grade\Services;

use App\Models\User;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\SectionInstructor;
use App\Modules\System\Services\PolicyResolver;
use App\Support\Exceptions\BusinessRuleException;
use App\Support\Services\BaseService;
use DateTimeInterface;
use Illuminate\Support\Facades\File;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Writer\XLSX\Entity\SheetView;
use OpenSpout\Writer\XLSX\Writer;
use Throwable;

/**
 * Sổ điểm dạng bảng Excel cho giảng viên (yêu cầu người dùng 28/10): mỗi sinh viên một dòng, mỗi thành phần điểm một cột,
 * điền sẵn điểm đã có; cột Tổng kết / Điểm chữ / Hệ 4 / Kết quả là công thức theo cùng quy tắc CourseGradeService (trọng
 * số, làm tròn, điểm sàn, thang quy đổi của bộ quy chế theo khóa) để xem trước. Khi nhập lại chỉ đọc các cột thành phần,
 * khớp sinh viên theo MSSV, ô trống = chưa có điểm; hệ thống tự tính kết quả.
 */
class GradebookSpreadsheet extends BaseService
{
    public const SHEET = 'Bảng điểm';

    private const META_SHEET = '_ma';

    private const HEADER_ROW = 4;

    public function __construct(
        private readonly GradebookService $gradebook,
        private readonly PolicyResolver $policies,
    ) {}

    /** Tạo tệp .xlsx (đường dẫn tạm) của sổ điểm lớp. */
    public function write(CourseSection $section, User $actor): string
    {
        $data = $this->gradebook->gradebook($section, $actor);
        $components = collect($data['components'])->values();
        $section->loadMissing('subject', 'term.academicYear');
        $students = collect($data['students']);
        $cohorts = Enrollment::query()->with('student:id,cohort')->whereIn('id', $students->pluck('enrollment_id'))->get()
            ->mapWithKeys(fn (Enrollment $e) => [$e->id => (int) $e->student?->cohort]);
        $lecturer = SectionInstructor::query()->with('lecturer:id,full_name')->where('course_section_id', $section->id)
            ->where('role', SectionInstructor::MAIN)->first()?->lecturer?->full_name;

        $dir = storage_path('app/tmp');
        File::ensureDirectoryExists($dir);
        $path = tempnam($dir, 'grd_');
        if ($path === false) {
            $this->fail('Không tạo được file Excel sổ điểm.', 'Kiểm tra quyền ghi thư mục storage/app/tmp.');
        }

        $first = 4;                      // cột D: thành phần đầu tiên
        $total = $first + $components->count();
        $bold = (new Style)->setFontBold();
        $header = (new Style)->setFontBold()->setBackgroundColor('DCE6F7')->setShouldWrapText(true);
        $computed = (new Style)->setFontColor(Color::rgb(55, 65, 81))->setBackgroundColor('F3F4F6');

        $writer = new Writer;
        try {
            $writer->openToFile($path);
            $sheet = $writer->getCurrentSheet();
            $sheet->setName(self::SHEET);
            $sheet->setSheetView((new SheetView)->setFreezeRow(self::HEADER_ROW + 1)->setFreezeColumn('D'));
            $sheet->setColumnWidth(6, 1);
            $sheet->setColumnWidth(13, 2);
            $sheet->setColumnWidth(28, 3);
            $sheet->setColumnWidthForRange(14, $first, $total + 3);

            $writer->addRow(new Row([Cell::fromValue("SỔ ĐIỂM LỚP HỌC PHẦN {$section->code} – {$section->subject?->name} ({$section->subject?->credits} TC)", $bold)]));
            $writer->addRow(new Row([Cell::fromValue(trim("{$section->term?->name} {$section->term?->academicYear?->name}").($lecturer ? " · Giảng viên: {$lecturer}" : '')." · Sĩ số: {$students->count()} · Cơ cấu điểm: "
                .$components->map(fn ($c) => "{$c['name']} {$this->number((float) $c['weight'])}%".($c['min_score'] !== null ? " (sàn {$this->number((float) $c['min_score'])})" : ''))->implode(' + '))]));
            $writer->addRow(new Row([Cell::fromValue('Chỉ nhập điểm vào các cột thành phần (thang 0–10). Ô để trống = chưa có điểm. Không đổi MSSV và tên cột. '
                .'Cột xám (Tổng kết, Điểm chữ, Hệ 4, Kết quả) tự tính để xem trước; khi nhập file lên hệ thống tự tính lại.')]));
            $writer->addRow(new Row(array_map(fn ($v) => Cell::fromValue($v, $header), [
                'STT', 'MSSV', 'Họ và tên',
                ...$components->map(fn ($c) => "{$c['name']} ({$this->number((float) $c['weight'])}%)")->all(),
                'Tổng kết', 'Điểm chữ', 'Hệ 4', 'Kết quả',
            ])));

            foreach ($students->values() as $i => $student) {
                $r = self::HEADER_ROW + 1 + $i;
                $scores = collect($student['scores'])->keyBy('component_id');
                $cells = [Cell::fromValue($i + 1), Cell::fromValue($student['student']['student_code'] ?? ''), Cell::fromValue($student['student']['full_name'] ?? '')];
                foreach ($components as $c) {
                    $score = $scores->get($c['id'])['score'] ?? null;
                    $cells[] = $score === null ? Cell::fromValue(null) : Cell::fromValue((float) $score);
                }
                foreach ($this->formulas($section, $components->all(), $first, $r, $cohorts[$student['enrollment_id']] ?? 0) as $formula) {
                    $cells[] = Cell::fromValue($formula, $computed);
                }
                $writer->addRow(new Row($cells));
            }

            // Sheet ẩn: mã lớp, để không nhập nhầm file của lớp khác
            $meta = $writer->addNewSheetAndMakeItCurrent();
            $meta->setName(self::META_SHEET);
            $meta->setIsVisible(false);
            $writer->addRow(new Row([Cell::fromValue('course_section_id'), Cell::fromValue($section->id)]));
            $writer->addRow(new Row([Cell::fromValue('course_section_code'), Cell::fromValue($section->code)]));
        } finally {
            $writer->close();
        }

        return $path;
    }

    /**
     * Đọc tệp đã điền: trả về các dòng điểm cho GradebookService::saveDraft (mỗi sinh viên × thành phần, ô trống = null).
     *
     * @return list<array{enrollment_id: int, component_id: int, score: ?float}>
     */
    public function read(CourseSection $section, User $actor, string $path): array
    {
        $data = $this->gradebook->gradebook($section, $actor);
        $components = collect($data['components']);
        $enrollments = collect($data['students'])->mapWithKeys(fn ($s) => [mb_strtoupper(trim((string) ($s['student']['student_code'] ?? ''))) => (int) $s['enrollment_id']]);
        $min = (float) config('grades.min_score');
        $max = (float) config('grades.max_score');
        $step = (float) config('grades.score_step');
        $items = [];
        $errors = [];
        $seen = [];
        $sheetFound = false;
        $reader = new Reader;

        try {
            $reader->open($path);
            foreach ($reader->getSheetIterator() as $sheet) {
                if ($sheet->getName() === self::META_SHEET) {
                    foreach ($sheet->getRowIterator() as $row) {
                        $cols = $row->toArray();
                        if (($cols[0] ?? null) === 'course_section_id' && (int) ($cols[1] ?? 0) !== $section->id) {
                            $this->fail('File Excel là sổ điểm của lớp khác.', "Tải lại file từ lớp {$section->code} rồi nhập điểm vào file đó.");
                        }
                    }

                    continue;
                }
                if ($sheetFound) {
                    continue;
                }
                $sheetFound = true;
                $map = null; // cột MSSV và cột của từng thành phần, lấy từ dòng tiêu đề

                foreach ($sheet->getRowIterator() as $line => $row) {
                    $cols = $row->toArray();
                    if ($map === null) {
                        $map = $this->headerMap($cols, $components->all());

                        continue;
                    }
                    $code = mb_strtoupper(trim((string) ($cols[$map['code']] ?? '')));
                    if ($code === '') {
                        continue;
                    }
                    if (! $enrollments->has($code)) {
                        $errors[] = "Dòng {$line}: MSSV {$code} không có trong lớp (hoặc đã hủy / rút).";

                        continue;
                    }
                    if (isset($seen[$code])) {
                        $errors[] = "Dòng {$line}: MSSV {$code} lặp lại (đã có ở dòng {$seen[$code]}).";

                        continue;
                    }
                    $seen[$code] = $line;

                    foreach ($map['components'] as $index => $component) {
                        [$score, $error] = $this->score($cols[$index] ?? null, $min, $max, $step);
                        if ($error !== null) {
                            $errors[] = "Dòng {$line}, cột {$component['name']} (MSSV {$code}): {$error}";

                            continue;
                        }
                        $items[] = ['enrollment_id' => $enrollments[$code], 'component_id' => (int) $component['id'], 'score' => $score];
                    }
                }

                if ($map === null) {
                    $this->fail('Không tìm thấy dòng tiêu đề có cột MSSV.', 'Tải file sổ điểm từ nút "Mẫu Excel" của lớp rồi điền điểm, không xóa dòng tiêu đề.');
                }
            }
        } catch (BusinessRuleException $e) {
            throw $e;
        } catch (Throwable) {
            $this->fail('Không đọc được file Excel.', 'Kiểm tra file .xlsx mở được bằng Excel và được tải từ nút "Mẫu Excel" của lớp.');
        } finally {
            $reader->close();
        }

        if ($errors !== []) {
            $shown = array_slice($errors, 0, 8);
            $this->fail('File có '.count($errors).' lỗi, chưa lưu điểm nào: '.implode(' ', $shown).(count($errors) > 8 ? ' …' : ''),
                'Sửa các ô bị báo trong file rồi nhập lại. Điểm từ 0 đến 10; ô không có điểm để trống.');
        }
        if ($items === []) {
            $this->fail('File không có sinh viên nào.', 'Giữ nguyên cột MSSV của file tải về và điền điểm vào các cột thành phần.');
        }

        return $items;
    }

    /** Dòng tiêu đề: tìm cột "MSSV" và cột của từng thành phần (tên cột = tên thành phần, có thể kèm " (10%)"). */
    private function headerMap(array $cols, array $components): ?array
    {
        $names = array_map(fn ($v) => mb_strtolower(trim((string) $v)), $cols);
        $code = array_search('mssv', $names, true);
        if ($code === false) {
            return null; // chưa tới dòng tiêu đề (tiêu đề sổ điểm, thông tin lớp, hướng dẫn)
        }

        $map = ['code' => $code, 'components' => []];
        foreach ($components as $component) {
            $name = mb_strtolower(trim($component['name']));
            $index = collect($names)->search(fn ($v) => $v === $name || str_starts_with($v, $name.' ('));
            if ($index === false) {
                $this->fail("File thiếu cột điểm \"{$component['name']}\".", 'Tải lại file từ nút "Mẫu Excel", không đổi hay xóa tên cột.');
            }
            $map['components'][$index] = $component;
        }

        return $map;
    }

    /** @return array{0: ?float, 1: ?string} điểm hợp lệ hoặc lỗi */
    private function score(mixed $value, float $min, float $max, float $step): array
    {
        if ($value instanceof DateTimeInterface) {
            return [null, 'Excel hiểu ô này là ngày tháng; định dạng ô là Số rồi nhập lại điểm (ví dụ 8,5).'];
        }
        if ($value === null || (is_string($value) && trim($value) === '')) {
            return [null, null];
        }
        $text = is_string($value) ? str_replace(',', '.', trim($value)) : $value;
        if (! is_numeric($text)) {
            return [null, "\"{$value}\" không phải là số."];
        }
        $score = (float) $text;
        if ($score < $min || $score > $max || ($step > 0 && abs($score / $step - round($score / $step)) > 0.00001)) {
            return [null, "{$this->number($score)} ngoài thang {$this->number($min)}–{$this->number($max)} hoặc sai bước {$this->number($step)}."];
        }

        return [round($score, 2), null];
    }

    /**
     * Công thức xem trước cho dòng $r: Tổng kết (thiếu thành phần bắt buộc thì để trống), Điểm chữ, Hệ 4, Kết quả — cùng
     * quy tắc CourseGradeService, thang quy đổi và điểm đạt theo bộ quy chế của khóa sinh viên.
     *
     * @param  list<array<string, mixed>>  $components
     * @return list<string>
     */
    private function formulas(CourseSection $section, array $components, int $first, int $r, int $cohort): array
    {
        $cell = fn (int $i) => $this->letters($first + $i).$r;
        $total = $this->letters($first + count($components)).$r;
        $letter = $this->letters($first + count($components) + 1).$r;
        $required = [];
        $terms = [];
        $floors = [];
        foreach ($components as $i => $c) {
            $weight = $this->number((float) $c['weight'] / 100, 4);
            if ($c['is_required']) {
                $required[] = 'ISBLANK('.$cell($i).')';
                $terms[] = $cell($i).'*'.$weight;
                if ($c['min_score'] !== null) {
                    $floors[] = $cell($i).'<'.$this->number((float) $c['min_score']);
                }
            } else {
                $terms[] = 'N('.$cell($i).')*'.$weight;
            }
        }
        $digits = (int) config('grades.round_digits', 1);
        $pass = $this->number((float) $this->policies->value('grading.pass_score', $cohort));
        $sum = 'ROUND('.implode('+', $terms ?: ['0']).','.$digits.')';
        $fail = 'OR('.implode(',', [$total.'<'.$pass, ...$floors]).')';
        $totalFormula = $required === [] ? '='.$sum : '=IF(OR('.implode(',', $required).'),"",'.$sum.')';
        $result = '=IF('.$total.'="","",IF('.$fail.',"Không đạt","Đạt"))';

        if ($section->subject?->isPassFailOnly()) {
            return [$totalFormula, '=""', '=""', $result];
        }

        $bands = collect((array) $this->policies->value('grading.scale', $cohort))
            ->map(fn (array $b) => ['min' => (float) $b['min'], 'letter' => (string) $b['letter'], 'gpa' => (float) $b['gpa']])
            ->sortBy('min')->values();
        if ($bands->isEmpty() || $bands->first()['min'] > 0) {
            $bands->prepend(['min' => 0.0, 'letter' => 'F', 'gpa' => 0.0]);
        }
        $mins = '{'.$bands->map(fn ($b) => $this->number($b['min']))->implode(',').'}';
        $letters = '{'.$bands->map(fn ($b) => '"'.str_replace('"', '', $b['letter']).'"')->implode(',').'}';
        $gpas = '{'.$bands->map(fn ($b) => $this->number($b['gpa']))->implode(',').'}';

        return [
            $totalFormula,
            '=IF('.$total.'="","",IF('.$fail.',"F",LOOKUP('.$total.','.$mins.','.$letters.')))',
            '=IF('.$total.'="","",IF('.$letter.'="F",0,LOOKUP('.$total.','.$mins.','.$gpas.')))',
            $result,
        ];
    }

    /** Số trong công thức Excel: dấu chấm thập phân, bỏ số 0 thừa. */
    private function number(float $value, int $digits = 2): string
    {
        return rtrim(rtrim(number_format($value, $digits, '.', ''), '0'), '.') ?: '0';
    }

    /** Chỉ số cột (1 = A) thành chữ cột Excel. */
    private function letters(int $index): string
    {
        $name = '';
        while ($index > 0) {
            $index--;
            $name = chr(65 + $index % 26).$name;
            $index = intdiv($index, 26);
        }

        return $name;
    }
}
