<?php

namespace Database\Seeders;

use App\Modules\Auth\Enums\DataScope;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Models\Permission;
use App\Modules\Auth\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

/**
 * Vai trò hệ thống (docs/BA.md mục 4.1), danh mục quyền và ma trận phân quyền mặc định (mục 4.2, 4.3).
 *
 * Chạy lặp lại an toàn: chỉ nạp ma trận cho vai trò chưa có quyền nào, nên không ghi đè
 * các điều chỉnh mà quản trị viên đã làm qua giao diện (FR-AUTH-012).
 */
class AuthSeeder extends Seeder
{
    /** Mã => [tên, phạm vi dữ liệu mặc định cho ô có dấu *, đang hoạt động] */
    public const ROLES = [
        'ADMIN' => ['Quản trị hệ thống', DataScope::All, true],
        'ACAD' => ['Cán bộ Đào tạo', DataScope::All, true],
        'EXAM' => ['Cán bộ Khảo thí', DataScope::All, true],
        'CTSV' => ['Cán bộ Công tác sinh viên', DataScope::All, true],
        'FIN' => ['Cán bộ Tài chính – Kế toán', DataScope::All, true],
        'DEAN' => ['Lãnh đạo Khoa / Bộ môn', DataScope::Faculty, true],
        'LEC' => ['Giảng viên', DataScope::Section, true],
        'ADV' => ['Cố vấn học tập', DataScope::Advisee, true],
        'STU' => ['Sinh viên', DataScope::Own, true],
        'GUA' => ['Phụ huynh (tùy chọn)', DataScope::Own, false],
    ];

    public const MODULES = [
        'AUTH' => 'Xác thực và phân quyền',
        'STU' => 'Sinh viên',
        'CLS' => 'Lớp',
        'FAC' => 'Khoa / bộ môn',
        'SUB' => 'Học phần',
        'ENR' => 'Đăng ký học phần',
        'TTB' => 'Thời khóa biểu',
        'ATT' => 'Điểm danh',
        'GRD' => 'Điểm',
        'FEE' => 'Học phí',
        'DOC' => 'Hồ sơ và giấy tờ',
        'NOT' => 'Thông báo',
        'EXM' => 'Thi',
        'TCH' => 'Giảng viên',
        'RPT' => 'Báo cáo',
        'SYS' => 'Quản trị hệ thống',
        'ACY' => 'Năm học – học kỳ',
        'CUR' => 'Chương trình đào tạo',
        'ROM' => 'Phòng học',
        'GRA' => 'Học vụ và tốt nghiệp',
        'REQ' => 'Đơn từ',
        'SCH' => 'Học bổng – rèn luyện – kỷ luật',
        'EVA' => 'Khảo sát chất lượng',
        'AUDIT' => 'Nhật ký kiểm toán',
    ];

    /**
     * Ma trận mặc định, chép từ docs/BA.md mục 4.2.
     * Cột: ADMIN ACAD EXAM CTSV FIN DEAN LEC ADV STU. '-' là không có quyền.
     * Ô có dấu * chỉ áp dụng trong phạm vi dữ liệu của vai trò (xem scopeFor()).
     * Hàng AUDIT không có trong mục 4.2: thêm theo FR-SYS-004 (chỉ ADMIN xem và xuất nhật ký).
     */
    public const MATRIX = [
        'AUTH' => ['CRUDA', 'U*', 'U*', 'U*', 'U*', 'U*', 'U*', 'U*', 'U*'],
        'STU' => ['R', 'CRUAX', 'R', 'CRUAX', 'R', 'RX*', 'R*', 'RU*', 'RU*'],
        'CLS' => ['R', 'CRUDAX', 'R', 'RU', 'R', 'RA*', 'R*', 'R*', 'R*'],
        'FAC' => ['CRUD', 'CRUD', 'R', 'R', 'R', 'RU*', 'R', 'R', 'R'],
        'SUB' => ['R', 'CRUDAX', 'R', 'R', 'R', 'CRUA*', 'RU*', 'R', 'R'],
        'ENR' => ['R', 'CRUDAX', 'R', 'R', 'R', 'R*', 'R*', 'R*', 'CRD*'],
        'TTB' => ['R', 'CRUDAX', 'R', 'R', '-', 'CR*', 'CR*', 'R*', 'R*'],
        'ATT' => ['R', 'RAX', 'R', 'R', '-', 'RX*', 'CRU*', 'R*', 'R*'],
        'GRD' => ['R', 'RUAX', 'RAX', 'R', '-', 'RA*', 'CRU*', 'R*', 'R*'],
        'FEE' => ['R', 'R', '-', 'RA', 'CRUDAX', 'R*', '-', 'R*', 'R*'],
        'DOC' => ['R', 'CRUAX', 'R', 'CRUAX', 'R*', 'R*', '-', 'R*', 'CR*'],
        'NOT' => ['CRUDX', 'CRX', 'CR', 'CR', 'CR', 'CR*', 'CR*', 'CR*', 'RU*'],
        'EXM' => ['R', 'RX', 'CRUDAX', 'R', '-', 'RA*', 'RU*', 'R*', 'R*'],
        'TCH' => ['R', 'CRUX', 'R', 'R', 'R', 'CRUAX*', 'RU*', 'RU*', 'R'],
        'RPT' => ['RX', 'RX', 'RX', 'RX', 'RX', 'RX*', 'R*', 'R*', 'R*'],
        'SYS' => ['CRUDX', 'RU', '-', '-', '-', '-', '-', '-', '-'],
        'ACY' => ['R', 'CRUDA', 'R', 'R', 'R', 'R', 'R', 'R', 'R'],
        'CUR' => ['R', 'CRUDAX', 'R', 'R', 'R', 'CRUA*', 'R', 'R', 'R*'],
        'ROM' => ['CRUD', 'CRUDX', 'R', 'R', '-', 'R', 'R', 'R', 'R'],
        'GRA' => ['R', 'CRUDAX', 'RX', 'RA', 'R', 'RA*', '-', 'R*', 'CR*'],
        'REQ' => ['R', 'CRUA', 'RA', 'RA', 'RA', 'RA*', 'RA*', 'RA*', 'CR*'],
        'SCH' => ['R', 'R', 'R', 'CRUDAX', 'R', 'RA*', 'R*', 'RA*', 'CR*'],
        'EVA' => ['R', 'CRUAX', 'R', 'R', '-', 'R*', 'R*', '-', 'CR*'],
        'AUDIT' => ['RX', '-', '-', '-', '-', '-', '-', '-', '-'],
    ];

    public const MATRIX_COLUMNS = ['ADMIN', 'ACAD', 'EXAM', 'CTSV', 'FIN', 'DEAN', 'LEC', 'ADV', 'STU'];

    public function run(): void
    {
        foreach (self::MODULES as $module => $moduleName) {
            foreach (PermissionAction::cases() as $action) {
                Permission::firstOrCreate(
                    ['module' => $module, 'action' => $action->value],
                    ['name' => $action->label().' — '.$moduleName],
                );
            }
        }

        $permissions = Permission::all()->keyBy(fn (Permission $p) => $p->key());

        foreach (self::ROLES as $code => [$name, , $active]) {
            $role = Role::firstOrCreate(
                ['code' => $code],
                ['name' => $name, 'is_system' => true, 'status' => $active ? 'active' : 'inactive'],
            );

            if ($role->permissions()->exists()) {
                continue;
            }

            $role->permissions()->sync($this->defaultPermissionsFor($code, $permissions));
        }
    }

    /**
     * @param  Collection<string, Permission>  $permissions
     * @return array<int, array{scope: string}>
     */
    private function defaultPermissionsFor(string $role, $permissions): array
    {
        $column = array_search($role, self::MATRIX_COLUMNS, true);

        if ($column === false) {
            return [];
        }

        $sync = [];

        foreach (self::MATRIX as $module => $cells) {
            $cell = $cells[$column];

            if ($cell === '-') {
                continue;
            }

            $scope = self::scopeFor($module, $role, str_ends_with($cell, '*'));

            foreach (str_split(rtrim($cell, '*')) as $letter) {
                $permission = $permissions->get($module.'.'.PermissionAction::fromLetter($letter)->value);
                $sync[$permission->id] = ['scope' => $scope->value];
            }
        }

        return $sync;
    }

    /** Phạm vi của một ô trong ma trận. */
    public static function scopeFor(string $module, string $role, bool $starred): DataScope
    {
        if (! $starred) {
            return DataScope::All;
        }

        // Hàng AUTH: "U*" là mỗi người tự quản lý tài khoản của chính mình (ghi chú 2, mục 4.2)
        if ($module === 'AUTH') {
            return DataScope::Own;
        }

        // Hàng TCH: giảng viên, cố vấn chỉ xem và sửa hồ sơ của chính mình (mục 4.3)
        if ($module === 'TCH' && in_array($role, ['LEC', 'ADV'], true)) {
            return DataScope::Own;
        }

        // Còn lại theo phạm vi mặc định của vai trò. Riêng ô DOC của FIN ("R*") vì FIN có phạm vi ALL
        // nên tạm hiểu là toàn trường; nếu cần chỉ giấy tờ liên quan học phí thì điều chỉnh ở module DOC.
        return self::ROLES[$role][1];
    }
}
