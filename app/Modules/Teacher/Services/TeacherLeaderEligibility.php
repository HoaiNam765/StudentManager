<?php

namespace App\Modules\Teacher\Services;

use App\Models\User;
use App\Modules\Auth\Enums\ProfileType;
use App\Modules\Faculty\Contracts\LeaderEligibility;
use App\Modules\Faculty\Enums\LeadershipPosition;
use App\Modules\Faculty\Models\Department;
use App\Modules\Faculty\Models\Faculty;
use App\Modules\Faculty\Models\LeadershipTerm;
use App\Modules\Teacher\Models\Lecturer;

/**
 * Điều kiện giao chức vụ lãnh đạo đơn vị dựa trên hồ sơ giảng viên.
 *
 * - BR-FAC-04: giảng viên phải đang làm việc và thuộc đơn vị (đơn vị chính hoặc kiêm nhiệm có hiệu lực vào ngày bắt đầu;
 *   với khoa thì tính cả các bộ môn của khoa). Ngoại lệ "ngoài đơn vị" bật bằng `studentmanager.faculty.allow_leader_outside_unit`.
 *   Tài khoản cán bộ chưa có hồ sơ nhân sự trong hệ thống nên chưa kiểm tra đơn vị; tài khoản sinh viên không được giao.
 * - BR-TCH-06: mỗi người chỉ giữ chức vụ trưởng (trưởng khoa, trưởng bộ môn) ở một đơn vị tại một thời điểm.
 */
class TeacherLeaderEligibility implements LeaderEligibility
{
    public function reasonIneligible(User $user, Faculty|Department $unit, LeadershipPosition $position, ?string $startsOn = null, ?string $endsOn = null): ?string
    {
        $startsOn ??= now(config('studentmanager.display_timezone'))->toDateString();
        $lecturer = Lecturer::query()->where('user_id', $user->id)->with('department')->first();

        if ($lecturer !== null) {
            if (! $lecturer->isAssignable()) {
                return "Giảng viên {$lecturer->code} đang ở tình trạng {$lecturer->status_label}.";
            }

            if (! config('studentmanager.faculty.allow_leader_outside_unit') && ! $this->belongsTo($lecturer, $unit, $startsOn)) {
                return "Giảng viên {$lecturer->code} không thuộc {$unit->code} (đơn vị chính hoặc kiêm nhiệm) vào ngày {$startsOn}.";
            }
        } elseif ($user->profile_type === ProfileType::Student) {
            return "Tài khoản {$user->username} là tài khoản sinh viên.";
        }

        if ($position->isHead()) {
            $other = LeadershipTerm::query()
                ->where('user_id', $user->id)
                ->whereIn('position', [LeadershipPosition::Dean->value, LeadershipPosition::Head->value])
                ->where(fn ($q) => $q->where('unit_type', '!=', $position->unitType())->orWhere('unit_id', '!=', $unit->id))
                ->overlapping($startsOn, $endsOn)
                ->first();

            if ($other !== null) {
                return "{$user->name} đang giữ chức vụ {$other->position->label()} {$other->unit()?->code}; mỗi người chỉ giữ chức vụ trưởng ở một đơn vị tại một thời điểm (BR-TCH-06).";
            }
        }

        return null;
    }

    private function belongsTo(Lecturer $lecturer, Faculty|Department $unit, string $day): bool
    {
        if ($unit instanceof Department) {
            return $lecturer->department_id === $unit->id
                || $lecturer->units()->effectiveOn($day)->where('unit_type', 'department')->where('unit_id', $unit->id)->exists();
        }

        if ($lecturer->department?->faculty_id === $unit->id) {
            return true;
        }

        return $lecturer->units()->effectiveOn($day)
            ->where(fn ($q) => $q
                ->where(fn ($f) => $f->where('unit_type', 'faculty')->where('unit_id', $unit->id))
                ->orWhere(fn ($d) => $d->where('unit_type', 'department')->whereIn('unit_id', $unit->departments()->pluck('id'))))
            ->exists();
    }
}
