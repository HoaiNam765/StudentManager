{{--
    BẢN TẠM của nhóm BE (issue #72) để đăng nhập chạy được. Nhóm FE thay bằng thiết kế ở issue #2.
    Giữ nguyên: POST route('login.store'), các trường `login`, `password`, `remember`, @csrf,
    lỗi hiển thị ở $errors->first('login'), thông báo ở session('status').
--}}
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập — {{ config('app.name') }}</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #f4f6f8; margin: 0; display: grid; place-items: center; min-height: 100vh; }
        main { background: #fff; padding: 2rem; border-radius: 8px; width: min(92vw, 380px); box-shadow: 0 1px 4px rgba(0,0,0,.1); }
        label { display: block; margin-top: 1rem; font-weight: 600; }
        input[type=text], input[type=password] { width: 100%; box-sizing: border-box; padding: .6rem; margin-top: .3rem; border: 1px solid #999; border-radius: 4px; }
        button { margin-top: 1.25rem; width: 100%; padding: .7rem; background: #1d4ed8; color: #fff; border: 0; border-radius: 4px; font-size: 1rem; }
        .error { color: #b91c1c; margin-top: 1rem; }
        .status { color: #065f46; }
    </style>
</head>
<body>
<main>
    <h1>Đăng nhập</h1>

    @if (session('status'))
        <p class="status" role="status">{{ session('status') }}</p>
    @endif

    @if ($errors->any())
        <p class="error" role="alert">{{ $errors->first('login') ?: $errors->first() }}</p>
    @endif

    <form method="POST" action="{{ route('login.store') }}">
        @csrf
        <label for="login">Tên đăng nhập hoặc email</label>
        <input id="login" name="login" type="text" value="{{ old('login') }}" autocomplete="username" required autofocus>

        <label for="password">Mật khẩu</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>

        <label><input type="checkbox" name="remember" value="1"> Ghi nhớ đăng nhập</label>

        <button type="submit">Đăng nhập</button>
    </form>
</main>
</body>
</html>
