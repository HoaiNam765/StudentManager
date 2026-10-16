@extends('fe_preview.layouts.student')
@section('page_title', 'Thời khóa biểu Sinh viên - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="light"@endsection
@section('body_attributes')class="bg-[#f8f9ff] text-slate-800 antialiased min-h-screen flex selection:bg-blue-100 selection:text-blue-900"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Thời khóa biểu Sinh viên - HUIT Học Vụ</title>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            primary: {
              DEFAULT: '#1c55c4',
              hover: '#1645a5',
              tint: '#eef2fd',
              50: '#f0f5ff',
            },
            surface: '#f8f9ff',
          },
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
          }
        }
      }
    }
  </script>
<style>
    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 20;
      font-size: 20px;
      line-height: 1;
      display: inline-block;
    }
    body { font-family: 'Inter', sans-serif; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    @media print {
      .no-print { display: none !important; }
      .print-only { display: block !important; }
      body { background: white !important; }
    }
  </style>
<style id="mobile-wrap-fix">
/* Vá tạm cho 360px: cho phép hàng flex xuống dòng, không cho phần tử con tràn khỏi khung. Thay bằng sửa bố cục từng màn khi có thời gian. */
@media (max-width:639px){
 main .flex:not(.flex-col):not(.flex-wrap):not(.overflow-x-auto){flex-wrap:wrap}
 main .flex>*{min-width:0}
 main img,main svg,main video{max-width:100%}
 main table{max-width:100%}
}
</style>
<style id="ui-overflow-guard">html,body{max-width:100%;} img,svg,video{max-width:100%;height:auto} :focus-visible{outline:3px solid #e49b15;outline-offset:2px}</style>
@endsection
@section('content')

 Mobile Drawer Backdrop 
<div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-40 hidden lg:hidden transition-opacity duration-300 opacity-0 pointer-events-none no-print" id="mobile-backdrop" onclick="toggleSidebar()"></div>
 ================= SIDEBAR ================= 
<aside class="w-64 min-w-[256px] h-screen bg-white border-r border-slate-200/80 flex flex-col justify-between fixed left-0 top-0 z-50 transition-all duration-300 -translate-x-full lg:translate-x-0 no-print" id="main-sidebar">
<!-- Brand / School Header -->
<div class="p-4 border-b border-slate-100 flex items-center justify-between">
<div class="flex items-center gap-3">
<div class="w-9 h-9 rounded-xl bg-primary text-white flex items-center justify-center font-bold text-sm shadow-xs shrink-0">
<span class="material-symbols-outlined text-[20px]">school</span>
</div>
<div class="overflow-hidden">
<div class="font-bold text-[13.5px] text-slate-900 leading-tight truncate">HUIT Học Vụ</div>
<div class="text-[11px] text-slate-500 font-medium truncate">Cổng Sinh viên</div>
</div>
</div>
<button class="lg:hidden p-1.5 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100" onclick="toggleSidebar()">
<span class="material-symbols-outlined text-[20px]">close</span>
</button>
</div>
<!-- Navigation Menu (Dynamic by Stage) -->
<div class="flex-1 overflow-y-auto custom-scrollbar p-3 space-y-6">
<div class="space-y-1">
<div class="px-3 py-1.5 text-[10.5px] font-bold tracking-wider text-slate-500 uppercase">HỌC TẬP &amp; ĐÀO TẠO</div>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/tong-quan']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">dashboard</span>
<span class="truncate">Bàn làm việc</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/dang-ky-hoc-phan']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">how_to_reg</span>
<span class="truncate">Đăng ký học phần</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/ket-qua-dang-ky']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">fact_check</span>
<span class="truncate">Kết quả đăng ký</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/thoi-khoa-bieu']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">calendar_today</span>
<span class="truncate">Thời khóa biểu</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/ket-qua-hoc-tap']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">grade</span>
<span class="truncate">Kết quả học tập</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/chuong-trinh-dao-tao']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">account_tree</span>
<span class="truncate">Chương trình đào tạo</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/lich-thi']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">event_available</span>
<span class="truncate">Lịch thi</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/chuyen-can']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">rule</span>
<span class="truncate">Chuyên cần</span>
</a>
</div>
<div class="space-y-1">
<div class="px-3 py-1.5 text-[10.5px] font-bold tracking-wider text-slate-500 uppercase">HÀNH CHÍNH &amp; TÀI CHÍNH</div>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/hoc-phi']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">payments</span>
<span class="truncate">Học phí &amp; Thanh toán</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/ho-so-giay-to']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">folder_shared</span>
<span class="truncate">Hồ sơ &amp; Giấy tờ</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/don-tu']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">assignment</span>
<span class="truncate">Đơn từ trực tuyến</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/hoc-bong-ren-luyen']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">military_tech</span>
<span class="truncate">Học bổng &amp; Rèn luyện</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/khao-sat']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">quiz</span>
<span class="truncate">Khảo sát chất lượng</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/thanh-toan-online']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">credit_card</span>
<span class="truncate">Thanh toán trực tuyến</span>
</a>
</div>
<div class="space-y-1">
<div class="px-3 py-1.5 text-[10.5px] font-bold tracking-wider text-slate-500 uppercase">TÀI KHOẢN</div>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/ho-so-ca-nhan']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">person</span>
<span class="truncate">Hồ sơ cá nhân</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/thong-bao']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">notifications</span>
<span class="truncate">Thông báo học vụ</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/cai-dat-tai-khoan']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">settings</span>
<span class="truncate">Cài đặt tài khoản</span>
</a>
</div>
</div>
<!-- Sidebar Bottom: Support Hotline -->
<div class="p-3.5 border-t border-slate-100 bg-slate-50/70">
<div class="flex items-center gap-2.5 text-xs text-slate-600">
<span class="material-symbols-outlined text-primary text-[18px]">support_agent</span>
<div>
<div class="font-semibold text-slate-800 text-[11.5px]">Hỗ trợ kỹ thuật: 028 0000 0000</div>
<div class="text-[11px] text-slate-500">nguoidung038@example.test</div>
</div>
</div>
</div>
</aside>
 ================= MAIN WRAPPER ================= 
<div class="flex-1 flex flex-col lg:pl-64 min-w-0 transition-all duration-300">
<!-- TOP HEADER -->
<header class="h-16 bg-white border-b border-slate-200/80 px-4 sm:px-6 flex items-center justify-between sticky top-0 z-30 no-print shadow-xs">
<div class="flex items-center gap-3 min-w-0 flex-1">
<!-- Hamburger Mobile Button -->
<button class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100 focus:outline-none" onclick="toggleSidebar()">
<span class="material-symbols-outlined text-[22px]">menu</span>
</button>
<div class="min-w-0">
<div class="flex items-center gap-2 min-w-0">
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Thời khóa biểu Sinh viên</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Thời khóa biểu Sinh viên</div>
</div>
</div>
<!-- Header Right: Role Switcher & Student Profile -->
<div class="flex items-center gap-2 sm:gap-3 shrink-0">
<!-- Role quick switch -->
<div class="hidden md:flex items-center p-1 bg-slate-100 rounded-lg text-xs font-medium">
<span class="px-2.5 py-1 rounded-md bg-white text-primary font-semibold shadow-xs">Sinh viên</span>
<a class="px-2.5 py-1 text-slate-600 hover:text-slate-900" href="{{ route('fe.preview.show', ['screen' => 'teacher/lich-giang-day']) }}">Giảng viên</a>
<a class="px-2.5 py-1 text-slate-600 hover:text-slate-900" href="{{ route('fe.preview.show', ['screen' => 'admin/tong-quan']) }}">Phòng ĐT</a>
</div>
<!-- Notification Bell -->
<a class="relative p-2 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/thong-bao']) }}" title="Thông báo">
<span class="material-symbols-outlined text-[21px]">notifications</span>
<span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-500"></span>
</a>
<!-- User Profile Pill -->
<div class="flex items-center gap-2.5 pl-2 border-l border-slate-200">
<div class="w-8 h-8 rounded-full bg-blue-100 text-primary font-bold text-xs flex items-center justify-center border border-blue-200">
            NVA
          </div>
<div class="hidden sm:block text-left">
<div class="text-xs font-semibold text-slate-900 leading-tight">Người dùng mẫu 25</div>
<div class="text-[11px] text-slate-500">2400000007 • 12DHTH01</div>
</div>
</div>
</div>
</header>
<!-- MAIN BODY CONTENT -->
<main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
<!-- ============================================================== -->
<!-- 3. ACTION CONTROLS & TIMETABLE TOOLBAR BANNER                 -->
<!-- ============================================================== -->
<div class="bg-surface-container-lowest rounded-xl p-5 border border-outline-variant shadow-sm flex flex-wrap items-center justify-between gap-4">
<!-- Left: View Mode Toggler -->
<div class="flex items-center gap-4">
<div class="bg-surface-container-low p-1 rounded-lg border border-outline-variant flex items-center gap-1">
<button class="px-3.5 py-1.5 rounded-md bg-surface-container-lowest text-primary font-semibold font-label-md text-label-md shadow-xs flex items-center gap-1.5">
<span class="material-symbols-outlined text-sm" data-icon="calendar_view_week">calendar_view_week</span>
              Xem theo Tuần
            </button>
<button class="px-3.5 py-1.5 rounded-md text-secondary hover:text-on-surface font-label-md text-label-md transition-colors flex items-center gap-1.5">
<span class="material-symbols-outlined text-sm" data-icon="calendar_month">calendar_month</span>
              Xem theo Học kỳ
            </button>
</div>
</div>
<!-- Center: Week Navigator Controls -->
<div class="flex items-center gap-2">
<button class="px-3 py-1.5 border border-outline-variant rounded-lg text-secondary hover:text-on-surface hover:bg-surface-container-low transition-colors flex items-center gap-1 font-label-md text-label-md" title="Tuần trước">
<span class="material-symbols-outlined text-base" data-icon="chevron_left">chevron_left</span>
<span>Tuần trước</span>
</button>
<div class="px-4 py-1.5 bg-surface-container-lowest text-center min-w-[280px]">
<div class="font-headline-sm text-headline-sm font-bold text-on-surface tracking-tight">Tuần 08: 14/10/2026 - 20/10/2026</div>
<div class="font-label-sm text-label-sm text-primary font-medium">Học kỳ 1 • Năm học 2026-2027</div>
</div>
<button class="px-3 py-1.5 border border-outline-variant rounded-lg text-secondary hover:text-on-surface hover:bg-surface-container-low transition-colors flex items-center gap-1 font-label-md text-label-md" title="Tuần sau">
<span>Tuần sau</span>
<span class="material-symbols-outlined text-base" data-icon="chevron_right">chevron_right</span>
</button>
<button class="ml-1 px-3 py-1.5 bg-primary-fixed text-primary font-semibold border border-primary/20 rounded-lg font-label-md text-label-md hover:bg-primary-container hover:text-white transition-colors flex items-center gap-1">
<span class="material-symbols-outlined text-sm" data-icon="today">today</span>
<span>Hôm nay</span>
</button>
</div>
<!-- Right: Export & Print Action Buttons -->
<div class="flex items-center gap-2">
<button class="px-3 py-1.5 border border-outline-variant bg-surface-container-lowest rounded-lg font-label-md text-label-md text-on-surface font-medium hover:bg-surface-container-low transition-colors flex items-center gap-1.5 shadow-xs">
<span class="material-symbols-outlined text-base text-secondary" data-icon="calendar_add_on">calendar_add_on</span>
<span>Xuất lịch Google (.ics)</span>
</button>
<button class="px-4 py-1.5 bg-primary text-white rounded-lg font-label-md text-label-md font-semibold hover:bg-primary-container transition-colors flex items-center gap-1.5 shadow-sm">
<span class="material-symbols-outlined text-base" data-icon="print">print</span>
<span>In thời khóa biểu (A4)</span>
</button>
</div>
</div>
<!-- ============================================================== -->
<!-- 4. TIMETABLE GRID (Weekly Schedule Matrix D11 #23 Desktop)    -->
<!-- ============================================================== -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
<!-- Grid Table Container -->
<div class="overflow-x-auto">
<table class="w-full border-collapse table-fixed text-left min-w-[1180px]">
<!-- Table Header: Days of the Week -->
<thead>
<tr class="bg-surface-container-low/60 border-b border-outline-variant divide-x divide-outline-variant/60 text-center font-label-md text-label-md text-secondary">
<!-- Col 1: Time Header -->
<th class="w-32 py-3 px-2 font-semibold text-on-surface-variant bg-surface-container-low">
<div class="flex items-center justify-center gap-1">
<span class="material-symbols-outlined text-sm text-secondary" data-icon="schedule">schedule</span>
<span>Tiết / Giờ</span>
</div>
</th>
<!-- Col 2: Thứ Hai -->
<th class="py-3 px-2 font-medium">
<div class="text-on-surface font-semibold text-sm">Thứ Hai</div>
<div class="text-xs text-secondary">14/10/2026</div>
</th>
<!-- Col 3: Thứ Ba (HÔM NAY - ACTIVE DAY) -->
<th class="py-3 px-2 bg-blue-50/70 border-x-2 border-primary relative">
<div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-primary text-white text-[10px] font-bold mb-1 shadow-xs">
<span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                    Hôm nay
                  </div>
<div class="text-primary font-bold text-sm">Thứ Ba</div>
<div class="text-xs font-semibold text-primary">15/10/2026</div>
</th>
<!-- Col 4: Thứ Tư -->
<th class="py-3 px-2 font-medium">
<div class="text-on-surface font-semibold text-sm">Thứ Tư</div>
<div class="text-xs text-secondary">16/10/2026</div>
</th>
<!-- Col 5: Thứ Năm -->
<th class="py-3 px-2 font-medium">
<div class="text-on-surface font-semibold text-sm">Thứ Năm</div>
<div class="text-xs text-secondary">17/10/2026</div>
</th>
<!-- Col 6: Thứ Sáu -->
<th class="py-3 px-2 font-medium">
<div class="text-on-surface font-semibold text-sm">Thứ Sáu</div>
<div class="text-xs text-secondary">18/10/2026</div>
</th>
<!-- Col 7: Thứ Bảy -->
<th class="py-3 px-2 font-medium">
<div class="text-on-surface font-semibold text-sm">Thứ Bảy</div>
<div class="text-xs text-secondary">19/10/2026</div>
</th>
<!-- Col 8: Chủ Nhật -->
<th class="py-3 px-2 font-medium">
<div class="text-on-surface font-semibold text-sm text-error/80">Chủ Nhật</div>
<div class="text-xs text-secondary">20/10/2026</div>
</th>
</tr>
</thead>
<!-- Table Body: Morning, Afternoon, Evening Shifts -->
<tbody class="divide-y divide-outline-variant/60 font-body-sm text-body-sm">
<!-- ================= CA SÁNG ================= -->
<tr class="bg-surface-container-low/30">
<td class="px-4 py-1.5 font-label-md text-label-md text-primary font-bold tracking-wide uppercase bg-gradient-to-r from-blue-50 to-transparent border-y border-outline-variant/70" colspan="8">
<div class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-base" data-icon="wb_sunny">wb_sunny</span>
<span>Ca Sáng (07:00 — 11:00)</span>
</div>
</td>
</tr>
<!-- Block Tiết 1 - 3 (07:00 - 09:30) -->
<tr class="divide-x divide-outline-variant/60 h-36">
<!-- Timeline Info -->
<td class="p-2.5 bg-surface-container-lowest align-top">
<div class="font-semibold text-on-surface font-label-md text-label-md">Tiết 1 - 3</div>
<div class="text-secondary text-xs mt-0.5">07:00 - 09:30</div>
<span class="mt-2 inline-block px-1.5 py-0.5 bg-surface-container-low text-secondary text-[10px] rounded border border-outline-variant/40">3 tiết</span>
</td>
<!-- Thứ 2 (14/10): Lập trình Web -->
<td class="p-2 align-top bg-surface-container-lowest hover:bg-slate-50 transition-colors">
<div class="h-full bg-blue-50/90 border-l-4 border-l-blue-600 border border-blue-200 rounded-lg p-2.5 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow">
<div>
<div class="flex items-center justify-between gap-1 mb-1">
<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700">Lý thuyết</span>
<span class="text-[10px] font-medium text-blue-800">3 TC</span>
</div>
<div class="font-headline-sm text-sm font-bold text-blue-900 leading-snug">Lập trình Web</div>
<div class="text-[11px] text-blue-700 font-mono mt-0.5 truncate" title="241_71ITEC3201_01">241_71ITEC3201_01</div>
</div>
<div class="pt-2 border-t border-blue-200/60 space-y-0.5 text-xs text-slate-700">
<div class="flex items-center gap-1">
<span class="material-symbols-outlined text-xs text-blue-600" data-icon="location_on">location_on</span>
<span class="font-semibold text-blue-950">P.A2.301</span>
<span class="text-[10px] text-slate-500">(Cơ sở Tân Phú)</span>
</div>
<div class="flex items-center gap-1 truncate" title="TS. Người dùng mẫu 38">
<span class="material-symbols-outlined text-xs text-blue-600" data-icon="person">person</span>
<span class="truncate">TS. Người dùng mẫu 38</span>
</div>
</div>
</div>
</td>
<!-- Thứ 3 (15/10 - HÔM NAY): An toàn thông tin -->
<td class="p-2 align-top bg-blue-50/30 border-x-2 border-primary">
<div class="h-full bg-emerald-50/90 border-l-4 border-l-emerald-600 border border-emerald-300 rounded-lg p-2.5 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow ring-1 ring-emerald-400/50">
<div>
<div class="flex items-center justify-between gap-1 mb-1">
<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 flex items-center gap-1">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-ping"></span>
                          Hôm nay • Đang diễn ra
                        </span>
<span class="text-[10px] font-bold text-emerald-800">3 TC</span>
</div>
<div class="font-headline-sm text-sm font-bold text-emerald-950 leading-snug">An toàn thông tin</div>
<div class="text-[11px] text-emerald-700 font-mono mt-0.5 truncate" title="241_71ITEC2102_02">241_71ITEC2102_02</div>
</div>
<div class="pt-2 border-t border-emerald-200/80 space-y-0.5 text-xs text-slate-700">
<div class="flex items-center gap-1">
<span class="material-symbols-outlined text-xs text-emerald-700" data-icon="location_on">location_on</span>
<span class="font-semibold text-emerald-950">P.B.102</span>
<span class="text-[10px] text-slate-500">(Cơ sở Tân Phú)</span>
</div>
<div class="flex items-center gap-1 truncate" title="ThS. Người dùng mẫu 56">
<span class="material-symbols-outlined text-xs text-emerald-700" data-icon="person">person</span>
<span class="truncate font-medium">ThS. Người dùng mẫu 56</span>
</div>
</div>
</div>
</td>
<!-- Thứ 4: Trống -->
<td class="p-2 align-middle text-center text-secondary/40 bg-surface-container-lowest hover:bg-slate-50/50">
<span class="text-xs italic">—</span>
</td>
<!-- Thứ 5: Trống sáng -->
<td class="p-2 align-middle text-center text-secondary/40 bg-surface-container-lowest hover:bg-slate-50/50">
<span class="text-xs italic">—</span>
</td>
<!-- Thứ 6 (18/10): Cấu trúc dữ liệu & GT -->
<td class="p-2 align-top bg-surface-container-lowest hover:bg-slate-50 transition-colors">
<div class="h-full bg-sky-50/90 border-l-4 border-l-sky-600 border border-sky-200 rounded-lg p-2.5 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow">
<div>
<div class="flex items-center justify-between gap-1 mb-1">
<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-100 text-sky-800">Lý thuyết</span>
<span class="text-[10px] font-bold text-sky-800">4 TC</span>
</div>
<div class="font-headline-sm text-sm font-bold text-sky-950 leading-snug">Cấu trúc dữ liệu &amp; GT</div>
<div class="text-[11px] text-sky-700 font-mono mt-0.5 truncate" title="241_71ITEC2104_01">241_71ITEC2104_01</div>
</div>
<div class="pt-2 border-t border-sky-200/70 space-y-0.5 text-xs text-slate-700">
<div class="flex items-center gap-1">
<span class="material-symbols-outlined text-xs text-sky-600" data-icon="location_on">location_on</span>
<span class="font-semibold text-sky-950">P.B.204</span>
</div>
<div class="flex items-center gap-1 truncate" title="ThS. Người dùng mẫu 11">
<span class="material-symbols-outlined text-xs text-sky-600" data-icon="person">person</span>
<span class="truncate">ThS. Người dùng mẫu 11</span>
</div>
</div>
</div>
</td>
<!-- Thứ 7: Nghỉ học -->
<td class="p-2 align-middle text-center text-secondary/50 bg-slate-50/60">
<div class="flex flex-col items-center justify-center gap-1">
<span class="material-symbols-outlined text-slate-300 text-xl" data-icon="bedtime">bedtime</span>
<span class="text-[11px] font-medium text-slate-500">Nghỉ học</span>
</div>
</td>
<!-- Chủ nhật: Nghỉ học -->
<td class="p-2 align-middle text-center text-secondary/50 bg-slate-50/60">
<div class="flex flex-col items-center justify-center gap-1">
<span class="material-symbols-outlined text-slate-300 text-xl" data-icon="weekend">weekend</span>
<span class="text-[11px] font-medium text-slate-500">Nghỉ học</span>
</div>
</td>
</tr>
<!-- Block Tiết 4 - 5 (09:30 - 11:00) -->
<tr class="divide-x divide-outline-variant/60 h-20">
<td class="p-2.5 bg-surface-container-lowest align-top">
<div class="font-semibold text-on-surface font-label-md text-label-md">Tiết 4 - 5</div>
<div class="text-secondary text-xs mt-0.5">09:30 - 11:00</div>
<span class="mt-1 inline-block px-1.5 py-0.5 bg-surface-container-low text-secondary text-[10px] rounded border border-outline-variant/40">2 tiết</span>
</td>
<td class="p-2 align-middle text-center text-secondary/40 bg-surface-container-lowest">
<span class="text-xs italic">—</span>
</td>
<td class="p-2 align-middle text-center text-secondary/40 bg-blue-50/30 border-x-2 border-primary">
<span class="text-xs italic text-blue-400">Trống tiết</span>
</td>
<td class="p-2 align-middle text-center text-secondary/40 bg-surface-container-lowest">
<span class="text-xs italic">—</span>
</td>
<td class="p-2 align-middle text-center text-secondary/40 bg-surface-container-lowest">
<span class="text-xs italic">—</span>
</td>
<td class="p-2 align-middle text-center text-secondary/40 bg-surface-container-lowest">
<span class="text-xs italic">—</span>
</td>
<td class="p-2 align-middle text-center text-secondary/50 bg-slate-50/60">
<span class="text-[11px] text-slate-500 italic">Nghỉ học</span>
</td>
<td class="p-2 align-middle text-center text-secondary/50 bg-slate-50/60">
<span class="text-[11px] text-slate-500 italic">Nghỉ học</span>
</td>
</tr>
<!-- ================= CA CHIỀU ================= -->
<tr class="bg-surface-container-low/30">
<td class="px-4 py-1.5 font-label-md text-label-md text-primary font-bold tracking-wide uppercase bg-gradient-to-r from-blue-50 to-transparent border-y border-outline-variant/70" colspan="8">
<div class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-base" data-icon="light_mode">light_mode</span>
<span>Ca Chiều (12:30 — 16:30)</span>
</div>
</td>
</tr>
<!-- Block Tiết 6 (12:30 - 13:15) -->
<tr class="divide-x divide-outline-variant/60 h-16">
<td class="p-2.5 bg-surface-container-lowest align-top">
<div class="font-semibold text-on-surface font-label-md text-label-md">Tiết 6</div>
<div class="text-secondary text-xs mt-0.5">12:30 - 13:15</div>
</td>
<td class="p-2 align-middle text-center text-secondary/40 bg-surface-container-lowest"><span class="text-xs italic">—</span></td>
<td class="p-2 align-middle text-center text-secondary/40 bg-blue-50/30 border-x-2 border-primary"><span class="text-xs italic">—</span></td>
<td class="p-2 align-middle text-center text-secondary/40 bg-surface-container-lowest"><span class="text-xs italic">—</span></td>
<td class="p-2 align-middle text-center text-secondary/40 bg-surface-container-lowest"><span class="text-xs italic">—</span></td>
<td class="p-2 align-middle text-center text-secondary/40 bg-surface-container-lowest"><span class="text-xs italic">—</span></td>
<td class="p-2 align-middle text-center text-secondary/50 bg-slate-50/60"><span class="text-[11px] text-slate-500 italic">Nghỉ</span></td>
<td class="p-2 align-middle text-center text-secondary/50 bg-slate-50/60"><span class="text-[11px] text-slate-500 italic">Nghỉ</span></td>
</tr>
<!-- Block Tiết 7 - 9 (13:15 - 15:45) -->
<tr class="divide-x divide-outline-variant/60 h-36">
<!-- Timeline Info -->
<td class="p-2.5 bg-surface-container-lowest align-top">
<div class="font-semibold text-on-surface font-label-md text-label-md">Tiết 7 - 9</div>
<div class="text-secondary text-xs mt-0.5">13:15 - 15:45</div>
<span class="mt-2 inline-block px-1.5 py-0.5 bg-surface-container-low text-secondary text-[10px] rounded border border-outline-variant/40">3 tiết</span>
</td>
<!-- Thứ 2: Trống chiều -->
<td class="p-2 align-middle text-center text-secondary/40 bg-surface-container-lowest hover:bg-slate-50/50">
<span class="text-xs italic">—</span>
</td>
<!-- Thứ 3 (HÔM NAY): Trống chiều -->
<td class="p-2 align-middle text-center text-secondary/40 bg-blue-50/30 border-x-2 border-primary">
<span class="text-xs italic text-blue-400">Tự học / Làm bài tập</span>
</td>
<!-- Thứ 4 (16/10): Đại số tuyến tính -->
<td class="p-2 align-top bg-surface-container-lowest hover:bg-slate-50 transition-colors">
<div class="h-full bg-purple-50/90 border-l-4 border-l-purple-600 border border-purple-200 rounded-lg p-2.5 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow">
<div>
<div class="flex items-center justify-between gap-1 mb-1">
<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800">Lý thuyết</span>
<span class="text-[10px] font-bold text-purple-800">3 TC</span>
</div>
<div class="font-headline-sm text-sm font-bold text-purple-950 leading-snug">Đại số tuyến tính</div>
<div class="text-[11px] text-purple-700 font-mono mt-0.5 truncate" title="241_71MATH1101_01">241_71MATH1101_01</div>
</div>
<div class="pt-2 border-t border-purple-200/70 space-y-0.5 text-xs text-slate-700">
<div class="flex items-center gap-1">
<span class="material-symbols-outlined text-xs text-purple-700" data-icon="location_on">location_on</span>
<span class="font-semibold text-purple-950">P.A1.201</span>
</div>
<div class="flex items-center gap-1 truncate" title="TS. Người dùng mẫu 46">
<span class="material-symbols-outlined text-xs text-purple-700" data-icon="person">person</span>
<span class="truncate">TS. Người dùng mẫu 46</span>
</div>
</div>
</div>
</td>
<!-- Thứ 5 (17/10): Thực hành Mạng máy tính -->
<td class="p-2 align-top bg-surface-container-lowest hover:bg-slate-50 transition-colors">
<div class="h-full bg-orange-50/90 border-l-4 border-l-orange-500 border border-orange-200 rounded-lg p-2.5 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow">
<div>
<div class="flex items-center justify-between gap-1 mb-1">
<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-800">Thực hành</span>
<span class="text-[10px] font-bold text-orange-800">1 TC</span>
</div>
<div class="font-headline-sm text-sm font-bold text-orange-950 leading-snug">TH Mạng máy tính</div>
<div class="text-[11px] text-orange-700 font-mono mt-0.5 truncate" title="241_71ITEC3208_03">241_71ITEC3208_03</div>
</div>
<div class="pt-2 border-t border-orange-200/70 space-y-0.5 text-xs text-slate-700">
<div class="flex items-center gap-1">
<span class="material-symbols-outlined text-xs text-orange-600" data-icon="terminal">terminal</span>
<span class="font-semibold text-orange-950">Lab PM04</span>
<span class="text-[10px] text-slate-500">(Nhà B)</span>
</div>
<div class="flex items-center gap-1 truncate" title="ThS. Người dùng mẫu 05">
<span class="material-symbols-outlined text-xs text-orange-600" data-icon="person">person</span>
<span class="truncate">ThS. Người dùng mẫu 05</span>
</div>
</div>
</div>
</td>
<!-- Thứ 6: Trống chiều -->
<td class="p-2 align-middle text-center text-secondary/40 bg-surface-container-lowest hover:bg-slate-50/50">
<span class="text-xs italic">—</span>
</td>
<!-- Thứ 7: Nghỉ học -->
<td class="p-2 align-middle text-center text-secondary/50 bg-slate-50/60">
<div class="flex flex-col items-center justify-center gap-1">
<span class="material-symbols-outlined text-slate-300 text-xl" data-icon="local_cafe">local_cafe</span>
<span class="text-[11px] font-medium text-slate-500">Nghỉ học</span>
</div>
</td>
<!-- Chủ nhật: Nghỉ học -->
<td class="p-2 align-middle text-center text-secondary/50 bg-slate-50/60">
<div class="flex flex-col items-center justify-center gap-1">
<span class="material-symbols-outlined text-slate-300 text-xl" data-icon="park">park</span>
<span class="text-[11px] font-medium text-slate-500">Nghỉ học</span>
</div>
</td>
</tr>
<!-- Block Tiết 10 (15:45 - 16:30) -->
<tr class="divide-x divide-outline-variant/60 h-16">
<td class="p-2.5 bg-surface-container-lowest align-top">
<div class="font-semibold text-on-surface font-label-md text-label-md">Tiết 10</div>
<div class="text-secondary text-xs mt-0.5">15:45 - 16:30</div>
</td>
<td class="p-2 align-middle text-center text-secondary/40 bg-surface-container-lowest"><span class="text-xs italic">—</span></td>
<td class="p-2 align-middle text-center text-secondary/40 bg-blue-50/30 border-x-2 border-primary"><span class="text-xs italic">—</span></td>
<td class="p-2 align-middle text-center text-secondary/40 bg-surface-container-lowest"><span class="text-xs italic">—</span></td>
<td class="p-2 align-middle text-center text-secondary/40 bg-surface-container-lowest"><span class="text-xs italic">—</span></td>
<td class="p-2 align-middle text-center text-secondary/40 bg-surface-container-lowest"><span class="text-xs italic">—</span></td>
<td class="p-2 align-middle text-center text-secondary/50 bg-slate-50/60"><span class="text-[11px] text-slate-500 italic">Nghỉ</span></td>
<td class="p-2 align-middle text-center text-secondary/50 bg-slate-50/60"><span class="text-[11px] text-slate-500 italic">Nghỉ</span></td>
</tr>
<!-- ================= CA TỐI ================= -->
<tr class="bg-surface-container-low/30">
<td class="px-4 py-1.5 font-label-md text-label-md text-primary font-bold tracking-wide uppercase bg-gradient-to-r from-blue-50 to-transparent border-y border-outline-variant/70" colspan="8">
<div class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-base" data-icon="dark_mode">dark_mode</span>
<span>Ca Tối (17:30 — 19:00)</span>
</div>
</td>
</tr>
<!-- Block Tiết 11 - 12 (17:30 - 19:00) -->
<tr class="divide-x divide-outline-variant/60 h-20">
<td class="p-2.5 bg-surface-container-lowest align-top">
<div class="font-semibold text-on-surface font-label-md text-label-md">Tiết 11 - 12</div>
<div class="text-secondary text-xs mt-0.5">17:30 - 19:00</div>
<span class="mt-1 inline-block px-1.5 py-0.5 bg-surface-container-low text-secondary text-[10px] rounded border border-outline-variant/40">2 tiết</span>
</td>
<td class="p-2 align-middle text-center text-secondary/40 bg-surface-container-lowest"><span class="text-xs italic">—</span></td>
<td class="p-2 align-middle text-center text-secondary/40 bg-blue-50/30 border-x-2 border-primary"><span class="text-xs italic">—</span></td>
<td class="p-2 align-middle text-center text-secondary/40 bg-surface-container-lowest"><span class="text-xs italic">—</span></td>
<td class="p-2 align-middle text-center text-secondary/40 bg-surface-container-lowest"><span class="text-xs italic">—</span></td>
<td class="p-2 align-middle text-center text-secondary/40 bg-surface-container-lowest"><span class="text-xs italic">—</span></td>
<td class="p-2 align-middle text-center text-secondary/50 bg-slate-50/60"><span class="text-[11px] text-slate-500 italic">Nghỉ</span></td>
<td class="p-2 align-middle text-center text-secondary/50 bg-slate-50/60"><span class="text-[11px] text-slate-500 italic">Nghỉ</span></td>
</tr>
</tbody>
</table>
</div>
<!-- Table Legend Bar -->
<div class="bg-surface-container-low/50 px-6 py-3 border-t border-outline-variant flex flex-wrap items-center justify-between text-xs text-secondary gap-3">
<div class="flex items-center gap-6">
<span class="font-semibold text-on-surface">Ghi chú phân loại:</span>
<div class="flex items-center gap-1.5">
<span class="w-3 h-3 rounded bg-blue-100 border border-blue-400"></span>
<span>Lớp học phần lý thuyết</span>
</div>
<div class="flex items-center gap-1.5">
<span class="w-3 h-3 rounded bg-orange-100 border border-orange-400"></span>
<span>Lớp học phần thực hành (Lab)</span>
</div>
<div class="flex items-center gap-1.5">
<span class="w-3 h-3 rounded bg-emerald-100 border border-emerald-500"></span>
<span>Đang diễn ra (Hôm nay)</span>
</div>
</div>
<div class="flex items-center gap-2 text-slate-500">
<span class="material-symbols-outlined text-sm" data-icon="info">info</span>
<span>Sinh viên có mặt trước giờ học 10 phút để ổn định vị trí phòng học.</span>
</div>
</div>
</div>
<!-- ============================================================== -->
<!-- 5. SUMMARY STATS BENTO & EMPTY STATE PREVIEW TOGGLE           -->
<!-- ============================================================== -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
<!-- Summary Stats Card (Bento Item 1 & 2) -->
<div class="lg:col-span-2 bg-surface-container-lowest rounded-xl p-6 border border-outline-variant shadow-sm space-y-4">
<div class="flex items-center justify-between pb-3 border-b border-outline-variant/60">
<div class="flex items-center gap-2">
<div class="p-2 rounded-lg bg-primary-fixed text-primary">
<span class="material-symbols-outlined text-lg" data-icon="analytics">analytics</span>
</div>
<h3 class="font-headline-sm text-headline-sm font-semibold text-on-surface">Chỉ số học vụ tuần 08</h3>
</div>
<span class="text-xs px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-semibold">Tiến độ bình thường</span>
</div>
<!-- Metric Cards Grid -->
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
<div class="p-4 rounded-xl bg-surface-container-low/70 border border-outline-variant/50">
<span class="text-xs text-secondary font-medium">Buổi học trong tuần</span>
<div class="mt-1 font-display-lg text-2xl font-bold text-primary">05 <span class="text-xs font-normal text-secondary">buổi</span></div>
<span class="text-[11px] text-emerald-700 font-medium">Từ Thứ 2 đến Thứ 6</span>
</div>
<div class="p-4 rounded-xl bg-surface-container-low/70 border border-outline-variant/50">
<span class="text-xs text-secondary font-medium">Tổng số tiết học</span>
<div class="mt-1 font-display-lg text-2xl font-bold text-on-surface">16 <span class="text-xs font-normal text-secondary">tiết</span></div>
<span class="text-[11px] text-secondary font-medium">13 LT • 3 TH</span>
</div>
<div class="p-4 rounded-xl bg-surface-container-low/70 border border-outline-variant/50">
<span class="text-xs text-secondary font-medium">Lịch học học phần</span>
<div class="mt-1 font-display-lg text-2xl font-bold text-emerald-700">00 <span class="text-xs font-normal text-secondary">kỳ thi</span></div>
<span class="text-[11px] text-secondary font-medium">Không có lịch học</span>
</div>
<div class="p-4 rounded-xl bg-surface-container-low/70 border border-outline-variant/50">
<span class="text-xs text-secondary font-medium">Tín chỉ đăng ký</span>
<div class="mt-1 font-display-lg text-2xl font-bold text-primary">19 <span class="text-xs font-normal text-secondary">TC</span></div>
<span class="text-[11px] text-secondary font-medium">Dự kiến quá trình học tập 100%</span>
</div>
</div>
</div>
<!-- Empty State Preview Card (Interactive component demonstrating clean empty state) -->
<div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant shadow-sm flex flex-col justify-between">
<div>
<div class="flex items-center justify-between pb-3 border-b border-outline-variant/60">
<h4 class="font-headline-sm text-sm font-semibold text-on-surface">Tuần tự học &amp; Không có lịch</h4>
<span class="material-symbols-outlined text-secondary text-sm" data-icon="visibility">visibility</span>
</div>
<div class="py-5 text-center space-y-2">
<div class="w-12 h-12 mx-auto rounded-full bg-blue-50 text-primary flex items-center justify-center">
<span class="material-symbols-outlined text-2xl" data-icon="event_busy">event_busy</span>
</div>
<p class="font-label-md text-label-md text-on-surface font-semibold">Tuần này bạn không có lịch học</p>
<p class="font-body-sm text-body-sm text-secondary px-4">
                Chúc bạn có tuần tự học hiệu quả hoặc tham gia các hoạt động ngoại khóa tại HUIT!
              </p>
</div>
</div>
<button class="w-full py-2 bg-surface-container-low hover:bg-surface-container text-primary font-semibold rounded-lg font-label-md text-label-md border border-outline-variant/70 transition-colors">
            Xem kế hoạch năm học 2026-2027
          </button>
</div>
</div>
</main>
<!-- FOOTER -->
<footer class="py-4 px-6 bg-white border-t border-slate-200/80 text-center text-xs text-slate-500 no-print flex flex-col sm:flex-row items-center justify-between gap-2">
<div>© 2026 <strong>Trường Đại học Công Thương TP.HCM (HUIT)</strong> — Hệ thống Quản lý Học vụ.</div>
<div class="flex items-center gap-3 text-slate-500 text-[11px]">
<span>Hỗ trợ: 028 0000 0000</span>
<span>•</span>
<span>nguoidung038@example.test</span>
</div>
</footer>
</div>
<script>
    function toggleSidebar() {
      const sb = document.getElementById('main-sidebar');
      const bd = document.getElementById('mobile-backdrop');
      if (sb.classList.contains('-translate-x-full')) {
        sb.classList.remove('-translate-x-full');
        bd.classList.remove('hidden', 'pointer-events-none', 'opacity-0');
        bd.classList.add('opacity-100');
      } else {
        sb.classList.add('-translate-x-full');
        bd.classList.add('opacity-0', 'pointer-events-none');
        setTimeout(() => bd.classList.add('hidden'), 300);
      }
    }
  </script>
<script id="state-demo">
/* HUIT Học Vụ - demo trạng thái giao diện (UX-08).
   Thêm ?state=loading | empty | error vào URL để xem trạng thái đang tải / rỗng / lỗi của vùng dữ liệu chính.
   Không có tham số: trang hiển thị dữ liệu bình thường. */
(function () {
  var st = new URLSearchParams(location.search).get('state');
  if (!st) return;
  function run() {
    var t = document.querySelector('main table');
    if (!t) return;
    var box = t.closest('.overflow-x-auto') || t.parentElement;
    var msg = {
      loading: ['Đang tải dữ liệu…', 'Vui lòng chờ trong giây lát.'],
      empty: ['Chưa có dữ liệu', 'Thử đổi bộ lọc hoặc thêm mới để bắt đầu.'],
      error: ['Không thể tải dữ liệu', 'Kết nối bị gián đoạn. Hãy kiểm tra mạng rồi thử lại.']
    }[st];
    if (!msg) return;
    var p = document.createElement('div');
    p.setAttribute('role', st === 'error' ? 'alert' : 'status');
    p.style.cssText = 'padding:48px 16px;text-align:center;color:#334155;border:1px dashed #cbd5e1;border-radius:12px;background:#fff';
    var h = '<p style="font-weight:600;font-size:15px;margin:0 0 6px">' + msg[0] + '</p><p style="font-size:13px;margin:0;color:#475569">' + msg[1] + '</p>';
    if (st === 'loading') {
      var rows = '';
      for (var i = 0; i < 4; i++) rows += '<div style="height:14px;margin:10px auto;max-width:640px;border-radius:6px;background:#e2e8f0;animation:pulse 1.4s ease-in-out infinite"></div>';
      h = rows + h + '<style>@keyframes pulse{50%{opacity:.4}}</style>';
    }
    if (st === 'error') h += '<button type="button" onclick="location.href=location.pathname" style="margin-top:14px;padding:8px 16px;border-radius:8px;background:#1c55c4;color:#fff;font-weight:600">Thử lại</button>';
    p.innerHTML = h;
    box.style.display = 'none';
    box.parentNode.insertBefore(p, box.nextSibling);
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', run); else run();
})();
</script>

@endsection
