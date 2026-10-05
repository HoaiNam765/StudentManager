<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Http\Requests\LoginRequest;
use App\Modules\Auth\Services\LoginService;
use App\Modules\Auth\Services\PasswordService;
use App\Modules\Auth\Services\PortalResolver;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/** Đăng nhập và đăng xuất (FR-AUTH-001, FR-AUTH-002). */
class LoginController extends Controller
{
    public function __construct(
        private readonly LoginService $login,
        private readonly PasswordService $passwords,
        private readonly PortalResolver $portals,
    ) {}

    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse|JsonResponse
    {
        $user = $this->login->attempt(
            $request->string('login')->toString(),
            $request->string('password')->toString(),
            $request->boolean('remember'),
        );

        $request->session()->regenerate();

        $target = $this->passwords->mustChange($user)
            ? route('password.change')
            : route($this->portals->homeRouteFor($user));

        return $request->expectsJson()
            ? response()->json(['redirect' => $target])
            : redirect()->intended($target);
    }

    public function destroy(Request $request, AuditLogger $audit): RedirectResponse|JsonResponse
    {
        $audit->record(AuditEvent::Logout, $request->user());

        // Hủy phiên ở máy chủ, không chỉ xóa cookie (FR-AUTH-002)
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $request->expectsJson()
            ? response()->json(['redirect' => route('login')])
            : redirect()->route('login')->with('status', 'Bạn đã đăng xuất.');
    }
}
