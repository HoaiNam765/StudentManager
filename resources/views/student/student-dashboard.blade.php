@extends('layouts.app')

@section('title', 'HUIT Học Vụ - Cổng thông tin Sinh viên')

@section('content')
<div class="flex min-h-screen">
    <!-- Left Sidebar -->
    <aside id="app-sidebar" class="h-screen w-64 flex flex-col fixed left-0 top-0 z-40 -translate-x-full md:translate-x-0 transition-transform duration-200 bg-white border-r border-[#E2E8F0] shadow-sm select-none">
        <!-- Header Brand -->
        <div class="p-4 border-b border-[#E2E8F0] flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-white font-bold text-base shadow-sm ring-2 ring-primary/20">
                HUIT
            </div>
            <div class="flex flex-col min-w-0">
                <div class="text-[15px] font-bold text-on-surface tracking-tight leading-snug truncate">{{ config('studentmanager.school.short_name') }}</div>
                <div class="text-xs text-on-surface-variant truncate">Cổng Sinh viên</div>
            </div>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 px-3 py-4 space-y-1 overflow-y-auto custom-scrollbar">
            <!-- Tab 1: Tổng quan (Active) -->
            <a href="{{ route('student.home') }}" class="bg-[#EEF2FD] text-primary font-semibold rounded-lg px-3 py-2.5 flex items-center gap-3 border-l-[3px] border-primary transition-colors">
                <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">dashboard</span>
                <span class="text-sm font-semibold">Tổng quan</span>
            </a>
            <a href="{{ route('student.pages.curriculum') }}" class="text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal rounded-lg px-3 py-2.5 flex items-center gap-3 transition-colors">
                <span class="material-symbols-outlined">school</span>
                <span class="text-sm">Chương trình đào tạo</span>
            </a>
            <a href="{{ route('student.pages.timetable') }}" class="text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal rounded-lg px-3 py-2.5 flex items-center gap-3 transition-colors">
                <span class="material-symbols-outlined">calendar_month</span>
                <span class="text-sm">Thời khóa biểu</span>
            </a>
            <a href="{{ route('student.pages.registration') }}" class="text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal rounded-lg px-3 py-2.5 flex items-center gap-3 transition-colors">
                <span class="material-symbols-outlined">how_to_reg</span>
                <span class="text-sm">Đăng ký học phần</span>
            </a>
            <a href="{{ route('student.pages.grades') }}" class="text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal rounded-lg px-3 py-2.5 flex items-center gap-3 transition-colors">
                <span class="material-symbols-outlined">grade</span>
                <span class="text-sm">Điểm</span>
            </a>
            <a href="{{ route('notifications.page') }}" class="text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal rounded-lg px-3 py-2.5 flex items-center justify-between transition-colors">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="text-sm">Thông báo</span>
                </div>
                <span class="px-1.5 py-0.5 rounded-full text-[11px] font-bold bg-error text-white">{{ $notifications['unread'] }}</span>
            </a>
            <a href="{{ route('student.pages.profile') }}" class="text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal rounded-lg px-3 py-2.5 flex items-center gap-3 transition-colors">
                <span class="material-symbols-outlined">person</span>
                <span class="text-sm">Hồ sơ cá nhân</span>
            </a>
            <a href="{{ route('account.settings') }}" class="text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal rounded-lg px-3 py-2.5 flex items-center gap-3 transition-colors">
                <span class="material-symbols-outlined">settings</span>
                <span class="text-sm">Cài đặt</span>
            </a>
        </div>

        <!-- Footer Student Profile -->
        <div class="p-3.5 border-t border-[#E2E8F0] bg-white flex items-center justify-between">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-9 h-9 rounded-full bg-primary-fixed flex items-center justify-center text-primary font-bold border border-outline-variant/60 shadow-xs shrink-0">
                    <span class="text-xs">{{ $student['initials'] }}</span>
                </div>
                <div class="flex flex-col min-w-0">
                    <div class="text-xs font-semibold text-on-surface truncate">{{ $student['name'] }}</div>
                    <div class="text-[11px] text-on-surface-variant truncate">{{ $student['student_id'] }} • {{ $student['class'] }}</div>
                </div>
            </div>
            @include('partials.logout-form')
        </div>
    </aside>

    <!-- Workspace Shell -->
    <div class="flex-1 flex flex-col md:ml-64 min-w-0 min-h-screen">
        <!-- Top Navigation Bar -->
        <header class="sticky top-0 z-30 h-14 bg-white border-b border-[#E2E8F0] px-4 md:px-8 flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-3 md:gap-4 min-w-0">
                @include('partials.menu-button')
                <nav class="flex items-center gap-2 text-xs text-on-surface-variant min-w-0">
                    <span class="hidden sm:inline text-on-surface-variant">Trang chủ</span>
                    <span class="hidden sm:inline material-symbols-outlined text-[14px] text-outline-variant">chevron_right</span>
                    <span class="text-on-surface font-semibold truncate">Cổng thông tin Sinh viên</span>
                </nav>
                <div class="h-4 w-px bg-outline-variant/60 hidden sm:block"></div>
                <div class="hidden md:inline-flex items-center gap-1.5 bg-[#EEF2FD] text-primary font-semibold px-2.5 py-1 rounded-full text-xs border border-primary/20">
                    <span class="material-symbols-outlined text-[15px]">calendar_today</span>
                    <span>{{ $termLabel }}</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="relative hidden sm:block">
                    <input type="search" aria-label="Tìm mục trong menu" oninput="document.querySelectorAll('#app-sidebar a').forEach(a => a.hidden = !a.textContent.toLocaleLowerCase('vi').includes(this.value.toLocaleLowerCase('vi')))" placeholder="Tìm kiếm nhanh..." class="h-8 text-xs pl-8 pr-3 bg-surface-container-low/50 border border-outline-variant/60 rounded-lg text-on-surface placeholder:text-outline focus:outline-none focus:ring-2 focus:ring-primary/20 w-48">
                    <span class="material-symbols-outlined absolute left-2 top-2 text-[15px] text-outline">search</span>
                </div>
                <a href="{{ route('notifications.page') }}" title="Thông báo" aria-label="Thông báo" class="relative p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-low transition-colors">
                    <span class="material-symbols-outlined text-[20px]">notifications</span>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-error rounded-full ring-2 ring-white"></span>
                </a>
                <button type="button" onclick="document.getElementById('portal-help').showModal()" title="Trợ giúp" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-low transition-colors hidden sm:block">
                    <span class="material-symbols-outlined text-[20px]">help</span>
                </button>
                <div class="h-5 w-px bg-outline-variant/60 mx-0.5"></div>
                <div class="w-8 h-8 rounded-full bg-primary-fixed flex items-center justify-center text-primary font-bold border border-outline-variant/60 shadow-xs">
                    <span class="text-xs">{{ $student['initials'] }}</span>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 p-4 md:p-8 w-full bg-[#F7F9FC]">
            <div class="max-w-[1120px] w-full mx-auto space-y-6">
                @if ($profile === null)<p role="status" class="rounded-lg border border-outline-variant bg-white p-4 text-sm">Tài khoản chưa được liên kết với hồ sơ sinh viên. Liên hệ Phòng Đào tạo để cập nhật.</p>@endif
                <!-- Welcome Banner Card -->
                <div class="bg-white border border-[#E2E8F0] rounded-xl p-6 shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-5 relative overflow-hidden">
                    <div class="space-y-1.5 relative z-10">
                        <h1 class="text-[30px] font-bold text-on-surface tracking-tight leading-tight">
                            Xin chào, {{ $student['name'] }} 👋
                        </h1>
                        <p class="text-xs text-on-surface-variant">
                            MSSV: <strong class="text-primary font-semibold">{{ $student['student_id'] }}</strong> • Ngành: <strong class="text-on-surface">{{ $student['major'] }}</strong> • Khóa: <strong>{{ $student['cohort'] }}</strong> • Lớp: <strong>{{ $student['class'] }}</strong>
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2.5 relative z-10 shrink-0">
                        <a href="{{ route('student.pages.registration') }}" class="bg-primary hover:bg-primary-container text-white text-xs font-semibold px-4 py-2.5 rounded-lg flex items-center gap-1.5 shadow-sm transition-all active:scale-95">
                            <span class="material-symbols-outlined text-[16px]">add</span>
                            <span>Đăng ký học phần</span>
                        </a>
                        <button type="button" disabled title="Lịch thi chưa được triển khai" class="bg-white border border-outline-variant hover:bg-surface-container-low text-on-surface text-xs font-semibold px-3.5 py-2.5 rounded-lg flex items-center gap-1.5 shadow-xs transition-colors">
                            <span class="material-symbols-outlined text-primary text-[16px]">event_note</span>
                            <span>Xem lịch thi HK1</span>
                        </button>
                        <button type="button" disabled title="Học phí chưa được triển khai" class="bg-white border border-outline-variant hover:bg-surface-container-low text-on-surface text-xs font-semibold px-3.5 py-2.5 rounded-lg flex items-center gap-1.5 shadow-xs transition-colors">
                            <span class="material-symbols-outlined text-primary text-[16px]">receipt_long</span>
                            <span>Tra cứu Học phí</span>
                        </button>
                    </div>
                </div>

                <!-- Overview 4 Stat Cards Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Stat 1: Tiến độ tín chỉ -->
                    <div class="bg-white border border-[#E2E8F0] rounded-xl p-5 shadow-xs flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-on-surface-variant font-medium">Tiến độ tín chỉ</span>
                            <div class="w-7 h-7 rounded-lg bg-primary-fixed flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-[16px]">school</span>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-[22px] font-bold text-on-surface leading-tight">{{ $student['credits_earned'] ?? '—' }}</span>
                                <span class="text-xs text-on-surface-variant font-medium">/ {{ $student['credits_total'] ?? '—' }} TC</span>
                            </div>
                            <div class="w-full h-2 rounded-full bg-surface-container-high overflow-hidden">
                                <div class="h-full rounded-full bg-primary" style="width: {{ $student['credits_pct'] }}%"></div>
                            </div>
                            <div class="text-[11px] text-on-surface-variant flex items-center justify-between pt-0.5">
                                <span>Đã tích lũy: <strong class="text-primary">{{ $student['credits_pct'] }}%</strong></span>
                                <span>Còn lại: <strong>{{ $student['credits_remaining'] ?? '—' }} TC</strong></span>
                            </div>
                        </div>
                    </div>

                    <!-- Stat 2: GPA -->
                    <div class="bg-white border border-[#E2E8F0] rounded-xl p-5 shadow-xs flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-on-surface-variant font-medium">Điểm trung bình (GPA)</span>
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">
                                Xếp loại: {{ $student['classification'] }}
                            </span>
                        </div>
                        <div class="space-y-1.5">
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-[22px] font-bold text-primary leading-tight">{{ $student['gpa_4'] === null ? '—' : number_format($student['gpa_4'], 2) }}</span>
                                <span class="text-xs text-on-surface-variant font-medium">/ 4.0</span>
                            </div>
                            <div class="text-[11px] text-on-surface-variant pt-1 border-t border-[#F1F5F9] flex items-center justify-between">
                                <span><a href="{{ route('student.pages.grades') }}">Xem bảng điểm đã công bố</a></span>
                                <span class="text-emerald-700 font-medium flex items-center gap-0.5">
                                    <span class="material-symbols-outlined text-[14px]">trending_up</span> 
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Stat 3: Lớp học hiện tại -->
                    <div class="bg-white border border-[#E2E8F0] rounded-xl p-5 shadow-xs flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-on-surface-variant font-medium">Lớp học hiện tại</span>
                            <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-[16px]">menu_book</span>
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-[22px] font-bold text-on-surface leading-tight">{{ $student['current_courses_count'] }}</span>
                                <span class="text-xs text-on-surface-variant font-medium">Học phần</span>
                            </div>
                            <div class="text-[11px] text-on-surface-variant pt-1 border-t border-[#F1F5F9] flex items-center justify-between">
                                <span>{{ $student['current_credits'] }} tín chỉ</span>
                                <span class="font-medium text-primary">Đang theo học</span>
                            </div>
                        </div>
                    </div>

                    <!-- Stat 4: Học phí -->
                    <div class="bg-white border border-[#E2E8F0] rounded-xl p-5 shadow-xs flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-on-surface-variant font-medium">Tình trạng học phí</span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Chưa có dữ liệu
                            </span>
                        </div>
                        <div class="space-y-1.5">
                            <div class="flex items-baseline gap-1">
                                <span class="text-[18px] font-bold text-emerald-700 leading-tight">—</span>
                                <span class="text-xs text-on-surface-variant font-normal"></span>
                            </div>
                            <div class="text-[11px] text-on-surface-variant pt-1 border-t border-[#F1F5F9] flex items-center justify-between">
                                <a href="javascript:void(0)" aria-disabled="true" title="Chức năng chưa được triển khai" class="text-primary hover:underline flex items-center gap-0.5 font-medium">
                                    Xem biên lai HK1 ↗
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 12-Column Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- Left 8 Columns -->
                    <div class="lg:col-span-8 space-y-6">
                        <!-- Thời khóa biểu hôm nay -->
                        <div class="bg-white border border-[#E2E8F0] rounded-xl shadow-xs p-6 space-y-4">
                            <div class="flex items-center justify-between border-b border-[#F1F5F9] pb-3">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary text-[22px]">calendar_today</span>
                                    <h2 class="text-[20px] font-bold text-on-surface leading-tight">Thời khóa biểu hôm nay ({{ $todayLabel }})</h2>
                                </div>
                                <a href="{{ route('student.pages.timetable') }}" class="text-xs text-primary hover:underline flex items-center gap-0.5">
                                    Xem toàn bộ tuần →
                                </a>
                            </div>

                            <div class="space-y-3">
                                @include('student.partials.today-classes')
                            </div>
                        </div>

                        <!-- Tiến độ Chương trình đào tạo -->
                        <div class="bg-white border border-[#E2E8F0] rounded-xl shadow-xs p-6 space-y-4">
                            <div class="flex items-center justify-between border-b border-[#F1F5F9] pb-3">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary text-[22px]">trending_up</span>
                                    <h2 class="text-[18px] font-bold text-on-surface">Tiến độ Chương trình đào tạo (84 / {{ $student['credits_total'] ?? '—' }} TC - {{ $student['credits_pct'] }}%)</h2>
                                </div>
                                <a href="{{ route('student.pages.curriculum') }}" class="text-xs text-primary hover:underline flex items-center gap-0.5">
                                    Xem khung chương trình →
                                </a>
                            </div>

                            <div class="space-y-2">
                                <div class="w-full h-3 rounded-full bg-[#E2E8F0] overflow-hidden flex">
                                    <div class="h-full bg-primary" style="width: {{ $student['credits_pct'] }}%" title="Đã tích lũy: {{ $student['credits_pct'] }}%"></div>
                                    <div class="h-full bg-primary-container" style="width: {{ $student['current_pct'] }}%" title="Đang học: {{ $student['current_pct'] }}%"></div>
                                    <div class="h-full bg-outline-variant/60" style="width: {{ $student['remaining_pct'] }}%" title="Còn lại: {{ $student['remaining_pct'] }}%"></div>
                                </div>
                                <div class="flex flex-wrap items-center justify-between text-xs text-on-surface-variant pt-1">
                                    <span class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-primary"></span> Đã tích lũy: <strong class="text-on-surface">{{ $student['credits_earned'] ?? '—' }} TC ({{ $student['credits_pct'] }}%)</strong>
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-primary-container"></span> Đang học: <strong class="text-on-surface">{{ $student['current_credits'] }} TC ({{ $student['current_pct'] }}%)</strong>
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-outline-variant"></span> Còn lại: <strong class="text-on-surface">{{ $student['credits_remaining'] ?? '—' }} TC ({{ $student['remaining_pct'] }}%)</strong>
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                                @include('student.partials.blocks')
                            </div>
                        </div>
                    </div>

                    <!-- Right 4 Columns -->
                    <div class="lg:col-span-4 space-y-6">
                        <!-- Kết quả mới cập nhật -->
                        <div class="bg-white border border-[#E2E8F0] rounded-xl shadow-xs p-5 space-y-4">
                            <div class="flex items-center justify-between border-b border-[#F1F5F9] pb-3">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary text-[20px]">verified</span>
                                    <h3 class="text-sm font-bold text-on-surface">Kết quả mới cập nhật</h3>
                                </div>
                                <a href="{{ route('student.pages.grades') }}" class="text-xs text-primary hover:underline">Bảng điểm đầy đủ</a>
                            </div>
                            <div class="space-y-2.5 text-xs">
                                @include('student.partials.recent-grades')
                            </div>
                        </div>

                        <!-- Thông báo học vụ mới nhất -->
                        <div class="bg-white border border-[#E2E8F0] rounded-xl shadow-xs p-5 space-y-4">
                            <div class="flex items-center justify-between border-b border-[#F1F5F9] pb-3">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary text-[20px]">campaign</span>
                                    <h3 class="text-sm font-bold text-on-surface">Thông báo học vụ mới nhất</h3>
                                </div>
                                <a href="{{ route('notifications.page') }}" class="text-xs text-primary hover:underline">Tất cả</a>
                            </div>
                            <div class="space-y-3">
                                @include('student.partials.recent-notifications')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="mt-auto py-4 px-8 border-t border-[#E2E8F0] text-center text-xs text-on-surface-variant bg-white flex flex-col sm:flex-row items-center justify-between gap-2">
            <div>
                © {{ date('Y') }} <strong>{{ config('studentmanager.school.name') }} ({{ config('studentmanager.school.abbr') }})</strong> - Hệ thống Quản lý Học vụ.
            </div>
            <div class="flex items-center gap-4 text-xs">
                <a href="javascript:void(0)" aria-disabled="true" title="Chức năng chưa được triển khai" class="hover:text-primary transition-colors">Quy chế đào tạo tín chỉ</a>
                <span>•</span>
                <a href="javascript:void(0)" aria-disabled="true" title="Chức năng chưa được triển khai" class="hover:text-primary transition-colors">Hướng dẫn sinh viên</a>
                <span>•</span>
                <a href="javascript:void(0)" aria-disabled="true" title="Chức năng chưa được triển khai" class="hover:text-primary transition-colors">Chính sách bảo mật</a>
            </div>
        </footer>
    </div>
</div>
<dialog id="portal-help" class="m-auto rounded-xl border border-outline-variant p-6 max-w-lg"><h2 class="font-bold">Hướng dẫn sinh viên</h2><p class="my-4">Dùng menu để đăng ký học phần, xem kết quả đăng ký, lịch học và điểm đã công bố. Nếu tài khoản chưa có hồ sơ, liên hệ Phòng Đào tạo để liên kết MSSV.</p><form method="dialog"><button class="px-4 py-2 rounded-lg bg-primary text-white">Đóng</button></form></dialog>
@include('partials.sidebar-toggle')
@endsection
