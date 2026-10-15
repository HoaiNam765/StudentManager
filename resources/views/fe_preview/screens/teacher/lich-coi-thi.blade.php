@extends('fe_preview.layouts.teacher')
@section('page_title', 'Lá»‹ch coi thi & PhÃ¢n cÃ´ng giÃ¡m thá»‹ - HUIT Há»c Vá»¥')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Lá»‹ch coi thi &amp; PhÃ¢n cÃ´ng giÃ¡m thá»‹ - HUIT Há»c Vá»¥</title>
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
/* VÃ¡ táº¡m cho 360px: cho phÃ©p hÃ ng flex xuá»‘ng dÃ²ng, khÃ´ng cho pháº§n tá»­ con trÃ n khá»i khung. Thay báº±ng sá»­a bá»‘ cá»¥c tá»«ng mÃ n khi cÃ³ thá»i gian. */
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
<!-- Brand / Header -->
<div class="p-4 border-b border-slate-100 flex items-center justify-between">
<div class="flex items-center gap-3">
<div class="w-9 h-9 rounded-xl bg-primary text-white flex items-center justify-center font-bold text-sm shadow-xs shrink-0">
<span class="material-symbols-outlined text-[20px]">co_present</span>
</div>
<div class="overflow-hidden">
<div class="font-bold text-[13.5px] text-slate-900 leading-tight truncate">HUIT Há»c Vá»¥</div>
<div class="text-[11px] text-slate-500 font-medium truncate">Cá»•ng Giáº£ng viÃªn</div>
</div>
</div>
<button class="lg:hidden p-1.5 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100" onclick="toggleSidebar()">
<span class="material-symbols-outlined text-[20px]">close</span>
</button>
</div>
<!-- Navigation Menu (Dynamic by Stage) -->
<div class="flex-1 overflow-y-auto custom-scrollbar p-3 space-y-6">
<div class="space-y-1">
<div class="px-3 py-1.5 text-[10.5px] font-bold tracking-wider text-slate-500 uppercase">HOáº T Äá»˜NG GIáº¢NG Dáº Y</div>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/lich-giang-day']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">calendar_month</span>
<span class="truncate">Lá»‹ch giáº£ng dáº¡y</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/lop-hoc-phan']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">class</span>
<span class="truncate">Lá»›p há»c pháº§n phá»¥ trÃ¡ch</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/nhap-diem']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">edit_note</span>
<span class="truncate">Nháº­p &amp; ná»™p Ä‘iá»ƒm</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/de-cuong-hoc-phan']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">menu_book</span>
<span class="truncate">Äá» cÆ°Æ¡ng há»c pháº§n</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/thong-bao-lop']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">campaign</span>
<span class="truncate">Gá»­i thÃ´ng bÃ¡o cho lá»›p</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/ho-so-lich-ban']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">event_busy</span>
<span class="truncate">Há»“ sÆ¡ &amp; Lá»‹ch báº­n</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/lich-coi-thi']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">event_upcoming</span>
<span class="truncate">Lá»‹ch coi thi</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/diem-danh']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">check_circle</span>
<span class="truncate">Äiá»ƒm danh sinh viÃªn</span>
</a>
</div>
</div>
<!-- Sidebar Bottom -->
<div class="p-3.5 border-t border-slate-100 bg-slate-50/70">
<div class="flex items-center gap-2.5 text-xs text-slate-600">
<span class="material-symbols-outlined text-primary text-[18px]">support_agent</span>
<div>
<div class="font-semibold text-slate-800 text-[11.5px]">Há»— trá»£ ká»¹ thuáº­t: 028 3816 1673</div>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Lá»‹ch coi thi &amp; PhÃ¢n cÃ´ng giÃ¡m thá»‹</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Há»c ká»³ 1 â€¢ 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chá»§ / Cá»•ng Giáº£ng viÃªn / Lá»‹ch coi thi</div>
</div>
</div>
<!-- Header Right -->
<div class="flex items-center gap-2 sm:gap-3 shrink-0">
<!-- Role quick switch -->
<div class="hidden md:flex items-center p-1 bg-slate-100 rounded-lg text-xs font-medium">
<a class="px-2.5 py-1 text-slate-600 hover:text-slate-900" href="{{ route('fe.preview.show', ['screen' => 'student/tong-quan']) }}">Sinh viÃªn</a>
<span class="px-2.5 py-1 rounded-md bg-white text-primary font-semibold shadow-xs">Giáº£ng viÃªn</span>
<a class="px-2.5 py-1 text-slate-600 hover:text-slate-900" href="{{ route('fe.preview.show', ['screen' => 'admin/tong-quan']) }}">PhÃ²ng ÄT</a>
</div>
<!-- Notification Bell -->
<a class="relative p-2 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/thong-bao-lop']) }}" title="ThÃ´ng bÃ¡o">
<span class="material-symbols-outlined text-[21px]">notifications</span>
</a>
<!-- User Profile Pill -->
<div class="flex items-center gap-2.5 pl-2 border-l border-slate-200">
<div class="w-8 h-8 rounded-full bg-blue-100 text-primary font-bold text-xs flex items-center justify-center border border-blue-200">
            THN
          </div>
<div class="hidden sm:block text-left">
<div class="text-xs font-semibold text-slate-900 leading-tight">TS. Tráº§n HoÃ ng Nam</div>
<div class="text-[11px] text-slate-500">GV-2018042 â€¢ Khoa CNTT</div>
</div>
</div>
</div>
</header>
<!-- MAIN BODY CONTENT -->
<main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
<div class="space-y-6">
<!-- Top Navigation Tabs (Unified with Teaching Schedule) -->
<div class="flex items-center gap-2 border-b border-slate-200 pb-3">
<a class="px-4 py-2 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 flex items-center gap-1.5 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/lich-giang-day']) }}">
<span class="material-symbols-outlined text-[18px]">calendar_month</span>
<span>Lá»‹ch giáº£ng dáº¡y</span>
</a>
<span class="px-4 py-2 rounded-lg text-xs font-semibold bg-primary text-white flex items-center gap-1.5 shadow-xs">
<span class="material-symbols-outlined text-[18px]">event_upcoming</span>
<span>Lá»‹ch coi thi</span>
</span>
</div>
<!-- Summary Metric Cards -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
<div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
<span class="text-[11px] font-semibold text-slate-500 block uppercase">Tá»•ng sá»‘ ca coi thi</span>
<span class="text-xl font-extrabold text-slate-900 mt-1 block">04 ca</span>
<span class="text-[11px] text-slate-500">Ká»³ thi HK1 â€¢ 2026-2027</span>
</div>
<div class="bg-white p-4 rounded-xl border border-blue-200 bg-blue-50/20 shadow-xs">
<span class="text-[11px] font-semibold text-primary block uppercase">Ca thi káº¿ tiáº¿p</span>
<span class="text-base font-extrabold text-slate-900 mt-1 block truncate">18/11/2026 â€¢ Ca 1 (07:30)</span>
<span class="text-[11px] text-primary font-medium">PhÃ²ng B.302 (GiÃ¡m thá»‹ 1)</span>
</div>
<div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
<span class="text-[11px] font-semibold text-slate-500 block uppercase">Tá»•ng giá» quy Ä‘á»•i</span>
<span class="text-xl font-extrabold text-slate-900 mt-1 block">12.0 giá»</span>
<span class="text-[11px] text-emerald-700 font-medium">ÄÃ£ xÃ¡c nháº­n phÃ¢n cÃ´ng</span>
</div>
</div>
<!-- Exam Proctoring Table -->
<div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
<div class="p-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
<div>
<h3 class="font-bold text-slate-900 text-sm">Danh sÃ¡ch ca phÃ¢n cÃ´ng coi thi cÃ¡ nhÃ¢n</h3>
<p class="text-xs text-slate-500 mt-0.5">Giáº£ng viÃªn: TS. Tráº§n HoÃ ng Nam (GV-2018042) â€¢ Khoa CÃ´ng nghá»‡ ThÃ´ng tin</p>
</div>
<button class="h-9 px-3.5 rounded-lg border border-slate-300 hover:bg-slate-50 text-xs font-semibold text-slate-700 flex items-center gap-1.5 transition-colors no-print" onclick="window.print()">
<span class="material-symbols-outlined text-[16px]">print</span>
<span>In lá»‹ch coi thi</span>
</button>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
<th class="py-3 px-4">NgÃ y thi</th>
<th class="py-3 px-4">Ca thi / Thá»i gian</th>
<th class="py-3 px-4">PhÃ²ng thi</th>
<th class="py-3 px-4">Há»c pháº§n</th>
<th class="py-3 px-4 text-center">Sá»‘ thÃ­ sinh</th>
<th class="py-3 px-4">Vai trÃ² giÃ¡m thá»‹</th>
<th class="py-3 px-4 text-center">Tráº¡ng thÃ¡i</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
<tr class="hover:bg-blue-50/50 transition-colors bg-blue-50/20">
<td class="py-3.5 px-4 font-bold text-primary">18/11/2026</td>
<td class="py-3.5 px-4">Ca 1 (07:30 - 09:30)</td>
<td class="py-3.5 px-4 font-bold text-slate-900">B.302 (CS chÃ­nh)</td>
<td class="py-3.5 px-4">
<div class="font-semibold text-slate-900">Láº­p trÃ¬nh Java</div>
<div class="text-[11px] text-slate-500">MÃ£ LHP: 241_71ITEC3201_01</div>
</td>
<td class="py-3.5 px-4 text-center font-bold">42</td>
<td class="py-3.5 px-4">
<span class="px-2 py-0.5 rounded font-bold bg-blue-100 text-primary border border-blue-200">CÃ¡n bá»™ coi thi 1</span>
</td>
<td class="py-3.5 px-4 text-center">
<span class="px-2 py-0.5 rounded font-semibold bg-amber-50 text-amber-700 border border-amber-200">Sáº¯p diá»…n ra</span>
</td>
</tr>
<tr class="hover:bg-blue-50/50 transition-colors">
<td class="py-3.5 px-4 font-bold text-slate-900">22/11/2026</td>
<td class="py-3.5 px-4">Ca 3 (13:30 - 15:30)</td>
<td class="py-3.5 px-4 font-bold text-slate-900">C.205 (CS chÃ­nh)</td>
<td class="py-3.5 px-4">
<div class="font-semibold text-slate-900">CÆ¡ sá»Ÿ dá»¯ liá»‡u nÃ¢ng cao</div>
<div class="text-[11px] text-slate-500">MÃ£ LHP: 241_71ITEC2202_02</div>
</td>
<td class="py-3.5 px-4 text-center font-bold">45</td>
<td class="py-3.5 px-4">
<span class="px-2 py-0.5 rounded font-bold bg-slate-100 text-slate-800 border border-slate-200">CÃ¡n bá»™ coi thi 2</span>
</td>
<td class="py-3.5 px-4 text-center">
<span class="px-2 py-0.5 rounded font-semibold bg-slate-100 text-slate-600">ChÆ°a báº¯t Ä‘áº§u</span>
</td>
</tr>
<tr class="hover:bg-blue-50/50 transition-colors">
<td class="py-3.5 px-4 font-bold text-slate-900">25/11/2026</td>
<td class="py-3.5 px-4">Ca 2 (09:30 - 11:30)</td>
<td class="py-3.5 px-4 font-bold text-slate-900">Lab 4.2 (CS chÃ­nh)</td>
<td class="py-3.5 px-4">
<div class="font-semibold text-slate-900">Ká»¹ thuáº­t pháº§n má»m</div>
<div class="text-[11px] text-slate-500">MÃ£ LHP: 241_71ITEC2105_01</div>
</td>
<td class="py-3.5 px-4 text-center font-bold">38</td>
<td class="py-3.5 px-4">
<span class="px-2 py-0.5 rounded font-bold bg-blue-100 text-primary border border-blue-200">CÃ¡n bá»™ coi thi 1</span>
</td>
<td class="py-3.5 px-4 text-center">
<span class="px-2 py-0.5 rounded font-semibold bg-slate-100 text-slate-600">ChÆ°a báº¯t Ä‘áº§u</span>
</td>
</tr>
<tr class="hover:bg-blue-50/50 transition-colors">
<td class="py-3.5 px-4 font-bold text-slate-900">30/11/2026</td>
<td class="py-3.5 px-4">Ca 1 (07:30 - 09:30)</td>
<td class="py-3.5 px-4 font-bold text-slate-900">A.104 (CS chÃ­nh)</td>
<td class="py-3.5 px-4">
<div class="font-semibold text-slate-900">Máº¡ng mÃ¡y tÃ­nh</div>
<div class="text-[11px] text-slate-500">MÃ£ LHP: 241_71ITEC2108_02</div>
</td>
<td class="py-3.5 px-4 text-center font-bold">40</td>
<td class="py-3.5 px-4">
<span class="px-2 py-0.5 rounded font-bold bg-slate-100 text-slate-800 border border-slate-200">CÃ¡n bá»™ coi thi 2</span>
</td>
<td class="py-3.5 px-4 text-center">
<span class="px-2 py-0.5 rounded font-semibold bg-slate-100 text-slate-600">ChÆ°a báº¯t Ä‘áº§u</span>
</td>
</tr>
</tbody>
</table>
</div>
</div>
</div>
</main>
<!-- FOOTER -->
<footer class="py-4 px-6 bg-white border-t border-slate-200/80 text-center text-xs text-slate-500 no-print flex flex-col sm:flex-row items-center justify-between gap-2">
<div>Â© 2026 <strong>TrÆ°á»ng Äáº¡i há»c CÃ´ng ThÆ°Æ¡ng TP.HCM (HUIT)</strong> â€” Há»‡ thá»‘ng Quáº£n lÃ½ Há»c vá»¥.</div>
<div class="flex items-center gap-3 text-slate-500 text-[11px]">
<span>Há»— trá»£: 028 3816 1673</span>
<span>â€¢</span>
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
/* HUIT Há»c Vá»¥ - demo tráº¡ng thÃ¡i giao diá»‡n (UX-08).
   ThÃªm ?state=loading | empty | error vÃ o URL Ä‘á»ƒ xem tráº¡ng thÃ¡i Ä‘ang táº£i / rá»—ng / lá»—i cá»§a vÃ¹ng dá»¯ liá»‡u chÃ­nh.
   KhÃ´ng cÃ³ tham sá»‘: trang hiá»ƒn thá»‹ dá»¯ liá»‡u bÃ¬nh thÆ°á»ng. */
(function () {
  var st = new URLSearchParams(location.search).get('state');
  if (!st) return;
  function run() {
    var t = document.querySelector('main table');
    if (!t) return;
    var box = t.closest('.overflow-x-auto') || t.parentElement;
    var msg = {
      loading: ['Äang táº£i dá»¯ liá»‡uâ€¦', 'Vui lÃ²ng chá» trong giÃ¢y lÃ¡t.'],
      empty: ['ChÆ°a cÃ³ dá»¯ liá»‡u', 'Thá»­ Ä‘á»•i bá»™ lá»c hoáº·c thÃªm má»›i Ä‘á»ƒ báº¯t Ä‘áº§u.'],
      error: ['KhÃ´ng thá»ƒ táº£i dá»¯ liá»‡u', 'Káº¿t ná»‘i bá»‹ giÃ¡n Ä‘oáº¡n. HÃ£y kiá»ƒm tra máº¡ng rá»“i thá»­ láº¡i.']
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
    if (st === 'error') h += '<button type="button" onclick="location.href=location.pathname" style="margin-top:14px;padding:8px 16px;border-radius:8px;background:#1c55c4;color:#fff;font-weight:600">Thá»­ láº¡i</button>';
    p.innerHTML = h;
    box.style.display = 'none';
    box.parentNode.insertBefore(p, box.nextSibling);
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', run); else run();
})();
</script>

@endsection

