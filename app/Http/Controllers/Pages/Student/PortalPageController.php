<?php

namespace App\Http\Controllers\Pages\Student;

use App\Http\Controllers\Controller;
use App\Modules\Faculty\Models\Major;
use App\Modules\Report\Services\StudentPortalData;
use App\Modules\Student\Http\Controllers\StudentSelfController;
use App\Modules\Student\Models\StudentChangeRequest;
use App\Modules\Student\Models\StudentContact;
use App\Modules\Timetable\Http\Controllers\TimetableController;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortalPageController extends Controller
{
    public function __construct(private readonly StudentPortalData $data) {}

    public function curriculum(Request $request): View
    {
        return view('student.curriculum', $this->data->for($request->user()));
    }

    public function timetable(Request $request, TimetableController $timetable): View
    {
        $schedule = $timetable->mineStudent($request)->getData(true);
        $from = CarbonImmutable::parse($schedule['from'])->startOfWeek();
        $to = CarbonImmutable::parse($schedule['to']);

        return view('student.timetable', $this->data->for($request->user()) + [
            'schedule' => $schedule,
            'weeks' => collect(range(0, (int) ceil(($from->diffInDays($to) + 1) / 7) - 1))
                ->map(fn ($week) => $from->addWeeks($week)),
            'previous' => $from->subWeek()->toDateString(),
            'next' => $from->addWeek()->toDateString(),
        ]);
    }

    public function profile(Request $request, StudentSelfController $self): View
    {
        $details = $self->show($request)->getData(true);

        return view('student.profile', $this->data->for($request->user()) + [
            'details' => $details,
            'majors' => Major::query()->orderBy('name')->get(['id', 'code', 'name']),
        ]);
    }

    public function updateProfile(Request $request, StudentSelfController $self): RedirectResponse
    {
        if (! $request->filled('permanent_address')) {
            $request->request->remove('permanent_address');
        }
        $self->update($request);

        return back()->with('status', 'Đã cập nhật thông tin liên hệ.');
    }

    public function photo(Request $request, StudentSelfController $self): RedirectResponse
    {
        $self->photo($request);

        return back()->with('status', 'Đã cập nhật ảnh.');
    }

    public function requestChange(Request $request, StudentSelfController $self): RedirectResponse
    {
        foreach (['full_name', 'date_of_birth', 'national_id', 'major_id'] as $field) {
            if (! $request->filled($field)) {
                $request->request->remove($field);
            }
        }
        $self->storeRequest($request);

        return back()->with('status', 'Đã gửi yêu cầu chỉnh sửa để xét duyệt.');
    }

    public function cancelChange(Request $request, StudentChangeRequest $studentChangeRequest, StudentSelfController $self): RedirectResponse
    {
        $self->cancelRequest($request, $studentChangeRequest);

        return back()->with('status', 'Đã hủy yêu cầu chỉnh sửa.');
    }

    public function storeContact(Request $request, StudentSelfController $self): RedirectResponse
    {
        $self->storeContact($request);

        return back()->with('status', 'Đã thêm người liên hệ.');
    }

    public function updateContact(Request $request, StudentContact $studentContact, StudentSelfController $self): RedirectResponse
    {
        $self->updateContact($request, $studentContact);

        return back()->with('status', 'Đã cập nhật người liên hệ.');
    }

    public function destroyContact(Request $request, StudentContact $studentContact, StudentSelfController $self): RedirectResponse
    {
        $self->destroyContact($request, $studentContact);

        return back()->with('status', 'Đã xóa người liên hệ.');
    }
}
