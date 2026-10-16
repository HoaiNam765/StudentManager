@extends('fe_preview.layouts.student')
@section('page_title', 'Sinh viên - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="light"@endsection
@section('body_attributes')class="bg-[#f8f9ff] text-slate-800 antialiased min-h-screen flex selection:bg-blue-100 selection:text-blue-900"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Sinh viên - HUIT Học Vụ</title>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Sinh viên</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Sinh viên</div>
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
<!-- Ô tìm kiếm gọn gàng -->
<div class="relative">
<span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-500 text-[18px]">search</span>
<input aria-label="Tìm tên học phần, mã lớp..." class="w-full pl-9 pr-8 py-2 bg-white text-[12px] border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] shadow-sm text-slate-700 placeholder:text-slate-500" placeholder="Tìm tên học phần, mã lớp..." type="text" value=""/>
<button class="absolute right-2.5 top-2.5 text-slate-500 hover:text-slate-600">
<span class="material-symbols-outlined text-[16px]">tune</span>
</button>
</div>
<!-- Bộ lọc nhanh danh mục -->
<div class="flex gap-1.5 overflow-x-auto no-scrollbar text-[11px] pb-0.5">
<span class="px-2.5 py-1 rounded-lg bg-[#1c55c4] text-white font-medium whitespace-nowrap shadow-xs">Theo kế hoạch CTĐT</span>
<span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-600 whitespace-nowrap">Học lại / Cải thiện</span>
<span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-600 whitespace-nowrap">Khoa CNTT</span>
</div>
<!-- THẺ 1: Lớp 241_71ITEC3201_01 • Lập trình Web (Đã chọn) -->
<div class="bg-white rounded-xl p-3 border border-blue-200 shadow-sm relative overflow-hidden">
<div class="absolute top-0 right-0 bg-blue-50 text-[#1c55c4] text-[10px] font-bold px-2 py-0.5 rounded-bl-lg border-l border-b border-blue-100 flex items-center gap-1">
<span class="material-symbols-outlined text-[13px]">check_circle</span>
          Đã vào TKB tạm
        </div>
<div class="pr-20 mb-1">
<span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider block">241_71ITEC3201_01</span>
<h3 class="text-[13px] font-bold text-slate-900 leading-snug">Lập trình Web</h3>
</div>
<div class="flex items-center gap-2 mb-2 text-[11px] text-slate-600">
<span class="px-1.5 py-0.5 bg-blue-50 text-[#1c55c4] font-semibold rounded text-[10px]">3 Tín chỉ</span>
<span class="flex items-center gap-0.5 text-slate-500">
<span class="material-symbols-outlined text-[14px]">person</span>
            TS. Người dùng mẫu 38
          </span>
</div>
<!-- Chi tiết lịch & sĩ số -->
<div class="bg-slate-50 rounded-lg p-2 mb-2.5 space-y-1 text-[11px] border border-slate-100">
<div class="flex items-center justify-between text-slate-700">
<span class="flex items-center gap-1 text-slate-600">
<span class="material-symbols-outlined text-[14px] text-slate-500">schedule</span>
              Thứ 2, Tiết 1 - 3 (07:00 - 09:30)
            </span>
<span class="font-semibold text-slate-800">P. A2.301</span>
</div>
<div class="flex items-center justify-between text-[10px] pt-1 border-t border-slate-200/60">
<span class="text-slate-500">Sĩ số: <strong class="text-slate-800 font-semibold">48/60</strong> SV</span>
<span class="text-emerald-700 font-semibold">Còn 12 chỗ trống</span>
</div>
</div>
<!-- Nút lớn dễ bấm: [Đã chọn - Bỏ chọn] -->
<button class="touch-btn w-full py-2.5 bg-red-50 text-red-700 border border-red-200 rounded-lg text-[12px] font-semibold flex items-center justify-center gap-1.5 active:bg-red-100 transition-colors">
<span class="material-symbols-outlined text-[16px]">delete</span>
<span>Bỏ chọn lớp này</span>
</button>
</div>
<!-- THẺ 2: Lớp 241_71ITEC2104_02 • Cấu trúc dữ liệu & GT (Cảnh báo trùng lịch) -->
<div class="bg-white rounded-xl p-3 border-2 border-amber-300 shadow-sm relative overflow-hidden bg-amber-50/20">
<div class="flex justify-between items-start mb-1">
<div>
<span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider block">241_71ITEC2104_02</span>
<h3 class="text-[13px] font-bold text-slate-900 leading-snug">Cấu trúc dữ liệu &amp; Giải thuật</h3>
</div>
<span class="px-1.5 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-bold rounded">4 Tín chỉ</span>
</div>
<div class="flex items-center gap-2 mb-2 text-[11px] text-slate-600">
<span class="flex items-center gap-0.5 text-slate-500">
<span class="material-symbols-outlined text-[14px]">person</span>
            ThS. Người dùng mẫu 11
          </span>
<span>•</span>
<span class="text-slate-600">Thứ 3, Tiết 4 - 6 (P. B.204)</span>
</div>
<!-- Badge Cảnh báo trùng lịch -->
<div class="bg-red-50 border border-red-200 rounded-lg p-2 mb-2.5 flex items-start gap-1.5 text-[11px]">
<span class="material-symbols-outlined text-red-700 text-[16px] shrink-0 mt-0.5">warning</span>
<div class="leading-tight">
<span class="font-bold text-red-700">Trùng lịch: </span>
<span class="text-red-700">Trùng thời gian Thứ 3 với Lớp Lập trình Web đã chọn.</span>
</div>
</div>
<!-- Nút lớn: [Trùng lịch - Xem chi tiết] -->
<button class="touch-btn w-full py-2.5 bg-amber-500 text-white rounded-lg text-[12px] font-bold flex items-center justify-center gap-1.5 shadow-sm active:bg-amber-600 transition-colors">
<span class="material-symbols-outlined text-[17px]">sync_problem</span>
<span>Trùng lịch - Xem chi tiết &amp; Xử lý</span>
</button>
</div>
<!-- THẺ 3: Lớp 241_71MATH1102_01 • Xác suất thống kê (Đã hết chỗ) -->
<div class="bg-white/80 rounded-xl p-3 border border-slate-200 shadow-xs relative opacity-85">
<div class="flex justify-between items-start mb-1">
<div>
<span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider block">241_71MATH1102_01</span>
<h3 class="text-[13px] font-bold text-slate-700 leading-snug">Xác suất thống kê</h3>
</div>
<span class="px-1.5 py-0.5 bg-slate-100 text-slate-600 text-[10px] font-semibold rounded">3 Tín chỉ</span>
</div>
<div class="flex items-center gap-2 mb-2 text-[11px] text-slate-500">
<span class="flex items-center gap-0.5">
<span class="material-symbols-outlined text-[14px]">person</span>
            TS. Người dùng mẫu 22
          </span>
<span>•</span>
<span>Thứ 4, Tiết 1 - 3</span>
</div>
<!-- Sĩ số đã đủ -->
<div class="flex items-center justify-between bg-slate-100 rounded-lg px-2.5 py-1.5 mb-2.5 text-[11px]">
<span class="text-slate-600">Sĩ số: <strong class="text-red-700 font-bold">60 / 60 SV</strong></span>
<span class="px-2 py-0.5 bg-red-100 text-red-700 font-bold text-[10px] rounded-full">Lớp đã đủ sĩ số</span>
</div>
<!-- Nút disabled: [Đã hết chỗ] -->
<button class="touch-btn w-full py-2.5 bg-slate-200 text-slate-500 rounded-lg text-[12px] font-medium flex items-center justify-center gap-1.5 cursor-not-allowed" disabled="">
<span class="material-symbols-outlined text-[16px]">lock</span>
<span>Lớp đã hết chỗ</span>
</button>
</div>
<!-- THẺ 4: Lớp 241_71ITEC3208_02 • Mạng máy tính (Còn chỗ - Có thể chọn) -->
<div class="bg-white rounded-xl p-3 border border-slate-200 shadow-sm relative">
<div class="flex justify-between items-start mb-1">
<div>
<span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider block">241_71ITEC3208_02</span>
<h3 class="text-[13px] font-bold text-slate-900 leading-snug">Mạng máy tính</h3>
</div>
<span class="px-1.5 py-0.5 bg-blue-50 text-[#1c55c4] text-[10px] font-bold rounded">3 Tín chỉ</span>
</div>
<div class="flex items-center gap-2 mb-2 text-[11px] text-slate-600">
<span class="flex items-center gap-0.5 text-slate-500">
<span class="material-symbols-outlined text-[14px]">person</span>
            ThS. Người dùng mẫu 56
          </span>
</div>
<!-- Thông tin lớp học -->
<div class="bg-slate-50 rounded-lg p-2 mb-2.5 space-y-1 text-[11px] border border-slate-100">
<div class="flex items-center justify-between text-slate-700">
<span class="flex items-center gap-1 text-slate-600">
<span class="material-symbols-outlined text-[14px] text-slate-500">schedule</span>
              Thứ 6, Tiết 1 - 3 (07:00 - 09:30)
            </span>
<span class="font-semibold text-slate-800">P. B.105</span>
</div>
<div class="flex items-center justify-between text-[10px] pt-1 border-t border-slate-200/60">
<span class="text-slate-500">Sĩ số: <strong class="text-slate-800 font-semibold">35/60</strong> SV</span>
<span class="text-emerald-700 font-semibold">Còn 25 chỗ trống</span>
</div>
</div>
<!-- Nút lớn: [+ Chọn lớp này] -->
<button class="touch-btn w-full py-2.5 bg-[#1c55c4] text-white rounded-lg text-[12px] font-bold flex items-center justify-center gap-1.5 shadow-sm active:bg-blue-800 transition-colors">
<span class="material-symbols-outlined text-[18px]">add_circle</span>
<span>+ Chọn lớp này vào TKB</span>
</button>
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
