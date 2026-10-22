{{--
    Additive Admin-only shell: copies the original Admin dashboard sidebar/topbar,
    preserving the 516-line dashboard and its visuals. Used only by the Grade page.
    TODO: consolidate duplicate shell only after explicit approval of a visual-preserving refactor.
--}}
@extends('layouts.app')

@section('title', 'Quản lý & Duyệt điểm - Hệ thống Quản lý Học vụ - HUIT')

@section('content')
<div class="flex min-h-screen">
    <!-- ================= SIDEBAR (260px) ================= -->
    <aside id="app-sidebar" class="w-64 min-w-[260px] h-screen p-4 flex flex-col justify-between overflow-y-auto custom-scrollbar fixed left-0 top-0 z-40 -translate-x-full md:translate-x-0 transition-transform duration-200 bg-surface-container-lowest border-r border-outline-variant shadow-sm">
        <div class="flex flex-col flex-1">
            <!-- Brand -->
            <div class="flex items-center gap-3 px-2 py-3 mb-4 border-b border-surface-container pb-4">
                <div class="w-10 h-10 rounded-xl bg-primary text-on-primary flex items-center justify-center font-bold text-lg shadow-sm shrink-0">
                    <span class="material-symbols-outlined text-[24px]">school</span>
                </div>
                <div class="overflow-hidden">
                    <div class="font-bold text-[15px] text-on-surface leading-snug truncate">{{ config('studentmanager.school.short_name') }}</div>
                    <div class="font-medium text-[12px] text-on-surface-variant truncate">Hệ thống Quản lý Học vụ</div>
                </div>
            </div>

            <div class="mb-4 px-1">
                <button class="w-full flex items-center justify-center gap-2 bg-primary hover:bg-primary-container text-on-primary font-medium text-xs py-2.5 px-3 rounded-lg transition-colors shadow-sm active:scale-[0.99]">
                    <span class="material-symbols-outlined text-[18px]">add_circle</span>
                    <span>Đăng ký môn học</span>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="space-y-1 flex-1">
                <a href="{{ route('admin.home') }}" class="text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal rounded-lg px-3 py-2.5 flex items-center gap-3 transition-colors">
                    <span class="material-symbols-outlined text-primary text-[20px]" style="font-variation-settings: 'FILL' 1;">dashboard</span>
                    <span class="text-sm">Tổng quan</span>
                </a>
                <a href="#" class="text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal rounded-lg px-3 py-2.5 flex items-center gap-3 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">group</span>
                    <span class="text-sm">Sinh viên</span>
                </a>
                <a href="#" class="text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal rounded-lg px-3 py-2.5 flex items-center gap-3 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">badge</span>
                    <span class="text-sm">Giảng viên</span>
                </a>

                <!-- Collapsible: Đào tạo -->
                <div class="pt-1">
                    <button type="button" onclick="document.getElementById('sub-dao-tao').classList.toggle('hidden'); document.getElementById('icon-dao-tao').classList.toggle('rotate-180')" class="w-full text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal rounded-lg px-3 py-2 flex items-center justify-between transition-colors">
                        <span class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-[20px]">menu_book</span>
                            <span class="text-sm">Đào tạo</span>
                        </span>
                        <span id="icon-dao-tao" class="material-symbols-outlined text-on-surface-variant transition-transform text-[18px]">expand_more</span>
                    </button>
                    <div id="sub-dao-tao" class="mt-1 ml-4 pl-3 border-l-2 border-outline-variant space-y-1">
                        <a href="#" class="block px-2 py-1.5 text-xs text-on-surface-variant hover:text-primary rounded transition-colors">Học phần</a>
                        <a href="#" class="block px-2 py-1.5 text-xs text-on-surface-variant hover:text-primary rounded transition-colors">Chương trình đào tạo</a>
                        <a href="#" class="block px-2 py-1.5 text-xs text-on-surface-variant hover:text-primary rounded transition-colors">Khóa</a>
                        <a href="#" class="block px-2 py-1.5 text-xs text-on-surface-variant hover:text-primary rounded transition-colors">Lớp hành chính</a>
                    </div>
                </div>

                {{-- Grade #124–#127: add navigation without replacing the original Admin dashboard. --}}
                @if (\Illuminate\Support\Facades\Route::has('admin.pages.grades') && app(\App\Modules\Auth\Services\AccessControl::class)->allows(auth()->user(), 'GRD', \App\Modules\Auth\Enums\PermissionAction::View))
                    <a href="{{ route('admin.pages.grades') }}"
                       @if (request()->routeIs('admin.pages.grades')) aria-current="page" @endif
                       class="{{ request()->routeIs('admin.pages.grades') ? 'bg-[#EEF2FD] text-primary border-l-4 border-l-primary font-semibold' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal' }} rounded-lg px-3 py-2.5 flex items-center gap-3 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">verified</span>
                        <span class="text-sm">Quản lý &amp; Duyệt điểm</span>
                    </a>
                @endif

                <!-- Collapsible: Danh mục -->
                <div class="pt-1">
                    <button type="button" onclick="document.getElementById('sub-danh-muc').classList.toggle('hidden'); document.getElementById('icon-danh-muc').classList.toggle('rotate-180')" class="w-full text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal rounded-lg px-3 py-2 flex items-center justify-between transition-colors">
                        <span class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-[20px]">category</span>
                            <span class="text-sm">Danh mục</span>
                        </span>
                        <span id="icon-danh-muc" class="material-symbols-outlined text-on-surface-variant transition-transform text-[18px]">expand_more</span>
                    </button>
                    <div id="sub-danh-muc" class="hidden mt-1 ml-4 pl-3 border-l-2 border-outline-variant space-y-1">
                        <a href="#" class="block px-2 py-1.5 text-xs text-on-surface-variant hover:text-primary rounded transition-colors">Khoa / Bộ môn / Ngành</a>
                        <a href="#" class="block px-2 py-1.5 text-xs text-on-surface-variant hover:text-primary rounded transition-colors">Năm học / Học kỳ</a>
                        <a href="#" class="block px-2 py-1.5 text-xs text-on-surface-variant hover:text-primary rounded transition-colors">Phòng học</a>
                    </div>
                </div>

                <a href="#" class="text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal rounded-lg px-3 py-2.5 flex items-center gap-3 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">verified_user</span>
                    <span class="text-sm">Người dùng & Phân quyền</span>
                </a>

                <!-- Collapsible: Hệ thống -->
                <div class="pt-1">
                    <button type="button" onclick="document.getElementById('sub-he-thong').classList.toggle('hidden'); document.getElementById('icon-he-thong').classList.toggle('rotate-180')" class="w-full text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal rounded-lg px-3 py-2 flex items-center justify-between transition-colors">
                        <span class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-[20px]">tune</span>
                            <span class="text-sm">Hệ thống</span>
                        </span>
                        <span id="icon-he-thong" class="material-symbols-outlined text-on-surface-variant transition-transform text-[18px]">expand_more</span>
                    </button>
                    <div id="sub-he-thong" class="hidden mt-1 ml-4 pl-3 border-l-2 border-outline-variant space-y-1">
                        <a href="#" class="block px-2 py-1.5 text-xs text-on-surface-variant hover:text-primary rounded transition-colors">Cấu hình hệ thống</a>
                        <a href="#" class="block px-2 py-1.5 text-xs text-on-surface-variant hover:text-primary rounded transition-colors">Quy chế học vụ</a>
                        <a href="#" class="block px-2 py-1.5 text-xs text-on-surface-variant hover:text-primary rounded transition-colors">Nhật ký thao tác</a>
                    </div>
                </div>

                <a href="#" class="text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal rounded-lg px-3 py-2.5 flex items-center gap-3 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">settings</span>
                    <span class="text-sm">Cài đặt tài khoản</span>
                </a>
            </nav>
        </div>

        <!-- User Summary -->
        <div class="border-t border-surface-container pt-3">
            <div class="flex items-center justify-between p-2 rounded-lg bg-surface-container-low hover:bg-surface-container transition-colors">
                <div class="flex items-center gap-2.5 overflow-hidden">
                    <div class="w-9 h-9 rounded-full bg-primary text-on-primary flex items-center justify-center font-bold text-xs shrink-0">
                        AD
                    </div>
                    <div class="overflow-hidden leading-tight">
                        <div class="font-semibold text-xs text-on-surface truncate">Phòng Đào tạo</div>
                        <div class="text-[11px] text-on-surface-variant truncate">admin@huit.edu.vn</div>
                    </div>
                </div>
                @include('partials.logout-form', ['class' => 'p-1 text-on-surface-variant hover:text-error rounded transition-colors', 'icon' => 'text-[18px]'])
            </div>
        </div>
    </aside>

    <!-- ================= MAIN LAYOUT WRAPPER ================= -->
    <div class="flex-1 md:ml-64 min-w-0 flex flex-col min-h-screen">
        <!-- TOPBAR -->
        <header class="h-14 px-4 md:px-8 bg-surface-container-lowest border-b border-outline-variant/60 flex items-center justify-between sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-3 lg:gap-6 flex-1 min-w-0 max-w-2xl">
                @include('partials.menu-button')
                <nav class="flex items-center gap-2 text-xs text-on-surface-variant font-medium min-w-0">
                    <a href="#" class="hidden md:flex hover:text-primary transition-colors items-center gap-1.5 text-on-surface-variant">
                        <span class="material-symbols-outlined text-[18px]">home</span>
                        <span>Trang chủ</span>
                    </a>
                    <span class="hidden md:inline text-outline-variant">/</span>
                    <span class="text-on-surface font-semibold truncate">Quản lý &amp; Duyệt điểm</span>
                </nav>
                <div class="hidden xl:block h-4 w-px bg-outline-variant shrink-0"></div>
                <div class="hidden xl:flex items-center gap-2 bg-[#EEF2FD] text-primary font-semibold px-3 py-1 rounded-full text-xs border border-primary/20 shrink-0 cursor-pointer shadow-sm">
                    <span class="material-symbols-outlined text-[15px]">calendar_today</span>
                    <span>Năm học 2024-2025 • Học kỳ 1</span>
                    <span class="material-symbols-outlined text-[16px]">arrow_drop_down</span>
                </div>
            </div>
            
            <div class="flex items-center gap-2 md:gap-4 shrink-0">
                <div class="relative hidden lg:block w-64 xl:w-80">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">search</span>
                    <input type="text" placeholder="Tìm kiếm sinh viên, học phần, giảng viên..." class="w-full bg-[#F7F9FC] border border-outline-variant/80 rounded-lg pl-9 pr-9 py-1.5 text-xs text-on-surface placeholder:text-outline focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                    <kbd class="hidden sm:inline-block absolute right-2.5 top-1/2 -translate-y-1/2 px-1.5 py-0.5 text-[10px] font-semibold text-outline-variant bg-surface-container border border-outline-variant rounded">⌘K</kbd>
                </div>
                <div class="hidden lg:block h-5 w-px bg-outline-variant/60"></div>
                <div class="flex items-center gap-1">
                    <button title="Thông báo học vụ" aria-label="Thông báo học vụ" class="relative p-2 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-[20px]">notifications</span>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-error ring-2 ring-surface-container-lowest"></span>
                    </button>
                    <button title="Trợ giúp nghiệp vụ" class="hidden sm:block p-2 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-[20px]">help</span>
                    </button>
                </div>
                <div class="h-5 w-px bg-outline-variant/60"></div>
                <div class="flex items-center gap-2.5 pl-1 cursor-pointer hover:bg-surface-container-low p-1.5 rounded-lg transition-colors">
                    <div class="w-8 h-8 rounded-full bg-primary-container text-on-primary flex items-center justify-center font-semibold text-xs overflow-hidden shadow-sm">
                        <span class="material-symbols-outlined text-[18px]">person</span>
                    </div>
                    <div class="text-left hidden md:block">
                        <div class="font-semibold text-xs text-on-surface leading-tight">Phòng Đào tạo</div>
                        <div class="text-[11px] text-on-surface-variant leading-tight">Quản trị hệ thống (Admin)</div>
                    </div>
                    <span class="material-symbols-outlined text-outline text-[18px]">arrow_drop_down</span>
                </div>
            </div>
        </header>

        <!-- MAIN WORKSPACE BODY -->

        <main class="flex-1 p-4 md:p-8 space-y-6 max-w-[1120px] w-full mx-auto">
            @yield('page')
        </main>
    </div>
</div>
<div id="toast-region" class="fixed bottom-4 right-4 z-50 flex w-[min(92vw,380px)] flex-col gap-2 pointer-events-none" aria-live="polite"></div>
@isset($pageData)
    <script type="application/json" id="page-data">@json($pageData)</script>
@endisset
@include('partials.sidebar-toggle')
@endsection
