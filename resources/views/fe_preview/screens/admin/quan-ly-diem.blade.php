@extends('fe_preview.layouts.admin')
@section('page_title', 'Quản lý Điểm & Sổ điểm Lớp học phần - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Quản lý Điểm &amp; Sổ điểm Lớp học phần - HUIT Học Vụ</title>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/quan-ly-diem']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">grading</span>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Quản lý Điểm &amp; Sổ điểm Lớp học phần</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Quản lý Điểm &amp; Sổ điểm Lớp học phần</div>
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
<div class="text-[11px] text-slate-500">nguoidung015@example.test</div>
</div>
</div>
</div>
</header>
<!-- MAIN BODY CONTENT -->
<main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
<!-- Top Administrative App Bar -->
<header class="h-16 border-b border-outline-variant/30 bg-surface-container-lowest px-6 flex items-center justify-between shadow-xs shrink-0">
<!-- Breadcrumb & Path -->
<div class="flex items-center gap-2 font-label-md text-label-md">
<span class="text-on-surface-variant">Đào tạo</span>
<span class="material-symbols-outlined text-outline text-[16px]" data-icon="chevron_right">chevron_right</span>
<span class="text-primary font-bold">Quản lý Điểm học phần</span>
<span class="ml-3 px-2 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed text-[11px] font-semibold border border-primary/20">
            HK1 (2026-2027)
          </span>
</div>
<!-- Top Trailing Controls -->
<div class="flex items-center gap-3">
<!-- Học kỳ dropdown selector -->
<div class="relative">
<select aria-label="Học kỳ 1 (Năm học 2026-2027)" class="appearance-none bg-surface border border-outline-variant/60 rounded-lg pl-3 pr-8 py-1.5 font-label-md text-label-md text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/25 cursor-pointer">
<option selected="">Học kỳ 1 (Năm học 2026-2027)</option>
<option>Học kỳ 2 (Năm học 2026-2027)</option>
<option>Học kỳ hè (Năm học 2025-2026)</option>
</select>
<span class="material-symbols-outlined pointer-events-none absolute right-2 top-2 text-[16px] text-outline" data-icon="expand_more">expand_more</span>
</div>
<!-- Nút Làm mới -->
<button class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-outline-variant/60 bg-surface-container-lowest text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-label-md text-label-md transition-all shadow-xs">
<span class="material-symbols-outlined text-[18px]" data-icon="refresh">refresh</span>
<span>Làm mới</span>
</button>
<!-- Nút Xuất báo cáo tổng hợp -->
<button class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-primary text-white font-label-md text-label-md hover:bg-primary-container shadow-xs transition-all active:scale-[0.98]">
<span class="material-symbols-outlined text-[18px]" data-icon="download">download</span>
<span>Xuất báo cáo tổng hợp</span>
</button>
<div class="h-6 w-px bg-outline-variant/50 mx-1"></div>
<!-- Icon actions -->
<button class="p-2 rounded-lg text-outline hover:text-on-surface hover:bg-surface-container-low relative" title="Thông báo">
<span class="material-symbols-outlined text-[20px]" data-icon="notifications">notifications</span>
<span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-error ring-2 ring-surface-container-lowest"></span>
</button>
<button class="p-2 rounded-lg text-outline hover:text-on-surface hover:bg-surface-container-low" title="Trợ giúp">
<span class="material-symbols-outlined text-[20px]" data-icon="help">help</span>
</button>
</div>
</header>
<!-- Main Scrollable Content Area -->
<div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar">
<!-- 3. KPI STATS CARDS (TIẾN ĐỘ NỘP ĐIỂM TOÀN TRƯỜNG) -->
<section class="grid grid-cols-4 gap-4">
<!-- KPI Card 1 -->
<div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant/40 shadow-xs flex items-start justify-between">
<div class="space-y-1">
<p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Tổng lớp học phần</p>
<h3 class="font-headline-lg text-headline-lg font-bold text-on-surface">1.240 <span class="text-body-sm font-normal text-on-surface-variant">lớp</span></h3>
<div class="flex items-center gap-1.5 font-label-sm text-label-sm text-emerald-700">
<span class="material-symbols-outlined text-[16px]" data-icon="trending_up">trending_up</span>
<span>+4.2% so với HK trước</span>
</div>
</div>
<div class="p-2.5 rounded-lg bg-surface-container text-primary">
<span class="material-symbols-outlined text-[24px]" data-icon="description">description</span>
</div>
</div>
<!-- KPI Card 2 -->
<div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant/40 shadow-xs flex items-start justify-between">
<div class="space-y-1">
<p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Đã khóa sổ điểm</p>
<h3 class="font-headline-lg text-headline-lg font-bold text-emerald-800">856 <span class="text-body-sm font-normal text-on-surface-variant">(69%)</span></h3>
<div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-800 font-label-sm text-[11px] font-semibold border border-emerald-200">
<span class="material-symbols-outlined text-[13px]" data-icon="check_circle">check_circle</span>
<span>Đã khóa an toàn</span>
</div>
</div>
<div class="p-2.5 rounded-lg bg-emerald-50 text-emerald-700">
<span class="material-symbols-outlined text-[24px]" data-icon="lock">lock</span>
</div>
</div>
<!-- KPI Card 3 -->
<div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant/40 shadow-xs flex items-start justify-between">
<div class="space-y-1">
<p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Đã nộp chờ khóa</p>
<h3 class="font-headline-lg text-headline-lg font-bold text-amber-800">214 <span class="text-body-sm font-normal text-on-surface-variant">(17%)</span></h3>
<div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 text-amber-800 font-label-sm text-[11px] font-semibold border border-amber-200">
<span class="material-symbols-outlined text-[13px]" data-icon="pending">pending</span>
<span>Cần phê duyệt</span>
</div>
</div>
<div class="p-2.5 rounded-lg bg-amber-50 text-amber-700">
<span class="material-symbols-outlined text-[24px]" data-icon="notification_important">notification_important</span>
</div>
</div>
<!-- KPI Card 4 -->
<div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant/40 shadow-xs flex items-start justify-between">
<div class="space-y-1">
<p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Chưa nộp / Đang nhập</p>
<h3 class="font-headline-lg text-headline-lg font-bold text-rose-800">170 <span class="text-body-sm font-normal text-on-surface-variant">(14%)</span></h3>
<div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-rose-50 text-rose-800 font-label-sm text-[11px] font-semibold border border-rose-200">
<span class="material-symbols-outlined text-[13px]" data-icon="warning">warning</span>
<span>Cảnh báo đôn đốc</span>
</div>
</div>
<div class="p-2.5 rounded-lg bg-rose-50 text-rose-700">
<span class="material-symbols-outlined text-[24px]" data-icon="timer">timer</span>
</div>
</div>
</section>
<!-- 4. MAIN DATA TABLE MODULE: BẢNG DANH SÁCH LỚP HỌC PHẦN -->
<section class="bg-surface-container-lowest rounded-xl border border-outline-variant/40 shadow-xs overflow-hidden">
<!-- Table Toolbar & Filter -->
<div class="p-4 border-b border-outline-variant/30 flex flex-wrap items-center justify-between gap-4 bg-surface-container-lowest">
<!-- Left: Search Box & Faculty Filter -->
<div class="flex items-center gap-3 flex-1 min-w-[500px]">
<!-- Search Input -->
<div class="relative flex-1">
<span class="material-symbols-outlined absolute left-3 top-2.5 text-outline text-[18px]" data-icon="search">search</span>
<input aria-label="Tìm mã lớp, tên học phần, giảng viên..." class="w-full pl-9 pr-3 py-2 bg-surface-container-low/50 border border-outline-variant/50 rounded-lg font-body-md text-body-md text-on-surface placeholder-outline focus:bg-surface-container-lowest focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" placeholder="Tìm mã lớp, tên học phần, giảng viên..." type="text"/>
</div>
<!-- Lọc Khoa -->
<div class="w-60">
<select aria-label="Khoa Công nghệ Thông tin" class="w-full px-3 py-2 bg-surface-container-low/50 border border-outline-variant/50 rounded-lg font-body-md text-body-md text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/20">
<option selected="">Khoa Công nghệ Thông tin</option>
<option>Khoa Thực phẩm</option>
<option>Khoa Quản trị Kinh doanh</option>
<option>Khoa Ngoại ngữ</option>
<option>Khoa Điện - Điện tử</option>
</select>
</div>
<!-- Lọc Trạng thái điểm -->
<div class="w-48">
<select aria-label="Tất cả trạng thái" class="w-full px-3 py-2 bg-surface-container-low/50 border border-outline-variant/50 rounded-lg font-body-md text-body-md text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/20">
<option value="all">Tất cả trạng thái</option>
<option value="locked">Đã khóa sổ điểm</option>
<option value="submitted">Đã nộp - Chờ khóa</option>
<option value="draft">Đang nhập điểm</option>
<option value="overdue">Chưa nhập - Quá hạn</option>
</select>
</div>
</div>
<!-- Right: Quick Action Controls -->
<div class="flex items-center gap-2">
<button class="flex items-center gap-1.5 px-3 py-2 rounded-lg border border-outline-variant/50 hover:bg-surface-container-low text-on-surface-variant font-label-md text-label-md transition-colors" title="Bộ lọc nâng cao">
<span class="material-symbols-outlined text-[18px]" data-icon="tune">tune</span>
<span>Bộ lọc</span>
</button>
<button class="flex items-center gap-1.5 px-3 py-2 rounded-lg border border-outline-variant/50 hover:bg-surface-container-low text-on-surface-variant font-label-md text-label-md transition-colors">
<span class="material-symbols-outlined text-[18px]" data-icon="table_rows">table_rows</span>
<span>Tùy biến cột</span>
</button>
</div>
</div>
<!-- Academic Data Table with Horizontal Scroll Container -->
<div class="overflow-x-auto custom-scrollbar">
<table class="w-full text-left border-collapse min-w-[1100px]">
<thead>
<tr class="bg-surface-bright border-b border-outline-variant/40 font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">
<th class="py-3 px-4 w-12 text-center sticky left-0 bg-surface-bright z-10">
<input aria-label="Chọn dòng" class="rounded border-outline-variant text-primary focus:ring-primary/20" type="checkbox"/>
</th>
<th class="py-3 px-4 sticky left-12 bg-surface-bright z-10">Mã Lớp Học Phần</th>
<th class="py-3 px-4">Tên Học Phần</th>
<th class="py-3 px-4">Giảng Viên Phụ Trách</th>
<th class="py-3 px-3 text-center">Sĩ Số</th>
<th class="py-3 px-4">Hạn Nộp Điểm</th>
<th class="py-3 px-4">Tiến Độ Nhập</th>
<th class="py-3 px-4">Trạng Thái Sổ Điểm</th>
<th class="py-3 px-4 text-right">Thao Tác Quản Trị</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant/20 font-body-md text-body-md text-on-surface">
<!-- Dòng 1: Lập trình Web (Đã nộp - Chờ khóa) -->
<tr class="hover:bg-surface-container-low/40 transition-colors bg-primary-fixed/10">
<td class="py-3 px-4 text-center sticky left-0 bg-surface-container-lowest group-hover:bg-surface-container-low/40">
<input aria-label="Chọn dòng" checked="" class="rounded border-outline-variant text-primary focus:ring-primary/20" type="checkbox"/>
</td>
<td class="py-3 px-4 sticky left-12 bg-surface-container-lowest font-semibold text-primary">
                    241_71ITEC3201_01
                  </td>
<td class="py-3 px-4">
<div class="font-medium text-on-surface">Lập trình Web</div>
<div class="text-[11px] text-on-surface-variant">3 Tín chỉ (2 LT + 1 TH)</div>
</td>
<td class="py-3 px-4">
<div class="font-medium text-on-surface">TS. Người dùng mẫu 38</div>
<div class="text-[11px] text-on-surface-variant">Bộ môn Công nghệ Phần mềm</div>
</td>
<td class="py-3 px-3 text-center font-semibold">60/60</td>
<td class="py-3 px-4 text-on-surface-variant font-label-md text-label-md">
                    30/11/2026
                  </td>
<td class="py-3 px-4">
<div class="w-36">
<div class="flex justify-between text-[11px] font-medium mb-1">
<span class="text-emerald-700">60/60 SV</span>
<span class="text-emerald-700">100%</span>
</div>
<div class="w-full h-1.5 bg-surface-container rounded-full overflow-hidden">
<div class="h-full bg-emerald-600 rounded-full w-full"></div>
</div>
</div>
</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 font-label-md text-[12px] font-semibold border border-amber-200">
<span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                      Đã nộp - Chờ khóa
                    </span>
</td>
<td class="py-3 px-4 text-right">
<div class="flex items-center justify-end gap-2">
<button class="px-3 py-1.5 rounded-lg bg-primary text-white font-label-md text-label-md hover:bg-primary-container shadow-xs transition-colors flex items-center gap-1">
<span class="material-symbols-outlined text-[16px]" data-icon="visibility">visibility</span>
<span>Xem sổ điểm</span>
</button>
<button class="px-3 py-1.5 rounded-lg border border-emerald-600 text-emerald-800 bg-emerald-50 hover:bg-emerald-100 font-label-md text-label-md transition-colors flex items-center gap-1">
<span class="material-symbols-outlined text-[16px]" data-icon="lock">lock</span>
<span>Khóa sổ điểm</span>
</button>
</div>
</td>
</tr>
<!-- Dòng 2: Cấu trúc dữ liệu & GT (Đã khóa sổ điểm) -->
<tr class="hover:bg-surface-container-low/40 transition-colors">
<td class="py-3 px-4 text-center sticky left-0 bg-surface-container-lowest">
<input aria-label="Chọn dòng" class="rounded border-outline-variant text-primary focus:ring-primary/20" type="checkbox"/>
</td>
<td class="py-3 px-4 sticky left-12 bg-surface-container-lowest font-medium text-on-surface">
                    241_71ITEC2104_02
                  </td>
<td class="py-3 px-4">
<div class="font-medium text-on-surface">Cấu trúc dữ liệu &amp; GT</div>
<div class="text-[11px] text-on-surface-variant">4 Tín chỉ (3 LT + 1 TH)</div>
</td>
<td class="py-3 px-4">
<div class="font-medium text-on-surface">ThS. Người dùng mẫu 11</div>
<div class="text-[11px] text-on-surface-variant">Bộ môn Khoa học Máy tính</div>
</td>
<td class="py-3 px-3 text-center font-semibold">58/60</td>
<td class="py-3 px-4 text-on-surface-variant font-label-md text-label-md">
                    30/11/2026
                  </td>
<td class="py-3 px-4">
<div class="w-36">
<div class="flex justify-between text-[11px] font-medium mb-1">
<span class="text-emerald-700">58/58 SV</span>
<span class="text-emerald-700">100%</span>
</div>
<div class="w-full h-1.5 bg-surface-container rounded-full overflow-hidden">
<div class="h-full bg-emerald-600 rounded-full w-full"></div>
</div>
</div>
</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-label-md text-[12px] font-semibold border border-emerald-200">
<span class="material-symbols-outlined text-[14px]" data-icon="lock">lock</span>
                      Đã khóa sổ điểm
                    </span>
</td>
<td class="py-3 px-4 text-right">
<div class="flex items-center justify-end gap-2">
<button class="px-2.5 py-1.5 rounded-lg border border-outline-variant/60 text-on-surface hover:bg-surface-container-low font-label-md text-label-md transition-colors flex items-center gap-1">
<span class="material-symbols-outlined text-[16px]" data-icon="visibility">visibility</span>
<span>Xem sổ điểm</span>
</button>
<button class="px-2.5 py-1.5 rounded-lg border border-amber-500/50 text-amber-900 bg-amber-50 hover:bg-amber-100 font-label-md text-label-md transition-colors flex items-center gap-1">
<span class="material-symbols-outlined text-[16px]" data-icon="lock_open">lock_open</span>
<span>Mở khóa</span>
</button>
</div>
</td>
</tr>
<!-- Dòng 3: Mạng máy tính (Đang nhập điểm) -->
<tr class="hover:bg-surface-container-low/40 transition-colors">
<td class="py-3 px-4 text-center sticky left-0 bg-surface-container-lowest">
<input aria-label="Chọn dòng" class="rounded border-outline-variant text-primary focus:ring-primary/20" type="checkbox"/>
</td>
<td class="py-3 px-4 sticky left-12 bg-surface-container-lowest font-medium text-on-surface">
                    241_71ITEC3208_01
                  </td>
<td class="py-3 px-4">
<div class="font-medium text-on-surface">Mạng máy tính</div>
<div class="text-[11px] text-on-surface-variant">3 Tín chỉ (2 LT + 1 TH)</div>
</td>
<td class="py-3 px-4">
<div class="font-medium text-on-surface">ThS. Người dùng mẫu 56</div>
<div class="text-[11px] text-on-surface-variant">Bộ môn Mạng &amp; ATTT</div>
</td>
<td class="py-3 px-3 text-center font-semibold">55/60</td>
<td class="py-3 px-4 text-on-surface-variant font-label-md text-label-md">
                    30/11/2026
                  </td>
<td class="py-3 px-4">
<div class="w-36">
<div class="flex justify-between text-[11px] font-medium mb-1">
<span class="text-primary">30/55 SV</span>
<span class="text-primary">54%</span>
</div>
<div class="w-full h-1.5 bg-surface-container rounded-full overflow-hidden">
<div class="h-full bg-primary rounded-full w-[54%]"></div>
</div>
</div>
</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-100 text-blue-800 font-label-md text-[12px] font-semibold border border-blue-200">
<span class="material-symbols-outlined text-[14px]" data-icon="edit_note">edit_note</span>
                      Đang nhập điểm
                    </span>
</td>
<td class="py-3 px-4 text-right">
<div class="flex items-center justify-end gap-2">
<button class="px-2.5 py-1.5 rounded-lg border border-outline-variant/60 text-amber-800 bg-amber-50 hover:bg-amber-100 font-label-md text-label-md transition-colors flex items-center gap-1">
<span class="material-symbols-outlined text-[16px]" data-icon="mail">mail</span>
<span>Nhắc nhở</span>
</button>
<button class="px-2.5 py-1.5 rounded-lg border border-outline-variant/60 text-on-surface hover:bg-surface-container-low font-label-md text-label-md transition-colors flex items-center gap-1">
<span class="material-symbols-outlined text-[16px]" data-icon="read_more">read_more</span>
<span>Xem chi tiết</span>
</button>
</div>
</td>
</tr>
<!-- Dòng 4: Trí tuệ nhân tạo (Chưa nhập điểm - Quá hạn) -->
<tr class="hover:bg-surface-container-low/40 transition-colors bg-rose-50/20">
<td class="py-3 px-4 text-center sticky left-0 bg-surface-container-lowest">
<input aria-label="Chọn dòng" class="rounded border-outline-variant text-primary focus:ring-primary/20" type="checkbox"/>
</td>
<td class="py-3 px-4 sticky left-12 bg-surface-container-lowest font-medium text-rose-700">
                    241_71ITEC3305_01
                  </td>
<td class="py-3 px-4">
<div class="font-medium text-on-surface">Trí tuệ nhân tạo</div>
<div class="text-[11px] text-on-surface-variant">3 Tín chỉ (3 LT + 0 TH)</div>
</td>
<td class="py-3 px-4">
<div class="font-medium text-on-surface">PGS.TS. Người dùng mẫu 49</div>
<div class="text-[11px] text-on-surface-variant">Bộ môn Khoa học Máy tính</div>
</td>
<td class="py-3 px-3 text-center font-semibold">45/60</td>
<td class="py-3 px-4 text-error font-label-md text-label-md font-semibold">
                    25/11/2026 (Quá 5 ngày)
                  </td>
<td class="py-3 px-4">
<div class="w-36">
<div class="flex justify-between text-[11px] font-medium mb-1">
<span class="text-rose-700">0/45 SV</span>
<span class="text-rose-700">0%</span>
</div>
<div class="w-full h-1.5 bg-surface-container rounded-full overflow-hidden">
<div class="h-full bg-rose-600 rounded-full w-0"></div>
</div>
</div>
</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 font-label-md text-[12px] font-semibold border border-rose-200">
<span class="material-symbols-outlined text-[14px]" data-icon="error">error</span>
                      Chưa nhập điểm - Quá hạn
                    </span>
</td>
<td class="py-3 px-4 text-right">
<div class="flex items-center justify-end gap-2">
<button class="px-3 py-1.5 rounded-lg bg-rose-700 text-white font-label-md text-label-md hover:bg-rose-800 transition-colors flex items-center gap-1 shadow-xs">
<span class="material-symbols-outlined text-[16px]" data-icon="warning">warning</span>
<span>Gửi cảnh báo GV</span>
</button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<!-- Table Pagination Footer -->
<div class="p-4 border-t border-outline-variant/30 flex items-center justify-between font-label-md text-label-md text-on-surface-variant bg-surface-bright">
<div class="flex items-center gap-3">
<span>Hiển thị <strong>1 - 4</strong> trong <strong>1.240</strong> lớp học phần</span>
<div class="flex items-center gap-1">
<span>Số dòng:</span>
<select aria-label="10" class="px-2 py-1 bg-surface-container-lowest border border-outline-variant/60 rounded text-on-surface">
<option>10</option>
<option selected="">20</option>
<option>50</option>
</select>
</div>
</div>
<div class="flex items-center gap-1">
<button class="px-2 py-1 rounded border border-outline-variant/50 text-outline hover:bg-surface-container-low disabled:opacity-50" disabled="">
<span class="material-symbols-outlined text-[16px]" data-icon="first_page">first_page</span>
</button>
<button class="px-2 py-1 rounded border border-outline-variant/50 text-outline hover:bg-surface-container-low disabled:opacity-50" disabled="">
<span class="material-symbols-outlined text-[16px]" data-icon="chevron_left">chevron_left</span>
</button>
<span class="px-3 py-1 rounded bg-primary text-white font-bold">1</span>
<button class="px-3 py-1 rounded hover:bg-surface-container-low text-on-surface">2</button>
<button class="px-3 py-1 rounded hover:bg-surface-container-low text-on-surface">3</button>
<span class="px-1 text-outline">...</span>
<button class="px-3 py-1 rounded hover:bg-surface-container-low text-on-surface">62</button>
<button class="px-2 py-1 rounded border border-outline-variant/50 text-on-surface hover:bg-surface-container-low">
<span class="material-symbols-outlined text-[16px]" data-icon="chevron_right">chevron_right</span>
</button>
<button class="px-2 py-1 rounded border border-outline-variant/50 text-on-surface hover:bg-surface-container-low">
<span class="material-symbols-outlined text-[16px]" data-icon="last_page">last_page</span>
</button>
</div>
</div>
</section>
<!-- 5. TRỰC QUAN HÓA SỔ ĐIỂM CHI TIẾT LỚP HỌC PHẦN (PANEL BẬT SẪN MINH HỌA) -->
<section class="bg-surface-container-lowest rounded-xl border border-outline-variant/40 shadow-sm overflow-hidden">
<!-- Drawer Header Bar -->
<div class="p-4 bg-surface-container-low border-b border-outline-variant/40 flex items-center justify-between">
<div class="flex items-center gap-3">
<span class="p-2 rounded-lg bg-primary text-white">
<span class="material-symbols-outlined text-[20px]" data-icon="menu_book">menu_book</span>
</span>
<div>
<div class="flex items-center gap-2">
<h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">
                    Sổ điểm chi tiết: 241_71ITEC3201_01 - Lập trình Web (3 Tín chỉ)
                  </h2>
<span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[11px] font-semibold border border-amber-200">
                    Đã nộp - Chờ Phòng Đào tạo khóa
                  </span>
</div>
<p class="font-label-sm text-label-sm text-on-surface-variant">
                  Giảng viên: TS. Người dùng mẫu 38 • Bộ môn Công nghệ Phần mềm • Ngày nộp điểm: 29/11/2026 16:45
                </p>
</div>
</div>
<!-- Header Action Buttons -->
<div class="flex items-center gap-2">
<button class="px-3 py-1.5 rounded-lg border border-outline-variant/60 bg-surface-container-lowest text-on-surface hover:bg-surface-container-low font-label-md text-label-md flex items-center gap-1.5 transition-colors">
<span class="material-symbols-outlined text-[16px] text-emerald-700" data-icon="file_download">file_download</span>
<span>Xuất Excel .xlsx</span>
</button>
<button class="px-3 py-1.5 rounded-lg border border-outline-variant/60 bg-surface-container-lowest text-on-surface hover:bg-surface-container-low font-label-md text-label-md flex items-center gap-1.5 transition-colors">
<span class="material-symbols-outlined text-[16px] text-primary" data-icon="print">print</span>
<span>In bảng điểm A4</span>
</button>
<button class="px-3 py-1.5 rounded-lg border border-amber-600 text-amber-800 bg-amber-50 hover:bg-amber-100 font-label-md text-label-md flex items-center gap-1.5 transition-colors">
<span class="material-symbols-outlined text-[16px]" data-icon="lock_open">lock_open</span>
<span>Mở khóa cho GV sửa</span>
</button>
<button class="px-4 py-1.5 rounded-lg bg-primary text-white hover:bg-primary-container font-label-md text-label-md flex items-center gap-1.5 transition-all shadow-xs">
<span class="material-symbols-outlined text-[16px]" data-icon="verified">verified</span>
<span>Khóa sổ điểm chính thức</span>
</button>
</div>
</div>
<!-- Formula & Component Weights Notification -->
<div class="px-4 py-2.5 bg-surface-bright border-b border-outline-variant/30 flex items-center justify-between text-on-surface-variant font-label-md text-label-md">
<div class="flex items-center gap-6">
<span><strong>Công thức tính:</strong> TK = Quá trình học tập×0.1 + Bài tập×0.2 + Giữa kỳ×0.2 + Cuối kỳ×0.5</span>
<span class="text-outline">|</span>
<span><strong>Quy chuẩn thang điểm:</strong> ĐH Công Thương TP.HCM (Quyết định số 1428/QĐ-DCT)</span>
</div>
<div class="flex items-center gap-2 text-emerald-700 font-semibold">
<span class="material-symbols-outlined text-[16px]" data-icon="shield">shield</span>
<span>Chữ ký số Giảng viên: HỢP LỆ (SHA-256)</span>
</div>
</div>
<!-- Table of Detailed Student Grades -->
<div class="overflow-x-auto custom-scrollbar">
<table class="w-full text-left border-collapse min-w-[980px]">
<thead>
<tr class="bg-surface-container-low/50 border-b border-outline-variant/40 font-label-sm text-label-sm text-on-surface-variant uppercase">
<th class="py-2.5 px-3 text-center w-12">STT</th>
<th class="py-2.5 px-4 w-32">MSSV</th>
<th class="py-2.5 px-4">Họ và tên</th>
<th class="py-2.5 px-3 text-center bg-blue-50/50">Quá trình học tập (10%)</th>
<th class="py-2.5 px-3 text-center bg-blue-50/50">Bài tập (20%)</th>
<th class="py-2.5 px-3 text-center bg-blue-50/50">Giữa kỳ (20%)</th>
<th class="py-2.5 px-3 text-center bg-blue-50/50 font-bold text-primary">Cuối kỳ (50%)</th>
<th class="py-2.5 px-3 text-center font-bold">Điểm TK (Hệ 10)</th>
<th class="py-2.5 px-3 text-center font-bold">Điểm chữ</th>
<th class="py-2.5 px-3 text-center font-bold">Điểm hệ 4</th>
<th class="py-2.5 px-3 text-center">Đánh giá</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant/20 font-body-sm text-body-sm">
<!-- SV 1 -->
<tr class="hover:bg-surface-container-low/30">
<td class="py-2.5 px-3 text-center font-medium">1</td>
<td class="py-2.5 px-4 font-mono font-medium text-primary">2001210123</td>
<td class="py-2.5 px-4 font-semibold text-on-surface">Người dùng mẫu 25</td>
<td class="py-2.5 px-3 text-center font-medium">9.0</td>
<td class="py-2.5 px-3 text-center font-medium">8.5</td>
<td class="py-2.5 px-3 text-center font-medium">8.0</td>
<td class="py-2.5 px-3 text-center font-semibold text-primary">8.5</td>
<td class="py-2.5 px-3 text-center font-bold text-on-surface">8.5</td>
<td class="py-2.5 px-3 text-center font-bold text-emerald-700">A</td>
<td class="py-2.5 px-3 text-center font-semibold">3.7</td>
<td class="py-2.5 px-3 text-center">
<span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold">Đạt</span>
</td>
</tr>
<!-- SV 2 -->
<tr class="hover:bg-surface-container-low/30">
<td class="py-2.5 px-3 text-center font-medium">2</td>
<td class="py-2.5 px-4 font-mono font-medium text-primary">2001210045</td>
<td class="py-2.5 px-4 font-semibold text-on-surface">Người dùng mẫu 41</td>
<td class="py-2.5 px-3 text-center font-medium">10.0</td>
<td class="py-2.5 px-3 text-center font-medium">9.0</td>
<td class="py-2.5 px-3 text-center font-medium">8.5</td>
<td class="py-2.5 px-3 text-center font-semibold text-primary">9.0</td>
<td class="py-2.5 px-3 text-center font-bold text-on-surface">9.0</td>
<td class="py-2.5 px-3 text-center font-bold text-emerald-700">A+</td>
<td class="py-2.5 px-3 text-center font-semibold">4.0</td>
<td class="py-2.5 px-3 text-center">
<span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold">Đạt</span>
</td>
</tr>
<!-- SV 3 -->
<tr class="hover:bg-surface-container-low/30">
<td class="py-2.5 px-3 text-center font-medium">3</td>
<td class="py-2.5 px-4 font-mono font-medium text-primary">2001210189</td>
<td class="py-2.5 px-4 font-semibold text-on-surface">Người dùng mẫu 07</td>
<td class="py-2.5 px-3 text-center font-medium">8.0</td>
<td class="py-2.5 px-3 text-center font-medium">7.0</td>
<td class="py-2.5 px-3 text-center font-medium">6.5</td>
<td class="py-2.5 px-3 text-center font-semibold text-primary">7.0</td>
<td class="py-2.5 px-3 text-center font-bold text-on-surface">7.0</td>
<td class="py-2.5 px-3 text-center font-bold text-blue-700">B</td>
<td class="py-2.5 px-3 text-center font-semibold">3.0</td>
<td class="py-2.5 px-3 text-center">
<span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold">Đạt</span>
</td>
</tr>
<!-- SV 4 -->
<tr class="hover:bg-surface-container-low/30 bg-rose-50/10">
<td class="py-2.5 px-3 text-center font-medium">4</td>
<td class="py-2.5 px-4 font-mono font-medium text-primary">2001210311</td>
<td class="py-2.5 px-4 font-semibold text-on-surface">Người dùng mẫu 31</td>
<td class="py-2.5 px-3 text-center font-medium">5.0</td>
<td class="py-2.5 px-3 text-center font-medium">4.5</td>
<td class="py-2.5 px-3 text-center font-medium">4.0</td>
<td class="py-2.5 px-3 text-center font-semibold text-primary">3.5</td>
<td class="py-2.5 px-3 text-center font-bold text-rose-700">3.9</td>
<td class="py-2.5 px-3 text-center font-bold text-rose-700">F</td>
<td class="py-2.5 px-3 text-center font-semibold">0.0</td>
<td class="py-2.5 px-3 text-center">
<span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 text-[11px] font-bold">Học lại</span>
</td>
</tr>
</tbody>
</table>
</div>
<div class="p-3 bg-surface-bright text-center text-[12px] text-on-surface-variant border-t border-outline-variant/30">
            Hiển thị 4 trên tổng số 60 sinh viên trong lớp học phần • <a class="text-primary hover:underline font-medium" href="#">Tải xem toàn bộ danh sách 60 sinh viên</a>
</div>
</section>
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
