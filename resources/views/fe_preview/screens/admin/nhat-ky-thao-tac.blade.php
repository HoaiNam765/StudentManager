@extends('fe_preview.layouts.admin')
@section('page_title', 'Admin - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Admin - HUIT Học Vụ</title>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/diem-danh-chuyen-can']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">fact_check</span>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/hoc-vu-tot-nghiep']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">school</span>
<span class="truncate">Học vụ &amp; Tốt nghiệp</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/xep-lich-tu-dong']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">auto_fix_high</span>
<span class="truncate">Xếp lịch tự động</span>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/don-tu']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">mark_email_unread</span>
<span class="truncate">Hộp thư đơn từ</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/hoc-bong-ren-luyen-ky-luat']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">military_tech</span>
<span class="truncate">Học bổng &amp; Kỷ luật</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/khao-sat']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">query_stats</span>
<span class="truncate">Quản lý khảo sát</span>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/nhat-ky-thao-tac']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">history</span>
<span class="truncate">Nhật ký thao tác</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/bao-cao']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">analytics</span>
<span class="truncate">Báo cáo &amp; Thống kê</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/sao-luu-khoa-so-import']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">settings_backup_restore</span>
<span class="truncate">Sao lưu &amp; Khóa sổ</span>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Admin</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Admin</div>
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
<!-- Breadcrumbs -->
<nav class="flex items-center gap-2 text-xs text-slate-500">
<a class="hover:text-slate-800 transition-colors flex items-center gap-1" href="#">
<span class="material-symbols-outlined text-[15px]">home</span>
<span>Trang chủ</span>
</a>
<span class="text-slate-300">/</span>
<span class="hover:text-slate-800 cursor-pointer">Quản trị hệ thống</span>
<span class="text-slate-300">/</span>
<span class="text-slate-800 font-semibold">Nhật ký thao tác</span>
</nav>
<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
<div>
<div class="flex items-center gap-2.5">
<h1 class="text-2xl font-bold text-slate-900 tracking-tight">Nhật ký thao tác</h1>
<span class="px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Ghi nhận thời gian thực
            </span>
</div>
<p class="text-xs text-slate-500 mt-1">
            Theo dõi và kiểm toán toàn bộ lịch sử thay đổi dữ liệu, cấu hình học vụ và các phiên làm việc trong hệ thống
          </p>
</div>
<!-- Top Actions -->
<div class="flex items-center gap-2.5 flex-shrink-0">
<button class="px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 rounded-lg text-xs font-medium inline-flex items-center gap-1.5 shadow-2xs transition-colors">
<span class="material-symbols-outlined text-[17px] text-slate-500">file_download</span>
<span>Xuất file Excel / CSV</span>
</button>
<button class="px-3 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 rounded-lg text-xs font-medium inline-flex items-center gap-1.5 shadow-2xs transition-colors">
<span class="material-symbols-outlined text-[17px] text-slate-500">refresh</span>
<span>Làm mới dữ liệu</span>
</button>
</div>
</div>
<!-- Summary Metric Cards (4 cards in grid-cols-4) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
<!-- Metric Card 1 -->
<div class="bg-white p-[18px] rounded-xl border border-[#E2E8F0] shadow-2xs flex items-center justify-between">
<div>
<p class="text-xs font-medium text-slate-500">Tổng lượt thao tác hôm nay</p>
<p class="text-2xl font-bold text-slate-900 mt-1">1,248</p>
<p class="text-[11px] font-medium text-emerald-700 mt-1 flex items-center gap-0.5">
<span class="material-symbols-outlined text-[14px]">trending_up</span>
<span>+12% so với hôm qua</span>
</p>
</div>
<div class="w-11 h-11 rounded-xl bg-blue-50 text-primary flex items-center justify-center">
<span class="material-symbols-outlined text-[22px]">history_edu</span>
</div>
</div>
<!-- Metric Card 2 -->
<div class="bg-white p-[18px] rounded-xl border border-[#E2E8F0] shadow-2xs flex items-center justify-between">
<div>
<p class="text-xs font-medium text-slate-500">Thao tác cập nhật / ghi đè</p>
<p class="text-2xl font-bold text-slate-900 mt-1">342</p>
<p class="text-[11px] text-slate-500 mt-1 flex items-center gap-1">
<span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> 27.4% trên tổng lưu lượng
            </p>
</div>
<div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
<span class="material-symbols-outlined text-[22px]">edit_note</span>
</div>
</div>
<!-- Metric Card 3 -->
<div class="bg-white p-[18px] rounded-xl border border-[#E2E8F0] shadow-2xs flex items-center justify-between">
<div>
<p class="text-xs font-medium text-slate-500">Thao tác cảnh báo / thất bại</p>
<p class="text-2xl font-bold text-red-700 mt-1">6</p>
<p class="text-[11px] font-semibold text-red-700 mt-1 flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]">warning</span>
<span>Cần kiểm tra ngay</span>
</p>
</div>
<div class="w-11 h-11 rounded-xl bg-red-50 text-red-500 flex items-center justify-center">
<span class="material-symbols-outlined text-[22px]">error_outline</span>
</div>
</div>
<!-- Metric Card 4 -->
<div class="bg-white p-[18px] rounded-xl border border-[#E2E8F0] shadow-2xs flex items-center justify-between">
<div>
<p class="text-xs font-medium text-slate-500">Quản trị viên đang hoạt động</p>
<p class="text-2xl font-bold text-slate-900 mt-1">14</p>
<p class="text-[11px] font-medium text-emerald-700 mt-1 flex items-center gap-1">
<span class="w-2 h-2 rounded-full bg-emerald-500"></span> 100% tài khoản đã xác thực 2FA
            </p>
</div>
<div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
<span class="material-symbols-outlined text-[22px]">manage_accounts</span>
</div>
</div>
</div>
<!-- Filter & Search Toolbar -->
<div class="bg-white p-4 rounded-xl border border-[#E2E8F0] shadow-2xs space-y-3">
<!-- Main Filter Row -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
<!-- Search Input (span 3 cols) -->
<div class="lg:col-span-3 relative">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-[18px]">search</span>
<input aria-label="Tìm theo Người dùng, IP, Mã bản ghi..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" placeholder="Tìm theo Người dùng, IP, Mã bản ghi..." type="text"/>
</div>
<!-- Dropdown: Loại thao tác (span 2 cols) -->
<div class="lg:col-span-2 relative">
<select aria-label="Tất cả thao tác" class="w-full appearance-none pl-3 pr-8 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary cursor-pointer">
<option value="">Tất cả thao tác</option>
<option value="create">Tạo mới (INSERT)</option>
<option value="update">Cập nhật (UPDATE)</option>
<option value="delete">Xóa bản ghi (DELETE)</option>
<option value="login">Đăng nhập (AUTH)</option>
<option value="grade">Duyệt &amp; Khóa điểm</option>
<option value="lock">Khóa tài khoản</option>
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-500 text-[16px] pointer-events-none">expand_more</span>
</div>
<!-- Dropdown: Đối tượng / Module (span 2 cols) -->
<div class="lg:col-span-2 relative">
<select aria-label="Tất cả phân hệ" class="w-full appearance-none pl-3 pr-8 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary cursor-pointer">
<option value="">Tất cả phân hệ</option>
<option value="sys">Tham số hệ thống</option>
<option value="reg">Quy chế học vụ</option>
<option value="sub">Điểm học phần</option>
<option value="enroll">Đăng ký học phần</option>
<option value="user">Người dùng &amp; Tài khoản</option>
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-500 text-[16px] pointer-events-none">expand_more</span>
</div>
<!-- Dropdown: Khoảng thời gian (span 2 cols) -->
<div class="lg:col-span-2 relative">
<select aria-label="Hôm nay, 11/10/2026" class="w-full appearance-none pl-3 pr-8 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary cursor-pointer">
<option value="today">Hôm nay, 11/10/2026</option>
<option value="7d">7 ngày qua</option>
<option value="30d">30 ngày qua</option>
<option value="custom">Tùy chọn ngày...</option>
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-500 text-[16px] pointer-events-none">date_range</span>
</div>
<!-- Dropdown: Kết quả (span 1 col) -->
<div class="lg:col-span-1 relative">
<select aria-label="Kết quả" class="w-full appearance-none pl-2.5 pr-7 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary cursor-pointer">
<option value="">Kết quả</option>
<option value="success">Thành công</option>
<option value="failed">Thất bại</option>
</select>
<span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-slate-500 text-[15px] pointer-events-none">expand_more</span>
</div>
<!-- Action buttons (span 2 cols) -->
<div class="lg:col-span-2 flex items-center justify-end gap-2">
<button class="px-2.5 py-2 text-xs font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors flex items-center gap-1">
<span class="material-symbols-outlined text-[16px]">tune</span>
<span>Lọc nâng cao</span>
</button>
<button class="p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors" title="Đặt lại bộ lọc">
<span class="material-symbols-outlined text-[17px]">restart_alt</span>
</button>
</div>
</div>
<!-- Active Filter Tags -->
<div class="flex items-center gap-2 pt-1 border-t border-slate-100 text-[11px] text-slate-500">
<span class="font-medium text-slate-600">Đang lọc theo:</span>
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-50 text-primary border border-blue-200 font-medium">
            Thời gian: Hôm nay
            <button class="hover:text-blue-800"><span class="material-symbols-outlined text-[12px]">close</span></button>
</span>
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
            Trạng thái: Tất cả
          </span>
<span class="text-slate-500 text-[10px] ml-auto">Tìm thấy <strong>1,248</strong> bản ghi phù hợp</span>
</div>
</div>
<!-- Data Table Container -->
<div class="bg-white rounded-xl border border-[#E2E8F0] shadow-2xs overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<!-- Headers -->
<thead>
<tr class="bg-[#F1F5F9] border-b border-[#E2E8F0] text-[11px] font-semibold text-slate-600 uppercase tracking-wider">
<th class="py-3 px-4 w-[140px]" scope="col">MÃ BẢN GHI</th>
<th class="py-3 px-4 w-[160px]" scope="col">THỜI GIAN</th>
<th class="py-3 px-4" scope="col">NGƯỜI THỰC HIỆN</th>
<th class="py-3 px-4 w-[150px]" scope="col">VAI TRÒ</th>
<th class="py-3 px-4 w-[130px]" scope="col">LOẠI THAO TÁC</th>
<th class="py-3 px-4" scope="col">ĐỐI TƯỢNG TÁC ĐỘNG</th>
<th class="py-3 px-4 w-[160px]" scope="col">ĐỊA CHỈ IP</th>
<th class="py-3 px-4 w-[130px]" scope="col">KẾT QUẢ</th>
<th class="py-3 px-4 w-[90px] text-right" scope="col">HÀNH ĐỘNG</th>
</tr>
</thead>
<!-- Rows -->
<tbody class="divide-y divide-[#E2E8F0] text-xs text-slate-700">
<!-- ROW 1 -->
<tr class="h-12 hover:bg-[#F8FAFC] transition-colors">
<td class="py-3 px-4 font-mono font-semibold text-primary">#LOG-2024-9841</td>
<td class="py-3 px-4 text-slate-600 font-mono text-[11.5px]">
                  11/10/2026 <span class="text-slate-500">10:45:12</span>
</td>
<td class="py-3 px-4">
<div class="flex items-center gap-2">
<div class="w-7 h-7 rounded-full bg-rose-100 text-rose-700 font-semibold text-[11px] flex items-center justify-center flex-shrink-0">
                      LH
                    </div>
<div class="min-w-0">
<p class="font-medium text-slate-900 truncate">Lê Thị Mai Hương</p>
<p class="text-[11px] text-slate-500 truncate">huong.ltm@huit.edu.vn</p>
</div>
</div>
</td>
<td class="py-3 px-4">
<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-[#EEF2FD] text-primary border border-blue-200">
                    Trưởng phòng Đào tạo
                  </span>
</td>
<td class="py-3 px-4">
<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-[#FEF3C7] text-[#B45309] border border-amber-200">
                    CẬP NHẬT
                  </span>
</td>
<td class="py-3 px-4">
<div class="leading-tight">
<span class="font-medium text-slate-900">Quy chế học vụ - Khóa 2024</span>
<span class="ml-1 text-[11px] text-slate-500 font-mono">(Mã: REG-K24)</span>
</div>
</td>
<td class="py-3 px-4 text-slate-600 font-mono text-[11px]">
                  115.79.44.18 <span class="text-slate-500">(TP.HCM)</span>
</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-[#DCFCE7] text-[#15803D] border border-emerald-200">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Thành công
                  </span>
</td>
<td class="py-3 px-4 text-right">
<a class="text-primary hover:text-primary-hover font-semibold text-xs inline-flex items-center gap-0.5 transition-colors" href="#">
                    Chi tiết <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
</a>
</td>
</tr>
<!-- ROW 2 -->
<tr class="h-12 hover:bg-[#F8FAFC] transition-colors">
<td class="py-3 px-4 font-mono font-semibold text-primary">#LOG-2024-9840</td>
<td class="py-3 px-4 text-slate-600 font-mono text-[11.5px]">
                  11/10/2026 <span class="text-slate-500">10:32:05</span>
</td>
<td class="py-3 px-4">
<div class="flex items-center gap-2">
<div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 font-semibold text-[11px] flex items-center justify-center flex-shrink-0">
                      TT
                    </div>
<div class="min-w-0">
<p class="font-medium text-slate-900 truncate">TS. Trần Anh Tuấn</p>
<p class="text-[11px] text-slate-500 truncate">tuan.ta@huit.edu.vn</p>
</div>
</div>
</td>
<td class="py-3 px-4">
<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-[#F1F5F9] text-slate-600 border border-slate-200">
                    Giảng viên
                  </span>
</td>
<td class="py-3 px-4">
<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-[#DBEAFE] text-[#1D4ED8] border border-blue-200">
                    NHẬP ĐIỂM
                  </span>
</td>
<td class="py-3 px-4">
<div class="leading-tight">
<span class="font-medium text-slate-900">Bảng điểm lớp 12DHTH01 - Lập trình Web</span>
<span class="ml-1 text-[11px] text-slate-500 font-mono">(Mã: LHP-0824)</span>
</div>
</td>
<td class="py-3 px-4 text-slate-600 font-mono text-[11px]">
                  14.232.208.62 <span class="text-slate-500">(Ký túc xá)</span>
</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-[#DCFCE7] text-[#15803D] border border-emerald-200">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Thành công
                  </span>
</td>
<td class="py-3 px-4 text-right">
<a class="text-primary hover:text-primary-hover font-semibold text-xs inline-flex items-center gap-0.5 transition-colors" href="#">
                    Chi tiết <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
</a>
</td>
</tr>
<!-- ROW 3 -->
<tr class="h-12 hover:bg-[#F8FAFC] transition-colors">
<td class="py-3 px-4 font-mono font-semibold text-primary">#LOG-2024-9839</td>
<td class="py-3 px-4 text-slate-600 font-mono text-[11.5px]">
                  11/10/2026 <span class="text-slate-500">09:58:44</span>
</td>
<td class="py-3 px-4">
<div class="flex items-center gap-2">
<div class="w-7 h-7 rounded-full bg-slate-100 text-slate-600 font-semibold text-[11px] flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined text-[15px]">smart_toy</span>
</div>
<div class="min-w-0">
<p class="font-medium text-slate-900 truncate">Hệ thống tự động (Cron Job)</p>
<p class="text-[11px] text-slate-500 truncate">huit-scheduler@sys.internal</p>
</div>
</div>
</td>
<td class="py-3 px-4">
<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-[#F1F5F9] text-slate-500 border border-slate-200">
                    System Engine
                  </span>
</td>
<td class="py-3 px-4">
<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-[#DCFCE7] text-[#15803D] border border-emerald-200">
                    GẠCH NỢ
                  </span>
</td>
<td class="py-3 px-4">
<div class="leading-tight">
<span class="font-medium text-slate-900">Thanh toán học vụ - 348 giao dịch</span>
<span class="ml-1 text-[11px] text-slate-500 font-mono">(Mã: FEE-SYNC)</span>
</div>
</td>
<td class="py-3 px-4 text-slate-600 font-mono text-[11px]">
                  127.0.0.1 <span class="text-slate-500">(Internal)</span>
</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-[#DCFCE7] text-[#15803D] border border-emerald-200">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Thành công
                  </span>
</td>
<td class="py-3 px-4 text-right">
<a class="text-primary hover:text-primary-hover font-semibold text-xs inline-flex items-center gap-0.5 transition-colors" href="#">
                    Chi tiết <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
</a>
</td>
</tr>
<!-- ROW 4 -->
<tr class="h-12 hover:bg-[#F8FAFC] transition-colors">
<td class="py-3 px-4 font-mono font-semibold text-primary">#LOG-2024-9838</td>
<td class="py-3 px-4 text-slate-600 font-mono text-[11.5px]">
                  11/10/2026 <span class="text-slate-500">09:15:30</span>
</td>
<td class="py-3 px-4">
<div class="flex items-center gap-2">
<div class="w-7 h-7 rounded-full bg-amber-100 text-amber-700 font-semibold text-[11px] flex items-center justify-center flex-shrink-0">
                      NT
                    </div>
<div class="min-w-0">
<p class="font-medium text-slate-900 truncate">Nguyễn Văn Tuấn</p>
<p class="text-[11px] text-slate-500 truncate">tuan.nv@huit.edu.vn</p>
</div>
</div>
</td>
<td class="py-3 px-4">
<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-[#FEE2E2] text-[#DC2626] border border-red-200">
                    Quản trị hệ thống
                  </span>
</td>
<td class="py-3 px-4">
<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-[#FEF3C7] text-[#B45309] border border-amber-200">
                    ĐỔI THAM SỐ
                  </span>
</td>
<td class="py-3 px-4">
<div class="leading-tight">
<span class="font-medium text-slate-900">Thời gian hết hạn phiên đăng nhập (30p -&gt; 45p)</span>
<span class="ml-1 text-[11px] text-slate-500 font-mono">(Mã: SYS_PARAM)</span>
</div>
</td>
<td class="py-3 px-4 text-slate-600 font-mono text-[11px]">
                  115.79.44.18 <span class="text-slate-500">(Phòng CNTT)</span>
</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-[#DCFCE7] text-[#15803D] border border-emerald-200">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Thành công
                  </span>
</td>
<td class="py-3 px-4 text-right">
<a class="text-primary hover:text-primary-hover font-semibold text-xs inline-flex items-center gap-0.5 transition-colors" href="#">
                    Chi tiết <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
</a>
</td>
</tr>
<!-- ROW 5 -->
<tr class="h-12 bg-red-50/30 hover:bg-red-50/60 transition-colors">
<td class="py-3 px-4 font-mono font-semibold text-primary">#LOG-2024-9837</td>
<td class="py-3 px-4 text-slate-600 font-mono text-[11.5px]">
                  11/10/2026 <span class="text-slate-500">08:42:19</span>
</td>
<td class="py-3 px-4">
<div class="flex items-center gap-2">
<div class="w-7 h-7 rounded-full bg-slate-200 text-slate-600 font-semibold text-[11px] flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined text-[16px]">person_off</span>
</div>
<div class="min-w-0">
<p class="font-medium text-slate-900 truncate">Vô danh</p>
<p class="text-[11px] text-slate-500 truncate">IP: 171.244.12.8</p>
</div>
</div>
</td>
<td class="py-3 px-4">
<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-[#F1F5F9] text-slate-500 border border-slate-200">
                    Khách vãng lai
                  </span>
</td>
<td class="py-3 px-4">
<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-[#FEE2E2] text-[#DC2626] border border-red-200">
                    ĐĂNG NHẬP SAI
                  </span>
</td>
<td class="py-3 px-4">
<div class="leading-tight">
<span class="font-medium text-slate-900">Tài khoản admin@huit.edu.vn</span>
<span class="ml-1 text-[11px] text-red-500">(Sai MK lần 3)</span>
</div>
</td>
<td class="py-3 px-4 text-slate-600 font-mono text-[11px]">
                  171.244.12.8 <span class="text-slate-500">(Hà Nội)</span>
</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-[#FEE2E2] text-[#DC2626] border border-red-200">
<span class="w-1.5 h-1.5 rounded-full bg-red-600"></span> Thất bại
                  </span>
</td>
<td class="py-3 px-4 text-right">
<a class="text-primary hover:text-primary-hover font-semibold text-xs inline-flex items-center gap-0.5 transition-colors" href="#">
                    Chi tiết <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
</a>
</td>
</tr>
</tbody>
</table>
</div>
<!-- Table Pagination Footer -->
<div class="py-3.5 px-4 border-t border-[#E2E8F0] bg-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-slate-600">
<div class="flex items-center gap-2">
<span>Hiển thị <strong>1 - 5</strong> trong tổng số <strong>1,248</strong> nhật ký thao tác</span>
<span class="text-slate-300">|</span>
<label class="flex items-center gap-1.5 text-slate-500">
<span>Số dòng/trang:</span>
<select aria-label="5" class="px-2 py-1 bg-slate-50 border border-slate-200 rounded text-xs text-slate-700 focus:outline-none">
<option>5</option>
<option>10</option>
<option>25</option>
<option>50</option>
</select>
</label>
</div>
<!-- Pagination Controls -->
<div class="flex items-center gap-1">
<button class="px-2.5 py-1.5 rounded border border-slate-200 text-slate-500 hover:bg-slate-50 disabled:opacity-40 transition-colors text-xs font-medium" disabled="">
              Trước
            </button>
<button class="w-8 h-8 rounded bg-primary text-white font-semibold text-xs flex items-center justify-center shadow-2xs">
              1
            </button>
<button class="w-8 h-8 rounded border border-slate-200 text-slate-700 hover:bg-slate-50 font-medium text-xs flex items-center justify-center transition-colors">
              2
            </button>
<button class="w-8 h-8 rounded border border-slate-200 text-slate-700 hover:bg-slate-50 font-medium text-xs flex items-center justify-center transition-colors">
              3
            </button>
<span class="px-1 text-slate-500 font-mono text-xs">...</span>
<button class="w-8 h-8 rounded border border-slate-200 text-slate-700 hover:bg-slate-50 font-medium text-xs flex items-center justify-center transition-colors">
              250
            </button>
<button class="px-2.5 py-1.5 rounded border border-slate-200 text-slate-700 hover:bg-slate-50 transition-colors text-xs font-medium">
              Sau
            </button>
</div>
</div>
</div>
<!-- System Security & Audit Compliance Notice -->
<div class="p-3.5 bg-blue-50/60 border border-blue-200/80 rounded-xl flex items-start gap-3 text-xs text-slate-600">
<span class="material-symbols-outlined text-primary text-[19px] mt-0.5 flex-shrink-0">verified_user</span>
<div class="leading-relaxed">
<span class="font-semibold text-slate-800">Quy chuẩn lưu vết &amp; Toàn vẹn dữ liệu:</span>
          Tất cả các bản ghi nhật ký thao tác (Audit Logs) được tự động ghi nhận tức thời, mã hóa chuẩn SHA-256 và lưu trữ bất biến trong vòng 36 tháng theo quy định kiểm định chất lượng đào tạo &amp; an toàn thông tin của Trường Đại học Công Thương TP. Hồ Chí Minh.
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
