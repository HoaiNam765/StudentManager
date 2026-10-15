@extends('fe_preview.layouts.admin')
@section('page_title', 'Quản lý Đợt đăng ký học phần - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Quản lý Đợt đăng ký học phần - HUIT Học Vụ</title>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/dot-dang-ky-hoc-phan']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">event_available</span>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Quản lý Đợt đăng ký học phần</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Quản lý Đợt đăng ký học phần</div>
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
<!-- Page Header with Title and Primary Actions -->
<div class="flex items-center justify-between">
<div>
<h1 class="font-headline-xl text-headline-xl font-bold text-on-surface tracking-tight">Quản lý Đợt đăng ký học phần</h1>
<p class="font-body-md text-body-md text-on-surface-variant mt-0.5">Lập kế hoạch, phân bổ chỉ tiêu và kiểm soát các đợt mở cổng đăng ký học phần cho sinh viên toàn trường.</p>
</div>
<div class="flex items-center gap-3">
<button class="h-10 px-4 rounded-lg border border-outline-variant bg-surface-container-lowest hover:bg-surface-container-low text-on-surface font-label-lg text-label-lg flex items-center gap-2 shadow-xs transition-colors">
<span class="material-symbols-outlined text-[18px]">download</span>
<span>Xuất báo cáo đợt</span>
</button>
<button class="h-10 px-4 rounded-lg bg-primary hover:bg-[#1546a3] active:bg-[#0e3582] text-white font-label-lg text-label-lg flex items-center gap-2 shadow-sm transition-all shadow-primary/20">
<span class="material-symbols-outlined text-[18px]">add_circle</span>
<span>+ Tạo đợt đăng ký</span>
</button>
</div>
</div>
<!-- 2. BENTO STATS / 4 THẺ KPI THỐNG KÊ NHANH -->
<section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
<!-- KPI 1: Tổng đợt -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-xs flex flex-col justify-between relative overflow-hidden">
<div class="flex items-start justify-between">
<div>
<p class="font-label-md text-label-md text-on-surface-variant font-medium">Tổng đợt đăng ký</p>
<h3 class="font-display-lg text-headline-xl font-bold text-on-surface mt-1">4 <span class="text-body-md font-normal text-on-surface-variant">đợt</span></h3>
</div>
<div class="w-10 h-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[22px]">event_repeat</span>
</div>
</div>
<div class="mt-4 pt-3 border-t border-[#f1f5f9] flex items-center justify-between font-label-sm text-label-sm text-on-surface-variant">
<span class="flex items-center gap-1 text-emerald-700 font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>1 Mở</span>
<span class="flex items-center gap-1 text-sky-700 font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-sky-600"></span>1 Sắp</span>
<span class="flex items-center gap-1 text-amber-700"><span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>1 Đóng</span>
<span class="flex items-center gap-1 text-purple-700"><span class="w-1.5 h-1.5 rounded-full bg-purple-600"></span>1 Chốt</span>
</div>
</div>
<!-- KPI 2: Sinh viên đã đăng ký -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-xs flex flex-col justify-between">
<div class="flex items-start justify-between">
<div>
<p class="font-label-md text-label-md text-on-surface-variant font-medium">Sinh viên đã đăng ký</p>
<h3 class="font-display-lg text-headline-xl font-bold text-on-surface mt-1">14.820 <span class="text-body-md font-normal text-on-surface-variant">/ 18.500 SV</span></h3>
</div>
<div class="w-10 h-10 rounded-lg bg-secondary-container/60 text-primary flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[22px]">how_to_reg</span>
</div>
</div>
<div class="mt-4 pt-3 border-t border-[#f1f5f9]">
<div class="flex items-center justify-between font-label-sm text-label-sm mb-1.5">
<span class="text-on-surface-variant">Tỷ lệ hoàn thành</span>
<span class="font-bold text-primary">80.1%</span>
</div>
<div class="w-full h-1.5 bg-[#eff4ff] rounded-full overflow-hidden">
<div class="h-full bg-primary rounded-full transition-all duration-500" style="width: 80.1%"></div>
</div>
</div>
</div>
<!-- KPI 3: Tín chỉ thành công -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-xs flex flex-col justify-between">
<div class="flex items-start justify-between">
<div>
<p class="font-label-md text-label-md text-on-surface-variant font-medium">Tín chỉ đăng ký thành công</p>
<h3 class="font-display-lg text-headline-xl font-bold text-on-surface mt-1">254.320 <span class="text-body-md font-normal text-on-surface-variant">TC</span></h3>
</div>
<div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[22px]">auto_stories</span>
</div>
</div>
<div class="mt-4 pt-3 border-t border-[#f1f5f9] flex items-center gap-1.5 text-emerald-700 font-label-sm text-label-sm">
<span class="material-symbols-outlined text-[16px]">trending_up</span>
<span>+12.4% so với học kỳ trước</span>
</div>
</div>
<!-- KPI 4: Cảnh báo / Xung đột -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-xs flex flex-col justify-between">
<div class="flex items-start justify-between">
<div>
<p class="font-label-md text-label-md text-on-surface-variant font-medium">Cảnh báo / Xung đột đăng ký</p>
<h3 class="font-display-lg text-headline-xl font-bold text-emerald-700 mt-1">0 <span class="text-body-md font-normal text-on-surface-variant">lỗi hệ thống</span></h3>
</div>
<div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[22px]">verified_user</span>
</div>
</div>
<div class="mt-4 pt-3 border-t border-[#f1f5f9] flex items-center gap-1.5 text-on-surface-variant font-label-sm text-label-sm">
<span class="material-symbols-outlined text-[16px] text-emerald-700">check_circle</span>
<span>Cơ chế kiểm soát tải vận hành ổn định</span>
</div>
</div>
</section>
<!-- 3. DANH SÁCH ĐỢT ĐĂNG KÝ HỌC PHẦN (TABLE PANEL) -->
<section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-xs overflow-hidden">
<!-- Toolbar lọc và tìm kiếm -->
<div class="p-5 border-b border-[#f1f5f9] flex flex-wrap items-center justify-between gap-4">
<div class="flex items-center gap-3 flex-1 min-w-[320px] max-w-md">
<div class="relative w-full">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-outline">search</span>
<input aria-label="Tìm theo tên đợt, mã đợt..." class="w-full h-10 pl-9 pr-3 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-outline focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" placeholder="Tìm theo tên đợt, mã đợt..." type="text"/>
</div>
</div>
<div class="flex items-center gap-3">
<!-- Filter Trạng thái -->
<div class="flex items-center gap-2">
<label class="font-label-md text-label-md text-on-surface-variant" for="filter-status">Trạng thái:</label>
<select aria-label="Tất cả trạng thái" class="h-10 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-label-md text-label-md py-1.5 pl-3 pr-8 focus:border-primary focus:ring-1 focus:ring-primary" id="filter-status">
<option>Tất cả trạng thái</option>
<option>Đang mở</option>
<option>Sắp mở</option>
<option>Đã đóng</option>
<option>Đã chốt</option>
</select>
</div>
<!-- Filter Đối tượng -->
<div class="flex items-center gap-2">
<label class="font-label-md text-label-md text-on-surface-variant" for="filter-cohort">Đối tượng:</label>
<select aria-label="Tất cả khóa (K21, K22, K23, K24)" class="h-10 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-label-md text-label-md py-1.5 pl-3 pr-8 focus:border-primary focus:ring-1 focus:ring-primary" id="filter-cohort">
<option>Tất cả khóa (K21, K22, K23, K24)</option>
<option>Khóa 2021 (K21)</option>
<option>Khóa 2022 (K22)</option>
<option>Khóa 2023 (K23)</option>
<option>Khóa 2024 (K24)</option>
</select>
</div>
<button class="h-10 px-3 rounded-lg border border-outline-variant hover:bg-surface-container-low text-on-surface-variant flex items-center gap-1.5 font-label-md text-label-md">
<span class="material-symbols-outlined text-[18px]">filter_list</span>
<span>Lọc nâng cao</span>
</button>
</div>
</div>
<!-- Academic Table Structure -->
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-[#f8fafc] border-b border-outline-variant/60 font-label-md text-label-md text-secondary">
<th class="py-3 px-4 font-semibold">Mã &amp; Tên đợt đăng ký</th>
<th class="py-3 px-3 font-semibold">Học kỳ áp dụng</th>
<th class="py-3 px-4 font-semibold">Thời gian mở - đóng</th>
<th class="py-3 px-3 font-semibold">Đối tượng áp dụng</th>
<th class="py-3 px-3 font-semibold">Giới hạn tín chỉ</th>
<th class="py-3 px-4 font-semibold">Tiến độ đăng ký</th>
<th class="py-3 px-3 font-semibold">Trạng thái</th>
<th class="py-3 px-4 text-right font-semibold">Thao tác</th>
</tr>
</thead>
<tbody class="divide-y divide-[#f1f5f9] font-body-md text-body-md">
<!-- Row 1: Đang mở (Highlighted) -->
<tr class="hover:bg-surface-container-low/50 transition-colors bg-blue-50/20">
<td class="py-3.5 px-4">
<div class="flex flex-col">
<span class="font-semibold text-primary">Đợt 1 - ĐKHP Chính thức Khóa 2021 &amp; 2022</span>
<span class="font-label-sm text-label-sm text-on-surface-variant font-mono">DHK1-2425-01</span>
</div>
</td>
<td class="py-3.5 px-3 font-medium text-on-surface whitespace-nowrap">HK1 2026-2027</td>
<td class="py-3.5 px-4 whitespace-nowrap">
<div class="flex flex-col text-body-sm">
<span class="font-medium text-on-surface">08:00 15/10/2026 → 23:59 22/10/2026</span>
<span class="text-emerald-700 font-label-sm text-label-sm flex items-center gap-1 mt-0.5">
<span class="material-symbols-outlined text-[13px]">schedule</span>
                      Còn 3 ngày 14 giờ
                    </span>
</div>
</td>
<td class="py-3.5 px-3">
<div class="flex items-center gap-1.5 flex-wrap">
<span class="px-2 py-0.5 rounded bg-surface-container-low border border-outline-variant font-label-sm text-label-sm text-on-surface font-semibold">K21</span>
<span class="px-2 py-0.5 rounded bg-surface-container-low border border-outline-variant font-label-sm text-label-sm text-on-surface font-semibold">K22</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Tất cả ngành</span>
</div>
</td>
<td class="py-3.5 px-3 whitespace-nowrap text-body-sm">
<span class="text-on-surface-variant">Tối thiểu: <strong class="text-on-surface font-semibold">14 TC</strong></span><br/>
<span class="text-on-surface-variant">Tối đa: <strong class="text-on-surface font-semibold">24 TC</strong></span>
</td>
<td class="py-3.5 px-4 min-w-[170px]">
<div class="flex flex-col gap-1">
<div class="flex justify-between font-label-sm text-label-sm">
<span class="text-on-surface font-semibold">8.450 / 9.200 SV</span>
<span class="text-primary font-bold">91.8%</span>
</div>
<div class="w-full h-1.5 bg-[#eff4ff] rounded-full overflow-hidden">
<div class="h-full bg-primary rounded-full" style="width: 91.8%"></div>
</div>
</div>
</td>
<td class="py-3.5 px-3 whitespace-nowrap">
<!-- Badge Đang mở có pulse -->
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-label-sm text-label-sm font-semibold">
<span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Đang mở
                  </span>
</td>
<td class="py-3.5 px-4 text-right whitespace-nowrap">
<div class="inline-flex items-center gap-1">
<button class="p-1.5 rounded-lg text-primary hover:bg-primary/10 transition-colors" title="Chỉnh sửa đợt">
<span class="material-symbols-outlined text-[18px]">edit</span>
</button>
<button class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-low transition-colors" title="Xem chi tiết">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
<button class="p-1.5 rounded-lg text-amber-700 hover:bg-amber-50 transition-colors" title="Chốt đợt sớm">
<span class="material-symbols-outlined text-[18px]">lock</span>
</button>
<button class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-low transition-colors" title="Báo cáo tiến độ">
<span class="material-symbols-outlined text-[18px]">bar_chart</span>
</button>
</div>
</td>
</tr>
<!-- Row 2: Sắp mở -->
<tr class="hover:bg-[#f8fafc] transition-colors">
<td class="py-3.5 px-4">
<div class="flex flex-col">
<span class="font-semibold text-on-surface">Đợt 2 - ĐKHP Bổ sung &amp; Cải thiện Toàn trường</span>
<span class="font-label-sm text-label-sm text-on-surface-variant font-mono">DHK1-2425-02</span>
</div>
</td>
<td class="py-3.5 px-3 font-medium text-on-surface whitespace-nowrap">HK1 2026-2027</td>
<td class="py-3.5 px-4 whitespace-nowrap">
<div class="flex flex-col text-body-sm">
<span class="font-medium text-on-surface">08:00 25/10/2026 → 17:00 30/10/2026</span>
<span class="text-sky-700 font-label-sm text-label-sm flex items-center gap-1 mt-0.5">
<span class="material-symbols-outlined text-[13px]">event_upcoming</span>
                      Mở sau 6 ngày
                    </span>
</div>
</td>
<td class="py-3.5 px-3">
<div class="flex items-center gap-1.5 flex-wrap">
<span class="px-2 py-0.5 rounded bg-surface-container-low border border-outline-variant font-label-sm text-label-sm text-on-surface font-semibold">K20</span>
<span class="px-2 py-0.5 rounded bg-surface-container-low border border-outline-variant font-label-sm text-label-sm text-on-surface font-semibold">K21</span>
<span class="px-2 py-0.5 rounded bg-surface-container-low border border-outline-variant font-label-sm text-label-sm text-on-surface font-semibold">K22</span>
<span class="px-2 py-0.5 rounded bg-surface-container-low border border-outline-variant font-label-sm text-label-sm text-on-surface font-semibold">K23</span>
</div>
</td>
<td class="py-3.5 px-3 whitespace-nowrap text-body-sm">
<span class="text-on-surface-variant">Tối thiểu: <strong class="text-on-surface font-semibold">10 TC</strong></span><br/>
<span class="text-on-surface-variant">Tối đa: <strong class="text-on-surface font-semibold">30 TC</strong></span>
</td>
<td class="py-3.5 px-4 min-w-[170px]">
<div class="flex flex-col gap-1">
<div class="flex justify-between font-label-sm text-label-sm">
<span class="text-on-surface-variant">0 / 18.500 SV</span>
<span class="text-on-surface-variant">0%</span>
</div>
<div class="w-full h-1.5 bg-[#eff4ff] rounded-full overflow-hidden">
<div class="h-full bg-outline-variant rounded-full" style="width: 0%"></div>
</div>
</div>
</td>
<td class="py-3.5 px-3 whitespace-nowrap">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-sky-50 text-sky-700 border border-sky-200 font-label-sm text-label-sm font-semibold">
<span class="w-2 h-2 rounded-full bg-sky-500"></span>
                    Sắp mở
                  </span>
</td>
<td class="py-3.5 px-4 text-right whitespace-nowrap">
<div class="inline-flex items-center gap-1">
<button class="p-1.5 rounded-lg text-primary hover:bg-primary/10 transition-colors" title="Chỉnh sửa đợt">
<span class="material-symbols-outlined text-[18px]">edit</span>
</button>
<button class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-low transition-colors" title="Xem chi tiết">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
<button class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-low transition-colors" title="Báo cáo tiến độ">
<span class="material-symbols-outlined text-[18px]">bar_chart</span>
</button>
</div>
</td>
</tr>
<!-- Row 3: Đã đóng -->
<tr class="hover:bg-[#f8fafc] transition-colors">
<td class="py-3.5 px-4">
<div class="flex flex-col">
<span class="font-semibold text-on-surface">Đợt Dự thính &amp; Học lại Chuyên ngành K22</span>
<span class="font-label-sm text-label-sm text-on-surface-variant font-mono">DHK1-2425-DT</span>
</div>
</td>
<td class="py-3.5 px-3 font-medium text-on-surface whitespace-nowrap">HK1 2026-2027</td>
<td class="py-3.5 px-4 whitespace-nowrap">
<div class="flex flex-col text-body-sm">
<span class="font-medium text-on-surface">08:00 05/10/2026 → 23:59 12/10/2026</span>
<span class="text-on-surface-variant font-label-sm text-label-sm">Đã kết thúc ngày 12/10</span>
</div>
</td>
<td class="py-3.5 px-3">
<div class="flex items-center gap-1.5 flex-wrap">
<span class="px-2 py-0.5 rounded bg-surface-container-low border border-outline-variant font-label-sm text-label-sm text-on-surface font-semibold">K22</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Khoa CNTT, Thực phẩm</span>
</div>
</td>
<td class="py-3.5 px-3 whitespace-nowrap text-body-sm">
<span class="text-on-surface-variant">Tối thiểu: <strong class="text-on-surface font-semibold">4 TC</strong></span><br/>
<span class="text-on-surface-variant">Tối đa: <strong class="text-on-surface font-semibold">16 TC</strong></span>
</td>
<td class="py-3.5 px-4 min-w-[170px]">
<div class="flex flex-col gap-1">
<div class="flex justify-between font-label-sm text-label-sm">
<span class="text-on-surface font-semibold">2.180 / 2.300 SV</span>
<span class="text-amber-700 font-bold">94.7%</span>
</div>
<div class="w-full h-1.5 bg-[#eff4ff] rounded-full overflow-hidden">
<div class="h-full bg-amber-500 rounded-full" style="width: 94.7%"></div>
</div>
</div>
</td>
<td class="py-3.5 px-3 whitespace-nowrap">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 font-label-sm text-label-sm font-semibold">
<span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    Đã đóng
                  </span>
</td>
<td class="py-3.5 px-4 text-right whitespace-nowrap">
<div class="inline-flex items-center gap-1">
<button class="p-1.5 rounded-lg text-primary hover:bg-primary/10 transition-colors" title="Xem chi tiết">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
<button class="p-1.5 rounded-lg text-purple-700 hover:bg-purple-50 transition-colors" title="Chốt số liệu đợt">
<span class="material-symbols-outlined text-[18px]">task_alt</span>
</button>
<button class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-low transition-colors" title="Xuất biên bản">
<span class="material-symbols-outlined text-[18px]">description</span>
</button>
</div>
</td>
</tr>
<!-- Row 4: Đã chốt -->
<tr class="hover:bg-[#f8fafc] transition-colors bg-surface-container-low/20">
<td class="py-3.5 px-4">
<div class="flex flex-col">
<span class="font-semibold text-on-surface">Đợt ĐKHP Khóa Luận &amp; Tốt nghiệp Khóa 2020</span>
<span class="font-label-sm text-label-sm text-on-surface-variant font-mono">DHK1-2425-TN20</span>
</div>
</td>
<td class="py-3.5 px-3 font-medium text-on-surface whitespace-nowrap">HK1 2026-2027</td>
<td class="py-3.5 px-4 whitespace-nowrap">
<div class="flex flex-col text-body-sm">
<span class="font-medium text-on-surface">08:00 01/10/2026 → 17:00 04/10/2026</span>
<span class="text-purple-700 font-label-sm text-label-sm">Đã chốt danh sách &amp; sinh học vụ</span>
</div>
</td>
<td class="py-3.5 px-3">
<div class="flex items-center gap-1.5 flex-wrap">
<span class="px-2 py-0.5 rounded bg-purple-50 border border-purple-200 font-label-sm text-label-sm text-purple-700 font-semibold">K20</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Sinh viên đủ ĐK tốt nghiệp</span>
</div>
</td>
<td class="py-3.5 px-3 whitespace-nowrap text-body-sm">
<span class="text-on-surface-variant">Tối thiểu: <strong class="text-on-surface font-semibold">6 TC</strong></span><br/>
<span class="text-on-surface-variant">Tối đa: <strong class="text-on-surface font-semibold">14 TC</strong></span>
</td>
<td class="py-3.5 px-4 min-w-[170px]">
<div class="flex flex-col gap-1">
<div class="flex justify-between font-label-sm text-label-sm">
<span class="text-on-surface font-semibold">4.190 / 4.200 SV</span>
<span class="text-purple-700 font-bold">99.8%</span>
</div>
<div class="w-full h-1.5 bg-[#eff4ff] rounded-full overflow-hidden">
<div class="h-full bg-purple-600 rounded-full" style="width: 99.8%"></div>
</div>
</div>
</td>
<td class="py-3.5 px-3 whitespace-nowrap">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-purple-50 text-purple-700 border border-purple-200 font-label-sm text-label-sm font-semibold">
<span class="w-2 h-2 rounded-full bg-purple-600"></span>
                    Đã chốt
                  </span>
</td>
<td class="py-3.5 px-4 text-right whitespace-nowrap">
<div class="inline-flex items-center gap-1">
<button class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-low transition-colors" title="Xem chi tiết">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
<button class="p-1.5 rounded-lg text-primary hover:bg-primary/10 transition-colors" title="Xuất dữ liệu phòng đào tạo">
<span class="material-symbols-outlined text-[18px]">file_download</span>
</button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<!-- Pagination & Summary Footer -->
<div class="p-4 bg-surface-container-lowest border-t border-[#f1f5f9] flex items-center justify-between text-body-sm text-on-surface-variant">
<div>Hiển thị <span class="font-semibold text-on-surface">1 - 4</span> trên tổng số <span class="font-semibold text-on-surface">4</span> đợt đăng ký</div>
<div class="flex items-center gap-2">
<button class="px-2.5 py-1 rounded border border-outline-variant bg-surface-container-lowest text-outline cursor-not-allowed" disabled="">
<span class="material-symbols-outlined text-[16px]">chevron_left</span>
</button>
<button class="px-3 py-1 rounded bg-primary text-white font-semibold text-label-sm">1</button>
<button class="px-2.5 py-1 rounded border border-outline-variant bg-surface-container-lowest text-outline cursor-not-allowed" disabled="">
<span class="material-symbols-outlined text-[16px]">chevron_right</span>
</button>
</div>
</div>
</section>
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
