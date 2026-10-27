<?php

namespace Database\Seeders;

use App\Modules\Subject\Enums\RelationType;
use App\Modules\Subject\Models\Subject;
use App\Modules\Subject\Models\SubjectRelation;
use App\Modules\Subject\Services\SubjectRelationService;
use Illuminate\Database\Seeder;

/**
 * Quan hệ mẫu giữa các học phần của SubjectSeeder (dữ liệu giả). Tạo qua SubjectRelationService nên không có vòng lặp
 * (BR-SUB-03). Chạy lặp lại an toàn: bỏ qua quan hệ đã có.
 */
class SubjectRelationSeeder extends Seeder
{
    /** [học phần, học phần liên quan, loại]: (B, A, prerequisite) nghĩa là A là tiên quyết của B. */
    public const RELATIONS = [
        ['INT102', 'INT101', 'prerequisite'],
        ['INT201', 'INT102', 'prerequisite'],
        ['INT201', 'TOAN104', 'corequisite'],
        ['INT202', 'INT101', 'prior'],
        ['INT301', 'INT202', 'prerequisite'],
        ['INT302', 'INT301', 'prior'],
        ['INT303', 'INT203', 'prerequisite'],
        ['INT303', 'INT202', 'prior'],
        ['INT304', 'INT303', 'prior'],
        ['INT305', 'INT203', 'prerequisite'],
        ['INT306', 'INT201', 'prerequisite'],
        ['INT307', 'INT306', 'prerequisite'],
        ['INT307', 'TOAN103', 'prior'],
        ['INT204', 'INT206', 'prior'],
        ['INT308', 'INT205', 'prerequisite'],
        ['INT309', 'INT205', 'prior'],
        ['INT401', 'INT301', 'prior'],
        ['INT402', 'INT401', 'prerequisite'],
        ['ENG102', 'ENG101', 'prerequisite'],
        ['ENG103', 'ENG102', 'prerequisite'],
        ['ENG301', 'ENG203', 'prior'],
        ['ENG302', 'ENG202', 'prior'],
        ['ENG402', 'ENG401', 'prerequisite'],
        ['CHN102', 'CHN101', 'prerequisite'],
        ['GDTC102', 'GDTC101', 'prior'],
        ['GDTC103', 'GDTC102', 'prior'],
        ['LLCT102', 'LLCT101', 'prior'],
        ['LLCT103', 'LLCT102', 'prior'],
        ['ECO102', 'ECO101', 'prior'],
        ['BUS301', 'BUS201', 'prerequisite'],
        ['ACC301', 'ACC201', 'prerequisite'],

        // Học phần bổ sung cho CTĐT đầy đủ (luôn sớm hơn theo học kỳ dự kiến của CurriculumSeeder, BR-CUR-03).
        // Thực hành tách riêng học song hành với lý thuyết.
        ['INT207', 'INT205', 'corequisite'],
        ['INT208', 'INT202', 'prior'],
        ['INT209', 'INT208', 'corequisite'],
        ['INT210', 'INT203', 'prerequisite'],
        ['INT211', 'INT210', 'corequisite'],
        ['INT212', 'INT101', 'prior'],
        ['INT213', 'INT212', 'corequisite'],
        ['INT310', 'INT203', 'prerequisite'],
        ['INT311', 'INT310', 'corequisite'],
        ['INT312', 'INT301', 'prior'],
        ['INT313', 'INT301', 'prerequisite'],
        ['INT314', 'INT301', 'prior'],
        ['INT315', 'INT205', 'prerequisite'],
        ['INT316', 'INT315', 'corequisite'],
        ['INT317', 'INT203', 'prerequisite'],
        ['INT317', 'TOAN103', 'prior'],
        ['INT318', 'INT205', 'prerequisite'],
        ['INT319', 'INT203', 'prior'],
        ['INT320', 'INT306', 'prerequisite'],
        ['INT321', 'INT205', 'prior'],
        ['INT322', 'INT301', 'prior'],
        ['INT323', 'INT301', 'prior'],
        ['TOAN105', 'TOAN103', 'prerequisite'],
        ['BUS204', 'TOAN103', 'prior'],
        ['BUS205', 'BUS201', 'prior'],
        ['BUS303', 'BUS202', 'prerequisite'],
        ['BUS304', 'BUS201', 'prior'],
        ['BUS305', 'ECO102', 'prior'],
        ['BUS306', 'BUS201', 'prior'],
        ['BUS307', 'BUS303', 'prerequisite'],
        ['BUS307', 'BUS204', 'prior'],
        ['BUS308', 'BUS303', 'prior'],
        ['BUS310', 'BUS202', 'prior'],
        ['BUS311', 'BUS301', 'prior'],
        ['BUS312', 'BUS304', 'prior'],
        ['BUS314', 'BUS304', 'prior'],
        ['BUS316', 'BUS303', 'prior'],
        ['BUS317', 'BUS303', 'prior'],
        ['BUS401', 'BUS301', 'prior'],
        ['BUS402', 'BUS401', 'prerequisite'],
        ['ACC202', 'ACC201', 'prior'],
        ['ACC203', 'ECO102', 'prior'],
        ['ACC302', 'ACC301', 'prerequisite'],
        ['ACC303', 'ACC201', 'prior'],
        ['ACC304', 'ACC201', 'prior'],
        ['ACC305', 'ACC301', 'prior'],
        ['ACC306', 'ACC301', 'prior'],
        ['ACC307', 'ACC306', 'prerequisite'],
        ['ACC308', 'ACC302', 'prior'],
        ['ACC309', 'ACC301', 'prior'],
        ['ACC310', 'ACC301', 'prior'],
        ['ACC311', 'ACC302', 'prior'],
        ['ACC312', 'ACC302', 'prior'],
        ['ACC313', 'ACC302', 'prior'],
        ['ACC314', 'ACC313', 'corequisite'],
        ['ACC316', 'ACC305', 'prior'],
        ['ACC317', 'ACC306', 'prior'],
        ['ACC318', 'ACC306', 'prior'],
        ['ACC320', 'ACC308', 'prior'],
        ['ACC401', 'ACC302', 'prior'],
        ['ACC402', 'ACC401', 'prerequisite'],
        ['ENG205', 'ENG204', 'prerequisite'],
        ['ENG206', 'ENG202', 'prior'],
        ['ENG208', 'ENG203', 'prior'],
        ['ENG209', 'ENG201', 'prior'],
        ['ENG304', 'ENG205', 'prior'],
        ['ENG305', 'ENG207', 'prior'],
        ['ENG308', 'ENG206', 'prior'],
        ['ENG309', 'ENG301', 'prerequisite'],
        ['ENG311', 'ENG208', 'prior'],
        ['ENG312', 'ENG302', 'prerequisite'],
        ['ENG314', 'ENG301', 'prior'],
        ['ENG317', 'ENG309', 'prior'],
        ['ENG317', 'ENG312', 'prior'],
        ['LIN105', 'LIN101', 'prior'],
        ['CHN103', 'CHN102', 'prerequisite'],
        ['CHN104', 'CHN103', 'prerequisite'],
        ['CHN105', 'CHN104', 'prerequisite'],
        ['CHN204', 'CHN203', 'prerequisite'],
        ['CHN205', 'CHN201', 'prior'],
        ['CHN206', 'CHN103', 'prior'],
        ['CHN301', 'CHN104', 'prior'],
        ['CHN302', 'CHN105', 'prior'],
        ['CHN306', 'CHN301', 'prerequisite'],
        ['CHN307', 'CHN306', 'prior'],
        ['CHN310', 'CHN105', 'prior'],
        ['CHN312', 'CHN306', 'prior'],
        ['CHN401', 'CHN301', 'prior'],
        ['CHN402', 'CHN401', 'prerequisite'],
    ];

    public function run(): void
    {
        $subjects = Subject::query()->get()->keyBy('code');
        $service = app(SubjectRelationService::class);

        foreach (self::RELATIONS as [$code, $relatedCode, $type]) {
            $subject = $subjects[$code] ?? null;
            $related = $subjects[$relatedCode] ?? null;

            if ($subject === null || $related === null) {
                continue;
            }

            $exists = SubjectRelation::query()
                ->where('subject_id', $subject->id)
                ->where('related_subject_id', $related->id)
                ->where('type', $type)
                ->exists();

            if (! $exists) {
                $service->add($subject, $related, RelationType::from($type));
            }
        }
    }
}
