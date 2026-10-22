<?php

namespace Database\Factories;

use App\Modules\Faculty\Models\Major;
use App\Modules\Faculty\Models\TrainingType;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Services\IdentityNumber;
use App\Modules\System\Models\LookupCategory;
use App\Modules\System\Models\LookupValue;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Hồ sơ thí sinh giả (NFR-DAT-02): họ tên Faker `vi_VN`, CCCD giả bắt đầu 099 (không trùng số thật), email
 * example.test, trạng thái Chờ nhập học. Dùng cho dữ liệu demo (#122) và test.
 *
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    private static ?Generator $vi = null;

    private static ?int $sequence = null;

    public function definition(): array
    {
        $vi = self::$vi ??= tap(FakerFactory::create('vi_VN'), fn (Generator $f) => $f->seed(2026));
        // Số thứ tự tiếp nối hồ sơ demo đã có để CCCD giả / email không trùng khi chạy lại
        self::$sequence ??= Student::withTrashed()->where('personal_email', 'like', 'demo.sv%')->count();
        $n = ++self::$sequence;
        $female = $n % 2 === 0;
        $nationalId = sprintf('0993%08d', $n);
        $lookup = fn (string $category, string $code) => LookupValue::query()->where('code', $code)
            ->whereHas('category', fn ($q) => $q->where('code', $category))->value('id');

        return [
            'full_name' => $vi->lastName().' '.$vi->middleName($female ? 'female' : 'male').' '.($female ? $vi->firstNameFemale() : $vi->firstNameMale()),
            'gender_id' => $lookup(LookupCategory::GENDER, $female ? '2' : '1'),
            'date_of_birth' => $vi->dateTimeBetween('-20 years', '-18 years')->format('Y-m-d'),
            'place_of_birth' => $vi->city(),
            'ethnicity_id' => $lookup(LookupCategory::ETHNICITY, '01'),
            'nationality_id' => $lookup(LookupCategory::NATIONALITY, 'VN'),
            'id_type' => 'national_id',
            'national_id' => $nationalId,
            'national_id_hash' => IdentityNumber::hash($nationalId),
            'personal_email' => sprintf('demo.sv%05d@example.test', $n),
            'phone' => sprintf('0901%06d', $n),
            'major_id' => fn () => Major::query()->inRandomOrder()->value('id'),
            'training_type_id' => fn () => TrainingType::query()->where('code', 'CQ')->value('id'),
            'cohort' => 2026,
            'admission_date' => '2026-09-05',
            'status' => 'pending',
        ];
    }

    /** Khóa và ngày nhập học (năm sinh lùi theo khóa). */
    public function cohort(int $year): static
    {
        return $this->state(fn () => [
            'cohort' => $year,
            'admission_date' => "{$year}-09-05",
            'date_of_birth' => sprintf('%d-%02d-%02d', $year - 18, random_int(1, 12), random_int(1, 28)),
        ]);
    }
}
