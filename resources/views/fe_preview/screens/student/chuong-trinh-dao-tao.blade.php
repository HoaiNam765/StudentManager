@extends('fe_preview.layouts.student')
@section('page_title', 'Chương trình đào tạo và Tiến độ tích lũy - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="light"@endsection
@section('body_attributes')class="bg-[#f8f9ff] text-slate-800 antialiased min-h-screen flex selection:bg-blue-100 selection:text-blue-900"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Chương trình đào tạo và Tiến độ tích lũy - HUIT Học Vụ</title>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/thoi-khoa-bieu']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">calendar_today</span>
<span class="truncate">Thời khóa biểu</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/ket-qua-hoc-tap']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">grade</span>
<span class="truncate">Kết quả học tập</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/chuong-trinh-dao-tao']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">account_tree</span>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Chương trình đào tạo và Tiến độ tích lũy</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Chương trình đào tạo và Tiến độ tích lũy</div>
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
<div class="flex flex-col gap-6">
<!-- SECTION 1: BANNER TỔNG QUAN CHƯƠNG TRÌNH & THẺ TIẾN ĐỘ TÍCH LŨY -->
<section class="bg-surface-container-lowest rounded-xl border border-slate-200 p-6 shadow-sm">
<!-- Header Banner & Action Buttons -->
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-6 border-b border-slate-100">
<div>
<div class="flex items-center gap-2.5">
<span class="px-2.5 py-0.5 rounded-full bg-surface-container text-primary font-label-sm text-label-sm font-semibold">Khóa 2021 - 2025</span>
<span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-secondary font-label-sm text-label-sm font-medium">Hệ: Đại học chính quy</span>
<span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-secondary font-label-sm text-label-sm font-medium">Mã CTĐT: 7480201</span>
</div>
<h1 class="font-headline-xl text-headline-xl text-on-surface font-bold tracking-tight mt-2">
              Chương trình đào tạo: Công nghệ Thông tin
            </h1>
<p class="font-body-md text-body-md text-secondary mt-1">
              Khoa Công nghệ Thông tin • Thời gian đào tạo chuẩn: 4 năm (8 học kỳ chính)
            </p>
</div>
<!-- CTAs: Download PDF & Plan -->
<div class="flex items-center gap-3 flex-shrink-0">
<button class="inline-flex items-center gap-2 px-4 py-2 bg-surface-container-lowest border border-slate-300 rounded-lg font-label-md text-label-md font-semibold text-on-surface hover:bg-slate-50 transition-colors shadow-sm">
<span class="material-symbols-outlined text-[18px] text-secondary" data-icon="file_download">file_download</span>
<span>Tải khung chương trình (PDF)</span>
</button>
<button class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg font-label-md text-label-md font-semibold hover:bg-[#15439e] transition-colors shadow-sm">
<span class="material-symbols-outlined text-[18px]" data-icon="view_timeline">view_timeline</span>
<span>Kế hoạch học tập đề xuất</span>
</button>
</div>
</div>
<!-- Academic Metrics Grid -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 pt-6">
<!-- Column 1: Multi-Segmented Credit Progress (7 cols) -->
<div class="lg:col-span-7 flex flex-col justify-between">
<div>
<div class="flex items-baseline justify-between mb-2">
<span class="font-label-lg text-label-lg text-secondary font-semibold">Tiến độ tích lũy tín chỉ toàn khóa</span>
<div class="flex items-baseline gap-1.5">
<span class="font-display-lg text-display-lg text-primary font-bold tracking-tight">96</span>
<span class="font-headline-md text-headline-md text-secondary font-semibold">/ 150</span>
<span class="font-body-md text-body-md text-secondary ml-1">Tín chỉ</span>
<span class="ml-2 font-label-md text-label-md px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold">Đạt 64.0%</span>
</div>
</div>
<!-- Multi-segmented Progress Bar -->
<div class="w-full h-4 bg-slate-100 rounded-full overflow-hidden flex shadow-inner my-3">
<!-- Segment 1: Đã tích lũy 64% (96 TC) -->
<div class="h-full bg-emerald-500 transition-all duration-500" style="width: 64%;" title="Đã tích lũy: 96 TC (64%)"></div>
<!-- Segment 2: Đang học HK1 12% (18 TC) -->
<div class="h-full bg-amber-400 transition-all duration-500" style="width: 12%;" title="Đang học HK1: 18 TC (12%)"></div>
<!-- Segment 3: Chưa học 24% (36 TC) -->
<div class="h-full bg-slate-200 transition-all duration-500" style="width: 24%;" title="Chưa học: 36 TC (24%)"></div>
</div>
<!-- Color Legend & Breakdown details -->
<div class="grid grid-cols-3 gap-2 pt-2">
<div class="flex items-center gap-2">
<span class="w-3 h-3 rounded-sm bg-emerald-500 flex-shrink-0"></span>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-secondary">Đã tích lũy</span>
<span class="font-label-md text-label-md font-bold text-on-surface">96 TC (64%)</span>
</div>
</div>
<div class="flex items-center gap-2">
<span class="w-3 h-3 rounded-sm bg-amber-400 flex-shrink-0"></span>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-secondary">Đang học HK1</span>
<span class="font-label-md text-label-md font-bold text-on-surface">18 TC (12%)</span>
</div>
</div>
<div class="flex items-center gap-2">
<span class="w-3 h-3 rounded-sm bg-slate-300 flex-shrink-0"></span>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-secondary">Chưa học</span>
<span class="font-label-md text-label-md font-bold text-on-surface">36 TC (24%)</span>
</div>
</div>
</div>
</div>
<!-- Graduation Requirement Hint -->
<p class="font-body-sm text-body-sm text-secondary pt-3 flex items-center gap-1.5">
<span class="material-symbols-outlined text-[16px] text-primary" data-icon="info">info</span>
<span>Yêu cầu tốt nghiệp chuẩn HUIT: Tích lũy tối thiểu 150 tín chỉ theo đúng khung CTĐT và đạt các chuẩn đầu ra.</span>
</p>
</div>
<!-- Column 2: Academic Benchmarks & Standards (5 cols) -->
<div class="lg:col-span-5 bg-surface-container-low rounded-xl p-4 border border-slate-200/80 flex flex-col justify-between">
<div>
<span class="font-label-md text-label-md text-secondary font-semibold uppercase tracking-wider block mb-3">Chỉ số học tập &amp; Chuẩn đầu ra</span>
<div class="grid grid-cols-2 gap-3">
<!-- GPA Tích lũy -->
<div class="bg-surface-container-lowest p-3 rounded-lg border border-slate-200">
<span class="font-body-sm text-body-sm text-secondary block">GPA Tích lũy (Hệ 4)</span>
<div class="flex items-baseline gap-1 mt-1">
<span class="font-headline-lg text-headline-lg font-bold text-primary">3.42</span>
<span class="font-body-sm text-body-sm text-secondary">/ 4.0</span>
</div>
<span class="inline-block mt-1 font-label-sm text-label-sm px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-semibold">Xếp loại: Giỏi</span>
</div>
<!-- Điểm rèn luyện -->
<div class="bg-surface-container-lowest p-3 rounded-lg border border-slate-200">
<span class="font-body-sm text-body-sm text-secondary block">Điểm rèn luyện HK gần nhất</span>
<div class="flex items-baseline gap-1 mt-1">
<span class="font-headline-lg text-headline-lg font-bold text-on-surface">88</span>
<span class="font-body-sm text-body-sm text-secondary">/ 100</span>
</div>
<span class="inline-block mt-1 font-label-sm text-label-sm px-2 py-0.5 rounded-full bg-blue-50 text-primary font-semibold">Xếp loại: Tốt</span>
</div>
</div>
</div>
<!-- Standard Exit Credentials -->
<div class="flex flex-col gap-2 pt-3 border-t border-slate-200/80 mt-3">
<div class="flex items-center justify-between text-body-sm">
<span class="text-secondary flex items-center gap-1.5">
<span class="material-symbols-outlined text-[16px] text-emerald-700" data-icon="verified">verified</span>
<span>Chuẩn Ngoại ngữ (B1 CEFR / VSTEP):</span>
</span>
<span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">Đã đạt (TOEIC 550)</span>
</div>
<div class="flex items-center justify-between text-body-sm">
<span class="text-secondary flex items-center gap-1.5">
<span class="material-symbols-outlined text-[16px] text-emerald-700" data-icon="verified">verified</span>
<span>Chuẩn CNTT Cơ bản (Thông tư 03):</span>
</span>
<span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">Đã đạt</span>
</div>
</div>
</div>
</div>
<!-- Alert Thông Báo Cảnh Báo Thông Minh (Amber/Warning) -->
<div class="mt-6 p-4 rounded-xl bg-amber-50 border border-amber-200 flex items-start gap-3">
<span class="material-symbols-outlined text-amber-700 text-[22px] flex-shrink-0 mt-0.5" data-icon="warning">warning</span>
<div class="flex flex-col">
<span class="font-label-lg text-label-lg font-bold text-amber-900">
              Học phần còn thiếu để xét tốt nghiệp đúng hạn:
            </span>
<p class="font-body-md text-body-md text-amber-800 mt-0.5">
              Bạn còn <strong class="font-semibold text-amber-950">36 tín chỉ chưa tích lũy</strong> (bao gồm 18 TC chuyên ngành bắt buộc, 6 TC tự chọn chuyên sâu và 10 TC Thực tập doanh nghiệp / Khóa luận tốt nghiệp). Hãy chú ý điều kiện tiên quyết và kế hoạch mở lớp cho các học kỳ tiếp theo.
            </p>
</div>
</div>
</section>
<!-- SECTION 2: THANH CÔNG CỤ TÌM KIẾM & BỘ LỌC HỌC PHẦN -->
<section class="bg-surface-container-lowest rounded-xl border border-slate-200 p-4 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
<!-- Search and Filter Dropdowns -->
<div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
<!-- Input Tìm kiếm -->
<div class="relative w-full sm:w-80">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-outline" data-icon="search">search</span>
<input aria-label="Tìm theo mã học phần, tên học phần..." class="w-full pl-9 pr-3 py-2 bg-surface-container-lowest border border-slate-300 rounded-lg font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm" placeholder="Tìm theo mã học phần, tên học phần..." type="text"/>
</div>
<!-- Dropdown Trạng thái -->
<div class="relative">
<select aria-label="Tất cả trạng thái" class="appearance-none bg-surface-container-lowest border border-slate-300 rounded-lg pl-3 pr-8 py-2 font-body-sm text-body-sm text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm cursor-pointer">
<option value="all">Tất cả trạng thái</option>
<option value="completed">Đã hoàn thành</option>
<option value="in_progress">Đang học</option>
<option value="unregistered">Chưa học</option>
</select>
<span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-[18px] text-outline pointer-events-none">expand_more</span>
</div>
<!-- Dropdown Khối kiến thức -->
<div class="relative">
<select aria-label="Tất cả khối kiến thức" class="appearance-none bg-surface-container-lowest border border-slate-300 rounded-lg pl-3 pr-8 py-2 font-body-sm text-body-sm text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm cursor-pointer">
<option value="all">Tất cả khối kiến thức</option>
<option value="general">Giáo dục đại cương (32 TC)</option>
<option value="fundamental">Cơ sở ngành (28 TC)</option>
<option value="specialized">Chuyên ngành bắt buộc (42 TC)</option>
<option value="elective">Tự chọn chuyên sâu (12 TC)</option>
<option value="thesis">Thực tập &amp; Khóa luận (10 TC)</option>
</select>
<span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-[18px] text-outline pointer-events-none">expand_more</span>
</div>
</div>
<!-- Expansion Controls -->
<div class="flex items-center gap-2 flex-shrink-0 w-full sm:w-auto justify-end">
<button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-300 font-label-md text-label-md text-secondary hover:bg-slate-50 transition-colors">
<span class="material-symbols-outlined text-[16px]" data-icon="unfold_less">unfold_less</span>
<span>Thu gọn tất cả</span>
</button>
<button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-300 font-label-md text-label-md text-secondary hover:bg-slate-50 transition-colors">
<span class="material-symbols-outlined text-[16px]" data-icon="unfold_more">unfold_more</span>
<span>Mở rộng tất cả</span>
</button>
</div>
</section>
<!-- SECTION 3: DANH SÁCH 5 KHỐI KIẾN THỨC DẠNG ACCORDION -->
<div class="flex flex-col gap-4">
<!-- ==================== KHỐI 1 ==================== -->
<div class="bg-surface-container-lowest rounded-xl border border-slate-200 overflow-hidden shadow-sm transition-all duration-200">
<!-- Accordion Header -->
<div class="p-5 flex items-center justify-between cursor-pointer hover:bg-slate-50/70 border-b border-slate-100">
<div class="flex items-center gap-3">
<span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-label-md">1</span>
<div>
<div class="flex items-center gap-2">
<h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">Kiến thức Giáo dục Đại cương</h2>
<span class="font-body-sm text-body-sm text-secondary">(Yêu cầu: 32 TC)</span>
</div>
<p class="font-body-sm text-body-sm text-secondary mt-0.5">Lý luận chính trị, Toán học, Khoa học tự nhiên và Kỹ năng bổ trợ</p>
</div>
</div>
<div class="flex items-center gap-3">
<!-- Status Badge -->
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 font-label-md text-label-md font-semibold">
<span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                Đã tích lũy: 32/32 TC - Hoàn thành 100%
              </span>
<span class="material-symbols-outlined text-outline text-[22px]">keyboard_arrow_up</span>
</div>
</div>
<!-- Academic Table -->
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-slate-50 border-b border-slate-200 text-secondary font-label-md text-label-md uppercase tracking-wider">
<th class="py-3 px-4 w-12 text-center">STT</th>
<th class="py-3 px-4 w-28">Mã HP</th>
<th class="py-3 px-4">Tên học phần</th>
<th class="py-3 px-4 w-20 text-center">Số TC</th>
<th class="py-3 px-4 w-32">Điều kiện</th>
<th class="py-3 px-4 w-24 text-center">Điểm số</th>
<th class="py-3 px-4 w-24 text-center">Điểm chữ</th>
<th class="py-3 px-4 w-36 text-center">Trạng thái</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 font-body-sm text-body-sm">
<!-- Row 1 -->
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center text-secondary">01</td>
<td class="py-3 px-4 font-mono font-medium text-primary">01010001</td>
<td class="py-3 px-4 font-medium text-on-surface">Triết học Mác - Lênin</td>
<td class="py-3 px-4 text-center font-semibold">3</td>
<td class="py-3 px-4 text-secondary">-</td>
<td class="py-3 px-4 text-center font-bold text-on-surface">8.5</td>
<td class="py-3 px-4 text-center font-bold text-emerald-700">A</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">Đã hoàn thành</span>
</td>
</tr>
<!-- Row 2 -->
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center text-secondary">02</td>
<td class="py-3 px-4 font-mono font-medium text-primary">01010002</td>
<td class="py-3 px-4 font-medium text-on-surface">Kinh tế chính trị Mác - Lênin</td>
<td class="py-3 px-4 text-center font-semibold">2</td>
<td class="py-3 px-4 text-secondary">Học trước: 01010001</td>
<td class="py-3 px-4 text-center font-bold text-on-surface">8.0</td>
<td class="py-3 px-4 text-center font-bold text-emerald-700">B+</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">Đã hoàn thành</span>
</td>
</tr>
<!-- Row 3 -->
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center text-secondary">03</td>
<td class="py-3 px-4 font-mono font-medium text-primary">01010003</td>
<td class="py-3 px-4 font-medium text-on-surface">Chủ nghĩa xã hội khoa học</td>
<td class="py-3 px-4 text-center font-semibold">2</td>
<td class="py-3 px-4 text-secondary">Học trước: 01010002</td>
<td class="py-3 px-4 text-center font-bold text-on-surface">8.5</td>
<td class="py-3 px-4 text-center font-bold text-emerald-700">A</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">Đã hoàn thành</span>
</td>
</tr>
<!-- Row 4 -->
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center text-secondary">04</td>
<td class="py-3 px-4 font-mono font-medium text-primary">01010004</td>
<td class="py-3 px-4 font-medium text-on-surface">Lịch sử Đảng ĐCSVN</td>
<td class="py-3 px-4 text-center font-semibold">2</td>
<td class="py-3 px-4 text-secondary">-</td>
<td class="py-3 px-4 text-center font-bold text-on-surface">8.0</td>
<td class="py-3 px-4 text-center font-bold text-emerald-700">B+</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">Đã hoàn thành</span>
</td>
</tr>
<!-- Row 5 -->
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center text-secondary">05</td>
<td class="py-3 px-4 font-mono font-medium text-primary">01010005</td>
<td class="py-3 px-4 font-medium text-on-surface">Tư tưởng Hồ Chí Minh</td>
<td class="py-3 px-4 text-center font-semibold">2</td>
<td class="py-3 px-4 text-secondary">-</td>
<td class="py-3 px-4 text-center font-bold text-on-surface">9.0</td>
<td class="py-3 px-4 text-center font-bold text-emerald-700">A+</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">Đã hoàn thành</span>
</td>
</tr>
<!-- Row 6 -->
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center text-secondary">06</td>
<td class="py-3 px-4 font-mono font-medium text-primary">01020011</td>
<td class="py-3 px-4 font-medium text-on-surface">Tiếng Anh 1, 2, 3 (Tích hợp)</td>
<td class="py-3 px-4 text-center font-semibold">9</td>
<td class="py-3 px-4 text-secondary">Kiểm tra đầu vào</td>
<td class="py-3 px-4 text-center font-bold text-on-surface">8.2</td>
<td class="py-3 px-4 text-center font-bold text-emerald-700">B+</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">Đã hoàn thành</span>
</td>
</tr>
<!-- Row 7 -->
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center text-secondary">07</td>
<td class="py-3 px-4 font-mono font-medium text-primary">01030001</td>
<td class="py-3 px-4 font-medium text-on-surface">Toán cao cấp A1, A2</td>
<td class="py-3 px-4 text-center font-semibold">6</td>
<td class="py-3 px-4 text-secondary">-</td>
<td class="py-3 px-4 text-center font-bold text-on-surface">7.8</td>
<td class="py-3 px-4 text-center font-bold text-emerald-700">B</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">Đã hoàn thành</span>
</td>
</tr>
<!-- Row 8 -->
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center text-secondary">08</td>
<td class="py-3 px-4 font-mono font-medium text-primary">01030005</td>
<td class="py-3 px-4 font-medium text-on-surface">Xác suất thống kê</td>
<td class="py-3 px-4 text-center font-semibold">3</td>
<td class="py-3 px-4 text-secondary">Toán cao cấp A1</td>
<td class="py-3 px-4 text-center font-bold text-on-surface">8.0</td>
<td class="py-3 px-4 text-center font-bold text-emerald-700">B+</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">Đã hoàn thành</span>
</td>
</tr>
<!-- Row 9 -->
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center text-secondary">09</td>
<td class="py-3 px-4 font-mono font-medium text-primary">01040001</td>
<td class="py-3 px-4 font-medium text-on-surface">Pháp luật đại cương</td>
<td class="py-3 px-4 text-center font-semibold">3</td>
<td class="py-3 px-4 text-secondary">-</td>
<td class="py-3 px-4 text-center font-bold text-on-surface">8.5</td>
<td class="py-3 px-4 text-center font-bold text-emerald-700">A</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">Đã hoàn thành</span>
</td>
</tr>
</tbody>
</table>
</div>
</div>
<!-- ==================== KHỐI 2 ==================== -->
<div class="bg-surface-container-lowest rounded-xl border border-slate-200 overflow-hidden shadow-sm transition-all duration-200">
<!-- Accordion Header -->
<div class="p-5 flex items-center justify-between cursor-pointer hover:bg-slate-50/70 border-b border-slate-100">
<div class="flex items-center gap-3">
<span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-label-md">2</span>
<div>
<div class="flex items-center gap-2">
<h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">Kiến thức Cơ sở ngành</h2>
<span class="font-body-sm text-body-sm text-secondary">(Yêu cầu: 28 TC)</span>
</div>
<p class="font-body-sm text-body-sm text-secondary mt-0.5">Nền tảng thuật toán, lập trình, cấu trúc dữ liệu và hệ thống máy tính</p>
</div>
</div>
<div class="flex items-center gap-3">
<!-- Status Badge -->
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 font-label-md text-label-md font-semibold">
<span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                Đã tích lũy: 28/28 TC - Hoàn thành 100%
              </span>
<span class="material-symbols-outlined text-outline text-[22px]">keyboard_arrow_up</span>
</div>
</div>
<!-- Academic Table -->
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-slate-50 border-b border-slate-200 text-secondary font-label-md text-label-md uppercase tracking-wider">
<th class="py-3 px-4 w-12 text-center">STT</th>
<th class="py-3 px-4 w-28">Mã HP</th>
<th class="py-3 px-4">Tên học phần</th>
<th class="py-3 px-4 w-20 text-center">Số TC</th>
<th class="py-3 px-4 w-32">Điều kiện</th>
<th class="py-3 px-4 w-24 text-center">Điểm số</th>
<th class="py-3 px-4 w-24 text-center">Điểm chữ</th>
<th class="py-3 px-4 w-36 text-center">Trạng thái</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 font-body-sm text-body-sm">
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center text-secondary">01</td>
<td class="py-3 px-4 font-mono font-medium text-primary">02010010</td>
<td class="py-3 px-4 font-medium text-on-surface">Cơ sở lập trình</td>
<td class="py-3 px-4 text-center font-semibold">3</td>
<td class="py-3 px-4 text-secondary">-</td>
<td class="py-3 px-4 text-center font-bold text-on-surface">9.0</td>
<td class="py-3 px-4 text-center font-bold text-emerald-700">A+</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">Đã hoàn thành</span>
</td>
</tr>
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center text-secondary">02</td>
<td class="py-3 px-4 font-mono font-medium text-primary">02010012</td>
<td class="py-3 px-4 font-medium text-on-surface">Cấu trúc dữ liệu &amp; Giải thuật</td>
<td class="py-3 px-4 text-center font-semibold">4</td>
<td class="py-3 px-4 text-secondary">Tiên quyết: 02010010</td>
<td class="py-3 px-4 text-center font-bold text-on-surface">8.5</td>
<td class="py-3 px-4 text-center font-bold text-emerald-700">A</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">Đã hoàn thành</span>
</td>
</tr>
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center text-secondary">03</td>
<td class="py-3 px-4 font-mono font-medium text-primary">02010014</td>
<td class="py-3 px-4 font-medium text-on-surface">Lập trình hướng đối tượng C++</td>
<td class="py-3 px-4 text-center font-semibold">3</td>
<td class="py-3 px-4 text-secondary">Học trước: 02010010</td>
<td class="py-3 px-4 text-center font-bold text-on-surface">8.0</td>
<td class="py-3 px-4 text-center font-bold text-emerald-700">B+</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">Đã hoàn thành</span>
</td>
</tr>
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center text-secondary">04</td>
<td class="py-3 px-4 font-mono font-medium text-primary">02010020</td>
<td class="py-3 px-4 font-medium text-on-surface">Mạng máy tính</td>
<td class="py-3 px-4 text-center font-semibold">3</td>
<td class="py-3 px-4 text-secondary">-</td>
<td class="py-3 px-4 text-center font-bold text-on-surface">8.1</td>
<td class="py-3 px-4 text-center font-bold text-emerald-700">B+</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">Đã hoàn thành</span>
</td>
</tr>
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center text-secondary">05</td>
<td class="py-3 px-4 font-mono font-medium text-primary">02010025</td>
<td class="py-3 px-4 font-medium text-on-surface">Cơ sở dữ liệu</td>
<td class="py-3 px-4 text-center font-semibold">3</td>
<td class="py-3 px-4 text-secondary">-</td>
<td class="py-3 px-4 text-center font-bold text-on-surface">8.5</td>
<td class="py-3 px-4 text-center font-bold text-emerald-700">A</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">Đã hoàn thành</span>
</td>
</tr>
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center text-secondary">06</td>
<td class="py-3 px-4 font-mono font-medium text-primary">02010030</td>
<td class="py-3 px-4 font-medium text-on-surface">Hệ điều hành</td>
<td class="py-3 px-4 text-center font-semibold">3</td>
<td class="py-3 px-4 text-secondary">Kiến trúc máy tính</td>
<td class="py-3 px-4 text-center font-bold text-on-surface">7.5</td>
<td class="py-3 px-4 text-center font-bold text-emerald-700">B</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">Đã hoàn thành</span>
</td>
</tr>
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center text-secondary">07</td>
<td class="py-3 px-4 font-mono font-medium text-primary">02010035</td>
<td class="py-3 px-4 font-medium text-on-surface">Kiến trúc máy tính</td>
<td class="py-3 px-4 text-center font-semibold">3</td>
<td class="py-3 px-4 text-secondary">-</td>
<td class="py-3 px-4 text-center font-bold text-on-surface">8.0</td>
<td class="py-3 px-4 text-center font-bold text-emerald-700">B+</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">Đã hoàn thành</span>
</td>
</tr>
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center text-secondary">08</td>
<td class="py-3 px-4 font-mono font-medium text-primary">02010040</td>
<td class="py-3 px-4 font-medium text-on-surface">Nhập môn Trí tuệ nhân tạo</td>
<td class="py-3 px-4 text-center font-semibold">3</td>
<td class="py-3 px-4 text-secondary">Cấu trúc dữ liệu</td>
<td class="py-3 px-4 text-center font-bold text-on-surface">8.0</td>
<td class="py-3 px-4 text-center font-bold text-emerald-700">B+</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">Đã hoàn thành</span>
</td>
</tr>
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center text-secondary">09</td>
<td class="py-3 px-4 font-mono font-medium text-primary">02010045</td>
<td class="py-3 px-4 font-medium text-on-surface">Đại số tuyến tính</td>
<td class="py-3 px-4 text-center font-semibold">3</td>
<td class="py-3 px-4 text-secondary">-</td>
<td class="py-3 px-4 text-center font-bold text-on-surface">7.8</td>
<td class="py-3 px-4 text-center font-bold text-emerald-700">B</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">Đã hoàn thành</span>
</td>
</tr>
</tbody>
</table>
</div>
</div>
<!-- ==================== KHỐI 3 ==================== -->
<div class="bg-surface-container-lowest rounded-xl border border-slate-200 overflow-hidden shadow-sm transition-all duration-200">
<!-- Accordion Header -->
<div class="p-5 flex items-center justify-between cursor-pointer hover:bg-slate-50/70 border-b border-slate-100">
<div class="flex items-center gap-3">
<span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center font-bold text-label-md">3</span>
<div>
<div class="flex items-center gap-2">
<h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">Kiến thức Chuyên ngành bắt buộc</h2>
<span class="font-body-sm text-body-sm text-secondary">(Yêu cầu: 42 TC)</span>
</div>
<p class="font-body-sm text-body-sm text-secondary mt-0.5">Kỹ thuật phần mềm, công nghệ web, phân tán và an toàn thông tin</p>
</div>
</div>
<div class="flex items-center gap-3">
<!-- Status Badge -->
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-800 font-label-md text-label-md font-semibold">
<span class="w-2 h-2 rounded-full bg-amber-500"></span>
                Đã tích lũy: 24/42 TC - Còn thiếu 18 TC
              </span>
<span class="material-symbols-outlined text-outline text-[22px]">keyboard_arrow_up</span>
</div>
</div>
<!-- Academic Table -->
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-slate-50 border-b border-slate-200 text-secondary font-label-md text-label-md uppercase tracking-wider">
<th class="py-3 px-4 w-12 text-center">STT</th>
<th class="py-3 px-4 w-28">Mã HP</th>
<th class="py-3 px-4">Tên học phần</th>
<th class="py-3 px-4 w-20 text-center">Số TC</th>
<th class="py-3 px-4 w-48">Điều kiện / Lớp HP</th>
<th class="py-3 px-4 w-24 text-center">Điểm số</th>
<th class="py-3 px-4 w-24 text-center">Điểm chữ</th>
<th class="py-3 px-4 w-44 text-center">Trạng thái</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 font-body-sm text-body-sm">
<!-- Completed Rows -->
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center text-secondary">01</td>
<td class="py-3 px-4 font-mono font-medium text-primary">03010101</td>
<td class="py-3 px-4 font-medium text-on-surface">Lập trình mạng</td>
<td class="py-3 px-4 text-center font-semibold">3</td>
<td class="py-3 px-4 text-secondary">Tiên quyết: Mạng máy tính</td>
<td class="py-3 px-4 text-center font-bold text-on-surface">8.5</td>
<td class="py-3 px-4 text-center font-bold text-emerald-700">A</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">Đã hoàn thành</span>
</td>
</tr>
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center text-secondary">02</td>
<td class="py-3 px-4 font-mono font-medium text-primary">03010105</td>
<td class="py-3 px-4 font-medium text-on-surface">Kiến trúc phần mềm</td>
<td class="py-3 px-4 text-center font-semibold">3</td>
<td class="py-3 px-4 text-secondary">OOP C++</td>
<td class="py-3 px-4 text-center font-bold text-on-surface">7.8</td>
<td class="py-3 px-4 text-center font-bold text-emerald-700">B</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">Đã hoàn thành</span>
</td>
</tr>
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center text-secondary">03</td>
<td class="py-3 px-4 font-mono font-medium text-primary">03010110</td>
<td class="py-3 px-4 font-medium text-on-surface">Cơ sở dữ liệu phân tán</td>
<td class="py-3 px-4 text-center font-semibold">3</td>
<td class="py-3 px-4 text-secondary">Cơ sở dữ liệu</td>
<td class="py-3 px-4 text-center font-bold text-on-surface">7.8</td>
<td class="py-3 px-4 text-center font-bold text-emerald-700">B</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">Đã hoàn thành</span>
</td>
</tr>
<!-- In Progress Rows (Amber) -->
<tr class="bg-amber-50/30 hover:bg-amber-50/60 transition-colors">
<td class="py-3 px-4 text-center text-secondary">04</td>
<td class="py-3 px-4 font-mono font-medium text-primary">71ITEC3201</td>
<td class="py-3 px-4 font-medium text-on-surface">Lập trình Web</td>
<td class="py-3 px-4 text-center font-semibold text-amber-900">3</td>
<td class="py-3 px-4 font-mono text-secondary">241_71ITEC3201_01</td>
<td class="py-3 px-4 text-center text-secondary font-medium">--</td>
<td class="py-3 px-4 text-center text-secondary font-medium">--</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 font-label-sm text-label-sm font-semibold">Đang học HK1 2026-2027</span>
</td>
</tr>
<tr class="bg-amber-50/30 hover:bg-amber-50/60 transition-colors">
<td class="py-3 px-4 text-center text-secondary">05</td>
<td class="py-3 px-4 font-mono font-medium text-primary">71ITEC3202</td>
<td class="py-3 px-4 font-medium text-on-surface">Công nghệ phần mềm</td>
<td class="py-3 px-4 text-center font-semibold text-amber-900">3</td>
<td class="py-3 px-4 font-mono text-secondary">241_71ITEC3202_02</td>
<td class="py-3 px-4 text-center text-secondary font-medium">--</td>
<td class="py-3 px-4 text-center text-secondary font-medium">--</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 font-label-sm text-label-sm font-semibold">Đang học HK1 2026-2027</span>
</td>
</tr>
<!-- Unregistered Rows (Gray/Slate) -->
<tr class="hover:bg-slate-50 transition-colors">
<td class="py-3 px-4 text-center text-secondary">06</td>
<td class="py-3 px-4 font-mono font-medium text-secondary">03010120</td>
<td class="py-3 px-4 font-medium text-on-surface">Phát triển ứng dụng di động</td>
<td class="py-3 px-4 text-center font-semibold">3</td>
<td class="py-3 px-4 text-secondary">Tiên quyết: Lập trình Java</td>
<td class="py-3 px-4 text-center text-secondary font-medium">--</td>
<td class="py-3 px-4 text-center text-secondary font-medium">--</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-slate-100 text-secondary font-label-sm text-label-sm font-medium">Chưa học</span>
</td>
</tr>
<tr class="hover:bg-slate-50 transition-colors">
<td class="py-3 px-4 text-center text-secondary">07</td>
<td class="py-3 px-4 font-mono font-medium text-secondary">03010125</td>
<td class="py-3 px-4 font-medium text-on-surface">Trí tuệ nhân tạo nâng cao</td>
<td class="py-3 px-4 text-center font-semibold">3</td>
<td class="py-3 px-4 text-secondary">Tiên quyết: Cấu trúc dữ liệu &amp; GT</td>
<td class="py-3 px-4 text-center text-secondary font-medium">--</td>
<td class="py-3 px-4 text-center text-secondary font-medium">--</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-slate-100 text-secondary font-label-sm text-label-sm font-medium">Chưa học</span>
</td>
</tr>
<tr class="hover:bg-slate-50 transition-colors">
<td class="py-3 px-4 text-center text-secondary">08</td>
<td class="py-3 px-4 font-mono font-medium text-secondary">03010130</td>
<td class="py-3 px-4 font-medium text-on-surface">An toàn và Bảo mật thông tin</td>
<td class="py-3 px-4 text-center font-semibold">3</td>
<td class="py-3 px-4 text-secondary">Tiên quyết: Mạng máy tính</td>
<td class="py-3 px-4 text-center text-secondary font-medium">--</td>
<td class="py-3 px-4 text-center text-secondary font-medium">--</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-slate-100 text-secondary font-label-sm text-label-sm font-medium">Chưa học</span>
</td>
</tr>
<tr class="hover:bg-slate-50 transition-colors">
<td class="py-3 px-4 text-center text-secondary">09</td>
<td class="py-3 px-4 font-mono font-medium text-secondary">03010135</td>
<td class="py-3 px-4 font-medium text-on-surface">Kiểm thử và Đảm bảo chất lượng phần mềm</td>
<td class="py-3 px-4 text-center font-semibold">3</td>
<td class="py-3 px-4 text-secondary">Công nghệ phần mềm</td>
<td class="py-3 px-4 text-center text-secondary font-medium">--</td>
<td class="py-3 px-4 text-center text-secondary font-medium">--</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-slate-100 text-secondary font-label-sm text-label-sm font-medium">Chưa học</span>
</td>
</tr>
</tbody>
</table>
</div>
</div>
<!-- ==================== KHỐI 4 ==================== -->
<div class="bg-surface-container-lowest rounded-xl border border-slate-200 overflow-hidden shadow-sm transition-all duration-200">
<!-- Accordion Header -->
<div class="p-5 flex items-center justify-between cursor-pointer hover:bg-slate-50/70 border-b border-slate-100">
<div class="flex items-center gap-3">
<span class="w-8 h-8 rounded-lg bg-blue-50 text-primary flex items-center justify-center font-bold text-label-md">4</span>
<div>
<div class="flex items-center gap-2">
<h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">Kiến thức Tự chọn chuyên sâu</h2>
<span class="font-body-sm text-body-sm text-secondary">(Yêu cầu tối thiểu: 12 TC)</span>
</div>
<p class="font-body-sm text-body-sm text-secondary mt-0.5">Lựa chọn các hướng nghiên cứu hiện đại: Cloud, IoT, Big Data, DevOps</p>
</div>
</div>
<div class="flex items-center gap-3">
<!-- Status Badge -->
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-800 font-label-md text-label-md font-semibold">
<span class="w-2 h-2 rounded-full bg-amber-500"></span>
                Đã tích lũy: 6/12 TC - Còn thiếu 6 TC
              </span>
<span class="material-symbols-outlined text-outline text-[22px]">keyboard_arrow_up</span>
</div>
</div>
<!-- Academic Table -->
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-slate-50 border-b border-slate-200 text-secondary font-label-md text-label-md uppercase tracking-wider">
<th class="py-3 px-4 w-12 text-center">STT</th>
<th class="py-3 px-4 w-28">Mã HP</th>
<th class="py-3 px-4">Tên học phần</th>
<th class="py-3 px-4 w-20 text-center">Số TC</th>
<th class="py-3 px-4 w-36">Định hướng</th>
<th class="py-3 px-4 w-24 text-center">Điểm số</th>
<th class="py-3 px-4 w-24 text-center">Điểm chữ</th>
<th class="py-3 px-4 w-36 text-center">Trạng thái</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 font-body-sm text-body-sm">
<!-- Completed Rows -->
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center text-secondary">01</td>
<td class="py-3 px-4 font-mono font-medium text-primary">04010201</td>
<td class="py-3 px-4 font-medium text-on-surface">Điện toán đám mây &amp; AWS</td>
<td class="py-3 px-4 text-center font-semibold">3</td>
<td class="py-3 px-4 text-secondary">Cloud Architecture</td>
<td class="py-3 px-4 text-center font-bold text-on-surface">9.0</td>
<td class="py-3 px-4 text-center font-bold text-emerald-700">A+</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">Đã hoàn thành</span>
</td>
</tr>
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center text-secondary">02</td>
<td class="py-3 px-4 font-mono font-medium text-primary">04010205</td>
<td class="py-3 px-4 font-medium text-on-surface">Phát triển hệ thống IoT</td>
<td class="py-3 px-4 text-center font-semibold">3</td>
<td class="py-3 px-4 text-secondary">Embedded &amp; IoT</td>
<td class="py-3 px-4 text-center font-bold text-on-surface">8.5</td>
<td class="py-3 px-4 text-center font-bold text-emerald-700">A</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">Đã hoàn thành</span>
</td>
</tr>
<!-- Unregistered Elective Rows -->
<tr class="hover:bg-slate-50 transition-colors">
<td class="py-3 px-4 text-center text-secondary">03</td>
<td class="py-3 px-4 font-mono font-medium text-secondary">04010210</td>
<td class="py-3 px-4 font-medium text-on-surface">DevOps &amp; CI/CD tự động hóa</td>
<td class="py-3 px-4 text-center font-semibold">3</td>
<td class="py-3 px-4 text-secondary">Systems Operation</td>
<td class="py-3 px-4 text-center text-secondary font-medium">--</td>
<td class="py-3 px-4 text-center text-secondary font-medium">--</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-slate-100 text-secondary font-label-sm text-label-sm font-medium">Chưa học</span>
</td>
</tr>
<tr class="hover:bg-slate-50 transition-colors">
<td class="py-3 px-4 text-center text-secondary">04</td>
<td class="py-3 px-4 font-mono font-medium text-secondary">04010215</td>
<td class="py-3 px-4 font-medium text-on-surface">Dữ liệu lớn Big Data &amp; Xử lý phân tán</td>
<td class="py-3 px-4 text-center font-semibold">3</td>
<td class="py-3 px-4 text-secondary">Data Engineering</td>
<td class="py-3 px-4 text-center text-secondary font-medium">--</td>
<td class="py-3 px-4 text-center text-secondary font-medium">--</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-slate-100 text-secondary font-label-sm text-label-sm font-medium">Chưa học</span>
</td>
</tr>
<tr class="hover:bg-slate-50 transition-colors">
<td class="py-3 px-4 text-center text-secondary">05</td>
<td class="py-3 px-4 font-mono font-medium text-secondary">04010220</td>
<td class="py-3 px-4 font-medium text-on-surface">Xử lý ngôn ngữ tự nhiên</td>
<td class="py-3 px-4 text-center font-semibold">3</td>
<td class="py-3 px-4 text-secondary">AI Specialization</td>
<td class="py-3 px-4 text-center text-secondary font-medium">--</td>
<td class="py-3 px-4 text-center text-secondary font-medium">--</td>
<td class="py-3 px-4 text-center">
<span class="inline-block px-2.5 py-0.5 rounded-full bg-slate-100 text-secondary font-label-sm text-label-sm font-medium">Chưa học</span>
</td>
</tr>
</tbody>
</table>
</div>
</div>
<!-- ==================== KHỐI 5 ==================== -->
<div class="bg-surface-container-lowest rounded-xl border border-slate-200 overflow-hidden shadow-sm transition-all duration-200">
<!-- Accordion Header -->
<div class="p-5 flex items-center justify-between cursor-pointer hover:bg-slate-50/70 border-b border-slate-100">
<div class="flex items-center gap-3">
<span class="w-8 h-8 rounded-lg bg-red-50 text-red-700 flex items-center justify-center font-bold text-label-md">5</span>
<div>
<div class="flex items-center gap-2">
<h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">Thực tập doanh nghiệp &amp; Khóa luận tốt nghiệp</h2>
<span class="font-body-sm text-body-sm text-secondary">(Yêu cầu: 10 TC)</span>
</div>
<p class="font-body-sm text-body-sm text-secondary mt-0.5">Giai đoạn thực chiến tại doanh nghiệp và nghiên cứu hoàn thành khóa học</p>
</div>
</div>
<div class="flex items-center gap-3">
<!-- Status Badge -->
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-50 border border-red-200 text-red-800 font-label-md text-label-md font-semibold">
<span class="w-2 h-2 rounded-full bg-red-600"></span>
                Đã tích lũy: 0/10 TC - Chưa hoàn thành
              </span>
<span class="material-symbols-outlined text-outline text-[22px]">keyboard_arrow_up</span>
</div>
</div>
<!-- Academic Table -->
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-slate-50 border-b border-slate-200 text-secondary font-label-md text-label-md uppercase tracking-wider">
<th class="py-3 px-4 w-12 text-center">STT</th>
<th class="py-3 px-4 w-28">Mã HP</th>
<th class="py-3 px-4">Tên học phần</th>
<th class="py-3 px-4 w-20 text-center">Số TC</th>
<th class="py-3 px-4">Điều kiện xét đăng ký</th>
<th class="py-3 px-4 w-24 text-center">Điểm số</th>
<th class="py-3 px-4 w-24 text-center">Điểm chữ</th>
<th class="py-3 px-4 w-64 text-center">Trạng thái</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 font-body-sm text-body-sm">
<!-- Internship Row -->
<tr class="hover:bg-slate-50 transition-colors">
<td class="py-3 px-4 text-center text-secondary">01</td>
<td class="py-3 px-4 font-mono font-medium text-secondary">05010001</td>
<td class="py-3 px-4 font-medium text-on-surface">Thực tập doanh nghiệp</td>
<td class="py-3 px-4 text-center font-semibold">4</td>
<td class="py-3 px-4 text-secondary">
<span class="text-amber-800 font-medium">Yêu cầu tích lũy: ≥ 100 TC</span>
</td>
<td class="py-3 px-4 text-center text-secondary font-medium">--</td>
<td class="py-3 px-4 text-center text-secondary font-medium">--</td>
<td class="py-3 px-4 text-center">
<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-slate-100 text-secondary font-label-sm text-label-sm font-medium">
<span class="material-symbols-outlined text-[14px] text-amber-700" data-icon="lock">lock</span>
                      Chưa học / Chưa đủ điều kiện (Hiện 96/100 TC)
                    </span>
</td>
</tr>
<!-- Thesis Row -->
<tr class="hover:bg-slate-50 transition-colors">
<td class="py-3 px-4 text-center text-secondary">02</td>
<td class="py-3 px-4 font-mono font-medium text-secondary">05010002</td>
<td class="py-3 px-4 font-medium text-on-surface">Khóa luận tốt nghiệp</td>
<td class="py-3 px-4 text-center font-semibold">6</td>
<td class="py-3 px-4 text-secondary">
<span class="text-secondary">Yêu cầu: ≥ 120 TC &amp; GPA ≥ 2.50</span>
</td>
<td class="py-3 px-4 text-center text-secondary font-medium">--</td>
<td class="py-3 px-4 text-center text-secondary font-medium">--</td>
<td class="py-3 px-4 text-center">
<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-slate-100 text-secondary font-label-sm text-label-sm font-medium">
<span class="material-symbols-outlined text-[14px] text-outline" data-icon="lock">lock</span>
                      Chưa học / Chưa đủ điều kiện
                    </span>
</td>
</tr>
</tbody>
</table>
</div>
</div>
</div>
<!-- ==================== FOOTER ==================== -->
<footer class="mt-8 pt-6 pb-4 border-t border-slate-200 text-center font-body-sm text-body-sm text-secondary">
<p class="font-medium text-on-surface-variant">
          © 2026 Trường Đại học Công Thương TP. Hồ Chí Minh (HUIT) • Cổng thông tin Sinh viên &amp; Quản lý Học vụ
        </p>
<p class="mt-1 text-outline">
          Hỗ trợ kỹ thuật: <a class="text-primary hover:underline font-medium" href="mailto:nguoidung031@example.test">nguoidung031@example.test</a> • Hotline: <span class="font-medium text-on-surface-variant">(028) 0000 0000</span> • Phòng Đào tạo (A102, 140 Người dùng mẫu 18, P. Tây Thạnh, Q. Tân Phú)
        </p>
</footer>
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
