<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Http\Requests\ChangePasswordRequest;
use App\Modules\Auth\Services\PasswordService;
use App\Modules\Auth\Services\PortalResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/** Đổi mật khẩu (FR-AUTH-004). */
class PasswordController extends Controller
{
    public function __construct(
        private readonly PasswordService $passwords,
        private readonly PortalResolver $portals,
    ) {}

    public function edit(): View
    {
        return view('auth.change-password');
    }

    public function update(ChangePasswordRequest $request): RedirectResponse|JsonResponse
    {
        $user = $request->user();

        $this->passwords->change(
            $user,
            $request->string('current_password')->toString(),
            $request->string('password')->toString(),
            $request->session()->getId(),
        );

        $home = route($this->portals->homeRouteFor($user) ?? 'login');

        return $request->expectsJson()
            ? response()->json(['message' => 'Đổi mật khẩu thành công.', 'redirect' => $home])
            : redirect($home)->with('status', 'Đổi mật khẩu thành công.');
    }
}
