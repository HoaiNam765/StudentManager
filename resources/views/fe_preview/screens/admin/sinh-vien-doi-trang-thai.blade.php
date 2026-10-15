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
<div class="text-[11px] text-slate-500">admin@huit.edu.vn</div>
</div>
</div>
</div>
</header>
<!-- MAIN BODY CONTENT -->
<main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
<!-- Back action & Page title header bar -->
<div class="space-y-3">
<div>
<a class="inline-flex items-center gap-1.5 text-primary hover:text-primary-container font-label-md text-label-md group transition-colors" href="#">
<span class="material-symbols-outlined text-[18px] group-hover:-translate-x-0.5 transition-transform" data-icon="arrow_back">arrow_back</span>
<span>← Quay lại danh sách sinh viên</span>
</a>
</div>
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-sm">
<div class="space-y-1">
<div class="flex flex-wrap items-center gap-3">
<h1 class="font-headline-lg text-headline-lg text-on-surface font-semibold tracking-tight">
                  Chỉnh sửa hồ sơ sinh viên: Nguyễn Văn An (MSSV: 2001210123)
                </h1>
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-label-sm font-label-sm bg-[#DCFCE7] text-[#15803D] font-semibold border border-[#BBF7D0]">
<span class="w-1.5 h-1.5 rounded-full bg-[#15803D]"></span>
                  Đang học (Active)
                </span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant">
                Khoa Công nghệ Thông tin • Lớp 12DHTH01 • Khóa 2021 (Đại học Chính quy)
              </p>
</div>
<!-- Action buttons cluster -->
<div class="flex items-center flex-wrap gap-2.5">
<!-- Special Danger Status Switch Button -->
<button class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-amber-50 border border-amber-300 text-amber-800 hover:bg-amber-100 font-label-md text-label-md transition-all shadow-sm" id="openModalBtn" type="button">
<span class="material-symbols-outlined text-[18px] text-amber-700" data-icon="warning">warning</span>
<span>⚠️ Đổi trạng thái học vụ</span>
</button>
<button class="px-4 py-2 rounded-lg bg-surface-container-lowest border border-outline-variant text-on-surface hover:bg-surface-container-low font-label-md text-label-md transition-colors" type="button">
                Hủy bỏ
              </button>
<button class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-primary text-white hover:bg-[#1647a8] font-label-md text-label-md shadow-sm transition-all" type="button">
<span class="material-symbols-outlined text-[18px]" data-icon="save">save</span>
<span>Lưu thay đổi hồ sơ</span>
</button>
</div>
</div>
</div>
<!-- 4. NỘI DUNG BIỂU MẪU CHÍNH (FORM 4 NHÓM TRƯỜNG KHOA HỌC) -->
<form class="space-y-6" onsubmit="event.preventDefault();">
<!-- GRID 1: NHÓM 1 & NHÓM 2 -->
<div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
<!-- NHÓM 1: THÔNG TIN HỌC VỤ & ĐÀO TẠO (Col 12 on mobile, Col 7 on desktop) -->
<section class="xl:col-span-7 bg-surface-container-lowest rounded-xl border border-outline-variant p-6 shadow-sm flex flex-col justify-between">
<div>
<div class="flex items-center justify-between pb-4 mb-5 border-b border-outline-variant/60">
<div class="flex items-center gap-2.5">
<div class="w-8 h-8 rounded-lg bg-primary-fixed flex items-center justify-center text-primary">
<span class="material-symbols-outlined text-[20px]" data-icon="school">school</span>
</div>
<div>
<h2 class="font-headline-sm text-headline-sm font-semibold text-on-surface">1. Thông tin học vụ &amp; Đào tạo</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Dữ liệu quản lý chính quy liên kết trực tiếp phòng Đào tạo HUIT</p>
</div>
</div>
<span class="text-label-sm font-label-sm bg-surface-container px-2 py-0.5 rounded text-outline">Quy chế K21</span>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
<!-- Mã sinh viên (Disabled/Read-only) -->
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-1.5">
                      Mã sinh viên (MSSV) <span class="text-outline font-normal">(Khóa cứng)</span>
</label>
<div class="relative">
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 bg-surface-container-low/70 border border-outline-variant rounded-lg text-on-surface font-semibold cursor-not-allowed" readonly="" type="text" value="2001210123"/>
<span class="absolute right-3 top-2.5 material-symbols-outlined text-[18px] text-outline" data-icon="lock">lock</span>
</div>
</div>
<!-- Khóa học -->
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-1.5">Khóa học</label>
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 bg-surface-container-low/70 border border-outline-variant rounded-lg text-on-surface font-medium cursor-not-allowed" readonly="" type="text" value="Khóa 2021 (K21 - ĐHCQ)"/>
</div>
<!-- Ngành đào tạo -->
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-1.5">Ngành đào tạo</label>
<select aria-label="Công nghệ Thông tin" class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface font-medium focus:border-primary focus:ring-2 focus:ring-primary/20">
<option selected="">Công nghệ Thông tin</option>
<option>Kỹ thuật Phần mềm</option>
<option>An toàn Thông tin</option>
<option>Khoa học Dữ liệu</option>
</select>
</div>
<!-- Chuyên ngành -->
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-1.5">Chuyên ngành</label>
<select aria-label="Kỹ thuật phần mềm" class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface font-medium focus:border-primary focus:ring-2 focus:ring-primary/20">
<option selected="">Kỹ thuật phần mềm</option>
<option>Hệ thống Thông tin Doanh nghiệp</option>
<option>Trí tuệ Nhân tạo Ứng dụng</option>
</select>
</div>
<!-- Lớp hành chính -->
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-1.5">Lớp hành chính</label>
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface font-medium focus:border-primary focus:ring-2 focus:ring-primary/20" type="text" value="12DHTH01"/>
</div>
<!-- Bậc đào tạo -->
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-1.5">Bậc đào tạo</label>
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 bg-surface-container-low/70 border border-outline-variant rounded-lg text-on-surface font-medium cursor-not-allowed" readonly="" type="text" value="Đại học chính quy (4 năm)"/>
</div>
<!-- Cố vấn học tập -->
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-1.5">Cố vấn học tập</label>
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface font-medium focus:border-primary focus:ring-2 focus:ring-primary/20" type="text" value="TS. Trần Hoàng Nam (Khoa CNTT)"/>
</div>
<!-- Tình trạng học vụ -->
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-1.5">Tình trạng học vụ hiện hành</label>
<div class="h-10 px-3 bg-emerald-50/60 border border-emerald-300 rounded-lg flex items-center justify-between text-emerald-800 font-semibold text-body-sm font-body-sm">
<span class="flex items-center gap-1.5">
<span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                        Đang học (Bình thường)
                      </span>
<span class="text-label-sm font-label-sm text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded">HK1 24-25</span>
</div>
</div>
</div>
</div>
<div class="mt-5 pt-3 border-t border-outline-variant/40 flex items-center justify-between text-body-sm font-body-sm text-on-surface-variant">
<span>Số tín chỉ đã tích lũy: <strong class="text-on-surface">94 / 142</strong></span>
<span>Điểm trung bình tích lũy (CPA): <strong class="text-primary font-semibold">3.24 / 4.00</strong></span>
</div>
</section>
<!-- NHÓM 2: THÔNG TIN CÁ NHÂN & NHẬN DẠNG (Col 5 on desktop) -->
<section class="xl:col-span-5 bg-surface-container-lowest rounded-xl border border-outline-variant p-6 shadow-sm flex flex-col justify-between">
<div>
<div class="flex items-center justify-between pb-4 mb-5 border-b border-outline-variant/60">
<div class="flex items-center gap-2.5">
<div class="w-8 h-8 rounded-lg bg-primary-fixed flex items-center justify-center text-primary">
<span class="material-symbols-outlined text-[20px]" data-icon="person">person</span>
</div>
<div>
<h2 class="font-headline-sm text-headline-sm font-semibold text-on-surface">2. Thông tin cá nhân &amp; Nhận dạng</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Dữ liệu định danh học viên &amp; thông tin căn cước</p>
</div>
</div>
</div>
<!-- Avatar & Họ tên section -->
<div class="flex items-start gap-4 mb-4 pb-4 border-b border-outline-variant/40">
<div class="relative w-24 h-32 rounded-lg border-2 border-dashed border-outline-variant overflow-hidden flex flex-col items-center justify-center bg-surface-container-low shrink-0 group">
<img alt="" class="w-full h-full object-cover" data-alt="A formal student portrait photo of a Vietnamese male college student wearing a neat white collared shirt against a clean light blue backdrop, professionally framed in standard university academic card format with clear balanced studio lighting." src="https://lh3.googleusercontent.com/aida-public/AB6AXuByQuBc8dniIJ-NKJ5yF4x-cxNLk-u9yKDCKxWhYnZ275CpNLBxBX9ve_GrGVOjErMvrsaFMZdQI8FYPGNYrkXtFucCFpIIJbpjkJYiXqOy3mJ2vXh9dOtLjKtGblXb0Xv_1JvzT0IKppwvvs6ZCSMxFrcZE-eqTSmKzmqE0xm3mAkUEpgtqEG4J9clmSpAyrmHvWGMvPNEtheN-jyl8whOo5SgwRYFFVq5hjG-9mxHEmwQdtZsiB2zfCJ4jSBigEYE841hW-zU15c"/>
<div class="absolute inset-0 bg-on-surface/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity cursor-pointer">
<span class="material-symbols-outlined text-white text-[20px]" data-icon="photo_camera">photo_camera</span>
</div>
</div>
<div class="flex-1 space-y-2">
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-1">
                        Họ và tên khai sinh <span class="text-error">*</span>
</label>
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface font-semibold focus:border-primary focus:ring-2 focus:ring-primary/20" type="text" value="Nguyễn Văn An"/>
</div>
<div>
<button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-outline-variant bg-surface-container-low hover:bg-surface-container text-on-surface text-label-sm font-label-sm transition-colors" type="button">
<span class="material-symbols-outlined text-[16px]" data-icon="upload_file">upload_file</span>
<span>Tải ảnh thẻ mới</span>
</button>
<p class="text-[11px] text-on-surface-variant mt-1 leading-tight">Ảnh chuẩn 3x4 cm nền xanh, định dạng .JPG hoặc .PNG (Tối đa 2MB).</p>
</div>
</div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
<!-- Ngày sinh -->
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-1">Ngày sinh</label>
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface text-body-md font-body-md" type="date" value="2003-08-15"/>
</div>
<!-- Giới tính -->
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-1">Giới tính</label>
<select aria-label="Nam" class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface font-medium">
<option selected="">Nam</option>
<option>Nữ</option>
<option>Khác</option>
</select>
</div>
<!-- SỐ CCCD - WITH INLINE ERROR FIELD -->
<div class="sm:col-span-2">
<label class="block font-label-md text-label-md text-error font-semibold mb-1">
                      Số CCCD / Mã định danh cá nhân <span class="text-error">*</span>
</label>
<div class="relative">
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 pr-10 bg-error-container/20 border-2 border-error rounded-lg text-error font-semibold focus:ring-2 focus:ring-error/20" type="text" value="07920300123"/>
<span class="absolute right-3 top-2.5 material-symbols-outlined text-[20px] text-error" data-icon="error">error</span>
</div>
<!-- Inline Error Notification Callout -->
<p class="mt-1.5 text-body-sm font-body-sm text-error flex items-start gap-1 leading-snug">
<span class="material-symbols-outlined text-[16px] shrink-0 mt-0.5" data-icon="warning">warning</span>
<span><strong>CCCD thiếu số:</strong> Số CCCD phải có đủ đúng 12 chữ số theo quy định Cục CSQLHC. Vui lòng kiểm tra lại.</span>
</p>
</div>
<!-- Ngày cấp CCCD -->
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-1">Ngày cấp CCCD</label>
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface" type="text" value="12/04/2021"/>
</div>
<!-- Nơi cấp -->
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-1">Nơi cấp</label>
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface" type="text" value="Cục CSQLHC về TTXH"/>
</div>
<!-- Dân tộc & Tôn giáo & Quốc tịch -->
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-1">Dân tộc / Tôn giáo</label>
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface" type="text" value="Kinh / Không"/>
</div>
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-1">Nơi sinh (Tỉnh/TP)</label>
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface" type="text" value="TP. Hồ Chí Minh"/>
</div>
</div>
</div>
</section>
</div>
<!-- GRID 2: NHÓM 3 & NHÓM 4 -->
<div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
<!-- NHÓM 3: THÔNG TIN LIÊN LẠC & CƯ TRÚ (Col 7 on desktop) -->
<section class="xl:col-span-7 bg-surface-container-lowest rounded-xl border border-outline-variant p-6 shadow-sm">
<div class="flex items-center justify-between pb-4 mb-5 border-b border-outline-variant/60">
<div class="flex items-center gap-2.5">
<div class="w-8 h-8 rounded-lg bg-primary-fixed flex items-center justify-center text-primary">
<span class="material-symbols-outlined text-[20px]" data-icon="contact_mail">contact_mail</span>
</div>
<div>
<h2 class="font-headline-sm text-headline-sm font-semibold text-on-surface">3. Thông tin liên lạc &amp; Cư trú</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Địa chỉ thông báo giấy báo học vụ &amp; trao đổi học tập</p>
</div>
</div>
</div>
<div class="space-y-4">
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
<!-- Email trường (Office365) -->
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-1">
                      Email trường (HUIT cấp)
                    </label>
<div class="relative">
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 pr-24 bg-surface-container-low/70 border border-outline-variant rounded-lg text-on-surface font-medium cursor-not-allowed" readonly="" type="text" value="2001210123@huit.edu.vn"/>
<span class="absolute right-2 top-2 px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800">Office 365</span>
</div>
</div>
<!-- Số điện thoại cá nhân -->
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-1">Số điện thoại cá nhân</label>
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface font-medium focus:border-primary focus:ring-2 focus:ring-primary/20" type="text" value="0903123456"/>
</div>
</div>
<!-- EMAIL CÁ NHÂN - WITH INLINE ERROR FIELD -->
<div>
<label class="block font-label-md text-label-md text-error font-semibold mb-1">
                    Email cá nhân (Dùng khôi phục mật khẩu) <span class="text-error">*</span>
</label>
<div class="relative">
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 pr-10 bg-error-container/20 border-2 border-error rounded-lg text-error font-semibold focus:ring-2 focus:ring-error/20" type="text" value="an.nguyenvan2003@@gmail"/>
<span class="absolute right-3 top-2.5 material-symbols-outlined text-[20px] text-error" data-icon="priority_high">priority_high</span>
</div>
<!-- Error banner warning -->
<p class="mt-1.5 text-body-sm font-body-sm text-error flex items-start gap-1 leading-snug">
<span class="material-symbols-outlined text-[16px] shrink-0 mt-0.5" data-icon="error">error</span>
<span><strong>Email sai định dạng:</strong> Địa chỉ email chứa ký tự trùng lặp không hợp lệ (@@). Ví dụ chuẩn: example@domain.com</span>
</p>
</div>
<!-- Địa chỉ thường trú -->
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-1">Địa chỉ thường trú (Theo sổ hộ khẩu / CCCD)</label>
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface font-medium focus:border-primary focus:ring-2 focus:ring-primary/20" type="text" value="140 Lê Trọng Tấn, P. Tây Thạnh, Q. Tân Phú, TP. Hồ Chí Minh"/>
</div>
<!-- Địa chỉ tạm trú -->
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-1">Địa chỉ tạm trú / Nơi ở hiện tại</label>
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface font-medium focus:border-primary focus:ring-2 focus:ring-primary/20" type="text" value="KTX Cơ sở Tân Phú HUIT - Phòng 402 Dãy B"/>
</div>
</div>
</section>
<!-- NHÓM 4: THÔNG TIN GIA ĐÌNH & LIÊN HỆ KHẨN CẤP (Col 5 on desktop) -->
<section class="xl:col-span-5 bg-surface-container-lowest rounded-xl border border-outline-variant p-6 shadow-sm flex flex-col justify-between">
<div>
<div class="flex items-center justify-between pb-4 mb-5 border-b border-outline-variant/60">
<div class="flex items-center gap-2.5">
<div class="w-8 h-8 rounded-lg bg-primary-fixed flex items-center justify-center text-primary">
<span class="material-symbols-outlined text-[20px]" data-icon="emergency">emergency</span>
</div>
<div>
<h2 class="font-headline-sm text-headline-sm font-semibold text-on-surface">4. Thông tin gia đình &amp; Khẩn cấp</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Thông tin người giám hộ để Nhà trường liên lạc</p>
</div>
</div>
</div>
<div class="space-y-4">
<!-- Họ tên phụ huynh -->
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-1">
                      Họ tên phụ huynh / Người bảo hộ
                    </label>
<div class="grid grid-cols-3 gap-2">
<input aria-label="Ô nhập liệu" class="col-span-2 h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface font-medium focus:border-primary focus:ring-2 focus:ring-primary/20" type="text" value="Nguyễn Văn Bình"/>
<input aria-label="Ô nhập liệu" class="h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface text-center font-medium" type="text" value="Cha"/>
</div>
</div>
<!-- SĐT liên hệ khẩn cấp -->
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-1">Số điện thoại người liên hệ khẩn cấp</label>
<div class="relative">
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 pl-10 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface font-semibold focus:border-primary focus:ring-2 focus:ring-primary/20" type="text" value="0912345678"/>
<span class="absolute left-3 top-2.5 material-symbols-outlined text-[18px] text-primary" data-icon="call">call</span>
</div>
</div>
<!-- Nghề nghiệp / Cơ quan -->
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-1">Nghề nghiệp / Nơi công tác</label>
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface font-medium" type="text" value="Kỹ sư xây dựng - Công ty CP Đầu tư XD Miền Nam"/>
</div>
<!-- Địa chỉ liên hệ -->
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-1">Địa chỉ liên hệ người giám hộ</label>
<input aria-label="Ô nhập liệu" class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface font-medium" type="text" value="140 Lê Trọng Tấn, P. Tây Thạnh, TP.HCM"/>
</div>
</div>
</div>
<div class="mt-4 p-3 rounded-lg bg-surface-container-low text-body-sm font-body-sm text-on-surface-variant flex items-center gap-2">
<span class="material-symbols-outlined text-[18px] text-primary shrink-0" data-icon="verified_user">verified_user</span>
<span>Thông tin đã được xác nhận với Ban cán sự lớp 12DHTH01.</span>
</div>
</section>
</div>
</form>
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

@endsection
