@extends('layouts.app')

@section('title', 'Cổng Giảng viên - Trường Đại học Công Thương TP. Hồ Chí Minh')

@section('content')
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside id="app-sidebar" class="w-64 h-screen bg-white border-r border-[#E2E8F0] shadow-sm fixed left-0 top-0 z-40 -translate-x-full md:translate-x-0 transition-transform duration-200 flex flex-col justify-between overflow-y-auto">
        <div class="p-5">
            <div class="flex items-center gap-3 px-1 py-1 mb-6">
                <div class="w-10 h-10 rounded-xl bg-[#4776E6] text-white flex items-center justify-center font-bold tracking-wider text-base shadow-sm shrink-0">
                    HUIT
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="font-bold text-[15px] leading-5 text-slate-800 tracking-tight truncate">Trường ĐH Công Thương</span>
                    <span class="text-[12px] leading-4 text-slate-500 truncate">Cổng Giảng viên • QL Học vụ</span>
                </div>
            </div>

            <nav class="flex flex-col gap-1">
                <a href="{{ route('teacher.home') }}" class="bg-[#EEF2FD] text-[#4776E6] font-semibold rounded-lg px-3.5 py-2.5 flex items-center gap-3 border-l-[3px] border-[#4776E6] transition-colors">
                    <span class="material-symbols-outlined text-[20px]">dashboard</span>
                    <span class="text-[14px]">Tổng quan</span>
                </a>
                <a href="#" class="text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium rounded-lg px-3.5 py-2.5 flex items-center gap-3 transition-colors">
                    <span class="material-symbols-outlined text-[20px] text-slate-400">school</span>
                    <span class="text-[14px]">Học phần phụ trách</span>
                </a>
                <a href="#" class="text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium rounded-lg px-3.5 py-2.5 flex items-center gap-3 transition-colors">
                    <span class="material-symbols-outlined text-[20px] text-slate-400">menu_book</span>
                    <span class="text-[14px]">Đề cương học phần</span>
                </a>
                <a href="#" class="text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium rounded-lg px-3.5 py-2.5 flex items-center gap-3 transition-colors">
                    <span class="material-symbols-outlined text-[20px] text-slate-400">calendar_month</span>
                    <span class="text-[14px]">Lịch giảng dạy</span>
                </a>
                <a href="#" class="text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium rounded-lg px-3.5 py-2.5 flex items-center gap-3 transition-colors">
                    <span class="material-symbols-outlined text-[20px] text-slate-400">event_busy</span>
                    <span class="text-[14px]">Lịch bận</span>
                </a>
                <a href="#" class="text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium rounded-lg px-3.5 py-2.5 flex items-center gap-3 transition-colors">
                    <span class="material-symbols-outlined text-[20px] text-slate-400">grade</span>
                    <span class="text-[14px]">Điểm</span>
                </a>
                <a href="#" class="text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium rounded-lg px-3.5 py-2.5 flex items-center justify-between transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px] text-slate-400">notifications</span>
                        <span class="text-[14px]">Thông báo</span>
                    </div>
                    <span class="bg-red-500 text-white text-[11px] font-semibold px-1.5 py-0.5 rounded-full">3</span>
                </a>
                <a href="#" class="text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium rounded-lg px-3.5 py-2.5 flex items-center gap-3 transition-colors">
                    <span class="material-symbols-outlined text-[20px] text-slate-400">badge</span>
                    <span class="text-[14px]">Hồ sơ cá nhân</span>
                </a>
            </nav>
        </div>

        <div class="p-4 border-t border-slate-200 flex flex-col gap-3 bg-slate-50/50">
            <div class="p-2 bg-white rounded-lg border border-slate-200 flex items-center justify-between text-slate-600 shadow-sm">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[12px] font-medium">Cổng học vụ trực tuyến</span>
                </div>
                <span class="text-[11px] text-slate-400">v2.6</span>
            </div>
            <div class="flex items-center justify-between pt-1">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-[#4776E6] text-white font-semibold flex items-center justify-center text-xs shrink-0 shadow-sm">
                        TN
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span class="text-[13px] font-semibold text-slate-800 truncate">TS. Trần Hoàng Nam</span>
                        <span class="text-[11px] text-slate-500 truncate">Khoa CNTT (Giảng viên)</span>
                    </div>
                </div>
                @include('partials.logout-form', ['class' => 'text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-200 transition-colors', 'icon' => 'text-[18px]'])
            </div>
        </div>
    </aside>

    <!-- Workspace -->
    <div class="flex-1 md:ml-64 min-w-0 flex flex-col min-h-screen">
        <!-- Topbar -->
        <header class="h-14 bg-white border-b border-[#E2E8F0] sticky top-0 z-30 px-4 md:px-8 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3 md:gap-4 min-w-0">
                @include('partials.menu-button')
                <nav class="flex items-center gap-1.5 text-xs text-slate-500 min-w-0">
                    <a href="#" class="hidden sm:flex hover:text-[#4776E6] transition-colors items-center gap-1 font-medium">
                        <span class="material-symbols-outlined text-sm">home</span>
                        Trang chủ
                    </a>
                    <span class="hidden sm:inline text-slate-300">/</span>
                    <span class="text-slate-800 font-semibold truncate">Cổng Giảng viên</span>
                </nav>
                <span class="hidden lg:inline text-slate-300">|</span>
<div class="hidden lg:flex items-center gap-2 bg-[#EEF2FD] text-[#4776E6]|</span>
                <div class="flex items-center gap-2 bg-[#EEF2FD] text-[#4776E6] px-3 py-1 rounded-full border border-blue-100">
                    <span class="material-symbols-outlined text-base">date_range</span>
                    <span class="text-xs font-semibold">Học kỳ 1 • 2024–2025</span>
                    <button type="button" title="Chuyển học kỳ" class="flex items-center text-[#4776E6] hover:opacity-80 transition-opacity">
                        <span class="material-symbols-outlined text-sm">expand_more</span>
                    </button>
                </div>
                <div class="text-slate-600 text-xs hidden xl:flex items-center gap-1 font-medium">
                    <span class="material-symbols-outlined text-sm text-slate-400">domain</span>
                    Khoa Công nghệ Thông tin
                </div>
            </div>

            <div class="flex items-center gap-2 md:gap-3.5 shrink-0">
                <button title="Thông báo hệ thống" aria-label="Thông báo hệ thống" class="relative p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 transition-colors">
                    <span class="material-symbols-outlined text-xl">notifications</span>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                </button>
                <button title="Trợ giúp" class="hidden sm:block p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 transition-colors">
                    <span class="material-symbols-outlined text-xl">help_outline</span>
                </button>
                <div class="h-5 w-px bg-slate-200"></div>
                <div class="flex items-center gap-2.5 pl-1">
                    <div class="w-8 h-8 rounded-full bg-[#4776E6] text-white font-semibold flex items-center justify-center text-xs shadow-sm">
                        TN
                    </div>
                    <span class="hidden md:inline text-xs font-semibold text-slate-800">TS. Trần Hoàng Nam</span>
                </div>
            </div>
        </header>

        <!-- Canvas -->
        <main class="flex-1 p-4 md:p-8 w-full bg-[#F7F9FC]">
            <div class="max-w-[1120px] mx-auto space-y-7">
                <!-- Header -->
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-1 border-b border-slate-200/60">
                    <div>
                        <h1 class="text-[28px] font-bold text-[#1E293B] tracking-tight flex items-center gap-2">
                            Xin chào, TS. Trần Hoàng Nam <span>👋</span>
                        </h1>
                        <div class="flex items-center gap-2 mt-1 text-sm text-slate-500 font-normal">
                            <span class="material-symbols-outlined text-base text-[#4776E6]">calendar_today</span>
                            <span>Thứ Năm, 15/10/2026</span>
                            <span class="text-slate-300">•</span>
                            <span class="inline-flex items-center gap-1.5 font-semibold text-[#4776E6]">
                                <span class="w-2 h-2 rounded-full bg-[#4776E6] animate-ping inline-block"></span>
                                3 ca dạy đang chờ
                            </span>
                            <span class="text-slate-300">•</span>
                            <span>Học kỳ 1 (2024–2025)</span>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 lg:shrink-0">
                        <button class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-lg shadow-sm transition-all active:scale-95">
                            <span class="material-symbols-outlined text-lg text-slate-500">how_to_reg</span>
                            Điểm danh nhanh
                        </button>
                        <button class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#4776E6] hover:bg-[#3b63c4] text-white text-sm font-semibold rounded-lg shadow-sm transition-all active:scale-95">
                            <span class="material-symbols-outlined text-lg">edit_note</span>
                            Nhập điểm học phần
                        </button>
                    </div>
                </div>

                <!-- Alert Panel -->
                <div class="bg-[#FFFBEB] border border-amber-200 rounded-xl p-5 shadow-sm">
                    <div class="flex items-center justify-between pb-3 border-b border-amber-200/80 mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm">
                                <span class="material-symbols-outlined text-base">priority_high</span>
                            </div>
                            <h2 class="text-base font-bold text-slate-800">Công việc học vụ cần xử lý</h2>
                            <span class="bg-amber-100 text-amber-800 text-xs font-semibold px-2 py-0.5 rounded-full border border-amber-300/50">3 việc cần xử lý</span>
                        </div>
                        <a href="#" class="text-xs font-semibold text-[#4776E6] hover:underline flex items-center gap-1">
                            Xử lý tất cả
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="bg-white rounded-lg p-3 border border-amber-200/80 flex items-start justify-between gap-2 shadow-sm">
                            <div class="flex flex-col">
                                <span class="text-xs font-semibold text-slate-800 line-clamp-1">Nộp điểm cuối kỳ HP Lập trình Java</span>
                                <span class="text-[11px] text-slate-500 mt-0.5">Deadline: Hôm nay 15/10/2026</span>
                            </div>
                            <span class="bg-red-100 text-red-700 text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0">Khẩn cấp</span>
                        </div>
                        <div class="bg-white rounded-lg p-3 border border-amber-200/80 flex items-start justify-between gap-2 shadow-sm">
                            <div class="flex flex-col">
                                <span class="text-xs font-semibold text-slate-800 line-clamp-1">Phê duyệt đơn xin nghỉ học của SV</span>
                                <span class="text-[11px] text-slate-500 mt-0.5">3 đơn đang chờ duyệt</span>
                            </div>
                            <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0">Cần xử lý</span>
                        </div>
                        <div class="bg-white rounded-lg p-3 border border-amber-200/80 flex items-start justify-between gap-2 shadow-sm">
                            <div class="flex flex-col">
                                <span class="text-xs font-semibold text-slate-800 line-clamp-1">Cập nhật đề cương Công nghệ phần mềm</span>
                                <span class="text-[11px] text-slate-500 mt-0.5">Thời hạn: 20/10/2026</span>
                            </div>
                            <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0">Cần xử lý</span>
                        </div>
                    </div>
                </div>

                <!-- Lịch giảng dạy hôm nay -->
                <section class="space-y-3.5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[#4776E6] text-xl">calendar_today</span>
                            <h2 class="text-[20px] font-bold text-[#1E293B]">Lịch giảng dạy hôm nay</h2>
                            <span class="text-xs font-semibold bg-slate-200 text-slate-700 px-2 py-0.5 rounded-full">15/10/2026</span>
                        </div>
                        <a href="#" class="text-xs font-semibold text-[#4776E6] hover:underline flex items-center gap-0.5">
                            Xem toàn bộ thời khóa biểu
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <!-- Ca 1 -->
                        <div class="bg-white border-2 border-emerald-400 rounded-xl p-5 shadow-sm relative flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-semibold text-[#4776E6] flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm">schedule</span>
                                        Ca 1: 07:30 – 09:10 (Tiết 1 - 4)
                                    </span>
                                    <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-800 text-[11px] font-bold px-2 py-0.5 rounded-full border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                        Đang diễn ra
                                    </span>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 mt-1 truncate">Lập trình hướng đối tượng</h3>
                                <div class="mt-3.5 space-y-1.5 text-xs text-slate-600 bg-slate-50 p-3 rounded-lg border border-slate-200">
                                    <div class="flex justify-between">
                                        <span class="text-slate-400">Mã HP:</span>
                                        <span class="font-medium text-slate-800">IT302</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-400">Lớp học:</span>
                                        <span class="font-medium text-slate-800">D22CNPM01</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-400">Phòng:</span>
                                        <span class="font-medium text-slate-800">B.204 (Tân Phú)</span>
                                    </div>
                                    <div class="flex justify-between pt-1 border-t border-slate-200/60">
                                        <span class="text-slate-400">Sĩ số:</span>
                                        <span class="font-bold text-emerald-600">42/42 SV</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-[11px] text-slate-500">Đã điểm danh 40/42</span>
                                <button class="px-3 py-1.5 bg-[#4776E6] hover:bg-[#3b63c4] text-white text-xs font-semibold rounded-lg shadow-sm transition-all active:scale-95 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">fact_check</span>
                                    Điểm danh
                                </button>
                            </div>
                        </div>

                        <!-- Ca 2 -->
                        <div class="bg-white border border-[#E2E8F0] rounded-xl p-5 shadow-sm relative flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-semibold text-slate-600 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm text-slate-400">schedule</span>
                                        Ca 2: 09:30 – 11:10 (Tiết 5 - 8)
                                    </span>
                                    <span class="inline-flex items-center gap-1 bg-blue-100 text-[#4776E6] text-[11px] font-bold px-2 py-0.5 rounded-full border border-blue-200">
                                        Sắp bắt đầu
                                    </span>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 mt-1 truncate">Cơ sở dữ liệu</h3>
                                <div class="mt-3.5 space-y-1.5 text-xs text-slate-600 bg-slate-50 p-3 rounded-lg border border-slate-200">
                                    <div class="flex justify-between">
                                        <span class="text-slate-400">Mã HP:</span>
                                        <span class="font-medium text-slate-800">IT205</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-400">Lớp học:</span>
                                        <span class="font-medium text-slate-800">D22HTTT02</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-400">Phòng:</span>
                                        <span class="font-medium text-slate-800">A.301</span>
                                    </div>
                                    <div class="flex justify-between pt-1 border-t border-slate-200/60">
                                        <span class="text-slate-400">Sĩ số:</span>
                                        <span class="font-semibold text-slate-800">38/40 SV</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-[11px] text-slate-500">Phòng máy sẵn sàng</span>
                                <button class="px-3 py-1.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-lg transition-colors">
                                    Xem danh sách
                                </button>
                            </div>
                        </div>

                        <!-- Ca 3 -->
                        <div class="bg-white border border-[#E2E8F0] rounded-xl p-5 shadow-sm relative flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-semibold text-slate-600 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm text-slate-400">schedule</span>
                                        Ca 3: 13:00 – 14:40 (Tiết 9 - 12)
                                    </span>
                                    <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-600 text-[11px] font-bold px-2 py-0.5 rounded-full border border-slate-200">
                                        Chưa bắt đầu
                                    </span>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 mt-1 truncate">Nhập môn CNTT</h3>
                                <div class="mt-3.5 space-y-1.5 text-xs text-slate-600 bg-slate-50 p-3 rounded-lg border border-slate-200">
                                    <div class="flex justify-between">
                                        <span class="text-slate-400">Mã HP:</span>
                                        <span class="font-medium text-slate-800">IT101</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-400">Lớp học:</span>
                                        <span class="font-medium text-slate-800">D23CNTT03</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-400">Phòng:</span>
                                        <span class="font-medium text-slate-800">C.105</span>
                                    </div>
                                    <div class="flex justify-between pt-1 border-t border-slate-200/60">
                                        <span class="text-slate-400">Sĩ số:</span>
                                        <span class="font-semibold text-slate-800">45/45 SV</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-[11px] text-slate-500">Buổi 8/15</span>
                                <button class="px-3 py-1.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-lg transition-colors">
                                    Xem danh sách
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Grid 8-4 -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    <!-- Học phần phụ trách (8 cols) -->
                    <section class="lg:col-span-8 bg-white border border-[#E2E8F0] rounded-xl p-5 shadow-sm space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[#4776E6] text-xl">school</span>
                                <h2 class="text-base font-bold text-[#1E293B]">Học phần & Lớp phụ trách (Học kỳ 1)</h2>
                            </div>
                            <span class="text-xs font-semibold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-full border border-slate-200">
                                4 học phần hoạt động
                            </span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- HP 1 -->
                            <div class="p-4 rounded-xl border border-slate-200 hover:border-[#4776E6]/60 transition-all bg-white shadow-sm flex flex-col justify-between">
                                <div>
                                    <div class="flex items-start justify-between gap-2">
                                        <span class="text-[11px] font-semibold text-[#4776E6] bg-blue-50 border border-blue-200 px-2 py-0.5 rounded">IT302 • 3 Tín chỉ</span>
                                        <span class="text-xs text-slate-500 font-medium">D22CNPM01</span>
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-900 mt-2">Lập trình hướng đối tượng</h3>
                                    <p class="text-xs text-slate-500 mt-0.5">42 sinh viên</p>
                                    <div class="mt-3">
                                        <div class="flex justify-between text-[11px] mb-1 text-slate-500">
                                            <span>Tiến độ giảng dạy</span>
                                            <span class="font-semibold text-slate-800">8/15 tuần (53%)</span>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-[#4776E6] h-1.5 rounded-full" style="width: 53.3%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                                    <a href="#" class="text-slate-600 hover:text-[#4776E6] font-medium transition-colors">Xem danh sách</a>
                                    <a href="#" class="text-[#4776E6] font-semibold hover:underline">Quản lý điểm</a>
                                </div>
                            </div>

                            <!-- HP 2 -->
                            <div class="p-4 rounded-xl border border-slate-200 hover:border-[#4776E6]/60 transition-all bg-white shadow-sm flex flex-col justify-between">
                                <div>
                                    <div class="flex items-start justify-between gap-2">
                                        <span class="text-[11px] font-semibold text-[#4776E6] bg-blue-50 border border-blue-200 px-2 py-0.5 rounded">IT205 • 3 Tín chỉ</span>
                                        <span class="text-xs text-slate-500 font-medium">D22HTTT02</span>
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-900 mt-2">Cơ sở dữ liệu</h3>
                                    <p class="text-xs text-slate-500 mt-0.5">38 sinh viên</p>
                                    <div class="mt-3">
                                        <div class="flex justify-between text-[11px] mb-1 text-slate-500">
                                            <span>Tiến độ giảng dạy</span>
                                            <span class="font-semibold text-slate-800">8/15 tuần (53%)</span>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-[#4776E6] h-1.5 rounded-full" style="width: 53.3%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                                    <a href="#" class="text-slate-600 hover:text-[#4776E6] font-medium transition-colors">Xem danh sách</a>
                                    <a href="#" class="text-[#4776E6] font-semibold hover:underline">Quản lý điểm</a>
                                </div>
                            </div>

                            <!-- HP 3 -->
                            <div class="p-4 rounded-xl border border-slate-200 hover:border-[#4776E6]/60 transition-all bg-white shadow-sm flex flex-col justify-between">
                                <div>
                                    <div class="flex items-start justify-between gap-2">
                                        <span class="text-[11px] font-semibold text-[#4776E6] bg-blue-50 border border-blue-200 px-2 py-0.5 rounded">IT101 • 2 Tín chỉ</span>
                                        <span class="text-xs text-slate-500 font-medium">D23CNTT03</span>
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-900 mt-2">Nhập môn CNTT</h3>
                                    <p class="text-xs text-slate-500 mt-0.5">45 sinh viên</p>
                                    <div class="mt-3">
                                        <div class="flex justify-between text-[11px] mb-1 text-slate-500">
                                            <span>Tiến độ giảng dạy</span>
                                            <span class="font-semibold text-slate-800">9/15 tuần (60%)</span>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-[#4776E6] h-1.5 rounded-full" style="width: 60%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                                    <a href="#" class="text-slate-600 hover:text-[#4776E6] font-medium transition-colors">Xem danh sách</a>
                                    <a href="#" class="text-[#4776E6] font-semibold hover:underline">Quản lý điểm</a>
                                </div>
                            </div>

                            <!-- HP 4 -->
                            <div class="p-4 rounded-xl border border-slate-200 hover:border-[#4776E6]/60 transition-all bg-white shadow-sm flex flex-col justify-between">
                                <div>
                                    <div class="flex items-start justify-between gap-2">
                                        <span class="text-[11px] font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 px-2 py-0.5 rounded">IT401 • 2 Tín chỉ</span>
                                        <span class="text-xs text-slate-500 font-medium">HĐ Khoa CNTT</span>
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-900 mt-2">Đồ án chuyên ngành CNTT</h3>
                                    <p class="text-xs text-slate-500 mt-0.5">12 nhóm SV • 28 sinh viên</p>
                                    <div class="mt-3">
                                        <div class="flex justify-between text-[11px] mb-1 text-slate-500">
                                            <span>Tiến độ nghiệm thu</span>
                                            <span class="font-semibold text-slate-800">Giai đoạn 2 (Báo cáo sơ bộ)</span>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-indigo-600 h-1.5 rounded-full" style="width: 45%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                                    <a href="#" class="text-slate-600 hover:text-[#4776E6] font-medium transition-colors">Danh sách đề tài</a>
                                    <a href="#" class="text-[#4776E6] font-semibold hover:underline">Đánh giá tiến độ</a>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Thông báo & Tài liệu (4 cols) -->
                    <div class="lg:col-span-4 space-y-5">
                        <div class="bg-white border border-[#E2E8F0] rounded-xl p-5 shadow-sm space-y-3.5">
                            <div class="flex items-center justify-between pb-2.5 border-b border-slate-200">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[#4776E6] text-xl">campaign</span>
                                    <h2 class="text-sm font-bold text-[#1E293B]">Thông báo Học vụ mới nhất</h2>
                                </div>
                                <a href="#" class="text-xs text-[#4776E6] hover:underline font-semibold">Xem tất cả</a>
                            </div>
                            <div class="space-y-2.5">
                                <div class="p-2.5 rounded-lg border-l-4 border-l-red-500 border border-slate-200 bg-red-50/30 hover:bg-red-50/60 transition-colors">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="bg-red-100 text-red-800 text-[10px] font-bold px-1.5 py-0.5 rounded">Khẩn</span>
                                        <span class="text-[11px] text-slate-400">14/10/2026</span>
                                    </div>
                                    <h4 class="text-xs font-semibold text-slate-900 hover:text-[#4776E6] cursor-pointer line-clamp-2">
                                        Hạn chót nhập điểm giữa kỳ đợt 1 năm học 2024-2025
                                    </h4>
                                </div>
                                <div class="p-2.5 rounded-lg border-l-4 border-l-blue-500 border border-slate-200 bg-blue-50/20 hover:bg-blue-50/50 transition-colors">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-1.5 py-0.5 rounded">Đào tạo</span>
                                        <span class="text-[11px] text-slate-400">12/10/2026</span>
                                    </div>
                                    <h4 class="text-xs font-semibold text-slate-900 hover:text-[#4776E6] cursor-pointer line-clamp-2">
                                        Kế hoạch đăng ký dạy bù và bảo trì phòng Lab
                                    </h4>
                                </div>
                                <div class="p-2.5 rounded-lg border-l-4 border-l-amber-500 border border-slate-200 bg-amber-50/20 hover:bg-amber-50/50 transition-colors">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-1.5 py-0.5 rounded">Khảo thí</span>
                                        <span class="text-[11px] text-slate-400">10/10/2026</span>
                                    </div>
                                    <h4 class="text-xs font-semibold text-slate-900 hover:text-[#4776E6] cursor-pointer line-clamp-2">
                                        Phân công coi thi và chấm thi đợt bổ sung
                                    </h4>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white border border-[#E2E8F0] rounded-xl p-5 shadow-sm">
                            <div class="flex items-center gap-2 pb-2.5 border-b border-slate-200 mb-3">
                                <span class="material-symbols-outlined text-slate-500 text-lg">folder_open</span>
                                <h2 class="text-sm font-bold text-[#1E293B]">Biểu mẫu & Quy định đào tạo</h2>
                            </div>
                            <div class="space-y-2 text-xs">
                                <a href="#" class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-colors">
                                    <div class="flex items-center gap-2 text-slate-700 hover:text-[#4776E6]">
                                        <span class="material-symbols-outlined text-base text-red-500">picture_as_pdf</span>
                                        <span class="font-medium">Mẫu đề cương chi tiết HP 2024</span>
                                    </div>
                                    <span class="text-[10px] font-semibold text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded">PDF</span>
                                </a>
                                <a href="#" class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-colors">
                                    <div class="flex items-center gap-2 text-slate-700 hover:text-[#4776E6]">
                                        <span class="material-symbols-outlined text-base text-blue-500">description</span>
                                        <span class="font-medium">Quy chế đào tạo tín chỉ sửa đổi</span>
                                    </div>
                                    <span class="text-[10px] font-semibold text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded">DOCX</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-[#E2E8F0] py-4 px-8 mt-auto flex">
            <div class="max-w-[1120px] w-full mx-auto flex flex-col md:flex-row items-center justify-between text-xs text-slate-500 gap-2">
                <div>© 2024 Trường Đại học Công Thương TP. Hồ Chí Minh (HUIT) - Hệ thống Quản lý Học vụ điện tử.</div>
                <div class="flex items-center gap-3 font-medium">
                    <span>Hỗ trợ kỹ thuật: 028 3816 1673</span>
                    <span class="text-slate-300">|</span>
                    <a href="mailto:daotao@huit.edu.vn" class="hover:text-[#4776E6] transition-colors">daotao@huit.edu.vn</a>
                </div>
            </div>
        </footer>
    </div>
</div>
@include('partials.sidebar-toggle')
@endsection
