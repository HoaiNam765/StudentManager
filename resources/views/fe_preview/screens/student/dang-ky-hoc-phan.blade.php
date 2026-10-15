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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/dang-ky-hoc-phan']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">how_to_reg</span>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/thong-bao']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">notifications</span>
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
<!-- ==================== BANNER HERO NỔI BẬT ==================== -->
<section class="rounded-2xl p-6 bg-gradient-to-r from-primary via-primary-container to-tertiary text-white shadow-md relative overflow-hidden">
<div class="absolute -right-8 -bottom-10 opacity-10 pointer-events-none">
<span class="material-symbols-outlined text-[240px]" data-icon="school">school</span>
</div>
<div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
<div class="space-y-2.5 max-w-3xl">
<div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-white font-label-sm text-label-sm font-semibold tracking-wider uppercase">
<span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
              Cổng đăng ký trực tuyến chính thức
            </div>
<h1 class="font-headline-xl text-headline-xl text-white font-bold tracking-tight">
              ĐỢT 1 - ĐĂNG KÝ HỌC PHẦN CHÍNH THỨC (HỌC KỲ 1 NĂM HỌC 2026-2027)
            </h1>
<p class="font-body-md text-body-md text-white">
              Đối tượng: Áp dụng cho Sinh viên <span class="font-semibold text-white underline decoration-white/40">Khóa 2021 &amp; Khóa 2022</span> hệ Đại học chính quy toàn trường.
            </p>
<div class="flex flex-wrap items-center gap-3 pt-1 text-white font-label-md text-label-md">
<span class="flex items-center gap-1.5 bg-black/15 px-3 py-1 rounded-lg backdrop-blur-sm">
<span class="material-symbols-outlined text-[18px]" data-icon="badge">badge</span>
                MSSV: <strong>2001210123</strong>
</span>
<span class="flex items-center gap-1.5 bg-black/15 px-3 py-1 rounded-lg backdrop-blur-sm">
<span class="material-symbols-outlined text-[18px]" data-icon="groups">groups</span>
                Lớp sinh hoạt: <strong>12DHTH01</strong>
</span>
<span class="flex items-center gap-1.5 bg-black/15 px-3 py-1 rounded-lg backdrop-blur-sm">
<span class="material-symbols-outlined text-[18px]" data-icon="computer">computer</span>
                Ngành: <strong>Công nghệ Thông tin</strong>
</span>
</div>
</div>
<!-- Đồng hồ đếm ngược -->
<div class="flex-shrink-0 bg-white/10 backdrop-blur-md p-4 rounded-xl border border-white/20 text-center min-w-[240px]">
<span class="text-xs font-semibold uppercase tracking-wider text-white block mb-1">Hạn chót đợt 1</span>
<div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-error text-white font-label-lg text-label-lg font-bold shadow-sm">
<span class="material-symbols-outlined text-[20px] animate-bounce" data-icon="timer">timer</span>
<span>Thời gian còn lại: 02 ngày 14 giờ 35 phút</span>
</div>
<p class="text-[11px] text-white mt-2">Đóng cổng vào lúc 17:00 ngày 20/09/2026</p>
</div>
</div>
</section>
<!-- ==================== BỐ CỤC 2 CỘT CHUYÊN SÂU ==================== -->
<div class="grid grid-cols-12 gap-6 items-start">
<!-- ==================== CỘT TRÁI (~58% -> 7 COLUMNS) ==================== -->
<section class="col-span-12 xl:col-span-7 space-y-4">
<!-- Thanh điều hướng Tab Danh mục -->
<div class="bg-surface-container-lowest p-2 rounded-xl border border-outline-variant shadow-sm flex items-center gap-2">
<button class="flex-1 py-2.5 px-3 rounded-lg bg-primary text-white font-label-md text-label-md font-semibold text-center transition-all shadow-sm">
              Học phần theo kế hoạch CTĐT (Gợi ý)
            </button>
<button class="flex-1 py-2.5 px-3 rounded-lg text-on-surface-variant hover:bg-surface-container-low font-label-md text-label-md font-medium text-center transition-all">
              Học phần học lại / Cải thiện
            </button>
<button class="flex-1 py-2.5 px-3 rounded-lg text-on-surface-variant hover:bg-surface-container-low font-label-md text-label-md font-medium text-center transition-all">
              Tất cả học phần mở
            </button>
</div>
<!-- Bộ lọc & Ô Tìm kiếm -->
<div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant shadow-sm space-y-3">
<div class="flex flex-col sm:flex-row gap-3">
<div class="relative flex-1">
<span class="material-symbols-outlined absolute left-3 top-2.5 text-secondary text-[20px]" data-icon="search">search</span>
<input aria-label="Nhập mã học phần, tên học phần hoặc tên giảng viên..." class="w-full pl-10 pr-4 py-2 border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20" placeholder="Nhập mã học phần, tên học phần hoặc tên giảng viên..." type="text"/>
</div>
<div class="flex gap-2">
<select aria-label="Lọc Thứ (Tất cả)" class="px-3 py-2 border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface bg-white focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
<option value="">Lọc Thứ (Tất cả)</option>
<option value="2">Thứ 2</option>
<option value="3">Thứ 3</option>
<option value="4">Thứ 4</option>
<option value="5">Thứ 5</option>
<option value="6">Thứ 6</option>
<option value="7">Thứ 7</option>
</select>
<select aria-label="Lọc Buổi (Tất cả)" class="px-3 py-2 border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface bg-white focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
<option value="">Lọc Buổi (Tất cả)</option>
<option value="sang">Sáng (Tiết 1 - 5)</option>
<option value="chieu">Chiều (Tiết 6 - 10)</option>
<option value="toi">Tối (Tiết 11 - 13)</option>
</select>
</div>
</div>
<div class="flex items-center justify-between text-secondary font-label-sm text-label-sm pt-1 border-t border-surface-container">
<span>Hiển thị <strong>5</strong> lớp học phần mở đăng ký phù hợp</span>
<span class="text-primary cursor-pointer hover:underline flex items-center gap-1">
<span class="material-symbols-outlined text-[16px]" data-icon="refresh">refresh</span>
                Làm mới danh sách
              </span>
</div>
</div>
<!-- DANH SÁCH CÁC THẺ LỚP HỌC PHẦN -->
<div class="space-y-3.5">
<!-- THẺ 1: Lập trình Web [ĐÃ CHỌN VÀO TKB TẠM] -->
<article class="bg-surface-container-lowest p-4 rounded-xl border-2 border-primary/40 shadow-sm transition-all hover:shadow-md">
<div class="flex items-start justify-between gap-3">
<div class="space-y-1">
<div class="flex items-center gap-2">
<span class="px-2 py-0.5 rounded bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-bold">241_71ITEC3201_01</span>
<span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">3 Tín chỉ</span>
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-blue-50 text-primary border border-primary/30 font-label-sm text-label-sm font-semibold">
<span class="material-symbols-outlined text-[14px]" data-icon="check_circle">check_circle</span>
                      Đã chọn vào TKB tạm
                    </span>
</div>
<h3 class="font-headline-sm text-headline-sm font-bold text-on-surface">Lập trình Web</h3>
<p class="font-body-sm text-body-sm text-secondary flex items-center gap-2">
<span class="flex items-center gap-1">
<span class="material-symbols-outlined text-[16px]" data-icon="person_outline">person_outline</span>
                      TS. Trần Hoàng Nam
                    </span>
<span>•</span>
<span class="flex items-center gap-1 text-on-surface">
<span class="material-symbols-outlined text-[16px] text-primary" data-icon="schedule">schedule</span>
                      Thứ 2, Tiết 1 - 3 (07:00 - 09:30)
                    </span>
<span>•</span>
<span class="flex items-center gap-1 text-on-surface">
<span class="material-symbols-outlined text-[16px] text-primary" data-icon="meeting_room">meeting_room</span>
                      Phòng A2.301
                    </span>
</p>
</div>
<button class="px-3.5 py-2 rounded-lg border border-red-300 bg-red-50 text-red-700 hover:bg-red-100 font-label-md text-label-md font-semibold transition-colors flex items-center gap-1.5 flex-shrink-0">
<span class="material-symbols-outlined text-[18px]" data-icon="delete_outline">delete_outline</span>
<span>Bỏ chọn</span>
</button>
</div>
<!-- Thước đo sĩ số -->
<div class="mt-3 pt-3 border-t border-surface-container flex items-center justify-between text-secondary font-label-sm text-label-sm">
<div class="flex items-center gap-2">
<span>Sĩ số: <strong class="text-on-surface">48/60</strong></span>
<div class="w-24 h-2 bg-slate-200 rounded-full overflow-hidden">
<div class="bg-primary h-full rounded-full" style="width: 80%"></div>
</div>
<span class="text-emerald-700 font-medium">(Còn 12 chỗ)</span>
</div>
<span class="text-outline">Hình thức: Trực tiếp tại cơ sở Tân Phú</span>
</div>
</article>
<!-- THẺ 2: Cấu trúc dữ liệu & Giải thuật [CẢNH BÁO TRÙNG LỊCH] -->
<article class="bg-surface-container-lowest p-4 rounded-xl border-2 border-red-400 shadow-sm transition-all hover:shadow-md relative overflow-hidden">
<div class="flex items-start justify-between gap-3">
<div class="space-y-1">
<div class="flex items-center gap-2">
<span class="px-2 py-0.5 rounded bg-surface-container text-on-surface font-label-sm text-label-sm font-bold">241_71ITEC2104_02</span>
<span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">4 Tín chỉ</span>
<span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-label-sm text-label-sm font-bold flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]" data-icon="warning">warning</span>
                      Xung đột thời khóa biểu
                    </span>
</div>
<h3 class="font-headline-sm text-headline-sm font-bold text-on-surface">Cấu trúc dữ liệu &amp; Giải thuật</h3>
<p class="font-body-sm text-body-sm text-secondary flex items-center gap-2">
<span class="flex items-center gap-1">
<span class="material-symbols-outlined text-[16px]" data-icon="person_outline">person_outline</span>
                      ThS. Lê Quang Huy
                    </span>
<span>•</span>
<span class="flex items-center gap-1 text-red-700 font-medium">
<span class="material-symbols-outlined text-[16px]" data-icon="schedule">schedule</span>
                      Thứ 3, Tiết 4 - 6 (09:30 - 12:00)
                    </span>
<span>•</span>
<span class="flex items-center gap-1 text-on-surface">
<span class="material-symbols-outlined text-[16px]" data-icon="meeting_room">meeting_room</span>
                      Phòng B.204
                    </span>
</p>
</div>
<button class="px-3.5 py-2 rounded-lg bg-amber-700 hover:bg-amber-800 text-white font-label-md text-label-md font-semibold transition-colors flex items-center gap-1 flex-shrink-0 shadow-sm">
<span class="material-symbols-outlined text-[18px]" data-icon="priority_high">priority_high</span>
<span>Trùng lịch - Vẫn xem xét</span>
</button>
</div>
<!-- CẢNH BÁO TRÙNG LỊCH NỔI BẬT THEO YÊU CẦU -->
<div class="mt-3 p-3 bg-red-100 border border-red-400 rounded-lg text-red-700 text-sm">
<div class="flex items-start gap-2">
<span class="material-symbols-outlined text-red-700 text-[20px] flex-shrink-0 mt-0.5" data-icon="error">error</span>
<div class="space-y-1">
<p class="font-bold">⚠️ CẢNH BÁO TRÙNG LỊCH:</p>
<p>Trùng lịch học với lớp <strong>Lập trình Web</strong> vào Thứ 3 Tiết 4-6.</p>
<p class="text-xs text-red-800 bg-red-50 p-2 rounded border border-red-200 mt-1">
<strong>Cách khắc phục:</strong> Hãy chọn <strong>Lớp 01</strong> (Thứ 5 Tiết 1-3) hoặc <strong>Lớp 03</strong> (Thứ 7 Tiết 7-9) để đảm bảo không bị cấn giờ thi và điểm quá trình.
                    </p>
</div>
</div>
</div>
<div class="mt-3 pt-2 border-t border-surface-container flex items-center justify-between text-secondary font-label-sm text-label-sm">
<div class="flex items-center gap-2">
<span>Sĩ số: <strong class="text-on-surface">52/60</strong></span>
<div class="w-24 h-2 bg-slate-200 rounded-full overflow-hidden">
<div class="bg-amber-500 h-full rounded-full" style="width: 86%"></div>
</div>
<span class="text-amber-700 font-medium">(Còn 8 chỗ)</span>
</div>
<span class="text-outline">Hình thức: Trực tiếp tại cơ sở Tân Phú</span>
</div>
</article>
<!-- THẺ 3: Xác suất thống kê [HẾT CHỖ - DISABLED] -->
<article class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant shadow-sm opacity-80">
<div class="flex items-start justify-between gap-3">
<div class="space-y-1">
<div class="flex items-center gap-2">
<span class="px-2 py-0.5 rounded bg-surface-container text-secondary font-label-sm text-label-sm font-bold">241_71MATH1102_01</span>
<span class="px-2 py-0.5 rounded-full bg-slate-100 text-secondary font-label-sm text-label-sm font-semibold">3 Tín chỉ</span>
<span class="px-2 py-0.5 rounded-full bg-red-100 text-red-900 font-label-sm text-label-sm font-bold">Lớp đã đủ sĩ số</span>
</div>
<h3 class="font-headline-sm text-headline-sm font-bold text-on-surface">Xác suất thống kê</h3>
<p class="font-body-sm text-body-sm text-secondary flex items-center gap-2">
<span class="flex items-center gap-1">
<span class="material-symbols-outlined text-[16px]" data-icon="person_outline">person_outline</span>
                      TS. Nguyễn Thanh Tùng
                    </span>
<span>•</span>
<span class="flex items-center gap-1">
<span class="material-symbols-outlined text-[16px]" data-icon="schedule">schedule</span>
                      Thứ 4, Tiết 1 - 3
                    </span>
<span>•</span>
<span class="flex items-center gap-1">
<span class="material-symbols-outlined text-[16px]" data-icon="meeting_room">meeting_room</span>
                      Phòng A1.102
                    </span>
</p>
</div>
<button class="px-3.5 py-2 rounded-lg bg-slate-200 text-slate-500 font-label-md text-label-md font-semibold cursor-not-allowed flex items-center gap-1 flex-shrink-0" disabled="">
<span class="material-symbols-outlined text-[18px]" data-icon="block">block</span>
<span>Đã hết chỗ</span>
</button>
</div>
<div class="mt-3 pt-3 border-t border-surface-container flex items-center justify-between text-secondary font-label-sm text-label-sm">
<div class="flex items-center gap-2">
<span>Sĩ số: <strong class="text-red-700">60/60 SV (HẾT CHỖ)</strong></span>
<div class="w-24 h-2 bg-slate-200 rounded-full overflow-hidden">
<div class="bg-red-500 h-full rounded-full" style="width: 100%"></div>
</div>
</div>
<span class="text-error font-medium">Hệ thống đã khóa đăng ký lớp này</span>
</div>
</article>
<!-- THẺ 4: Trí tuệ nhân tạo nâng cao [THIẾU TIÊN QUYẾT] -->
<article class="bg-surface-container-lowest p-4 rounded-xl border border-amber-300 bg-amber-50/20 shadow-sm">
<div class="flex items-start justify-between gap-3">
<div class="space-y-1">
<div class="flex items-center gap-2">
<span class="px-2 py-0.5 rounded bg-surface-container text-on-surface font-label-sm text-label-sm font-bold">241_71ITEC3305_01</span>
<span class="px-2 py-0.5 rounded-full bg-slate-100 text-secondary font-label-sm text-label-sm font-semibold">3 Tín chỉ</span>
</div>
<h3 class="font-headline-sm text-headline-sm font-bold text-on-surface">Trí tuệ nhân tạo nâng cao</h3>
<p class="font-body-sm text-body-sm text-secondary">
                    Học phần chuyên ngành tự chọn định hướng Trí tuệ nhân tạo &amp; Dữ liệu lớn
                  </p>
</div>
<button class="px-3.5 py-2 rounded-lg bg-slate-200 text-slate-500 font-label-md text-label-md font-semibold cursor-not-allowed flex items-center gap-1 flex-shrink-0" disabled="">
<span class="material-symbols-outlined text-[18px]" data-icon="lock">lock</span>
<span>Không đủ điều kiện</span>
</button>
</div>
<!-- Badge lỗi điều kiện tiên quyết -->
<div class="mt-3 p-2.5 bg-amber-100/70 border border-amber-300 rounded-lg text-amber-900 font-label-sm text-label-sm flex items-center gap-2">
<span class="material-symbols-outlined text-amber-700 text-[18px] flex-shrink-0" data-icon="info">info</span>
<span><strong>Từ chối:</strong> Thiếu học phần tiên quyết <em>Giải tích 1</em>. Hãy đăng ký Giải tích 1 trước hoặc chọn học phần khác.</span>
</div>
</article>
<!-- THẺ 5: Mạng máy tính [KHẢ DỤNG] -->
<article class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant shadow-sm transition-all hover:border-primary/50 hover:shadow-md">
<div class="flex items-start justify-between gap-3">
<div class="space-y-1">
<div class="flex items-center gap-2">
<span class="px-2 py-0.5 rounded bg-surface-container text-on-surface font-label-sm text-label-sm font-bold">241_71ITEC3208_02</span>
<span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">3 Tín chỉ</span>
<span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 font-label-sm text-label-sm font-semibold">Học phần cơ sở ngành</span>
</div>
<h3 class="font-headline-sm text-headline-sm font-bold text-on-surface">Mạng máy tính</h3>
<p class="font-body-sm text-body-sm text-secondary flex items-center gap-2">
<span class="flex items-center gap-1">
<span class="material-symbols-outlined text-[16px]" data-icon="person_outline">person_outline</span>
                      ThS. Đỗ Văn Cường
                    </span>
<span>•</span>
<span class="flex items-center gap-1 text-on-surface">
<span class="material-symbols-outlined text-[16px] text-primary" data-icon="schedule">schedule</span>
                      Thứ 6, Tiết 1 - 3
                    </span>
<span>•</span>
<span class="flex items-center gap-1 text-on-surface">
<span class="material-symbols-outlined text-[16px] text-primary" data-icon="meeting_room">meeting_room</span>
                      Phòng B.105
                    </span>
</p>
</div>
<button class="px-4 py-2 rounded-lg bg-primary hover:bg-primary-container text-white font-label-md text-label-md font-semibold transition-all flex items-center gap-1.5 flex-shrink-0 shadow-sm active:scale-95">
<span class="material-symbols-outlined text-[18px]" data-icon="add_circle">add_circle</span>
<span>+ Chọn học phần</span>
</button>
</div>
<div class="mt-3 pt-3 border-t border-surface-container flex items-center justify-between text-secondary font-label-sm text-label-sm">
<div class="flex items-center gap-2">
<span>Sĩ số: <strong class="text-on-surface">35/60</strong></span>
<div class="w-24 h-2 bg-slate-200 rounded-full overflow-hidden">
<div class="bg-emerald-600 h-full rounded-full" style="width: 58%"></div>
</div>
<span class="text-emerald-700 font-medium">(Còn 25 chỗ)</span>
</div>
<span class="text-outline">Hình thức: Trực tiếp tại cơ sở Tân Phú</span>
</div>
</article>
</div>
</section>
<!-- ==================== CỘT PHẢI (~42% -> 5 COLUMNS) ==================== -->
<section class="col-span-12 xl:col-span-5 space-y-4">
<!-- THẺ 1: Giám sát Tín chỉ & Học vụ dự kiến -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-sm space-y-4">
<div class="flex items-center justify-between pb-3 border-b border-surface-container">
<div>
<span class="font-label-sm text-label-sm text-secondary uppercase tracking-wider block">Tiến độ ghi danh kỳ này</span>
<h3 class="font-headline-sm text-headline-sm font-bold text-on-surface">Tổng tín chỉ đã chọn: 18 / 24 Tín chỉ</h3>
</div>
<span class="px-2.5 py-1 rounded-full bg-primary/10 text-primary font-label-md text-label-md font-bold">75% Định mức</span>
</div>
<!-- Progress Bar -->
<div>
<div class="w-full bg-surface-container rounded-full h-3 overflow-hidden">
<div class="bg-primary h-3 rounded-full transition-all duration-500" style="width: 75%"></div>
</div>
<div class="flex justify-between items-center mt-2 font-label-sm text-label-sm text-secondary">
<span>Tối thiểu: 14 TC</span>
<span class="font-semibold text-primary">Còn được đăng ký tối đa 6 TC nữa</span>
<span>Tối đa: 24 TC</span>
</div>
</div>
</div>
<!-- THẺ 2: Lưới Thời khóa biểu tuần tạm (Weekly Preview Grid) -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-sm space-y-3">
<div class="flex items-center justify-between">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[22px]" data-icon="calendar_view_week">calendar_view_week</span>
<h3 class="font-headline-sm text-headline-sm font-bold text-on-surface">Thời khóa biểu tuần tạm</h3>
</div>
<span class="text-xs text-secondary bg-surface-container px-2 py-0.5 rounded">Xem trước trực quan</span>
</div>
<!-- Grid TKB Tuần -->
<div class="border border-outline-variant rounded-lg overflow-hidden bg-white text-xs">
<!-- Header ngày trong tuần -->
<div class="grid grid-cols-7 bg-surface-container text-center font-bold text-on-surface py-2 border-b border-outline-variant">
<div class="text-secondary font-medium">Buổi / Tiết</div>
<div>Thứ 2</div>
<div>Thứ 3</div>
<div>Thứ 4</div>
<div>Thứ 5</div>
<div>Thứ 6</div>
<div>Thứ 7</div>
</div>
<!-- Hàng Ca Sáng (Tiết 1-5) -->
<div class="grid grid-cols-7 border-b border-outline-variant min-h-[90px]">
<div class="p-2 border-r border-outline-variant bg-surface-container-low text-center flex flex-col justify-center">
<span class="font-bold text-primary">CA SÁNG</span>
<span class="text-[10px] text-secondary">Tiết 1 - 5</span>
<span class="text-[9px] text-outline">07:00-11:30</span>
</div>
<!-- Thứ 2: Lập trình Web -->
<div class="p-1 border-r border-outline-variant bg-surface-bright relative">
<div class="h-full bg-primary/10 border-l-4 border-primary p-1.5 rounded text-[11px] flex flex-col justify-between">
<span class="font-bold text-primary leading-tight">Lập trình Web</span>
<span class="text-[10px] text-secondary">Tiết 1-3 • P.A2.301</span>
</div>
</div>
<!-- Thứ 3: CẢNH BÁO XUNG ĐỘT -->
<div class="p-1 border-r border-outline-variant bg-red-50 relative">
<div class="h-full bg-red-100 border-2 border-dashed border-red-500 p-1 rounded text-[10px] flex flex-col justify-between text-red-800">
<span class="font-bold leading-tight flex items-center gap-0.5 text-red-700">
<span class="material-symbols-outlined text-[12px]" data-icon="report">report</span>
                      XUNG ĐỘT!
                    </span>
<span class="text-[9px] leading-tight font-medium">CTDL &amp; Giải thuật trùng lịch!</span>
</div>
</div>
<!-- Thứ 4: Trống -->
<div class="p-1 border-r border-outline-variant bg-white"></div>
<!-- Thứ 5: Trống -->
<div class="p-1 border-r border-outline-variant bg-white"></div>
<!-- Thứ 6: DỰ KIẾN: Mạng máy tính -->
<div class="p-1 border-r border-outline-variant bg-surface-bright">
<div class="h-full border border-dashed border-primary/60 bg-blue-50/50 p-1.5 rounded text-[11px] flex flex-col justify-between">
<span class="font-semibold text-primary leading-tight">Dự kiến: Mạng máy tính</span>
<span class="text-[10px] text-secondary">Tiết 1-3 • P.B.105</span>
</div>
</div>
<!-- Thứ 7: Trống -->
<div class="p-1 bg-white"></div>
</div>
<!-- Hàng Ca Chiều (Tiết 6-10) -->
<div class="grid grid-cols-7 min-h-[90px]">
<div class="p-2 border-r border-outline-variant bg-surface-container-low text-center flex flex-col justify-center">
<span class="font-bold text-primary">CA CHIỀU</span>
<span class="text-[10px] text-secondary">Tiết 6 - 10</span>
<span class="text-[9px] text-outline">12:30-17:00</span>
</div>
<!-- Thứ 2: Trống -->
<div class="p-1 border-r border-outline-variant bg-white"></div>
<!-- Thứ 3: Trống -->
<div class="p-1 border-r border-outline-variant bg-white"></div>
<!-- Thứ 4: Đại số tuyến tính -->
<div class="p-1 border-r border-outline-variant bg-surface-bright">
<div class="h-full bg-primary/10 border-l-4 border-primary p-1.5 rounded text-[11px] flex flex-col justify-between">
<span class="font-bold text-primary leading-tight">Đại số tuyến tính</span>
<span class="text-[10px] text-secondary">Tiết 7-9 • P.A1.201</span>
</div>
</div>
<!-- Thứ 5: Trống -->
<div class="p-1 border-r border-outline-variant bg-white"></div>
<!-- Thứ 6: Trống -->
<div class="p-1 border-r border-outline-variant bg-white"></div>
<!-- Thứ 7: Trống -->
<div class="p-1 bg-white"></div>
</div>
</div>
<!-- Chú thích lưới màu -->
<div class="flex items-center justify-between text-[11px] text-secondary pt-1">
<span class="flex items-center gap-1.5">
<span class="w-3 h-3 rounded bg-primary/20 border border-primary"></span>
<span>Lớp đã chọn hợp lệ</span>
</span>
<span class="flex items-center gap-1.5">
<span class="w-3 h-3 rounded bg-red-100 border border-red-500"></span>
<span class="text-red-700 font-bold">Xung đột / Trùng lịch</span>
</span>
<span class="flex items-center gap-1.5">
<span class="w-3 h-3 rounded border border-dashed border-primary bg-blue-50/50"></span>
<span>Lớp đang dự kiến</span>
</span>
</div>
</div>
<!-- NÚT HÀNH ĐỘNG CHÍNH -->
<div class="space-y-3 pt-2">
<button class="w-full py-3.5 px-6 rounded-xl bg-primary hover:bg-primary-container active:scale-[0.99] text-white font-headline-sm text-headline-sm font-bold shadow-lg transition-all flex items-center justify-center gap-2">
<span class="material-symbols-outlined text-[24px]" data-icon="check_circle">check_circle</span>
<span>XÁC NHẬN ĐĂNG KÝ HỌC PHẦN (18 TÍN CHỈ)</span>
</button>
<button class="w-full py-2.5 px-4 rounded-lg text-slate-500 hover:text-red-700 font-label-md text-label-md transition-colors flex items-center justify-center gap-1">
<span class="material-symbols-outlined text-[18px]" data-icon="restart_alt">restart_alt</span>
<span>Xóa toàn bộ lớp đã chọn</span>
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
