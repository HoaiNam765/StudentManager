{{--
    BẢN TẠM của nhóm BE (issue #72). Nhóm FE thay bằng thiết kế ở issue #6 (Cài đặt tài khoản).
    Giữ nguyên: PUT route('password.update'), các trường `current_password`, `password`,
    `password_confirmation`, @csrf, @method('PUT').
--}}
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đổi mật khẩu — {{ config('app.name') }}</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #f4f6f8; margin: 0; display: grid; place-items: center; min-height: 100vh; }
        main { background: #fff; padding: 2rem; border-radius: 8px; width: min(92vw, 420px); box-shadow: 0 1px 4px rgba(0,0,0,.1); }
        label { display: block; margin-top: 1rem; font-weight: 600; }
        input { width: 100%; box-sizing: border-box; padding: .6rem; margin-top: .3rem; border: 1px solid #999; border-radius: 4px; }
        button { margin-top: 1.25rem; width: 100%; padding: .7rem; background: #1d4ed8; color: #fff; border: 0; border-radius: 4px; font-size: 1rem; }
        .error { color: #b91c1c; font-size: .9rem; margin: .3rem 0 0; }
        .status { color: #92400e; }
    </style>
</head>
<body>
<main>
    <h1>Đổi mật khẩu</h1>

    @if (session('status'))
        <p class="status" role="status">{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        @method('PUT')

        <label for="current_password">Mật khẩu hiện tại</label>
        <input id="current_password" name="current_password" type="password" autocomplete="current-password" required>
        @error('current_password') <p class="error" role="alert">{{ $message }}</p> @enderror

        <label for="password">Mật khẩu mới</label>
        <input id="password" name="password" type="password" autocomplete="new-password" required>
        @error('password') <p class="error" role="alert">{{ $message }}</p> @enderror

        <label for="password_confirmation">Nhập lại mật khẩu mới</label>
        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>

        <button type="submit">Đổi mật khẩu</button>
    </form>
</main>
</body>
</html>
