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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/dang-ky-ngoai-le']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">assignment_turned_in</span>
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
<!-- CỘT TRUNG TÂM: Bảng danh sách sinh viên đăng ký -->
<div class="flex-1 min-w-0 flex flex-col gap-4">
<!-- Thanh công cụ tìm kiếm và bộ lọc -->
<div class="bg-surface-container-lowest border border-[#e2e8f0] rounded-xl p-4 shadow-sm">
<div class="grid grid-cols-12 gap-3 items-center">
<!-- Ô tìm kiếm MSSV, họ tên -->
<div class="col-span-5 relative">
<span class="material-symbols-outlined absolute left-3 top-2.5 text-[20px] text-outline">search</span>
<input aria-label="Tìm theo MSSV, Họ tên sinh viên..." class="w-full bg-white border border-[#cbd5e1] rounded-lg pl-10 pr-4 py-2 text-body-md text-on-surface placeholder:text-[#94a3b8] focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all" placeholder="Tìm theo MSSV, Họ tên sinh viên..." type="text" value="Nguyễn Văn An"/>
</div>
<!-- Bộ lọc Lớp học phần -->
<div class="col-span-3">
<select aria-label="Tất cả lớp học phần" class="w-full bg-white border border-[#cbd5e1] rounded-lg px-3 py-2 text-body-md text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
<option selected="">Tất cả lớp học phần</option>
<option>241_71ITEC3201_01 - Lập trình Web</option>
<option>241_71ITEC2104_02 - Cấu trúc dữ liệu &amp; GT</option>
<option>241_71MATH1101_01 - Đại số tuyến tính</option>
</select>
</div>
<!-- Bộ lọc Trạng thái -->
<div class="col-span-2">
<select aria-label="Tất cả trạng thái" class="w-full bg-white border border-[#cbd5e1] rounded-lg px-2.5 py-2 text-body-md text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
<option selected="">Tất cả trạng thái</option>
<option>Hợp lệ</option>
<option>Có cảnh báo</option>
<option>Đã cấp ngoại lệ</option>
</select>
</div>
<!-- Bộ lọc Khoa -->
<div class="col-span-2">
<select aria-label="Khoa Công nghệ Thông tin" class="w-full bg-white border border-[#cbd5e1] rounded-lg px-2.5 py-2 text-body-md text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
<option selected="">Khoa Công nghệ Thông tin</option>
<option>Khoa Thực phẩm</option>
<option>Khoa Quản trị Kinh doanh</option>
<option>Khoa Điện - Điện tử</option>
</select>
</div>
</div>
<!-- Dòng tóm tắt nhanh số liệu (Data indicators) -->
<div class="mt-3 pt-3 border-t border-[#f1f5f9] flex items-center justify-between text-body-sm text-secondary">
<div class="flex items-center gap-4">
<span>Tổng số sinh viên trong đợt: <strong class="text-on-surface font-semibold">1,248</strong></span>
<span class="inline-block w-1 h-1 rounded-full bg-outline-variant"></span>
<span>Hợp lệ: <strong class="text-[#15803d] font-semibold">1,180</strong></span>
<span class="inline-block w-1 h-1 rounded-full bg-outline-variant"></span>
<span>Cần phê duyệt ngoại lệ: <strong class="text-[#b45309] font-semibold">14</strong></span>
<span class="inline-block w-1 h-1 rounded-full bg-outline-variant"></span>
<span>Đã duyệt ngoại lệ: <strong class="text-[#7e22ce] font-semibold">54</strong></span>
</div>
<div class="text-xs text-outline">Cập nhật theo thời gian thực</div>
</div>
</div>
<!-- Bảng danh sách sinh viên đăng ký học phần -->
<div class="bg-surface-container-lowest border border-[#e2e8f0] rounded-xl shadow-sm flex-1 flex flex-col overflow-hidden">
<div class="overflow-x-auto flex-1 custom-scrollbar">
<table class="w-full text-left border-collapse text-body-md">
<thead>
<tr class="bg-[#f8fafc] border-b border-[#e2e8f0] text-secondary font-label-md text-label-md">
<th class="py-3 px-4 w-12 text-center">STT</th>
<th class="py-3 px-4 sticky left-0 bg-[#f8fafc] z-10 w-32 shadow-[1px_0_0_0_#e2e8f0]">MSSV</th>
<th class="py-3 px-4 min-w-[170px]">Họ và tên</th>
<th class="py-3 px-4 min-w-[120px]">Lớp hành chính</th>
<th class="py-3 px-4 text-center min-w-[140px]">Số HP đã ĐK</th>
<th class="py-3 px-4 text-center min-w-[130px]">Tổng tín chỉ</th>
<th class="py-3 px-4 min-w-[180px]">Trạng thái</th>
<th class="py-3 px-4 min-w-[160px]">Thời gian đăng ký cuối</th>
<th class="py-3 px-4 text-center min-w-[150px]">Thao tác</th>
</tr>
</thead>
<tbody class="divide-y divide-[#f1f5f9]">
<!-- Dòng 1: Đang chọn (Nguyễn Văn An) -->
<tr class="bg-[#eff4ff]/70 hover:bg-[#eff4ff] transition-colors group cursor-pointer border-l-4 border-l-primary">
<td class="py-3 px-4 text-center font-medium text-secondary">01</td>
<td class="py-3 px-4 sticky left-0 bg-[#eff4ff] group-hover:bg-[#eff4ff] z-10 font-bold text-primary shadow-[1px_0_0_0_#e2e8f0]">
                      2001210123
                    </td>
<td class="py-3 px-4 font-semibold text-on-surface">Nguyễn Văn An</td>
<td class="py-3 px-4 text-secondary">12DHTH01</td>
<td class="py-3 px-4 text-center font-medium">6 học phần</td>
<td class="py-3 px-4 text-center">
<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-surface-container text-primary">
                        18 / 24 TC
                      </span>
</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-[#f3e8ff] text-[#7e22ce]">
<span class="w-1.5 h-1.5 rounded-full bg-[#7e22ce]"></span>
                        Đã cấp ngoại lệ
                      </span>
</td>
<td class="py-3 px-4 text-xs text-secondary">15/10/2026 08:34</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1.5">
<button class="px-2 py-1 rounded text-xs font-semibold text-primary hover:bg-white transition-colors" title="Xem chi tiết">
                          Chi tiết
                        </button>
<button class="px-2 py-1 rounded text-xs font-semibold text-white bg-primary hover:bg-[#1647a8] transition-colors" onclick="document.getElementById('exceptionModal').classList.remove('hidden')" title="Cấp ngoại lệ">
                          Ngoại lệ
                        </button>
</div>
</td>
</tr>
<!-- Dòng 2: Lê Thị Thu Thảo -->
<tr class="hover:bg-[#f8fafc] transition-colors group">
<td class="py-3 px-4 text-center text-secondary">02</td>
<td class="py-3 px-4 sticky left-0 bg-white group-hover:bg-[#f8fafc] z-10 font-semibold text-on-surface shadow-[1px_0_0_0_#e2e8f0]">
                      2001210045
                    </td>
<td class="py-3 px-4 text-on-surface">Lê Thị Thu Thảo</td>
<td class="py-3 px-4 text-secondary">12DHTH02</td>
<td class="py-3 px-4 text-center">5 học phần</td>
<td class="py-3 px-4 text-center">
<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700">
                        15 / 24 TC
                      </span>
</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-[#dcfce7] text-[#15803d]">
<span class="w-1.5 h-1.5 rounded-full bg-[#15803d]"></span>
                        Hợp lệ
                      </span>
</td>
<td class="py-3 px-4 text-xs text-secondary">15/10/2026 09:12</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1.5">
<button class="px-2 py-1 rounded text-xs font-semibold text-primary hover:bg-[#eff4ff] transition-colors">
                          Chi tiết
                        </button>
<button class="px-2 py-1 rounded text-xs font-semibold border border-[#cbd5e1] text-secondary hover:bg-slate-100 transition-colors" onclick="document.getElementById('exceptionModal').classList.remove('hidden')">
                          Ngoại lệ
                        </button>
</div>
</td>
</tr>
<!-- Dòng 3: Trần Quốc Bảo (Cảnh báo trùng lịch) -->
<tr class="hover:bg-[#f8fafc] transition-colors group">
<td class="py-3 px-4 text-center text-secondary">03</td>
<td class="py-3 px-4 sticky left-0 bg-white group-hover:bg-[#f8fafc] z-10 font-semibold text-on-surface shadow-[1px_0_0_0_#e2e8f0]">
                      2001210189
                    </td>
<td class="py-3 px-4 text-on-surface">Trần Quốc Bảo</td>
<td class="py-3 px-4 text-secondary">12DHPM01</td>
<td class="py-3 px-4 text-center">7 học phần</td>
<td class="py-3 px-4 text-center">
<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-amber-50 text-amber-700">
                        22 / 24 TC
                      </span>
</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-[#fef3c7] text-[#b45309]">
<span class="w-1.5 h-1.5 rounded-full bg-[#b45309]"></span>
                        Có cảnh báo trùng lịch
                      </span>
</td>
<td class="py-3 px-4 text-xs text-secondary">15/10/2026 10:05</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1.5">
<button class="px-2 py-1 rounded text-xs font-semibold text-primary hover:bg-[#eff4ff] transition-colors">
                          Chi tiết
                        </button>
<button class="px-2 py-1 rounded text-xs font-semibold bg-amber-600 text-white hover:bg-amber-700 transition-colors" onclick="document.getElementById('exceptionModal').classList.remove('hidden')">
                          Xử lý
                        </button>
</div>
</td>
</tr>
<!-- Dòng 4: Phạm Minh Hoàng -->
<tr class="hover:bg-[#f8fafc] transition-colors group">
<td class="py-3 px-4 text-center text-secondary">04</td>
<td class="py-3 px-4 sticky left-0 bg-white group-hover:bg-[#f8fafc] z-10 font-semibold text-on-surface shadow-[1px_0_0_0_#e2e8f0]">
                      2001210344
                    </td>
<td class="py-3 px-4 text-on-surface">Phạm Minh Hoàng</td>
<td class="py-3 px-4 text-secondary">12DHTH01</td>
<td class="py-3 px-4 text-center">6 học phần</td>
<td class="py-3 px-4 text-center">
<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700">
                        19 / 24 TC
                      </span>
</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-[#dcfce7] text-[#15803d]">
<span class="w-1.5 h-1.5 rounded-full bg-[#15803d]"></span>
                        Hợp lệ
                      </span>
</td>
<td class="py-3 px-4 text-xs text-secondary">15/10/2026 10:48</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1.5">
<button class="px-2 py-1 rounded text-xs font-semibold text-primary hover:bg-[#eff4ff] transition-colors">
                          Chi tiết
                        </button>
<button class="px-2 py-1 rounded text-xs font-semibold border border-[#cbd5e1] text-secondary hover:bg-slate-100 transition-colors" onclick="document.getElementById('exceptionModal').classList.remove('hidden')">
                          Ngoại lệ
                        </button>
</div>
</td>
</tr>
<!-- Dòng 5: Hoàng Thị Mai -->
<tr class="hover:bg-[#f8fafc] transition-colors group">
<td class="py-3 px-4 text-center text-secondary">05</td>
<td class="py-3 px-4 sticky left-0 bg-white group-hover:bg-[#f8fafc] z-10 font-semibold text-on-surface shadow-[1px_0_0_0_#e2e8f0]">
                      2001210512
                    </td>
<td class="py-3 px-4 text-on-surface">Hoàng Thị Mai</td>
<td class="py-3 px-4 text-secondary">12DHPM02</td>
<td class="py-3 px-4 text-center">7 học phần</td>
<td class="py-3 px-4 text-center">
<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700">
                        21 / 24 TC
                      </span>
</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-[#dcfce7] text-[#15803d]">
<span class="w-1.5 h-1.5 rounded-full bg-[#15803d]"></span>
                        Hợp lệ
                      </span>
</td>
<td class="py-3 px-4 text-xs text-secondary">15/10/2026 11:15</td>
<td class="py-3 px-4 text-center">
<div class="flex items-center justify-center gap-1.5">
<button class="px-2 py-1 rounded text-xs font-semibold text-primary hover:bg-[#eff4ff] transition-colors">
                          Chi tiết
                        </button>
<button class="px-2 py-1 rounded text-xs font-semibold border border-[#cbd5e1] text-secondary hover:bg-slate-100 transition-colors" onclick="document.getElementById('exceptionModal').classList.remove('hidden')">
                          Ngoại lệ
                        </button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<!-- Phân trang (Pagination footer) -->
<div class="p-3 bg-[#f8fafc] border-t border-[#e2e8f0] flex items-center justify-between text-body-sm text-secondary">
<div>Hiển thị <strong>1 - 5</strong> trên <strong>1,248</strong> sinh viên</div>
<div class="flex items-center gap-2">
<button class="px-3 py-1 border border-[#cbd5e1] bg-white rounded-md text-xs font-medium hover:bg-slate-50 disabled:opacity-50" disabled="">
                  Trang trước
                </button>
<div class="flex items-center gap-1">
<span class="w-7 h-7 flex items-center justify-center rounded-md bg-primary text-white text-xs font-bold">1</span>
<span class="w-7 h-7 flex items-center justify-center rounded-md hover:bg-slate-200 text-xs text-secondary cursor-pointer">2</span>
<span class="w-7 h-7 flex items-center justify-center rounded-md hover:bg-slate-200 text-xs text-secondary cursor-pointer">3</span>
<span class="text-xs">...</span>
<span class="w-7 h-7 flex items-center justify-center rounded-md hover:bg-slate-200 text-xs text-secondary cursor-pointer">125</span>
</div>
<button class="px-3 py-1 border border-[#cbd5e1] bg-white rounded-md text-xs font-medium hover:bg-slate-50">
                  Trang kế
                </button>
</div>
</div>
</div>
</div>
<!-- B) SIDE DETAIL DRAWER / PANEL BÊN PHẢI (w-96) -->
<aside class="w-96 shrink-0 bg-surface-container-lowest border border-[#e2e8f0] rounded-xl flex flex-col shadow-sm overflow-hidden">
<!-- Đầu panel: Thông tin sinh viên đang chọn -->
<div class="p-5 border-b border-[#f1f5f9] bg-gradient-to-b from-[#f8faff] to-white">
<div class="flex items-start justify-between">
<div>
<span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-primary/10 text-primary mb-1">
                  Khóa K21 - CNTT Chính quy
                </span>
<h3 class="font-headline-sm text-headline-sm font-bold text-on-surface">Nguyễn Văn An</h3>
<p class="font-body-sm text-body-sm text-secondary">MSSV: <strong class="text-on-surface font-semibold">2001210123</strong> | Lớp: 12DHTH01</p>
</div>
<div class="w-10 h-10 rounded-full bg-primary/10 border border-primary/20 flex items-center justify-center text-primary font-bold">
                AN
              </div>
</div>
<!-- Tín chỉ metric card -->
<div class="mt-4 p-3 bg-surface-container-low rounded-lg border border-[#e2e8f0] flex items-center justify-between">
<div>
<span class="text-[11px] text-secondary font-medium block">Tín chỉ đăng ký đợt này:</span>
<span class="text-base font-bold text-primary">18 / 24 Tín chỉ</span>
</div>
<div class="text-right">
<span class="text-[11px] text-secondary font-medium block">Trạng thái học vụ:</span>
<span class="text-xs font-semibold text-[#15803d]">Đủ điều kiện HK1</span>
</div>
</div>
</div>
<!-- Danh sách các lớp học phần sinh viên đã đăng ký trong đợt -->
<div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar">
<div class="flex items-center justify-between">
<span class="font-label-md text-label-md text-secondary uppercase tracking-wider">Học phần đã đăng ký (4/6 hiển thị)</span>
<span class="text-xs font-semibold text-primary">HK1 2026-2027</span>
</div>
<!-- Học phần 1: Lập trình Web -->
<div class="p-3.5 rounded-lg border border-[#e2e8f0] bg-white hover:border-primary/40 transition-colors shadow-2xs">
<div class="flex items-start justify-between gap-2">
<div class="font-semibold text-sm text-on-surface">Lập trình Web</div>
<span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[#dcfce7] text-[#15803d] shrink-0">Hợp lệ</span>
</div>
<div class="text-xs text-secondary mt-1 font-mono">241_71ITEC3201_01 • 3 TC</div>
<div class="mt-2 text-xs text-on-surface-variant flex items-center gap-1.5 pt-2 border-t border-[#f1f5f9]">
<span class="material-symbols-outlined text-[15px] text-primary">schedule</span>
<span>Thứ 2 | Tiết 1 - 3 (Phòng C.402)</span>
</div>
</div>
<!-- Học phần 2: Cấu trúc dữ liệu & GT (Ngoại lệ vượt sĩ số) -->
<div class="p-3.5 rounded-lg border-2 border-[#d8b4fe] bg-[#faf5ff] shadow-2xs">
<div class="flex items-start justify-between gap-2">
<div class="font-semibold text-sm text-on-surface">Cấu trúc dữ liệu &amp; GT</div>
<span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[#f3e8ff] text-[#7e22ce] shrink-0">
                  Ngoại lệ: Vượt sĩ số
                </span>
</div>
<div class="text-xs text-secondary mt-1 font-mono">241_71ITEC2104_02 • 4 TC</div>
<div class="mt-2 text-xs text-on-surface-variant flex items-center gap-1.5 pt-2 border-t border-[#e9d5ff]">
<span class="material-symbols-outlined text-[15px] text-[#7e22ce]">schedule</span>
<span>Thứ 3 | Tiết 3 - 5 (Phòng Lab B.201)</span>
</div>
<div class="mt-1.5 text-[11px] text-[#7e22ce] flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]">verified_user</span>
<span>Đã duyệt bởi: Ban Chủ nhiệm Khoa CNTT</span>
</div>
</div>
<!-- Học phần 3: Đại số tuyến tính -->
<div class="p-3.5 rounded-lg border border-[#e2e8f0] bg-white hover:border-primary/40 transition-colors shadow-2xs">
<div class="flex items-start justify-between gap-2">
<div class="font-semibold text-sm text-on-surface">Đại số tuyến tính</div>
<span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[#dcfce7] text-[#15803d] shrink-0">Hợp lệ</span>
</div>
<div class="text-xs text-secondary mt-1 font-mono">241_71MATH1101_01 • 3 TC</div>
<div class="mt-2 text-xs text-on-surface-variant flex items-center gap-1.5 pt-2 border-t border-[#f1f5f9]">
<span class="material-symbols-outlined text-[15px] text-primary">schedule</span>
<span>Thứ 4 | Tiết 7 - 9 (Phòng A.305)</span>
</div>
</div>
<!-- Học phần 4: An toàn mạng & Mật mã -->
<div class="p-3.5 rounded-lg border border-[#e2e8f0] bg-white hover:border-primary/40 transition-colors shadow-2xs">
<div class="flex items-start justify-between gap-2">
<div class="font-semibold text-sm text-on-surface">An toàn mạng &amp; Mật mã</div>
<span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[#dcfce7] text-[#15803d] shrink-0">Hợp lệ</span>
</div>
<div class="text-xs text-secondary mt-1 font-mono">241_71ITEC3208_03 • 3 TC</div>
<div class="mt-2 text-xs text-on-surface-variant flex items-center gap-1.5 pt-2 border-t border-[#f1f5f9]">
<span class="material-symbols-outlined text-[15px] text-primary">schedule</span>
<span>Thứ 6 | Tiết 4 - 6 (Phòng B.102)</span>
</div>
</div>
</div>
<!-- Nút tác vụ chân panel -->
<div class="p-4 border-t border-[#e2e8f0] bg-white flex flex-col gap-2">
<button class="w-full py-2.5 px-3 bg-white border border-[#cbd5e1] hover:bg-slate-50 text-on-surface font-semibold text-xs rounded-lg flex items-center justify-center gap-2 transition-colors">
<span class="material-symbols-outlined text-[18px]">print</span>
<span>In phiếu đăng ký học phần</span>
</button>
<button class="w-full py-2.5 px-3 bg-[#fee2e2] hover:bg-[#fecaca] text-[#b91c1c] font-semibold text-xs rounded-lg flex items-center justify-center gap-2 transition-colors">
<span class="material-symbols-outlined text-[18px]">cancel</span>
<span>Hủy đăng ký lớp đã chọn</span>
</button>
</div>
</aside>
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
