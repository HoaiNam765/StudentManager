@extends('fe_preview.layouts.student')
@section('page_title', 'Cổng Sinh viên - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Cổng Sinh viên - HUIT Học Vụ</title>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/tong-quan']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">dashboard</span>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Cổng Sinh viên</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Cổng Sinh viên</div>
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
<!-- Header Chào mừng -->
<div class="bg-white border border-[#e2e8f0] rounded-xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
<div class="space-y-1">
<div class="flex flex-wrap items-center gap-3">
<h1 class="text-2xl lg:text-[28px] font-bold text-on-surface tracking-tight">
            Xin chào, Người dùng mẫu 25 👋
          </h1>
<span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 flex items-center gap-1">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Sinh viên chính quy
          </span>
</div>
<div class="flex flex-wrap items-center gap-y-1 gap-x-2 text-on-surface-variant text-[13px]">
<span>MSSV: <strong class="text-on-surface font-semibold">2001210123</strong></span>
<span class="text-[#e2e8f0]">•</span>
<span>Ngành: <strong class="text-on-surface font-semibold">Công nghệ Thông tin</strong></span>
<span class="text-[#e2e8f0]">•</span>
<span>Lớp: <strong class="text-on-surface font-semibold">12DHTT01</strong></span>
<span class="text-[#e2e8f0]">•</span>
<span>Khóa: <strong class="text-on-surface font-semibold">2021 - 2025</strong></span>
</div>
</div>
<div class="text-xs text-on-surface-variant bg-surface-container-low px-3.5 py-2 rounded-lg border border-[#e2e8f0] self-start md:self-auto flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[18px]">verified</span>
<span>Tình trạng học vụ: <strong class="text-emerald-700 font-semibold">Bình thường</strong></span>
</div>
</div>
<!-- HÀNG THẺ CHỈ SỐ TỔNG QUAN P1 (Grid 3 Cột) -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-5">
<!-- Thẻ 1: Kết quả học tập gần nhất -->
<div class="bg-white border border-[#e2e8f0] rounded-xl p-5 shadow-sm space-y-3 hover:border-primary/40 transition-colors">
<div class="flex items-center justify-between">
<span class="text-[13px] text-on-surface-variant font-medium">Kết quả học tập gần nhất</span>
<div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
<span class="material-symbols-outlined text-[18px]">trending_up</span>
</div>
</div>
<div>
<div class="flex items-baseline gap-1.5">
<span class="text-2xl font-bold text-primary">3.42</span>
<span class="text-xs text-on-surface-variant font-medium">/ 4.0</span>
<span class="ml-2 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">
              Xếp loại: Giỏi
            </span>
</div>
<div class="flex items-center justify-between text-xs text-on-surface-variant pt-2 border-t border-[#e2e8f0]/80 mt-2.5">
<span>Tích lũy: <strong class="text-on-surface font-semibold">3.38 / 4.0</strong></span>
<span class="text-emerald-700 font-medium flex items-center gap-0.5">
              ĐTB hệ 10: <strong>8.35</strong> (+0.08)
            </span>
</div>
</div>
</div>
<!-- Thẻ 2: Tổng tín chỉ đã tích lũy -->
<div class="bg-white border border-[#e2e8f0] rounded-xl p-5 shadow-sm space-y-3 hover:border-primary/40 transition-colors">
<div class="flex items-center justify-between">
<span class="text-[13px] text-on-surface-variant font-medium">Tổng tín chỉ đã tích lũy</span>
<div class="w-8 h-8 rounded-lg bg-blue-50 text-primary flex items-center justify-center">
<span class="material-symbols-outlined text-[18px]">auto_stories</span>
</div>
</div>
<div>
<div class="flex items-baseline justify-between">
<div class="flex items-baseline gap-1">
<span class="text-2xl font-bold text-on-surface">68</span>
<span class="text-xs text-on-surface-variant font-medium">/ 135 tín chỉ</span>
</div>
<span class="text-xs font-bold text-primary">50.4%</span>
</div>
<div class="w-full h-2 rounded-full bg-[#e2e8f0] overflow-hidden mt-2">
<div class="h-full rounded-full bg-primary transition-all duration-500" style="width: 50.4%"></div>
</div>
<div class="flex justify-between text-xs text-on-surface-variant pt-1.5">
<span>Tiến độ chương trình</span>
<span>Còn lại: <strong class="text-on-surface">67 tín chỉ</strong></span>
</div>
</div>
</div>
<!-- Thẻ 3: Học phần đang học (HK1) -->
<div class="bg-white border border-[#e2e8f0] rounded-xl p-5 shadow-sm space-y-3 hover:border-primary/40 transition-colors">
<div class="flex items-center justify-between">
<span class="text-[13px] text-on-surface-variant font-medium">Học phần đang học (HK1)</span>
<div class="w-8 h-8 rounded-lg bg-blue-50 text-primary flex items-center justify-center">
<span class="material-symbols-outlined text-[18px]">layers</span>
</div>
</div>
<div>
<div class="flex items-baseline gap-1.5">
<span class="text-2xl font-bold text-on-surface">6</span>
<span class="text-xs text-on-surface-variant font-medium">lớp học phần</span>
<span class="ml-auto px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800">
              19 tín chỉ
            </span>
</div>
<div class="flex items-center justify-between text-xs text-on-surface-variant pt-2 border-t border-[#e2e8f0]/80 mt-2.5">
<span>Trạng thái: <strong class="text-primary font-semibold">Đang trong kỳ học</strong></span>
<span class="text-outline">HK1 (2026-2027)</span>
</div>
</div>
</div>
</div>
<!-- KHỐI BANNER NỔI BẬT: Đợt đăng ký học phần đang mở -->
<div class="rounded-xl border border-blue-200 bg-gradient-to-r from-blue-50/90 via-indigo-50/70 to-white p-5 lg:p-6 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-5 relative overflow-hidden">
<div class="absolute -right-8 -bottom-8 w-40 h-40 bg-primary/5 rounded-full blur-2xl pointer-events-none"></div>
<div class="space-y-2 max-w-3xl">
<div class="flex flex-wrap items-center gap-2.5">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
<span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            Đang mở đăng ký
          </span>
<span class="text-xs font-medium text-primary bg-white px-2 py-0.5 rounded border border-blue-200 flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]">timer</span>
            Còn lại: 3 ngày 14 giờ
          </span>
</div>
<h2 class="text-lg lg:text-xl font-bold text-on-surface flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[22px]">how_to_reg</span>
          Đợt đăng ký học phần Học kỳ 2 (Năm học 2026-2027)
        </h2>
<p class="text-xs md:text-[13px] text-on-surface-variant leading-relaxed">
<strong>Đối tượng:</strong> Sinh viên chính quy Khóa 2021, 2022, 2023 • <strong>Hạn chót:</strong> 23:59 ngày 20/10/2026. Vui lòng kiểm tra kỹ danh sách học phần tiên quyết trước khi xác nhận lưu đăng ký.
        </p>
</div>
<div class="shrink-0">
<a class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-[#1545a5] text-white font-semibold text-sm px-5 py-3 rounded-lg shadow-sm hover:shadow transition-all active:scale-95" href="#">
<span>Đăng ký học phần ngay</span>
<span class="material-symbols-outlined text-[18px]">arrow_forward</span>
</a>
</div>
</div>
<!-- BỐ CỤC CHIA CỘT 8 + 4 -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
<!-- CỘT TRÁI (8 phần) -->
<div class="lg:col-span-8 space-y-6">
<!-- Khối Lịch học hôm nay -->
<div class="bg-white border border-[#e2e8f0] rounded-xl shadow-sm p-6 space-y-4">
<div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#e2e8f0] pb-3">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[22px]">today</span>
<h2 class="text-lg font-bold text-on-surface">Lịch học hôm nay (Thứ Tư, 16/10/2026)</h2>
</div>
<!-- Tab nhỏ phụ thể hiện các trạng thái UI cho kiểm thử -->
<div class="flex items-center gap-1 bg-surface-container-low p-1 rounded-lg border border-[#e2e8f0] text-[11px]">
<button class="px-2 py-0.5 rounded font-semibold bg-white text-primary shadow-2xs">Bình thường</button>
<button class="px-2 py-0.5 rounded font-medium text-on-surface-variant hover:text-on-surface" title="Xem trạng thái Đang tải">Đang tải</button>
<button class="px-2 py-0.5 rounded font-medium text-on-surface-variant hover:text-on-surface" title="Xem trạng thái Danh sách rỗng">Rỗng</button>
<button class="px-2 py-0.5 rounded font-medium text-on-surface-variant hover:text-on-surface" title="Xem trạng thái Lỗi">Lỗi tải</button>
</div>
</div>
<!-- Danh sách ca học trong ngày chuẩn P1 -->
<div class="space-y-3">
<!-- Ca 1: Lập trình Web -->
<div class="p-4 rounded-xl border border-emerald-200 bg-emerald-50/30 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:border-emerald-300 transition-colors">
<div class="space-y-1.5 min-w-0">
<div class="flex flex-wrap items-center gap-2">
<span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-primary text-white">Tiết 1 - 3 (07:00 - 09:15)</span>
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                    Đang diễn ra
                  </span>
<span class="text-xs text-outline font-mono">241_71ITEC3201_01</span>
</div>
<h3 class="text-base font-bold text-on-surface">Lập trình Web</h3>
<div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-on-surface-variant">
<span>Phòng: <strong class="text-primary font-semibold">B.204</strong></span>
<span class="text-outline-variant">•</span>
<span>Giảng viên: <strong class="text-on-surface">ThS. Nguyễn Văn A</strong></span>
<span class="text-outline-variant">•</span>
<span>Hình thức: <span class="font-medium text-on-surface">Trực tiếp</span></span>
</div>
</div>
<div class="shrink-0 self-start sm:self-center">
<span class="px-3 py-1.5 text-xs font-semibold text-emerald-800 bg-white border border-emerald-200 rounded-lg shadow-2xs flex items-center gap-1">
<span class="material-symbols-outlined text-[16px] text-emerald-700">room</span> B.204
                </span>
</div>
</div>
<!-- Ca 2: Cơ sở dữ liệu phân tán -->
<div class="p-4 rounded-xl border border-blue-200 bg-blue-50/20 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:border-blue-300 transition-colors">
<div class="space-y-1.5 min-w-0">
<div class="flex flex-wrap items-center gap-2">
<span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-surface-container-high text-on-surface">Tiết 4 - 6 (09:25 - 11:40)</span>
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800">
<span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                    Sắp bắt đầu
                  </span>
<span class="text-xs text-outline font-mono">241_71ITEC3202_02</span>
</div>
<h3 class="text-base font-bold text-on-surface">Cơ sở dữ liệu phân tán</h3>
<div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-on-surface-variant">
<span>Phòng: <strong class="text-primary font-semibold">B.301</strong></span>
<span class="text-outline-variant">•</span>
<span>Giảng viên: <strong class="text-on-surface">TS. Người dùng mẫu 15</strong></span>
<span class="text-outline-variant">•</span>
<span>Hình thức: <span class="font-medium text-on-surface">Trực tiếp</span></span>
</div>
</div>
<div class="shrink-0 self-start sm:self-center">
<span class="px-3 py-1.5 text-xs font-semibold text-primary bg-white border border-blue-200 rounded-lg shadow-2xs flex items-center gap-1">
<span class="material-symbols-outlined text-[16px]">meeting_room</span> B.301
                </span>
</div>
</div>
<!-- Ca 3: Thực hành Lập trình Mạng -->
<div class="p-4 rounded-xl border border-[#e2e8f0] bg-white flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:border-outline-variant transition-colors">
<div class="space-y-1.5 min-w-0">
<div class="flex flex-wrap items-center gap-2">
<span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-surface-container-high text-on-surface">Tiết 7 - 9 (13:00 - 15:15)</span>
<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-gray-100 text-gray-700">
                    Chiều nay
                  </span>
<span class="text-xs text-outline font-mono">241_71ITEC3203_03</span>
</div>
<h3 class="text-base font-bold text-on-surface">Thực hành Lập trình Mạng</h3>
<div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-on-surface-variant">
<span>Phòng: <strong class="text-primary font-semibold">PM04 (Phòng máy)</strong></span>
<span class="text-outline-variant">•</span>
<span>Giảng viên: <strong class="text-on-surface">ThS. Người dùng mẫu 38</strong></span>
<span class="text-outline-variant">•</span>
<span>Hình thức: <span class="font-medium text-on-surface">Thực hành tại lab</span></span>
</div>
</div>
<div class="shrink-0 self-start sm:self-center">
<span class="px-3 py-1.5 text-xs font-semibold text-on-surface-variant bg-surface-container-low border border-[#e2e8f0] rounded-lg shadow-2xs flex items-center gap-1">
<span class="material-symbols-outlined text-[16px]">computer</span> Lab PM04
                </span>
</div>
</div>
</div>
<div class="pt-2 flex justify-end">
<a class="text-xs font-semibold text-primary hover:text-blue-800 flex items-center gap-1 group py-1" href="#">
<span>Xem toàn bộ thời khóa biểu tuần</span>
<span class="material-symbols-outlined text-[16px] group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
</a>
</div>
</div>
<!-- Khối Thông tin học vụ bổ trợ chuẩn P1: Tiến độ Chương trình đào tạo -->
<div class="bg-white border border-[#e2e8f0] rounded-xl shadow-sm p-6 space-y-4">
<div class="flex flex-wrap items-center justify-between gap-2 border-b border-[#e2e8f0] pb-3">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[22px]">account_tree</span>
<h2 class="text-lg font-bold text-on-surface">Phân bổ Khối lượng Học phần</h2>
</div>
<span class="text-xs text-on-surface-variant">Chương trình 2021: <strong class="text-on-surface">135 tín chỉ</strong></span>
</div>
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
<div class="p-3.5 rounded-lg border border-[#e2e8f0] bg-surface-container-low/40 space-y-1.5">
<div class="flex justify-between items-center text-xs">
<span class="font-semibold text-on-surface truncate">Đại cương</span>
<span class="font-bold text-emerald-700">100%</span>
</div>
<div class="w-full h-1.5 rounded-full bg-[#e2e8f0] overflow-hidden">
<div class="h-full bg-emerald-600 rounded-full" style="width: 100%"></div>
</div>
<div class="text-[11px] text-on-surface-variant flex justify-between">
<span>30 / 30 tín chỉ</span>
<span class="text-emerald-700 font-medium">Hoàn thành</span>
</div>
</div>
<div class="p-3.5 rounded-lg border border-[#e2e8f0] bg-surface-container-low/40 space-y-1.5">
<div class="flex justify-between items-center text-xs">
<span class="font-semibold text-on-surface truncate">Cơ sở nhóm ngành</span>
<span class="font-bold text-primary">68%</span>
</div>
<div class="w-full h-1.5 rounded-full bg-[#e2e8f0] overflow-hidden">
<div class="h-full bg-primary rounded-full" style="width: 68%"></div>
</div>
<div class="text-[11px] text-on-surface-variant flex justify-between">
<span>26 / 38 tín chỉ</span>
<span>Còn 12 TC</span>
</div>
</div>
<div class="p-3.5 rounded-lg border border-[#e2e8f0] bg-surface-container-low/40 space-y-1.5">
<div class="flex justify-between items-center text-xs">
<span class="font-semibold text-on-surface truncate">Chuyên ngành</span>
<span class="font-bold text-amber-700">18%</span>
</div>
<div class="w-full h-1.5 rounded-full bg-[#e2e8f0] overflow-hidden">
<div class="h-full bg-amber-500 rounded-full" style="width: 18%"></div>
</div>
<div class="text-[11px] text-on-surface-variant flex justify-between">
<span>12 / 67 tín chỉ</span>
<span>Còn 55 TC</span>
</div>
</div>
</div>
</div>
</div>
<!-- CỘT PHẢI (4 phần) -->
<div class="lg:col-span-4 space-y-6">
<!-- Khối Thông báo mới -->
<div class="bg-white border border-[#e2e8f0] rounded-xl shadow-sm p-6 space-y-4">
<div class="flex items-center justify-between border-b border-[#e2e8f0] pb-3">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[22px]">notifications_active</span>
<h2 class="text-lg font-bold text-on-surface">Thông báo mới</h2>
</div>
<span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
</div>
<!-- Danh sách thông báo học vụ từ Phòng Đào tạo kèm nhãn mức độ bằng chữ + màu -->
<div class="space-y-3">
<!-- Thông báo 1: Khẩn -->
<a class="block p-3.5 rounded-lg border border-[#e2e8f0] hover:border-red-300 hover:bg-red-50/20 transition-all group" href="#">
<div class="flex items-center justify-between text-[11px] mb-1.5">
<span class="px-2 py-0.5 rounded font-bold bg-[#fee2e2] text-[#b91c1c]">
                  [Khẩn]
                </span>
<span class="text-on-surface-variant">15/10/2026</span>
</div>
<h3 class="text-[13px] font-semibold text-on-surface group-hover:text-primary transition-colors line-clamp-2 leading-snug">
                Thông báo điều chỉnh lịch học đánh giá giữa kỳ HK1 năm học 2026-2027
              </h3>
<p class="text-[11px] text-on-surface-variant mt-1">Phòng Đào tạo • Áp dụng toàn trường</p>
</a>
<!-- Thông báo 2: Quan trọng -->
<a class="block p-3.5 rounded-lg border border-[#e2e8f0] hover:border-amber-300 hover:bg-amber-50/20 transition-all group" href="#">
<div class="flex items-center justify-between text-[11px] mb-1.5">
<span class="px-2 py-0.5 rounded font-bold bg-[#fef3c7] text-[#b45309]">
                  [Quan trọng]
                </span>
<span class="text-on-surface-variant">14/10/2026</span>
</div>
<h3 class="text-[13px] font-semibold text-on-surface group-hover:text-primary transition-colors line-clamp-2 leading-snug">
                Hướng dẫn đăng ký học phần trực tuyến HK2 (2026-2027) và xử lý trùng lịch
              </h3>
<p class="text-[11px] text-on-surface-variant mt-1">Tổ Quản lý Đào tạo • Cổng trực tuyến</p>
</a>
<!-- Thông báo 3: Thông thường -->
<a class="block p-3.5 rounded-lg border border-[#e2e8f0] hover:border-blue-300 hover:bg-blue-50/20 transition-all group" href="#">
<div class="flex items-center justify-between text-[11px] mb-1.5">
<span class="px-2 py-0.5 rounded font-bold bg-[#eff6ff] text-[#2563eb]">
                  [Thông thường]
                </span>
<span class="text-on-surface-variant">12/10/2026</span>
</div>
<h3 class="text-[13px] font-semibold text-on-surface group-hover:text-primary transition-colors line-clamp-2 leading-snug">
                Danh sách sinh viên nhận khen thưởng khuyến khích học tập kỳ trước
              </h3>
<p class="text-[11px] text-on-surface-variant mt-1">Phòng Công tác Sinh viên</p>
</a>
<!-- Thông báo 4: Thông thường -->
<a class="block p-3.5 rounded-lg border border-[#e2e8f0] hover:border-blue-300 hover:bg-blue-50/20 transition-all group" href="#">
<div class="flex items-center justify-between text-[11px] mb-1.5">
<span class="px-2 py-0.5 rounded font-bold bg-[#eff6ff] text-[#2563eb]">
                  [Thông thường]
                </span>
<span class="text-on-surface-variant">10/10/2026</span>
</div>
<h3 class="text-[13px] font-semibold text-on-surface group-hover:text-primary transition-colors line-clamp-2 leading-snug">
                Đăng ký đề tài Nghiên cứu khoa học sinh viên cấp Khoa
              </h3>
<p class="text-[11px] text-on-surface-variant mt-1">Khoa Công nghệ Thông tin</p>
</a>
</div>
<div class="pt-1 text-center">
<a class="text-xs font-semibold text-primary hover:text-blue-800 flex items-center justify-center gap-1 group py-1" href="#">
<span>Xem tất cả thông báo học vụ</span>
<span class="material-symbols-outlined text-[16px] group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
</a>
</div>
</div>
<!-- Hộp Hỗ trợ nhanh Học vụ (Chuẩn P1) -->
<div class="bg-gradient-to-br from-blue-50/50 to-white border border-[#e2e8f0] rounded-xl p-5 shadow-sm space-y-3">
<div class="flex items-center gap-2 text-primary font-bold text-sm">
<span class="material-symbols-outlined text-[20px]">support_agent</span>
<span>Hỗ trợ Học vụ HUIT</span>
</div>
<p class="text-xs text-on-surface-variant leading-relaxed">
            Sinh viên cần hỗ trợ về thủ tục đăng ký học phần, bảo lưu, chuyển lớp học phần liên hệ qua:
          </p>
<div class="space-y-1.5 text-xs text-on-surface">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[16px] text-primary">call</span>
<span>Hotline: <strong class="font-semibold text-primary">028.38161673</strong></span>
</div>
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[16px] text-primary">mail</span>
<span>Email: <strong class="font-semibold">nguoidung025@example.test</strong></span>
</div>
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[16px] text-primary">location_on</span>
<span>Phòng Đào tạo: Nhà A, Cơ sở chính</span>
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
