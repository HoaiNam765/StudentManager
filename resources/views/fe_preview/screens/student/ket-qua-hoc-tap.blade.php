@extends('fe_preview.layouts.student')
@section('page_title', 'Cổng Thông tin Sinh viên - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Cổng Thông tin Sinh viên - HUIT Học Vụ</title>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/ket-qua-hoc-tap']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">grade</span>
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
<div class="font-semibold text-slate-800 text-[11.5px]">Hỗ trợ kỹ thuật: 028 3816 1673</div>
<div class="text-[11px] text-slate-500">support@huit.edu.vn</div>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Cổng Thông tin Sinh viên</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Cổng Thông tin Sinh viên</div>
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
<div class="text-xs font-semibold text-slate-900 leading-tight">Nguyễn Văn An</div>
<div class="text-[11px] text-slate-500">2001210123 • 12DHTH01</div>
</div>
</div>
</div>
</header>
<!-- MAIN BODY CONTENT -->
<main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
<!-- Semester Selector & Overview Title -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-surface-container-lowest p-5 rounded-xl border border-[#e2e8f0] shadow-sm">
<div>
<h1 class="font-headline-lg text-headline-lg font-bold text-on-surface">Kết quả học tập học phần</h1>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Dữ liệu kết quả chính thức từ Hệ thống Đào tạo &amp; Khảo thí Trường ĐH Công Thương TP.HCM (HUIT)</p>
</div>
<!-- Filter & View options -->
<div class="flex items-center gap-3">
<div class="relative">
<select aria-label="Học kỳ 1 (Năm học 2026 - 2027)" class="h-10 pl-3.5 pr-9 bg-surface-container-low border border-outline-variant rounded-lg font-label-md text-label-md text-on-surface focus:ring-2 focus:ring-primary focus:border-primary outline-none appearance-none cursor-pointer">
<option selected="">Học kỳ 1 (Năm học 2026 - 2027)</option>
<option>Học kỳ 3 (Năm học 2023 - 2024)</option>
<option>Học kỳ 2 (Năm học 2023 - 2024)</option>
<option>Học kỳ 1 (Năm học 2023 - 2024)</option>
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant text-[18px]" data-icon="arrow_drop_down">arrow_drop_down</span>
</div>
<button class="h-10 px-4 rounded-lg border border-outline-variant hover:border-primary hover:text-primary bg-surface-container-lowest font-label-md text-label-md text-on-surface-variant flex items-center gap-2 transition-colors">
<span class="material-symbols-outlined text-[18px]" data-icon="all_inclusive">all_inclusive</span>
<span>Tất cả học kỳ (Toàn khóa)</span>
</button>
</div>
</div>
<!-- 4 Thẻ KPI Học tập Sinh viên (Modern Bento Cards) -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
<!-- Card 1: GPA Học kỳ -->
<div class="bg-surface-container-lowest border border-[#e2e8f0] rounded-xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between">
<div class="flex items-start justify-between">
<div>
<span class="font-label-md text-label-md text-on-surface-variant font-medium">GPA Học kỳ hiện tại</span>
<div class="flex items-baseline gap-2 mt-1">
<span class="text-3xl font-bold tracking-tight text-primary">3.52</span>
<span class="font-body-sm text-body-sm text-outline">/ 4.00</span>
</div>
</div>
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#dcfce7] text-[#15803d]">
              Xuất sắc
            </span>
</div>
<div class="mt-4 pt-3 border-t border-[#f1f5f9] flex items-center justify-between text-xs text-on-surface-variant">
<span>Điểm hệ 10: <strong class="text-on-surface font-semibold">8.45</strong></span>
<span>Xếp loại: <strong class="text-primary font-semibold">Giỏi</strong></span>
</div>
</div>
<!-- Card 2: GPA Tích lũy toàn khóa -->
<div class="bg-surface-container-lowest border border-[#e2e8f0] rounded-xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between">
<div class="flex items-start justify-between">
<div>
<span class="font-label-md text-label-md text-on-surface-variant font-medium">GPA Tích lũy toàn khóa</span>
<div class="flex items-baseline gap-2 mt-1">
<span class="text-3xl font-bold tracking-tight text-on-surface">3.46</span>
<span class="font-body-sm text-body-sm text-outline">/ 4.00</span>
</div>
</div>
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#eff4ff] text-primary">
              Khá - Giỏi
            </span>
</div>
<div class="mt-4 pt-3 border-t border-[#f1f5f9] flex items-center justify-between text-xs text-on-surface-variant">
<span>Điểm hệ 10: <strong class="text-on-surface font-semibold">8.32</strong></span>
<span>Khóa học: <strong class="text-on-surface font-semibold">2021 - 2025</strong></span>
</div>
</div>
<!-- Card 3: Tín chỉ đạt trong kỳ -->
<div class="bg-surface-container-lowest border border-[#e2e8f0] rounded-xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between">
<div class="flex items-start justify-between">
<div>
<span class="font-label-md text-label-md text-on-surface-variant font-medium">Tín chỉ đăng ký học kỳ</span>
<div class="flex items-baseline gap-2 mt-1">
<span class="text-3xl font-bold tracking-tight text-[#15803d]">18</span>
<span class="font-body-sm text-body-sm text-outline">/ 18 TC</span>
</div>
</div>
<div class="w-8 h-8 rounded-full bg-[#dcfce7] flex items-center justify-center text-[#15803d]">
<span class="material-symbols-outlined text-lg" data-icon="check_circle">check_circle</span>
</div>
</div>
<div class="mt-4 pt-3 border-t border-[#f1f5f9]">
<div class="flex items-center justify-between text-xs text-on-surface-variant mb-1">
<span>Tỉ lệ đạt học phần:</span>
<span class="font-semibold text-[#15803d]">100% (6/6 học phần)</span>
</div>
<div class="w-full bg-[#eff4ff] rounded-full h-1.5 overflow-hidden">
<div class="bg-[#15803d] h-1.5 rounded-full" style="width: 100%"></div>
</div>
</div>
</div>
<!-- Card 4: Tổng tín chỉ tích lũy toàn khóa -->
<div class="bg-surface-container-lowest border border-[#e2e8f0] rounded-xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between">
<div class="flex items-start justify-between">
<div>
<span class="font-label-md text-label-md text-on-surface-variant font-medium">Tích lũy chương trình</span>
<div class="flex items-baseline gap-2 mt-1">
<span class="text-3xl font-bold tracking-tight text-on-surface">96</span>
<span class="font-body-sm text-body-sm text-outline">/ 150 TC</span>
</div>
</div>
<div class="w-8 h-8 rounded-full bg-secondary-container flex items-center justify-center text-primary">
<span class="material-symbols-outlined text-lg" data-icon="flag">flag</span>
</div>
</div>
<div class="mt-4 pt-3 border-t border-[#f1f5f9]">
<div class="flex items-center justify-between text-xs text-on-surface-variant mb-1">
<span>Tiến độ cử nhân:</span>
<span class="font-semibold text-primary">64.0%</span>
</div>
<div class="w-full bg-[#e5eeff] rounded-full h-1.5 overflow-hidden">
<div class="bg-primary h-1.5 rounded-full" style="width: 64.0%"></div>
</div>
</div>
</div>
</div>
<!-- ==================== BẢNG ĐIỂM CHI TIẾT HỌC KỲ ==================== -->
<section class="bg-surface-container-lowest border border-[#e2e8f0] rounded-xl shadow-sm overflow-hidden">
<!-- Card Header -->
<div class="px-6 py-4 border-b border-[#f1f5f9] flex flex-wrap items-center justify-between gap-3">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined text-primary text-2xl" data-icon="table_chart">table_chart</span>
<h2 class="font-headline-sm text-headline-sm font-semibold text-on-surface">Bảng điểm chi tiết học kỳ 1 (2026-2027)</h2>
<span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-surface-container text-primary">6 Học phần đã hoàn tất</span>
</div>
<div class="flex items-center gap-2">
<div class="relative">
<input aria-label="Tìm tên hoặc mã học phần..." class="h-9 pl-8 pr-3 text-xs bg-surface-container-low border border-outline-variant rounded-lg w-64 focus:ring-1 focus:ring-primary focus:border-primary outline-none" placeholder="Tìm tên hoặc mã học phần..." type="text"/>
<span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-outline-variant text-[16px]" data-icon="search">search</span>
</div>
</div>
</div>
<!-- High-density Data Table with Fixed Columns -->
<div class="overflow-x-auto w-full">
<table class="w-full text-left text-sm border-collapse min-w-[1000px]">
<thead>
<tr class="bg-[#f8fafc] border-b border-[#e2e8f0] font-label-md text-label-md text-[#64748b] uppercase tracking-wider">
<th class="py-3 px-3 w-12 text-center sticky-col-1 bg-[#f8fafc]">STT</th>
<th class="py-3 px-4 w-32 sticky-col-2 bg-[#f8fafc]">Mã HP</th>
<th class="py-3 px-4 min-w-[220px] sticky-col-3 bg-[#f8fafc]">Tên học phần</th>
<th class="py-3 px-3 text-center w-16">Số TC</th>
<th class="py-3 px-3 text-center w-24">Quá trình học tập</th>
<th class="py-3 px-3 text-center w-24">Bài tập</th>
<th class="py-3 px-3 text-center w-24">Giữa kỳ</th>
<th class="py-3 px-3 text-center w-24">Cuối kỳ</th>
<th class="py-3 px-3 text-center w-24 font-bold text-on-surface">Tổng kết (10)</th>
<th class="py-3 px-3 text-center w-20 font-bold text-on-surface">Điểm chữ</th>
<th class="py-3 px-3 text-center w-20 font-bold text-primary">Thang 4</th>
<th class="py-3 px-4 text-center w-28">Trạng thái</th>
</tr>
</thead>
<tbody class="divide-y divide-[#f1f5f9] text-[#0f172a]">
<!-- STT 1 -->
<tr class="hover:bg-[#f8fafc] transition-colors">
<td class="py-3 px-3 text-center font-medium sticky-col-1 bg-white hover:bg-[#f8fafc]">1</td>
<td class="py-3 px-4 font-mono font-medium text-primary sticky-col-2 bg-white hover:bg-[#f8fafc]">71ITEC3201</td>
<td class="py-3 px-4 font-medium text-on-surface sticky-col-3 bg-white hover:bg-[#f8fafc]">Lập trình Web</td>
<td class="py-3 px-3 text-center">3</td>
<td class="py-3 px-3 text-center text-on-surface-variant">9.5</td>
<td class="py-3 px-3 text-center text-on-surface-variant">8.5</td>
<td class="py-3 px-3 text-center text-on-surface-variant">8.0</td>
<td class="py-3 px-3 text-center text-on-surface-variant">8.5</td>
<td class="py-3 px-3 text-center font-bold text-on-surface">8.5</td>
<td class="py-3 px-3 text-center font-bold text-[#15803d]">A</td>
<td class="py-3 px-3 text-center font-bold text-primary">4.0</td>
<td class="py-3 px-4 text-center">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#dcfce7] text-[#15803d]">Đạt</span>
</td>
</tr>
<!-- STT 2 -->
<tr class="hover:bg-[#f8fafc] transition-colors">
<td class="py-3 px-3 text-center font-medium sticky-col-1 bg-white hover:bg-[#f8fafc]">2</td>
<td class="py-3 px-4 font-mono font-medium text-primary sticky-col-2 bg-white hover:bg-[#f8fafc]">71ITEC2104</td>
<td class="py-3 px-4 font-medium text-on-surface sticky-col-3 bg-white hover:bg-[#f8fafc]">Cấu trúc dữ liệu &amp; GT</td>
<td class="py-3 px-3 text-center">4</td>
<td class="py-3 px-3 text-center text-on-surface-variant">9.0</td>
<td class="py-3 px-3 text-center text-on-surface-variant">8.0</td>
<td class="py-3 px-3 text-center text-on-surface-variant">8.5</td>
<td class="py-3 px-3 text-center text-on-surface-variant">8.5</td>
<td class="py-3 px-3 text-center font-bold text-on-surface">8.5</td>
<td class="py-3 px-3 text-center font-bold text-[#15803d]">A</td>
<td class="py-3 px-3 text-center font-bold text-primary">4.0</td>
<td class="py-3 px-4 text-center">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#dcfce7] text-[#15803d]">Đạt</span>
</td>
</tr>
<!-- STT 3 -->
<tr class="hover:bg-[#f8fafc] transition-colors">
<td class="py-3 px-3 text-center font-medium sticky-col-1 bg-white hover:bg-[#f8fafc]">3</td>
<td class="py-3 px-4 font-mono font-medium text-primary sticky-col-2 bg-white hover:bg-[#f8fafc]">71ITEC3208</td>
<td class="py-3 px-4 font-medium text-on-surface sticky-col-3 bg-white hover:bg-[#f8fafc]">Mạng máy tính</td>
<td class="py-3 px-3 text-center">3</td>
<td class="py-3 px-3 text-center text-on-surface-variant">8.5</td>
<td class="py-3 px-3 text-center text-on-surface-variant">8.5</td>
<td class="py-3 px-3 text-center text-on-surface-variant">8.0</td>
<td class="py-3 px-3 text-center text-on-surface-variant">8.0</td>
<td class="py-3 px-3 text-center font-bold text-on-surface">8.1</td>
<td class="py-3 px-3 text-center font-bold text-primary">B+</td>
<td class="py-3 px-3 text-center font-bold text-primary">3.5</td>
<td class="py-3 px-4 text-center">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#dcfce7] text-[#15803d]">Đạt</span>
</td>
</tr>
<!-- STT 4 -->
<tr class="hover:bg-[#f8fafc] transition-colors">
<td class="py-3 px-3 text-center font-medium sticky-col-1 bg-white hover:bg-[#f8fafc]">4</td>
<td class="py-3 px-4 font-mono font-medium text-primary sticky-col-2 bg-white hover:bg-[#f8fafc]">71MATH1101</td>
<td class="py-3 px-4 font-medium text-on-surface sticky-col-3 bg-white hover:bg-[#f8fafc]">Đại số tuyến tính</td>
<td class="py-3 px-3 text-center">3</td>
<td class="py-3 px-3 text-center text-on-surface-variant">9.0</td>
<td class="py-3 px-3 text-center text-on-surface-variant">7.5</td>
<td class="py-3 px-3 text-center text-on-surface-variant">8.0</td>
<td class="py-3 px-3 text-center text-on-surface-variant">7.5</td>
<td class="py-3 px-3 text-center font-bold text-on-surface">7.8</td>
<td class="py-3 px-3 text-center font-bold text-on-secondary-container">B</td>
<td class="py-3 px-3 text-center font-bold text-primary">3.0</td>
<td class="py-3 px-4 text-center">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#dcfce7] text-[#15803d]">Đạt</span>
</td>
</tr>
<!-- STT 5 -->
<tr class="hover:bg-[#f8fafc] transition-colors">
<td class="py-3 px-3 text-center font-medium sticky-col-1 bg-white hover:bg-[#f8fafc]">5</td>
<td class="py-3 px-4 font-mono font-medium text-primary sticky-col-2 bg-white hover:bg-[#f8fafc]">71LANG1201</td>
<td class="py-3 px-4 font-medium text-on-surface sticky-col-3 bg-white hover:bg-[#f8fafc]">Tiếng Anh chuyên ngành</td>
<td class="py-3 px-3 text-center">2</td>
<td class="py-3 px-3 text-center text-on-surface-variant">9.5</td>
<td class="py-3 px-3 text-center text-on-surface-variant">9.0</td>
<td class="py-3 px-3 text-center text-on-surface-variant">8.5</td>
<td class="py-3 px-3 text-center text-on-surface-variant">9.0</td>
<td class="py-3 px-3 text-center font-bold text-on-surface">9.0</td>
<td class="py-3 px-3 text-center font-bold text-[#15803d]">A</td>
<td class="py-3 px-3 text-center font-bold text-primary">4.0</td>
<td class="py-3 px-4 text-center">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#dcfce7] text-[#15803d]">Đạt</span>
</td>
</tr>
<!-- STT 6 -->
<tr class="hover:bg-[#f8fafc] transition-colors">
<td class="py-3 px-3 text-center font-medium sticky-col-1 bg-white hover:bg-[#f8fafc]">6</td>
<td class="py-3 px-4 font-mono font-medium text-primary sticky-col-2 bg-white hover:bg-[#f8fafc]">71ITEC2102</td>
<td class="py-3 px-4 font-medium text-on-surface sticky-col-3 bg-white hover:bg-[#f8fafc]">An toàn thông tin</td>
<td class="py-3 px-3 text-center">3</td>
<td class="py-3 px-3 text-center text-on-surface-variant">8.5</td>
<td class="py-3 px-3 text-center text-on-surface-variant">8.0</td>
<td class="py-3 px-3 text-center text-on-surface-variant">7.5</td>
<td class="py-3 px-3 text-center text-on-surface-variant">8.0</td>
<td class="py-3 px-3 text-center font-bold text-on-surface">8.0</td>
<td class="py-3 px-3 text-center font-bold text-on-secondary-container">B</td>
<td class="py-3 px-3 text-center font-bold text-primary">3.0</td>
<td class="py-3 px-4 text-center">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#dcfce7] text-[#15803d]">Đạt</span>
</td>
</tr>
</tbody>
<!-- Table Footer Summary -->
<tfoot class="bg-[#eff4ff] border-t-2 border-primary-fixed text-on-surface font-label-md text-label-md">
<tr>
<td class="py-3.5 px-4 font-bold sticky-col-1 bg-[#eff4ff]" colspan="3">
                  TỔNG CỘNG HỌC KỲ: 18 TÍN CHỈ
                </td>
<td class="py-3.5 px-3 text-center font-bold">18 TC</td>
<td class="py-3.5 px-3 text-right text-on-surface-variant font-medium" colspan="4">
                  Điểm trung bình học kỳ:
                </td>
<td class="py-3.5 px-3 text-center font-bold text-base text-primary">8.45</td>
<td class="py-3.5 px-3 text-center font-bold text-primary">A</td>
<td class="py-3.5 px-3 text-center font-bold text-base text-primary">3.52</td>
<td class="py-3.5 px-4 text-center">
<span class="font-bold text-[#15803d]">Hoàn tất</span>
</td>
</tr>
</tfoot>
</table>
</div>
<!-- Academic Assessment Bottom Bar -->
<div class="p-4 bg-surface-container-low border-t border-[#e2e8f0] flex flex-wrap items-center justify-between gap-4 text-xs">
<div class="flex items-center gap-6">
<div><span class="text-on-surface-variant">Điểm trung bình học kỳ (hệ 10):</span> <strong class="text-on-surface font-semibold ml-1">8.45</strong></div>
<div><span class="text-on-surface-variant">Điểm trung bình học kỳ (hệ 4):</span> <strong class="text-primary font-semibold ml-1">3.52</strong></div>
<div><span class="text-on-surface-variant">Xếp loại học lực:</span> <strong class="text-[#15803d] font-semibold ml-1">Giỏi</strong></div>
</div>
<div class="flex items-center gap-2">
<span class="text-on-surface-variant">Điểm rèn luyện:</span>
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#eff4ff] text-primary">
              Xuất sắc (92 điểm)
            </span>
</div>
</div>
</section>
<!-- ==================== BAN XEM TRUOC BANG DIEM IN AN KHO A4 ==================== -->
<section class="mt-12">
<div class="flex items-center justify-between mb-4">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-2xl" data-icon="description">description</span>
<h2 class="font-headline-md text-headline-md font-bold text-on-surface">Bản xem trước Bảng điểm in ấn khổ A4 (Chính thức)</h2>
</div>
<div class="flex items-center gap-3">
<span class="text-xs text-on-surface-variant flex items-center gap-1">
<span class="material-symbols-outlined text-sm text-[#15803d]" data-icon="verified">verified</span>
              Mã bảo mật điện tử hợp lệ
            </span>
<button class="h-9 px-4 rounded-lg bg-primary hover:bg-[#3b66d1] text-white text-xs font-semibold flex items-center gap-1.5 shadow-sm transition-colors" onclick="window.print()">
<span class="material-symbols-outlined text-base" data-icon="print">print</span>
              In bản này
            </button>
</div>
</div>
<!-- The Printable A4 Sheet Container -->
<div class="bg-white border border-[#cbd5e1] shadow-2xl rounded-sm max-w-4xl mx-auto p-12 text-[#0f172a] relative overflow-hidden font-sans" id="print-sheet">
<!-- Institutional Header Layout -->
<div class="grid grid-cols-2 gap-4 pb-6 border-b border-[#cbd5e1] text-center">
<!-- Left Side: Institution -->
<div>
<p class="text-xs font-bold uppercase tracking-wider text-slate-800">BỘ CÔNG THƯƠNG</p>
<p class="text-sm font-extrabold uppercase text-primary tracking-tight mt-0.5">TRƯỜNG ĐẠI HỌC CÔNG THƯƠNG TP. HỒ CHÍ MINH</p>
<p class="text-xs font-semibold text-slate-600 uppercase mt-0.5">PHÒNG ĐÀO TẠO</p>
<div class="w-20 h-0.5 bg-primary mx-auto mt-1"></div>
</div>
<!-- Right Side: State Motto -->
<div>
<p class="text-xs font-bold uppercase tracking-wider text-slate-800">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</p>
<p class="text-xs font-bold tracking-normal text-slate-700 mt-0.5">Độc lập - Tự do - Hạnh phúc</p>
<div class="w-28 h-0.5 bg-slate-400 mx-auto mt-1.5"></div>
<p class="text-[11px] italic text-slate-500 mt-2">TP. Hồ Chí Minh, ngày 15 tháng 01 năm 2025</p>
</div>
</div>
<!-- Document Title -->
<div class="text-center my-6">
<h3 class="text-xl font-bold uppercase tracking-wide text-primary">BẢNG ĐIỂM KẾT QUẢ HỌC TẬP</h3>
<p class="text-xs font-semibold uppercase text-slate-600 mt-1">HỌC KỲ 1 - NĂM HỌC 2026 - 2027</p>
<p class="text-[11px] text-slate-500 italic mt-0.5">(Hệ đào tạo: Đại học chính quy - Khóa 2021)</p>
</div>
<!-- Student Profile Grid (Administrative Standard) -->
<div class="bg-slate-50 p-4 rounded border border-slate-200 text-xs mb-6 grid grid-cols-2 gap-y-2 gap-x-6">
<div>
<span class="text-slate-500">Họ và tên sinh viên:</span>
<strong class="font-bold text-slate-900 ml-1.5 uppercase">NGUYỄN VĂN AN</strong>
</div>
<div>
<span class="text-slate-500">Mã số sinh viên (MSSV):</span>
<strong class="font-mono font-bold text-slate-900 ml-1.5">2001210123</strong>
</div>
<div>
<span class="text-slate-500">Ngày sinh:</span>
<span class="font-medium text-slate-800 ml-1.5">14/08/2003</span>
</div>
<div>
<span class="text-slate-500">Lớp sinh hoạt:</span>
<span class="font-medium text-slate-800 ml-1.5">12DHTH01</span>
</div>
<div>
<span class="text-slate-500">Ngành đào tạo:</span>
<strong class="font-semibold text-slate-800 ml-1.5">Công nghệ Thông tin</strong>
</div>
<div>
<span class="text-slate-500">Khoa quản lý:</span>
<span class="font-medium text-slate-800 ml-1.5">Công nghệ Thông tin (HUIT)</span>
</div>
</div>
<!-- Official Academic Result Table -->
<table class="w-full text-left text-xs border border-slate-300 border-collapse mb-6">
<thead>
<tr class="bg-slate-100 border-b border-slate-300 text-slate-700 font-semibold text-center">
<th class="py-2.5 px-2 border-r border-slate-300 w-10">STT</th>
<th class="py-2.5 px-3 border-r border-slate-300 w-28">Mã HP</th>
<th class="py-2.5 px-3 border-r border-slate-300 text-left">Tên học phần</th>
<th class="py-2.5 px-2 border-r border-slate-300 w-14">Số TC</th>
<th class="py-2.5 px-2 border-r border-slate-300 w-20">Thang 10</th>
<th class="py-2.5 px-2 border-r border-slate-300 w-16">Điểm chữ</th>
<th class="py-2.5 px-2 border-r border-slate-300 w-16">Thang 4</th>
<th class="py-2.5 px-2 w-20">Kết quả</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-200">
<tr class="text-center">
<td class="py-2 px-2 border-r border-slate-200">1</td>
<td class="py-2 px-3 border-r border-slate-200 font-mono text-left">71ITEC3201</td>
<td class="py-2 px-3 border-r border-slate-200 text-left font-medium">Lập trình Web</td>
<td class="py-2 px-2 border-r border-slate-200">3</td>
<td class="py-2 px-2 border-r border-slate-200 font-bold">8.5</td>
<td class="py-2 px-2 border-r border-slate-200 font-semibold text-slate-800">A</td>
<td class="py-2 px-2 border-r border-slate-200 font-bold text-primary">4.0</td>
<td class="py-2 px-2 font-medium text-green-700">Đạt</td>
</tr>
<tr class="text-center bg-slate-50/50">
<td class="py-2 px-2 border-r border-slate-200">2</td>
<td class="py-2 px-3 border-r border-slate-200 font-mono text-left">71ITEC2104</td>
<td class="py-2 px-3 border-r border-slate-200 text-left font-medium">Cấu trúc dữ liệu &amp; GT</td>
<td class="py-2 px-2 border-r border-slate-200">4</td>
<td class="py-2 px-2 border-r border-slate-200 font-bold">8.5</td>
<td class="py-2 px-2 border-r border-slate-200 font-semibold text-slate-800">A</td>
<td class="py-2 px-2 border-r border-slate-200 font-bold text-primary">4.0</td>
<td class="py-2 px-2 font-medium text-green-700">Đạt</td>
</tr>
<tr class="text-center">
<td class="py-2 px-2 border-r border-slate-200">3</td>
<td class="py-2 px-3 border-r border-slate-200 font-mono text-left">71ITEC3208</td>
<td class="py-2 px-3 border-r border-slate-200 text-left font-medium">Mạng máy tính</td>
<td class="py-2 px-2 border-r border-slate-200">3</td>
<td class="py-2 px-2 border-r border-slate-200 font-bold">8.1</td>
<td class="py-2 px-2 border-r border-slate-200 font-semibold text-slate-800">B+</td>
<td class="py-2 px-2 border-r border-slate-200 font-bold text-primary">3.5</td>
<td class="py-2 px-2 font-medium text-green-700">Đạt</td>
</tr>
<tr class="text-center bg-slate-50/50">
<td class="py-2 px-2 border-r border-slate-200">4</td>
<td class="py-2 px-3 border-r border-slate-200 font-mono text-left">71MATH1101</td>
<td class="py-2 px-3 border-r border-slate-200 text-left font-medium">Đại số tuyến tính</td>
<td class="py-2 px-2 border-r border-slate-200">3</td>
<td class="py-2 px-2 border-r border-slate-200 font-bold">7.8</td>
<td class="py-2 px-2 border-r border-slate-200 font-semibold text-slate-800">B</td>
<td class="py-2 px-2 border-r border-slate-200 font-bold text-primary">3.0</td>
<td class="py-2 px-2 font-medium text-green-700">Đạt</td>
</tr>
<tr class="text-center">
<td class="py-2 px-2 border-r border-slate-200">5</td>
<td class="py-2 px-3 border-r border-slate-200 font-mono text-left">71LANG1201</td>
<td class="py-2 px-3 border-r border-slate-200 text-left font-medium">Tiếng Anh chuyên ngành</td>
<td class="py-2 px-2 border-r border-slate-200">2</td>
<td class="py-2 px-2 border-r border-slate-200 font-bold">9.0</td>
<td class="py-2 px-2 border-r border-slate-200 font-semibold text-slate-800">A</td>
<td class="py-2 px-2 border-r border-slate-200 font-bold text-primary">4.0</td>
<td class="py-2 px-2 font-medium text-green-700">Đạt</td>
</tr>
<tr class="text-center bg-slate-50/50">
<td class="py-2 px-2 border-r border-slate-200">6</td>
<td class="py-2 px-3 border-r border-slate-200 font-mono text-left">71ITEC2102</td>
<td class="py-2 px-3 border-r border-slate-200 text-left font-medium">An toàn thông tin</td>
<td class="py-2 px-2 border-r border-slate-200">3</td>
<td class="py-2 px-2 border-r border-slate-200 font-bold">8.0</td>
<td class="py-2 px-2 border-r border-slate-200 font-semibold text-slate-800">B</td>
<td class="py-2 px-2 border-r border-slate-200 font-bold text-primary">3.0</td>
<td class="py-2 px-2 font-medium text-green-700">Đạt</td>
</tr>
</tbody>
<tfoot class="bg-slate-100 font-semibold text-slate-800">
<tr>
<td class="py-2.5 px-3 border-r border-slate-300 font-bold" colspan="3">Tổng kết học kỳ:</td>
<td class="py-2.5 px-2 text-center border-r border-slate-300">18 TC</td>
<td class="py-2.5 px-2 text-center border-r border-slate-300 font-bold text-primary">8.45</td>
<td class="py-2.5 px-2 text-center border-r border-slate-300">A</td>
<td class="py-2.5 px-2 text-center border-r border-slate-300 font-bold text-primary">3.52</td>
<td class="py-2.5 px-2 text-center text-green-700">Đạt 100%</td>
</tr>
</tfoot>
</table>
<!-- Evaluation Summary Metrics -->
<div class="grid grid-cols-2 gap-4 text-xs border border-slate-200 rounded p-3 mb-8 bg-white">
<div class="space-y-1">
<p>• Số tín chỉ đăng ký học kỳ: <strong>18</strong> | Tín chỉ tích lũy học kỳ: <strong>18</strong></p>
<p>• Điểm trung bình chung học kỳ (hệ 10): <strong>8.45</strong></p>
<p>• Điểm trung bình chung học kỳ (hệ 4): <strong>3.52</strong></p>
<p>• Xếp loại học tập học kỳ: <strong class="text-primary uppercase">Giỏi</strong></p>
</div>
<div class="space-y-1">
<p>• Tổng số tín chỉ tích lũy toàn khóa: <strong>96 / 150</strong></p>
<p>• Điểm trung bình tích lũy toàn khóa (hệ 4): <strong>3.46</strong></p>
<p>• Điểm đánh giá rèn luyện: <strong>92 (Xuất sắc)</strong></p>
<p>• Tình trạng học vụ: <strong class="text-green-700">Bình thường (Đủ điều kiện tiếp tục học)</strong></p>
</div>
</div>
<!-- Official Signature Block & Digital Seal -->
<div class="grid grid-cols-3 gap-6 pt-4 text-center text-xs">
<!-- Col 1: Student -->
<div class="flex flex-col justify-between h-40">
<div>
<p class="font-bold text-slate-800">SINH VIÊN</p>
<p class="italic text-[11px] text-slate-500">(Ký và ghi rõ họ tên)</p>
</div>
<div>
<p class="font-semibold text-slate-700 italic text-[11px]">Nguyễn Văn An</p>
<p class="font-bold text-slate-800 uppercase mt-1">Nguyễn Văn An</p>
</div>
</div>
<!-- Col 2: Online Verification QR -->
<div class="flex flex-col items-center justify-center space-y-1">
<div class="w-24 h-24 border-2 border-primary/30 p-1 bg-white rounded flex items-center justify-center shadow-inner">
<span class="material-symbols-outlined text-6xl text-primary" data-icon="qr_code_2">qr_code_2</span>
</div>
<p class="text-[10px] text-slate-500 font-mono tracking-tight">VERIFY: HUIT-2024-210123</p>
<p class="text-[10px] text-primary italic font-medium">Quét mã kiểm tra tính hợp lệ</p>
</div>
<!-- Col 3: Academic Office Registrar -->
<div class="flex flex-col justify-between h-40">
<div>
<p class="font-bold text-slate-800 uppercase">TL. HIỆU TRƯỞNG</p>
<p class="font-bold text-primary uppercase text-[11px]">TRƯỞNG PHÒNG ĐÀO TẠO</p>
</div>
<div class="relative">
<!-- Red Seal Stamp Indicator -->
<div class="w-24 h-24 rounded-full border-2 border-dashed border-red-500/40 text-red-700 flex items-center justify-center mx-auto opacity-70 rotate-[-12deg] absolute -top-12 left-1/2 -translate-x-1/2 pointer-events-none text-[10px] font-bold text-center leading-tight">
                  ĐÃ XÁC THỰC<br/>PHÒNG ĐÀO TẠO<br/>HUIT
                </div>
<p class="font-serif italic text-primary font-bold text-sm">Lê Trung Đạo</p>
<p class="font-bold text-slate-900 mt-1">PGS. TS. Lê Trung Đạo</p>
</div>
</div>
</div>
<!-- Bottom Security Code & Legal Note -->
<div class="mt-8 pt-4 border-t border-slate-200 flex items-center justify-between text-[10px] text-slate-500">
<span>Văn bản trích xuất từ Cổng Thông tin Quản lý Đào tạo HUIT (https://sinhvien.huit.edu.vn)</span>
<span>Mã bảo mật điện tử: 98FC-42A1-B88E-2025</span>
</div>
</div>
</section>
<!-- Bottom Helper Banner -->
<footer class="mt-8 p-4 rounded-xl bg-surface-container border border-outline-variant flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-on-surface-variant">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-lg" data-icon="info">info</span>
<span>Nếu có bất kỳ thắc mắc hoặc sai sót về điểm học phần, sinh viên vui lòng nộp đơn phúc khảo tại Văn phòng một cửa (Nhà B) trong vòng 07 ngày làm việc kể từ ngày công bố điểm.</span>
</div>
<a class="text-primary font-semibold hover:underline flex items-center gap-1 shrink-0" href="#">
<span>Quy chế đào tạo HUIT</span>
<span class="material-symbols-outlined text-sm" data-icon="open_in_new">open_in_new</span>
</a>
</footer>
</main>
<!-- FOOTER -->
<footer class="py-4 px-6 bg-white border-t border-slate-200/80 text-center text-xs text-slate-500 no-print flex flex-col sm:flex-row items-center justify-between gap-2">
<div>© 2026 <strong>Trường Đại học Công Thương TP.HCM (HUIT)</strong> — Hệ thống Quản lý Học vụ.</div>
<div class="flex items-center gap-3 text-slate-500 text-[11px]">
<span>Hỗ trợ: 028 3816 1673</span>
<span>•</span>
<span>support@huit.edu.vn</span>
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
