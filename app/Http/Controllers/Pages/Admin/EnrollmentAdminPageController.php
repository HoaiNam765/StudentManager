<?php

namespace App\Http\Controllers\Pages\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Trang quản trị đợt đăng ký và chốt đăng ký học phần (#20). */
class EnrollmentAdminPageController extends Controller
{
    public function __construct(private readonly AccessControl $access) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        return view('admin.enrollment.index', ['pageData' => [
            'api' => [
                'terms' => route('admin.academic-years.index'),
                'periods' => route('admin.registration-periods.index'),
                'periodShow' => route('admin.registration-periods.show', ['registrationPeriod' => '__ID__']),
                'periodStore' => route('admin.registration-periods.store'),
                'periodUpdate' => route('admin.registration-periods.update', ['registrationPeriod' => '__ID__']),
                'periodPublish' => route('admin.registration-periods.publish', ['registrationPeriod' => '__ID__']),
                'periodOpenEarly' => route('admin.registration-periods.open-early', ['registrationPeriod' => '__ID__']),
                'frameworks' => route('admin.training-frameworks.index'),
                'offerings' => route('admin.registration-periods.offerings.index', ['registrationPeriod' => '__ID__']),
                'offeringStore' => route('admin.registration-periods.offerings.store', ['registrationPeriod' => '__ID__']),
                'offeringUpdate' => route('admin.registration-periods.offerings.update', ['registrationPeriod' => '__ID__', 'courseSection' => '__SECTION__']),
                'offeringDelete' => route('admin.registration-periods.offerings.destroy', ['registrationPeriod' => '__ID__', 'courseSection' => '__SECTION__']),
                'offeringDeleteMany' => route('admin.registration-periods.offerings.destroy-many', ['registrationPeriod' => '__ID__']),
                'lecturerOptions' => route('admin.registration-periods.lecturers', ['registrationPeriod' => '__ID__']),
                'announce' => route('admin.registration-periods.announce', ['registrationPeriod' => '__ID__']),
                'frameworkSetTerm' => route('admin.registration-periods.frameworks.set-term', ['registrationPeriod' => '__ID__']),
                'frameworkDetach' => route('admin.registration-periods.frameworks.detach', ['registrationPeriod' => '__ID__', 'trainingFramework' => '__FW__']),
                'frameworkClasses' => route('admin.registration-periods.frameworks.classes', ['registrationPeriod' => '__ID__', 'trainingFramework' => '__FW__']),
                'frameworkLecturers' => route('admin.training-frameworks.lecturer-options', ['trainingFramework' => '__FW__']),
                'periodDelete' => route('admin.registration-periods.destroy', ['registrationPeriod' => '__ID__']),
                'stats' => route('admin.enrollment-stats'),
                'preview' => route('admin.terms.registration.finalize-preview', ['term' => '__TERM__']),
                'finalize' => route('admin.terms.registration.finalize', ['term' => '__TERM__']),
                'underMin' => route('admin.terms.under-min-sections', ['term' => '__TERM__']),
                'merge' => route('admin.course-sections.merge', ['courseSection' => '__ID__']),
                'sectionStatus' => route('admin.course-sections.status', ['courseSection' => '__ID__', 'status' => '__STATUS__']),
                'cohorts' => route('admin.cohorts.index'),
                'majors' => route('admin.units.index', ['unitType' => 'majors']),
                'adminClasses' => route('admin.admin-classes.index'),
            ],
            'can' => [
                'create' => $this->access->allowsGlobally($user, 'ENR', PermissionAction::Create),
                'update' => $this->access->allowsGlobally($user, 'ENR', PermissionAction::Update),
                'delete' => $this->access->allowsGlobally($user, 'ENR', PermissionAction::Delete),
                'approve' => $this->access->allowsGlobally($user, 'ENR', PermissionAction::Approve),
                'classUpdate' => $this->access->allowsGlobally($user, 'CLS', PermissionAction::Update),
                'offerings' => $this->access->allowsGlobally($user, 'CLS', PermissionAction::Create)
                    && $this->access->allowsGlobally($user, 'TTB', PermissionAction::Create)
                    && $this->access->allowsGlobally($user, 'ENR', PermissionAction::Update),
                'notify' => $this->access->allows($user, 'NOT', PermissionAction::Create),
            ],
        ]]);
    }
}
