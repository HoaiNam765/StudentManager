@extends('fe_preview.layouts.admin')
@section('page_title', 'Admin - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="light"@endsection
@section('body_attributes')class="bg-[#f8f9ff] text-slate-800 antialiased min-h-screen flex selection:bg-blue-100 selection:text-blue-900"@endsection
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
<!-- 3. HEADER TRANG: Tiêu đề, Mã C9. #16 & Nút thao tác mẫu -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-whisper">
<div class="space-y-1">
<div class="flex items-center gap-3">
<h1 class="font-headline-lg text-headline-lg font-bold text-on-surface tracking-tight">
              Nhập dữ liệu Sinh viên từ file Excel
            </h1>
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-label-md text-label-md bg-primary-fixed text-on-primary-fixed border border-primary/20">
<span class="material-symbols-outlined text-[14px]">tune</span></span>
</div>
<p class="font-body-md text-body-md text-on-surface-variant">
            Tải lên danh sách hồ sơ sinh viên chính quy, liên thông và bằng hai vào hệ thống học vụ HUIT.
          </p>
</div>
<!-- 2 Nút tiện ích hướng dẫn và file mẫu -->
<div class="flex items-center gap-3 shrink-0">
<button class="h-10 px-4 bg-surface-container-lowest border border-outline-variant hover:border-outline hover:bg-surface-container-low text-on-surface font-label-md text-label-md rounded-lg flex items-center gap-2 transition-all shadow-whisper" type="button">
<span class="material-symbols-outlined text-[18px] text-tertiary">menu_book</span>
<span>Hướng dẫn chuẩn bị dữ liệu</span>
</button>
<button class="h-10 px-4 bg-primary/10 border border-primary/30 hover:bg-primary/15 text-primary font-label-md text-label-md rounded-lg flex items-center gap-2 transition-all" type="button">
<span class="material-symbols-outlined text-[18px]">download_for_offline</span>
<span>Tải file mẫu chuẩn (.xlsx)</span>
</button>
</div>
</div>
<!-- 4. STEPPER 3 BƯỚC ĐỒNG BỘ -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
<!-- Bước 1: Hoàn thành -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 flex items-center gap-3.5 shadow-whisper">
<div class="w-10 h-10 rounded-full bg-[#dcfce7] text-[#15803d] flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[24px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
</div>
<div class="min-w-0">
<div class="flex items-center gap-2">
<span class="font-label-md text-label-md font-bold text-on-surface">1. Tải lên tệp dữ liệu</span>
<span class="text-[10px] bg-[#dcfce7] text-[#15803d] font-semibold px-2 py-0.5 rounded-full">Đã tải lên</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant truncate mt-0.5">DS_SinhVien_K24_KhoaCNTT.xlsx (2.4 MB)</p>
</div>
</div>
<!-- Bước 2: Đang chọn (Active) -->
<div class="bg-surface-container-lowest border-2 border-primary rounded-xl p-4 flex items-center gap-3.5 shadow-whisper relative ring-2 ring-primary/10">
<div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center shrink-0 font-bold font-headline-sm">
            2
          </div>
<div class="min-w-0">
<div class="flex items-center gap-2">
<span class="font-label-md text-label-md font-bold text-primary">2. Xem trước &amp; Xác thực dữ liệu</span>
<span class="text-[10px] bg-primary text-white font-semibold px-2 py-0.5 rounded-full animate-pulse">Đang xử lý</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant truncate mt-0.5">Kiểm tra hợp lệ 145 hồ sơ</p>
</div>
</div>
<!-- Bước 3: Chờ -->
<div class="bg-surface-container-lowest/60 border border-outline-variant/70 rounded-xl p-4 flex items-center gap-3.5 opacity-75">
<div class="w-10 h-10 rounded-full bg-surface-container text-outline flex items-center justify-center shrink-0 font-bold font-headline-sm">
            3
          </div>
<div class="min-w-0">
<div class="flex items-center gap-2">
<span class="font-label-md text-label-md font-bold text-secondary">3. Hoàn tất &amp; Kết quả</span>
<span class="text-[10px] bg-surface-container text-secondary font-semibold px-2 py-0.5 rounded-full">Chờ</span>
</div>
<p class="font-body-sm text-body-sm text-outline truncate mt-0.5">Ghi cơ sở dữ liệu học vụ</p>
</div>
</div>
</div>
<!-- 5. KHỐI THẺ THỐNG KÊ KẾT QUẢ XÁC THỰC (KPI CARDS) -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-5">
<!-- Thẻ 1: Tổng số dòng tệp -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 shadow-whisper flex items-center justify-between">
<div class="space-y-1">
<p class="font-label-md text-label-md text-on-surface-variant font-medium">Tổng số dòng tệp tải lên</p>
<p class="font-headline-xl text-headline-xl font-bold text-on-surface leading-tight">145</p>
<p class="font-body-sm text-body-sm text-on-surface-variant">Tổng hồ sơ sinh viên được nạp</p>
</div>
<div class="w-12 h-12 rounded-xl bg-surface-container flex items-center justify-center text-primary">
<span class="material-symbols-outlined text-[28px]">format_list_bulleted</span>
</div>
</div>
<!-- Thẻ 2: Dữ liệu Hợp lệ (Xanh lục) -->
<div class="bg-surface-container-lowest border border-[#bbf7d0] rounded-xl p-5 shadow-whisper flex items-center justify-between relative overflow-hidden">
<div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#15803d]"></div>
<div class="space-y-1 pl-1">
<div class="flex items-center gap-2">
<p class="font-label-md text-label-md text-[#15803d] font-bold">Dữ liệu Hợp lệ</p>
<span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-[#dcfce7] text-[#15803d]">97.9%</span>
</div>
<p class="font-headline-xl text-headline-xl font-bold text-[#15803d] leading-tight">142 <span class="text-base font-normal text-on-surface-variant">dòng</span></p>
<p class="font-body-sm text-body-sm text-on-surface-variant">Đầy đủ MSSV, email, ngành, lớp</p>
</div>
<div class="w-12 h-12 rounded-xl bg-[#dcfce7] flex items-center justify-center text-[#15803d]">
<span class="material-symbols-outlined text-[28px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
</div>
</div>
<!-- Thẻ 3: Phát hiện lỗi dữ liệu (Đỏ cảnh báo) -->
<div class="bg-surface-container-lowest border border-error-container rounded-xl p-5 shadow-whisper flex items-center justify-between relative overflow-hidden">
<div class="absolute left-0 top-0 bottom-0 w-1.5 bg-error"></div>
<div class="space-y-1 pl-1">
<div class="flex items-center gap-2">
<p class="font-label-md text-label-md text-error font-bold">Phát hiện lỗi dữ liệu</p>
<span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-[#fee2e2] text-[#b91c1c]">2.1%</span>
</div>
<p class="font-headline-xl text-headline-xl font-bold text-error leading-tight">03 <span class="text-base font-normal text-on-surface-variant">dòng</span></p>
<p class="font-body-sm text-body-sm text-on-surface-variant">Trùng MSSV, sai email, sai mã lớp</p>
</div>
<div class="w-12 h-12 rounded-xl bg-[#fee2e2] flex items-center justify-center text-error">
<span class="material-symbols-outlined text-[28px]" style="font-variation-settings: 'FILL' 1;">error</span>
</div>
</div>
</div>
<!-- 6. KHUNG CẢNH BÁO TÍNH TOÀN VẸN DỮ LIỆU -->
<div class="bg-[#fffbeb] border-l-4 border-[#f59e0b] border-y border-r border-[#fef3c7] rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
<div class="flex items-start gap-3">
<span class="material-symbols-outlined text-[#b45309] text-[24px] mt-0.5 shrink-0" style="font-variation-settings: 'FILL' 1;">warning</span>
<div>
<h2 class="font-label-lg text-label-lg font-bold text-[#92400e]">Cảnh báo tính toàn vẹn dữ liệu: Phát hiện 3 bản ghi chứa thông tin không hợp lệ</h2>
<p class="font-body-md text-body-md text-[#b45309] mt-0.5">
              Bạn có thể chọn nhập trước 142 dòng hợp lệ hoặc tải file đối soát lỗi để chỉnh sửa trực tiếp trước khi ghi vào cơ sở dữ liệu học vụ.
            </p>
</div>
</div>
<button class="h-9 px-4 bg-surface-container-lowest border border-[#d97706]/40 hover:bg-[#fef3c7] text-[#92400e] font-label-md text-label-md rounded-lg flex items-center gap-2 shrink-0 transition-colors shadow-sm" type="button">
<span class="material-symbols-outlined text-[18px]">file_download</span>
<span>Tải bảng lỗi đối soát (.xlsx)</span>
</button>
</div>
<!-- 7. BẢNG XEM TRƯỚC DỮ LIỆU (Cuộn ngang, Sticky 2 cột đầu) -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-whisper overflow-hidden">
<!-- Toolbar Bộ lọc phía trên bảng -->
<div class="p-4 border-b border-[#f1f5f9] flex flex-wrap items-center justify-between gap-4 bg-surface-container-lowest">
<!-- Tabs lọc nhanh -->
<div class="flex items-center gap-1 bg-surface-container-low p-1 rounded-lg border border-outline-variant/60">
<button class="px-3.5 py-1.5 rounded-md font-label-md text-label-md font-medium text-on-surface-variant hover:text-on-surface hover:bg-surface-container-lowest transition-colors" type="button">
              Tất cả (145)
            </button>
<button class="px-3.5 py-1.5 rounded-md font-label-md text-label-md font-medium text-on-surface-variant hover:text-on-surface hover:bg-surface-container-lowest transition-colors" type="button">
              Hợp lệ (142)
            </button>
<button class="px-3.5 py-1.5 rounded-md font-label-md text-label-md font-bold bg-surface-container-lowest text-primary shadow-sm" type="button">
              Bị lỗi (3)
            </button>
</div>
<!-- Filter & Action Controls -->
<div class="flex items-center gap-4">
<label class="flex items-center gap-2 cursor-pointer text-on-surface text-sm font-medium select-none bg-error/5 hover:bg-error/10 px-3 py-1.5 rounded-lg border border-error/20 transition-colors">
<input aria-label="Chọn dòng" checked="" class="w-4 h-4 rounded text-error border-outline-variant focus:ring-error" type="checkbox"/>
<span class="text-error font-semibold font-label-md text-label-md">Chỉ hiển thị dòng bị lỗi (3 dòng)</span>
</label>
<button class="h-9 px-3.5 bg-surface-container-lowest border border-outline-variant hover:bg-surface-container-low text-on-surface font-label-md text-label-md rounded-lg flex items-center gap-2 transition-colors" type="button">
<span class="material-symbols-outlined text-[18px] text-error">download</span>
<span>Tải báo cáo lỗi (.xlsx)</span>
</button>
</div>
</div>
<!-- Khung bảng dữ liệu có scroll ngang và sticky header/columns -->
<div class="overflow-x-auto custom-scrollbar relative max-h-[500px]">
<table class="w-full text-left border-collapse min-w-[1300px]">
<thead class="sticky top-0 z-20 bg-[#f8fafc] border-b border-[#e2e8f0]">
<tr class="font-label-md text-label-md uppercase tracking-wider text-[#64748b]">
<!-- Sticky Col 1: STT -->
<th class="sticky left-0 z-30 bg-[#f8fafc] px-4 py-3.5 w-16 text-center border-r border-[#e2e8f0]" scope="col">
                  STT
                </th>
<!-- Cột Trạng thái -->
<th class="px-4 py-3.5 w-28" scope="col">Trạng thái</th>
<!-- Sticky Col 2: MSSV -->
<th class="sticky left-16 z-30 bg-[#f8fafc] px-4 py-3.5 w-36 border-r border-[#e2e8f0]" scope="col">
                  MSSV
                </th>
<th class="px-4 py-3.5 w-48" scope="col">Họ và tên</th>
<th class="px-4 py-3.5 w-28" scope="col">Ngày sinh</th>
<th class="px-4 py-3.5 w-28" scope="col">Lớp HC</th>
<th class="px-4 py-3.5 w-44" scope="col">Ngành đào tạo</th>
<th class="px-4 py-3.5 w-28" scope="col">Khóa</th>
<th class="px-4 py-3.5 w-52" scope="col">Email sinh viên</th>
<th class="px-4 py-3.5 min-w-[340px]" scope="col">Chi tiết lỗi &amp; Cách sửa</th>
</tr>
</thead>
<tbody class="divide-y divide-[#f1f5f9] font-body-sm text-body-sm">
<!-- DÒNG LỖI 1: STT 18 - Lỗi Email thiếu @ -->
<tr class="bg-[#fff5f5] hover:bg-[#fee2e2]/60 transition-colors border-l-4 border-l-error">
<td class="sticky left-0 z-10 bg-[#fff5f5] px-4 py-3 text-center font-bold text-error border-r border-[#fecaca]">
                  18
                </td>
<td class="px-4 py-3">
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#fee2e2] text-[#b91c1c] border border-[#fca5a5]">
<span class="material-symbols-outlined text-[12px]">cancel</span>
                    Bị lỗi
                  </span>
</td>
<td class="sticky left-16 z-10 bg-[#fff5f5] px-4 py-3 font-semibold text-on-surface border-r border-[#fecaca]">
                  2480201018
                </td>
<td class="px-4 py-3 font-medium text-on-surface">Người dùng mẫu 21</td>
<td class="px-4 py-3 text-on-surface-variant">15/09/2006</td>
<td class="px-4 py-3 font-mono text-xs font-semibold">15DHTH01</td>
<td class="px-4 py-3 text-on-surface-variant">Công nghệ thông tin</td>
<td class="px-4 py-3 text-on-surface-variant">Khóa 2024</td>
<td class="px-4 py-3 text-error font-medium underline decoration-dotted">
                  nguoidung042-example.test
                </td>
<td class="px-4 py-3">
<div class="p-2 rounded bg-white/80 border border-error/30 text-xs space-y-0.5">
<p class="font-semibold text-error flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]">error</span>
                      Cột Email: email "nguoidung042-example.test" không đúng định dạng.
                    </p>
<p class="text-on-surface-variant">
<strong class="text-on-surface">Cách sửa:</strong> Thêm ký tự "@" hợp lệ (vd: <span class="text-primary font-mono">nguoidung036@example.test</span>).
                    </p>
</div>
</td>
</tr>
<!-- DÒNG LỖI 2: STT 42 - Trùng MSSV K21 -->
<tr class="bg-[#fff5f5] hover:bg-[#fee2e2]/60 transition-colors border-l-4 border-l-error">
<td class="sticky left-0 z-10 bg-[#fff5f5] px-4 py-3 text-center font-bold text-error border-r border-[#fecaca]">
                  42
                </td>
<td class="px-4 py-3">
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#fee2e2] text-[#b91c1c] border border-[#fca5a5]">
<span class="material-symbols-outlined text-[12px]">cancel</span>
                    Bị lỗi
                  </span>
</td>
<td class="sticky left-16 z-10 bg-[#fff5f5] px-4 py-3 font-semibold text-error border-r border-[#fecaca]">
                  2400000007
                </td>
<td class="px-4 py-3 font-medium text-on-surface">Người dùng mẫu 44</td>
<td class="px-4 py-3 text-on-surface-variant">20/04/2006</td>
<td class="px-4 py-3 font-mono text-xs font-semibold">15DHTH02</td>
<td class="px-4 py-3 text-on-surface-variant">Công nghệ thông tin</td>
<td class="px-4 py-3 text-on-surface-variant">Khóa 2024</td>
<td class="px-4 py-3 text-on-surface-variant">nguoidung035@example.test</td>
<td class="px-4 py-3">
<div class="p-2 rounded bg-white/80 border border-error/30 text-xs space-y-0.5">
<p class="font-semibold text-error flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]">error</span>
                      Cột MSSV: "2400000007" đã tồn tại trong hệ thống (trùng sinh viên khóa K21).
                    </p>
<p class="text-on-surface-variant">
<strong class="text-on-surface">Cách sửa:</strong> Đổi MSSV mới hoặc chọn chế độ cập nhật ghi đè.
                    </p>
</div>
</td>
</tr>
<!-- DÒNG LỖI 3: STT 89 - Sai mã lớp hành chính -->
<tr class="bg-[#fff5f5] hover:bg-[#fee2e2]/60 transition-colors border-l-4 border-l-error">
<td class="sticky left-0 z-10 bg-[#fff5f5] px-4 py-3 text-center font-bold text-error border-r border-[#fecaca]">
                  89
                </td>
<td class="px-4 py-3">
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#fee2e2] text-[#b91c1c] border border-[#fca5a5]">
<span class="material-symbols-outlined text-[12px]">cancel</span>
                    Bị lỗi
                  </span>
</td>
<td class="sticky left-16 z-10 bg-[#fff5f5] px-4 py-3 font-semibold text-on-surface border-r border-[#fecaca]">
                  2480201089
                </td>
<td class="px-4 py-3 font-medium text-on-surface">Người dùng mẫu 14</td>
<td class="px-4 py-3 text-on-surface-variant">11/12/2006</td>
<td class="px-4 py-3 font-mono text-xs font-semibold text-error">15DHTH99</td>
<td class="px-4 py-3 text-on-surface-variant">Công nghệ thông tin</td>
<td class="px-4 py-3 text-on-surface-variant">Khóa 2024</td>
<td class="px-4 py-3 text-on-surface-variant">nguoidung023@example.test</td>
<td class="px-4 py-3">
<div class="p-2 rounded bg-white/80 border border-error/30 text-xs space-y-0.5">
<p class="font-semibold text-error flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]">error</span>
                      Cột Lớp: "15DHTH99" không có trong danh mục Lớp hành chính HUIT.
                    </p>
<p class="text-on-surface-variant">
<strong class="text-on-surface">Cách sửa:</strong> Chọn lại lớp hợp lệ (vd: "15DHTH01" hoặc "15DHTH02").
                    </p>
</div>
</td>
</tr>
<!-- DÒNG HỢP LỆ 1 (Mẫu) -->
<tr class="hover:bg-[#f8fafc] transition-colors">
<td class="sticky left-0 z-10 bg-surface-container-lowest hover:bg-[#f8fafc] px-4 py-3 text-center text-on-surface-variant font-medium border-r border-[#f1f5f9]">
                  01
                </td>
<td class="px-4 py-3">
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#dcfce7] text-[#15803d]">
<span class="material-symbols-outlined text-[12px]">check</span>
                    Hợp lệ
                  </span>
</td>
<td class="sticky left-16 z-10 bg-surface-container-lowest hover:bg-[#f8fafc] px-4 py-3 font-semibold text-on-surface border-r border-[#f1f5f9]">
                  2480201001
                </td>
<td class="px-4 py-3 font-medium text-on-surface">Người dùng mẫu 25</td>
<td class="px-4 py-3 text-on-surface-variant">02/01/2006</td>
<td class="px-4 py-3 font-mono text-xs font-semibold">15DHTH01</td>
<td class="px-4 py-3 text-on-surface-variant">Công nghệ thông tin</td>
<td class="px-4 py-3 text-on-surface-variant">Khóa 2024</td>
<td class="px-4 py-3 text-on-surface-variant">nguoidung018@example.test</td>
<td class="px-4 py-3 text-[#15803d] font-medium flex items-center gap-1.5 pt-4">
<span class="material-symbols-outlined text-[16px]">check_circle</span>
                  Dữ liệu hoàn toàn hợp lệ
                </td>
</tr>
<!-- DÒNG HỢP LỆ 2 (Mẫu) -->
<tr class="hover:bg-[#f8fafc] transition-colors">
<td class="sticky left-0 z-10 bg-surface-container-lowest hover:bg-[#f8fafc] px-4 py-3 text-center text-on-surface-variant font-medium border-r border-[#f1f5f9]">
                  02
                </td>
<td class="px-4 py-3">
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#dcfce7] text-[#15803d]">
<span class="material-symbols-outlined text-[12px]">check</span>
                    Hợp lệ
                  </span>
</td>
<td class="sticky left-16 z-10 bg-surface-container-lowest hover:bg-[#f8fafc] px-4 py-3 font-semibold text-on-surface border-r border-[#f1f5f9]">
                  2480201002
                </td>
<td class="px-4 py-3 font-medium text-on-surface">Người dùng mẫu 37</td>
<td class="px-4 py-3 text-on-surface-variant">14/05/2006</td>
<td class="px-4 py-3 font-mono text-xs font-semibold">15DHTH01</td>
<td class="px-4 py-3 text-on-surface-variant">Công nghệ thông tin</td>
<td class="px-4 py-3 text-on-surface-variant">Khóa 2024</td>
<td class="px-4 py-3 text-on-surface-variant">nguoidung022@example.test</td>
<td class="px-4 py-3 text-[#15803d] font-medium flex items-center gap-1.5 pt-4">
<span class="material-symbols-outlined text-[16px]">check_circle</span>
                  Dữ liệu hoàn toàn hợp lệ
                </td>
</tr>
<!-- DÒNG HỢP LỆ 3 (Mẫu) -->
<tr class="hover:bg-[#f8fafc] transition-colors">
<td class="sticky left-0 z-10 bg-surface-container-lowest hover:bg-[#f8fafc] px-4 py-3 text-center text-on-surface-variant font-medium border-r border-[#f1f5f9]">
                  03
                </td>
<td class="px-4 py-3">
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#dcfce7] text-[#15803d]">
<span class="material-symbols-outlined text-[12px]">check</span>
                    Hợp lệ
                  </span>
</td>
<td class="sticky left-16 z-10 bg-surface-container-lowest hover:bg-[#f8fafc] px-4 py-3 font-semibold text-on-surface border-r border-[#f1f5f9]">
                  2480201003
                </td>
<td class="px-4 py-3 font-medium text-on-surface">Người dùng mẫu 08</td>
<td class="px-4 py-3 text-on-surface-variant">29/08/2006</td>
<td class="px-4 py-3 font-mono text-xs font-semibold">15DHTH02</td>
<td class="px-4 py-3 text-on-surface-variant">Công nghệ thông tin</td>
<td class="px-4 py-3 text-on-surface-variant">Khóa 2024</td>
<td class="px-4 py-3 text-on-surface-variant">nguoidung027@example.test</td>
<td class="px-4 py-3 text-[#15803d] font-medium flex items-center gap-1.5 pt-4">
<span class="material-symbols-outlined text-[16px]">check_circle</span>
                  Dữ liệu hoàn toàn hợp lệ
                </td>
</tr>
</tbody>
</table>
</div>
<!-- Pagination / Footer tóm tắt bảng -->
<div class="px-5 py-3 bg-[#f8fafc] border-t border-[#f1f5f9] flex flex-wrap items-center justify-between text-xs text-on-surface-variant">
<div>
            Hiển thị <span class="font-bold text-on-surface">3 dòng lỗi</span> và <span class="font-bold text-on-surface">3 dòng mẫu đại diện</span> (trong tổng số 145 bản ghi).
          </div>
<div class="flex items-center gap-2">
<span class="text-outline">Cơ chế xác thực: Chuẩn danh mục QĐ-HUIT v2024.1</span>
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
