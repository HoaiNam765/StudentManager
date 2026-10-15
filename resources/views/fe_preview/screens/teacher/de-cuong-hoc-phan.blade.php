@extends('fe_preview.layouts.lecturer')
@section('page_title', 'Đề cương chi tiết học phần - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Đề cương chi tiết học phần - HUIT Học Vụ</title>
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
<!-- Brand / Header -->
<div class="p-4 border-b border-slate-100 flex items-center justify-between">
<div class="flex items-center gap-3">
<div class="w-9 h-9 rounded-xl bg-primary text-white flex items-center justify-center font-bold text-sm shadow-xs shrink-0">
<span class="material-symbols-outlined text-[20px]">co_present</span>
</div>
<div class="overflow-hidden">
<div class="font-bold text-[13.5px] text-slate-900 leading-tight truncate">HUIT Học Vụ</div>
<div class="text-[11px] text-slate-500 font-medium truncate">Cổng Giảng viên</div>
</div>
</div>
<button class="lg:hidden p-1.5 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100" onclick="toggleSidebar()">
<span class="material-symbols-outlined text-[20px]">close</span>
</button>
</div>
<!-- Navigation Menu (Dynamic by Stage) -->
<div class="flex-1 overflow-y-auto custom-scrollbar p-3 space-y-6">
<div class="space-y-1">
<div class="px-3 py-1.5 text-[10.5px] font-bold tracking-wider text-slate-500 uppercase">HOẠT ĐỘNG GIẢNG DẠY</div>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/lich-giang-day']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">calendar_month</span>
<span class="truncate">Lịch giảng dạy</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/lop-hoc-phan']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">class</span>
<span class="truncate">Lớp học phần phụ trách</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/nhap-diem']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">edit_note</span>
<span class="truncate">Nhập &amp; nộp điểm</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/de-cuong-hoc-phan']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">menu_book</span>
<span class="truncate">Đề cương học phần</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/thong-bao-lop']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">campaign</span>
<span class="truncate">Gửi thông báo cho lớp</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/ho-so-lich-ban']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">event_busy</span>
<span class="truncate">Hồ sơ &amp; Lịch bận</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/lich-coi-thi']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">event_upcoming</span>
<span class="truncate">Lịch coi thi</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/diem-danh']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">check_circle</span>
<span class="truncate">Điểm danh sinh viên</span>
</a>
</div>
<div class="space-y-1">
<div class="px-3 py-1.5 text-[10.5px] font-bold tracking-wider text-slate-500 uppercase">CỐ VẤN &amp; DUYỆT ĐƠN</div>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/lop-co-van-hoc-tap']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">supervisor_account</span>
<span class="truncate">Lớp cố vấn học tập</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/duyet-don']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">rule_folder</span>
<span class="truncate">Duyệt đơn từ</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/ket-qua-khao-sat']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">poll</span>
<span class="truncate">Kết quả khảo sát</span>
</a>
</div>
</div>
<!-- Sidebar Bottom -->
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Đề cương chi tiết học phần</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Đề cương chi tiết học phần</div>
</div>
</div>
<!-- Header Right -->
<div class="flex items-center gap-2 sm:gap-3 shrink-0">
<!-- Role quick switch -->
<div class="hidden md:flex items-center p-1 bg-slate-100 rounded-lg text-xs font-medium">
<a class="px-2.5 py-1 text-slate-600 hover:text-slate-900" href="{{ route('fe.preview.show', ['screen' => 'student/tong-quan']) }}">Sinh viên</a>
<span class="px-2.5 py-1 rounded-md bg-white text-primary font-semibold shadow-xs">Giảng viên</span>
<a class="px-2.5 py-1 text-slate-600 hover:text-slate-900" href="{{ route('fe.preview.show', ['screen' => 'admin/tong-quan']) }}">Phòng ĐT</a>
</div>
<!-- Notification Bell -->
<a class="relative p-2 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/thong-bao-lop']) }}" title="Thông báo">
<span class="material-symbols-outlined text-[21px]">notifications</span>
</a>
<!-- User Profile Pill -->
<div class="flex items-center gap-2.5 pl-2 border-l border-slate-200">
<div class="w-8 h-8 rounded-full bg-blue-100 text-primary font-bold text-xs flex items-center justify-center border border-blue-200">
            THN
          </div>
<div class="hidden sm:block text-left">
<div class="text-xs font-semibold text-slate-900 leading-tight">TS. Trần Hoàng Nam</div>
<div class="text-[11px] text-slate-500">GV-2018042 • Khoa CNTT</div>
</div>
</div>
</div>
</header>
<!-- MAIN BODY CONTENT -->
<main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
<!-- ===================================================================== -->
<!-- CỘT TRÁI (340px): DANH SÁCH HỌC PHẦN BIÊN SOẠN ĐỀ CƯƠNG               -->
<!-- ===================================================================== -->
<section class="w-[350px] flex-shrink-0 flex flex-col gap-4">
<!-- Header cột trái + Nút tạo mới -->
<div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant shadow-sm flex flex-col gap-3">
<div class="flex items-center justify-between">
<h2 class="font-headline-sm text-headline-sm text-on-surface flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[20px]">assignment</span>
<span>Học phần biên soạn</span>
</h2>
<span class="px-2 py-0.5 bg-surface-container text-primary font-bold text-xs rounded-full">4 môn</span>
</div>
<!-- Nút Tạo phiên bản mới -->
<button class="w-full h-10 border border-primary text-primary hover:bg-primary/5 rounded-lg flex items-center justify-center gap-2 font-label-md text-label-md transition-all active:scale-[0.98]">
<span class="material-symbols-outlined text-[18px]">add_circle</span>
<span>+ Tạo phiên bản đề cương mới</span>
</button>
<!-- Ô tìm kiếm mã/tên -->
<div class="relative">
<span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-[18px] text-outline">filter_list</span>
<input aria-label="Tìm theo mã hoặc tên..." class="w-full h-9 pl-9 pr-3 rounded-lg border border-outline-variant bg-surface-container-low text-on-surface font-body-sm text-body-sm placeholder:text-outline focus:border-primary focus:bg-surface-container-lowest" placeholder="Tìm theo mã hoặc tên..." type="text"/>
</div>
<!-- Bộ lọc trạng thái tabs/chips -->
<div class="flex flex-wrap gap-1.5 pt-1">
<button class="px-2.5 py-1 rounded-full bg-primary text-white font-label-sm text-[11px] shadow-sm">Tất cả (4)</button>
<button class="px-2.5 py-1 rounded-full bg-surface-container-low hover:bg-surface-container text-secondary font-label-sm text-[11px] border border-outline-variant/60">Đã duyệt (2)</button>
<button class="px-2.5 py-1 rounded-full bg-surface-container-low hover:bg-surface-container text-secondary font-label-sm text-[11px] border border-outline-variant/60">Bản nháp (1)</button>
<button class="px-2.5 py-1 rounded-full bg-surface-container-low hover:bg-surface-container text-secondary font-label-sm text-[11px] border border-outline-variant/60">Chờ duyệt (1)</button>
</div>
</div>
<!-- Danh sách 4 thẻ học phần -->
<div class="flex flex-col gap-3 overflow-y-auto pr-0.5">
<!-- THẺ 1: Lập trình Web -->
<div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant shadow-sm hover:border-primary/50 transition-all cursor-pointer">
<div class="flex items-start justify-between gap-2">
<span class="font-label-sm text-xs font-bold text-outline">71ITEC3201</span>
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                Đã duyệt
              </span>
</div>
<h3 class="font-headline-sm text-[14px] text-on-surface font-semibold mt-1">Lập trình Web</h3>
<div class="flex items-center gap-2 mt-2 text-secondary font-body-sm text-body-sm">
<span class="bg-surface-container-low px-1.5 py-0.5 rounded text-[11px]">3 Tín chỉ</span>
<span>•</span>
<span class="text-[11px]">Phiên bản: <strong class="text-on-surface">v2.1</strong></span>
</div>
<div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-outline text-[11px] font-body-sm">
<span>Cập nhật: 15/06/2026</span>
<span class="material-symbols-outlined text-[16px] text-outline-variant">chevron_right</span>
</div>
</div>
<!-- THẺ 2 [ACTIVE / ĐANG CHỌN]: Công nghệ phần mềm -->
<div class="bg-blue-50/70 p-4 rounded-xl border-2 border-primary shadow-sm transition-all cursor-pointer relative overflow-hidden">
<div class="absolute left-0 top-0 bottom-0 w-1.5 bg-primary"></div>
<div class="flex items-start justify-between gap-2">
<div class="flex items-center gap-1.5">
<span class="font-label-sm text-xs font-bold text-primary">71ITEC3202</span>
<span class="bg-primary text-white text-[9px] font-bold px-1.5 py-0.2 rounded uppercase tracking-wider">Đang xem</span>
</div>
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-300">
<span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                Bản nháp
              </span>
</div>
<h3 class="font-headline-sm text-[15px] text-primary font-bold mt-1.5 leading-snug">Công nghệ phần mềm</h3>
<div class="flex items-center gap-2 mt-2 text-on-secondary-container font-body-sm text-body-sm">
<span class="bg-primary-fixed text-on-primary-fixed px-1.5 py-0.5 rounded font-semibold text-[11px]">3 Tín chỉ (2 LT + 1 TH)</span>
<span>•</span>
<span class="text-[11px]">Phiên bản: <strong class="text-primary">v2.2</strong></span>
</div>
<div class="mt-2.5 pt-2 border-t border-primary/20 flex items-center justify-between text-secondary text-[11px] font-body-sm">
<span class="flex items-center gap-1">
<span class="material-symbols-outlined text-[14px] text-amber-700">update</span>
                Cập nhật: 10/10/2026
              </span>
<span class="text-primary font-semibold flex items-center">
                Chi tiết <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
</span>
</div>
</div>
<!-- THẺ 3: Kiến trúc phần mềm -->
<div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant shadow-sm hover:border-primary/50 transition-all cursor-pointer">
<div class="flex items-start justify-between gap-2">
<span class="font-label-sm text-xs font-bold text-outline">71ITEC4105</span>
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                Đã duyệt
              </span>
</div>
<h3 class="font-headline-sm text-[14px] text-on-surface font-semibold mt-1">Kiến trúc phần mềm</h3>
<div class="flex items-center gap-2 mt-2 text-secondary font-body-sm text-body-sm">
<span class="bg-surface-container-low px-1.5 py-0.5 rounded text-[11px]">3 Tín chỉ</span>
<span>•</span>
<span class="text-[11px]">Phiên bản: <strong class="text-on-surface">v1.0</strong></span>
</div>
<div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-outline text-[11px] font-body-sm">
<span>Cập nhật: 20/01/2026</span>
<span class="material-symbols-outlined text-[16px] text-outline-variant">chevron_right</span>
</div>
</div>
<!-- THẺ 4: DevOps và Điện toán đám mây -->
<div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant shadow-sm hover:border-primary/50 transition-all cursor-pointer">
<div class="flex items-start justify-between gap-2">
<span class="font-label-sm text-xs font-bold text-outline">71ITEC4208</span>
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-orange-50 text-orange-700 border border-orange-200">
<span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                Chờ duyệt
              </span>
</div>
<h3 class="font-headline-sm text-[14px] text-on-surface font-semibold mt-1">DevOps và Điện toán đám mây</h3>
<div class="flex items-center gap-2 mt-2 text-secondary font-body-sm text-body-sm">
<span class="bg-surface-container-low px-1.5 py-0.5 rounded text-[11px]">3 Tín chỉ</span>
<span>•</span>
<span class="text-[11px]">Phiên bản: <strong class="text-on-surface">v1.1</strong></span>
</div>
<div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-outline text-[11px] font-body-sm">
<span>Cập nhật: 05/10/2026</span>
<span class="material-symbols-outlined text-[16px] text-outline-variant">chevron_right</span>
</div>
</div>
</div>
</section>
<!-- ===================================================================== -->
<!-- CỘT PHẢI (flex-1): NỘI DUNG CHI TIẾT ĐỀ CƯƠNG HỌC PHẦN                 -->
<!-- ===================================================================== -->
<section class="flex-1 flex flex-col gap-5 overflow-hidden">
<!-- CARD HEADER CHI TIẾT & ACTION BUTTONS -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-sm">
<div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 pb-4 border-b border-slate-100">
<div>
<div class="flex items-center gap-2 flex-wrap mb-1">
<span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-300 flex items-center gap-1">
<span class="w-2 h-2 rounded-full bg-amber-500"></span>
                  Bản nháp - Đang biên soạn
                </span>
<span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-surface-container text-primary border border-outline-variant/50">
                  Phiên bản: v2.2 (Niên giám 2026 - 2027)
                </span>
<span class="text-secondary font-body-sm text-body-sm">Mã HP: <strong class="text-on-surface font-semibold">71ITEC3202</strong></span>
</div>
<h1 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">
                CÔNG NGHỆ PHẦN MỀM <span class="text-secondary font-normal text-headline-sm">(SOFTWARE ENGINEERING)</span>
</h1>
<div class="flex flex-wrap items-center gap-y-1 gap-x-4 mt-2 text-secondary font-body-sm text-body-sm">
<span>Số tín chỉ: <strong class="text-on-surface font-semibold">3 TC (2 LT + 1 TH)</strong></span>
<span>•</span>
<span>Khoa: <strong class="text-on-surface font-semibold">Công nghệ thông tin</strong></span>
<span>•</span>
<span>Bộ môn: <strong class="text-on-surface font-semibold">Kỹ thuật phần mềm</strong></span>
<span>•</span>
<span>Giảng viên biên soạn: <strong class="text-on-surface font-semibold">TS. Nguyễn Văn Dũng</strong></span>
</div>
</div>
<!-- CỤM NÚT HÀNH ĐỘNG TRÊN CÙNG -->
<div class="flex flex-wrap items-center gap-2">
<!-- Nút Ghost: Lịch sử phiên bản -->
<button class="h-9 px-3 rounded-lg text-secondary hover:bg-surface-container-low hover:text-on-surface border border-outline-variant font-label-md text-label-md flex items-center gap-1.5 transition-colors">
<span class="material-symbols-outlined text-[18px]">history</span>
<span>Lịch sử (3)</span>
</button>
<!-- Nút Outline: Xuất PDF/Word -->
<button class="h-9 px-3 rounded-lg text-on-surface hover:bg-surface-container-low border border-outline-variant font-label-md text-label-md flex items-center gap-1.5 transition-colors">
<span class="material-symbols-outlined text-[18px]">download</span>
<span>Xuất PDF/Word</span>
</button>
<!-- Nút Secondary: Gửi Trưởng bộ môn phê duyệt -->
<button class="h-9 px-3.5 rounded-lg bg-white border border-primary text-primary hover:bg-primary/5 font-label-md text-label-md flex items-center gap-1.5 transition-all shadow-sm">
<span class="material-symbols-outlined text-[18px]">send</span>
<span>Gửi duyệt</span>
</button>
<!-- Nút Primary: Chỉnh sửa đề cương -->
<button class="h-9 px-4 rounded-lg bg-primary hover:bg-[#15429e] text-white font-label-md text-label-md flex items-center gap-1.5 shadow transition-all active:scale-95">
<span class="material-symbols-outlined text-[18px]">edit</span>
<span>Chỉnh sửa</span>
</button>
</div>
</div>
<!-- BANNER CẢNH BÁO BẢN NHÁP -->
<div class="mt-4 p-3.5 bg-amber-50/80 border border-amber-300 rounded-lg flex items-start gap-3">
<span class="material-symbols-outlined text-amber-700 text-[22px] flex-shrink-0 mt-0.5">warning</span>
<div class="text-xs font-body-sm leading-relaxed text-amber-950">
<strong class="font-semibold text-amber-900">Lưu ý học vụ:</strong> Bạn đang thao tác trên <strong>Phiên bản nháp v2.2</strong>. Sau khi chỉnh sửa hoàn tất, hãy bấm <strong>"Gửi Trưởng bộ môn phê duyệt"</strong> để thẩm định chuyên môn trước khi công bố áp dụng chính thức cho sinh viên HK1 (2026 - 2027).
            </div>
</div>
</div>
<!-- =================================================================== -->
<!-- KHỐI 5 PHẦN CẤU TRÚC ĐỀ CƯƠNG CHI TIẾT HUIT                        -->
<!-- =================================================================== -->
<div class="space-y-5 pb-8">
<!-- ----------------------------------------------------------------- -->
<!-- PHẦN 1: MỤC TIÊU HỌC PHẦN (COURSE OBJECTIVES)                     -->
<!-- ----------------------------------------------------------------- -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-sm">
<div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
<h3 class="font-headline-sm text-headline-sm text-on-surface flex items-center gap-2">
<span class="w-6 h-6 rounded bg-primary/10 text-primary flex items-center justify-center font-bold text-xs">1</span>
<span>Mục tiêu học phần (Course Objectives)</span>
</h3>
<span class="text-secondary font-body-sm text-xs">4 Mục tiêu đào tạo</span>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
<!-- CO1 -->
<div class="p-3.5 rounded-lg border border-outline-variant bg-surface-container-low/30 hover:border-primary/40 transition-colors">
<div class="flex items-center gap-2 mb-1.5">
<span class="px-2 py-0.5 rounded bg-primary text-white font-label-sm text-[11px] font-bold">CO1</span>
<span class="font-label-md text-label-md font-semibold text-on-surface">Kiến thức nền tảng &amp; Quy trình</span>
</div>
<p class="font-body-md text-[13px] text-on-surface-variant leading-relaxed">
                  Nắm vững các mô hình quy trình phát triển phần mềm hiện đại (Agile, Scrum, Waterfall) và nguyên lý quản lý vòng đời sản phẩm (SDLC).
                </p>
</div>
<!-- CO2 -->
<div class="p-3.5 rounded-lg border border-outline-variant bg-surface-container-low/30 hover:border-primary/40 transition-colors">
<div class="flex items-center gap-2 mb-1.5">
<span class="px-2 py-0.5 rounded bg-primary text-white font-label-sm text-[11px] font-bold">CO2</span>
<span class="font-label-md text-label-md font-semibold text-on-surface">Kỹ nghệ yêu cầu &amp; Đặc tả</span>
</div>
<p class="font-body-md text-[13px] text-on-surface-variant leading-relaxed">
                  Phân tích yêu cầu nghiệp vụ thực tế, mô hình hóa luồng dữ liệu hệ thống bằng ngôn ngữ UML chuẩn và xây dựng tài liệu đặc tả yêu cầu SRS.
                </p>
</div>
<!-- CO3 -->
<div class="p-3.5 rounded-lg border border-outline-variant bg-surface-container-low/30 hover:border-primary/40 transition-colors">
<div class="flex items-center gap-2 mb-1.5">
<span class="px-2 py-0.5 rounded bg-primary text-white font-label-sm text-[11px] font-bold">CO3</span>
<span class="font-label-md text-label-md font-semibold text-on-surface">Thiết kế hệ thống &amp; Kiểm thử</span>
</div>
<p class="font-body-md text-[13px] text-on-surface-variant leading-relaxed">
                  Thiết kế kiến trúc phần mềm, áp dụng mẫu thiết kế (Design Patterns) phổ biến, lập trình hướng đối tượng và thực thi kiểm thử phần mềm tự động (Unit Test).
                </p>
</div>
<!-- CO4 -->
<div class="p-3.5 rounded-lg border border-outline-variant bg-surface-container-low/30 hover:border-primary/40 transition-colors">
<div class="flex items-center gap-2 mb-1.5">
<span class="px-2 py-0.5 rounded bg-primary text-white font-label-sm text-[11px] font-bold">CO4</span>
<span class="font-label-md text-label-md font-semibold text-on-surface">Kỹ năng mềm &amp; Đạo đức nghề nghiệp</span>
</div>
<p class="font-body-md text-[13px] text-on-surface-variant leading-relaxed">
                  Kỹ năng làm việc nhóm hiệu quả, giao tiếp văn bản kỹ thuật, thuyết trình giải pháp và năng lực quản lý dự án công nghệ theo chuẩn tiến độ Agile.
                </p>
</div>
</div>
</div>
<!-- ----------------------------------------------------------------- -->
<!-- PHẦN 2: CHUẨN ĐẦU RA HỌC PHẦN (CLOs) & MA TRẬN ÁNH XẠ             -->
<!-- ----------------------------------------------------------------- -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-sm">
<div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
<h3 class="font-headline-sm text-headline-sm text-on-surface flex items-center gap-2">
<span class="w-6 h-6 rounded bg-primary/10 text-primary flex items-center justify-center font-bold text-xs">2</span>
<span>Chuẩn đầu ra học phần (CLOs) &amp; Ma trận ánh xạ PLOs</span>
</h3>
<div class="flex items-center gap-2">
<span class="text-xs bg-emerald-50 text-emerald-800 border border-emerald-200 px-2 py-0.5 rounded font-medium">Chuẩn ABET / CDIO</span>
</div>
</div>
<!-- Bảng CLOs -->
<div class="overflow-x-auto rounded-lg border border-outline-variant custom-scrollbar">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-slate-50 border-b border-outline-variant text-[12px] font-semibold text-secondary uppercase font-label-md">
<th class="py-3 px-3.5 w-20 text-center">Mã CLO</th>
<th class="py-3 px-4 min-w-[280px]">Mô tả Chuẩn đầu ra (Course Learning Outcomes)</th>
<th class="py-3 px-3 text-center w-28">Ánh xạ PLO</th>
<th class="py-3 px-3 text-center w-28">Mức Bloom</th>
<th class="py-3 px-4 min-w-[180px]">Phương pháp giảng dạy</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 text-[13px] font-body-md text-on-surface">
<tr class="hover:bg-slate-50/70 transition-colors">
<td class="py-3 px-3.5 text-center font-bold text-primary font-label-md">CLO1</td>
<td class="py-3 px-4">Giải thích được các nguyên lý kỹ nghệ phần mềm và so sánh ưu nhược điểm của các mô hình phát triển phần mềm.</td>
<td class="py-3 px-3 text-center">
<span class="px-2 py-0.5 bg-blue-50 text-primary font-bold text-xs rounded border border-blue-200">PLO1, PLO2</span>
</td>
<td class="py-3 px-3 text-center">
<span class="px-2 py-0.5 bg-amber-50 text-amber-800 font-semibold text-xs rounded border border-amber-200">K3 (Apply)</span>
</td>
<td class="py-3 px-4 text-secondary">Thuyết giảng, Thảo luận nhóm tình huống</td>
</tr>
<tr class="hover:bg-slate-50/70 transition-colors">
<td class="py-3 px-3.5 text-center font-bold text-primary font-label-md">CLO2</td>
<td class="py-3 px-4">Thu thập, phân tích và mô hình hóa yêu cầu hệ thống bằng chuẩn biểu đồ Use Case, Activity, Class diagram.</td>
<td class="py-3 px-3 text-center">
<span class="px-2 py-0.5 bg-blue-50 text-primary font-bold text-xs rounded border border-blue-200">PLO3</span>
</td>
<td class="py-3 px-3 text-center">
<span class="px-2 py-0.5 bg-purple-50 text-purple-800 font-semibold text-xs rounded border border-purple-200">K4 (Analyze)</span>
</td>
<td class="py-3 px-4 text-secondary">Thực hành Case study, Hướng dẫn đồ án</td>
</tr>
<tr class="hover:bg-slate-50/70 transition-colors">
<td class="py-3 px-3.5 text-center font-bold text-primary font-label-md">CLO3</td>
<td class="py-3 px-4">Áp dụng kiến trúc Model-View-Controller (MVC) và viết mã kiểm thử đơn vị (Unit Test) cho một mô đun nghiệp vụ.</td>
<td class="py-3 px-3 text-center">
<span class="px-2 py-0.5 bg-blue-50 text-primary font-bold text-xs rounded border border-blue-200">PLO3, PLO5</span>
</td>
<td class="py-3 px-3 text-center">
<span class="px-2 py-0.5 bg-purple-50 text-purple-800 font-semibold text-xs rounded border border-purple-200">K4 (Analyze)</span>
</td>
<td class="py-3 px-4 text-secondary">Lập trình thực hành tại phòng Lab</td>
</tr>
<tr class="hover:bg-slate-50/70 transition-colors">
<td class="py-3 px-3.5 text-center font-bold text-primary font-label-md">CLO4</td>
<td class="py-3 px-4">Phối hợp nhóm xây dựng đồ án học phần, sử dụng công cụ Git/GitHub và thuyết trình bảo vệ sản phẩm trước lớp.</td>
<td class="py-3 px-3 text-center">
<span class="px-2 py-0.5 bg-blue-50 text-primary font-bold text-xs rounded border border-blue-200">PLO7, PLO9</span>
</td>
<td class="py-3 px-3 text-center">
<span class="px-2 py-0.5 bg-amber-50 text-amber-800 font-semibold text-xs rounded border border-amber-200">K3 (Apply)</span>
</td>
<td class="py-3 px-4 text-secondary">Làm việc nhóm theo Sprint Scrum</td>
</tr>
</tbody>
</table>
</div>
</div>
<!-- ----------------------------------------------------------------- -->
<!-- PHẦN 3: KẾ HOẠCH GIẢNG DẠY CHI TIẾT 15 TUẦN                       -->
<!-- ----------------------------------------------------------------- -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-sm">
<div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
<h3 class="font-headline-sm text-headline-sm text-on-surface flex items-center gap-2">
<span class="w-6 h-6 rounded bg-primary/10 text-primary flex items-center justify-center font-bold text-xs">3</span>
<span>Kế hoạch giảng dạy chi tiết (15 Tuần chuẩn Đào tạo HUIT)</span>
</h3>
<div class="flex items-center gap-3 text-xs text-secondary font-body-sm">
<span>Tổng: <strong class="text-on-surface">30 tiết LT</strong></span>
<span>•</span>
<span><strong class="text-on-surface">30 tiết TH</strong></span>
</div>
</div>
<div class="overflow-x-auto rounded-lg border border-outline-variant custom-scrollbar">
<table class="w-full text-left border-collapse text-[13px]">
<thead>
<tr class="bg-slate-50 border-b border-outline-variant text-[12px] font-semibold text-secondary uppercase font-label-md">
<th class="py-3 px-3.5 w-16 text-center">Tuần</th>
<th class="py-3 px-4 min-w-[260px]">Nội dung bài học / Hoạt động giảng dạy</th>
<th class="py-3 px-3 text-center w-20">LT/TH</th>
<th class="py-3 px-3 text-center w-24">CLOs</th>
<th class="py-3 px-4 min-w-[220px]">Hoạt động học tập &amp; Đánh giá</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 font-body-md text-on-surface">
<!-- Mốc Tuần 1 -->
<tr class="hover:bg-slate-50 transition-colors">
<td class="py-3 px-3.5 text-center font-bold text-primary font-label-md bg-blue-50/40">Tuần 1</td>
<td class="py-3 px-4">
<div class="font-semibold text-on-surface">Giới thiệu Tổng quan Công nghệ phần mềm</div>
<div class="text-xs text-secondary mt-0.5">Vòng đời phần mềm (SDLC), mô hình Waterfall, Agile Manifesto và khung làm việc Scrum.</div>
</td>
<td class="py-3 px-3 text-center font-semibold text-secondary text-xs">2 LT / 0 TH</td>
<td class="py-3 px-3 text-center"><span class="px-1.5 py-0.5 bg-slate-100 rounded text-xs font-semibold">CLO1</span></td>
<td class="py-3 px-4 text-secondary text-xs">Nghe giảng, thành lập nhóm đồ án (4-5 SV/nhóm), chọn đề tài.</td>
</tr>
<!-- Mốc Tuần 4 -->
<tr class="hover:bg-slate-50 transition-colors">
<td class="py-3 px-3.5 text-center font-bold text-primary font-label-md bg-blue-50/40">Tuần 4</td>
<td class="py-3 px-4">
<div class="font-semibold text-on-surface">Kỹ nghệ yêu cầu &amp; Đặc tả phần mềm (SRS)</div>
<div class="text-xs text-secondary mt-0.5">Kỹ thuật thu thập yêu cầu, viết User Story theo mẫu INVEST, phân biệt chức năng và phi chức năng.</div>
</td>
<td class="py-3 px-3 text-center font-semibold text-secondary text-xs">2 LT / 2 TH</td>
<td class="py-3 px-3 text-center"><span class="px-1.5 py-0.5 bg-slate-100 rounded text-xs font-semibold">CLO2</span></td>
<td class="py-3 px-4 text-secondary text-xs">Thực hành viết tài liệu SRS cho đồ án nhóm; Nộp bản phác thảo Sprint 1.</td>
</tr>
<!-- Mốc Tuần 8 -->
<tr class="hover:bg-slate-50 transition-colors">
<td class="py-3 px-3.5 text-center font-bold text-primary font-label-md bg-blue-50/40">Tuần 8</td>
<td class="py-3 px-4">
<div class="font-semibold text-on-surface">Thiết kế kiến trúc, Thiết kế chi tiết &amp; Mô hình hóa UML</div>
<div class="text-xs text-secondary mt-0.5">Thiết kế CSDL quan hệ, thiết kế giao diện (UI/UX wireframe), áp dụng mô hình phân lớp.</div>
</td>
<td class="py-3 px-3 text-center font-semibold text-secondary text-xs">2 LT / 2 TH</td>
<td class="py-3 px-3 text-center"><span class="px-1.5 py-0.5 bg-slate-100 rounded text-xs font-semibold">CLO2, CLO3</span></td>
<td class="py-3 px-4 text-secondary text-xs">Báo cáo tiến độ Giữa kỳ (Checkpoint 1); Đánh giá sơ bộ điểm nhóm.</td>
</tr>
<!-- Mốc Tuần 11 -->
<tr class="hover:bg-slate-50 transition-colors">
<td class="py-3 px-3.5 text-center font-bold text-primary font-label-md bg-blue-50/40">Tuần 11</td>
<td class="py-3 px-4">
<div class="font-semibold text-on-surface">Kiểm thử phần mềm &amp; Đảm bảo chất lượng (QA/QC)</div>
<div class="text-xs text-secondary mt-0.5">Nguyên lý kiểm thử White-box và Black-box, viết Test Cases, kiểm thử tự động với JUnit/Jest.</div>
</td>
<td class="py-3 px-3 text-center font-semibold text-secondary text-xs">2 LT / 2 TH</td>
<td class="py-3 px-3 text-center"><span class="px-1.5 py-0.5 bg-slate-100 rounded text-xs font-semibold">CLO3</span></td>
<td class="py-3 px-4 text-secondary text-xs">Thực hành viết Unit Test tại phòng máy; Ghi nhận tỷ lệ bao phủ mã (Code Coverage).</td>
</tr>
<!-- Mốc Tuần 15 -->
<tr class="hover:bg-slate-50 transition-colors bg-blue-50/20">
<td class="py-3 px-3.5 text-center font-bold text-primary font-label-md bg-blue-50/60">Tuần 15</td>
<td class="py-3 px-4">
<div class="font-semibold text-on-surface">Báo cáo đồ án học phần &amp; Đánh giá tổng kết</div>
<div class="text-xs text-secondary mt-0.5">Demo sản phẩm phần mềm hoàn chỉnh, bảo vệ giải pháp kiến trúc và trả lời câu hỏi vấn đáp.</div>
</td>
<td class="py-3 px-3 text-center font-semibold text-secondary text-xs">0 LT / 4 TH</td>
<td class="py-3 px-3 text-center"><span class="px-1.5 py-0.5 bg-blue-100 text-primary font-bold rounded text-xs">CLO1-4</span></td>
<td class="py-3 px-4 text-primary font-medium text-xs">Vấn đáp hội đồng giảng viên; Chấm điểm Đồ án kết thúc học phần.</td>
</tr>
</tbody>
</table>
</div>
<div class="mt-3 flex items-center justify-between text-xs text-secondary">
<span>* Hiển thị các tuần mốc trọng yếu. Bấm để xem toàn bộ 15 tuần chi tiết.</span>
<button class="text-primary hover:underline font-semibold flex items-center gap-1">
<span>Xem chi tiết đầy đủ 15 tuần</span>
<span class="material-symbols-outlined text-[16px]">expand_more</span>
</button>
</div>
</div>
<!-- ----------------------------------------------------------------- -->
<!-- PHẦN 4: PHƯƠNG PHÁP ĐÁNH GIÁ & TRỌNG SỐ ĐIỂM                      -->
<!-- ----------------------------------------------------------------- -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-sm">
<div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
<h3 class="font-headline-sm text-headline-sm text-on-surface flex items-center gap-2">
<span class="w-6 h-6 rounded bg-primary/10 text-primary flex items-center justify-center font-bold text-xs">4</span>
<span>Phương pháp đánh giá &amp; Trọng số điểm (Chuẩn HUIT 100%)</span>
</h3>
<span class="text-xs bg-primary/10 text-primary px-2.5 py-0.5 rounded-full font-bold">Thang điểm 10</span>
</div>
<div class="overflow-hidden rounded-lg border border-outline-variant">
<table class="w-full text-left border-collapse text-[13px]">
<thead>
<tr class="bg-slate-50 border-b border-outline-variant text-[12px] font-semibold text-secondary uppercase font-label-md">
<th class="py-3 px-4">Thành phần đánh giá</th>
<th class="py-3 px-4">Hình thức / Công cụ đánh giá</th>
<th class="py-3 px-3 text-center w-28">CLOs đánh giá</th>
<th class="py-3 px-4 text-center w-28">Trọng số</th>
<th class="py-3 px-4 text-right w-36">Tiêu chí đạt</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 font-body-md text-on-surface">
<!-- Thành phần 1 -->
<tr class="hover:bg-slate-50/70 transition-colors">
<td class="py-3 px-4">
<div class="font-semibold">Quá trình học tập &amp; Ý thức học tập</div>
<div class="text-xs text-secondary">Đánh giá quá trình tại lớp</div>
</td>
<td class="py-3 px-4 text-secondary">Điểm quá trình qua Cổng SV, phát biểu xây dựng bài, thảo luận nhóm</td>
<td class="py-3 px-3 text-center">
<span class="px-1.5 py-0.5 bg-slate-100 text-on-surface font-semibold text-xs rounded">CLO4</span>
</td>
<td class="py-3 px-4 text-center font-bold text-primary font-label-md">10%</td>
<td class="py-3 px-4 text-right text-xs text-secondary">Tham gia ≥ 80% số tiết</td>
</tr>
<!-- Thành phần 2 -->
<tr class="hover:bg-slate-50/70 transition-colors">
<td class="py-3 px-4">
<div class="font-semibold">Đánh giá giữa kỳ / Bài tập lớn nhóm</div>
<div class="text-xs text-secondary">Đồ án thực hành theo Sprint</div>
</td>
<td class="py-3 px-4 text-secondary">Báo cáo Sprint 1 &amp; 2, nộp tài liệu SRS + Thiết kế kiến trúc UML, mã nguồn Git</td>
<td class="py-3 px-3 text-center">
<span class="px-1.5 py-0.5 bg-slate-100 text-on-surface font-semibold text-xs rounded">CLO1, CLO2</span>
</td>
<td class="py-3 px-4 text-center font-bold text-primary font-label-md">30%</td>
<td class="py-3 px-4 text-right text-xs text-secondary">Điểm thành phần ≥ 5.0</td>
</tr>
<!-- Thành phần 3 -->
<tr class="hover:bg-slate-50/70 transition-colors">
<td class="py-3 px-4">
<div class="font-semibold">Thi kết thúc học phần</div>
<div class="text-xs text-secondary">Đánh giá chung cuộc</div>
</td>
<td class="py-3 px-4 text-secondary">Báo cáo sản phẩm phần mềm hoàn chỉnh &amp; Vấn đáp trực tiếp trước giảng viên</td>
<td class="py-3 px-3 text-center">
<span class="px-1.5 py-0.5 bg-slate-100 text-on-surface font-semibold text-xs rounded">CLO1 - CLO4</span>
</td>
<td class="py-3 px-4 text-center font-bold text-primary font-label-md">60%</td>
<td class="py-3 px-4 text-right text-xs text-secondary">Điểm thi ≥ 4.0 (chuẩn HUIT)</td>
</tr>
<!-- DÒNG TỔNG KẾT NỔI BẬT -->
<tr class="bg-primary/5 font-semibold font-label-md border-t-2 border-primary/20">
<td class="py-3 px-4 text-primary font-bold" colspan="3">
                      TỔNG CỘNG TRỌNG SỐ ĐÁNH GIÁ HỌC PHẦN
                    </td>
<td class="py-3 px-4 text-center text-primary font-extrabold text-[15px]">
                      100%
                    </td>
<td class="py-3 px-4 text-right text-xs text-primary font-bold">
                      Chuẩn đạt: Điểm HP ≥ 5.0
                    </td>
</tr>
</tbody>
</table>
</div>
</div>
<!-- ----------------------------------------------------------------- -->
<!-- PHẦN 5: GIÁO TRÌNH & TÀI LIỆU HỌC TẬP                             -->
<!-- ----------------------------------------------------------------- -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-sm">
<div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
<h3 class="font-headline-sm text-headline-sm text-on-surface flex items-center gap-2">
<span class="w-6 h-6 rounded bg-primary/10 text-primary flex items-center justify-center font-bold text-xs">5</span>
<span>Giáo trình &amp; Tài liệu học tập (Textbooks &amp; References)</span>
</h3>
<span class="text-secondary font-body-sm text-xs">Thư viện HUIT số hóa</span>
</div>
<div class="space-y-3">
<!-- Giáo trình chính -->
<div class="p-4 rounded-lg border border-primary/30 bg-blue-50/40 flex items-start gap-3">
<div class="w-9 h-9 rounded-lg bg-primary text-white flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined text-[20px]">menu_book</span>
</div>
<div class="flex-1">
<div class="flex items-center gap-2">
<span class="px-2 py-0.5 rounded bg-primary text-white font-label-sm text-[10px] font-bold uppercase tracking-wider">Giáo trình chính</span>
<span class="text-xs text-secondary font-mono">ISBN: 978-0133943030</span>
</div>
<h4 class="font-headline-sm text-[14px] text-on-surface font-bold mt-1">
                    Ian Sommerville (2020), Software Engineering (10th Edition), Pearson Education.
                  </h4>
<p class="font-body-sm text-xs text-secondary mt-1">
                    Vị trí tại Trung tâm Thông tin - Thư viện HUIT: Mã xếp giá <strong class="text-on-surface">005.1 SOM-I</strong> (Sách in &amp; E-book bản quyền).
                  </p>
</div>
</div>
<!-- Tài liệu tham khảo 1 & 2 -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1">
<!-- TLTK 1 -->
<div class="p-3.5 rounded-lg border border-outline-variant bg-surface-container-low/20">
<div class="flex items-center gap-1.5 text-xs text-outline mb-1 font-semibold">
<span class="material-symbols-outlined text-[16px] text-secondary">auto_stories</span>
<span>Tài liệu tham khảo 1</span>
</div>
<div class="font-body-md text-xs font-semibold text-on-surface">
                    Roger S. Pressman, Bruce R. Maxim (2019)
                  </div>
<p class="text-xs text-secondary mt-0.5">
<em>Software Engineering: A Practitioner's Approach (9th Edition)</em>, McGraw-Hill Education.
                  </p>
</div>
<!-- TLTK 2 -->
<div class="p-3.5 rounded-lg border border-outline-variant bg-surface-container-low/20">
<div class="flex items-center gap-1.5 text-xs text-outline mb-1 font-semibold">
<span class="material-symbols-outlined text-[16px] text-secondary">integration_instructions</span>
<span>Tài liệu thực hành nội bộ</span>
</div>
<div class="font-body-md text-xs font-semibold text-on-surface">
                    Bộ môn Công nghệ phần mềm - Khoa CNTT HUIT (2024)
                  </div>
<p class="text-xs text-secondary mt-0.5">
<em>Tập bài giảng và Hướng dẫn thực hành Công nghệ phần mềm trên nền tảng GitHub &amp; Jira</em>, Lưu hành nội bộ ĐH Công Thương.
                  </p>
</div>
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
