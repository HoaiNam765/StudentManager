<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DevUserSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seed_tao_tai_khoan_dung_thu(): void
    {
        $this->seed();

        $user = User::where('email', DevUserSeeder::EMAIL)->first();

        $this->assertNotNull($user);
        $this->assertTrue(Hash::check(DevUserSeeder::PASSWORD, $user->password), 'Mật khẩu phải được băm');
    }

    public function test_seed_chay_lap_lai_khong_loi_trung(): void
    {
        $this->seed();
        $this->seed();

        $this->assertSame(1, User::where('email', DevUserSeeder::EMAIL)->count());
    }

    public function test_khong_tao_tai_khoan_dung_thu_o_production(): void
    {
        $this->app->detectEnvironment(fn () => 'production');

        // Gọi thẳng seeder: lệnh db:seed ở production tự dừng để hỏi xác nhận, sẽ không kiểm tra được gì
        $this->app->make(DevUserSeeder::class)->run();

        $this->assertSame(0, User::count());
    }

    public function test_database_seeder_khong_tat_su_kien_model(): void
    {
        // Nếu tắt sự kiện model, lớp nền app/Support không điền được người tạo/sửa và search_text
        $this->assertNotContains(WithoutModelEvents::class, class_uses_recursive(DatabaseSeeder::class));
    }

    public function test_seeder_cua_module_trong_danh_sach_deu_ton_tai(): void
    {
        $this->assertIsArray(DatabaseSeeder::MODULE_SEEDERS);

        foreach (DatabaseSeeder::MODULE_SEEDERS as $seeder) {
            $this->assertTrue(class_exists($seeder), "Không tìm thấy seeder {$seeder}");
        }
    }
}
