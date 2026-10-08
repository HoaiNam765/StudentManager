<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Lưu ý: KHÔNG dùng trait WithoutModelEvents ở đây. Trait đó tắt sự kiện model trong lúc seed,
 * khiến lớp nền ở app/Support không điền được người tạo/sửa và cột search_text.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seeder của từng module, gọi theo thứ tự phụ thuộc trong docs/BA.md mục 5.5.
     * Khi làm module: tạo database/seeders/<TênModule>Seeder.php (dùng Factory, Faker vi_VN, chỉ dữ liệu giả)
     * rồi bỏ chú thích đúng dòng bên dưới. Seeder phải chạy lặp lại được mà không lỗi trùng.
     *
     * @var list<class-string<Seeder>>
     */
    public const MODULE_SEEDERS = [
        SystemSeeder::class,          // SYS: cấu hình, bộ quy chế, danh mục dùng chung
        AuthSeeder::class,            // AUTH: vai trò, quyền, ma trận phân quyền mặc định
        FacultySeeder::class,         // FAC: khoa, bộ môn, ngành
        AcademicYearSeeder::class,    // ACY: năm học, học kỳ, lịch học vụ
        RoomSeeder::class,            // ROM: phòng học
        // SubjectSeeder::class,      // SUB: học phần, quan hệ, cơ cấu điểm
        // CurriculumSeeder::class,   // CUR: chương trình đào tạo
        // TeacherSeeder::class,      // TCH: giảng viên
        // SchoolClassSeeder::class,  // CLS: khóa, lớp hành chính, lớp học phần
        // StudentSeeder::class,      // STU: sinh viên
        // TimetableSeeder::class,    // TTB: thời khóa biểu, buổi học
        // EnrollmentSeeder::class,   // ENR: đăng ký học phần
        // GradeSeeder::class,        // GRD: điểm
    ];

    public function run(): void
    {
        $this->call(self::MODULE_SEEDERS);

        // Chạy sau seeder của module để tài khoản dùng thử được gán vai trò ADMIN
        $this->call(DevUserSeeder::class);
    }
}
