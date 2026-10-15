@extends('fe_preview.layouts.lecturer')
@section('page_title', 'Cổng Giảng viên HUIT - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Cổng Giảng viên HUIT - HUIT Học Vụ</title>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/lop-hoc-phan']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">class</span>
<span class="truncate">Lớp học phần phụ trách</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/nhap-diem']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">edit_note</span>
<span class="truncate">Nhập &amp; nộp điểm</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/de-cuong-hoc-phan']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">menu_book</span>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Cổng Giảng viên HUIT</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Cổng Giảng viên HUIT</div>
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
<!-- ================= SECTION: 4 KPI CARDS ================= -->
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
<!-- KPI 1 -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/80 shadow-sm flex flex-col justify-between hover:border-primary/50 transition-colors">
<div class="flex items-start justify-between">
<div>
<p class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider font-semibold">Số lớp phụ trách</p>
<h3 class="font-headline-lg text-headline-lg font-bold text-on-surface mt-1">4 <span class="font-body-md text-body-md font-normal text-on-surface-variant">lớp học phần</span></h3>
</div>
<div class="w-10 h-10 rounded-lg bg-surface-container-low text-primary flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined text-[22px]" data-icon="school">school</span>
</div>
</div>
<div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-green-100 text-green-800 text-xs font-semibold">
              Đang giảng dạy
            </span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Học kỳ 1</span>
</div>
</div>
<!-- KPI 2 -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/80 shadow-sm flex flex-col justify-between hover:border-primary/50 transition-colors">
<div class="flex items-start justify-between">
<div>
<p class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider font-semibold">Tổng sinh viên</p>
<h3 class="font-headline-lg text-headline-lg font-bold text-on-surface mt-1">235 <span class="font-body-md text-body-md font-normal text-on-surface-variant">sinh viên</span></h3>
</div>
<div class="w-10 h-10 rounded-lg bg-surface-container-low text-primary flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined text-[22px]" data-icon="groups">groups</span>
</div>
</div>
<div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
<span class="font-body-sm text-body-sm text-on-surface-variant">Trung bình:</span>
<span class="font-label-md text-label-md font-semibold text-on-surface">58 SV/lớp</span>
</div>
</div>
<!-- KPI 3 -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/80 shadow-sm flex flex-col justify-between hover:border-primary/50 transition-colors">
<div class="flex items-start justify-between">
<div>
<p class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider font-semibold">Tổng giờ quy chuẩn</p>
<h3 class="font-headline-lg text-headline-lg font-bold text-on-surface mt-1">180 <span class="font-body-md text-body-md font-normal text-on-surface-variant">tiết</span></h3>
</div>
<div class="w-10 h-10 rounded-lg bg-surface-container-low text-primary flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined text-[22px]" data-icon="hourglass_bottom">hourglass_bottom</span>
</div>
</div>
<div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
<span class="font-body-sm text-body-sm text-on-surface-variant">Tín chỉ định mức:</span>
<span class="font-label-md text-label-md font-semibold text-primary">14 Tín chỉ</span>
</div>
</div>
<!-- KPI 4 -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/80 shadow-sm flex flex-col justify-between hover:border-primary/50 transition-colors">
<div class="flex items-start justify-between">
<div>
<p class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider font-semibold">Tiến độ học kỳ</p>
<h3 class="font-headline-lg text-headline-lg font-bold text-on-surface mt-1">Tuần 08 <span class="font-body-md text-body-md font-normal text-on-surface-variant">/ 15 tuần</span></h3>
</div>
<div class="w-10 h-10 rounded-lg bg-surface-container-low text-primary flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined text-[22px]" data-icon="timelapse">timelapse</span>
</div>
</div>
<div class="mt-4 pt-3 border-t border-slate-100">
<div class="flex items-center justify-between text-xs mb-1">
<span class="text-on-surface-variant">Tiến độ giảng dạy</span>
<span class="font-semibold text-primary">53.3%</span>
</div>
<div class="w-full bg-surface-container-high h-2 rounded-full overflow-hidden">
<div class="bg-primary h-full rounded-full" style="width: 53.3%;"></div>
</div>
</div>
</div>
</section>
<!-- ================= SECTION 1: LƯỚI THẺ LỚP HỌC PHẦN ĐANG DẠY ================= -->
<section class="space-y-3">
<div class="flex items-center justify-between">
<div>
<h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">Lớp học phần đang phụ trách</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Chọn một lớp học phần để xem danh sách sinh viên, điểm quá trình và quản lý học vụ</p>
</div>
<span class="font-label-md text-label-md text-on-surface-variant bg-surface-container-low px-3 py-1 rounded-full border border-outline-variant/60">
            4 Lớp trong học kỳ
          </span>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
<!-- CARD 1 (ACTIVE / ĐANG XEM CHI TIẾT) -->
<div class="bg-surface-container-lowest rounded-xl p-5 border-2 border-primary shadow-md relative flex flex-col justify-between ring-2 ring-primary/10">
<div>
<div class="flex items-center justify-between mb-2">
<span class="font-label-sm text-label-sm font-bold text-primary tracking-wide">241_71ITEC3201_01</span>
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-primary text-white font-label-sm text-label-sm font-semibold shadow-xs">
<span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                  Đang xem chi tiết
                </span>
</div>
<h3 class="font-headline-sm text-headline-sm font-bold text-on-surface mb-1">Lập trình Web</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant mb-3 font-medium">3 Tín chỉ • 45 Tiết lý thuyết &amp; thực hành</p>
<div class="space-y-2 py-2 border-t border-b border-slate-100 font-body-sm text-body-sm">
<div class="flex items-center gap-2 text-on-surface">
<span class="material-symbols-outlined text-[18px] text-primary" data-icon="schedule">schedule</span>
<span>Thứ 2, Tiết 1 - 3 (07:00 - 09:30)</span>
</div>
<div class="flex items-center gap-2 text-on-surface">
<span class="material-symbols-outlined text-[18px] text-primary" data-icon="meeting_room">meeting_room</span>
<span>Phòng A2.301 (Cơ sở Tân Phú)</span>
</div>
<div class="flex items-center justify-between pt-1">
<span class="text-on-surface-variant">Sĩ số: <strong class="text-on-surface">60 / 60 SV</strong> (100%)</span>
<span class="text-xs bg-slate-100 px-2 py-0.5 rounded font-medium text-slate-700">12DHTH01, 12DHTH02</span>
</div>
</div>
<!-- Trạng thái nộp điểm -->
<div class="mt-3">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-amber-50 text-amber-800 border border-amber-200 text-xs font-semibold">
<span class="material-symbols-outlined text-[14px] text-amber-700" data-icon="warning">warning</span>
                  Chưa nộp điểm - Hạn: 30/11
                </span>
</div>
</div>
<!-- Tác vụ nhanh -->
<div class="mt-4 pt-3 flex items-center gap-2 border-t border-slate-100">
<button class="flex-1 py-1.5 px-2 text-center rounded-lg bg-primary text-white font-label-md text-label-md font-semibold hover:bg-primary-container transition-colors shadow-sm">
                Xem danh sách SV
              </button>
<button class="py-1.5 px-2 rounded-lg border border-outline-variant hover:bg-slate-50 text-on-surface font-label-md text-label-md transition-colors" title="Nhập điểm học phần">
                Nhập điểm
              </button>
<button class="p-1.5 rounded-lg border border-outline-variant hover:bg-slate-50 text-on-surface-variant hover:text-on-surface transition-colors" title="Gửi thông báo đến lớp">
<span class="material-symbols-outlined text-[18px]" data-icon="mail">mail</span>
</button>
</div>
</div>
<!-- CARD 2 -->
<div class="bg-surface-container-lowest rounded-xl p-5 border border-outline-variant/80 shadow-sm hover:border-primary/50 transition-colors flex flex-col justify-between">
<div>
<div class="flex items-center justify-between mb-2">
<span class="font-label-sm text-label-sm font-bold text-secondary tracking-wide">241_71ITEC2104_02</span>
<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-label-sm text-label-sm">
                  Lớp chính quy
                </span>
</div>
<h3 class="font-headline-sm text-headline-sm font-bold text-on-surface mb-1">Cấu trúc dữ liệu &amp; Giải thuật</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant mb-3 font-medium">4 Tín chỉ • 60 Tiết</p>
<div class="space-y-2 py-2 border-t border-b border-slate-100 font-body-sm text-body-sm">
<div class="flex items-center gap-2 text-on-surface">
<span class="material-symbols-outlined text-[18px] text-secondary" data-icon="schedule">schedule</span>
<span>Thứ 3, Tiết 4 - 6 (09:45 - 12:15)</span>
</div>
<div class="flex items-center gap-2 text-on-surface">
<span class="material-symbols-outlined text-[18px] text-secondary" data-icon="meeting_room">meeting_room</span>
<span>Phòng B.204 (Cơ sở Tân Phú)</span>
</div>
<div class="flex items-center justify-between pt-1">
<span class="text-on-surface-variant">Sĩ số: <strong class="text-on-surface">58 / 60 SV</strong> (96%)</span>
<span class="text-xs bg-slate-100 px-2 py-0.5 rounded font-medium text-slate-700">12DHTH03</span>
</div>
</div>
<!-- Trạng thái nộp điểm -->
<div class="mt-3">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-green-50 text-green-800 border border-green-200 text-xs font-semibold">
<span class="material-symbols-outlined text-[14px] text-green-600" data-icon="check_circle">check_circle</span>
                  Đã nhập điểm quá trình
                </span>
</div>
</div>
<!-- Tác vụ nhanh -->
<div class="mt-4 pt-3 flex items-center gap-2 border-t border-slate-100">
<button class="flex-1 py-1.5 px-2 text-center rounded-lg border border-outline-variant hover:border-primary hover:text-primary font-label-md text-label-md font-semibold text-on-surface transition-colors">
                Xem chi tiết lớp
              </button>
<button class="py-1.5 px-2 rounded-lg border border-outline-variant hover:bg-slate-50 text-on-surface font-label-md text-label-md transition-colors">
                Nhập điểm
              </button>
<button class="p-1.5 rounded-lg border border-outline-variant hover:bg-slate-50 text-on-surface-variant hover:text-on-surface transition-colors" title="Gửi thông báo">
<span class="material-symbols-outlined text-[18px]" data-icon="mail">mail</span>
</button>
</div>
</div>
<!-- CARD 3 -->
<div class="bg-surface-container-lowest rounded-xl p-5 border border-outline-variant/80 shadow-sm hover:border-primary/50 transition-colors flex flex-col justify-between">
<div>
<div class="flex items-center justify-between mb-2">
<span class="font-label-sm text-label-sm font-bold text-secondary tracking-wide">241_71ITEC3208_01</span>
<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-label-sm text-label-sm">
                  Lớp chính quy
                </span>
</div>
<h3 class="font-headline-sm text-headline-sm font-bold text-on-surface mb-1">Mạng máy tính</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant mb-3 font-medium">3 Tín chỉ • 45 Tiết</p>
<div class="space-y-2 py-2 border-t border-b border-slate-100 font-body-sm text-body-sm">
<div class="flex items-center gap-2 text-on-surface">
<span class="material-symbols-outlined text-[18px] text-secondary" data-icon="schedule">schedule</span>
<span>Thứ 6, Tiết 1 - 3 (07:00 - 09:30)</span>
</div>
<div class="flex items-center gap-2 text-on-surface">
<span class="material-symbols-outlined text-[18px] text-secondary" data-icon="meeting_room">meeting_room</span>
<span>Phòng B.105 (Cơ sở Tân Phú)</span>
</div>
<div class="flex items-center justify-between pt-1">
<span class="text-on-surface-variant">Sĩ số: <strong class="text-on-surface">55 / 60 SV</strong> (91%)</span>
<span class="text-xs bg-slate-100 px-2 py-0.5 rounded font-medium text-slate-700">12DHMM01</span>
</div>
</div>
<!-- Trạng thái nộp điểm -->
<div class="mt-3">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-amber-50 text-amber-800 border border-amber-200 text-xs font-semibold">
<span class="material-symbols-outlined text-[14px] text-amber-700" data-icon="warning">warning</span>
                  Chưa nộp điểm - Hạn: 30/11
                </span>
</div>
</div>
<!-- Tác vụ nhanh -->
<div class="mt-4 pt-3 flex items-center gap-2 border-t border-slate-100">
<button class="flex-1 py-1.5 px-2 text-center rounded-lg border border-outline-variant hover:border-primary hover:text-primary font-label-md text-label-md font-semibold text-on-surface transition-colors">
                Xem chi tiết lớp
              </button>
<button class="py-1.5 px-2 rounded-lg border border-outline-variant hover:bg-slate-50 text-on-surface font-label-md text-label-md transition-colors">
                Nhập điểm
              </button>
<button class="p-1.5 rounded-lg border border-outline-variant hover:bg-slate-50 text-on-surface-variant hover:text-on-surface transition-colors" title="Gửi thông báo">
<span class="material-symbols-outlined text-[18px]" data-icon="mail">mail</span>
</button>
</div>
</div>
<!-- CARD 4 -->
<div class="bg-surface-container-lowest rounded-xl p-5 border border-outline-variant/80 shadow-sm hover:border-primary/50 transition-colors flex flex-col justify-between">
<div>
<div class="flex items-center justify-between mb-2">
<span class="font-label-sm text-label-sm font-bold text-secondary tracking-wide">241_71ITEC4105_03</span>
<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 font-label-sm text-label-sm font-semibold">
                  Khóa luận TN
                </span>
</div>
<h3 class="font-headline-sm text-headline-sm font-bold text-on-surface mb-1">Khóa luận tốt nghiệp</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant mb-3 font-medium">4 Tín chỉ • Đồ án tốt nghiệp</p>
<div class="space-y-2 py-2 border-t border-b border-slate-100 font-body-sm text-body-sm">
<div class="flex items-center gap-2 text-on-surface">
<span class="material-symbols-outlined text-[18px] text-secondary" data-icon="schedule">schedule</span>
<span>Lịch họp theo phân công GV</span>
</div>
<div class="flex items-center gap-2 text-on-surface">
<span class="material-symbols-outlined text-[18px] text-secondary" data-icon="meeting_room">meeting_room</span>
<span>Phòng Lab PM04</span>
</div>
<div class="flex items-center justify-between pt-1">
<span class="text-on-surface-variant">Sĩ số hướng dẫn: <strong class="text-on-surface">12 SV</strong></span>
<span class="text-xs bg-slate-100 px-2 py-0.5 rounded font-medium text-slate-700">Khóa 11</span>
</div>
</div>
<!-- Trạng thái nộp điểm -->
<div class="mt-3">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-blue-50 text-blue-800 border border-blue-200 text-xs font-semibold">
<span class="material-symbols-outlined text-[14px] text-blue-600" data-icon="history_edu">history_edu</span>
                  Đang chấm tiến độ đợt 2
                </span>
</div>
</div>
<!-- Tác vụ nhanh -->
<div class="mt-4 pt-3 flex items-center gap-2 border-t border-slate-100">
<button class="flex-1 py-1.5 px-2 text-center rounded-lg border border-outline-variant hover:border-primary hover:text-primary font-label-md text-label-md font-semibold text-on-surface transition-colors">
                Xem chi tiết đề tài
              </button>
<button class="py-1.5 px-2 rounded-lg border border-outline-variant hover:bg-slate-50 text-on-surface font-label-md text-label-md transition-colors">
                Nhập điểm
              </button>
<button class="p-1.5 rounded-lg border border-outline-variant hover:bg-slate-50 text-on-surface-variant hover:text-on-surface transition-colors" title="Gửi thông báo">
<span class="material-symbols-outlined text-[18px]" data-icon="mail">mail</span>
</button>
</div>
</div>
</div>
</section>
<!-- ================= SECTION 2: CHI TIẾT LỚP HỌC PHẦN & DANH SÁCH SINH VIÊN ================= -->
<section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
<!-- Panel Header -->
<div class="p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-lg bg-surface-container-low text-primary flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined text-[24px]" data-icon="format_list_bulleted">format_list_bulleted</span>
</div>
<div>
<div class="flex items-center gap-2">
<h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">
                  Danh sách sinh viên lớp: 241_71ITEC3201_01 - Lập trình Web
                </h2>
<span class="px-2 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed text-xs font-semibold">Sĩ số: 60 SV</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant">Thứ 2 (Tiết 1-3) • Phòng A2.301 • Giảng viên phụ trách: TS. Trần Hoàng Nam</p>
</div>
</div>
<!-- Quick Action Buttons -->
<div class="flex items-center gap-2 flex-wrap">
<button class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-outline-variant text-on-surface font-label-md text-label-md hover:bg-slate-50 transition-colors">
<span class="material-symbols-outlined text-[18px]" data-icon="print">print</span>
<span>In danh sách lớp (A4)</span>
</button>
<button class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-primary text-white font-label-md text-label-md font-semibold hover:bg-primary-container transition-colors shadow-sm">
<span class="material-symbols-outlined text-[18px]" data-icon="download">download</span>
<span>Xuất Excel (.xlsx)</span>
</button>
</div>
</div>
<!-- Filter & Search Toolbar -->
<div class="p-4 bg-surface-container-low/50 border-b border-slate-100 grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
<!-- Search Bar -->
<div class="md:col-span-5 relative">
<span class="material-symbols-outlined absolute left-3 top-2.5 text-on-surface-variant text-[20px]" data-icon="search">search</span>
<input aria-label="Tìm kiếm theo MSSV, Họ và tên sinh viên..." class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface font-body-md text-body-md placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary" placeholder="Tìm kiếm theo MSSV, Họ và tên sinh viên..." type="text"/>
</div>
<!-- Lớp hành chính Filter -->
<div class="md:col-span-3">
<div class="relative">
<select aria-label="Lớp hành chính: Tất cả" class="w-full px-3 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:border-primary">
<option value="">Lớp hành chính: Tất cả</option>
<option value="12DHTH01">12DHTH01 (32 sinh viên)</option>
<option value="12DHTH02">12DHTH02 (28 sinh viên)</option>
</select>
</div>
</div>
<!-- Attendance / Status Filter -->
<div class="md:col-span-3">
<div class="relative">
<select aria-label="Trạng thái điểm quá trình: Tất cả" class="w-full px-3 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:border-primary">
<option value="">Trạng thái điểm quá trình: Tất cả</option>
<option value="normal">Bình thường (Đủ điều kiện dự thi)</option>
<option value="warning">Cảnh báo vắng (&gt; 20% số buổi)</option>
</select>
</div>
</div>
<!-- Refresh / Reset -->
<div class="md:col-span-1 flex justify-end">
<button class="p-2 border border-outline-variant rounded-lg bg-surface-container-lowest hover:bg-slate-50 text-on-surface-variant hover:text-on-surface" title="Đặt lại bộ lọc">
<span class="material-symbols-outlined text-[20px]" data-icon="refresh">refresh</span>
</button>
</div>
</div>
<!-- Academic Roster Data Table -->
<div class="overflow-x-auto custom-scrollbar">
<table class="w-full text-left border-collapse min-w-[1100px]">
<thead>
<tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-label-md text-label-md uppercase tracking-wider">
<th class="py-3 px-4 w-14 text-center sticky left-0 bg-slate-50 z-10 border-r border-slate-200">STT</th>
<th class="py-3 px-4 w-32 sticky left-14 bg-slate-50 z-10 border-r border-slate-200">MSSV</th>
<th class="py-3 px-4 min-w-[220px]">Họ và tên sinh viên</th>
<th class="py-3 px-4 w-28">Lớp HC</th>
<th class="py-3 px-4 w-28">Ngày sinh</th>
<th class="py-3 px-4 min-w-[200px]">Email HUIT</th>
<th class="py-3 px-4 w-28 text-center">Số buổi vắng</th>
<th class="py-3 px-4 w-44">Trạng thái học vụ</th>
<th class="py-3 px-4 w-44 text-right">Thao tác</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 font-body-md text-body-md text-on-surface">
<!-- Row 1 -->
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center font-medium sticky left-0 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">1</td>
<td class="py-3 px-4 font-mono font-semibold text-primary sticky left-14 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">2001210123</td>
<td class="py-3 px-4">
<div class="flex items-center gap-3">
<img alt="" class="w-8 h-8 rounded-full object-cover ring-1 ring-slate-200" data-alt="Chân dung thẻ sinh viên nam thanh niên Việt Nam mỉm cười nghiêm túc, phong cách ảnh hồ sơ giáo dục đại học, áo sơ mi trắng, phông nền xanh nhạt hiện đại trường Đại học HUIT." src="https://lh3.googleusercontent.com/aida-public/AB6AXuDlX_Llk9V_SbXl0azq4_fs6jBfn9YWE7euBWyAfpkWL0VIaJ2w3pLGkfQ5_6NTDE8pmNn4kbz179P3VfQhhiW_IIVUSfW-z-G4ObllQGxUev_Sq0hhm84_PWHTji0sV2wGslLQmRDr2JGS34tpK4rSp04VTuEEfkGxj3iMUl8Cf_SOGlgQuz3CHiEPxZSU39N3ylZSeNgWpPCGvCPMpJh-ehavPl7zMH_-Je9Kybzx1GDpvzQvdcvD_cqajt9Kdj8rEXQ4UmpeePM"/>
<div>
<span class="font-semibold block">Nguyễn Văn An</span>
<span class="text-xs text-on-surface-variant">Nhóm 01 (Thực hành)</span>
</div>
</div>
</td>
<td class="py-3 px-4 font-medium">12DHTH01</td>
<td class="py-3 px-4 text-on-surface-variant">14/05/2003</td>
<td class="py-3 px-4 text-on-surface-variant font-mono text-xs">2001210123@huit.edu.vn</td>
<td class="py-3 px-4 text-center font-semibold text-slate-700">0 / 15</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-label-sm text-label-sm font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Bình thường
                  </span>
</td>
<td class="py-3 px-4 text-right">
<div class="inline-flex items-center gap-1">
<button class="px-2.5 py-1 rounded border border-outline-variant hover:bg-primary hover:text-white hover:border-primary text-xs font-medium transition-colors" title="Điểm quá trình buổi hôm nay">Điểm quá trình</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Ghi chú học vụ">
<span class="material-symbols-outlined text-[18px]" data-icon="edit_note">edit_note</span>
</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Xem hồ sơ">
<span class="material-symbols-outlined text-[18px]" data-icon="visibility">visibility</span>
</button>
</div>
</td>
</tr>
<!-- Row 2 -->
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center font-medium sticky left-0 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">2</td>
<td class="py-3 px-4 font-mono font-semibold text-primary sticky left-14 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">2001210045</td>
<td class="py-3 px-4">
<div class="flex items-center gap-3">
<img alt="" class="w-8 h-8 rounded-full object-cover ring-1 ring-slate-200" data-alt="Chân dung thẻ sinh viên nữ Việt Nam tóc dài gọn gàng, đeo kính cận, nụ cười thanh lịch tự tin, áo đồng phục đại học, phong cách chuẩn mực ảnh hồ sơ trường HUIT." src="https://lh3.googleusercontent.com/aida-public/AB6AXuAdcF8aZGCsvGOnekCbE6cDneGDF2FfrYfYQXlDRjVbElOu81clJX0sOX86JweAkvJN6e2s9GI9qs-Ilcv2VbGLk4HA-dHB2VDbzSGWwY36a1zVfunYyATPZaiovuBeJY1CZ1Zmn9KE8aT5jMP7cKFR20tu3evTDpa0RIrRSiF7siwO9fPR-0efp1f9g8haqO-gnVbL2xwfAR4QpZ5jrGCqJ9mtAQM__m4T1J1IW5BMikBMuAJ1NwuFSx4VFnRA8zoVTJiJg3cQqAI"/>
<div>
<span class="font-semibold block">Trần Thị Bích</span>
<span class="text-xs text-on-surface-variant">Nhóm 01 (Thực hành)</span>
</div>
</div>
</td>
<td class="py-3 px-4 font-medium">12DHTH01</td>
<td class="py-3 px-4 text-on-surface-variant">22/08/2003</td>
<td class="py-3 px-4 text-on-surface-variant font-mono text-xs">2001210045@huit.edu.vn</td>
<td class="py-3 px-4 text-center font-semibold text-slate-700">1 / 15</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-label-sm text-label-sm font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Bình thường
                  </span>
</td>
<td class="py-3 px-4 text-right">
<div class="inline-flex items-center gap-1">
<button class="px-2.5 py-1 rounded border border-outline-variant hover:bg-primary hover:text-white hover:border-primary text-xs font-medium transition-colors" title="Điểm quá trình buổi hôm nay">Điểm quá trình</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Ghi chú học vụ">
<span class="material-symbols-outlined text-[18px]" data-icon="edit_note">edit_note</span>
</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Xem hồ sơ">
<span class="material-symbols-outlined text-[18px]" data-icon="visibility">visibility</span>
</button>
</div>
</td>
</tr>
<!-- Row 3 (Cảnh báo vắng) -->
<tr class="hover:bg-slate-50/80 bg-red-50/30 transition-colors">
<td class="py-3 px-4 text-center font-medium sticky left-0 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">3</td>
<td class="py-3 px-4 font-mono font-semibold text-primary sticky left-14 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">2001210189</td>
<td class="py-3 px-4">
<div class="flex items-center gap-3">
<img alt="" class="w-8 h-8 rounded-full object-cover ring-1 ring-slate-200" data-alt="Chân dung thẻ sinh viên nam thanh niên Việt Nam hiện đại, thần thái tập trung, góc chụp thẳng trang trọng, phông nền trường học tông xanh dương công nghệ cao." src="https://lh3.googleusercontent.com/aida-public/AB6AXuBucS_M8gwoH4-6q4irZH5cMG--lImWzkFH-klpjcqDBa9kKPApKgccr0Qrqv5sAiK9olU-C-fttk5wEAsC8YxxuIAxuytVKDjMfA7_j_6Tspo8qwHK3G5a6erlxF7tl-5pIpvDkVVj9m2nty9_UzGnr_GWKMArQDVUqV6E5lC13hs-yPhhBrvM_kSrg7D7SFmuz35t2mL_rlWSu0U215UOUXH7_eDEtUvCLc5Cfc6IBAEq2Nm8GddZ4csiwpL2y5b9Sj8hAo9blYs"/>
<div>
<span class="font-semibold block text-red-950">Lê Hoàng Cường</span>
<span class="text-xs text-red-700 font-medium">Vắng 3 buổi liên tiếp</span>
</div>
</div>
</td>
<td class="py-3 px-4 font-medium">12DHTH02</td>
<td class="py-3 px-4 text-on-surface-variant">05/11/2003</td>
<td class="py-3 px-4 text-on-surface-variant font-mono text-xs">2001210189@huit.edu.vn</td>
<td class="py-3 px-4 text-center font-bold text-red-700">3 / 15</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 border border-rose-300 font-label-sm text-label-sm font-bold">
<span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                    Cảnh báo vắng &gt; 20%
                  </span>
</td>
<td class="py-3 px-4 text-right">
<div class="inline-flex items-center gap-1">
<button class="px-2.5 py-1 rounded border border-rose-300 text-rose-700 hover:bg-rose-600 hover:text-white text-xs font-semibold transition-colors" title="Điểm quá trình buổi hôm nay">Điểm quá trình</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Ghi chú học vụ">
<span class="material-symbols-outlined text-[18px]" data-icon="edit_note">edit_note</span>
</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Xem hồ sơ">
<span class="material-symbols-outlined text-[18px]" data-icon="visibility">visibility</span>
</button>
</div>
</td>
</tr>
<!-- Row 4 -->
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center font-medium sticky left-0 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">4</td>
<td class="py-3 px-4 font-mono font-semibold text-primary sticky left-14 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">2001210012</td>
<td class="py-3 px-4">
<div class="flex items-center gap-3">
<img alt="" class="w-8 h-8 rounded-full object-cover ring-1 ring-slate-200" data-alt="Chân dung sinh viên nữ Việt Nam tóc ngắn cá tính, mỉm cười nhẹ nhàng tự tin, ánh sáng studio trắng mềm, đồng phục học viện công nghệ HUIT chuyên nghiệp." src="https://lh3.googleusercontent.com/aida-public/AB6AXuA1a--_nN7xz2L-WRNUqUd_09u348gjJm63VDQAnkkRfteOp06kB7IqsJtiPDBtcaRkun0PwSfS8HaOSeWqYQahjQZ5T61e8M1cnQ_wy3gOvjCV0rNhh85rlLZ1mNXl_lsOpB1cQEkvadmf7wd6zSgKMcgPl1Z5JPBwGhdwiqPkpAX4f5nh70JVzMMF1yXwwte2oUJzIBDC-1A-xWoN8Tk3KVmQ4cirpB8U-rDBwUlOxZ_OOr1BNYyVLZvoh5DM0OsOd1EeLz8UbL4"/>
<div>
<span class="font-semibold block">Phạm Thị Dung</span>
<span class="text-xs text-on-surface-variant">Lớp trưởng 12DHTH01</span>
</div>
</div>
</td>
<td class="py-3 px-4 font-medium">12DHTH01</td>
<td class="py-3 px-4 text-on-surface-variant">19/02/2003</td>
<td class="py-3 px-4 text-on-surface-variant font-mono text-xs">2001210012@huit.edu.vn</td>
<td class="py-3 px-4 text-center font-semibold text-slate-700">0 / 15</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-label-sm text-label-sm font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Bình thường
                  </span>
</td>
<td class="py-3 px-4 text-right">
<div class="inline-flex items-center gap-1">
<button class="px-2.5 py-1 rounded border border-outline-variant hover:bg-primary hover:text-white hover:border-primary text-xs font-medium transition-colors">Điểm quá trình</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Ghi chú học vụ">
<span class="material-symbols-outlined text-[18px]" data-icon="edit_note">edit_note</span>
</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Xem hồ sơ">
<span class="material-symbols-outlined text-[18px]" data-icon="visibility">visibility</span>
</button>
</div>
</td>
</tr>
<!-- Row 5 -->
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center font-medium sticky left-0 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">5</td>
<td class="py-3 px-4 font-mono font-semibold text-primary sticky left-14 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">2001210356</td>
<td class="py-3 px-4">
<div class="flex items-center gap-3">
<img alt="" class="w-8 h-8 rounded-full object-cover ring-1 ring-slate-200" data-alt="Chân dung thẻ sinh viên nam người Việt Nam thông minh, kính cận tròn, áo sơ mi cởi cúc trên, góc chụp chuyên nghiệp hiện đại ánh sáng nhẹ." src="https://lh3.googleusercontent.com/aida-public/AB6AXuAbMzJ_mZWlKgNbLMHAuOQoQY3yueIHVgcFrZFY4YTGPUSOup1aiAxNKZjtyiANxyQrrNMaoHmWdKSrjZ1UvMmZaBCV04HXIThks0FRurIjd5w2FF1uHFN0O1JL18TE7lAcO6KMGyScS5_hac_PP-MWSgyiRg9A-PYTaeZiKSFXMgqvmo-Ep7F0xPwj-1WtsozCLzCIiksB9A5gTGSP8y7aUl0R2LRKWGc225qI7Yqwz36gJK4WKtZZtkvdRaD3xuVWoZM0aAZ9k0w"/>
<div>
<span class="font-semibold block">Đỗ Quốc Dũng</span>
<span class="text-xs text-on-surface-variant">Nhóm 02 (Thực hành)</span>
</div>
</div>
</td>
<td class="py-3 px-4 font-medium">12DHTH02</td>
<td class="py-3 px-4 text-on-surface-variant">30/09/2003</td>
<td class="py-3 px-4 text-on-surface-variant font-mono text-xs">2001210356@huit.edu.vn</td>
<td class="py-3 px-4 text-center font-semibold text-slate-700">1 / 15</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-label-sm text-label-sm font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Bình thường
                  </span>
</td>
<td class="py-3 px-4 text-right">
<div class="inline-flex items-center gap-1">
<button class="px-2.5 py-1 rounded border border-outline-variant hover:bg-primary hover:text-white hover:border-primary text-xs font-medium transition-colors">Điểm quá trình</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Ghi chú học vụ">
<span class="material-symbols-outlined text-[18px]" data-icon="edit_note">edit_note</span>
</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Xem hồ sơ">
<span class="material-symbols-outlined text-[18px]" data-icon="visibility">visibility</span>
</button>
</div>
</td>
</tr>
<!-- Row 6 -->
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center font-medium sticky left-0 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">6</td>
<td class="py-3 px-4 font-mono font-semibold text-primary sticky left-14 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">2001210411</td>
<td class="py-3 px-4">
<div class="flex items-center gap-3">
<img alt="" class="w-8 h-8 rounded-full object-cover ring-1 ring-slate-200" data-alt="Chân dung thẻ sinh viên nữ sinh Việt Nam duyên dáng, ánh mắt tự tin, tóc cột cao thanh lịch, phông nền xanh dương đặc trưng của đại học." src="https://lh3.googleusercontent.com/aida-public/AB6AXuBpritCM7Jy6FjrTqgg3qGCslvfw8LIRbMq7PyuQCwSn4Yyp5OKq-LQK0Rw_9EHPTyfnO8LlDWa-xCKC4PCKj2jeDIOf1GiVqlKKCtMVpunz4IEROJuzkdQbBn-75Fhn9_weBI-VHAIdRg-UGrggOP7TH47FLB3r-DpMWtfkgHz-y-PqezHHqvBoU1O6WJ_PLMl0W1rF52rfguOW77HcrToBInt4R5HDrFpNSGV9Sriy2OXCxtfBLEs8gdF_nw3dEBrd5pnGzCgVQA"/>
<div>
<span class="font-semibold block">Vũ Mai Hương</span>
<span class="text-xs text-on-surface-variant">Nhóm 02 (Thực hành)</span>
</div>
</div>
</td>
<td class="py-3 px-4 font-medium">12DHTH01</td>
<td class="py-3 px-4 text-on-surface-variant">11/04/2003</td>
<td class="py-3 px-4 text-on-surface-variant font-mono text-xs">2001210411@huit.edu.vn</td>
<td class="py-3 px-4 text-center font-semibold text-slate-700">0 / 15</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-label-sm text-label-sm font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Bình thường
                  </span>
</td>
<td class="py-3 px-4 text-right">
<div class="inline-flex items-center gap-1">
<button class="px-2.5 py-1 rounded border border-outline-variant hover:bg-primary hover:text-white hover:border-primary text-xs font-medium transition-colors">Điểm quá trình</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Ghi chú học vụ">
<span class="material-symbols-outlined text-[18px]" data-icon="edit_note">edit_note</span>
</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Xem hồ sơ">
<span class="material-symbols-outlined text-[18px]" data-icon="visibility">visibility</span>
</button>
</div>
</td>
</tr>
<!-- Row 7 -->
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center font-medium sticky left-0 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">7</td>
<td class="py-3 px-4 font-mono font-semibold text-primary sticky left-14 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">2001210098</td>
<td class="py-3 px-4">
<div class="flex items-center gap-3">
<img alt="" class="w-8 h-8 rounded-full object-cover ring-1 ring-slate-200" data-alt="Chân dung thẻ sinh viên nam sinh viên ngành phần mềm Việt Nam trẻ trung, biểu cảm tập trung, nền studio xanh ghi thanh lịch chất lượng cao." src="https://lh3.googleusercontent.com/aida-public/AB6AXuBbfIL59yakz06R_ZnUYoITFv1jPNzbXSWwNKoz1U86DUpAgGTV5y5eErV5qAjIwgYUUJW2kJLm2LYfx50LRZfhtEIorET6KWQopLmn4QdsGMKkX6TSUDYjv9X0IXJW_58VbwHDV-hBQ7N-fsr4BH_JYlJVp7QlDSfbE5pAEPr-bICARmkkKU1KE8tDuDmE0zPvWYxNZRhPn4yVju0Nltj7AMtc-w4nuHAT4SY-WwsfmBvWXb4dkC_8Nx2RoFMNLj5uiIGa6z2XDC4"/>
<div>
<span class="font-semibold block">Hoàng Minh Khôi</span>
<span class="text-xs text-on-surface-variant">Nhóm 02 (Thực hành)</span>
</div>
</div>
</td>
<td class="py-3 px-4 font-medium">12DHTH02</td>
<td class="py-3 px-4 text-on-surface-variant">17/12/2003</td>
<td class="py-3 px-4 text-on-surface-variant font-mono text-xs">2001210098@huit.edu.vn</td>
<td class="py-3 px-4 text-center font-semibold text-slate-700">2 / 15</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 font-label-sm text-label-sm font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    Cần lưu ý
                  </span>
</td>
<td class="py-3 px-4 text-right">
<div class="inline-flex items-center gap-1">
<button class="px-2.5 py-1 rounded border border-outline-variant hover:bg-primary hover:text-white hover:border-primary text-xs font-medium transition-colors">Điểm quá trình</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Ghi chú học vụ">
<span class="material-symbols-outlined text-[18px]" data-icon="edit_note">edit_note</span>
</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Xem hồ sơ">
<span class="material-symbols-outlined text-[18px]" data-icon="visibility">visibility</span>
</button>
</div>
</td>
</tr>
<!-- Row 8 -->
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center font-medium sticky left-0 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">8</td>
<td class="py-3 px-4 font-mono font-semibold text-primary sticky left-14 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">2001210512</td>
<td class="py-3 px-4">
<div class="flex items-center gap-3">
<img alt="" class="w-8 h-8 rounded-full object-cover ring-1 ring-slate-200" data-alt="Chân dung thẻ sinh viên nam thanh niên Việt Nam nụ cười tươi tắn, đồng phục áo polo xanh thêu logo trường HUIT, ánh sáng cân đối tự nhiên." src="https://lh3.googleusercontent.com/aida-public/AB6AXuAhtEaKXJd8jRv43CwqwahDfrYa5nUd_MewZMOflKKJdhDsovAM_W-AZR9aiti2Hz9w1Ee6JycDbsUVCoBvA_qcwf5qi_-v9EU7M26Yifj4aikAV6s7gWeDDFNF-nMScvuOAlVWvD-vcDFDb2bqzzJlAZ0uD_bW6KWugBldn04IAZ_zqRuSGu0lHVQEMgzjkmvZvdoSvJGTDkAffEmIS6MqC3fqXKAlaucoGjbYuVFwzlAWiEaHzy_6WxtdIUZYj-nn3wRlV7iYvfY"/>
<div>
<span class="font-semibold block">Lý Thành Long</span>
<span class="text-xs text-on-surface-variant">Nhóm 01 (Thực hành)</span>
</div>
</div>
</td>
<td class="py-3 px-4 font-medium">12DHTH01</td>
<td class="py-3 px-4 text-on-surface-variant">08/07/2003</td>
<td class="py-3 px-4 text-on-surface-variant font-mono text-xs">2001210512@huit.edu.vn</td>
<td class="py-3 px-4 text-center font-semibold text-slate-700">0 / 15</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-label-sm text-label-sm font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Bình thường
                  </span>
</td>
<td class="py-3 px-4 text-right">
<div class="inline-flex items-center gap-1">
<button class="px-2.5 py-1 rounded border border-outline-variant hover:bg-primary hover:text-white hover:border-primary text-xs font-medium transition-colors">Điểm quá trình</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Ghi chú học vụ">
<span class="material-symbols-outlined text-[18px]" data-icon="edit_note">edit_note</span>
</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Xem hồ sơ">
<span class="material-symbols-outlined text-[18px]" data-icon="visibility">visibility</span>
</button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<!-- Pagination Controls -->
<div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-on-surface-variant font-body-sm text-body-sm bg-surface-container-lowest">
<div class="flex items-center gap-2">
<span>Hiển thị</span>
<select aria-label="8 sinh viên" class="px-2 py-1 bg-surface-container-low border border-outline-variant rounded font-medium text-on-surface text-xs focus:outline-none">
<option value="8">8 sinh viên</option>
<option value="15">15 sinh viên</option>
<option value="30">30 sinh viên</option>
<option value="60">Tất cả 60 sinh viên</option>
</select>
<span>trong tổng số <strong>60</strong> sinh viên của lớp học phần</span>
</div>
<div class="flex items-center gap-1">
<button class="p-1.5 rounded border border-outline-variant hover:bg-slate-50 disabled:opacity-40" disabled="">
<span class="material-symbols-outlined text-[18px]" data-icon="first_page">first_page</span>
</button>
<button class="p-1.5 rounded border border-outline-variant hover:bg-slate-50 disabled:opacity-40" disabled="">
<span class="material-symbols-outlined text-[18px]" data-icon="chevron_left">chevron_left</span>
</button>
<button class="w-8 h-8 rounded bg-primary text-white font-semibold text-xs shadow-xs">1</button>
<button class="w-8 h-8 rounded border border-outline-variant hover:bg-slate-50 font-medium text-xs text-on-surface">2</button>
<button class="w-8 h-8 rounded border border-outline-variant hover:bg-slate-50 font-medium text-xs text-on-surface">3</button>
<span class="px-1 text-slate-500">...</span>
<button class="w-8 h-8 rounded border border-outline-variant hover:bg-slate-50 font-medium text-xs text-on-surface">8</button>
<button class="p-1.5 rounded border border-outline-variant hover:bg-slate-50 text-on-surface">
<span class="material-symbols-outlined text-[18px]" data-icon="chevron_right">chevron_right</span>
</button>
<button class="p-1.5 rounded border border-outline-variant hover:bg-slate-50 text-on-surface">
<span class="material-symbols-outlined text-[18px]" data-icon="last_page">last_page</span>
</button>
</div>
</div>
</section>
<!-- Institutional Footer -->
<footer class="pt-6 pb-8 border-t border-outline-variant/60 text-center text-on-surface-variant space-y-1">
<p class="font-label-md text-label-md font-semibold text-on-surface">
          Trường Đại học Công Thương TP. Hồ Chí Minh (HUIT) • Cổng Thông tin Giảng viên &amp; Quản lý Đào tạo
        </p>
<p class="font-body-sm text-body-sm">
          Địa chỉ: 140 Lê Trọng Tấn, P. Tây Thạnh, Q. Tân Phú, TP. Hồ Chí Minh | Phòng Quản lý Đào tạo: (028) 3816 1673 - daotao@huit.edu.vn
        </p>
<p class="font-label-sm text-label-sm text-slate-500">
          Phiên bản hệ thống: 4.8.2-ERP • Bản quyền © 2026 HUIT. Tất cả các quyền được bảo lưu.
        </p>
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
