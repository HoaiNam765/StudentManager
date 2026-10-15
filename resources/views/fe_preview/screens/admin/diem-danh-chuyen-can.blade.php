@extends('fe_preview.layouts.admin')
@section('page_title', 'Điểm danh & Chuyên cần toàn trường - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Điểm danh &amp; Chuyên cần toàn trường - HUIT Học Vụ</title>
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
<span class="material-symbols-outlined text-[20px]">admin_panel_settings</span>
</div>
<div class="overflow-hidden">
<div class="font-bold text-[13.5px] text-slate-900 leading-tight truncate">HUIT Quản Trị</div>
<div class="text-[11px] text-slate-500 font-medium truncate">Phòng Đào tạo</div>
</div>
</div>
<button class="lg:hidden p-1.5 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100" onclick="toggleSidebar()">
<span class="material-symbols-outlined text-[20px]">close</span>
</button>
</div>
<!-- Navigation Menu (Dynamic by Stage) -->
<div class="flex-1 overflow-y-auto custom-scrollbar p-3 space-y-6">
<div class="space-y-1">
<div class="px-3 py-1.5 text-[10.5px] font-bold tracking-wider text-slate-500 uppercase">TỔNG QUAN</div>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/tong-quan']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">dashboard</span>
<span class="truncate">Bàn làm việc quản lý</span>
</a>
</div>
<div class="space-y-1">
<div class="px-3 py-1.5 text-[10.5px] font-bold tracking-wider text-slate-500 uppercase">ĐÀO TẠO &amp; HỌC VỤ</div>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/danh-muc-nen']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">category</span>
<span class="truncate">Danh mục nền đào tạo</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/hoc-phan']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">library_books</span>
<span class="truncate">Quản lý Học phần</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/chuong-trinh-dao-tao']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">schema</span>
<span class="truncate">Chương trình đào tạo</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/khoa-va-lop-hanh-chinh']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">badge</span>
<span class="truncate">Khóa &amp; Lớp hành chính</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/lop-hoc-phan']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">groups</span>
<span class="truncate">Quản lý Lớp học phần</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/xep-thoi-khoa-bieu']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">date_range</span>
<span class="truncate">Xếp thời khóa biểu</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/dot-dang-ky-hoc-phan']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">event_available</span>
<span class="truncate">Đợt ĐK học phần</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/dang-ky-ngoai-le']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">assignment_turned_in</span>
<span class="truncate">Đăng ký ngoại lệ</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/quan-ly-diem']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">grading</span>
<span class="truncate">Quản lý Điểm số</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/diem-danh-chuyen-can']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">fact_check</span>
<span class="truncate">Điểm danh &amp; Chuyên cần</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/ky-thi']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">assignment_late</span>
<span class="truncate">Kỳ thi &amp; Phòng thi</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/lich-day-bu-phong']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">meeting_room</span>
<span class="truncate">Dạy bù &amp; Phòng học</span>
</a>
</div>
<div class="space-y-1">
<div class="px-3 py-1.5 text-[10.5px] font-bold tracking-wider text-slate-500 uppercase">TÀI CHÍNH &amp; HỒ SƠ</div>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/hoc-phi']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">account_balance_wallet</span>
<span class="truncate">Học phí &amp; Công nợ</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/ho-so-giay-to']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">folder_shared</span>
<span class="truncate">Hồ sơ &amp; Giấy tờ SV</span>
</a>
</div>
<div class="space-y-1">
<div class="px-3 py-1.5 text-[10.5px] font-bold tracking-wider text-slate-500 uppercase">NGƯỜI DÙNG &amp; TỔ CHỨC</div>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/giang-vien']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">co_present</span>
<span class="truncate">Quản lý Giảng viên</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/sinh-vien']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">person_search</span>
<span class="truncate">Quản lý Sinh viên</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/nguoi-dung']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">manage_accounts</span>
<span class="truncate">Người dùng &amp; Phân quyền</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/thong-bao']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">campaign</span>
<span class="truncate">Quản lý Thông báo</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/mau-thong-bao']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">forward_to_inbox</span>
<span class="truncate">Mẫu thông báo &amp; Email</span>
</a>
</div>
<div class="space-y-1">
<div class="px-3 py-1.5 text-[10.5px] font-bold tracking-wider text-slate-500 uppercase">HỆ THỐNG &amp; BÁO CÁO</div>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/tham-so-he-thong']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">tune</span>
<span class="truncate">Tham số hệ thống</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/quy-che-hoc-vu']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">gavel</span>
<span class="truncate">Quy chế học vụ</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/nhat-ky-thao-tac']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">history</span>
<span class="truncate">Nhật ký thao tác</span>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Điểm danh &amp; Chuyên cần toàn trường</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Cổng Quản trị / Điểm danh &amp; Chuyên cần</div>
</div>
</div>
<!-- Header Right -->
<div class="flex items-center gap-2 sm:gap-3 shrink-0">
<!-- Role quick switch -->
<div class="hidden md:flex items-center p-1 bg-slate-100 rounded-lg text-xs font-medium">
<a class="px-2.5 py-1 text-slate-600 hover:text-slate-900" href="{{ route('fe.preview.show', ['screen' => 'student/tong-quan']) }}">Sinh viên</a>
<a class="px-2.5 py-1 text-slate-600 hover:text-slate-900" href="{{ route('fe.preview.show', ['screen' => 'teacher/lich-giang-day']) }}">Giảng viên</a>
<span class="px-2.5 py-1 rounded-md bg-white text-primary font-semibold shadow-xs">Phòng ĐT</span>
</div>
<!-- Notification Bell -->
<a class="relative p-2 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/thong-bao']) }}" title="Thông báo">
<span class="material-symbols-outlined text-[21px]">notifications</span>
</a>
<!-- User Profile Pill -->
<div class="flex items-center gap-2.5 pl-2 border-l border-slate-200">
<div class="w-8 h-8 rounded-full bg-blue-100 text-primary font-bold text-xs flex items-center justify-center border border-blue-200">
            PĐT
          </div>
<div class="hidden sm:block text-left">
<div class="text-xs font-semibold text-slate-900 leading-tight">Phòng Đào tạo</div>
<div class="text-[11px] text-slate-500">admin@huit.edu.vn</div>
</div>
</div>
</div>
</header>
<!-- MAIN BODY CONTENT -->
<main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
<div class="space-y-6">
<!-- Top Stat Cards -->
<div class="grid grid-cols-1 sm:grid-cols-4 gap-3.5">
<div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
<span class="text-[11px] font-semibold text-slate-500 block uppercase">Tổng số lớp HP</span>
<span class="text-xl font-extrabold text-slate-900 mt-1 block">342 lớp</span>
<span class="text-[11px] text-slate-500">Đang hoạt động trong HK1</span>
</div>
<div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
<span class="text-[11px] font-semibold text-emerald-700 block uppercase">Tỷ lệ chuyên cần TB</span>
<span class="text-xl font-extrabold text-emerald-700 mt-1 block">94.2%</span>
<span class="text-[11px] text-slate-500">Toàn trường</span>
</div>
<div class="bg-white p-4 rounded-xl border border-amber-200 bg-amber-50/20 shadow-xs">
<span class="text-[11px] font-semibold text-amber-700 block uppercase">Chạm ngưỡng cảnh báo</span>
<span class="text-xl font-extrabold text-amber-700 mt-1 block">18 SV</span>
<span class="text-[11px] text-amber-700 font-medium">Vắng 15% - 19% số tiết</span>
</div>
<div class="bg-white p-4 rounded-xl border border-red-200 bg-red-50/20 shadow-xs">
<span class="text-[11px] font-semibold text-red-700 block uppercase">Cấm thi chuyên cần</span>
<span class="text-xl font-extrabold text-red-700 mt-1 block">05 SV</span>
<span class="text-[11px] text-red-700 font-medium">Vắng ≥ 20% số tiết</span>
</div>
</div>
<!-- Filters -->
<div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs flex flex-wrap items-center gap-3">
<div class="flex-1 min-w-[200px]">
<label class="block text-[11px] font-bold text-slate-500 uppercase mb-1" for="adm-faculty-sel">Khoa / Đơn vị</label>
<select aria-label="Khoa Công nghệ Thông tin" class="w-full h-9 px-3 rounded-lg border border-slate-300 text-xs bg-slate-50" id="adm-faculty-sel">
<option>Khoa Công nghệ Thông tin</option>
<option>Khoa Quản trị Kinh doanh</option>
<option>Khoa Công nghệ Thực phẩm</option>
</select>
</div>
<div class="flex-1 min-w-[200px]">
<label class="block text-[11px] font-bold text-slate-500 uppercase mb-1" for="adm-lhp-sel">Lớp học phần</label>
<select aria-label="Tất cả lớp học phần" class="w-full h-9 px-3 rounded-lg border border-slate-300 text-xs bg-slate-50" id="adm-lhp-sel">
<option>Tất cả lớp học phần</option>
<option>241_71ITEC3201_01 - Lập trình Java</option>
<option>241_71ITEC2202_02 - CSDL nâng cao</option>
</select>
</div>
<div class="flex-1 min-w-[150px]">
<label class="block text-[11px] font-bold text-slate-500 uppercase mb-1" for="adm-status-sel">Trạng thái sinh viên</label>
<select aria-label="Tất cả" class="w-full h-9 px-3 rounded-lg border border-slate-300 text-xs bg-slate-50" id="adm-status-sel">
<option>Tất cả</option>
<option>Chỉ hiển thị cảnh báo &amp; cấm thi</option>
<option>Bình thường (≥ 85%)</option>
</select>
</div>
</div>
<!-- High-Risk Students List Table -->
<div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
<div class="p-4 border-b border-slate-200 flex items-center justify-between">
<div>
<h3 class="font-bold text-slate-900 text-sm">Danh sách sinh viên vi phạm / chạm ngưỡng cấm thi</h3>
<p class="text-xs text-slate-500 mt-0.5">Xác định theo quy chế đào tạo tín chỉ HUIT mục 6.8</p>
</div>
<button class="h-9 px-3.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold flex items-center gap-1.5 transition-colors" onclick="alert('Đã gửi thông báo cảnh báo chuyên cần tới 18 sinh viên qua email &amp; ứng dụng!');">
<span class="material-symbols-outlined text-[16px]">campaign</span>
<span>Gửi cảnh báo hàng loạt</span>
</button>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
<th class="py-3 px-4">MSSV</th>
<th class="py-3 px-4">Họ và tên</th>
<th class="py-3 px-4">Lớp HP</th>
<th class="py-3 px-4">Giảng viên</th>
<th class="py-3 px-4 text-center">Buổi vắng / Tổng</th>
<th class="py-3 px-4 text-center">Tỷ lệ vắng</th>
<th class="py-3 px-4 text-center">Phân loại</th>
<th class="py-3 px-4 text-center">Thao tác</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
<tr class="bg-red-50/30 hover:bg-red-50/50 transition-colors">
<td class="py-3 px-4 font-mono font-bold text-slate-900">2001210888</td>
<td class="py-3 px-4 font-bold text-slate-900">Phạm Văn Hùng</td>
<td class="py-3 px-4">241_71ITEC3201_01</td>
<td class="py-3 px-4">TS. Trần Hoàng Nam</td>
<td class="py-3 px-4 text-center font-bold text-red-700">3 / 12 buổi</td>
<td class="py-3 px-4 text-center font-extrabold text-red-700">25.0%</td>
<td class="py-3 px-4 text-center">
<span class="px-2 py-0.5 rounded font-extrabold bg-red-100 text-red-800 border border-red-300">CẤM THI</span>
</td>
<td class="py-3 px-4 text-center">
<button class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-[11px]" onclick="openAdjustModal('Phạm Văn Hùng', '2001210888')">
                Điều chỉnh
              </button>
</td>
</tr>
<tr class="bg-amber-50/20 hover:bg-amber-50/40 transition-colors">
<td class="py-3 px-4 font-mono font-bold text-slate-900">2001210123</td>
<td class="py-3 px-4 font-bold text-slate-900">Nguyễn Văn An</td>
<td class="py-3 px-4">241_71ITEC3201_01</td>
<td class="py-3 px-4">TS. Trần Hoàng Nam</td>
<td class="py-3 px-4 text-center font-bold text-amber-700">2 / 12 buổi</td>
<td class="py-3 px-4 text-center font-extrabold text-amber-700">16.7%</td>
<td class="py-3 px-4 text-center">
<span class="px-2 py-0.5 rounded font-extrabold bg-amber-100 text-amber-800 border border-amber-300">CẢNH BÁO</span>
</td>
<td class="py-3 px-4 text-center">
<button class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-[11px]" onclick="openAdjustModal('Nguyễn Văn An', '2001210123')">
                Điều chỉnh
              </button>
</td>
</tr>
</tbody>
</table>
</div>
</div>
<!-- Modal Adjust Attendance with Mandatory Reason -->
<div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden" id="adjustModal">
<div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4">
<div class="flex items-center justify-between border-b border-slate-100 pb-3">
<h3 class="font-bold text-slate-900 text-sm">Điều chỉnh bản ghi chuyên cần sinh viên</h3>
<button class="text-slate-500 hover:text-slate-600" onclick="closeAdjustModal()">
<span class="material-symbols-outlined text-[20px]">close</span>
</button>
</div>
<div class="space-y-3 text-xs">
<div>
<span class="text-slate-500 block">Sinh viên:</span>
<strong class="text-slate-900 text-sm" id="modalStudentName">Nguyễn Văn An (2001210123)</strong>
</div>
<div>
<label class="block font-semibold text-slate-700 mb-1" for="modal-session-sel">Buổi học cần điều chỉnh</label>
<select aria-label="Buổi 4 (26/09/2026) - Đang ghi nhận: Vắng không phép" class="w-full h-9 px-3 rounded-lg border border-slate-300 bg-slate-50" id="modal-session-sel">
<option>Buổi 4 (26/09/2026) - Đang ghi nhận: Vắng không phép</option>
</select>
</div>
<div>
<label class="block font-semibold text-slate-700 mb-1" for="modal-target-status">Trạng thái mới</label>
<select aria-label="Vắng có phép (Đã nộp giấy xác nhận bệnh viện)" class="w-full h-9 px-3 rounded-lg border border-slate-300 bg-slate-50" id="modal-target-status">
<option>Vắng có phép (Đã nộp giấy xác nhận bệnh viện)</option>
<option>Có mặt (Điểm danh bù)</option>
</select>
</div>
<div>
<label class="block font-semibold text-slate-700 mb-1" for="modal-adjust-reason">Lý do điều chỉnh (Bắt buộc ghi audit log) *</label>
<textarea class="w-full p-2.5 rounded-lg border border-slate-300 bg-slate-50 text-xs" id="modal-adjust-reason" placeholder="Nhập căn cứ, số giấy tờ hoặc chỉ đạo từ Ban chủ nhiệm khoa..." required="" rows="3"></textarea>
</div>
</div>
<div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
<button class="h-9 px-4 rounded-lg border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-50" onclick="closeAdjustModal()">
          Hủy
        </button>
<button class="h-9 px-4 rounded-lg bg-primary hover:bg-primary-hover text-white font-bold text-xs" onclick="confirmAdjust()">
          Lưu thay đổi &amp; Ghi log
        </button>
</div>
</div>
</div>
</div>
<script>
  function openAdjustModal(name, mssv) {
    document.getElementById('modalStudentName').innerText = `${name} (${mssv})`;
    document.getElementById('adjustModal').classList.remove('hidden');
  }
  function closeAdjustModal() {
    document.getElementById('adjustModal').classList.add('hidden');
  }
  function confirmAdjust() {
    const reason = document.getElementById('modal-adjust-reason').value;
    if (!reason) {
      alert('Vui lòng nhập lý do điều chỉnh bắt buộc!');
      return;
    }
    alert('Đã cập nhật bản ghi chuyên cần và ghi nhận vào Nhật ký kiểm toán hệ thống (SYS-03).');
    closeAdjustModal();
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
