<?php

namespace Database\Seeders;

use App\Modules\Curriculum\Models\Curriculum;
use App\Modules\Curriculum\Services\CurriculumService;
use App\Modules\Faculty\Models\Major;
use App\Modules\Faculty\Models\TrainingType;
use App\Modules\Subject\Models\Subject;
use Illuminate\Database\Seeder;

/**
 * CTĐT mẫu (docs/BA.md mục 11.2: mỗi ngành 1 CTĐT), áp dụng từ khóa 2026, hệ chính quy, đã ban hành. Đây là CTĐT
 * **rút gọn** dựng từ 60 học phần mẫu: tổng tín chỉ yêu cầu bằng đúng số tín chỉ có trong CTĐT (thấp hơn tổng tín chỉ
 * chuẩn của ngành). Học kỳ dự kiến xếp theo quan hệ tiên quyết của SubjectRelationSeeder. Chạy lặp lại an toàn theo mã.
 */
class CurriculumSeeder extends Seeder
{
    /** Học kỳ dự kiến của từng học phần mẫu. */
    private const TERMS = [
        'LLCT101' => 1, 'LLCT102' => 2, 'LLCT103' => 3, 'LLCT104' => 4, 'LLCT105' => 4, 'LLCT106' => 1,
        'GDTC101' => 1, 'GDTC102' => 2, 'GDTC103' => 3, 'GDQP101' => 2, 'GDQP102' => 2, 'GDQP103' => 2, 'SHCD101' => 1,
        'TOAN101' => 1, 'TOAN102' => 1, 'TOAN103' => 2, 'TOAN104' => 2,
        'ENG101' => 1, 'ENG102' => 2, 'ENG103' => 3, 'ENG201' => 1, 'ENG202' => 2, 'ENG203' => 3,
        'ENG301' => 5, 'ENG302' => 6, 'ENG303' => 6, 'ENG401' => 7, 'ENG402' => 8,
        'CHN101' => 1, 'CHN102' => 2, 'CHN201' => 3, 'CHN301' => 5,
        'INT101' => 1, 'INT102' => 2, 'INT201' => 3, 'INT202' => 3, 'INT203' => 3, 'INT204' => 4, 'INT205' => 4, 'INT206' => 3,
        'INT301' => 5, 'INT302' => 6, 'INT303' => 5, 'INT304' => 6, 'INT305' => 5, 'INT306' => 5, 'INT307' => 6, 'INT308' => 6,
        'INT309' => 7, 'INT401' => 7, 'INT402' => 8,
        'ECO101' => 1, 'ECO102' => 2, 'BUS201' => 2, 'BUS202' => 3, 'BUS301' => 5, 'BUS302' => 6, 'BUS401' => 7,
        'ACC201' => 2, 'ACC301' => 4,
    ];

    private const POLITICS = ['LLCT101', 'LLCT102', 'LLCT103', 'LLCT104', 'LLCT105', 'LLCT106'];

    private const CONDITIONS = ['GDTC101', 'GDTC102', 'GDTC103', 'GDQP101', 'GDQP102', 'GDQP103', 'SHCD101'];

    /**
     * mã ngành => [mã CTĐT, tên, [loại khối => [tên khối, [học phần bắt buộc], [tên nhóm tự chọn, tín chỉ tối thiểu, [học phần]]|null]]]
     */
    private const CURRICULA = [
        '7480201' => ['CTDT-7480201-2026', 'CTĐT ngành Công nghệ thông tin (2026)', [
            'general' => ['Giáo dục đại cương', [...self::POLITICS, ...self::CONDITIONS, 'TOAN101', 'TOAN102', 'TOAN103', 'ENG101', 'ENG102', 'ENG103'], null],
            'foundation' => ['Cơ sở ngành', ['TOAN104', 'INT101', 'INT102', 'INT201', 'INT202', 'INT203', 'INT204', 'INT205', 'INT206'], null],
            'specialized' => ['Chuyên ngành', ['INT301', 'INT303', 'INT305', 'INT306', 'INT308'], ['Tự chọn chuyên ngành', 6, ['INT302', 'INT304', 'INT307', 'INT309']]],
            'internship' => ['Thực tập', ['INT401'], null],
            'thesis' => ['Đồ án tốt nghiệp', ['INT402'], null],
        ]],
        '7480103' => ['CTDT-7480103-2026', 'CTĐT ngành Kỹ thuật phần mềm (2026)', [
            'general' => ['Giáo dục đại cương', [...self::POLITICS, ...self::CONDITIONS, 'TOAN101', 'TOAN102', 'TOAN103', 'ENG101', 'ENG102', 'ENG103'], null],
            'foundation' => ['Cơ sở ngành', ['TOAN104', 'INT101', 'INT102', 'INT201', 'INT202', 'INT203', 'INT204', 'INT205', 'INT206'], null],
            'specialized' => ['Chuyên ngành', ['INT301', 'INT302', 'INT303', 'INT305'], ['Tự chọn chuyên ngành', 6, ['INT304', 'INT306', 'INT307', 'INT308', 'INT309']]],
            'internship' => ['Thực tập', ['INT401'], null],
            'thesis' => ['Đồ án tốt nghiệp', ['INT402'], null],
        ]],
        '7340101' => ['CTDT-7340101-2026', 'CTĐT ngành Quản trị kinh doanh (2026)', [
            'general' => ['Giáo dục đại cương', [...self::POLITICS, ...self::CONDITIONS, 'TOAN101', 'TOAN103', 'ENG101', 'ENG102', 'ENG103', 'ECO101', 'ECO102'], null],
            'foundation' => ['Cơ sở ngành', ['BUS201', 'BUS202', 'ACC201'], null],
            'specialized' => ['Chuyên ngành', ['BUS301'], ['Tự chọn chuyên ngành', 3, ['BUS302', 'ENG303']]],
            'internship' => ['Thực tập', ['BUS401'], null],
        ]],
        '7340301' => ['CTDT-7340301-2026', 'CTĐT ngành Kế toán (2026)', [
            'general' => ['Giáo dục đại cương', [...self::POLITICS, ...self::CONDITIONS, 'TOAN101', 'TOAN103', 'ENG101', 'ENG102', 'ENG103', 'ECO101', 'ECO102'], null],
            'foundation' => ['Cơ sở ngành', ['BUS201', 'BUS202', 'ACC201'], null],
            'specialized' => ['Chuyên ngành', ['ACC301'], ['Tự chọn chuyên ngành', 3, ['BUS301', 'BUS302']]],
        ]],
        '7220201' => ['CTDT-7220201-2026', 'CTĐT ngành Ngôn ngữ Anh (2026)', [
            'general' => ['Giáo dục đại cương', [...self::POLITICS, ...self::CONDITIONS, 'CHN101'], null],
            'foundation' => ['Cơ sở ngành', ['ENG201', 'ENG202', 'ENG203'], null],
            'specialized' => ['Chuyên ngành', ['ENG301', 'ENG302'], ['Tự chọn chuyên ngành', 3, ['ENG303', 'BUS202']]],
            'internship' => ['Thực tập', ['ENG401'], null],
            'thesis' => ['Khóa luận tốt nghiệp', ['ENG402'], null],
        ]],
        '7220204' => ['CTDT-7220204-2026', 'CTĐT ngành Ngôn ngữ Trung Quốc (2026)', [
            'general' => ['Giáo dục đại cương', [...self::POLITICS, ...self::CONDITIONS, 'ENG101', 'ENG102', 'ENG103'], null],
            'foundation' => ['Cơ sở ngành', ['CHN101', 'CHN102', 'CHN201'], null],
            'specialized' => ['Chuyên ngành', ['CHN301'], null],
        ]],
    ];

    public function run(): void
    {
        $service = app(CurriculumService::class);
        $subjects = Subject::query()->get()->keyBy('code');
        $regular = TrainingType::query()->where('code', 'CQ')->value('id');

        foreach (self::CURRICULA as $majorCode => [$code, $name, $blocks]) {
            $major = Major::query()->where('code', $majorCode)->first();

            if ($major === null || $regular === null || Curriculum::withTrashed()->where('code', $code)->exists()) {
                continue;
            }

            $counted = fn (array $codes) => (int) collect($codes)->sum(fn (string $c) => $subjects[$c]->counts_toward_gpa ? $subjects[$c]->credits : 0);
            $total = 0;

            foreach ($blocks as [, $compulsory, $group]) {
                $total += $counted($compulsory) + ($group[1] ?? 0);
            }

            $curriculum = $service->create([
                'code' => $code,
                'name' => $name,
                'major_id' => $major->id,
                'training_type_id' => $regular,
                'cohort_from' => 2026,
                'total_credits' => $total,
                'description' => 'CTĐT mẫu rút gọn (dữ liệu giả).',
            ]);

            foreach ($blocks as $type => [$blockName, $compulsory, $group]) {
                $block = $service->addBlock($curriculum, [
                    'name' => $blockName,
                    'block_type' => $type,
                    'required_credits' => $counted($compulsory) + ($group[1] ?? 0),
                ]);

                foreach ($compulsory as $subjectCode) {
                    $service->addSubject($curriculum, ['subject_id' => $subjects[$subjectCode]->id, 'curriculum_block_id' => $block->id, 'planned_term' => self::TERMS[$subjectCode]]);
                }

                if ($group !== null) {
                    $electiveGroup = $service->addGroup($curriculum, ['curriculum_block_id' => $block->id, 'name' => $group[0], 'min_credits' => $group[1]]);

                    foreach ($group[2] as $subjectCode) {
                        $service->addSubject($curriculum, [
                            'subject_id' => $subjects[$subjectCode]->id,
                            'curriculum_block_id' => $block->id,
                            'curriculum_elective_group_id' => $electiveGroup->id,
                            'planned_term' => self::TERMS[$subjectCode],
                        ]);
                    }
                }
            }

            $service->publish($curriculum->refresh());
        }
    }
}
