@extends('fe_preview.layouts.admin')
@section('page_title', 'Chi tiết Lớp học phần - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Chi tiết Lớp học phần - HUIT Học Vụ</title>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Chi tiết Lớp học phần</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Chi tiết Lớp học phần</div>
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
<!-- ==================== HERO CARD: CHI TIẾT LỚP HỌC PHẦN ==================== -->
<section class="bg-surface-container-lowest rounded-xl border border-slate-200 p-6 shadow-sm">
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b border-slate-100">
<!-- Left: Title, Badges & Identifiers -->
<div class="space-y-3">
<div class="flex flex-wrap items-center gap-2.5">
<span class="px-2.5 py-1 bg-surface-container-low text-primary rounded-md font-mono text-body-sm font-semibold border border-primary/20">
                241_71ITEC3201_01
              </span>
<span class="px-2.5 py-1 bg-slate-100 text-secondary rounded-md font-label-sm text-label-sm">
                Mã lớp HK1 2026-2027
              </span>
<!-- Status Chip -->
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-label-md text-label-md bg-[#DCFCE7] text-[#15803D]">
<span class="w-2 h-2 rounded-full bg-[#15803D] animate-pulse"></span>
                Đang mở đăng ký
              </span>
</div>
<div>
<h2 class="font-headline-xl text-headline-xl text-on-surface font-bold tracking-tight">
                Lập trình Web
              </h2>
<p class="font-body-md text-body-md text-outline mt-0.5">
                Học phần cơ sở chuyên ngành CNTT • 3 Tín chỉ chuẩn (Mã học phần: <span class="font-mono text-on-surface">ITEC3201</span>)
              </p>
</div>
</div>
<!-- Right: Actions Cluster -->
<div class="flex flex-wrap items-center gap-2.5">
<button class="px-3.5 py-2 bg-surface-container-lowest border border-slate-200 hover:border-slate-300 text-on-surface hover:bg-slate-50 rounded-lg font-label-lg text-label-lg flex items-center gap-2 shadow-xs transition-colors" onclick="document.getElementById('editModal').classList.remove('hidden')">
<span class="material-symbols-outlined text-[18px] text-outline">edit</span>
<span>Sửa thông tin</span>
</button>
<button class="px-3.5 py-2 bg-amber-50 border border-amber-200 text-amber-800 hover:bg-amber-100 rounded-lg font-label-lg text-label-lg flex items-center gap-2 shadow-xs transition-colors" onclick="openModal('confirmLockModal')">
<span class="material-symbols-outlined text-[18px] text-amber-700">lock</span>
<span>Chốt lớp học phần</span>
</button>
<button class="px-3.5 py-2 bg-surface-container-lowest border border-slate-200 hover:border-slate-300 text-on-surface hover:bg-slate-50 rounded-lg font-label-lg text-label-lg flex items-center gap-2 shadow-xs transition-colors" onclick="window.print()">
<span class="material-symbols-outlined text-[18px] text-outline">print</span>
<span>In danh sách</span>
</button>
<button class="px-4 py-2 bg-primary hover:bg-[#3B66D1] text-white rounded-lg font-label-lg text-label-lg flex items-center gap-2 shadow-sm transition-colors">
<span class="material-symbols-outlined text-[18px]">download</span>
<span>Xuất Excel</span>
</button>
</div>
</div>
<!-- Bottom Hero: Sĩ số & Trực quan tiến độ -->
<div class="pt-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
<div class="flex-1 max-w-xl">
<div class="flex items-center justify-between mb-2">
<div class="flex items-center gap-2">
<span class="font-label-lg text-label-lg text-on-surface">Sĩ số lớp học phần:</span>
<span class="font-headline-sm text-headline-sm font-bold text-primary">48 / 60 Sinh viên</span>
<span class="text-xs text-outline font-normal">(80% chỉ tiêu)</span>
</div>
<span class="font-label-md text-label-md text-[#15803D] font-semibold bg-[#DCFCE7] px-2 py-0.5 rounded">
                Còn trống 12 chỗ
              </span>
</div>
<!-- Progress Bar -->
<div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden flex">
<div class="h-full bg-primary rounded-full transition-all duration-500" style="width: 80%;"></div>
</div>
</div>
<div class="flex items-center gap-6 text-body-sm text-outline border-t md:border-t-0 md:border-l border-slate-100 pt-3 md:pt-0 md:pl-6">
<div>
<span class="block text-slate-500 text-xs">Hình thức học</span>
<span class="font-semibold text-on-surface">Tập trung (Trực tiếp)</span>
</div>
<div>
<span class="block text-slate-500 text-xs">Cơ sở giảng dạy</span>
<span class="font-semibold text-on-surface">Tân Phú (140 Lê Trọng Tấn)</span>
</div>
<div>
<span class="block text-slate-500 text-xs">Hạn hủy đăng ký</span>
<span class="font-semibold text-on-surface">20/10/2026</span>
</div>
</div>
</div>
</section>
<!-- ==================== 4 QUICK KPI CARDS ==================== -->
<section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
<!-- KPI 1 -->
<div class="bg-surface-container-lowest border border-slate-200 rounded-xl p-4 shadow-sm hover:border-primary/40 transition-colors">
<div class="flex items-start gap-3">
<div class="p-2.5 rounded-lg bg-surface-container-low text-primary flex-shrink-0">
<span class="material-symbols-outlined text-[22px]">calendar_month</span>
</div>
<div class="min-w-0">
<p class="font-body-sm text-body-sm text-outline">Thời khóa biểu &amp; Phòng</p>
<h4 class="font-label-lg text-label-lg text-on-surface mt-1 truncate">Thứ 2, Tiết 1 - 3</h4>
<p class="font-body-sm text-body-sm text-primary font-medium mt-0.5 truncate">Phòng B.204 • CS Tân Phú</p>
</div>
</div>
</div>
<!-- KPI 2 -->
<div class="bg-surface-container-lowest border border-slate-200 rounded-xl p-4 shadow-sm hover:border-primary/40 transition-colors">
<div class="flex items-start gap-3">
<div class="p-2.5 rounded-lg bg-emerald-50 text-emerald-700 flex-shrink-0">
<span class="material-symbols-outlined text-[22px]">auto_stories</span>
</div>
<div class="min-w-0">
<p class="font-body-sm text-body-sm text-outline">Cấu trúc tín chỉ</p>
<h4 class="font-label-lg text-label-lg text-on-surface mt-1 truncate">3 Tín chỉ (2 LT + 1 TH)</h4>
<p class="font-body-sm text-body-sm text-emerald-700 font-medium mt-0.5 truncate">45 tiết định mức chuẩn</p>
</div>
</div>
</div>
<!-- KPI 3 -->
<div class="bg-surface-container-lowest border border-slate-200 rounded-xl p-4 shadow-sm hover:border-primary/40 transition-colors">
<div class="flex items-start gap-3">
<div class="p-2.5 rounded-lg bg-purple-50 text-purple-700 flex-shrink-0">
<span class="material-symbols-outlined text-[22px]">badge</span>
</div>
<div class="min-w-0">
<p class="font-body-sm text-body-sm text-outline">Giảng viên phụ trách</p>
<h4 class="font-label-lg text-label-lg text-on-surface mt-1 truncate">TS. Trần Hoàng Nam</h4>
<p class="font-body-sm text-body-sm text-purple-700 font-medium mt-0.5 truncate">Khoa CNTT • Bộ môn Mạng &amp; ATTT</p>
</div>
</div>
</div>
<!-- KPI 4 -->
<div class="bg-surface-container-lowest border border-slate-200 rounded-xl p-4 shadow-sm hover:border-primary/40 transition-colors">
<div class="flex items-start gap-3">
<div class="p-2.5 rounded-lg bg-sky-50 text-sky-700 flex-shrink-0">
<span class="material-symbols-outlined text-[22px]">verified_user</span>
</div>
<div class="min-w-0">
<p class="font-body-sm text-body-sm text-outline">Tiến độ đăng ký học phần</p>
<h4 class="font-label-lg text-label-lg text-on-surface mt-1 truncate">48 sinh viên hợp lệ</h4>
<p class="font-body-sm text-body-sm text-sky-700 font-medium mt-0.5 truncate">0 trường hợp trùng lịch</p>
</div>
</div>
</div>
</section>
<!-- ==================== TABS CONTAINER & MAIN DATA CARD ==================== -->
<section class="bg-surface-container-lowest rounded-xl border border-slate-200 shadow-sm overflow-hidden">
<!-- Tab Navigation Bar -->
<div class="border-b border-slate-200 bg-surface-container-lowest px-6 flex items-center justify-between">
<div class="flex space-x-8">
<!-- Tab 1 -->
<button class="py-4 text-outline hover:text-on-surface font-label-lg text-label-lg flex items-center gap-2 border-b-2 border-transparent transition-colors">
<span class="material-symbols-outlined text-[20px]">info</span>
<span>Thông tin lớp</span>
</button>
<!-- Tab 2 -->
<button class="py-4 text-outline hover:text-on-surface font-label-lg text-label-lg flex items-center gap-2 border-b-2 border-transparent transition-colors">
<span class="material-symbols-outlined text-[20px]">person</span>
<span>Giảng viên</span>
</button>
<!-- Tab 3: ACTIVE -->
<button class="py-4 text-primary font-headline-sm text-headline-sm flex items-center gap-2 border-b-2 border-primary transition-colors">
<span class="material-symbols-outlined text-[20px] text-primary">groups</span>
<span>Sinh viên đã đăng ký</span>
<span class="px-2 py-0.5 bg-primary-fixed text-primary rounded-full text-label-sm font-semibold ml-1">48</span>
</button>
<!-- Tab 4 -->
<button class="py-4 text-outline hover:text-on-surface font-label-lg text-label-lg flex items-center gap-2 border-b-2 border-transparent transition-colors">
<span class="material-symbols-outlined text-[20px]">grade</span>
<span>Bảng điểm lớp học phần</span>
<span class="text-[11px] bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded font-medium">HK1</span>
</button>
</div>
<div class="text-body-sm text-outline hidden sm:block">
            Cập nhật thời gian thực: <span class="font-mono text-on-surface">14:32:10 12/10/2026</span>
</div>
</div>
<!-- Tab Content: Sinh viên đã đăng ký (48) -->
<div class="p-6 space-y-4">
<!-- Toolbar & Filters -->
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-surface p-3.5 rounded-lg border border-slate-200">
<div class="flex flex-wrap items-center gap-3 flex-1">
<!-- Search Student -->
<div class="relative min-w-[280px] flex-1 max-w-sm">
<span class="material-symbols-outlined absolute left-3 top-2.5 text-outline text-[18px]">search</span>
<input aria-label="Tìm theo MSSV, họ và tên sinh viên..." class="w-full h-9 pl-9 pr-4 bg-surface-container-lowest border border-slate-300 rounded-lg text-body-sm font-body-sm placeholder:text-outline focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20" placeholder="Tìm theo MSSV, họ và tên sinh viên..." type="text"/>
</div>
<!-- Filter: Lớp hành chính -->
<div class="flex items-center gap-2">
<span class="font-label-md text-label-md text-outline">Lớp HC:</span>
<select aria-label="Tất cả lớp hành chính" class="h-9 bg-surface-container-lowest border border-slate-300 rounded-lg text-body-sm font-body-sm px-3 pr-8 focus:outline-none focus:border-primary">
<option value="all">Tất cả lớp hành chính</option>
<option value="12DHTH01">12DHTH01</option>
<option value="12DHTH02">12DHTH02</option>
<option value="12DHPM01">12DHPM01</option>
<option value="12DHTH03">12DHTH03</option>
</select>
</div>
<!-- Filter: Trạng thái học vụ -->
<div class="flex items-center gap-2">
<span class="font-label-md text-label-md text-outline">Học vụ:</span>
<select aria-label="Tất cả trạng thái" class="h-9 bg-surface-container-lowest border border-slate-300 rounded-lg text-body-sm font-body-sm px-3 pr-8 focus:outline-none focus:border-primary">
<option value="all">Tất cả trạng thái</option>
<option value="normal">Bình thường</option>
<option value="probation">Cảnh báo học vụ</option>
</select>
</div>
</div>
<!-- Export / Secondary Actions -->
<div class="flex items-center gap-2 flex-shrink-0">
<button class="h-9 px-3 bg-surface-container-lowest border border-slate-300 hover:bg-slate-50 text-on-surface rounded-lg font-label-md text-label-md flex items-center gap-1.5 transition-colors">
<span class="material-symbols-outlined text-[17px] text-emerald-700">table_view</span>
<span>Xuất DS điểm quá trình (.xlsx)</span>
</button>
<button class="h-9 px-3 bg-surface-container-lowest border border-slate-300 hover:bg-slate-50 text-on-surface rounded-lg font-label-md text-label-md flex items-center gap-1.5 transition-colors">
<span class="material-symbols-outlined text-[17px] text-outline">contract_edit</span>
<span>In bảng ký tên</span>
</button>
</div>
</div>
<!-- Table Container with Horizontal Scroll & Sticky Columns -->
<div class="border border-slate-200 rounded-lg overflow-hidden bg-surface-container-lowest">
<div class="overflow-x-auto custom-scrollbar">
<table class="w-full text-left border-collapse min-w-[1100px]">
<thead>
<tr class="bg-slate-50 border-b border-slate-200 text-outline font-label-md text-label-md uppercase tracking-wider text-[11px]">
<th class="py-3 px-3 w-10 text-center sticky left-0 bg-slate-50 z-20 shadow-[1px_0_0_0_#e2e8f0]">
<input aria-label="Chọn dòng" class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary" type="checkbox"/>
</th>
<th class="py-3 px-3 w-12 text-center sticky left-10 bg-slate-50 z-20 shadow-[1px_0_0_0_#e2e8f0]">STT</th>
<th class="py-3 px-4 w-32 sticky left-[88px] bg-slate-50 z-20 shadow-[1px_0_0_0_#e2e8f0]">MSSV</th>
<th class="py-3 px-4 min-w-[200px] sticky left-[216px] bg-slate-50 z-20 shadow-[2px_0_4px_rgba(0,0,0,0.04)]">Họ và tên</th>
<th class="py-3 px-4">Lớp hành chính</th>
<th class="py-3 px-4">Ngành đào tạo</th>
<th class="py-3 px-4">Khóa học</th>
<th class="py-3 px-4">Thời gian đăng ký</th>
<th class="py-3 px-4">Trạng thái</th>
<th class="py-3 px-4 text-center">Thao tác</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 font-body-sm text-body-sm">
<!-- Row 1 -->
<tr class="hover:bg-slate-50/80 transition-colors group">
<td class="py-3 px-3 text-center sticky left-0 bg-white group-hover:bg-slate-50/80 z-10 shadow-[1px_0_0_0_#e2e8f0]">
<input aria-label="Chọn dòng" class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary" type="checkbox"/>
</td>
<td class="py-3 px-3 text-center text-outline sticky left-10 bg-white group-hover:bg-slate-50/80 z-10 shadow-[1px_0_0_0_#e2e8f0]">1</td>
<td class="py-3 px-4 font-mono font-medium text-primary sticky left-[88px] bg-white group-hover:bg-slate-50/80 z-10 shadow-[1px_0_0_0_#e2e8f0]">2001210123</td>
<td class="py-3 px-4 font-semibold text-on-surface sticky left-[216px] bg-white group-hover:bg-slate-50/80 z-10 shadow-[2px_0_4px_rgba(0,0,0,0.04)]">
                      Nguyễn Văn An
                    </td>
<td class="py-3 px-4 text-secondary font-medium">12DHTH01</td>
<td class="py-3 px-4 text-on-surface">Công nghệ Thông tin</td>
<td class="py-3 px-4 text-outline">Khóa 2021 (K21)</td>
<td class="py-3 px-4 text-outline font-mono text-[11px]">12/10/2026 08:15</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-label-sm text-label-sm bg-[#DCFCE7] text-[#15803D]">
<span class="w-1.5 h-1.5 rounded-full bg-[#15803D]"></span>
                        Thành công
                      </span>
</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1.5">
<button class="p-1.5 text-outline hover:text-error hover:bg-error-container/20 rounded transition-colors" onclick="openDeregisterModal('Nguyễn Văn An', '2001210123')" title="Hủy đăng ký học phần">
<span class="material-symbols-outlined text-[18px]">person_remove</span>
</button>
<button class="p-1.5 text-outline hover:text-primary hover:bg-surface-container-low rounded transition-colors" title="Xem hồ sơ sinh viên">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
</div>
</td>
</tr>
<!-- Row 2 -->
<tr class="hover:bg-slate-50/80 transition-colors group">
<td class="py-3 px-3 text-center sticky left-0 bg-white group-hover:bg-slate-50/80 z-10 shadow-[1px_0_0_0_#e2e8f0]">
<input aria-label="Chọn dòng" class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary" type="checkbox"/>
</td>
<td class="py-3 px-3 text-center text-outline sticky left-10 bg-white group-hover:bg-slate-50/80 z-10 shadow-[1px_0_0_0_#e2e8f0]">2</td>
<td class="py-3 px-4 font-mono font-medium text-primary sticky left-[88px] bg-white group-hover:bg-slate-50/80 z-10 shadow-[1px_0_0_0_#e2e8f0]">2001210045</td>
<td class="py-3 px-4 font-semibold text-on-surface sticky left-[216px] bg-white group-hover:bg-slate-50/80 z-10 shadow-[2px_0_4px_rgba(0,0,0,0.04)]">
                      Lê Thị Thu Thảo
                    </td>
<td class="py-3 px-4 text-secondary font-medium">12DHTH01</td>
<td class="py-3 px-4 text-on-surface">Công nghệ Thông tin</td>
<td class="py-3 px-4 text-outline">Khóa 2021 (K21)</td>
<td class="py-3 px-4 text-outline font-mono text-[11px]">12/10/2026 08:22</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-label-sm text-label-sm bg-[#DCFCE7] text-[#15803D]">
<span class="w-1.5 h-1.5 rounded-full bg-[#15803D]"></span>
                        Thành công
                      </span>
</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1.5">
<button class="p-1.5 text-outline hover:text-error hover:bg-error-container/20 rounded transition-colors" onclick="openDeregisterModal('Lê Thị Thu Thảo', '2001210045')" title="Hủy đăng ký học phần">
<span class="material-symbols-outlined text-[18px]">person_remove</span>
</button>
<button class="p-1.5 text-outline hover:text-primary hover:bg-surface-container-low rounded transition-colors" title="Xem hồ sơ sinh viên">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
</div>
</td>
</tr>
<!-- Row 3 -->
<tr class="hover:bg-slate-50/80 transition-colors group">
<td class="py-3 px-3 text-center sticky left-0 bg-white group-hover:bg-slate-50/80 z-10 shadow-[1px_0_0_0_#e2e8f0]">
<input aria-label="Chọn dòng" class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary" type="checkbox"/>
</td>
<td class="py-3 px-3 text-center text-outline sticky left-10 bg-white group-hover:bg-slate-50/80 z-10 shadow-[1px_0_0_0_#e2e8f0]">3</td>
<td class="py-3 px-4 font-mono font-medium text-primary sticky left-[88px] bg-white group-hover:bg-slate-50/80 z-10 shadow-[1px_0_0_0_#e2e8f0]">2001210189</td>
<td class="py-3 px-4 font-semibold text-on-surface sticky left-[216px] bg-white group-hover:bg-slate-50/80 z-10 shadow-[2px_0_4px_rgba(0,0,0,0.04)]">
                      Trần Quốc Bảo
                    </td>
<td class="py-3 px-4 text-secondary font-medium">12DHPM01</td>
<td class="py-3 px-4 text-on-surface">Kỹ thuật Phần mềm</td>
<td class="py-3 px-4 text-outline">Khóa 2021 (K21)</td>
<td class="py-3 px-4 text-outline font-mono text-[11px]">12/10/2026 08:35</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-label-sm text-label-sm bg-[#DCFCE7] text-[#15803D]">
<span class="w-1.5 h-1.5 rounded-full bg-[#15803D]"></span>
                        Thành công
                      </span>
</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1.5">
<button class="p-1.5 text-outline hover:text-error hover:bg-error-container/20 rounded transition-colors" onclick="openDeregisterModal('Trần Quốc Bảo', '2001210189')" title="Hủy đăng ký học phần">
<span class="material-symbols-outlined text-[18px]">person_remove</span>
</button>
<button class="p-1.5 text-outline hover:text-primary hover:bg-surface-container-low rounded transition-colors" title="Xem hồ sơ sinh viên">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
</div>
</td>
</tr>
<!-- Row 4 -->
<tr class="hover:bg-slate-50/80 transition-colors group">
<td class="py-3 px-3 text-center sticky left-0 bg-white group-hover:bg-slate-50/80 z-10 shadow-[1px_0_0_0_#e2e8f0]">
<input aria-label="Chọn dòng" class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary" type="checkbox"/>
</td>
<td class="py-3 px-3 text-center text-outline sticky left-10 bg-white group-hover:bg-slate-50/80 z-10 shadow-[1px_0_0_0_#e2e8f0]">4</td>
<td class="py-3 px-4 font-mono font-medium text-primary sticky left-[88px] bg-white group-hover:bg-slate-50/80 z-10 shadow-[1px_0_0_0_#e2e8f0]">2001210012</td>
<td class="py-3 px-4 font-semibold text-on-surface sticky left-[216px] bg-white group-hover:bg-slate-50/80 z-10 shadow-[2px_0_4px_rgba(0,0,0,0.04)]">
                      Phạm Minh Hoàng
                    </td>
<td class="py-3 px-4 text-secondary font-medium">12DHTH02</td>
<td class="py-3 px-4 text-on-surface">Công nghệ Thông tin</td>
<td class="py-3 px-4 text-outline">Khóa 2021 (K21)</td>
<td class="py-3 px-4 text-outline font-mono text-[11px]">12/10/2026 08:41</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-label-sm text-label-sm bg-[#DCFCE7] text-[#15803D]">
<span class="w-1.5 h-1.5 rounded-full bg-[#15803D]"></span>
                        Thành công
                      </span>
</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1.5">
<button class="p-1.5 text-outline hover:text-error hover:bg-error-container/20 rounded transition-colors" onclick="openDeregisterModal('Phạm Minh Hoàng', '2001210012')" title="Hủy đăng ký học phần">
<span class="material-symbols-outlined text-[18px]">person_remove</span>
</button>
<button class="p-1.5 text-outline hover:text-primary hover:bg-surface-container-low rounded transition-colors" title="Xem hồ sơ sinh viên">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
</div>
</td>
</tr>
<!-- Row 5 -->
<tr class="hover:bg-slate-50/80 transition-colors group">
<td class="py-3 px-3 text-center sticky left-0 bg-white group-hover:bg-slate-50/80 z-10 shadow-[1px_0_0_0_#e2e8f0]">
<input aria-label="Chọn dòng" class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary" type="checkbox"/>
</td>
<td class="py-3 px-3 text-center text-outline sticky left-10 bg-white group-hover:bg-slate-50/80 z-10 shadow-[1px_0_0_0_#e2e8f0]">5</td>
<td class="py-3 px-4 font-mono font-medium text-primary sticky left-[88px] bg-white group-hover:bg-slate-50/80 z-10 shadow-[1px_0_0_0_#e2e8f0]">2001210341</td>
<td class="py-3 px-4 font-semibold text-on-surface sticky left-[216px] bg-white group-hover:bg-slate-50/80 z-10 shadow-[2px_0_4px_rgba(0,0,0,0.04)]">
                      Vũ Đình Trọng
                    </td>
<td class="py-3 px-4 text-secondary font-medium">12DHTH01</td>
<td class="py-3 px-4 text-on-surface">Công nghệ Thông tin</td>
<td class="py-3 px-4 text-outline">Khóa 2021 (K21)</td>
<td class="py-3 px-4 text-outline font-mono text-[11px]">12/10/2026 09:03</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-label-sm text-label-sm bg-[#DCFCE7] text-[#15803D]">
<span class="w-1.5 h-1.5 rounded-full bg-[#15803D]"></span>
                        Thành công
                      </span>
</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1.5">
<button class="p-1.5 text-outline hover:text-error hover:bg-error-container/20 rounded transition-colors" onclick="openDeregisterModal('Vũ Đình Trọng', '2001210341')" title="Hủy đăng ký học phần">
<span class="material-symbols-outlined text-[18px]">person_remove</span>
</button>
<button class="p-1.5 text-outline hover:text-primary hover:bg-surface-container-low rounded transition-colors" title="Xem hồ sơ sinh viên">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
</div>
</td>
</tr>
<!-- Row 6 -->
<tr class="hover:bg-slate-50/80 transition-colors group">
<td class="py-3 px-3 text-center sticky left-0 bg-white group-hover:bg-slate-50/80 z-10 shadow-[1px_0_0_0_#e2e8f0]">
<input aria-label="Chọn dòng" class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary" type="checkbox"/>
</td>
<td class="py-3 px-3 text-center text-outline sticky left-10 bg-white group-hover:bg-slate-50/80 z-10 shadow-[1px_0_0_0_#e2e8f0]">6</td>
<td class="py-3 px-4 font-mono font-medium text-primary sticky left-[88px] bg-white group-hover:bg-slate-50/80 z-10 shadow-[1px_0_0_0_#e2e8f0]">2001210419</td>
<td class="py-3 px-4 font-semibold text-on-surface sticky left-[216px] bg-white group-hover:bg-slate-50/80 z-10 shadow-[2px_0_4px_rgba(0,0,0,0.04)]">
                      Đặng Phương Nam
                    </td>
<td class="py-3 px-4 text-secondary font-medium">12DHTH02</td>
<td class="py-3 px-4 text-on-surface">Công nghệ Thông tin</td>
<td class="py-3 px-4 text-outline">Khóa 2021 (K21)</td>
<td class="py-3 px-4 text-outline font-mono text-[11px]">12/10/2026 09:12</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-label-sm text-label-sm bg-[#DCFCE7] text-[#15803D]">
<span class="w-1.5 h-1.5 rounded-full bg-[#15803D]"></span>
                        Thành công
                      </span>
</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1.5">
<button class="p-1.5 text-outline hover:text-error hover:bg-error-container/20 rounded transition-colors" onclick="openDeregisterModal('Đặng Phương Nam', '2001210419')" title="Hủy đăng ký học phần">
<span class="material-symbols-outlined text-[18px]">person_remove</span>
</button>
<button class="p-1.5 text-outline hover:text-primary hover:bg-surface-container-low rounded transition-colors" title="Xem hồ sơ sinh viên">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
</div>
</td>
</tr>
<!-- Row 7 -->
<tr class="hover:bg-slate-50/80 transition-colors group">
<td class="py-3 px-3 text-center sticky left-0 bg-white group-hover:bg-slate-50/80 z-10 shadow-[1px_0_0_0_#e2e8f0]">
<input aria-label="Chọn dòng" class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary" type="checkbox"/>
</td>
<td class="py-3 px-3 text-center text-outline sticky left-10 bg-white group-hover:bg-slate-50/80 z-10 shadow-[1px_0_0_0_#e2e8f0]">7</td>
<td class="py-3 px-4 font-mono font-medium text-primary sticky left-[88px] bg-white group-hover:bg-slate-50/80 z-10 shadow-[1px_0_0_0_#e2e8f0]">2001210512</td>
<td class="py-3 px-4 font-semibold text-on-surface sticky left-[216px] bg-white group-hover:bg-slate-50/80 z-10 shadow-[2px_0_4px_rgba(0,0,0,0.04)]">
                      Hoàng Kim Tuyến
                    </td>
<td class="py-3 px-4 text-secondary font-medium">12DHPM01</td>
<td class="py-3 px-4 text-on-surface">Kỹ thuật Phần mềm</td>
<td class="py-3 px-4 text-outline">Khóa 2021 (K21)</td>
<td class="py-3 px-4 text-outline font-mono text-[11px]">12/10/2026 09:20</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-label-sm text-label-sm bg-[#DCFCE7] text-[#15803D]">
<span class="w-1.5 h-1.5 rounded-full bg-[#15803D]"></span>
                        Thành công
                      </span>
</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1.5">
<button class="p-1.5 text-outline hover:text-error hover:bg-error-container/20 rounded transition-colors" onclick="openDeregisterModal('Hoàng Kim Tuyến', '2001210512')" title="Hủy đăng ký học phần">
<span class="material-symbols-outlined text-[18px]">person_remove</span>
</button>
<button class="p-1.5 text-outline hover:text-primary hover:bg-surface-container-low rounded transition-colors" title="Xem hồ sơ sinh viên">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
</div>
</td>
</tr>
<!-- Row 8 -->
<tr class="hover:bg-slate-50/80 transition-colors group">
<td class="py-3 px-3 text-center sticky left-0 bg-white group-hover:bg-slate-50/80 z-10 shadow-[1px_0_0_0_#e2e8f0]">
<input aria-label="Chọn dòng" class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary" type="checkbox"/>
</td>
<td class="py-3 px-3 text-center text-outline sticky left-10 bg-white group-hover:bg-slate-50/80 z-10 shadow-[1px_0_0_0_#e2e8f0]">8</td>
<td class="py-3 px-4 font-mono font-medium text-primary sticky left-[88px] bg-white group-hover:bg-slate-50/80 z-10 shadow-[1px_0_0_0_#e2e8f0]">2001210603</td>
<td class="py-3 px-4 font-semibold text-on-surface sticky left-[216px] bg-white group-hover:bg-slate-50/80 z-10 shadow-[2px_0_4px_rgba(0,0,0,0.04)]">
                      Bùi Ngọc Mai
                    </td>
<td class="py-3 px-4 text-secondary font-medium">12DHTH03</td>
<td class="py-3 px-4 text-on-surface">Công nghệ Thông tin</td>
<td class="py-3 px-4 text-outline">Khóa 2021 (K21)</td>
<td class="py-3 px-4 text-outline font-mono text-[11px]">12/10/2026 09:44</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-label-sm text-label-sm bg-[#DCFCE7] text-[#15803D]">
<span class="w-1.5 h-1.5 rounded-full bg-[#15803D]"></span>
                        Thành công
                      </span>
</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1.5">
<button class="p-1.5 text-outline hover:text-error hover:bg-error-container/20 rounded transition-colors" onclick="openDeregisterModal('Bùi Ngọc Mai', '2001210603')" title="Hủy đăng ký học phần">
<span class="material-symbols-outlined text-[18px]">person_remove</span>
</button>
<button class="p-1.5 text-outline hover:text-primary hover:bg-surface-container-low rounded transition-colors" title="Xem hồ sơ sinh viên">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
</div>
</td>
</tr>
<!-- Row 9 -->
<tr class="hover:bg-slate-50/80 transition-colors group">
<td class="py-3 px-3 text-center sticky left-0 bg-white group-hover:bg-slate-50/80 z-10 shadow-[1px_0_0_0_#e2e8f0]">
<input aria-label="Chọn dòng" class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary" type="checkbox"/>
</td>
<td class="py-3 px-3 text-center text-outline sticky left-10 bg-white group-hover:bg-slate-50/80 z-10 shadow-[1px_0_0_0_#e2e8f0]">9</td>
<td class="py-3 px-4 font-mono font-medium text-primary sticky left-[88px] bg-white group-hover:bg-slate-50/80 z-10 shadow-[1px_0_0_0_#e2e8f0]">2001210724</td>
<td class="py-3 px-4 font-semibold text-on-surface sticky left-[216px] bg-white group-hover:bg-slate-50/80 z-10 shadow-[2px_0_4px_rgba(0,0,0,0.04)]">
                      Lý Gia Hân
                    </td>
<td class="py-3 px-4 text-secondary font-medium">12DHTH01</td>
<td class="py-3 px-4 text-on-surface">Công nghệ Thông tin</td>
<td class="py-3 px-4 text-outline">Khóa 2021 (K21)</td>
<td class="py-3 px-4 text-outline font-mono text-[11px]">12/10/2026 10:02</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-label-sm text-label-sm bg-[#DCFCE7] text-[#15803D]">
<span class="w-1.5 h-1.5 rounded-full bg-[#15803D]"></span>
                        Thành công
                      </span>
</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1.5">
<button class="p-1.5 text-outline hover:text-error hover:bg-error-container/20 rounded transition-colors" onclick="openDeregisterModal('Lý Gia Hân', '2001210724')" title="Hủy đăng ký học phần">
<span class="material-symbols-outlined text-[18px]">person_remove</span>
</button>
<button class="p-1.5 text-outline hover:text-primary hover:bg-surface-container-low rounded transition-colors" title="Xem hồ sơ sinh viên">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
</div>
</td>
</tr>
<!-- Row 10 -->
<tr class="hover:bg-slate-50/80 transition-colors group">
<td class="py-3 px-3 text-center sticky left-0 bg-white group-hover:bg-slate-50/80 z-10 shadow-[1px_0_0_0_#e2e8f0]">
<input aria-label="Chọn dòng" class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary" type="checkbox"/>
</td>
<td class="py-3 px-3 text-center text-outline sticky left-10 bg-white group-hover:bg-slate-50/80 z-10 shadow-[1px_0_0_0_#e2e8f0]">10</td>
<td class="py-3 px-4 font-mono font-medium text-primary sticky left-[88px] bg-white group-hover:bg-slate-50/80 z-10 shadow-[1px_0_0_0_#e2e8f0]">2001210889</td>
<td class="py-3 px-4 font-semibold text-on-surface sticky left-[216px] bg-white group-hover:bg-slate-50/80 z-10 shadow-[2px_0_4px_rgba(0,0,0,0.04)]">
                      Dương Tuấn Khang
                    </td>
<td class="py-3 px-4 text-secondary font-medium">12DHTH02</td>
<td class="py-3 px-4 text-on-surface">Công nghệ Thông tin</td>
<td class="py-3 px-4 text-outline">Khóa 2021 (K21)</td>
<td class="py-3 px-4 text-outline font-mono text-[11px]">12/10/2026 10:15</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-label-sm text-label-sm bg-[#DCFCE7] text-[#15803D]">
<span class="w-1.5 h-1.5 rounded-full bg-[#15803D]"></span>
                        Thành công
                      </span>
</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1.5">
<button class="p-1.5 text-outline hover:text-error hover:bg-error-container/20 rounded transition-colors" onclick="openDeregisterModal('Dương Tuấn Khang', '2001210889')" title="Hủy đăng ký học phần">
<span class="material-symbols-outlined text-[18px]">person_remove</span>
</button>
<button class="p-1.5 text-outline hover:text-primary hover:bg-surface-container-low rounded transition-colors" title="Xem hồ sơ sinh viên">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<!-- Table Pagination Toolbar -->
<div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-body-sm text-outline">
<div>
                Hiển thị <span class="font-semibold text-on-surface">1 - 10</span> trong tổng số <span class="font-semibold text-on-surface">48</span> sinh viên
              </div>
<div class="flex items-center space-x-1.5">
<button class="px-2.5 py-1.5 border border-slate-200 rounded-md bg-white hover:bg-slate-100 disabled:opacity-40 font-label-sm text-label-sm" disabled="">
<span class="material-symbols-outlined text-[16px] align-middle">chevron_left</span>
</button>
<button class="px-3 py-1 bg-primary text-white rounded-md font-label-md text-label-md font-semibold">1</button>
<button class="px-3 py-1 border border-slate-200 rounded-md bg-white hover:bg-slate-100 text-on-surface font-label-md text-label-md">2</button>
<button class="px-3 py-1 border border-slate-200 rounded-md bg-white hover:bg-slate-100 text-on-surface font-label-md text-label-md">3</button>
<button class="px-3 py-1 border border-slate-200 rounded-md bg-white hover:bg-slate-100 text-on-surface font-label-md text-label-md">4</button>
<button class="px-3 py-1 border border-slate-200 rounded-md bg-white hover:bg-slate-100 text-on-surface font-label-md text-label-md">5</button>
<button class="px-2.5 py-1.5 border border-slate-200 rounded-md bg-white hover:bg-slate-100 font-label-sm text-label-sm">
<span class="material-symbols-outlined text-[16px] align-middle">chevron_right</span>
</button>
</div>
</div>
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
