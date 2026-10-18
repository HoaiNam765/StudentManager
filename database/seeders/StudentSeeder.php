<?php

namespace Database\Seeders;

use App\Modules\Faculty\Models\Major;
use App\Modules\Faculty\Models\TrainingType;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Services\IdentityNumber;
use App\Modules\System\Models\LookupCategory;
use App\Modules\System\Models\LookupValue;
use Illuminate\Database\Seeder;

/**
 * Hồ sơ sinh viên mẫu (STU): mỗi ngành 6 thí sinh trúng tuyển khóa 2026, trạng thái Chờ nhập học. Dữ liệu hoàn toàn giả
 * (NFR-DAT-02): họ tên ghép ngẫu nhiên có cố định, số CCCD giả bắt đầu 099, email ví dụ example.test.
 * MSSV, email trường, tài khoản cấp khi xác nhận nhập học (#102). Chạy lặp lại an toàn (bỏ qua số giấy tờ đã có).
 */
class StudentSeeder extends Seeder
{
    private const FAMILY = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Võ', 'Đặng', 'Bùi', 'Đỗ', 'Ngô'];

    private const MIDDLE = ['Văn', 'Thị', 'Minh', 'Ngọc', 'Hữu', 'Thanh', 'Gia', 'Anh'];

    private const GIVEN = ['An', 'Bình', 'Châu', 'Dũng', 'Giang', 'Hà', 'Khánh', 'Linh', 'Mai', 'Nam', 'Phúc', 'Quân', 'Tâm', 'Vy'];

    public const PER_MAJOR = 6;

    public const COHORT = 2026;

    public function run(): void
    {
        $type = TrainingType::query()->where('code', 'CQ')->first();
        $majors = Major::query()->orderBy('code')->get();

        if ($type === null || $majors->isEmpty()) {
            return;
        }

        $lookup = fn (string $category, string $code) => LookupValue::query()
            ->where('code', $code)
            ->whereHas('category', fn ($q) => $q->where('code', $category))
            ->value('id');

        $male = $lookup(LookupCategory::GENDER, '1');
        $female = $lookup(LookupCategory::GENDER, '2');
        $kinh = $lookup(LookupCategory::ETHNICITY, '01');
        $vietnam = $lookup(LookupCategory::NATIONALITY, 'VN');
        $n = 0;

        foreach ($majors as $major) {
            for ($i = 0; $i < self::PER_MAJOR; $i++, $n++) {
                $nationalId = sprintf('0992%08d', 26000 + $n);

                if (Student::withTrashed()->where('national_id_hash', IdentityNumber::hash($nationalId))->exists()) {
                    continue;
                }

                $isFemale = $n % 2 === 1;
                $name = self::FAMILY[$n % count(self::FAMILY)].' '.($isFemale ? 'Thị' : self::MIDDLE[$n % count(self::MIDDLE)]).' '.self::GIVEN[($n * 5) % count(self::GIVEN)];

                Student::create([
                    'full_name' => $name,
                    'gender_id' => $isFemale ? $female : $male,
                    'date_of_birth' => sprintf('2008-%02d-%02d', 1 + $n % 12, 1 + ($n * 3) % 28),
                    'place_of_birth' => 'TP. Hồ Chí Minh',
                    'ethnicity_id' => $kinh,
                    'nationality_id' => $vietnam,
                    'id_type' => 'national_id',
                    'national_id' => $nationalId,
                    'national_id_hash' => IdentityNumber::hash($nationalId),
                    'personal_email' => sprintf('thisinh%03d@example.test', $n + 1),
                    'phone' => sprintf('0900%06d', 260000 + $n),
                    'major_id' => $major->id,
                    'training_type_id' => $type->id,
                    'cohort' => self::COHORT,
                    'admission_date' => self::COHORT.'-09-05',
                    'status' => 'pending',
                ]);
            }
        }
    }
}
