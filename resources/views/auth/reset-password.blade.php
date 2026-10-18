{{-- Trang tạm của BE (issue #82): nhóm FE thay giao diện, giữ nguyên route `password.store` và các trường token, email, password, password_confirmation. --}}
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu - {{ config('studentmanager.school.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50 p-4">
<main class="w-full max-w-md bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
    <h1 class="text-xl font-semibold mb-4">Đặt lại mật khẩu</h1>

    @error('token')
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-800" role="alert">
            {{ $message }} <a href="{{ route('password.request') }}" class="underline">Yêu cầu liên kết mới</a>
        </div>
    @enderror

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ old('email', $email) }}">
        <div>
            <label for="password" class="block text-sm font-medium mb-1">Mật khẩu mới</label>
            <input id="password" name="password" type="password" required autocomplete="new-password" class="w-full rounded-lg border border-gray-300 px-3 py-2">
            @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="password_confirmation" class="block text-sm font-medium mb-1">Nhập lại mật khẩu mới</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="w-full rounded-lg border border-gray-300 px-3 py-2">
        </div>
        <button type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-2 text-white font-medium hover:bg-blue-700">Đặt lại mật khẩu</button>
    </form>
</main>
</body>
</html>
