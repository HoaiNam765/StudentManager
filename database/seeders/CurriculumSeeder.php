<?php

namespace Database\Seeders;

use App\Modules\Curriculum\Models\Curriculum;
use App\Modules\Curriculum\Services\CurriculumService;
use App\Modules\Faculty\Models\Major;
use App\Modules\Faculty\Models\TrainingType;
use App\Modules\Subject\Models\Subject;
use Illuminate\Database\Seeder;

/**
 * CTĐT mẫu (docs/BA.md mục 11.2: mỗi ngành 1 CTĐT), áp dụng từ khóa 2026, hệ chính quy, đã ban hành. Mỗi CTĐT 8 học kỳ,
 * khoảng 14–18 tín chỉ tích lũy mỗi học kỳ, tổng 126–129 tín chỉ (chưa tính học phần điều kiện: giáo dục thể chất,
 * quốc phòng – an ninh, tuần sinh hoạt công dân); có học phần bắt buộc, nhóm tự chọn theo học kỳ (chọn đủ số tín chỉ tối
 * thiểu), thực tập ở học kỳ 7, đồ án / khóa luận ở học kỳ 8. Tổng tín chỉ yêu cầu của từng khối = tín chỉ bắt buộc + tối
 * thiểu của các nhóm tự chọn (BR-CUR-02). Học kỳ dự kiến xếp theo quan hệ của SubjectRelationSeeder (BR-CUR-03). Chạy lặp
 * lại an toàn theo mã.
 */
class CurriculumSeeder extends Seeder
{
    /** Học kỳ dự kiến của từng học phần mẫu (dùng chung cho mọi CTĐT có học phần đó). */
    private const TERMS = [
        'LLCT101' => 1, 'LLCT102' => 2, 'LLCT103' => 3, 'LLCT104' => 4, 'LLCT105' => 4, 'LLCT106' => 1, 'LLCT107' => 5, 'LLCT108' => 3,
        'GDTC101' => 1, 'GDTC102' => 2, 'GDTC103' => 3, 'GDQP101' => 2, 'GDQP102' => 2, 'GDQP103' => 2, 'SHCD101' => 1,
        'TOAN101' => 1, 'TOAN102' => 1, 'TOAN103' => 2, 'TOAN104' => 2, 'TOAN105' => 4,
        'ENG101' => 1, 'ENG102' => 2, 'ENG103' => 3, 'ENG201' => 1, 'ENG202' => 2, 'ENG203' => 3,
        'ENG204' => 1, 'ENG205' => 2, 'ENG206' => 3, 'ENG207' => 3, 'ENG208' => 4, 'ENG209' => 4, 'ENG210' => 4,
        'ENG301' => 5, 'ENG302' => 6, 'ENG303' => 6, 'ENG304' => 5, 'ENG305' => 4, 'ENG306' => 5, 'ENG307' => 5, 'ENG308' => 5,
        'ENG309' => 6, 'ENG310' => 6, 'ENG311' => 6, 'ENG312' => 7, 'ENG313' => 7, 'ENG314' => 7, 'ENG315' => 7, 'ENG316' => 7,
        'ENG317' => 8, 'ENG401' => 7, 'ENG402' => 8,
        'LIN101' => 2, 'LIN102' => 2, 'LIN103' => 3, 'LIN104' => 6, 'LIN105' => 8,
        'CHN101' => 1, 'CHN102' => 2, 'CHN103' => 3, 'CHN104' => 4, 'CHN105' => 5, 'CHN201' => 3, 'CHN202' => 1, 'CHN203' => 2,
        'CHN204' => 3, 'CHN205' => 4, 'CHN206' => 4, 'CHN207' => 4, 'CHN208' => 5, 'CHN209' => 5,
        'CHN301' => 5, 'CHN302' => 6, 'CHN303' => 6, 'CHN304' => 5, 'CHN305' => 6, 'CHN306' => 6, 'CHN307' => 7, 'CHN308' => 7,
        'CHN309' => 7, 'CHN310' => 6, 'CHN311' => 7, 'CHN312' => 7, 'CHN313' => 8, 'CHN401' => 7, 'CHN402' => 8,
        'INT101' => 1, 'INT102' => 2, 'INT103' => 1, 'INT104' => 1,
        'INT201' => 3, 'INT202' => 3, 'INT203' => 3, 'INT204' => 4, 'INT205' => 4, 'INT206' => 3,
        'INT207' => 4, 'INT208' => 4, 'INT209' => 4, 'INT210' => 4, 'INT211' => 4, 'INT212' => 4, 'INT213' => 4,
        'INT301' => 5, 'INT302' => 6, 'INT303' => 5, 'INT304' => 6, 'INT305' => 5, 'INT306' => 5, 'INT307' => 6, 'INT308' => 6,
        'INT309' => 7, 'INT310' => 5, 'INT311' => 5, 'INT312' => 6, 'INT313' => 6, 'INT314' => 6, 'INT315' => 6, 'INT316' => 6,
        'INT317' => 6, 'INT318' => 6, 'INT319' => 7, 'INT320' => 7, 'INT321' => 7, 'INT322' => 7, 'INT323' => 7, 'INT324' => 8,
        'INT401' => 7, 'INT402' => 8,
        'ECO101' => 1, 'ECO102' => 2, 'BUS103' => 3, 'BUS104' => 8, 'BUS201' => 2, 'BUS202' => 3, 'BUS203' => 3, 'BUS204' => 3,
        'BUS205' => 4, 'BUS301' => 5, 'BUS302' => 6, 'BUS303' => 4, 'BUS304' => 5, 'BUS305' => 5, 'BUS306' => 5, 'BUS307' => 5,
        'BUS308' => 6, 'BUS309' => 6, 'BUS310' => 6, 'BUS311' => 6, 'BUS312' => 6, 'BUS313' => 7, 'BUS314' => 7, 'BUS315' => 7,
        'BUS316' => 7, 'BUS317' => 7, 'BUS318' => 8, 'BUS319' => 8, 'BUS401' => 7, 'BUS402' => 8,
        'ACC201' => 2, 'ACC202' => 4, 'ACC203' => 3, 'ACC301' => 4, 'ACC302' => 5, 'ACC303' => 4, 'ACC304' => 5, 'ACC305' => 5,
        'ACC306' => 5, 'ACC307' => 6, 'ACC308' => 6, 'ACC309' => 6, 'ACC310' => 6, 'ACC311' => 6, 'ACC312' => 6, 'ACC313' => 7,
        'ACC314' => 7, 'ACC315' => 7, 'ACC316' => 7, 'ACC317' => 7, 'ACC318' => 7, 'ACC319' => 8, 'ACC320' => 8,
        'ACC401' => 7, 'ACC402' => 8,
    ];

    private const POLITICS = ['LLCT101', 'LLCT102', 'LLCT103', 'LLCT104', 'LLCT105', 'LLCT106'];

    private const CONDITIONS = ['GDTC101', 'GDTC102', 'GDTC103', 'GDQP101', 'GDQP102', 'GDQP103', 'SHCD101'];

    /** Đại cương chung của hai ngành CNTT, KTPM */
    private const IT_GENERAL = [...self::POLITICS, ...self::CONDITIONS, 'LLCT107', 'TOAN101', 'TOAN102', 'TOAN103', 'ENG101', 'ENG102', 'ENG103', 'BUS104'];

    /** Đại cương chung của hai ngành QTKD, Kế toán */
    private const ECONOMICS_GENERAL = [...self::POLITICS, ...self::CONDITIONS, 'LLCT107', 'TOAN101', 'TOAN103', 'ENG101', 'ENG102', 'ENG103', 'ECO101', 'ECO102',
        'INT103', 'INT104', 'BUS103', 'BUS104'];

    /** Cơ sở ngành chung của hai ngành QTKD, Kế toán */
    private const ECONOMICS_FOUNDATION = ['BUS201', 'BUS202', 'ACC201', 'BUS203', 'BUS204', 'ACC202', 'ACC203', 'TOAN105'];

    /** Đại cương chung của hai ngành ngôn ngữ */
    private const LANGUAGE_GENERAL = [...self::POLITICS, ...self::CONDITIONS, 'LLCT107', 'LLCT108', 'LIN102', 'INT103', 'INT104', 'BUS104'];

    /**
     * mã ngành => [mã CTĐT, tên, [loại khối => [tên khối, [học phần bắt buộc], [[tên nhóm tự chọn, tín chỉ tối thiểu, [học phần]], ...]]]]
     *
     * Mỗi nhóm tự chọn gồm học phần cùng một học kỳ dự kiến.
     */
    private const CURRICULA = [
        '7480201' => ['CTDT-7480201-2026', 'CTĐT ngành Công nghệ thông tin (2026)', [
            'general' => ['Giáo dục đại cương', self::IT_GENERAL, []],
            'foundation' => ['Cơ sở ngành', ['TOAN104', 'INT101', 'INT102', 'INT201', 'INT202', 'INT203', 'INT204', 'INT205', 'INT206',
                'INT207', 'INT210', 'INT211', 'INT212', 'INT213'], []],
            'specialized' => ['Chuyên ngành', ['INT301', 'INT303', 'INT305', 'INT306', 'INT310', 'INT311', 'INT308', 'INT312', 'INT315', 'INT316',
                'INT323', 'INT324'], [
                    ['Tự chọn chuyên ngành 1 (HK6)', 6, ['INT302', 'INT304', 'INT307', 'INT317', 'INT318']],
                    ['Tự chọn chuyên ngành 2 (HK7)', 9, ['INT309', 'INT319', 'INT320', 'INT321']],
                ]],
            'internship' => ['Thực tập', ['INT401'], []],
            'thesis' => ['Đồ án tốt nghiệp', ['INT402'], []],
        ]],
        '7480103' => ['CTDT-7480103-2026', 'CTĐT ngành Kỹ thuật phần mềm (2026)', [
            'general' => ['Giáo dục đại cương', self::IT_GENERAL, []],
            'foundation' => ['Cơ sở ngành', ['TOAN104', 'INT101', 'INT102', 'INT201', 'INT202', 'INT203', 'INT204', 'INT205', 'INT206',
                'INT207', 'INT208', 'INT209', 'INT210', 'INT211'], []],
            'specialized' => ['Chuyên ngành', ['INT301', 'INT303', 'INT305', 'INT306', 'INT310', 'INT311', 'INT302', 'INT312', 'INT313',
                'INT323', 'INT324'], [
                    ['Tự chọn chuyên ngành 1 (HK6)', 6, ['INT304', 'INT307', 'INT308', 'INT314']],
                    ['Tự chọn chuyên ngành 2 (HK7)', 9, ['INT309', 'INT319', 'INT321', 'INT322']],
                ]],
            'internship' => ['Thực tập', ['INT401'], []],
            'thesis' => ['Đồ án tốt nghiệp', ['INT402'], []],
        ]],
        '7340101' => ['CTDT-7340101-2026', 'CTĐT ngành Quản trị kinh doanh (2026)', [
            'general' => ['Giáo dục đại cương', self::ECONOMICS_GENERAL, []],
            'foundation' => ['Cơ sở ngành', [...self::ECONOMICS_FOUNDATION, 'BUS205'], []],
            'specialized' => ['Chuyên ngành', ['BUS303', 'BUS301', 'BUS304', 'BUS305', 'BUS306', 'BUS307', 'BUS310', 'BUS311', 'BUS312',
                'BUS313', 'BUS318', 'BUS319'], [
                    ['Tự chọn chuyên ngành 1 (HK6)', 6, ['BUS302', 'ENG303', 'BUS308', 'BUS309']],
                    ['Tự chọn chuyên ngành 2 (HK7)', 9, ['BUS314', 'BUS315', 'BUS316', 'BUS317']],
                ]],
            'internship' => ['Thực tập', ['BUS401'], []],
            'thesis' => ['Khóa luận tốt nghiệp', ['BUS402'], []],
        ]],
        '7340301' => ['CTDT-7340301-2026', 'CTĐT ngành Kế toán (2026)', [
            'general' => ['Giáo dục đại cương', self::ECONOMICS_GENERAL, []],
            'foundation' => ['Cơ sở ngành', [...self::ECONOMICS_FOUNDATION, 'ACC303'], []],
            'specialized' => ['Chuyên ngành', ['ACC301', 'ACC302', 'ACC304', 'ACC305', 'ACC306', 'ACC307', 'ACC308', 'ACC309', 'ACC310',
                'ACC313', 'ACC314', 'ACC315', 'ACC319', 'ACC320'], [
                    ['Tự chọn chuyên ngành 1 (HK5)', 3, ['BUS301', 'BUS305']],
                    ['Tự chọn chuyên ngành 2 (HK6)', 3, ['BUS302', 'ACC311', 'ACC312']],
                    ['Tự chọn chuyên ngành 3 (HK7)', 6, ['ACC316', 'ACC317', 'ACC318']],
                ]],
            'internship' => ['Thực tập', ['ACC401'], []],
            'thesis' => ['Khóa luận tốt nghiệp', ['ACC402'], []],
        ]],
        '7220201' => ['CTDT-7220201-2026', 'CTĐT ngành Ngôn ngữ Anh (2026)', [
            'general' => ['Giáo dục đại cương', [...self::LANGUAGE_GENERAL, 'CHN101', 'CHN102'], []],
            'foundation' => ['Cơ sở ngành', ['ENG201', 'ENG202', 'ENG203', 'ENG204', 'ENG205', 'ENG206', 'ENG207', 'ENG208', 'ENG209', 'ENG210',
                'LIN101', 'LIN103'], []],
            'specialized' => ['Chuyên ngành', ['ENG301', 'ENG302', 'ENG304', 'ENG305', 'ENG308', 'ENG309', 'ENG311', 'ENG312', 'ENG316',
                'ENG317', 'LIN105'], [
                    ['Tự chọn chuyên ngành 1 (HK5)', 3, ['ENG306', 'ENG307']],
                    ['Tự chọn chuyên ngành 2 (HK6)', 6, ['ENG303', 'ENG310', 'LIN104']],
                    ['Tự chọn chuyên ngành 3 (HK7)', 6, ['ENG313', 'ENG314', 'ENG315']],
                ]],
            'internship' => ['Thực tập', ['ENG401'], []],
            'thesis' => ['Khóa luận tốt nghiệp', ['ENG402'], []],
        ]],
        '7220204' => ['CTDT-7220204-2026', 'CTĐT ngành Ngôn ngữ Trung Quốc (2026)', [
            'general' => ['Giáo dục đại cương', [...self::LANGUAGE_GENERAL, 'ENG101', 'ENG102', 'ENG103'], []],
            'foundation' => ['Cơ sở ngành', ['CHN101', 'CHN102', 'CHN103', 'CHN104', 'CHN105', 'CHN201', 'CHN202', 'CHN203', 'CHN204', 'CHN205',
                'CHN206', 'CHN207', 'CHN208', 'CHN209', 'LIN101', 'LIN103'], []],
            'specialized' => ['Chuyên ngành', ['CHN301', 'CHN304', 'CHN302', 'CHN306', 'CHN310', 'CHN307', 'CHN312', 'CHN313', 'LIN105'], [
                ['Tự chọn chuyên ngành 1 (HK6)', 6, ['CHN303', 'CHN305', 'LIN104']],
                ['Tự chọn chuyên ngành 2 (HK7)', 6, ['CHN308', 'CHN309', 'CHN311']],
            ]],
            'internship' => ['Thực tập', ['CHN401'], []],
            'thesis' => ['Khóa luận tốt nghiệp', ['CHN402'], []],
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
            $required = fn (array $compulsory, array $groups) => $counted($compulsory) + (int) array_sum(array_column($groups, 1));
            $total = 0;

            foreach ($blocks as [, $compulsory, $groups]) {
                $total += $required($compulsory, $groups);
            }

            $curriculum = $service->create([
                'code' => $code,
                'name' => $name,
                'major_id' => $major->id,
                'training_type_id' => $regular,
                'cohort_from' => 2026,
                'total_credits' => $total,
                'description' => 'CTĐT mẫu 8 học kỳ (dữ liệu giả).',
            ]);

            foreach ($blocks as $type => [$blockName, $compulsory, $groups]) {
                $block = $service->addBlock($curriculum, [
                    'name' => $blockName,
                    'block_type' => $type,
                    'required_credits' => $required($compulsory, $groups),
                ]);

                foreach ($compulsory as $subjectCode) {
                    $service->addSubject($curriculum, ['subject_id' => $subjects[$subjectCode]->id, 'curriculum_block_id' => $block->id, 'planned_term' => self::TERMS[$subjectCode]]);
                }

                foreach ($groups as [$groupName, $minCredits, $groupSubjects]) {
                    $electiveGroup = $service->addGroup($curriculum, ['curriculum_block_id' => $block->id, 'name' => $groupName, 'min_credits' => $minCredits]);

                    foreach ($groupSubjects as $subjectCode) {
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
