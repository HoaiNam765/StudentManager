@extends('fe_preview.layouts.admin')
@section('page_title', 'Chi tiết & Cấu trúc Khối kiến thức CTĐT - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Chi tiết &amp; Cấu trúc Khối kiến thức CTĐT - HUIT Học Vụ</title>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Chi tiết &amp; Cấu trúc Khối kiến thức CTĐT</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Chi tiết &amp; Cấu trúc Khối kiến thức CTĐT</div>
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
<div class="max-w-[1400px] mx-auto space-y-6">
<!-- ================= 3. ĐẦU TRANG (Header CTĐT Card) ================= -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-6 shadow-sm">
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
<!-- Left Info -->
<div class="space-y-2">
<div class="flex items-center gap-3 flex-wrap">
<h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">
                  Chương trình Đào tạo: Công nghệ Thông tin
                </h1>
<!-- Badge Bản nháp -->
<span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-semibold bg-[#FEF3C7] text-[#D97706] border border-[#FDE68A]">
<span class="w-2 h-2 rounded-full bg-[#D97706] animate-pulse"></span>
                  Bản nháp
                </span>
</div>
<!-- Metadata attributes -->
<div class="flex flex-wrap items-center gap-y-1 gap-x-4 text-xs text-on-surface-variant">
<div class="flex items-center gap-1">
<span class="text-outline">Mã CTĐT:</span>
<span class="font-semibold text-on-surface font-mono">7480201</span>
</div>
<span class="text-outline-variant">•</span>
<div class="flex items-center gap-1">
<span class="text-outline">Ngành:</span>
<span class="font-medium text-on-surface">Công nghệ Thông tin</span>
</div>
<span class="text-outline-variant">•</span>
<div class="flex items-center gap-1">
<span class="text-outline">Khóa tuyển sinh:</span>
<span class="font-semibold text-primary">Khóa 2024 (K24)</span>
</div>
<span class="text-outline-variant">•</span>
<div class="flex items-center gap-1">
<span class="text-outline">Khoa phụ trách:</span>
<span class="font-medium text-on-surface">Khoa Công nghệ Thông tin</span>
</div>
<span class="text-outline-variant">•</span>
<div class="flex items-center gap-1">
<span class="text-outline">Hệ đào tạo:</span>
<span class="font-medium text-on-surface">Đại học chính quy (4 năm)</span>
</div>
</div>
</div>
<!-- Right Actions Buttons -->
<div class="flex items-center gap-2.5 flex-shrink-0">
<button class="h-10 px-3.5 bg-surface-container-lowest border border-outline-variant hover:bg-surface-container-low text-on-surface rounded-lg font-label-md text-label-md flex items-center gap-2 transition-colors shadow-sm">
<span class="material-symbols-outlined text-lg text-outline">file_download</span>
<span>Xuất khung CTĐT</span>
</button>
<button class="h-10 px-3.5 bg-surface-container-lowest border border-outline-variant hover:bg-surface-container-low text-on-surface rounded-lg font-label-md text-label-md flex items-center gap-2 transition-colors shadow-sm">
<span class="material-symbols-outlined text-lg text-outline">history</span>
<span>Lịch sử cập nhật</span>
</button>
<!-- CTA: Ban hành CTĐT (triggers modal) -->
<button class="h-10 px-4 bg-primary hover:bg-[#1647a8] active:scale-[0.98] text-white rounded-lg font-label-md text-label-md flex items-center gap-2 shadow transition-all" onclick="document.getElementById('publishModal').classList.remove('hidden')">
<span class="material-symbols-outlined text-lg">verified</span>
<span>Ban hành CTĐT</span>
</button>
</div>
</div>
</div>
<!-- ================= 4. NỘI DUNG CHÍNH - CÁC KHỐI KIẾN THỨC ================= -->
<div class="space-y-5">
<!-- ---------------- KHỐI 1: KHỐI ĐẠI CƯƠNG ---------------- -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
<!-- Header Khối -->
<div class="p-4 bg-surface-container-low/40 border-b border-outline-variant/70 flex items-center justify-between">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">
                  1
                </div>
<div>
<div class="flex items-center gap-2.5">
<h3 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Khối Đại cương</h3>
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#DCFCE7] text-[#15803D] border border-[#BBF7D0]">
<span class="material-symbols-outlined text-xs">check_circle</span>
                      Đạt chuẩn 32 TC
                    </span>
</div>
<p class="text-xs text-on-surface-variant mt-0.5">Yêu cầu tối thiểu theo chuẩn HUIT: 32 tín chỉ (Bao gồm Lý luận chính trị, Toán, Ngoại ngữ, GDQP-AN)</p>
</div>
</div>
<div class="flex items-center gap-2">
<button class="h-8 px-3 rounded-lg bg-surface-container-lowest border border-outline-variant hover:bg-surface-container-low text-primary text-xs font-semibold flex items-center gap-1.5 transition-colors">
<span class="material-symbols-outlined text-sm">add</span>
<span>Thêm học phần vào khối Đại cương</span>
</button>
<span class="material-symbols-outlined text-outline cursor-pointer p-1">expand_less</span>
</div>
</div>
<!-- Bảng dữ liệu Khối 1 -->
<div class="overflow-x-auto">
<table class="w-full text-left text-xs border-collapse">
<thead>
<tr class="bg-surface-container-low text-outline uppercase font-label-md tracking-wider border-b border-outline-variant/60">
<th class="py-2.5 px-4 w-12 text-center">STT</th>
<th class="py-2.5 px-4 w-28">Mã HP</th>
<th class="py-2.5 px-4 min-w-[220px]">Tên học phần</th>
<th class="py-2.5 px-4 w-28">Tính chất</th>
<th class="py-2.5 px-4 w-32 text-center">Số TC (LT-TH)</th>
<th class="py-2.5 px-4 min-w-[140px]">Tiên quyết</th>
<th class="py-2.5 px-4 w-24 text-center">Thao tác</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant/40">
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-3 px-4 text-center text-outline">1</td>
<td class="py-3 px-4 font-mono font-medium text-primary">001001</td>
<td class="py-3 px-4 font-medium text-on-surface">Triết học Mác - Lênin</td>
<td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-surface-container text-on-secondary-container font-medium text-[11px]">Bắt buộc</span></td>
<td class="py-3 px-4 text-center font-semibold">3 <span class="text-outline font-normal text-[11px]">(3 - 0)</span></td>
<td class="py-3 px-4 text-outline italic">Không</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1 hover:text-primary rounded hover:bg-surface-container-low" title="Sửa"><span class="material-symbols-outlined text-base">edit</span></button>
<button class="p-1 hover:text-error rounded hover:bg-error-container/40" title="Xóa"><span class="material-symbols-outlined text-base">delete</span></button>
</div>
</td>
</tr>
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-3 px-4 text-center text-outline">2</td>
<td class="py-3 px-4 font-mono font-medium text-primary">001002</td>
<td class="py-3 px-4 font-medium text-on-surface">Kinh tế chính trị Mác - Lênin</td>
<td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-surface-container text-on-secondary-container font-medium text-[11px]">Bắt buộc</span></td>
<td class="py-3 px-4 text-center font-semibold">2 <span class="text-outline font-normal text-[11px]">(2 - 0)</span></td>
<td class="py-3 px-4 text-on-surface-variant font-mono">001001</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1 hover:text-primary rounded hover:bg-surface-container-low" title="Sửa"><span class="material-symbols-outlined text-base">edit</span></button>
<button class="p-1 hover:text-error rounded hover:bg-error-container/40" title="Xóa"><span class="material-symbols-outlined text-base">delete</span></button>
</div>
</td>
</tr>
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-3 px-4 text-center text-outline">3</td>
<td class="py-3 px-4 font-mono font-medium text-primary">002001</td>
<td class="py-3 px-4 font-medium text-on-surface">Toán cao cấp 1 (Giải tích)</td>
<td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-surface-container text-on-secondary-container font-medium text-[11px]">Bắt buộc</span></td>
<td class="py-3 px-4 text-center font-semibold">3 <span class="text-outline font-normal text-[11px]">(3 - 0)</span></td>
<td class="py-3 px-4 text-outline italic">Không</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1 hover:text-primary rounded hover:bg-surface-container-low" title="Sửa"><span class="material-symbols-outlined text-base">edit</span></button>
<button class="p-1 hover:text-error rounded hover:bg-error-container/40" title="Xóa"><span class="material-symbols-outlined text-base">delete</span></button>
</div>
</td>
</tr>
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-3 px-4 text-center text-outline">4</td>
<td class="py-3 px-4 font-mono font-medium text-primary">002002</td>
<td class="py-3 px-4 font-medium text-on-surface">Đại số tuyến tính</td>
<td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-surface-container text-on-secondary-container font-medium text-[11px]">Bắt buộc</span></td>
<td class="py-3 px-4 text-center font-semibold">3 <span class="text-outline font-normal text-[11px]">(3 - 0)</span></td>
<td class="py-3 px-4 text-outline italic">Không</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1 hover:text-primary rounded hover:bg-surface-container-low" title="Sửa"><span class="material-symbols-outlined text-base">edit</span></button>
<button class="p-1 hover:text-error rounded hover:bg-error-container/40" title="Xóa"><span class="material-symbols-outlined text-base">delete</span></button>
</div>
</td>
</tr>
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-3 px-4 text-center text-outline">5</td>
<td class="py-3 px-4 font-mono font-medium text-primary">003005</td>
<td class="py-3 px-4 font-medium text-on-surface">Pháp luật đại cương</td>
<td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-surface-container text-on-secondary-container font-medium text-[11px]">Bắt buộc</span></td>
<td class="py-3 px-4 text-center font-semibold">2 <span class="text-outline font-normal text-[11px]">(2 - 0)</span></td>
<td class="py-3 px-4 text-outline italic">Không</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1 hover:text-primary rounded hover:bg-surface-container-low" title="Sửa"><span class="material-symbols-outlined text-base">edit</span></button>
<button class="p-1 hover:text-error rounded hover:bg-error-container/40" title="Xóa"><span class="material-symbols-outlined text-base">delete</span></button>
</div>
</td>
</tr>
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-3 px-4 text-center text-outline">6</td>
<td class="py-3 px-4 font-mono font-medium text-primary">004011</td>
<td class="py-3 px-4 font-medium text-on-surface">Tiếng Anh 1 (General English 1)</td>
<td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-surface-container text-on-secondary-container font-medium text-[11px]">Bắt buộc</span></td>
<td class="py-3 px-4 text-center font-semibold">3 <span class="text-outline font-normal text-[11px]">(2 - 1)</span></td>
<td class="py-3 px-4 text-outline italic">Không</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1 hover:text-primary rounded hover:bg-surface-container-low" title="Sửa"><span class="material-symbols-outlined text-base">edit</span></button>
<button class="p-1 hover:text-error rounded hover:bg-error-container/40" title="Xóa"><span class="material-symbols-outlined text-base">delete</span></button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<!-- Summary row -->
<div class="p-3 bg-surface-container-lowest border-t border-outline-variant/60 flex items-center justify-between text-xs text-on-surface-variant px-4">
<span>Đang hiển thị 6/11 học phần thuộc Khối Đại cương</span>
<span class="font-semibold text-on-surface">Tổng cộng khối: <strong class="text-primary font-bold">32 Tín chỉ</strong> (Lý thuyết: 28, Thực hành: 4)</span>
</div>
</div>
<!-- ---------------- KHỐI 2: KHỐI CƠ SỞ NGÀNH ---------------- -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
<!-- Header Khối -->
<div class="p-4 bg-surface-container-low/40 border-b border-outline-variant/70 flex items-center justify-between">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">
                  2
                </div>
<div>
<div class="flex items-center gap-2.5">
<h3 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Khối Cơ sở ngành</h3>
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#DCFCE7] text-[#15803D] border border-[#BBF7D0]">
<span class="material-symbols-outlined text-xs">check_circle</span>
                      Đạt chuẩn 36 TC
                    </span>
</div>
<p class="text-xs text-on-surface-variant mt-0.5">Kiến thức nền tảng bắt buộc của nhóm ngành Máy tính và Công nghệ thông tin</p>
</div>
</div>
<div class="flex items-center gap-2">
<button class="h-8 px-3 rounded-lg bg-surface-container-lowest border border-outline-variant hover:bg-surface-container-low text-primary text-xs font-semibold flex items-center gap-1.5 transition-colors">
<span class="material-symbols-outlined text-sm">add</span>
<span>Thêm học phần vào khối Cơ sở ngành</span>
</button>
<span class="material-symbols-outlined text-outline cursor-pointer p-1">expand_less</span>
</div>
</div>
<!-- Bảng dữ liệu Khối 2 -->
<div class="overflow-x-auto">
<table class="w-full text-left text-xs border-collapse">
<thead>
<tr class="bg-surface-container-low text-outline uppercase font-label-md tracking-wider border-b border-outline-variant/60">
<th class="py-2.5 px-4 w-12 text-center">STT</th>
<th class="py-2.5 px-4 w-28">Mã HP</th>
<th class="py-2.5 px-4 min-w-[220px]">Tên học phần</th>
<th class="py-2.5 px-4 w-28">Tính chất</th>
<th class="py-2.5 px-4 w-32 text-center">Số TC (LT-TH)</th>
<th class="py-2.5 px-4 min-w-[140px]">Tiên quyết</th>
<th class="py-2.5 px-4 w-24 text-center">Thao tác</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant/40">
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-3 px-4 text-center text-outline">1</td>
<td class="py-3 px-4 font-mono font-medium text-primary">010101</td>
<td class="py-3 px-4 font-medium text-on-surface">Kỹ thuật lập trình (C/C++)</td>
<td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-surface-container text-on-secondary-container font-medium text-[11px]">Bắt buộc</span></td>
<td class="py-3 px-4 text-center font-semibold">4 <span class="text-outline font-normal text-[11px]">(3 - 1)</span></td>
<td class="py-3 px-4 text-outline italic">Không</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1 hover:text-primary rounded hover:bg-surface-container-low" title="Sửa"><span class="material-symbols-outlined text-base">edit</span></button>
<button class="p-1 hover:text-error rounded hover:bg-error-container/40" title="Xóa"><span class="material-symbols-outlined text-base">delete</span></button>
</div>
</td>
</tr>
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-3 px-4 text-center text-outline">2</td>
<td class="py-3 px-4 font-mono font-medium text-primary">010102</td>
<td class="py-3 px-4 font-medium text-on-surface">Cấu trúc dữ liệu &amp; Giải thuật</td>
<td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-surface-container text-on-secondary-container font-medium text-[11px]">Bắt buộc</span></td>
<td class="py-3 px-4 text-center font-semibold">4 <span class="text-outline font-normal text-[11px]">(3 - 1)</span></td>
<td class="py-3 px-4 text-on-surface-variant font-mono">010101</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1 hover:text-primary rounded hover:bg-surface-container-low" title="Sửa"><span class="material-symbols-outlined text-base">edit</span></button>
<button class="p-1 hover:text-error rounded hover:bg-error-container/40" title="Xóa"><span class="material-symbols-outlined text-base">delete</span></button>
</div>
</td>
</tr>
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-3 px-4 text-center text-outline">3</td>
<td class="py-3 px-4 font-mono font-medium text-primary">010105</td>
<td class="py-3 px-4 font-medium text-on-surface">Kiến trúc máy tính &amp; Hợp ngữ</td>
<td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-surface-container text-on-secondary-container font-medium text-[11px]">Bắt buộc</span></td>
<td class="py-3 px-4 text-center font-semibold">3 <span class="text-outline font-normal text-[11px]">(3 - 0)</span></td>
<td class="py-3 px-4 text-outline italic">Không</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1 hover:text-primary rounded hover:bg-surface-container-low" title="Sửa"><span class="material-symbols-outlined text-base">edit</span></button>
<button class="p-1 hover:text-error rounded hover:bg-error-container/40" title="Xóa"><span class="material-symbols-outlined text-base">delete</span></button>
</div>
</td>
</tr>
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-3 px-4 text-center text-outline">4</td>
<td class="py-3 px-4 font-mono font-medium text-primary">010108</td>
<td class="py-3 px-4 font-medium text-on-surface">Hệ điều hành</td>
<td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-surface-container text-on-secondary-container font-medium text-[11px]">Bắt buộc</span></td>
<td class="py-3 px-4 text-center font-semibold">3 <span class="text-outline font-normal text-[11px]">(2 - 1)</span></td>
<td class="py-3 px-4 text-on-surface-variant font-mono">010105</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1 hover:text-primary rounded hover:bg-surface-container-low" title="Sửa"><span class="material-symbols-outlined text-base">edit</span></button>
<button class="p-1 hover:text-error rounded hover:bg-error-container/40" title="Xóa"><span class="material-symbols-outlined text-base">delete</span></button>
</div>
</td>
</tr>
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-3 px-4 text-center text-outline">5</td>
<td class="py-3 px-4 font-mono font-medium text-primary">010112</td>
<td class="py-3 px-4 font-medium text-on-surface">Mạng máy tính căn bản</td>
<td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-surface-container text-on-secondary-container font-medium text-[11px]">Bắt buộc</span></td>
<td class="py-3 px-4 text-center font-semibold">3 <span class="text-outline font-normal text-[11px]">(2 - 1)</span></td>
<td class="py-3 px-4 text-outline italic">Không</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1 hover:text-primary rounded hover:bg-surface-container-low" title="Sửa"><span class="material-symbols-outlined text-base">edit</span></button>
<button class="p-1 hover:text-error rounded hover:bg-error-container/40" title="Xóa"><span class="material-symbols-outlined text-base">delete</span></button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<div class="p-3 bg-surface-container-lowest border-t border-outline-variant/60 flex items-center justify-between text-xs text-on-surface-variant px-4">
<span>Đang hiển thị 5/10 học phần thuộc Khối Cơ sở ngành</span>
<span class="font-semibold text-on-surface">Tổng cộng khối: <strong class="text-primary font-bold">36 Tín chỉ</strong></span>
</div>
</div>
<!-- ---------------- KHỐI 3: KHỐI CHUYÊN NGÀNH (CHƯA ĐẠT - CẢNH BÁO) ---------------- -->
<div class="bg-surface-container-lowest rounded-xl border-2 border-amber-300 shadow-sm overflow-hidden ring-1 ring-amber-200">
<!-- Header Khối -->
<div class="p-4 bg-amber-50/70 border-b border-amber-200 flex items-center justify-between">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                  3
                </div>
<div>
<div class="flex items-center gap-2.5">
<h3 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Khối Chuyên ngành</h3>
<!-- Badge cảnh báo cam -->
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FEF3C7] text-[#B45309] border border-[#FDE68A]">
<span class="material-symbols-outlined text-xs">warning</span>
                      Hiện có 36/42 TC - Còn thiếu 6 TC
                    </span>
</div>
<p class="text-xs text-on-surface-variant mt-0.5">Yêu cầu tiêu chuẩn CTĐT: 42 tín chỉ chuyên ngành chuyên sâu</p>
</div>
</div>
<div class="flex items-center gap-2">
<button class="h-8 px-3 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold flex items-center gap-1.5 transition-colors shadow-sm">
<span class="material-symbols-outlined text-sm">add_circle</span>
<span>+ Thêm học phần vào khối Chuyên ngành</span>
</button>
<span class="material-symbols-outlined text-outline cursor-pointer p-1">expand_less</span>
</div>
</div>
<!-- Cảnh báo Inline màu cam -->
<div class="mx-4 my-3 p-3 bg-[#FFFBEB] border-l-4 border-amber-500 rounded-r-lg flex items-center gap-3 text-amber-900 text-xs">
<span class="material-symbols-outlined text-amber-700 text-lg flex-shrink-0">report_problem</span>
<p class="leading-relaxed font-medium">
<strong>Lưu ý kiểm định:</strong> Cần bổ sung thêm ít nhất 2 học phần (tổng 6 tín chỉ) vào khối Chuyên ngành bắt buộc hoặc chuyên sâu trước khi có thể ban hành chính thức cho Khóa 2024.
              </p>
</div>
<!-- Bảng dữ liệu Khối 3 -->
<div class="overflow-x-auto">
<table class="w-full text-left text-xs border-collapse">
<thead>
<tr class="bg-surface-container-low text-outline uppercase font-label-md tracking-wider border-b border-outline-variant/60">
<th class="py-2.5 px-4 w-12 text-center">STT</th>
<th class="py-2.5 px-4 w-28">Mã HP</th>
<th class="py-2.5 px-4 min-w-[220px]">Tên học phần</th>
<th class="py-2.5 px-4 w-28">Tính chất</th>
<th class="py-2.5 px-4 w-32 text-center">Số TC (LT-TH)</th>
<th class="py-2.5 px-4 min-w-[140px]">Tiên quyết</th>
<th class="py-2.5 px-4 w-24 text-center">Thao tác</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant/40">
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-3 px-4 text-center text-outline">1</td>
<td class="py-3 px-4 font-mono font-medium text-primary">010201</td>
<td class="py-3 px-4 font-medium text-on-surface">Lập trình Web &amp; Ứng dụng</td>
<td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-surface-container text-on-secondary-container font-medium text-[11px]">Bắt buộc</span></td>
<td class="py-3 px-4 text-center font-semibold">4 <span class="text-outline font-normal text-[11px]">(3 - 1)</span></td>
<td class="py-3 px-4 text-on-surface-variant font-mono">010101</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1 hover:text-primary rounded hover:bg-surface-container-low" title="Sửa"><span class="material-symbols-outlined text-base">edit</span></button>
<button class="p-1 hover:text-error rounded hover:bg-error-container/40" title="Xóa"><span class="material-symbols-outlined text-base">delete</span></button>
</div>
</td>
</tr>
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-3 px-4 text-center text-outline">2</td>
<td class="py-3 px-4 font-mono font-medium text-primary">010206</td>
<td class="py-3 px-4 font-medium text-on-surface">Cơ sở dữ liệu phân tán &amp; NoSQL</td>
<td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-surface-container text-on-secondary-container font-medium text-[11px]">Bắt buộc</span></td>
<td class="py-3 px-4 text-center font-semibold">3 <span class="text-outline font-normal text-[11px]">(2 - 1)</span></td>
<td class="py-3 px-4 text-on-surface-variant font-mono">010102</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1 hover:text-primary rounded hover:bg-surface-container-low" title="Sửa"><span class="material-symbols-outlined text-base">edit</span></button>
<button class="p-1 hover:text-error rounded hover:bg-error-container/40" title="Xóa"><span class="material-symbols-outlined text-base">delete</span></button>
</div>
</td>
</tr>
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-3 px-4 text-center text-outline">3</td>
<td class="py-3 px-4 font-mono font-medium text-primary">010214</td>
<td class="py-3 px-4 font-medium text-on-surface">Phát triển ứng dụng di động (Flutter / React Native)</td>
<td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-surface-container text-on-secondary-container font-medium text-[11px]">Bắt buộc</span></td>
<td class="py-3 px-4 text-center font-semibold">3 <span class="text-outline font-normal text-[11px]">(2 - 1)</span></td>
<td class="py-3 px-4 text-on-surface-variant font-mono">010201</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1 hover:text-primary rounded hover:bg-surface-container-low" title="Sửa"><span class="material-symbols-outlined text-base">edit</span></button>
<button class="p-1 hover:text-error rounded hover:bg-error-container/40" title="Xóa"><span class="material-symbols-outlined text-base">delete</span></button>
</div>
</td>
</tr>
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-3 px-4 text-center text-outline">4</td>
<td class="py-3 px-4 font-mono font-medium text-primary">010219</td>
<td class="py-3 px-4 font-medium text-on-surface">Trí tuệ nhân tạo căn bản</td>
<td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-surface-container text-on-secondary-container font-medium text-[11px]">Bắt buộc</span></td>
<td class="py-3 px-4 text-center font-semibold">3 <span class="text-outline font-normal text-[11px]">(2 - 1)</span></td>
<td class="py-3 px-4 text-on-surface-variant font-mono">010102</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1 hover:text-primary rounded hover:bg-surface-container-low" title="Sửa"><span class="material-symbols-outlined text-base">edit</span></button>
<button class="p-1 hover:text-error rounded hover:bg-error-container/40" title="Xóa"><span class="material-symbols-outlined text-base">delete</span></button>
</div>
</td>
</tr>
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-3 px-4 text-center text-outline">5</td>
<td class="py-3 px-4 font-mono font-medium text-primary">010223</td>
<td class="py-3 px-4 font-medium text-on-surface">An toàn &amp; Bảo mật hệ thống thông tin</td>
<td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-surface-container text-on-secondary-container font-medium text-[11px]">Bắt buộc</span></td>
<td class="py-3 px-4 text-center font-semibold">3 <span class="text-outline font-normal text-[11px]">(2 - 1)</span></td>
<td class="py-3 px-4 text-on-surface-variant font-mono">010112</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1 hover:text-primary rounded hover:bg-surface-container-low" title="Sửa"><span class="material-symbols-outlined text-base">edit</span></button>
<button class="p-1 hover:text-error rounded hover:bg-error-container/40" title="Xóa"><span class="material-symbols-outlined text-base">delete</span></button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<div class="p-3 bg-amber-50/50 border-t border-amber-200 flex items-center justify-between text-xs text-on-surface-variant px-4">
<span>Đang hiển thị 5/11 học phần thuộc Khối Chuyên ngành</span>
<span class="font-medium text-amber-900">Hiện tại: <strong class="text-amber-700 font-bold">36/42 Tín chỉ</strong> (Còn thiếu 6 TC)</span>
</div>
</div>
<!-- ---------------- KHỐI 4: KHỐI TỰ CHỌN ---------------- -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
<!-- Header Khối -->
<div class="p-4 bg-surface-container-low/40 border-b border-outline-variant/70 flex items-center justify-between">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">
                  4
                </div>
<div>
<div class="flex items-center gap-2.5">
<h3 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Khối Tự chọn</h3>
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#DCFCE7] text-[#15803D] border border-[#BBF7D0]">
<span class="material-symbols-outlined text-xs">check_circle</span>
                      Đạt chuẩn 15 TC
                    </span>
</div>
<p class="text-xs text-on-surface-variant mt-0.5">Sinh viên tích lũy tối thiểu 15 tín chỉ từ danh mục các học phần chuyên sâu định hướng nghề nghiệp</p>
</div>
</div>
<div class="flex items-center gap-2">
<button class="h-8 px-3 rounded-lg bg-surface-container-lowest border border-outline-variant hover:bg-surface-container-low text-primary text-xs font-semibold flex items-center gap-1.5 transition-colors">
<span class="material-symbols-outlined text-sm">add</span>
<span>Thêm học phần vào khối Tự chọn</span>
</button>
<span class="material-symbols-outlined text-outline cursor-pointer p-1">expand_less</span>
</div>
</div>
<!-- Bảng dữ liệu Khối 4 -->
<div class="overflow-x-auto">
<table class="w-full text-left text-xs border-collapse">
<thead>
<tr class="bg-surface-container-low text-outline uppercase font-label-md tracking-wider border-b border-outline-variant/60">
<th class="py-2.5 px-4 w-12 text-center">STT</th>
<th class="py-2.5 px-4 w-28">Mã HP</th>
<th class="py-2.5 px-4 min-w-[220px]">Tên học phần</th>
<th class="py-2.5 px-4 w-28">Tính chất</th>
<th class="py-2.5 px-4 w-32 text-center">Số TC (LT-TH)</th>
<th class="py-2.5 px-4 min-w-[140px]">Tiên quyết</th>
<th class="py-2.5 px-4 w-24 text-center">Thao tác</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant/40">
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-3 px-4 text-center text-outline">1</td>
<td class="py-3 px-4 font-mono font-medium text-primary">010311</td>
<td class="py-3 px-4 font-medium text-on-surface">Điện toán đám mây (AWS / Azure)</td>
<td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-[#E0F2FE] text-[#0369A1] font-semibold text-[11px]">Tự chọn</span></td>
<td class="py-3 px-4 text-center font-semibold">3 <span class="text-outline font-normal text-[11px]">(2 - 1)</span></td>
<td class="py-3 px-4 text-on-surface-variant font-mono">010112</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1 hover:text-primary rounded hover:bg-surface-container-low" title="Sửa"><span class="material-symbols-outlined text-base">edit</span></button>
<button class="p-1 hover:text-error rounded hover:bg-error-container/40" title="Xóa"><span class="material-symbols-outlined text-base">delete</span></button>
</div>
</td>
</tr>
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-3 px-4 text-center text-outline">2</td>
<td class="py-3 px-4 font-mono font-medium text-primary">010315</td>
<td class="py-3 px-4 font-medium text-on-surface">Xử lý ngôn ngữ tự nhiên (NLP)</td>
<td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-[#E0F2FE] text-[#0369A1] font-semibold text-[11px]">Tự chọn</span></td>
<td class="py-3 px-4 text-center font-semibold">3 <span class="text-outline font-normal text-[11px]">(2 - 1)</span></td>
<td class="py-3 px-4 text-on-surface-variant font-mono">010219</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1 hover:text-primary rounded hover:bg-surface-container-low" title="Sửa"><span class="material-symbols-outlined text-base">edit</span></button>
<button class="p-1 hover:text-error rounded hover:bg-error-container/40" title="Xóa"><span class="material-symbols-outlined text-base">delete</span></button>
</div>
</td>
</tr>
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-3 px-4 text-center text-outline">3</td>
<td class="py-3 px-4 font-mono font-medium text-primary">010320</td>
<td class="py-3 px-4 font-medium text-on-surface">Thị giác máy tính (Computer Vision)</td>
<td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-[#E0F2FE] text-[#0369A1] font-semibold text-[11px]">Tự chọn</span></td>
<td class="py-3 px-4 text-center font-semibold">3 <span class="text-outline font-normal text-[11px]">(2 - 1)</span></td>
<td class="py-3 px-4 text-on-surface-variant font-mono">010219</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1 hover:text-primary rounded hover:bg-surface-container-low" title="Sửa"><span class="material-symbols-outlined text-base">edit</span></button>
<button class="p-1 hover:text-error rounded hover:bg-error-container/40" title="Xóa"><span class="material-symbols-outlined text-base">delete</span></button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<div class="p-3 bg-surface-container-lowest border-t border-outline-variant/60 flex items-center justify-between text-xs text-on-surface-variant px-4">
<span>Đang hiển thị 3/8 học phần trong nhóm Tự chọn</span>
<span class="font-semibold text-on-surface">Tích lũy tối thiểu: <strong class="text-primary font-bold">15 Tín chỉ</strong></span>
</div>
</div>
<!-- ---------------- KHỐI 5: KHỐI THỰC TẬP & ĐỒ ÁN TỐT NGHIỆP ---------------- -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
<!-- Header Khối -->
<div class="p-4 bg-surface-container-low/40 border-b border-outline-variant/70 flex items-center justify-between">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">
                  5
                </div>
<div>
<div class="flex items-center gap-2.5">
<h3 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Khối Thực tập &amp; Đồ án tốt nghiệp</h3>
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#DCFCE7] text-[#15803D] border border-[#BBF7D0]">
<span class="material-symbols-outlined text-xs">check_circle</span>
                      Đạt chuẩn 10 TC
                    </span>
</div>
<p class="text-xs text-on-surface-variant mt-0.5">Khối kiến thức và trải nghiệm thực tế cuối khóa (Học kỳ 8)</p>
</div>
</div>
<div class="flex items-center gap-2">
<button class="h-8 px-3 rounded-lg bg-surface-container-lowest border border-outline-variant hover:bg-surface-container-low text-primary text-xs font-semibold flex items-center gap-1.5 transition-colors">
<span class="material-symbols-outlined text-sm">add</span>
<span>Thêm học phần vào khối Thực tập &amp; Đồ án</span>
</button>
<span class="material-symbols-outlined text-outline cursor-pointer p-1">expand_less</span>
</div>
</div>
<!-- Bảng dữ liệu Khối 5 -->
<div class="overflow-x-auto">
<table class="w-full text-left text-xs border-collapse">
<thead>
<tr class="bg-surface-container-low text-outline uppercase font-label-md tracking-wider border-b border-outline-variant/60">
<th class="py-2.5 px-4 w-12 text-center">STT</th>
<th class="py-2.5 px-4 w-28">Mã HP</th>
<th class="py-2.5 px-4 min-w-[220px]">Tên học phần</th>
<th class="py-2.5 px-4 w-28">Tính chất</th>
<th class="py-2.5 px-4 w-32 text-center">Số TC (LT-TH)</th>
<th class="py-2.5 px-4 min-w-[140px]">Tiên quyết</th>
<th class="py-2.5 px-4 w-24 text-center">Thao tác</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant/40">
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-3 px-4 text-center text-outline">1</td>
<td class="py-3 px-4 font-mono font-medium text-primary">010401</td>
<td class="py-3 px-4 font-medium text-on-surface">Thực tập doanh nghiệp (Internship)</td>
<td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-surface-container text-on-secondary-container font-medium text-[11px]">Bắt buộc</span></td>
<td class="py-3 px-4 text-center font-semibold">4 <span class="text-outline font-normal text-[11px]">(0 - 4)</span></td>
<td class="py-3 px-4 text-on-surface-variant font-mono">Tích lũy &gt;= 105 TC</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1 hover:text-primary rounded hover:bg-surface-container-low" title="Sửa"><span class="material-symbols-outlined text-base">edit</span></button>
<button class="p-1 hover:text-error rounded hover:bg-error-container/40" title="Xóa"><span class="material-symbols-outlined text-base">delete</span></button>
</div>
</td>
</tr>
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-3 px-4 text-center text-outline">2</td>
<td class="py-3 px-4 font-mono font-medium text-primary">010402</td>
<td class="py-3 px-4 font-medium text-on-surface">Khóa luận / Đồ án tốt nghiệp CNTT</td>
<td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-surface-container text-on-secondary-container font-medium text-[11px]">Bắt buộc</span></td>
<td class="py-3 px-4 text-center font-semibold">6 <span class="text-outline font-normal text-[11px]">(0 - 6)</span></td>
<td class="py-3 px-4 text-on-surface-variant font-mono">010401 &amp; &gt;= 115 TC</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1">
<button class="p-1 hover:text-primary rounded hover:bg-surface-container-low" title="Sửa"><span class="material-symbols-outlined text-base">edit</span></button>
<button class="p-1 hover:text-error rounded hover:bg-error-container/40" title="Xóa"><span class="material-symbols-outlined text-base">delete</span></button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<div class="p-3 bg-surface-container-lowest border-t border-outline-variant/60 flex items-center justify-between text-xs text-on-surface-variant px-4">
<span>Đang hiển thị 2/2 học phần tốt nghiệp</span>
<span class="font-semibold text-on-surface">Tổng cộng khối: <strong class="text-primary font-bold">10 Tín chỉ</strong></span>
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
