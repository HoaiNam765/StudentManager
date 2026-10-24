<?php

namespace App\Http\Navigation;

use App\Models\User;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use Illuminate\Support\Facades\Route;

/**
 * Menu bên của các trang đã nối API (layouts/portal), theo cổng. Một mục chỉ hiện khi route tồn tại và người dùng có
 * đúng quyền mà middleware của route trang yêu cầu. Ẩn mục chỉ để tiện dùng, không phải bảo mật: route vẫn chặn.
 * Trang mới nối thì thêm một dòng vào ITEMS (xem docs/fe-be-mapping.md).
 */
class PortalMenu
{
    /** Cổng => nhóm => danh sách [nhãn, biểu tượng Material Symbols, tên route, quyền "MODULE.action[,ALL]" hoặc null]. */
    private const ITEMS = [
        'student' => [
            'Học tập' => [
                ['Tổng quan', 'dashboard', 'student.home', null],
                ['Chương trình đào tạo', 'school', 'student.pages.curriculum', 'CUR.view'],
                ['Thời khóa biểu', 'calendar_month', 'student.pages.timetable', 'TTB.view'],
                ['Hồ sơ cá nhân', 'person', 'student.pages.profile', 'STU.view'],
                ['Đăng ký học phần', 'how_to_reg', 'student.pages.registration', 'ENR.view'],
                ['Kết quả đăng ký', 'fact_check', 'student.pages.registration-result', 'ENR.view'],
                ['Kết quả học tập & Điểm', 'grade', 'student.pages.grades', 'GRD.view'],
            ],
        ],
        'teacher' => [
            'Giảng dạy' => [
                ['Tổng quan', 'dashboard', 'teacher.home', null],
                ['Quản lý & Nhập điểm', 'fact_check', 'teacher.pages.gradebook', 'GRD.view'],
                ['Gửi thông báo', 'campaign', 'teacher.pages.announcements', 'NOT.create'],
            ],
        ],
        'admin' => [
            'Quản lý đào tạo' => [
                ['Tổng quan', 'dashboard', 'admin.home', null],
                ['Vận hành & Khóa sổ', 'settings_backup_restore', 'admin.pages.system-operations', 'SYS.view'],
                ['Dạy bù & Phòng học', 'meeting_room', 'admin.pages.makeup', 'TTB.view'],
                ['Đăng ký học phần', 'how_to_reg', 'admin.pages.enrollment', 'ENR.view'],
                ['Xếp thời khóa biểu', 'calendar_month', 'admin.pages.timetable', 'TTB.view'],
                ['Năm học & Lịch học vụ', 'calendar_month', 'admin.pages.academic-years', 'ACY.view,ALL'],
                ['Lớp học phần', 'groups', 'admin.pages.course-sections', 'CLS.view'],
                ['Quản lý & Duyệt điểm', 'verified', 'admin.pages.grades', 'GRD.view'],
                ['Duyệt điều chỉnh điểm', 'rule', 'admin.pages.grade-changes', 'GRD.approve'],
                ['Gửi thông báo', 'campaign', 'admin.pages.announcements', 'NOT.create'],
            ],
        ],
    ];

    /** Tên hiển thị của từng cổng. */
    public const PORTALS = [
        'student' => 'Cổng Sinh viên',
        'teacher' => 'Cổng Giảng viên',
        'admin' => 'Quản lý Đào tạo',
    ];

    public function __construct(private readonly AccessControl $access) {}

    /**
     * @return array<string, list<array{label: string, icon: string, url: string, active: bool}>>
     */
    public function for(string $portal, User $user): array
    {
        $menu = [];

        foreach (self::ITEMS[$portal] ?? [] as $group => $items) {
            foreach ($items as [$label, $icon, $route, $permission]) {
                if (! Route::has($route) || ($permission !== null && ! $this->allows($user, $permission))) {
                    continue;
                }

                $menu[$group][] = [
                    'label' => $label,
                    'icon' => $icon,
                    'url' => route($route),
                    'active' => request()->routeIs($route),
                ];
            }
        }

        return $menu;
    }

    private function allows(User $user, string $permission): bool
    {
        [$permission, $scope] = array_pad(explode(',', $permission, 2), 2, null);
        [$module, $action] = explode('.', $permission, 2);
        $action = PermissionAction::from($action);

        return $scope === 'ALL'
            ? $this->access->allowsGlobally($user, $module, $action)
            : $this->access->allows($user, $module, $action);
    }
}
