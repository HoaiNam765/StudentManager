@extends('fe_preview.layouts.admin')
@section('page_title', 'HUIT Học Vụ - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>HUIT Học Vụ - HUIT Học Vụ</title>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/quy-che-hoc-vu']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">gavel</span>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">HUIT Học Vụ</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / HUIT Học Vụ</div>
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
<div class="p-7 max-w-[1440px] mx-auto w-full pb-28">
<!-- Breadcrumbs -->
<nav class="flex items-center gap-2 text-body-sm text-outline mb-3">
<a class="hover:text-primary transition-colors flex items-center gap-1" href="#home">
<span class="material-symbols-outlined text-base" data-icon="home">home</span>
          Trang chủ
        </a>
<span class="material-symbols-outlined text-xs" data-icon="chevron_right">chevron_right</span>
<a class="hover:text-primary transition-colors" href="#admin">Quản trị hệ thống</a>
<span class="material-symbols-outlined text-xs" data-icon="chevron_right">chevron_right</span>
<span class="text-on-surface font-semibold">Quy chế học vụ theo khóa</span>
</nav>
<!-- Page Header with Module Badge & Action Cluster -->
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-6 border-b border-outline-variant">
<div>
<div class="flex items-center gap-3">
<h1 class="font-headline-lg text-headline-lg font-bold text-on-surface tracking-tight">Quy chế học vụ theo khóa</h1>
</div>
<p class="font-body-md text-body-md text-on-surface-variant mt-1 max-w-3xl">
            Thiết lập các ngưỡng học vụ, điều kiện tín chỉ, tiêu chuẩn cảnh báo và xếp loại tốt nghiệp áp dụng cho từng khóa tuyển sinh.
          </p>
</div>
<div class="flex items-center gap-2.5 flex-wrap">
<button class="h-10 px-3.5 bg-surface-container-lowest hover:bg-surface-container-low text-on-surface border border-outline-variant rounded-lg font-label-lg text-label-lg flex items-center gap-2 transition-all shadow-xs active:scale-[0.98]" type="button">
<span class="material-symbols-outlined text-lg text-outline" data-icon="content_copy">content_copy</span>
<span>Sao chép quy chế từ khóa trước</span>
</button>
<button class="h-10 px-3 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high rounded-lg font-label-lg text-label-lg flex items-center gap-1.5 transition-all" type="button">
<span class="material-symbols-outlined text-lg" data-icon="history">history</span>
<span>Lịch sử phiên bản</span>
</button>
<button class="h-10 px-5 bg-primary hover:bg-primary-container active:scale-[0.98] text-white font-label-lg text-label-lg rounded-lg flex items-center gap-2 shadow-sm transition-all" type="button">
<span class="material-symbols-outlined text-lg" data-icon="save">save</span>
<span>Lưu quy chế khóa</span>
</button>
</div>
</div>
<!-- Cohort Selector Bar (Level 1 Card) -->
<section class="mt-6 bg-surface-container-lowest border border-outline-variant rounded-xl p-4 shadow-sm">
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
<div class="flex flex-wrap items-center gap-4 flex-1">
<!-- Cohort Selector -->
<div class="flex items-center gap-2">
<label class="font-label-lg text-label-lg text-on-surface whitespace-nowrap font-semibold" for="cohort-select">
                Chọn khóa tuyển sinh áp dụng:
              </label>
<div class="relative min-w-[260px]">
<select aria-label="Khóa 2024 (K24 - Tuyển sinh 2024)" class="w-full h-10 pl-3 pr-8 bg-surface-container-lowest border border-outline rounded-lg text-body-md text-on-surface font-semibold focus:border-primary focus:ring-2 focus:ring-primary/20 appearance-none transition-all" id="cohort-select">
<option selected="">Khóa 2024 (K24 - Tuyển sinh 2024)</option>
<option>Khóa 2023 (K23 - Tuyển sinh 2023)</option>
<option>Khóa 2022 (K22 - Tuyển sinh 2022)</option>
<option>Khóa 2021 (K21 - Tuyển sinh 2021)</option>
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-outline" data-icon="unfold_more">unfold_more</span>
</div>
</div>
<div class="h-6 w-px bg-outline-variant hidden sm:block"></div>
<!-- Regulatory Document Selector -->
<div class="flex items-center gap-2">
<span class="font-label-md text-label-md text-on-surface-variant whitespace-nowrap">Văn bản quy chế gốc:</span>
<div class="relative min-w-[320px]">
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 bg-surface-container-low border border-outline-variant rounded-lg text-body-sm text-on-surface cursor-default focus:outline-none" readonly="" type="text" value="Thông tư 56/2026/TT-BGDĐT &amp; Quy định riêng HUIT"/>
</div>
</div>
</div>
<!-- Status and Student Metric Group -->
<div class="flex items-center gap-4 justify-between lg:justify-end border-t lg:border-t-0 pt-3 lg:pt-0 border-outline-variant">
<!-- Active Status Badge -->
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-label-sm font-semibold bg-[#DCFCE7] text-[#15803D] border border-[#BBF7D0]">
<span class="w-2 h-2 rounded-full bg-[#15803D] animate-pulse"></span>
              Đang áp dụng (Chính thức)
            </span>
<!-- Scale Information -->
<div class="flex items-center gap-1.5 text-body-sm text-on-surface-variant font-medium bg-surface-container-low px-3 py-1 rounded-lg border border-outline-variant">
<span class="material-symbols-outlined text-base text-primary" data-icon="groups">groups</span>
<span>Tổng quy mô: <strong class="text-on-surface font-semibold">4,850 sinh viên</strong></span>
</div>
</div>
</div>
</section>
<!-- Detailed Configuration Cards Stack -->
<div class="mt-6 space-y-5">
<!-- ================================================================= -->
<!-- CARD 1: Khung khối lượng học tập & Tín chỉ mỗi học kỳ             -->
<!-- ================================================================= -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm">
<div class="flex items-center justify-between pb-4 mb-5 border-b border-surface-container">
<div class="flex items-center gap-2.5">
<div class="w-7 h-7 rounded-lg bg-primary-fixed text-primary flex items-center justify-center font-bold text-sm">
                1
              </div>
<h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">
                1. Khung khối lượng học tập &amp; Tín chỉ mỗi học kỳ
              </h2>
</div>
<span class="font-label-sm text-label-sm text-outline">Quy định Điều 8 - Quy chế Đào tạo</span>
</div>
<!-- 4-Column Responsive Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
<div class="space-y-1.5">
<label class="block font-label-md text-label-md text-on-surface font-semibold" for="tc-min">
                Tín chỉ tối thiểu / học kỳ chính
              </label>
<div class="relative">
<input class="w-full h-10 px-3 pr-10 bg-surface-container-lowest border border-outline rounded-lg font-body-md text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all font-semibold" id="tc-min" type="text" value="14 tín chỉ"/>
<span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline text-lg" data-icon="numbers">numbers</span>
</div>
<p class="font-body-sm text-[11px] text-outline">Không áp dụng cho học kỳ cuối khóa</p>
</div>
<div class="space-y-1.5">
<label class="block font-label-md text-label-md text-on-surface font-semibold" for="tc-max-normal">
                Tín chỉ tối đa / học kỳ (Bình thường)
              </label>
<div class="relative">
<input class="w-full h-10 px-3 pr-10 bg-surface-container-lowest border border-outline rounded-lg font-body-md text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all font-semibold" id="tc-max-normal" type="text" value="24 tín chỉ"/>
<span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline text-lg" data-icon="assignment">assignment</span>
</div>
<p class="font-body-sm text-[11px] text-outline">Dành cho sinh viên có GPA học kỳ trước &gt;= 2.0</p>
</div>
<div class="space-y-1.5">
<label class="block font-label-md text-label-md text-on-surface font-semibold" for="tc-max-good">
                Tín chỉ tối đa (Học lực Khá/Giỏi)
              </label>
<div class="relative">
<input class="w-full h-10 px-3 pr-10 bg-surface-container-lowest border border-outline rounded-lg font-body-md text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all font-semibold" id="tc-max-good" type="text" value="28 tín chỉ"/>
<span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline text-lg" data-icon="stars">stars</span>
</div>
<p class="font-body-sm text-[11px] text-outline">Dành cho sinh viên có GPA học kỳ trước &gt;= 2.5</p>
</div>
<div class="space-y-1.5">
<label class="block font-label-md text-label-md text-on-surface font-semibold" for="tc-summer">
                Tín chỉ tối đa học kỳ hè
              </label>
<div class="relative">
<input class="w-full h-10 px-3 pr-10 bg-surface-container-lowest border border-outline rounded-lg font-body-md text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all font-semibold" id="tc-summer" type="text" value="8 tín chỉ"/>
<span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline text-lg" data-icon="wb_sunny">wb_sunny</span>
</div>
<p class="font-body-sm text-[11px] text-outline">Học kỳ phụ tự nguyện</p>
</div>
</div>
<!-- Helper Callout Note -->
<div class="mt-4 p-3.5 bg-surface-container-low border border-primary-fixed rounded-lg flex items-start gap-3">
<span class="material-symbols-outlined text-primary text-xl flex-shrink-0 mt-0.5" data-icon="info">info</span>
<div class="text-body-sm text-on-surface leading-relaxed">
<strong class="font-semibold text-primary">Lưu ý quy chế tín chỉ:</strong> Sinh viên thuộc diện cảnh báo học vụ chỉ được phép đăng ký tối đa <strong class="text-on-surface underline decoration-primary font-bold">14 tín chỉ</strong> trong học kỳ tiếp theo để cải thiện kết quả.
            </div>
</div>
</div>
<!-- ================================================================= -->
<!-- CARD 2: Tiêu chí cảnh báo học vụ & Buộc thôi học                   -->
<!-- ================================================================= -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm">
<div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-5 border-b border-surface-container gap-2">
<div class="flex items-center gap-2.5">
<div class="w-7 h-7 rounded-lg bg-error-container text-error flex items-center justify-center font-bold text-sm">
                2
              </div>
<div>
<h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">
                  2. Tiêu chí cảnh báo học vụ &amp; Buộc thôi học (Theo BA Phụ lục C)
                </h2>
<p class="font-body-sm text-body-sm text-outline mt-0.5">
                  Hệ thống tự động quét và gắn cờ cảnh báo sau khi khóa sổ điểm mỗi học kỳ.
                </p>
</div>
</div>
<span class="inline-flex items-center gap-1 text-label-sm font-semibold text-error bg-error-container/40 px-2.5 py-1 rounded-full border border-error/20">
<span class="material-symbols-outlined text-sm" data-icon="warning">warning</span>
              Quy trình tự động hóa
            </span>
</div>
<!-- Section Row 1: Term Warning Thresholds (3 cols) -->
<div class="mb-5">
<h3 class="font-label-lg text-label-lg font-semibold text-on-surface mb-3 flex items-center gap-1.5">
<span class="material-symbols-outlined text-base text-primary" data-icon="rule">rule</span>
              Ngưỡng điểm trung bình học kỳ (GPA)
            </h3>
<div class="grid grid-cols-1 md:grid-cols-3 gap-5">
<div class="space-y-1.5">
<label class="block font-label-md text-label-md text-on-surface-variant font-medium" for="gpa-warn-1">
                  Ngưỡng GPA học kỳ cảnh báo (Lần 1)
                </label>
<div class="relative">
<input class="w-full h-10 px-3 bg-surface-container-lowest border border-outline rounded-lg font-body-md text-error font-semibold focus:border-error focus:ring-2 focus:ring-error/20 transition-all" id="gpa-warn-1" type="text" value="&lt; 1.00 / 4.0"/>
<span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline text-lg" data-icon="trending_down">trending_down</span>
</div>
<p class="font-body-sm text-[11px] text-outline">Cảnh báo học vụ mức 1</p>
</div>
<div class="space-y-1.5">
<label class="block font-label-md text-label-md text-on-surface-variant font-medium" for="gpa-warn-2">
                  Ngưỡng GPA học kỳ cảnh báo (Lần 2 liên tiếp)
                </label>
<div class="relative">
<input class="w-full h-10 px-3 bg-surface-container-lowest border border-outline rounded-lg font-body-md text-error font-semibold focus:border-error focus:ring-2 focus:ring-error/20 transition-all" id="gpa-warn-2" type="text" value="&lt; 1.10 / 4.0"/>
<span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline text-lg" data-icon="notification_important">notification_important</span>
</div>
<p class="font-body-sm text-[11px] text-outline">Cảnh báo học vụ mức 2</p>
</div>
<div class="space-y-1.5">
<label class="block font-label-md text-label-md text-on-surface-variant font-medium" for="cgpa-y1">
                  Ngưỡng CGPA tích lũy năm 1
                </label>
<div class="relative">
<input class="w-full h-10 px-3 bg-surface-container-lowest border border-outline rounded-lg font-body-md text-on-surface font-semibold focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" id="cgpa-y1" type="text" value="&lt; 1.20 / 4.0"/>
<span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline text-lg" data-icon="school">school</span>
</div>
<p class="font-body-sm text-[11px] text-outline">Xét sau 2 học kỳ chính năm thứ nhất</p>
</div>
</div>
</div>
<!-- Section Row 2: Cumulative GPA & F Credits (3 cols) -->
<div class="mb-5">
<h3 class="font-label-lg text-label-lg font-semibold text-on-surface mb-3 flex items-center gap-1.5">
<span class="material-symbols-outlined text-base text-primary" data-icon="analytics">analytics</span>
              Ngưỡng điểm tích lũy (CGPA) &amp; Tín chỉ rớt
            </h3>
<div class="grid grid-cols-1 md:grid-cols-3 gap-5">
<div class="space-y-1.5">
<label class="block font-label-md text-label-md text-on-surface-variant font-medium" for="cgpa-y2">
                  Ngưỡng CGPA tích lũy năm 2
                </label>
<div class="relative">
<input class="w-full h-10 px-3 bg-surface-container-lowest border border-outline rounded-lg font-body-md text-on-surface font-semibold focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" id="cgpa-y2" type="text" value="&lt; 1.40 / 4.0"/>
<span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline text-lg" data-icon="timeline">timeline</span>
</div>
<p class="font-body-sm text-[11px] text-outline">Xét sau khi kết thúc năm 2</p>
</div>
<div class="space-y-1.5">
<label class="block font-label-md text-label-md text-on-surface-variant font-medium" for="cgpa-y3">
                  Ngưỡng CGPA tích lũy năm 3 trở đi
                </label>
<div class="relative">
<input class="w-full h-10 px-3 bg-surface-container-lowest border border-outline rounded-lg font-body-md text-on-surface font-semibold focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" id="cgpa-y3" type="text" value="&lt; 1.60 / 4.0"/>
<span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline text-lg" data-icon="monitoring">monitoring</span>
</div>
<p class="font-body-sm text-[11px] text-outline">Áp dụng các năm tiếp theo đến khi ra trường</p>
</div>
<div class="space-y-1.5">
<label class="block font-label-md text-label-md text-on-surface-variant font-medium" for="tc-f-max">
                  Số tín chỉ rớt (F) tối đa tích lũy
                </label>
<div class="relative">
<input class="w-full h-10 px-3 bg-surface-container-lowest border border-error/50 rounded-lg font-body-md text-error font-semibold focus:border-error focus:ring-2 focus:ring-error/20 transition-all" id="tc-f-max" type="text" value="24 tín chỉ"/>
<span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-error text-lg" data-icon="error_outline">error_outline</span>
</div>
<p class="font-body-sm text-[11px] text-error">Vượt quá sẽ bị đưa vào danh sách thôi học</p>
</div>
</div>
</div>
<!-- Section Row 3: Critical Expulsion Thresholds (2 cols) -->
<div class="p-4 bg-error-container/20 rounded-xl border border-error-container">
<h3 class="font-label-lg text-label-lg font-semibold text-error mb-3 flex items-center gap-1.5">
<span class="material-symbols-outlined text-base" data-icon="gavel">gavel</span>
              Giới hạn Cảnh báo dẫn đến Buộc thôi học
            </h3>
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
<div class="space-y-1.5">
<label class="block font-label-md text-label-md text-on-surface font-semibold" for="cb-consecutive">
                  Số lần cảnh báo học vụ tối đa liên tiếp
                </label>
<div class="relative">
<input class="w-full h-10 px-3 bg-surface-container-lowest border border-error rounded-lg font-body-md text-error font-bold focus:border-error focus:ring-2 focus:ring-error/20 transition-all" id="cb-consecutive" type="text" value="3 lần (Buộc thôi học)"/>
<span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-error text-lg" data-icon="block">block</span>
</div>
<p class="font-body-sm text-[11px] text-outline">Bị cảnh báo 3 học kỳ liên tiếp sẽ xử lý buộc thôi học</p>
</div>
<div class="space-y-1.5">
<label class="block font-label-md text-label-md text-on-surface font-semibold" for="cb-cumulative">
                  Số lần cảnh báo học vụ tối đa lũy kế
                </label>
<div class="relative">
<input class="w-full h-10 px-3 bg-surface-container-lowest border border-error rounded-lg font-body-md text-error font-bold focus:border-error focus:ring-2 focus:ring-error/20 transition-all" id="cb-cumulative" type="text" value="4 lần (Buộc thôi học)"/>
<span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-error text-lg" data-icon="cancel">cancel</span>
</div>
<p class="font-body-sm text-[11px] text-outline">Tổng số lần cảnh báo trong toàn khóa học không quá 4 lần</p>
</div>
</div>
</div>
</div>
<!-- ================================================================= -->
<!-- CARD 3: Bảng quy đổi điểm & Thang điểm học phần                    -->
<!-- ================================================================= -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm">
<div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-5 border-b border-surface-container gap-2">
<div class="flex items-center gap-2.5">
<div class="w-7 h-7 rounded-lg bg-primary-fixed text-primary flex items-center justify-center font-bold text-sm">
                3
              </div>
<div>
<h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">
                  3. Bảng quy đổi điểm &amp; Thang điểm học phần
                </h2>
<p class="font-body-sm text-body-sm text-outline mt-0.5">
                  Chuẩn quy đổi thang điểm 10 sang thang chữ và thang 4 áp dụng toàn trường.
                </p>
</div>
</div>
<div class="flex items-center gap-2">
<button class="h-8 px-3 bg-surface-container-low hover:bg-surface-container border border-outline-variant rounded-lg text-label-md font-label-md text-on-surface flex items-center gap-1.5 transition-colors" type="button">
<span class="material-symbols-outlined text-base" data-icon="tune">tune</span>
<span>Chỉnh sửa ngưỡng</span>
</button>
</div>
</div>
<!-- Academic Data Table -->
<div class="overflow-x-auto border border-outline-variant rounded-lg">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container-low border-b border-outline-variant">
<th class="py-3 px-4 font-label-md text-label-md uppercase text-outline font-semibold tracking-wider">
                    Thang điểm 10
                  </th>
<th class="py-3 px-4 font-label-md text-label-md uppercase text-outline font-semibold tracking-wider">
                    Thang điểm chữ
                  </th>
<th class="py-3 px-4 font-label-md text-label-md uppercase text-outline font-semibold tracking-wider">
                    Thang điểm 4
                  </th>
<th class="py-3 px-4 font-label-md text-label-md uppercase text-outline font-semibold tracking-wider">
                    Đánh giá / Phân loại
                  </th>
<th class="py-3 px-4 font-label-md text-label-md uppercase text-outline font-semibold tracking-wider text-right">
                    Điểm đạt (Pass)
                  </th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant/60 font-body-sm text-body-sm">
<!-- Row 1: A -->
<tr class="hover:bg-surface-container-low/60 transition-colors">
<td class="py-3 px-4 font-semibold text-on-surface">8.5 - 10.0</td>
<td class="py-3 px-4">
<span class="inline-block w-7 h-7 leading-7 text-center rounded-md font-bold bg-primary-fixed text-primary">A</span>
</td>
<td class="py-3 px-4 font-bold text-on-surface">4.0</td>
<td class="py-3 px-4 text-on-surface font-medium">Xuất sắc / Giỏi</td>
<td class="py-3 px-4 text-right">
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-label-sm font-semibold bg-[#DCFCE7] text-[#15803D]">
<span class="material-symbols-outlined text-xs" data-icon="check_circle">check_circle</span>
                      Đạt
                    </span>
</td>
</tr>
<!-- Row 2: B+ -->
<tr class="hover:bg-surface-container-low/60 transition-colors">
<td class="py-3 px-4 font-semibold text-on-surface">8.0 - 8.4</td>
<td class="py-3 px-4">
<span class="inline-block w-7 h-7 leading-7 text-center rounded-md font-bold bg-surface-container-high text-on-surface">B+</span>
</td>
<td class="py-3 px-4 font-bold text-on-surface">3.5</td>
<td class="py-3 px-4 text-on-surface font-medium">Khá giỏi</td>
<td class="py-3 px-4 text-right">
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-label-sm font-semibold bg-[#DCFCE7] text-[#15803D]">
<span class="material-symbols-outlined text-xs" data-icon="check_circle">check_circle</span>
                      Đạt
                    </span>
</td>
</tr>
<!-- Row 3: B -->
<tr class="hover:bg-surface-container-low/60 transition-colors">
<td class="py-3 px-4 font-semibold text-on-surface">7.0 - 7.9</td>
<td class="py-3 px-4">
<span class="inline-block w-7 h-7 leading-7 text-center rounded-md font-bold bg-surface-container-high text-on-surface">B</span>
</td>
<td class="py-3 px-4 font-bold text-on-surface">3.0</td>
<td class="py-3 px-4 text-on-surface font-medium">Khá</td>
<td class="py-3 px-4 text-right">
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-label-sm font-semibold bg-[#DCFCE7] text-[#15803D]">
<span class="material-symbols-outlined text-xs" data-icon="check_circle">check_circle</span>
                      Đạt
                    </span>
</td>
</tr>
<!-- Row 4: C+ -->
<tr class="hover:bg-surface-container-low/60 transition-colors">
<td class="py-3 px-4 font-semibold text-on-surface">6.5 - 6.9</td>
<td class="py-3 px-4">
<span class="inline-block w-7 h-7 leading-7 text-center rounded-md font-bold bg-surface-container text-on-surface">C+</span>
</td>
<td class="py-3 px-4 font-bold text-on-surface">2.5</td>
<td class="py-3 px-4 text-on-surface font-medium">Trung bình khá</td>
<td class="py-3 px-4 text-right">
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-label-sm font-semibold bg-[#DCFCE7] text-[#15803D]">
<span class="material-symbols-outlined text-xs" data-icon="check_circle">check_circle</span>
                      Đạt
                    </span>
</td>
</tr>
<!-- Row 5: C -->
<tr class="hover:bg-surface-container-low/60 transition-colors">
<td class="py-3 px-4 font-semibold text-on-surface">5.5 - 6.4</td>
<td class="py-3 px-4">
<span class="inline-block w-7 h-7 leading-7 text-center rounded-md font-bold bg-surface-container text-on-surface">C</span>
</td>
<td class="py-3 px-4 font-bold text-on-surface">2.0</td>
<td class="py-3 px-4 text-on-surface font-medium">Trung bình</td>
<td class="py-3 px-4 text-right">
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-label-sm font-semibold bg-[#DCFCE7] text-[#15803D]">
<span class="material-symbols-outlined text-xs" data-icon="check_circle">check_circle</span>
                      Đạt
                    </span>
</td>
</tr>
<!-- Row 6: D+ -->
<tr class="hover:bg-surface-container-low/60 transition-colors">
<td class="py-3 px-4 font-semibold text-on-surface">5.0 - 5.4</td>
<td class="py-3 px-4">
<span class="inline-block w-7 h-7 leading-7 text-center rounded-md font-bold bg-surface-dim text-on-surface">D+</span>
</td>
<td class="py-3 px-4 font-bold text-on-surface">1.5</td>
<td class="py-3 px-4 text-on-surface font-medium">Trung bình yếu</td>
<td class="py-3 px-4 text-right">
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-label-sm font-semibold bg-[#DCFCE7] text-[#15803D]">
<span class="material-symbols-outlined text-xs" data-icon="check_circle">check_circle</span>
                      Đạt
                    </span>
</td>
</tr>
<!-- Row 7: D -->
<tr class="hover:bg-surface-container-low/60 transition-colors">
<td class="py-3 px-4 font-semibold text-on-surface">4.0 - 4.9</td>
<td class="py-3 px-4">
<span class="inline-block w-7 h-7 leading-7 text-center rounded-md font-bold bg-surface-dim text-on-surface">D</span>
</td>
<td class="py-3 px-4 font-bold text-on-surface">1.0</td>
<td class="py-3 px-4 text-on-surface font-medium">Đạt tối thiểu</td>
<td class="py-3 px-4 text-right">
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-label-sm font-semibold bg-[#DCFCE7] text-[#15803D]">
<span class="material-symbols-outlined text-xs" data-icon="check_circle">check_circle</span>
                      Đạt
                    </span>
</td>
</tr>
<!-- Row 8: F -->
<tr class="bg-error-container/10 hover:bg-error-container/20 transition-colors">
<td class="py-3 px-4 font-bold text-error">&lt; 4.0</td>
<td class="py-3 px-4">
<span class="inline-block w-7 h-7 leading-7 text-center rounded-md font-bold bg-error text-on-error">F</span>
</td>
<td class="py-3 px-4 font-bold text-error">0.0</td>
<td class="py-3 px-4 text-error font-semibold">Không đạt (Học lại)</td>
<td class="py-3 px-4 text-right">
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-label-sm font-semibold bg-[#FEE2E2] text-[#B91C1C]">
<span class="material-symbols-outlined text-xs" data-icon="cancel">cancel</span>
                      Rớt / Học lại
                    </span>
</td>
</tr>
</tbody>
</table>
</div>
</div>
<!-- ================================================================= -->
<!-- CARD 4: Tiêu chuẩn xét tốt nghiệp & Xếp hạng bằng                 -->
<!-- ================================================================= -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm">
<div class="flex items-center justify-between pb-4 mb-5 border-b border-surface-container">
<div class="flex items-center gap-2.5">
<div class="w-7 h-7 rounded-lg bg-primary-fixed text-primary flex items-center justify-center font-bold text-sm">
                4
              </div>
<h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">
                4. Tiêu chuẩn xét tốt nghiệp &amp; Xếp hạng bằng
              </h2>
</div>
<span class="font-label-sm text-label-sm text-outline">Quy định Cấp văn bằng Đại học chính quy</span>
</div>
<!-- 4-Column Graduation Classification Bento -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
<div class="p-4 rounded-xl border border-primary/30 bg-surface-container-low">
<div class="flex items-center justify-between mb-2">
<span class="font-label-md text-label-md uppercase font-bold text-primary tracking-wider">Xuất sắc</span>
<span class="material-symbols-outlined text-primary text-xl" data-icon="military_tech">military_tech</span>
</div>
<p class="font-headline-md text-headline-md font-bold text-on-surface">CGPA 3.60 - 4.00</p>
<p class="font-body-sm text-[11px] text-outline mt-1">Điểm rèn luyện toàn khóa &gt;= 90</p>
</div>
<div class="p-4 rounded-xl border border-outline-variant bg-surface-container-lowest">
<div class="flex items-center justify-between mb-2">
<span class="font-label-md text-label-md uppercase font-bold text-on-surface tracking-wider">Giỏi</span>
<span class="material-symbols-outlined text-outline text-xl" data-icon="workspace_premium">workspace_premium</span>
</div>
<p class="font-headline-md text-headline-md font-bold text-on-surface">CGPA 3.20 - 3.59</p>
<p class="font-body-sm text-[11px] text-outline mt-1">Điểm rèn luyện toàn khóa &gt;= 80</p>
</div>
<div class="p-4 rounded-xl border border-outline-variant bg-surface-container-lowest">
<div class="flex items-center justify-between mb-2">
<span class="font-label-md text-label-md uppercase font-bold text-on-surface tracking-wider">Khá</span>
<span class="material-symbols-outlined text-outline text-xl" data-icon="verified">verified</span>
</div>
<p class="font-headline-md text-headline-md font-bold text-on-surface">CGPA 2.50 - 3.19</p>
<p class="font-body-sm text-[11px] text-outline mt-1">Điểm rèn luyện toàn khóa &gt;= 65</p>
</div>
<div class="p-4 rounded-xl border border-outline-variant bg-surface-container-lowest">
<div class="flex items-center justify-between mb-2">
<span class="font-label-md text-label-md uppercase font-bold text-on-surface tracking-wider">Trung bình</span>
<span class="material-symbols-outlined text-outline text-xl" data-icon="task_alt">task_alt</span>
</div>
<p class="font-headline-md text-headline-md font-bold text-on-surface">CGPA 2.00 - 2.49</p>
<p class="font-body-sm text-[11px] text-outline mt-1">Điểm rèn luyện toàn khóa &gt;= 50</p>
</div>
</div>
<!-- Additional Graduation Prerequisite Checklists -->
<div class="pt-4 border-t border-outline-variant">
<h3 class="font-label-lg text-label-lg font-semibold text-on-surface mb-3 flex items-center gap-1.5">
<span class="material-symbols-outlined text-base text-primary" data-icon="checklist">checklist</span>
              Điều kiện phụ và Chuẩn đầu ra bắt buộc toàn khóa
            </h3>
<div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
<label class="flex items-start gap-3 p-3 rounded-lg border border-outline-variant bg-surface-container-low/40 hover:bg-surface-container-low cursor-pointer transition-colors">
<input aria-label="Chọn dòng" checked="" class="mt-0.5 w-4 h-4 rounded text-primary focus:ring-primary focus:ring-offset-0 border-outline" type="checkbox"/>
<div>
<span class="font-body-md text-body-md font-semibold text-on-surface block">
                    Đạt chuẩn đầu ra Ngoại ngữ theo ngành (B1 / TOEIC 450+)
                  </span>
<span class="font-body-sm text-[11px] text-outline">
                    Chứng chỉ quốc tế hoặc kỳ thi đánh giá năng lực của Trường còn hiệu lực.
                  </span>
</div>
</label>
<label class="flex items-start gap-3 p-3 rounded-lg border border-outline-variant bg-surface-container-low/40 hover:bg-surface-container-low cursor-pointer transition-colors">
<input aria-label="Chọn dòng" checked="" class="mt-0.5 w-4 h-4 rounded text-primary focus:ring-primary focus:ring-offset-0 border-outline" type="checkbox"/>
<div>
<span class="font-body-md text-body-md font-semibold text-on-surface block">
                    Đạt chuẩn đầu ra Tin học (MOS / Ứng dụng CNTT cơ bản)
                  </span>
<span class="font-body-sm text-[11px] text-outline">
                    Theo Quy định số 108/QĐ-DCT về tiêu chuẩn công nghệ thông tin.
                  </span>
</div>
</label>
<label class="flex items-start gap-3 p-3 rounded-lg border border-outline-variant bg-surface-container-low/40 hover:bg-surface-container-low cursor-pointer transition-colors">
<input aria-label="Chọn dòng" checked="" class="mt-0.5 w-4 h-4 rounded text-primary focus:ring-primary focus:ring-offset-0 border-outline" type="checkbox"/>
<div>
<span class="font-body-md text-body-md font-semibold text-on-surface block">
                    Hoàn thành chứng chỉ Giáo dục Quốc phòng - An ninh &amp; Giáo dục Thể chất
                  </span>
<span class="font-body-sm text-[11px] text-outline">
                    Không tính vào điểm trung bình chung tích lũy (CGPA) nhưng là điều kiện bắt buộc.
                  </span>
</div>
</label>
<label class="flex items-start gap-3 p-3 rounded-lg border border-outline-variant bg-surface-container-low/40 hover:bg-surface-container-low cursor-pointer transition-colors">
<input aria-label="Chọn dòng" checked="" class="mt-0.5 w-4 h-4 rounded text-primary focus:ring-primary focus:ring-offset-0 border-outline" type="checkbox"/>
<div>
<span class="font-body-md text-body-md font-semibold text-on-surface block">
                    Điểm rèn luyện toàn khóa đạt từ Trung bình trở lên (&gt;= 50 điểm)
                  </span>
<span class="font-body-sm text-[11px] text-outline">
                    Căn cứ quy chế đánh giá kết quả rèn luyện người học HUIT.
                  </span>
</div>
</label>
<label class="flex items-start gap-3 p-3 rounded-lg border border-outline-variant bg-surface-container-low/40 hover:bg-surface-container-low cursor-pointer transition-colors md:col-span-2">
<input aria-label="Chọn dòng" checked="" class="mt-0.5 w-4 h-4 rounded text-primary focus:ring-primary focus:ring-offset-0 border-outline" type="checkbox"/>
<div>
<span class="font-body-md text-body-md font-semibold text-on-surface block">
                    Không trong thời gian bị kỷ luật từ mức cảnh cáo trở lên
                  </span>
<span class="font-body-sm text-[11px] text-outline">
                    Hồ sơ kỷ luật không bị đình chỉ tại thời điểm Hội đồng xét tốt nghiệp họp.
                  </span>
</div>
</label>
</div>
</div>
</div>
</div>
</div>
<!-- ========================================================================= -->
<!-- 4. STICKY FLOATING ACTION BAR (Bottom Canvas)                              -->
<!-- ========================================================================= -->
<div class="sticky bottom-0 z-30 bg-surface-container-lowest/95 backdrop-blur-md border-t border-outline-variant px-6 py-3.5 shadow-lg">
<div class="max-w-[1440px] mx-auto flex flex-col sm:flex-row items-center justify-between gap-3">
<div class="flex items-center gap-3">
<span class="flex h-3 w-3 relative">
<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
<span class="relative inline-flex rounded-full h-3 w-3 bg-primary"></span>
</span>
<p class="font-body-md text-body-md text-on-surface">
            Bạn đang chỉnh sửa quy chế <strong class="text-primary font-bold">Khóa 2024 (K24)</strong>. Nhấn Lưu để ghi nhận phiên bản mới.
          </p>
</div>
<div class="flex items-center gap-3 w-full sm:w-auto justify-end">
<button class="h-10 px-4 bg-surface-container-lowest hover:bg-surface-container-low text-on-surface-variant hover:text-on-surface border border-outline-variant rounded-lg font-label-lg text-label-lg transition-all active:scale-95" type="button">
            Hủy bỏ
          </button>
<button class="h-10 px-6 bg-primary hover:bg-primary-container active:scale-95 text-white font-label-lg text-label-lg rounded-lg flex items-center gap-2 shadow-sm transition-all" type="button">
<span class="material-symbols-outlined text-lg" data-icon="save">save</span>
<span>Lưu quy chế khóa</span>
</button>
</div>
</div>
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
