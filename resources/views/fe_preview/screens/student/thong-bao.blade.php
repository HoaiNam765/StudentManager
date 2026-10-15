@extends('fe_preview.layouts.student')
@section('page_title', 'Cổng Sinh viên HUIT - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Cổng Sinh viên HUIT - HUIT Học Vụ</title>
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
<span class="material-symbols-outlined text-[20px]">school</span>
</div>
<div class="overflow-hidden">
<div class="font-bold text-[13.5px] text-slate-900 leading-tight truncate">HUIT Học Vụ</div>
<div class="text-[11px] text-slate-500 font-medium truncate">Cổng Sinh viên</div>
</div>
</div>
<button class="lg:hidden p-1.5 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100" onclick="toggleSidebar()">
<span class="material-symbols-outlined text-[20px]">close</span>
</button>
</div>
<!-- Navigation Menu (Dynamic by Stage) -->
<div class="flex-1 overflow-y-auto custom-scrollbar p-3 space-y-6">
<div class="space-y-1">
<div class="px-3 py-1.5 text-[10.5px] font-bold tracking-wider text-slate-500 uppercase">HỌC TẬP &amp; ĐÀO TẠO</div>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/tong-quan']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">dashboard</span>
<span class="truncate">Bàn làm việc</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/dang-ky-hoc-phan']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">how_to_reg</span>
<span class="truncate">Đăng ký học phần</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/ket-qua-dang-ky']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">fact_check</span>
<span class="truncate">Kết quả đăng ký</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/thoi-khoa-bieu']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">calendar_today</span>
<span class="truncate">Thời khóa biểu</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/ket-qua-hoc-tap']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">grade</span>
<span class="truncate">Kết quả học tập</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/chuong-trinh-dao-tao']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">account_tree</span>
<span class="truncate">Chương trình đào tạo</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/lich-thi']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">event_available</span>
<span class="truncate">Lịch thi</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/chuyen-can']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">rule</span>
<span class="truncate">Chuyên cần</span>
</a>
</div>
<div class="space-y-1">
<div class="px-3 py-1.5 text-[10.5px] font-bold tracking-wider text-slate-500 uppercase">HÀNH CHÍNH &amp; TÀI CHÍNH</div>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/hoc-phi']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">payments</span>
<span class="truncate">Học phí &amp; Thanh toán</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/ho-so-giay-to']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">folder_shared</span>
<span class="truncate">Hồ sơ &amp; Giấy tờ</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/don-tu']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">assignment</span>
<span class="truncate">Đơn từ trực tuyến</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/hoc-bong-ren-luyen']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">military_tech</span>
<span class="truncate">Học bổng &amp; Rèn luyện</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/khao-sat']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">quiz</span>
<span class="truncate">Khảo sát chất lượng</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/thanh-toan-online']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">credit_card</span>
<span class="truncate">Thanh toán trực tuyến</span>
</a>
</div>
<div class="space-y-1">
<div class="px-3 py-1.5 text-[10.5px] font-bold tracking-wider text-slate-500 uppercase">TÀI KHOẢN</div>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/ho-so-ca-nhan']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">person</span>
<span class="truncate">Hồ sơ cá nhân</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/thong-bao']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">notifications</span>
<span class="truncate">Thông báo học vụ</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/cai-dat-tai-khoan']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">settings</span>
<span class="truncate">Cài đặt tài khoản</span>
</a>
</div>
</div>
<!-- Sidebar Bottom: Support Hotline -->
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Cổng Sinh viên HUIT</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Cổng Sinh viên HUIT</div>
</div>
</div>
<!-- Header Right: Role Switcher & Student Profile -->
<div class="flex items-center gap-2 sm:gap-3 shrink-0">
<!-- Role quick switch -->
<div class="hidden md:flex items-center p-1 bg-slate-100 rounded-lg text-xs font-medium">
<span class="px-2.5 py-1 rounded-md bg-white text-primary font-semibold shadow-xs">Sinh viên</span>
<a class="px-2.5 py-1 text-slate-600 hover:text-slate-900" href="{{ route('fe.preview.show', ['screen' => 'teacher/lich-giang-day']) }}">Giảng viên</a>
<a class="px-2.5 py-1 text-slate-600 hover:text-slate-900" href="{{ route('fe.preview.show', ['screen' => 'admin/tong-quan']) }}">Phòng ĐT</a>
</div>
<!-- Notification Bell -->
<a class="relative p-2 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/thong-bao']) }}" title="Thông báo">
<span class="material-symbols-outlined text-[21px]">notifications</span>
<span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-500"></span>
</a>
<!-- User Profile Pill -->
<div class="flex items-center gap-2.5 pl-2 border-l border-slate-200">
<div class="w-8 h-8 rounded-full bg-blue-100 text-primary font-bold text-xs flex items-center justify-center border border-blue-200">
            NVA
          </div>
<div class="hidden sm:block text-left">
<div class="text-xs font-semibold text-slate-900 leading-tight">Nguyễn Văn An</div>
<div class="text-[11px] text-slate-500">2001210123 • 12DHTH01</div>
</div>
</div>
</div>
</header>
<!-- MAIN BODY CONTENT -->
<main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
<!-- Tiêu đề trang & Đếm nhanh -->
<div class="flex items-center justify-between mb-4 flex-shrink-0">
<div>
<h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Thông báo học vụ</h1>
<p class="font-body-sm text-body-sm text-secondary mt-0.5">
            Cập nhật kịp thời các thông báo từ Nhà trường, Phòng Đào tạo và Giảng viên phụ trách lớp học phần.
          </p>
</div>
<div class="flex items-center gap-2 px-3 py-1.5 bg-surface-container-low rounded-lg border border-outline-variant/50">
<span class="w-2 h-2 rounded-full bg-primary"></span>
<span class="font-label-md text-label-md text-on-surface font-medium">Tổng số: 18 thông báo</span>
<span class="text-outline-variant">•</span>
<span class="font-label-md text-label-md text-error font-semibold">3 chưa đọc</span>
</div>
</div>
<!-- Tab Lọc phân loại danh mục -->
<div class="flex items-center gap-2 border-b border-outline-variant flex-shrink-0 mb-4 overflow-x-auto">
<button class="flex items-center gap-1.5 px-3.5 py-2 font-label-md text-label-md font-semibold text-primary border-b-2 border-primary -mb-px transition-colors">
<span>Tất cả</span>
<span class="px-1.5 py-0.2 bg-primary/10 text-primary rounded-full text-[11px] font-bold">18</span>
</button>
<button class="flex items-center gap-1.5 px-3.5 py-2 font-label-md text-label-md text-secondary hover:text-on-surface transition-colors">
<span>Chưa đọc</span>
<span class="px-1.5 py-0.5 bg-red-100 text-red-700 rounded-full text-label-sm font-bold">3</span>
</button>
<button class="flex items-center gap-1.5 px-3.5 py-2 font-label-md text-label-md text-secondary hover:text-on-surface transition-colors">
<span>Quan trọng / Khẩn cấp</span>
<span class="px-1.5 py-0.5 bg-amber-100 text-amber-800 rounded-full text-label-sm font-medium">2</span>
</button>
<button class="flex items-center gap-1.5 px-3.5 py-2 font-label-md text-label-md text-secondary hover:text-on-surface transition-colors">
<span>Từ Giảng viên</span>
<span class="px-1.5 py-0.5 bg-surface-container rounded-full text-label-sm font-medium">5</span>
</button>
<button class="flex items-center gap-1.5 px-3.5 py-2 font-label-md text-label-md text-secondary hover:text-on-surface transition-colors">
<span>Từ Phòng Đào tạo</span>
<span class="px-1.5 py-0.5 bg-surface-container rounded-full text-label-sm font-medium">8</span>
</button>
<button class="flex items-center gap-1.5 px-3.5 py-2 font-label-md text-label-md text-secondary hover:text-on-surface transition-colors">
<span>Tài chính - Học vụ</span>
<span class="px-1.5 py-0.5 bg-surface-container rounded-full text-label-sm font-medium">3</span>
</button>
</div>
<!-- Khung hiển thị 2 Cột: 52% List & 48% Chi tiết -->
<div class="flex-1 flex gap-5 overflow-hidden">
<!-- ================= CỘT TRÁI: DANH SÁCH THÔNG BÁO (52%) ================= -->
<section class="w-[52%] flex flex-col h-full bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
<!-- Thanh công cụ lọc & tìm trong danh sách -->
<div class="p-3 border-b border-outline-variant/70 bg-surface flex items-center justify-between flex-shrink-0">
<span class="font-label-md text-label-md text-secondary font-medium">Danh sách hiển thị</span>
<div class="flex items-center gap-2">
<span class="text-body-sm font-body-sm text-secondary">Sắp xếp: Mới nhất</span>
<span class="material-symbols-outlined text-base text-outline">sort</span>
</div>
</div>
<!-- Danh sách cuộn -->
<div class="flex-1 overflow-y-auto divide-y divide-outline-variant/60 custom-scrollbar">
<!-- Item 1: ĐANG CHỌN / XEM - Chưa đọc -->
<article class="p-4 bg-surface-container/60 hover:bg-surface-container/80 transition-all cursor-pointer border-l-4 border-l-primary relative">
<div class="flex items-start justify-between gap-3 mb-1.5">
<div class="flex items-center gap-2">
<span class="w-2.5 h-2.5 rounded-full bg-primary flex-shrink-0" title="Chưa đọc"></span>
<span class="px-2 py-0.5 rounded-full font-label-sm text-label-sm bg-red-100 text-red-700 font-semibold border border-red-200">
                    Quan trọng
                  </span>
<span class="font-label-md text-label-md text-primary font-semibold truncate max-w-[240px]">
                    TS. Trần Hoàng Nam • CNTT
                  </span>
</div>
<span class="font-body-sm text-body-sm text-secondary flex-shrink-0">15 phút trước</span>
</div>
<h2 class="font-headline-sm text-headline-sm text-on-surface font-bold leading-snug line-clamp-1 mb-1 text-primary">
                Thông báo dời lịch học thực hành Lab PM04 sang Thứ 5 ngày 24/10 và gia hạn bài tập lớn đồ án
              </h2>
<p class="font-body-sm text-body-sm text-on-surface-variant line-clamp-2 mb-2.5 leading-relaxed">
                Chào các em sinh viên hai lớp học phần Lập trình Web, Do phòng máy Lab PM04 bảo trì định kỳ vào Thứ 2 ngày 21/10, lịch học thực hành sẽ chuyển sang Thứ 5...
              </p>
<div class="flex items-center justify-between text-body-sm font-body-sm text-secondary pt-1">
<div class="flex items-center gap-1.5 text-on-surface-variant bg-surface-container-lowest px-2 py-0.5 rounded border border-outline-variant/60">
<span class="material-symbols-outlined text-sm text-primary">attach_file</span>
<span class="font-label-sm text-label-sm">2 tệp đính kèm [PDF, DOCX]</span>
</div>
<div class="flex items-center gap-1 text-primary font-label-sm font-semibold">
<span>Đang xem</span>
<span class="material-symbols-outlined text-xs">arrow_forward</span>
</div>
</div>
</article>
<!-- Item 2: Chưa đọc -->
<article class="p-4 hover:bg-surface-container-low transition-all cursor-pointer border-l-4 border-l-transparent">
<div class="flex items-start justify-between gap-3 mb-1.5">
<div class="flex items-center gap-2">
<span class="w-2.5 h-2.5 rounded-full bg-primary flex-shrink-0" title="Chưa đọc"></span>
<span class="px-2 py-0.5 rounded-full font-label-sm text-label-sm bg-amber-100 text-amber-800 font-semibold border border-amber-200">
                    Khẩn cấp
                  </span>
<span class="font-label-md text-label-md text-secondary font-medium truncate max-w-[240px]">
                    Phòng Đào tạo HUIT
                  </span>
</div>
<span class="font-body-sm text-body-sm text-secondary flex-shrink-0">Hôm qua 08:00</span>
</div>
<h2 class="font-headline-sm text-headline-sm text-on-surface font-semibold leading-snug line-clamp-1 mb-1 hover:text-primary transition-colors">
                Mở cổng Đăng ký học phần Đợt 1 (HK1 2026-2027) chính thức cho Khóa 2021 &amp; Khóa 2022
              </h2>
<p class="font-body-sm text-body-sm text-on-surface-variant line-clamp-2 mb-2 leading-relaxed">
                Sinh viên chú ý thời gian mở cổng từ 08:00 ngày 15/10 đến 23:59 ngày 20/10. Vui lòng rà soát danh sách học phần tiên quyết trước khi xác nhận...
              </p>
<div class="flex items-center gap-1.5 text-secondary">
<span class="material-symbols-outlined text-sm">attach_file</span>
<span class="font-body-sm text-body-sm">1 tệp [PDF Kế hoạch]</span>
</div>
</article>
<!-- Item 3: Đã đọc -->
<article class="p-4 hover:bg-surface-container-low transition-all cursor-pointer opacity-90 border-l-4 border-l-transparent">
<div class="flex items-start justify-between gap-3 mb-1.5">
<div class="flex items-center gap-2">
<span class="w-2.5 h-2.5 rounded-full bg-transparent flex-shrink-0"></span>
<span class="px-2 py-0.5 rounded-full font-label-sm text-label-sm bg-blue-100 text-blue-700 font-semibold border border-blue-200">
                    Học vụ
                  </span>
<span class="font-label-md text-label-md text-secondary font-medium truncate">
                    Phòng Kế hoạch - Tài chính
                  </span>
</div>
<span class="font-body-sm text-body-sm text-secondary flex-shrink-0">12/10/2026</span>
</div>
<h2 class="font-headline-sm text-headline-sm text-on-surface font-medium leading-snug line-clamp-1 mb-1">
                Thông báo mức thu học vụ và thời hạn đóng học vụ Học kỳ 1 Năm học 2026-2027
              </h2>
<p class="font-body-sm text-body-sm text-secondary line-clamp-1">
                Hướng dẫn chi tiết thanh toán học vụ qua cổng dịch vụ trực tuyến hoặc chuyển khoản ngân hàng số sinh viên...
              </p>
</article>
<!-- Item 4: Đã đọc -->
<article class="p-4 hover:bg-surface-container-low transition-all cursor-pointer opacity-90 border-l-4 border-l-transparent">
<div class="flex items-start justify-between gap-3 mb-1.5">
<div class="flex items-center gap-2">
<span class="w-2.5 h-2.5 rounded-full bg-transparent flex-shrink-0"></span>
<span class="px-2 py-0.5 rounded-full font-label-sm text-label-sm bg-purple-100 text-purple-700 font-semibold border border-purple-200">
                    Đánh giá học vụ
                  </span>
<span class="font-label-md text-label-md text-secondary font-medium truncate">
                    Phòng Công tác Sinh viên
                  </span>
</div>
<span class="font-body-sm text-body-sm text-secondary flex-shrink-0">10/10/2026</span>
</div>
<h2 class="font-headline-sm text-headline-sm text-on-surface font-medium leading-snug line-clamp-1 mb-1">
                Đánh giá học vụ lấy ý kiến sinh viên về hoạt động giảng dạy và cơ sở vật chất học kỳ trước
              </h2>
<p class="font-body-sm text-body-sm text-secondary line-clamp-1">
                Đề nghị các bạn hoàn tất phiếu đánh giá học vụ trực tuyến trước ngày 25/10 để đảm bảo quyền lợi tích lũy điểm rèn luyện...
              </p>
</article>
<!-- Item 5: Đã đọc -->
<article class="p-4 hover:bg-surface-container-low transition-all cursor-pointer opacity-90 border-l-4 border-l-transparent">
<div class="flex items-start justify-between gap-3 mb-1.5">
<div class="flex items-center gap-2">
<span class="w-2.5 h-2.5 rounded-full bg-transparent flex-shrink-0"></span>
<span class="px-2 py-0.5 rounded-full font-label-sm text-label-sm bg-slate-100 text-slate-700 font-semibold border border-slate-200">
                    Thông thường
                  </span>
<span class="font-label-md text-label-md text-secondary font-medium truncate">
                    Trung tâm Khảo thí &amp; ĐBCL
                  </span>
</div>
<span class="font-body-sm text-body-sm text-secondary flex-shrink-0">05/10/2026</span>
</div>
<h2 class="font-headline-sm text-headline-sm text-on-surface font-medium leading-snug line-clamp-1 mb-1">
                Lịch nộp đơn phúc khảo bài thi kết thúc học phần giai đoạn hè 2024
              </h2>
<p class="font-body-sm text-body-sm text-secondary line-clamp-1">
                Thời gian tiếp nhận đơn phúc khảo bắt đầu từ ngày 07/10 đến 14/10 tại bàn tiếp nhận một cửa cơ sở chính...
              </p>
</article>
</div>
<!-- Phân trang danh sách -->
<div class="p-2.5 border-t border-outline-variant/70 bg-surface flex items-center justify-between flex-shrink-0 text-body-sm font-body-sm text-secondary">
<span>Hiển thị 1 - 5 trên 18</span>
<div class="flex items-center gap-1">
<button class="w-7 h-7 rounded border border-outline-variant bg-surface-container-lowest flex items-center justify-center text-outline disabled:opacity-40" disabled="">
<span class="material-symbols-outlined text-sm">chevron_left</span>
</button>
<span class="px-2 font-semibold text-primary">Trang 1</span>
<button class="w-7 h-7 rounded border border-outline-variant bg-surface-container-lowest flex items-center justify-center text-on-surface hover:bg-surface-container-low">
<span class="material-symbols-outlined text-sm">chevron_right</span>
</button>
</div>
</div>
</section>
<!-- ================= CỘT PHẢI: CHI TIẾT THÔNG BÁO (48%) ================= -->
<section class="w-[48%] flex flex-col h-full bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-6 justify-between overflow-hidden">
<!-- Nội dung chi tiết cuộn -->
<div class="flex-1 overflow-y-auto pr-2 custom-scrollbar flex flex-col">
<!-- Header Panel: Huy hiệu & Action đóng/thu nhỏ -->
<div class="flex items-center justify-between mb-4 flex-shrink-0">
<span class="px-3 py-1 rounded-full text-label-sm font-label-sm bg-red-50 text-red-700 font-bold border border-red-200 tracking-wide uppercase">
                THÔNG BÁO QUAN TRỌNG TỪ GIẢNG VIÊN
              </span>
<div class="flex items-center gap-1">
<button class="p-1 rounded-lg text-outline hover:text-on-surface hover:bg-surface-container-low transition-colors" title="Mở rộng cửa sổ">
<span class="material-symbols-outlined text-lg">open_in_full</span>
</button>
<button class="p-1 rounded-lg text-outline hover:text-on-surface hover:bg-surface-container-low transition-colors" title="Đóng panel">
<span class="material-symbols-outlined text-lg">close</span>
</button>
</div>
</div>
<!-- Khối Người gửi & Thời gian -->
<div class="p-3.5 rounded-xl bg-surface-container-low border border-outline-variant/60 flex items-start gap-3.5 mb-5 flex-shrink-0">
<div class="w-12 h-12 rounded-xl bg-primary-container text-on-primary-container flex items-center justify-center font-bold text-headline-sm flex-shrink-0 shadow-xs">
                TH
              </div>
<div class="flex-1 min-w-0">
<div class="flex items-center justify-between">
<h3 class="font-label-lg text-label-lg font-bold text-on-surface truncate">
                    TS. Trần Hoàng Nam
                  </h3>
<span class="font-body-sm text-body-sm text-secondary">
                    10:30 Thứ Hai, 21/10/2026
                  </span>
</div>
<p class="font-body-sm text-body-sm text-primary font-medium mt-0.5">
                  Giảng viên học phần Lập trình Web • Khoa Công nghệ Thông tin
                </p>
<div class="mt-2 pt-2 border-t border-outline-variant/50 flex items-center gap-3 text-[11px] text-secondary">
<span class="flex items-center gap-1">
<span class="material-symbols-outlined text-xs">group</span>
<span>Mã lớp: 241_71ITEC3201_01 &amp; 02</span>
</span>
<span>•</span>
<span>Sĩ số: 118 sinh viên</span>
</div>
</div>
</div>
<!-- Tiêu đề lớn -->
<h2 class="font-headline-md text-headline-md text-on-surface font-bold mb-4 leading-snug">
              Thông báo dời lịch học thực hành Lab PM04 sang Thứ 5 ngày 24/10 và gia hạn đồ án học phần
            </h2>
<!-- Toàn văn nội dung thông báo -->
<div class="space-y-3.5 text-body-md font-body-md text-on-surface-variant leading-relaxed mb-6">
<p>
                Chào các em sinh viên hai lớp học phần <span class="font-semibold text-on-surface">Lập trình Web (241_71ITEC3201)</span>,
              </p>
<p>
                Do phòng máy <span class="font-semibold text-on-surface">Lab PM04</span> cần bảo trì định kỳ hệ thống điện mạng và nâng cấp máy chủ nội bộ vào Thứ Hai ngày 21/10, buổi học thực hành của chúng ta sẽ chuyển sang <span class="font-semibold text-primary underline underline-offset-2">Thứ 5 ngày 24/10/2026</span> cùng khung giờ (Tiết 7 - 9, từ 12:45 đến 15:15) tại cùng phòng máy.
              </p>
<p>
                Đồng thời, xét theo tiến độ tiếp thu đề tài, thời hạn nộp <span class="font-semibold text-on-surface">Báo cáo đồ án giữa kỳ</span> được gia hạn thêm 02 ngày: đến <span class="font-semibold text-error">23:59 ngày 26/10/2026</span> trực tiếp trên hệ thống LMS. Đề nghị ban cán sự lớp thông báo lại cho các nhóm để hoàn thiện tài liệu đúng quy cách.
              </p>
<p class="pt-2 text-on-surface font-medium">
                Thân ái,<br/>
<span class="font-semibold">Thầy Nam</span>
</p>
</div>
<!-- Khối Tệp đính kèm học tập -->
<div class="mb-4">
<div class="flex items-center justify-between mb-2.5">
<span class="font-label-md text-label-md font-bold text-on-surface flex items-center gap-1.5">
<span class="material-symbols-outlined text-base text-primary">folder_open</span>
<span>Tệp đính kèm (2)</span>
</span>
<span class="font-body-sm text-body-sm text-secondary">Đã quét virus an toàn</span>
</div>
<div class="grid grid-cols-2 gap-3">
<!-- Tệp 1: PDF -->
<div class="p-3 bg-surface rounded-xl border border-outline-variant hover:border-primary/50 transition-all flex items-center justify-between group">
<div class="flex items-center gap-2.5 min-w-0">
<div class="w-9 h-9 rounded-lg bg-red-100 text-red-700 flex items-center justify-center font-bold text-xs flex-shrink-0">
                      PDF
                    </div>
<div class="min-w-0">
<p class="font-label-md text-label-md font-medium text-on-surface truncate group-hover:text-primary transition-colors" title="De_cuong_thuc_hanh_buoi_04.pdf">
                        De_cuong_thuc_hanh_buoi_04.pdf
                      </p>
<span class="font-body-sm text-body-sm text-secondary">1.8 MB</span>
</div>
</div>
<button class="w-8 h-8 rounded-lg hover:bg-surface-container-high text-primary flex items-center justify-center transition-colors" title="Tải tệp này">
<span class="material-symbols-outlined text-base">download</span>
</button>
</div>
<!-- Tệp 2: DOCX -->
<div class="p-3 bg-surface rounded-xl border border-outline-variant hover:border-primary/50 transition-all flex items-center justify-between group">
<div class="flex items-center gap-2.5 min-w-0">
<div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs flex-shrink-0">
                      DOC
                    </div>
<div class="min-w-0">
<p class="font-label-md text-label-md font-medium text-on-surface truncate group-hover:text-primary transition-colors" title="Huong_dan_nop_bai_giua_ky.docx">
                        Huong_dan_nop_bai_giua_ky.docx
                      </p>
<span class="font-body-sm text-body-sm text-secondary">420 KB</span>
</div>
</div>
<button class="w-8 h-8 rounded-lg hover:bg-surface-container-high text-primary flex items-center justify-center transition-colors" title="Tải tệp này">
<span class="material-symbols-outlined text-base">download</span>
</button>
</div>
</div>
</div>
</div>
<!-- Footer Panel Actions -->
<div class="pt-4 border-t border-outline-variant/80 flex items-center justify-between flex-shrink-0">
<div class="flex items-center gap-2">
<button class="h-9 px-3 bg-surface-container-lowest border border-outline-variant hover:bg-surface-container-low text-on-surface rounded-lg font-label-md text-label-md flex items-center gap-1.5 transition-colors">
<span class="material-symbols-outlined text-base text-secondary">mark_email_unread</span>
<span>Đánh dấu chưa đọc</span>
</button>
<button class="h-9 px-3 bg-surface-container-lowest border border-outline-variant hover:bg-surface-container-low text-on-surface rounded-lg font-label-md text-label-md flex items-center gap-1.5 transition-colors">
<span class="material-symbols-outlined text-base text-secondary">share</span>
<span>Chia sẻ liên kết</span>
</button>
</div>
<!-- Primary CTA -->
<button class="h-9 px-4 bg-primary hover:bg-primary-container text-white rounded-lg font-label-md text-label-md font-semibold flex items-center gap-2 shadow-xs transition-colors">
<span class="material-symbols-outlined text-base">cloud_download</span>
<span>Tải toàn bộ tệp đính kèm</span>
</button>
</div>
</section>
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

@endsection
