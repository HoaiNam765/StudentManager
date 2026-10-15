@extends('fe_preview.layouts.student')
@section('page_title', 'Học phí & Thanh toán - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Học phí &amp; Thanh toán - HUIT Học Vụ</title>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/hoc-phi']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">payments</span>
<span class="truncate">Học phí &amp; Thanh toán</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/ho-so-giay-to']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">folder_shared</span>
<span class="truncate">Hồ sơ &amp; Giấy tờ</span>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Học phí &amp; Thanh toán</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Cổng Sinh viên / Học phí</div>
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
<div class="space-y-6">
<!-- Summary Cards (VND Integer, No Decimals) -->
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
<div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
<span class="text-[11px] font-semibold text-slate-500 block uppercase">Tổng học phí HK1</span>
<span class="text-xl font-extrabold text-slate-900 mt-1 block">9.450.000 đ</span>
<span class="text-[11px] text-slate-500">15 tín chỉ đã đăng ký</span>
</div>
<div class="bg-white p-4 rounded-xl border border-emerald-200 bg-emerald-50/20 shadow-xs">
<span class="text-[11px] font-semibold text-emerald-700 block uppercase">Đã thanh toán</span>
<span class="text-xl font-extrabold text-emerald-700 mt-1 block">9.450.000 đ</span>
<span class="text-[11px] text-emerald-700 font-medium">Hoàn tất 100%</span>
</div>
<div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
<span class="text-[11px] font-semibold text-slate-500 block uppercase">Còn nợ</span>
<span class="text-xl font-extrabold text-slate-900 mt-1 block">0 đ</span>
<span class="text-[11px] text-slate-500">Không có dư nợ</span>
</div>
<div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
<span class="text-[11px] font-semibold text-slate-500 block uppercase">Số dư tạm ứng</span>
<span class="text-xl font-extrabold text-slate-900 mt-1 block">0 đ</span>
<span class="text-[11px] text-slate-500">Ví tài khoản HUIT</span>
</div>
</div>
<!-- Invoices and Payment History Tabs -->
<div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
<div class="p-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
<div>
<h3 class="font-bold text-slate-900 text-sm">Hóa đơn học phí &amp; Lịch sử đóng tiền</h3>
<p class="text-xs text-slate-500 mt-0.5">Học kỳ 1 • 2026-2027 • Trường Đại học Công Thương TP.HCM</p>
</div>
<button class="h-9 px-3.5 rounded-lg border border-slate-300 hover:bg-slate-50 text-xs font-semibold text-slate-700 flex items-center gap-1.5 transition-colors no-print" onclick="window.print()">
<span class="material-symbols-outlined text-[16px]">receipt_long</span>
<span>In biên lai học phí</span>
</button>
</div>
<!-- Invoice Detail Table -->
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
<th class="py-3 px-4">Số hóa đơn</th>
<th class="py-3 px-4">Khoản thu / Học phần</th>
<th class="py-3 px-4 text-center">Số TC</th>
<th class="py-3 px-4 text-right">Số tiền (VND)</th>
<th class="py-3 px-4 text-center">Trạng thái</th>
<th class="py-3 px-4">Ngày giao dịch</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
<tr>
<td class="py-3.5 px-4 font-mono font-bold text-primary">HD2026-08129</td>
<td class="py-3.5 px-4">
<div class="font-semibold text-slate-900">Lập trình Java (241_71ITEC3201_01)</div>
<div class="text-[11px] text-slate-500">Đơn giá 630.000 đ / TC lý thuyết</div>
</td>
<td class="py-3.5 px-4 text-center">3</td>
<td class="py-3.5 px-4 text-right font-semibold text-slate-900">1.890.000 đ</td>
<td class="py-3.5 px-4 text-center">
<span class="px-2 py-0.5 rounded font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Đã thanh toán</span>
</td>
<td class="py-3.5 px-4 text-slate-500">15/09/2026 09:24</td>
</tr>
<tr>
<td class="py-3.5 px-4 font-mono font-bold text-primary">HD2026-08129</td>
<td class="py-3.5 px-4">
<div class="font-semibold text-slate-900">Cơ sở dữ liệu nâng cao (241_71ITEC2202_02)</div>
<div class="text-[11px] text-slate-500">Đơn giá 630.000 đ / TC lý thuyết</div>
</td>
<td class="py-3.5 px-4 text-center">3</td>
<td class="py-3.5 px-4 text-right font-semibold text-slate-900">1.890.000 đ</td>
<td class="py-3.5 px-4 text-center">
<span class="px-2 py-0.5 rounded font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Đã thanh toán</span>
</td>
<td class="py-3.5 px-4 text-slate-500">15/09/2026 09:24</td>
</tr>
<tr>
<td class="py-3.5 px-4 font-mono font-bold text-primary">HD2026-08129</td>
<td class="py-3.5 px-4">
<div class="font-semibold text-slate-900">Phát triển ứng dụng Web (241_71ITEC3304_01)</div>
<div class="text-[11px] text-slate-500">Đơn giá 630.000 đ / TC</div>
</td>
<td class="py-3.5 px-4 text-center">3</td>
<td class="py-3.5 px-4 text-right font-semibold text-slate-900">1.890.000 đ</td>
<td class="py-3.5 px-4 text-center">
<span class="px-2 py-0.5 rounded font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Đã thanh toán</span>
</td>
<td class="py-3.5 px-4 text-slate-500">15/09/2026 09:24</td>
</tr>
<tr>
<td class="py-3.5 px-4 font-mono font-bold text-primary">HD2026-08129</td>
<td class="py-3.5 px-4">
<div class="font-semibold text-slate-900">Kỹ thuật phần mềm (241_71ITEC2105_01)</div>
<div class="text-[11px] text-slate-500">Đơn giá 630.000 đ / TC</div>
</td>
<td class="py-3.5 px-4 text-center">3</td>
<td class="py-3.5 px-4 text-right font-semibold text-slate-900">1.890.000 đ</td>
<td class="py-3.5 px-4 text-center">
<span class="px-2 py-0.5 rounded font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Đã thanh toán</span>
</td>
<td class="py-3.5 px-4 text-slate-500">15/09/2026 09:24</td>
</tr>
<tr>
<td class="py-3.5 px-4 font-mono font-bold text-primary">HD2026-08129</td>
<td class="py-3.5 px-4">
<div class="font-semibold text-slate-900">Mạng máy tính (241_71ITEC2108_02)</div>
<div class="text-[11px] text-slate-500">Đơn giá 630.000 đ / TC</div>
</td>
<td class="py-3.5 px-4 text-center">3</td>
<td class="py-3.5 px-4 text-right font-semibold text-slate-900">1.890.000 đ</td>
<td class="py-3.5 px-4 text-center">
<span class="px-2 py-0.5 rounded font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Đã thanh toán</span>
</td>
<td class="py-3.5 px-4 text-slate-500">15/09/2026 09:24</td>
</tr>
</tbody>
<tfoot class="bg-slate-50 border-t border-slate-200 font-bold text-xs text-slate-900">
<tr>
<td class="py-3 px-4 text-right" colspan="3">Tổng thanh toán:</td>
<td class="py-3 px-4 text-right text-primary text-sm">9.450.000 đ</td>
<td class="py-3 px-4 text-emerald-700" colspan="2">Đã gạch nợ thành công</td>
</tr>
</tfoot>
</table>
</div>
</div>
<!-- Bank Transfer Guidance -->
<div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
<div class="flex items-center gap-2 mb-3">
<span class="material-symbols-outlined text-primary text-[20px]">account_balance</span>
<h3 class="font-bold text-slate-900 text-sm">Hướng dẫn chuyển khoản nộp học phí</h3>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
<div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 space-y-2">
<div class="font-semibold text-slate-800">Tài khoản Ngân hàng Vietcombank (Chi nhánh Tân Bình):</div>
<div>Tên thụ hưởng: <strong>TRUONG DAI HOC CONG THUONG TP.HCM</strong></div>
<div>Số tài khoản: <strong class="font-mono text-primary text-sm">0071000888999</strong></div>
<div class="pt-1 text-slate-600">
          Cú pháp bắt buộc: <code class="px-2 py-1 bg-white border border-slate-300 rounded font-bold text-red-700">HOCPHI 2001210123 Nguyen Van An</code>
</div>
</div>
<div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 flex items-center gap-3">
<div class="w-20 h-20 bg-white border border-slate-300 rounded-lg p-1 flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[48px] text-slate-700">qr_code_2</span>
</div>
<div class="space-y-1">
<div class="font-semibold text-slate-900">Quét mã VietQR chuyển nhanh</div>
<p class="text-[11px] text-slate-500">Mã QR tự động điền đúng số tiền học phí và cú pháp MSSV của bạn.</p>
<span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700">Gạch nợ tức thời 24/7</span>
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
