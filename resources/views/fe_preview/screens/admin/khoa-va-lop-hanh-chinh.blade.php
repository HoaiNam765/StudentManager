@extends('fe_preview.layouts.admin')
@section('page_title', 'Quản lý Khóa tuyển sinh và Lớp hành chính - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Quản lý Khóa tuyển sinh và Lớp hành chính - HUIT Học Vụ</title>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/khoa-va-lop-hanh-chinh']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">badge</span>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Quản lý Khóa tuyển sinh và Lớp hành chính</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Quản lý Khóa tuyển sinh và Lớp hành chính</div>
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
<div class="max-w-[1440px] mx-auto p-6 space-y-6">
<!-- ================= Page Header & Meta ================= -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
<div>
<div class="flex items-center gap-2 mb-1">
<span class="font-label-sm text-label-sm uppercase tracking-wider px-2 py-0.5 rounded bg-primary-fixed text-primary font-semibold">
              ĐÀO TẠO • CLASS-ADM
            </span>
<span class="text-xs text-on-surface-variant">• Mã phân hệ: HUIT-ADM-2024</span>
</div>
<h1 class="font-headline-xl text-headline-xl text-on-surface font-bold tracking-tight">
            Quản lý Khóa tuyển sinh &amp; Lớp hành chính
          </h1>
<p class="font-body-md text-body-md text-on-surface-variant mt-1">
            Quản lý các khóa tuyển sinh đào tạo chính quy, cơ cấu lớp sinh viên và phân công Cố vấn học tập (CVHT).
          </p>
</div>
<div class="flex items-center gap-3">
<button class="h-10 px-4 bg-surface-container-lowest border border-outline-variant hover:bg-surface-container-low text-on-surface font-label-md text-label-md rounded-lg flex items-center gap-2 transition-colors shadow-xs">
<span class="material-symbols-outlined text-lg">history</span>
<span>Lịch sử điều chuyển</span>
</button>
<button class="h-10 px-4 bg-primary hover:bg-[#15429b] text-white font-label-md text-label-md font-semibold rounded-lg flex items-center gap-2 transition-colors shadow-sm" onclick="document.getElementById('modal-add-class').classList.remove('hidden')">
<span class="material-symbols-outlined text-lg">add_circle</span>
<span>+ Thêm lớp hành chính</span>
</button>
</div>
</div>
<!-- ================= Level 1 Tabs ================= -->
<div class="border-b border-outline-variant flex gap-8">
<!-- Tab 1 -->
<button class="pb-3 px-1 font-headline-sm text-headline-sm text-on-surface-variant hover:text-on-surface border-b-2 border-transparent transition-all flex items-center gap-2 font-medium">
<span>Khóa tuyển sinh (Cohorts)</span>
<span class="px-2 py-0.5 rounded-full bg-surface-container text-on-surface-variant text-label-sm font-label-sm">5 khóa</span>
</button>
<!-- Tab 2: ACTIVE -->
<button class="pb-3 px-1 font-headline-sm text-headline-sm text-primary border-b-2 border-primary transition-all flex items-center gap-2 font-semibold">
<span>Lớp hành chính (Classes)</span>
<span class="px-2 py-0.5 rounded-full bg-primary-fixed text-primary text-label-sm font-label-sm font-bold">248 lớp</span>
</button>
</div>
<!-- ================= 4 Summary Metric Cards ================= -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
<!-- Card 1 -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-xs flex items-center justify-between">
<div class="space-y-1">
<span class="font-label-md text-label-md text-on-surface-variant">Tổng số lớp</span>
<div class="font-headline-lg text-headline-lg font-bold text-on-surface">248 <span class="font-body-sm text-body-sm font-normal text-on-surface-variant">lớp hành chính</span></div>
<div class="font-body-sm text-body-sm text-tertiary flex items-center gap-1">
<span class="material-symbols-outlined text-sm">trending_up</span>
<span>Tăng 12 lớp so với K23</span>
</div>
</div>
<div class="w-12 h-12 rounded-xl bg-surface-container-low text-primary flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined text-2xl">meeting_room</span>
</div>
</div>
<!-- Card 2 -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-xs flex items-center justify-between">
<div class="space-y-1">
<span class="font-label-md text-label-md text-on-surface-variant">Tổng sinh viên</span>
<div class="font-headline-lg text-headline-lg font-bold text-on-surface">14.250 <span class="font-body-sm text-body-sm font-normal text-on-surface-variant">sinh viên</span></div>
<div class="font-body-sm text-body-sm text-[#15803D] flex items-center gap-1">
<span class="material-symbols-outlined text-sm">check_circle</span>
<span>100% Đã kích hoạt hồ sơ</span>
</div>
</div>
<div class="w-12 h-12 rounded-xl bg-[#DCFCE7] text-[#15803D] flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined text-2xl">groups</span>
</div>
</div>
<!-- Card 3 -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-xs flex items-center justify-between">
<div class="space-y-1">
<span class="font-label-md text-label-md text-on-surface-variant">Sĩ số trung bình</span>
<div class="font-headline-lg text-headline-lg font-bold text-on-surface">57.4 <span class="font-body-sm text-body-sm font-normal text-on-surface-variant">SV / lớp</span></div>
<div class="font-body-sm text-body-sm text-on-surface-variant">
<span>Định mức chuẩn: 60 SV</span>
</div>
</div>
<div class="w-12 h-12 rounded-xl bg-surface-container-low text-primary flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined text-2xl">analytics</span>
</div>
</div>
<!-- Card 4 -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-xs flex items-center justify-between">
<div class="space-y-1">
<span class="font-label-md text-label-md text-on-surface-variant">Tỷ lệ có CVHT</span>
<div class="font-headline-lg text-headline-lg font-bold text-[#15803D]">100%</div>
<div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-[#DCFCE7] text-[#15803D] font-label-sm text-label-sm">
<span class="material-symbols-outlined text-xs">verified</span>
<span>Đã phân công đầy đủ</span>
</div>
</div>
<div class="w-12 h-12 rounded-xl bg-secondary-fixed text-primary flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined text-2xl">supervisor_account</span>
</div>
</div>
</div>
<!-- ================= Card Wrapper: Filters + Data Table ================= -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-xs overflow-hidden">
<!-- Filter Toolbar -->
<div class="p-4 border-b border-outline-variant bg-surface-bright flex flex-col xl:flex-row gap-4 items-start xl:items-center justify-between">
<!-- Filters Group -->
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 w-full xl:w-auto">
<!-- Search Input -->
<div class="relative min-w-[240px]">
<span class="material-symbols-outlined absolute left-3 top-2.5 text-lg text-outline">search</span>
<input aria-label="Tìm mã lớp, tên lớp, CVHT..." class="w-full h-10 pl-9 pr-3 text-body-sm font-body-sm bg-surface-container-lowest border border-outline-variant rounded-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 text-on-surface" placeholder="Tìm mã lớp, tên lớp, CVHT..." type="text"/>
</div>
<!-- Dropdown Khoa -->
<div>
<select aria-label="Khoa Công nghệ thông tin" class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg text-body-sm font-body-sm text-on-surface focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
<option selected="">Khoa Công nghệ thông tin</option>
<option>Khoa Thực phẩm</option>
<option>Khoa Kinh tế</option>
<option>Khoa Hóa học</option>
</select>
</div>
<!-- Dropdown Khóa -->
<div>
<select aria-label="Khóa 21 (2021 - 2025)" class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg text-body-sm font-body-sm text-on-surface focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
<option selected="">Khóa 21 (2021 - 2025)</option>
<option>Khóa 22 (2022 - 2026)</option>
<option>Khóa 23 (2023 - 2027)</option>
<option>Khóa 24 (2024 - 2028)</option>
</select>
</div>
<!-- Dropdown Ngành -->
<div>
<select aria-label="Tất cả ngành" class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg text-body-sm font-body-sm text-on-surface focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
<option selected="">Tất cả ngành</option>
<option>Công nghệ thông tin</option>
<option>Kỹ thuật phần mềm</option>
<option>An toàn thông tin</option>
</select>
</div>
<!-- Dropdown Trạng thái -->
<div>
<select aria-label="Đang hoạt động" class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg text-body-sm font-body-sm text-on-surface focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
<option selected="">Đang hoạt động</option>
<option>Tất cả trạng thái</option>
<option>Đã tốt nghiệp</option>
<option>Tạm khóa</option>
</select>
</div>
</div>
<!-- Action Buttons Group -->
<div class="flex items-center gap-2 self-end xl:self-auto flex-shrink-0">
<button class="h-10 px-3 bg-surface-container-lowest border border-outline-variant hover:bg-surface-container-low text-on-surface font-label-md text-label-md rounded-lg flex items-center gap-1.5 transition-colors">
<span class="material-symbols-outlined text-base">upload_file</span>
<span>Nhập từ Excel</span>
</button>
<button class="h-10 px-3 bg-surface-container-lowest border border-outline-variant hover:bg-surface-container-low text-on-surface font-label-md text-label-md rounded-lg flex items-center gap-1.5 transition-colors">
<span class="material-symbols-outlined text-base">file_download</span>
<span>Xuất file</span>
</button>
</div>
</div>
<!-- Academic Table: Overflow & Sticky Column -->
<div class="overflow-x-auto custom-scrollbar">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container-low/70 border-b border-outline-variant text-on-surface-variant font-label-md text-label-md uppercase tracking-wider">
<th class="py-3.5 px-4 sticky left-0 bg-surface-container-low z-20 shadow-[1px_0_0_0_#c3c6d5] w-36">
                  Mã lớp
                </th>
<th class="py-3.5 px-4">Tên lớp</th>
<th class="py-3.5 px-4">Ngành đào tạo</th>
<th class="py-3.5 px-4">Khóa</th>
<th class="py-3.5 px-4 min-w-[200px]">Cố vấn học tập (CVHT)</th>
<th class="py-3.5 px-4 text-center">Sĩ số thực tế / Tối đa</th>
<th class="py-3.5 px-4">Trạng thái</th>
<th class="py-3.5 px-4 text-right">Thao tác</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant font-body-sm text-body-sm">
<!-- Dòng 1 -->
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-3.5 px-4 font-label-md text-label-md text-primary font-bold sticky left-0 bg-surface-container-lowest z-10 shadow-[1px_0_0_0_#c3c6d5]">
                  12DHTH01
                </td>
<td class="py-3.5 px-4 font-medium text-on-surface">ĐH Tin học 01 - K21</td>
<td class="py-3.5 px-4 text-on-surface-variant">Công nghệ thông tin</td>
<td class="py-3.5 px-4">
<span class="px-2 py-0.5 rounded bg-surface-container text-on-surface-variant font-label-sm text-label-sm">Khóa 21</span>
</td>
<td class="py-3.5 px-4">
<div class="font-medium text-on-surface">ThS. Lê Hoàng Tuấn</div>
<div class="text-[11px] text-on-surface-variant">SĐT: 0908 123 456</div>
</td>
<td class="py-3.5 px-4 text-center">
<div class="font-semibold text-on-surface">60 / 60</div>
<span class="font-label-sm text-label-sm text-error">Đủ sĩ số</span>
</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#DCFCE7] text-[#15803D] font-label-sm text-label-sm font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-[#15803D]"></span>
                    Đang hoạt động
                  </span>
</td>
<td class="py-3.5 px-4 text-right">
<div class="inline-flex items-center gap-1.5">
<button class="px-2.5 py-1 rounded-md bg-surface-container-low text-primary hover:bg-surface-container border border-outline-variant font-label-sm text-label-sm font-medium transition-colors">
                      Danh sách SV
                    </button>
<button class="px-2.5 py-1 rounded-md bg-surface-container-lowest text-on-surface hover:bg-surface-container-low border border-outline-variant font-label-sm text-label-sm font-medium transition-colors" onclick="document.getElementById('modal-add-class').classList.remove('hidden')">
                      Sửa
                    </button>
</div>
</td>
</tr>
<!-- Dòng 2 -->
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-3.5 px-4 font-label-md text-label-md text-primary font-bold sticky left-0 bg-surface-container-lowest z-10 shadow-[1px_0_0_0_#c3c6d5]">
                  12DHTH02
                </td>
<td class="py-3.5 px-4 font-medium text-on-surface">ĐH Tin học 02 - K21</td>
<td class="py-3.5 px-4 text-on-surface-variant">Công nghệ thông tin</td>
<td class="py-3.5 px-4">
<span class="px-2 py-0.5 rounded bg-surface-container text-on-surface-variant font-label-sm text-label-sm">Khóa 21</span>
</td>
<td class="py-3.5 px-4">
<div class="font-medium text-on-surface">TS. Nguyễn Văn Dũng</div>
<div class="text-[11px] text-on-surface-variant">SĐT: 0913 888 999</div>
</td>
<td class="py-3.5 px-4 text-center">
<div class="font-semibold text-on-surface">58 / 60</div>
<span class="font-label-sm text-label-sm text-[#15803D]">Còn 2 chỗ</span>
</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#DCFCE7] text-[#15803D] font-label-sm text-label-sm font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-[#15803D]"></span>
                    Đang hoạt động
                  </span>
</td>
<td class="py-3.5 px-4 text-right">
<div class="inline-flex items-center gap-1.5">
<button class="px-2.5 py-1 rounded-md bg-surface-container-low text-primary hover:bg-surface-container border border-outline-variant font-label-sm text-label-sm font-medium transition-colors">
                      Danh sách SV
                    </button>
<button class="px-2.5 py-1 rounded-md bg-surface-container-lowest text-on-surface hover:bg-surface-container-low border border-outline-variant font-label-sm text-label-sm font-medium transition-colors" onclick="document.getElementById('modal-add-class').classList.remove('hidden')">
                      Sửa
                    </button>
</div>
</td>
</tr>
<!-- Dòng 3 -->
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-3.5 px-4 font-label-md text-label-md text-primary font-bold sticky left-0 bg-surface-container-lowest z-10 shadow-[1px_0_0_0_#c3c6d5]">
                  12DHPM01
                </td>
<td class="py-3.5 px-4 font-medium text-on-surface">ĐH Kỹ thuật phần mềm 01</td>
<td class="py-3.5 px-4 text-on-surface-variant">Kỹ thuật phần mềm</td>
<td class="py-3.5 px-4">
<span class="px-2 py-0.5 rounded bg-surface-container text-on-surface-variant font-label-sm text-label-sm">Khóa 21</span>
</td>
<td class="py-3.5 px-4">
<div class="font-medium text-on-surface">TS. Trần Minh Tâm</div>
<div class="text-[11px] text-on-surface-variant">SĐT: 0903 555 777</div>
</td>
<td class="py-3.5 px-4 text-center">
<div class="font-semibold text-on-surface">55 / 60</div>
<span class="font-label-sm text-label-sm text-on-surface-variant">Còn 5 chỗ</span>
</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#DCFCE7] text-[#15803D] font-label-sm text-label-sm font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-[#15803D]"></span>
                    Đang hoạt động
                  </span>
</td>
<td class="py-3.5 px-4 text-right">
<div class="inline-flex items-center gap-1.5">
<button class="px-2.5 py-1 rounded-md bg-surface-container-low text-primary hover:bg-surface-container border border-outline-variant font-label-sm text-label-sm font-medium transition-colors">
                      Danh sách SV
                    </button>
<button class="px-2.5 py-1 rounded-md bg-surface-container-lowest text-on-surface hover:bg-surface-container-low border border-outline-variant font-label-sm text-label-sm font-medium transition-colors" onclick="document.getElementById('modal-add-class').classList.remove('hidden')">
                      Sửa
                    </button>
</div>
</td>
</tr>
<!-- Dòng 4 -->
<tr class="hover:bg-surface-container-low/50 transition-colors bg-surface-bright/40">
<td class="py-3.5 px-4 font-label-md text-label-md text-primary font-bold sticky left-0 bg-surface-container-lowest z-10 shadow-[1px_0_0_0_#c3c6d5]">
                  11DHTH01
                </td>
<td class="py-3.5 px-4 font-medium text-on-surface">ĐH Tin học 01 - K20</td>
<td class="py-3.5 px-4 text-on-surface-variant">Công nghệ thông tin</td>
<td class="py-3.5 px-4">
<span class="px-2 py-0.5 rounded bg-surface-container text-on-surface-variant font-label-sm text-label-sm">Khóa 20</span>
</td>
<td class="py-3.5 px-4">
<div class="font-medium text-on-surface">PGS.TS Phạm Văn An</div>
<div class="text-[11px] text-on-surface-variant">SĐT: 0918 333 222</div>
</td>
<td class="py-3.5 px-4 text-center">
<div class="font-semibold text-on-surface">52 / 55</div>
<span class="font-label-sm text-label-sm text-on-surface-variant">Tốt nghiệp: 50 SV</span>
</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#FEF3C7] text-[#B45309] font-label-sm text-label-sm font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-[#B45309]"></span>
                    Đã tốt nghiệp
                  </span>
</td>
<td class="py-3.5 px-4 text-right">
<div class="inline-flex items-center gap-1.5">
<button class="px-2.5 py-1 rounded-md bg-surface-container-low text-primary hover:bg-surface-container border border-outline-variant font-label-sm text-label-sm font-medium transition-colors">
                      Danh sách SV
                    </button>
<button class="px-2.5 py-1 rounded-md bg-surface-container-lowest text-on-surface hover:bg-surface-container-low border border-outline-variant font-label-sm text-label-sm font-medium transition-colors" onclick="document.getElementById('modal-add-class').classList.remove('hidden')">
                      Sửa
                    </button>
</div>
</td>
</tr>
<!-- Dòng 5 -->
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-3.5 px-4 font-label-md text-label-md text-primary font-bold sticky left-0 bg-surface-container-lowest z-10 shadow-[1px_0_0_0_#c3c6d5]">
                  13DHAT01
                </td>
<td class="py-3.5 px-4 font-medium text-on-surface">ĐH An toàn thông tin 01</td>
<td class="py-3.5 px-4 text-on-surface-variant">An toàn thông tin</td>
<td class="py-3.5 px-4">
<span class="px-2 py-0.5 rounded bg-surface-container text-on-surface-variant font-label-sm text-label-sm">Khóa 22</span>
</td>
<td class="py-3.5 px-4">
<div class="font-medium text-on-surface">ThS. Vũ Đức Trí</div>
<div class="text-[11px] text-on-surface-variant">SĐT: 0977 444 111</div>
</td>
<td class="py-3.5 px-4 text-center">
<div class="font-semibold text-on-surface">60 / 60</div>
<span class="font-label-sm text-label-sm text-error">Đủ sĩ số</span>
</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#DCFCE7] text-[#15803D] font-label-sm text-label-sm font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-[#15803D]"></span>
                    Đang hoạt động
                  </span>
</td>
<td class="py-3.5 px-4 text-right">
<div class="inline-flex items-center gap-1.5">
<button class="px-2.5 py-1 rounded-md bg-surface-container-low text-primary hover:bg-surface-container border border-outline-variant font-label-sm text-label-sm font-medium transition-colors">
                      Danh sách SV
                    </button>
<button class="px-2.5 py-1 rounded-md bg-surface-container-lowest text-on-surface hover:bg-surface-container-low border border-outline-variant font-label-sm text-label-sm font-medium transition-colors" onclick="document.getElementById('modal-add-class').classList.remove('hidden')">
                      Sửa
                    </button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<!-- Pagination Footer -->
<div class="p-4 border-t border-outline-variant flex flex-col sm:flex-row items-center justify-between gap-3 text-body-sm text-on-surface-variant">
<div class="font-body-sm text-body-sm">
            Hiển thị <span class="font-semibold text-on-surface">1 - 5</span> trong tổng số <span class="font-semibold text-on-surface">248</span> lớp
          </div>
<div class="flex items-center gap-1">
<button class="p-1.5 rounded-md border border-outline-variant text-outline cursor-not-allowed" disabled="">
<span class="material-symbols-outlined text-base">chevron_left</span>
</button>
<button class="w-8 h-8 rounded-md bg-primary text-white font-semibold text-label-md">1</button>
<button class="w-8 h-8 rounded-md hover:bg-surface-container text-on-surface text-label-md">2</button>
<button class="w-8 h-8 rounded-md hover:bg-surface-container text-on-surface text-label-md">3</button>
<span class="px-1 text-outline">...</span>
<button class="w-8 h-8 rounded-md hover:bg-surface-container text-on-surface text-label-md">50</button>
<button class="p-1.5 rounded-md border border-outline-variant hover:bg-surface-container text-on-surface">
<span class="material-symbols-outlined text-base">chevron_right</span>
</button>
</div>
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
