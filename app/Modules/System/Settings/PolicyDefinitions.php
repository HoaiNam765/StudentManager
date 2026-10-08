<?php

namespace App\Modules\System\Settings;

use Illuminate\Support\Facades\Validator;

/**
 * Danh mục tham số của bộ quy chế đào tạo (FR-SYS-003, GC-12). Giá trị mặc định chép từ docs/BA.md phụ lục C
 * (tham chiếu Thông tư 56/2026/TT-BGDĐT theo các bản tổng hợp, cần đối chiếu quy chế của trường).
 *
 * Module cần ngưỡng nào thì đọc bằng PolicyResolver::value('attendance.ban_percent', $khoa, $ngay), không viết cứng.
 * Thêm tham số mới: khai báo ở đây; bộ quy chế đã ban hành chưa có khóa mới thì dùng giá trị mặc định.
 */
final class PolicyDefinitions
{
    /** @return array<string, array{label: string, group: string, default: mixed, rules?: list<string>, bands?: string}> */
    public static function all(): array
    {
        return [
            'grading.scale' => ['label' => 'Thang quy đổi điểm học phần (thang 10 → điểm chữ → thang 4)', 'group' => 'Điểm', 'bands' => 'grading', 'default' => [
                ['min' => 8.5, 'letter' => 'A', 'gpa' => 4.0, 'passed' => true],
                ['min' => 7.0, 'letter' => 'B', 'gpa' => 3.0, 'passed' => true],
                ['min' => 5.5, 'letter' => 'C', 'gpa' => 2.0, 'passed' => true],
                ['min' => 4.0, 'letter' => 'D', 'gpa' => 1.0, 'passed' => true],
                ['min' => 0.0, 'letter' => 'F', 'gpa' => 0.0, 'passed' => false],
            ]],
            'grading.pass_score' => ['label' => 'Điểm đạt học phần (thang 10)', 'group' => 'Điểm', 'default' => 4.0, 'rules' => ['required', 'numeric', 'between:0,10']],
            'grading.min_components' => ['label' => 'Số điểm thành phần tối thiểu của một học phần', 'group' => 'Điểm', 'default' => 3, 'rules' => ['required', 'integer', 'between:1,10']],
            'grading.final_exam_min_weight' => ['label' => 'Trọng số tối thiểu của điểm thi cuối kỳ (%)', 'group' => 'Điểm', 'default' => 50, 'rules' => ['required', 'integer', 'between:0,100']],
            'grading.remote_max_weight' => ['label' => 'Trọng số tối đa của đánh giá từ xa (%)', 'group' => 'Điểm', 'default' => 50, 'rules' => ['required', 'integer', 'between:0,100']],
            'grading.appeal_days' => ['label' => 'Thời hạn phúc khảo (ngày kể từ ngày công bố)', 'group' => 'Điểm', 'default' => 7, 'rules' => ['required', 'integer', 'between:0,60']],

            'classification.bands' => ['label' => 'Xếp loại học lực theo GPA (thang 4)', 'group' => 'Xếp loại', 'bands' => 'classification', 'default' => [
                ['min' => 3.6, 'label' => 'Xuất sắc', 'graduation' => true],
                ['min' => 3.2, 'label' => 'Giỏi', 'graduation' => true],
                ['min' => 2.5, 'label' => 'Khá', 'graduation' => true],
                ['min' => 2.0, 'label' => 'Trung bình', 'graduation' => true],
                ['min' => 1.0, 'label' => 'Yếu', 'graduation' => false],
                ['min' => 0.0, 'label' => 'Kém', 'graduation' => false],
            ]],

            'registration.load_min_ratio' => ['label' => 'Khối lượng đăng ký tối thiểu (tỷ lệ so với khối lượng trung bình một học kỳ)', 'group' => 'Đăng ký học phần', 'default' => 0.6667, 'rules' => ['required', 'numeric', 'between:0,3']],
            'registration.load_max_ratio' => ['label' => 'Khối lượng đăng ký tối đa (tỷ lệ so với khối lượng trung bình một học kỳ)', 'group' => 'Đăng ký học phần', 'default' => 1.5, 'rules' => ['required', 'numeric', 'between:0,3']],
            'study.max_duration_factor' => ['label' => 'Thời gian học tối đa (lần thời gian đào tạo chuẩn)', 'group' => 'Đăng ký học phần', 'default' => 2.0, 'rules' => ['required', 'numeric', 'between:1,3']],

            'academic_warning.failed_credit_percent' => ['label' => 'Cảnh báo: tín chỉ không đạt trong kỳ vượt (%) số tín chỉ đăng ký', 'group' => 'Cảnh báo học vụ', 'default' => 50, 'rules' => ['required', 'integer', 'between:1,100']],
            'academic_warning.term_gpa_first' => ['label' => 'Cảnh báo: GPA học kỳ đầu của khóa dưới', 'group' => 'Cảnh báo học vụ', 'default' => 0.8, 'rules' => ['required', 'numeric', 'between:0,4']],
            'academic_warning.term_gpa_other' => ['label' => 'Cảnh báo: GPA các học kỳ sau dưới', 'group' => 'Cảnh báo học vụ', 'default' => 1.0, 'rules' => ['required', 'numeric', 'between:0,4']],
            'academic_warning.debt_credits' => ['label' => 'Cảnh báo: tổng tín chỉ nợ từ (ví dụ tham khảo, theo quy chế trường)', 'group' => 'Cảnh báo học vụ', 'default' => 24, 'rules' => ['required', 'integer', 'between:1,200']],
            'academic_warning.max_consecutive' => ['label' => 'Số lần cảnh báo liên tiếp tối đa trước khi xét buộc thôi học', 'group' => 'Cảnh báo học vụ', 'default' => 3, 'rules' => ['required', 'integer', 'between:1,10']],

            'graduation.min_cgpa' => ['label' => 'Điều kiện tốt nghiệp: CGPA tối thiểu', 'group' => 'Tốt nghiệp', 'default' => 2.0, 'rules' => ['required', 'numeric', 'between:0,4']],
            'graduation.retake_downgrade_percent' => ['label' => 'Giảm hạng tốt nghiệp khi khối lượng học lại vượt (%) khối lượng chuẩn', 'group' => 'Tốt nghiệp', 'default' => 10, 'rules' => ['required', 'integer', 'between:0,100']],

            'attendance.warning_percent' => ['label' => 'Cảnh báo vắng khi vắng từ (%) số buổi', 'group' => 'Điểm danh', 'default' => 10, 'rules' => ['required', 'integer', 'between:0,100']],
            'attendance.ban_percent' => ['label' => 'Cấm thi khi vắng vượt (%) số buổi', 'group' => 'Điểm danh', 'default' => 20, 'rules' => ['required', 'integer', 'between:0,100']],
        ];
    }

    /** @return array<string, mixed> */
    public static function defaults(): array
    {
        return array_map(fn (array $definition) => $definition['default'], self::all());
    }

    /** Lỗi của một giá trị, hoặc null nếu hợp lệ. */
    public static function errorFor(string $key, mixed $value): ?string
    {
        $definition = self::all()[$key] ?? null;

        if ($definition === null) {
            return "Không có tham số quy chế \"{$key}\".";
        }

        if (isset($definition['bands'])) {
            return self::bandsError($definition['bands'], $value);
        }

        $validator = Validator::make(['value' => $value], ['value' => $definition['rules']], [], ['value' => $definition['label']]);

        return $validator->fails() ? $validator->errors()->first('value') : null;
    }

    /**
     * Ràng buộc giữa các tham số trong cùng một bộ.
     *
     * @param  array<string, mixed>  $values
     * @return list<string>
     */
    public static function crossErrors(array $values): array
    {
        $errors = [];

        if (($values['registration.load_min_ratio'] ?? 0) >= ($values['registration.load_max_ratio'] ?? 0)) {
            $errors[] = 'Khối lượng đăng ký tối thiểu phải nhỏ hơn khối lượng tối đa.';
        }

        if (($values['attendance.warning_percent'] ?? 0) >= ($values['attendance.ban_percent'] ?? 0)) {
            $errors[] = 'Ngưỡng cảnh báo vắng phải nhỏ hơn ngưỡng cấm thi.';
        }

        $passScore = $values['grading.pass_score'] ?? null;
        $lowestPassing = collect($values['grading.scale'] ?? [])->where('passed', true)->min('min');

        if ($passScore !== null && $lowestPassing !== null && (float) $passScore !== (float) $lowestPassing) {
            $errors[] = "Điểm đạt học phần ({$passScore}) phải bằng ngưỡng dưới của mức đạt thấp nhất trong thang điểm ({$lowestPassing}).";
        }

        return $errors;
    }

    private static function bandsError(string $type, mixed $value): ?string
    {
        if (! is_array($value) || ! array_is_list($value) || count($value) < 2 || count($value) > 12) {
            return 'Bảng phải có từ 2 đến 12 mức.';
        }

        $previous = null;
        $names = [];

        foreach ($value as $index => $band) {
            $position = $index + 1;

            if (! is_array($band) || ! is_numeric($band['min'] ?? null)) {
                return "Mức {$position}: thiếu ngưỡng dưới (min).";
            }

            $max = $type === 'grading' ? 10 : 4;

            if ($band['min'] < 0 || $band['min'] > $max) {
                return "Mức {$position}: ngưỡng dưới phải từ 0 đến {$max}.";
            }

            if ($previous !== null && $band['min'] >= $previous) {
                return "Mức {$position}: các mức phải xếp từ cao xuống thấp, ngưỡng dưới giảm dần.";
            }

            $previous = (float) $band['min'];

            if ($type === 'grading') {
                if (! is_string($band['letter'] ?? null) || preg_match('/^[A-F][+]?$/', $band['letter']) !== 1) {
                    return "Mức {$position}: điểm chữ phải là A, B+, B, C+, C, D+, D hoặc F.";
                }

                if (! is_numeric($band['gpa'] ?? null) || $band['gpa'] < 0 || $band['gpa'] > 4) {
                    return "Mức {$position}: điểm thang 4 phải từ 0 đến 4.";
                }

                if (! is_bool($band['passed'] ?? null)) {
                    return "Mức {$position}: thiếu kết quả Đạt/Không đạt (passed).";
                }

                $names[] = $band['letter'];
            } else {
                if (! is_string($band['label'] ?? null) || trim($band['label']) === '') {
                    return "Mức {$position}: thiếu tên xếp loại.";
                }

                if (! is_bool($band['graduation'] ?? null)) {
                    return "Mức {$position}: thiếu cờ dùng cho xếp hạng tốt nghiệp (graduation).";
                }

                $names[] = $band['label'];
            }
        }

        if ((float) end($value)['min'] !== 0.0) {
            return 'Mức cuối cùng phải có ngưỡng dưới bằng 0 để mọi điểm đều được xếp mức.';
        }

        if (count($names) !== count(array_unique($names))) {
            return 'Tên các mức không được trùng nhau.';
        }

        return null;
    }
}
