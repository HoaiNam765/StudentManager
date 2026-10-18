<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Models\LoginHistory;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API tra cứu lịch sử đăng nhập toàn trường (FR-AUTH-016). Quyền `AUTH.view` phạm vi toàn trường ở routes/admin.php
 * (mặc định ADMIN). Lọc: user_id, login (chuỗi đã nhập), successful (0/1), unusual (1), ip, from, to (Y-m-d).
 */
class LoginHistoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'user_id' => ['sometimes', 'integer'],
            'login' => ['sometimes', 'string', 'max:100'],
            'successful' => ['sometimes', 'boolean'],
            'unusual' => ['sometimes', 'boolean'],
            'ip' => ['sometimes', 'string', 'max:45'],
            'from' => ['sometimes', 'date_format:Y-m-d'],
            'to' => ['sometimes', 'date_format:Y-m-d', 'after_or_equal:from'],
        ], [], ['from' => 'từ ngày', 'to' => 'đến ngày']);

        $histories = LoginHistory::query()
            ->with('user:id,username,name')
            ->when(isset($filters['user_id']), fn ($q) => $q->where('user_id', $filters['user_id']))
            ->when(isset($filters['login']), fn ($q) => $q->where('login', 'like', '%'.$filters['login'].'%'))
            ->when(isset($filters['successful']), fn ($q) => $q->where('successful', $request->boolean('successful')))
            ->when($request->boolean('unusual'), fn ($q) => $q->where('unusual', true))
            ->when(isset($filters['ip']), fn ($q) => $q->where('ip_address', $filters['ip']))
            ->when(isset($filters['from']), fn ($q) => $q->where('created_at', '>=', $this->localDay($filters['from'])->utc()))
            ->when(isset($filters['to']), fn ($q) => $q->where('created_at', '<', $this->localDay($filters['to'])->addDay()->utc()))
            ->latest('id')
            ->paginate(50);

        return response()->json($histories);
    }

    /** Ngày người dùng chọn theo giờ hiển thị (UTC+7); CSDL lưu UTC. */
    private function localDay(string $date): CarbonImmutable
    {
        return CarbonImmutable::createFromFormat('Y-m-d', $date, config('studentmanager.display_timezone'))->startOfDay();
    }
}
