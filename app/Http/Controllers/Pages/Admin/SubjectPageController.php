<?php

namespace App\Http\Controllers\Pages\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Trang quản lý học phần (FE #10 phần học phần, mock admin/hoc-phan): danh sách, thêm / sửa / ngừng / xóa, quan hệ tiên
 * quyết – học trước – song hành – tương đương, cơ cấu điểm thành phần, xuất Excel. Chỉ trả view + URL API + cờ quyền;
 * dữ liệu do resources/js/pages/admin-subjects.js gọi API SUB.
 */
class SubjectPageController extends Controller
{
    public function __construct(private readonly AccessControl $access) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        return view('admin.subject.index', ['pageData' => [
            'api' => [
                'list' => route('admin.subjects.index'),
                'show' => route('admin.subjects.show', ['subject' => '__ID__']),
                'store' => route('admin.subjects.store'),
                'update' => route('admin.subjects.update', ['subject' => '__ID__']),
                'destroy' => route('admin.subjects.destroy', ['subject' => '__ID__']),
                'export' => route('admin.subjects.export'),
                'relations' => route('admin.subjects.relations.index', ['subject' => '__ID__']),
                'relationStore' => route('admin.subjects.relations.store', ['subject' => '__ID__']),
                'relationEnd' => route('admin.subject-relations.end', ['subjectRelation' => '__ID__']),
                'relationDestroy' => route('admin.subject-relations.destroy', ['subjectRelation' => '__ID__']),
                'components' => route('admin.subjects.grade-components.show', ['subject' => '__ID__']),
                'componentsUpdate' => route('admin.subjects.grade-components.update', ['subject' => '__ID__']),
                'faculties' => route('admin.units.index', ['unitType' => 'faculties']),
                'departments' => route('admin.units.index', ['unitType' => 'departments']),
            ],
            'can' => [
                'create' => $this->access->allows($user, 'SUB', PermissionAction::Create),
                'update' => $this->access->allows($user, 'SUB', PermissionAction::Update),
                'delete' => $this->access->allows($user, 'SUB', PermissionAction::Delete),
                'export' => $this->access->allows($user, 'SUB', PermissionAction::Export),
            ],
        ]]);
    }
}
