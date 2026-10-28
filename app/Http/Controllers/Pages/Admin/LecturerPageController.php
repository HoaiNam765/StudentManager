<?php

namespace App\Http\Controllers\Pages\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\System\Services\ImportRegistry;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Trang quản lý giảng viên (mock admin/giang-vien*) cho Phòng Đào tạo / Quản trị, cùng kiểu trang Sinh viên. Chỉ trả
 * view + URL API + cờ quyền; dữ liệu do resources/js/pages/admin-lecturers.js gọi API TCH (danh sách, hồ sơ, thêm / sửa,
 * nghỉ việc / nghỉ hưu, tài khoản, kiêm nhiệm, xuất Excel); nhập file dùng chung trang trung tâm import (importer `lecturers`).
 */
class LecturerPageController extends Controller
{
    public function __construct(private readonly AccessControl $access) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        return view('admin.lecturer.index', ['pageData' => [
            'api' => [
                'list' => route('admin.lecturers.index'),
                'show' => route('admin.lecturers.show', ['lecturer' => '__ID__']),
                'store' => route('admin.lecturers.store'),
                'update' => route('admin.lecturers.update', ['lecturer' => '__ID__']),
                'status' => route('admin.lecturers.status', ['lecturer' => '__ID__']),
                'handover' => route('admin.lecturers.handover', ['lecturer' => '__ID__']),
                'account' => route('admin.lecturers.account', ['lecturer' => '__ID__']),
                'export' => route('admin.lecturers.export'),
                'faculties' => route('admin.units.index', ['unitType' => 'faculties']),
                'departments' => route('admin.units.index', ['unitType' => 'departments']),
                'academicYears' => route('admin.academic-years.index'),
                'currentTerm' => route('admin.academic-years.current-term'),
                'lookup' => route('lookups.options', ['category' => '__CODE__']),
            ],
            'links' => ['import' => route('admin.pages.lecturers.import')],
            'can' => [
                'create' => $this->access->allows($user, 'TCH', PermissionAction::Create),
                'update' => $this->access->allows($user, 'TCH', PermissionAction::Update),
                'export' => $this->access->allows($user, 'TCH', PermissionAction::Export),
                'import' => $this->access->allows($user, 'SYS', PermissionAction::View),
            ],
        ]]);
    }

    /** Nhập danh sách giảng viên từ Excel qua trung tâm import (importer `lecturers`, cùng mã JS trang nhập sinh viên). */
    public function import(Request $request, ImportRegistry $importers): View
    {
        $user = $request->user();

        return view('admin.lecturer.import', ['pageData' => [
            'importer' => 'lecturers',
            'noun' => 'hồ sơ giảng viên',
            'api' => [
                'importers' => route('admin.imports.importers'),
                'list' => route('admin.imports.index'),
                'upload' => route('admin.imports.upload'),
                'show' => route('admin.imports.show', ['batch' => '__ID__']),
                'validate' => route('admin.imports.validate', ['batch' => '__ID__']),
                'preview' => route('admin.imports.preview', ['batch' => '__ID__']),
                'save' => route('admin.imports.save', ['batch' => '__ID__']),
                'rollback' => route('admin.imports.rollback', ['batch' => '__ID__']),
            ],
            'links' => ['list' => route('admin.pages.lecturers')],
            'can' => [
                'create' => $this->access->allows($user, 'SYS', PermissionAction::Create),
                'delete' => $this->access->allows($user, 'SYS', PermissionAction::Delete),
            ],
            'available' => $importers->has('lecturers'),
        ]]);
    }
}
