{{--
    Khung chung của các trang tài khoản (đổi mật khẩu, cài đặt, phiên đăng nhập, thông báo).
    Dùng cho mọi vai trò nên menu không gắn với riêng cổng nào.
    Trang con: @extends('layouts.account'), @section('title'), @section('page-title'), @section('account').
--}}
@extends('layouts.app')

@section('content')
@php
    $currentUser = auth()->user();
    $homeRoute = app(\App\Modules\Auth\Services\PortalResolver::class)->homeRouteFor($currentUser);
    $homeUrl = $homeRoute !== null ? route($homeRoute) : '#';
    $initials = \Illuminate\Support\Str::of($currentUser->name)->trim()->explode(' ')->filter()->take(-2)
        ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))->implode('');

    $accountLinks = [
        ['route' => 'password.change', 'icon' => 'lock_reset', 'label' => 'Đổi mật khẩu', 'active' => 'password.*'],
        ['route' => 'account.sessions', 'icon' => 'devices', 'label' => 'Phiên đăng nhập', 'active' => 'account.sessions'],
        ['route' => 'account.notifications', 'icon' => 'notifications_active', 'label' => 'Tùy chọn thông báo', 'active' => 'account.notifications'],
        ['route' => 'account.settings', 'icon' => 'manage_accounts', 'label' => 'Thông tin tài khoản', 'active' => 'account.settings'],
    ];
@endphp
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside id="app-sidebar" class="w-64 min-w-[260px] h-screen bg-surface-container-lowest flex flex-col justify-between border-r border-outline-variant fixed left-0 top-0 z-40 select-none -translate-x-full md:translate-x-0 transition-transform duration-200">
        <div class="p-4 flex flex-col gap-5 overflow-y-auto">
            <div class="flex items-center gap-3 px-1 py-1">
                <div class="w-10 h-10 rounded-lg bg-primary flex items-center justify-center text-white shadow-sm flex-shrink-0">
                    <span class="material-symbols-outlined text-2xl font-bold" aria-hidden="true">school</span>
                </div>
                <div class="flex flex-col overflow-hidden">
                    <span class="text-sm font-bold text-primary tracking-tight leading-none">HUIT Học Vụ</span>
                    <span class="text-xs text-on-surface-variant truncate mt-1">Cổng thông tin Đào tạo</span>
                </div>
            </div>

            <a href="{{ $homeUrl }}" class="w-full bg-[#EEF2FD] hover:bg-surface-container text-primary text-xs font-semibold py-2.5 px-3 rounded-lg flex items-center justify-center gap-2 transition-colors border border-outline-variant active:scale-[0.99]">
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">dashboard</span>
                <span>Về trang chủ</span>
            </a>

            <div class="flex flex-col gap-1">
                <div class="px-3 pb-1 text-on-surface-variant text-[11px] uppercase tracking-wider font-semibold">Tài khoản</div>
                <nav class="flex flex-col gap-0.5" aria-label="Menu tài khoản">
                    @foreach ($accountLinks as $link)
                        @if (request()->routeIs($link['active']))
                            <a href="{{ route($link['route']) }}" aria-current="page" class="bg-secondary-container text-primary font-semibold rounded-lg px-3 py-2 flex items-center gap-3 border-l-4 border-primary transition-colors">
                                <span class="material-symbols-outlined text-[20px] text-primary" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">{{ $link['icon'] }}</span>
                                <span class="text-sm font-semibold">{{ $link['label'] }}</span>
                            </a>
                        @else
                            <a href="{{ route($link['route']) }}" class="text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal rounded-lg px-3 py-2 flex items-center gap-3 transition-colors">
                                <span class="material-symbols-outlined text-[20px]" aria-hidden="true">{{ $link['icon'] }}</span>
                                <span class="text-sm">{{ $link['label'] }}</span>
                            </a>
                        @endif
                    @endforeach
                </nav>
            </div>
        </div>

        <div class="p-3 border-t border-outline-variant bg-surface-container-low m-2 rounded-xl flex items-center justify-between gap-2">
            <div class="flex items-center gap-2.5 overflow-hidden">
                <div class="w-8 h-8 rounded-full bg-primary text-white text-xs flex items-center justify-center font-bold flex-shrink-0">{{ $initials }}</div>
                <div class="flex flex-col min-w-0">
                    <p class="text-xs font-semibold text-on-surface truncate">{{ $currentUser->name }}</p>
                    <p class="text-[11px] text-on-surface-variant truncate">{{ $currentUser->username ?? $currentUser->email }}</p>
                </div>
            </div>
            @include('partials.logout-form', ['class' => 'p-1.5 text-on-surface-variant hover:text-error hover:bg-surface-container rounded-lg transition-colors'])
        </div>
    </aside>

    <!-- Workspace -->
    <div class="flex-1 flex flex-col md:ml-64 min-w-0 min-h-screen bg-background">
        <header class="h-16 bg-surface-container-lowest border-b border-outline-variant flex items-center justify-between gap-3 px-4 md:px-6 sticky top-0 z-30 shadow-xs">
            <div class="flex items-center gap-3 min-w-0">
                @include('partials.menu-button')
                <nav class="flex items-center text-on-surface-variant text-xs gap-2 min-w-0" aria-label="Đường dẫn">
                    <a href="{{ $homeUrl }}" class="hidden sm:flex hover:text-primary transition-colors items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]" aria-hidden="true">home</span>
                        <span>Trang chủ</span>
                    </a>
                    <span class="hidden sm:inline text-outline-variant">/</span>
                    <span class="hidden sm:inline">Tài khoản</span>
                    <span class="hidden sm:inline text-outline-variant">/</span>
                    <span class="text-primary font-semibold truncate">@yield('page-title')</span>
                </nav>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <div class="w-8 h-8 rounded-full bg-primary-container text-white text-xs flex items-center justify-center font-bold shadow-xs" aria-hidden="true">{{ $initials }}</div>
                <span class="hidden md:inline text-xs text-on-surface font-semibold">{{ $currentUser->name }}</span>
            </div>
        </header>

        <main class="flex-1 p-4 md:p-8 flex flex-col justify-between">
            <div class="w-full mx-auto flex flex-col items-center">
                @if (session('status'))
                    <div class="w-full max-w-[540px] mb-4 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800" role="status">{{ session('status') }}</div>
                @endif

                @yield('account')
            </div>

            <footer class="w-full max-w-[1440px] mx-auto mt-12 pt-6 border-t border-outline-variant text-center text-xs text-on-surface-variant">
                <p>© {{ now()->year }} Trường Đại học Công Thương TP. Hồ Chí Minh (HUIT) - Cổng Quản lý Học vụ | Hỗ trợ kỹ thuật: <a href="mailto:daotao@huit.edu.vn" class="text-primary hover:underline">daotao@huit.edu.vn</a></p>
            </footer>
        </main>
    </div>
</div>
@include('partials.sidebar-toggle')
@endsection
