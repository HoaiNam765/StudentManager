{{-- Trang tạm của BE (issue #82): nhóm FE thay giao diện, giữ nguyên route `password.email` và trường `login`. --}}
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - {{ config('studentmanager.school.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50 p-4">
<main class="w-full max-w-md bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
    <h1 class="text-xl font-semibold mb-2">Quên mật khẩu</h1>
    <p class="text-sm text-gray-600 mb-4">Nhập tên đăng nhập (MSSV, mã cán bộ) hoặc email. Liên kết đặt lại mật khẩu sẽ được gửi tới email đã đăng ký.</p>

    @if (session('status'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-3 text-sm text-green-800" role="status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf
        <div>
            <label for="login" class="block text-sm font-medium mb-1">Tên đăng nhập hoặc email</label>
            <input id="login" name="login" type="text" value="{{ old('login') }}" required autofocus class="w-full rounded-lg border border-gray-300 px-3 py-2">
            @error('login')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-2 text-white font-medium hover:bg-blue-700">Gửi liên kết đặt lại</button>
    </form>

    <p class="mt-4 text-sm"><a href="{{ route('login') }}" class="text-blue-600 hover:underline">Quay lại đăng nhập</a></p>
</main>
</body>
</html>
