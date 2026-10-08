<?php

namespace App\Modules\Faculty\Services;

use App\Models\User;
use App\Modules\Auth\Models\Role;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Faculty\Contracts\LeaderEligibility;
use App\Modules\Faculty\Enums\LeadershipPosition;
use App\Modules\Faculty\Models\Department;
use App\Modules\Faculty\Models\Faculty;
use App\Modules\Faculty\Models\LeadershipTerm;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLogger;
use App\Support\Services\BaseService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

/**
 * Lãnh đạo đơn vị theo nhiệm kỳ và đồng bộ vai trò DEAN (FR-FAC-006; BR-FAC-04, 06).
 *
 * - Mỗi nhiệm kỳ sinh một dòng vai trò DEAN (user_roles) có cùng ngày hiệu lực: tới ngày bắt đầu, người mới tự có
 *   quyền DEAN và phạm vi FACULTY của đơn vị (qua FacultyAccess); hết nhiệm kỳ thì tự mất. Không cần sửa tay vai trò.
 * - Mỗi đơn vị tối đa một trưởng khoa / trưởng bộ môn tại một thời điểm (BR-FAC-04); phó thì nhiều người được.
 * - Thay trưởng từ ngày D: `assign([... 'replace_current' => true])` đóng nhiệm kỳ người cũ vào ngày D - 1.
 * - Lịch sử nhiệm kỳ giữ nguyên (GC-06): kết thúc bằng ngày kết thúc; chỉ hủy được nhiệm kỳ chưa bắt đầu.
 * - Lệnh `faculty:sync-leadership` chạy hằng ngày: sửa dòng vai trò lệch với nhiệm kỳ và ghi nhật ký nhiệm kỳ
 *   bắt đầu / kết thúc hôm đó.
 */
class LeadershipService extends BaseService
{
    public function __construct(
        private readonly AuditLogger $audit,
        private readonly AccessControl $access,
        private readonly FacultyAccess $facultyAccess,
        private readonly LeaderEligibility $eligibility,
    ) {}

    /**
     * @param  array{unit_type: string, unit_id: int, user_id: int, position: string, starts_on: string, ends_on?: ?string, note?: ?string, replace_current?: bool}  $data
     */
    public function assign(array $data, ?User $actor = null): LeadershipTerm
    {
        $position = LeadershipPosition::from($data['position']);
        $startsOn = CarbonImmutable::parse($data['starts_on'])->toDateString();
        $endsOn = isset($data['ends_on']) ? CarbonImmutable::parse($data['ends_on'])->toDateString() : null;

        if ($position->unitType() !== $data['unit_type']) {
            $this->fail(
                "Chức vụ {$position->label()} không dùng cho ".($data['unit_type'] === 'faculty' ? 'khoa' : 'bộ môn').'.',
                'Trưởng/phó khoa gán cho khoa; trưởng/phó bộ môn gán cho bộ môn.'
            );
        }

        if ($endsOn !== null && $endsOn < $startsOn) {
            $this->fail('Ngày kết thúc nhiệm kỳ phải sau hoặc bằng ngày bắt đầu.', 'Chọn lại ngày kết thúc, hoặc để trống nếu chưa xác định.');
        }

        $user = User::query()->findOrFail($data['user_id']);
        $unit = $this->unit($data['unit_type'], (int) $data['unit_id']);

        if (! $unit->isActive()) {
            $this->fail("Đơn vị {$unit->code} đang ngừng hoạt động.", 'Chỉ giao chức vụ cho đơn vị đang hoạt động.');
        }

        $reason = $this->eligibility->reasonIneligible($user, $unit, $position);

        if ($reason !== null) {
            $this->fail($reason, 'Chọn giảng viên hoặc cán bộ đang hoạt động thuộc đơn vị (BR-FAC-04).');
        }

        $term = $this->transaction(function () use ($data, $position, $startsOn, $endsOn, $user, $unit, $actor): LeadershipTerm {
            // Khóa đơn vị để hai yêu cầu giao chức vụ trưởng cùng lúc không cùng lọt qua kiểm tra
            $unit::query()->whereKey($unit->id)->lockForUpdate()->first();

            $overlapping = LeadershipTerm::query()
                ->forUnit($data['unit_type'], $unit->id)
                ->where('position', $position->value)
                ->overlapping($startsOn, $endsOn)
                ->with('user')
                ->get();

            if ($overlapping->contains('user_id', $user->id)) {
                $this->fail("{$user->name} đã giữ chức vụ {$position->label()} của {$unit->code} trong khoảng thời gian này.", 'Sửa ngày của nhiệm kỳ đã có thay vì tạo nhiệm kỳ mới.');
            }

            if ($position->isHead() && $overlapping->isNotEmpty()) {
                if (empty($data['replace_current'])) {
                    $current = $overlapping->first();

                    $this->fail(
                        "{$unit->code} đã có {$position->label()} là {$current->user->name} (".$this->period($current).').',
                        "Mỗi đơn vị chỉ có một {$position->label()} tại một thời điểm (BR-FAC-04): kết thúc nhiệm kỳ hiện tại trước, hoặc chọn \"thay thế người đang giữ chức vụ\" để tự đóng nhiệm kỳ cũ vào ngày trước {$startsOn}."
                    );
                }

                foreach ($overlapping as $current) {
                    if ($current->starts_on->toDateString() >= $startsOn) {
                        $this->fail(
                            "Đã có nhiệm kỳ {$position->label()} của {$current->user->name} bắt đầu từ ".$current->starts_on->format('d/m/Y').'.',
                            'Hủy hoặc sửa nhiệm kỳ đã lên lịch đó trước khi giao người khác.'
                        );
                    }

                    $this->closeTerm($current, CarbonImmutable::parse($startsOn)->subDay()->toDateString(), $actor, 'Thay lãnh đạo đơn vị');
                }
            }

            $roleId = $this->grantDeanRole($user, $startsOn, $endsOn, $actor, "Nhiệm kỳ {$position->label()} {$unit->code}");

            return LeadershipTerm::create([
                'unit_type' => $data['unit_type'],
                'unit_id' => $unit->id,
                'user_id' => $user->id,
                'position' => $position,
                'starts_on' => $startsOn,
                'ends_on' => $endsOn,
                'user_role_id' => $roleId,
                'note' => $data['note'] ?? null,
            ]);
        });

        $this->flush();

        return $term->load('user');
    }

    /** Đặt (rút ngắn hoặc kéo dài) ngày kết thúc nhiệm kỳ; vai trò DEAN đi theo. */
    public function end(LeadershipTerm $term, string $endsOn, ?User $actor = null): LeadershipTerm
    {
        $endsOn = CarbonImmutable::parse($endsOn)->toDateString();

        if ($endsOn < $term->starts_on->toDateString()) {
            $this->fail('Ngày kết thúc phải sau hoặc bằng ngày bắt đầu nhiệm kỳ.', 'Nhiệm kỳ chưa bắt đầu thì dùng thao tác hủy nhiệm kỳ.');
        }

        $this->transaction(function () use ($term, $endsOn, $actor): void {
            if ($term->position->isHead()) {
                $clash = LeadershipTerm::query()
                    ->forUnit($term->unit_type, $term->unit_id)
                    ->where('position', $term->position->value)
                    ->whereKeyNot($term->id)
                    ->overlapping($term->starts_on->toDateString(), $endsOn)
                    ->with('user')
                    ->first();

                if ($clash !== null) {
                    $this->fail(
                        "Kéo dài tới {$endsOn} sẽ trùng nhiệm kỳ của {$clash->user->name} (".$this->period($clash).').',
                        'Chọn ngày kết thúc trước ngày bắt đầu của nhiệm kỳ sau (BR-FAC-04).'
                    );
                }
            }

            $this->closeTerm($term, $endsOn, $actor, 'Kết thúc nhiệm kỳ lãnh đạo đơn vị');
        });

        $this->flush();

        return $term->refresh()->load('user');
    }

    /** Hủy nhiệm kỳ chưa bắt đầu (giao nhầm người, đổi kế hoạch). */
    public function cancel(LeadershipTerm $term, ?User $actor = null): void
    {
        if ($term->starts_on->toDateString() <= $this->today()) {
            $this->fail('Nhiệm kỳ đã bắt đầu nên không hủy được.', 'Kết thúc nhiệm kỳ bằng cách đặt ngày kết thúc; lịch sử nhiệm kỳ được giữ nguyên.');
        }

        $this->transaction(function () use ($term, $actor): void {
            // Quy ước của RoleService: valid_to < valid_from là lần gán đã hủy
            $this->updateRoleRow($term, $term->starts_on->toDateString(), $term->starts_on->subDay()->toDateString(), $actor, 'Hủy nhiệm kỳ lãnh đạo đơn vị');
            $term->delete();
        });

        $this->flush();
    }

    /**
     * Đồng bộ hằng ngày: dòng vai trò DEAN khớp đúng ngày của từng nhiệm kỳ; ghi nhật ký nhiệm kỳ bắt đầu hôm nay
     * và kết thúc hôm qua (BR-FAC-06).
     *
     * @return array{fixed: int, started: int, ended: int}
     */
    public function sync(): array
    {
        $today = $this->today();
        $yesterday = CarbonImmutable::parse($today)->subDay()->toDateString();
        $result = ['fixed' => 0, 'started' => 0, 'ended' => 0];

        LeadershipTerm::query()->with('user')->chunkById(200, function ($terms) use ($today, $yesterday, &$result): void {
            foreach ($terms as $term) {
                $row = $term->user_role_id === null ? null : DB::table('user_roles')->find($term->user_role_id);
                $from = $term->starts_on->toDateString();
                $to = $term->ends_on?->toDateString();

                if ($row === null) {
                    $term->forceFill(['user_role_id' => $this->grantDeanRole($term->user, $from, $to, null, 'Đồng bộ nhiệm kỳ lãnh đạo đơn vị')])->save();
                    $result['fixed']++;
                } elseif ($row->valid_from !== $from || $row->valid_to !== $to) {
                    $this->updateRoleRow($term, $from, $to, null, 'Đồng bộ nhiệm kỳ lãnh đạo đơn vị');
                    $result['fixed']++;
                }

                if ($from === $today) {
                    $this->audit->record(AuditEvent::Updated, $term->user, [], ['DEAN' => $this->describe($term)], 'Nhiệm kỳ lãnh đạo đơn vị bắt đầu có hiệu lực');
                    $result['started']++;
                }

                if ($to === $yesterday) {
                    $this->audit->record(AuditEvent::Updated, $term->user, ['DEAN' => $this->describe($term)], [], 'Nhiệm kỳ lãnh đạo đơn vị đã kết thúc');
                    $result['ended']++;
                }
            }
        });

        $this->flush();

        return $result;
    }

    // ---------------------------------------------------------------------

    private function closeTerm(LeadershipTerm $term, string $endsOn, ?User $actor, string $reason): void
    {
        $term->update(['ends_on' => $endsOn]);
        $this->updateRoleRow($term, $term->starts_on->toDateString(), $endsOn, $actor, $reason);
    }

    private function grantDeanRole(User $user, string $from, ?string $to, ?User $actor, string $reason): int
    {
        $role = Role::query()->where('code', 'DEAN')->first();

        if ($role === null || ! $role->isActive()) {
            $this->fail('Vai trò DEAN chưa có hoặc đang ngừng.', 'Chạy AuthSeeder hoặc kích hoạt vai trò DEAN trước khi giao chức vụ lãnh đạo.');
        }

        // Ghi trực tiếp (không qua RoleService::assign) vì một người có thể giữ nhiều chức vụ cùng lúc:
        // mỗi nhiệm kỳ một dòng DEAN riêng, quyền là hợp các dòng đang hiệu lực
        $id = DB::table('user_roles')->insertGetId([
            'user_id' => $user->id,
            'role_id' => $role->id,
            'valid_from' => $from,
            'valid_to' => $to,
            'assigned_by' => $actor?->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->audit->record(AuditEvent::Updated, $user, [], ['assigned' => 'DEAN', 'valid_from' => $from, 'valid_to' => $to], $reason);

        return $id;
    }

    private function updateRoleRow(LeadershipTerm $term, string $from, ?string $to, ?User $actor, string $reason): void
    {
        if ($term->user_role_id === null) {
            return;
        }

        $before = DB::table('user_roles')->find($term->user_role_id);
        DB::table('user_roles')->where('id', $term->user_role_id)->update(['valid_from' => $from, 'valid_to' => $to, 'updated_at' => now()]);

        $this->audit->record(
            AuditEvent::Updated,
            $term->user,
            ['DEAN' => ['valid_from' => $before?->valid_from, 'valid_to' => $before?->valid_to]],
            ['DEAN' => ['valid_from' => $from, 'valid_to' => $to]],
            $reason.($actor === null ? ' (tự động)' : ''),
        );
    }

    private function unit(string $type, int $id): Faculty|Department
    {
        $model = LeadershipTerm::UNIT_TYPES[$type] ?? null;

        if ($model === null) {
            $this->fail("Loại đơn vị {$type} không hợp lệ.", 'Dùng faculty (khoa) hoặc department (bộ môn).');
        }

        return $model::query()->findOrFail($id);
    }

    private function period(LeadershipTerm $term): string
    {
        return 'từ '.$term->starts_on->format('d/m/Y').($term->ends_on === null ? ', chưa có ngày kết thúc' : ' đến '.$term->ends_on->format('d/m/Y'));
    }

    private function describe(LeadershipTerm $term): string
    {
        return $term->position->label().' '.($term->unit()?->code ?? '#'.$term->unit_id).' '.$this->period($term);
    }

    private function flush(): void
    {
        $this->access->flush();
        $this->facultyAccess->flush();
    }

    private function today(): string
    {
        return now(config('studentmanager.display_timezone'))->toDateString();
    }
}
