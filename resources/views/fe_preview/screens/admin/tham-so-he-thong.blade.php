@extends('fe_preview.layouts.admin')
@section('page_title', 'Cấu hình Tham số Hệ thống (SYS - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Cấu hình Tham số Hệ thống (SYS - HUIT Học Vụ</title>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/tham-so-he-thong']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">tune</span>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Cấu hình Tham số Hệ thống (SYS</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Cấu hình Tham số Hệ thống (SYS</div>
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
<div class="max-w-[1440px] mx-auto space-y-6 pb-24">
<!-- Breadcrumb Navigation -->
<nav class="flex items-center gap-2 text-body-sm font-body-sm text-on-surface-variant">
<a class="hover:text-primary transition-colors flex items-center gap-1" href="#">
<span class="material-symbols-outlined text-[16px]">home</span>
<span>Trang chủ</span>
</a>
<span class="text-outline">/</span>
<a class="hover:text-primary transition-colors" href="#">Quản trị hệ thống</a>
<span class="text-outline">/</span>
<span class="text-primary font-semibold">Tham số hệ thống</span>
</nav>
<!-- Page Header + Primary Actions -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-2 border-b border-outline-variant">
<div>
<div class="flex items-center gap-2.5">
<h2 class="font-headline-lg text-headline-lg text-on-surface font-bold">Tham số hệ thống</h2>
</div>
<p class="font-body-md text-body-md text-on-surface-variant mt-1">
              Quản lý thông tin nhà trường, thiết lập năm học - học kỳ hiện hành và các quy định vận hành hệ thống
            </p>
</div>
<div class="flex items-center gap-3 shrink-0">
<button class="h-10 px-4 bg-surface-container-lowest border border-outline-variant hover:border-outline text-on-surface font-label-lg text-label-lg rounded-lg flex items-center gap-2 transition-colors shadow-xs" type="button">
<span class="material-symbols-outlined text-[18px] text-outline">restart_alt</span>
<span>Khôi phục mặc định</span>
</button>
<button class="h-10 px-5 bg-primary hover:bg-primary-container active:scale-[0.99] text-white font-label-lg text-label-lg rounded-lg flex items-center gap-2 shadow-sm transition-all" type="button">
<span class="material-symbols-outlined text-[18px]">save</span>
<span>Lưu thay đổi</span>
</button>
</div>
</div>
<!-- System Warning Banner Alert -->
<div class="p-4 rounded-xl bg-amber-50/80 border border-amber-200/90 text-amber-950 flex items-start gap-3.5 shadow-xs">
<span class="material-symbols-outlined text-[22px] text-amber-700 shrink-0 mt-0.5">warning</span>
<div class="flex-1">
<p class="font-label-lg text-label-lg font-semibold text-amber-900">Lưu ý quan trọng từ Ban Quản trị</p>
<p class="font-body-md text-body-md text-amber-800 mt-0.5">
              Thay đổi tham số hệ thống sẽ ảnh hưởng tức thì đến toàn bộ phiên làm việc của giảng viên và sinh viên. Vui lòng kiểm tra kỹ lưỡng các giá trị thời gian và trạng thái cổng học vụ trước khi áp dụng.
            </p>
</div>
<span class="px-2 py-0.5 rounded bg-amber-200/60 text-amber-900 font-label-sm text-label-sm font-bold uppercase tracking-wider">Hiệu lực ngay</span>
</div>
<!-- =================================================================== -->
<!-- FORM SECTION CARDS                                                  -->
<!-- =================================================================== -->
<!-- Card 1. Thông tin đơn vị đào tạo -->
<section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-xs">
<div class="flex items-center justify-between pb-4 mb-6 border-b border-surface-container">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-lg bg-surface-container-low text-primary flex items-center justify-center font-semibold">
<span class="material-symbols-outlined text-[20px]">account_balance</span>
</div>
<div>
<h3 class="font-headline-sm text-headline-sm text-on-surface font-semibold">1. Thông tin đơn vị đào tạo</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant">Thông tin hành chính dùng cho văn bằng, chứng chỉ và các mẫu in học vụ</p>
</div>
</div>
<span class="text-outline font-label-sm text-label-sm">Đã đồng bộ với Bộ GD&amp;ĐT</span>
</div>
<!-- Logo Upload Sub-section -->
<div class="flex flex-col sm:flex-row sm:items-center gap-5 p-4 mb-6 rounded-lg bg-surface-container-low border border-outline-variant">
<div class="w-20 h-20 rounded-lg bg-surface-container-lowest border border-outline-variant flex items-center justify-center overflow-hidden shrink-0 shadow-xs p-2">
<div class="w-full h-full rounded flex flex-col items-center justify-center bg-primary/10 text-primary border border-primary/20">
<span class="font-headline-sm text-headline-sm font-extrabold tracking-tight">HUIT</span>
<span class="text-[9px] font-bold text-outline uppercase tracking-wider mt-0.5">Học Vụ</span>
</div>
</div>
<div class="flex-1 space-y-1">
<h4 class="font-label-lg text-label-lg text-on-surface font-semibold">Biểu trưng số hóa chính thức (Logo HUIT)</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant">
                Định dạng hỗ trợ: PNG, SVG trong suốt. Kích thước tối ưu 512x512px (Dung lượng tối đa 2.5MB).
              </p>
<div class="pt-2 flex items-center gap-3">
<button class="h-8 px-3 rounded-lg bg-surface-container-lowest border border-outline-variant hover:border-outline text-on-surface font-label-md text-label-md flex items-center gap-1.5 transition-colors" type="button">
<span class="material-symbols-outlined text-[16px] text-primary">upload</span>
<span>Thay đổi logo</span>
</button>
<button class="h-8 px-2 text-on-surface-variant hover:text-error text-label-md font-label-md transition-colors" type="button">
                  Xóa logo hiện tại
                </button>
</div>
</div>
</div>
<!-- Form Fields: 2 Columns Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
<!-- Tên trường (Tiếng Việt) -->
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1.5">Tên trường (Tiếng Việt) <span class="text-error">*</span></label>
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary" type="text" value="Trường Đại học Công Thương TP. Hồ Chí Minh"/>
</div>
<!-- Tên trường (Tiếng Anh) -->
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1.5">Tên trường (Tiếng Anh) <span class="text-error">*</span></label>
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary" type="text" value="Ho Chi Minh City University of Industry and Trade"/>
</div>
<!-- Tên viết tắt -->
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1.5">Tên viết tắt</label>
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary" type="text" value="HUIT"/>
</div>
<!-- Mã trường -->
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1.5">Mã trường tuyển sinh</label>
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary" type="text" value="DCT"/>
</div>
<!-- Địa chỉ trụ sở (Span 2 col) -->
<div class="md:col-span-2">
<label class="block font-label-md text-label-md text-on-surface mb-1.5">Địa chỉ trụ sở chính</label>
<div class="relative">
<span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-outline">
<span class="material-symbols-outlined text-[18px]">location_on</span>
</span>
<input aria-label="Ô nhập liệu" class="w-full h-10 pl-9 pr-3 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary" type="text" value="140 Lê Trọng Tấn, Phường Tây Thạnh, Quận Tân Phú, TP. Hồ Chí Minh"/>
</div>
</div>
<!-- Số điện thoại / Hotline -->
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1.5">Số điện thoại / Hotline</label>
<div class="relative">
<span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-outline">
<span class="material-symbols-outlined text-[18px]">call</span>
</span>
<input aria-label="Ô nhập liệu" class="w-full h-10 pl-9 pr-3 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary" type="text" value="(028) 38161673"/>
</div>
</div>
<!-- Email liên hệ đào tạo -->
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1.5">Email liên hệ đào tạo</label>
<div class="relative">
<span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-outline">
<span class="material-symbols-outlined text-[18px]">mail</span>
</span>
<input aria-label="Ô nhập liệu" class="w-full h-10 pl-9 pr-3 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary" type="email" value="daotao@huit.edu.vn"/>
</div>
</div>
<!-- Website chính thức (Span 2 col) -->
<div class="md:col-span-2">
<label class="block font-label-md text-label-md text-on-surface mb-1.5">Website chính thức</label>
<div class="relative">
<span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-outline">
<span class="material-symbols-outlined text-[18px]">language</span>
</span>
<input aria-label="Ô nhập liệu" class="w-full h-10 pl-9 pr-3 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary" type="url" value="https://huit.edu.vn"/>
</div>
</div>
</div>
</section>
<!-- Card 2. Cấu hình năm học & học kỳ hiện hành -->
<section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-xs">
<div class="flex items-center justify-between pb-4 mb-6 border-b border-surface-container">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-lg bg-surface-container-low text-primary flex items-center justify-center font-semibold">
<span class="material-symbols-outlined text-[20px]">calendar_today</span>
</div>
<div>
<h3 class="font-headline-sm text-headline-sm text-on-surface font-semibold">2. Cấu hình năm học &amp; học kỳ hiện hành</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant">Xác lập ngữ cảnh thời gian mặc định cho các hoạt động đăng ký tín chỉ và chấm điểm</p>
</div>
</div>
<span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-label-sm text-label-sm font-semibold flex items-center gap-1.5">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
              Đang trong kỳ học
            </span>
</div>
<!-- Top Row: 3 Columns -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
<!-- Năm học hiện hành -->
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1.5">Năm học hiện hành <span class="text-error">*</span></label>
<div class="relative">
<select aria-label="2026 - 2027" class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary appearance-none">
<option selected="">2026 - 2027</option>
<option>2023 - 2024</option>
<option>2025 - 2026</option>
</select>
<span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-outline">
<span class="material-symbols-outlined text-[20px]">expand_more</span>
</span>
</div>
</div>
<!-- Học kỳ hiện hành -->
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1.5">Học kỳ hiện hành <span class="text-error">*</span></label>
<div class="relative">
<select aria-label="Học kỳ 1" class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary appearance-none">
<option selected="">Học kỳ 1</option>
<option>Học kỳ 2</option>
<option>Học kỳ hè (Học kỳ phụ)</option>
</select>
<span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-outline">
<span class="material-symbols-outlined text-[20px]">expand_more</span>
</span>
</div>
</div>
<!-- Trạng thái cổng học vụ (Toggle Card) -->
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1.5">Trạng thái cổng học vụ</label>
<div class="h-10 px-3.5 bg-emerald-50 border border-emerald-200 rounded-lg flex items-center justify-between">
<span class="font-label-md text-label-md text-emerald-800 font-semibold flex items-center gap-2">
<span class="material-symbols-outlined text-[18px] text-emerald-700">lock_open</span>
                  Đang mở đợt đăng ký tín chỉ
                </span>
<!-- Switch Input -->
<label class="relative inline-flex items-center cursor-pointer">
<input aria-label="Chọn dòng" checked="" class="sr-only peer" type="checkbox"/>
<div class="w-10 h-5 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
</label>
</div>
</div>
</div>
<!-- Bottom Row: 2 Columns Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-3 border-t border-surface-container">
<!-- Ngày bắt đầu học kỳ -->
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1.5">Ngày bắt đầu học kỳ</label>
<div class="relative">
<span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-outline">
<span class="material-symbols-outlined text-[18px]">event</span>
</span>
<input aria-label="Ô nhập liệu" class="w-full h-10 pl-9 pr-3 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary" type="text" value="09/09/2026"/>
</div>
</div>
<!-- Ngày kết thúc học kỳ -->
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1.5">Ngày kết thúc học kỳ</label>
<div class="relative">
<span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-outline">
<span class="material-symbols-outlined text-[18px]">event</span>
</span>
<input aria-label="Ô nhập liệu" class="w-full h-10 pl-9 pr-3 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary" type="text" value="19/01/2027"/>
</div>
</div>
<!-- Hạn nộp học vụ học kỳ -->
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1.5">Hạn nộp học vụ học kỳ</label>
<div class="relative">
<span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-outline">
<span class="material-symbols-outlined text-[18px]">event_busy</span>
</span>
<input aria-label="Ô nhập liệu" class="w-full h-10 pl-9 pr-3 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary" type="text" value="15/10/2026"/>
</div>
</div>
<!-- Số tín chỉ tối đa SV được đăng ký/học kỳ -->
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1.5">Số tín chỉ tối đa SV được đăng ký/học kỳ</label>
<div class="relative">
<span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-outline">
<span class="material-symbols-outlined text-[18px]">format_list_numbered</span>
</span>
<input aria-label="Ô nhập liệu" class="w-full h-10 pl-9 pr-3 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary" type="text" value="24 tín chỉ"/>
</div>
</div>
</div>
</section>
<!-- Card 3. Quy ước hiển thị & thời gian -->
<section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-xs">
<div class="flex items-center justify-between pb-4 mb-6 border-b border-surface-container">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-lg bg-surface-container-low text-primary flex items-center justify-center font-semibold">
<span class="material-symbols-outlined text-[20px]">schedule</span>
</div>
<div>
<h3 class="font-headline-sm text-headline-sm text-on-surface font-semibold">3. Quy ước hiển thị &amp; thời gian</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant">Thiết lập format ngày tháng, múi giờ và bản địa hóa toàn bộ báo cáo</p>
</div>
</div>
<span class="material-symbols-outlined text-outline">public</span>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
<!-- Định dạng ngày -->
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1.5">Định dạng ngày</label>
<div class="relative">
<select aria-label="DD/MM/YYYY" class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary appearance-none">
<option selected="">DD/MM/YYYY</option>
<option>YYYY-MM-DD</option>
<option>MM/DD/YYYY</option>
</select>
<span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-outline">
<span class="material-symbols-outlined text-[18px]">expand_more</span>
</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-1 text-[11px]">Ví dụ: 25/12/2026</p>
</div>
<!-- Định dạng giờ -->
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1.5">Định dạng giờ</label>
<div class="relative">
<select aria-label="24 giờ (HH:mm:ss)" class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary appearance-none">
<option selected="">24 giờ (HH:mm:ss)</option>
<option>12 giờ (hh:mm:ss A)</option>
</select>
<span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-outline">
<span class="material-symbols-outlined text-[18px]">expand_more</span>
</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-1 text-[11px]">Ví dụ: 14:30:00</p>
</div>
<!-- Múi giờ hệ thống -->
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1.5">Múi giờ hệ thống</label>
<div class="relative">
<select aria-label="(GMT+07:00) Bangkok, Hanoi, Jakarta" class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary appearance-none">
<option selected="">(GMT+07:00) Bangkok, Hanoi, Jakarta</option>
<option>(GMT+08:00) Singapore, Beijing</option>
<option>(GMT+00:00) UTC</option>
</select>
<span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-outline">
<span class="material-symbols-outlined text-[18px]">expand_more</span>
</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-1 text-[11px]">ICT (Indochina Time)</p>
</div>
<!-- Ngôn ngữ mặc định -->
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1.5">Ngôn ngữ mặc định</label>
<div class="relative">
<select aria-label="Tiếng Việt (vi-VN)" class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary appearance-none">
<option selected="">Tiếng Việt (vi-VN)</option>
<option>English (en-US)</option>
</select>
<span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-outline">
<span class="material-symbols-outlined text-[18px]">expand_more</span>
</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-1 text-[11px]">Hỗ trợ song ngữ trên cổng SV</p>
</div>
</div>
</section>
<!-- Card 4. Chính sách bảo mật & phiên đăng nhập -->
<section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-xs">
<div class="flex items-center justify-between pb-4 mb-6 border-b border-surface-container">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-lg bg-surface-container-low text-primary flex items-center justify-center font-semibold">
<span class="material-symbols-outlined text-[20px]">security</span>
</div>
<div>
<h3 class="font-headline-sm text-headline-sm text-on-surface font-semibold">4. Chính sách bảo mật &amp; phiên đăng nhập</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant">Bảo vệ phiên truy cập phòng thi trắc nghiệm, nhập điểm và kiểm toán hệ thống</p>
</div>
</div>
<span class="material-symbols-outlined text-outline">lock</span>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
<!-- Thời gian chờ hết phiên (Session Timeout) -->
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1.5">Thời gian chờ hết phiên (Session Timeout)</label>
<div class="relative">
<span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-outline">
<span class="material-symbols-outlined text-[18px]">timer</span>
</span>
<input aria-label="Ô nhập liệu" class="w-full h-10 pl-9 pr-3 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary" type="text" value="30 phút"/>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-1 text-[11px]">Tự động ngắt kết nối nếu không có hoạt động</p>
</div>
<!-- Số lần đăng nhập sai tối đa -->
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1.5">Số lần đăng nhập sai tối đa</label>
<div class="relative">
<span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-outline">
<span class="material-symbols-outlined text-[18px]">pin</span>
</span>
<input aria-label="Ô nhập liệu" class="w-full h-10 pl-9 pr-3 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary" type="text" value="5 lần"/>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-1 text-[11px]">Tạm khóa tài khoản trong 15 phút sau đó</p>
</div>
<!-- Bắt buộc 2FA cho quản trị viên -->
<div class="p-3 bg-surface-container-low border border-outline-variant rounded-lg flex items-center justify-between">
<div class="pr-2">
<p class="font-label-md text-label-md text-on-surface font-semibold">Bắt buộc 2FA cho Quản trị viên</p>
<p class="font-body-sm text-body-sm text-on-surface-variant text-[11px]">Sử dụng mã OTP xác thực kép</p>
</div>
<!-- Switch Active Toggle -->
<label class="relative inline-flex items-center cursor-pointer shrink-0">
<input aria-label="Chọn dòng" checked="" class="sr-only peer" type="checkbox"/>
<div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
</label>
</div>
</div>
</section>
<!-- Footer Văn bản Bản quyền -->
<footer class="pt-6 pb-12 text-center border-t border-outline-variant/60 text-body-sm text-on-surface-variant">
<p>© 2026 Trường Đại học Công Thương TP. Hồ Chí Minh (HUIT) - Hệ thống Quản lý Học vụ.</p>
<p class="mt-1 text-[11px] text-outline">Hỗ trợ kỹ thuật: daotao@huit.edu.vn | Hotline: (028) 38161673 | Trung tâm CNTT &amp; Chuyển đổi số</p>
</footer>
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

@endsection
