@extends('layouts.app')

@section('title', 'HUIT Học Vụ - Cổng thông tin Sinh viên')

@section('content')
<div class="flex min-h-screen">
    <!-- Left Sidebar -->
    <aside class="h-screen w-64 flex flex-col fixed left-0 top-0 z-40 bg-white border-r border-[#E2E8F0] shadow-sm select-none">
        <!-- Header Brand -->
        <div class="p-4 border-b border-[#E2E8F0] flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-white font-bold text-base shadow-sm ring-2 ring-primary/20">
                HUIT
            </div>
            <div class="flex flex-col min-w-0">
                <div class="text-[15px] font-bold text-on-surface tracking-tight leading-snug truncate">Trường ĐH Công Thương</div>
                <div class="text-xs text-on-surface-variant truncate">Cổng Sinh viên</div>
            </div>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 px-3 py-4 space-y-1 overflow-y-auto custom-scrollbar">
            <!-- Tab 1: Tổng quan (Active) -->
            <a href="{{ route('student.dashboard') ?? '#' }}" class="bg-[#EEF2FD] text-primary font-semibold rounded-lg px-3 py-2.5 flex items-center gap-3 border-l-[3px] border-primary transition-colors">
                <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">dashboard</span>
                <span class="text-sm font-semibold">Tổng quan</span>
            </a>
            <a href="#" class="text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal rounded-lg px-3 py-2.5 flex items-center gap-3 transition-colors">
                <span class="material-symbols-outlined">school</span>
                <span class="text-sm">Chương trình đào tạo</span>
            </a>
            <a href="#" class="text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal rounded-lg px-3 py-2.5 flex items-center gap-3 transition-colors">
                <span class="material-symbols-outlined">calendar_month</span>
                <span class="text-sm">Thời khóa biểu</span>
            </a>
            <a href="#" class="text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal rounded-lg px-3 py-2.5 flex items-center gap-3 transition-colors">
                <span class="material-symbols-outlined">how_to_reg</span>
                <span class="text-sm">Đăng ký học phần</span>
            </a>
            <a href="#" class="text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal rounded-lg px-3 py-2.5 flex items-center gap-3 transition-colors">
                <span class="material-symbols-outlined">grade</span>
                <span class="text-sm">Điểm</span>
            </a>
            <a href="#" class="text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal rounded-lg px-3 py-2.5 flex items-center justify-between transition-colors">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="text-sm">Thông báo</span>
                </div>
                <span class="px-1.5 py-0.5 rounded-full text-[11px] font-bold bg-error text-white">3</span>
            </a>
            <a href="#" class="text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal rounded-lg px-3 py-2.5 flex items-center gap-3 transition-colors">
                <span class="material-symbols-outlined">person</span>
                <span class="text-sm">Hồ sơ cá nhân</span>
            </a>
            <a href="#" class="text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal rounded-lg px-3 py-2.5 flex items-center gap-3 transition-colors">
                <span class="material-symbols-outlined">settings</span>
                <span class="text-sm">Cài đặt</span>
            </a>
        </div>

        <!-- Footer Student Profile -->
        <div class="p-3.5 border-t border-[#E2E8F0] bg-white flex items-center justify-between">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-9 h-9 rounded-full bg-primary-fixed flex items-center justify-center text-primary font-bold border border-outline-variant/60 shadow-xs shrink-0">
                    <span class="text-xs">NA</span>
                </div>
                <div class="flex flex-col min-w-0">
                    <div class="text-xs font-semibold text-on-surface truncate">Nguyễn Văn An</div>
                    <div class="text-[11px] text-on-surface-variant truncate">2001210123 • 12DHTT01</div>
                </div>
            </div>
            <button title="Đăng xuất" class="p-1 text-on-surface-variant hover:text-error transition-colors rounded-lg">
                <span class="material-symbols-outlined text-[20px]">logout</span>
            </button>
        </div>
    </aside>

    <!-- Workspace Shell -->
    <div class="flex-1 flex flex-col ml-64 min-w-0 min-h-screen">
        <!-- Top Navigation Bar -->
        <header class="sticky top-0 z-30 h-14 bg-white border-b border-[#E2E8F0] px-8 flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-4">
                <nav class="flex items-center gap-2 text-xs text-on-surface-variant">
                    <span class="text-on-surface-variant">Trang chủ</span>
                    <span class="material-symbols-outlined text-[14px] text-outline-variant">chevron_right</span>
                    <span class="text-on-surface font-semibold">Cổng thông tin Sinh viên</span>
                </nav>
                <div class="h-4 w-px bg-outline-variant/60 hidden sm:block"></div>
                <div class="hidden md:inline-flex items-center gap-1.5 bg-[#EEF2FD] text-primary font-semibold px-2.5 py-1 rounded-full text-xs border border-primary/20">
                    <span class="material-symbols-outlined text-[15px]">calendar_today</span>
                    <span>Học kỳ 1 • Năm học 2024-2025</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="relative hidden sm:block">
                    <input type="text" placeholder="Tìm kiếm nhanh..." class="h-8 text-xs pl-8 pr-3 bg-surface-container-low/50 border border-outline-variant/60 rounded-lg text-on-surface placeholder:text-outline focus:outline-none focus:ring-2 focus:ring-primary/20 w-48">
                    <span class="material-symbols-outlined absolute left-2 top-2 text-[15px] text-outline">search</span>
                </div>
                <button title="Thông báo" class="relative p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-low transition-colors">
                    <span class="material-symbols-outlined text-[20px]">notifications</span>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-error rounded-full ring-2 ring-white"></span>
                </button>
                <button title="Trợ giúp" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-low transition-colors hidden sm:block">
                    <span class="material-symbols-outlined text-[20px]">help</span>
                </button>
                <div class="h-5 w-px bg-outline-variant/60 mx-0.5"></div>
                <div class="w-8 h-8 rounded-full bg-primary-fixed flex items-center justify-center text-primary font-bold border border-outline-variant/60 shadow-xs">
                    <span class="text-xs">NA</span>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 p-8 w-full bg-[#F7F9FC]">
            <div class="max-w-[1120px] w-full mx-auto space-y-6">
                <!-- Welcome Banner Card -->
                <div class="bg-white border border-[#E2E8F0] rounded-xl p-6 shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-5 relative overflow-hidden">
                    <div class="space-y-1.5 relative z-10">
                        <h1 class="text-[30px] font-bold text-on-surface tracking-tight leading-tight">
                            Xin chào, Nguyễn Văn An 👋
                        </h1>
                        <p class="text-xs text-on-surface-variant">
                            MSSV: <strong class="text-primary font-semibold">2001210123</strong> • Ngành: <strong class="text-on-surface">Công nghệ Thông tin</strong> • Khóa: <strong>12 (2021–2025)</strong> • Lớp: <strong>12DHTT01</strong>
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2.5 relative z-10 shrink-0">
                        <button class="bg-primary hover:bg-primary-container text-white text-xs font-semibold px-4 py-2.5 rounded-lg flex items-center gap-1.5 shadow-sm transition-all active:scale-95">
                            <span class="material-symbols-outlined text-[16px]">add</span>
                            <span>Đăng ký học phần HK1</span>
                        </button>
                        <button class="bg-white border border-outline-variant hover:bg-surface-container-low text-on-surface text-xs font-semibold px-3.5 py-2.5 rounded-lg flex items-center gap-1.5 shadow-xs transition-colors">
                            <span class="material-symbols-outlined text-primary text-[16px]">event_note</span>
                            <span>Xem lịch thi HK1</span>
                        </button>
                        <button class="bg-white border border-outline-variant hover:bg-surface-container-low text-on-surface text-xs font-semibold px-3.5 py-2.5 rounded-lg flex items-center gap-1.5 shadow-xs transition-colors">
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
                                <span class="text-[22px] font-bold text-on-surface leading-tight">84</span>
                                <span class="text-xs text-on-surface-variant font-medium">/ 132 TC</span>
                            </div>
                            <div class="w-full h-2 rounded-full bg-surface-container-high overflow-hidden">
                                <div class="h-full rounded-full bg-primary" style="width: 63.6%"></div>
                            </div>
                            <div class="text-[11px] text-on-surface-variant flex items-center justify-between pt-0.5">
                                <span>Đã tích lũy: <strong class="text-primary">63.6%</strong></span>
                                <span>Còn lại: <strong>48 TC</strong></span>
                            </div>
                        </div>
                    </div>

                    <!-- Stat 2: GPA -->
                    <div class="bg-white border border-[#E2E8F0] rounded-xl p-5 shadow-xs flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-on-surface-variant font-medium">Điểm trung bình (GPA)</span>
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">
                                Xếp loại: Giỏi
                            </span>
                        </div>
                        <div class="space-y-1.5">
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-[22px] font-bold text-primary leading-tight">3.42</span>
                                <span class="text-xs text-on-surface-variant font-medium">/ 4.0</span>
                            </div>
                            <div class="text-[11px] text-on-surface-variant pt-1 border-t border-[#F1F5F9] flex items-center justify-between">
                                <span>ĐTB hệ 10: <strong class="text-on-surface">8.35</strong></span>
                                <span class="text-emerald-700 font-medium flex items-center gap-0.5">
                                    <span class="material-symbols-outlined text-[14px]">trending_up</span> +0.08
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Stat 3: Lớp học HK1 -->
                    <div class="bg-white border border-[#E2E8F0] rounded-xl p-5 shadow-xs flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-on-surface-variant font-medium">Lớp học HK1</span>
                            <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-[16px]">menu_book</span>
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-[22px] font-bold text-on-surface leading-tight">6</span>
                                <span class="text-xs text-on-surface-variant font-medium">Học phần</span>
                            </div>
                            <div class="text-[11px] text-on-surface-variant pt-1 border-t border-[#F1F5F9] flex items-center justify-between">
                                <span>18 tín chỉ</span>
                                <span class="font-medium text-primary">Đang theo học</span>
                            </div>
                        </div>
                    </div>

                    <!-- Stat 4: Học phí -->
                    <div class="bg-white border border-[#E2E8F0] rounded-xl p-5 shadow-xs flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-on-surface-variant font-medium">Tình trạng học phí</span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Đã hoàn tất
                            </span>
                        </div>
                        <div class="space-y-1.5">
                            <div class="flex items-baseline gap-1">
                                <span class="text-[18px] font-bold text-emerald-700 leading-tight">0 VNĐ</span>
                                <span class="text-xs text-on-surface-variant font-normal">(Không nợ)</span>
                            </div>
                            <div class="text-[11px] text-on-surface-variant pt-1 border-t border-[#F1F5F9] flex items-center justify-between">
                                <a href="#" class="text-primary hover:underline flex items-center gap-0.5 font-medium">
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
                                    <h2 class="text-[20px] font-bold text-on-surface leading-tight">Thời khóa biểu hôm nay (Thứ Tư, 07/10/2026)</h2>
                                </div>
                                <a href="#" class="text-xs text-primary hover:underline flex items-center gap-0.5">
                                    Xem toàn bộ tuần →
                                </a>
                            </div>

                            <div class="space-y-3">
                                <!-- Class 1 -->
                                <div class="p-4 rounded-xl border border-primary/30 bg-[#EEF2FD]/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 rounded text-xs font-bold bg-primary text-white">07:30 - 09:10</span>
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">Đang diễn ra</span>
                                        </div>
                                        <h3 class="text-sm font-bold text-on-surface">Lập trình Web nâng cao</h3>
                                        <div class="flex flex-wrap items-center gap-x-3 text-xs text-on-surface-variant">
                                            <span>Phòng: <strong class="text-primary font-semibold">A3.04</strong></span>
                                            <span>•</span>
                                            <span>Giảng viên: <strong class="text-on-surface">TS. Trần Hoàng Nam</strong></span>
                                        </div>
                                    </div>
                                    <button class="self-start sm:self-center px-3 py-1.5 rounded-lg border border-primary/30 bg-white text-primary text-xs font-semibold hover:bg-surface-container-low transition-colors shadow-xs">
                                        Tài liệu môn học
                                    </button>
                                </div>

                                <!-- Class 2 -->
                                <div class="p-4 rounded-xl border border-outline-variant/60 bg-surface-container-low/30 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 rounded text-xs font-bold bg-secondary-container text-on-secondary-fixed">09:30 - 11:10</span>
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800">Sắp bắt đầu</span>
                                        </div>
                                        <h3 class="text-sm font-bold text-on-surface">Cơ sở dữ liệu phân tán</h3>
                                        <div class="flex flex-wrap items-center gap-x-3 text-xs text-on-surface-variant">
                                            <span>Phòng: <strong class="text-on-surface font-semibold">B1.02</strong></span>
                                            <span>•</span>
                                            <span>Giảng viên: <strong class="text-on-surface">ThS. Lê Thị Bình</strong></span>
                                        </div>
                                    </div>
                                    <button class="self-start sm:self-center px-3 py-1.5 rounded-lg border border-outline-variant/60 bg-white text-on-surface text-xs font-semibold hover:bg-surface-container-low transition-colors shadow-xs">
                                        Xem đề cương
                                    </button>
                                </div>

                                <!-- Class 3 -->
                                <div class="p-4 rounded-xl border border-outline-variant/60 bg-surface-container-low/30 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 rounded text-xs font-bold bg-surface-container-high text-on-surface-variant">13:00 - 15:35</span>
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-surface-container-high text-on-surface-variant">Chiều nay • 3 Tiết</span>
                                        </div>
                                        <h3 class="text-sm font-bold text-on-surface">Thực hành Lập trình Web</h3>
                                        <div class="flex flex-wrap items-center gap-x-3 text-xs text-on-surface-variant">
                                            <span>Phòng máy: <strong class="text-primary font-semibold">PM04</strong></span>
                                            <span>•</span>
                                            <span>Giảng viên: <strong class="text-on-surface">TS. Trần Hoàng Nam</strong></span>
                                        </div>
                                    </div>
                                    <button class="self-start sm:self-center px-3 py-1.5 rounded-lg border border-outline-variant/60 bg-white text-on-surface text-xs font-semibold hover:bg-surface-container-low transition-colors shadow-xs">
                                        Vào phòng học
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Tiến độ Chương trình đào tạo -->
                        <div class="bg-white border border-[#E2E8F0] rounded-xl shadow-xs p-6 space-y-4">
                            <div class="flex items-center justify-between border-b border-[#F1F5F9] pb-3">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary text-[22px]">trending_up</span>
                                    <h2 class="text-[18px] font-bold text-on-surface">Tiến độ Chương trình đào tạo (84 / 132 TC - 63.6%)</h2>
                                </div>
                                <a href="#" class="text-xs text-primary hover:underline flex items-center gap-0.5">
                                    Xem khung chương trình →
                                </a>
                            </div>

                            <div class="space-y-2">
                                <div class="w-full h-3 rounded-full bg-[#E2E8F0] overflow-hidden flex">
                                    <div class="h-full bg-primary" style="width: 63.6%" title="Đã tích lũy: 63.6%"></div>
                                    <div class="h-full bg-primary-container" style="width: 13.6%" title="Đang học HK1: 13.6%"></div>
                                    <div class="h-full bg-outline-variant/60" style="width: 22.8%" title="Còn lại: 22.8%"></div>
                                </div>
                                <div class="flex flex-wrap items-center justify-between text-xs text-on-surface-variant pt-1">
                                    <span class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-primary"></span> Đã tích lũy: <strong class="text-on-surface">84 TC (63.6%)</strong>
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-primary-container"></span> Đang học HK1: <strong class="text-on-surface">18 TC (13.6%)</strong>
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-outline-variant"></span> Còn lại: <strong class="text-on-surface">30 TC (22.8%)</strong>
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                                <div class="p-3 rounded-lg border border-[#E2E8F0] bg-surface-container-low/30 space-y-1">
                                    <div class="flex justify-between text-xs font-semibold text-on-surface">
                                        <span>Đại cương & Cơ sở nhóm ngành</span>
                                        <span class="text-emerald-700">100%</span>
                                    </div>
                                    <div class="w-full h-1.5 rounded-full bg-surface-container-high overflow-hidden">
                                        <div class="h-full bg-emerald-500 rounded-full" style="width: 100%"></div>
                                    </div>
                                    <div class="text-[11px] text-on-surface-variant">42 / 42 Tín chỉ hoàn thành</div>
                                </div>
                                <div class="p-3 rounded-lg border border-[#E2E8F0] bg-surface-container-low/30 space-y-1">
                                    <div class="flex justify-between text-xs font-semibold text-on-surface">
                                        <span>Cơ sở ngành</span>
                                        <span class="text-primary">84.2%</span>
                                    </div>
                                    <div class="w-full h-1.5 rounded-full bg-surface-container-high overflow-hidden">
                                        <div class="h-full bg-primary rounded-full" style="width: 84.2%"></div>
                                    </div>
                                    <div class="text-[11px] text-on-surface-variant">32 / 38 Tín chỉ hoàn thành</div>
                                </div>
                                <div class="p-3 rounded-lg border border-[#E2E8F0] bg-surface-container-low/30 space-y-1">
                                    <div class="flex justify-between text-xs font-semibold text-on-surface">
                                        <span>Chuyên ngành bắt buộc & tự chọn</span>
                                        <span class="text-amber-600">52.1%</span>
                                    </div>
                                    <div class="w-full h-1.5 rounded-full bg-surface-container-high overflow-hidden">
                                        <div class="h-full bg-amber-500 rounded-full" style="width: 52.1%"></div>
                                    </div>
                                    <div class="text-[11px] text-on-surface-variant">25 / 48 Tín chỉ hoàn thành</div>
                                </div>
                                <div class="p-3 rounded-lg border border-[#E2E8F0] bg-surface-container-low/30 space-y-1">
                                    <div class="flex justify-between text-xs font-semibold text-on-surface">
                                        <span>Khóa luận / Đồ án tốt nghiệp</span>
                                        <span class="text-outline">0%</span>
                                    </div>
                                    <div class="w-full h-1.5 rounded-full bg-surface-container-high overflow-hidden">
                                        <div class="h-full bg-outline rounded-full" style="width: 0%"></div>
                                    </div>
                                    <div class="text-[11px] text-on-surface-variant">0 / 10 Tín chỉ (Chưa đăng ký)</div>
                                </div>
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
                                <a href="#" class="text-xs text-primary hover:underline">Bảng điểm đầy đủ</a>
                            </div>
                            <div class="space-y-2.5 text-xs">
                                <div class="p-2.5 rounded-lg border border-[#E2E8F0] bg-surface-container-low/30 flex items-center justify-between">
                                    <div>
                                        <div class="font-semibold text-on-surface">Lập trình Web API</div>
                                        <div class="text-[11px] text-on-surface-variant">3 tín chỉ • HK trước</div>
                                    </div>
                                    <div class="text-right">
                                        <span class="px-2 py-0.5 rounded font-bold text-emerald-700 bg-emerald-50 border border-emerald-200">9.5 (A+)</span>
                                    </div>
                                </div>
                                <div class="p-2.5 rounded-lg border border-[#E2E8F0] bg-surface-container-low/30 flex items-center justify-between">
                                    <div>
                                        <div class="font-semibold text-on-surface">Hệ quản trị CSDL SQL</div>
                                        <div class="text-[11px] text-on-surface-variant">3 tín chỉ • HK trước</div>
                                    </div>
                                    <div class="text-right">
                                        <span class="px-2 py-0.5 rounded font-bold text-emerald-700 bg-emerald-50 border border-emerald-200">8.8 (A)</span>
                                    </div>
                                </div>
                                <div class="p-2.5 rounded-lg border border-[#E2E8F0] bg-surface-container-low/30 flex items-center justify-between">
                                    <div>
                                        <div class="font-semibold text-on-surface">Hệ điều hành máy tính</div>
                                        <div class="text-[11px] text-on-surface-variant">3 tín chỉ • HK trước</div>
                                    </div>
                                    <div class="text-right">
                                        <span class="px-2 py-0.5 rounded font-bold text-blue-800 bg-blue-100">7.8 (B+)</span>
                                    </div>
                                </div>
                                <div class="p-2.5 rounded-lg border border-[#E2E8F0] bg-surface-container-low/30 flex items-center justify-between">
                                    <div>
                                        <div class="font-semibold text-on-surface">Kiến trúc máy tính</div>
                                        <div class="text-[11px] text-on-surface-variant">3 tín chỉ • HK trước</div>
                                    </div>
                                    <div class="text-right">
                                        <span class="px-2 py-0.5 rounded font-bold text-emerald-700 bg-emerald-50 border border-emerald-200">8.5 (A)</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Thông báo học vụ mới nhất -->
                        <div class="bg-white border border-[#E2E8F0] rounded-xl shadow-xs p-5 space-y-4">
                            <div class="flex items-center justify-between border-b border-[#F1F5F9] pb-3">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary text-[20px]">campaign</span>
                                    <h3 class="text-sm font-bold text-on-surface">Thông báo học vụ mới nhất</h3>
                                </div>
                                <a href="#" class="text-xs text-primary hover:underline">Tất cả</a>
                            </div>
                            <div class="space-y-3">
                                <a href="#" class="block p-2.5 rounded-lg border border-[#E2E8F0] hover:bg-surface-container-low transition-colors space-y-1">
                                    <div class="flex items-center justify-between text-[10px]">
                                        <span class="px-1.5 py-0.5 rounded font-bold bg-[#EEF2FD] text-primary">PHÒNG ĐÀO TẠO</span>
                                        <span class="text-on-surface-variant">10:30 Hôm nay</span>
                                    </div>
                                    <h4 class="text-xs font-semibold text-on-surface hover:text-primary leading-snug line-clamp-2">
                                        Kế hoạch thi giữa kỳ Học kỳ 1 năm học 2024-2025
                                    </h4>
                                </a>
                                <a href="#" class="block p-2.5 rounded-lg border border-[#E2E8F0] hover:bg-surface-container-low transition-colors space-y-1">
                                    <div class="flex items-center justify-between text-[10px]">
                                        <span class="px-1.5 py-0.5 rounded font-bold bg-amber-200/70 text-amber-900">CỔNG ĐĂNG KÝ</span>
                                        <span class="text-on-surface-variant">Hôm qua</span>
                                    </div>
                                    <h4 class="text-xs font-semibold text-on-surface hover:text-primary leading-snug line-clamp-2">
                                        Mở cổng đăng ký học phần bổ sung từ ngày 10/10 đến 18/10
                                    </h4>
                                </a>
                                <a href="#" class="block p-2.5 rounded-lg border border-[#E2E8F0] hover:bg-surface-container-low transition-colors space-y-1">
                                    <div class="flex items-center justify-between text-[10px]">
                                        <span class="px-1.5 py-0.5 rounded font-bold bg-emerald-100 text-emerald-800">PHÒNG CTSV</span>
                                        <span class="text-on-surface-variant">05/10/2026</span>
                                    </div>
                                    <h4 class="text-xs font-semibold text-on-surface hover:text-primary leading-snug line-clamp-2">
                                        Danh sách sinh viên nhận học bổng khuyến khích học tập
                                    </h4>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="mt-auto py-4 px-8 border-t border-[#E2E8F0] text-center text-xs text-on-surface-variant bg-white flex flex-col sm:flex-row items-center justify-between gap-2">
            <div>
                © 2024 <strong>Trường Đại học Công Thương TP. Hồ Chí Minh (HUIT)</strong> - Hệ thống Quản lý Học vụ.
            </div>
            <div class="flex items-center gap-4 text-xs">
                <a href="#" class="hover:text-primary transition-colors">Quy chế đào tạo tín chỉ</a>
                <span>•</span>
                <a href="#" class="hover:text-primary transition-colors">Hướng dẫn sinh viên</a>
                <span>•</span>
                <a href="#" class="hover:text-primary transition-colors">Chính sách bảo mật</a>
            </div>
        </footer>
    </div>
</div>
@endsection