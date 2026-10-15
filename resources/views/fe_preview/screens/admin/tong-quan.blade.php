@extends('fe_preview.layouts.admin')
@section('page_title', 'Tổng quan Quản lý Đào tạo - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Tổng quan Quản lý Đào tạo - HUIT Học Vụ</title>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/tong-quan']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">dashboard</span>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Tổng quan Quản lý Đào tạo</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Tổng quan Quản lý Đào tạo</div>
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
<div class="flex items-center justify-between"><div class="flex items-center gap-2 text-label-sm text-on-surface-variant bg-surface-container-lowest px-3 py-1.5 rounded-lg border border-outline-variant/60 shadow-sm"><span class="material-symbols-outlined text-[16px] text-primary" data-icon="sync">sync</span><span>Hệ thống cơ sở dữ liệu đồng bộ lúc: <strong class="text-on-surface font-semibold">10:45 • Hôm nay</strong></span></div><div class="flex items-center gap-2 text-label-sm text-on-surface-variant"><span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#DCFCE7] text-[#15803D] font-semibold text-xs"><span class="w-2 h-2 rounded-full bg-[#15803D]"></span>Cổng đăng ký đang mở (Đợt 2)</span></div></div><div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/60 shadow-sm"><div><h1 class="text-[30px] font-bold text-on-surface tracking-tight leading-tight">Tổng quan Quản lý Đào tạo</h1><p class="text-body-md text-on-surface-variant mt-1.5">Thống kê chỉ số đào tạo toàn trường, tiến độ mở lớp và các yêu cầu học vụ chờ xử lý.</p></div><div class="flex items-center gap-3 shrink-0"><button class="h-10 px-4 bg-surface-container-lowest border border-outline-variant hover:bg-surface-container-low text-on-surface text-body-sm font-medium rounded-lg flex items-center gap-2 transition-colors active:scale-95 shadow-sm"><span class="material-symbols-outlined text-[18px] text-on-surface-variant" data-icon="download">download</span><span>Xuất báo cáo Thống kê</span></button><button class="h-10 px-5 bg-primary hover:bg-primary-container text-white text-body-sm font-medium rounded-lg flex items-center gap-2 transition-colors active:scale-95 shadow-sm"><span class="material-symbols-outlined text-[18px]" data-icon="add_circle">add_circle</span><span>+ Mở lớp học phần HK1</span></button></div></div><div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6"><div class="bg-surface-container-lowest border border-outline-variant/60 rounded-xl p-5 shadow-sm hover:shadow transition-shadow"><div class="flex items-center justify-between"><span class="text-body-sm text-on-surface-variant font-medium">Tổng sinh viên</span><div class="w-9 h-9 rounded-full bg-[#EFF6FF] flex items-center justify-center text-primary"><span class="material-symbols-outlined text-[20px]" data-icon="groups">groups</span></div></div><div class="mt-3"><div class="text-headline-xl text-on-surface font-bold tracking-tight">18,450</div><div class="mt-1.5 flex items-center gap-1.5"><span class="text-label-sm text-[#15803D] bg-[#DCFCE7] px-2 py-0.5 rounded font-semibold">+3.2% so với năm 2023</span></div></div></div><div class="bg-surface-container-lowest border border-outline-variant/60 rounded-xl p-5 shadow-sm hover:shadow transition-shadow"><div class="flex items-center justify-between"><span class="text-body-sm text-on-surface-variant font-medium">Giảng viên cơ hữu</span><div class="w-9 h-9 rounded-full bg-[#EFF6FF] flex items-center justify-center text-primary"><span class="material-symbols-outlined text-[20px]" data-icon="how_to_reg">how_to_reg</span></div></div><div class="mt-3"><div class="text-headline-xl text-on-surface font-bold tracking-tight">680</div><div class="mt-1.5 flex items-center gap-1.5"><span class="text-label-sm text-primary bg-[#EFF6FF] px-2 py-0.5 rounded font-semibold">98% đã phân công lịch dạy</span></div></div></div><div class="bg-surface-container-lowest border border-outline-variant/60 rounded-xl p-5 shadow-sm hover:shadow transition-shadow"><div class="flex items-center justify-between"><span class="text-body-sm text-on-surface-variant font-medium">Lớp học phần HK1</span><div class="w-9 h-9 rounded-full bg-[#EFF6FF] flex items-center justify-center text-primary"><span class="material-symbols-outlined text-[20px]" data-icon="menu_book">menu_book</span></div></div><div class="mt-3"><div class="text-headline-xl text-on-surface font-bold tracking-tight">1,240</div><div class="mt-1.5 flex items-center gap-1.5"><span class="text-label-sm text-[#15803D] bg-[#DCFCE7] px-2 py-0.5 rounded font-semibold">94% đã xếp phòng học</span></div></div></div><div class="bg-surface-container-lowest border border-outline-variant/60 rounded-xl p-5 shadow-sm hover:shadow transition-shadow"><div class="flex items-center justify-between"><span class="text-body-sm text-on-surface-variant font-medium">Yêu cầu chờ duyệt</span><div class="w-9 h-9 rounded-full bg-[#FEF3C7] flex items-center justify-center text-[#b45309]"><span class="material-symbols-outlined text-[20px]" data-icon="error_outline">error_outline</span></div></div><div class="mt-3"><div class="text-headline-xl text-on-surface font-bold tracking-tight">28</div><div class="mt-1.5 flex items-center gap-1.5"><span class="text-label-sm text-[#B45309] bg-[#FEF3C7] px-2 py-0.5 rounded font-semibold">12 đơn cần xử lý gấp</span></div></div></div></div><div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start"><div class="lg:col-span-8 bg-surface-container-lowest border border-outline-variant/60 rounded-xl shadow-sm flex flex-col p-6"><div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-surface-container"><div class="flex items-center gap-3"><h2 class="text-[20px] font-bold text-on-surface">Yêu cầu &amp; Thủ tục chờ phê duyệt</h2><span class="text-label-sm font-semibold bg-[#FEF3C7] text-[#B45309] px-2.5 py-1 rounded-full shadow-sm">28 đơn chờ xử lý</span></div><button class="p-1.5 border border-outline-variant hover:bg-surface-container-low rounded-lg text-on-surface-variant self-start sm:self-auto transition-colors" title="Làm mới danh sách"><span class="material-symbols-outlined text-[18px]" data-icon="refresh">refresh</span></button></div><div class="flex flex-wrap items-center gap-2 py-3.5 border-b border-surface-container"><button class="px-3 py-1.5 rounded-lg text-body-sm font-semibold bg-primary text-white transition-colors shadow-sm">Tất cả (28)</button><button class="px-3 py-1.5 rounded-lg text-body-sm font-medium text-on-surface-variant hover:bg-surface-container-low transition-colors">Đăng ký học phần trễ (12)</button><button class="px-3 py-1.5 rounded-lg text-body-sm font-medium text-on-surface-variant hover:bg-surface-container-low transition-colors">Hoãn thi / Hoãn học (9)</button><button class="px-3 py-1.5 rounded-lg text-body-sm font-medium text-on-surface-variant hover:bg-surface-container-low transition-colors">Chuyển điểm / Miễn HP (7)</button></div><div class="overflow-x-auto flex-1"><table class="w-full text-left border-collapse"><thead><tr class="bg-surface-bright border-b border-surface-container text-outline text-xs uppercase font-semibold tracking-wider"><th class="py-3.5 px-3">Mã đơn</th><th class="py-3.5 px-3">Sinh viên &amp; MSSV</th><th class="py-3.5 px-3">Khoa</th><th class="py-3.5 px-3">Loại yêu cầu</th><th class="py-3.5 px-3">Ngày gửi</th><th class="py-3.5 px-3 text-center">Trạng thái</th><th class="py-3.5 px-3 text-right">Thao tác</th></tr></thead><tbody class="divide-y divide-surface-container text-body-sm"><tr class="hover:bg-surface-container-low transition-colors"><td class="py-3.5 px-3 font-mono text-xs font-semibold text-primary">ĐH-2024-0891</td><td class="py-3.5 px-3"><div class="font-semibold text-on-surface">Nguyễn Văn Bảo</div><div class="font-mono text-xs text-on-surface-variant">2001210452</div></td><td class="py-3.5 px-3 text-on-surface-variant">Khoa CNTT</td><td class="py-3.5 px-3 text-on-surface font-medium">ĐK học phần trễ</td><td class="py-3.5 px-3 text-on-surface-variant text-xs font-mono">15/10/2026</td><td class="py-3.5 px-3 text-center"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#FEF3C7] text-[#B45309]">Chờ duyệt</span></td><td class="py-3.5 px-3 text-right whitespace-nowrap"><button class="px-2.5 py-1 text-xs font-semibold bg-primary hover:bg-primary-container text-white rounded-md transition-colors mr-1 shadow-sm">Duyệt nhanh</button><button class="px-2.5 py-1 text-xs font-medium border border-outline-variant hover:bg-surface-container rounded-md text-on-surface-variant transition-colors">Chi tiết</button></td></tr><tr class="hover:bg-surface-container-low transition-colors"><td class="py-3.5 px-3 font-mono text-xs font-semibold text-primary">ĐH-2024-0889</td><td class="py-3.5 px-3"><div class="font-semibold text-on-surface">Trần Thị Mai Phương</div><div class="font-mono text-xs text-on-surface-variant">2001220114</div></td><td class="py-3.5 px-3 text-on-surface-variant">Khoa Kế toán</td><td class="py-3.5 px-3 text-on-surface font-medium">Hoãn thi kết thúc HP</td><td class="py-3.5 px-3 text-on-surface-variant text-xs font-mono">15/10/2026</td><td class="py-3.5 px-3 text-center"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#FEE2E2] text-[#B91C1C]">Bổ sung minh chứng</span></td><td class="py-3.5 px-3 text-right whitespace-nowrap"><button class="px-2.5 py-1 text-xs font-medium border border-outline-variant hover:bg-surface-container rounded-md text-on-surface-variant transition-colors">Chi tiết</button></td></tr><tr class="hover:bg-surface-container-low transition-colors"><td class="py-3.5 px-3 font-mono text-xs font-semibold text-primary">ĐH-2024-0885</td><td class="py-3.5 px-3"><div class="font-semibold text-on-surface">Lê Hoàng Nam</div><div class="font-mono text-xs text-on-surface-variant">2001200876</div></td><td class="py-3.5 px-3 text-on-surface-variant">Khoa CN Thực phẩm</td><td class="py-3.5 px-3 text-on-surface font-medium">Miễn HP Anh văn B1</td><td class="py-3.5 px-3 text-on-surface-variant text-xs font-mono">14/10/2026</td><td class="py-3.5 px-3 text-center"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#DCFCE7] text-[#15803D]">Đã duyệt</span></td><td class="py-3.5 px-3 text-right whitespace-nowrap"><button class="px-2.5 py-1 text-xs font-semibold bg-primary hover:bg-primary-container text-white rounded-md transition-colors mr-1 shadow-sm">Phê duyệt</button><button class="px-2.5 py-1 text-xs font-medium border border-outline-variant hover:bg-surface-container rounded-md text-on-surface-variant transition-colors">Chi tiết</button></td></tr><tr class="hover:bg-surface-container-low transition-colors"><td class="py-3.5 px-3 font-mono text-xs font-semibold text-primary">ĐH-2024-0878</td><td class="py-3.5 px-3"><div class="font-semibold text-on-surface">Phạm Quốc Huy</div><div class="font-mono text-xs text-on-surface-variant">2001211090</div></td><td class="py-3.5 px-3 text-on-surface-variant">Khoa CNTT</td><td class="py-3.5 px-3 text-on-surface font-medium">ĐK học phần trễ</td><td class="py-3.5 px-3 text-on-surface-variant text-xs font-mono">14/10/2026</td><td class="py-3.5 px-3 text-center"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#FEF3C7] text-[#B45309]">Chờ duyệt</span></td><td class="py-3.5 px-3 text-right whitespace-nowrap"><button class="px-2.5 py-1 text-xs font-semibold bg-primary hover:bg-primary-container text-white rounded-md transition-colors mr-1 shadow-sm">Duyệt nhanh</button><button class="px-2.5 py-1 text-xs font-medium border border-outline-variant hover:bg-surface-container rounded-md text-on-surface-variant transition-colors">Chi tiết</button></td></tr><tr class="hover:bg-surface-container-low transition-colors"><td class="py-3.5 px-3 font-mono text-xs font-semibold text-primary">ĐH-2024-0870</td><td class="py-3.5 px-3"><div class="font-semibold text-on-surface">Đỗ Thu Hằng</div><div class="font-mono text-xs text-on-surface-variant">2001221532</div></td><td class="py-3.5 px-3 text-on-surface-variant">Khoa QTKD</td><td class="py-3.5 px-3 text-on-surface font-medium">Xin thôi học tạm thời</td><td class="py-3.5 px-3 text-on-surface-variant text-xs font-mono">13/10/2026</td><td class="py-3.5 px-3 text-center"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-surface-container text-on-surface-variant">Chờ Khoa duyệt</span></td><td class="py-3.5 px-3 text-right whitespace-nowrap"><button class="px-2.5 py-1 text-xs font-medium border border-outline-variant hover:bg-surface-container rounded-md text-on-surface-variant transition-colors">Chi tiết</button></td></tr></tbody></table></div><div class="pt-4 border-t border-surface-container flex items-center justify-between text-body-sm text-on-surface-variant"><span>Hiển thị <strong>5 trên 28</strong> đơn chờ duyệt</span><div class="flex items-center gap-1.5"><button class="px-2.5 py-1 rounded-md border border-outline-variant text-xs font-medium hover:bg-surface-container-low disabled:opacity-50 transition-colors">Trước</button><button class="px-2.5 py-1 rounded-md bg-primary text-white text-xs font-semibold shadow-sm">1</button><button class="px-2.5 py-1 rounded-md border border-outline-variant text-xs font-medium hover:bg-surface-container-low transition-colors">2</button><button class="px-2.5 py-1 rounded-md border border-outline-variant text-xs font-medium hover:bg-surface-container-low transition-colors">3</button><button class="px-2.5 py-1 rounded-md border border-outline-variant text-xs font-medium hover:bg-surface-container-low transition-colors">Sau</button></div></div></div><div class="lg:col-span-4 flex flex-col gap-6"><div class="bg-surface-container-lowest border border-outline-variant/60 rounded-xl p-5 shadow-sm"><div class="flex items-center justify-between pb-3 border-b border-surface-container mb-3"><h3 class="font-bold text-[16px] text-on-surface">Tiến độ đăng ký học phần HK1</h3><span class="text-label-sm font-bold text-primary bg-[#EFF6FF] px-2 py-0.5 rounded-full">78%</span></div><p class="text-body-sm text-on-surface-variant mb-2">14,200 / 18,450 sinh viên đã hoàn tất</p><div class="w-full bg-surface-container rounded-full h-2.5 mb-4 overflow-hidden"><div class="bg-primary h-2.5 rounded-full" style="width: 78%"></div></div><div class="space-y-2.5 text-body-sm"><div class="flex items-center justify-between text-on-surface"><span class="text-on-surface-variant">Khóa 2021 (Năm 4)</span><span class="font-semibold text-[#15803D]">98% hoàn thành</span></div><div class="flex items-center justify-between text-on-surface"><span class="text-on-surface-variant">Khóa 2022 (Năm 3)</span><span class="font-semibold text-primary">92% hoàn thành</span></div><div class="flex items-center justify-between text-on-surface"><span class="text-on-surface-variant">Khóa 2023 (Năm 2)</span><span class="font-semibold text-primary">81% hoàn thành</span></div><div class="flex items-center justify-between text-on-surface"><span class="text-on-surface-variant">Khóa 2024 (Tân sinh viên)</span><span class="font-semibold text-[#B45309]">45% đang mở cổng</span></div></div></div><div class="bg-surface-container-lowest border border-outline-variant/60 rounded-xl p-5 shadow-sm"><div class="flex items-center justify-between pb-3 border-b border-surface-container mb-3"><h3 class="font-bold text-[16px] text-on-surface flex items-center gap-2"><span class="material-symbols-outlined text-primary text-[20px]" data-icon="campaign">campaign</span><span>Thông báo Ban Giám hiệu</span></h3></div><div class="space-y-3"><div class="p-3 rounded-lg border border-outline-variant/60 bg-surface-bright hover:bg-surface-container-low transition-colors cursor-pointer"><div class="flex items-center gap-2 mb-1"><span class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded bg-[#FEE2E2] text-[#B91C1C]">Khẩn</span><span class="text-[11px] text-outline">Hôm nay 09:30</span></div><h4 class="text-body-sm font-semibold text-on-surface leading-snug">Chốt sĩ số mở lớp HK1 năm học 2026-2027</h4></div><div class="p-3 rounded-lg border border-outline-variant/60 bg-surface-bright hover:bg-surface-container-low transition-colors cursor-pointer"><div class="flex items-center gap-2 mb-1"><span class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded bg-[#EFF6FF] text-primary">Đào tạo</span><span class="text-[11px] text-outline">14/10/2026</span></div><h4 class="text-body-sm font-semibold text-on-surface leading-snug">Kế hoạch phân công phòng học giảng đường khu B</h4></div><div class="p-3 rounded-lg border border-outline-variant/60 bg-surface-bright hover:bg-surface-container-low transition-colors cursor-pointer"><div class="flex items-center gap-2 mb-1"><span class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded bg-surface-container text-on-surface-variant">Học vụ</span><span class="text-[11px] text-outline">12/10/2026</span></div><h4 class="text-body-sm font-semibold text-on-surface leading-snug">Hướng dẫn xử lý hồ sơ sinh viên quy chế đào tạo gia hạn</h4></div></div></div><div class="bg-surface-container-lowest border border-outline-variant/60 rounded-xl p-5 shadow-sm"><div class="flex items-center justify-between pb-3 border-b border-surface-container mb-3"><h3 class="font-bold text-[16px] text-on-surface flex items-center gap-2"><span class="material-symbols-outlined text-primary text-[20px]" data-icon="history">history</span><span>Nhật ký hệ thống mới nhất</span></h3></div><div class="space-y-3 text-body-sm"><div class="flex items-start gap-2.5 pb-2.5 border-b border-surface-container/60"><span class="text-xs font-mono font-semibold text-primary pt-0.5">10:45</span><div class="flex-1 leading-snug"><span class="font-semibold text-on-surface">Lê Thị Mai Hương</span> đã duyệt mở thêm 02 lớp HP Lập trình Web.</div></div><div class="flex items-start gap-2.5 pb-2.5 border-b border-surface-container/60"><span class="text-xs font-mono font-semibold text-primary pt-0.5">10:15</span><div class="flex-1 leading-snug"><span class="font-semibold text-on-surface">TS. Trần Anh Tuấn</span> đã cập nhật bảng điểm quá trình Lớp 12DHTH01.</div></div><div class="flex items-start gap-2.5"><span class="text-xs font-mono font-semibold text-primary pt-0.5">09:30</span><div class="flex-1 leading-snug"><span class="font-semibold text-on-surface">Hệ thống tự động</span> gạch quy chế đào tạo cho 340 giao dịch ngân hàng.</div></div></div></div></div></div><footer class="pt-8 border-t border-surface-container text-body-sm text-on-surface-variant flex flex-col items-center justify-center text-center gap-2 pb-6"><div class="text-xs text-on-surface-variant">© 2026 <strong>Trường Đại học Công Thương TP. Hồ Chí Minh (HUIT)</strong> - Hệ thống Quản lý Học vụ.</div><div class="text-xs text-outline flex items-center gap-3"><span>Hỗ trợ kỹ thuật: <a class="hover:text-primary transition-colors" href="mailto:daotao@huit.edu.vn">daotao@huit.edu.vn</a></span><span>•</span><span>Hotline: 028.38161673</span></div></footer>
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
