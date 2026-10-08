<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Http\Requests\ResetPasswordRequest;
use App\Modules\Auth\Services\PasswordResetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Quên mật khẩu và đặt lại mật khẩu bằng liên kết (FR-AUTH-003). Trang `auth.forgot-password`, `auth.reset-password`
 * là bản tạm của BE; nhóm FE thay giao diện, giữ nguyên tên route và tên trường.
 */
class PasswordResetController extends Controller
{
    public const SENT_MESSAGE = 'Nếu thông tin trùng với một tài khoản, liên kết đặt lại mật khẩu đã được gửi tới email đăng ký. Liên kết dùng một lần và hết hạn sau :minutes phút.';

    public function __construct(private readonly PasswordResetService $resets) {}

    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /** Luôn trả cùng một thông báo, dù tài khoản có tồn tại hay không (BR-AUTH-09). */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate(['login' => ['required', 'string', 'max:255']], [], ['login' => 'tên đăng nhập hoặc email']);
        $this->resets->requestLink($data['login']);

        $message = str_replace(':minutes', (string) config('auth.passwords.users.expire'), self::SENT_MESSAGE);

        return $request->expectsJson()
            ? response()->json(['message' => $message])
            : back()->with('status', $message);
    }

    public function edit(Request $request, string $token): View
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->string('email')->toString()]);
    }

    public function update(ResetPasswordRequest $request): RedirectResponse|JsonResponse
    {
        $this->resets->reset($request->string('email')->toString(), $request->string('token')->toString(), $request->string('password')->toString());

        $message = 'Đã đặt lại mật khẩu. Hãy đăng nhập bằng mật khẩu mới; các phiên đăng nhập cũ đã bị đăng xuất.';

        return $request->expectsJson()
            ? response()->json(['message' => $message, 'redirect' => route('login')])
            : redirect()->route('login')->with('status', $message);
    }
}
