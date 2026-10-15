@extends('fe_preview.layouts.admin')
@section('page_title', 'Quản lý Lớp học phần - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Quản lý Lớp học phần - HUIT Học Vụ</title>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/lop-hoc-phan']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">groups</span>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Quản lý Lớp học phần</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Quản lý Lớp học phần</div>
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
<!-- 1. Header Trang & Bộ chọn Học kỳ -->
<section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 p-6 shadow-sm">
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
<!-- Title & Context -->
<div>
<div class="flex items-center gap-2 mb-1">
<span class="px-2 py-0.5 rounded text-[11px] font-semibold tracking-wider uppercase bg-primary-fixed text-on-primary-fixed border border-primary/20">
                  ĐÀO TẠO • D1. #18
                </span>
<span class="text-on-surface-variant text-body-sm font-body-sm">• Quản lý Học vụ Chính quy</span>
</div>
<h1 class="font-headline-xl text-headline-xl text-on-surface font-bold tracking-tight">
                Quản lý Lớp học phần
              </h1>
<p class="font-body-md text-body-md text-on-surface-variant mt-0.5">
                Quản trị danh mục lớp học phần, điều phối phân công giảng dạy, theo dõi ngưỡng đăng ký và tổ chức lớp tín chỉ.
              </p>
</div>
<!-- Semester Selector & Actions -->
<div class="flex flex-wrap items-center gap-3">
<!-- Bộ chọn Học kỳ nổi bật -->
<div class="flex items-center gap-2 bg-surface-container-low border border-primary/30 p-1.5 rounded-lg">
<span class="material-symbols-outlined text-primary ml-1.5 text-[20px]">date_range</span>
<div class="flex flex-col">
<span class="text-[10px] uppercase font-semibold text-on-surface-variant tracking-wider leading-none">Học kỳ điều hành</span>
<select aria-label="Học kỳ 1 - Năm học 2026-2027" class="bg-transparent text-on-surface font-label-lg text-label-lg font-semibold border-none p-0 pr-6 focus:ring-0 cursor-pointer">
<option selected="" value="20241">Học kỳ 1 - Năm học 2026-2027</option>
<option value="20242">Học kỳ 2 - Năm học 2026-2027</option>
<option value="20243">Học kỳ Hè - Năm học 2026-2027</option>
<option value="20232">Học kỳ 2 - Năm học 2025-2026</option>
</select>
</div>
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300 ml-1">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                  Đang mở cổng đăng ký tín chỉ
                </span>
</div>
<!-- Button Actions -->
<div class="flex items-center gap-2">
<button class="px-4 py-2.5 rounded-lg bg-primary text-white hover:bg-[#1648a7] active:scale-95 transition font-label-lg text-label-lg font-semibold flex items-center gap-2 shadow-sm" onclick="document.getElementById('openClassModal').classList.remove('hidden')">
<span class="material-symbols-outlined text-[18px]">add_circle</span>
<span>+ Mở lớp học phần mới</span>
</button>
<button class="px-3 py-2.5 rounded-lg bg-surface-container-lowest border border-outline-variant text-on-surface hover:bg-surface-container-low font-label-lg text-label-lg flex items-center gap-1.5 transition">
<span class="material-symbols-outlined text-[18px] text-emerald-700">file_download</span>
<span>Xuất Excel</span>
</button>
<button class="px-3 py-2.5 rounded-lg bg-surface-container-lowest border border-outline-variant text-on-surface hover:bg-surface-container-low font-label-lg text-label-lg flex items-center gap-1.5 transition">
<span class="material-symbols-outlined text-[18px] text-primary">upload_file</span>
<span>Nhập từ file</span>
</button>
</div>
</div>
</div>
</section>
<!-- 2. Hàng Thẻ KPI Thống Kê Tổng Quan -->
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
<!-- Card 1: Tổng số lớp -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/50 shadow-sm flex items-center justify-between">
<div>
<p class="font-label-md text-label-md text-on-surface-variant font-medium">Tổng số lớp học phần</p>
<h3 class="font-headline-lg text-headline-lg font-bold text-on-surface mt-1">1,240 <span class="font-body-sm text-body-sm font-normal text-on-surface-variant">lớp</span></h3>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-1 flex items-center gap-1">
<span class="text-primary font-semibold">14 khoa</span> tham gia giảng dạy
              </p>
</div>
<div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
<span class="material-symbols-outlined text-[26px]">grid_view</span>
</div>
</div>
<!-- Card 2: Lớp đủ sĩ số -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/50 shadow-sm flex items-center justify-between">
<div>
<div class="flex items-center gap-2">
<p class="font-label-md text-label-md text-on-surface-variant font-medium">Lớp đủ sĩ số (Đạt chỉ tiêu)</p>
<span class="px-1.5 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800">87.5%</span>
</div>
<h3 class="font-headline-lg text-headline-lg font-bold text-emerald-700 mt-1">1,085 <span class="font-body-sm text-body-sm font-normal text-on-surface-variant">lớp</span></h3>
<p class="font-body-sm text-body-sm text-emerald-700 mt-1 flex items-center gap-1">
<span class="material-symbols-outlined text-[16px]">check_circle</span> Sẵn sàng xếp thời khóa biểu
              </p>
</div>
<div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center border border-emerald-200">
<span class="material-symbols-outlined text-[26px]">how_to_reg</span>
</div>
</div>
<!-- Card 3: Lớp dưới ngưỡng (Nguy cơ hủy lớp) -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-amber-200 bg-amber-50/20 shadow-sm flex items-center justify-between">
<div>
<div class="flex items-center gap-2">
<p class="font-label-md text-label-md text-on-surface-variant font-medium">Lớp dưới ngưỡng (&lt;15 SV)</p>
<span class="px-1.5 py-0.5 rounded text-[11px] font-bold bg-amber-100 text-amber-800">Cảnh báo</span>
</div>
<h3 class="font-headline-lg text-headline-lg font-bold text-amber-700 mt-1">42 <span class="font-body-sm text-body-sm font-normal text-on-surface-variant">lớp</span></h3>
<p class="font-body-sm text-body-sm text-amber-700 mt-1 flex items-center gap-1">
<span class="material-symbols-outlined text-[16px]">warning</span> Nguy cơ hủy hoặc ghép lớp
              </p>
</div>
<div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
<span class="material-symbols-outlined text-[26px]">group_off</span>
</div>
</div>
<!-- Card 4: Lớp thiếu giảng viên -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-red-200 bg-red-50/20 shadow-sm flex items-center justify-between">
<div>
<div class="flex items-center gap-2">
<p class="font-label-md text-label-md text-on-surface-variant font-medium">Thiếu giảng viên chính</p>
<span class="px-1.5 py-0.5 rounded text-[11px] font-bold bg-red-100 text-red-800">Khẩn cấp</span>
</div>
<h3 class="font-headline-lg text-headline-lg font-bold text-error mt-1">18 <span class="font-body-sm text-body-sm font-normal text-on-surface-variant">lớp</span></h3>
<p class="font-body-sm text-body-sm text-error mt-1 flex items-center gap-1">
<span class="material-symbols-outlined text-[16px]">person_alert</span> Cần Trưởng bộ môn phân công
              </p>
</div>
<div class="w-12 h-12 rounded-xl bg-red-100 text-error flex items-center justify-center">
<span class="material-symbols-outlined text-[26px]">person_remove</span>
</div>
</div>
</section>
<!-- 3. Thanh Công Cụ Tìm Kiếm, Lọc & State Switcher -->
<section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 p-5 shadow-sm space-y-4">
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
<!-- Search Bar -->
<div class="flex-1 max-w-md relative">
<span class="material-symbols-outlined text-outline absolute left-3 top-2.5 text-[20px]">search</span>
<input aria-label="Tìm theo mã lớp HP, tên học phần, giảng viên..." class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface placeholder:text-outline text-body-md font-body-md focus:border-primary focus:ring-2 focus:ring-primary/20 transition outline-none" placeholder="Tìm theo mã lớp HP, tên học phần, giảng viên..." type="text"/>
</div>
<!-- Filters -->
<div class="flex flex-wrap items-center gap-3">
<!-- Filter Khoa/Bộ môn -->
<div class="flex items-center gap-2">
<span class="font-label-md text-label-md text-on-surface-variant whitespace-nowrap">Khoa/Viện:</span>
<select aria-label="Khoa Công nghệ Thông tin (CNTT)" class="bg-surface-container-lowest border border-outline-variant text-on-surface rounded-lg py-1.5 px-3 font-body-md text-body-md focus:border-primary focus:ring-1 focus:ring-primary">
<option selected="">Khoa Công nghệ Thông tin (CNTT)</option>
<option>Khoa Công nghệ Thực phẩm</option>
<option>Khoa Quản trị Kinh doanh</option>
<option>Khoa Ngoại ngữ</option>
<option>Khoa Điện - Điện tử</option>
</select>
</div>
<!-- Filter Trạng thái lớp -->
<div class="flex items-center gap-2">
<span class="font-label-md text-label-md text-on-surface-variant whitespace-nowrap">Trạng thái:</span>
<select aria-label="Tất cả trạng thái" class="bg-surface-container-lowest border border-outline-variant text-on-surface rounded-lg py-1.5 px-3 font-body-md text-body-md focus:border-primary focus:ring-1 focus:ring-primary">
<option selected="">Tất cả trạng thái</option>
<option>Đang mở đăng ký</option>
<option>Đã chốt sĩ số</option>
<option>Dưới ngưỡng (&lt; 15 SV)</option>
<option>Bản nháp</option>
</select>
</div>
<button class="p-2 border border-outline-variant rounded-lg hover:bg-surface-container-low text-on-surface-variant" title="Xóa bộ lọc">
<span class="material-symbols-outlined text-[18px]">restart_alt</span>
</button>
</div>
</div>
<!-- UI State Switcher Prototype Demonstration -->
<div class="pt-3 border-t border-outline-variant/30 flex flex-wrap items-center justify-between gap-3">
<div class="flex items-center gap-2 text-on-surface-variant">
<span class="material-symbols-outlined text-[18px]">visibility</span>
<span class="font-label-md text-label-md">Xem chế độ giao diện mô phỏng (State Switcher):</span>
</div>
<div class="inline-flex rounded-lg border border-outline-variant/70 p-0.5 bg-surface-container-low text-label-sm font-label-sm">
<button class="px-3 py-1 rounded-md bg-surface-container-lowest shadow-xs text-primary font-bold transition" id="btnStateNormal" onclick="switchTableState('normal')">
                Bình thường (1,240 lớp)
              </button>
<button class="px-3 py-1 rounded-md text-on-surface-variant hover:text-on-surface transition" id="btnStateSkeleton" onclick="switchTableState('skeleton')">
                Đang tải (Skeleton)
              </button>
<button class="px-3 py-1 rounded-md text-on-surface-variant hover:text-on-surface transition" id="btnStateEmpty" onclick="switchTableState('empty')">
                Danh sách rỗng
              </button>
<button class="px-3 py-1 rounded-md text-on-surface-variant hover:text-on-surface transition" id="btnStateError" onclick="switchTableState('error')">
                Lỗi tải kết nối
              </button>
</div>
</div>
</section>
<!-- 4. Bảng Danh Sách Lớp Học Phần (HUIT Table with Sticky Left Column) -->
<section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-sm overflow-hidden">
<!-- Normal Table State Container -->
<div class="overflow-x-auto custom-scrollbar" id="tableNormalState">
<table class="w-full text-left border-collapse min-w-[1100px]">
<thead>
<tr class="bg-surface-container-low/80 border-b border-outline-variant/50 font-label-md text-label-md uppercase text-on-surface-variant tracking-wider">
<!-- Sticky Column Header -->
<th class="py-3 px-4 sticky left-0 bg-surface-container-low z-20 shadow-[2px_0_4px_-1px_rgba(0,0,0,0.05)] w-80" scope="col">
                    Mã Lớp HP &amp; Tên Học Phần
                  </th>
<th class="py-3 px-4" scope="col">Số TC</th>
<th class="py-3 px-4" scope="col">Giảng viên giảng dạy</th>
<th class="py-3 px-4 w-44" scope="col">Sĩ số (Đã ĐK / Tối đa)</th>
<th class="py-3 px-4" scope="col">Lịch học &amp; Phòng học</th>
<th class="py-3 px-4" scope="col">Trạng thái lớp</th>
<th class="py-3 px-4 text-right" scope="col">Thao tác</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant/30 text-body-md font-body-md">
<!-- ROW 1: Lập trình Web -->
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-3.5 px-4 sticky left-0 bg-surface-container-lowest group-hover:bg-surface-container-low z-10 shadow-[2px_0_4px_-1px_rgba(0,0,0,0.05)]">
<div class="flex items-center gap-2">
<span class="font-mono font-bold text-primary text-[13px] bg-primary-fixed/50 px-2 py-0.5 rounded border border-primary/20">
                        241_71ITEC3201_01
                      </span>
</div>
<div class="font-headline-sm text-headline-sm text-on-surface font-semibold mt-1">
                      Lập trình Web nâng cao
                    </div>
<div class="font-body-sm text-body-sm text-on-surface-variant text-[11px]">
                      Học phần cốt lõi ngành CNTT
                    </div>
</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center justify-center font-bold px-2.5 py-0.5 rounded-full bg-surface-container text-on-surface text-label-sm">
                      3 TC
                    </span>
</td>
<td class="py-3.5 px-4">
<div class="flex items-center gap-2.5">
<div class="w-8 h-8 rounded-full bg-primary/10 border border-primary/20 flex items-center justify-center text-primary font-bold text-[12px]">
                        TN
                      </div>
<div>
<p class="font-label-lg text-label-lg text-on-surface font-semibold">TS. Người dùng mẫu 38</p>
<p class="font-body-sm text-body-sm text-on-surface-variant text-[11px]">Bộ môn CNPM • Khoa CNTT</p>
</div>
</div>
</td>
<td class="py-3.5 px-4">
<div class="space-y-1">
<div class="flex items-center justify-between text-label-sm font-label-sm">
<span class="font-bold text-on-surface">58 / 60</span>
<span class="text-emerald-700 font-semibold">96.7%</span>
</div>
<!-- Progress bar -->
<div class="w-full bg-surface-container rounded-full h-2 overflow-hidden">
<div class="bg-emerald-500 h-2 rounded-full" style="width: 96.7%"></div>
</div>
<span class="text-[10px] text-emerald-700 font-medium">Gần đầy sĩ số</span>
</div>
</td>
<td class="py-3.5 px-4">
<div class="space-y-0.5">
<div class="flex items-center gap-1.5 font-label-md text-label-md text-on-surface">
<span class="material-symbols-outlined text-[16px] text-primary">schedule</span>
                        Thứ 2 (Tiết 1 - 3)
                      </div>
<div class="flex items-center gap-1.5 font-body-sm text-body-sm text-on-surface-variant">
<span class="material-symbols-outlined text-[16px] text-outline">meeting_room</span>
                        Phòng B.204 (Cơ sở Tân Phú)
                      </div>
</div>
</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-label-sm font-label-sm font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                      Đang mở đăng ký
                    </span>
</td>
<td class="py-3.5 px-4 text-right">
<div class="inline-flex items-center gap-1">
<button class="p-1.5 rounded hover:bg-surface-container-low text-primary transition" title="Xem chi tiết lớp">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
<button class="p-1.5 rounded hover:bg-surface-container-low text-secondary transition" title="Điều chỉnh giảng viên">
<span class="material-symbols-outlined text-[18px]">manage_accounts</span>
</button>
<button class="p-1.5 rounded hover:bg-surface-container-low text-error transition" title="Khóa / Đóng đăng ký">
<span class="material-symbols-outlined text-[18px]">lock</span>
</button>
</div>
</td>
</tr>
<!-- ROW 2: Cấu trúc dữ liệu & Giải thuật -->
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-3.5 px-4 sticky left-0 bg-surface-container-lowest z-10 shadow-[2px_0_4px_-1px_rgba(0,0,0,0.05)]">
<div class="flex items-center gap-2">
<span class="font-mono font-bold text-primary text-[13px] bg-primary-fixed/50 px-2 py-0.5 rounded border border-primary/20">
                        241_71ITEC3203_02
                      </span>
</div>
<div class="font-headline-sm text-headline-sm text-on-surface font-semibold mt-1">
                      Cấu trúc dữ liệu &amp; Giải thuật
                    </div>
<div class="font-body-sm text-body-sm text-on-surface-variant text-[11px]">
                      Học phần bắt buộc chuyên ngành
                    </div>
</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center justify-center font-bold px-2.5 py-0.5 rounded-full bg-surface-container text-on-surface text-label-sm">
                      4 TC
                    </span>
</td>
<td class="py-3.5 px-4">
<div class="flex items-center gap-2.5">
<div class="w-8 h-8 rounded-full bg-indigo-100 border border-indigo-200 flex items-center justify-center text-indigo-700 font-bold text-[12px]">
                        MH
                      </div>
<div>
<p class="font-label-lg text-label-lg text-on-surface font-semibold">ThS. Người dùng mẫu 15</p>
<p class="font-body-sm text-body-sm text-on-surface-variant text-[11px]">Bộ môn Khoa học máy tính</p>
</div>
</div>
</td>
<td class="py-3.5 px-4">
<div class="space-y-1">
<div class="flex items-center justify-between text-label-sm font-label-sm">
<span class="font-bold text-on-surface">60 / 60</span>
<span class="text-primary font-semibold">100%</span>
</div>
<div class="w-full bg-surface-container rounded-full h-2 overflow-hidden">
<div class="bg-primary h-2 rounded-full" style="width: 100%"></div>
</div>
<span class="text-[10px] text-primary font-medium">Đã chốt danh sách</span>
</div>
</td>
<td class="py-3.5 px-4">
<div class="space-y-0.5">
<div class="flex items-center gap-1.5 font-label-md text-label-md text-on-surface">
<span class="material-symbols-outlined text-[16px] text-primary">schedule</span>
                        Thứ 4 (Tiết 7 - 10)
                      </div>
<div class="flex items-center gap-1.5 font-body-sm text-body-sm text-on-surface-variant">
<span class="material-symbols-outlined text-[16px] text-outline">meeting_room</span>
                        Lab PM04 (Nhà C)
                      </div>
</div>
</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-label-sm font-label-sm font-semibold bg-blue-100 text-blue-800 border border-blue-300">
<span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                      Đã chốt sĩ số
                    </span>
</td>
<td class="py-3.5 px-4 text-right">
<div class="inline-flex items-center gap-1">
<button class="p-1.5 rounded hover:bg-surface-container-low text-primary transition" title="Xem chi tiết lớp">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
<button class="p-1.5 rounded hover:bg-surface-container-low text-secondary transition" title="Điều chỉnh giảng viên">
<span class="material-symbols-outlined text-[18px]">manage_accounts</span>
</button>
<button class="p-1.5 rounded hover:bg-surface-container-low text-on-surface-variant transition" title="In bảng điểm quá trình">
<span class="material-symbols-outlined text-[18px]">print</span>
</button>
</div>
</td>
</tr>
<!-- ROW 3: An toàn mạng & Mật mã học (CẢNH BÁO DƯỚI NGƯỠNG) -->
<tr class="hover:bg-amber-50/40 bg-amber-50/10 transition-colors">
<td class="py-3.5 px-4 sticky left-0 bg-surface-container-lowest z-10 shadow-[2px_0_4px_-1px_rgba(0,0,0,0.05)]">
<div class="flex items-center gap-2">
<span class="font-mono font-bold text-amber-700 text-[13px] bg-amber-100 px-2 py-0.5 rounded border border-amber-300">
                        241_71ITEC3208_03
                      </span>
</div>
<div class="font-headline-sm text-headline-sm text-on-surface font-semibold mt-1">
                      An toàn mạng &amp; Mật mã học
                    </div>
<div class="font-body-sm text-body-sm text-on-surface-variant text-[11px]">
                      Học phần tự chọn chuyên sâu
                    </div>
</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center justify-center font-bold px-2.5 py-0.5 rounded-full bg-surface-container text-on-surface text-label-sm">
                      3 TC
                    </span>
</td>
<td class="py-3.5 px-4">
<div class="flex items-center gap-2.5">
<div class="w-8 h-8 rounded-full bg-emerald-100 border border-emerald-200 flex items-center justify-center text-emerald-800 font-bold text-[12px]">
                        VT
                      </div>
<div>
<p class="font-label-lg text-label-lg text-on-surface font-semibold">PGS.TS. Người dùng mẫu 49</p>
<p class="font-body-sm text-body-sm text-on-surface-variant text-[11px]">Bộ môn An ninh mạng</p>
</div>
</div>
</td>
<td class="py-3.5 px-4">
<div class="space-y-1">
<div class="flex items-center justify-between text-label-sm font-label-sm">
<span class="font-bold text-amber-800">12 / 60</span>
<span class="text-amber-700 font-bold">20%</span>
</div>
<div class="w-full bg-surface-container rounded-full h-2 overflow-hidden">
<div class="bg-amber-500 h-2 rounded-full" style="width: 20%"></div>
</div>
<span class="text-[10px] text-amber-700 font-semibold flex items-center gap-1">
<span class="material-symbols-outlined text-[12px]">warning</span> Thiếu 3 SV để mở
                      </span>
</div>
</td>
<td class="py-3.5 px-4">
<div class="space-y-0.5">
<div class="flex items-center gap-1.5 font-label-md text-label-md text-on-surface">
<span class="material-symbols-outlined text-[16px] text-primary">schedule</span>
                        Thứ 6 (Tiết 4 - 6)
                      </div>
<div class="flex items-center gap-1.5 font-body-sm text-body-sm text-on-surface-variant">
<span class="material-symbols-outlined text-[16px] text-outline">meeting_room</span>
                        Phòng A.301 (Nhà A)
                      </div>
</div>
</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-label-sm font-label-sm font-semibold bg-amber-100 text-amber-900 border border-amber-300">
<span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                      Dưới ngưỡng (&lt;15 SV)
                    </span>
</td>
<td class="py-3.5 px-4 text-right">
<div class="inline-flex items-center gap-1">
<button class="p-1.5 rounded hover:bg-amber-100 text-amber-800 transition" title="Ghép hoặc chuyển lớp">
<span class="material-symbols-outlined text-[18px]">merge_type</span>
</button>
<button class="p-1.5 rounded hover:bg-red-100 text-error transition" title="Hủy lớp học phần">
<span class="material-symbols-outlined text-[18px]">delete_forever</span>
</button>
</div>
</td>
</tr>
<!-- ROW 4: Trí tuệ nhân tạo (CHƯA PHÂN CÔNG GIẢNG VIÊN) -->
<tr class="hover:bg-red-50/30 bg-red-50/10 transition-colors">
<td class="py-3.5 px-4 sticky left-0 bg-surface-container-lowest z-10 shadow-[2px_0_4px_-1px_rgba(0,0,0,0.05)]">
<div class="flex items-center gap-2">
<span class="font-mono font-bold text-primary text-[13px] bg-primary-fixed/50 px-2 py-0.5 rounded border border-primary/20">
                        241_71ITEC3212_01
                      </span>
</div>
<div class="font-headline-sm text-headline-sm text-on-surface font-semibold mt-1">
                      Trí tuệ nhân tạo ứng dụng
                    </div>
<div class="font-body-sm text-body-sm text-on-surface-variant text-[11px]">
                      Học phần nâng cao
                    </div>
</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center justify-center font-bold px-2.5 py-0.5 rounded-full bg-surface-container text-on-surface text-label-sm">
                      3 TC
                    </span>
</td>
<td class="py-3.5 px-4">
<div class="flex items-center gap-2">
<span class="px-2 py-1 rounded bg-red-100 text-error border border-red-200 text-label-sm font-semibold flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]">person_off</span>
                        [Chưa phân công]
                      </span>
<button class="text-primary hover:underline text-body-sm font-semibold text-[11px]">Gán GV</button>
</div>
</td>
<td class="py-3.5 px-4">
<div class="space-y-1">
<div class="flex items-center justify-between text-label-sm font-label-sm">
<span class="font-bold text-on-surface">45 / 60</span>
<span class="text-primary font-semibold">75%</span>
</div>
<div class="w-full bg-surface-container rounded-full h-2 overflow-hidden">
<div class="bg-primary h-2 rounded-full" style="width: 75%"></div>
</div>
<span class="text-[10px] text-on-surface-variant">Đạt chỉ tiêu mở</span>
</div>
</td>
<td class="py-3.5 px-4">
<div class="space-y-0.5">
<div class="flex items-center gap-1.5 font-label-md text-label-md text-on-surface">
<span class="material-symbols-outlined text-[16px] text-primary">schedule</span>
                        Thứ 5 (Tiết 1 - 3)
                      </div>
<div class="flex items-center gap-1.5 font-body-sm text-body-sm text-on-surface-variant">
<span class="material-symbols-outlined text-[16px] text-outline">meeting_room</span>
                        Phòng B.402 (Nhà B)
                      </div>
</div>
</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-label-sm font-label-sm font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                      Đang mở đăng ký
                    </span>
</td>
<td class="py-3.5 px-4 text-right">
<div class="inline-flex items-center gap-1">
<button class="px-2 py-1 rounded bg-primary text-white text-label-sm font-semibold hover:bg-[#1648a7]" title="Phân công giảng viên ngay">
                        Phân công
                      </button>
<button class="p-1.5 rounded hover:bg-surface-container-low text-on-surface-variant" title="Thao tác khác">
<span class="material-symbols-outlined text-[18px]">more_vert</span>
</button>
</div>
</td>
</tr>
<!-- ROW 5: Bản nháp -->
<tr class="hover:bg-surface-container-low/50 opacity-80">
<td class="py-3.5 px-4 sticky left-0 bg-surface-container-lowest z-10 shadow-[2px_0_4px_-1px_rgba(0,0,0,0.05)]">
<div class="flex items-center gap-2">
<span class="font-mono font-bold text-on-surface-variant text-[13px] bg-surface-container px-2 py-0.5 rounded border border-outline-variant">
                        241_71ITEC4101_04
                      </span>
</div>
<div class="font-headline-sm text-headline-sm text-on-surface font-semibold mt-1">
                      Phát triển ứng dụng Di động (Mobile App)
                    </div>
<div class="font-body-sm text-body-sm text-on-surface-variant text-[11px]">
                      Học phần mới năm 2024
                    </div>
</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center justify-center font-bold px-2.5 py-0.5 rounded-full bg-surface-container text-on-surface text-label-sm">
                      3 TC
                    </span>
</td>
<td class="py-3.5 px-4">
<span class="text-on-surface-variant text-body-sm italic">Chưa xác định</span>
</td>
<td class="py-3.5 px-4">
<span class="text-body-sm text-on-surface-variant">0 / 60 (Chưa công bố)</span>
</td>
<td class="py-3.5 px-4">
<span class="text-body-sm text-on-surface-variant italic">Chưa xếp phòng</span>
</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-label-sm font-label-sm font-semibold bg-gray-100 text-gray-700 border border-gray-300">
<span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>
                      Bản nháp
                    </span>
</td>
<td class="py-3.5 px-4 text-right">
<div class="inline-flex items-center gap-1">
<button class="p-1.5 rounded hover:bg-surface-container-low text-primary transition" title="Chỉnh sửa bản nháp">
<span class="material-symbols-outlined text-[18px]">edit</span>
</button>
<button class="p-1.5 rounded hover:bg-surface-container-low text-error transition" title="Xóa nháp">
<span class="material-symbols-outlined text-[18px]">delete</span>
</button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<!-- Skeleton Loading State (Hidden by default) -->
<div class="hidden p-6 space-y-4 animate-pulse" id="tableSkeletonState">
<div class="h-6 bg-surface-container rounded w-1/3"></div>
<div class="space-y-3">
<div class="h-12 bg-surface-container-low rounded-lg w-full"></div>
<div class="h-12 bg-surface-container-low rounded-lg w-full"></div>
<div class="h-12 bg-surface-container-low rounded-lg w-full"></div>
<div class="h-12 bg-surface-container-low rounded-lg w-full"></div>
</div>
<p class="text-center text-on-surface-variant font-label-md text-label-md">Đang đồng bộ dữ liệu lớp học phần từ cổng Đào tạo HUIT...</p>
</div>
<!-- Empty State (Hidden by default) -->
<div class="hidden p-12 text-center flex-col items-center justify-center space-y-3" id="tableEmptyState">
<div class="w-16 h-16 rounded-full bg-surface-container-low flex items-center justify-center text-outline mx-auto">
<span class="material-symbols-outlined text-[36px]">search_off</span>
</div>
<h4 class="font-headline-sm text-headline-sm font-bold text-on-surface">Không tìm thấy lớp học phần phù hợp</h4>
<p class="font-body-md text-body-md text-on-surface-variant max-w-md mx-auto">
              Không có lớp học phần nào thỏa điều kiện tìm kiếm hoặc chưa được khởi tạo trong học kỳ được chọn.
            </p>
<button class="px-4 py-2 rounded-lg bg-primary text-white text-label-md font-semibold" onclick="document.getElementById('openClassModal').classList.remove('hidden')">
              + Mở lớp học phần mới ngay
            </button>
</div>
<!-- Error State (Hidden by default) -->
<div class="hidden p-12 text-center flex-col items-center justify-center space-y-3 bg-red-50/20" id="tableErrorState">
<div class="w-16 h-16 rounded-full bg-red-100 text-error flex items-center justify-center mx-auto">
<span class="material-symbols-outlined text-[36px]">cloud_off</span>
</div>
<h4 class="font-headline-sm text-headline-sm font-bold text-error">Lỗi kết nối cơ sở dữ liệu học vụ HUIT</h4>
<p class="font-body-md text-body-md text-on-surface-variant max-w-md mx-auto">
              Không thể tải danh sách lớp học phần do gián đoạn mạng hoặc dịch vụ API đào tạo đang bận. Vui lòng thử lại.
            </p>
<button class="px-4 py-2 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold flex items-center gap-1.5 mx-auto" onclick="switchTableState('normal')">
<span class="material-symbols-outlined text-[16px]">refresh</span>
              Thử kết nối lại
            </button>
</div>
<!-- Table Footer Pagination -->
<div class="p-4 border-t border-outline-variant/40 flex flex-col sm:flex-row items-center justify-between gap-3 text-body-sm font-body-sm text-on-surface-variant">
<div class="flex items-center gap-2">
<span>Hiển thị 1 - 5 trong tổng số <strong class="text-on-surface">1,240</strong> lớp học phần</span>
<span class="text-outline">•</span>
<span>Trang 1 / 248</span>
</div>
<div class="flex items-center gap-1">
<button class="px-2.5 py-1.5 rounded border border-outline-variant hover:bg-surface-container-low disabled:opacity-50" disabled="">
<span class="material-symbols-outlined text-[16px]">chevron_left</span>
</button>
<button class="px-3 py-1.5 rounded bg-primary text-white font-bold">1</button>
<button class="px-3 py-1.5 rounded border border-outline-variant hover:bg-surface-container-low">2</button>
<button class="px-3 py-1.5 rounded border border-outline-variant hover:bg-surface-container-low">3</button>
<span class="px-2">...</span>
<button class="px-3 py-1.5 rounded border border-outline-variant hover:bg-surface-container-low">248</button>
<button class="px-2.5 py-1.5 rounded border border-outline-variant hover:bg-surface-container-low">
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
