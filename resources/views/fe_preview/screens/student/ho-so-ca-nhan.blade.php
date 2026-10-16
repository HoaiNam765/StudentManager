@extends('fe_preview.layouts.student')
@section('page_title', 'Hồ sơ cá nhân và Yêu cầu chỉnh sửa - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="light"@endsection
@section('body_attributes')class="bg-[#f8f9ff] text-slate-800 antialiased min-h-screen flex selection:bg-blue-100 selection:text-blue-900"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Hồ sơ cá nhân và Yêu cầu chỉnh sửa - HUIT Học Vụ</title>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/ho-so-ca-nhan']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">person</span>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Hồ sơ cá nhân và Yêu cầu chỉnh sửa</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Hồ sơ cá nhân và Yêu cầu chỉnh sửa</div>
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
<!-- 1. PROFILE HEADER CARD (Banner thông tin đầu trang) -->
<section class="bg-surface-container-lowest rounded-xl border border-slate-200 p-6 shadow-sm">
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
<!-- Left: Avatar & Administrative Information -->
<div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
<div class="relative group">
<img alt="" class="w-24 h-24 rounded-xl object-cover border-2 border-primary/20 shadow-sm" data-alt="Chân dung sinh viên nam năng động khoa công nghệ thông tin trường HUIT, chụp cận cảnh sắc nét với biểu cảm tự tin thân thiện, ánh sáng mềm trong trẻo, phong cách tối giản thanh lịch." src="https://lh3.googleusercontent.com/aida-public/AB6AXuCJg1m6PF0nlM6aQAFug8_m_dOQmB7bUlQCzbYDx5yJWgO5a7FNTqkINM4DuxjVayRUZddGpCV_fOH_5BSs508BnE2kEYRWQmKCocLIekswKVeW5b3v-OQvcUvALpI5k6zM8h5TZf2fIXAGuhM-02aOHkMakdeu_ln_aNS2HLGiwc2h4RgNUrES6-v2kVNLGAt9bO6hMAW_1ARLbqAEnjYdbq0TOJE71I7jK6-SvOCPSahJ7FMcBxxAWeJ2ZH01Egc0liN_VEnAxgo"/>
<button class="absolute inset-0 bg-inverse-surface/50 rounded-xl flex flex-col items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity backdrop-blur-[2px]" title="Đổi ảnh thẻ sinh viên" type="button">
<span class="material-symbols-outlined text-[20px]" data-icon="photo_camera">photo_camera</span>
<span class="text-[10px] font-medium mt-0.5">Đổi ảnh</span>
</button>
</div>
<div class="space-y-1.5">
<div class="flex flex-wrap items-center gap-2.5">
<h1 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">NGUYỄN VĂN AN</h1>
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                Đang học - Khóa 21 (2021 - 2025)
              </span>
</div>
<div class="flex flex-wrap items-center gap-y-1 gap-x-3 font-body-sm text-body-sm text-on-surface-variant">
<span><strong class="text-on-surface">MSSV:</strong> 2400000007</span>
<span class="text-outline-variant">•</span>
<span><strong class="text-on-surface">Lớp hành chính:</strong> 12DHTH01</span>
<span class="text-outline-variant">•</span>
<span><strong class="text-on-surface">Ngành:</strong> Công nghệ thông tin</span>
<span class="text-outline-variant">•</span>
<span><strong class="text-on-surface">Khoa:</strong> Công nghệ thông tin</span>
</div>
<!-- Cố vấn học tập -->
<div class="flex items-center gap-2 pt-1 font-body-sm text-body-sm text-secondary">
<span class="material-symbols-outlined text-[16px] text-primary" data-icon="school">school</span>
<span>Cố vấn học tập: <strong class="text-on-surface">ThS. Người dùng mẫu 10</strong></span>
<span class="text-outline-variant">|</span>
<a class="hover:text-primary flex items-center gap-1 text-[11px]" href="mailto:nguoidung041@example.test" title="Gửi thư cho CVHT">
<span class="material-symbols-outlined text-[14px]" data-icon="mail">mail</span>
                nguoidung041@example.test
              </a>
<span class="text-outline-variant">|</span>
<span class="flex items-center gap-1 text-[11px]">
<span class="material-symbols-outlined text-[14px]" data-icon="call">call</span>
                0908 123 456
              </span>
</div>
</div>
</div>
<!-- Right: 3 Quick Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 self-stretch lg:self-auto">
<!-- Stat 1: Tín chỉ tích lũy -->
<div class="bg-surface-container-low border border-slate-200/80 rounded-lg p-3.5 flex flex-col justify-between min-w-[160px]">
<div class="flex items-center justify-between text-on-surface-variant">
<span class="font-label-sm text-label-sm font-semibold uppercase tracking-wider text-secondary">Tích lũy tín chỉ</span>
<span class="material-symbols-outlined text-[18px] text-primary" data-icon="fact_check">fact_check</span>
</div>
<div class="my-1.5">
<div class="flex items-baseline gap-1">
<span class="font-headline-lg text-headline-lg font-bold text-primary">96</span>
<span class="text-on-surface-variant text-sm font-medium">/ 150 TC</span>
</div>
<div class="w-full bg-slate-200 h-1.5 rounded-full mt-2 overflow-hidden">
<div class="bg-primary h-full rounded-full" style="width: 64%"></div>
</div>
</div>
<span class="text-[11px] text-on-surface-variant font-medium">Tiến độ đào tạo: 64%</span>
</div>
<!-- Stat 2: GPA tích lũy -->
<div class="bg-surface-container-low border border-slate-200/80 rounded-lg p-3.5 flex flex-col justify-between min-w-[160px]">
<div class="flex items-center justify-between text-on-surface-variant">
<span class="font-label-sm text-label-sm font-semibold uppercase tracking-wider text-secondary">GPA tích lũy</span>
<span class="material-symbols-outlined text-[18px] text-tertiary" data-icon="grade">grade</span>
</div>
<div class="my-1.5">
<div class="flex items-baseline gap-1">
<span class="font-headline-lg text-headline-lg font-bold text-on-surface">3.42</span>
<span class="text-on-surface-variant text-sm font-medium">/ 4.0</span>
</div>
<span class="inline-block mt-1 font-body-sm text-body-sm text-secondary">Hệ 10: <strong class="text-on-surface">8.32</strong></span>
</div>
<span class="text-[11px] text-emerald-700 font-semibold flex items-center gap-0.5">
<span class="material-symbols-outlined text-[14px]" data-icon="trending_up">trending_up</span>
              Tăng 0.15 so với HK trước
            </span>
</div>
<!-- Stat 3: Xếp loại học lực -->
<div class="bg-surface-container-low border border-slate-200/80 rounded-lg p-3.5 flex flex-col justify-between min-w-[160px]">
<div class="flex items-center justify-between text-on-surface-variant">
<span class="font-label-sm text-label-sm font-semibold uppercase tracking-wider text-secondary">Xếp loại học lực</span>
<span class="material-symbols-outlined text-[18px] text-amber-700" data-icon="military_tech">military_tech</span>
</div>
<div class="my-1.5">
<span class="font-headline-lg text-headline-lg font-bold text-emerald-700">Giỏi</span>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Đạt chuẩn tiến độ</p>
</div>
<span class="text-[11px] text-secondary font-medium">Không cảnh báo học vụ</span>
</div>
</div>
</div>
</section>
<!-- 2. GRID 12 CỘT: CỘT TRÁI (7 CỘT) & CỘT PHẢI (5 CỘT) -->
<div class="grid grid-cols-12 gap-6">
<!-- ================= CỘT TRÁI: HỒ SƠ CHI TIẾT (COL-SPAN-7) ================= -->
<div class="col-span-12 lg:col-span-7 space-y-6">
<!-- NHÓM 1: Thông tin cá nhân cơ bản (Dữ liệu gốc pháp lý) -->
<div class="bg-surface-container-lowest rounded-xl border border-slate-200 shadow-sm overflow-hidden">
<div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-surface-bright">
<div class="flex items-center gap-2.5">
<span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
<span class="material-symbols-outlined text-[18px]" data-icon="badge">badge</span>
</span>
<div>
<h2 class="font-headline-sm text-headline-sm text-on-surface font-semibold">1. Thông tin cá nhân cơ bản</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Dữ liệu định danh pháp lý đồng bộ với Cơ sở dữ liệu Quốc gia</p>
</div>
</div>
<span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-700 rounded-md border border-slate-200 flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]" data-icon="lock">lock</span>
              Bảo mật cấp cao
            </span>
</div>
<!-- Thông báo bảo mật -->
<div class="p-6 space-y-5">
<div class="bg-amber-50 border border-amber-200 text-amber-900 rounded-lg p-3.5 flex items-start gap-3">
<span class="material-symbols-outlined text-[20px] text-amber-700 mt-0.5 flex-shrink-0" data-icon="shield_person">shield_person</span>
<p class="font-body-sm text-body-sm text-amber-800 leading-relaxed">
                Các trường định danh pháp lý được khóa bảo mật. Để thay đổi cần gửi yêu cầu kèm minh chứng gốc (CCCD gắn chip, Giấy khai sinh trích lục hoặc xác nhận cư trú CT07).
              </p>
</div>
<!-- Fields Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
<!-- Họ và tên (Khóa) -->
<div class="space-y-1">
<div class="flex items-center justify-between">
<label class="font-label-md text-label-md text-on-surface-variant font-medium">Họ và tên</label>
<button class="text-primary text-[11px] font-semibold hover:underline flex items-center gap-0.5" onclick="document.getElementById('editModal').classList.remove('hidden')" type="button">
<span>Yêu cầu chỉnh sửa</span>
<span class="material-symbols-outlined text-[12px]" data-icon="edit">edit</span>
</button>
</div>
<div class="relative">
<input aria-label="Ô nhập liệu" class="w-full bg-slate-100 border border-slate-300 rounded-lg px-3 py-2 text-on-surface font-medium cursor-not-allowed pr-8 text-sm" disabled="" type="text" value="Người dùng mẫu 25"/>
<span class="material-symbols-outlined text-[16px] text-slate-500 absolute right-2.5 top-2.5" data-icon="lock" title="Chỉ đọc">lock</span>
</div>
</div>
<!-- Số CCCD (Khóa) -->
<div class="space-y-1">
<div class="flex items-center justify-between">
<label class="font-label-md text-label-md text-on-surface-variant font-medium">Số CCCD / Định danh</label>
<button class="text-primary text-[11px] font-semibold hover:underline flex items-center gap-0.5" onclick="document.getElementById('editModal').classList.remove('hidden')" type="button">
<span>Yêu cầu chỉnh sửa</span>
<span class="material-symbols-outlined text-[12px]" data-icon="edit">edit</span>
</button>
</div>
<div class="relative">
<input aria-label="Ô nhập liệu" class="w-full bg-slate-100 border border-slate-300 rounded-lg px-3 py-2 text-on-surface font-medium cursor-not-allowed pr-8 text-sm" disabled="" type="text" value="079203001234"/>
<span class="material-symbols-outlined text-[16px] text-slate-500 absolute right-2.5 top-2.5" data-icon="lock" title="Chỉ đọc">lock</span>
</div>
</div>
<!-- Ngày sinh (Khóa) -->
<div class="space-y-1">
<div class="flex items-center justify-between">
<label class="font-label-md text-label-md text-on-surface-variant font-medium">Ngày sinh</label>
<span class="text-[11px] text-slate-500 font-medium">Khóa</span>
</div>
<div class="relative">
<input aria-label="Ô nhập liệu" class="w-full bg-slate-100 border border-slate-300 rounded-lg px-3 py-2 text-on-surface font-medium cursor-not-allowed pr-8 text-sm" disabled="" type="text" value="15/08/2003"/>
<span class="material-symbols-outlined text-[16px] text-slate-500 absolute right-2.5 top-2.5" data-icon="lock">lock</span>
</div>
</div>
<!-- Giới tính (Khóa) -->
<div class="space-y-1">
<div class="flex items-center justify-between">
<label class="font-label-md text-label-md text-on-surface-variant font-medium">Giới tính</label>
<span class="text-[11px] text-slate-500 font-medium">Khóa</span>
</div>
<div class="relative">
<input aria-label="Ô nhập liệu" class="w-full bg-slate-100 border border-slate-300 rounded-lg px-3 py-2 text-on-surface font-medium cursor-not-allowed pr-8 text-sm" disabled="" type="text" value="Nam"/>
<span class="material-symbols-outlined text-[16px] text-slate-500 absolute right-2.5 top-2.5" data-icon="lock">lock</span>
</div>
</div>
<!-- Dân tộc, Tôn giáo, Nơi sinh (ReadOnly) -->
<div class="space-y-1">
<label class="font-label-md text-label-md text-on-surface-variant font-medium">Dân tộc</label>
<input aria-label="Ô nhập liệu" class="w-full bg-slate-100 border border-slate-300 rounded-lg px-3 py-2 text-on-surface font-medium cursor-not-allowed text-sm" disabled="" type="text" value="Kinh"/>
</div>
<div class="space-y-1">
<label class="font-label-md text-label-md text-on-surface-variant font-medium">Tôn giáo</label>
<input aria-label="Ô nhập liệu" class="w-full bg-slate-100 border border-slate-300 rounded-lg px-3 py-2 text-on-surface font-medium cursor-not-allowed text-sm" disabled="" type="text" value="Không"/>
</div>
<div class="sm:col-span-2 space-y-1">
<label class="font-label-md text-label-md text-on-surface-variant font-medium">Nơi sinh (theo Giấy khai sinh)</label>
<input aria-label="Ô nhập liệu" class="w-full bg-slate-100 border border-slate-300 rounded-lg px-3 py-2 text-on-surface font-medium cursor-not-allowed text-sm" disabled="" type="text" value="TP. Hồ Chí Minh"/>
</div>
</div>
</div>
</div>
<!-- NHÓM 2: Thông tin liên hệ & Cư trú (Cho phép sinh viên cập nhật trực tiếp) -->
<div class="bg-surface-container-lowest rounded-xl border border-slate-200 shadow-sm overflow-hidden">
<div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-surface-bright">
<div class="flex items-center gap-2.5">
<span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
<span class="material-symbols-outlined text-[18px]" data-icon="contact_phone">contact_phone</span>
</span>
<div>
<h2 class="font-headline-sm text-headline-sm text-on-surface font-semibold">2. Thông tin liên hệ &amp; Cư trú</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Sinh viên chủ động cập nhật để nhận thông báo khẩn cấp từ nhà trường</p>
</div>
</div>
<span class="text-xs font-semibold px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-md border border-emerald-200">
              Có thể cập nhật
            </span>
</div>
<form class="p-6 space-y-4" onsubmit="event.preventDefault(); alert('Cập nhật thông tin liên hệ thành công!');">
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
<!-- Email SV (Cố định) -->
<div class="space-y-1">
<label class="font-label-md text-label-md text-on-surface-variant font-medium">Email sinh viên (Cấp bởi trường)</label>
<div class="relative">
<input aria-label="Ô nhập liệu" class="w-full bg-slate-100 border border-slate-300 rounded-lg px-3 py-2 text-on-surface font-medium cursor-not-allowed pr-8 text-sm" disabled="" type="email" value="nguoidung006@example.test"/>
<span class="material-symbols-outlined text-[16px] text-slate-500 absolute right-2.5 top-2.5" data-icon="lock">lock</span>
</div>
</div>
<!-- Email cá nhân (Có thể sửa) -->
<div class="space-y-1">
<div class="flex items-center justify-between">
<label class="font-label-md text-label-md text-on-surface-variant font-medium">Email cá nhân dự phòng</label>
<span class="inline-flex items-center gap-0.5 text-[10px] text-emerald-700 font-semibold bg-emerald-50 px-1.5 py-0.5 rounded">
<span class="material-symbols-outlined text-[12px]" data-icon="verified">verified</span>
                    Đã xác thực
                  </span>
</div>
<input aria-label="Ô nhập liệu" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-on-surface focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 text-sm" type="email" value="nguoidung016@example.test"/>
</div>
<!-- SĐT di động (Có thể sửa) -->
<div class="space-y-1 sm:col-span-2">
<label class="font-label-md text-label-md text-on-surface-variant font-medium">Số điện thoại di động chính thức</label>
<div class="relative">
<input aria-label="Ô nhập liệu" class="w-full bg-white border border-slate-300 rounded-lg pl-10 pr-3 py-2 text-on-surface focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 text-sm" type="tel" value="0912 345 678"/>
<span class="material-symbols-outlined text-[18px] text-slate-500 absolute left-3 top-2.5" data-icon="smartphone">smartphone</span>
</div>
</div>
<!-- Địa chỉ thường trú -->
<div class="space-y-1 sm:col-span-2">
<label class="font-label-md text-label-md text-on-surface-variant font-medium">Địa chỉ thường trú / Hộ khẩu hiện tại</label>
<div class="relative">
<input aria-label="Ô nhập liệu" class="w-full bg-white border border-slate-300 rounded-lg pl-10 pr-3 py-2 text-on-surface focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 text-sm" type="text" value="140 Người dùng mẫu 18, P. Tây Thạnh, Q. Tân Phú, TP. Hồ Chí Minh"/>
<span class="material-symbols-outlined text-[18px] text-slate-500 absolute left-3 top-2.5" data-icon="location_on">location_on</span>
</div>
</div>
</div>
<!-- Nút Cập nhật thông tin liên hệ -->
<div class="pt-2 flex justify-end">
<button class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white rounded-lg font-label-md text-label-md font-semibold hover:bg-primary-container transition-colors shadow-sm" type="submit">
<span class="material-symbols-outlined text-[18px]" data-icon="save">save</span>
<span>Cập nhật thông tin liên hệ</span>
</button>
</div>
</form>
</div>
<!-- NHÓM 3: Thông tin gia đình & Liên hệ khẩn cấp -->
<div class="bg-surface-container-lowest rounded-xl border border-slate-200 shadow-sm overflow-hidden">
<div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-surface-bright">
<div class="flex items-center gap-2.5">
<span class="w-8 h-8 rounded-lg bg-tertiary/10 text-tertiary flex items-center justify-center">
<span class="material-symbols-outlined text-[18px]" data-icon="family_restroom">family_restroom</span>
</span>
<div>
<h2 class="font-headline-sm text-headline-sm text-on-surface font-semibold">3. Thông tin gia đình &amp; Liên hệ khẩn cấp</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Sử dụng trong các trường hợp bảo hộ quyền lợi và sự cố bất khả kháng</p>
</div>
</div>
</div>
<div class="p-6 space-y-4">
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
<div class="space-y-1">
<label class="font-label-md text-label-md text-on-surface-variant font-medium">Họ và tên người liên hệ / Phụ huynh</label>
<input aria-label="Ô nhập liệu" class="w-full bg-slate-100 border border-slate-300 rounded-lg px-3 py-2 text-on-surface font-medium cursor-not-allowed text-sm" disabled="" type="text" value="Người dùng mẫu 26"/>
</div>
<div class="space-y-1">
<label class="font-label-md text-label-md text-on-surface-variant font-medium">Mối quan hệ</label>
<input aria-label="Ô nhập liệu" class="w-full bg-slate-100 border border-slate-300 rounded-lg px-3 py-2 text-on-surface font-medium cursor-not-allowed text-sm" disabled="" type="text" value="Cha (Bố)"/>
</div>
<div class="space-y-1">
<label class="font-label-md text-label-md text-on-surface-variant font-medium">Số điện thoại liên hệ khẩn cấp</label>
<input aria-label="Ô nhập liệu" class="w-full bg-slate-100 border border-slate-300 rounded-lg px-3 py-2 text-on-surface font-medium cursor-not-allowed text-sm" disabled="" type="text" value="0903 987 654"/>
</div>
<div class="space-y-1">
<label class="font-label-md text-label-md text-on-surface-variant font-medium">Địa chỉ người thân</label>
<input aria-label="Ô nhập liệu" class="w-full bg-slate-100 border border-slate-300 rounded-lg px-3 py-2 text-on-surface font-medium cursor-not-allowed text-sm" disabled="" type="text" value="Trùng địa chỉ thường trú sinh viên"/>
</div>
</div>
</div>
</div>
</div>
<!-- ================= CỘT PHẢI: LỊCH SỬ YÊU CẦU & MODAL DEMO (COL-SPAN-5) ================= -->
<div class="col-span-12 lg:col-span-5 space-y-6">
<!-- CARD: Lịch sử yêu cầu chỉnh sửa thông tin học vụ -->
<div class="bg-surface-container-lowest rounded-xl border border-slate-200 shadow-sm overflow-hidden">
<div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-surface-bright">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[20px] text-primary" data-icon="history">history</span>
<h2 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Lịch sử yêu cầu chỉnh sửa</h2>
</div>
<button class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary text-white rounded-lg font-label-md text-label-md font-semibold hover:bg-primary-container transition-colors shadow-sm" onclick="document.getElementById('editModal').classList.remove('hidden')" type="button">
<span class="material-symbols-outlined text-[16px]" data-icon="add_circle">add_circle</span>
<span>Tạo yêu cầu mới</span>
</button>
</div>
<div class="p-5 space-y-4">
<!-- Yêu cầu 1: #YCC-1082 (Chờ duyệt) -->
<div class="border border-amber-200 bg-amber-50/50 rounded-lg p-4 space-y-2.5 transition-all hover:shadow-sm">
<div class="flex items-start justify-between gap-2">
<div>
<span class="font-label-sm text-label-sm font-bold text-amber-800">#YCC-1082</span>
<h3 class="font-headline-sm text-headline-sm text-on-surface font-semibold mt-0.5">Điều chỉnh Số CCCD và Quê quán</h3>
</div>
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200 flex-shrink-0">
<span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                  Chờ phòng Đào tạo duyệt
                </span>
</div>
<div class="font-body-sm text-body-sm text-on-surface-variant space-y-1">
<p class="flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]" data-icon="calendar_today">calendar_today</span>
<span>Gửi ngày: <strong>10/10/2026</strong> (09:42 SA)</span>
</p>
<p class="bg-white/80 p-2 rounded border border-amber-200/60 text-secondary text-xs">
<strong>Ghi chú:</strong> Đổi sang CCCD gắn chip mới, số định danh 12 chữ số theo quy chuẩn mới của Bộ Công an.
                </p>
</div>
<!-- File đính kèm -->
<div class="pt-1 flex items-center justify-between text-xs">
<span class="text-secondary font-medium">Tệp minh chứng:</span>
<a class="inline-flex items-center gap-1 text-primary font-medium hover:underline bg-white px-2 py-1 rounded border border-slate-200" href="#">
<span class="material-symbols-outlined text-[14px] text-error" data-icon="picture_as_pdf">picture_as_pdf</span>
<span>cccd_mat_truoc_sau.pdf (1.8 MB)</span>
</a>
</div>
</div>
<!-- Yêu cầu 2: #YCC-0941 (Đã duyệt) -->
<div class="border border-emerald-200 bg-emerald-50/40 rounded-lg p-4 space-y-2.5 transition-all hover:shadow-sm">
<div class="flex items-start justify-between gap-2">
<div>
<span class="font-label-sm text-label-sm font-bold text-emerald-800">#YCC-0941</span>
<h3 class="font-headline-sm text-headline-sm text-on-surface font-semibold mt-0.5">Sửa sai chính tả Tên đệm</h3>
</div>
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200 flex-shrink-0">
<span class="material-symbols-outlined text-[13px]" data-icon="check_circle">check_circle</span>
                  Đã duyệt
                </span>
</div>
<div class="font-body-sm text-body-sm text-on-surface-variant space-y-1">
<p class="flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]" data-icon="calendar_today">calendar_today</span>
<span>Duyệt ngày: <strong>15/01/2026</strong></span>
</p>
<p class="text-xs text-secondary">
                  Thực hiện bởi: <strong class="text-on-surface">Cán bộ Phòng Đào tạo (P. Đào tạo)</strong> - Đã cập nhật vào hồ sơ gốc sinh viên.
                </p>
</div>
</div>
<!-- Yêu cầu 3: #YCC-0715 (Từ chối) -->
<div class="border border-red-200 bg-red-50/40 rounded-lg p-4 space-y-2.5 transition-all hover:shadow-sm">
<div class="flex items-start justify-between gap-2">
<div>
<span class="font-label-sm text-label-sm font-bold text-red-800">#YCC-0715</span>
<h3 class="font-headline-sm text-headline-sm text-on-surface font-semibold mt-0.5">Thay đổi Hộ khẩu thường trú</h3>
</div>
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-200 flex-shrink-0">
<span class="material-symbols-outlined text-[13px]" data-icon="cancel">cancel</span>
                  Từ chối
                </span>
</div>
<div class="font-body-sm text-body-sm text-on-surface-variant space-y-1">
<p class="text-xs bg-red-100/60 text-red-900 p-2.5 rounded border border-red-200 leading-relaxed">
<strong>Lý do từ chối:</strong> Ảnh chụp giấy xác nhận cư trú CT07 bị mờ, không rõ dấu mộc đỏ của Công an Phường. Vui lòng nộp lại bản scan màu rõ nét.
                </p>
<div class="flex justify-end pt-1">
<button class="text-primary text-xs font-semibold hover:underline flex items-center gap-0.5" onclick="document.getElementById('editModal').classList.remove('hidden')" type="button">
<span>Gửi lại yêu cầu mới</span>
<span class="material-symbols-outlined text-[12px]" data-icon="arrow_forward">arrow_forward</span>
</button>
</div>
</div>
</div>
</div>
</div>
<!-- Card Hướng dẫn quy trình điều chỉnh -->
<div class="bg-surface-container-low rounded-xl border border-slate-200/80 p-5 space-y-3">
<div class="flex items-center gap-2 text-primary font-headline-sm text-headline-sm font-semibold">
<span class="material-symbols-outlined" data-icon="help_outline">help_outline</span>
<span>Quy trình xử lý yêu cầu</span>
</div>
<ol class="space-y-2 text-xs text-on-surface-variant list-decimal list-inside leading-relaxed">
<li>Sinh viên chọn trường thông tin pháp lý cần điều chỉnh và điền giá trị mới chính xác.</li>
<li>Đính kèm file scan minh chứng gốc (định dạng PDF hoặc JPEG, dung lượng &lt; 5MB).</li>
<li>Phòng Đào tạo đối chiếu hồ sơ tuyển sinh gốc trong vòng <strong>3 - 5 ngày làm việc</strong>.</li>
<li>Kết quả cập nhật sẽ tự động đồng bộ trên Cổng Sinh viên và ứng dụng HUIT Student.</li>
</ol>
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
