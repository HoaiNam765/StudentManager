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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/xep-thoi-khoa-bieu']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">date_range</span>
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
<!-- ================= 4. BANNER CẢNH BÁO XUNG ĐỘT (Conflict Alert Callout) ================= -->
<section class="bg-[#fef2f2] border border-[#ef4444] rounded-xl p-4 shadow-sm relative overflow-hidden">
<div class="flex items-start justify-between gap-4">
<!-- Alert Content -->
<div class="flex items-start gap-3">
<div class="p-2 bg-[#fee2e2] text-error rounded-lg flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-error fill-icon text-[24px]">warning</span>
</div>
<div class="space-y-1.5">
<div class="flex items-center gap-2.5">
<h2 class="font-headline-sm text-headline-sm text-error font-bold tracking-tight">
                PHÁT HIỆN 1 XUNG ĐỘT THỜI KHÓA BIỂU CẦN XỬ LÝ
              </h2>
<span class="px-2.5 py-0.5 rounded-full text-label-sm font-semibold bg-[#fee2e2] text-error border border-[#fca5a5]">
                [Xung đột lịch học]
              </span>
</div>
<p class="font-body-md text-body-md text-[#7f1d1d] leading-relaxed">
<strong>Lý do cụ thể:</strong> Phòng <strong>A2.301</strong> đã được phân bổ cho lớp <strong>241_71ITEC3201_01 (Lập trình Web)</strong> vào Thứ 3, Tiết 3 - 5. Giảng viên <strong>TS. Người dùng mẫu 38</strong> cũng đang có lịch dạy khác tại phòng <strong>B.102</strong> trong cùng khung giờ.
            </p>
<p class="font-body-sm text-body-sm text-[#991b1b]">
<strong>Cách khắc phục khuyến nghị:</strong> Chuyển lớp <strong>241_71ITEC2104_02</strong> sang phòng <strong>A2.302</strong> (còn trống tiết 3-5, sức chứa 65 chỗ) hoặc dời lịch sang <strong>Thứ 5, Tiết 1 - 3</strong>.
            </p>
</div>
</div>
<!-- Quick Action Buttons -->
<div class="flex items-center gap-2 shrink-0 self-center">
<button class="px-3.5 py-2 bg-white border border-[#ef4444] text-[#b91c1c] hover:bg-[#fff1f2] rounded-lg text-label-md font-semibold transition flex items-center gap-1.5 shadow-xs">
<span class="material-symbols-outlined text-[16px]">change_circle</span>
<span>Đổi sang Phòng A2.302</span>
</button>
<button class="px-3.5 py-2 bg-white border border-[#cbd5e1] text-on-surface hover:bg-[#f8fafc] rounded-lg text-label-md font-semibold transition flex items-center gap-1.5 shadow-xs">
<span class="material-symbols-outlined text-[16px]">schedule</span>
<span>Đổi sang Thứ 5, Tiết 1 - 3</span>
</button>
<button class="px-2.5 py-2 text-secondary hover:text-[#0b1c30] hover:underline text-label-md font-medium transition">
            Bỏ xếp lịch lớp này
          </button>
</div>
</div>
</section>
<!-- ================= 5. BỐ CỤC 2 CỘT CHÍNH ================= -->
<div class="flex gap-6 items-start w-full">
<!-- ================= 5A. CỘT TRÁI: LỚP HỌC PHẦN CHƯA XẾP LỊCH (w-80 / 320px) ================= -->
<aside class="w-80 shrink-0 bg-surface-container-lowest border border-[#e2e8f0] rounded-xl p-4 shadow-sm flex flex-col gap-4">
<!-- Header -->
<div class="flex items-center justify-between pb-3 border-b border-[#f1f5f9]">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[20px]">pending_actions</span>
<h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Lớp chưa xếp lịch</h3>
</div>
<span class="px-2 py-0.5 rounded-full text-label-sm font-semibold bg-[#fef3c7] text-[#b45309] border border-[#fde68a]">
            [14 lớp chờ xếp]
          </span>
</div>
<!-- Search Input -->
<div class="relative">
<span class="material-symbols-outlined absolute left-3 top-2.5 text-secondary text-[18px]">search</span>
<input aria-label="Tìm mã lớp, tên học phần..." class="w-full pl-9 pr-3 py-2 bg-[#f8fafc] border border-[#cbd5e1] rounded-lg text-body-sm text-on-surface placeholder:text-secondary focus:bg-white focus:border-primary focus:ring-1 focus:ring-primary transition" placeholder="Tìm mã lớp, tên học phần..." type="text"/>
</div>
<!-- Department Filters -->
<div class="grid grid-cols-2 gap-2 text-body-sm">
<div>
<label class="block text-label-sm text-secondary font-medium mb-1">Khoa</label>
<select aria-label="Khoa CNTT" class="w-full bg-[#f8fafc] border border-[#cbd5e1] rounded-lg p-1.5 text-xs text-on-surface focus:ring-primary focus:border-primary">
<option selected="">Khoa CNTT</option>
<option>Khoa Thực phẩm</option>
<option>Khoa QTKD</option>
</select>
</div>
<div>
<label class="block text-label-sm text-secondary font-medium mb-1">Ngành</label>
<select aria-label="CNTT" class="w-full bg-[#f8fafc] border border-[#cbd5e1] rounded-lg p-1.5 text-xs text-on-surface focus:ring-primary focus:border-primary">
<option selected="">CNTT</option>
<option>An toàn TT</option>
<option>Kỹ thuật PM</option>
</select>
</div>
</div>
<!-- Class Cards Container -->
<div class="space-y-3 max-h-[580px] overflow-y-auto pr-1">
<!-- Card 1 -->
<div class="p-3 bg-white border border-[#cbd5e1] hover:border-primary rounded-xl shadow-xs transition hover:shadow-sm cursor-grab group">
<div class="flex items-start justify-between gap-1 mb-1.5">
<span class="font-label-md text-label-md text-primary font-bold tracking-tight">241_71ITEC3201_02</span>
<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[#fef3c7] text-[#b45309] shrink-0">
                Chưa xếp lịch
              </span>
</div>
<div class="font-headline-sm text-headline-sm text-on-surface font-semibold mb-1 group-hover:text-primary transition-colors">
              Lập trình Web
            </div>
<div class="text-body-sm text-secondary space-y-0.5 mb-2.5">
<div class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-[15px] text-[#94a3b8]">credit_card</span>
<span>3 Tín chỉ (2LT+1TH)</span>
</div>
<div class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-[15px] text-[#94a3b8]">groups</span>
<span>60/60 Sinh viên</span>
</div>
<div class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-[15px] text-[#94a3b8]">person</span>
<span class="font-medium text-on-surface">TS. Người dùng mẫu 38</span>
</div>
</div>
<button class="w-full py-1.5 px-3 bg-[#eff4ff] hover:bg-primary text-primary hover:text-white rounded-lg text-label-sm font-semibold transition flex items-center justify-center gap-1">
<span class="material-symbols-outlined text-[16px]">touch_app</span>
<span>Xếp nhanh</span>
</button>
</div>
<!-- Card 2 -->
<div class="p-3 bg-white border border-[#cbd5e1] hover:border-primary rounded-xl shadow-xs transition hover:shadow-sm cursor-grab group">
<div class="flex items-start justify-between gap-1 mb-1.5">
<span class="font-label-md text-label-md text-primary font-bold tracking-tight">241_71ITEC2104_01</span>
<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[#fef3c7] text-[#b45309] shrink-0">
                Chưa xếp lịch
              </span>
</div>
<div class="font-headline-sm text-headline-sm text-on-surface font-semibold mb-1 group-hover:text-primary transition-colors">
              Cấu trúc dữ liệu &amp; Giải thuật
            </div>
<div class="text-body-sm text-secondary space-y-0.5 mb-2.5">
<div class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-[15px] text-[#94a3b8]">credit_card</span>
<span>4 Tín chỉ</span>
</div>
<div class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-[15px] text-[#94a3b8]">groups</span>
<span>55/60 Sinh viên</span>
</div>
<div class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-[15px] text-[#94a3b8]">person</span>
<span class="font-medium text-on-surface">ThS. Người dùng mẫu 11</span>
</div>
</div>
<button class="w-full py-1.5 px-3 bg-[#eff4ff] hover:bg-primary text-primary hover:text-white rounded-lg text-label-sm font-semibold transition flex items-center justify-center gap-1">
<span class="material-symbols-outlined text-[16px]">touch_app</span>
<span>Xếp nhanh</span>
</button>
</div>
<!-- Card 3 -->
<div class="p-3 bg-white border border-[#cbd5e1] hover:border-primary rounded-xl shadow-xs transition hover:shadow-sm cursor-grab group">
<div class="flex items-start justify-between gap-1 mb-1.5">
<span class="font-label-md text-label-md text-primary font-bold tracking-tight">241_71MATH1102_03</span>
<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[#fef3c7] text-[#b45309] shrink-0">
                Chưa xếp lịch
              </span>
</div>
<div class="font-headline-sm text-headline-sm text-on-surface font-semibold mb-1 group-hover:text-primary transition-colors">
              Xác suất thống kê
            </div>
<div class="text-body-sm text-secondary space-y-0.5 mb-2.5">
<div class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-[15px] text-[#94a3b8]">credit_card</span>
<span>3 Tín chỉ</span>
</div>
<div class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-[15px] text-[#94a3b8]">groups</span>
<span>70/70 Sinh viên</span>
</div>
<div class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-[15px] text-[#94a3b8]">person</span>
<span class="font-medium text-on-surface">TS. Người dùng mẫu 22</span>
</div>
</div>
<button class="w-full py-1.5 px-3 bg-[#eff4ff] hover:bg-primary text-primary hover:text-white rounded-lg text-label-sm font-semibold transition flex items-center justify-center gap-1">
<span class="material-symbols-outlined text-[16px]">touch_app</span>
<span>Xếp nhanh</span>
</button>
</div>
</div>
<!-- Support drag-and-drop info block -->
<div class="p-3 bg-[#eff4ff] border border-secondary-fixed rounded-xl flex items-start gap-2.5 text-xs text-primary">
<span class="material-symbols-outlined text-primary text-[18px] shrink-0">drag_indicator</span>
<p class="leading-relaxed font-medium">
            Kéo thả thẻ lớp vào ô lịch hoặc click để xếp nhanh vào phòng đang chọn.
          </p>
</div>
</aside>
<!-- ================= 5B. VÙNG CHÍNH: MAIN SCHEDULE GRID CANVAS ================= -->
<section class="flex-1 bg-surface-container-lowest border border-[#e2e8f0] rounded-xl p-5 shadow-sm overflow-hidden flex flex-col gap-4">
<!-- Top Control Bar of Timetable -->
<div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-[#f1f5f9]">
<!-- Left: View Toggle Tabs & Object Selector -->
<div class="flex items-center gap-4 flex-wrap">
<!-- View Tabs -->
<div class="flex items-center p-1 bg-[#f1f5f9] rounded-lg">
<button class="px-3 py-1.5 bg-white text-primary font-semibold text-label-md rounded-md shadow-xs flex items-center gap-1.5 transition">
<span class="material-symbols-outlined text-[16px]">meeting_room</span>
<span>Theo phòng học</span>
</button>
<button class="px-3 py-1.5 text-secondary hover:text-on-surface text-label-md font-medium rounded-md transition flex items-center gap-1.5">
<span class="material-symbols-outlined text-[16px]">person</span>
<span>Theo giảng viên</span>
</button>
<button class="px-3 py-1.5 text-secondary hover:text-on-surface text-label-md font-medium rounded-md transition flex items-center gap-1.5">
<span class="material-symbols-outlined text-[16px]">groups</span>
<span>Theo lớp hành chính</span>
</button>
</div>
<!-- Target Object Selector -->
<div class="flex items-center gap-2 bg-[#f8fafc] border border-[#cbd5e1] rounded-lg px-3 py-1.5 shadow-2xs">
<span class="material-symbols-outlined text-primary text-[18px]">apartment</span>
<select aria-label="Phòng A2.301 (Cơ sở Tân Phú - Sức chứa: 60 chỗ - Máy chiếu &amp; Mic)" class="bg-transparent text-label-md font-semibold text-on-surface border-none p-0 focus:ring-0 cursor-pointer min-w-[340px]">
<option selected="">Phòng A2.301 (Cơ sở Tân Phú - Sức chứa: 60 chỗ - Máy chiếu &amp; Mic)</option>
<option>Phòng A2.302 (Cơ sở Tân Phú - Sức chứa: 65 chỗ - Máy chiếu &amp; Mic)</option>
<option>Phòng B.102 (Cơ sở Tân Phú - Sức chứa: 50 chỗ - Lab Máy tính)</option>
<option>Phòng C.405 (Cơ sở Tân Phú - Sức chứa: 80 chỗ - Hội trường nhỏ)</option>
</select>
</div>
</div>
<!-- Right: Color Legend -->
<div class="flex items-center gap-3 text-label-sm">
<div class="flex items-center gap-1.5">
<span class="w-3 h-3 rounded-full bg-[#15803d]"></span>
<span class="text-on-surface font-medium">Đã xếp hợp lệ</span>
</div>
<div class="flex items-center gap-1.5">
<span class="w-3 h-3 rounded-full bg-[#ef4444] animate-pulse"></span>
<span class="text-error font-medium">Trùng lịch / Xung đột</span>
</div>
<div class="flex items-center gap-1.5">
<span class="w-3 h-3 rounded-full bg-[#cbd5e1] border border-dashed border-[#94a3b8]"></span>
<span class="text-secondary font-medium">Ô trống có thể xếp</span>
</div>
</div>
</div>
<!-- Weekly Time Table Grid (Scrollable Canvas) -->
<div class="overflow-x-auto border border-[#e2e8f0] rounded-xl bg-white shadow-2xs">
<table class="w-full border-collapse text-left text-body-sm min-w-[980px]">
<!-- Header Columns: Days of Week -->
<thead>
<tr class="bg-[#f8fafc] border-b border-[#e2e8f0] text-secondary font-label-md text-label-md">
<th class="p-3 w-32 border-r border-[#e2e8f0] font-semibold text-center uppercase tracking-wider">
                  Giờ / Tiết
                </th>
<th class="p-3 border-r border-[#e2e8f0] w-[12.5%] text-center">
<div class="font-bold text-on-surface">Thứ 2</div>
<div class="text-xs text-secondary font-normal">14/10/2026</div>
</th>
<th class="p-3 border-r border-[#e2e8f0] w-[12.5%] text-center bg-red-50/50">
<div class="font-bold text-error">Thứ 3</div>
<div class="text-xs text-error font-normal">15/10/2026</div>
</th>
<th class="p-3 border-r border-[#e2e8f0] w-[12.5%] text-center">
<div class="font-bold text-on-surface">Thứ 4</div>
<div class="text-xs text-secondary font-normal">16/10/2026</div>
</th>
<th class="p-3 border-r border-[#e2e8f0] w-[12.5%] text-center">
<div class="font-bold text-on-surface">Thứ 5</div>
<div class="text-xs text-secondary font-normal">17/10/2026</div>
</th>
<th class="p-3 border-r border-[#e2e8f0] w-[12.5%] text-center">
<div class="font-bold text-on-surface">Thứ 6</div>
<div class="text-xs text-secondary font-normal">18/10/2026</div>
</th>
<th class="p-3 border-r border-[#e2e8f0] w-[12.5%] text-center">
<div class="font-bold text-on-surface">Thứ 7</div>
<div class="text-xs text-secondary font-normal">19/10/2026</div>
</th>
<th class="p-3 w-[12.5%] text-center">
<div class="font-bold text-secondary">Chủ nhật</div>
<div class="text-xs text-secondary font-normal">20/10/2026</div>
</th>
</tr>
</thead>
<tbody class="divide-y divide-[#f1f5f9]">
<!-- ================= SECTION 1: CA SÁNG ================= -->
<tr class="bg-[#eff4ff]/60 border-y border-[#dae2fd]">
<td class="px-3 py-1 font-label-sm text-label-sm font-bold text-primary uppercase tracking-wider" colspan="8">
                  Ca Sáng (07:00 - 11:00)
                </td>
</tr>
<!-- Tiết 1 -->
<tr class="h-14">
<td class="p-2 border-r border-[#e2e8f0] text-center bg-[#fafafa]">
<div class="font-bold text-on-surface text-xs">Tiết 1</div>
<div class="text-[11px] text-secondary">07:00 - 07:45</div>
</td>
<!-- Thứ 2 (Span 3 tiết: Tiết 1 - 3) -->
<td class="p-2 border-r border-[#e2e8f0] align-top bg-white" rowspan="3">
<div class="h-full bg-[#eff4ff] border border-[#3b82f6] rounded-lg p-2.5 flex flex-col justify-between shadow-2xs hover:shadow-xs transition">
<div>
<div class="flex items-center justify-between mb-1">
<span class="font-label-md text-label-md font-bold text-primary">241_71ITEC3201_01</span>
<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[#dcfce7] text-[#15803d]">
                          Đã xếp hợp lệ
                        </span>
</div>
<div class="font-headline-sm text-headline-sm text-on-surface font-semibold mb-1">Lập trình Web</div>
<div class="text-xs text-secondary flex items-center gap-1 mb-0.5">
<span class="material-symbols-outlined text-[14px]">groups</span>
<span>Sĩ số: 48/60 SV</span>
</div>
<div class="text-xs text-secondary flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]">person</span>
<span class="font-medium text-on-surface">GV: TS. Người dùng mẫu 38</span>
</div>
</div>
<div class="text-[10px] text-primary font-semibold mt-2 pt-1 border-t border-[#bfdbfe] flex items-center justify-between">
<span>Tiết 1 - 3</span>
<span>Phòng A2.301</span>
</div>
</div>
</td>
<!-- Thứ 3: Tiết 1 trống -->
<td class="p-2 border-r border-[#e2e8f0] text-center group hover:bg-[#f8fafc] cursor-pointer">
<div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center p-1 text-secondary group-hover:border-primary group-hover:text-primary transition">
<span class="text-xs flex items-center gap-0.5">
<span class="material-symbols-outlined text-[14px]">add</span> Trống
                    </span>
</div>
</td>
<!-- Thứ 4: Tiết 1 trống -->
<td class="p-2 border-r border-[#e2e8f0] text-center group hover:bg-[#f8fafc] cursor-pointer">
<div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center p-1 text-secondary group-hover:border-primary group-hover:text-primary transition">
<span class="text-xs flex items-center gap-0.5"><span class="material-symbols-outlined text-[14px]">add</span> Trống</span>
</div>
</td>
<!-- Thứ 5: Tiết 1 trống -->
<td class="p-2 border-r border-[#e2e8f0] text-center group hover:bg-[#f8fafc] cursor-pointer">
<div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center p-1 text-secondary group-hover:border-primary group-hover:text-primary transition">
<span class="text-xs flex items-center gap-0.5"><span class="material-symbols-outlined text-[14px]">add</span> Trống</span>
</div>
</td>
<!-- Thứ 6 (Span 4 tiết: Tiết 1 - 4) -->
<td class="p-2 border-r border-[#e2e8f0] align-top bg-white" rowspan="4">
<div class="h-full bg-[#eff4ff] border border-[#3b82f6] rounded-lg p-2.5 flex flex-col justify-between shadow-2xs hover:shadow-xs transition">
<div>
<div class="flex items-center justify-between mb-1">
<span class="font-label-md text-label-md font-bold text-primary">241_71ITEC3208_01</span>
<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[#dcfce7] text-[#15803d]">
                          Đã xếp hợp lệ
                        </span>
</div>
<div class="font-headline-sm text-headline-sm text-on-surface font-semibold mb-1">Mạng máy tính</div>
<div class="text-xs text-secondary flex items-center gap-1 mb-0.5">
<span class="material-symbols-outlined text-[14px]">groups</span>
<span>Sĩ số: 50/60 SV</span>
</div>
<div class="text-xs text-secondary flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]">person</span>
<span class="font-medium text-on-surface">GV: ThS. Người dùng mẫu 56</span>
</div>
</div>
<div class="text-[10px] text-primary font-semibold mt-2 pt-1 border-t border-[#bfdbfe] flex items-center justify-between">
<span>Tiết 1 - 4 (Ca sáng)</span>
<span>Phòng A2.301</span>
</div>
</div>
</td>
<!-- Thứ 7: Tiết 1 trống -->
<td class="p-2 border-r border-[#e2e8f0] text-center group hover:bg-[#f8fafc] cursor-pointer">
<div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center p-1 text-secondary group-hover:border-primary group-hover:text-primary transition">
<span class="text-xs flex items-center gap-0.5"><span class="material-symbols-outlined text-[14px]">add</span> Trống</span>
</div>
</td>
<!-- Chủ nhật: Tiết 1 trống -->
<td class="p-2 text-center group hover:bg-[#f8fafc] cursor-pointer bg-[#fafafa]">
<div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center p-1 text-secondary">
<span class="text-xs text-gray-400">Nghỉ</span>
</div>
</td>
</tr>
<!-- Tiết 2 -->
<tr class="h-14">
<td class="p-2 border-r border-[#e2e8f0] text-center bg-[#fafafa]">
<div class="font-bold text-on-surface text-xs">Tiết 2</div>
<div class="text-[11px] text-secondary">07:45 - 08:30</div>
</td>
<!-- Thứ 3: Tiết 2 trống -->
<td class="p-2 border-r border-[#e2e8f0] text-center group hover:bg-[#f8fafc] cursor-pointer">
<div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center p-1 text-secondary group-hover:border-primary group-hover:text-primary transition">
<span class="text-xs flex items-center gap-0.5"><span class="material-symbols-outlined text-[14px]">add</span> Trống</span>
</div>
</td>
<!-- Thứ 4: Tiết 2 trống -->
<td class="p-2 border-r border-[#e2e8f0] text-center group hover:bg-[#f8fafc] cursor-pointer">
<div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center p-1 text-secondary group-hover:border-primary group-hover:text-primary transition">
<span class="text-xs flex items-center gap-0.5"><span class="material-symbols-outlined text-[14px]">add</span> Trống</span>
</div>
</td>
<!-- Thứ 5: Tiết 2 trống -->
<td class="p-2 border-r border-[#e2e8f0] text-center group hover:bg-[#f8fafc] cursor-pointer">
<div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center p-1 text-secondary group-hover:border-primary group-hover:text-primary transition">
<span class="text-xs flex items-center gap-0.5"><span class="material-symbols-outlined text-[14px]">add</span> Trống</span>
</div>
</td>
<!-- Thứ 7: Tiết 2 trống -->
<td class="p-2 border-r border-[#e2e8f0] text-center group hover:bg-[#f8fafc] cursor-pointer">
<div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center p-1 text-secondary group-hover:border-primary group-hover:text-primary transition">
<span class="text-xs flex items-center gap-0.5"><span class="material-symbols-outlined text-[14px]">add</span> Trống</span>
</div>
</td>
<!-- CN -->
<td class="p-2 text-center bg-[#fafafa]">
<span class="text-xs text-gray-400">Nghỉ</span>
</td>
</tr>
<!-- Tiết 3 -->
<tr class="h-14">
<td class="p-2 border-r border-[#e2e8f0] text-center bg-[#fafafa]">
<div class="font-bold text-on-surface text-xs">Tiết 3</div>
<div class="text-[11px] text-secondary">08:45 - 09:30</div>
</td>
<!-- Thứ 3: Span 3 tiết (Tiết 3 - 5) XUNG ĐỘT -->
<td class="p-2 border-r border-[#e2e8f0] align-top bg-red-50/30" rowspan="3">
<div class="h-full bg-[#fee2e2] border-2 border-[#ef4444] rounded-lg p-2.5 flex flex-col justify-between shadow-sm relative overflow-hidden animate-pulse">
<div class="absolute top-1.5 right-1.5 text-error">
<span class="material-symbols-outlined fill-icon text-[20px]">warning</span>
</div>
<div>
<div class="flex items-center justify-between mb-1 pr-6">
<span class="font-label-md text-label-md font-bold text-error">241_71ITEC2104_02</span>
</div>
<div class="mb-1">
<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white text-error border border-red-300">
                          Trùng lịch phòng &amp; GV
                        </span>
</div>
<div class="font-headline-sm text-headline-sm text-on-surface font-semibold mb-1 leading-snug">
                        Cấu trúc dữ liệu &amp; GT
                      </div>
<div class="text-xs text-secondary flex items-center gap-1 mb-0.5">
<span class="material-symbols-outlined text-[14px]">groups</span>
<span>Sĩ số: 58/60 SV</span>
</div>
<div class="text-xs text-error font-medium flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]">person</span>
<span>TS. Người dùng mẫu 38 (Trùng B.102)</span>
</div>
</div>
<div class="text-[10px] text-error font-semibold mt-2 pt-1 border-t border-[#fca5a5] flex items-center justify-between">
<span>Tiết 3 - 5</span>
<span class="underline cursor-pointer font-bold">Xử lý ngay →</span>
</div>
</div>
</td>
<!-- Thứ 4: Tiết 3 trống -->
<td class="p-2 border-r border-[#e2e8f0] text-center group hover:bg-[#f8fafc] cursor-pointer">
<div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center p-1 text-secondary group-hover:border-primary group-hover:text-primary transition">
<span class="text-xs flex items-center gap-0.5"><span class="material-symbols-outlined text-[14px]">add</span> Trống</span>
</div>
</td>
<!-- Thứ 5: Tiết 3 trống -->
<td class="p-2 border-r border-[#e2e8f0] text-center group hover:bg-[#f8fafc] cursor-pointer">
<div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center p-1 text-secondary group-hover:border-primary group-hover:text-primary transition">
<span class="text-xs flex items-center gap-0.5"><span class="material-symbols-outlined text-[14px]">add</span> Trống</span>
</div>
</td>
<!-- Thứ 7: Tiết 3 trống -->
<td class="p-2 border-r border-[#e2e8f0] text-center group hover:bg-[#f8fafc] cursor-pointer">
<div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center p-1 text-secondary group-hover:border-primary group-hover:text-primary transition">
<span class="text-xs flex items-center gap-0.5"><span class="material-symbols-outlined text-[14px]">add</span> Trống</span>
</div>
</td>
<!-- CN -->
<td class="p-2 text-center bg-[#fafafa]">
<span class="text-xs text-gray-400">Nghỉ</span>
</td>
</tr>
<!-- Tiết 4 -->
<tr class="h-14">
<td class="p-2 border-r border-[#e2e8f0] text-center bg-[#fafafa]">
<div class="font-bold text-on-surface text-xs">Tiết 4</div>
<div class="text-[11px] text-secondary">09:30 - 10:15</div>
</td>
<!-- Thứ 2: Tiết 4 trống -->
<td class="p-2 border-r border-[#e2e8f0] text-center group hover:bg-[#f8fafc] cursor-pointer">
<div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center p-1 text-secondary group-hover:border-primary group-hover:text-primary transition">
<span class="text-xs flex items-center gap-0.5"><span class="material-symbols-outlined text-[14px]">add</span> Trống</span>
</div>
</td>
<!-- Thứ 4: Tiết 4 trống -->
<td class="p-2 border-r border-[#e2e8f0] text-center group hover:bg-[#f8fafc] cursor-pointer">
<div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center p-1 text-secondary group-hover:border-primary group-hover:text-primary transition">
<span class="text-xs flex items-center gap-0.5"><span class="material-symbols-outlined text-[14px]">add</span> Trống</span>
</div>
</td>
<!-- Thứ 5: Tiết 4 trống -->
<td class="p-2 border-r border-[#e2e8f0] text-center group hover:bg-[#f8fafc] cursor-pointer">
<div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center p-1 text-secondary group-hover:border-primary group-hover:text-primary transition">
<span class="text-xs flex items-center gap-0.5"><span class="material-symbols-outlined text-[14px]">add</span> Trống</span>
</div>
</td>
<!-- Thứ 7: Tiết 4 trống -->
<td class="p-2 border-r border-[#e2e8f0] text-center group hover:bg-[#f8fafc] cursor-pointer">
<div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center p-1 text-secondary group-hover:border-primary group-hover:text-primary transition">
<span class="text-xs flex items-center gap-0.5"><span class="material-symbols-outlined text-[14px]">add</span> Trống</span>
</div>
</td>
<!-- CN -->
<td class="p-2 text-center bg-[#fafafa]">
<span class="text-xs text-gray-400">Nghỉ</span>
</td>
</tr>
<!-- Tiết 5 -->
<tr class="h-14">
<td class="p-2 border-r border-[#e2e8f0] text-center bg-[#fafafa]">
<div class="font-bold text-on-surface text-xs">Tiết 5</div>
<div class="text-[11px] text-secondary">10:15 - 11:00</div>
</td>
<!-- Thứ 2: Tiết 5 trống -->
<td class="p-2 border-r border-[#e2e8f0] text-center group hover:bg-[#f8fafc] cursor-pointer">
<div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center p-1 text-secondary group-hover:border-primary group-hover:text-primary transition">
<span class="text-xs flex items-center gap-0.5"><span class="material-symbols-outlined text-[14px]">add</span> Trống</span>
</div>
</td>
<!-- Thứ 4: Tiết 5 trống -->
<td class="p-2 border-r border-[#e2e8f0] text-center group hover:bg-[#f8fafc] cursor-pointer">
<div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center p-1 text-secondary group-hover:border-primary group-hover:text-primary transition">
<span class="text-xs flex items-center gap-0.5"><span class="material-symbols-outlined text-[14px]">add</span> Trống</span>
</div>
</td>
<!-- Thứ 5: Tiết 5 trống -->
<td class="p-2 border-r border-[#e2e8f0] text-center group hover:bg-[#f8fafc] cursor-pointer">
<div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center p-1 text-secondary group-hover:border-primary group-hover:text-primary transition">
<span class="text-xs flex items-center gap-0.5"><span class="material-symbols-outlined text-[14px]">add</span> Trống</span>
</div>
</td>
<!-- Thứ 6: Tiết 5 trống -->
<td class="p-2 border-r border-[#e2e8f0] text-center group hover:bg-[#f8fafc] cursor-pointer">
<div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center p-1 text-secondary group-hover:border-primary group-hover:text-primary transition">
<span class="text-xs flex items-center gap-0.5"><span class="material-symbols-outlined text-[14px]">add</span> Trống</span>
</div>
</td>
<!-- Thứ 7: Tiết 5 trống -->
<td class="p-2 border-r border-[#e2e8f0] text-center group hover:bg-[#f8fafc] cursor-pointer">
<div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center p-1 text-secondary group-hover:border-primary group-hover:text-primary transition">
<span class="text-xs flex items-center gap-0.5"><span class="material-symbols-outlined text-[14px]">add</span> Trống</span>
</div>
</td>
<!-- CN -->
<td class="p-2 text-center bg-[#fafafa]">
<span class="text-xs text-gray-400">Nghỉ</span>
</td>
</tr>
<!-- ================= SECTION 2: CA CHIỀU ================= -->
<tr class="bg-[#eff4ff]/60 border-y border-[#dae2fd]">
<td class="px-3 py-1 font-label-sm text-label-sm font-bold text-primary uppercase tracking-wider" colspan="8">
                  Ca Chiều (12:30 - 16:30)
                </td>
</tr>
<!-- Tiết 6 -->
<tr class="h-14">
<td class="p-2 border-r border-[#e2e8f0] text-center bg-[#fafafa]">
<div class="font-bold text-on-surface text-xs">Tiết 6</div>
<div class="text-[11px] text-secondary">12:30 - 13:15</div>
</td>
<!-- Thứ 2 -> CN trống -->
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 text-center bg-[#fafafa]"><span class="text-xs text-gray-400">Nghỉ</span></td>
</tr>
<!-- Tiết 7 -->
<tr class="h-14">
<td class="p-2 border-r border-[#e2e8f0] text-center bg-[#fafafa]">
<div class="font-bold text-on-surface text-xs">Tiết 7</div>
<div class="text-[11px] text-secondary">13:15 - 14:00</div>
</td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<!-- Thứ 4: Span 3 tiết Ca chiều (Tiết 7 - 9) -->
<td class="p-2 border-r border-[#e2e8f0] align-top bg-white" rowspan="3">
<div class="h-full bg-[#eff4ff] border border-[#3b82f6] rounded-lg p-2.5 flex flex-col justify-between shadow-2xs hover:shadow-xs transition">
<div>
<div class="flex items-center justify-between mb-1">
<span class="font-label-md text-label-md font-bold text-primary">241_71MATH1101_01</span>
<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[#dcfce7] text-[#15803d]">
                          Đã xếp hợp lệ
                        </span>
</div>
<div class="font-headline-sm text-headline-sm text-on-surface font-semibold mb-1">Đại số tuyến tính</div>
<div class="text-xs text-secondary flex items-center gap-1 mb-0.5">
<span class="material-symbols-outlined text-[14px]">groups</span>
<span>Sĩ số: 60/60 SV</span>
</div>
<div class="text-xs text-secondary flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]">person</span>
<span class="font-medium text-on-surface">GV: TS. Người dùng mẫu 46</span>
</div>
</div>
<div class="text-[10px] text-primary font-semibold mt-2 pt-1 border-t border-[#bfdbfe] flex items-center justify-between">
<span>Tiết 7 - 9 (Ca chiều)</span>
<span>Phòng A2.301</span>
</div>
</div>
</td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 text-center bg-[#fafafa]"><span class="text-xs text-gray-400">Nghỉ</span></td>
</tr>
<!-- Tiết 8 -->
<tr class="h-14">
<td class="p-2 border-r border-[#e2e8f0] text-center bg-[#fafafa]">
<div class="font-bold text-on-surface text-xs">Tiết 8</div>
<div class="text-[11px] text-secondary">14:15 - 15:00</div>
</td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 text-center bg-[#fafafa]"><span class="text-xs text-gray-400">Nghỉ</span></td>
</tr>
<!-- Tiết 9 -->
<tr class="h-14">
<td class="p-2 border-r border-[#e2e8f0] text-center bg-[#fafafa]">
<div class="font-bold text-on-surface text-xs">Tiết 9</div>
<div class="text-[11px] text-secondary">15:00 - 15:45</div>
</td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 text-center bg-[#fafafa]"><span class="text-xs text-gray-400">Nghỉ</span></td>
</tr>
<!-- Tiết 10 -->
<tr class="h-14">
<td class="p-2 border-r border-[#e2e8f0] text-center bg-[#fafafa]">
<div class="font-bold text-on-surface text-xs">Tiết 10</div>
<div class="text-[11px] text-secondary">15:45 - 16:30</div>
</td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 text-center bg-[#fafafa]"><span class="text-xs text-gray-400">Nghỉ</span></td>
</tr>
<!-- ================= SECTION 3: CA TỐI ================= -->
<tr class="bg-[#eff4ff]/60 border-y border-[#dae2fd]">
<td class="px-3 py-1 font-label-sm text-label-sm font-bold text-primary uppercase tracking-wider" colspan="8">
                  Ca Tối (17:30 - 19:00)
                </td>
</tr>
<!-- Tiết 11 -->
<tr class="h-14">
<td class="p-2 border-r border-[#e2e8f0] text-center bg-[#fafafa]">
<div class="font-bold text-on-surface text-xs">Tiết 11</div>
<div class="text-[11px] text-secondary">17:30 - 18:15</div>
</td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 text-center bg-[#fafafa]"><span class="text-xs text-gray-400">Nghỉ</span></td>
</tr>
<!-- Tiết 12 -->
<tr class="h-14">
<td class="p-2 border-r border-[#e2e8f0] text-center bg-[#fafafa]">
<div class="font-bold text-on-surface text-xs">Tiết 12</div>
<div class="text-[11px] text-secondary">18:15 - 19:00</div>
</td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 border-r border-[#e2e8f0] text-center"><div class="h-full border border-dashed border-[#cbd5e1] rounded-lg flex items-center justify-center text-xs text-secondary"><span class="material-symbols-outlined text-[14px]">add</span> Trống</div></td>
<td class="p-2 text-center bg-[#fafafa]"><span class="text-xs text-gray-400">Nghỉ</span></td>
</tr>
</tbody>
</table>
</div>
<!-- Timetable Footer status summary -->
<div class="flex items-center justify-between text-xs text-secondary pt-2">
<div class="flex items-center gap-4">
<span>Tổng số tiết đã xếp trong tuần: <strong class="text-on-surface">13/72 tiết (18.1%)</strong></span>
<span>Số sinh viên thụ hưởng lịch phòng: <strong class="text-on-surface">216 sinh viên</strong></span>
</div>
<div class="flex items-center gap-2">
<span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
<span>Hệ thống kiểm tra tự động: Không có xung đột thiết bị phòng học</span>
</div>
</div>
</section>
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
