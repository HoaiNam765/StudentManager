<?php

namespace App\Modules\Grade\Services;

use App\Models\User;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grade\Models\CourseResult;
use App\Modules\Grade\Models\GradeEntry;
use App\Modules\Grade\Models\SectionGradeComponent;
use App\Modules\Grade\Models\SectionGradeWorkflow;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Subject\Enums\ComponentKind;
use App\Support\Services\BaseService;

/**
 * Trạng thái điểm đặc biệt (FR-GRD-012; BR-GRD-07): giảng viên được phân công ghi / bỏ trạng thái cho từng sinh viên khi
 * bảng điểm còn sửa được (Nháp / Bị trả lại); sau công bố đi qua yêu cầu điều chỉnh. Danh sách trạng thái, ký hiệu và
 * cách tính ở `config/grade_special_statuses.php`:
 * - `final_zero` (vắng thi không lý do, cấm thi): ghi 0 cho thành phần thi cuối kỳ rồi tính điểm bình thường.
 * - `gpa` = `exclude` (hoãn thi, miễn, công nhận…): kết quả không có điểm số, không tính GPA, không cần đủ điểm khi nộp.
 */
class SpecialStatusService extends BaseService
{
    public function __construct(
        private readonly GradebookService $gradebook,
        private readonly CourseGradeService $calculator,
    ) {}

    /** @return array<string, mixed> kết quả học phần sau khi ghi */
    public function set(Enrollment $enrollment, User $actor, ?string $status, ?string $reason): array
    {
        $section = CourseSection::query()->findOrFail($enrollment->course_section_id);
        $this->gradebook->authorizeGrading($section, $actor);
        $definitions = (array) config('grade_special_statuses', []);

        if ($status !== null && ! isset($definitions[$status])) {
            $this->fail("Trạng thái điểm \"{$status}\" không có.", 'Chọn một trong: '.implode(', ', array_keys($definitions)).'.');
        }

        if ($status !== null && blank($reason)) {
            $this->fail('Ghi trạng thái điểm đặc biệt phải có lý do.', 'Ghi lý do / số quyết định (ví dụ "Vắng thi ngày 12/01, không có đơn").');
        }

        if (! $enrollment->status->isActive()) {
            $this->fail('Đăng ký đã hủy / rút, không ghi trạng thái điểm.', 'Chỉ ghi cho sinh viên đăng ký còn hiệu lực.');
        }

        return $this->transaction(function () use ($enrollment, $section, $status, $reason, $definitions): array {
            $workflow = SectionGradeWorkflow::query()->where('course_section_id', $section->id)->lockForUpdate()->first();

            if ($workflow !== null && ! $workflow->status->editable()) {
                $this->fail("Bảng điểm lớp đang ở trạng thái \"{$workflow->status->label()}\", không ghi trạng thái điểm trực tiếp được.",
                    $workflow->status->public()
                        ? 'Sau công bố, gửi yêu cầu điều chỉnh điểm kèm lý do và minh chứng (BR-GRD-06).'
                        : 'Đề nghị khoa / Phòng Đào tạo trả lại bảng điểm rồi ghi lại.');
            }

            if ($status !== null && ($definitions[$status]['final_zero'] ?? false)) {
                $final = SectionGradeComponent::query()->where('course_section_id', $section->id)
                    ->where('kind', ComponentKind::FinalExam->value)->first();

                if ($final === null) {
                    $this->fail('Lớp không có thành phần thi cuối kỳ để ghi 0 điểm.', 'Kiểm tra cơ cấu điểm lớp (cần một thành phần loại "Thi cuối kỳ").');
                }

                GradeEntry::query()->firstOrNew(['enrollment_id' => $enrollment->id, 'section_grade_component_id' => $final->id])
                    ->fill(['score' => 0, 'status' => 'draft'])->save();
            }

            $result = CourseResult::query()->firstOrNew(['enrollment_id' => $enrollment->id]);
            $result->fill(['special_status' => $status, 'special_reason' => $status === null ? null : $reason]);

            if (! $result->exists) {
                $result->fill(['status' => 'incomplete']);
            }

            $result->save();

            return $this->calculator->save($enrollment) + ['special_status' => $status, 'special_reason' => $result->special_reason];
        });
    }
}
