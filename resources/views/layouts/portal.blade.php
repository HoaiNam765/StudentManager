{{--
    Khung chung của các trang đã nối API ở ba cổng (docs/fe-be-mapping.md mục 2.2).
    Trang con: @extends('layouts.portal'), @section('title'), @section('page-title'), @section('page').
    Controller truyền $pageData (mảng) để JavaScript đọc qua ui.pageData(); nội dung có data-page="…" thì
    resources/js/app.js nạp resources/js/pages/….js.
--}}
@extends('layouts.app')

@section('content')
@php
    $currentUser = auth()->user();
    $portal = \Illuminate\Support\Str::before(request()->route()?->getName() ?? '', '.');
    $homeRoute = app(\App\Modules\Auth\Services\PortalResolver::class)->homeRouteFor($currentUser);
    // Trang dùng chung (trung tâm thông báo…) không thuộc cổng nào: lấy cổng theo vai trò của người dùng
    if (! isset(\App\Http\Navigation\PortalMenu::PORTALS[$portal]) && $homeRoute !== null) {
        $portal = \Illuminate\Support\Str::before($homeRoute, '.');
    }
    $menu = app(\App\Http\Navigation\PortalMenu::class)->for($portal, $currentUser);
    $portalName = \App\Http\Navigation\PortalMenu::PORTALS[$portal] ?? 'Hệ thống Quản lý Học vụ';
    $homeUrl = $homeRoute !== null ? route($homeRoute) : url('/');
    $initials = \Illuminate\Support\Str::of($currentUser->name)->trim()->explode(' ')->filter()->take(-2)
        ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))->implode('');

    $accountLinks = [
        ['route' => 'notifications.page', 'icon' => 'notifications', 'label' => 'Thông báo'],
        ['route' => 'account.settings', 'icon' => 'manage_accounts', 'label' => 'Thông tin tài khoản'],
        ['route' => 'password.change', 'icon' => 'lock_reset', 'label' => 'Đổi mật khẩu'],
        ['route' => 'account.sessions', 'icon' => 'devices', 'label' => 'Phiên đăng nhập'],
    ];
@endphp
<div class="flex min-h-screen">
    <aside id="app-sidebar" class="w-64 min-w-[256px] h-screen bg-surface-container-lowest border-r border-outline-variant flex flex-col justify-between fixed left-0 top-0 z-40 -translate-x-full md:translate-x-0 transition-transform duration-200 select-none print:hidden">
        <div class="flex flex-col min-h-0">
            <div class="p-4 border-b border-outline-variant flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-on-primary shadow-sm shrink-0">
                    <span class="material-symbols-outlined text-[22px]" aria-hidden="true">school</span>
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="text-[15px] font-bold text-on-surface leading-snug truncate">{{ config('studentmanager.school.short_name') }}</span>
                    <span class="text-xs text-on-surface-variant truncate">{{ $portalName }}</span>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto custom-scrollbar p-3 space-y-5" aria-label="Menu chính">
                @foreach ($menu as $group => $items)
                    <div class="space-y-0.5">
                        <div class="px-3 pb-1 text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">{{ $group }}</div>
                        @foreach ($items as $item)
                            <a href="{{ $item['url'] }}" @if ($item['active']) aria-current="page" @endif
                               class="{{ $item['active'] ? 'bg-secondary-container text-primary font-semibold border-l-4 border-primary' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low' }} rounded-lg px-3 py-2 flex items-center gap-3 text-sm transition-colors">
                                <span class="material-symbols-outlined text-[20px] {{ $item['active'] ? 'fill' : '' }}" aria-hidden="true">{{ $item['icon'] }}</span>
                                <span class="truncate">{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                @endforeach

                <div class="space-y-0.5">
                    <div class="px-3 pb-1 text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Tài khoản</div>
                    @foreach ($accountLinks as $link)
                        <a href="{{ route($link['route']) }}" class="text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low rounded-lg px-3 py-2 flex items-center gap-3 text-sm transition-colors">
                            <span class="material-symbols-outlined text-[20px]" aria-hidden="true">{{ $link['icon'] }}</span>
                            <span class="truncate">{{ $link['label'] }}</span>
                        </a>
                    @endforeach
                </div>

                @if (app()->environment('local') && Route::has('fe.preview.index'))
                    <a href="{{ route('fe.preview.index') }}" class="mx-3 block rounded-lg border border-dashed border-outline-variant px-3 py-2 text-xs text-on-surface-variant hover:text-primary hover:border-primary/40">
                        Màn hình mẫu chưa nối BE (chỉ môi trường local)
                    </a>
                @endif
            </nav>
        </div>

        <div class="p-3 border-t border-outline-variant bg-surface-container-low m-2 rounded-xl flex items-center justify-between gap-2">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-full bg-primary text-on-primary text-xs flex items-center justify-center font-bold shrink-0" aria-hidden="true">{{ $initials }}</div>
                <div class="flex flex-col min-w-0">
                    <p class="text-xs font-semibold text-on-surface truncate">{{ $currentUser->name }}</p>
                    <p class="text-[11px] text-on-surface-variant truncate">{{ $currentUser->username ?? $currentUser->email }}</p>
                </div>
            </div>
            @include('partials.logout-form', ['class' => 'p-1.5 text-on-surface-variant hover:text-error hover:bg-surface-container rounded-lg transition-colors'])
        </div>
    </aside>

    <div class="flex-1 flex flex-col md:ml-64 print:ml-0 min-w-0 min-h-screen">
        <header class="h-14 bg-surface-container-lowest border-b border-outline-variant flex items-center justify-between gap-3 px-4 md:px-6 sticky top-0 z-30 shadow-xs print:hidden">
            <div class="flex items-center gap-3 min-w-0">
                @include('partials.menu-button')
                <nav class="flex items-center gap-2 text-xs text-on-surface-variant min-w-0" aria-label="Đường dẫn">
                    <a href="{{ $homeUrl }}" class="hidden sm:inline-flex items-center gap-1 hover:text-primary">
                        <span class="material-symbols-outlined text-[16px]" aria-hidden="true">home</span>
                        <span>Trang chủ</span>
                    </a>
                    <span class="hidden sm:inline text-outline-variant" aria-hidden="true">/</span>
                    <span class="text-primary font-semibold truncate">@yield('page-title')</span>
                </nav>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                @if (Route::has('notifications.page') && app(\App\Modules\Auth\Services\AccessControl::class)->allows($currentUser, 'NOT', \App\Modules\Auth\Enums\PermissionAction::View))
                    {{-- Chuông thông báo (FR-NOT-001): số chưa đọc do resources/js/notification-bell.js hỏi định kỳ --}}
                    <a href="{{ route('notifications.page') }}" class="relative p-2 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-low"
                       data-notification-bell data-count-url="{{ route('notifications.unread-count') }}" data-poll="{{ (int) config('notifications.poll_seconds', 60) }}" aria-label="Thông báo">
                        <span class="material-symbols-outlined text-[22px]" aria-hidden="true">notifications</span>
                        <span data-bell-count hidden class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 rounded-full bg-error text-white text-[10px] font-bold leading-[18px] text-center"></span>
                    </a>
                @endif
                <div class="w-8 h-8 rounded-full bg-primary-container text-on-primary text-xs flex items-center justify-center font-bold" aria-hidden="true">{{ $initials }}</div>
                <span class="hidden md:inline text-xs font-semibold text-on-surface">{{ $currentUser->name }}</span>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-6 lg:p-8 w-full bg-[#F7F9FC] print:bg-white print:p-0">
            <div class="max-w-7xl w-full mx-auto">
                @yield('page')
            </div>
        </main>

        <footer class="py-4 px-6 bg-surface-container-lowest border-t border-outline-variant text-center text-xs text-on-surface-variant print:hidden">
            © {{ now()->year }} {{ config('studentmanager.school.name') }} ({{ config('studentmanager.school.abbr') }}) — Hệ thống Quản lý Học vụ
        </footer>
    </div>
</div>

<div id="toast-region" class="fixed bottom-4 right-4 z-50 flex w-[min(92vw,380px)] flex-col gap-2 pointer-events-none print:hidden" aria-live="polite"></div>

@isset($pageData)
    <script type="application/json" id="page-data">@json($pageData)</script>
@endisset

@include('partials.sidebar-toggle')
@endsection
