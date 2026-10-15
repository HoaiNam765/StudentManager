@extends('fe_preview.layouts.student')
@section('page_title', 'Cổng Thông tin Sinh viên HUIT - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Cổng Thông tin Sinh viên HUIT - HUIT Học Vụ</title>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/ket-qua-dang-ky']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">fact_check</span>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Cổng Thông tin Sinh viên HUIT</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Cổng Thông tin Sinh viên HUIT</div>
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
<!-- PHẦN 1: DASHBOARD VIEW (KẾT QUẢ ĐĂNG KÝ HỌC PHẦN TRỰC TUYẾN) -->
<section class="space-y-6">
<!-- Header Section Info Banner -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-sm">
<div>
<div class="flex items-center gap-3">
<h1 class="font-headline-lg text-headline-lg text-on-surface">Kết quả đăng ký học phần trực tuyến</h1>
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                Cổng đăng ký đang mở
              </span>
</div>
<p class="font-body-md text-body-md text-secondary mt-1">
              Đợt đăng ký đại trà sinh viên Khóa 21 • Hạn chót điều chỉnh học phần: 17:00, 20/10/2026
            </p>
</div>
<div class="flex items-center gap-2 text-body-sm text-secondary bg-[#f8f9ff] px-4 py-2.5 rounded-lg border border-outline-variant">
<span class="material-symbols-outlined text-primary text-[20px]">verified_user</span>
<span>Thời gian máy chủ: <strong class="text-on-surface font-semibold" id="live-time">16/10/2026 10:24:18</strong></span>
</div>
</div>
<!-- HÀNG 4 THẺ TÓM TẮT NHANH (KPI SUMMARY CARDS) -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
<!-- Thẻ 1: Tổng tín chỉ đã đăng ký -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-sm flex flex-col justify-between relative overflow-hidden">
<div class="absolute -right-3 -top-3 w-16 h-16 bg-[#eff4ff] rounded-full flex items-center justify-center opacity-60">
<span class="material-symbols-outlined text-[#1c55c4] text-3xl">fact_check</span>
</div>
<div>
<span class="font-label-md text-label-md text-secondary uppercase tracking-wider">Tổng tín chỉ đã đăng ký</span>
<div class="flex items-baseline gap-2 mt-2">
<span class="text-3xl font-bold text-primary">18</span>
<span class="text-secondary font-medium">/ 24 TC</span>
</div>
</div>
<div class="mt-4 pt-3 border-t border-slate-100">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
<span class="material-symbols-outlined text-[14px]">check_circle</span>
                Hợp lệ - Đạt định mức tối thiểu 14 TC
              </span>
</div>
</div>
<!-- Thẻ 2: Số học phần chính thức -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-sm flex flex-col justify-between relative overflow-hidden">
<div class="absolute -right-3 -top-3 w-16 h-16 bg-[#eff4ff] rounded-full flex items-center justify-center opacity-60">
<span class="material-symbols-outlined text-[#1c55c4] text-3xl">menu_book</span>
</div>
<div>
<span class="font-label-md text-label-md text-secondary uppercase tracking-wider">Số học phần chính thức</span>
<div class="flex items-baseline gap-2 mt-2">
<span class="text-3xl font-bold text-on-surface">6</span>
<span class="text-secondary font-medium">học phần</span>
</div>
</div>
<div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-body-sm text-secondary">
<span>Mật độ lịch học:</span>
<span class="font-semibold text-on-surface">14 buổi học / tuần</span>
</div>
</div>
<!-- Thẻ 4: Trạng thái đợt -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-sm flex flex-col justify-between relative overflow-hidden">
<div class="absolute -right-3 -top-3 w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center opacity-60">
<span class="material-symbols-outlined text-emerald-700 text-3xl">task_alt</span>
</div>
<div>
<span class="font-label-md text-label-md text-secondary uppercase tracking-wider">Trạng thái đợt</span>
<div class="mt-2.5">
<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
<span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                  Đã xác nhận đăng ký chính thức
                </span>
</div>
</div>
<div class="mt-4 pt-3 border-t border-slate-100 text-body-sm text-secondary flex items-center gap-1">
<span class="material-symbols-outlined text-[16px] text-emerald-700">security</span>
<span>Dữ liệu đã khóa bảo mật học vụ</span>
</div>
</div>
</div>
<!-- BẢNG DANH SÁCH HỌC PHẦN ĐÃ ĐĂNG KÝ (9 CỘT, KHÔNG CẮT CHỮ) -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
<div class="px-6 py-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-3">
<div>
<h2 class="font-headline-sm text-headline-sm text-on-surface">Danh sách học phần đã gửi đăng ký</h2>
<p class="font-body-sm text-body-sm text-secondary">Chi tiết lịch học lý thuyết, thực hành và giảng viên phụ trách</p>
</div>
<div class="flex items-center gap-3">
<span class="text-body-sm text-secondary">Hiển thị 7 bản ghi</span>
<div class="h-4 w-px bg-slate-200"></div>
<button class="text-primary hover:text-[#15429b] text-body-sm font-semibold flex items-center gap-1">
<span class="material-symbols-outlined text-[18px]">sync</span>
                Làm mới trạng thái
              </button>
</div>
</div>
<!-- Bảng cuộn ngang -->
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse min-w-[1240px]">
<thead>
<tr class="bg-[#f8f9ff] border-b border-outline-variant text-[12px] font-semibold text-secondary uppercase tracking-wider">
<th class="py-3.5 px-4 text-center w-12">STT</th>
<th class="py-3.5 px-4 w-44">Mã lớp học phần</th>
<th class="py-3.5 px-4 min-w-[210px]">Tên học phần</th>
<th class="py-3.5 px-3 text-center w-16">Số TC</th>
<th class="py-3.5 px-4 min-w-[230px]">Lịch học &amp; Phòng</th>
<th class="py-3.5 px-4 min-w-[180px]">Giảng viên phụ trách</th>
<th class="py-3.5 px-4 w-36">Ngày đăng ký</th>
<th class="py-3.5 px-4 min-w-[190px]">Trạng thái đăng ký</th>
<th class="py-3.5 px-4 text-center w-28">Thao tác</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 text-body-sm text-on-surface">
<!-- Dòng 1 -->
<tr class="hover:bg-slate-50 transition-colors">
<td class="py-3 px-4 text-center text-secondary font-medium">1</td>
<td class="py-3 px-4 font-mono font-medium text-primary">241_71ITEC3201_01</td>
<td class="py-3 px-4 font-semibold text-on-surface">Lập trình Web</td>
<td class="py-3 px-3 text-center font-bold">3</td>
<td class="py-3 px-4 text-secondary">
<div class="flex items-center gap-1.5 text-on-surface">
<span class="material-symbols-outlined text-[16px] text-primary">calendar_today</span>
                      Thứ 2 Tiết 1-3
                    </div>
<div class="text-[12px] text-secondary ml-5">Phòng: A2.301</div>
</td>
<td class="py-3 px-4 text-secondary">TS. Trần Hoàng Nam</td>
<td class="py-3 px-4 text-secondary font-mono text-[12px]">15/10/2026 08:15</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
<span class="material-symbols-outlined text-[14px]">check</span>
                      Thành công
                    </span>
</td>
<td class="py-3 px-4 text-center">
<button class="text-rose-700 hover:text-rose-800 font-medium hover:underline text-body-sm inline-flex items-center gap-0.5" onclick="openModal('241_71ITEC3201_01', 'Lập trình Web', '3')">
<span class="material-symbols-outlined text-[16px]">cancel</span>
                      Hủy học phần
                    </button>
</td>
</tr>
<!-- Dòng 2 -->
<tr class="hover:bg-slate-50 transition-colors">
<td class="py-3 px-4 text-center text-secondary font-medium">2</td>
<td class="py-3 px-4 font-mono font-medium text-primary">241_71ITEC2104_01</td>
<td class="py-3 px-4 font-semibold text-on-surface">Cấu trúc dữ liệu &amp; GT</td>
<td class="py-3 px-3 text-center font-bold">4</td>
<td class="py-3 px-4 text-secondary">
<div class="flex items-center gap-1.5 text-on-surface">
<span class="material-symbols-outlined text-[16px] text-primary">calendar_today</span>
                      Thứ 3 Tiết 4-6
                    </div>
<div class="text-[12px] text-secondary ml-5">Phòng: B.204</div>
</td>
<td class="py-3 px-4 text-secondary">ThS. Lê Quang Huy</td>
<td class="py-3 px-4 text-secondary font-mono text-[12px]">15/10/2026 08:18</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
<span class="material-symbols-outlined text-[14px]">check</span>
                      Thành công
                    </span>
</td>
<td class="py-3 px-4 text-center">
<button class="text-rose-700 hover:text-rose-800 font-medium hover:underline text-body-sm inline-flex items-center gap-0.5" onclick="openModal('241_71ITEC2104_01', 'Cấu trúc dữ liệu &amp; GT', '4')">
<span class="material-symbols-outlined text-[16px]">cancel</span>
                      Hủy học phần
                    </button>
</td>
</tr>
<!-- Dòng 3 -->
<tr class="hover:bg-slate-50 transition-colors">
<td class="py-3 px-4 text-center text-secondary font-medium">3</td>
<td class="py-3 px-4 font-mono font-medium text-primary">241_71MATH1101_01</td>
<td class="py-3 px-4 font-semibold text-on-surface">Đại số tuyến tính</td>
<td class="py-3 px-3 text-center font-bold">3</td>
<td class="py-3 px-4 text-secondary">
<div class="flex items-center gap-1.5 text-on-surface">
<span class="material-symbols-outlined text-[16px] text-primary">calendar_today</span>
                      Thứ 4 Tiết 1-3
                    </div>
<div class="text-[12px] text-secondary ml-5">Phòng: A1.102</div>
</td>
<td class="py-3 px-4 text-secondary">TS. Võ Văn Hùng</td>
<td class="py-3 px-4 text-secondary font-mono text-[12px]">15/10/2026 08:22</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
<span class="material-symbols-outlined text-[14px]">check</span>
                      Thành công
                    </span>
</td>
<td class="py-3 px-4 text-center">
<button class="text-rose-700 hover:text-rose-800 font-medium hover:underline text-body-sm inline-flex items-center gap-0.5" onclick="openModal('241_71MATH1101_01', 'Đại số tuyến tính', '3')">
<span class="material-symbols-outlined text-[16px]">cancel</span>
                      Hủy học phần
                    </button>
</td>
</tr>
<!-- Dòng 4 -->
<tr class="hover:bg-slate-50 transition-colors">
<td class="py-3 px-4 text-center text-secondary font-medium">4</td>
<td class="py-3 px-4 font-mono font-medium text-primary">241_71ITEC2102_02</td>
<td class="py-3 px-4 font-semibold text-on-surface">An toàn thông tin</td>
<td class="py-3 px-3 text-center font-bold">3</td>
<td class="py-3 px-4 text-secondary">
<div class="flex items-center gap-1.5 text-on-surface">
<span class="material-symbols-outlined text-[16px] text-primary">calendar_today</span>
                      Thứ 6 Tiết 1-3
                    </div>
<div class="text-[12px] text-secondary ml-5">Phòng: B.105</div>
</td>
<td class="py-3 px-4 text-secondary">ThS. Đỗ Văn Cường</td>
<td class="py-3 px-4 text-secondary font-mono text-[12px]">15/10/2026 08:25</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
<span class="material-symbols-outlined text-[14px]">check</span>
                      Thành công
                    </span>
</td>
<td class="py-3 px-4 text-center">
<button class="text-rose-700 hover:text-rose-800 font-medium hover:underline text-body-sm inline-flex items-center gap-0.5" onclick="openModal('241_71ITEC2102_02', 'An toàn thông tin', '3')">
<span class="material-symbols-outlined text-[16px]">cancel</span>
                      Hủy học phần
                    </button>
</td>
</tr>
<!-- Dòng 5 -->
<tr class="hover:bg-slate-50 transition-colors">
<td class="py-3 px-4 text-center text-secondary font-medium">5</td>
<td class="py-3 px-4 font-mono font-medium text-primary">241_71ITEC3208_03</td>
<td class="py-3 px-4 font-semibold text-on-surface">Mạng máy tính</td>
<td class="py-3 px-3 text-center font-bold">3</td>
<td class="py-3 px-4 text-secondary">
<div class="flex items-center gap-1.5 text-on-surface">
<span class="material-symbols-outlined text-[16px] text-primary">calendar_today</span>
                      Thứ 5 Tiết 7-9
                    </div>
<div class="text-[12px] text-secondary ml-5">Lab: PM04</div>
</td>
<td class="py-3 px-4 text-secondary">PGS.TS. Vũ Đình Trọng</td>
<td class="py-3 px-4 text-secondary font-mono text-[12px]">15/10/2026 08:31</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
<span class="material-symbols-outlined text-[14px]">check</span>
                      Thành công
                    </span>
</td>
<td class="py-3 px-4 text-center">
<button class="text-rose-700 hover:text-rose-800 font-medium hover:underline text-body-sm inline-flex items-center gap-0.5" onclick="openModal('241_71ITEC3208_03', 'Mạng máy tính', '3')">
<span class="material-symbols-outlined text-[16px]">cancel</span>
                      Hủy học phần
                    </button>
</td>
</tr>
<!-- Dòng 6: Chờ xử lý ngoại lệ -->
<tr class="hover:bg-amber-50/40 bg-amber-50/20 transition-colors">
<td class="py-3 px-4 text-center text-secondary font-medium">6</td>
<td class="py-3 px-4 font-mono font-medium text-amber-800">241_71LANG1201_04</td>
<td class="py-3 px-4 font-semibold text-on-surface">Tiếng Anh chuyên ngành CNTT</td>
<td class="py-3 px-3 text-center font-bold">2</td>
<td class="py-3 px-4 text-secondary">
<div class="flex items-center gap-1.5 text-on-surface">
<span class="material-symbols-outlined text-[16px] text-amber-700">calendar_today</span>
                      Thứ 7 Tiết 1-3
                    </div>
<div class="text-[12px] text-secondary ml-5">Phòng: A2.205</div>
</td>
<td class="py-3 px-4 text-secondary">ThS. Hoàng Minh Đức</td>
<td class="py-3 px-4 text-secondary font-mono text-[12px]">15/10/2026 09:12</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
<span class="material-symbols-outlined text-[14px]">hourglass_top</span>
                      Chờ xử lý ngoại lệ
                    </span>
</td>
<td class="py-3 px-4 text-center">
<button class="text-rose-700 hover:text-rose-800 font-medium hover:underline text-body-sm inline-flex items-center gap-0.5" onclick="openModal('241_71LANG1201_04', 'Tiếng Anh chuyên ngành CNTT', '2')">
<span class="material-symbols-outlined text-[16px]">cancel</span>
                      Hủy học phần
                    </button>
</td>
</tr>
<!-- Dòng 7: Bị từ chối -->
<tr class="hover:bg-rose-50/40 bg-rose-50/20 transition-colors opacity-90">
<td class="py-3 px-4 text-center text-secondary font-medium">7</td>
<td class="py-3 px-4 font-mono font-medium text-rose-700">241_71ITEC4101_01</td>
<td class="py-3 px-4 font-semibold text-rose-950">Phát triển ứng dụng Di động</td>
<td class="py-3 px-3 text-center font-bold text-rose-800">3</td>
<td class="py-3 px-4 text-secondary italic">
                    Chưa xếp (Lớp đã đầy)
                  </td>
<td class="py-3 px-4 text-secondary">ThS. Nguyễn Văn Tuấn</td>
<td class="py-3 px-4 text-secondary font-mono text-[12px]">15/10/2026 09:45</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 border border-rose-200">
<span class="material-symbols-outlined text-[14px]">error</span>
                      Bị từ chối: Lớp đã đủ sĩ số tối đa
                    </span>
</td>
<td class="py-3 px-4 text-center">
<button class="text-slate-500 font-medium text-body-sm cursor-not-allowed inline-flex items-center gap-0.5" disabled="">
<span class="material-symbols-outlined text-[16px]">block</span>
                      Đã loại
                    </button>
</td>
</tr>
</tbody>
</table>
</div>
<div class="px-6 py-3.5 bg-[#f8f9ff] border-t border-slate-100 flex items-center justify-between text-body-sm text-secondary">
<div class="flex items-center gap-2">
<span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
<span>Tổng học phần được chấp nhận chính thức: <strong>6 học phần</strong> (18 tín chỉ)</span>
</div>
<div class="text-[12px] text-secondary">
              Lưu ý: Học phần chờ xử lý ngoại lệ sẽ được Phòng Đào tạo giải quyết trước ngày 18/10/2026.
            </div>
</div>
</div>
</section>
<!-- ========================================================================= -->
<!-- PHẦN 2: BẢN XEM TRƯỚC PHIẾU ĐĂNG KÝ HỌC PHẦN KHỔ IN A4 (PRINT PREVIEW PAPER CARD) -->
<!-- ========================================================================= -->
<section class="space-y-4 pt-4">
<div class="flex items-center justify-between">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[28px]">picture_as_pdf</span>
<div>
<h2 class="font-headline-lg text-headline-lg text-on-surface">Bản xem trước Phiếu đăng ký học phần (Khổ in A4)</h2>
<p class="font-body-sm text-body-sm text-secondary">Mẫu văn bản hành chính học vụ chính thức dùng để lưu trữ và nộp xác nhận đào tạo</p>
</div>
</div>
<div class="flex items-center gap-3">
<span class="inline-flex items-center gap-1 text-xs font-medium text-slate-500 bg-white border border-slate-200 px-3 py-1.5 rounded-md">
<span class="material-symbols-outlined text-[16px]">aspect_ratio</span>
              Kích thước: 210mm x 297mm (A4 Chuẩn)
            </span>
<button class="bg-[#1c55c4] hover:bg-[#15429b] text-white px-4 py-2 rounded-lg font-medium text-body-sm flex items-center gap-2 shadow-sm transition-colors" onclick="window.print()">
<span class="material-symbols-outlined text-[18px]">print</span>
<span>In ngay trang này</span>
</button>
</div>
</div>
<!-- Khung giấy A4 giả lập (Print Preview Paper Sheet) -->
<div class="bg-slate-200/80 p-6 md:p-10 rounded-2xl flex justify-center border border-slate-300">
<div class="print-sheet bg-white text-slate-900 w-full max-w-[850px] min-h-[1180px] p-12 md:p-14 border border-slate-300 rounded-sm flex flex-col justify-between font-serif relative" id="print-preview-section">
<div>
<!-- 1. QUỐC HIỆU & TIÊU NGỮ + BỘ CÔNG THƯƠNG -->
<div class="grid grid-cols-2 gap-4 pb-6 border-b border-slate-300">
<!-- Cột trái: Bộ & Trường -->
<div class="text-center font-sans">
<div class="text-[12px] uppercase tracking-wider text-slate-700">BỘ CÔNG THƯƠNG</div>
<div class="text-[13px] font-bold uppercase text-slate-900 mt-0.5 leading-snug">
                    TRƯỜNG ĐẠI HỌC CÔNG THƯƠNG<br/>TP. HỒ CHÍ MINH
                  </div>
<div class="text-[12px] font-semibold text-slate-700 mt-1">PHÒNG ĐÀO TẠO</div>
<div class="w-24 h-[1px] bg-slate-400 mx-auto mt-1"></div>
</div>
<!-- Cột phải: Quốc hiệu - Tiêu ngữ -->
<div class="text-center font-sans">
<div class="text-[13px] font-bold uppercase tracking-wide text-slate-900">
                    CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM
                  </div>
<div class="text-[13px] font-semibold text-slate-800 mt-0.5">
                    Độc lập - Tự do - Hạnh phúc
                  </div>
<div class="w-36 h-[1.5px] bg-slate-800 mx-auto mt-1.5"></div>
<div class="text-[11px] italic text-slate-600 mt-3 font-serif">
                    TP. Hồ Chí Minh, ngày 16 tháng 10 năm 2026
                  </div>
</div>
</div>
<!-- 2. TIÊU ĐỀ PHIẾU ĐĂNG KÝ -->
<div class="text-center my-6">
<h3 class="font-sans text-xl md:text-2xl font-bold uppercase tracking-wide text-slate-950">
                  PHIẾU ĐĂNG KÝ HỌC PHẦN
                </h3>
<div class="font-sans text-sm font-semibold uppercase tracking-wider text-slate-700 mt-1">
                  HỌC KỲ 1 - NĂM HỌC 2026 - 2027
                </div>
<div class="text-[11px] italic text-slate-500 mt-1">
                  (Dành cho sinh viên hệ Chính quy - Lưu hồ sơ cá nhân và đối chiếu học vụ)
                </div>
</div>
<!-- 3. KHỐI THÔNG TIN SINH VIÊN IN ẤN -->
<div class="bg-slate-50/70 p-4 border border-slate-200 rounded text-xs font-sans mb-6">
<div class="grid grid-cols-2 md:grid-cols-3 gap-y-2.5 gap-x-4">
<div>
<span class="text-slate-600">Họ và tên:</span>
<strong class="ml-1 text-slate-900 uppercase font-semibold">Nguyễn Văn An</strong>
</div>
<div>
<span class="text-slate-600">Mã số sinh viên:</span>
<strong class="ml-1 text-slate-900 font-mono font-semibold">2001210123</strong>
</div>
<div>
<span class="text-slate-600">Ngày sinh:</span>
<span class="ml-1 text-slate-900 font-medium">15/08/2003</span>
</div>
<div>
<span class="text-slate-600">Lớp sinh hoạt:</span>
<strong class="ml-1 text-slate-900 font-semibold">12DHTH01</strong>
</div>
<div>
<span class="text-slate-600">Khóa học:</span>
<span class="ml-1 text-slate-900">2021 (K21 - ĐHCQ)</span>
</div>
<div>
<span class="text-slate-600">Ngành học:</span>
<span class="ml-1 text-slate-900 font-medium">Công nghệ Thông tin</span>
</div>
</div>
</div>
<!-- 4. BẢNG HỌC PHẦN IN ẤN CHÍNH THỨC (6 HỌC PHẦN HỢP LỆ) -->
<div class="font-sans mb-4 overflow-x-auto">
<div class="text-xs font-bold uppercase tracking-wider text-slate-800 mb-2">
                  I. Danh sách học phần đã ghi nhận thành công:
                </div>
<table class="w-full border-collapse border border-slate-400 text-[11px] leading-tight">
<thead>
<tr class="bg-slate-100 text-slate-900 font-bold border-b border-slate-400">
<th class="border border-slate-400 py-2 px-1 text-center w-8">STT</th>
<th class="border border-slate-400 py-2 px-2 text-center w-32">Mã lớp HP</th>
<th class="border border-slate-400 py-2 px-2 text-left">Tên học phần</th>
<th class="border border-slate-400 py-2 px-1 text-center w-12">Số TC</th>
<th class="border border-slate-400 py-2 px-2 text-center w-28">Lịch học (Thứ, Tiết)</th>
<th class="border border-slate-400 py-2 px-1 text-center w-16">Phòng</th>
<th class="border border-slate-400 py-2 px-2 text-left w-36">Giảng viên</th>
<th class="border border-slate-400 py-2 px-1 text-center w-20">Ký xác nhận</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-300">
<tr>
<td class="border border-slate-400 py-1.5 px-1 text-center">1</td>
<td class="border border-slate-400 py-1.5 px-2 font-mono text-center">241_71ITEC3201_01</td>
<td class="border border-slate-400 py-1.5 px-2 font-semibold">Lập trình Web</td>
<td class="border border-slate-400 py-1.5 px-1 text-center font-bold">3</td>
<td class="border border-slate-400 py-1.5 px-2 text-center">Thứ 2 (1-3)</td>
<td class="border border-slate-400 py-1.5 px-1 text-center">A2.301</td>
<td class="border border-slate-400 py-1.5 px-2">TS. Trần Hoàng Nam</td>
<td class="border border-slate-400 py-1.5 px-1 text-center italic text-slate-500">Đã duyệt</td>
</tr>
<tr>
<td class="border border-slate-400 py-1.5 px-1 text-center">2</td>
<td class="border border-slate-400 py-1.5 px-2 font-mono text-center">241_71ITEC2104_01</td>
<td class="border border-slate-400 py-1.5 px-2 font-semibold">Cấu trúc dữ liệu &amp; GT</td>
<td class="border border-slate-400 py-1.5 px-1 text-center font-bold">4</td>
<td class="border border-slate-400 py-1.5 px-2 text-center">Thứ 3 (4-6)</td>
<td class="border border-slate-400 py-1.5 px-1 text-center">B.204</td>
<td class="border border-slate-400 py-1.5 px-2">ThS. Lê Quang Huy</td>
<td class="border border-slate-400 py-1.5 px-1 text-center italic text-slate-500">Đã duyệt</td>
</tr>
<tr>
<td class="border border-slate-400 py-1.5 px-1 text-center">3</td>
<td class="border border-slate-400 py-1.5 px-2 font-mono text-center">241_71MATH1101_01</td>
<td class="border border-slate-400 py-1.5 px-2 font-semibold">Đại số tuyến tính</td>
<td class="border border-slate-400 py-1.5 px-1 text-center font-bold">3</td>
<td class="border border-slate-400 py-1.5 px-2 text-center">Thứ 4 (1-3)</td>
<td class="border border-slate-400 py-1.5 px-1 text-center">A1.102</td>
<td class="border border-slate-400 py-1.5 px-2">TS. Võ Văn Hùng</td>
<td class="border border-slate-400 py-1.5 px-1 text-center italic text-slate-500">Đã duyệt</td>
</tr>
<tr>
<td class="border border-slate-400 py-1.5 px-1 text-center">4</td>
<td class="border border-slate-400 py-1.5 px-2 font-mono text-center">241_71ITEC2102_02</td>
<td class="border border-slate-400 py-1.5 px-2 font-semibold">An toàn thông tin</td>
<td class="border border-slate-400 py-1.5 px-1 text-center font-bold">3</td>
<td class="border border-slate-400 py-1.5 px-2 text-center">Thứ 6 (1-3)</td>
<td class="border border-slate-400 py-1.5 px-1 text-center">B.105</td>
<td class="border border-slate-400 py-1.5 px-2">ThS. Đỗ Văn Cường</td>
<td class="border border-slate-400 py-1.5 px-1 text-center italic text-slate-500">Đã duyệt</td>
</tr>
<tr>
<td class="border border-slate-400 py-1.5 px-1 text-center">5</td>
<td class="border border-slate-400 py-1.5 px-2 font-mono text-center">241_71ITEC3208_03</td>
<td class="border border-slate-400 py-1.5 px-2 font-semibold">Mạng máy tính</td>
<td class="border border-slate-400 py-1.5 px-1 text-center font-bold">3</td>
<td class="border border-slate-400 py-1.5 px-2 text-center">Thứ 5 (7-9)</td>
<td class="border border-slate-400 py-1.5 px-1 text-center">Lab PM04</td>
<td class="border border-slate-400 py-1.5 px-2">PGS.TS. Vũ Đình Trọng</td>
<td class="border border-slate-400 py-1.5 px-1 text-center italic text-slate-500">Đã duyệt</td>
</tr>
<tr>
<td class="border border-slate-400 py-1.5 px-1 text-center">6</td>
<td class="border border-slate-400 py-1.5 px-2 font-mono text-center">241_71LANG1201_04</td>
<td class="border border-slate-400 py-1.5 px-2 font-semibold">Tiếng Anh chuyên ngành CNTT</td>
<td class="border border-slate-400 py-1.5 px-1 text-center font-bold">2</td>
<td class="border border-slate-400 py-1.5 px-2 text-center">Thứ 7 (1-3)</td>
<td class="border border-slate-400 py-1.5 px-1 text-center">A2.205</td>
<td class="border border-slate-400 py-1.5 px-2">ThS. Hoàng Minh Đức</td>
<td class="border border-slate-400 py-1.5 px-1 text-center italic text-slate-500">Đã duyệt</td>
</tr>
</tbody>
</table>
</div>
<!-- 5. DÒNG TỔNG KẾT TÍN CHỈ VÀ HỌC PHÍ -->
<div class="font-sans text-xs bg-slate-50 border border-slate-300 p-3 rounded space-y-1 mb-8">
<div class="flex items-center justify-between">
<span><strong>Tổng số tín chỉ đăng ký hợp lệ:</strong> 18 tín chỉ (Mười tám tín chỉ).</span>
<span><strong>Tổng số học phần:</strong> 06 học phần.</span>
</div>
</div>
<!-- 6. PHẦN KÝ TÊN XÁC NHẬN HỌC VỤ 2 BÊN -->
<div class="grid grid-cols-2 gap-8 text-center font-sans pt-2">
<!-- Bên trái: Trưởng phòng đào tạo -->
<div>
<div class="text-xs font-bold uppercase text-slate-900">TRƯỞNG PHÒNG ĐÀO TẠO</div>
<div class="text-[11px] italic text-slate-600 mt-0.5">(Ký tên, đóng dấu xác nhận của Nhà trường)</div>
<!-- Khung con dấu học vụ mô phỏng -->
<div class="h-28 flex items-center justify-center relative">
<div class="w-24 h-24 border-2 border-dashed border-red-500 rounded-full flex flex-col items-center justify-center text-red-800 text-[10px] uppercase font-bold leading-none rotate-[-8deg]">
<span>ĐÃ ĐỐI CHIẾU</span>
<span class="text-[9px] mt-1 text-red-800">P. ĐÀO TẠO</span>
<span class="text-[8px] mt-1 text-slate-700">16-10-2026</span>
</div>
</div>
<div class="text-xs font-bold text-slate-900 uppercase">PGS. TS. Lê Trung Đạo</div>
</div>
<!-- Bên phải: Sinh viên ký tên -->
<div>
<div class="text-xs font-bold uppercase text-slate-900">SINH VIÊN KÝ TÊN</div>
<div class="text-[11px] italic text-slate-600 mt-0.5">(Ký và ghi rõ họ tên)</div>
<!-- Mô phỏng chữ ký tay mềm mại -->
<div class="h-28 flex flex-col items-center justify-center">
<span class="font-serif italic text-2xl text-blue-900 tracking-wider">NguyenVanAn</span>
</div>
<div class="text-xs font-bold text-slate-900 uppercase">Nguyễn Văn An</div>
</div>
</div>
</div>
<!-- 7. CHÂN PHIẾU IN: MÃ VẠCH / QR CODE XÁC THỰC VĂN BẢN ĐIỆN TỬ -->
<div class="border-t border-slate-300 pt-4 mt-8 flex items-center justify-between font-sans text-[10px] text-slate-500">
<div class="flex items-center gap-3">
<!-- Barcode SVG / QR Code mock -->
<div class="w-12 h-12 bg-slate-100 border border-slate-300 p-1 flex items-center justify-center rounded">
<span class="material-symbols-outlined text-[32px] text-slate-800">qr_code_2</span>
</div>
<div>
<div class="font-mono font-semibold text-slate-800">MÃ VĂN BẢN: HUIT-DKHP-20261-2001210123</div>
<div>Tra cứu tính hợp lệ tại: <span class="underline text-primary">portal.huit.edu.vn/verify</span></div>
<div>Thời điểm in: 16/10/2026 10:24:18 (IP: 115.79.45.12)</div>
</div>
</div>
<div class="text-right">
<div class="font-medium text-slate-700">Trang 1 / 1</div>
<div>Hệ thống Quản lý Đào tạo HUIT ERP</div>
</div>
</div>
</div>
</div>
</section>
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
