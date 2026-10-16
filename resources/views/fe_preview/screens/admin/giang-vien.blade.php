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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'admin/giang-vien']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">co_present</span>
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
<!-- Top Title Bar & Breadcrumb context -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
<div>
<h1 class="font-headline-lg text-headline-lg font-bold text-on-surface tracking-tight">
            Quản lý Giảng viên &amp; Hồ sơ Học vụ
          </h1>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">
            Dữ liệu nhân sự học thuật, phân bổ định mức giảng dạy và giám sát học phần trực thuộc các Khoa viện HUIT.
          </p>
</div>
<!-- State Switcher UI Buttons for testing states -->
<div class="inline-flex items-center p-1 bg-surface-container-lowest border border-outline-variant rounded-lg shadow-sm">
<span class="font-label-sm text-label-sm text-outline px-2 font-medium">Chế độ xem:</span>
<button class="px-2.5 py-1 rounded font-label-sm text-label-sm font-semibold bg-primary text-white shadow-xs transition-all" id="stateNormalBtn" onclick="switchState('normal')">
            Bình thường (642)
          </button>
<button class="px-2.5 py-1 rounded font-label-sm text-label-sm text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low transition-all" id="stateLoadingBtn" onclick="switchState('loading')">
            Đang tải (Skeleton)
          </button>
<button class="px-2.5 py-1 rounded font-label-sm text-label-sm text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low transition-all" id="stateEmptyBtn" onclick="switchState('empty')">
            Danh sách rỗng
          </button>
<button class="px-2.5 py-1 rounded font-label-sm text-label-sm text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low transition-all" id="stateErrorBtn" onclick="switchState('error')">
            Lỗi kết nối
          </button>
</div>
</div>
<!-- ===================================================================== -->
<!-- 3. THẺ THỐNG KÊ TỔNG QUAN (4 KPI CARDS)                               -->
<!-- ===================================================================== -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
<!-- KPI 1 -->
<div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant shadow-sm hover:shadow transition-shadow flex items-center justify-between">
<div>
<p class="font-label-md text-label-md text-on-surface-variant font-medium">Tổng số giảng viên</p>
<div class="flex items-baseline gap-2 mt-1">
<span class="font-headline-xl text-headline-xl font-bold text-on-surface">680</span>
<span class="inline-flex items-center text-xs font-semibold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded-full border border-emerald-200">
<span class="material-symbols-outlined text-xs mr-0.5" data-icon="trending_up">trending_up</span> +14
              </span>
</div>
<p class="font-body-sm text-body-sm text-outline mt-1">So với năm học 2025-2026</p>
</div>
<div class="w-12 h-12 rounded-xl bg-surface-container-low text-primary flex items-center justify-center border border-primary/20">
<span class="material-symbols-outlined text-2xl" data-icon="groups">groups</span>
</div>
</div>
<!-- KPI 2 -->
<div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant shadow-sm hover:shadow transition-shadow flex items-center justify-between">
<div>
<p class="font-label-md text-label-md text-on-surface-variant font-medium">Đang giảng dạy (Active)</p>
<div class="flex items-baseline gap-2 mt-1">
<span class="font-headline-xl text-headline-xl font-bold text-emerald-700">642</span>
<span class="font-label-sm text-label-sm font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                94.4%
              </span>
</div>
<p class="font-body-sm text-body-sm text-outline mt-1">Biên chế &amp; thỉnh giảng phụ trách lớp</p>
</div>
<div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center border border-emerald-200">
<span class="material-symbols-outlined text-2xl" data-icon="cast_for_education">cast_for_education</span>
</div>
</div>
<!-- KPI 3 -->
<div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant shadow-sm hover:shadow transition-shadow flex items-center justify-between">
<div>
<p class="font-label-md text-label-md text-on-surface-variant font-medium">Tạm nghỉ / Công tác</p>
<div class="flex items-baseline gap-2 mt-1">
<span class="font-headline-xl text-headline-xl font-bold text-amber-700">26</span>
<span class="font-label-sm text-label-sm font-semibold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">
                Theo dõi
              </span>
</div>
<p class="font-body-sm text-body-sm text-outline mt-1">Nghỉ thai sản, du học, tu nghiệp</p>
</div>
<div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center border border-amber-200">
<span class="material-symbols-outlined text-2xl" data-icon="event_busy">event_busy</span>
</div>
</div>
<!-- KPI 4 -->
<div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant shadow-sm hover:shadow transition-shadow flex items-center justify-between">
<div>
<p class="font-label-md text-label-md text-on-surface-variant font-medium">Đã nghỉ việc / Chuyển CT</p>
<div class="flex items-baseline gap-2 mt-1">
<span class="font-headline-xl text-headline-xl font-bold text-slate-600">12</span>
<span class="font-label-sm text-label-sm text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full border border-slate-200">
                1.7%
              </span>
</div>
<p class="font-body-sm text-body-sm text-outline mt-1">Hồ sơ đã lưu trữ học vụ</p>
</div>
<div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center border border-slate-200">
<span class="material-symbols-outlined text-2xl" data-icon="person_off">person_off</span>
</div>
</div>
</div>
<!-- ===================================================================== -->
<!-- 4. THANH CÔNG CỤ & BỘ LỌC (TOOLBAR & FILTERS)                          -->
<!-- ===================================================================== -->
<div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant shadow-sm space-y-3">
<div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
<!-- Filters Group -->
<div class="flex flex-wrap items-center gap-2.5 flex-1">
<!-- Search Input -->
<div class="relative min-w-[260px] flex-1 sm:max-w-xs">
<span class="material-symbols-outlined absolute left-3 top-2.5 text-outline text-lg" data-icon="search">search</span>
<input aria-label="Tìm mã GV, họ tên, email..." class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg pl-9 pr-3 py-2 font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:border-primary focus:ring-2 focus:ring-primary/20" placeholder="Tìm mã GV, họ tên, email..." type="text"/>
</div>
<!-- Filter: Khoa / Đơn vị -->
<div class="min-w-[190px]">
<select aria-label="Tất cả các khoa" class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg px-3 py-2 font-body-sm text-body-sm text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/20">
<option value="">Tất cả các khoa</option>
<option selected="" value="cntt">Khoa Công nghệ Thông tin</option>
<option value="cntp">Khoa Công nghệ Thực phẩm</option>
<option value="qtkd">Khoa Quản trị Kinh doanh</option>
<option value="tckt">Khoa Tài chính - Kế toán</option>
<option value="dtt">Khoa Điện - Điện tử</option>
</select>
</div>
<!-- Filter: Học vị -->
<div class="min-w-[150px]">
<select aria-label="Tất cả học vị" class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg px-3 py-2 font-body-sm text-body-sm text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/20">
<option value="">Tất cả học vị</option>
<option value="gs">Giáo sư</option>
<option value="pgs">Phó Giáo sư</option>
<option value="ts">Tiến sĩ</option>
<option value="ths">Thạc sĩ</option>
<option value="ks">Kỹ sư / Cử nhân</option>
</select>
</div>
<!-- Filter: Trạng thái -->
<div class="min-w-[160px]">
<select aria-label="Tất cả trạng thái" class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg px-3 py-2 font-body-sm text-body-sm text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/20">
<option value="">Tất cả trạng thái</option>
<option value="active">Đang giảng dạy</option>
<option value="pause">Tạm nghỉ</option>
<option value="leave">Đã nghỉ việc</option>
</select>
</div>
<button class="p-2 border border-outline-variant hover:bg-surface-container-low rounded-lg text-outline hover:text-on-surface transition-colors" title="Đặt lại bộ lọc">
<span class="material-symbols-outlined" data-icon="refresh">refresh</span>
</button>
</div>
<!-- Action Buttons Group -->
<div class="flex items-center gap-2 self-end lg:self-center shrink-0">
<!-- Xuất Excel -->
<button class="inline-flex items-center gap-1.5 px-3 py-2 bg-surface-container-lowest border border-outline-variant hover:bg-surface-container-low rounded-lg font-label-md text-label-md text-on-surface font-medium transition-colors shadow-xs">
<span class="material-symbols-outlined text-outline text-lg" data-icon="file_download">file_download</span>
<span>Xuất Excel</span>
</button>
<!-- Nhập CSV -->
<button class="inline-flex items-center gap-1.5 px-3 py-2 bg-surface-container-lowest border border-outline-variant hover:bg-surface-container-low rounded-lg font-label-md text-label-md text-on-surface font-medium transition-colors shadow-xs">
<span class="material-symbols-outlined text-outline text-lg" data-icon="upload_file">upload_file</span>
<span>Nhập file</span>
</button>
<!-- Thêm giảng viên -->
<button class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary hover:bg-[#1647a8] active:bg-[#123c91] text-white rounded-lg font-label-md text-label-md font-semibold transition-all shadow-sm" onclick="openModal('addTeacherModal')">
<span class="material-symbols-outlined text-lg" data-icon="add">add</span>
<span>Thêm giảng viên mới</span>
</button>
</div>
</div>
</div>
<!-- ===================================================================== -->
<!-- 5. BẢNG DANH SÁCH GIẢNG VIÊN (DATA TABLE WITH STICKY COLUMNS)         -->
<!-- ===================================================================== -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden flex flex-col">
<!-- Table Scroll Container -->
<div class="overflow-x-auto w-full" id="tableContainer">
<!-- NORMAL STATE TABLE -->
<table class="w-full text-left border-collapse min-w-[1180px]" id="stateNormal">
<thead>
<tr class="bg-surface-bright border-b border-outline-variant font-label-md text-label-md text-outline uppercase tracking-wider select-none">
<!-- Sticky Left 1: Checkbox -->
<th class="py-3 px-3 w-10 sticky left-0 z-20 bg-surface-bright border-r border-outline-variant/60 text-center">
<input aria-label="Chọn dòng" class="rounded border-outline-variant text-primary focus:ring-primary/20 h-4 w-4" type="checkbox"/>
</th>
<!-- Sticky Left 2: Mã GV -->
<th class="py-3 px-3 w-28 sticky left-10 z-20 bg-surface-bright border-r border-outline-variant/60 font-semibold">
                  Mã GV
                </th>
<!-- Sticky Left 3: Giảng viên & Email -->
<th class="py-3 px-4 w-64 sticky left-38 z-20 bg-surface-bright border-r border-outline-variant shadow-[4px_0_6px_-2px_rgba(0,0,0,0.05)] font-semibold">
                  Họ và tên giảng viên
                </th>
<!-- Columns -->
<th class="py-3 px-4 font-semibold">Đơn vị / Khoa</th>
<th class="py-3 px-4 font-semibold">Bộ môn trực thuộc</th>
<th class="py-3 px-3 text-center font-semibold">Học vị</th>
<th class="py-3 px-3 text-center font-semibold">Lớp HP (HK1)</th>
<th class="py-3 px-4 text-center font-semibold">Giờ chuẩn / Thực</th>
<th class="py-3 px-4 text-center font-semibold">Trạng thái</th>
<th class="py-3 px-4 text-right font-semibold pr-6">Thao tác</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant/70 font-body-sm text-body-sm">
<!-- Dòng 1: TS. Người dùng mẫu 38 (Selected highlight) -->
<tr class="hover:bg-surface-container-low/60 transition-colors bg-surface-container-low/30 group">
<td class="py-3.5 px-3 sticky left-0 z-10 bg-surface-container-lowest group-hover:bg-surface-container-low/60 border-r border-outline-variant/60 text-center">
<input aria-label="Chọn dòng" checked="" class="rounded border-outline-variant text-primary focus:ring-primary/20 h-4 w-4" type="checkbox"/>
</td>
<td class="py-3.5 px-3 sticky left-10 z-10 bg-surface-container-lowest group-hover:bg-surface-container-low/60 border-r border-outline-variant/60 font-semibold font-mono text-primary">
                  GV201809
                </td>
<td class="py-3.5 px-4 sticky left-38 z-10 bg-surface-container-lowest group-hover:bg-surface-container-low/60 border-r border-outline-variant shadow-[4px_0_6px_-2px_rgba(0,0,0,0.05)]">
<div class="flex items-center gap-3">
<img alt="" class="w-9 h-9 rounded-full object-cover border border-outline-variant shrink-0" data-alt="A distinguished Vietnamese male professor in his late thirties wearing modern academic glasses and a sharp navy blue blazer against an architectural university background with clean institutional blue tones and professional soft daylight." src="https://lh3.googleusercontent.com/aida-public/AB6AXuCAlW5Gaz4l5_NHbITOoccJ60-psjepeBQHpzmqEXWxLjOIK3gJLJsER1B7ahXzfb6qu9QxQ7I4OXcrCG8tPKYC0ERcGbVAz2o7Hbbw2deo8-dierXSW4zuj-WbWaVCVEN_iZ1WZPjBVOvuW0Z8T7ZOVUhanQgGOClrlEtEQkbIbesFkuLorwUVo_AB-bZkLt5P7LPrZk9ig9Rir24EciKWAuTTUdiql2eHLMWPCYAKVKbO6yZIOM4LDFPSd5wRMCAY1JjZNXC9clc"/>
<div class="min-w-0">
<p class="font-label-lg text-label-lg font-bold text-on-surface truncate">TS. Người dùng mẫu 38</p>
<p class="text-xs text-on-surface-variant truncate">nguoidung037@example.test</p>
</div>
</div>
</td>
<td class="py-3.5 px-4">
<span class="font-medium text-on-surface">Khoa Công nghệ Thông tin</span>
</td>
<td class="py-3.5 px-4 text-on-surface-variant">
                  Bộ môn Mạng &amp; ATTT
                </td>
<td class="py-3.5 px-3 text-center">
<span class="inline-block px-2 py-0.5 rounded text-xs font-semibold bg-surface-container-high text-primary border border-primary/20">
                    Tiến sĩ
                  </span>
</td>
<td class="py-3.5 px-3 text-center">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                    3 lớp HP
                  </span>
</td>
<td class="py-3.5 px-4 text-center font-mono">
<span class="font-semibold text-emerald-700">120</span> / 120 tiết
                </td>
<td class="py-3.5 px-4 text-center">
<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                    Đang giảng dạy
                  </span>
</td>
<td class="py-3.5 px-4 text-right pr-6">
<div class="flex items-center justify-end gap-1.5">
<button class="px-2.5 py-1 rounded bg-surface-container hover:bg-surface-container-high text-primary font-label-md text-label-md font-semibold transition-colors flex items-center gap-1" onclick="openDrawer()" title="Xem hồ sơ">
<span class="material-symbols-outlined text-sm" data-icon="visibility">visibility</span>
<span>Hồ sơ</span>
</button>
<button class="p-1 text-on-surface-variant hover:text-primary hover:bg-surface-container rounded" onclick="openModal('addTeacherModal')" title="Sửa thông tin">
<span class="material-symbols-outlined text-lg" data-icon="edit">edit</span>
</button>
<button class="p-1 text-on-surface-variant hover:text-amber-700 hover:bg-amber-50 rounded" onclick="openModal('changeStatusModal')" title="Đổi trạng thái học vụ">
<span class="material-symbols-outlined text-lg" data-icon="sync_alt">sync_alt</span>
</button>
</div>
</td>
</tr>
<!-- Dòng 2: PGS.TS. Người dùng mẫu 15 -->
<tr class="hover:bg-surface-container-low/60 transition-colors group">
<td class="py-3.5 px-3 sticky left-0 z-10 bg-surface-container-lowest group-hover:bg-surface-container-low/60 border-r border-outline-variant/60 text-center">
<input aria-label="Chọn dòng" class="rounded border-outline-variant text-primary focus:ring-primary/20 h-4 w-4" type="checkbox"/>
</td>
<td class="py-3.5 px-3 sticky left-10 z-10 bg-surface-container-lowest group-hover:bg-surface-container-low/60 border-r border-outline-variant/60 font-semibold font-mono text-primary">
                  GV201502
                </td>
<td class="py-3.5 px-4 sticky left-38 z-10 bg-surface-container-lowest group-hover:bg-surface-container-low/60 border-r border-outline-variant shadow-[4px_0_6px_-2px_rgba(0,0,0,0.05)]">
<div class="flex items-center gap-3">
<img alt="" class="w-9 h-9 rounded-full object-cover border border-outline-variant shrink-0" data-alt="A senior Vietnamese female associate professor with warm authoritative expression dressed in a formal beige silk blouse inside a modern academic campus library illuminated by soft natural light with a collegiate backdrop." src="https://lh3.googleusercontent.com/aida-public/AB6AXuCC0XakoeHTPCkuHdZPMjg4mRyQwt9GxTk_xGHG8ShmhidgBA5XACtKJcnn1vpu_ylbRt7ZS1q2JPWZGLCrNL8ZMWgMu87KBv6w8k4NN3_uQrkyJA2iLKal3kDooSabuUsjJsJ4L8H2Dxcztnv_MHvflWttEpju6ZUVtGDETzpHlAhKBS7YVKIGuC2uaVuCS52Zwh1DsjFw-q9kwnn-Cb4vL1f4MolVw6AZ08_rn3LHjBYamZudKK0ss-zjd589Cb-xChUoXyHhdU0"/>
<div class="min-w-0">
<p class="font-label-lg text-label-lg font-bold text-on-surface truncate">PGS.TS. Người dùng mẫu 15</p>
<p class="text-xs text-on-surface-variant truncate">nguoidung033@example.test</p>
</div>
</div>
</td>
<td class="py-3.5 px-4">
<span class="font-medium text-on-surface">Khoa Công nghệ Thông tin</span>
</td>
<td class="py-3.5 px-4 text-on-surface-variant">
                  Bộ môn Hệ thống thông tin
                </td>
<td class="py-3.5 px-3 text-center">
<span class="inline-block px-2 py-0.5 rounded text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                    PGS.TS
                  </span>
</td>
<td class="py-3.5 px-3 text-center">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                    2 lớp HP
                  </span>
</td>
<td class="py-3.5 px-4 text-center font-mono">
<span class="font-semibold text-emerald-700">90</span> / 90 tiết
                </td>
<td class="py-3.5 px-4 text-center">
<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                    Đang giảng dạy
                  </span>
</td>
<td class="py-3.5 px-4 text-right pr-6">
<div class="flex items-center justify-end gap-1.5">
<button class="px-2.5 py-1 rounded bg-surface-container hover:bg-surface-container-high text-primary font-label-md text-label-md font-semibold transition-colors flex items-center gap-1" onclick="openDrawer()">
<span class="material-symbols-outlined text-sm" data-icon="visibility">visibility</span>
<span>Hồ sơ</span>
</button>
<button class="p-1 text-on-surface-variant hover:text-primary hover:bg-surface-container rounded" onclick="openModal('addTeacherModal')" title="Sửa">
<span class="material-symbols-outlined text-lg" data-icon="edit">edit</span>
</button>
<button class="p-1 text-on-surface-variant hover:text-amber-700 hover:bg-amber-50 rounded" onclick="openModal('changeStatusModal')" title="Đổi trạng thái">
<span class="material-symbols-outlined text-lg" data-icon="sync_alt">sync_alt</span>
</button>
</div>
</td>
</tr>
<!-- Dòng 3: ThS. Người dùng mẫu 29 -->
<tr class="hover:bg-surface-container-low/60 transition-colors group">
<td class="py-3.5 px-3 sticky left-0 z-10 bg-surface-container-lowest group-hover:bg-surface-container-low/60 border-r border-outline-variant/60 text-center">
<input aria-label="Chọn dòng" class="rounded border-outline-variant text-primary focus:ring-primary/20 h-4 w-4" type="checkbox"/>
</td>
<td class="py-3.5 px-3 sticky left-10 z-10 bg-surface-container-lowest group-hover:bg-surface-container-low/60 border-r border-outline-variant/60 font-semibold font-mono text-primary">
                  GV202014
                </td>
<td class="py-3.5 px-4 sticky left-38 z-10 bg-surface-container-lowest group-hover:bg-surface-container-low/60 border-r border-outline-variant shadow-[4px_0_6px_-2px_rgba(0,0,0,0.05)]">
<div class="flex items-center gap-3">
<img alt="" class="w-9 h-9 rounded-full object-cover border border-outline-variant shrink-0" data-alt="A young dynamic Asian software engineering lecturer in his early thirties with clean hair cut wearing smart casual light blue shirt in front of an IT laboratory setting with high key cool educational ambient lighting." src="https://lh3.googleusercontent.com/aida-public/AB6AXuD_lCM4GQPjUXL0a0eUKGR-AqZkn-GwG9GjcqvKWQpObzHZ8j7oT7G-peTuOH3nyEj0QHy3Eg6dmNre5qa99spS-1eCcx7a2lyjXMvBgXEDjrXcWaa0tNKo9yaqRsrxK8kQERF2jK7NxwNjx65J_pJhdxjoc7rAt8FXY-FlPzTGOGxpRceHfKyNzD4SZ9p3pzMdBBZYsKNm65sy4QkQZ3tlWPvnLvaZItd60iBDDN2hLETK2pdauOd2l54figLZkYx1-gjAeeGuRPw"/>
<div class="min-w-0">
<p class="font-label-lg text-label-lg font-bold text-on-surface truncate">ThS. Người dùng mẫu 29</p>
<p class="text-xs text-on-surface-variant truncate">nguoidung039@example.test</p>
</div>
</div>
</td>
<td class="py-3.5 px-4">
<span class="font-medium text-on-surface">Khoa Công nghệ Thông tin</span>
</td>
<td class="py-3.5 px-4 text-on-surface-variant">
                  Bộ môn Công nghệ phần mềm
                </td>
<td class="py-3.5 px-3 text-center">
<span class="inline-block px-2 py-0.5 rounded text-xs font-semibold bg-surface-container text-on-surface-variant border border-outline-variant">
                    Thạc sĩ
                  </span>
</td>
<td class="py-3.5 px-3 text-center">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                    4 lớp HP
                  </span>
</td>
<td class="py-3.5 px-4 text-center font-mono">
<span class="font-semibold text-amber-700">150</span> / 120 tiết <span class="text-[11px] text-amber-700">(Vượt)</span>
</td>
<td class="py-3.5 px-4 text-center">
<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                    Đang giảng dạy
                  </span>
</td>
<td class="py-3.5 px-4 text-right pr-6">
<div class="flex items-center justify-end gap-1.5">
<button class="px-2.5 py-1 rounded bg-surface-container hover:bg-surface-container-high text-primary font-label-md text-label-md font-semibold transition-colors flex items-center gap-1" onclick="openDrawer()">
<span class="material-symbols-outlined text-sm" data-icon="visibility">visibility</span>
<span>Hồ sơ</span>
</button>
<button class="p-1 text-on-surface-variant hover:text-primary hover:bg-surface-container rounded" onclick="openModal('addTeacherModal')" title="Sửa">
<span class="material-symbols-outlined text-lg" data-icon="edit">edit</span>
</button>
<button class="p-1 text-on-surface-variant hover:text-amber-700 hover:bg-amber-50 rounded" onclick="openModal('changeStatusModal')" title="Đổi trạng thái">
<span class="material-symbols-outlined text-lg" data-icon="sync_alt">sync_alt</span>
</button>
</div>
</td>
</tr>
<!-- Dòng 4: ThS. Người dùng mẫu 33 (Tạm nghỉ) -->
<tr class="hover:bg-surface-container-low/60 transition-colors group">
<td class="py-3.5 px-3 sticky left-0 z-10 bg-surface-container-lowest group-hover:bg-surface-container-low/60 border-r border-outline-variant/60 text-center">
<input aria-label="Chọn dòng" class="rounded border-outline-variant text-primary focus:ring-primary/20 h-4 w-4" type="checkbox"/>
</td>
<td class="py-3.5 px-3 sticky left-10 z-10 bg-surface-container-lowest group-hover:bg-surface-container-low/60 border-r border-outline-variant/60 font-semibold font-mono text-primary">
                  GV201940
                </td>
<td class="py-3.5 px-4 sticky left-38 z-10 bg-surface-container-lowest group-hover:bg-surface-container-low/60 border-r border-outline-variant shadow-[4px_0_6px_-2px_rgba(0,0,0,0.05)]">
<div class="flex items-center gap-3">
<img alt="" class="w-9 h-9 rounded-full object-cover border border-outline-variant shrink-0" data-alt="A portrait of a Vietnamese female food technology university lecturer smiling politely with polite neat hair style against a research laboratory glass wall with bright diffuse natural lighting." src="https://lh3.googleusercontent.com/aida-public/AB6AXuDWPvDY-vNZY7bluVFkQbMNwtEAWjPLyz1IgBZN7s-dRNDVqJwFmrargLeUNayU07RYUt1uQyouEwDn-Yac5v9IetinFKR6fnQil0-r2VQ-xqHjNE-dNmxRlfe1yKR8BFuco-mSyZcK9niL0v3hTgbLRvQE9Ns2SHA_daBYb0PBr0zrt9B6ruYN3odkV55siEBZIf8m6KtMCCyryIlx-rkMnuwEGaXSxgXKGwJ0tKeNdO8052nXd499UFZL__EykotLqSeMkt2K24M"/>
<div class="min-w-0">
<p class="font-label-lg text-label-lg font-bold text-on-surface truncate">ThS. Người dùng mẫu 33</p>
<p class="text-xs text-on-surface-variant truncate">nguoidung030@example.test</p>
</div>
</div>
</td>
<td class="py-3.5 px-4">
<span class="font-medium text-on-surface">Khoa CN Thực phẩm</span>
</td>
<td class="py-3.5 px-4 text-on-surface-variant">
                  Bộ môn Hóa sinh
                </td>
<td class="py-3.5 px-3 text-center">
<span class="inline-block px-2 py-0.5 rounded text-xs font-semibold bg-surface-container text-on-surface-variant border border-outline-variant">
                    Thạc sĩ
                  </span>
</td>
<td class="py-3.5 px-3 text-center">
<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                    0 lớp
                  </span>
</td>
<td class="py-3.5 px-4 text-center font-mono text-outline">
                  0 / 0 tiết
                </td>
<td class="py-3.5 px-4 text-center">
<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-300">
<span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                    Tạm nghỉ (Thai sản)
                  </span>
</td>
<td class="py-3.5 px-4 text-right pr-6">
<div class="flex items-center justify-end gap-1.5">
<button class="px-2.5 py-1 rounded bg-surface-container hover:bg-surface-container-high text-primary font-label-md text-label-md font-semibold transition-colors flex items-center gap-1" onclick="openDrawer()">
<span class="material-symbols-outlined text-sm" data-icon="visibility">visibility</span>
<span>Hồ sơ</span>
</button>
<button class="p-1 text-on-surface-variant hover:text-primary hover:bg-surface-container rounded" onclick="openModal('addTeacherModal')" title="Sửa">
<span class="material-symbols-outlined text-lg" data-icon="edit">edit</span>
</button>
<button class="p-1 text-on-surface-variant hover:text-amber-700 hover:bg-amber-50 rounded" onclick="openModal('changeStatusModal')" title="Đổi trạng thái">
<span class="material-symbols-outlined text-lg" data-icon="sync_alt">sync_alt</span>
</button>
</div>
</td>
</tr>
<!-- Dòng 5: TS. Người dùng mẫu 52 (Đã nghỉ việc) -->
<tr class="hover:bg-surface-container-low/60 transition-colors group opacity-80">
<td class="py-3.5 px-3 sticky left-0 z-10 bg-surface-container-lowest group-hover:bg-surface-container-low/60 border-r border-outline-variant/60 text-center">
<input aria-label="Chọn dòng" class="rounded border-outline-variant text-primary focus:ring-primary/20 h-4 w-4" type="checkbox"/>
</td>
<td class="py-3.5 px-3 sticky left-10 z-10 bg-surface-container-lowest group-hover:bg-surface-container-low/60 border-r border-outline-variant/60 font-semibold font-mono text-outline">
                  GV201625
                </td>
<td class="py-3.5 px-4 sticky left-38 z-10 bg-surface-container-lowest group-hover:bg-surface-container-low/60 border-r border-outline-variant shadow-[4px_0_6px_-2px_rgba(0,0,0,0.05)]">
<div class="flex items-center gap-3">
<img alt="" class="w-9 h-9 rounded-full object-cover border border-outline-variant shrink-0 grayscale" data-alt="A portrait of a male business administration scholar in his forties with grey rimmed glasses and formal grey suit against an understated modern lecture hall background." src="https://lh3.googleusercontent.com/aida-public/AB6AXuDTtcACtrev-m_SidELV816vwzu-nuI5-TD9liUdV6OCfT7d81V-IH2Pzp-EzaDA8X-ka28BrraiwbQBcvqD9_fKhVss3WyVodAOg45PTasFmo5wKBmQKKilpNVSnybJGj6nOHgIoLXKanjDvWwt_93vJWZp4W1_AUogx3_CMoE77qSVx3hX4OGaLeHG0i0pqHNzSnXPfsEWm_rUxB0UShrjQJYs0kYmkQwnhYw3sTE8xkIDSdH_temNWag1Ebzy2HD9oYtboro4fc"/>
<div class="min-w-0">
<p class="font-label-lg text-label-lg font-bold text-on-surface truncate">TS. Người dùng mẫu 52</p>
<p class="text-xs text-on-surface-variant truncate">nguoidung020@example.test</p>
</div>
</div>
</td>
<td class="py-3.5 px-4">
<span class="font-medium text-on-surface">Khoa Quản trị Kinh doanh</span>
</td>
<td class="py-3.5 px-4 text-on-surface-variant">
                  Bộ môn Quản trị chiến lược
                </td>
<td class="py-3.5 px-3 text-center">
<span class="inline-block px-2 py-0.5 rounded text-xs font-semibold bg-surface-container text-on-surface-variant border border-outline-variant">
                    Tiến sĩ
                  </span>
</td>
<td class="py-3.5 px-3 text-center">
<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-500">
                    0 lớp
                  </span>
</td>
<td class="py-3.5 px-4 text-center font-mono text-outline">
                  0 tiết
                </td>
<td class="py-3.5 px-4 text-center">
<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-200 text-slate-700 border border-slate-300">
<span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                    Đã nghỉ việc
                  </span>
</td>
<td class="py-3.5 px-4 text-right pr-6">
<div class="flex items-center justify-end gap-1.5">
<button class="px-2.5 py-1 rounded bg-surface-container hover:bg-surface-container-high text-primary font-label-md text-label-md font-semibold transition-colors flex items-center gap-1" onclick="openDrawer()">
<span class="material-symbols-outlined text-sm" data-icon="visibility">visibility</span>
<span>Hồ sơ</span>
</button>
<button class="p-1 text-on-surface-variant hover:text-primary hover:bg-surface-container rounded" onclick="openModal('addTeacherModal')" title="Sửa">
<span class="material-symbols-outlined text-lg" data-icon="edit">edit</span>
</button>
<button class="p-1 text-on-surface-variant hover:text-amber-700 hover:bg-amber-50 rounded" onclick="openModal('changeStatusModal')" title="Đổi trạng thái">
<span class="material-symbols-outlined text-lg" data-icon="sync_alt">sync_alt</span>
</button>
</div>
</td>
</tr>
</tbody>
</table>
<!-- SKELETON LOADING STATE (HIDDEN BY DEFAULT) -->
<div class="hidden p-6 space-y-4" id="stateLoading">
<div class="flex items-center gap-4">
<div class="w-8 h-8 bg-surface-container-high rounded animate-pulse"></div>
<div class="h-4 bg-surface-container-high rounded w-48 animate-pulse"></div>
<div class="h-4 bg-surface-container-high rounded w-32 animate-pulse"></div>
</div>
<div class="space-y-3">
<div class="h-12 bg-surface-container-low rounded-lg w-full animate-pulse"></div>
<div class="h-12 bg-surface-container-low rounded-lg w-full animate-pulse"></div>
<div class="h-12 bg-surface-container-low rounded-lg w-full animate-pulse"></div>
<div class="h-12 bg-surface-container-low rounded-lg w-full animate-pulse"></div>
<div class="h-12 bg-surface-container-low rounded-lg w-full animate-pulse"></div>
</div>
</div>
<!-- EMPTY STATE (HIDDEN BY DEFAULT) -->
<div class="hidden py-16 px-4 text-center" id="stateEmpty">
<div class="w-16 h-16 mx-auto mb-3 rounded-full bg-surface-container flex items-center justify-center text-outline">
<span class="material-symbols-outlined text-3xl" data-icon="person_search">person_search</span>
</div>
<h3 class="font-headline-sm text-headline-sm font-semibold text-on-surface">Không tìm thấy giảng viên nào</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-1 max-w-sm mx-auto">
              Không có dữ liệu thỏa mãn điều kiện lọc. Vui lòng thử tìm kiếm với từ khóa khác hoặc thiết lập lại bộ lọc.
            </p>
<button class="mt-4 px-4 py-2 rounded-lg bg-primary text-white font-label-md text-label-md font-semibold" onclick="switchState('normal')">
              Xóa bộ lọc
            </button>
</div>
<!-- ERROR STATE (HIDDEN BY DEFAULT) -->
<div class="hidden py-16 px-4 text-center" id="stateError">
<div class="w-16 h-16 mx-auto mb-3 rounded-full bg-error-container/50 text-error flex items-center justify-center">
<span class="material-symbols-outlined text-3xl" data-icon="cloud_off">cloud_off</span>
</div>
<h3 class="font-headline-sm text-headline-sm font-semibold text-on-surface">Lỗi kết nối CSDL Đào tạo</h3>
<p class="font-body-sm text-body-sm text-error mt-1 max-w-sm mx-auto">
              Mã lỗi: ERR_ACADEMIC_SERVER_TIMEOUT. Không thể đồng bộ danh sách nhân sự từ máy chủ HUIT Core.
            </p>
<button class="mt-4 px-4 py-2 rounded-lg bg-surface-container hover:bg-surface-container-high text-primary font-label-md text-label-md font-semibold border border-outline-variant" onclick="switchState('normal')">
              Thử kết nối lại
            </button>
</div>
</div>
<!-- Table Pagination -->
<div class="px-6 py-3.5 bg-surface-container-lowest border-t border-outline-variant flex flex-col sm:flex-row items-center justify-between gap-3">
<div class="flex items-center gap-3">
<span class="font-body-sm text-body-sm text-on-surface-variant">
              Hiển thị <span class="font-semibold text-on-surface">1 - 5</span> trong <span class="font-semibold text-on-surface">642</span> giảng viên
            </span>
<div class="flex items-center gap-1.5 text-xs text-outline">
<span>Số dòng:</span>
<select aria-label="10" class="bg-surface-container-lowest border border-outline-variant rounded px-2 py-0.5 text-xs font-semibold text-on-surface focus:ring-1 focus:ring-primary">
<option value="10">10</option>
<option value="20">20</option>
<option value="50">50</option>
</select>
</div>
</div>
<div class="flex items-center gap-1">
<button class="p-1 rounded text-outline hover:text-on-surface hover:bg-surface-container disabled:opacity-40" disabled="" title="Trang trước">
<span class="material-symbols-outlined text-lg" data-icon="chevron_left">chevron_left</span>
</button>
<button class="w-7 h-7 rounded text-xs font-semibold bg-primary text-white">1</button>
<button class="w-7 h-7 rounded text-xs font-semibold text-on-surface-variant hover:bg-surface-container">2</button>
<button class="w-7 h-7 rounded text-xs font-semibold text-on-surface-variant hover:bg-surface-container">3</button>
<span class="px-1 text-xs text-outline">...</span>
<button class="w-7 h-7 rounded text-xs font-semibold text-on-surface-variant hover:bg-surface-container">65</button>
<button class="p-1 rounded text-outline hover:text-on-surface hover:bg-surface-container" title="Trang sau">
<span class="material-symbols-outlined text-lg" data-icon="chevron_right">chevron_right</span>
</button>
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
