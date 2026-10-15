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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/nhat-ky-thao-tac']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">history</span>
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
<!-- Breadcrumb Navigation -->
<nav class="flex items-center gap-1.5 text-xs text-text-sub mb-3">
<a class="hover:text-primary transition-colors flex items-center gap-1" href="#">
<span class="material-symbols-outlined text-[15px]">home</span>
<span>Trang chủ</span>
</a>
<span class="text-slate-300">/</span>
<a class="hover:text-primary transition-colors" href="#">Quản trị hệ thống</a>
<span class="text-slate-300">/</span>
<a class="hover:text-primary transition-colors" href="#">Nhật ký thao tác</a>
<span class="text-slate-300">/</span>
<span class="text-text-main font-semibold text-slate-700">Bản ghi #LOG-2024-9841</span>
</nav>
<!-- Page Header with Back Button & Actions -->
<div class="flex items-start justify-between pb-5 border-b border-slate-200 mb-6">
<div class="flex flex-col">
<!-- Back Button Link -->
<a class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:text-primary-dark transition-colors mb-1.5 group" href="#">
<span class="material-symbols-outlined text-[16px] group-hover:-translate-x-0.5 transition-transform">arrow_back</span>
<span>Quay lại danh sách nhật ký</span>
</a>
<!-- Title & Badges -->
<div class="flex items-center gap-3 flex-wrap">
<h1 class="text-2xl font-bold text-text-main tracking-tight font-mono">Chi tiết nhật ký: #LOG-2024-9841</h1>
<!-- Badges -->
<span class="px-2.5 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 text-xs font-semibold rounded-full flex items-center gap-1">
<span class="material-symbols-outlined text-[13px]">edit_note</span>
<span>CẬP NHẬT CẤU HÌNH</span>
</span>
<span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-semibold rounded-full flex items-center gap-1.5">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
<span>Thành công</span>
</span>
</div>
<!-- Subtitle with SHA-256 integrity indicator -->
<p class="text-xs text-text-sub mt-1.5 flex items-center gap-2">
<span>Thời gian ghi nhận: <strong class="text-slate-700 font-medium font-mono">11/10/2026 10:45:12 (GMT+7)</strong></span>
<span class="text-slate-300">•</span>
<span class="inline-flex items-center gap-1 text-slate-700">
<span class="material-symbols-outlined text-[15px] text-emerald-700">verified</span>
<span>Toàn vẹn dữ liệu: <strong class="text-emerald-700 font-medium">Đã xác thực chữ ký SHA-256</strong></span>
</span>
</p>
</div>
<!-- Right Action Buttons -->
<div class="flex items-center gap-2.5 pt-2">
<button class="h-9 px-3.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 rounded-lg text-xs font-medium flex items-center gap-1.5 shadow-sm transition-colors">
<span class="material-symbols-outlined text-[18px] text-slate-500">print</span>
<span>In biên bản kiểm toán</span>
</button>
<button class="h-9 px-3.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 rounded-lg text-xs font-medium flex items-center gap-1.5 shadow-sm transition-colors">
<span class="material-symbols-outlined text-[18px] text-slate-500">data_object</span>
<span>Xuất file JSON Payload</span>
</button>
</div>
</div>
<!-- ================= TOP GRID: 2 INFORMATION CARDS ================= -->
<div class="grid grid-cols-2 gap-6 mb-6">
<!-- Card 1: User & Session Info -->
<div class="bg-white rounded-xl border border-outline p-5 shadow-sm">
<div class="flex items-center justify-between pb-3.5 mb-3.5 border-b border-slate-100">
<div class="flex items-center gap-2">
<div class="w-7 h-7 rounded-lg bg-blue-50 text-primary flex items-center justify-center">
<span class="material-symbols-outlined text-[18px]">person</span>
</div>
<h2 class="text-sm font-bold text-text-main">1. Thông tin người thực hiện &amp; Phiên truy cập</h2>
</div>
<span class="text-[11px] font-mono text-slate-500 bg-slate-50 px-2 py-0.5 rounded border border-slate-100">AUTH: VERIFIED</span>
</div>
<!-- 2-column key-value grid -->
<div class="grid grid-cols-2 gap-x-6 gap-y-3.5 text-xs">
<div>
<span class="text-text-sub block text-[11px] mb-0.5">Họ và tên</span>
<div class="flex items-center gap-2">
<div class="w-6 h-6 rounded-full bg-rose-100 text-rose-700 font-bold text-[10px] flex items-center justify-center">
                    LH
                  </div>
<span class="font-semibold text-text-main">Lê Thị Mai Hương</span>
</div>
</div>
<div>
<span class="text-text-sub block text-[11px] mb-0.5">Email cán bộ</span>
<span class="font-medium text-slate-800">huong.ltm@huit.edu.vn</span>
</div>
<div>
<span class="text-text-sub block text-[11px] mb-0.5">Mã người dùng (User ID)</span>
<span class="font-mono font-semibold text-primary bg-blue-50 px-2 py-0.5 rounded inline-block">CB199412</span>
</div>
<div>
<span class="text-text-sub block text-[11px] mb-0.5">Vai trò hệ thống</span>
<span class="font-medium text-slate-800 inline-flex items-center gap-1">
<span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                  Trưởng phòng Đào tạo (Admin)
                </span>
</div>
<div>
<span class="text-text-sub block text-[11px] mb-0.5">Đơn vị công tác</span>
<span class="font-medium text-slate-800">Phòng Quản lý Đào tạo - HUIT</span>
</div>
<div>
<span class="text-text-sub block text-[11px] mb-0.5">Địa chỉ IP truy cập</span>
<div class="flex items-center gap-1.5">
<span class="font-mono font-medium text-slate-800">115.79.44.18</span>
<span class="text-[10px] bg-slate-100 text-slate-600 px-1.5 py-0.2 rounded font-sans">Mạng nội bộ HUIT</span>
</div>
</div>
<div>
<span class="text-text-sub block text-[11px] mb-0.5">Thiết bị &amp; Trình duyệt</span>
<span class="font-medium text-slate-800 truncate block" title="Chrome 129.0 on macOS (Apple Silicon)">
                  Chrome 129.0 on macOS (Apple Silicon)
                </span>
</div>
<div>
<span class="text-text-sub block text-[11px] mb-0.5">Mã định danh phiên (Session ID)</span>
<span class="font-mono text-slate-500 bg-slate-50 px-2 py-0.5 rounded border border-slate-100 text-[11px] block truncate" title="sess_89f2a41c9b0e12">
                  sess_89f2a41c9b0e12
                </span>
</div>
</div>
</div>
<!-- Card 2: Target Entity & Event Info -->
<div class="bg-white rounded-xl border border-outline p-5 shadow-sm">
<div class="flex items-center justify-between pb-3.5 mb-3.5 border-b border-slate-100">
<div class="flex items-center gap-2">
<div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center">
<span class="material-symbols-outlined text-[18px]">tune</span>
</div>
<h2 class="text-sm font-bold text-text-main">2. Thông tin đối tượng tác động &amp; Sự kiện</h2>
</div>
<span class="text-[11px] font-mono text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100 flex items-center gap-1">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> 2FA TOTP Verified
              </span>
</div>
<!-- 2-column key-value grid -->
<div class="grid grid-cols-2 gap-x-6 gap-y-3.5 text-xs">
<div>
<span class="text-text-sub block text-[11px] mb-0.5">Loại thao tác</span>
<span class="font-semibold text-text-main">UPDATE_CONFIG (Cập nhật tham số)</span>
</div>
<div>
<span class="text-text-sub block text-[11px] mb-0.5">Phân hệ (Module)</span>
<span class="font-medium text-slate-800">SYS - Quản trị hệ thống / Quy chế học vụ</span>
</div>
<div>
<span class="text-text-sub block text-[11px] mb-0.5">Đối tượng tác động</span>
<span class="font-semibold text-slate-900">Cấu hình Quy chế Học vụ Khóa 2024</span>
</div>
<div>
<span class="text-text-sub block text-[11px] mb-0.5">Mã đối tượng (Target ID)</span>
<span class="font-mono font-bold text-primary bg-blue-50 px-2 py-0.5 rounded inline-block">REG-K24-2024</span>
</div>
<div>
<span class="text-text-sub block text-[11px] mb-0.5">HTTP Method &amp; Endpoint</span>
<span class="font-mono text-slate-800 bg-slate-50 px-2 py-0.5 rounded border border-slate-100 block truncate" title="PUT /api/v1/admin/system/regulations/k24">
<strong class="text-blue-700 font-bold">PUT</strong> /api/v1/admin/system/regulations/k24
                </span>
</div>
<div>
<span class="text-text-sub block text-[11px] mb-0.5">Mã phản hồi HTTP</span>
<div class="flex items-center gap-1.5">
<span class="font-mono font-semibold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded">200 OK</span>
<span class="text-[11px] text-slate-500">(Execution time: 84ms)</span>
</div>
</div>
<div>
<span class="text-text-sub block text-[11px] mb-0.5">Mức độ ảnh hưởng</span>
<span class="px-2 py-0.5 bg-rose-50 text-rose-700 border border-rose-200 text-[11px] font-semibold rounded inline-flex items-center gap-1">
<span class="material-symbols-outlined text-[13px]">warning</span>
<span>Tác động cao (Toàn khóa SV)</span>
</span>
</div>
<div>
<span class="text-text-sub block text-[11px] mb-0.5">Xác thực bổ sung (Multi-Factor)</span>
<span class="font-medium text-emerald-700 inline-flex items-center gap-1">
<span class="material-symbols-outlined text-[16px] text-emerald-700">check_circle</span>
<span>2FA TOTP Verified (Chữ ký điện tử)</span>
</span>
</div>
</div>
</div>
</div>
<!-- ================= DIFF VIEWER CARD (BEFORE / AFTER) ================= -->
<div class="bg-white rounded-xl border border-outline p-6 shadow-sm mb-6">
<div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
<div class="flex items-center gap-2.5">
<div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
<span class="material-symbols-outlined text-[20px]">difference</span>
</div>
<div>
<h2 class="text-base font-bold text-text-main">3. So sánh dữ liệu thay đổi (Before / After Data Diff)</h2>
<p class="text-xs text-text-sub mt-0.5">So sánh chi tiết các trường dữ liệu có sự thay đổi giữa phiên bản <strong class="text-slate-700 font-mono">v1.2</strong> và <strong class="text-slate-700 font-mono">v1.3</strong></p>
</div>
</div>
<div class="flex items-center gap-2 text-xs">
<span class="inline-flex items-center gap-1.5 px-2 py-1 bg-slate-100 text-slate-600 rounded text-[11px]">
<span class="w-2 h-2 rounded-full bg-rose-500"></span> Giá trị cũ (Before)
              </span>
<span class="inline-flex items-center gap-1.5 px-2 py-1 bg-slate-100 text-slate-600 rounded text-[11px]">
<span class="w-2 h-2 rounded-full bg-emerald-500"></span> Giá trị mới (After)
              </span>
</div>
</div>
<!-- Diff Data Table -->
<div class="w-full border border-outline rounded-lg overflow-hidden">
<table class="w-full text-left border-collapse text-xs">
<thead>
<tr class="bg-slate-100/90 text-slate-600 border-b border-outline uppercase font-semibold text-[11px] tracking-wider select-none">
<th class="py-3 px-4 w-[32%]">Trường dữ liệu (Field)</th>
<th class="py-3 px-4 w-[28%]">Giá trị trước (Before / Cũ)</th>
<th class="py-3 px-4 w-[28%]">Giá trị sau (After / Mới)</th>
<th class="py-3 px-4 w-[12%] text-center">Trạng thái</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100">
<!-- Row 1: max_credits_normal -->
<tr class="hover:bg-slate-50/70 transition-colors">
<td class="py-3.5 px-4">
<span class="font-mono font-medium text-slate-900 block">max_credits_normal</span>
<span class="text-[11px] text-text-sub">Tín chỉ tối đa kỳ chính (Bình thường)</span>
</td>
<td class="py-3.5 px-4 font-mono">
<span class="inline-block px-2.5 py-1 rounded bg-rose-50 text-rose-800 line-through border border-rose-200">
                      20 tín chỉ
                    </span>
</td>
<td class="py-3.5 px-4 font-mono">
<span class="inline-block px-2.5 py-1 rounded bg-emerald-50 text-emerald-800 font-semibold border border-emerald-200">
                      24 tín chỉ
                    </span>
</td>
<td class="py-3.5 px-4 text-center">
<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                      Thay đổi
                    </span>
</td>
</tr>
<!-- Row 2: max_credits_honor -->
<tr class="hover:bg-slate-50/70 transition-colors">
<td class="py-3.5 px-4">
<span class="font-mono font-medium text-slate-900 block">max_credits_honor</span>
<span class="text-[11px] text-text-sub">Tín chỉ tối đa kỳ chính (Khá / Giỏi)</span>
</td>
<td class="py-3.5 px-4 font-mono">
<span class="inline-block px-2.5 py-1 rounded bg-rose-50 text-rose-800 line-through border border-rose-200">
                      24 tín chỉ
                    </span>
</td>
<td class="py-3.5 px-4 font-mono">
<span class="inline-block px-2.5 py-1 rounded bg-emerald-50 text-emerald-800 font-semibold border border-emerald-200">
                      28 tín chỉ
                    </span>
</td>
<td class="py-3.5 px-4 text-center">
<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                      Thay đổi
                    </span>
</td>
</tr>
<!-- Row 3: gpa_warning_level_1 -->
<tr class="hover:bg-slate-50/70 transition-colors">
<td class="py-3.5 px-4">
<span class="font-mono font-medium text-slate-900 block">gpa_warning_level_1</span>
<span class="text-[11px] text-text-sub">Ngưỡng GPA cảnh báo học vụ HK lần 1</span>
</td>
<td class="py-3.5 px-4 font-mono">
<span class="inline-block px-2.5 py-1 rounded bg-rose-50 text-rose-800 line-through border border-rose-200">
                      0.80 / 4.0
                    </span>
</td>
<td class="py-3.5 px-4 font-mono">
<span class="inline-block px-2.5 py-1 rounded bg-emerald-50 text-emerald-800 font-semibold border border-emerald-200">
                      1.00 / 4.0
                    </span>
</td>
<td class="py-3.5 px-4 text-center">
<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200" title="Điều chỉnh siết chặt quy định">
                      Thay đổi (Siết chặt ngưỡng)
                    </span>
</td>
</tr>
<!-- Row 4: max_failed_credits (Unchanged) -->
<tr class="hover:bg-slate-50/70 transition-colors bg-slate-50/30">
<td class="py-3.5 px-4">
<span class="font-mono font-medium text-slate-700 block">max_failed_credits</span>
<span class="text-[11px] text-text-sub">Tín chỉ F tối đa tích lũy</span>
</td>
<td class="py-3.5 px-4 font-mono text-slate-600">
<span class="px-2 py-1">24 tín chỉ</span>
</td>
<td class="py-3.5 px-4 font-mono text-slate-600">
<span class="px-2 py-1">24 tín chỉ</span>
</td>
<td class="py-3.5 px-4 text-center">
<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-500">
                      Không đổi
                    </span>
</td>
</tr>
<!-- Row 5: updated_by_reason (New Addition) -->
<tr class="hover:bg-slate-50/70 transition-colors">
<td class="py-3.5 px-4">
<span class="font-mono font-medium text-slate-900 block">updated_by_reason</span>
<span class="text-[11px] text-text-sub">Lý do cập nhật &amp; Phê duyệt</span>
</td>
<td class="py-3.5 px-4 text-slate-500 italic">
<span class="px-2 py-1">(Trống)</span>
</td>
<td class="py-3.5 px-4">
<span class="inline-block px-2.5 py-1 rounded bg-emerald-50 text-emerald-800 border border-emerald-200 font-sans leading-relaxed">
                      "Cập nhật định mức tín chỉ theo đề xuất Hội đồng Đào tạo ngày 10/10/2026"
                    </span>
</td>
<td class="py-3.5 px-4 text-center">
<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                      Thêm mới
                    </span>
</td>
</tr>
</tbody>
</table>
</div>
</div>
<!-- ================= RAW JSON PAYLOAD & AUDIT SECURITY ACCORDION ================= -->
<div class="bg-white rounded-xl border border-outline overflow-hidden shadow-sm mb-6">
<div class="p-4 bg-slate-50 border-b border-outline flex items-center justify-between cursor-pointer hover:bg-slate-100/80 transition-colors">
<div class="flex items-center gap-2.5">
<span class="material-symbols-outlined text-[20px] text-primary">terminal</span>
<span class="text-xs font-bold text-text-main">Xem chi tiết JSON Payload &amp; Security Hash (Dành cho kiểm toán viên)</span>
</div>
<div class="flex items-center gap-3">
<span class="text-[11px] font-mono text-slate-500">Payload size: 1.28 KB</span>
<span class="material-symbols-outlined text-[18px] text-slate-500">expand_less</span>
</div>
</div>
<div class="p-5 bg-slate-900 text-sky-400 font-mono text-xs overflow-x-auto leading-relaxed select-text">
<pre class="font-mono"><span class="text-slate-500">// Bản ghi kiểm toán điện tử SHA-256 HUIT Audit Log Engine v2.4</span>
{
  <span class="text-amber-400">"log_id"</span>: <span class="text-emerald-400">"LOG-2024-9841"</span>,
  <span class="text-amber-400">"timestamp"</span>: <span class="text-emerald-400">"2024-10-11T10:45:12.842+07:00"</span>,
  <span class="text-amber-400">"event_type"</span>: <span class="text-emerald-400">"SYS_REGULATION_UPDATE"</span>,
  <span class="text-amber-400">"actor"</span>: {
    <span class="text-amber-400">"user_id"</span>: <span class="text-emerald-400">"CB199412"</span>,
    <span class="text-amber-400">"username"</span>: <span class="text-emerald-400">"huong.ltm"</span>,
    <span class="text-amber-400">"email"</span>: <span class="text-emerald-400">"huong.ltm@huit.edu.vn"</span>,
    <span class="text-amber-400">"role"</span>: <span class="text-emerald-400">"ROLE_ADMIN_DEPT_HEAD"</span>,
    <span class="text-amber-400">"ip_address"</span>: <span class="text-emerald-400">"115.79.44.18"</span>,
    <span class="text-amber-400">"user_agent"</span>: <span class="text-emerald-400">"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Chrome/129.0.0.0"</span>,
    <span class="text-amber-400">"mfa_verified"</span>: <span class="text-purple-400">true</span>
  },
  <span class="text-amber-400">"target"</span>: {
    <span class="text-amber-400">"module"</span>: <span class="text-emerald-400">"SYS_REGULATION"</span>,
    <span class="text-amber-400">"target_id"</span>: <span class="text-emerald-400">"REG-K24-2024"</span>,
    <span class="text-amber-400">"title"</span>: <span class="text-emerald-400">"Quy chế học vụ - Khóa 2024 (K24)"</span>
  },
  <span class="text-amber-400">"diff"</span>: {
    <span class="text-amber-400">"max_credits_normal"</span>: { <span class="text-amber-400">"before"</span>: <span class="text-orange-400">20</span>, <span class="text-amber-400">"after"</span>: <span class="text-orange-400">24</span> },
    <span class="text-amber-400">"max_credits_honor"</span>:  { <span class="text-amber-400">"before"</span>: <span class="text-orange-400">24</span>, <span class="text-amber-400">"after"</span>: <span class="text-orange-400">28</span> },
    <span class="text-amber-400">"gpa_warning_level_1"</span>: { <span class="text-amber-400">"before"</span>: <span class="text-orange-400">0.80</span>, <span class="text-amber-400">"after"</span>: <span class="text-orange-400">1.00</span> },
    <span class="text-amber-400">"updated_by_reason"</span>:   { <span class="text-amber-400">"before"</span>: <span class="text-purple-400">null</span>, <span class="text-amber-400">"after"</span>: <span class="text-emerald-400">"Cập nhật định mức tín chỉ theo đề xuất Hội đồng Đào tạo ngày 10/10/2026"</span> }
  },
  <span class="text-amber-400">"hash_sha256"</span>: <span class="text-emerald-400">"8f3b4c91d8e27a6fb37d8912c4019aeb9f270921dc8b41724083aef1b83d1c08"</span>,
  <span class="text-amber-400">"signature_status"</span>: <span class="text-emerald-400">"VERIFIED"</span>
}</pre>
</div>
</div>
<!-- System Audit Standard Footer Banner -->
<div class="p-4 rounded-xl bg-blue-50/60 border border-blue-100 flex items-center justify-between text-xs text-slate-600 mb-8">
<div class="flex items-center gap-2.5">
<span class="material-symbols-outlined text-[20px] text-primary">security</span>
<span>
<strong>Quy chuẩn lưu vết &amp; Toàn vẹn dữ liệu:</strong> Bản ghi nhật ký thao tác này được ghi nhận tức thời và ký băm SHA-256 bất biến. Bản ghi được lưu trữ bảo mật trong vòng 36 tháng theo quy định kiểm định chất lượng đào tạo &amp; ATTT của Trường Đại học Công Thương TP. Hồ Chí Minh.
            </span>
</div>
<span class="font-mono text-slate-500 shrink-0 ml-4">ISO/IEC 27001 COMPLIANT</span>
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
