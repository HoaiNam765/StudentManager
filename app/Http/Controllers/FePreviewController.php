<?php

namespace App\Http\Controllers;

use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\SchoolClass\Models\AdminClass;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

/** Only registered for local environment via routes/fe-preview.php. */
class FePreviewController extends Controller
{
    public function index(): View
    {
        abort_unless(App::environment('local'), 404);

        return view('fe_preview.index', ['screens' => config('fe_preview', [])]);
    }

    public function show(string $screen, Request $request, AccessControl $access): View
    {
        abort_unless(App::environment('local'), 404);

        $item = collect(config('fe_preview', []))->firstWhere('key', $screen);
        abort_if($item === null, 404);

        if ($screen !== 'admin/khoa-va-lop-hanh-chinh') {
            return view($item['view']);
        }

        $query = $access->constrain(AdminClass::query(), $request->user(), 'CLS', PermissionAction::View);
        $with = [
            'cohort:id,code,start_year',
            'major:id,code,name,faculty_id',
            'major.faculty:id,name',
            'advisorAssignments' => fn ($assignments) => $assignments->current()->with('lecturer:id,code,full_name'),
        ];
        $withCount = ['members as current_students_count' => fn ($members) => $members->current()];
        $visibleClasses = (clone $query)->with($with)->withCount($withCount)->orderBy('code')->get();
        $activeClasses = $visibleClasses->filter(fn (AdminClass $class) => $class->isActive());
        $assignedAdvisorCount = $activeClasses->filter(fn (AdminClass $class) => $class->advisorAssignments->isNotEmpty())->count();
        $currentStudentCount = $visibleClasses->sum('current_students_count');

        $classes = (clone $query)
            ->with($with)
            ->withCount($withCount)
            ->when($request->filled('q'), fn ($classes) => $classes->search($request->string('q')->toString()))
            ->when($request->filled('cohort_id'), fn ($classes) => $classes->where('cohort_id', $request->integer('cohort_id')))
            ->when($request->filled('major_id'), fn ($classes) => $classes->where('major_id', $request->integer('major_id')))
            ->when($request->filled('faculty_id'), fn ($classes) => $classes->whereHas('major', fn ($major) => $major->where('faculty_id', $request->integer('faculty_id'))))
            ->when(in_array($request->input('status'), ['active', 'inactive'], true), fn ($classes) => $classes->where('status', $request->input('status')))
            ->orderBy('code')
            ->paginate(25)
            ->withQueryString();

        return view($item['view'], [
            'classes' => $classes,
            'cohorts' => $visibleClasses->pluck('cohort')->unique('id')->sortByDesc('start_year')->values(),
            'majors' => $visibleClasses->pluck('major')->unique('id')->sortBy('name')->values(),
            'faculties' => $visibleClasses->pluck('major.faculty')->filter()->unique('id')->sortBy('name')->values(),
            'totalClasses' => $visibleClasses->count(),
            'totalStudents' => $currentStudentCount,
            'averageStudents' => $activeClasses->count() > 0 ? $activeClasses->sum('current_students_count') / $activeClasses->count() : 0,
            'advisorCoverage' => $activeClasses->count() > 0 ? round($assignedAdvisorCount / $activeClasses->count() * 100) : 0,
            'missingAdvisorCount' => $activeClasses->count() - $assignedAdvisorCount,
            'activeClassCount' => $activeClasses->count(),
            'canViewClasses' => $access->allows($request->user(), 'CLS', PermissionAction::View),
        ]);
    }
}
