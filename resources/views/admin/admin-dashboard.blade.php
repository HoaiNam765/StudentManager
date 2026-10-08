@extends('layouts.app')

@section('title', 'Tổng quan Quản lý Đào tạo - Hệ thống Quản lý Học vụ - HUIT')

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
                    <div class="font-bold text-[15px] text-on-surface leading-snug truncate">Trường ĐH Công Thương</div>
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
                <a href="{{ route('admin.home') }}" class="bg-[#EEF2FD] text-primary border-l-4 border-l-primary font-semibold rounded-lg px-3 py-2.5 flex items-center gap-3 transition-colors">
                    <span class="material-symbols-outlined text-primary text-[20px]" style="font-variation-settings: 'FILL' 1;">dashboard</span>
                    <span class="text-sm font-semibold">Tổng quan</span>
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
                    <span class="text-on-surface font-semibold truncate">Tổng quan Quản lý Đào tạo</span>
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
            <!-- Sync & Status Banner -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-xs text-on-surface-variant bg-surface-container-lowest px-3 py-1.5 rounded-lg border border-outline-variant/60 shadow-sm">
                    <span class="material-symbols-outlined text-[16px] text-primary">sync</span>
                    <span>Hệ thống cơ sở dữ liệu đồng bộ lúc: <strong class="text-on-surface font-semibold">10:45 • Hôm nay</strong></span>
                </div>
                <div class="flex items-center gap-2 text-xs text-on-surface-variant">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#DCFCE7] text-[#15803D] font-semibold text-xs">
                        <span class="w-2 h-2 rounded-full bg-[#15803D]"></span>
                        Cổng đăng ký đang mở (Đợt 2)
                    </span>
                </div>
            </div>

            <!-- Page Header Card -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/60 shadow-sm">
                <div>
                    <h1 class="text-[30px] font-bold text-on-surface tracking-tight leading-tight">Tổng quan Quản lý Đào tạo</h1>
                    <p class="text-sm text-on-surface-variant mt-1.5">Thống kê chỉ số đào tạo toàn trường, tiến độ mở lớp và các yêu cầu học vụ chờ xử lý.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3 lg:shrink-0">
                    <button class="h-10 px-4 bg-surface-container-lowest border border-outline-variant hover:bg-surface-container-low text-on-surface text-xs font-medium rounded-lg flex items-center gap-2 transition-colors active:scale-95 shadow-sm">
                        <span class="material-symbols-outlined text-[18px] text-on-surface-variant">download</span>
                        <span>Xuất báo cáo Thống kê</span>
                    </button>
                    <button class="h-10 px-5 bg-primary hover:bg-primary-container text-on-primary text-xs font-medium rounded-lg flex items-center gap-2 transition-colors active:scale-95 shadow-sm">
                        <span class="material-symbols-outlined text-[18px]">add_circle</span>
                        <span>+ Mở lớp học phần HK1</span>
                    </button>
                </div>
            </div>

            <!-- KPI Metric Cards (4 cards) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Card 1 -->
                <div class="bg-surface-container-lowest border border-outline-variant/60 rounded-xl p-5 shadow-sm hover:shadow transition-shadow">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-on-surface-variant font-medium">Tổng sinh viên</span>
                        <div class="w-9 h-9 rounded-full bg-[#EFF6FF] flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-[20px]">groups</span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-[30px] text-on-surface font-bold tracking-tight">18,450</div>
                        <div class="mt-1.5 flex items-center gap-1.5">
                            <span class="text-xs text-[#15803D] bg-[#DCFCE7] px-2 py-0.5 rounded font-semibold">+3.2% so với năm 2023</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="bg-surface-container-lowest border border-outline-variant/60 rounded-xl p-5 shadow-sm hover:shadow transition-shadow">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-on-surface-variant font-medium">Giảng viên cơ hữu</span>
                        <div class="w-9 h-9 rounded-full bg-[#EFF6FF] flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-[20px]">how_to_reg</span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-[30px] text-on-surface font-bold tracking-tight">680</div>
                        <div class="mt-1.5 flex items-center gap-1.5">
                            <span class="text-xs text-primary bg-[#EFF6FF] px-2 py-0.5 rounded font-semibold">98% đã phân công lịch dạy</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="bg-surface-container-lowest border border-outline-variant/60 rounded-xl p-5 shadow-sm hover:shadow transition-shadow">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-on-surface-variant font-medium">Lớp học phần HK1</span>
                        <div class="w-9 h-9 rounded-full bg-[#EFF6FF] flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-[20px]">menu_book</span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-[30px] text-on-surface font-bold tracking-tight">1,240</div>
                        <div class="mt-1.5 flex items-center gap-1.5">
                            <span class="text-xs text-[#15803D] bg-[#DCFCE7] px-2 py-0.5 rounded font-semibold">94% đã xếp phòng học</span>
                        </div>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="bg-surface-container-lowest border border-outline-variant/60 rounded-xl p-5 shadow-sm hover:shadow transition-shadow">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-on-surface-variant font-medium">Yêu cầu chờ duyệt</span>
                        <div class="w-9 h-9 rounded-full bg-[#FEF3C7] flex items-center justify-center text-[#d97706]">
                            <span class="material-symbols-outlined text-[20px]">error_outline</span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-[30px] text-on-surface font-bold tracking-tight">28</div>
                        <div class="mt-1.5 flex items-center gap-1.5">
                            <span class="text-xs text-[#B45309] bg-[#FEF3C7] px-2 py-0.5 rounded font-semibold">12 đơn cần xử lý gấp</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Two Column Main Grid (8 cols left + 4 cols right) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <!-- Left 8 cols: Yêu cầu & Đơn từ chờ phê duyệt -->
                <div class="lg:col-span-8 bg-surface-container-lowest border border-outline-variant/60 rounded-xl shadow-sm flex flex-col p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-surface-container">
                        <div class="flex items-center gap-3">
                            <h2 class="text-[20px] font-bold text-on-surface">Yêu cầu & Đơn từ chờ phê duyệt</h2>
                            <span class="text-xs font-semibold bg-[#FEF3C7] text-[#B45309] px-2.5 py-1 rounded-full shadow-sm">28 đơn chờ xử lý</span>
                        </div>
                        <button title="Làm mới danh sách" class="p-1.5 border border-outline-variant hover:bg-surface-container-low rounded-lg text-on-surface-variant self-start sm:self-auto transition-colors">
                            <span class="material-symbols-outlined text-[18px]">refresh</span>
                        </button>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="flex flex-wrap items-center gap-2 py-3.5 border-b border-surface-container">
                        <button class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-primary text-on-primary transition-colors shadow-sm">Tất cả (28)</button>
                        <button class="px-3 py-1.5 rounded-lg text-xs font-medium text-on-surface-variant hover:bg-surface-container-low transition-colors">Đăng ký học phần trễ (12)</button>
                        <button class="px-3 py-1.5 rounded-lg text-xs font-medium text-on-surface-variant hover:bg-surface-container-low transition-colors">Hoãn thi / Hoãn học (9)</button>
                        <button class="px-3 py-1.5 rounded-lg text-xs font-medium text-on-surface-variant hover:bg-surface-container-low transition-colors">Chuyển điểm / Miễn HP (7)</button>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto flex-1">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-surface-bright border-b border-surface-container text-outline text-[11px] uppercase font-semibold tracking-wider">
                                    <th class="py-3.5 px-3">Mã đơn</th>
                                    <th class="py-3.5 px-3">Sinh viên & MSSV</th>
                                    <th class="py-3.5 px-3">Khoa</th>
                                    <th class="py-3.5 px-3">Loại yêu cầu</th>
                                    <th class="py-3.5 px-3">Ngày gửi</th>
                                    <th class="py-3.5 px-3 text-center">Trạng thái</th>
                                    <th class="py-3.5 px-3 text-right">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-container text-xs">
                                <tr class="hover:bg-surface-container-low transition-colors">
                                    <td class="py-3.5 px-3 font-mono font-semibold text-primary">ĐH-2024-0891</td>
                                    <td class="py-3.5 px-3">
                                        <div class="font-semibold text-on-surface">Nguyễn Văn Bảo</div>
                                        <div class="font-mono text-[11px] text-on-surface-variant">2001210452</div>
                                    </td>
                                    <td class="py-3.5 px-3 text-on-surface-variant">Khoa CNTT</td>
                                    <td class="py-3.5 px-3 text-on-surface font-medium">ĐK học phần trễ</td>
                                    <td class="py-3.5 px-3 text-on-surface-variant text-[11px] font-mono">15/10/2024</td>
                                    <td class="py-3.5 px-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FEF3C7] text-[#B45309]">Chờ duyệt</span>
                                    </td>
                                    <td class="py-3.5 px-3 text-right whitespace-nowrap">
                                        <button class="px-2.5 py-1 text-xs font-semibold bg-primary hover:bg-primary-container text-on-primary rounded-md transition-colors mr-1 shadow-sm">Duyệt nhanh</button>
                                        <button class="px-2.5 py-1 text-xs font-medium border border-outline-variant hover:bg-surface-container rounded-md text-on-surface-variant transition-colors">Chi tiết</button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-surface-container-low transition-colors">
                                    <td class="py-3.5 px-3 font-mono font-semibold text-primary">ĐH-2024-0889</td>
                                    <td class="py-3.5 px-3">
                                        <div class="font-semibold text-on-surface">Trần Thị Mai Phương</div>
                                        <div class="font-mono text-[11px] text-on-surface-variant">2001220114</div>
                                    </td>
                                    <td class="py-3.5 px-3 text-on-surface-variant">Khoa Kế toán</td>
                                    <td class="py-3.5 px-3 text-on-surface font-medium">Hoãn thi kết thúc HP</td>
                                    <td class="py-3.5 px-3 text-on-surface-variant text-[11px] font-mono">15/10/2024</td>
                                    <td class="py-3.5 px-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FEE2E2] text-[#B91C1C]">Bổ sung minh chứng</span>
                                    </td>
                                    <td class="py-3.5 px-3 text-right whitespace-nowrap">
                                        <button class="px-2.5 py-1 text-xs font-medium border border-outline-variant hover:bg-surface-container rounded-md text-on-surface-variant transition-colors">Chi tiết</button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-surface-container-low transition-colors">
                                    <td class="py-3.5 px-3 font-mono font-semibold text-primary">ĐH-2024-0885</td>
                                    <td class="py-3.5 px-3">
                                        <div class="font-semibold text-on-surface">Lê Hoàng Nam</div>
                                        <div class="font-mono text-[11px] text-on-surface-variant">2001200876</div>
                                    </td>
                                    <td class="py-3.5 px-3 text-on-surface-variant">Khoa CN Thực phẩm</td>
                                    <td class="py-3.5 px-3 text-on-surface font-medium">Miễn HP Anh văn B1</td>
                                    <td class="py-3.5 px-3 text-on-surface-variant text-[11px] font-mono">14/10/2024</td>
                                    <td class="py-3.5 px-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#DCFCE7] text-[#15803D]">Đã xác thực</span>
                                    </td>
                                    <td class="py-3.5 px-3 text-right whitespace-nowrap">
                                        <button class="px-2.5 py-1 text-xs font-semibold bg-primary hover:bg-primary-container text-on-primary rounded-md transition-colors mr-1 shadow-sm">Phê duyệt</button>
                                        <button class="px-2.5 py-1 text-xs font-medium border border-outline-variant hover:bg-surface-container rounded-md text-on-surface-variant transition-colors">Chi tiết</button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-surface-container-low transition-colors">
                                    <td class="py-3.5 px-3 font-mono font-semibold text-primary">ĐH-2024-0878</td>
                                    <td class="py-3.5 px-3">
                                        <div class="font-semibold text-on-surface">Phạm Quốc Huy</div>
                                        <div class="font-mono text-[11px] text-on-surface-variant">2001211090</div>
                                    </td>
                                    <td class="py-3.5 px-3 text-on-surface-variant">Khoa CNTT</td>
                                    <td class="py-3.5 px-3 text-on-surface font-medium">ĐK học phần trễ</td>
                                    <td class="py-3.5 px-3 text-on-surface-variant text-[11px] font-mono">14/10/2024</td>
                                    <td class="py-3.5 px-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FEF3C7] text-[#B45309]">Chờ duyệt</span>
                                    </td>
                                    <td class="py-3.5 px-3 text-right whitespace-nowrap">
                                        <button class="px-2.5 py-1 text-xs font-semibold bg-primary hover:bg-primary-container text-on-primary rounded-md transition-colors mr-1 shadow-sm">Duyệt nhanh</button>
                                        <button class="px-2.5 py-1 text-xs font-medium border border-outline-variant hover:bg-surface-container rounded-md text-on-surface-variant transition-colors">Chi tiết</button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-surface-container-low transition-colors">
                                    <td class="py-3.5 px-3 font-mono font-semibold text-primary">ĐH-2024-0870</td>
                                    <td class="py-3.5 px-3">
                                        <div class="font-semibold text-on-surface">Đỗ Thu Hằng</div>
                                        <div class="font-mono text-[11px] text-on-surface-variant">2001221532</div>
                                    </td>
                                    <td class="py-3.5 px-3 text-on-surface-variant">Khoa QTKD</td>
                                    <td class="py-3.5 px-3 text-on-surface font-medium">Xin thôi học tạm thời</td>
                                    <td class="py-3.5 px-3 text-on-surface-variant text-[11px] font-mono">13/10/2024</td>
                                    <td class="py-3.5 px-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-surface-container text-on-surface-variant">Chờ Khoa duyệt</span>
                                    </td>
                                    <td class="py-3.5 px-3 text-right whitespace-nowrap">
                                        <button class="px-2.5 py-1 text-xs font-medium border border-outline-variant hover:bg-surface-container rounded-md text-on-surface-variant transition-colors">Chi tiết</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="pt-4 border-t border-surface-container flex items-center justify-between text-xs text-on-surface-variant">
                        <span>Hiển thị <strong>5 trên 28</strong> đơn chờ duyệt</span>
                        <div class="flex items-center gap-1.5">
                            <button class="px-2.5 py-1 rounded-md border border-outline-variant text-xs font-medium hover:bg-surface-container-low disabled:opacity-50 transition-colors">Trước</button>
                            <button class="px-2.5 py-1 rounded-md bg-primary text-on-primary text-xs font-semibold shadow-sm">1</button>
                            <button class="px-2.5 py-1 rounded-md border border-outline-variant text-xs font-medium hover:bg-surface-container-low transition-colors">2</button>
                            <button class="px-2.5 py-1 rounded-md border border-outline-variant text-xs font-medium hover:bg-surface-container-low transition-colors">3</button>
                            <button class="px-2.5 py-1 rounded-md border border-outline-variant text-xs font-medium hover:bg-surface-container-low transition-colors">Sau</button>
                        </div>
                    </div>
                </div>

                <!-- Right 4 cols: Tiến độ & Thông báo -->
                <div class="lg:col-span-4 flex flex-col gap-6">
                    <!-- Tiến độ đăng ký HK1 -->
                    <div class="bg-surface-container-lowest border border-outline-variant/60 rounded-xl p-5 shadow-sm">
                        <div class="flex items-center justify-between pb-3 border-b border-surface-container mb-3">
                            <h3 class="font-bold text-sm text-on-surface">Tiến độ đăng ký học phần HK1</h3>
                            <span class="text-xs font-bold text-primary bg-[#EFF6FF] px-2 py-0.5 rounded-full">78%</span>
                        </div>
                        <p class="text-xs text-on-surface-variant mb-2">14,200 / 18,450 sinh viên đã hoàn tất</p>
                        <div class="w-full bg-surface-container rounded-full h-2.5 mb-4 overflow-hidden">
                            <div class="bg-primary h-2.5 rounded-full" style="width: 78%"></div>
                        </div>
                        <div class="space-y-2.5 text-xs">
                            <div class="flex items-center justify-between text-on-surface">
                                <span class="text-on-surface-variant">Khóa 2021 (Năm 4)</span>
                                <span class="font-semibold text-[#15803D]">98% hoàn thành</span>
                            </div>
                            <div class="flex items-center justify-between text-on-surface">
                                <span class="text-on-surface-variant">Khóa 2022 (Năm 3)</span>
                                <span class="font-semibold text-primary">92% hoàn thành</span>
                            </div>
                            <div class="flex items-center justify-between text-on-surface">
                                <span class="text-on-surface-variant">Khóa 2023 (Năm 2)</span>
                                <span class="font-semibold text-primary">81% hoàn thành</span>
                            </div>
                            <div class="flex items-center justify-between text-on-surface">
                                <span class="text-on-surface-variant">Khóa 2024 (Tân sinh viên)</span>
                                <span class="font-semibold text-[#B45309]">45% đang mở cổng</span>
                            </div>
                        </div>
                    </div>

                    <!-- Thông báo Ban Giám hiệu -->
                    <div class="bg-surface-container-lowest border border-outline-variant/60 rounded-xl p-5 shadow-sm">
                        <div class="flex items-center justify-between pb-3 border-b border-surface-container mb-3">
                            <h3 class="font-bold text-sm text-on-surface flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-[20px]">campaign</span>
                                <span>Thông báo Ban Giám hiệu</span>
                            </h3>
                        </div>
                        <div class="space-y-3">
                            <div class="p-3 rounded-lg border border-outline-variant/60 bg-surface-bright hover:bg-surface-container-low transition-colors cursor-pointer">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded bg-[#FEE2E2] text-[#B91C1C]">Khẩn</span>
                                    <span class="text-[11px] text-outline">Hôm nay 09:30</span>
                                </div>
                                <h4 class="text-xs font-semibold text-on-surface leading-snug">Chốt sĩ số mở lớp HK1 năm học 2024-2025</h4>
                            </div>
                            <div class="p-3 rounded-lg border border-outline-variant/60 bg-surface-bright hover:bg-surface-container-low transition-colors cursor-pointer">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded bg-[#EFF6FF] text-primary">Đào tạo</span>
                                    <span class="text-[11px] text-outline">14/10/2024</span>
                                </div>
                                <h4 class="text-xs font-semibold text-on-surface leading-snug">Kế hoạch phân công phòng học giảng đường khu B</h4>
                            </div>
                            <div class="p-3 rounded-lg border border-outline-variant/60 bg-surface-bright hover:bg-surface-container-low transition-colors cursor-pointer">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded bg-surface-container text-on-surface-variant">Học vụ</span>
                                    <span class="text-[11px] text-outline">12/10/2024</span>
                                </div>
                                <h4 class="text-xs font-semibold text-on-surface leading-snug">Hướng dẫn xử lý hồ sơ sinh viên nợ học phí gia hạn</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Nhật ký hệ thống mới nhất -->
                    <div class="bg-surface-container-lowest border border-outline-variant/60 rounded-xl p-5 shadow-sm">
                        <div class="flex items-center justify-between pb-3 border-b border-surface-container mb-3">
                            <h3 class="font-bold text-sm text-on-surface flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-[20px]">history</span>
                                <span>Nhật ký hệ thống mới nhất</span>
                            </h3>
                        </div>
                        <div class="space-y-3 text-xs">
                            <div class="flex items-start gap-2.5 pb-2.5 border-b border-surface-container/60">
                                <span class="text-xs font-mono font-semibold text-primary pt-0.5">10:45</span>
                                <div class="flex-1 leading-snug"><span class="font-semibold text-on-surface">Lê Thị Mai Hương</span> đã duyệt mở thêm 02 lớp HP Lập trình Web.</div>
                            </div>
                            <div class="flex items-start gap-2.5 pb-2.5 border-b border-surface-container/60">
                                <span class="text-xs font-mono font-semibold text-primary pt-0.5">10:15</span>
                                <div class="flex-1 leading-snug"><span class="font-semibold text-on-surface">TS. Trần Anh Tuấn</span> đã cập nhật bảng điểm quá trình Lớp 12DHTH01.</div>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <span class="text-xs font-mono font-semibold text-primary pt-0.5">09:30</span>
                                <div class="flex-1 leading-snug"><span class="font-semibold text-on-surface">Hệ thống tự động</span> gạch nợ học phí cho 340 giao dịch ngân hàng.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="pt-8 border-t border-surface-container text-xs text-on-surface-variant flex flex-col items-center justify-center text-center gap-2 pb-6">
                <div>© 2024 <strong>Trường Đại học Công Thương TP. Hồ Chí Minh (HUIT)</strong> - Hệ thống Quản lý Học vụ.</div>
                <div class="text-outline flex items-center gap-3">
                    <span>Hỗ trợ kỹ thuật: <a href="mailto:daotao@huit.edu.vn" class="hover:text-primary transition-colors">daotao@huit.edu.vn</a></span>
                    <span>•</span>
                    <span>Hotline: 028.38161673</span>
                </div>
            </footer>
        </main>
    </div>
</div>
@include('partials.sidebar-toggle')
@endsection
