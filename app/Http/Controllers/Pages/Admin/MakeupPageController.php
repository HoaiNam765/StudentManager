<?php

namespace App\Http\Controllers\Pages\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Trang vận hành nghỉ / dạy bù từ các API buổi học hiện có (#48). */
class MakeupPageController extends Controller
{
    public function __construct(private readonly AccessControl $access) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        return view('admin.timetable.makeup', ['pageData' => [
            'api' => [
                'terms' => route('admin.academic-years.index'),
                'sections' => route('admin.course-sections.index'),
                'sessions' => route('admin.course-sections.sessions.index', ['courseSection' => '__ID__']),
                'generate' => route('admin.course-sections.sessions.generate', ['courseSection' => '__ID__']),
                'makeup' => route('admin.class-sessions.makeup', ['classSession' => '__ID__']),
                'cancel' => route('admin.class-sessions.cancel', ['classSession' => '__ID__']),
                'rooms' => route('admin.rooms.index'),
                'periods' => route('admin.periods.index'),
                'timetable' => route('admin.timetable.show'),
            ],
            'can' => [
                'create' => $this->access->allowsGlobally($user, 'TTB', PermissionAction::Create),
                'update' => $this->access->allowsGlobally($user, 'TTB', PermissionAction::Update),
            ],
        ]]);
    }
}
