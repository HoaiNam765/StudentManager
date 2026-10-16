<?php

namespace App\Modules\Teacher\Services;

use App\Models\User;
use App\Modules\Auth\Enums\ProfileType;
use App\Modules\Auth\Models\Role;
use App\Modules\Auth\Services\RoleService;
use App\Modules\Auth\Services\UserService;
use App\Modules\Faculty\Models\Department;
use App\Modules\Faculty\Models\Faculty;
use App\Modules\Faculty\Models\LeadershipTerm;
use App\Modules\Teacher\Contracts\TeachingAssignments;
use App\Modules\Teacher\Enums\AcademicTitle;
use App\Modules\Teacher\Enums\LecturerStatus;
use App\Modules\Teacher\Models\Lecturer;
use App\Modules\Teacher\Models\LecturerUnit;
use App\Support\Audit\AuditLogger;
use App\Support\Services\BaseService;
use Carbon\CarbonImmutable;

/**
 * Hồ sơ giảng viên (FR-TCH-001, 003; BR-TCH-01, 02; BR-FAC-03).
 *
 * - Mã giảng viên duy nhất, không cấp lại (kể cả đã xóa); mỗi hồ sơ gắn tối đa một tài khoản. Tạo hồ sơ thì tạo luôn
 *   tài khoản (tên đăng nhập = mã giảng viên) với vai trò LEC, hoặc gắn vào một tài khoản có sẵn (BR-TCH-01).
 * - Đúng một đơn vị chính là bộ môn đang hoạt động; kiêm nhiệm thêm bộ môn hoặc khoa khác, có ngày hiệu lực (BR-FAC-03).
 * - Nghỉ việc, nghỉ hưu: giữ lịch sử; không ghi nhận được khi còn lớp đang dạy hoặc còn chức vụ quản lý, liệt kê
 *   những việc cần chuyển; kiêm nhiệm tự kết thúc vào ngày nghỉ (BR-TCH-02).
 */
class LecturerService extends BaseService
{
    public function __construct(
        private readonly UserService $users,
        private readonly RoleService $roles,
        private readonly AuditLogger $audit,
        private readonly TeachingAssignments $teaching,
    ) {}

    /**
     * Tạo hồ sơ. `account`: "new" (mặc định, cần email) tạo tài khoản mới; "link" gắn vào tài khoản `user_id`;
     * "none" chưa cấp tài khoản.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, ?User $actor = null): Lecturer
    {
        $account = $data['account'] ?? 'new';
        unset($data['account']);

        $this->assertDepartmentActive(Department::query()->findOrFail($data['department_id']));
        $this->assertCodeFree($data['code']);
        $this->assertNationalIdFree($data['national_id'] ?? null);

        if ($account === 'new' && empty($data['email'])) {
            $this->fail('Cần email để cấp tài khoản cho giảng viên.', 'Nhập email công vụ (mật khẩu tạm được gửi tới email này), hoặc chọn "chưa cấp tài khoản".');
        }

        $userId = $data['user_id'] ?? null;
        unset($data['user_id']);

        if ($account === 'link' && $userId === null) {
            $this->fail('Chưa chọn tài khoản để gắn với hồ sơ.', 'Chọn tài khoản có sẵn, hoặc chọn tạo tài khoản mới.');
        }

        return $this->transaction(function () use ($data, $account, $userId, $actor): Lecturer {
            // Gán sẵn mặc định để model trả về có đủ giá trị (không đọc lại mặc định của CSDL)
            $lecturer = Lecturer::create($data + [
                'academic_title' => AcademicTitle::Lecturer->value,
                'status' => LecturerStatus::Working->value,
            ]);

            match ($account) {
                'new' => $this->createAccount($lecturer, $actor),
                'link' => $this->linkAccount($lecturer, User::query()->findOrFail($userId)),
                default => null,
            };

            return $lecturer->refresh();
        });
    }

    /** Cấp tài khoản cho hồ sơ chưa có: tên đăng nhập = mã giảng viên, vai trò LEC, mật khẩu tạm gửi qua email. */
    public function createAccount(Lecturer $lecturer, ?User $actor = null, ?string $email = null): Lecturer
    {
        $this->assertNoAccount($lecturer);
        $email ??= $lecturer->email;

        if (empty($email)) {
            $this->fail("Giảng viên {$lecturer->code} chưa có email.", 'Cập nhật email công vụ cho hồ sơ trước khi cấp tài khoản.');
        }

        return $this->transaction(function () use ($lecturer, $actor, $email): Lecturer {
            $user = $this->users->create([
                'username' => $lecturer->code,
                'name' => $lecturer->full_name,
                'email' => $email,
                'profile_type' => ProfileType::Teacher->value,
                'profile_id' => $lecturer->id,
            ], $actor);

            $lecturer->update(['user_id' => $user->id]);

            return $lecturer->refresh();
        });
    }

    /** Gắn hồ sơ vào tài khoản có sẵn (ví dụ cán bộ được tuyển làm giảng viên) và bổ sung vai trò LEC nếu chưa có. */
    public function linkAccount(Lecturer $lecturer, User $user): Lecturer
    {
        $this->assertNoAccount($lecturer);

        if (Lecturer::query()->where('user_id', $user->id)->whereKeyNot($lecturer->id)->exists()) {
            $this->fail("Tài khoản {$user->username} đã gắn với một hồ sơ giảng viên khác.", 'Mỗi tài khoản chỉ gắn một hồ sơ (BR-TCH-01); chọn tài khoản khác.');
        }

        return $this->transaction(function () use ($lecturer, $user): Lecturer {
            $this->users->linkProfile($user, ProfileType::Teacher, $lecturer->id);
            $lecturer->update(['user_id' => $user->id]);

            if (! $user->hasRole('LEC')) {
                $this->roles->assign($user, Role::query()->where('code', 'LEC')->firstOrFail());
            }

            return $lecturer->refresh();
        });
    }

    /**
     * Sửa hồ sơ (cán bộ quản lý). Đổi tình trạng công tác dùng `changeStatus()`; giảng viên tự sửa dùng
     * `LecturerProfileService` (BR-TCH-07).
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Lecturer $lecturer, array $data): Lecturer
    {
        if (isset($data['code']) && $data['code'] !== $lecturer->code) {
            $this->fail('Không đổi được mã giảng viên.', 'Mã giảng viên là tên đăng nhập và khóa tra cứu của lịch sử giảng dạy (BR-TCH-01); sửa các thông tin khác thì được.');
        }

        if (isset($data['national_id']) && $data['national_id'] !== $lecturer->national_id) {
            $this->assertNationalIdFree($data['national_id'], $lecturer);
        }

        if (isset($data['department_id']) && (int) $data['department_id'] !== $lecturer->department_id) {
            $department = Department::query()->findOrFail($data['department_id']);
            $this->assertDepartmentActive($department);

            $concurrent = $lecturer->units()->effectiveOn($this->today())->where('unit_type', 'department')->where('unit_id', $department->id)->exists();

            if ($concurrent) {
                $this->fail(
                    "Giảng viên {$lecturer->code} đang kiêm nhiệm ở bộ môn {$department->code}.",
                    'Kết thúc kiêm nhiệm ở bộ môn này trước, rồi chuyển đơn vị chính (mỗi giảng viên đúng một đơn vị chính, BR-FAC-03).'
                );
            }
        }

        $this->transaction(function () use ($lecturer, $data): void {
            $lecturer->update($data);
            $this->syncAccountName($lecturer);
        });

        return $lecturer->refresh();
    }

    /**
     * Ghi nhận nghỉ việc, nghỉ hưu hoặc làm việc trở lại (BR-TCH-02).
     */
    public function changeStatus(Lecturer $lecturer, LecturerStatus $status, ?string $on = null, ?string $reason = null): Lecturer
    {
        if ($status === $lecturer->status) {
            $this->fail("Giảng viên {$lecturer->code} đang ở tình trạng {$status->label()}.", 'Chọn tình trạng khác với tình trạng hiện tại.');
        }

        if ($status->isActive()) {
            return $this->audit->withReason($reason ?? 'Làm việc trở lại', function () use ($lecturer, $status): Lecturer {
                $lecturer->update(['status' => $status, 'left_on' => null, 'left_reason' => null]);

                return $lecturer->refresh();
            });
        }

        if (empty($reason)) {
            $this->fail('Cần ghi lý do khi ghi nhận '.mb_strtolower($status->label()).'.', 'Nhập lý do hoặc số quyết định.');
        }

        $on = CarbonImmutable::parse($on ?? $this->today())->toDateString();
        $blocking = $this->workToHandOver($lecturer, $on);

        if ($blocking !== []) {
            $this->fail(
                "Chưa ghi nhận {$status->label()} cho giảng viên {$lecturer->code} được vì còn việc phải chuyển cho người khác: ".implode('; ', $blocking).'.',
                'Phân công giảng viên khác cho các lớp và kết thúc chức vụ quản lý trước, rồi ghi nhận lại (BR-TCH-02).'
            );
        }

        return $this->audit->withReason("{$status->label()}: {$reason}", function () use ($lecturer, $status, $on, $reason): Lecturer {
            return $this->transaction(function () use ($lecturer, $status, $on, $reason): Lecturer {
                // Kiêm nhiệm kết thúc vào ngày nghỉ; lịch sử giữ nguyên
                $lecturer->units()->overlapping($on, null)->get()->each(function (LecturerUnit $unit) use ($on): void {
                    $unit->starts_on->toDateString() > $on
                        ? $unit->delete()
                        : $unit->update(['ends_on' => $on]);
                });

                $lecturer->update(['status' => $status, 'left_on' => $on, 'left_reason' => $reason]);

                return $lecturer->refresh();
            });
        });
    }

    /**
     * Việc phải chuyển trước khi giảng viên nghỉ: lớp đang dạy (CLS) và chức vụ quản lý còn hiệu lực sau ngày nghỉ.
     *
     * @return list<string>
     */
    public function workToHandOver(Lecturer $lecturer, string $on): array
    {
        $items = [];
        $sections = $this->teaching->activeSections($lecturer);

        if ($sections !== []) {
            $items[] = count($sections).' lớp đang dạy ('.implode(', ', $sections).')';
        }

        if ($lecturer->user_id !== null) {
            $terms = LeadershipTerm::query()
                ->where('user_id', $lecturer->user_id)
                ->overlapping(CarbonImmutable::parse($on)->addDay()->toDateString(), null)
                ->get();

            foreach ($terms as $term) {
                $items[] = "chức vụ {$term->position->label()} {$term->unit()?->code}";
            }
        }

        return $items;
    }

    // ---------------------------------------------------------------------
    // Kiêm nhiệm (FR-TCH-003)
    // ---------------------------------------------------------------------

    /** @param  array{unit_type: string, unit_id: int, starts_on?: ?string, ends_on?: ?string, note?: ?string}  $data */
    public function addUnit(Lecturer $lecturer, array $data): LecturerUnit
    {
        if (! $lecturer->isAssignable()) {
            $this->fail("Giảng viên {$lecturer->code} đang {$lecturer->status_label}.", 'Chỉ giao kiêm nhiệm cho giảng viên đang làm việc (BR-TCH-02).');
        }

        $startsOn = CarbonImmutable::parse($data['starts_on'] ?? $this->today())->toDateString();
        $endsOn = isset($data['ends_on']) ? CarbonImmutable::parse($data['ends_on'])->toDateString() : null;

        if ($endsOn !== null && $endsOn < $startsOn) {
            $this->fail('Ngày kết thúc kiêm nhiệm phải sau hoặc bằng ngày bắt đầu.', 'Chọn lại ngày kết thúc, hoặc để trống nếu chưa xác định.');
        }

        $unit = $data['unit_type'] === 'faculty'
            ? Faculty::query()->findOrFail($data['unit_id'])
            : Department::query()->findOrFail($data['unit_id']);

        if (! $unit->isActive()) {
            $this->fail("Đơn vị {$unit->code} đang ngừng hoạt động.", 'Chọn đơn vị đang hoạt động.');
        }

        $main = $lecturer->department;

        if (($unit instanceof Department && $unit->is($main)) || ($unit instanceof Faculty && $unit->id === $main->faculty_id)) {
            $this->fail(
                "{$unit->code} là đơn vị chính (hoặc khoa của đơn vị chính) của giảng viên {$lecturer->code}.",
                'Kiêm nhiệm chỉ dành cho đơn vị khác với đơn vị chính (BR-FAC-03).'
            );
        }

        $duplicate = $lecturer->units()
            ->where('unit_type', $data['unit_type'])
            ->where('unit_id', $unit->id)
            ->overlapping($startsOn, $endsOn)
            ->exists();

        if ($duplicate) {
            $this->fail("Giảng viên {$lecturer->code} đã kiêm nhiệm ở {$unit->code} trong khoảng thời gian này.", 'Sửa ngày của lần kiêm nhiệm đã có thay vì tạo mới.');
        }

        return LecturerUnit::create([
            'lecturer_id' => $lecturer->id,
            'unit_type' => $data['unit_type'],
            'unit_id' => $unit->id,
            'starts_on' => $startsOn,
            'ends_on' => $endsOn,
            'note' => $data['note'] ?? null,
        ]);
    }

    /** Kết thúc kiêm nhiệm (giữ lịch sử). */
    public function endUnit(LecturerUnit $unit, string $endsOn): LecturerUnit
    {
        $endsOn = CarbonImmutable::parse($endsOn)->toDateString();

        if ($endsOn < $unit->starts_on->toDateString()) {
            $this->fail('Ngày kết thúc phải sau hoặc bằng ngày bắt đầu kiêm nhiệm.', 'Kiêm nhiệm chưa bắt đầu thì hủy thay vì kết thúc.');
        }

        $unit->update(['ends_on' => $endsOn]);

        return $unit->refresh();
    }

    /** Hủy kiêm nhiệm chưa bắt đầu (giao nhầm). */
    public function cancelUnit(LecturerUnit $unit): void
    {
        if ($unit->starts_on->toDateString() <= $this->today()) {
            $this->fail('Kiêm nhiệm đã bắt đầu nên không hủy được.', 'Đặt ngày kết thúc kiêm nhiệm; lịch sử được giữ nguyên.');
        }

        $unit->delete();
    }

    // ---------------------------------------------------------------------

    /** Họ tên trên tài khoản đi theo hồ sơ. */
    public function syncAccountName(Lecturer $lecturer): void
    {
        $user = $lecturer->user;

        if ($user !== null && $user->name !== $lecturer->full_name) {
            $this->users->update($user, ['name' => $lecturer->full_name]);
        }
    }

    private function assertNoAccount(Lecturer $lecturer): void
    {
        if ($lecturer->user_id !== null) {
            $this->fail("Giảng viên {$lecturer->code} đã có tài khoản.", 'Mỗi hồ sơ gắn tối đa một tài khoản (BR-TCH-01).');
        }
    }

    private function assertDepartmentActive(Department $department): void
    {
        if (! $department->isActive()) {
            $this->fail("Bộ môn {$department->code} đang ngừng hoạt động.", 'Chọn bộ môn đang hoạt động làm đơn vị chính.');
        }
    }

    private function assertCodeFree(string $code): void
    {
        if (Lecturer::withTrashed()->where('code', $code)->exists()) {
            $this->fail("Mã giảng viên {$code} đã được dùng.", 'Mã giảng viên không được cấp lại, kể cả khi hồ sơ cũ đã xóa; hãy chọn mã khác (BR-TCH-01).');
        }
    }

    private function assertNationalIdFree(?string $nationalId, ?Lecturer $except = null): void
    {
        if ($nationalId === null) {
            return;
        }

        $taken = Lecturer::withTrashed()
            ->where('national_id', $nationalId)
            ->when($except !== null, fn ($q) => $q->whereKeyNot($except->id))
            ->value('code');

        if ($taken !== null) {
            $this->fail("Số CCCD này đã có trong hồ sơ giảng viên {$taken}.", 'Kiểm tra lại số CCCD; mỗi người chỉ có một hồ sơ giảng viên.');
        }
    }

    private function today(): string
    {
        return now(config('studentmanager.display_timezone'))->toDateString();
    }
}
