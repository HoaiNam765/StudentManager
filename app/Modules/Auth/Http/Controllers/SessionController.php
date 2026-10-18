<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Models\LoginHistory;
use App\Modules\Auth\Services\UserSessions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Trang "Phiên đăng nhập" của mọi người dùng (FR-AUTH-007, FR-AUTH-016): các phiên còn hiệu lực, thu hồi một phiên
 * hoặc mọi phiên khác, lịch sử đăng nhập của chính mình. Chỉ thao tác trên phiên của người đang đăng nhập (phạm vi OWN).
 */
class SessionController extends Controller
{
    /** Số lần đăng nhập gần nhất hiển thị cho người dùng. */
    private const HISTORY_LIMIT = 20;

    public function __construct(private readonly UserSessions $sessions) {}

    public function index(Request $request): View|JsonResponse
    {
        $user = $request->user();
        $sessions = $this->sessions->active($user, $request->session()->getId());
        $history = LoginHistory::query()
            ->where('user_id', $user->id)
            ->latest('id')
            ->limit(self::HISTORY_LIMIT)
            ->get(['id', 'successful', 'failure_reason', 'ip_address', 'device', 'device_type', 'unusual', 'created_at']);

        if ($request->expectsJson()) {
            return response()->json([
                'sessions' => $sessions->map(fn (array $session) => [
                    ...$session,
                    'last_activity_at' => $session['last_activity_at']->toIso8601String(),
                    'logged_in_at' => $session['logged_in_at']?->toIso8601String(),
                ]),
                'history' => $history,
            ]);
        }

        return view('account.sessions', compact('sessions', 'history'));
    }

    public function destroy(Request $request, string $handle): RedirectResponse|JsonResponse
    {
        $this->sessions->revoke($request->user(), $handle, $request->session()->getId());

        return $this->done($request, 'Đã thu hồi phiên đăng nhập.');
    }

    public function destroyOthers(Request $request): RedirectResponse|JsonResponse
    {
        $count = $this->sessions->revokeOthers($request->user(), $request->session()->getId());

        return $this->done($request, $count > 0 ? "Đã đăng xuất khỏi {$count} thiết bị khác." : 'Không có thiết bị nào khác đang đăng nhập.');
    }

    private function done(Request $request, string $message): RedirectResponse|JsonResponse
    {
        return $request->expectsJson()
            ? response()->json(['message' => $message])
            : redirect()->route('account.sessions')->with('status', $message);
    }
}
