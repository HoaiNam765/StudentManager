@extends('fe_preview.layouts.student')
@section('page_title', 'Phiên đăng nhập đang hoạt động - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Phiên đăng nhập đang hoạt động - HUIT Học Vụ</title>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Phiên đăng nhập đang hoạt động</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Phiên đăng nhập đang hoạt động</div>
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
<div class="max-w-[1080px] mx-auto flex flex-col gap-6">
<!-- Sub-navigation Pills -->
<div class="flex items-center gap-2 border-b border-outline-variant pb-4">
<a class="px-3.5 py-1.5 rounded-lg font-label-lg text-label-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low transition-colors" href="#">
              Cài đặt tài khoản
            </a>
<a class="px-3.5 py-1.5 rounded-lg font-label-lg text-label-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low transition-colors" href="#">
              Đổi mật khẩu
            </a>
<a class="px-3.5 py-1.5 rounded-lg font-label-lg text-label-lg bg-primary-fixed text-on-primary-fixed font-semibold border border-outline-variant shadow-xs" href="#">
              Phiên đăng nhập
            </a>
<a class="px-3.5 py-1.5 rounded-lg font-label-lg text-label-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low transition-colors" href="#">
              Tùy chọn thông báo
            </a>
</div>
<!-- Page Header & Global Action -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
<div class="flex flex-col gap-1">
<h1 class="font-headline-lg text-headline-lg font-bold text-on-surface">Phiên đăng nhập đang hoạt động</h1>
<p class="font-body-md text-body-md text-on-surface-variant">Theo dõi, kiểm soát và đăng xuất khỏi các thiết bị hoặc trình duyệt đã xác thực tài khoản học vụ của bạn.</p>
</div>
<div>
<button class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-red-300 text-error hover:bg-error-container hover:text-on-error-container font-label-lg text-label-lg transition-colors shadow-xs active:scale-95" onclick="document.getElementById('confirmation-modal').classList.remove('hidden')">
<span class="material-symbols-outlined text-lg" data-icon="logout">logout</span>
<span>Đăng xuất khỏi tất cả các thiết bị khác</span>
</button>
</div>
</div>
<!-- Summary Stats Strip -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 shadow-sm flex flex-wrap items-center justify-between gap-4">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center text-primary">
<span class="material-symbols-outlined text-xl" data-icon="devices_other">devices_other</span>
</div>
<div>
<div class="font-label-lg text-label-lg text-on-surface font-semibold">3 thiết bị đang hoạt động</div>
<div class="font-body-sm text-body-sm text-on-surface-variant">Bao gồm 1 máy tính để bàn, 1 điện thoại và 1 máy tính bảng</div>
</div>
</div>
<div class="h-8 w-px bg-outline-variant hidden sm:block"></div>
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-lg bg-secondary-container flex items-center justify-center text-on-secondary-fixed">
<span class="material-symbols-outlined text-xl" data-icon="pin_drop">pin_drop</span>
</div>
<div>
<div class="font-label-lg text-label-lg text-on-surface font-semibold">Phiên hiện tại: Chrome trên Windows 11</div>
<div class="font-body-sm text-body-sm text-on-surface-variant">IP: 115.78.23.45 • TP. Hồ Chí Minh</div>
</div>
</div>
<div class="h-8 w-px bg-outline-variant hidden md:block"></div>
<div class="flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm">
<span class="material-symbols-outlined text-lg text-primary" data-icon="auto_delete">auto_delete</span>
<span>Tự động ngắt kết nối sau 30 ngày không có hoạt động</span>
</div>
</div>
<!-- LIST OF ACTIVE SESSIONS -->
<div class="flex flex-col gap-4">
<div class="flex items-center justify-between">
<h2 class="font-headline-sm text-headline-sm text-on-surface">Danh sách phiên kết nối được ghi nhận</h2>
<span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Cập nhật theo thời gian thực</span>
</div>
<!-- Card 1: Current Session -->
<div class="bg-surface-container-lowest border-2 border-primary-container rounded-xl p-5 shadow-sm transition-all hover:shadow-md">
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-outline-variant">
<div class="flex items-center gap-3.5">
<div class="w-12 h-12 rounded-xl bg-primary-container text-on-primary-container flex items-center justify-center shadow-xs">
<span class="material-symbols-outlined text-2xl" data-icon="desktop_windows">desktop_windows</span>
</div>
<div class="flex flex-col">
<div class="flex items-center gap-2.5">
<h3 class="font-headline-sm text-headline-sm text-on-surface">Google Chrome trên Windows 11</h3>
<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-label-sm text-label-sm">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                        Thiết bị hiện tại
                      </span>
</div>
<span class="font-body-sm text-body-sm text-on-surface-variant">Máy tính văn phòng / cá nhân</span>
</div>
</div>
<div>
<span class="inline-flex items-center gap-1 text-on-surface-variant font-label-md text-label-md bg-surface-container px-3 py-1.5 rounded-lg border border-outline-variant cursor-not-allowed">
<span class="material-symbols-outlined text-sm text-primary" data-icon="lock">lock</span>
                    Đang sử dụng trên máy này
                  </span>
</div>
</div>
<!-- Metadata Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-4">
<div class="flex flex-col">
<span class="font-label-sm text-label-sm uppercase text-on-surface-variant">Vị trí</span>
<div class="flex items-center gap-1 mt-0.5 text-on-surface font-body-md text-body-md">
<span class="material-symbols-outlined text-base text-primary" data-icon="location_on">location_on</span>
<span>TP. Hồ Chí Minh, Việt Nam</span>
</div>
</div>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm uppercase text-on-surface-variant">Địa chỉ IP</span>
<div class="flex items-center gap-1 mt-0.5 text-on-surface font-body-md text-body-md">
<span class="material-symbols-outlined text-base text-secondary" data-icon="lan">lan</span>
<span>115.78.23.45 <span class="text-primary font-medium text-xs">(Mạng HUIT)</span></span>
</div>
</div>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm uppercase text-on-surface-variant">Thời gian đăng nhập</span>
<div class="flex items-center gap-1 mt-0.5 text-on-surface font-body-md text-body-md">
<span class="material-symbols-outlined text-base text-emerald-700" data-icon="schedule">schedule</span>
<span>Hôm nay lúc 08:15 <span class="text-emerald-700 font-medium text-xs">(Đang online)</span></span>
</div>
</div>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm uppercase text-on-surface-variant">Trình duyệt &amp; Nền tảng</span>
<div class="flex items-center gap-1 mt-0.5 text-on-surface font-body-md text-body-md">
<span class="material-symbols-outlined text-base text-secondary" data-icon="language">language</span>
<span>Chrome 124.0 • Windows 64-bit</span>
</div>
</div>
</div>
</div>
<!-- Card 2: Mobile iPhone -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 shadow-sm transition-all hover:border-outline hover:shadow-md">
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-outline-variant">
<div class="flex items-center gap-3.5">
<div class="w-12 h-12 rounded-xl bg-surface-container-high text-on-surface flex items-center justify-center shadow-xs">
<span class="material-symbols-outlined text-2xl" data-icon="smartphone">smartphone</span>
</div>
<div class="flex flex-col">
<div class="flex items-center gap-2.5">
<h3 class="font-headline-sm text-headline-sm text-on-surface">Safari trên iPhone 14 Pro</h3>
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-surface-container text-on-surface-variant font-label-sm text-label-sm border border-outline-variant">
                        Di động
                      </span>
</div>
<span class="font-body-sm text-body-sm text-on-surface-variant">Apple WebKit Client</span>
</div>
</div>
<div>
<button class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg border border-red-200 text-error hover:bg-error-container hover:text-on-error-container font-label-md text-label-md transition-colors active:scale-95 shadow-xs">
<span class="material-symbols-outlined text-base" data-icon="power_settings_new">power_settings_new</span>
<span>Đăng xuất</span>
</button>
</div>
</div>
<!-- Metadata Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-4">
<div class="flex flex-col">
<span class="font-label-sm text-label-sm uppercase text-on-surface-variant">Vị trí</span>
<div class="flex items-center gap-1 mt-0.5 text-on-surface font-body-md text-body-md">
<span class="material-symbols-outlined text-base text-primary" data-icon="location_on">location_on</span>
<span>TP. Hồ Chí Minh, Việt Nam</span>
</div>
</div>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm uppercase text-on-surface-variant">Địa chỉ IP</span>
<div class="flex items-center gap-1 mt-0.5 text-on-surface font-body-md text-body-md">
<span class="material-symbols-outlined text-base text-secondary" data-icon="cell_tower">cell_tower</span>
<span>14.241.12.89 <span class="text-on-surface-variant text-xs">(Viettel 4G/5G)</span></span>
</div>
</div>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm uppercase text-on-surface-variant">Hoạt động gần nhất</span>
<div class="flex items-center gap-1 mt-0.5 text-on-surface font-body-md text-body-md">
<span class="material-symbols-outlined text-base text-secondary" data-icon="history">history</span>
<span>2 giờ trước (14:30 - 14/03/2026)</span>
</div>
</div>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm uppercase text-on-surface-variant">Trình duyệt &amp; Nền tảng</span>
<div class="flex items-center gap-1 mt-0.5 text-on-surface font-body-md text-body-md">
<span class="material-symbols-outlined text-base text-secondary" data-icon="apple">file_download</span>
<span>Mobile Safari 17.4 • iOS 17.4.1</span>
</div>
</div>
</div>
</div>
<!-- Card 3: Tablet Samsung -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 shadow-sm transition-all hover:border-outline hover:shadow-md">
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-outline-variant">
<div class="flex items-center gap-3.5">
<div class="w-12 h-12 rounded-xl bg-surface-container-high text-on-surface flex items-center justify-center shadow-xs">
<span class="material-symbols-outlined text-2xl" data-icon="tablet_android">tablet_android</span>
</div>
<div class="flex flex-col">
<div class="flex items-center gap-2.5">
<h3 class="font-headline-sm text-headline-sm text-on-surface">Google Chrome trên Samsung Galaxy Tab S9</h3>
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-surface-container text-on-surface-variant font-label-sm text-label-sm border border-outline-variant">
                        Máy tính bảng
                      </span>
</div>
<span class="font-body-sm text-body-sm text-on-surface-variant">Android Tablet Edition</span>
</div>
</div>
<div>
<button class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg border border-red-200 text-error hover:bg-error-container hover:text-on-error-container font-label-md text-label-md transition-colors active:scale-95 shadow-xs">
<span class="material-symbols-outlined text-base" data-icon="power_settings_new">power_settings_new</span>
<span>Đăng xuất</span>
</button>
</div>
</div>
<!-- Metadata Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-4">
<div class="flex flex-col">
<span class="font-label-sm text-label-sm uppercase text-on-surface-variant">Vị trí</span>
<div class="flex items-center gap-1 mt-0.5 text-on-surface font-body-md text-body-md">
<span class="material-symbols-outlined text-base text-primary" data-icon="location_on">location_on</span>
<span>Hà Nội, Việt Nam</span>
</div>
</div>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm uppercase text-on-surface-variant">Địa chỉ IP</span>
<div class="flex items-center gap-1 mt-0.5 text-on-surface font-body-md text-body-md">
<span class="material-symbols-outlined text-base text-secondary" data-icon="wifi">wifi</span>
<span>118.69.182.202 <span class="text-on-surface-variant text-xs">(FPT Telecom)</span></span>
</div>
</div>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm uppercase text-on-surface-variant">Hoạt động gần nhất</span>
<div class="flex items-center gap-1 mt-0.5 text-on-surface font-body-md text-body-md">
<span class="material-symbols-outlined text-base text-secondary" data-icon="history">history</span>
<span>3 ngày trước (11/03/2026)</span>
</div>
</div>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm uppercase text-on-surface-variant">Trình duyệt &amp; Nền tảng</span>
<div class="flex items-center gap-1 mt-0.5 text-on-surface font-body-md text-body-md">
<span class="material-symbols-outlined text-base text-secondary" data-icon="android">android</span>
<span>Chrome Mobile 123.0 • Android 14</span>
</div>
</div>
</div>
</div>
</div>
<!-- SECURITY WARNING BOX -->
<div class="bg-amber-50 border border-amber-200 rounded-xl p-5 shadow-xs flex items-start gap-4">
<div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center text-amber-700 shrink-0 mt-0.5">
<span class="material-symbols-outlined text-2xl" data-icon="shield">shield</span>
</div>
<div class="flex-1 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
<p class="font-body-md text-body-md text-amber-900 leading-relaxed">
                Nếu bạn phát hiện thiết bị hoặc vị trí đăng nhập lạ mà bạn không nhận ra, hãy lập tức nhấn <strong class="font-semibold text-amber-950">Đăng xuất tất cả thiết bị khác</strong> và <strong class="font-semibold text-amber-950">Đổi mật khẩu ngay</strong> để bảo vệ dữ liệu học vụ, điểm số và học vụ cá nhân.
              </p>
<a class="inline-flex items-center gap-1 text-primary hover:text-on-primary-fixed-variant font-label-lg text-label-lg whitespace-nowrap font-semibold" href="#">
<span>Đi tới đổi mật khẩu</span>
<span class="material-symbols-outlined text-base" data-icon="arrow_forward">arrow_forward</span>
</a>
</div>
</div>
<!-- DEMO / PREVIEW CONFIRMATION MODAL -->
<div class="relative bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-md overflow-hidden" id="confirmation-modal">
<div class="flex items-start gap-4">
<div class="w-12 h-12 rounded-full bg-error-container text-on-error-container flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-2xl" data-icon="warning">warning</span>
</div>
<div class="flex-1">
<div class="flex items-center justify-between">
<h3 class="font-headline-sm text-headline-sm text-on-surface">Xác nhận đăng xuất thiết bị?</h3>
<span class="font-label-sm text-label-sm px-2 py-0.5 rounded bg-surface-container text-on-surface-variant font-medium">Bản xem trước hộp thoại xác nhận</span>
</div>
<p class="mt-2 font-body-md text-body-md text-on-surface-variant leading-relaxed">
                  Bạn sẽ được đăng xuất khỏi tất cả <span class="font-semibold text-on-surface">2 thiết bị khác</span>. Phiên làm việc trên thiết bị hiện tại này sẽ được giữ lại. Các phiên bị ngắt sẽ cần nhập lại mật khẩu và mã OTP (nếu có) khi truy cập lại cổng đào tạo HUIT.
                </p>
<div class="mt-5 flex items-center justify-end gap-3">
<button class="px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface hover:bg-surface-container-low font-label-lg text-label-lg transition-colors" type="button">
                    Hủy bỏ
                  </button>
<button class="px-4 py-2 rounded-lg bg-error text-on-error hover:opacity-90 font-label-lg text-label-lg transition-opacity flex items-center gap-2 shadow-xs" type="button">
<span class="material-symbols-outlined text-base" data-icon="logout">logout</span>
                    Đăng xuất 2 thiết bị
                  </button>
</div>
</div>
</div>
</div>
</div>
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

@endsection
