@extends('fe_preview.layouts.admin')
@section('page_title', 'Admin - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
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
<!-- Page Title & Top Actions -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2">
<div>
<div class="flex items-center gap-3">
<h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Nhập dữ liệu Giảng viên từ file Excel</h1>
</div>
<p class="font-body-md text-body-md text-on-surface-variant mt-1">
              Tải lên danh sách hồ sơ cán bộ giảng viên cơ hữu và thỉnh giảng vào hệ thống học vụ HUIT.
            </p>
</div>
<div class="flex items-center gap-3 flex-shrink-0">
<button class="flex items-center gap-2 bg-surface-container-lowest border border-outline-variant hover:border-outline text-on-surface px-4 py-2 rounded-lg font-label-md text-label-md transition-colors shadow-sm hover:bg-surface-container-low">
<span class="material-symbols-outlined text-[18px]">menu_book</span>
<span>Hướng dẫn chuẩn bị dữ liệu</span>
</button>
<button class="flex items-center gap-2 bg-surface-container-lowest border border-outline-variant hover:border-primary text-primary px-4 py-2 rounded-lg font-label-md text-label-md transition-colors shadow-sm hover:bg-primary/5">
<span class="material-symbols-outlined text-[18px]">download</span>
<span>Tải file mẫu chuẩn (.xlsx)</span>
</button>
</div>
</div>
<!-- ==================== STEPPER (3 BƯỚC) ==================== -->
<div class="bg-surface-container-lowest rounded-xl p-5 border border-outline-variant shadow-sm">
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
<!-- Step 1: Hoàn thành -->
<div class="flex items-center gap-3 p-3 bg-surface-container-low rounded-lg border border-outline-variant/60">
<div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0 font-bold">
<span class="material-symbols-outlined text-[22px]">check</span>
</div>
<div class="min-w-0 flex-1">
<div class="flex items-center justify-between">
<span class="font-label-md text-label-md font-semibold text-on-surface">1. Tải lên tệp dữ liệu</span>
<span class="font-label-sm text-label-sm text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded font-medium">Đã tải lên</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant truncate mt-0.5 flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]">attach_file</span>
                  DS_GiangVien_K24_HK1.xlsx (1.2 MB)
                </p>
</div>
</div>
<!-- Step 2: Đang thực hiện / Active -->
<div class="flex items-center gap-3 p-3 bg-primary-fixed/30 rounded-lg border-2 border-primary shadow-sm relative">
<div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center flex-shrink-0 font-bold shadow">
<span class="font-label-lg text-label-lg">2</span>
</div>
<div class="min-w-0 flex-1">
<div class="flex items-center justify-between">
<span class="font-label-md text-label-md font-semibold text-primary">2. Xem trước &amp; Xác thực dữ liệu</span>
<span class="inline-flex items-center gap-1 font-label-sm text-label-sm bg-primary text-white px-2 py-0.5 rounded-full font-medium animate-pulse">
                    Đang xử lý
                  </span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Kiểm tra hợp lệ 51 hồ sơ</p>
</div>
</div>
<!-- Step 3: Chưa thực hiện -->
<div class="flex items-center gap-3 p-3 bg-surface-container-low/50 rounded-lg border border-outline-variant/40 opacity-60">
<div class="w-10 h-10 rounded-full bg-outline-variant/40 text-on-surface-variant flex items-center justify-center flex-shrink-0 font-bold">
<span class="font-label-lg text-label-lg">3</span>
</div>
<div class="min-w-0 flex-1">
<span class="font-label-md text-label-md font-semibold text-on-surface">3. Hoàn tất &amp; Kết quả</span>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Ghi cơ sở dữ liệu học vụ</p>
</div>
</div>
</div>
</div>
<!-- ==================== STATS METRIC SUMMARY CARDS ==================== -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-5">
<!-- Card 1: Tổng số -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-sm flex items-center justify-between">
<div class="space-y-1">
<span class="font-label-md text-label-md text-on-surface-variant font-medium">Tổng số dòng tệp</span>
<div class="flex items-baseline gap-2">
<span class="font-headline-xl text-headline-xl text-on-surface">51</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">hồ sơ cán bộ</span>
</div>
<p class="font-body-sm text-body-sm text-outline">Bao gồm GV cơ hữu &amp; Thỉnh giảng</p>
</div>
<div class="w-12 h-12 rounded-xl bg-surface-container-low flex items-center justify-center text-on-surface-variant">
<span class="material-symbols-outlined text-[28px]" data-icon="format_list_bulleted">format_list_bulleted</span>
</div>
</div>
<!-- Card 2: Hợp lệ -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-emerald-200 shadow-sm flex items-center justify-between">
<div class="space-y-1">
<div class="flex items-center gap-2">
<span class="font-label-md text-label-md text-emerald-700 font-semibold">Dữ liệu Hợp lệ</span>
<span class="bg-emerald-100 text-emerald-800 font-label-sm text-label-sm px-2 py-0.5 rounded-full font-bold">94.1%</span>
</div>
<div class="flex items-baseline gap-2">
<span class="font-headline-xl text-headline-xl text-emerald-700">48</span>
<span class="font-body-sm text-body-sm text-emerald-700">dòng sẵn sàng</span>
</div>
<p class="font-body-sm text-body-sm text-emerald-700/80">Đầy đủ mã GV, email, học vị, đơn vị</p>
</div>
<div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-700">
<span class="material-symbols-outlined text-[28px] fill-icon" data-icon="check_circle">check_circle</span>
</div>
</div>
<!-- Card 3: Có lỗi -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-error/30 shadow-sm flex items-center justify-between">
<div class="space-y-1">
<div class="flex items-center gap-2">
<span class="font-label-md text-label-md text-error font-semibold">Phát hiện lỗi dữ liệu</span>
<span class="bg-error-container text-on-error-container font-label-sm text-label-sm px-2 py-0.5 rounded-full font-bold">5.9%</span>
</div>
<div class="flex items-baseline gap-2">
<span class="font-headline-xl text-headline-xl text-error">3</span>
<span class="font-body-sm text-body-sm text-error">dòng cần xử lý</span>
</div>
<p class="font-body-sm text-body-sm text-error/80">Trùng mã GV, sai email, sai danh mục khoa</p>
</div>
<div class="w-12 h-12 rounded-xl bg-error-container/50 flex items-center justify-center text-error">
<span class="material-symbols-outlined text-[28px] fill-icon" data-icon="error">error</span>
</div>
</div>
</div>
<!-- ==================== BANNER CẢNH BÁO TỔNG HỢP ==================== -->
<div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3.5 shadow-sm">
<div class="p-1 bg-amber-100 rounded-lg text-amber-800 flex-shrink-0 mt-0.5">
<span class="material-symbols-outlined text-[22px]">warning</span>
</div>
<div class="flex-1 flex flex-col md:flex-row md:items-center justify-between gap-3">
<div>
<h4 class="font-headline-sm text-headline-sm text-amber-900 font-semibold">Cảnh báo tính toàn vẹn dữ liệu</h4>
<p class="font-body-md text-body-md text-amber-800 mt-0.5">
                Phát hiện <strong>3 bản ghi</strong> chứa thông tin không hợp lệ hoặc trùng lặp mã giảng viên. Bạn có thể chọn nhập trước <strong>48 dòng hợp lệ</strong> hoặc tải file đối soát lỗi để chỉnh sửa.
              </p>
</div>
<div class="flex items-center gap-2 flex-shrink-0">
<button class="bg-amber-600 hover:bg-amber-700 text-white font-label-md text-label-md px-3.5 py-1.5 rounded-lg transition-colors shadow-sm flex items-center gap-1.5">
<span class="material-symbols-outlined text-[16px]">download</span>
<span>Tải bảng lỗi đối soát (.xlsx)</span>
</button>
</div>
</div>
</div>
<!-- ==================== DATA PREVIEW & VALIDATION TABLE ==================== -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
<!-- Card Header & Table Toolbars -->
<div class="p-4 border-b border-outline-variant bg-surface-container-lowest flex flex-col lg:flex-row lg:items-center justify-between gap-4">
<div class="flex items-center gap-4 flex-wrap">
<div class="flex items-center gap-2">
<span class="font-headline-sm text-headline-sm text-on-surface">Chi tiết bản ghi xác thực</span>
<span class="text-xs text-on-surface-variant font-medium bg-surface-container px-2 py-0.5 rounded-md">51 dòng</span>
</div>
<!-- Filter Pills -->
<div class="flex items-center bg-surface-container-low p-1 rounded-lg border border-outline-variant text-label-md font-label-md">
<button class="px-3 py-1 rounded bg-white text-on-surface font-semibold shadow-sm transition-all">Tất cả (51)</button>
<button class="px-3 py-1 rounded text-on-surface-variant hover:text-on-surface transition-all">Hợp lệ (48)</button>
<button class="px-3 py-1 rounded text-error hover:bg-error-container/30 transition-all font-semibold flex items-center gap-1">
<span>Bị lỗi (3)</span>
</button>
</div>
</div>
<!-- Toolbar Actions & Quick Filters -->
<div class="flex items-center gap-3 flex-wrap">
<!-- Toggle Checkbox: Chỉ hiển thị các dòng lỗi -->
<label class="flex items-center gap-2 cursor-pointer bg-error-container/20 hover:bg-error-container/40 border border-error/20 px-3 py-1.5 rounded-lg transition-colors">
<input aria-label="Chọn dòng" checked="" class="w-4 h-4 rounded border-error text-error focus:ring-error" type="checkbox"/>
<span class="font-label-md text-label-md text-error font-medium">Chỉ hiển thị dòng bị lỗi (3 dòng)</span>
</label>
<!-- Button download error -->
<button class="flex items-center gap-1.5 bg-surface-container-low hover:bg-surface-container text-on-surface border border-outline-variant px-3 py-1.5 rounded-lg font-label-md text-label-md transition-colors">
<span class="material-symbols-outlined text-[16px]">file_download</span>
<span>Tải báo cáo lỗi (.xlsx)</span>
</button>
</div>
</div>
<!-- Table Container with Horizontal Scroll & Sticky Columns -->
<div class="overflow-x-auto max-h-[580px] relative">
<table class="w-full text-left border-collapse text-body-sm font-body-sm">
<thead class="bg-surface-container-low text-on-surface-variant uppercase font-label-md text-label-md tracking-wider sticky top-0 z-30 shadow-sm border-b border-outline-variant">
<tr>
<th class="py-3 px-3 w-14 text-center sticky-col-1 bg-surface-container-low border-r border-outline-variant/60" scope="col">STT</th>
<th class="py-3 px-3 w-32 sticky-col-2 bg-surface-container-low border-r border-outline-variant/60" scope="col">Trạng thái</th>
<th class="py-3 px-3 w-28" scope="col">Mã GV</th>
<th class="py-3 px-4 w-44" scope="col">Họ và tên</th>
<th class="py-3 px-3 w-24" scope="col">Học vị</th>
<th class="py-3 px-4 w-52" scope="col">Email công vụ</th>
<th class="py-3 px-3 w-32" scope="col">Số điện thoại</th>
<th class="py-3 px-4 w-48" scope="col">Khoa / Đơn vị</th>
<th class="py-3 px-3 w-36" scope="col">Bộ môn</th>
<th class="py-3 px-3 w-28 text-center" scope="col">Định mức</th>
<th class="py-3 px-4 min-w-[320px] bg-amber-50/60 border-l border-amber-200" scope="col">Chi tiết lỗi &amp; Hướng dẫn khắc phục</th>
<th class="py-3 px-3 w-28 text-center sticky right-0 bg-surface-container-low border-l border-outline-variant" scope="col">Thao tác</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant/50">
<!-- ROW 1: HỢP LỆ -->
<tr class="hover:bg-surface-container-low/60 transition-colors">
<td class="py-3 px-3 text-center sticky-col-1 bg-white border-r border-outline-variant/40 font-medium text-on-surface-variant">01</td>
<td class="py-3 px-3 sticky-col-2 bg-white border-r border-outline-variant/40">
<span class="inline-flex items-center gap-1 font-label-sm text-label-sm bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                      Hợp lệ
                    </span>
</td>
<td class="py-3 px-3 font-mono font-semibold text-on-surface">GV201801</td>
<td class="py-3 px-4 font-medium text-on-surface">PGS.TS. Người dùng mẫu 56</td>
<td class="py-3 px-3 text-on-surface">Phó Giáo sư</td>
<td class="py-3 px-4 text-on-surface font-mono">nguoidung024@example.test</td>
<td class="py-3 px-3 text-on-surface-variant font-mono">0903123456</td>
<td class="py-3 px-4 text-on-surface">Khoa Công nghệ Thông tin</td>
<td class="py-3 px-3 text-on-surface">Kỹ thuật phần mềm</td>
<td class="py-3 px-3 text-center font-semibold text-on-surface">120 tiết</td>
<td class="py-3 px-4 text-emerald-700 bg-emerald-50/30 border-l border-emerald-100 font-medium flex items-center gap-1.5">
<span class="material-symbols-outlined text-[16px]">check_circle</span>
                    Dữ liệu chuẩn
                  </td>
<td class="py-3 px-3 text-center sticky right-0 bg-white border-l border-outline-variant">
<button class="text-on-surface-variant hover:text-primary p-1 rounded hover:bg-surface-container" title="Chi tiết">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
</td>
</tr>
<!-- ROW 12: BỊ LỖI EMAIL (Theo yêu cầu) -->
<tr class="bg-red-50/70 hover:bg-red-100/60 transition-colors">
<td class="py-3 px-3 text-center sticky-col-1 bg-red-50 border-r border-red-200 font-bold text-red-900">12</td>
<td class="py-3 px-3 sticky-col-2 bg-red-50 border-r border-red-200">
<span class="inline-flex items-center gap-1 font-label-sm text-label-sm bg-red-200 text-red-900 px-2 py-0.5 rounded-full font-bold">
<span class="material-symbols-outlined text-[13px] fill-icon">cancel</span>
                      Lỗi dữ liệu
                    </span>
</td>
<td class="py-3 px-3 font-mono font-semibold text-on-surface">GV202412</td>
<td class="py-3 px-4 font-medium text-on-surface">ThS. Người dùng mẫu 29</td>
<td class="py-3 px-3 text-on-surface">Thạc sĩ</td>
<!-- Bôi đỏ email sai -->
<td class="py-3 px-4">
<span class="bg-red-200 text-red-900 px-2 py-0.5 rounded font-mono font-bold border border-red-400">
                      tuan.nv-huit.edu.vn
                    </span>
</td>
<td class="py-3 px-3 text-on-surface font-mono">0918234567</td>
<td class="py-3 px-4 text-on-surface">Khoa Công nghệ Thông tin</td>
<td class="py-3 px-3 text-on-surface">Mạng máy tính</td>
<td class="py-3 px-3 text-center font-semibold text-on-surface">120 tiết</td>
<!-- Chi tiết lỗi & hướng dẫn khắc phục -->
<td class="py-3 px-4 bg-red-100/50 border-l border-red-300">
<div class="text-red-900 font-semibold flex items-start gap-1.5">
<span class="material-symbols-outlined text-error text-[18px] flex-shrink-0 mt-0.5">error</span>
<div>
<p class="font-medium">Dòng 12, cột Email: email '<span class="font-mono underline">tuan.nv-huit.edu.vn</span>' không đúng định dạng.</p>
<p class="text-xs text-red-700 mt-0.5">💡 <strong>Cách sửa:</strong> Thêm ký tự '@' hợp lệ (VD: <span class="font-mono">nguoidung039@example.test</span>).</p>
</div>
</div>
</td>
<td class="py-3 px-3 text-center sticky right-0 bg-red-50 border-l border-red-200">
<div class="flex items-center justify-center gap-1">
<button class="text-primary hover:text-white hover:bg-primary p-1 rounded transition-colors" title="Sửa nhanh email trực tiếp">
<span class="material-symbols-outlined text-[18px]">edit</span>
</button>
<button class="text-error hover:bg-error-container p-1 rounded transition-colors" title="Bỏ qua dòng này">
<span class="material-symbols-outlined text-[18px]">block</span>
</button>
</div>
</td>
</tr>
<!-- ROW 20: HỢP LỆ -->
<tr class="hover:bg-surface-container-low/60 transition-colors">
<td class="py-3 px-3 text-center sticky-col-1 bg-white border-r border-outline-variant/40 font-medium text-on-surface-variant">20</td>
<td class="py-3 px-3 sticky-col-2 bg-white border-r border-outline-variant/40">
<span class="inline-flex items-center gap-1 font-label-sm text-label-sm bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                      Hợp lệ
                    </span>
</td>
<td class="py-3 px-3 font-mono font-semibold text-on-surface">GV201805</td>
<td class="py-3 px-4 font-medium text-on-surface">TS. Người dùng mẫu 15</td>
<td class="py-3 px-3 text-on-surface">Tiến sĩ</td>
<td class="py-3 px-4 text-on-surface font-mono">nguoidung033@example.test</td>
<td class="py-3 px-3 text-on-surface-variant font-mono">0987654321</td>
<td class="py-3 px-4 text-on-surface">Khoa Công nghệ Thông tin</td>
<td class="py-3 px-3 text-on-surface">Hệ thống thông tin</td>
<td class="py-3 px-3 text-center font-semibold text-on-surface">120 tiết</td>
<td class="py-3 px-4 text-emerald-700 bg-emerald-50/30 border-l border-emerald-100 font-medium flex items-center gap-1.5">
<span class="material-symbols-outlined text-[16px]">check_circle</span>
                    Dữ liệu chuẩn
                  </td>
<td class="py-3 px-3 text-center sticky right-0 bg-white border-l border-outline-variant">
<button class="text-on-surface-variant hover:text-primary p-1 rounded hover:bg-surface-container" title="Chi tiết">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
</td>
</tr>
<!-- ROW 27: BỊ LỖI TRÙNG MÃ GV (Theo yêu cầu) -->
<tr class="bg-red-50/70 hover:bg-red-100/60 transition-colors">
<td class="py-3 px-3 text-center sticky-col-1 bg-red-50 border-r border-red-200 font-bold text-red-900">27</td>
<td class="py-3 px-3 sticky-col-2 bg-red-50 border-r border-red-200">
<span class="inline-flex items-center gap-1 font-label-sm text-label-sm bg-red-200 text-red-900 px-2 py-0.5 rounded-full font-bold">
<span class="material-symbols-outlined text-[13px] fill-icon">cancel</span>
                      Lỗi dữ liệu
                    </span>
</td>
<!-- Bôi đỏ mã GV trùng -->
<td class="py-3 px-3">
<span class="bg-red-200 text-red-900 px-2 py-0.5 rounded font-mono font-bold border border-red-400">
                      GV201809
                    </span>
</td>
<td class="py-3 px-4 font-medium text-on-surface">TS. Người dùng mẫu 38</td>
<td class="py-3 px-3 text-on-surface">Tiến sĩ</td>
<td class="py-3 px-4 text-on-surface font-mono">nguoidung037@example.test</td>
<td class="py-3 px-3 text-on-surface font-mono">0790860012</td>
<td class="py-3 px-4 text-on-surface">Khoa Công nghệ Thông tin</td>
<td class="py-3 px-3 text-on-surface">Kỹ thuật máy tính</td>
<td class="py-3 px-3 text-center font-semibold text-on-surface">120 tiết</td>
<!-- Chi tiết lỗi & hướng dẫn khắc phục -->
<td class="py-3 px-4 bg-red-100/50 border-l border-red-300">
<div class="text-red-900 font-semibold flex items-start gap-1.5">
<span class="material-symbols-outlined text-error text-[18px] flex-shrink-0 mt-0.5">error</span>
<div>
<p class="font-medium">Dòng 27, cột Mã GV: '<span class="font-mono">GV201809</span>' đã tồn tại trong hệ thống (Trùng với TS. Người dùng mẫu 38 - Khoa CNTT).</p>
<p class="text-xs text-red-700 mt-0.5">💡 <strong>Cách sửa:</strong> Đổi mã GV mới hoặc chọn tùy chọn 'Ghi đè cập nhật hồ sơ'.</p>
</div>
</div>
</td>
<td class="py-3 px-3 text-center sticky right-0 bg-red-50 border-l border-red-200">
<div class="flex items-center justify-center gap-1">
<button class="text-primary hover:text-white hover:bg-primary p-1 rounded transition-colors" title="Đổi mã GV mới">
<span class="material-symbols-outlined text-[18px]">edit</span>
</button>
<button class="text-error hover:bg-error-container p-1 rounded transition-colors" title="Bỏ qua dòng này">
<span class="material-symbols-outlined text-[18px]">block</span>
</button>
</div>
</td>
</tr>
<!-- ROW 35: HỢP LỆ -->
<tr class="hover:bg-surface-container-low/60 transition-colors">
<td class="py-3 px-3 text-center sticky-col-1 bg-white border-r border-outline-variant/40 font-medium text-on-surface-variant">35</td>
<td class="py-3 px-3 sticky-col-2 bg-white border-r border-outline-variant/40">
<span class="inline-flex items-center gap-1 font-label-sm text-label-sm bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                      Hợp lệ
                    </span>
</td>
<td class="py-3 px-3 font-mono font-semibold text-on-surface">GV202115</td>
<td class="py-3 px-4 font-medium text-on-surface">ThS. Người dùng mẫu 33</td>
<td class="py-3 px-3 text-on-surface">Thạc sĩ</td>
<td class="py-3 px-4 text-on-surface font-mono">nguoidung030@example.test</td>
<td class="py-3 px-3 text-on-surface-variant font-mono">0912345678</td>
<td class="py-3 px-4 text-on-surface">Khoa Công nghệ Thực phẩm</td>
<td class="py-3 px-3 text-on-surface">Hóa sinh</td>
<td class="py-3 px-3 text-center font-semibold text-on-surface">120 tiết</td>
<td class="py-3 px-4 text-emerald-700 bg-emerald-50/30 border-l border-emerald-100 font-medium flex items-center gap-1.5">
<span class="material-symbols-outlined text-[16px]">check_circle</span>
                    Dữ liệu chuẩn
                  </td>
<td class="py-3 px-3 text-center sticky right-0 bg-white border-l border-outline-variant">
<button class="text-on-surface-variant hover:text-primary p-1 rounded hover:bg-surface-container" title="Chi tiết">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
</td>
</tr>
<!-- ROW 45: BỊ LỖI KHOA KHÔNG HỢP LỆ (Theo yêu cầu) -->
<tr class="bg-red-50/70 hover:bg-red-100/60 transition-colors">
<td class="py-3 px-3 text-center sticky-col-1 bg-red-50 border-r border-red-200 font-bold text-red-900">45</td>
<td class="py-3 px-3 sticky-col-2 bg-red-50 border-r border-red-200">
<span class="inline-flex items-center gap-1 font-label-sm text-label-sm bg-red-200 text-red-900 px-2 py-0.5 rounded-full font-bold">
<span class="material-symbols-outlined text-[13px] fill-icon">cancel</span>
                      Lỗi dữ liệu
                    </span>
</td>
<td class="py-3 px-3 font-mono font-semibold text-on-surface">GV202445</td>
<td class="py-3 px-4 font-medium text-on-surface">ThS. Người dùng mẫu 47</td>
<td class="py-3 px-3 text-on-surface">Thạc sĩ</td>
<td class="py-3 px-4 text-on-surface font-mono">nguoidung034@example.test</td>
<td class="py-3 px-3 text-on-surface font-mono">0934567890</td>
<!-- Bôi đỏ Khoa sai danh mục -->
<td class="py-3 px-4">
<span class="bg-red-200 text-red-900 px-2 py-0.5 rounded font-medium border border-red-400">
                      Khoa AI
                    </span>
</td>
<td class="py-3 px-3 text-on-surface">Trí tuệ nhân tạo</td>
<td class="py-3 px-3 text-center font-semibold text-on-surface">120 tiết</td>
<!-- Chi tiết lỗi & hướng dẫn khắc phục -->
<td class="py-3 px-4 bg-red-100/50 border-l border-red-300">
<div class="text-red-900 font-semibold flex items-start gap-1.5">
<span class="material-symbols-outlined text-error text-[18px] flex-shrink-0 mt-0.5">error</span>
<div>
<p class="font-medium">Dòng 45, cột Khoa: '<span class="underline">Khoa AI</span>' không có trong danh mục Khoa/Viện HUIT.</p>
<p class="text-xs text-red-700 mt-0.5">💡 <strong>Cách sửa:</strong> Chọn đúng đơn vị trong danh mục, ví dụ: 'Khoa Công nghệ Thông tin'.</p>
</div>
</div>
</td>
<td class="py-3 px-3 text-center sticky right-0 bg-red-50 border-l border-red-200">
<div class="flex items-center justify-center gap-1">
<button class="text-primary hover:text-white hover:bg-primary p-1 rounded transition-colors" title="Chọn lại danh mục khoa">
<span class="material-symbols-outlined text-[18px]">edit</span>
</button>
<button class="text-error hover:bg-error-container p-1 rounded transition-colors" title="Bỏ qua dòng này">
<span class="material-symbols-outlined text-[18px]">block</span>
</button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<!-- Table Footer Pagination and Counters -->
<div class="p-4 border-t border-outline-variant bg-surface-container-lowest flex flex-col sm:flex-row items-center justify-between gap-3 text-on-surface-variant font-body-sm text-body-sm">
<div class="flex items-center gap-2">
<span>Hiển thị <strong>6</strong> trong tổng số <strong>51</strong> dòng bản ghi</span>
<span class="text-outline-variant">•</span>
<span class="text-error font-medium">3 bản ghi cần đối soát</span>
</div>
<div class="flex items-center gap-1">
<button class="px-2.5 py-1 rounded border border-outline-variant text-on-surface hover:bg-surface-container disabled:opacity-40" disabled="">
<span class="material-symbols-outlined text-[16px]">chevron_left</span>
</button>
<button class="px-3 py-1 rounded bg-primary text-white font-semibold">1</button>
<button class="px-3 py-1 rounded hover:bg-surface-container text-on-surface">2</button>
<button class="px-3 py-1 rounded hover:bg-surface-container text-on-surface">3</button>
<span class="px-2 text-outline">...</span>
<button class="px-3 py-1 rounded hover:bg-surface-container text-on-surface">6</button>
<button class="px-2.5 py-1 rounded border border-outline-variant text-on-surface hover:bg-surface-container">
<span class="material-symbols-outlined text-[16px]">chevron_right</span>
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
