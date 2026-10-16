@extends('fe_preview.layouts.admin')
@section('page_title', 'Quản lý Chương trình Đào tạo - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="light"@endsection
@section('body_attributes')class="bg-[#f8f9ff] text-slate-800 antialiased min-h-screen flex selection:bg-blue-100 selection:text-blue-900"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Quản lý Chương trình Đào tạo - HUIT Học Vụ</title>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/chuong-trinh-dao-tao']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">schema</span>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Quản lý Chương trình Đào tạo</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Quản lý Chương trình Đào tạo</div>
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
<!-- Title & System Code Header -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2">
<div>
<div class="flex items-center gap-2.5">
<h2 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">Quản lý Chương trình Đào tạo</h2>
<span class="px-2.5 py-0.5 rounded-full text-label-sm font-semibold bg-primary-fixed text-on-primary-fixed border border-primary/20">Mã phân hệ: C2 - CTDT-01</span>
</div>
<p class="font-body-md text-body-md text-on-surface-variant mt-1">
          Phòng Quản lý Đào tạo HUIT - Thiết kế, chuẩn hóa và quản lý khung chương trình đào tạo theo từng khóa tuyển sinh.
        </p>
</div>
<div class="flex items-center gap-3">
<button class="inline-flex items-center gap-2 px-4 py-2 border border-outline-variant bg-surface-container-lowest text-on-surface hover:bg-surface-container-low rounded-lg font-label-lg text-label-lg transition-colors shadow-xs" id="btnExportExcel">
<span class="material-symbols-outlined text-[18px]">download</span>
          Xuất file Excel
        </button>
<button class="inline-flex items-center gap-2 px-4 py-2 bg-primary hover:bg-[#16449e] text-white rounded-lg font-label-lg text-label-lg shadow-sm transition-all active:scale-95" id="btnOpenModal">
<span class="material-symbols-outlined text-[18px]">add_circle</span>
          + Tạo CTĐT mới
        </button>
</div>
</div>
<!-- Quick Statistics Bento Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
<!-- Stat 1 -->
<div class="bg-surface-container-lowest p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
<div>
<p class="font-label-sm text-label-sm text-outline uppercase tracking-wider">Tổng số CTĐT</p>
<div class="flex items-baseline gap-2 mt-1">
<span class="font-headline-xl text-headline-xl text-primary font-bold">42</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">chương trình</span>
</div>
<p class="font-label-sm text-label-sm text-outline mt-1">Toàn bộ bậc Đại học chính quy</p>
</div>
<div class="w-12 h-12 rounded-xl bg-blue-50 text-primary flex items-center justify-center">
<span class="material-symbols-outlined text-[28px]">schema</span>
</div>
</div>
<!-- Stat 2 -->
<div class="bg-surface-container-lowest p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
<div>
<p class="font-label-sm text-label-sm text-outline uppercase tracking-wider">Đang áp dụng</p>
<div class="flex items-baseline gap-2 mt-1">
<span class="font-headline-xl text-headline-xl text-emerald-700 font-bold">36</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">chương trình</span>
</div>
<p class="font-label-sm text-label-sm text-emerald-700 font-medium mt-1">Đã ban hành &amp; áp dụng</p>
</div>
<div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
<span class="material-symbols-outlined text-[28px]">verified</span>
</div>
</div>
<!-- Stat 3 -->
<div class="bg-surface-container-lowest p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
<div>
<p class="font-label-sm text-label-sm text-outline uppercase tracking-wider">Bản nháp</p>
<div class="flex items-baseline gap-2 mt-1">
<span class="font-headline-xl text-headline-xl text-amber-700 font-bold">4</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">chương trình</span>
</div>
<p class="font-label-sm text-label-sm text-amber-700 font-medium mt-1">Đang cập nhật &amp; thẩm định</p>
</div>
<div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
<span class="material-symbols-outlined text-[28px]">edit_document</span>
</div>
</div>
<!-- Stat 4 -->
<div class="bg-surface-container-lowest p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
<div>
<p class="font-label-sm text-label-sm text-outline uppercase tracking-wider">Ngừng áp dụng</p>
<div class="flex items-baseline gap-2 mt-1">
<span class="font-headline-xl text-headline-xl text-slate-500 font-bold">2</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">chương trình</span>
</div>
<p class="font-label-sm text-label-sm text-outline mt-1">Lưu trữ lịch sử đào tạo</p>
</div>
<div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center">
<span class="material-symbols-outlined text-[28px]">archive</span>
</div>
</div>
</div>
<!-- Filter & Search Toolbar Panel -->
<div class="bg-surface-container-lowest p-4 rounded-xl border border-slate-200 shadow-sm space-y-3">
<div class="grid grid-cols-1 md:grid-cols-12 gap-3">
<!-- Input Search -->
<div class="md:col-span-5 relative">
<span class="material-symbols-outlined absolute left-3 top-2.5 text-[20px] text-outline">search</span>
<input aria-label="Tìm theo tên CTĐT, mã CTĐT, ngành đào tạo..." class="w-full h-10 pl-10 pr-4 bg-white border border-outline-variant rounded-lg text-body-md text-on-surface placeholder:text-outline focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary" placeholder="Tìm theo tên CTĐT, mã CTĐT, ngành đào tạo..." type="text"/>
</div>
<!-- Filter Ngành -->
<div class="md:col-span-3">
<select aria-label="Tất cả ngành đào tạo" class="w-full h-10 px-3 bg-white border border-outline-variant rounded-lg text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
<option value="">Tất cả ngành đào tạo</option>
<option value="cntt">Công nghệ Thông tin</option>
<option value="ktpm">Kỹ thuật Phần mềm</option>
<option value="khdl">Khoa học Dữ liệu</option>
<option value="cntp">Công nghệ Thực phẩm</option>
<option value="kt">Kế toán</option>
<option value="qtkd">Quản trị Kinh doanh</option>
</select>
</div>
<!-- Filter Khóa -->
<div class="md:col-span-2">
<select aria-label="Tất cả khóa" class="w-full h-10 px-3 bg-white border border-outline-variant rounded-lg text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
<option value="">Tất cả khóa</option>
<option value="k24">Khóa 2024 (K24)</option>
<option value="k23">Khóa 2023 (K23)</option>
<option value="k22">Khóa 2022 (K22)</option>
<option value="k21">Khóa 2021 (K21)</option>
</select>
</div>
<!-- Filter Trạng thái -->
<div class="md:col-span-2">
<select aria-label="Tất cả trạng thái" class="w-full h-10 px-3 bg-white border border-outline-variant rounded-lg text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
<option value="">Tất cả trạng thái</option>
<option value="published">Đã ban hành</option>
<option value="draft">Bản nháp</option>
<option value="archived">Ngừng áp dụng</option>
</select>
</div>
</div>
<!-- Quick Action Badges & Reset Filter -->
<div class="flex flex-wrap items-center justify-between text-body-sm pt-2 border-t border-slate-100">
<div class="flex items-center gap-2 text-on-surface-variant">
<span class="font-label-sm text-label-sm text-outline">Đang lọc theo:</span>
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs bg-slate-100 text-slate-700 border border-slate-200">
            Khóa: 2024 (K24)
            <button class="hover:text-red-500 font-bold ml-1">×</button>
</span>
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs bg-slate-100 text-slate-700 border border-slate-200">
            Hệ: Đại học chính quy
            <button class="hover:text-red-500 font-bold ml-1">×</button>
</span>
<button class="text-primary hover:underline font-label-sm text-label-sm ml-2">Xóa tất cả lọc</button>
</div>
<div class="text-on-surface-variant font-body-sm text-body-sm">
          Đang hiển thị <span class="font-semibold text-on-surface">6</span> kết quả phù hợp
        </div>
</div>
</div>
<!-- UI State Switcher Test Toolbar -->
<div class="flex items-center justify-between bg-surface-container-low px-4 py-2.5 rounded-xl border border-outline-variant/60">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[18px] text-primary">science</span>
<span class="font-label-md text-label-md text-primary font-semibold">Chế độ kiểm thử giao diện:</span>
</div>
<div class="inline-flex p-1 bg-white rounded-lg border border-slate-200 gap-1" id="stateSwitcher">
<button class="px-3 py-1 text-xs font-semibold rounded-md transition-colors bg-primary text-white shadow-xs" id="btnStateNormal" onclick="switchTableState('normal')">
          Bình thường (6)
        </button>
<button class="px-3 py-1 text-xs font-medium rounded-md transition-colors text-slate-600 hover:bg-slate-100" id="btnStateSkeleton" onclick="switchTableState('skeleton')">
          Đang tải (Skeleton)
        </button>
<button class="px-3 py-1 text-xs font-medium rounded-md transition-colors text-slate-600 hover:bg-slate-100" id="btnStateEmpty" onclick="switchTableState('empty')">
          Danh sách rỗng
        </button>
<button class="px-3 py-1 text-xs font-medium rounded-md transition-colors text-slate-600 hover:bg-slate-100" id="btnStateError" onclick="switchTableState('error')">
          Lỗi kết nối
        </button>
</div>
</div>
<!-- ==================== ACADEMIC DATA TABLE CONTAINER ==================== -->
<div class="bg-surface-container-lowest rounded-xl border border-slate-200 shadow-sm overflow-hidden">
<div class="overflow-x-auto w-full">
<table class="w-full text-left border-collapse" id="mainDataTable">
<thead>
<tr class="bg-slate-50 border-b border-slate-200 text-outline font-label-md text-label-md uppercase tracking-wider">
<th class="py-3.5 px-4 sticky-col-first bg-slate-50 min-w-[280px] shadow-[2px_0_5px_-2px_rgba(0,0,0,0.06)]" scope="col">
                Mã &amp; Tên Chương trình Đào tạo
              </th>
<th class="py-3.5 px-4 min-w-[180px]" scope="col">Ngành đào tạo</th>
<th class="py-3.5 px-4 min-w-[190px]" scope="col">Khoa phụ trách</th>
<th class="py-3.5 px-4 min-w-[120px]" scope="col">Khóa áp dụng</th>
<th class="py-3.5 px-4 min-w-[130px] text-center" scope="col">Tổng tín chỉ</th>
<th class="py-3.5 px-4 min-w-[140px] text-center" scope="col">Trạng thái</th>
<th class="py-3.5 px-4 min-w-[130px]" scope="col">Ngày cập nhật</th>
<th class="py-3.5 px-4 min-w-[130px] text-right" scope="col">Thao tác</th>
</tr>
</thead>
<!-- NORMAL STATE BODY -->
<tbody class="divide-y divide-slate-100 font-body-md text-body-md" id="stateNormalBody">
<!-- Row 1 -->
<tr class="hover:bg-slate-50/80 transition-colors group">
<td class="py-3.5 px-4 sticky-col-first bg-white group-hover:bg-slate-50/80 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.06)]">
<div class="flex items-center gap-2.5">
<span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span>
<div>
<a class="font-semibold text-primary hover:underline line-clamp-1" href="#">7480201 - Công nghệ Thông tin</a>
<span class="font-label-sm text-label-sm text-outline">Chính quy 4.0 năm • Định hướng ứng dụng</span>
</div>
</div>
</td>
<td class="py-3.5 px-4 font-medium text-on-surface">Công nghệ Thông tin</td>
<td class="py-3.5 px-4 text-on-surface-variant">Khoa Công nghệ Thông tin</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center px-2 py-0.5 rounded text-label-sm font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                  Khóa 2024 (K24)
                </span>
</td>
<td class="py-3.5 px-4 text-center font-semibold text-on-surface">
                135 Tín chỉ
              </td>
<td class="py-3.5 px-4 text-center">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-label-sm font-semibold bg-emerald-100 text-emerald-800">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                  Đã ban hành
                </span>
</td>
<td class="py-3.5 px-4 text-outline font-body-sm text-body-sm">12/10/2026</td>
<td class="py-3.5 px-4 text-right">
<div class="inline-flex items-center gap-1">
<button class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-blue-50 rounded-lg transition-colors" title="Xem chi tiết khung CTĐT">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
<button class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-blue-50 rounded-lg transition-colors" title="Nhân bản CTĐT">
<span class="material-symbols-outlined text-[18px]">content_copy</span>
</button>
<button class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-blue-50 rounded-lg transition-colors" title="Chỉnh sửa thông tin">
<span class="material-symbols-outlined text-[18px]">edit</span>
</button>
</div>
</td>
</tr>
<!-- Row 2 -->
<tr class="hover:bg-slate-50/80 transition-colors group">
<td class="py-3.5 px-4 sticky-col-first bg-white group-hover:bg-slate-50/80 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.06)]">
<div class="flex items-center gap-2.5">
<span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span>
<div>
<a class="font-semibold text-primary hover:underline line-clamp-1" href="#">7480103 - Kỹ thuật Phần mềm</a>
<span class="font-label-sm text-label-sm text-outline">Chính quy 4.0 năm • Kỹ sư phần mềm</span>
</div>
</div>
</td>
<td class="py-3.5 px-4 font-medium text-on-surface">Kỹ thuật Phần mềm</td>
<td class="py-3.5 px-4 text-on-surface-variant">Khoa Công nghệ Thông tin</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center px-2 py-0.5 rounded text-label-sm font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                  Khóa 2024 (K24)
                </span>
</td>
<td class="py-3.5 px-4 text-center font-semibold text-on-surface">
                140 Tín chỉ
              </td>
<td class="py-3.5 px-4 text-center">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-label-sm font-semibold bg-emerald-100 text-emerald-800">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                  Đã ban hành
                </span>
</td>
<td class="py-3.5 px-4 text-outline font-body-sm text-body-sm">05/10/2026</td>
<td class="py-3.5 px-4 text-right">
<div class="inline-flex items-center gap-1">
<button class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-blue-50 rounded-lg transition-colors" title="Xem chi tiết khung CTĐT">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
<button class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-blue-50 rounded-lg transition-colors" title="Nhân bản CTĐT">
<span class="material-symbols-outlined text-[18px]">content_copy</span>
</button>
<button class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-blue-50 rounded-lg transition-colors" title="Chỉnh sửa thông tin">
<span class="material-symbols-outlined text-[18px]">edit</span>
</button>
</div>
</td>
</tr>
<!-- Row 3 (Draft) -->
<tr class="hover:bg-slate-50/80 transition-colors group">
<td class="py-3.5 px-4 sticky-col-first bg-white group-hover:bg-slate-50/80 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.06)]">
<div class="flex items-center gap-2.5">
<span class="w-2 h-2 rounded-full bg-amber-500 flex-shrink-0"></span>
<div>
<a class="font-semibold text-primary hover:underline line-clamp-1" href="#">7480108 - Khoa học Dữ liệu</a>
<span class="font-label-sm text-label-sm text-amber-700 font-medium">Bổ sung khối học phần Chuyên sâu AI</span>
</div>
</div>
</td>
<td class="py-3.5 px-4 font-medium text-on-surface">Khoa học Dữ liệu</td>
<td class="py-3.5 px-4 text-on-surface-variant">Khoa Công nghệ Thông tin</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center px-2 py-0.5 rounded text-label-sm font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                  Khóa 2024 (K24)
                </span>
</td>
<td class="py-3.5 px-4 text-center font-semibold text-on-surface">
                138 Tín chỉ
              </td>
<td class="py-3.5 px-4 text-center">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-label-sm font-semibold bg-amber-100 text-amber-900">
<span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                  Bản nháp
                </span>
</td>
<td class="py-3.5 px-4 text-outline font-body-sm text-body-sm">18/10/2026</td>
<td class="py-3.5 px-4 text-right">
<div class="inline-flex items-center gap-1">
<button class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-blue-50 rounded-lg transition-colors" title="Xem chi tiết khung CTĐT">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
<button class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-blue-50 rounded-lg transition-colors" title="Nhân bản CTĐT">
<span class="material-symbols-outlined text-[18px]">content_copy</span>
</button>
<button class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-blue-50 rounded-lg transition-colors" title="Chỉnh sửa thông tin">
<span class="material-symbols-outlined text-[18px]">edit</span>
</button>
</div>
</td>
</tr>
<!-- Row 4 -->
<tr class="hover:bg-slate-50/80 transition-colors group">
<td class="py-3.5 px-4 sticky-col-first bg-white group-hover:bg-slate-50/80 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.06)]">
<div class="flex items-center gap-2.5">
<span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span>
<div>
<a class="font-semibold text-primary hover:underline line-clamp-1" href="#">7540101 - Công nghệ Thực phẩm</a>
<span class="font-label-sm text-label-sm text-outline">Chương trình chuẩn kiểm định AUN-QA</span>
</div>
</div>
</td>
<td class="py-3.5 px-4 font-medium text-on-surface">Công nghệ Thực phẩm</td>
<td class="py-3.5 px-4 text-on-surface-variant">Khoa Công nghệ Thực phẩm</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center px-2 py-0.5 rounded text-label-sm font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                  Khóa 2023 (K23)
                </span>
</td>
<td class="py-3.5 px-4 text-center font-semibold text-on-surface">
                145 Tín chỉ
              </td>
<td class="py-3.5 px-4 text-center">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-label-sm font-semibold bg-emerald-100 text-emerald-800">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                  Đã ban hành
                </span>
</td>
<td class="py-3.5 px-4 text-outline font-body-sm text-body-sm">28/08/2026</td>
<td class="py-3.5 px-4 text-right">
<div class="inline-flex items-center gap-1">
<button class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-blue-50 rounded-lg transition-colors" title="Xem chi tiết khung CTĐT">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
<button class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-blue-50 rounded-lg transition-colors" title="Nhân bản CTĐT">
<span class="material-symbols-outlined text-[18px]">content_copy</span>
</button>
<button class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-blue-50 rounded-lg transition-colors" title="Chỉnh sửa thông tin">
<span class="material-symbols-outlined text-[18px]">edit</span>
</button>
</div>
</td>
</tr>
<!-- Row 5 -->
<tr class="hover:bg-slate-50/80 transition-colors group">
<td class="py-3.5 px-4 sticky-col-first bg-white group-hover:bg-slate-50/80 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.06)]">
<div class="flex items-center gap-2.5">
<span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span>
<div>
<a class="font-semibold text-primary hover:underline line-clamp-1" href="#">7340301 - Kế toán</a>
<span class="font-label-sm text-label-sm text-outline">Kế toán doanh nghiệp &amp; Kiểm toán số</span>
</div>
</div>
</td>
<td class="py-3.5 px-4 font-medium text-on-surface">Kế toán</td>
<td class="py-3.5 px-4 text-on-surface-variant">Khoa Tài chính - Kế toán</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center px-2 py-0.5 rounded text-label-sm font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                  Khóa 2024 (K24)
                </span>
</td>
<td class="py-3.5 px-4 text-center font-semibold text-on-surface">
                130 Tín chỉ
              </td>
<td class="py-3.5 px-4 text-center">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-label-sm font-semibold bg-emerald-100 text-emerald-800">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                  Đã ban hành
                </span>
</td>
<td class="py-3.5 px-4 text-outline font-body-sm text-body-sm">15/09/2026</td>
<td class="py-3.5 px-4 text-right">
<div class="inline-flex items-center gap-1">
<button class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-blue-50 rounded-lg transition-colors" title="Xem chi tiết khung CTĐT">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
<button class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-blue-50 rounded-lg transition-colors" title="Nhân bản CTĐT">
<span class="material-symbols-outlined text-[18px]">content_copy</span>
</button>
<button class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-blue-50 rounded-lg transition-colors" title="Chỉnh sửa thông tin">
<span class="material-symbols-outlined text-[18px]">edit</span>
</button>
</div>
</td>
</tr>
<!-- Row 6 (Archived) -->
<tr class="hover:bg-slate-50/80 transition-colors group opacity-85">
<td class="py-3.5 px-4 sticky-col-first bg-white group-hover:bg-slate-50/80 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.06)]">
<div class="flex items-center gap-2.5">
<span class="w-2 h-2 rounded-full bg-slate-400 flex-shrink-0"></span>
<div>
<a class="font-semibold text-slate-700 hover:underline line-clamp-1" href="#">7340101 - Quản trị Kinh doanh (Cũ)</a>
<span class="font-label-sm text-label-sm text-slate-500">Đã thay thế bằng phiên bản CTĐT 2023</span>
</div>
</div>
</td>
<td class="py-3.5 px-4 font-medium text-slate-700">Quản trị Kinh doanh</td>
<td class="py-3.5 px-4 text-outline">Khoa Quản trị Kinh doanh</td>
<td class="py-3.5 px-4">
<span class="inline-flex items-center px-2 py-0.5 rounded text-label-sm font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                  Khóa 2020 (K20)
                </span>
</td>
<td class="py-3.5 px-4 text-center font-semibold text-slate-700">
                135 Tín chỉ
              </td>
<td class="py-3.5 px-4 text-center">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-label-sm font-semibold bg-slate-200 text-slate-700">
<span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                  Ngừng áp dụng
                </span>
</td>
<td class="py-3.5 px-4 text-outline font-body-sm text-body-sm">10/01/2026</td>
<td class="py-3.5 px-4 text-right">
<div class="inline-flex items-center gap-1">
<button class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-blue-50 rounded-lg transition-colors" title="Xem chi tiết khung CTĐT">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
<button class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-blue-50 rounded-lg transition-colors" title="Khôi phục trạng thái">
<span class="material-symbols-outlined text-[18px]">restore</span>
</button>
<button class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-blue-50 rounded-lg transition-colors" title="Nhật ký phiên bản">
<span class="material-symbols-outlined text-[18px]">history</span>
</button>
</div>
</td>
</tr>
</tbody>
<!-- SKELETON STATE BODY (Initially Hidden) -->
<tbody class="hidden divide-y divide-slate-100" id="stateSkeletonBody">
<tr class="animate-pulse">
<td class="py-4 px-4 sticky-col-first bg-white"><div class="h-4 bg-slate-200 rounded w-48 mb-2"></div><div class="h-3 bg-slate-100 rounded w-32"></div></td>
<td class="py-4 px-4"><div class="h-4 bg-slate-200 rounded w-32"></div></td>
<td class="py-4 px-4"><div class="h-4 bg-slate-200 rounded w-40"></div></td>
<td class="py-4 px-4"><div class="h-5 bg-slate-200 rounded w-20"></div></td>
<td class="py-4 px-4 text-center"><div class="h-4 bg-slate-200 rounded w-16 mx-auto"></div></td>
<td class="py-4 px-4 text-center"><div class="h-6 bg-slate-200 rounded-full w-24 mx-auto"></div></td>
<td class="py-4 px-4"><div class="h-4 bg-slate-200 rounded w-20"></div></td>
<td class="py-4 px-4 text-right"><div class="h-6 bg-slate-200 rounded w-16 ml-auto"></div></td>
</tr>
<tr class="animate-pulse">
<td class="py-4 px-4 sticky-col-first bg-white"><div class="h-4 bg-slate-200 rounded w-52 mb-2"></div><div class="h-3 bg-slate-100 rounded w-36"></div></td>
<td class="py-4 px-4"><div class="h-4 bg-slate-200 rounded w-36"></div></td>
<td class="py-4 px-4"><div class="h-4 bg-slate-200 rounded w-36"></div></td>
<td class="py-4 px-4"><div class="h-5 bg-slate-200 rounded w-20"></div></td>
<td class="py-4 px-4 text-center"><div class="h-4 bg-slate-200 rounded w-16 mx-auto"></div></td>
<td class="py-4 px-4 text-center"><div class="h-6 bg-slate-200 rounded-full w-24 mx-auto"></div></td>
<td class="py-4 px-4"><div class="h-4 bg-slate-200 rounded w-20"></div></td>
<td class="py-4 px-4 text-right"><div class="h-6 bg-slate-200 rounded w-16 ml-auto"></div></td>
</tr>
<tr class="animate-pulse">
<td class="py-4 px-4 sticky-col-first bg-white"><div class="h-4 bg-slate-200 rounded w-44 mb-2"></div><div class="h-3 bg-slate-100 rounded w-28"></div></td>
<td class="py-4 px-4"><div class="h-4 bg-slate-200 rounded w-28"></div></td>
<td class="py-4 px-4"><div class="h-4 bg-slate-200 rounded w-40"></div></td>
<td class="py-4 px-4"><div class="h-5 bg-slate-200 rounded w-20"></div></td>
<td class="py-4 px-4 text-center"><div class="h-4 bg-slate-200 rounded w-16 mx-auto"></div></td>
<td class="py-4 px-4 text-center"><div class="h-6 bg-slate-200 rounded-full w-24 mx-auto"></div></td>
<td class="py-4 px-4"><div class="h-4 bg-slate-200 rounded w-20"></div></td>
<td class="py-4 px-4 text-right"><div class="h-6 bg-slate-200 rounded w-16 ml-auto"></div></td>
</tr>
</tbody>
<!-- EMPTY STATE BODY (Initially Hidden) -->
<tbody class="hidden" id="stateEmptyBody">
<tr>
<td class="py-16 text-center" colspan="8">
<div class="max-w-sm mx-auto flex flex-col items-center justify-center">
<div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center text-outline mb-3">
<span class="material-symbols-outlined text-[36px]">folder_off</span>
</div>
<h4 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Không tìm thấy Chương trình Đào tạo</h4>
<p class="font-body-md text-body-md text-on-surface-variant mt-1 text-center">
                    Không có chương trình đào tạo nào khớp với bộ lọc hiện tại. Vui lòng thử tìm kiếm với từ khóa hoặc tiêu chí khác.
                  </p>
<button class="mt-4 px-4 py-2 border border-outline-variant rounded-lg text-primary hover:bg-slate-50 font-label-md text-label-md" onclick="switchTableState('normal')">
                    Đặt lại bộ lọc
                  </button>
</div>
</td>
</tr>
</tbody>
<!-- ERROR STATE BODY (Initially Hidden) -->
<tbody class="hidden" id="stateErrorBody">
<tr>
<td class="py-16 text-center" colspan="8">
<div class="max-w-sm mx-auto flex flex-col items-center justify-center">
<div class="w-16 h-16 rounded-full bg-error-container/50 flex items-center justify-center text-error mb-3">
<span class="material-symbols-outlined text-[36px]">cloud_off</span>
</div>
<h4 class="font-headline-sm text-headline-sm text-error font-semibold">Lỗi đồng bộ dữ liệu Học vụ</h4>
<p class="font-body-md text-body-md text-on-surface-variant mt-1 text-center">
                    Không thể kết nối đến máy chủ Quản lý Đào tạo (HUIT Academic Core). Vui lòng kiểm tra lại kết nối mạng hoặc thử lại sau.
                  </p>
<button class="mt-4 px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 font-label-md text-label-md flex items-center gap-1.5" onclick="switchTableState('normal')">
<span class="material-symbols-outlined text-[16px]">refresh</span>
                    Thử tải lại dữ liệu
                  </button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<!-- Table Footer & Pagination -->
<div class="px-4 py-3 bg-surface-container-lowest border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-body-sm text-on-surface-variant">
<div class="flex items-center gap-3">
<span>Hiển thị <strong class="text-on-surface font-semibold">1 - 6</strong> trong tổng số <strong class="text-on-surface font-semibold">42</strong> chương trình đào tạo</span>
<div class="flex items-center gap-1">
<span class="text-outline">Số dòng:</span>
<select aria-label="6 dòng / trang" class="h-8 px-2 bg-white border border-outline-variant rounded text-xs text-on-surface focus:outline-none focus:border-primary">
<option value="6">6 dòng / trang</option>
<option value="10">10 dòng / trang</option>
<option value="20">20 dòng / trang</option>
<option value="50">50 dòng / trang</option>
</select>
</div>
</div>
<!-- Pagination Controls -->
<nav class="flex items-center gap-1">
<button class="w-8 h-8 flex items-center justify-center rounded-lg border border-outline-variant text-outline hover:bg-slate-50 disabled:opacity-40" disabled="">
<span class="material-symbols-outlined text-[16px]">chevron_left</span>
</button>
<button class="w-8 h-8 flex items-center justify-center rounded-lg bg-primary text-white font-semibold text-xs shadow-xs">1</button>
<button class="w-8 h-8 flex items-center justify-center rounded-lg border border-outline-variant hover:bg-slate-50 text-on-surface text-xs">2</button>
<button class="w-8 h-8 flex items-center justify-center rounded-lg border border-outline-variant hover:bg-slate-50 text-on-surface text-xs">3</button>
<span class="px-1 text-outline">...</span>
<button class="w-8 h-8 flex items-center justify-center rounded-lg border border-outline-variant hover:bg-slate-50 text-on-surface text-xs">7</button>
<button class="w-8 h-8 flex items-center justify-center rounded-lg border border-outline-variant text-on-surface hover:bg-slate-50">
<span class="material-symbols-outlined text-[16px]">chevron_right</span>
</button>
</nav>
</div>
</div>
<!-- Supplementary Note Card -->
<div class="bg-blue-50/60 border border-blue-200/80 rounded-xl p-4 flex items-start gap-3">
<span class="material-symbols-outlined text-primary text-[22px] mt-0.5">info</span>
<div class="text-body-sm text-on-surface-variant">
<strong class="text-on-surface font-semibold">Quy chuẩn học vụ HUIT:</strong> Mỗi chương trình đào tạo chính quy được xây dựng theo hệ thống tín chỉ, cấu trúc phân bổ gồm các khối kiến thức: 
        <em>Giáo dục đại cương, Giáo dục chuyên nghiệp (Cơ sở ngành, Chuyên ngành bắt buộc &amp; Học phần tự chọn), Khóa luận tốt nghiệp hoặc các học phần thay thế</em>. 
        Mọi sửa đổi khung CTĐT sau khi ban hành cần thông qua Hội đồng Khoa học &amp; Đào tạo nhà trường.
      </div>
</div>
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
