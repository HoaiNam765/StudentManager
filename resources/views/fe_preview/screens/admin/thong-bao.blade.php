@extends('fe_preview.layouts.admin')
@section('page_title', 'Quản lý Thông báo học vụ - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Quản lý Thông báo học vụ - HUIT Học Vụ</title>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/thong-bao']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">campaign</span>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Quản lý Thông báo học vụ</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Quản lý Thông báo học vụ</div>
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
<!-- Topbar / Administrative Header -->
<header class="h-16 bg-surface-container-lowest border-b border-outline-variant px-8 flex items-center justify-between sticky top-0 z-30 shadow-xs">
<!-- Breadcrumb & Term Info -->
<div class="flex items-center gap-4">
<div class="flex items-center gap-2 font-body-md text-body-md text-secondary">
<span class="hover:text-primary transition-colors cursor-pointer">Quản trị hệ thống</span>
<span class="material-symbols-outlined text-sm text-slate-500" data-icon="chevron_right">chevron_right</span>
<span class="font-semibold text-on-surface">Thông báo học vụ</span>
</div>
<div class="h-4 w-[1px] bg-outline-variant"></div>
<!-- Term Badge Dropdown -->
<div class="inline-flex items-center gap-2 px-3 py-1 bg-surface-container-low border border-slate-200 rounded-full text-label-md font-label-md text-primary font-semibold">
<span class="material-symbols-outlined text-[16px]" data-icon="school">school</span>
<span>Học kỳ 1 (Năm học 2026 - 2027)</span>
<span class="material-symbols-outlined text-[14px] text-slate-500" data-icon="arrow_drop_down">arrow_drop_down</span>
</div>
</div>
<!-- Quick Search, Quick Actions & Profile -->
<div class="flex items-center gap-4">
<!-- Quick Search Input -->
<div class="relative w-64">
<span class="material-symbols-outlined absolute left-3 top-2.5 text-[18px] text-slate-500" data-icon="search">search</span>
<input aria-label="Tìm kiếm nhanh biểu mẫu, mã SV..." class="w-full pl-9 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-body-sm font-body-sm focus:bg-white focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" placeholder="Tìm kiếm nhanh biểu mẫu, mã SV..." type="text"/>
</div>
<!-- Notification Bell -->
<button class="relative p-2 text-slate-500 hover:text-on-surface hover:bg-slate-100 rounded-lg transition-colors">
<span class="material-symbols-outlined text-[20px]" data-icon="notifications">notifications</span>
<span class="absolute top-1.5 right-1.5 w-4 h-4 bg-error text-white font-bold text-[10px] rounded-full flex items-center justify-center leading-none">3</span>
</button>
<!-- Help Icon -->
<button class="p-2 text-slate-500 hover:text-on-surface hover:bg-slate-100 rounded-lg transition-colors" title="Trợ giúp nghiệp vụ">
<span class="material-symbols-outlined text-[20px]" data-icon="help_outline">help_outline</span>
</button>
<div class="h-6 w-[1px] bg-slate-200"></div>
<!-- Admin Profile Anchor -->
<div class="flex items-center gap-2.5 pl-1">
<div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-semibold text-xs border border-primary-fixed shadow-xs">
            HUIT
          </div>
<div class="flex flex-col text-left">
<span class="font-label-md text-label-md text-on-surface leading-tight font-semibold">Admin Quản lý Đào tạo</span>
<span class="text-[11px] text-slate-500 font-normal">Ban Đào tạo Chính quy</span>
</div>
</div>
</div>
</header>
<!-- Page Body Container -->
<div class="p-8 max-w-[1440px] w-full mx-auto flex flex-col gap-6">
<!-- Header Title & Secondary Tabs -->
<div class="flex flex-col gap-4 border-b border-outline-variant pb-1">
<div class="flex items-center justify-between">
<div>
<h1 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">Quản lý Thông báo học vụ</h1>
<p class="font-body-md text-body-md text-secondary mt-0.5">Soạn thảo, phê duyệt và điều phối truyền thông học vụ đến sinh viên và giảng viên toàn trường</p>
</div>
<!-- Quick Status Snapshot -->
<div class="flex items-center gap-3">
<div class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg shadow-xs flex items-center gap-2 font-label-md text-label-md text-slate-700">
<span class="w-2 h-2 rounded-full bg-emerald-500"></span>
<span>Cổng Mailer HUIT: <strong>Sẵn sàng</strong></span>
</div>
</div>
</div>
<!-- 3 Primary Navigation Tabs -->
<div class="flex items-center gap-8 -mb-[1px]">
<!-- Tab 1: Soạn thông báo mới (ACTIVE) -->
<button class="pb-3 border-b-2 border-primary text-primary font-semibold font-label-lg text-label-lg flex items-center gap-2">
<span class="material-symbols-outlined text-[18px]" data-icon="edit_note">edit_note</span>
<span>Soạn thông báo mới</span>
</button>
<!-- Tab 2: Mẫu thông báo (Templates) -->
<button class="pb-3 border-b-2 border-transparent text-secondary hover:text-on-surface font-medium font-label-lg text-label-lg flex items-center gap-2 transition-colors">
<span class="material-symbols-outlined text-[18px]" data-icon="description">description</span>
<span>Mẫu thông báo (Templates)</span>
<span class="px-2 py-0.5 text-[11px] font-semibold bg-slate-100 text-slate-600 rounded-full border border-slate-200">6 mẫu</span>
</button>
<!-- Tab 3: Lịch sử đã phát hành -->
<button class="pb-3 border-b-2 border-transparent text-secondary hover:text-on-surface font-medium font-label-lg text-label-lg flex items-center gap-2 transition-colors">
<span class="material-symbols-outlined text-[18px]" data-icon="history">history</span>
<span>Lịch sử đã phát hành</span>
<span class="px-2 py-0.5 text-[11px] font-semibold bg-blue-50 text-primary rounded-full border border-blue-100">42 thông báo</span>
</button>
</div>
</div>
<!-- MAIN TAB CONTENT: 2 COLUMNS (60% Form + 40% Live Preview & Confirmation Dialog) -->
<div class="grid grid-cols-12 gap-6 items-start">
<!-- ==================== LEFT COLUMN (60% ~ 7 cols) ==================== -->
<section class="col-span-7 flex flex-col gap-6">
<!-- Card Soạn thảo chính -->
<div class="bg-surface-container-lowest rounded-xl border border-slate-200 p-6 shadow-sm flex flex-col gap-6">
<!-- Phần 1: Đối tượng nhận thông báo (Targeting) -->
<div class="flex flex-col gap-3">
<div class="flex items-center justify-between">
<label class="font-headline-sm text-headline-sm text-on-surface font-semibold flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[20px]" data-icon="target">target</span>
<span>1. Đối tượng nhận thông báo (Targeting)</span>
</label>
<span class="font-label-sm text-label-sm text-slate-500 font-medium">Bắt buộc</span>
</div>
<!-- Chọn vai trò Checkboxes -->
<div class="flex items-center gap-6 pt-1">
<label class="inline-flex items-center gap-2 cursor-pointer text-body-md font-medium text-on-surface">
<input aria-label="Chọn dòng" checked="" class="w-4 h-4 rounded text-primary focus:ring-primary border-slate-300" type="checkbox"/>
<span class="text-primary font-semibold">Sinh viên</span>
</label>
<label class="inline-flex items-center gap-2 cursor-pointer text-body-md font-medium text-secondary hover:text-on-surface">
<input aria-label="Chọn dòng" class="w-4 h-4 rounded text-primary focus:ring-primary border-slate-300" type="checkbox"/>
<span>Giảng viên</span>
</label>
<label class="inline-flex items-center gap-2 cursor-pointer text-body-md font-medium text-secondary hover:text-on-surface">
<input aria-label="Chọn dòng" class="w-4 h-4 rounded text-primary border-slate-300" type="checkbox"/>
<span>Cố vấn học tập</span>
</label>
</div>
<!-- Bộ lọc chi tiết: Dropdowns 3 cột -->
<div class="grid grid-cols-3 gap-3 pt-1">
<div>
<span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Khoa / Viện</span>
<select aria-label="Khoa Công nghệ thông tin" class="w-full bg-white border border-slate-300 rounded-lg px-2.5 py-2 text-body-sm text-on-surface focus:border-primary focus:ring-1 focus:ring-primary font-medium">
<option selected="">Khoa Công nghệ thông tin</option>
<option>Khoa Thực phẩm</option>
<option>Khoa Quản trị kinh doanh</option>
<option>Khoa Điện - Điện tử</option>
</select>
</div>
<div>
<span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Khóa tuyển sinh</span>
<select aria-label="Khóa 21 (2021-2025)" class="w-full bg-white border border-slate-300 rounded-lg px-2.5 py-2 text-body-sm text-on-surface focus:border-primary focus:ring-1 focus:ring-primary font-medium">
<option selected="">Khóa 21 (2021-2025)</option>
<option>Khóa 22 (2022-2026)</option>
<option>Khóa 23 (2023-2027)</option>
<option>Khóa 24 (2024-2028)</option>
</select>
</div>
<div>
<span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Lớp sinh viên</span>
<select aria-label="Tất cả lớp thuộc K21" class="w-full bg-white border border-slate-300 rounded-lg px-2.5 py-2 text-body-sm text-on-surface focus:border-primary focus:ring-1 focus:ring-primary font-medium">
<option selected="">Tất cả lớp thuộc K21</option>
<option>12DHTH01</option>
<option>12DHTH02</option>
<option>12DHTH03</option>
</select>
</div>
</div>
<!-- Thẻ đếm đối tượng tự động -->
<div class="bg-blue-50 border border-blue-200 rounded-lg p-3 flex items-center justify-between text-body-sm">
<div class="flex items-center gap-2 text-primary font-medium">
<span class="text-base">🎯</span>
<span>Tổng số người nhận dự kiến: <strong class="text-on-primary-fixed underline decoration-primary font-bold">3.420 sinh viên</strong> (Khoa CNTT - Khóa 21)</span>
</div>
<span class="px-2 py-0.5 bg-blue-100 text-primary font-label-sm text-label-sm rounded font-semibold">Đồng bộ tức thời</span>
</div>
</div>
<div class="border-t border-slate-100"></div>
<!-- Phần 2: Mức độ thông báo -->
<div class="flex flex-col gap-2">
<label class="font-headline-sm text-headline-sm text-on-surface font-semibold flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[20px]" data-icon="priority_high">priority_high</span>
<span>2. Mức độ ưu tiên thông báo</span>
</label>
<div class="grid grid-cols-3 gap-3 pt-1">
<!-- Thường -->
<label class="border border-slate-200 rounded-lg p-3 flex items-center gap-3 cursor-pointer hover:bg-slate-50 transition-colors">
<input aria-label="Chọn dòng" class="text-primary border-slate-300" name="priority" type="radio"/>
<div class="flex flex-col">
<span class="font-label-lg text-label-lg text-slate-700">Thông thường</span>
<span class="text-[11px] text-slate-500">Hiển thị bảng tin chuẩn</span>
</div>
</label>
<!-- Quan trọng -->
<label class="border border-slate-200 rounded-lg p-3 flex items-center gap-3 cursor-pointer hover:bg-slate-50 transition-colors">
<input aria-label="Chọn dòng" class="text-primary border-slate-300" name="priority" type="radio"/>
<div class="flex flex-col">
<span class="font-label-lg text-label-lg text-amber-700 font-semibold">Quan trọng</span>
<span class="text-[11px] text-slate-500">Ghim lên đầu bảng tin</span>
</div>
</label>
<!-- Khẩn cấp (Active) -->
<label class="border-2 border-red-500 bg-red-50/50 rounded-lg p-3 flex items-center gap-3 cursor-pointer transition-colors shadow-xs">
<input aria-label="Chọn dòng" checked="" class="text-red-700 border-red-400 focus:ring-red-500" name="priority" type="radio"/>
<div class="flex flex-col">
<div class="flex items-center gap-1.5">
<span class="inline-flex w-2 h-2 rounded-full bg-red-600 animate-ping"></span>
<span class="font-label-lg text-label-lg text-red-700 font-bold">🔴 Khẩn cấp</span>
</div>
<span class="text-[11px] text-red-700 font-medium">Kèm Email &amp; SMS tức thì</span>
</div>
</label>
</div>
</div>
<div class="border-t border-slate-100"></div>
<!-- Phần 3: Tiêu đề thông báo -->
<div class="flex flex-col gap-2">
<div class="flex items-center justify-between">
<label class="font-headline-sm text-headline-sm text-on-surface font-semibold flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[20px]" data-icon="title">title</span>
<span>3. Tiêu đề thông báo học vụ</span>
</label>
<span class="text-[11px] text-slate-500">Tối đa 150 ký tự</span>
</div>
<input aria-label="Ô nhập liệu" class="w-full bg-white border border-slate-300 rounded-lg px-4 py-2.5 text-body-md font-semibold text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all shadow-xs" type="text" value="Thông báo về việc gia hạn thời gian Đăng ký học phần Học kỳ 1 (2026 - 2027)"/>
</div>
<!-- Phần 4: Nội dung thông báo (WYSIWYG Rich-text) -->
<div class="flex flex-col gap-2">
<label class="font-headline-sm text-headline-sm text-on-surface font-semibold flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[20px]" data-icon="format_shapes">format_shapes</span>
<span>4. Nội dung thông báo chi tiết</span>
</label>
<!-- Editor Box -->
<div class="border border-slate-300 rounded-lg overflow-hidden focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20">
<!-- Toolbar -->
<div class="bg-slate-50 border-b border-slate-200 p-2 flex items-center flex-wrap gap-1 text-slate-600 text-sm">
<button class="p-1.5 hover:bg-white rounded hover:text-primary font-bold" title="Đậm (Ctrl+B)" type="button">
<span class="material-symbols-outlined text-[18px]" data-icon="format_bold">format_bold</span>
</button>
<button class="p-1.5 hover:bg-white rounded hover:text-primary italic" title="Nghiêng (Ctrl+I)" type="button">
<span class="material-symbols-outlined text-[18px]" data-icon="format_italic">format_italic</span>
</button>
<button class="p-1.5 hover:bg-white rounded hover:text-primary underline" title="Gạch chân" type="button">
<span class="material-symbols-outlined text-[18px]" data-icon="format_underlined">format_underlined</span>
</button>
<div class="h-4 w-[1px] bg-slate-300 mx-1"></div>
<button class="p-1.5 hover:bg-white rounded hover:text-primary" title="Căn trái" type="button">
<span class="material-symbols-outlined text-[18px]" data-icon="format_align_left">format_align_left</span>
</button>
<button class="p-1.5 hover:bg-white rounded hover:text-primary" title="Căn giữa" type="button">
<span class="material-symbols-outlined text-[18px]" data-icon="format_align_center">format_align_center</span>
</button>
<button class="p-1.5 hover:bg-white rounded hover:text-primary" title="Căn đều" type="button">
<span class="material-symbols-outlined text-[18px]" data-icon="format_align_justify">format_align_justify</span>
</button>
<div class="h-4 w-[1px] bg-slate-300 mx-1"></div>
<button class="p-1.5 hover:bg-white rounded hover:text-primary" title="Danh sách bullet" type="button">
<span class="material-symbols-outlined text-[18px]" data-icon="format_list_bulleted">format_list_bulleted</span>
</button>
<button class="p-1.5 hover:bg-white rounded hover:text-primary" title="Đánh số" type="button">
<span class="material-symbols-outlined text-[18px]" data-icon="format_list_numbered">format_list_numbered</span>
</button>
<button class="p-1.5 hover:bg-white rounded hover:text-primary" title="Chèn liên kết" type="button">
<span class="material-symbols-outlined text-[18px]" data-icon="link">link</span>
</button>
<button class="p-1.5 hover:bg-white rounded hover:text-primary" title="Khối mã (Code block)" type="button">
<span class="material-symbols-outlined text-[18px]" data-icon="code">code</span>
</button>
</div>
<!-- Textarea Body -->
<textarea class="w-full p-4 text-body-md text-on-surface bg-white resize-none border-0 focus:ring-0 leading-relaxed" rows="7">Phòng Quản lý Đào tạo thông báo đến toàn thể sinh viên Khóa 21 thuộc Khoa Công nghệ thông tin về việc gia hạn thời gian Đăng ký học phần Học kỳ 1 năm học 2026 - 2027.

Nhằm tạo điều kiện tối đa cho các sinh viên bổ sung học phần tự chọn, cải thiện điểm và giải quyết trùng lịch thực hành:
• Hạn chót đóng cổng đăng ký điều chỉnh được dời đến đúng 23:59 ngày 25/10/2026.
• Đề nghị các bạn sinh viên kiểm tra kỹ thời khóa biểu và chốt số tín chỉ hợp lệ theo quy chế học vụ HUIT (tối thiểu 14 tín chỉ, tối đa 24 tín chỉ).
• Mọi vướng mắc liên quan đến lớp học phần bị khóa vui lòng liên hệ trực tiếp Văn phòng Khoa CNTT (Tòa nhà B, P.304).</textarea>
</div>
</div>
<!-- Phần 5: Tệp đính kèm học vụ -->
<div class="flex flex-col gap-2">
<label class="font-headline-sm text-headline-sm text-on-surface font-semibold flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[20px]" data-icon="attach_file">attach_file</span>
<span>5. Tệp đính kèm học vụ (PDF/Văn bản)</span>
</label>
<!-- Upload Drag-Drop Area -->
<div class="border-2 border-dashed border-slate-300 rounded-lg p-3 text-center bg-slate-50 hover:bg-blue-50/40 hover:border-primary/50 transition-colors flex items-center justify-center gap-2 cursor-pointer">
<span class="material-symbols-outlined text-slate-500 text-[20px]" data-icon="cloud_upload">cloud_upload</span>
<span class="text-body-sm text-slate-600 font-medium">Kéo thả tệp công văn ký số hoặc <strong class="text-primary underline">chọn từ máy tính</strong></span>
<span class="text-[11px] text-slate-500">(Tối đa 25MB)</span>
</div>
<!-- File Badge Đã tải -->
<div class="bg-surface-container-low border border-slate-200 rounded-lg p-2.5 flex items-center justify-between">
<div class="flex items-center gap-2.5">
<span class="text-xl">📄</span>
<div class="flex flex-col">
<span class="font-label-md text-label-md font-semibold text-on-surface">Thong_bao_418_gia_han_DKHP.pdf</span>
<span class="text-[11px] text-emerald-700 font-medium flex items-center gap-1">
<span class="material-symbols-outlined text-[13px]" data-icon="check_circle">check_circle</span>
                      1.4 MB • Đã quét an toàn chữ ký số HUIT
                    </span>
</div>
</div>
<div class="flex items-center gap-1">
<button class="p-1 text-slate-500 hover:text-primary rounded" title="Xem trước tệp">
<span class="material-symbols-outlined text-[18px]" data-icon="visibility">visibility</span>
</button>
<button class="p-1 text-slate-500 hover:text-error rounded" title="Xóa tệp">
<span class="material-symbols-outlined text-[18px]" data-icon="delete">delete</span>
</button>
</div>
</div>
</div>
<!-- Phần 6: Kênh phát hành -->
<div class="flex flex-col gap-2">
<label class="font-headline-sm text-headline-sm text-on-surface font-semibold flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[20px]" data-icon="cell_tower">cell_tower</span>
<span>6. Kênh phát hành đồng bộ</span>
</label>
<div class="flex items-center gap-6 pt-1">
<label class="inline-flex items-center gap-2 cursor-pointer text-body-md text-on-surface">
<input aria-label="Chọn dòng" checked="" class="w-4 h-4 rounded text-primary border-slate-300" type="checkbox"/>
<span class="font-medium">Cổng sinh viên</span>
</label>
<label class="inline-flex items-center gap-2 cursor-pointer text-body-md text-on-surface">
<input aria-label="Chọn dòng" checked="" class="w-4 h-4 rounded text-primary border-slate-300" type="checkbox"/>
<span class="font-medium">Ứng dụng di động HUIT App</span>
</label>
<label class="inline-flex items-center gap-2 cursor-pointer text-body-md text-on-surface">
<input aria-label="Chọn dòng" checked="" class="w-4 h-4 rounded text-primary border-slate-300" type="checkbox"/>
<span class="font-medium">Gửi email thông báo (@example.test)</span>
</label>
</div>
</div>
<!-- Phần 7: Tùy chọn thời gian gửi -->
<div class="flex flex-col gap-2">
<label class="font-headline-sm text-headline-sm text-on-surface font-semibold flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[20px]" data-icon="schedule">schedule</span>
<span>7. Kế hoạch thời gian phát hành</span>
</label>
<div class="flex items-center gap-6 pt-1">
<label class="inline-flex items-center gap-2 cursor-pointer text-body-md text-on-surface">
<input aria-label="Chọn dòng" checked="" class="text-primary border-slate-300" name="timing" type="radio"/>
<span class="font-semibold text-primary">Phát hành ngay bây giờ</span>
</label>
<label class="inline-flex items-center gap-2 cursor-pointer text-body-md text-secondary hover:text-on-surface">
<input aria-label="Chọn dòng" class="text-primary border-slate-300" name="timing" type="radio"/>
<span>Hẹn giờ phát hành tự động</span>
</label>
</div>
</div>
<!-- Cụm nút hành động dưới cùng -->
<div class="pt-4 border-t border-slate-200 flex items-center justify-between">
<button class="px-4 py-2 text-slate-500 hover:text-on-surface hover:bg-slate-100 font-label-lg text-label-lg rounded-lg transition-colors" type="button">
                Hủy bỏ
              </button>
<div class="flex items-center gap-3">
<button class="px-5 py-2 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-label-lg text-label-lg rounded-lg transition-colors shadow-xs" type="button">
                  Lưu bản nháp
                </button>
<button class="px-6 py-2.5 bg-primary hover:bg-[#1645a0] text-white font-label-lg text-label-lg font-semibold rounded-lg shadow-sm flex items-center gap-2 transition-all" type="button">
<span class="material-symbols-outlined text-[18px]" data-icon="send">send</span>
<span>Phát hành thông báo</span>
</button>
</div>
</div>
</div>
</section>
<!-- ==================== RIGHT COLUMN (40% ~ 5 cols) ==================== -->
<section class="col-span-5 flex flex-col gap-6 sticky top-20">
<!-- KHUNG 1: Xem trước thông báo (Giao diện phía Sinh viên) -->
<div class="bg-surface-container-lowest rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
<!-- Simulated Mac-style Browser Header -->
<div class="bg-slate-100 px-4 py-2.5 border-b border-slate-200 flex items-center justify-between">
<div class="flex items-center gap-1.5">
<span class="w-2.5 h-2.5 rounded-full bg-red-400"></span>
<span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
<span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
</div>
<div class="text-[11px] font-label-sm text-label-sm text-slate-500 font-medium">Bản xem trước trực quan - Màn hình Cổng Sinh viên</div>
<div class="flex items-center text-slate-500">
<span class="material-symbols-outlined text-[14px]" data-icon="open_in_new">open_in_new</span>
</div>
</div>
<!-- Content Card Mockup inside Student Portal -->
<div class="p-5 flex flex-col gap-4 bg-slate-50/60">
<div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex flex-col gap-3">
<!-- Top Card Info: HUIT Brand & Tag & Time -->
<div class="flex items-start justify-between gap-2">
<div class="flex items-center gap-2">
<div class="w-7 h-7 rounded bg-primary text-white font-bold text-[10px] flex items-center justify-center">
                      HUIT
                    </div>
<div class="flex flex-col">
<span class="text-xs font-bold text-on-surface">Phòng Quản lý Đào tạo</span>
<span class="text-[10px] text-slate-500">Vừa xong • Cổng Học vụ</span>
</div>
</div>
<span class="px-2 py-0.5 bg-red-100 text-red-700 border border-red-200 text-[10px] font-bold rounded-full uppercase tracking-wider flex items-center gap-1">
<span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                    Khẩn cấp
                  </span>
</div>
<!-- Notice Title -->
<h3 class="font-headline-sm text-headline-sm text-primary font-bold leading-snug">
                  Thông báo về việc gia hạn thời gian Đăng ký học phần Học kỳ 1 (2026 - 2027)
                </h3>
<!-- Sender info -->
<div class="text-[11px] text-slate-500 flex items-center gap-1 pb-1 border-b border-slate-100">
<span class="material-symbols-outlined text-[14px] text-slate-500" data-icon="person">person</span>
<span>Người ký duyệt: <strong>ThS. Người dùng mẫu 15</strong> - Trưởng phòng Đào tạo</span>
</div>
<!-- Body snippet preview -->
<div class="text-body-sm text-slate-700 leading-relaxed space-y-2">
<p>Phòng Quản lý Đào tạo thông báo đến toàn thể sinh viên Khóa 21 thuộc Khoa Công nghệ thông tin về việc gia hạn thời gian Đăng ký học phần Học kỳ 1 năm học 2026 - 2027...</p>
<div class="bg-amber-50/80 border-l-2 border-amber-500 p-2 text-[12px] text-amber-900 font-medium">
                    ⏰ Hạn chót đóng cổng dời đến: <strong>23:59 ngày 25/10/2026</strong>. Vui lòng kiểm tra thời khóa biểu và chốt tín chỉ hợp lệ (14 - 24 TC).
                  </div>
</div>
<!-- Attachment button in preview -->
<div class="mt-1 pt-2 border-t border-slate-100 flex items-center justify-between">
<div class="flex items-center gap-1.5 text-[11px] text-slate-600">
<span class="material-symbols-outlined text-[16px] text-red-500" data-icon="picture_as_pdf">picture_as_pdf</span>
<span class="font-medium truncate max-w-[160px]">Thong_bao_418_gia_han...pdf</span>
</div>
<button class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-800 text-[11px] font-semibold rounded flex items-center gap-1 transition-colors">
<span class="material-symbols-outlined text-[14px]" data-icon="download">download</span>
<span>Tải về PDF (1.4 MB)</span>
</button>
</div>
</div>
</div>
</div>
<!-- KHUNG 2: HỘP THOẠI CONFIRMATION DIALOG (Cảnh báo hệ quả học vụ) -->
<div class="bg-surface-container-lowest rounded-xl border-2 border-amber-400 shadow-lg overflow-hidden flex flex-col relative">
<div class="bg-amber-500/10 border-b border-amber-200 px-4 py-3 flex items-center gap-2.5">
<div class="w-7 h-7 rounded-full bg-amber-500 text-white flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[18px]" data-icon="warning" style="font-variation-settings: 'FILL' 1;">warning</span>
</div>
<div>
<h4 class="font-headline-sm text-headline-sm text-amber-950 font-bold leading-tight">Xác nhận phát hành thông báo học vụ</h4>
<p class="text-[11px] text-amber-800">Thao tác này sẽ đồng bộ trên toàn mạng lưới học viên</p>
</div>
</div>
<div class="p-5 flex flex-col gap-4">
<!-- Cảnh báo hệ quả -->
<div class="bg-red-50 border border-red-200 rounded-lg p-3.5 text-body-sm text-red-900 leading-relaxed">
<div class="font-bold text-red-700 flex items-center gap-1.5 mb-1">
<span class="material-symbols-outlined text-[18px]" data-icon="error">error</span>
<span>CẢNH BÁO HỆ QUẢ HỌC VỤ QUAN TRỌNG:</span>
</div>
                Thông báo này sẽ được phát hành tới <strong class="underline decoration-red-400 font-bold">3.420 sinh viên</strong> thuộc Khoa CNTT - Khóa 21 và tự động gửi <strong>3.420 email học vụ</strong> đến hộp thư sinh viên. Sau khi phát hành, hệ thống sẽ <strong class="text-red-700 uppercase">không thể thu hồi hoặc chỉnh sửa</strong> nội dung đã gửi!
              </div>
<!-- Checkbox cam kết bắt buộc -->
<label class="flex items-start gap-2.5 cursor-pointer p-2.5 rounded-lg hover:bg-slate-50 border border-slate-200 transition-colors">
<input aria-label="Chọn dòng" checked="" class="mt-0.5 w-4 h-4 rounded text-red-700 focus:ring-red-500 border-slate-300" type="checkbox"/>
<span class="text-body-sm font-medium text-slate-800 leading-tight">
                  Tôi đã kiểm tra kỹ nội dung, tệp đính kèm và danh sách <strong>3.420 đối tượng nhận</strong> theo đúng quy chế học vụ HUIT.
                </span>
</label>
<!-- Cặp nút thao tác -->
<div class="grid grid-cols-2 gap-3 pt-1">
<button class="w-full py-2.5 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 font-label-lg text-label-lg font-semibold rounded-lg transition-colors" type="button">
                  Hủy bỏ
                </button>
<button class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white font-label-lg text-label-lg font-bold rounded-lg shadow transition-colors flex items-center justify-center gap-1.5" type="button">
<span class="material-symbols-outlined text-[18px]" data-icon="send">send</span>
<span>Xác nhận gửi (3.420 SV)</span>
</button>
</div>
</div>
</div>
</section>
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

@endsection
