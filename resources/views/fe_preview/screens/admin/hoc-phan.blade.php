@extends('fe_preview.layouts.admin')
@section('page_title', 'Quản lý Học phần - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Quản lý Học phần - HUIT Học Vụ</title>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/hoc-phan']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">library_books</span>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Quản lý Học phần</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Quản lý Học phần</div>
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
<!-- PAGE TITLE & AUDIT CONTEXT -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
<div>
<h1 class="font-headline-xl text-headline-xl text-on-surface font-bold tracking-tight">Quản lý Học phần</h1>
<p class="font-body-md text-body-md text-on-surface-variant mt-0.5">Quản lý danh mục học phần, quy chuẩn tín chỉ lý thuyết/thực hành và cấu hình học phần tiên quyết theo CTĐT HUIT.</p>
</div>
<!-- State Switcher Preview Bar -->
<div class="flex items-center bg-surface-container-lowest border border-outline-variant p-1 rounded-xl shadow-xs self-start">
<span class="text-[11px] font-semibold uppercase tracking-wider text-outline px-2.5">Trạng thái giao diện:</span>
<button class="px-3 py-1 text-label-md font-semibold rounded-lg bg-primary text-white shadow-xs transition-all" id="btn-normal" onclick="switchView('normal')">Bình thường (428)</button>
<button class="px-3 py-1 text-label-md font-medium text-on-surface-variant hover:text-on-surface rounded-lg transition-all" id="btn-loading" onclick="switchView('loading')">Đang tải</button>
<button class="px-3 py-1 text-label-md font-medium text-on-surface-variant hover:text-on-surface rounded-lg transition-all" id="btn-empty" onclick="switchView('empty')">Rỗng</button>
<button class="px-3 py-1 text-label-md font-medium text-on-surface-variant hover:text-on-surface rounded-lg transition-all" id="btn-error" onclick="switchView('error')">Lỗi kết nối</button>
</div>
</div>
<!-- KPI STATS CARDS (4 cards) -->
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
<!-- Card 1 -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 shadow-xs flex items-center justify-between hover:border-primary transition-colors">
<div>
<p class="font-label-md text-label-md text-on-surface-variant font-medium">Tổng số học phần</p>
<div class="flex items-baseline gap-2 mt-1.5">
<span class="font-headline-xl text-headline-xl font-bold text-on-surface">428</span>
<span class="font-label-sm text-label-sm text-[#15803D] bg-[#DCFCE7] px-1.5 py-0.5 rounded-full font-semibold flex items-center">
<span class="material-symbols-outlined text-[14px]">trending_up</span> +12
              </span>
</div>
<p class="font-body-sm text-body-sm text-outline mt-1">So với niên khóa trước</p>
</div>
<div class="w-12 h-12 rounded-xl bg-primary-fixed text-primary flex items-center justify-center">
<span class="material-symbols-outlined text-[26px]">menu_book</span>
</div>
</div>
<!-- Card 2 -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 shadow-xs flex items-center justify-between hover:border-primary transition-colors">
<div>
<p class="font-label-md text-label-md text-on-surface-variant font-medium">Đang giảng dạy (Active)</p>
<div class="flex items-baseline gap-2 mt-1.5">
<span class="font-headline-xl text-headline-xl font-bold text-primary">386</span>
<span class="text-body-sm text-on-surface-variant">/ 428 HP</span>
</div>
<p class="font-body-sm text-body-sm text-outline mt-1">Tỷ lệ áp dụng: 90.2%</p>
</div>
<div class="w-12 h-12 rounded-xl bg-[#DCFCE7] text-[#15803D] flex items-center justify-center">
<span class="material-symbols-outlined text-[26px]">check_circle</span>
</div>
</div>
<!-- Card 3 -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 shadow-xs flex items-center justify-between hover:border-primary transition-colors">
<div>
<p class="font-label-md text-label-md text-on-surface-variant font-medium">Học phần Đại cương</p>
<div class="flex items-baseline gap-2 mt-1.5">
<span class="font-headline-xl text-headline-xl font-bold text-on-surface">94</span>
<span class="font-label-sm text-label-sm text-tertiary bg-tertiary-fixed px-2 py-0.5 rounded-full font-semibold">22.0%</span>
</div>
<p class="font-body-sm text-body-sm text-outline mt-1">Khối kiến thức chung</p>
</div>
<div class="w-12 h-12 rounded-xl bg-surface-container text-tertiary flex items-center justify-center">
<span class="material-symbols-outlined text-[26px]">public</span>
</div>
</div>
<!-- Card 4 -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 shadow-xs flex items-center justify-between hover:border-primary transition-colors">
<div>
<p class="font-label-md text-label-md text-on-surface-variant font-medium">Học phần Chuyên ngành</p>
<div class="flex items-baseline gap-2 mt-1.5">
<span class="font-headline-xl text-headline-xl font-bold text-on-surface">334</span>
<span class="font-label-sm text-label-sm text-secondary bg-secondary-fixed px-2 py-0.5 rounded-full font-semibold">78.0%</span>
</div>
<p class="font-body-sm text-body-sm text-outline mt-1">Cơ sở &amp; chuyên sâu khoa</p>
</div>
<div class="w-12 h-12 rounded-xl bg-secondary-container text-on-secondary-fixed flex items-center justify-center">
<span class="material-symbols-outlined text-[26px]">terminal</span>
</div>
</div>
</section>
<!-- TOOLBAR: FILTER & ACTIONS -->
<section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-4">
<!-- Filter Controls -->
<div class="flex flex-wrap items-center gap-3 flex-1">
<!-- Keyword input -->
<div class="relative min-w-[240px] flex-1 max-w-sm">
<span class="material-symbols-outlined absolute left-3 top-2.5 text-outline text-[18px]">search</span>
<input aria-label="Nhập mã HP, tên học phần..." class="w-full bg-surface-container-lowest pl-9 pr-3 py-2 rounded-lg border border-outline-variant text-body-md focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary placeholder:text-outline" placeholder="Nhập mã HP, tên học phần..." type="text"/>
</div>
<!-- Khoa Filter -->
<div class="w-48">
<select aria-label="Tất cả Khoa / Viện" class="w-full bg-surface-container-lowest py-2 px-3 rounded-lg border border-outline-variant text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
<option value="">Tất cả Khoa / Viện</option>
<option selected="" value="cntt">Khoa Công nghệ Thông tin</option>
<option value="tp">Khoa Công nghệ Thực phẩm</option>
<option value="qtkd">Khoa Quản trị Kinh doanh</option>
<option value="khud">Khoa Khoa học Ứng dụng</option>
</select>
</div>
<!-- Khối kiến thức Filter -->
<div class="w-44">
<select aria-label="Khối kiến thức: Tất cả" class="w-full bg-surface-container-lowest py-2 px-3 rounded-lg border border-outline-variant text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
<option value="">Khối kiến thức: Tất cả</option>
<option value="dc">Đại cương</option>
<option value="csn">Cơ sở ngành</option>
<option value="cn">Chuyên ngành</option>
</select>
</div>
<!-- Trạng thái Filter -->
<div class="w-40">
<select aria-label="Trạng thái: Tất cả" class="w-full bg-surface-container-lowest py-2 px-3 rounded-lg border border-outline-variant text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
<option value="">Trạng thái: Tất cả</option>
<option value="active">Đang hoạt động</option>
<option value="inactive">Tạm ngừng</option>
</select>
</div>
</div>
<!-- Action Buttons -->
<div class="flex items-center gap-2.5">
<button class="bg-surface-container-lowest border border-outline-variant hover:bg-surface-container-low text-on-surface font-medium px-3.5 py-2 rounded-lg text-body-md flex items-center gap-1.5 transition-colors shadow-xs">
<span class="material-symbols-outlined text-[18px]">upload</span>
            Nhập từ Excel
          </button>
<button class="bg-surface-container-lowest border border-outline-variant hover:bg-surface-container-low text-on-surface font-medium px-3.5 py-2 rounded-lg text-body-md flex items-center gap-1.5 transition-colors shadow-xs">
<span class="material-symbols-outlined text-[18px]">download</span>
            Xuất Excel
          </button>
<button class="bg-primary hover:bg-[#1546a3] text-white font-semibold px-4 py-2 rounded-lg text-body-md flex items-center gap-2 shadow-xs transition-all active:scale-95" onclick="toggleModal('edit-modal', true)">
<span class="material-symbols-outlined text-[18px]">add</span>
            + Thêm học phần mới
          </button>
</div>
</section>
<!-- ==================== MAIN VIEW CONTAINER ==================== -->
<div id="view-container">
<!-- 1. VIEW NORMAL: DATA TABLE -->
<section class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-xs overflow-hidden" id="view-normal">
<div class="overflow-x-auto custom-scrollbar">
<table class="w-full text-left border-collapse min-w-[1080px]">
<thead>
<tr class="bg-surface-container-low/70 border-b border-outline-variant text-on-surface-variant font-label-md text-label-md uppercase">
<!-- Sticky Column: Checkbox + Mã & Tên học phần -->
<th class="py-3 px-4 sticky left-0 bg-surface-container-low z-20 w-80 shadow-[2px_0_4px_-1px_rgba(0,0,0,0.05)]" scope="col">
<div class="flex items-center gap-3">
<input aria-label="Chọn dòng" class="rounded border-outline-variant text-primary focus:ring-primary w-4 h-4" type="checkbox"/>
<span>Mã &amp; Tên học phần</span>
</div>
</th>
<th class="py-3 px-4" scope="col">Số tín chỉ</th>
<th class="py-3 px-4" scope="col">Khoa quản lý</th>
<th class="py-3 px-4" scope="col">Khối kiến thức</th>
<th class="py-3 px-4" scope="col">Học phần tiên quyết</th>
<th class="py-3 px-4" scope="col">Trạng thái</th>
<th class="py-3 px-4 text-right" scope="col">Thao tác</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant/60 font-body-md text-body-md">
<!-- Row 1: Lập trình Web -->
<tr class="hover:bg-surface-container-low/50 transition-colors group">
<td class="py-3.5 px-4 sticky left-0 bg-surface-container-lowest group-hover:bg-surface-container-low/50 z-10 shadow-[2px_0_4px_-1px_rgba(0,0,0,0.05)]">
<div class="flex items-center gap-3">
<input aria-label="Chọn dòng" class="rounded border-outline-variant text-primary focus:ring-primary w-4 h-4" type="checkbox"/>
<div>
<span class="font-semibold text-primary block leading-tight">241_ITEC3201</span>
<span class="text-on-surface font-medium block">Lập trình Web</span>
<span class="text-[11px] text-outline italic">Web Programming</span>
</div>
</div>
</td>
<td class="py-3.5 px-4">
<div class="font-medium text-on-surface">3 TC</div>
<div class="text-body-sm text-on-surface-variant">2 LT - 1 TH</div>
</td>
<td class="py-3.5 px-4 text-on-surface">Khoa CNTT</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center px-2 py-0.5 rounded-full text-label-sm font-semibold bg-surface-container-high text-on-surface">
                      Chuyên ngành
                    </span>
</td>
<td class="py-3.5 px-4">
<div class="flex flex-wrap gap-1.5">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-tertiary-fixed text-on-tertiary-fixed border border-tertiary/20">
                        ITEC1201 (Cơ sở lập trình)
                      </span>
</div>
</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-label-sm font-semibold bg-[#DCFCE7] text-[#15803D]">
<span class="w-1.5 h-1.5 rounded-full bg-[#15803D]"></span>
                      Đang hoạt động
                    </span>
</td>
<td class="py-3.5 px-4 text-right">
<div class="flex items-center justify-end gap-1">
<button class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-surface-container-low rounded-lg transition-colors" onclick="toggleModal('edit-modal', true)" title="Chỉnh sửa">
<span class="material-symbols-outlined text-[18px]">edit</span>
</button>
<button class="p-1.5 text-on-surface-variant hover:text-tertiary hover:bg-surface-container-low rounded-lg transition-colors" title="Xem CTĐT">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
<button class="p-1.5 text-on-surface-variant hover:text-error hover:bg-error-container rounded-lg transition-colors" onclick="toggleModal('confirm-modal', true)" title="Ngừng hoạt động">
<span class="material-symbols-outlined text-[18px]">lock</span>
</button>
</div>
</td>
</tr>
<!-- Row 2: Cơ sở dữ liệu phân tán -->
<tr class="hover:bg-surface-container-low/50 transition-colors group">
<td class="py-3.5 px-4 sticky left-0 bg-surface-container-lowest group-hover:bg-surface-container-low/50 z-10 shadow-[2px_0_4px_-1px_rgba(0,0,0,0.05)]">
<div class="flex items-center gap-3">
<input aria-label="Chọn dòng" class="rounded border-outline-variant text-primary focus:ring-primary w-4 h-4" type="checkbox"/>
<div>
<span class="font-semibold text-primary block leading-tight">241_ITEC3202</span>
<span class="text-on-surface font-medium block">Cơ sở dữ liệu phân tán</span>
<span class="text-[11px] text-outline italic">Distributed Database Systems</span>
</div>
</div>
</td>
<td class="py-3.5 px-4">
<div class="font-medium text-on-surface">3 TC</div>
<div class="text-body-sm text-on-surface-variant">2 LT - 1 TH</div>
</td>
<td class="py-3.5 px-4 text-on-surface">Khoa CNTT</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center px-2 py-0.5 rounded-full text-label-sm font-semibold bg-surface-container-high text-on-surface">
                      Chuyên ngành
                    </span>
</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-tertiary-fixed text-on-tertiary-fixed border border-tertiary/20">
                      ITEC2104 (Hệ QT CSDL)
                    </span>
</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-label-sm font-semibold bg-[#DCFCE7] text-[#15803D]">
<span class="w-1.5 h-1.5 rounded-full bg-[#15803D]"></span>
                      Đang hoạt động
                    </span>
</td>
<td class="py-3.5 px-4 text-right">
<div class="flex items-center justify-end gap-1">
<button class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-surface-container-low rounded-lg transition-colors" onclick="toggleModal('edit-modal', true)">
<span class="material-symbols-outlined text-[18px]">edit</span>
</button>
<button class="p-1.5 text-on-surface-variant hover:text-tertiary hover:bg-surface-container-low rounded-lg transition-colors">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
<button class="p-1.5 text-on-surface-variant hover:text-error hover:bg-error-container rounded-lg transition-colors" onclick="toggleModal('confirm-modal', true)">
<span class="material-symbols-outlined text-[18px]">lock</span>
</button>
</div>
</td>
</tr>
<!-- Row 3: Lập trình Mạng -->
<tr class="hover:bg-surface-container-low/50 transition-colors group">
<td class="py-3.5 px-4 sticky left-0 bg-surface-container-lowest group-hover:bg-surface-container-low/50 z-10 shadow-[2px_0_4px_-1px_rgba(0,0,0,0.05)]">
<div class="flex items-center gap-3">
<input aria-label="Chọn dòng" class="rounded border-outline-variant text-primary focus:ring-primary w-4 h-4" type="checkbox"/>
<div>
<span class="font-semibold text-primary block leading-tight">241_ITEC3203</span>
<span class="text-on-surface font-medium block">Lập trình Mạng</span>
<span class="text-[11px] text-outline italic">Network Programming</span>
</div>
</div>
</td>
<td class="py-3.5 px-4">
<div class="font-medium text-on-surface">3 TC</div>
<div class="text-body-sm text-on-surface-variant">2 LT - 1 TH</div>
</td>
<td class="py-3.5 px-4 text-on-surface">Khoa CNTT</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center px-2 py-0.5 rounded-full text-label-sm font-semibold bg-surface-container-high text-on-surface">
                      Chuyên ngành
                    </span>
</td>
<td class="py-3.5 px-4">
<div class="flex flex-wrap gap-1.5">
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-semibold bg-tertiary-fixed text-on-tertiary-fixed border border-tertiary/20">
                        ITEC2202
                      </span>
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-semibold bg-tertiary-fixed text-on-tertiary-fixed border border-tertiary/20">
                        ITEC1201
                      </span>
</div>
</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-label-sm font-semibold bg-[#DCFCE7] text-[#15803D]">
<span class="w-1.5 h-1.5 rounded-full bg-[#15803D]"></span>
                      Đang hoạt động
                    </span>
</td>
<td class="py-3.5 px-4 text-right">
<div class="flex items-center justify-end gap-1">
<button class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-surface-container-low rounded-lg transition-colors" onclick="toggleModal('edit-modal', true)">
<span class="material-symbols-outlined text-[18px]">edit</span>
</button>
<button class="p-1.5 text-on-surface-variant hover:text-tertiary hover:bg-surface-container-low rounded-lg transition-colors">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
<button class="p-1.5 text-on-surface-variant hover:text-error hover:bg-error-container rounded-lg transition-colors" onclick="toggleModal('confirm-modal', true)">
<span class="material-symbols-outlined text-[18px]">lock</span>
</button>
</div>
</td>
</tr>
<!-- Row 4: Giải tích 1 -->
<tr class="hover:bg-surface-container-low/50 transition-colors group">
<td class="py-3.5 px-4 sticky left-0 bg-surface-container-lowest group-hover:bg-surface-container-low/50 z-10 shadow-[2px_0_4px_-1px_rgba(0,0,0,0.05)]">
<div class="flex items-center gap-3">
<input aria-label="Chọn dòng" class="rounded border-outline-variant text-primary focus:ring-primary w-4 h-4" type="checkbox"/>
<div>
<span class="font-semibold text-primary block leading-tight">241_MATH1101</span>
<span class="text-on-surface font-medium block">Giải tích 1</span>
<span class="text-[11px] text-outline italic">Calculus 1</span>
</div>
</div>
</td>
<td class="py-3.5 px-4">
<div class="font-medium text-on-surface">3 TC</div>
<div class="text-body-sm text-on-surface-variant">3 LT - 0 TH</div>
</td>
<td class="py-3.5 px-4 text-on-surface">Khoa Khoa học Ứng dụng</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center px-2 py-0.5 rounded-full text-label-sm font-semibold bg-tertiary-fixed text-on-tertiary-fixed">
                      Đại cương
                    </span>
</td>
<td class="py-3.5 px-4">
<span class="text-outline text-body-sm italic">Không có</span>
</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-label-sm font-semibold bg-[#DCFCE7] text-[#15803D]">
<span class="w-1.5 h-1.5 rounded-full bg-[#15803D]"></span>
                      Đang hoạt động
                    </span>
</td>
<td class="py-3.5 px-4 text-right">
<div class="flex items-center justify-end gap-1">
<button class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-surface-container-low rounded-lg transition-colors" onclick="toggleModal('edit-modal', true)">
<span class="material-symbols-outlined text-[18px]">edit</span>
</button>
<button class="p-1.5 text-on-surface-variant hover:text-tertiary hover:bg-surface-container-low rounded-lg transition-colors">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
<button class="p-1.5 text-on-surface-variant hover:text-error hover:bg-error-container rounded-lg transition-colors" onclick="toggleModal('confirm-modal', true)">
<span class="material-symbols-outlined text-[18px]">lock</span>
</button>
</div>
</td>
</tr>
<!-- Row 5: Xử lý ảnh nâng cao (Tạm ngừng) -->
<tr class="hover:bg-surface-container-low/50 transition-colors group bg-surface-container-low/20">
<td class="py-3.5 px-4 sticky left-0 bg-surface-container-lowest group-hover:bg-surface-container-low/50 z-10 shadow-[2px_0_4px_-1px_rgba(0,0,0,0.05)]">
<div class="flex items-center gap-3">
<input aria-label="Chọn dòng" class="rounded border-outline-variant text-primary focus:ring-primary w-4 h-4" type="checkbox"/>
<div>
<span class="font-semibold text-outline block leading-tight">241_ITEC4105</span>
<span class="text-on-surface-variant font-medium block">Xử lý ảnh nâng cao (Cũ)</span>
<span class="text-[11px] text-outline italic">Advanced Digital Image Processing</span>
</div>
</div>
</td>
<td class="py-3.5 px-4">
<div class="font-medium text-on-surface-variant">2 TC</div>
<div class="text-body-sm text-outline">2 LT - 0 TH</div>
</td>
<td class="py-3.5 px-4 text-on-surface-variant">Khoa CNTT</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center px-2 py-0.5 rounded-full text-label-sm font-semibold bg-surface-container-high text-on-surface-variant">
                      Chuyên ngành tự chọn
                    </span>
</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-semibold bg-surface-container-high text-on-surface-variant border border-outline-variant">
                      ITEC3102
                    </span>
</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-label-sm font-semibold bg-[#FEF3C7] text-[#B45309]">
<span class="w-1.5 h-1.5 rounded-full bg-[#B45309]"></span>
                      Tạm ngừng
                    </span>
</td>
<td class="py-3.5 px-4 text-right">
<div class="flex items-center justify-end gap-1">
<button class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-surface-container-low rounded-lg transition-colors" onclick="toggleModal('edit-modal', true)">
<span class="material-symbols-outlined text-[18px]">edit</span>
</button>
<button class="p-1.5 text-on-surface-variant hover:text-tertiary hover:bg-surface-container-low rounded-lg transition-colors">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
<button class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-surface-container-low rounded-lg transition-colors" title="Kích hoạt lại học phần">
<span class="material-symbols-outlined text-[18px]">lock_open</span>
</button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<!-- Pagination Footer -->
<div class="p-4 border-t border-outline-variant flex flex-col sm:flex-row items-center justify-between gap-3 text-body-sm text-on-surface-variant">
<div class="flex items-center gap-2">
<span>Hiển thị</span>
<select aria-label="10 học phần / trang" class="bg-surface-container-lowest border border-outline-variant rounded-md px-2 py-1 text-on-surface focus:outline-none focus:border-primary">
<option>10 học phần / trang</option>
<option>25 học phần / trang</option>
<option>50 học phần / trang</option>
</select>
<span>trên tổng số <strong>428</strong> học phần</span>
</div>
<div class="flex items-center gap-1">
<button class="px-2.5 py-1 rounded-md border border-outline-variant text-on-surface-variant hover:bg-surface-container-low disabled:opacity-50" disabled="">
<span class="material-symbols-outlined text-[16px]">chevron_left</span>
</button>
<button class="px-3 py-1 rounded-md bg-primary text-white font-semibold text-label-md">1</button>
<button class="px-3 py-1 rounded-md hover:bg-surface-container-low text-on-surface font-semibold text-label-md">2</button>
<button class="px-3 py-1 rounded-md hover:bg-surface-container-low text-on-surface font-semibold text-label-md">3</button>
<span class="px-1 text-outline">...</span>
<button class="px-3 py-1 rounded-md hover:bg-surface-container-low text-on-surface font-semibold text-label-md">43</button>
<button class="px-2.5 py-1 rounded-md border border-outline-variant text-on-surface hover:bg-surface-container-low">
<span class="material-symbols-outlined text-[16px]">chevron_right</span>
</button>
</div>
</div>
</section>
<!-- 2. VIEW LOADING (SKELETON SHIMMER) -->
<section class="hidden bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-xs space-y-4" id="view-loading">
<div class="flex items-center justify-between border-b border-outline-variant pb-4">
<div class="h-6 w-48 bg-surface-container-high rounded animate-pulse"></div>
<div class="h-8 w-32 bg-surface-container-high rounded animate-pulse"></div>
</div>
<div class="space-y-3">
<div class="h-12 w-full bg-surface-container-low rounded-lg animate-pulse flex items-center px-4 gap-4">
<div class="w-4 h-4 bg-outline-variant rounded"></div>
<div class="w-40 h-4 bg-outline-variant rounded"></div>
<div class="w-24 h-4 bg-outline-variant rounded"></div>
<div class="w-32 h-4 bg-outline-variant rounded"></div>
<div class="w-48 h-4 bg-outline-variant rounded"></div>
</div>
<div class="h-12 w-full bg-surface-container-low rounded-lg animate-pulse flex items-center px-4 gap-4">
<div class="w-4 h-4 bg-outline-variant rounded"></div>
<div class="w-40 h-4 bg-outline-variant rounded"></div>
<div class="w-24 h-4 bg-outline-variant rounded"></div>
<div class="w-32 h-4 bg-outline-variant rounded"></div>
<div class="w-48 h-4 bg-outline-variant rounded"></div>
</div>
<div class="h-12 w-full bg-surface-container-low rounded-lg animate-pulse flex items-center px-4 gap-4">
<div class="w-4 h-4 bg-outline-variant rounded"></div>
<div class="w-40 h-4 bg-outline-variant rounded"></div>
<div class="w-24 h-4 bg-outline-variant rounded"></div>
<div class="w-32 h-4 bg-outline-variant rounded"></div>
<div class="w-48 h-4 bg-outline-variant rounded"></div>
</div>
</div>
<div class="text-center pt-2 text-on-surface-variant font-body-sm animate-pulse">
            Đang tải dữ liệu danh mục học phần từ cơ sở dữ liệu đào tạo HUIT...
          </div>
</section>
<!-- 3. VIEW EMPTY STATE -->
<section class="hidden bg-surface-container-lowest border border-outline-variant rounded-xl p-12 shadow-xs text-center" id="view-empty">
<div class="w-16 h-16 rounded-full bg-surface-container-low text-primary mx-auto flex items-center justify-center mb-4">
<span class="material-symbols-outlined text-[36px]">library_books</span>
</div>
<h3 class="font-headline-md text-headline-md font-bold text-on-surface">Không tìm thấy học phần nào</h3>
<p class="font-body-md text-body-md text-on-surface-variant max-w-md mx-auto mt-2">
            Không có dữ liệu học phần phù hợp với bộ lọc đã chọn hoặc chưa có học phần nào được khởi tạo cho học kỳ này.
          </p>
<div class="mt-6 flex items-center justify-center gap-3">
<button class="px-4 py-2 bg-surface-container-low text-on-surface rounded-lg font-medium hover:bg-surface-container-high transition-colors text-body-md" onclick="switchView('normal')">
              Xóa bộ lọc
            </button>
<button class="px-4 py-2 bg-primary text-white rounded-lg font-semibold hover:bg-[#1546a3] transition-colors text-body-md flex items-center gap-1.5 shadow-xs" onclick="toggleModal('edit-modal', true)">
<span class="material-symbols-outlined text-[18px]">add</span>
              + Tạo học phần mới
            </button>
</div>
</section>
<!-- 4. VIEW ERROR STATE -->
<section class="hidden bg-surface-container-lowest border border-error/30 rounded-xl p-12 shadow-xs text-center" id="view-error">
<div class="w-16 h-16 rounded-full bg-error-container text-error mx-auto flex items-center justify-center mb-4">
<span class="material-symbols-outlined text-[36px]">cloud_off</span>
</div>
<h3 class="font-headline-md text-headline-md font-bold text-error">Lỗi kết nối máy chủ đào tạo</h3>
<p class="font-body-md text-body-md text-on-surface-variant max-w-lg mx-auto mt-2">
            Hệ thống không thể đồng bộ danh sách học phần từ máy chủ CSDL Đào tạo HUIT (Mã lỗi: <code class="font-mono text-error font-semibold">ERR_ORACLE_DB_TIMEOUT_504</code>).
          </p>
<div class="mt-6 flex items-center justify-center gap-3">
<button class="px-4 py-2 bg-primary text-white rounded-lg font-semibold hover:bg-[#1546a3] transition-colors text-body-md flex items-center gap-2 shadow-xs" onclick="switchView('normal')">
<span class="material-symbols-outlined text-[18px]">refresh</span>
              Thử kết nối lại
            </button>
<button class="px-4 py-2 border border-outline-variant text-on-surface rounded-lg font-medium hover:bg-surface-container-low transition-colors text-body-md">
              Kiểm tra trạng thái máy chủ
            </button>
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
