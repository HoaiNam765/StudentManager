@extends('layouts.account')

@section('title', 'Thông tin tài khoản - Hệ thống Quản lý Học vụ - HUIT')
@section('page-title', 'Thông tin tài khoản')

@section('account')
<div class="w-full max-w-[720px] space-y-5">
    <div>
        <h1 class="text-2xl text-on-surface font-bold tracking-tight">Thông tin tài khoản</h1>
        <p class="text-sm text-on-surface-variant mt-1.5">Thông tin đăng nhập và vai trò của bạn trong hệ thống. Liên hệ Phòng Đào tạo nếu cần chỉnh sửa.</p>
    </div>

    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-5 sm:p-8">
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
            <div>
                <dt class="text-xs text-on-surface-variant font-medium">Họ và tên</dt>
                <dd class="mt-1 text-sm text-on-surface font-semibold break-words">{{ $user->name }}</dd>
            </div>
            <div>
                <dt class="text-xs text-on-surface-variant font-medium">Tên đăng nhập</dt>
                <dd class="mt-1 text-sm text-on-surface font-semibold break-words">{{ $user->username ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-on-surface-variant font-medium">Email</dt>
                <dd class="mt-1 text-sm text-on-surface font-semibold break-all">{{ $user->email }}</dd>
            </div>
            <div>
                <dt class="text-xs text-on-surface-variant font-medium">Đăng nhập gần nhất</dt>
                <dd class="mt-1 text-sm text-on-surface font-semibold">{{ \App\Support\Format::dateTime($user->last_login_at) }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-xs text-on-surface-variant font-medium">Vai trò đang hiệu lực</dt>
                <dd class="mt-1.5 flex flex-wrap gap-2">
                    @forelse ($roles as $role)
                        <span class="inline-flex items-center rounded-full bg-[#EEF2FD] text-primary border border-primary/20 px-2.5 py-1 text-xs font-semibold">{{ $role }}</span>
                    @empty
                        <span class="text-sm text-on-surface-variant">Chưa có vai trò nào đang hiệu lực.</span>
                    @endforelse
                </dd>
            </div>
        </dl>
    </div>

    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-start gap-3">
            <div class="w-9 h-9 rounded-lg bg-surface-container-low flex items-center justify-center text-primary border border-outline-variant shrink-0">
                <span class="material-symbols-outlined text-[20px]" aria-hidden="true">lock_reset</span>
            </div>
            <div>
                <h2 class="text-sm text-on-surface font-semibold">Mật khẩu</h2>
                <p class="text-xs text-on-surface-variant mt-0.5">
                    Lần đổi gần nhất: {{ $user->password_changed_at ? \App\Support\Format::dateTime($user->password_changed_at) : 'chưa đổi lần nào' }}
                </p>
            </div>
        </div>
        <a href="{{ route('password.change') }}" class="inline-flex items-center justify-center h-10 px-4 bg-primary hover:bg-primary-container text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">Đổi mật khẩu</a>
    </div>
</div>
@endsection
