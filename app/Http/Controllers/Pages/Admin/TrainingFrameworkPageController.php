<?php

namespace App\Http\Controllers\Pages\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Trang chương trình khung theo khoa – khóa – học kỳ: lập danh sách học phần dự kiến, gợi ý từ CTĐT, mở lớp học phần
 * hàng loạt, gửi thông báo cho sinh viên khoa – khóa. Chỉ trả view + URL API; dữ liệu do
 * resources/js/pages/admin-training-frameworks.js gọi.
 */
class TrainingFrameworkPageController extends Controller
{
    public function __construct(private readonly AccessControl $access) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $id = ['trainingFramework' => '__ID__'];

        return view('admin.training-framework.index', ['pageData' => [
            'api' => [
                'list' => route('admin.training-frameworks.index'),
                'show' => route('admin.training-frameworks.show', $id),
                'store' => route('admin.training-frameworks.store'),
                'update' => route('admin.training-frameworks.update', $id),
                'destroy' => route('admin.training-frameworks.destroy', $id),
                'itemStore' => route('admin.training-frameworks.items.store', $id),
                'itemUpdate' => route('admin.training-framework-items.update', ['trainingFrameworkItem' => '__ID__']),
                'itemDestroy' => route('admin.training-framework-items.destroy', ['trainingFrameworkItem' => '__ID__']),
                'suggest' => route('admin.training-frameworks.suggest', $id),
                'terms' => route('admin.academic-years.index'),
                'faculties' => route('admin.units.index', ['unitType' => 'faculties']),
                'cohorts' => route('admin.cohorts.index'),
                'subjects' => route('admin.subjects.index'),
            ],
            'links' => ['section' => route('admin.pages.course-sections.show', ['courseSection' => '__ID__'])],
            'can' => [
                'create' => $this->access->allowsGlobally($user, 'CLS', PermissionAction::Create),
            ],
        ]]);
    }
}
