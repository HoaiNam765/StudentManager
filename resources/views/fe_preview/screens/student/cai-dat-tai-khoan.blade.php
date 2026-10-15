@extends('fe_preview.layouts.student')
@section('page_title', 'Cài đặt tài khoản & Bảo mật - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Cài đặt tài khoản &amp; Bảo mật - HUIT Học Vụ</title>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/cai-dat-tai-khoan']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">settings</span>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Cài đặt tài khoản &amp; Bảo mật</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Cài đặt tài khoản &amp; Bảo mật</div>
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
<div class="mx-auto space-y-6 max-w-[1440px] w-full">
<!-- PAGE HEADER & NAVIGATION TABS -->
<div class="space-y-4">
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
<div>
<h1 class="font-headline-lg text-headline-lg font-bold text-on-surface tracking-tight">Cài đặt tài khoản</h1>
<p class="font-body-md text-body-md text-on-surface-variant mt-0.5">
                  Quản lý thông tin định danh, tùy chọn bảo mật hai lớp, kiểm soát phiên đăng nhập và luồng thông báo đào tạo.
                </p>
</div>
<div class="flex items-center gap-2 self-start sm:self-auto">
<span class="bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm px-2.5 py-1 rounded-full border border-outline-variant/60 flex items-center gap-1.5">
<span class="w-2 h-2 rounded-full bg-[#15803D]"></span>
                  Khoa CNTT - Sinh viên chính quy
                </span>
</div>
</div>
<!-- Horizontal Tab Navigation Bar -->
<div class="border-b border-outline-variant flex items-center gap-1 overflow-x-auto pt-2">
<!-- Active Tab -->
<button class="flex items-center gap-2 px-4 py-3 font-label-lg text-label-lg text-primary border-b-2 border-primary font-semibold transition-all">
<span class="material-symbols-outlined text-[18px]">person</span>
<span class="">Thông tin cá nhân</span>
</button>
<button class="flex items-center gap-2 px-4 py-3 font-label-lg text-label-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low/50 rounded-t-lg transition-all font-medium">
<span class="material-symbols-outlined text-[18px]">lock_reset</span>
<span class="">Đổi mật khẩu</span>
</button>
<button class="flex items-center gap-2 px-4 py-3 font-label-lg text-label-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low/50 rounded-t-lg transition-all font-medium">
<span class="material-symbols-outlined text-[18px]">devices</span>
<span class="">Phiên đăng nhập</span>
<span class="bg-surface-container-high text-on-surface-variant text-[11px] px-1.5 py-0.2 rounded-full font-bold">3</span>
</button>
<button class="flex items-center gap-2 px-4 py-3 font-label-lg text-label-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low/50 rounded-t-lg transition-all font-medium">
<span class="material-symbols-outlined text-[18px]">notifications_active</span>
<span class="">Tùy chọn thông báo</span>
</button>
</div>
</div>
<!-- BENTO / SECTION 1: THÔNG TIN CƠ BẢN -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant card-academic overflow-hidden">
<!-- Card Header -->
<div class="px-6 py-4 border-b border-surface-container-high flex items-center justify-between bg-surface-container-low/30">
<div class="flex items-center gap-2.5">
<span class="material-symbols-outlined text-primary text-[22px]">badge</span>
<div>
<h2 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Thông tin cơ bản</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Dữ liệu học viên đồng bộ trực tiếp từ Cổng Quản lý Đào tạo HUIT</p>
</div>
</div>
<span class="text-label-sm font-label-sm text-outline flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]">history</span>
                Cập nhật lần cuối: 10:15 - 14/03/2026
              </span>
</div>
<!-- Card Body Form -->
<div class="p-6 space-y-6">
<!-- Avatar Upload Area & Quick Bio -->
<div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 pb-6 border-b border-outline-variant/60">
<div class="relative group">
<div class="w-24 h-24 rounded-full ring-4 ring-primary/10 overflow-hidden bg-surface-container flex items-center justify-center shadow-inner">
<img alt="" class="w-full h-full object-cover" data-alt="A clean modern professional passport portrait of a Vietnamese university male student wearing a crisp white collared shirt with blue university tie, soft studio lighting, pale university campus background, high sharpness, color palette containing soft navy and institutional blue." src="https://lh3.googleusercontent.com/aida-public/AB6AXuBCBA9m6EqjUkGittgJ-xubQNh71fhlojLss3gmme55zC78McfiEcEYP8X_pLD0L62vOR5VbFVUsz0rAI6y5UhZJ_C2czTp-CJ7c6IX92tzvH2hhLV75S4uS8FJpY10XWFJT-kO1Swlk0-UgQ8Mt8aJ67DItOoOFORoEpmdPVPl_R8Ighd5HkGu6yC9uDLaI51WiABYDoPK4jpczDpXaHqFipRvMSdRErAtjuZZRvE"/>
</div>
<button class="absolute bottom-0 right-0 w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center hover:bg-primary-container shadow-md transition-transform active:scale-95 border-2 border-surface-container-lowest" title="Tải ảnh mới">
<span class="material-symbols-outlined text-[16px]">photo_camera</span>
</button>
</div>
<div class="flex-1 text-center sm:text-left space-y-2">
<div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
<h3 class="font-headline-md text-headline-md font-bold text-on-surface">Nguyễn Văn An</h3>
<span class="bg-[#DCFCE7] text-[#15803D] font-label-sm text-label-sm px-2.5 py-0.5 rounded-full font-semibold border border-[#15803D]/20">
                      Sinh viên đang học
                    </span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant max-w-xl">
                    Cho phép tệp JPG, PNG dung lượng không vượt quá 2MB. Ảnh chân dung chuẩn thẻ sinh viên hỗ trợ nhận diện phòng thi trực tuyến và tra cứu hồ sơ cử nhân.
                  </p>
<div class="flex items-center justify-center sm:justify-start gap-3 pt-1">
<button class="px-3 py-1.5 rounded-lg border border-outline-variant font-label-md text-label-md hover:bg-surface-container-low transition-colors text-on-surface">
                      Tải ảnh mới
                    </button>
<button class="px-3 py-1.5 font-label-md text-label-md text-error hover:bg-error-container/30 rounded-lg transition-colors">
                      Xóa ảnh
                    </button>
</div>
</div>
</div>
<!-- Form Input Fields Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
<!-- Họ và tên -->
<div class="space-y-1.5">
<label class="block font-label-md text-label-md text-on-surface font-semibold">Họ và tên <span class="text-error">*</span></label>
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none" type="text" value="Nguyễn Văn An"/>
</div>
<!-- Mã số SV/Cán bộ -->
<div class="space-y-1.5">
<label class="block font-label-md text-label-md text-on-surface font-semibold">Mã số sinh viên (MSSV)</label>
<div class="relative">
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 pl-9 rounded-lg border border-outline-variant bg-surface-container-low text-on-surface-variant font-body-md text-body-md cursor-not-allowed" readonly="" type="text" value="2001210123"/>
<span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-[18px] text-outline">lock</span>
</div>
<p class="text-[11px] font-body-sm text-outline">Mã định danh duy nhất do Phòng Đào tạo cấp, không thể sửa đổi.</p>
</div>
<!-- Email HUIT (Readonly with badge) -->
<div class="space-y-1.5">
<div class="flex items-center justify-between">
<label class="block font-label-md text-label-md text-on-surface font-semibold">Email trường cấp (@huit.edu.vn)</label>
<span class="inline-flex items-center gap-1 text-[11px] font-label-sm font-semibold text-[#15803D] bg-[#DCFCE7] px-2 py-0.2 rounded-full border border-[#15803D]/20">
<span class="material-symbols-outlined text-[13px] fill-icon">verified</span>
                      Đã xác thực
                    </span>
</div>
<div class="relative">
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 pl-9 rounded-lg border border-outline-variant bg-surface-container-low text-on-surface font-body-md text-body-md cursor-not-allowed" readonly="" type="email" value="an.nv@huit.edu.vn"/>
<span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-[18px] text-outline">alternate_email</span>
</div>
</div>
<!-- Số điện thoại -->
<div class="space-y-1.5">
<label class="block font-label-md text-label-md text-on-surface font-semibold">Số điện thoại liên lạc</label>
<div class="relative">
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 pl-9 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none" type="tel" value="0987 654 321"/>
<span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-[18px] text-outline">phone</span>
</div>
</div>
<!-- Đơn vị công tác / Khoa CNTT -->
<div class="space-y-1.5">
<label class="block font-label-md text-label-md text-on-surface font-semibold">Khoa / Đơn vị quản lý</label>
<div class="relative">
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 pl-9 rounded-lg border border-outline-variant bg-surface-container-low text-on-surface-variant font-body-md text-body-md cursor-not-allowed" readonly="" type="text" value="Khoa Công nghệ Thông tin"/>
<span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-[18px] text-outline">apartment</span>
</div>
</div>
<!-- Chuyên ngành đào tạo -->
<div class="space-y-1.5">
<label class="block font-label-md text-label-md text-on-surface font-semibold">Chuyên ngành đào tạo</label>
<div class="relative">
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 pl-9 rounded-lg border border-outline-variant bg-surface-container-low text-on-surface-variant font-body-md text-body-md cursor-not-allowed" readonly="" type="text" value="Kỹ thuật Phần mềm (Khóa 12)"/>
<span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-[18px] text-outline">terminal</span>
</div>
</div>
</div>
<!-- Action Bar -->
<div class="pt-4 border-t border-outline-variant/60 flex items-center justify-end gap-3">
<button class="h-10 px-4 rounded-lg border border-outline-variant bg-surface-container-lowest font-label-lg text-label-lg text-on-surface hover:bg-surface-container-low transition-colors" type="button">
                  Hủy thao tác
                </button>
<button class="h-10 px-5 rounded-lg bg-primary text-white font-label-lg text-label-lg hover:bg-primary-container shadow-sm transition-all flex items-center gap-2 active:scale-95" type="button">
<span class="material-symbols-outlined text-[18px]">save</span>
<span class="">Lưu thay đổi</span>
</button>
</div>
</div>
</div>
<!-- SECTION 2 & 3: GRID 2 CỘT CHO BẢO MẬT & THIẾT BỊ -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
<!-- CỘT TRÁI (7 COLS): ĐỔI MẬT KHẨU & BẢO MẬT -->
<div class="lg:col-span-7 bg-surface-container-lowest rounded-xl border border-outline-variant card-academic flex flex-col justify-between">
<div>
<div class="px-6 py-4 border-b border-surface-container-high flex items-center gap-2.5 bg-surface-container-low/30">
<span class="material-symbols-outlined text-primary text-[22px]">shield</span>
<div>
<h2 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Đổi mật khẩu &amp; Bảo mật</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Khuyến nghị thay đổi mật khẩu định kỳ mỗi 90 ngày để an toàn</p>
</div>
</div>
<div class="p-6 space-y-4">
<!-- Mật khẩu hiện tại -->
<div class="space-y-1.5">
<label class="block font-label-md text-label-md text-on-surface font-semibold">Mật khẩu hiện tại</label>
<div class="relative">
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 pr-10 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none" type="password" value="currentPassword123"/>
<button class="absolute right-3 top-1/2 -translate-y-1/2 text-outline hover:text-on-surface" type="button">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
</div>
</div>
<!-- Mật khẩu mới -->
<div class="space-y-1.5">
<label class="block font-label-md text-label-md text-on-surface font-semibold">Mật khẩu mới</label>
<div class="relative">
<input aria-label="Nhập mật khẩu mới an toàn" class="w-full h-10 px-3 pr-10 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none" placeholder="Nhập mật khẩu mới an toàn" type="password"/>
<button class="absolute right-3 top-1/2 -translate-y-1/2 text-outline hover:text-on-surface" type="button">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
</div>
</div>
<!-- Xác nhận mật khẩu mới -->
<div class="space-y-1.5">
<label class="block font-label-md text-label-md text-on-surface font-semibold">Xác nhận mật khẩu mới</label>
<div class="relative">
<input aria-label="Nhập lại chính xác mật khẩu mới" class="w-full h-10 px-3 pr-10 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none" placeholder="Nhập lại chính xác mật khẩu mới" type="password"/>
<button class="absolute right-3 top-1/2 -translate-y-1/2 text-outline hover:text-on-surface" type="button">
<span class="material-symbols-outlined text-[18px]">visibility_off</span>
</button>
</div>
</div>
<!-- Yêu cầu mật khẩu chuẩn chính quy -->
<div class="p-3.5 rounded-lg bg-surface-container-low border border-outline-variant/60 space-y-2">
<span class="font-label-sm text-label-sm font-semibold text-on-surface block">Tiêu chuẩn mật khẩu HUIT:</span>
<ul class="text-body-sm font-body-sm space-y-1.5 text-on-surface-variant">
<li class="flex items-center gap-2">
<span class="material-symbols-outlined text-[16px] text-[#15803D]">check_circle</span>
<span class="">Độ dài tối thiểu từ 8 ký tự trở lên</span>
</li>
<li class="flex items-center gap-2">
<span class="material-symbols-outlined text-[16px] text-[#15803D]">check_circle</span>
<span class="">Bao gồm ít nhất một chữ hoa (A-Z) và một chữ thường (a-z)</span>
</li>
<li class="flex items-center gap-2">
<span class="material-symbols-outlined text-[16px] text-outline">radio_button_unchecked</span>
<span class="">Có ít nhất một số (0-9) hoặc ký tự đặc biệt (!@#$%^&amp;*)</span>
</li>
</ul>
</div>
</div>
</div>
<!-- Button Cập nhật mật khẩu -->
<div class="px-6 py-4 border-t border-outline-variant/60 flex items-center justify-end bg-surface-container-lowest">
<button class="h-10 px-5 rounded-lg bg-primary text-white font-label-lg text-label-lg hover:bg-primary-container shadow-sm transition-all flex items-center gap-2 active:scale-95" type="button">
<span class="material-symbols-outlined text-[18px]">key</span>
<span class="">Cập nhật mật khẩu</span>
</button>
</div>
</div>
<!-- CỘT PHẢI (5 COLS): TÙY CHỌN THÔNG BÁO HỌC VỤ -->
<div class="lg:col-span-5 bg-surface-container-lowest rounded-xl border border-outline-variant card-academic flex flex-col justify-between">
<div>
<div class="px-6 py-4 border-b border-surface-container-high flex items-center gap-2.5 bg-surface-container-low/30">
<span class="material-symbols-outlined text-primary text-[22px]">notifications</span>
<div>
<h2 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Tùy chọn thông báo</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Kênh nhận thông tin khẩn cấp từ Nhà trường</p>
</div>
</div>
<div class="p-6 divide-y divide-outline-variant/50">
<!-- Option 1: Lịch học / Lịch học -->
<div class="py-3.5 first:pt-0 flex items-start justify-between gap-3">
<div>
<p class="font-label-md text-label-md font-semibold text-on-surface">Lịch học &amp; Lịch học</p>
<p class="font-body-sm text-body-sm text-on-surface-variant">Nhắc lịch học học kỳ, thông báo đổi phòng học đột xuất.</p>
</div>
<!-- Switch Toggle -->
<label class="relative inline-flex items-center cursor-pointer shrink-0 mt-0.5">
<input aria-label="Chọn dòng" checked="" class="sr-only peer" type="checkbox"/>
<div class="w-10 h-5 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
</label>
</div>
<!-- Option 2: Điểm số & Đánh giá -->
<div class="py-3.5 flex items-start justify-between gap-3">
<div>
<p class="font-label-md text-label-md font-semibold text-on-surface">Công bố điểm thi &amp; Điểm rèn luyện</p>
<p class="font-body-sm text-body-sm text-on-surface-variant">Email khi giảng viên nhập điểm quá trình hoặc điểm kết thúc học phần.</p>
</div>
<label class="relative inline-flex items-center cursor-pointer shrink-0 mt-0.5">
<input aria-label="Chọn dòng" checked="" class="sr-only peer" type="checkbox"/>
<div class="w-10 h-5 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
</label>
</div>
<!-- Option 3: Thông báo khẩn & Học vụ -->
<div class="py-3.5 flex items-start justify-between gap-3">
<div>
<div class="flex items-center gap-1.5">
<p class="font-label-md text-label-md font-semibold text-on-surface">Thông báo học vụ khẩn</p>
<span class="text-[10px] font-label-sm bg-error-container text-on-error-container px-1.5 py-0.2 rounded font-bold">Bắt buộc</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant">Cảnh báo đóng học vụ, hoãn thi, lịch nghỉ lễ toàn trường.</p>
</div>
<label class="relative inline-flex items-center cursor-not-allowed shrink-0 mt-0.5 opacity-80">
<input aria-label="Chọn dòng" checked="" class="sr-only peer" disabled="" type="checkbox"/>
<div class="w-10 h-5 bg-primary rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4"></div>
</label>
</div>
<!-- Option 4: Bản tin sinh viên & Khen thưởng -->
<div class="py-3.5 last:pb-0 flex items-start justify-between gap-3">
<div>
<p class="font-label-md text-label-md font-semibold text-on-surface">Bản tin khen thưởng &amp; Việc làm</p>
<p class="font-body-sm text-body-sm text-on-surface-variant">Cơ hội thực tập doanh nghiệp IT, khen thưởng khuyến khích học tập.</p>
</div>
<label class="relative inline-flex items-center cursor-pointer shrink-0 mt-0.5">
<input aria-label="Chọn dòng" class="sr-only peer" type="checkbox"/>
<div class="w-10 h-5 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
</label>
</div>
</div>
</div>
<!-- Footer notification info -->
<div class="px-6 py-4 border-t border-outline-variant/60 bg-surface-container-low/30">
<span class="text-body-sm font-body-sm text-on-surface-variant flex items-center gap-1.5">
<span class="material-symbols-outlined text-[16px] text-tertiary">info</span>
                  Thông báo hệ thống sẽ được gửi song song về email <strong>an.nv@huit.edu.vn</strong>
</span>
</div>
</div>
</div>
<!-- SECTION 4: PHIÊN ĐĂNG NHẬP ĐANG HOẠT ĐỘNG (SESSION LIST) -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant card-academic overflow-hidden">
<!-- Table Header Bar -->
<div class="px-6 py-4 border-b border-surface-container-high flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-surface-container-low/30">
<div class="flex items-center gap-2.5">
<span class="material-symbols-outlined text-primary text-[22px]">devices</span>
<div>
<h2 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Phiên đăng nhập đang hoạt động</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Theo dõi và kiểm soát các thiết bị đã xác thực truy cập tài khoản học vụ</p>
</div>
</div>
<!-- Nút hành động nổi bật cảnh báo -->
<button class="h-9 px-3.5 rounded-lg bg-error hover:bg-error/90 text-on-error font-label-md text-label-md shadow-xs transition-all flex items-center gap-1.5 self-start sm:self-auto active:scale-95">
<span class="material-symbols-outlined text-[16px]">logout</span>
<span class="">Đăng xuất khỏi tất cả các thiết bị khác</span>
</button>
</div>
<!-- Session Items List -->
<div class="divide-y divide-outline-variant/60">
<!-- Item 1: Current Session -->
<div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-surface-container-low/20">
<div class="flex items-start gap-4">
<div class="w-10 h-10 rounded-lg bg-primary-fixed text-primary flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[24px]">desktop_windows</span>
</div>
<div class="space-y-1">
<div class="flex items-center gap-2">
<span class="font-headline-sm text-headline-sm font-semibold text-on-surface">Chrome trên Windows 11</span>
<span class="bg-[#DCFCE7] text-[#15803D] font-label-sm text-label-sm px-2 py-0.2 rounded-full font-bold border border-[#15803D]/20">
                        Phiên hiện tại
                      </span>
</div>
<div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-body-sm font-body-sm text-on-surface-variant">
<span class="flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]">location_on</span>
                        TP. Hồ Chí Minh, Việt Nam (115.78.23.41)
                      </span>
<span class="">•</span>
<span class="text-[#15803D] font-medium">Đang hoạt động ngay bây giờ</span>
</div>
</div>
</div>
<div class="flex items-center gap-2 md:self-center pl-14 md:pl-0">
<span class="text-label-sm font-label-sm text-outline italic">Thiết bị đang sử dụng</span>
</div>
</div>
<!-- Item 2: Safari on iPhone -->
<div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-surface-container-low/30 transition-colors">
<div class="flex items-start gap-4">
<div class="w-10 h-10 rounded-lg bg-surface-container text-secondary flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[24px]">smartphone</span>
</div>
<div class="space-y-1">
<div class="flex items-center gap-2">
<span class="font-headline-sm text-headline-sm font-semibold text-on-surface">Safari trên iPhone 14 Pro</span>
<span class="bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm px-2 py-0.2 rounded-full">
                        Di động
                      </span>
</div>
<div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-body-sm font-body-sm text-on-surface-variant">
<span class="flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]">location_on</span>
                        TP. Hồ Chí Minh, Việt Nam (14.241.12.89)
                      </span>
<span class="">•</span>
<span class="">Hoạt động 2 giờ trước</span>
</div>
</div>
</div>
<div class="flex items-center gap-2 md:self-center pl-14 md:pl-0">
<button class="px-3 py-1.5 text-label-md font-label-md text-error hover:bg-error-container/30 rounded-lg transition-colors border border-transparent hover:border-error/20 flex items-center gap-1">
<span class="material-symbols-outlined text-[16px]">no_accounts</span>
<span class="">Đăng xuất</span>
</button>
</div>
</div>
<!-- Item 3: Chrome on MacOS -->
<div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-surface-container-low/30 transition-colors">
<div class="flex items-start gap-4">
<div class="w-10 h-10 rounded-lg bg-surface-container text-secondary flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[24px]">laptop_mac</span>
</div>
<div class="space-y-1">
<div class="flex items-center gap-2">
<span class="font-headline-sm text-headline-sm font-semibold text-on-surface">Chrome trên MacOS Sonoma</span>
<span class="bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm px-2 py-0.2 rounded-full">
                        Phòng Lab CNTT
                      </span>
</div>
<div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-body-sm font-body-sm text-on-surface-variant">
<span class="flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]">location_on</span>
                        Tân Phú, TP. Hồ Chí Minh (118.69.182.202)
                      </span>
<span class="">•</span>
<span class="">3 ngày trước (11/03/2026)</span>
</div>
</div>
</div>
<div class="flex items-center gap-2 md:self-center pl-14 md:pl-0">
<button class="px-3 py-1.5 text-label-md font-label-md text-error hover:bg-error-container/30 rounded-lg transition-colors border border-transparent hover:border-error/20 flex items-center gap-1">
<span class="material-symbols-outlined text-[16px]">no_accounts</span>
<span class="">Đăng xuất</span>
</button>
</div>
</div>
</div>
<!-- Footer Help Note -->
<div class="px-6 py-3 bg-surface-container-low/40 border-t border-outline-variant/60 flex items-center justify-between text-body-sm font-body-sm text-on-surface-variant">
<span class="flex items-center gap-1">
<span class="material-symbols-outlined text-[16px] text-tertiary">lock_clock</span>
                Hệ thống tự động chấm dứt phiên sau 30 ngày không có hoạt động.
              </span>
<a class="text-primary hover:underline font-label-md text-label-md font-semibold" href="#">Nhật ký bảo mật chi tiết</a>
</div>
</div>
<!-- PAGE FOOTER (Institutional Copyright) -->
<footer class="pt-6 pb-8 border-t border-outline-variant/60 text-center space-y-1">
<p class="font-body-sm text-body-sm text-on-surface-variant">
              © 2026 Trường Đại học Công Thương TP. Hồ Chí Minh (HUIT) - Cổng Quản lý Học vụ &amp; Khảo thí
            </p>
<p class="font-label-sm text-label-sm text-outline">
              140 Lê Trọng Tấn, P. Tây Thạnh, Q. Tân Phú, TP. Hồ Chí Minh | Trung tâm CNTT HUIT
            </p>
</footer>
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
