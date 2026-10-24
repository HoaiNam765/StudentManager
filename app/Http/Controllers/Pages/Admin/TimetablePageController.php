<?php

namespace App\Http\Controllers\Pages\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Trang quản trị xếp và công bố thời khóa biểu (#19). */
class TimetablePageController extends Controller
{
    public function __construct(private readonly AccessControl $access) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        return view('admin.timetable.index', ['pageData' => [
            'api' => [
                'terms' => route('admin.academic-years.index'),
                'sections' => route('admin.course-sections.index'),
                'rooms' => route('admin.rooms.index'),
                'periods' => route('admin.periods.index'),
                'schedule' => route('admin.course-sections.schedule.index', ['courseSection' => '__ID__']),
                'scheduleStore' => route('admin.course-sections.schedule.store', ['courseSection' => '__ID__']),
                'scheduleUpdate' => route('admin.schedule-rules.update', ['scheduleRule' => '__ID__']),
                'scheduleDelete' => route('admin.schedule-rules.destroy', ['scheduleRule' => '__ID__']),
                'conflicts' => route('admin.course-sections.schedule.conflicts', ['courseSection' => '__ID__']),
                'timetableStatus' => route('admin.terms.timetable.status', ['term' => '__TERM__']),
                'timetablePublish' => route('admin.terms.timetable.publish', ['term' => '__TERM__']),
                'timetableLock' => route('admin.terms.timetable.lock', ['term' => '__TERM__']),
                'generateSessions' => route('admin.course-sections.sessions.generate', ['courseSection' => '__ID__']),
            ],
            'can' => [
                'create' => $this->access->allowsGlobally($user, 'TTB', PermissionAction::Create),
                'update' => $this->access->allowsGlobally($user, 'TTB', PermissionAction::Update),
                'delete' => $this->access->allowsGlobally($user, 'TTB', PermissionAction::Delete),
                'approve' => $this->access->allowsGlobally($user, 'TTB', PermissionAction::Approve),
            ],
        ]]);
    }
}
