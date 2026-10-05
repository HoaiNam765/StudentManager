{{--
    BẢN TẠM của nhóm BE (issue #72): trang chủ tạm của 3 cổng để kiểm tra đăng nhập và chuyển cổng theo vai trò.
    Nhóm FE thay bằng dashboard thật (các issue trang chủ của từng cổng).
--}}
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $portal }} — {{ config('app.name') }}</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 2rem; }
        .status { color: #065f46; }
        form { display: inline; }
    </style>
</head>
<body>
    <h1>{{ $portal }}</h1>

    @if (session('status'))
        <p class="status" role="status">{{ session('status') }}</p>
    @endif

    <p>Xin chào {{ auth()->user()->name }}. Trang chủ đang được nhóm giao diện xây dựng.</p>

    <p>
        <a href="{{ route('password.change') }}">Đổi mật khẩu</a> ·
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Đăng xuất</button>
        </form>
    </p>
</body>
</html>
