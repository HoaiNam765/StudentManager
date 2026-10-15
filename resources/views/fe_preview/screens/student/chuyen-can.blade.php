@extends('fe_preview.layouts.student')
@section('page_title', 'Theo dõi chuyên cần - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Theo dõi chuyên cần - HUIT Học Vụ</title>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/chuyen-can']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">rule</span>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Theo dõi chuyên cần</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Cổng Sinh viên / Chuyên cần</div>
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
<div class="space-y-6">
<!-- Warning Banner for Threshold -->
<div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-xl flex items-start gap-3 shadow-xs">
<span class="material-symbols-outlined text-amber-700 text-[22px] shrink-0 mt-0.5">warning</span>
<div>
<h3 class="font-bold text-slate-900 text-sm">Quy định về ngưỡng cấm thi chuyên cần (BA mục 6.8)</h3>
<p class="text-xs text-slate-600 mt-0.5 leading-relaxed">
        Sinh viên nghỉ học vượt quá <strong>20% tổng số tiết</strong> của một học phần sẽ bị <strong>CẤM THI</strong> kết thúc học phần đó. Cảnh báo tự động kích hoạt khi tỷ lệ vắng đạt từ 15%.
      </p>
</div>
</div>
<!-- Overall Stat Cards -->
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
<div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
<span class="text-[11px] font-semibold text-slate-500 block uppercase">Tổng học phần</span>
<span class="text-xl font-extrabold text-slate-900 mt-1 block">05</span>
<span class="text-[11px] text-slate-500">Đang theo học</span>
</div>
<div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
<span class="text-[11px] font-semibold text-emerald-700 block uppercase">Chuyên cần tốt</span>
<span class="text-xl font-extrabold text-emerald-700 mt-1 block">04</span>
<span class="text-[11px] text-slate-500">Vắng &lt; 10%</span>
</div>
<div class="bg-white p-4 rounded-xl border border-amber-200 bg-amber-50/30 shadow-xs">
<span class="text-[11px] font-semibold text-amber-700 block uppercase">Nguy cơ cấm thi</span>
<span class="text-xl font-extrabold text-amber-700 mt-1 block">01</span>
<span class="text-[11px] text-amber-700 font-medium">Vắng 16.7% (2/12 buổi)</span>
</div>
<div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
<span class="text-[11px] font-semibold text-slate-500 block uppercase">Bị cấm thi</span>
<span class="text-xl font-extrabold text-slate-900 mt-1 block">00</span>
<span class="text-[11px] text-emerald-700 font-medium">Không có</span>
</div>
</div>
<!-- Subject Attendance List (Responsive Cards + Accordion) -->
<div class="space-y-4">
<!-- Item 1: At-risk subject -->
<div class="bg-white rounded-xl border-2 border-amber-300 shadow-xs overflow-hidden">
<div class="p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-amber-50/20">
<div class="space-y-1">
<div class="flex items-center gap-2">
<span class="px-2 py-0.5 rounded text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-300">CẢNH BÁO: Gần ngưỡng cấm thi</span>
<span class="text-xs text-slate-500">Mã LHP: 241_71ITEC3201_01</span>
</div>
<h3 class="text-base font-bold text-slate-900">Lập trình Java (3 tín chỉ)</h3>
<p class="text-xs text-slate-600">Giảng viên: TS. Người dùng mẫu 38 • Phòng B.302</p>
</div>
<div class="flex items-center gap-6">
<div class="text-left md:text-right">
<div class="text-xs text-slate-500">Tỷ lệ vắng / Ngưỡng</div>
<div class="text-lg font-extrabold text-amber-700">16.7% <span class="text-xs text-slate-500 font-normal">/ 20%</span></div>
<div class="text-[11px] text-slate-500">Đã vắng <strong>2/12 buổi</strong> (10 buổi có mặt)</div>
</div>
<button class="h-9 px-3.5 rounded-lg bg-amber-100 hover:bg-amber-200 text-amber-900 font-semibold text-xs flex items-center gap-1 transition-colors shrink-0" onclick="toggleDetails('subj-1')">
<span>Chi tiết buổi</span>
<span class="material-symbols-outlined text-[16px]">expand_more</span>
</button>
</div>
</div>
<!-- Progress Bar -->
<div class="w-full bg-slate-100 h-2">
<div class="bg-amber-500 h-2" style="width: 83.3%;"></div>
</div>
<!-- Detail Table/List (Toggleable) -->
<div class="p-4 bg-slate-50/60 border-t border-slate-100 divide-y divide-slate-100 text-xs" id="subj-1">
<div class="grid grid-cols-2 sm:grid-cols-4 py-2 font-semibold text-slate-500 text-[11px] uppercase">
<div>Buổi học / Ngày</div>
<div>Tiết học</div>
<div>Trạng thái</div>
<div>Ghi chú</div>
</div>
<div class="grid grid-cols-2 sm:grid-cols-4 py-2.5 items-center">
<div class="font-medium text-slate-800">Buổi 1 • 05/09/2026</div>
<div class="text-slate-600">Tiết 1-3 (07:00 - 09:25)</div>
<div><span class="px-2 py-0.5 rounded font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Có mặt</span></div>
<div class="text-slate-500">Đúng giờ</div>
</div>
<div class="grid grid-cols-2 sm:grid-cols-4 py-2.5 items-center">
<div class="font-medium text-slate-800">Buổi 2 • 12/09/2026</div>
<div class="text-slate-600">Tiết 1-3 (07:00 - 09:25)</div>
<div><span class="px-2 py-0.5 rounded font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Có mặt</span></div>
<div class="text-slate-500">Đúng giờ</div>
</div>
<div class="grid grid-cols-2 sm:grid-cols-4 py-2.5 items-center bg-blue-50/40">
<div class="font-medium text-slate-800">Buổi 3 • 19/09/2026</div>
<div class="text-slate-600">Tiết 1-3 (07:00 - 09:25)</div>
<div><span class="px-2 py-0.5 rounded font-bold bg-blue-50 text-blue-700 border border-blue-200">Vắng có phép</span></div>
<div class="text-slate-600 font-medium">Đơn xin nghỉ phép số #0821 (ốm)</div>
</div>
<div class="grid grid-cols-2 sm:grid-cols-4 py-2.5 items-center bg-red-50/40">
<div class="font-medium text-slate-800">Buổi 4 • 26/09/2026</div>
<div class="text-slate-600">Tiết 1-3 (07:00 - 09:25)</div>
<div><span class="px-2 py-0.5 rounded font-bold bg-red-50 text-red-700 border border-red-200">Vắng không phép</span></div>
<div class="text-red-700 font-semibold">Cảnh báo: Nếu vắng thêm 1 buổi sẽ bị cấm thi</div>
</div>
</div>
</div>
<!-- Item 2: Normal subject -->
<div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
<div class="p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
<div class="space-y-1">
<div class="flex items-center gap-2">
<span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">An toàn</span>
<span class="text-xs text-slate-500">Mã LHP: 241_71ITEC2202_02</span>
</div>
<h3 class="text-base font-bold text-slate-900">Cơ sở dữ liệu nâng cao (3 tín chỉ)</h3>
<p class="text-xs text-slate-600">Giảng viên: ThS. Người dùng mẫu 13 • Phòng C.205</p>
</div>
<div class="flex items-center gap-6">
<div class="text-left md:text-right">
<div class="text-xs text-slate-500">Tỷ lệ vắng / Ngưỡng</div>
<div class="text-lg font-extrabold text-emerald-700">0.0% <span class="text-xs text-slate-500 font-normal">/ 20%</span></div>
<div class="text-[11px] text-slate-500">Đã học <strong>12/12 buổi</strong> (100% có mặt)</div>
</div>
<button class="h-9 px-3.5 rounded-lg border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold text-xs flex items-center gap-1 transition-colors shrink-0" onclick="toggleDetails('subj-2')">
<span>Chi tiết buổi</span>
<span class="material-symbols-outlined text-[16px]">expand_more</span>
</button>
</div>
</div>
<div class="w-full bg-slate-100 h-2">
<div class="bg-emerald-500 h-2" style="width: 100%;"></div>
</div>
<div class="hidden p-4 bg-slate-50/60 border-t border-slate-100 text-xs text-slate-600" id="subj-2">
        Sinh viên đã tham gia đầy đủ 12/12 buổi học phần này.
      </div>
</div>
</div>
</div>
<script>
  function toggleDetails(id) {
    const el = document.getElementById(id);
    el.classList.toggle('hidden');
  }
</script>
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
