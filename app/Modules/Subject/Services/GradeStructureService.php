<?php

namespace App\Modules\Subject\Services;

use App\Modules\Subject\Enums\ComponentKind;
use App\Modules\Subject\Models\GradeComponentTemplate;
use App\Modules\Subject\Models\Subject;
use App\Modules\System\Services\PolicyResolver;
use App\Modules\System\Settings\PolicyDefinitions;
use App\Support\Exceptions\BusinessRuleException;
use App\Support\References\ReferenceRegistry;
use App\Support\Services\BaseService;
use Illuminate\Support\Collection;

/**
 * Cơ cấu điểm của học phần (FR-SUB-005; BR-SUB-04, 05).
 *
 * Quy tắc (ngưỡng lấy từ bộ quy chế, PolicyDefinitions nhóm "Điểm"):
 * - tối thiểu `grading.min_components` thành phần (mặc định 3), tên không trùng;
 * - tổng trọng số đúng 100%; mỗi trọng số lớn hơn 0; điểm sàn trong thang 0–10;
 * - thi cuối kỳ có tổng trọng số tối thiểu `grading.final_exam_min_weight` (mặc định 50%);
 * - đánh giá từ xa không quá `grading.remote_max_weight` (mặc định 50%).
 *
 * Học phần đã có dữ liệu liên quan (lớp, điểm… đăng ký với ReferenceRegistry) thì không đổi cơ cấu điểm: thay đổi áp dụng
 * qua phiên bản học phần mới (BR-SUB-05). GRD gọi `templateFor()` để sao chép sang lớp học phần và có thể gọi
 * `validate()` với khóa của lớp khi giảng viên điều chỉnh cơ cấu riêng của lớp.
 */
class GradeStructureService extends BaseService
{
    public function __construct(
        private readonly PolicyResolver $policies,
        private readonly ReferenceRegistry $references,
    ) {}

    /**
     * Thay toàn bộ cơ cấu điểm của học phần.
     *
     * @param  list<array{name: string, kind: string, weight: int|float|string, min_score?: int|float|string|null, is_required?: bool, is_remote?: bool}>  $components
     * @return Collection<int, GradeComponentTemplate>
     */
    public function replace(Subject $subject, array $components): Collection
    {
        $usages = $this->references->usages($subject);

        if ($usages !== [] && $this->structureFor($subject)->isNotEmpty()) {
            $this->fail(
                "Không đổi được cơ cấu điểm của học phần {$subject->code} vì đã có dữ liệu liên quan (".ReferenceRegistry::describe($usages).').',
                'Điểm đã phát sinh phải giữ nguyên; cơ cấu điểm mới áp dụng qua phiên bản học phần từ học kỳ chưa có dữ liệu (BR-SUB-05).'
            );
        }

        $this->validate($components);

        $this->transaction(function () use ($subject, $components): void {
            GradeComponentTemplate::query()->where('subject_id', $subject->id)->get()->each->delete();

            foreach (array_values($components) as $i => $component) {
                GradeComponentTemplate::create([
                    'subject_id' => $subject->id,
                    'name' => trim($component['name']),
                    'kind' => $component['kind'],
                    'weight' => $component['weight'],
                    'min_score' => $component['min_score'] ?? null,
                    'is_required' => $component['is_required'] ?? true,
                    'is_remote' => $component['is_remote'] ?? false,
                    'sort_order' => $i + 1,
                ]);
            }
        });

        return $this->structureFor($subject);
    }

    /**
     * Kiểm tra một cơ cấu điểm theo quy chế áp dụng cho khóa $cohort (mặc định khóa của năm hiện tại).
     *
     * @param  list<array<string, mixed>>  $components
     */
    public function validate(array $components, ?int $cohort = null): void
    {
        $rules = $this->rules($cohort);

        if (count($components) < $rules['min_components']) {
            $this->fail(
                "Cơ cấu điểm cần tối thiểu {$rules['min_components']} điểm thành phần, hiện có ".count($components).'.',
                'Thêm thành phần điểm (ví dụ chuyên cần, giữa kỳ, cuối kỳ) (BR-SUB-04).'
            );
        }

        $names = array_map(fn (array $c) => mb_strtolower(trim((string) $c['name'])), $components);

        if (count($names) !== count(array_unique($names))) {
            $this->fail('Tên các thành phần điểm bị trùng nhau.', 'Đặt tên riêng cho từng thành phần, ví dụ "Bài tập 1", "Bài tập 2".');
        }

        foreach ($components as $component) {
            if ((float) $component['weight'] <= 0) {
                $this->fail("Thành phần \"{$component['name']}\" có trọng số {$component['weight']}%.", 'Trọng số mỗi thành phần phải lớn hơn 0; bỏ thành phần không dùng.');
            }

            $min = $component['min_score'] ?? null;

            if ($min !== null && ((float) $min < 0 || (float) $min > 10)) {
                $this->fail("Điểm sàn của \"{$component['name']}\" phải trong thang 0–10.", 'Nhập điểm sàn từ 0 đến 10, hoặc để trống nếu không có điểm sàn.');
            }
        }

        $total = round(array_sum(array_map(fn (array $c) => (float) $c['weight'], $components)), 2);

        if (abs($total - 100) > 0.001) {
            $gap = abs(100 - $total);

            $this->fail(
                'Tổng trọng số các thành phần điểm là '.$this->percent($total).', phải đúng 100% ('.($total < 100 ? 'thiếu ' : 'thừa ').$this->percent($gap).').',
                'Điều chỉnh trọng số để tổng bằng 100% (BR-SUB-04).'
            );
        }

        $final = $this->sumWeight($components, fn (array $c) => $c['kind'] === ComponentKind::FinalExam->value);

        if ($final < $rules['final_exam_min_weight']) {
            $this->fail(
                'Trọng số thi cuối kỳ là '.$this->percent($final).", thấp hơn mức tối thiểu {$rules['final_exam_min_weight']}%.",
                'Tăng trọng số thành phần "Thi cuối kỳ" (loại final_exam) theo quy chế (BR-SUB-04).'
            );
        }

        $remote = $this->sumWeight($components, fn (array $c) => ! empty($c['is_remote']));

        if ($remote > $rules['remote_max_weight']) {
            $this->fail(
                'Tổng trọng số đánh giá từ xa là '.$this->percent($remote).", vượt mức tối đa {$rules['remote_max_weight']}%.",
                'Giảm trọng số các thành phần đánh giá từ xa hoặc chuyển sang đánh giá trực tiếp (BR-SUB-04).'
            );
        }
    }

    /** @return Collection<int, GradeComponentTemplate> */
    public function structureFor(Subject $subject): Collection
    {
        return GradeComponentTemplate::query()->where('subject_id', $subject->id)->orderBy('sort_order')->get();
    }

    /**
     * Cơ cấu điểm để GRD sao chép sang lớp học phần khi mở lớp (BR-SUB-04: lớp kế thừa cơ cấu của học phần).
     *
     * @return list<array{name: string, kind: string, weight: float, min_score: ?float, is_required: bool, is_remote: bool, sort_order: int}>
     */
    public function templateFor(Subject $subject): array
    {
        $structure = $this->structureFor($subject);

        if ($structure->isEmpty()) {
            $this->fail("Học phần {$subject->code} chưa có cơ cấu điểm.", 'Khai báo cơ cấu điểm cho học phần trước khi mở lớp (FR-SUB-005).');
        }

        return $structure->map(fn (GradeComponentTemplate $c) => $c->toComponent())->all();
    }

    /**
     * Ngưỡng của quy chế đang áp dụng. Chưa ban hành bộ quy chế nào thì dùng giá trị mặc định (phụ lục C).
     *
     * @return array{min_components: int, final_exam_min_weight: float, remote_max_weight: float}
     */
    public function rules(?int $cohort = null): array
    {
        $cohort ??= (int) now(config('studentmanager.display_timezone'))->year;

        try {
            $values = $this->policies->values($cohort);
        } catch (BusinessRuleException) {
            $values = PolicyDefinitions::defaults();
        }

        return [
            'min_components' => (int) $values['grading.min_components'],
            'final_exam_min_weight' => (float) $values['grading.final_exam_min_weight'],
            'remote_max_weight' => (float) $values['grading.remote_max_weight'],
        ];
    }

    /** @param  list<array<string, mixed>>  $components */
    private function sumWeight(array $components, callable $filter): float
    {
        return round(array_sum(array_map(fn (array $c) => (float) $c['weight'], array_filter($components, $filter))), 2);
    }

    private function percent(float $value): string
    {
        return rtrim(rtrim(number_format($value, 2, ',', ''), '0'), ',').'%';
    }
}
