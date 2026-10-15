@extends('fe_preview.layouts.admin')
@section('page_title', 'HUIT Học Vụ - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
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
<!-- TOP ACTION BAR: Return Button & Context Navigation -->
<div class="flex items-center justify-between">
<a class="inline-flex items-center gap-1.5 text-xs font-semibold text-on-surface-variant hover:text-primary transition-colors group" href="#">
<span class="material-symbols-outlined text-base group-hover:-translate-x-0.5 transition-transform" data-icon="arrow_back">arrow_back</span>
<span>Quay lại danh sách sinh viên</span>
</a>
<div class="flex items-center gap-2 text-xs text-outline">
<span>Lần cập nhật cuối: 14:32 - 18/09/2026</span>
</div>
</div>
<!-- STUDENT HEADER CARD (MASTER SUMMARY PANEL) -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant/40 p-6 shadow-sm relative overflow-hidden">
<!-- Subtle Top Accent Line -->
<div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-primary via-primary-container to-surface-container-highest"></div>
<div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 pt-1">
<!-- Left: Identity cluster -->
<div class="flex items-start md:items-center gap-5">
<!-- Student Avatar -->
<div class="relative group flex-shrink-0">
<img alt="" class="w-20 h-20 rounded-xl object-cover ring-2 ring-primary/20 shadow-sm border border-outline-variant/30" data-alt="High quality professional front-facing studio student identification portrait photo of a 21-year-old Vietnamese male university engineering student named Người dùng mẫu 20 with smart clean-cut hairstyle and white collared shirt against a pale sky-blue studio background." src="https://lh3.googleusercontent.com/aida-public/AB6AXuAveUswnYLUDhwqYKsaL09bSoJpc5ZGfnE6BMjurEUompTHzLAXjcNwG5u2eds9kOl0jAsERT_3D360ME_PYrzNNJE60hwI7vJI0oVFW_MX3jAh7yF25n7dd1XBar9qgAw8WDQ40ZwBwvV8hGqOMyI6L4-p5YfUHoY-xgkF46tAH8g46AttE5LlBVGdNgAMnsdDP4ev2YFFEvgE9ytO-KNR0sqzrPS0ptLSCv84iagfiayD-3CqXI1Fed_XMXq1AiF47n_k0s6IMzA"/>
<button class="absolute -bottom-1 -right-1 bg-surface-container-lowest text-primary p-1 rounded-md shadow-md border border-outline-variant/40 hover:bg-surface-container-low transition-colors" title="Cập nhật ảnh đại diện thẻ" type="button">
<span class="material-symbols-outlined text-xs" data-icon="photo_camera">photo_camera</span>
</button>
</div>
<!-- Student Bio & Class -->
<div class="space-y-1.5">
<div class="flex flex-wrap items-center gap-3">
<h1 class="font-headline-xl text-headline-xl font-bold text-on-background tracking-tight">Người dùng mẫu 25</h1>
<!-- Student Status Badge -->
<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                  Đang học (Active)
                </span>
<span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-surface-container-high text-on-surface-variant">
                  Chính quy 4 năm
                </span>
</div>
<!-- Metadata Grid -->
<div class="flex flex-wrap items-center gap-y-1 gap-x-4 text-xs text-on-surface-variant">
<div class="flex items-center gap-1">
<span class="text-outline">MSSV:</span>
<span class="font-bold text-primary font-mono select-all bg-surface-container-low px-1.5 py-0.5 rounded border border-outline-variant/30">2001210123</span>
</div>
<span>•</span>
<div class="flex items-center gap-1">
<span class="text-outline">Lớp hành chính:</span>
<strong class="text-on-surface font-semibold">12DHTH01</strong>
</div>
<span>•</span>
<div class="flex items-center gap-1">
<span class="text-outline">Ngành:</span>
<span class="font-semibold text-on-surface">Công nghệ Thông tin</span>
<span class="text-outline-variant">(Khoa CNTT)</span>
</div>
<span>•</span>
<div class="flex items-center gap-1">
<span class="text-outline">Khóa:</span>
<span>Khóa 2021 (K21 - ĐHCQ)</span>
</div>
</div>
</div>
</div>
<!-- Right: Key Metric Badges & Action Buttons -->
<div class="flex flex-col sm:flex-row lg:flex-col xl:flex-row items-end lg:items-end xl:items-center gap-4 w-full lg:w-auto justify-between lg:justify-end border-t lg:border-t-0 pt-4 lg:pt-0 border-outline-variant/30">
<!-- Micro Stat Cards -->
<div class="flex items-center gap-3">
<!-- GPA Stat -->
<div class="px-3.5 py-2 rounded-lg bg-surface-container-low border border-outline-variant/30 text-center min-w-[90px]">
<div class="text-[11px] font-medium text-outline uppercase tracking-wider">GPA Tích lũy</div>
<div class="text-lg font-bold text-primary leading-tight">3.42 <span class="text-xs font-normal text-outline">/ 4.0</span></div>
<div class="text-[10px] font-semibold text-emerald-700">Loại Giỏi</div>
</div>
<!-- Credits Stat -->
<div class="px-3.5 py-2 rounded-lg bg-surface-container-low border border-outline-variant/30 text-center min-w-[90px]">
<div class="text-[11px] font-medium text-outline uppercase tracking-wider">Tín chỉ</div>
<div class="text-lg font-bold text-on-surface leading-tight">108 <span class="text-xs font-normal text-outline">/ 135</span></div>
<div class="text-[10px] font-semibold text-primary">Tiến độ 80%</div>
</div>
<!-- Warning Stat -->
<div class="px-3.5 py-2 rounded-lg bg-surface-container-low border border-outline-variant/30 text-center min-w-[85px]">
<div class="text-[11px] font-medium text-outline uppercase tracking-wider">Cảnh báo</div>
<div class="text-lg font-bold text-emerald-700 leading-tight">0 <span class="text-xs font-normal text-outline">lần</span></div>
<div class="text-[10px] font-medium text-outline">Bình thường</div>
</div>
</div>
<!-- Action Buttons Group -->
<div class="flex flex-wrap items-center gap-2">
<button class="px-3 py-2 text-xs font-semibold text-on-surface bg-surface-container-lowest border border-outline-variant hover:bg-surface-container-low rounded-lg inline-flex items-center gap-1.5 transition-colors shadow-xs active:scale-[0.98]" type="button">
<span class="material-symbols-outlined text-base text-secondary" data-icon="sync_alt">sync_alt</span>
<span>Đổi trạng thái</span>
</button>
<button class="px-3 py-2 text-xs font-semibold text-on-surface bg-surface-container-lowest border border-outline-variant hover:bg-surface-container-low rounded-lg inline-flex items-center gap-1.5 transition-colors shadow-xs active:scale-[0.98]" type="button">
<span class="material-symbols-outlined text-base text-secondary" data-icon="print">print</span>
<span>In hồ sơ</span>
</button>
<button class="px-3.5 py-2 text-xs font-semibold text-white bg-primary hover:bg-primary-container rounded-lg inline-flex items-center gap-1.5 transition-colors shadow-xs active:scale-[0.98]" type="button">
<span class="material-symbols-outlined text-base" data-icon="edit">edit</span>
<span>Chỉnh sửa hồ sơ</span>
</button>
</div>
</div>
</div>
</div>
<!-- 4 TABS NAVIGATION BAR -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant/40 shadow-xs px-4">
<nav aria-label="Hồ sơ tabs" class="flex items-center gap-6 overflow-x-auto">
<!-- Tab 1: Active -->
<button class="py-3.5 px-1 font-semibold text-xs border-b-2 border-primary text-primary flex items-center gap-2 whitespace-nowrap" type="button">
<span class="material-symbols-outlined text-lg" data-icon="person" style="font-variation-settings: 'FILL' 1;">person</span>
<span>1. Thông tin cá nhân</span>
</button>
<!-- Tab 2 -->
<button class="py-3.5 px-1 font-medium text-xs border-b-2 border-transparent text-on-surface-variant hover:text-on-surface hover:border-outline-variant flex items-center gap-2 whitespace-nowrap transition-colors" type="button">
<span class="material-symbols-outlined text-lg text-outline" data-icon="school">school</span>
<span>2. Tiến độ học tập &amp; CTĐT</span>
<span class="px-1.5 py-0.2 bg-surface-container-high text-primary rounded-full text-[10px] font-bold">80%</span>
</button>
<!-- Tab 3 -->
<button class="py-3.5 px-1 font-medium text-xs border-b-2 border-transparent text-on-surface-variant hover:text-on-surface hover:border-outline-variant flex items-center gap-2 whitespace-nowrap transition-colors" type="button">
<span class="material-symbols-outlined text-lg text-outline" data-icon="how_to_reg">how_to_reg</span>
<span>3. Đăng ký học phần (HK1 2026-2027)</span>
<span class="px-1.5 py-0.2 bg-surface-container-low text-secondary rounded-full text-[10px] font-bold">6 lớp</span>
</button>
<!-- Tab 4 -->
<button class="py-3.5 px-1 font-medium text-xs border-b-2 border-transparent text-on-surface-variant hover:text-on-surface hover:border-outline-variant flex items-center gap-2 whitespace-nowrap transition-colors" type="button">
<span class="material-symbols-outlined text-lg text-outline" data-icon="grade">grade</span>
<span>4. Kết quả &amp; Điểm học phần</span>
<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
</button>
</nav>
</div>
<!-- MAIN TAB CONTENT: 2-COLUMN STRUCTURE (Personal Details + Academic Quick Transcript) -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
<!-- LEFT COLUMN (7 Cols): 3 Personal Information Data Blocks -->
<div class="lg:col-span-7 space-y-6">
<!-- BLOCK 1: THÔNG TIN CƠ BẢN -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant/40 p-5 shadow-sm space-y-4">
<div class="flex items-center justify-between pb-3 border-b border-outline-variant/30">
<div class="flex items-center gap-2.5">
<span class="p-1.5 rounded-lg bg-surface-container text-primary">
<span class="material-symbols-outlined text-lg" data-icon="badge">badge</span>
</span>
<h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">Thông tin cơ bản sinh viên</h2>
</div>
<button class="text-xs font-semibold text-primary hover:text-primary-container inline-flex items-center gap-1 transition-colors" type="button">
<span class="material-symbols-outlined text-sm" data-icon="file_upload">file_upload</span>
                Cập nhật ảnh thẻ
              </button>
</div>
<!-- View-mode Field Grid (3 Columns) -->
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-y-4 gap-x-6 text-xs">
<div>
<label class="block text-outline font-medium text-[11px] mb-0.5">Họ và tên khai sinh</label>
<div class="font-semibold text-on-surface text-sm">Người dùng mẫu 25</div>
</div>
<div>
<label class="block text-outline font-medium text-[11px] mb-0.5">Ngày sinh</label>
<div class="font-semibold text-on-surface">15/08/2003</div>
</div>
<div>
<label class="block text-outline font-medium text-[11px] mb-0.5">Giới tính</label>
<div class="font-semibold text-on-surface">Nam</div>
</div>
<div class="sm:col-span-2">
<label class="block text-outline font-medium text-[11px] mb-0.5">Số CCCD / Mã định danh</label>
<div class="font-semibold text-on-surface font-mono">
                  079203001234
                  <span class="font-normal text-[11px] text-outline ml-1">(Cấp ngày: 12/04/2021 tại Cục CSQLHC về TTXH)</span>
</div>
</div>
<div>
<label class="block text-outline font-medium text-[11px] mb-0.5">Dân tộc</label>
<div class="font-semibold text-on-surface">Kinh</div>
</div>
<div>
<label class="block text-outline font-medium text-[11px] mb-0.5">Tôn giáo</label>
<div class="font-semibold text-on-surface">Không</div>
</div>
<div>
<label class="block text-outline font-medium text-[11px] mb-0.5">Quốc tịch</label>
<div class="font-semibold text-on-surface">Việt Nam</div>
</div>
<div>
<label class="block text-outline font-medium text-[11px] mb-0.5">Nơi sinh (Tỉnh/TP)</label>
<div class="font-semibold text-on-surface">TP. Hồ Chí Minh</div>
</div>
<div class="sm:col-span-3 pt-1">
<label class="block text-outline font-medium text-[11px] mb-0.5">Đoàn thể chính trị</label>
<div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-surface-container text-primary font-medium text-xs">
<span class="material-symbols-outlined text-sm text-primary" data-icon="flag">flag</span>
                  Đoàn viên Đoàn TNCS Hồ Chí Minh (Ngày vào đoàn: 26/03/2019)
                </div>
</div>
</div>
</div>
<!-- BLOCK 2: THÔNG TIN LIÊN LẠC & ĐỊA CHỈ -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant/40 p-5 shadow-sm space-y-4">
<div class="flex items-center justify-between pb-3 border-b border-outline-variant/30">
<div class="flex items-center gap-2.5">
<span class="p-1.5 rounded-lg bg-surface-container text-primary">
<span class="material-symbols-outlined text-lg" data-icon="contacts">contacts</span>
</span>
<h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">Thông tin liên lạc &amp; Địa chỉ cư trú</h2>
</div>
<span class="text-[11px] text-emerald-700 font-semibold flex items-center gap-1">
<span class="material-symbols-outlined text-sm" data-icon="verified">verified</span>
                Đã xác minh định danh
              </span>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
<!-- Trường Email -->
<div class="p-3 rounded-lg bg-surface-container-low/60 border border-outline-variant/30">
<div class="text-[11px] text-outline font-medium mb-1 flex items-center gap-1">
<span class="material-symbols-outlined text-xs" data-icon="mail">mail</span>
                  Email sinh viên (Trường HUIT cấp)
                </div>
<div class="font-semibold text-primary select-all font-mono">nguoidung006@example.test</div>
<span class="inline-block mt-1 text-[10px] text-emerald-700 bg-emerald-50 px-1.5 py-0.2 rounded font-semibold">Đã liên kết SSO Office365</span>
</div>
<!-- Email cá nhân -->
<div class="p-3 rounded-lg bg-surface-container-low/60 border border-outline-variant/30">
<div class="text-[11px] text-outline font-medium mb-1 flex items-center gap-1">
<span class="material-symbols-outlined text-xs" data-icon="alternate_email">alternate_email</span>
                  Email cá nhân
                </div>
<div class="font-semibold text-on-surface font-mono">nguoidung017@example.test</div>
<span class="inline-block mt-1 text-[10px] text-secondary bg-surface-container px-1.5 py-0.2 rounded font-medium">Email dự phòng kích hoạt</span>
</div>
<!-- Điện thoại di động -->
<div class="p-3 rounded-lg bg-surface-container-low/60 border border-outline-variant/30 sm:col-span-2">
<div class="flex items-center justify-between">
<div>
<div class="text-[11px] text-outline font-medium mb-1 flex items-center gap-1">
<span class="material-symbols-outlined text-xs" data-icon="phone_android">phone_android</span>
                      Số điện thoại di động
                    </div>
<div class="font-semibold text-on-surface text-sm font-mono">0912 345 678</div>
</div>
<span class="text-[11px] text-on-surface-variant bg-surface-container px-2 py-0.5 rounded font-medium">Chính chủ Viettel</span>
</div>
</div>
<!-- Địa chỉ thường trú -->
<div class="sm:col-span-2 space-y-1">
<label class="block text-outline font-medium text-[11px]">Địa chỉ thường trú (Hộ khẩu)</label>
<div class="p-2.5 rounded-lg border border-outline-variant/30 bg-surface-container-lowest font-medium text-on-surface leading-relaxed">
                  Số 140 Người dùng mẫu 18, Phường Tây Thạnh, Quận Tân Phú, TP. Hồ Chí Minh
                </div>
</div>
<!-- Địa chỉ tạm trú -->
<div class="sm:col-span-2 space-y-1">
<div class="flex items-center justify-between">
<label class="block text-outline font-medium text-[11px]">Địa chỉ tạm trú / Nơi ở hiện tại</label>
<span class="text-[11px] text-primary font-medium">Khu vực gần cơ sở chính HUIT</span>
</div>
<div class="p-2.5 rounded-lg border border-outline-variant/30 bg-surface-container-lowest font-medium text-on-surface leading-relaxed">
                  25/3 đường CN13, Phường Sơn Kỳ, Quận Tân Phú, TP. Hồ Chí Minh
                </div>
<p class="text-[11px] text-outline italic">Hình thức nơi ở: Nhà trọ ngoài trường (Có đăng ký tạm trú với Công an Phường Sơn Kỳ).</p>
</div>
</div>
</div>
<!-- BLOCK 3: THÔNG TIN GIA ĐÌNH & KHẨN CẤP -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant/40 p-5 shadow-sm space-y-4">
<div class="flex items-center justify-between pb-3 border-b border-outline-variant/30">
<div class="flex items-center gap-2.5">
<span class="p-1.5 rounded-lg bg-surface-container text-primary">
<span class="material-symbols-outlined text-lg" data-icon="family_restroom">family_restroom</span>
</span>
<h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">Thông tin gia đình &amp; Liên hệ khẩn cấp</h2>
</div>
<span class="text-xs text-outline">2 Phụ huynh ghi nhận</span>
</div>
<!-- Parent Rows Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
<!-- Thông tin Cha -->
<div class="border border-outline-variant/30 rounded-lg p-3 space-y-2 bg-surface-container-lowest">
<div class="flex items-center justify-between text-outline">
<span class="font-bold text-on-surface text-[12px]">Họ tên Cha</span>
<span class="text-[11px] bg-surface-container px-1.5 py-0.5 rounded">Sinh năm: 1975</span>
</div>
<div class="font-semibold text-sm text-on-surface">Người dùng mẫu 26</div>
<div class="text-on-surface-variant flex items-center gap-1.5">
<span class="material-symbols-outlined text-sm text-outline" data-icon="engineering">engineering</span>
                  Nghề nghiệp: Kỹ sư xây dựng
                </div>
<div class="text-primary font-mono font-semibold flex items-center gap-1.5 pt-1 border-t border-outline-variant/20">
<span class="material-symbols-outlined text-sm" data-icon="call">call</span>
                  0903 111 222
                </div>
</div>
<!-- Thông tin Mẹ -->
<div class="border border-outline-variant/30 rounded-lg p-3 space-y-2 bg-surface-container-lowest">
<div class="flex items-center justify-between text-outline">
<span class="font-bold text-on-surface text-[12px]">Họ tên Mẹ</span>
<span class="text-[11px] bg-surface-container px-1.5 py-0.5 rounded">Sinh năm: 1978</span>
</div>
<div class="font-semibold text-sm text-on-surface">Người dùng mẫu 42</div>
<div class="text-on-surface-variant flex items-center gap-1.5">
<span class="material-symbols-outlined text-sm text-outline" data-icon="school">school</span>
                  Nghề nghiệp: Giáo viên THPT
                </div>
<div class="text-primary font-mono font-semibold flex items-center gap-1.5 pt-1 border-t border-outline-variant/20">
<span class="material-symbols-outlined text-sm" data-icon="call">call</span>
                  0908 333 444
                </div>
</div>
<!-- Khẩn cấp Highlight Box -->
<div class="sm:col-span-2 p-3 rounded-lg bg-amber-50/70 border border-amber-200/80 flex items-start gap-3">
<span class="material-symbols-outlined text-amber-700 text-xl flex-shrink-0" data-icon="emergency">emergency</span>
<div class="space-y-0.5 text-xs">
<div class="font-bold text-amber-900">Người chỉ định liên hệ khẩn cấp khi cần báo tin học vụ:</div>
<div class="text-amber-800">
<strong>Người dùng mẫu 26</strong> (Quan hệ: Phụ huynh - Cha) • SĐT khẩn cấp: <strong class="font-mono text-amber-950 font-bold">0903 111 222</strong>
</div>
<div class="text-[11px] text-amber-700">Địa chỉ liên hệ: 140 Người dùng mẫu 18, P. Tây Thạnh, Q. Tân Phú, TP.HCM</div>
</div>
</div>
</div>
</div>
</div>
<!-- RIGHT COLUMN (5 Cols): Academic Summary & Latest Transcript Preview Table -->
<div class="lg:col-span-5 space-y-6">
<!-- CARD: BẢNG TỔNG HỢP HỌC LỰC & BẢNG ĐIỂM GẦN NHẤT -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant/40 shadow-sm overflow-hidden">
<!-- Header -->
<div class="p-5 border-b border-outline-variant/30 flex items-center justify-between bg-surface-container-lowest">
<div class="flex items-center gap-2">
<span class="p-1.5 rounded-lg bg-surface-container text-primary">
<span class="material-symbols-outlined text-lg" data-icon="analytics">analytics</span>
</span>
<div>
<h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">Tóm tắt Kết quả Học tập</h2>
<p class="text-[11px] text-on-surface-variant">Bảng điểm Học kỳ 2 (2023 - 2024)</p>
</div>
</div>
<a class="text-xs font-semibold text-primary hover:underline flex items-center gap-0.5" href="#">
                Xem toàn khóa
                <span class="material-symbols-outlined text-sm" data-icon="chevron_right">chevron_right</span>
</a>
</div>
<!-- Quick Metrics Header Pill Grid -->
<div class="grid grid-cols-3 divide-x divide-outline-variant/30 bg-surface-container-low/40 border-b border-outline-variant/30 p-3 text-center text-xs">
<div>
<div class="text-[10px] text-outline font-medium">GPA Kỳ trước</div>
<div class="text-base font-bold text-primary">3.42</div>
<div class="text-[10px] text-on-surface-variant">Thang 4.0</div>
</div>
<div>
<div class="text-[10px] text-outline font-medium">GPA Tích lũy</div>
<div class="text-base font-bold text-on-surface">3.38</div>
<div class="text-[10px] text-emerald-700 font-semibold">Loại Giỏi</div>
</div>
<div>
<div class="text-[10px] text-outline font-medium">TC Đã tích lũy</div>
<div class="text-base font-bold text-on-surface">108 / 135</div>
<div class="text-[10px] text-primary font-semibold">Đạt 80% CTĐT</div>
</div>
</div>
<!-- Specialized Academic Data Table (condensed) -->
<div class="overflow-x-auto custom-scrollbar">
<table class="w-full text-left border-collapse text-xs">
<thead>
<tr class="bg-surface-container-low/80 text-[11px] font-semibold text-outline uppercase tracking-wider border-b border-outline-variant/40">
<th class="py-2.5 px-3">Học phần</th>
<th class="py-2.5 px-1.5 text-center">TC</th>
<th class="py-2.5 px-1.5 text-center" title="Quá trình học tập (10%)">CC</th>
<th class="py-2.5 px-1.5 text-center" title="Quá trình (40%)">QT</th>
<th class="py-2.5 px-1.5 text-center" title="Thi (50%)">Thi</th>
<th class="py-2.5 px-1.5 text-center font-bold text-primary">Hệ 10</th>
<th class="py-2.5 px-1.5 text-center font-bold">Hệ 4</th>
<th class="py-2.5 px-1.5 text-center font-bold">Chữ</th>
<th class="py-2.5 px-3 text-center">Trạng thái</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant/20">
<!-- Course 1 -->
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-2.5 px-3">
<div class="font-semibold text-on-surface text-[12px]">Lập trình Web</div>
<div class="font-mono text-[10px] text-outline">241_ITEC3201</div>
</td>
<td class="py-2.5 px-1.5 text-center font-medium">3</td>
<td class="py-2.5 px-1.5 text-center text-on-surface-variant font-mono">9.0</td>
<td class="py-2.5 px-1.5 text-center text-on-surface-variant font-mono">8.5</td>
<td class="py-2.5 px-1.5 text-center text-on-surface-variant font-mono">8.5</td>
<td class="py-2.5 px-1.5 text-center font-bold text-primary font-mono">8.6</td>
<td class="py-2.5 px-1.5 text-center font-semibold font-mono">3.7</td>
<td class="py-2.5 px-1.5 text-center font-bold text-emerald-700">A</td>
<td class="py-2.5 px-3 text-center">
<span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Đạt</span>
</td>
</tr>
<!-- Course 2 -->
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-2.5 px-3">
<div class="font-semibold text-on-surface text-[12px]">Cơ sở dữ liệu phân tán</div>
<div class="font-mono text-[10px] text-outline">241_ITEC3202</div>
</td>
<td class="py-2.5 px-1.5 text-center font-medium">3</td>
<td class="py-2.5 px-1.5 text-center text-on-surface-variant font-mono">8.5</td>
<td class="py-2.5 px-1.5 text-center text-on-surface-variant font-mono">8.0</td>
<td class="py-2.5 px-1.5 text-center text-on-surface-variant font-mono">7.5</td>
<td class="py-2.5 px-1.5 text-center font-bold text-primary font-mono">7.8</td>
<td class="py-2.5 px-1.5 text-center font-semibold font-mono">3.0</td>
<td class="py-2.5 px-1.5 text-center font-bold text-on-surface">B</td>
<td class="py-2.5 px-3 text-center">
<span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Đạt</span>
</td>
</tr>
<!-- Course 3 -->
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-2.5 px-3">
<div class="font-semibold text-on-surface text-[12px]">Lập trình Mạng máy tính</div>
<div class="font-mono text-[10px] text-outline">241_ITEC3203</div>
</td>
<td class="py-2.5 px-1.5 text-center font-medium">3</td>
<td class="py-2.5 px-1.5 text-center text-on-surface-variant font-mono">10.0</td>
<td class="py-2.5 px-1.5 text-center text-on-surface-variant font-mono">8.5</td>
<td class="py-2.5 px-1.5 text-center text-on-surface-variant font-mono">9.0</td>
<td class="py-2.5 px-1.5 text-center font-bold text-primary font-mono">9.0</td>
<td class="py-2.5 px-1.5 text-center font-semibold font-mono">4.0</td>
<td class="py-2.5 px-1.5 text-center font-bold text-emerald-700">A+</td>
<td class="py-2.5 px-3 text-center">
<span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Đạt</span>
</td>
</tr>
<!-- Course 4 -->
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-2.5 px-3">
<div class="font-semibold text-on-surface text-[12px]">An toàn thông tin mạng</div>
<div class="font-mono text-[10px] text-outline">241_ITEC2102</div>
</td>
<td class="py-2.5 px-1.5 text-center font-medium">3</td>
<td class="py-2.5 px-1.5 text-center text-on-surface-variant font-mono">9.0</td>
<td class="py-2.5 px-1.5 text-center text-on-surface-variant font-mono">7.5</td>
<td class="py-2.5 px-1.5 text-center text-on-surface-variant font-mono">8.0</td>
<td class="py-2.5 px-1.5 text-center font-bold text-primary font-mono">8.0</td>
<td class="py-2.5 px-1.5 text-center font-semibold font-mono">3.5</td>
<td class="py-2.5 px-1.5 text-center font-bold text-emerald-700">B+</td>
<td class="py-2.5 px-3 text-center">
<span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Đạt</span>
</td>
</tr>
</tbody>
</table>
</div>
<!-- Table Footer Quick Summary -->
<div class="p-3 bg-surface-container-low/30 border-t border-outline-variant/30 flex items-center justify-between text-xs text-on-surface-variant">
<span>Tổng số tín chỉ học kỳ: <strong class="text-on-surface">12 TC (4 môn)</strong></span>
<span class="text-emerald-700 font-medium">Tỷ lệ qua môn: 100%</span>
</div>
</div>
<!-- CARD: CHỨNG CHỈ ĐIỀU KIỆN TỐT NGHIỆP BẮT BUỘC -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant/40 p-5 shadow-sm space-y-3.5">
<div class="flex items-center justify-between">
<div class="flex items-center gap-2">
<span class="p-1.5 rounded-lg bg-surface-container text-primary">
<span class="material-symbols-outlined text-lg" data-icon="verified_user">verified_user</span>
</span>
<h3 class="font-headline-sm text-headline-sm font-bold text-on-surface">Điều kiện chuẩn đầu ra (K21)</h3>
</div>
<span class="text-[11px] text-outline">Quy chế HUIT</span>
</div>
<div class="space-y-2.5 text-xs">
<!-- Ngoại ngữ -->
<div class="flex items-center justify-between p-2.5 rounded-lg border border-outline-variant/30 bg-surface-container-low/30">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-emerald-700 text-base" data-icon="check_circle" style="font-variation-settings: 'FILL' 1;">check_circle</span>
<div>
<span class="font-semibold text-on-surface">Tiếng Anh chuẩn đầu ra (TOEIC 500+)</span>
<div class="text-[11px] text-outline">Đã nộp chứng chỉ: TOEIC 680 (IIG cấp 11/2023)</div>
</div>
</div>
<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Đạt chuẩn</span>
</div>
<!-- Tin học -->
<div class="flex items-center justify-between p-2.5 rounded-lg border border-outline-variant/30 bg-surface-container-low/30">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-emerald-700 text-base" data-icon="check_circle" style="font-variation-settings: 'FILL' 1;">check_circle</span>
<div>
<span class="font-semibold text-on-surface">Chuẩn Tin học (MOS / CNTT Nâng cao)</span>
<div class="text-[11px] text-outline">Miễn theo quy định sinh viên ngành CNTT</div>
</div>
</div>
<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Miễn thi</span>
</div>
<!-- GDTC & GDQP -->
<div class="flex items-center justify-between p-2.5 rounded-lg border border-outline-variant/30 bg-surface-container-low/30">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-emerald-700 text-base" data-icon="check_circle" style="font-variation-settings: 'FILL' 1;">check_circle</span>
<div>
<span class="font-semibold text-on-surface">Giáo dục Thể chất &amp; GD Quốc phòng</span>
<div class="text-[11px] text-outline">Đã hoàn thành đầy đủ 4 học phần theo CTĐT</div>
</div>
</div>
<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Hoàn thành</span>
</div>
<!-- Điểm rèn luyện -->
<div class="flex items-center justify-between p-2.5 rounded-lg border border-outline-variant/30 bg-surface-container-low/30">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-base" data-icon="stars">stars</span>
<div>
<span class="font-semibold text-on-surface">Điểm rèn luyện kỳ gần nhất</span>
<div class="text-[11px] text-outline">Xếp loại: Tốt (Khoa CNTT phê duyệt)</div>
</div>
</div>
<span class="font-bold text-primary font-mono text-xs">88 / 100 đ</span>
</div>
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
