<?php

namespace App\Http\Controllers\Pages\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Trang import và kết thúc học kỳ từ các API thật hiện có (#55). */
class SystemOperationPageController extends Controller
{
    public function __construct(private readonly AccessControl $access) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        return view('admin.system.operations', ['pageData' => [
            'api' => [
                'terms' => route('admin.academic-years.index'),
                'termStatus' => route('admin.academic-years.terms.status', ['term' => '__TERM__', 'status' => '__STATUS__']),
                'timetableStatus' => route('admin.terms.timetable.status', ['term' => '__TERM__']),
                'timetableLock' => route('admin.terms.timetable.lock', ['term' => '__TERM__']),
                'importers' => route('admin.imports.importers'),
                'imports' => route('admin.imports.index'),
                'importShow' => route('admin.imports.show', ['batch' => '__ID__']),
                'importPreview' => route('admin.imports.preview', ['batch' => '__ID__']),
                'importProgress' => route('admin.imports.progress', ['batch' => '__ID__']),
                'importUpload' => route('admin.imports.upload'),
                'importValidate' => route('admin.imports.validate', ['batch' => '__ID__']),
                'importSave' => route('admin.imports.save', ['batch' => '__ID__']),
                'importRollback' => route('admin.imports.rollback', ['batch' => '__ID__']),
                'importDelete' => route('admin.imports.destroy', ['batch' => '__ID__']),
            ],
            'can' => [
                'termApprove' => $this->access->allowsGlobally($user, 'ACY', PermissionAction::Approve),
                'timetableApprove' => $this->access->allowsGlobally($user, 'TTB', PermissionAction::Approve),
                'importCreate' => $this->access->allows($user, 'SYS', PermissionAction::Create),
                'importDelete' => $this->access->allows($user, 'SYS', PermissionAction::Delete),
            ],
        ]]);
    }
}
