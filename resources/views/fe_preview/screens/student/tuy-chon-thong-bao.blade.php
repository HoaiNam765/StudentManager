@extends('fe_preview.layouts.student')
@section('page_title', 'Tùy chọn thông báo - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="light"@endsection
@section('body_attributes')class="bg-[#f8f9ff] text-slate-800 antialiased min-h-screen flex selection:bg-blue-100 selection:text-blue-900"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Tùy chọn thông báo - HUIT Học Vụ</title>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Tùy chọn thông báo</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Tùy chọn thông báo</div>
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
<!-- Page Title & Navigation Subtabs -->
<div class="mb-8">
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
<div>
<h1 class="font-headline-lg text-headline-lg font-bold text-on-surface tracking-tight">Tùy chọn thông báo</h1>
<p class="font-body-md text-body-md text-on-surface-variant mt-1 max-w-3xl">
              Tùy chỉnh phương thức nhận tin, loại nội dung và tần suất gửi thông báo từ Nhà trường để không bỏ lỡ các thông tin học vụ quan trọng.
            </p>
</div>
<!-- Quick Status Indicator -->
<div class="flex items-center gap-2 self-start md:self-auto bg-surface-container-lowest border border-outline-variant/80 rounded-xl px-4 py-2 shadow-sm">
<span class="material-symbols-outlined text-primary text-[20px]" style="font-variation-settings: 'FILL' 1;">mark_email_read</span>
<div class="text-left">
<div class="font-label-sm text-label-sm text-on-surface-variant">Trạng thái hòm thư</div>
<div class="font-label-md text-label-md font-semibold text-on-surface">Đã xác thực SV HUIT</div>
</div>
</div>
</div>
<!-- Horizontal Account Setting Tabs -->
<div class="flex items-center gap-2 border-b border-outline-variant/60 pb-px">
<a class="px-4 py-2.5 font-label-md text-label-md text-on-surface-variant hover:text-on-surface hover:border-b-2 hover:border-outline-variant transition-colors" href="#">
            Cài đặt tài khoản
          </a>
<a class="px-4 py-2.5 font-label-md text-label-md text-on-surface-variant hover:text-on-surface hover:border-b-2 hover:border-outline-variant transition-colors" href="#">
            Đổi mật khẩu
          </a>
<a class="px-4 py-2.5 font-label-md text-label-md text-on-surface-variant hover:text-on-surface hover:border-b-2 hover:border-outline-variant transition-colors" href="#">
            Phiên đăng nhập
          </a>
<a class="px-4 py-2.5 font-label-md text-label-md font-bold text-primary border-b-2 border-primary bg-primary-fixed/20 rounded-t-lg transition-colors" href="#">
            Tùy chọn thông báo
          </a>
</div>
</div>
<div class="space-y-6">
<!-- Section 1: Kênh thông báo (Notification Channels) -->
<section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm">
<div class="flex items-center justify-between pb-4 mb-5 border-b border-surface-container-high">
<div class="flex items-center gap-2.5">
<span class="material-symbols-outlined text-primary text-[22px]">cell_tower</span>
<h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">1. Kênh thông báo</h2>
</div>
<span class="font-label-sm text-label-sm text-on-surface-variant bg-surface-container-low px-2.5 py-1 rounded-full border border-outline-variant/50">
              4 phương thức liên lạc
            </span>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
<!-- Channel 1: Email trường (TOGGLE ON) -->
<div class="p-4 rounded-xl border border-outline-variant bg-surface-container-low/30 hover:border-primary/50 transition-all flex items-start justify-between">
<div class="flex items-start gap-3.5">
<div class="p-2.5 rounded-lg bg-primary-fixed text-primary mt-0.5">
<span class="material-symbols-outlined text-[22px]">mail</span>
</div>
<div>
<div class="flex items-center gap-2">
<h3 class="font-label-lg text-label-lg font-bold text-on-surface">Email trường</h3>
<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">Mặc định</span>
</div>
<p class="font-label-md text-label-md text-primary font-mono mt-0.5">nguoidung019@example.test</p>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-1.5">
                    Nhận thông báo qua hòm thư chính thức của sinh viên.
                  </p>
</div>
</div>
<!-- Toggle Switch ON -->
<label class="relative inline-flex items-center cursor-pointer ml-3 flex-shrink-0">
<input aria-label="Chọn dòng" checked="" class="sr-only peer" type="checkbox"/>
<div class="w-11 h-6 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
</label>
</div>
<!-- Channel 2: SMS (TOGGLE OFF) -->
<div class="p-4 rounded-xl border border-outline-variant bg-surface-container-low/30 hover:border-primary/50 transition-all flex items-start justify-between">
<div class="flex items-start gap-3.5">
<div class="p-2.5 rounded-lg bg-surface-container-high text-on-surface-variant mt-0.5">
<span class="material-symbols-outlined text-[22px]">sms</span>
</div>
<div>
<div class="flex items-center gap-2">
<h3 class="font-label-lg text-label-lg font-bold text-on-surface">Tin nhắn SMS</h3>
<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-surface-container text-on-surface-variant">Khẩn cấp</span>
</div>
<p class="font-label-md text-label-md text-on-surface font-mono mt-0.5">0987 *** 321</p>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-1.5">
                    Nhận tin nhắn các thông báo khẩn cấp qua số điện thoại.
                  </p>
</div>
</div>
<!-- Toggle Switch OFF -->
<label class="relative inline-flex items-center cursor-pointer ml-3 flex-shrink-0">
<input aria-label="Chọn dòng" class="sr-only peer" type="checkbox"/>
<div class="w-11 h-6 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
</label>
</div>
<!-- Channel 3: In-app (TOGGLE ON) -->
<div class="p-4 rounded-xl border border-outline-variant bg-surface-container-low/30 hover:border-primary/50 transition-all flex items-start justify-between">
<div class="flex items-start gap-3.5">
<div class="p-2.5 rounded-lg bg-primary-fixed text-primary mt-0.5">
<span class="material-symbols-outlined text-[22px]">notifications</span>
</div>
<div>
<div class="flex items-center gap-2">
<h3 class="font-label-lg text-label-lg font-bold text-on-surface">Thông báo trên hệ thống</h3>
<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">Hoạt động</span>
</div>
<p class="font-label-md text-label-md text-on-surface font-medium mt-0.5">Cổng học vụ web &amp; LMS</p>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-1.5">
                    Hiển thị chuông thông báo và thanh tiêu điểm trên cổng học vụ.
                  </p>
</div>
</div>
<!-- Toggle Switch ON -->
<label class="relative inline-flex items-center cursor-pointer ml-3 flex-shrink-0">
<input aria-label="Chọn dòng" checked="" class="sr-only peer" type="checkbox"/>
<div class="w-11 h-6 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
</label>
</div>
<!-- Channel 4: Web Push (TOGGLE OFF) -->
<div class="p-4 rounded-xl border border-outline-variant bg-surface-container-low/30 hover:border-primary/50 transition-all flex items-start justify-between">
<div class="flex items-start gap-3.5">
<div class="p-2.5 rounded-lg bg-surface-container-high text-on-surface-variant mt-0.5">
<span class="material-symbols-outlined text-[22px]">campaign</span>
</div>
<div>
<div class="flex items-center gap-2">
<h3 class="font-label-lg text-label-lg font-bold text-on-surface">Thông báo đẩy trình duyệt</h3>
<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-800">Chưa cấp quyền</span>
</div>
<p class="font-label-md text-label-md text-on-surface font-medium mt-0.5">Chrome / Edge / Firefox</p>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-1.5">
                    Nhận cảnh báo tức thì ngay cả khi không mở tab học vụ.
                  </p>
</div>
</div>
<!-- Toggle Switch OFF -->
<label class="relative inline-flex items-center cursor-pointer ml-3 flex-shrink-0">
<input aria-label="Chọn dòng" class="sr-only peer" type="checkbox"/>
<div class="w-11 h-6 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
</label>
</div>
</div>
</section>
<!-- Section 2: Loại thông báo & Phân loại học vụ (Notification Categories) -->
<section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm">
<div class="flex items-center justify-between pb-4 mb-5 border-b border-surface-container-high">
<div class="flex items-center gap-2.5">
<span class="material-symbols-outlined text-primary text-[22px]">tune</span>
<div>
<h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">2. Loại thông báo &amp; Phân loại học vụ</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Chọn lọc những luồng thông tin học thuật bạn muốn tiếp nhận định kỳ.</p>
</div>
</div>
<div class="flex items-center gap-2">
<button class="font-label-sm text-label-sm text-primary hover:underline px-2 py-1">Bật tất cả</button>
<span class="text-outline-variant">|</span>
<button class="font-label-sm text-label-sm text-on-surface-variant hover:text-on-surface px-2 py-1">Tắt tất cả</button>
</div>
</div>
<!-- Academic Categories Table Structure -->
<div class="divide-y divide-outline-variant/50">
<!-- Category Item 1 (ON) -->
<div class="py-4 flex items-center justify-between hover:bg-surface-container-low/40 px-3 rounded-lg transition-colors">
<div class="flex items-start gap-3.5 pr-4">
<div class="p-2 rounded-lg bg-blue-50 text-primary mt-0.5">
<span class="material-symbols-outlined text-[20px]">event_note</span>
</div>
<div>
<div class="flex items-center gap-2">
<span class="font-label-lg text-label-lg font-bold text-on-surface">Lịch học và bài tập</span>
<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">Quan trọng</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">
                    Lịch học bù, đổi phòng học, hạn nộp bài tập lớn và bài kiểm tra trên LMS.
                  </p>
</div>
</div>
<label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
<input aria-label="Chọn dòng" checked="" class="sr-only peer" type="checkbox"/>
<div class="w-11 h-6 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
</label>
</div>
<!-- Category Item 2 (ON) -->
<div class="py-4 flex items-center justify-between hover:bg-surface-container-low/40 px-3 rounded-lg transition-colors">
<div class="flex items-start gap-3.5 pr-4">
<div class="p-2 rounded-lg bg-blue-50 text-primary mt-0.5">
<span class="material-symbols-outlined text-[20px]">grading</span>
</div>
<div>
<div class="flex items-center gap-2">
<span class="font-label-lg text-label-lg font-bold text-on-surface">Kết quả điểm học tập</span>
<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">Quan trọng</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">
                    Công bố điểm thi, điểm quá trình, phúc khảo và xếp loại học lực.
                  </p>
</div>
</div>
<label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
<input aria-label="Chọn dòng" checked="" class="sr-only peer" type="checkbox"/>
<div class="w-11 h-6 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
</label>
</div>
<!-- Category Item 3 (ON) -->
<div class="py-4 flex items-center justify-between hover:bg-surface-container-low/40 px-3 rounded-lg transition-colors">
<div class="flex items-start gap-3.5 pr-4">
<div class="p-2 rounded-lg bg-amber-50 text-amber-700 mt-0.5">
<span class="material-symbols-outlined text-[20px]">request_quote</span>
</div>
<div>
<div class="flex items-center gap-2">
<span class="font-label-lg text-label-lg font-bold text-on-surface">Thông báo học vụ</span>
<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-800">Tài chính vụ</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">
                    Nhắc hạn đóng học vụ, phát hành hóa đơn điện tử, thông báo miễn giảm.
                  </p>
</div>
</div>
<label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
<input aria-label="Chọn dòng" checked="" class="sr-only peer" type="checkbox"/>
<div class="w-11 h-6 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
</label>
</div>
<!-- Category Item 4 (ON) -->
<div class="py-4 flex items-center justify-between hover:bg-surface-container-low/40 px-3 rounded-lg transition-colors">
<div class="flex items-start gap-3.5 pr-4">
<div class="p-2 rounded-lg bg-blue-50 text-primary mt-0.5">
<span class="material-symbols-outlined text-[20px]">how_to_reg</span>
</div>
<div>
<div class="flex items-center gap-2">
<span class="font-label-lg text-label-lg font-bold text-on-surface">Cập nhật đăng ký học phần</span>
<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-100 text-blue-800">Đào tạo</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">
                    Mở cổng đăng ký học phần, hủy lớp không đủ sĩ số, duyệt đơn học phần.
                  </p>
</div>
</div>
<label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
<input aria-label="Chọn dòng" checked="" class="sr-only peer" type="checkbox"/>
<div class="w-11 h-6 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
</label>
</div>
<!-- Category Item 5 (ON) -->
<div class="py-4 flex items-center justify-between hover:bg-surface-container-low/40 px-3 rounded-lg transition-colors">
<div class="flex items-start gap-3.5 pr-4">
<div class="p-2 rounded-lg bg-surface-container-high text-on-surface mt-0.5">
<span class="material-symbols-outlined text-[20px]">announcement</span>
</div>
<div>
<div class="flex items-center gap-2">
<span class="font-label-lg text-label-lg font-bold text-on-surface">Thông báo chung từ Nhà trường</span>
<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-surface-container text-on-surface-variant">Toàn trường</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">
                    Lịch nghỉ lễ Tết, đánh giá học vụ đánh giá giảng viên, quyết định đào tạo.
                  </p>
</div>
</div>
<label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
<input aria-label="Chọn dòng" checked="" class="sr-only peer" type="checkbox"/>
<div class="w-11 h-6 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
</label>
</div>
<!-- Category Item 6 (OFF) -->
<div class="py-4 flex items-center justify-between hover:bg-surface-container-low/40 px-3 rounded-lg transition-colors opacity-90">
<div class="flex items-start gap-3.5 pr-4">
<div class="p-2 rounded-lg bg-surface-container-high text-on-surface-variant mt-0.5">
<span class="material-symbols-outlined text-[20px]">workspace_premium</span>
</div>
<div>
<div class="flex items-center gap-2">
<span class="font-label-lg text-label-lg font-bold text-on-surface">Thư khen thưởng &amp; cơ hội việc làm</span>
<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-purple-100 text-purple-800">Cơ hội</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">
                    Khen thưởng khuyến khích học tập HUIT, cơ hội thực tập doanh nghiệp.
                  </p>
</div>
</div>
<label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
<input aria-label="Chọn dòng" class="sr-only peer" type="checkbox"/>
<div class="w-11 h-6 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
</label>
</div>
<!-- Category Item 7 (OFF) -->
<div class="py-4 flex items-center justify-between hover:bg-surface-container-low/40 px-3 rounded-lg transition-colors opacity-90">
<div class="flex items-start gap-3.5 pr-4">
<div class="p-2 rounded-lg bg-surface-container-high text-on-surface-variant mt-0.5">
<span class="material-symbols-outlined text-[20px]">groups</span>
</div>
<div>
<div class="flex items-center gap-2">
<span class="font-label-lg text-label-lg font-bold text-on-surface">Cuộc họp khoa &amp; Cố vấn học tập</span>
<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-surface-container text-on-surface-variant">Khoa CNTT</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">
                    Lịch sinh hoạt lớp cố vấn học tập, gặp gỡ lãnh đạo khoa.
                  </p>
</div>
</div>
<label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
<input aria-label="Chọn dòng" class="sr-only peer" type="checkbox"/>
<div class="w-11 h-6 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
</label>
</div>
</div>
</section>
<!-- Section 3: Tần suất & Thời gian nhận thông báo (Delivery Frequency) -->
<section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm">
<div class="flex items-center justify-between pb-4 mb-5 border-b border-surface-container-high">
<div class="flex items-center gap-2.5">
<span class="material-symbols-outlined text-primary text-[22px]">schedule</span>
<h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">3. Tần suất &amp; Thời gian nhận thông báo</h2>
</div>
<span class="font-label-sm text-label-sm text-primary font-semibold">Tối ưu luồng thư điện tử</span>
</div>
<!-- Radio Options Group -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
<!-- Option 1: Real-time -->
<label class="relative flex flex-col p-4 rounded-xl border border-outline-variant bg-surface-container-low/20 cursor-pointer hover:border-primary/60 transition-all">
<div class="flex items-center justify-between mb-2">
<span class="font-label-lg text-label-lg font-bold text-on-surface">Tức thì (Real-time)</span>
<input aria-label="Chọn dòng" class="h-4 w-4 text-primary border-outline-variant focus:ring-primary" name="frequency" type="radio" value="realtime"/>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant">
                Nhận thông báo ngay khi có sự kiện phát sinh từ hệ thống.
              </p>
<div class="mt-3 flex items-center gap-1.5 text-label-sm text-on-surface-variant">
<span class="material-symbols-outlined text-[16px]">bolt</span>
                Không có độ trễ
              </div>
</label>
<!-- Option 2: Daily Digest (SELECTED) -->
<label class="relative flex flex-col p-4 rounded-xl border-2 border-primary bg-primary-fixed/20 cursor-pointer shadow-sm transition-all">
<div class="flex items-center justify-between mb-2">
<div class="flex items-center gap-1.5">
<span class="font-label-lg text-label-lg font-bold text-primary">Hàng ngày (Daily Digest)</span>
</div>
<input aria-label="Chọn dòng" checked="" class="h-4 w-4 text-primary border-primary focus:ring-primary" name="frequency" type="radio" value="daily"/>
</div>
<p class="font-body-sm text-body-sm text-on-surface">
                Tổng hợp tất cả thông báo trong ngày vào lúc <strong>19:00</strong> mỗi tối.
              </p>
<div class="mt-3 flex items-center gap-1.5 text-label-sm text-primary font-medium">
<span class="material-symbols-outlined text-[16px]">recommend</span>
                Khuyên dùng cho sinh viên
              </div>
</label>
<!-- Option 3: Weekly Digest -->
<label class="relative flex flex-col p-4 rounded-xl border border-outline-variant bg-surface-container-low/20 cursor-pointer hover:border-primary/60 transition-all">
<div class="flex items-center justify-between mb-2">
<span class="font-label-lg text-label-lg font-bold text-on-surface">Hàng tuần (Weekly Digest)</span>
<input aria-label="Chọn dòng" class="h-4 w-4 text-primary border-outline-variant focus:ring-primary" name="frequency" type="radio" value="weekly"/>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant">
                Nhận bản tin tóm tắt kế hoạch học tập vào sáng thứ Hai đầu tuần.
              </p>
<div class="mt-3 flex items-center gap-1.5 text-label-sm text-on-surface-variant">
<span class="material-symbols-outlined text-[16px]">calendar_view_week</span>
                07:30 sáng Thứ Hai
              </div>
</label>
</div>
<!-- Note Callout Box -->
<div class="p-4 rounded-xl bg-blue-50/70 border border-blue-200/80 flex items-start gap-3">
<span class="material-symbols-outlined text-primary text-[22px] flex-shrink-0 mt-0.5">info</span>
<div class="text-body-sm">
<span class="font-bold text-on-surface">Lưu ý quan trọng:</span>
<span class="text-on-surface-variant">
                Thông tin học vụ khẩn cấp (như thông báo bão lũ, thời tiết nguy hiểm, hoặc nghỉ học đột xuất từ Ban Giám hiệu) sẽ luôn được gửi ngay lập tức qua Email và SMS bất kể tùy chọn tần suất bạn đã thiết lập.
              </span>
</div>
</div>
</section>
<!-- Form Actions Bar -->
<div class="sticky bottom-6 z-20 bg-surface-container-lowest border border-outline-variant/80 rounded-xl p-4 shadow-lg flex flex-col sm:flex-row items-center justify-between gap-4">
<div class="flex items-center gap-2 text-on-surface-variant font-label-md text-label-md">
<span class="material-symbols-outlined text-[18px]">history</span>
<span>Cập nhật lần cuối: <strong class="text-on-surface font-semibold">10:30 - 15/03/2026</strong></span>
</div>
<div class="flex items-center gap-3 w-full sm:w-auto justify-end">
<button class="px-5 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-label-md text-label-md transition-colors active:scale-95" type="button">
              Khôi phục mặc định
            </button>
<button class="px-6 py-2.5 rounded-lg bg-primary hover:bg-primary-container text-white font-label-md text-label-md shadow-sm transition-all flex items-center gap-2 active:scale-95" type="button">
<span class="material-symbols-outlined text-[18px]">save</span>
<span>Lưu thay đổi</span>
</button>
</div>
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

@endsection
