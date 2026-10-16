@extends('fe_preview.layouts.student')
@section('page_title', 'Đơn từ & Thủ tục hành chính - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="light"@endsection
@section('body_attributes')class="bg-[#f8f9ff] text-slate-800 antialiased min-h-screen flex selection:bg-blue-100 selection:text-blue-900"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Đơn từ &amp; Thủ tục hành chính - HUIT Học Vụ</title>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'student/don-tu']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">assignment</span>
<span class="truncate">Đơn từ trực tuyến</span>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Đơn từ &amp; Thủ tục hành chính</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Học kỳ 1 • 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chủ / Cổng Sinh viên / Đơn từ</div>
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
<div class="text-xs font-semibold text-slate-900 leading-tight">Người dùng mẫu 25</div>
<div class="text-[11px] text-slate-500">2400000007 • 12DHTH01</div>
</div>
</div>
</div>
</header>
<!-- MAIN BODY CONTENT -->
<main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
<div class="space-y-6">
<!-- Top Stat / Action Bar -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
<div>
<h3 class="font-bold text-slate-900 text-sm">Đơn từ và thủ tục hành chính trực tuyến</h3>
<p class="text-xs text-slate-500 mt-0.5">Theo dõi tiến độ xử lý đơn và nộp đơn trực tuyến 24/7</p>
</div>
<button class="h-9 px-4 rounded-lg bg-primary hover:bg-primary-hover text-white font-bold text-xs flex items-center gap-1.5 shadow-xs transition-colors shrink-0" onclick="openCreateReqModal()">
<span class="material-symbols-outlined text-[18px]">add_circle</span>
<span>Tạo đơn trực tuyến mới</span>
</button>
</div>
<!-- Active Requests & Tracking -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
<!-- Left 2 Cols: Request List -->
<div class="lg:col-span-2 space-y-4">
<!-- Request 1: In Progress with Timeline -->
<div class="bg-white rounded-xl border border-slate-200 shadow-xs p-5 space-y-4">
<div class="flex items-start justify-between gap-2 border-b border-slate-100 pb-3">
<div>
<div class="flex items-center gap-2">
<span class="px-2 py-0.5 rounded text-[10.5px] font-bold bg-blue-50 text-primary border border-blue-200 font-mono">DON-2026-0412</span>
<span class="text-xs text-slate-500">Gửi lúc: 08/10/2026 10:15</span>
</div>
<h4 class="font-bold text-slate-900 text-base mt-1">Đơn xin hoãn thi kết thúc học phần</h4>
<p class="text-xs text-slate-600 mt-0.5">Học phần: Lập trình Java (241_71ITEC3201_01) • Lý do: Nằm viện điều trị sốt xuất huyết</p>
</div>
<span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-primary shrink-0">Đang xử lý</span>
</div>
<!-- Status Timeline -->
<div class="space-y-1">
<span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-2">Tiến trình xử lý đơn:</span>
<div class="relative pl-6 space-y-4 before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
<!-- Step 1: Done -->
<div class="relative">
<div class="absolute -left-6 top-0.5 w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[10px]">✓</div>
<div class="text-xs font-bold text-slate-900">1. Sinh viên nộp đơn &amp; hồ sơ bệnh án</div>
<div class="text-[11px] text-slate-500">08/10/2026 10:15 • Kèm file giay_ra_vien.pdf</div>
</div>
<!-- Step 2: Done -->
<div class="relative">
<div class="absolute -left-6 top-0.5 w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[10px]">✓</div>
<div class="text-xs font-bold text-slate-900">2. Cố vấn học tập (TS. Người dùng mẫu 38) đã duyệt</div>
<div class="text-[11px] text-slate-500">09/10/2026 14:30 • "Đồng ý cho sinh viên hoãn thi đợt 1"</div>
</div>
<!-- Step 3: Current -->
<div class="relative">
<div class="absolute -left-6 top-0.5 w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] animate-pulse">●</div>
<div class="text-xs font-bold text-primary">3. Phòng Đào tạo đang thẩm định hồ sơ</div>
<div class="text-[11px] text-slate-500">Dự kiến hoàn tất trước 15/10/2026</div>
</div>
<!-- Step 4: Pending -->
<div class="relative opacity-50">
<div class="absolute -left-6 top-0.5 w-5 h-5 rounded-full bg-slate-300 text-slate-600 flex items-center justify-center text-[10px]">4</div>
<div class="text-xs font-bold text-slate-700">4. Ban Giám hiệu ra quyết định thi đợt bổ sung</div>
</div>
</div>
</div>
</div>
<!-- Request 2: Rejected with Specific Reason -->
<div class="bg-white rounded-xl border border-red-200 shadow-xs p-5 space-y-3 bg-red-50/10">
<div class="flex items-start justify-between gap-2 border-b border-red-100 pb-3">
<div>
<div class="flex items-center gap-2">
<span class="px-2 py-0.5 rounded text-[10.5px] font-bold bg-slate-100 text-slate-700 font-mono">DON-2026-0298</span>
<span class="text-xs text-slate-500">15/09/2026</span>
</div>
<h4 class="font-bold text-slate-900 text-sm mt-1">Đơn xin chuyển lớp học phần</h4>
<p class="text-xs text-slate-600">Từ lớp 241_71ITEC3201_01 sang 241_71ITEC3201_02</p>
</div>
<span class="px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 shrink-0">Bị từ chối</span>
</div>
<!-- Rejection reason callout -->
<div class="bg-red-50 p-3 rounded-lg border border-red-200 flex items-start gap-2.5 text-xs text-red-800">
<span class="material-symbols-outlined text-[18px] text-red-700 shrink-0 mt-0.5">error</span>
<div>
<strong class="font-bold">Lý do từ chối (Phòng Đào tạo):</strong>
<p class="mt-0.5 leading-relaxed">Lớp học phần chuyển đến đã vượt quá sĩ số tối đa (45/45 sinh viên). Quy chế đào tạo không cho phép tiếp nhận thêm ngoại lệ khi lớp đã đầy.</p>
</div>
</div>
</div>
</div>
<!-- Right 1 Col: Guidelines & Form Types -->
<div class="space-y-4">
<div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs space-y-3 text-xs">
<h4 class="font-bold text-slate-900 text-sm">Các loại đơn từ hỗ trợ</h4>
<div class="space-y-2 text-slate-600">
<div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200/60">
<strong class="text-slate-900 block font-semibold">Đơn xin hoãn thi</strong>
<span>Áp dụng khi ốm đau, tai nạn có giấy bệnh viện.</span>
</div>
<div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200/60">
<strong class="text-slate-900 block font-semibold">Đơn phúc khảo bài thi</strong>
<span>Nộp trong vòng 7 ngày sau khi công bố điểm học phần.</span>
</div>
<div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200/60">
<strong class="text-slate-900 block font-semibold">Đơn xin nghỉ học tạm thời</strong>
<span>Bảo lưu kết quả học tập từ 1 đến 2 học kỳ chính.</span>
</div>
</div>
</div>
</div>
</div>
<!-- Create Request Modal -->
<div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden" id="createReqModal">
<div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4">
<div class="flex items-center justify-between border-b border-slate-100 pb-3">
<h3 class="font-bold text-slate-900 text-sm">Tạo đơn trực tuyến mới</h3>
<button class="text-slate-500 hover:text-slate-600" onclick="closeCreateReqModal()">
<span class="material-symbols-outlined text-[20px]">close</span>
</button>
</div>
<form class="space-y-3 text-xs" onsubmit="handleSendReq(event)">
<div>
<label class="block font-semibold text-slate-700 mb-1" for="modal-req-type">Loại đơn</label>
<select aria-label="Đơn xin hoãn thi kết thúc học phần" class="w-full h-9 px-3 rounded-lg border border-slate-300 bg-slate-50" id="modal-req-type" required="">
<option>Đơn xin hoãn thi kết thúc học phần</option>
<option>Đơn xin phúc khảo kết quả học tập</option>
<option>Đơn xin nghỉ học tạm thời (Bảo lưu)</option>
<option>Đơn xin chuyển lớp học phần</option>
</select>
</div>
<div>
<label class="block font-semibold text-slate-700 mb-1" for="modal-req-reason">Lý do chi tiết *</label>
<textarea class="w-full p-2.5 rounded-lg border border-slate-300 bg-slate-50" id="modal-req-reason" placeholder="Trình bày lý do cụ thể..." required="" rows="3"></textarea>
</div>
<div>
<label class="block font-semibold text-slate-700 mb-1" for="modal-req-file">Tệp đính kèm minh chứng (PDF, JPG ≤ 5MB)</label>
<input class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded file:border-0 file:bg-primary file:text-white" id="modal-req-file" required="" type="file"/>
</div>
<div class="pt-2 flex justify-end gap-2">
<button class="h-9 px-4 rounded-lg border border-slate-300 text-slate-700 font-semibold" onclick="closeCreateReqModal()" type="button">Hủy</button>
<button class="h-9 px-4 rounded-lg bg-primary hover:bg-primary-hover text-white font-bold" type="submit">Nộp đơn</button>
</div>
</form>
</div>
</div>
</div>
<script>
  function openCreateReqModal() {
    document.getElementById('createReqModal').classList.remove('hidden');
  }
  function closeCreateReqModal() {
    document.getElementById('createReqModal').classList.add('hidden');
  }
  function handleSendReq(e) {
    e.preventDefault();
    alert('Đã gửi đơn trực tuyến thành công! Mã số đơn mới: DON-2026-0413. Cố vấn học tập sẽ xem xét.');
    closeCreateReqModal();
  }
</script>
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

@endsection
