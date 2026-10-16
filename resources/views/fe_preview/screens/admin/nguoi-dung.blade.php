@extends('fe_preview.layouts.admin')
@section('page_title', 'Danh sách người dùng - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="light"@endsection
@section('body_attributes')class="bg-[#f8f9ff] text-slate-800 antialiased min-h-screen flex selection:bg-blue-100 selection:text-blue-900"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Danh sách người dùng - HUIT Học Vụ</title>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/nguoi-dung']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">manage_accounts</span>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Danh sách người dùng</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Danh sách người dùng</div>
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
<div class="max-w-[1440px] mx-auto space-y-6">
<!-- PAGE HEADER WITH ACTIONS -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
<div>
<h1 class="font-headline-md text-headline-md font-bold text-slate-900 tracking-tight">Danh sách người dùng</h1>
<p class="font-body-md text-body-md text-slate-500 mt-1">
                Quản lý tài khoản, vai trò và trạng thái truy cập của người dùng trong hệ thống học vụ HUIT.
              </p>
</div>
<div class="flex items-center gap-2.5 flex-wrap">
<!-- Tertiary: Export -->
<button class="h-10 px-3.5 bg-white border border-[#CBD5E1] hover:bg-slate-50 text-slate-700 font-label-lg text-label-lg rounded-lg flex items-center gap-2 transition-colors active:scale-95 shadow-sm">
<span class="material-symbols-outlined text-[19px]">download</span>
<span>Xuất dữ liệu</span>
</button>
<!-- Secondary: Import -->
<button class="h-10 px-3.5 bg-white border border-[#CBD5E1] hover:bg-slate-50 text-slate-700 font-label-lg text-label-lg rounded-lg flex items-center gap-2 transition-colors active:scale-95 shadow-sm">
<span class="material-symbols-outlined text-[19px]">upload_file</span>
<span>Nhập từ file (Excel/CSV)</span>
</button>
<!-- Primary: Create User -->
<button class="h-10 px-4 bg-primary hover:bg-[#1648a8] text-white font-label-lg text-label-lg rounded-lg flex items-center gap-2 transition-colors shadow-sm active:scale-95">
<span class="material-symbols-outlined text-[20px]">person_add</span>
<span>+ Thêm người dùng</span>
</button>
</div>
</div>
<!-- SUMMARY KPI / METRIC MINI-CARDS (4 Clean Cards) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
<!-- Stat Card 1: Tổng người dùng -->
<div class="bg-white rounded-xl border border-[#E2E8F0] p-5 elevation-level-1 flex items-start justify-between">
<div>
<p class="font-label-md text-label-md text-slate-500 uppercase tracking-wider font-semibold">Tổng người dùng</p>
<h3 class="font-headline-xl text-headline-xl font-bold text-slate-900 mt-1.5">19,842</h3>
<p class="font-body-sm text-body-sm text-slate-500 mt-1 flex items-center gap-1">
<span class="text-emerald-700 font-semibold flex items-center text-[11px]">
<span class="material-symbols-outlined text-[14px]">trending_up</span> +342
                  </span>
<span>từ học kỳ trước</span>
</p>
</div>
<div class="w-11 h-11 rounded-xl bg-blue-50 text-primary flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[24px]">group</span>
</div>
</div>
<!-- Stat Card 2: Đang hoạt động -->
<div class="bg-white rounded-xl border border-[#E2E8F0] p-5 elevation-level-1 flex items-start justify-between">
<div>
<p class="font-label-md text-label-md text-slate-500 uppercase tracking-wider font-semibold">Đang hoạt động</p>
<h3 class="font-headline-xl text-headline-xl font-bold text-slate-900 mt-1.5">19,120</h3>
<p class="font-body-sm text-body-sm text-emerald-700 font-semibold mt-1 flex items-center gap-1">
<span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
<span>96.4% trên toàn hệ thống</span>
</p>
</div>
<div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[24px]">check_circle</span>
</div>
</div>
<!-- Stat Card 3: Tài khoản bị khóa -->
<div class="bg-white rounded-xl border border-[#E2E8F0] p-5 elevation-level-1 flex items-start justify-between">
<div>
<p class="font-label-md text-label-md text-slate-500 uppercase tracking-wider font-semibold">Tài khoản bị khóa</p>
<h3 class="font-headline-xl text-headline-xl font-bold text-slate-900 mt-1.5">48</h3>
<p class="font-body-sm text-body-sm text-rose-700 font-semibold mt-1 flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]">error</span>
<span>Cần kiểm tra xác minh</span>
</p>
</div>
<div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-700 flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[24px]">lock_person</span>
</div>
</div>
<!-- Stat Card 4: Chờ kích hoạt -->
<div class="bg-white rounded-xl border border-[#E2E8F0] p-5 elevation-level-1 flex items-start justify-between">
<div>
<p class="font-label-md text-label-md text-slate-500 uppercase tracking-wider font-semibold">Chờ kích hoạt</p>
<h3 class="font-headline-xl text-headline-xl font-bold text-slate-900 mt-1.5">674</h3>
<p class="font-body-sm text-body-sm text-amber-700 font-semibold mt-1 flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]">schedule</span>
<span>Tân sinh viên &amp; GV mới</span>
</p>
</div>
<div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[24px]">hourglass_top</span>
</div>
</div>
</div>
<!-- FILTER & SEARCH CONTROL TOOLBAR -->
<div class="bg-white rounded-xl border border-[#E2E8F0] p-4 elevation-level-1 space-y-3">
<div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
<!-- Search Box -->
<div class="relative flex-1 min-w-[280px]">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-slate-500">search</span>
<input aria-label="Tìm kiếm theo họ tên, email (@example.test), mã số..." class="w-full h-10 pl-10 pr-9 bg-white border border-[#CBD5E1] rounded-lg text-body-md font-body-md text-slate-900 placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" placeholder="Tìm kiếm theo họ tên, email (@example.test), mã số..." type="text"/>
<button class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-600 p-0.5 rounded">
<span class="material-symbols-outlined text-[18px]">cancel</span>
</button>
</div>
<!-- Filter Dropdowns Cluster -->
<div class="flex flex-wrap items-center gap-2.5">
<!-- Vai trò -->
<div class="relative min-w-[170px]">
<select aria-label="Tất cả vai trò" class="w-full h-10 pl-3 pr-8 bg-white border border-[#CBD5E1] rounded-lg text-body-md font-body-md text-slate-700 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 appearance-none cursor-pointer">
<option value="">Tất cả vai trò</option>
<option value="admin">Quản trị hệ thống</option>
<option value="head">Trưởng phòng Đào tạo</option>
<option value="lecturer">Giảng viên</option>
<option value="student">Sinh viên</option>
<option value="specialist">Chuyên viên đào tạo</option>
</select>
<span class="material-symbols-outlined pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-[18px] text-slate-500">expand_more</span>
</div>
<!-- Đơn vị / Khoa -->
<div class="relative min-w-[190px]">
<select aria-label="Tất cả khoa / phòng ban" class="w-full h-10 pl-3 pr-8 bg-white border border-[#CBD5E1] rounded-lg text-body-md font-body-md text-slate-700 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 appearance-none cursor-pointer">
<option value="">Tất cả khoa / phòng ban</option>
<option value="cntt">Khoa CNTT</option>
<option value="ketoan">Khoa Kế toán</option>
<option value="tp">Khoa CN Thực phẩm</option>
<option value="pdt">Phòng Quản lý Đào tạo</option>
<option value="nn">Khoa Ngoại ngữ</option>
</select>
<span class="material-symbols-outlined pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-[18px] text-slate-500">expand_more</span>
</div>
<!-- Trạng thái -->
<div class="relative min-w-[160px]">
<select aria-label="Tất cả trạng thái" class="w-full h-10 pl-3 pr-8 bg-white border border-[#CBD5E1] rounded-lg text-body-md font-body-md text-slate-700 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 appearance-none cursor-pointer">
<option value="">Tất cả trạng thái</option>
<option value="active">Đang hoạt động</option>
<option value="locked">Bị khóa</option>
<option value="pending">Chờ kích hoạt</option>
</select>
<span class="material-symbols-outlined pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-[18px] text-slate-500">expand_more</span>
</div>
<!-- Refresh Button -->
<button class="h-10 w-10 flex items-center justify-center border border-[#CBD5E1] rounded-lg text-slate-600 hover:bg-slate-50 hover:text-primary transition-colors" title="Làm mới bộ lọc">
<span class="material-symbols-outlined text-[20px]">refresh</span>
</button>
</div>
</div>
<!-- Active Filter Indicators / Quick Actions Bar -->
<div class="pt-2 border-t border-[#F1F5F9] flex items-center justify-between flex-wrap gap-2 text-body-sm font-body-sm">
<div class="flex items-center gap-2 text-slate-500">
<span class="font-medium text-slate-700">Bộ lọc đang chọn:</span>
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-xs">
                  Toàn bộ tài khoản
                </span>
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-blue-50 text-primary text-xs font-medium">
                  Học kỳ 1 • 2026-2027
                  <button class="hover:text-red-500 ml-0.5">×</button>
</span>
</div>
<div class="text-slate-500 flex items-center gap-2 text-xs">
<span>Đã chọn <strong class="text-slate-800 font-semibold">0</strong> bản ghi</span>
<button class="text-slate-500 cursor-not-allowed hover:text-slate-600 font-medium" disabled="">Thao tác hàng loạt ▾</button>
</div>
</div>
</div>
<!-- ACADEMIC DATA TABLE CONTAINER -->
<div class="bg-white rounded-xl border border-[#E2E8F0] shadow-sm overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<!-- Table Header: 48px height, Slate-100 bg, uppercase, label-md -->
<thead>
<tr class="h-12 bg-[#F1F5F9] border-b border-[#E2E8F0] text-slate-700 font-label-md text-label-md uppercase tracking-wider">
<th class="w-12 px-4 text-center">
<input aria-label="Chọn dòng" class="w-4 h-4 rounded border-[#CBD5E1] text-primary focus:ring-primary focus:ring-offset-0 cursor-pointer" title="Chọn tất cả" type="checkbox"/>
</th>
<th class="px-4 py-3 min-w-[220px]">Họ và tên / Email</th>
<th class="px-4 py-3 min-w-[120px]">Mã người dùng</th>
<th class="px-4 py-3 min-w-[160px]">Vai trò</th>
<th class="px-4 py-3 min-w-[180px]">Đơn vị / Khoa</th>
<th class="px-4 py-3 min-w-[140px]">Trạng thái</th>
<th class="px-4 py-3 min-w-[110px]">Ngày tạo</th>
<th class="px-4 py-3 min-w-[140px]">Đăng nhập cuối</th>
<th class="px-4 py-3 w-28 text-center">Thao tác</th>
</tr>
</thead>
<!-- Table Body: 48px standard row heights, subtle hover -->
<tbody class="divide-y divide-[#F1F5F9] font-body-md text-body-md text-slate-800">
<!-- ROW 1: TS. Người dùng mẫu 29 -->
<tr class="hover:bg-[#F8FAFC] transition-colors group">
<td class="px-4 py-3 text-center">
<input aria-label="Chọn dòng" class="w-4 h-4 rounded border-[#CBD5E1] text-primary focus:ring-primary focus:ring-offset-0 cursor-pointer" type="checkbox"/>
</td>
<td class="px-4 py-3">
<div class="flex items-center gap-3">
<div class="w-9 h-9 rounded-full bg-red-100 text-red-700 font-bold text-xs flex items-center justify-center shrink-0">
                          NT
                        </div>
<div class="flex flex-col min-w-0">
<span class="font-semibold text-slate-900 group-hover:text-primary transition-colors truncate">TS. Người dùng mẫu 29</span>
<span class="text-body-sm font-body-sm text-slate-500 truncate">nguoidung039@example.test</span>
</div>
</div>
</td>
<td class="px-4 py-3 font-mono font-medium text-slate-700 text-xs">
                      ADM001
                    </td>
<td class="px-4 py-3">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200">
                        Quản trị hệ thống
                      </span>
</td>
<td class="px-4 py-3 text-slate-700 text-sm">
                      Trung tâm CNTT
                    </td>
<td class="px-4 py-3">
<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Đang hoạt động
                      </span>
</td>
<td class="px-4 py-3 text-slate-500 text-xs">
                      15/08/2021
                    </td>
<td class="px-4 py-3 text-slate-600 text-xs font-medium">
                      Hôm nay, 08:35
                    </td>
<td class="px-4 py-3 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1.5 text-slate-500 hover:text-primary hover:bg-slate-100 rounded transition-colors" title="Chỉnh sửa">
<span class="material-symbols-outlined text-[18px]">edit</span>
</button>
<button class="p-1.5 text-slate-500 hover:text-rose-700 hover:bg-rose-50 rounded transition-colors" title="Khóa tài khoản">
<span class="material-symbols-outlined text-[18px]">lock</span>
</button>
<button class="p-1.5 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded transition-colors" title="Thao tác khác">
<span class="material-symbols-outlined text-[18px]">more_vert</span>
</button>
</div>
</td>
</tr>
<!-- ROW 2: ThS. Người dùng mẫu 15 -->
<tr class="hover:bg-[#F8FAFC] transition-colors group">
<td class="px-4 py-3 text-center">
<input aria-label="Chọn dòng" class="w-4 h-4 rounded border-[#CBD5E1] text-primary focus:ring-primary focus:ring-offset-0 cursor-pointer" type="checkbox"/>
</td>
<td class="px-4 py-3">
<div class="flex items-center gap-3">
<div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 font-bold text-xs flex items-center justify-center shrink-0">
                          LH
                        </div>
<div class="flex flex-col min-w-0">
<span class="font-semibold text-slate-900 group-hover:text-primary transition-colors truncate">ThS. Người dùng mẫu 15</span>
<span class="text-body-sm font-body-sm text-slate-500 truncate">nguoidung033@example.test</span>
</div>
</div>
</td>
<td class="px-4 py-3 font-mono font-medium text-slate-700 text-xs">
                      CB190412
                    </td>
<td class="px-4 py-3">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 border border-blue-200">
                        Trưởng phòng Đào tạo
                      </span>
</td>
<td class="px-4 py-3 text-slate-700 text-sm">
                      Phòng Quản lý Đào tạo
                    </td>
<td class="px-4 py-3">
<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Đang hoạt động
                      </span>
</td>
<td class="px-4 py-3 text-slate-500 text-xs">
                      10/01/2022
                    </td>
<td class="px-4 py-3 text-slate-600 text-xs font-medium">
                      Hôm nay, 09:12
                    </td>
<td class="px-4 py-3 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1.5 text-slate-500 hover:text-primary hover:bg-slate-100 rounded transition-colors" title="Chỉnh sửa">
<span class="material-symbols-outlined text-[18px]">edit</span>
</button>
<button class="p-1.5 text-slate-500 hover:text-rose-700 hover:bg-rose-50 rounded transition-colors" title="Khóa tài khoản">
<span class="material-symbols-outlined text-[18px]">lock</span>
</button>
<button class="p-1.5 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded transition-colors" title="Thao tác khác">
<span class="material-symbols-outlined text-[18px]">more_vert</span>
</button>
</div>
</td>
</tr>
<!-- ROW 3: TS. Người dùng mẫu 38 -->
<tr class="hover:bg-[#F8FAFC] transition-colors group">
<td class="px-4 py-3 text-center">
<input aria-label="Chọn dòng" class="w-4 h-4 rounded border-[#CBD5E1] text-primary focus:ring-primary focus:ring-offset-0 cursor-pointer" type="checkbox"/>
</td>
<td class="px-4 py-3">
<div class="flex items-center gap-3">
<div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs flex items-center justify-center shrink-0">
                          TN
                        </div>
<div class="flex flex-col min-w-0">
<span class="font-semibold text-slate-900 group-hover:text-primary transition-colors truncate">TS. Người dùng mẫu 38</span>
<span class="text-body-sm font-body-sm text-slate-500 truncate">nguoidung037@example.test</span>
</div>
</div>
</td>
<td class="px-4 py-3 font-mono font-medium text-slate-700 text-xs">
                      GV201809
                    </td>
<td class="px-4 py-3">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">
                        Giảng viên
                      </span>
</td>
<td class="px-4 py-3 text-slate-700 text-sm">
                      Khoa Công nghệ Thông tin
                    </td>
<td class="px-4 py-3">
<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Đang hoạt động
                      </span>
</td>
<td class="px-4 py-3 text-slate-500 text-xs">
                      01/09/2022
                    </td>
<td class="px-4 py-3 text-slate-600 text-xs font-medium">
                      Hôm qua, 17:40
                    </td>
<td class="px-4 py-3 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1.5 text-slate-500 hover:text-primary hover:bg-slate-100 rounded transition-colors" title="Chỉnh sửa">
<span class="material-symbols-outlined text-[18px]">edit</span>
</button>
<button class="p-1.5 text-slate-500 hover:text-rose-700 hover:bg-rose-50 rounded transition-colors" title="Khóa tài khoản">
<span class="material-symbols-outlined text-[18px]">lock</span>
</button>
<button class="p-1.5 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded transition-colors" title="Thao tác khác">
<span class="material-symbols-outlined text-[18px]">more_vert</span>
</button>
</div>
</td>
</tr>
<!-- ROW 4: Người dùng mẫu 25 -->
<tr class="hover:bg-[#F8FAFC] transition-colors group">
<td class="px-4 py-3 text-center">
<input aria-label="Chọn dòng" class="w-4 h-4 rounded border-[#CBD5E1] text-primary focus:ring-primary focus:ring-offset-0 cursor-pointer" type="checkbox"/>
</td>
<td class="px-4 py-3">
<div class="flex items-center gap-3">
<div class="w-9 h-9 rounded-full bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0">
                          NA
                        </div>
<div class="flex flex-col min-w-0">
<span class="font-semibold text-slate-900 group-hover:text-primary transition-colors truncate">Người dùng mẫu 25</span>
<span class="text-body-sm font-body-sm text-slate-500 truncate">nguoidung019@example.test</span>
</div>
</div>
</td>
<td class="px-4 py-3 font-mono font-medium text-slate-700 text-xs">
                      2400000007
                    </td>
<td class="px-4 py-3">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                        Sinh viên
                      </span>
</td>
<td class="px-4 py-3 text-slate-700 text-sm">
                      Khoa Công nghệ Thông tin
                    </td>
<td class="px-4 py-3">
<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Đang hoạt động
                      </span>
</td>
<td class="px-4 py-3 text-slate-500 text-xs">
                      20/09/2023
                    </td>
<td class="px-4 py-3 text-slate-600 text-xs font-medium">
                      Hôm nay, 08:15
                    </td>
<td class="px-4 py-3 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1.5 text-slate-500 hover:text-primary hover:bg-slate-100 rounded transition-colors" title="Chỉnh sửa">
<span class="material-symbols-outlined text-[18px]">edit</span>
</button>
<button class="p-1.5 text-slate-500 hover:text-rose-700 hover:bg-rose-50 rounded transition-colors" title="Khóa tài khoản">
<span class="material-symbols-outlined text-[18px]">lock</span>
</button>
<button class="p-1.5 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded transition-colors" title="Thao tác khác">
<span class="material-symbols-outlined text-[18px]">more_vert</span>
</button>
</div>
</td>
</tr>
<!-- ROW 5: Người dùng mẫu 52 -->
<tr class="hover:bg-[#F8FAFC] transition-colors group bg-rose-50/20">
<td class="px-4 py-3 text-center">
<input aria-label="Chọn dòng" class="w-4 h-4 rounded border-[#CBD5E1] text-primary focus:ring-primary focus:ring-offset-0 cursor-pointer" type="checkbox"/>
</td>
<td class="px-4 py-3">
<div class="flex items-center gap-3">
<div class="w-9 h-9 rounded-full bg-slate-200 text-slate-600 font-bold text-xs flex items-center justify-center shrink-0">
                          ĐB
                        </div>
<div class="flex flex-col min-w-0">
<span class="font-semibold text-slate-900 group-hover:text-primary transition-colors truncate">Người dùng mẫu 52</span>
<span class="text-body-sm font-body-sm text-slate-500 truncate">nguoidung020@example.test</span>
</div>
</div>
</td>
<td class="px-4 py-3 font-mono font-medium text-slate-700 text-xs">
                      2400000026
                    </td>
<td class="px-4 py-3">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                        Sinh viên
                      </span>
</td>
<td class="px-4 py-3 text-slate-700 text-sm">
                      Khoa Kế toán
                    </td>
<td class="px-4 py-3">
<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
<span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                        Bị khóa
                      </span>
</td>
<td class="px-4 py-3 text-slate-500 text-xs">
                      15/10/2023
                    </td>
<td class="px-4 py-3 text-slate-500 text-xs">
                      12/03/2026
                    </td>
<td class="px-4 py-3 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1.5 text-slate-500 hover:text-primary hover:bg-slate-100 rounded transition-colors" title="Chỉnh sửa">
<span class="material-symbols-outlined text-[18px]">edit</span>
</button>
<button class="p-1.5 text-emerald-700 hover:text-emerald-700 hover:bg-emerald-50 rounded transition-colors" title="Mở khóa tài khoản">
<span class="material-symbols-outlined text-[18px]">lock_open</span>
</button>
<button class="p-1.5 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded transition-colors" title="Thao tác khác">
<span class="material-symbols-outlined text-[18px]">more_vert</span>
</button>
</div>
</td>
</tr>
<!-- ROW 6: ThS. Người dùng mẫu 05 -->
<tr class="hover:bg-[#F8FAFC] transition-colors group">
<td class="px-4 py-3 text-center">
<input aria-label="Chọn dòng" class="w-4 h-4 rounded border-[#CBD5E1] text-primary focus:ring-primary focus:ring-offset-0 cursor-pointer" type="checkbox"/>
</td>
<td class="px-4 py-3">
<div class="flex items-center gap-3">
<div class="w-9 h-9 rounded-full bg-amber-100 text-amber-700 font-bold text-xs flex items-center justify-center shrink-0">
                          HĐ
                        </div>
<div class="flex flex-col min-w-0">
<span class="font-semibold text-slate-900 group-hover:text-primary transition-colors truncate">ThS. Người dùng mẫu 05</span>
<span class="text-body-sm font-body-sm text-slate-500 truncate">nguoidung026@example.test</span>
</div>
</div>
</td>
<td class="px-4 py-3 font-mono font-medium text-slate-700 text-xs">
                      GV202602
                    </td>
<td class="px-4 py-3">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">
                        Giảng viên
                      </span>
</td>
<td class="px-4 py-3 text-slate-700 text-sm">
                      Khoa Ngoại ngữ
                    </td>
<td class="px-4 py-3">
<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
<span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        Chờ kích hoạt
                      </span>
</td>
<td class="px-4 py-3 text-slate-500 text-xs">
                      10/03/2026
                    </td>
<td class="px-4 py-3 text-slate-500 text-xs italic">
                      Chưa đăng nhập
                    </td>
<td class="px-4 py-3 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1.5 text-slate-500 hover:text-primary hover:bg-slate-100 rounded transition-colors" title="Chỉnh sửa">
<span class="material-symbols-outlined text-[18px]">edit</span>
</button>
<button class="p-1.5 text-primary hover:bg-blue-50 rounded transition-colors" title="Gửi lại link kích hoạt">
<span class="material-symbols-outlined text-[18px]">forward_to_inbox</span>
</button>
<button class="p-1.5 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded transition-colors" title="Thao tác khác">
<span class="material-symbols-outlined text-[18px]">more_vert</span>
</button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<!-- TABLE FOOTER & PAGINATION -->
<div class="px-6 py-4 bg-white border-t border-[#E2E8F0] flex flex-col sm:flex-row items-center justify-between gap-4">
<!-- Result Counter & Rows Per Page -->
<div class="flex items-center gap-4 text-body-sm font-body-sm text-slate-600">
<span>Hiển thị <strong class="text-slate-900 font-semibold">1 - 6</strong> trong tổng số <strong class="text-slate-900 font-semibold">19,842</strong> người dùng</span>
<div class="flex items-center gap-1.5">
<span class="text-slate-500">Hiển thị:</span>
<select aria-label="10 / trang" class="h-8 pl-2 pr-6 bg-white border border-[#CBD5E1] rounded text-xs font-medium text-slate-700 focus:outline-none focus:border-primary appearance-none cursor-pointer">
<option>10 / trang</option>
<option>25 / trang</option>
<option>50 / trang</option>
<option>100 / trang</option>
</select>
</div>
</div>
<!-- Pagination Controls -->
<nav aria-label="Pagination" class="flex items-center gap-1">
<button class="h-8 px-2.5 rounded border border-[#CBD5E1] bg-slate-50 text-slate-500 text-xs font-medium flex items-center gap-1 cursor-not-allowed" disabled="">
<span class="material-symbols-outlined text-[16px]">chevron_left</span>
<span>Trước</span>
</button>
<button class="h-8 w-8 rounded border border-primary bg-primary text-white text-xs font-semibold flex items-center justify-center">
                  1
                </button>
<button class="h-8 w-8 rounded border border-[#CBD5E1] bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium flex items-center justify-center transition-colors">
                  2
                </button>
<button class="h-8 w-8 rounded border border-[#CBD5E1] bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium flex items-center justify-center transition-colors">
                  3
                </button>
<span class="px-1 text-slate-500 text-xs">...</span>
<button class="h-8 px-2 rounded border border-[#CBD5E1] bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium flex items-center justify-center transition-colors">
                  1985
                </button>
<button class="h-8 px-2.5 rounded border border-[#CBD5E1] bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium flex items-center gap-1 transition-colors">
<span>Sau</span>
<span class="material-symbols-outlined text-[16px]">chevron_right</span>
</button>
</nav>
</div>
</div>
<!-- SYSTEM AUDIT & QUICK NOTE BANNER -->
<div class="p-4 rounded-xl bg-blue-50/70 border border-blue-100 flex items-start gap-3 text-slate-700">
<span class="material-symbols-outlined text-primary text-[20px] shrink-0 mt-0.5">info</span>
<div class="text-body-sm font-body-sm leading-relaxed">
<span class="font-semibold text-slate-900">Lưu ý quản trị:</span> 
              Tài khoản tân sinh viên và cán bộ giảng viên mới tạo sẽ ở trạng thái <strong class="text-amber-700 font-semibold">Chờ kích hoạt</strong> cho đến khi hoàn tất xác thực email <code class="text-xs bg-white px-1.5 py-0.5 rounded border border-blue-200">@example.test</code> và cập nhật mật khẩu lần đầu. Mọi thay đổi về quyền truy cập được đồng bộ tự động với cổng đăng ký học phần trong vòng 5 phút.
            </div>
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
