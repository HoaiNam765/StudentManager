@extends('fe_preview.layouts.student')
@section('page_title', 'Lịch thi học kỳ - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Lịch thi học kỳ - HUIT Học Vụ</title>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/lich-thi']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">event_available</span>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Lịch thi học kỳ</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Cổng Sinh viên / Lịch thi</div>
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
<div class="space-y-6">
<!-- Countdown Banner -->
<div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-5 text-white shadow-md flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
<div class="flex items-center gap-3.5">
<div class="w-12 h-12 rounded-xl bg-white/15 backdrop-blur-md flex items-center justify-center shrink-0 border border-white/20">
<span class="material-symbols-outlined text-[28px] text-white">event_available</span>
</div>
<div>
<div class="flex items-center gap-2">
<span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-white/20 text-blue-100">Kỳ thi HK1 • 2026-2027</span>
<span class="px-2 py-0.5 rounded text-[11px] font-bold bg-amber-400 text-slate-900">Sắp diễn ra</span>
</div>
<h2 class="text-base sm:text-lg font-bold mt-0.5">Môn thi tiếp theo: Lập trình Java (241_71ITEC3201_01)</h2>
<p class="text-xs text-blue-100 mt-0.5">Thời gian: Thứ Hai, 18/11/2026 • Ca 1 (07:30 - 09:00) • Phòng B.302 Cơ sở chính</p>
</div>
</div>
<div class="flex items-center gap-2 bg-white/10 backdrop-blur-md px-4 py-2.5 rounded-xl border border-white/20 shrink-0">
<div class="text-center">
<span class="block text-2xl font-extrabold leading-none">04</span>
<span class="text-[10px] text-blue-100 uppercase tracking-wider">Ngày nữa</span>
</div>
</div>
</div>
<!-- Filter & View Mode Controls -->
<div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
<div class="flex items-center gap-2">
<label class="text-xs font-semibold text-slate-600" for="exam-term">Đợt thi:</label>
<select aria-label="Thi kết thúc học phần - Học kỳ 1 • 2026-2027" class="h-9 px-3 rounded-lg border border-slate-300 text-xs font-medium text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20" id="exam-term">
<option>Thi kết thúc học phần - Học kỳ 1 • 2026-2027</option>
<option>Thi phụ / Trả nợ học phần</option>
</select>
</div>
<div class="flex items-center gap-2">
<div class="flex items-center bg-slate-100 p-1 rounded-lg text-xs font-semibold">
<button class="px-3 py-1.5 rounded-md bg-white text-primary shadow-xs flex items-center gap-1.5 transition-all" id="btn-list-view" onclick="switchView('list')">
<span class="material-symbols-outlined text-[16px]">view_list</span>
<span>Danh sách</span>
</button>
<button class="px-3 py-1.5 rounded-md text-slate-600 hover:text-slate-900 flex items-center gap-1.5 transition-all" id="btn-cal-view" onclick="switchView('calendar')">
<span class="material-symbols-outlined text-[16px]">calendar_month</span>
<span>Lịch thi</span>
</button>
</div>
<button class="h-9 px-3.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 flex items-center gap-1.5 transition-colors no-print" onclick="window.print()">
<span class="material-symbols-outlined text-[16px]">print</span>
<span class="hidden sm:inline">In lịch thi</span>
</button>
</div>
</div>
<!-- LIST VIEW (Default) -->
<div class="space-y-4" id="view-list">
<!-- Desktop Table (Hidden on small mobile) -->
<div class="hidden md:block bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
<th class="py-3 px-4">STT</th>
<th class="py-3 px-4">Học phần</th>
<th class="py-3 px-4">Ngày thi</th>
<th class="py-3 px-4">Ca thi / Giờ</th>
<th class="py-3 px-4">Phòng thi</th>
<th class="py-3 px-4">SBD</th>
<th class="py-3 px-4">Hình thức</th>
<th class="py-3 px-4">Ghi chú</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
<tr class="hover:bg-blue-50/50 transition-colors bg-blue-50/20">
<td class="py-3.5 px-4 font-bold text-slate-900">01</td>
<td class="py-3.5 px-4">
<div class="font-semibold text-slate-900 text-sm">Lập trình Java</div>
<div class="text-[11px] text-slate-500">241_71ITEC3201_01 • 3 tín chỉ</div>
</td>
<td class="py-3.5 px-4 font-semibold text-primary">18/11/2026</td>
<td class="py-3.5 px-4">Ca 1 (07:30 - 09:00)</td>
<td class="py-3.5 px-4">
<span class="px-2 py-0.5 rounded font-bold bg-slate-100 text-slate-800 border border-slate-200">B.302</span>
</td>
<td class="py-3.5 px-4 font-mono font-bold text-slate-900">SBD-042</td>
<td class="py-3.5 px-4">
<span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Thực hành máy</span>
</td>
<td class="py-3.5 px-4 text-slate-500">Mang theo thẻ sinh viên</td>
</tr>
<tr class="hover:bg-blue-50/50 transition-colors">
<td class="py-3.5 px-4 font-bold text-slate-900">02</td>
<td class="py-3.5 px-4">
<div class="font-semibold text-slate-900 text-sm">Cơ sở dữ liệu nâng cao</div>
<div class="text-[11px] text-slate-500">241_71ITEC2202_02 • 3 tín chỉ</div>
</td>
<td class="py-3.5 px-4 font-semibold text-slate-900">22/11/2026</td>
<td class="py-3.5 px-4">Ca 3 (13:30 - 15:00)</td>
<td class="py-3.5 px-4">
<span class="px-2 py-0.5 rounded font-bold bg-slate-100 text-slate-800 border border-slate-200">C.205</span>
</td>
<td class="py-3.5 px-4 font-mono font-bold text-slate-900">SBD-042</td>
<td class="py-3.5 px-4">
<span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">Tự luận (90 phút)</span>
</td>
<td class="py-3.5 px-4 text-slate-500">Được sử dụng 1 tờ A4 viết tay</td>
</tr>
<tr class="hover:bg-blue-50/50 transition-colors">
<td class="py-3.5 px-4 font-bold text-slate-900">03</td>
<td class="py-3.5 px-4">
<div class="font-semibold text-slate-900 text-sm">Phát triển ứng dụng Web</div>
<div class="text-[11px] text-slate-500">241_71ITEC3304_01 • 3 tín chỉ</div>
</td>
<td class="py-3.5 px-4 font-semibold text-slate-900">28/11/2026</td>
<td class="py-3.5 px-4">Ca 2 (09:30 - 11:30)</td>
<td class="py-3.5 px-4">
<span class="px-2 py-0.5 rounded font-bold bg-slate-100 text-slate-800 border border-slate-200">Lab 4.1</span>
</td>
<td class="py-3.5 px-4 font-mono font-bold text-slate-900">SBD-042</td>
<td class="py-3.5 px-4">
<span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-purple-50 text-purple-700 border border-purple-200">Bảo vệ đồ án</span>
</td>
<td class="py-3.5 px-4 text-slate-500">Nộp slide và mã nguồn trước 24h</td>
</tr>
</tbody>
</table>
</div>
<!-- Mobile Card View (Optimized for 360px) -->
<div class="block md:hidden space-y-3">
<!-- Card 1 -->
<div class="bg-white p-4 rounded-xl border-l-4 border-l-primary border border-slate-200 shadow-xs space-y-2.5">
<div class="flex items-start justify-between gap-2">
<div>
<span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-primary border border-blue-100">18/11/2026 • Ca 1</span>
<h3 class="font-bold text-slate-900 text-sm mt-1">Lập trình Java</h3>
<p class="text-[11px] text-slate-500">241_71ITEC3201_01 • 3 TC</p>
</div>
<span class="px-2 py-0.5 rounded text-[10.5px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Máy tính</span>
</div>
<div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100 text-xs">
<div>
<span class="text-slate-500 block text-[10.5px]">Phòng thi</span>
<span class="font-bold text-slate-800">B.302 (CS chính)</span>
</div>
<div>
<span class="text-slate-500 block text-[10.5px]">Số báo danh</span>
<span class="font-bold text-primary font-mono">SBD-042</span>
</div>
</div>
<div class="text-[11px] text-slate-500 bg-slate-50 p-2 rounded-lg">
<strong>Ghi chú:</strong> Mang theo thẻ sinh viên có dán ảnh.
        </div>
</div>
<!-- Card 2 -->
<div class="bg-white p-4 rounded-xl border-l-4 border-l-blue-400 border border-slate-200 shadow-xs space-y-2.5">
<div class="flex items-start justify-between gap-2">
<div>
<span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">22/11/2026 • Ca 3</span>
<h3 class="font-bold text-slate-900 text-sm mt-1">Cơ sở dữ liệu nâng cao</h3>
<p class="text-[11px] text-slate-500">241_71ITEC2202_02 • 3 TC</p>
</div>
<span class="px-2 py-0.5 rounded text-[10.5px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">Tự luận</span>
</div>
<div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100 text-xs">
<div>
<span class="text-slate-500 block text-[10.5px]">Phòng thi</span>
<span class="font-bold text-slate-800">C.205 (CS chính)</span>
</div>
<div>
<span class="text-slate-500 block text-[10.5px]">Số báo danh</span>
<span class="font-bold text-primary font-mono">SBD-042</span>
</div>
</div>
</div>
</div>
</div>
<!-- CALENDAR VIEW (Initially hidden) -->
<div class="hidden bg-white p-5 rounded-xl border border-slate-200 shadow-xs" id="view-calendar">
<div class="text-center font-bold text-sm text-slate-800 mb-4">Lịch thi Tháng 11 / 2026</div>
<div class="grid grid-cols-7 gap-1 text-center text-xs font-semibold text-slate-500 mb-2">
<div>T2</div><div>T3</div><div>T4</div><div>T5</div><div>T6</div><div>T7</div><div>CN</div>
</div>
<div class="grid grid-cols-7 gap-2 text-xs">
<div class="h-20 p-1.5 rounded-lg border border-slate-100 bg-slate-50/50 text-slate-500">16</div>
<div class="h-20 p-1.5 rounded-lg border border-slate-100 bg-slate-50/50 text-slate-500">17</div>
<div class="h-20 p-1.5 rounded-lg border-2 border-primary bg-blue-50 text-slate-900 font-bold relative">
<span>18</span>
<div class="mt-1 p-1 rounded bg-primary text-white text-[10px] leading-tight truncate">07:30 Java (B.302)</div>
</div>
<div class="h-20 p-1.5 rounded-lg border border-slate-100 bg-slate-50/50 text-slate-500">19</div>
<div class="h-20 p-1.5 rounded-lg border border-slate-100 bg-slate-50/50 text-slate-500">20</div>
<div class="h-20 p-1.5 rounded-lg border border-slate-100 bg-slate-50/50 text-slate-500">21</div>
<div class="h-20 p-1.5 rounded-lg border-2 border-blue-400 bg-blue-50/70 text-slate-900 font-bold">
<span>22</span>
<div class="mt-1 p-1 rounded bg-blue-600 text-white text-[10px] leading-tight truncate">13:30 CSDL (C.205)</div>
</div>
</div>
</div>
</div>
<script>
  function switchView(mode) {
    const list = document.getElementById('view-list');
    const cal = document.getElementById('view-calendar');
    const btnList = document.getElementById('btn-list-view');
    const btnCal = document.getElementById('btn-cal-view');

    if (mode === 'list') {
      list.classList.remove('hidden');
      cal.classList.add('hidden');
      btnList.className = 'px-3 py-1.5 rounded-md bg-white text-primary shadow-xs flex items-center gap-1.5 transition-all';
      btnCal.className = 'px-3 py-1.5 rounded-md text-slate-600 hover:text-slate-900 flex items-center gap-1.5 transition-all';
    } else {
      list.classList.add('hidden');
      cal.classList.remove('hidden');
      btnCal.className = 'px-3 py-1.5 rounded-md bg-white text-primary shadow-xs flex items-center gap-1.5 transition-all';
      btnList.className = 'px-3 py-1.5 rounded-md text-slate-600 hover:text-slate-900 flex items-center gap-1.5 transition-all';
    }
  }
</script>
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
