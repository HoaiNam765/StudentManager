@extends('fe_preview.layouts.student')
@section('page_title', 'Đổi mật khẩu - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Đổi mật khẩu - HUIT Học Vụ</title>
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
<div class="font-semibold text-slate-800 text-[11.5px]">Hỗ trợ kỹ thuật: 028 3816 1673</div>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Đổi mật khẩu</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Đổi mật khẩu</div>
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
<div class="text-[11px] text-slate-500">2001210123 • 12DHTH01</div>
</div>
</div>
</div>
</header>
<!-- MAIN BODY CONTENT -->
<main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
<!-- Centered Container Max-Width 1440 -->
<div class="max-w-[1440px] w-full mx-auto flex flex-col items-center">
<!-- Page Header & Explanatory Subtitle -->
<div class="w-full max-w-[540px] text-center mb-6">
<div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-surface-container text-primary mb-3 shadow-sm border border-outline-variant">
<span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">security</span>
</div>
<h1 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">Đổi mật khẩu</h1>
<p class="font-body-md text-body-md text-on-surface-variant mt-1.5">
            Cập nhật mật khẩu định kỳ để bảo vệ tài khoản và dữ liệu học vụ cá nhân.
          </p>
</div>
<!-- Main Form Card (Level 1 Elevation) -->
<div class="w-full max-w-[540px] bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-8">
<!-- Card Sub-header -->
<div class="flex items-center gap-3 pb-5 mb-6 border-b border-surface-container">
<div class="w-9 h-9 rounded-lg bg-surface-container-low flex items-center justify-center text-primary border border-outline-variant">
<span class="material-symbols-outlined text-[20px]">lock_clock</span>
</div>
<div>
<h2 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Thiết lập mật khẩu mới</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Vui lòng điền đầy đủ các thông tin xác thực bảo mật bên dưới</p>
</div>
</div>
<form action="#" class="space-y-5" method="POST" onsubmit="event.preventDefault();">
@csrf

<!-- Field 1: Mật khẩu hiện tại -->
<div>
<div class="flex items-center justify-between mb-1.5">
<label class="font-label-md text-label-md text-on-surface font-semibold" for="current-password">
                  Mật khẩu hiện tại <span class="text-error">*</span>
</label>
<a class="font-label-sm text-label-sm text-primary hover:underline font-medium" href="#">Quên mật khẩu?</a>
</div>
<div class="relative">
<input class="w-full h-10 px-3 pr-10 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" id="current-password" name="current_password" placeholder="Nhập mật khẩu đang sử dụng" required="" type="password"/>
<button class="absolute right-3 top-2.5 text-on-surface-variant hover:text-on-surface transition-colors" onclick="togglePasswordVisibility('current-password', this)" type="button">
<span class="material-symbols-outlined text-[20px]">visibility</span>
</button>
</div>
</div>
<!-- Field 2: Mật khẩu mới -->
<div>
<label class="block font-label-md text-label-md text-on-surface font-semibold mb-1.5" for="new-password">
                Mật khẩu mới <span class="text-error">*</span>
</label>
<div class="relative">
<input class="w-full h-10 px-3 pr-10 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" id="new-password" name="new_password" placeholder="Nhập mật khẩu mới" required="" type="password" value="HuitSecure@2026"/>
<button class="absolute right-3 top-2.5 text-on-surface-variant hover:text-on-surface transition-colors" onclick="togglePasswordVisibility('new-password', this)" type="button">
<span class="material-symbols-outlined text-[20px]">visibility</span>
</button>
</div>
<!-- Password Strength Meter -->
<div class="mt-2.5 bg-surface-container-low p-2.5 rounded-lg border border-outline-variant/60">
<div class="flex items-center justify-between mb-1.5">
<span class="font-label-sm text-label-sm text-on-surface-variant">Thước đo độ mạnh mật khẩu:</span>
<span class="font-label-sm text-label-sm font-semibold text-primary flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]">verified_user</span>
                    Độ mạnh: Rất mạnh (85%)
                  </span>
</div>
<!-- Segmented Progress Bar -->
<div class="grid grid-cols-4 gap-1.5 h-1.5 w-full">
<div class="bg-primary rounded-full h-full"></div>
<div class="bg-primary rounded-full h-full"></div>
<div class="bg-primary rounded-full h-full"></div>
<div class="bg-outline-variant/50 rounded-full h-full"></div>
</div>
</div>
</div>
<!-- Field 3: Xác nhận mật khẩu mới -->
<div>
<label class="block font-label-md text-label-md text-on-surface font-semibold mb-1.5" for="confirm-password">
                Xác nhận mật khẩu mới <span class="text-error">*</span>
</label>
<div class="relative">
<input class="w-full h-10 px-3 pr-10 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" id="confirm-password" name="confirm_password" placeholder="Nhập lại chính xác mật khẩu mới" required="" type="password" value="HuitSecure@2026"/>
<button class="absolute right-3 top-2.5 text-on-surface-variant hover:text-on-surface transition-colors" onclick="togglePasswordVisibility('confirm-password', this)" type="button">
<span class="material-symbols-outlined text-[20px]">visibility</span>
</button>
</div>
</div>
<!-- Password Requirements Checklist (Tiêu chuẩn mật khẩu HUIT) -->
<div class="pt-2">
<div class="flex items-center gap-1.5 mb-2.5">
<span class="material-symbols-outlined text-primary text-[18px]">verified</span>
<span class="font-label-md text-label-md text-on-surface font-semibold">Tiêu chuẩn mật khẩu HUIT:</span>
</div>
<ul class="space-y-2 bg-surface-container-low/70 p-3.5 rounded-lg border border-outline-variant/60 font-body-sm text-body-sm text-on-surface">
<li class="flex items-center gap-2 text-on-surface">
<span class="material-symbols-outlined text-primary text-[16px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
<span>Tối thiểu 8 ký tự (tối đa 32 ký tự)</span>
</li>
<li class="flex items-center gap-2 text-on-surface">
<span class="material-symbols-outlined text-primary text-[16px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
<span>Ít nhất 1 chữ hoa (A-Z) và 1 chữ thường (a-z)</span>
</li>
<li class="flex items-center gap-2 text-on-surface">
<span class="material-symbols-outlined text-primary text-[16px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
<span>Ít nhất 1 chữ số (0-9)</span>
</li>
<li class="flex items-center gap-2 text-on-surface">
<span class="material-symbols-outlined text-primary text-[16px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
<span>Ít nhất 1 ký tự đặc biệt (!@#$%^&amp;*...)</span>
</li>
<li class="flex items-center gap-2 text-on-surface">
<span class="material-symbols-outlined text-primary text-[16px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
<span>Không trùng khớp với mật khẩu gần nhất</span>
</li>
</ul>
</div>
<!-- Submit CTA & Cancel Actions -->
<div class="pt-3 flex flex-col gap-3">
<button class="w-full h-11 bg-primary hover:bg-primary-container text-white font-label-lg text-label-lg rounded-lg shadow-sm flex items-center justify-center gap-2 transition-all active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2" type="submit">
<span class="material-symbols-outlined text-[20px]">save</span>
<span>Cập nhật mật khẩu ngay</span>
</button>
<a class="w-full text-center py-1.5 font-label-md text-label-md text-on-surface-variant hover:text-on-surface transition-colors" href="#">
                Hủy bỏ và quay lại
              </a>
</div>
</form>
</div>
<!-- Security Info Alert Box -->
<div class="w-full max-w-[540px] mt-4 bg-surface-container-low border border-outline-variant rounded-xl p-4 flex gap-3 items-start shadow-xs">
<span class="material-symbols-outlined text-primary text-[22px] flex-shrink-0 mt-0.5" style="font-variation-settings: 'FILL' 1;">info</span>
<div class="font-body-sm text-body-sm text-on-surface leading-relaxed">
<span class="font-semibold text-primary">Lưu ý bảo mật:</span> Sau khi đổi mật khẩu thành công, tất cả các phiên đăng nhập khác trên thiết bị di động hoặc máy tính công cộng sẽ tự động đăng xuất để đảm bảo an toàn tài khoản.
          </div>
</div>
</div>
<!-- Footer Section -->
<footer class="w-full max-w-[1440px] mx-auto mt-12 pt-6 border-t border-outline-variant text-center font-body-sm text-body-sm text-on-surface-variant">
<p>© 2026 Trường Đại học Công Thương TP. Hồ Chí Minh (HUIT) - Cổng Quản lý Học vụ &amp; Khảo thí | Hỗ trợ kỹ thuật: <a class="text-primary hover:underline" href="mailto:nguoidung025@example.test">nguoidung025@example.test</a></p>
</footer>
</main>
<!-- FOOTER -->
<footer class="py-4 px-6 bg-white border-t border-slate-200/80 text-center text-xs text-slate-500 no-print flex flex-col sm:flex-row items-center justify-between gap-2">
<div>© 2026 <strong>Trường Đại học Công Thương TP.HCM (HUIT)</strong> — Hệ thống Quản lý Học vụ.</div>
<div class="flex items-center gap-3 text-slate-500 text-[11px]">
<span>Hỗ trợ: 028 3816 1673</span>
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
