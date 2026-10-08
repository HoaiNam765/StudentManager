<?php

namespace App\Modules\System\Settings;

/**
 * Danh mục tham số hệ thống (FR-SYS-001). Thêm tham số mới: khai báo ở đây, không cần migration.
 *
 * - default: giá trị khi chưa ai lưu; tham số gắn với config thì mặc định là giá trị config hiện tại (từ .env).
 * - config: khóa config được ghi đè lúc khởi động khi tham số đã lưu (SettingService::applyToConfig).
 * - important: thay đổi thì thông báo cho các ADMIN khác (BR-SYS-08).
 *
 * Học kỳ hiện hành không nằm ở đây: do module ACY quản lý (đặt học kỳ "Đang diễn ra").
 */
final class SettingDefinitions
{
    /** @return array<string, array{label: string, group: string, default: mixed, rules: list<string>, important: bool, config?: string}> */
    public static function all(): array
    {
        return [
            // Giao diện (FE) đọc tên trường từ config studentmanager.school.*: tham số đã lưu ghi đè đúng các khóa đó
            'school.name' => ['label' => 'Tên trường', 'group' => 'Thông tin trường', 'default' => config('studentmanager.school.name'), 'rules' => ['required', 'string', 'max:255'], 'important' => false, 'config' => 'studentmanager.school.name'],
            'school.short_name' => ['label' => 'Tên ngắn', 'group' => 'Thông tin trường', 'default' => config('studentmanager.school.short_name'), 'rules' => ['required', 'string', 'max:100'], 'important' => false, 'config' => 'studentmanager.school.short_name'],
            'school.abbr' => ['label' => 'Tên viết tắt', 'group' => 'Thông tin trường', 'default' => config('studentmanager.school.abbr'), 'rules' => ['required', 'string', 'max:20'], 'important' => false, 'config' => 'studentmanager.school.abbr'],
            'school.logo_path' => ['label' => 'Logo (đường dẫn trên disk public)', 'group' => 'Thông tin trường', 'default' => null, 'rules' => ['nullable', 'string', 'max:500'], 'important' => false],
            'school.address' => ['label' => 'Địa chỉ', 'group' => 'Thông tin liên hệ', 'default' => null, 'rules' => ['nullable', 'string', 'max:500'], 'important' => false],
            'school.phone' => ['label' => 'Điện thoại', 'group' => 'Thông tin liên hệ', 'default' => null, 'rules' => ['nullable', 'string', 'max:30'], 'important' => false],
            'school.email' => ['label' => 'Email liên hệ', 'group' => 'Thông tin liên hệ', 'default' => null, 'rules' => ['nullable', 'email', 'max:255'], 'important' => false],
            'school.website' => ['label' => 'Trang web', 'group' => 'Thông tin liên hệ', 'default' => null, 'rules' => ['nullable', 'url', 'max:255'], 'important' => false],

            'locale.timezone' => ['label' => 'Múi giờ hiển thị', 'group' => 'Ngôn ngữ và định dạng', 'default' => config('studentmanager.display_timezone'), 'rules' => ['required', 'timezone:all'], 'important' => true, 'config' => 'studentmanager.display_timezone'],
            'locale.language' => ['label' => 'Ngôn ngữ mặc định', 'group' => 'Ngôn ngữ và định dạng', 'default' => config('app.locale'), 'rules' => ['required', 'in:vi,en'], 'important' => true, 'config' => 'app.locale'],
            'locale.date_format' => ['label' => 'Định dạng ngày', 'group' => 'Ngôn ngữ và định dạng', 'default' => config('studentmanager.formats.date'), 'rules' => ['required', 'in:d/m/Y,d-m-Y,Y-m-d'], 'important' => false, 'config' => 'studentmanager.formats.date'],

            'session.idle_minutes' => ['label' => 'Hết phiên sau số phút không hoạt động', 'group' => 'Chính sách phiên', 'default' => (int) config('session.lifetime'), 'rules' => ['required', 'integer', 'min:5', 'max:1440'], 'important' => true, 'config' => 'session.lifetime'],
            'session.remember_days' => ['label' => 'Số ngày "ghi nhớ đăng nhập" (0 = tắt)', 'group' => 'Chính sách phiên', 'default' => (int) config('studentmanager.auth.remember_days'), 'rules' => ['required', 'integer', 'min:0', 'max:365'], 'important' => true, 'config' => 'studentmanager.auth.remember_days'],
        ];
    }

    public static function has(string $key): bool
    {
        return array_key_exists($key, self::all());
    }
}
