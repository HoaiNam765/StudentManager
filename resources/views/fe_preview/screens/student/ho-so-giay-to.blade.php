@extends('fe_preview.layouts.student')
@section('page_title', 'Hồ sơ & Giấy tờ sinh viên - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Hồ sơ &amp; Giấy tờ sinh viên - HUIT Học Vụ</title>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/ho-so-giay-to']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">folder_shared</span>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Hồ sơ &amp; Giấy tờ sinh viên</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Cổng Sinh viên / Hồ sơ &amp; Giấy tờ</div>
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
<!-- Status Overview Cards -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
<div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
<span class="text-[11px] font-semibold text-slate-500 block uppercase">Hồ sơ đã nộp</span>
<span class="text-xl font-extrabold text-slate-900 mt-1 block">04 / 05</span>
<span class="text-[11px] text-emerald-700 font-medium">80% hồ sơ hoàn tất</span>
</div>
<div class="bg-white p-4 rounded-xl border border-amber-200 bg-amber-50/20 shadow-xs">
<span class="text-[11px] font-semibold text-amber-700 block uppercase">Cần bổ sung</span>
<span class="text-xl font-extrabold text-amber-700 mt-1 block">01</span>
<span class="text-[11px] text-amber-700">Giấy khám sức khỏe định kỳ</span>
</div>
<div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
<span class="text-[11px] font-semibold text-slate-500 block uppercase">Yêu cầu cấp giấy</span>
<span class="text-xl font-extrabold text-primary mt-1 block">02</span>
<span class="text-[11px] text-slate-500">Đã nhận 1 • Đang xử lý 1</span>
</div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
<!-- Left 2 Cols: Document Upload & List -->
<div class="lg:col-span-2 space-y-6">
<!-- Upload File Form with Validation & Progress -->
<div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs space-y-4">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[20px]">upload_file</span>
<h3 class="font-bold text-slate-900 text-sm">Tải lên hồ sơ, giấy tờ bổ sung</h3>
</div>
<form class="space-y-3" id="uploadDocForm" onsubmit="handleUpload(event)">
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
<div>
<label class="block text-xs font-semibold text-slate-700 mb-1" for="doc-type-sel">Loại giấy tờ</label>
<select aria-label="-- Chọn loại giấy tờ --" class="w-full h-9 px-3 rounded-lg border border-slate-300 text-xs text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20" id="doc-type-sel" required="">
<option value="">-- Chọn loại giấy tờ --</option>
<option value="gksk">Giấy khám sức khỏe (Cần bổ sung)</option>
<option value="cccd">Căn cước công dân (Bản chụp 2 mặt)</option>
<option value="thpt">Bằng tốt nghiệp THPT công chứng</option>
<option value="khac">Giấy tờ ưu tiên / Chứng chỉ khác</option>
</select>
</div>
<div>
<label class="block text-xs font-semibold text-slate-700 mb-1" for="doc-file-inp">Chọn tệp tin (PDF, JPG, PNG ≤ 5MB)</label>
<input accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-hover" id="doc-file-inp" required="" type="file"/>
</div>
</div>
<!-- Upload Progress Bar (hidden by default) -->
<div class="hidden space-y-1 pt-1" id="uploadProgress">
<div class="flex justify-between text-xs text-slate-600">
<span id="uploadStatusText">Đang tải lên...</span>
<span id="uploadPercent">75%</span>
</div>
<div class="w-full bg-slate-100 rounded-full h-2">
<div class="bg-primary h-2 rounded-full transition-all duration-300" id="uploadBar" style="width: 75%;"></div>
</div>
</div>
<div class="flex justify-end pt-2">
<button class="h-9 px-4 rounded-lg bg-primary hover:bg-primary-hover text-white font-semibold text-xs flex items-center gap-1.5 shadow-xs transition-all" type="submit">
<span class="material-symbols-outlined text-[16px]">cloud_upload</span>
<span>Gửi giấy tờ</span>
</button>
</div>
</form>
</div>
<!-- Submitted Documents Table -->
<div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
<div class="p-4 border-b border-slate-200">
<h3 class="font-bold text-slate-900 text-sm">Danh mục giấy tờ hồ sơ sinh viên</h3>
</div>
<div class="divide-y divide-slate-100 text-xs">
<div class="p-4 flex items-center justify-between gap-3 hover:bg-slate-50 transition-colors">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined text-emerald-700 text-[22px]">check_circle</span>
<div>
<div class="font-bold text-slate-900">Bằng tốt nghiệp THPT (Bản sao công chứng)</div>
<div class="text-[11px] text-slate-500">Tải lên: 12/09/2021 • File: bang_thpt_nguyenvanan.pdf (1.8 MB)</div>
</div>
</div>
<span class="px-2 py-0.5 rounded font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">Đã duyệt</span>
</div>
<div class="p-4 flex items-center justify-between gap-3 hover:bg-slate-50 transition-colors">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined text-emerald-700 text-[22px]">check_circle</span>
<div>
<div class="font-bold text-slate-900">Căn cước công dân (Mặt trước &amp; Mặt sau)</div>
<div class="text-[11px] text-slate-500">Tải lên: 12/09/2021 • File: cccd_2001210123.jpg (2.1 MB)</div>
</div>
</div>
<span class="px-2 py-0.5 rounded font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">Đã duyệt</span>
</div>
<div class="p-4 flex items-center justify-between gap-3 hover:bg-slate-50 transition-colors">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined text-emerald-700 text-[22px]">check_circle</span>
<div>
<div class="font-bold text-slate-900">Giấy khai sinh bản trích lục</div>
<div class="text-[11px] text-slate-500">Tải lên: 12/09/2021 • File: khai_sinh.pdf (950 KB)</div>
</div>
</div>
<span class="px-2 py-0.5 rounded font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">Đã duyệt</span>
</div>
<div class="p-4 flex items-center justify-between gap-3 bg-amber-50/40">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined text-amber-700 text-[22px]">error</span>
<div>
<div class="font-bold text-slate-900">Giấy khám sức khỏe định kỳ năm học 2026</div>
<div class="text-[11px] text-amber-700 font-medium">Chưa có bản chụp nộp lên hệ thống. Hạn chót: 30/11/2026.</div>
</div>
</div>
<span class="px-2 py-0.5 rounded font-bold bg-amber-100 text-amber-800 border border-amber-300 shrink-0">Cần bổ sung</span>
</div>
</div>
</div>
</div>
<!-- Right 1 Col: Request Student Certificate Form -->
<div class="space-y-6">
<div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs space-y-4">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[20px]">assignment_turned_in</span>
<h3 class="font-bold text-slate-900 text-sm">Yêu cầu cấp giấy tờ học vụ</h3>
</div>
<form class="space-y-3 text-xs" onsubmit="alert('Đã gửi yêu cầu cấp giấy thành công!'); return false;">
<div>
<label class="block font-semibold text-slate-700 mb-1" for="req-type-sel">Loại giấy xác nhận</label>
<select aria-label="Giấy xác nhận sinh viên (Vay vốn ngân hàng)" class="w-full h-9 px-3 rounded-lg border border-slate-300 bg-slate-50 text-xs" id="req-type-sel" required="">
<option>Giấy xác nhận sinh viên (Vay vốn ngân hàng)</option>
<option>Giấy tạm hoãn nghĩa vụ quân sự</option>
<option>Bảng điểm học tập tạm thời (Tiếng Việt)</option>
<option>Bảng điểm học tập (Song ngữ Anh - Việt)</option>
</select>
</div>
<div>
<label class="block font-semibold text-slate-700 mb-1" for="req-quantity">Số lượng bản</label>
<input class="w-full h-9 px-3 rounded-lg border border-slate-300 bg-slate-50 text-xs" id="req-quantity" max="5" min="1" required="" type="number" value="1"/>
</div>
<div>
<label class="block font-semibold text-slate-700 mb-1" for="req-delivery-sel">Phương thức nhận</label>
<select aria-label="Bản điện tử (PDF có chữ ký số &amp; mã QR)" class="w-full h-9 px-3 rounded-lg border border-slate-300 bg-slate-50 text-xs" id="req-delivery-sel">
<option>Bản điện tử (PDF có chữ ký số &amp; mã QR)</option>
<option>Nhận bản giấy trực tiếp tại Phòng Đào tạo</option>
</select>
</div>
<div>
<label class="block font-semibold text-slate-700 mb-1" for="req-reason-inp">Lý do / Cơ quan nộp</label>
<input class="w-full h-9 px-3 rounded-lg border border-slate-300 bg-slate-50 text-xs" id="req-reason-inp" placeholder="VD: Nộp Ngân hàng CSXH Quận Tân Phú" required="" type="text"/>
</div>
<button class="w-full h-9 rounded-lg bg-primary hover:bg-primary-hover text-white font-semibold text-xs flex items-center justify-center gap-1.5 shadow-xs transition-all mt-2" type="submit">
<span class="material-symbols-outlined text-[16px]">send</span>
<span>Gửi yêu cầu cấp giấy</span>
</button>
</form>
</div>
<!-- Tracking Table -->
<div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs space-y-2 text-xs">
<h4 class="font-bold text-slate-900 text-xs">Lịch sử yêu cầu cấp giấy</h4>
<div class="divide-y divide-slate-100">
<div class="py-2 flex items-center justify-between">
<div>
<div class="font-medium text-slate-800">Giấy xác nhận sinh viên</div>
<div class="text-[10.5px] text-slate-500">01/10/2026 • 1 bản PDF</div>
</div>
<span class="px-2 py-0.5 rounded font-bold bg-emerald-50 text-emerald-700">Đã cấp</span>
</div>
<div class="py-2 flex items-center justify-between">
<div>
<div class="font-medium text-slate-800">Giấy hoãn NVQS</div>
<div class="text-[10.5px] text-slate-500">10/10/2026 • Bản giấy</div>
</div>
<span class="px-2 py-0.5 rounded font-bold bg-blue-50 text-blue-700">Đang in ấn</span>
</div>
</div>
</div>
</div>
</div>
</div>
<script>
  function handleUpload(e) {
    e.preventDefault();
    const progress = document.getElementById('uploadProgress');
    progress.classList.remove('hidden');
    setTimeout(() => {
      alert('Tải lên giấy tờ thành công! Hồ sơ đã được chuyển vào hàng đợi chờ duyệt.');
      progress.classList.add('hidden');
      document.getElementById('uploadDocForm').reset();
    }, 800);
  }
</script>
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
