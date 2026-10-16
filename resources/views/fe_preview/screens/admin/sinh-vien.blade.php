@extends('fe_preview.layouts.admin')
@section('page_title', 'Admin - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="light"@endsection
@section('body_attributes')class="bg-[#f8f9ff] text-slate-800 antialiased min-h-screen flex selection:bg-blue-100 selection:text-blue-900"@endsection
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/sinh-vien']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">person_search</span>
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
<div class="font-semibold text-slate-800 text-[11.5px]">Hỗ trợ kỹ thuật: 028 0000 0000</div>
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
<div class="text-[11px] text-slate-500">nguoidung015@example.test</div>
</div>
</div>
</div>
</header>
<!-- MAIN BODY CONTENT -->
<main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
<!-- Page Header & Title Section -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2 border-b border-outline-variant/20">
<div>
<div class="flex items-center gap-2.5">
<h1 class="font-headline-lg text-headline-lg font-bold text-on-surface tracking-tight">Quản lý Sinh viên</h1>
</div>
<p class="font-body-md text-body-md text-secondary mt-0.5">Quản lý hồ sơ học vụ, trạng thái sinh viên và theo dõi tiến độ đào tạo các Khoa viện HUIT.</p>
</div>
<!-- Top Actions Bar -->
<div class="flex items-center gap-2.5">
<button class="h-9 px-3.5 rounded-lg border border-outline-variant bg-white text-on-surface hover:bg-surface-container-low hover:border-primary/40 font-label-md text-label-md flex items-center gap-1.5 transition-colors shadow-2xs">
<span class="material-symbols-outlined text-[18px] text-secondary">upload_file</span>
<span>Nhập từ file (Excel/CSV)</span>
</button>
<button class="h-9 px-3.5 rounded-lg border border-outline-variant bg-white text-on-surface hover:bg-surface-container-low hover:border-primary/40 font-label-md text-label-md flex items-center gap-1.5 transition-colors shadow-2xs">
<span class="material-symbols-outlined text-[18px] text-secondary">download</span>
<span>Xuất Excel</span>
</button>
<button class="h-9 px-4 rounded-lg bg-primary hover:bg-[#1647a8] active:scale-[0.98] text-white font-label-md text-label-md flex items-center gap-1.5 transition-all shadow-sm">
<span class="material-symbols-outlined text-[18px]">add</span>
<span>+ Thêm sinh viên</span>
</button>
</div>
</div>
<!-- State Switcher Tabs (UI States Simulator) -->
<div class="flex items-center justify-between bg-surface-container-low/60 p-1.5 rounded-xl border border-outline-variant/40">
<div class="flex items-center gap-1">
<span class="text-xs font-semibold text-secondary uppercase tracking-wider px-2.5">Chế độ xem:</span>
<button class="px-3 py-1 rounded-lg bg-white text-primary font-semibold text-label-md shadow-xs border border-primary/20 flex items-center gap-1.5">
<span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
            Bình thường (16,820)
          </button>
<button class="px-3 py-1 rounded-lg text-secondary hover:text-on-surface hover:bg-white/60 font-medium text-label-md transition-colors flex items-center gap-1.5">
<span class="material-symbols-outlined text-[14px]">hourglass_empty</span>
            Đang tải (Skeleton)
          </button>
<button class="px-3 py-1 rounded-lg text-secondary hover:text-on-surface hover:bg-white/60 font-medium text-label-md transition-colors flex items-center gap-1.5">
<span class="material-symbols-outlined text-[14px]">folder_off</span>
            Danh sách rỗng
          </button>
<button class="px-3 py-1 rounded-lg text-secondary hover:text-on-surface hover:bg-white/60 font-medium text-label-md transition-colors flex items-center gap-1.5">
<span class="material-symbols-outlined text-[14px]">wifi_off</span>
            Lỗi kết nối
          </button>
</div>
<div class="text-xs text-secondary font-medium px-2 flex items-center gap-1">
<span class="material-symbols-outlined text-[14px] text-green-600">sync</span>
          Đồng bộ máy chủ CSDL Đào tạo: 2 phút trước
        </div>
</div>
<!-- 4 Thẻ thống kê tổng quan (Metrics Cards Bento Grid) -->
<section class="grid grid-cols-4 gap-4">
<!-- Card 1: Tổng số SV -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/50 shadow-xs flex flex-col justify-between hover:border-primary/30 transition-colors">
<div class="flex items-start justify-between">
<div>
<p class="font-label-sm text-label-sm uppercase tracking-wide text-secondary font-semibold">Tổng số sinh viên</p>
<h3 class="font-headline-xl text-headline-xl font-bold text-on-surface mt-1">18,450</h3>
</div>
<div class="w-10 h-10 rounded-lg bg-primary-fixed flex items-center justify-center text-primary">
<span class="material-symbols-outlined text-[22px]">groups</span>
</div>
</div>
<div class="mt-3 flex items-center gap-2 pt-3 border-t border-outline-variant/20">
<span class="font-label-sm text-label-sm text-green-700 bg-green-50 px-1.5 py-0.5 rounded font-semibold flex items-center">
<span class="material-symbols-outlined text-[14px]">trending_up</span> +3.2%
            </span>
<span class="font-body-sm text-body-sm text-secondary">So với khóa trước</span>
</div>
</div>
<!-- Card 2: Đang học -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/50 shadow-xs flex flex-col justify-between hover:border-primary/30 transition-colors">
<div class="flex items-start justify-between">
<div>
<p class="font-label-sm text-label-sm uppercase tracking-wide text-secondary font-semibold">Đang học (Active)</p>
<h3 class="font-headline-xl text-headline-xl font-bold text-green-700 mt-1">16,820</h3>
</div>
<div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center text-green-600">
<span class="material-symbols-outlined text-[22px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
</div>
</div>
<div class="mt-3 flex items-center gap-2 pt-3 border-t border-outline-variant/20">
<span class="font-label-sm text-label-sm text-on-surface-variant font-medium">Chiếm <strong class="text-green-700 font-bold">91.2%</strong> tổng số hồ sơ</span>
</div>
</div>
<!-- Card 3: Bảo lưu / Tạm ngừng -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/50 shadow-xs flex flex-col justify-between hover:border-primary/30 transition-colors">
<div class="flex items-start justify-between">
<div>
<p class="font-label-sm text-label-sm uppercase tracking-wide text-secondary font-semibold">Bảo lưu / Tạm ngừng</p>
<h3 class="font-headline-xl text-headline-xl font-bold text-amber-700 mt-1">412</h3>
</div>
<div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center text-amber-700">
<span class="material-symbols-outlined text-[22px]">pause_circle</span>
</div>
</div>
<div class="mt-3 flex items-center gap-2 pt-3 border-t border-outline-variant/20">
<span class="font-label-sm text-label-sm text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded font-semibold">Theo dõi học vụ</span>
<span class="font-body-sm text-body-sm text-secondary">Hạn 1 - 2 học kỳ</span>
</div>
</div>
<!-- Card 4: Đã tốt nghiệp / Khác -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/50 shadow-xs flex flex-col justify-between hover:border-primary/30 transition-colors">
<div class="flex items-start justify-between">
<div>
<p class="font-label-sm text-label-sm uppercase tracking-wide text-secondary font-semibold">Đã tốt nghiệp / Khác</p>
<h3 class="font-headline-xl text-headline-xl font-bold text-tertiary mt-1">1,218</h3>
</div>
<div class="w-10 h-10 rounded-lg bg-sky-50 flex items-center justify-center text-tertiary">
<span class="material-symbols-outlined text-[22px]">school</span>
</div>
</div>
<div class="mt-3 flex items-center gap-2 pt-3 border-t border-outline-variant/20">
<span class="font-label-sm text-label-sm text-tertiary bg-sky-50 px-1.5 py-0.5 rounded font-semibold">Đã cấp văn bằng</span>
<span class="font-body-sm text-body-sm text-secondary">Đợt tháng 08/2024</span>
</div>
</div>
</section>
<!-- BỘ LỌC & TÌM KIẾM ĐA NĂNG (Card) -->
<section class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/50 shadow-xs space-y-4">
<!-- Row 1: Search Input & Dropdown Selects -->
<div class="grid grid-cols-12 gap-3 items-end">
<!-- Ô tìm kiếm MSSV / Tên -->
<div class="col-span-3">
<label class="block font-label-sm text-label-sm font-semibold text-on-surface-variant mb-1">Tìm kiếm sinh viên</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">search</span>
<input aria-label="Nhập MSSV, họ tên, email..." class="w-full h-10 pl-9 pr-3 text-body-md font-body-md bg-white border border-outline-variant rounded-lg text-on-surface placeholder:text-outline focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" placeholder="Nhập MSSV, họ tên, email..." type="text" value="2001"/>
</div>
</div>
<!-- Dropdown: Khoa -->
<div class="col-span-2">
<label class="block font-label-sm text-label-sm font-semibold text-on-surface-variant mb-1">Khoa / Viện</label>
<select aria-label="Khoa Công nghệ Thông tin" class="w-full h-10 px-3 text-body-md font-body-md bg-white border border-outline-variant rounded-lg text-on-surface focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
<option selected="">Khoa Công nghệ Thông tin</option>
<option>Khoa Công nghệ Thực phẩm</option>
<option>Khoa Quản trị Kinh doanh</option>
<option>Khoa Kỹ thuật Điện - Điện tử</option>
<option>Khoa Ngoại ngữ</option>
</select>
</div>
<!-- Dropdown: Ngành -->
<div class="col-span-2">
<label class="block font-label-sm text-label-sm font-semibold text-on-surface-variant mb-1">Ngành đào tạo</label>
<select aria-label="Tất cả các ngành" class="w-full h-10 px-3 text-body-md font-body-md bg-white border border-outline-variant rounded-lg text-on-surface focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
<option>Tất cả các ngành</option>
<option selected="">Công nghệ thông tin</option>
<option>Kỹ thuật phần mềm</option>
<option>Khoa học dữ liệu</option>
<option>An toàn thông tin</option>
</select>
</div>
<!-- Dropdown: Khóa -->
<div class="col-span-2">
<label class="block font-label-sm text-label-sm font-semibold text-on-surface-variant mb-1">Khóa học (Niên khóa)</label>
<select aria-label="Tất cả khóa" class="w-full h-10 px-3 text-body-md font-body-md bg-white border border-outline-variant rounded-lg text-on-surface focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
<option>Tất cả khóa</option>
<option selected="">Khóa 2024 (K24)</option>
<option>Khóa 2023 (K23)</option>
<option>Khóa 2022 (K22)</option>
<option>Khóa 2021 (K21)</option>
</select>
</div>
<!-- Dropdown: Lớp hành chính -->
<div class="col-span-1">
<label class="block font-label-sm text-label-sm font-semibold text-on-surface-variant mb-1">Lớp</label>
<select aria-label="Tất cả" class="w-full h-10 px-2 text-body-md font-body-md bg-white border border-outline-variant rounded-lg text-on-surface focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
<option>Tất cả</option>
<option>12DHTT01</option>
<option>12DHTT02</option>
<option>11DHPM01</option>
</select>
</div>
<!-- Dropdown: Trạng thái -->
<div class="col-span-2">
<label class="block font-label-sm text-label-sm font-semibold text-on-surface-variant mb-1">Trạng thái SV</label>
<select aria-label="Tất cả trạng thái" class="w-full h-10 px-3 text-body-md font-body-md bg-white border border-outline-variant rounded-lg text-on-surface focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
<option>Tất cả trạng thái</option>
<option selected="">Đang học (Active)</option>
<option>Bảo lưu</option>
<option>Thôi học / Đình chỉ</option>
<option>Đã tốt nghiệp</option>
</select>
</div>
</div>
<!-- Row 2: Active Filter Tags & Reset -->
<div class="flex items-center justify-between pt-3 border-t border-outline-variant/30 text-body-sm">
<div class="flex items-center gap-2 flex-wrap">
<span class="font-label-sm text-label-sm text-secondary font-semibold flex items-center gap-1">
<span class="material-symbols-outlined text-[16px]">filter_alt</span> Đang lọc theo:
            </span>
<!-- Tag 1: Khoa CNTT -->
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-surface-container-low text-primary border border-primary/20">
              Khoa: <strong>Công nghệ Thông tin</strong>
<button class="hover:text-error transition-colors flex items-center">
<span class="material-symbols-outlined text-[14px]">close</span>
</button>
</span>
<!-- Tag 2: Khóa 2024 -->
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-surface-container-low text-primary border border-primary/20">
              Khóa: <strong>2024 (K24)</strong>
<button class="hover:text-error transition-colors flex items-center">
<span class="material-symbols-outlined text-[14px]">close</span>
</button>
</span>
<!-- Tag 3: Trạng thái Đang học -->
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
              Trạng thái: <strong>Đang học</strong>
<button class="hover:text-error transition-colors flex items-center">
<span class="material-symbols-outlined text-[14px]">close</span>
</button>
</span>
<!-- Clear All -->
<button class="text-primary hover:text-primary-container font-label-md text-label-md font-semibold underline underline-offset-2 ml-1 transition-colors">
              Xóa tất cả bộ lọc
            </button>
</div>
<div class="text-secondary text-body-sm">
            Tìm thấy <strong class="text-on-surface font-semibold">1,420</strong> sinh viên phù hợp
          </div>
</div>
</section>
<!-- DATA TABLE CONTAINER (Academic Student Table) -->
<section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs overflow-hidden flex flex-col">
<!-- Table Control Toolbar -->
<div class="px-5 py-3 border-b border-outline-variant/30 flex items-center justify-between bg-surface-container-lowest">
<div class="flex items-center gap-3">
<span class="font-headline-sm text-headline-sm text-on-surface font-semibold">Danh sách Sinh viên chính quy</span>
<span class="font-label-sm text-label-sm bg-primary/10 text-primary px-2.5 py-0.5 rounded-full font-bold">10 / 16,820 sinh viên</span>
</div>
<div class="flex items-center gap-2">
<button class="p-1.5 rounded-lg border border-outline-variant/60 text-secondary hover:text-on-surface hover:bg-surface-container-low transition-colors" title="Làm mới dữ liệu">
<span class="material-symbols-outlined text-[18px]">refresh</span>
</button>
<button class="p-1.5 rounded-lg border border-outline-variant/60 text-secondary hover:text-on-surface hover:bg-surface-container-low transition-colors" title="Tùy chỉnh cột hiển thị">
<span class="material-symbols-outlined text-[18px]">view_column</span>
</button>
<button class="p-1.5 rounded-lg border border-outline-variant/60 text-secondary hover:text-on-surface hover:bg-surface-container-low transition-colors" title="Mật độ dòng">
<span class="material-symbols-outlined text-[18px]">density_medium</span>
</button>
</div>
</div>
<!-- Scrollable Table -->
<div class="overflow-x-auto custom-scrollbar">
<table class="w-full text-left border-collapse min-w-[1100px]">
<thead>
<tr class="bg-[#F8FAFC] border-b border-outline-variant/30 text-secondary font-label-md text-label-md uppercase tracking-wider">
<!-- Col 1 & 2 & 3: Sticky Checkbox + MSSV + Họ tên -->
<th class="sticky-col-header bg-[#F8FAFC] py-3.5 px-4 w-12 border-r border-outline-variant/20" scope="col">
<div class="flex items-center justify-center">
<input aria-label="Chọn dòng" class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary/20" type="checkbox"/>
</div>
</th>
<th class="sticky-col-header bg-[#F8FAFC] py-3.5 px-4 font-semibold text-on-surface-variant w-32 border-r border-outline-variant/20 left-12" scope="col">
                  MSSV
                </th>
<th class="sticky-col-header bg-[#F8FAFC] py-3.5 px-4 font-semibold text-on-surface-variant w-64 border-r border-outline-variant/20 left-44 shadow-[4px_0_6px_-2px_rgba(0,0,0,0.04)]" scope="col">
                  Họ và tên sinh viên
                </th>
<!-- Other regular cols -->
<th class="py-3.5 px-4 font-semibold text-on-surface-variant" scope="col">Lớp hành chính</th>
<th class="py-3.5 px-4 font-semibold text-on-surface-variant" scope="col">Ngành đào tạo</th>
<th class="py-3.5 px-4 font-semibold text-on-surface-variant" scope="col">Khóa học</th>
<th class="py-3.5 px-4 font-semibold text-on-surface-variant text-right" scope="col">ĐTB Tích lũy / TC</th>
<th class="py-3.5 px-4 font-semibold text-on-surface-variant text-center" scope="col">Trạng thái</th>
<th class="py-3.5 px-4 font-semibold text-on-surface-variant text-center w-28" scope="col">Thao tác</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant/20 font-body-md text-body-md text-on-surface">
<!-- ROW 1 -->
<tr class="hover:bg-[#F8FAFC] transition-colors group">
<td class="sticky-col-cell bg-white group-hover:bg-[#F8FAFC] py-3 px-4 text-center border-r border-outline-variant/20">
<input aria-label="Chọn dòng" class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary/20" type="checkbox"/>
</td>
<td class="sticky-col-cell bg-white group-hover:bg-[#F8FAFC] py-3 px-4 font-mono font-bold text-primary border-r border-outline-variant/20 left-12">
                  2400000007
                </td>
<td class="sticky-col-cell bg-white group-hover:bg-[#F8FAFC] py-3 px-4 border-r border-outline-variant/20 left-44 shadow-[4px_0_6px_-2px_rgba(0,0,0,0.04)]">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-blue-100 text-primary font-bold flex items-center justify-center text-xs ring-1 ring-blue-200">
                      NA
                    </div>
<div>
<p class="font-semibold text-on-surface group-hover:text-primary transition-colors">Người dùng mẫu 25</p>
<p class="text-[11px] text-secondary">nguoidung006@example.test</p>
</div>
</div>
</td>
<td class="py-3 px-4 font-medium text-secondary">12DHTT01</td>
<td class="py-3 px-4 font-medium">Công nghệ Thông tin</td>
<td class="py-3 px-4 text-secondary">K21 (2021)</td>
<td class="py-3 px-4 text-right">
<span class="font-bold text-green-700">3.42</span>
<span class="text-secondary text-xs"> / 108 TC</span>
</td>
<td class="py-3 px-4 text-center">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#DCFCE7] text-[#15803D]">
                    Đang học
                  </span>
</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1 rounded text-secondary hover:text-primary hover:bg-surface-container-low transition-colors" title="Xem hồ sơ">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
<button class="p-1 rounded text-secondary hover:text-primary hover:bg-surface-container-low transition-colors" title="Chỉnh sửa">
<span class="material-symbols-outlined text-[18px]">edit</span>
</button>
<button class="p-1 rounded text-secondary hover:text-amber-700 hover:bg-amber-50 transition-colors" title="Đổi trạng thái">
<span class="material-symbols-outlined text-[18px]">swap_horiz</span>
</button>
</div>
</td>
</tr>
<!-- ROW 2 -->
<tr class="hover:bg-[#F8FAFC] transition-colors group">
<td class="sticky-col-cell bg-white group-hover:bg-[#F8FAFC] py-3 px-4 text-center border-r border-outline-variant/20">
<input aria-label="Chọn dòng" class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary/20" type="checkbox"/>
</td>
<td class="sticky-col-cell bg-white group-hover:bg-[#F8FAFC] py-3 px-4 font-mono font-bold text-primary border-r border-outline-variant/20 left-12">
                  2400000005
                </td>
<td class="sticky-col-cell bg-white group-hover:bg-[#F8FAFC] py-3 px-4 border-r border-outline-variant/20 left-44 shadow-[4px_0_6px_-2px_rgba(0,0,0,0.04)]">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-pink-100 text-pink-700 font-bold flex items-center justify-center text-xs ring-1 ring-pink-200">
                      TT
                    </div>
<div>
<p class="font-semibold text-on-surface group-hover:text-primary transition-colors">Người dùng mẫu 16</p>
<p class="text-[11px] text-secondary">nguoidung004@example.test</p>
</div>
</div>
</td>
<td class="py-3 px-4 font-medium text-secondary">12DHTT02</td>
<td class="py-3 px-4 font-medium">Kỹ thuật Phần mềm</td>
<td class="py-3 px-4 text-secondary">K21 (2021)</td>
<td class="py-3 px-4 text-right">
<span class="font-bold text-green-700">3.68</span>
<span class="text-secondary text-xs"> / 112 TC</span>
</td>
<td class="py-3 px-4 text-center">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#DCFCE7] text-[#15803D]">
                    Đang học
                  </span>
</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1 rounded text-secondary hover:text-primary hover:bg-surface-container-low transition-colors" title="Xem hồ sơ">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
<button class="p-1 rounded text-secondary hover:text-primary hover:bg-surface-container-low transition-colors" title="Chỉnh sửa">
<span class="material-symbols-outlined text-[18px]">edit</span>
</button>
<button class="p-1 rounded text-secondary hover:text-amber-700 hover:bg-amber-50 transition-colors" title="Đổi trạng thái">
<span class="material-symbols-outlined text-[18px]">swap_horiz</span>
</button>
</div>
</td>
</tr>
<!-- ROW 3 (Bảo lưu) -->
<tr class="hover:bg-[#F8FAFC] transition-colors group">
<td class="sticky-col-cell bg-white group-hover:bg-[#F8FAFC] py-3 px-4 text-center border-r border-outline-variant/20">
<input aria-label="Chọn dòng" class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary/20" type="checkbox"/>
</td>
<td class="sticky-col-cell bg-white group-hover:bg-[#F8FAFC] py-3 px-4 font-mono font-bold text-primary border-r border-outline-variant/20 left-12">
                  2400000025
                </td>
<td class="sticky-col-cell bg-white group-hover:bg-[#F8FAFC] py-3 px-4 border-r border-outline-variant/20 left-44 shadow-[4px_0_6px_-2px_rgba(0,0,0,0.04)]">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-amber-100 text-amber-700 font-bold flex items-center justify-center text-xs ring-1 ring-amber-200">
                      QB
                    </div>
<div>
<p class="font-semibold text-on-surface group-hover:text-primary transition-colors">Người dùng mẫu 40</p>
<p class="text-[11px] text-secondary">nguoidung012@example.test</p>
</div>
</div>
</td>
<td class="py-3 px-4 font-medium text-secondary">11DHPM01</td>
<td class="py-3 px-4 font-medium">Khoa học Dữ liệu</td>
<td class="py-3 px-4 text-secondary">K22 (2022)</td>
<td class="py-3 px-4 text-right">
<span class="font-bold text-amber-700">2.45</span>
<span class="text-secondary text-xs"> / 45 TC</span>
</td>
<td class="py-3 px-4 text-center">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#FEF3C7] text-[#B45309]">
                    Bảo lưu
                  </span>
</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1 rounded text-secondary hover:text-primary hover:bg-surface-container-low transition-colors" title="Xem hồ sơ">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
<button class="p-1 rounded text-secondary hover:text-primary hover:bg-surface-container-low transition-colors" title="Chỉnh sửa">
<span class="material-symbols-outlined text-[18px]">edit</span>
</button>
<button class="p-1 rounded text-secondary hover:text-amber-700 hover:bg-amber-50 transition-colors" title="Đổi trạng thái">
<span class="material-symbols-outlined text-[18px]">swap_horiz</span>
</button>
</div>
</td>
</tr>
<!-- ROW 4 -->
<tr class="hover:bg-[#F8FAFC] transition-colors group">
<td class="sticky-col-cell bg-white group-hover:bg-[#F8FAFC] py-3 px-4 text-center border-r border-outline-variant/20">
<input aria-label="Chọn dòng" class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary/20" type="checkbox"/>
</td>
<td class="sticky-col-cell bg-white group-hover:bg-[#F8FAFC] py-3 px-4 font-mono font-bold text-primary border-r border-outline-variant/20 left-12">
                  2400000029
                </td>
<td class="sticky-col-cell bg-white group-hover:bg-[#F8FAFC] py-3 px-4 border-r border-outline-variant/20 left-44 shadow-[4px_0_6px_-2px_rgba(0,0,0,0.04)]">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-purple-100 text-purple-700 font-bold flex items-center justify-center text-xs ring-1 ring-purple-200">
                      MH
                    </div>
<div>
<p class="font-semibold text-on-surface group-hover:text-primary transition-colors">Người dùng mẫu 30</p>
<p class="text-[11px] text-secondary">nguoidung014@example.test</p>
</div>
</div>
</td>
<td class="py-3 px-4 font-medium text-secondary">14DHTT03</td>
<td class="py-3 px-4 font-medium">Công nghệ Thông tin</td>
<td class="py-3 px-4 text-secondary">K24 (2024)</td>
<td class="py-3 px-4 text-right">
<span class="font-bold text-green-700">3.15</span>
<span class="text-secondary text-xs"> / 18 TC</span>
</td>
<td class="py-3 px-4 text-center">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#DCFCE7] text-[#15803D]">
                    Đang học
                  </span>
</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1 rounded text-secondary hover:text-primary hover:bg-surface-container-low transition-colors" title="Xem hồ sơ">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
<button class="p-1 rounded text-secondary hover:text-primary hover:bg-surface-container-low transition-colors" title="Chỉnh sửa">
<span class="material-symbols-outlined text-[18px]">edit</span>
</button>
<button class="p-1 rounded text-secondary hover:text-amber-700 hover:bg-amber-50 transition-colors" title="Đổi trạng thái">
<span class="material-symbols-outlined text-[18px]">swap_horiz</span>
</button>
</div>
</td>
</tr>
<!-- ROW 5 (Thôi học) -->
<tr class="hover:bg-[#F8FAFC] transition-colors group">
<td class="sticky-col-cell bg-white group-hover:bg-[#F8FAFC] py-3 px-4 text-center border-r border-outline-variant/20">
<input aria-label="Chọn dòng" class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary/20" type="checkbox"/>
</td>
<td class="sticky-col-cell bg-white group-hover:bg-[#F8FAFC] py-3 px-4 font-mono font-bold text-primary border-r border-outline-variant/20 left-12">
                  2400000002
                </td>
<td class="sticky-col-cell bg-white group-hover:bg-[#F8FAFC] py-3 px-4 border-r border-outline-variant/20 left-44 shadow-[4px_0_6px_-2px_rgba(0,0,0,0.04)]">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-red-100 text-error font-bold flex items-center justify-center text-xs ring-1 ring-red-200">
                      HM
                    </div>
<div>
<p class="font-semibold text-on-surface group-hover:text-primary transition-colors">Người dùng mẫu 06</p>
<p class="text-[11px] text-secondary">nguoidung002@example.test</p>
</div>
</div>
</td>
<td class="py-3 px-4 font-medium text-secondary">10DHPM02</td>
<td class="py-3 px-4 font-medium">Kỹ thuật Phần mềm</td>
<td class="py-3 px-4 text-secondary">K20 (2020)</td>
<td class="py-3 px-4 text-right">
<span class="font-bold text-error">1.82</span>
<span class="text-secondary text-xs"> / 32 TC</span>
</td>
<td class="py-3 px-4 text-center">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#FEE2E2] text-[#B91C1C]">
                    Thôi học
                  </span>
</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1 rounded text-secondary hover:text-primary hover:bg-surface-container-low transition-colors" title="Xem hồ sơ">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
<button class="p-1 rounded text-secondary hover:text-primary hover:bg-surface-container-low transition-colors" title="Chỉnh sửa">
<span class="material-symbols-outlined text-[18px]">edit</span>
</button>
<button class="p-1 rounded text-secondary hover:text-amber-700 hover:bg-amber-50 transition-colors" title="Đổi trạng thái">
<span class="material-symbols-outlined text-[18px]">swap_horiz</span>
</button>
</div>
</td>
</tr>
<!-- ROW 6 (Đã tốt nghiệp) -->
<tr class="hover:bg-[#F8FAFC] transition-colors group">
<td class="sticky-col-cell bg-white group-hover:bg-[#F8FAFC] py-3 px-4 text-center border-r border-outline-variant/20">
<input aria-label="Chọn dòng" class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary/20" type="checkbox"/>
</td>
<td class="sticky-col-cell bg-white group-hover:bg-[#F8FAFC] py-3 px-4 font-mono font-bold text-primary border-r border-outline-variant/20 left-12">
                  2400000001
                </td>
<td class="sticky-col-cell bg-white group-hover:bg-[#F8FAFC] py-3 px-4 border-r border-outline-variant/20 left-44 shadow-[4px_0_6px_-2px_rgba(0,0,0,0.04)]">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-sky-100 text-tertiary font-bold flex items-center justify-center text-xs ring-1 ring-sky-200">
                      DT
                    </div>
<div>
<p class="font-semibold text-on-surface group-hover:text-primary transition-colors">Người dùng mẫu 54</p>
<p class="text-[11px] text-secondary">nguoidung001@example.test</p>
</div>
</div>
</td>
<td class="py-3 px-4 font-medium text-secondary">09DHTT01</td>
<td class="py-3 px-4 font-medium">Công nghệ Thông tin</td>
<td class="py-3 px-4 text-secondary">K19 (2019)</td>
<td class="py-3 px-4 text-right">
<span class="font-bold text-tertiary">3.55</span>
<span class="text-secondary text-xs"> / 142 TC</span>
</td>
<td class="py-3 px-4 text-center">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#E0F2FE] text-[#0369A1]">
                    Đã tốt nghiệp
                  </span>
</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1 rounded text-secondary hover:text-primary hover:bg-surface-container-low transition-colors" title="Xem hồ sơ">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
<button class="p-1 rounded text-secondary hover:text-primary hover:bg-surface-container-low transition-colors" title="Chỉnh sửa">
<span class="material-symbols-outlined text-[18px]">edit</span>
</button>
<button class="p-1 rounded text-secondary hover:text-amber-700 hover:bg-amber-50 transition-colors" title="Đổi trạng thái">
<span class="material-symbols-outlined text-[18px]">swap_horiz</span>
</button>
</div>
</td>
</tr>
<!-- ROW 7 -->
<tr class="hover:bg-[#F8FAFC] transition-colors group">
<td class="sticky-col-cell bg-white group-hover:bg-[#F8FAFC] py-3 px-4 text-center border-r border-outline-variant/20">
<input aria-label="Chọn dòng" class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary/20" type="checkbox"/>
</td>
<td class="sticky-col-cell bg-white group-hover:bg-[#F8FAFC] py-3 px-4 font-mono font-bold text-primary border-r border-outline-variant/20 left-12">
                  2400000021
                </td>
<td class="sticky-col-cell bg-white group-hover:bg-[#F8FAFC] py-3 px-4 border-r border-outline-variant/20 left-44 shadow-[4px_0_6px_-2px_rgba(0,0,0,0.04)]">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-teal-100 text-teal-700 font-bold flex items-center justify-center text-xs ring-1 ring-teal-200">
                      VT
                    </div>
<div>
<p class="font-semibold text-on-surface group-hover:text-primary transition-colors">Người dùng mẫu 48</p>
<p class="text-[11px] text-secondary">nguoidung011@example.test</p>
</div>
</div>
</td>
<td class="py-3 px-4 font-medium text-secondary">12DHTT01</td>
<td class="py-3 px-4 font-medium">An toàn Thông tin</td>
<td class="py-3 px-4 text-secondary">K21 (2021)</td>
<td class="py-3 px-4 text-right">
<span class="font-bold text-green-700">3.28</span>
<span class="text-secondary text-xs"> / 102 TC</span>
</td>
<td class="py-3 px-4 text-center">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#DCFCE7] text-[#15803D]">
                    Đang học
                  </span>
</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1 rounded text-secondary hover:text-primary hover:bg-surface-container-low transition-colors" title="Xem hồ sơ">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
<button class="p-1 rounded text-secondary hover:text-primary hover:bg-surface-container-low transition-colors" title="Chỉnh sửa">
<span class="material-symbols-outlined text-[18px]">edit</span>
</button>
<button class="p-1 rounded text-secondary hover:text-amber-700 hover:bg-amber-50 transition-colors" title="Đổi trạng thái">
<span class="material-symbols-outlined text-[18px]">swap_horiz</span>
</button>
</div>
</td>
</tr>
<!-- ROW 8 -->
<tr class="hover:bg-[#F8FAFC] transition-colors group">
<td class="sticky-col-cell bg-white group-hover:bg-[#F8FAFC] py-3 px-4 text-center border-r border-outline-variant/20">
<input aria-label="Chọn dòng" class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary/20" type="checkbox"/>
</td>
<td class="sticky-col-cell bg-white group-hover:bg-[#F8FAFC] py-3 px-4 font-mono font-bold text-primary border-r border-outline-variant/20 left-12">
                  2400000028
                </td>
<td class="sticky-col-cell bg-white group-hover:bg-[#F8FAFC] py-3 px-4 border-r border-outline-variant/20 left-44 shadow-[4px_0_6px_-2px_rgba(0,0,0,0.04)]">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-xs ring-1 ring-indigo-200">
                      BH
                    </div>
<div>
<p class="font-semibold text-on-surface group-hover:text-primary transition-colors">Người dùng mẫu 01</p>
<p class="text-[11px] text-secondary">nguoidung013@example.test</p>
</div>
</div>
</td>
<td class="py-3 px-4 font-medium text-secondary">13DHTT02</td>
<td class="py-3 px-4 font-medium">Khoa học Dữ liệu</td>
<td class="py-3 px-4 text-secondary">K23 (2023)</td>
<td class="py-3 px-4 text-right">
<span class="font-bold text-green-700">3.72</span>
<span class="text-secondary text-xs"> / 58 TC</span>
</td>
<td class="py-3 px-4 text-center">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#DCFCE7] text-[#15803D]">
                    Đang học
                  </span>
</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1 rounded text-secondary hover:text-primary hover:bg-surface-container-low transition-colors" title="Xem hồ sơ">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
<button class="p-1 rounded text-secondary hover:text-primary hover:bg-surface-container-low transition-colors" title="Chỉnh sửa">
<span class="material-symbols-outlined text-[18px]">edit</span>
</button>
<button class="p-1 rounded text-secondary hover:text-amber-700 hover:bg-amber-50 transition-colors" title="Đổi trạng thái">
<span class="material-symbols-outlined text-[18px]">swap_horiz</span>
</button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<!-- Pagination Bar -->
<div class="px-5 py-3.5 border-t border-outline-variant/30 flex items-center justify-between bg-surface-container-lowest text-body-sm">
<!-- Left: Showing summary -->
<div class="flex items-center gap-4 text-secondary font-medium">
<span>Hiển thị <strong class="text-on-surface font-semibold">1 - 10</strong> trong tổng số <strong class="text-on-surface font-semibold">16,820</strong> sinh viên</span>
<div class="flex items-center gap-2">
<span class="text-outline">|</span>
<span class="text-secondary text-xs">Số dòng mỗi trang:</span>
<select aria-label="10" class="h-8 px-2 text-xs font-semibold bg-white border border-outline-variant rounded-md text-on-surface focus:outline-none focus:border-primary">
<option selected="">10</option>
<option>25</option>
<option>50</option>
<option>100</option>
</select>
</div>
</div>
<!-- Right: Page Navigation -->
<div class="flex items-center gap-1">
<button class="h-8 px-2.5 rounded-lg border border-outline-variant/60 text-secondary hover:text-on-surface hover:bg-surface-container-low font-medium flex items-center gap-1 transition-colors disabled:opacity-40" disabled="">
<span class="material-symbols-outlined text-[16px]">chevron_left</span>
<span>Trước</span>
</button>
<!-- Page 1 (Active) -->
<button class="w-8 h-8 rounded-lg bg-primary text-white font-semibold text-xs flex items-center justify-center shadow-xs">
              1
            </button>
<button class="w-8 h-8 rounded-lg border border-outline-variant/60 text-on-surface hover:bg-surface-container-low font-medium text-xs flex items-center justify-center transition-colors">
              2
            </button>
<button class="w-8 h-8 rounded-lg border border-outline-variant/60 text-on-surface hover:bg-surface-container-low font-medium text-xs flex items-center justify-center transition-colors">
              3
            </button>
<span class="w-6 text-center text-outline text-xs">...</span>
<button class="w-8 h-8 rounded-lg border border-outline-variant/60 text-on-surface hover:bg-surface-container-low font-medium text-xs flex items-center justify-center transition-colors">
              1,682
            </button>
<button class="h-8 px-2.5 rounded-lg border border-outline-variant/60 text-secondary hover:text-on-surface hover:bg-surface-container-low font-medium flex items-center gap-1 transition-colors">
<span>Sau</span>
<span class="material-symbols-outlined text-[16px]">chevron_right</span>
</button>
</div>
</div>
</section>
<!-- Bottom Quick Summary Info Card (Institutional Footer Note) -->
<footer class="pt-2 pb-6 flex items-center justify-between text-secondary font-body-sm text-[11px] border-t border-outline-variant/20">
<div>
          Trường Đại học Công Thương TP. Hồ Chí Minh (HUIT) • 140 Người dùng mẫu 18, P. Tây Thạnh, Q. Tân Phú, TP. HCM
        </div>
<div class="flex items-center gap-4">
<a class="hover:text-primary transition-colors" href="#">Quy chế đào tạo theo học chế tín chỉ</a>
<span>•</span>
<a class="hover:text-primary transition-colors" href="#">Chính sách bảo mật dữ liệu sinh viên</a>
<span>•</span>
<span class="text-outline">Mã phiên làm việc: #ADMIN-SESSION-8841</span>
</div>
</footer>
</main>
<!-- FOOTER -->
<footer class="py-4 px-6 bg-white border-t border-slate-200/80 text-center text-xs text-slate-500 no-print flex flex-col sm:flex-row items-center justify-between gap-2">
<div>© 2026 <strong>Trường Đại học Công Thương TP.HCM (HUIT)</strong> — Hệ thống Quản lý Học vụ.</div>
<div class="flex items-center gap-3 text-slate-500 text-[11px]">
<span>Hỗ trợ: 028 0000 0000</span>
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
