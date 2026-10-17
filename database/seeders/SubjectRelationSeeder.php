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
