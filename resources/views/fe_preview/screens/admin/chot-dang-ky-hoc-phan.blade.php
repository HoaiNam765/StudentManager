@extends('fe_preview.layouts.admin')
@section('page_title', 'HUIT Học Vụ - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="light"@endsection
@section('body_attributes')class="bg-[#f8f9ff] text-slate-800 antialiased min-h-screen flex selection:bg-blue-100 selection:text-blue-900"@endsection
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
<!-- Breadcrumb & Top Bar -->
<nav class="flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm">
<span>Đào tạo</span>
<span class="material-symbols-outlined text-[16px]">chevron_right</span>
<span>Quản lý Đăng ký học phần</span>
<span class="material-symbols-outlined text-[16px]">chevron_right</span>
<span class="text-primary font-semibold">Chốt đợt đăng ký học phần</span>
</nav>
<!-- Page Title & Primary Meta -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
<div>
<div class="flex items-center gap-3">
<h1 class="font-headline-xl text-headline-xl text-on-surface font-bold tracking-tight">
              Chốt đợt Đăng ký học phần: Đợt 1 - Chính thức Khóa 2021 &amp; 2022
            </h1>
<span class="bg-primary text-white font-label-sm text-label-sm font-semibold px-2.5 py-1 rounded-full shadow-xs">
              HK1 (2026-2027)
            </span>
</div>
<p class="text-on-surface-variant font-body-md text-body-md mt-1">
            Kiểm tra toàn bộ điều kiện mở lớp, xử lý các lớp học phần không đủ sĩ số và khóa danh sách trước khi đưa vào thời khóa biểu học vụ chính thức.
          </p>
</div>
<div class="flex items-center gap-3">
<button class="bg-surface-container-lowest border border-outline-variant text-on-surface hover:bg-surface-container-low font-label-lg text-label-lg px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
<span class="material-symbols-outlined text-[18px]">download</span>
            Xuất dữ liệu XLS
          </button>
<button class="bg-primary hover:bg-[#3B66D1] text-white font-label-lg text-label-lg px-5 py-2.5 rounded-lg shadow-sm transition-colors flex items-center gap-2" onclick="document.getElementById('dangerModal').classList.remove('hidden')">
<span class="material-symbols-outlined text-[18px]">lock</span>
            Tiến hành Chốt đợt đăng ký
          </button>
</div>
</div>
<!-- Stepper Component (2 Bước trực quan) -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-4 shadow-sm">
<div class="grid grid-cols-2 gap-4">
<!-- Step 1: Hoàn thành -->
<div class="flex items-center gap-3.5 p-3 rounded-lg bg-surface-container-low border border-outline-variant/60">
<div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold">
<span class="material-symbols-outlined text-[20px]">check</span>
</div>
<div>
<div class="font-label-lg text-label-lg font-bold text-on-surface">
                1. Rà soát &amp; Thống kê tổng hợp
              </div>
<div class="font-body-sm text-body-sm text-emerald-700 font-medium">
                Đã hoàn thành phân tích sĩ số 1.240 lớp học phần
              </div>
</div>
</div>
<!-- Step 2: Đang thực hiện -->
<div class="flex items-center gap-3.5 p-3 rounded-lg bg-primary-fixed/40 border-2 border-primary">
<div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold font-label-md text-label-md">
              2
            </div>
<div>
<div class="font-label-lg text-label-lg font-bold text-primary">
                2. Xác nhận chốt &amp; Khóa dữ liệu học vụ
              </div>
<div class="font-body-sm text-body-sm text-on-surface-variant font-medium">
                Đang chờ xác nhận phê duyệt nguy hiểm từ Quản trị viên
              </div>
</div>
</div>
</div>
</div>
<!-- 4 Thẻ KPI Phân tích tổng quan -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-5">
<!-- Card 1 -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-5 shadow-sm flex flex-col justify-between">
<div class="flex items-start justify-between">
<span class="font-label-md text-label-md text-on-surface-variant">Tổng SV đã đăng ký</span>
<div class="w-9 h-9 rounded-lg bg-surface-container-high text-primary flex items-center justify-center">
<span class="material-symbols-outlined text-[20px]">group</span>
</div>
</div>
<div class="mt-4">
<div class="font-headline-lg text-headline-lg font-bold text-on-surface">14.200 SV</div>
<div class="font-body-sm text-body-sm text-on-surface-variant mt-1 flex items-center gap-1.5">
<span class="text-emerald-700 font-semibold font-label-sm text-label-sm">94.6%</span>
<span>chỉ tiêu dự kiến đợt 1</span>
</div>
</div>
</div>
<!-- Card 2 -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-5 shadow-sm flex flex-col justify-between">
<div class="flex items-start justify-between">
<span class="font-label-md text-label-md text-on-surface-variant">Lớp học phần đủ sĩ số</span>
<div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
<span class="material-symbols-outlined text-[20px]">task_alt</span>
</div>
</div>
<div class="mt-4">
<div class="flex items-center justify-between">
<div class="font-headline-lg text-headline-lg font-bold text-on-surface">1.198 <span class="font-body-md text-body-md font-normal text-on-surface-variant">lớp</span></div>
<span class="bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold px-2 py-0.5 rounded-full">
                Đạt chuẩn
              </span>
</div>
<div class="font-body-sm text-body-sm text-on-surface-variant mt-1">
              Sĩ số hiện tại &gt;= 30 SV / lớp
            </div>
</div>
</div>
<!-- Card 3 -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-5 shadow-sm flex flex-col justify-between">
<div class="flex items-start justify-between">
<span class="font-label-md text-label-md text-on-surface-variant">Lớp dưới ngưỡng (&lt;15 SV)</span>
<div class="w-9 h-9 rounded-lg bg-red-50 text-error flex items-center justify-center">
<span class="material-symbols-outlined text-[20px]">warning</span>
</div>
</div>
<div class="mt-4">
<div class="flex items-center justify-between">
<div class="font-headline-lg text-headline-lg font-bold text-error">42 <span class="font-body-md text-body-md font-normal text-on-surface-variant">lớp</span></div>
<span class="bg-red-100 text-red-700 font-label-sm text-label-sm font-semibold px-2 py-0.5 rounded-full">
                Cần hủy/gộp
              </span>
</div>
<div class="font-body-sm text-body-sm text-error font-medium mt-1">
              Chiếm 3.38% tổng số lớp học phần
            </div>
</div>
</div>
<!-- Card 4 -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-5 shadow-sm flex flex-col justify-between">
<div class="flex items-start justify-between">
<span class="font-label-md text-label-md text-on-surface-variant">Đăng ký xung đột / Lỗi</span>
<div class="w-9 h-9 rounded-lg bg-surface-container-high text-primary flex items-center justify-center">
<span class="material-symbols-outlined text-[20px]">verified</span>
</div>
</div>
<div class="mt-4">
<div class="flex items-center justify-between">
<div class="font-headline-lg text-headline-lg font-bold text-on-surface">0 <span class="font-body-md text-body-md font-normal text-on-surface-variant">tồn đọng</span></div>
<span class="bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold px-2 py-0.5 rounded-full">
                Hoàn tất
              </span>
</div>
<div class="font-body-sm text-body-sm text-on-surface-variant mt-1">
              Đã rà soát trùng lịch &amp; tiên quyết
            </div>
</div>
</div>
</div>
<!-- Data Panel: Bảng rà soát các lớp học phần có vấn đề -->
<section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
<!-- Table Header & Filter Bar -->
<div class="p-5 border-b border-[#F1F5F9] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
<div>
<div class="flex items-center gap-2">
<h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">
                Danh mục 42 lớp học phần dưới ngưỡng cần xử lý trước khi chốt
              </h2>
<span class="bg-red-100 text-red-800 text-label-sm font-label-sm font-bold px-2 py-0.5 rounded-full">
                Cần quyết định
              </span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">
              Các lớp học phần này sẽ tự động bị hủy hoặc gộp theo phương án khi kích hoạt lệnh Chốt dữ liệu.
            </p>
</div>
<div class="flex items-center gap-3">
<div class="relative">
<span class="material-symbols-outlined text-[18px] text-on-surface-variant absolute left-3 top-2.5">search</span>
<input aria-label="Tìm theo mã lớp hoặc môn..." class="pl-9 pr-3 py-1.5 text-body-sm font-body-sm rounded-lg border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary w-64 bg-surface-container-lowest" placeholder="Tìm theo mã lớp hoặc môn..." type="text"/>
</div>
<select aria-label="Tất cả khoa" class="py-1.5 px-3 text-body-sm font-body-sm rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface">
<option>Tất cả khoa</option>
<option>Khoa CNTT</option>
<option>Khoa CNSH &amp; TP</option>
<option>Khoa Khoa học ứng dụng</option>
</select>
</div>
</div>
<!-- Academic Data Table -->
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-[#F8FAFC] border-b border-[#F1F5F9]">
<th class="py-3 px-4 font-label-md text-label-md text-secondary uppercase tracking-wider text-center w-12">STT</th>
<th class="py-3 px-4 font-label-md text-label-md text-secondary uppercase tracking-wider">Mã lớp học phần</th>
<th class="py-3 px-4 font-label-md text-label-md text-secondary uppercase tracking-wider">Tên học phần</th>
<th class="py-3 px-4 font-label-md text-label-md text-secondary uppercase tracking-wider">Khoa quản lý</th>
<th class="py-3 px-4 font-label-md text-label-md text-secondary uppercase tracking-wider">Giảng viên</th>
<th class="py-3 px-4 font-label-md text-label-md text-secondary uppercase tracking-wider text-center">Sĩ số / Tối đa</th>
<th class="py-3 px-4 font-label-md text-label-md text-secondary uppercase tracking-wider text-center">Ngưỡng tối thiểu</th>
<th class="py-3 px-4 font-label-md text-label-md text-secondary uppercase tracking-wider">Vấn đề phát hiện</th>
<th class="py-3 px-4 font-label-md text-label-md text-secondary uppercase tracking-wider">Phương án đề xuất</th>
<th class="py-3 px-4 font-label-md text-label-md text-secondary uppercase tracking-wider text-right">Thao tác</th>
</tr>
</thead>
<tbody class="divide-y divide-[#F1F5F9]">
<!-- Row 1 -->
<tr class="hover:bg-[#F8FAFC] transition-colors">
<td class="py-3.5 px-4 font-body-sm text-body-sm text-center text-on-surface-variant font-medium">1</td>
<td class="py-3.5 px-4 font-label-md text-label-md font-semibold text-primary">
                  241_71CHEM1101_03
                </td>
<td class="py-3.5 px-4 font-body-sm text-body-sm font-semibold text-on-surface">
                  Hóa đại cương B
                </td>
<td class="py-3.5 px-4 font-body-sm text-body-sm text-on-surface-variant">
                  Khoa CNSH &amp; TP
                </td>
<td class="py-3.5 px-4 font-body-sm text-body-sm text-on-surface">
                  TS. Người dùng mẫu 45
                </td>
<td class="py-3.5 px-4 text-center">
<span class="font-label-md text-label-md font-bold text-error">8</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">/ 60 SV</span>
</td>
<td class="py-3.5 px-4 font-body-sm text-body-sm text-center text-on-surface-variant font-medium">
                  30 SV
                </td>
<td class="py-3.5 px-4">
<span class="bg-red-100 text-red-700 font-label-sm text-label-sm font-semibold px-2.5 py-1 rounded-full whitespace-nowrap">
                    Dưới ngưỡng mở lớp
                  </span>
</td>
<td class="py-3.5 px-4 font-body-sm text-body-sm font-semibold text-error">
                  Hủy lớp &amp; Hoàn tín chỉ
                </td>
<td class="py-3.5 px-4 text-right">
<button class="text-primary hover:text-[#3B66D1] font-label-sm text-label-sm font-semibold hover:underline">
                    Điều chỉnh
                  </button>
</td>
</tr>
<!-- Row 2 -->
<tr class="hover:bg-[#F8FAFC] transition-colors">
<td class="py-3.5 px-4 font-body-sm text-body-sm text-center text-on-surface-variant font-medium">2</td>
<td class="py-3.5 px-4 font-label-md text-label-md font-semibold text-primary">
                  241_71ITEC3304_02
                </td>
<td class="py-3.5 px-4 font-body-sm text-body-sm font-semibold text-on-surface">
                  Chuyên đề AI nâng cao
                </td>
<td class="py-3.5 px-4 font-body-sm text-body-sm text-on-surface-variant">
                  Khoa CNTT
                </td>
<td class="py-3.5 px-4 font-body-sm text-body-sm text-on-surface">
                  ThS. Người dùng mẫu 10
                </td>
<td class="py-3.5 px-4 text-center">
<span class="font-label-md text-label-md font-bold text-error">12</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">/ 40 SV</span>
</td>
<td class="py-3.5 px-4 font-body-sm text-body-sm text-center text-on-surface-variant font-medium">
                  25 SV
                </td>
<td class="py-3.5 px-4">
<span class="bg-red-100 text-red-700 font-label-sm text-label-sm font-semibold px-2.5 py-1 rounded-full whitespace-nowrap">
                    Dưới ngưỡng mở lớp
                  </span>
</td>
<td class="py-3.5 px-4 font-body-sm text-body-sm font-semibold text-tertiary">
                  Gộp vào lớp 01 (Sĩ số 28)
                </td>
<td class="py-3.5 px-4 text-right">
<button class="text-primary hover:text-[#3B66D1] font-label-sm text-label-sm font-semibold hover:underline">
                    Điều chỉnh
                  </button>
</td>
</tr>
<!-- Row 3 -->
<tr class="hover:bg-[#F8FAFC] transition-colors">
<td class="py-3.5 px-4 font-body-sm text-body-sm text-center text-on-surface-variant font-medium">3</td>
<td class="py-3.5 px-4 font-label-md text-label-md font-semibold text-primary">
                  241_71MATH1102_05
                </td>
<td class="py-3.5 px-4 font-body-sm text-body-sm font-semibold text-on-surface">
                  Xác suất thống kê
                </td>
<td class="py-3.5 px-4 font-body-sm text-body-sm text-on-surface-variant">
                  Khoa Khoa học ứng dụng
                </td>
<td class="py-3.5 px-4 font-body-sm text-body-sm text-on-surface">
                  GVC. Người dùng mẫu 24
                </td>
<td class="py-3.5 px-4 text-center">
<span class="font-label-md text-label-md font-bold text-error">14</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">/ 70 SV</span>
</td>
<td class="py-3.5 px-4 font-body-sm text-body-sm text-center text-on-surface-variant font-medium">
                  30 SV
                </td>
<td class="py-3.5 px-4">
<span class="bg-red-100 text-red-700 font-label-sm text-label-sm font-semibold px-2.5 py-1 rounded-full whitespace-nowrap">
                    Dưới ngưỡng mở lớp
                  </span>
</td>
<td class="py-3.5 px-4 font-body-sm text-body-sm font-semibold text-error">
                  Hủy lớp &amp; Hoàn tín chỉ
                </td>
<td class="py-3.5 px-4 text-right">
<button class="text-primary hover:text-[#3B66D1] font-label-sm text-label-sm font-semibold hover:underline">
                    Điều chỉnh
                  </button>
</td>
</tr>
<!-- Row 4 (Bổ sung để bảng đầy đặn thực tế) -->
<tr class="hover:bg-[#F8FAFC] transition-colors">
<td class="py-3.5 px-4 font-body-sm text-body-sm text-center text-on-surface-variant font-medium">4</td>
<td class="py-3.5 px-4 font-label-md text-label-md font-semibold text-primary">
                  241_71MECH2205_01
                </td>
<td class="py-3.5 px-4 font-body-sm text-body-sm font-semibold text-on-surface">
                  Cơ sở thiết kế máy công nghiệp
                </td>
<td class="py-3.5 px-4 font-body-sm text-body-sm text-on-surface-variant">
                  Khoa Cơ khí động lực
                </td>
<td class="py-3.5 px-4 font-body-sm text-body-sm text-on-surface">
                  TS. Người dùng mẫu 52
                </td>
<td class="py-3.5 px-4 text-center">
<span class="font-label-md text-label-md font-bold text-error">11</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">/ 50 SV</span>
</td>
<td class="py-3.5 px-4 font-body-sm text-body-sm text-center text-on-surface-variant font-medium">
                  25 SV
                </td>
<td class="py-3.5 px-4">
<span class="bg-red-100 text-red-700 font-label-sm text-label-sm font-semibold px-2.5 py-1 rounded-full whitespace-nowrap">
                    Dưới ngưỡng mở lớp
                  </span>
</td>
<td class="py-3.5 px-4 font-body-sm text-body-sm font-semibold text-error">
                  Hủy lớp &amp; Chuyển sang HK2
                </td>
<td class="py-3.5 px-4 text-right">
<button class="text-primary hover:text-[#3B66D1] font-label-sm text-label-sm font-semibold hover:underline">
                    Điều chỉnh
                  </button>
</td>
</tr>
</tbody>
</table>
</div>
<!-- Table Pagination Footer -->
<div class="p-4 border-t border-[#F1F5F9] bg-[#F8FAFC] flex items-center justify-between">
<div class="font-body-sm text-body-sm text-on-surface-variant">
            Hiển thị <span class="font-semibold text-on-surface">1 - 4</span> trong tổng số <span class="font-semibold text-on-surface">42</span> lớp học phần cần can thiệp
          </div>
<div class="flex items-center gap-2">
<button class="px-3 py-1 text-body-sm font-body-sm border border-outline-variant rounded-md bg-white text-on-surface-variant disabled:opacity-50" disabled="">
              Trước
            </button>
<span class="px-2 py-1 text-label-sm font-label-sm font-bold bg-primary text-white rounded">1</span>
<button class="px-2 py-1 text-body-sm font-body-sm border border-outline-variant rounded bg-white text-on-surface hover:bg-slate-50">2</button>
<button class="px-2 py-1 text-body-sm font-body-sm border border-outline-variant rounded bg-white text-on-surface hover:bg-slate-50">3</button>
<button class="px-3 py-1 text-body-sm font-body-sm border border-outline-variant rounded-md bg-white text-on-surface hover:bg-slate-50">
              Tiếp
            </button>
</div>
</div>
</section>
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
