@extends('fe_preview.layouts.teacher')
@section('page_title', 'Cá»•ng Giáº£ng viÃªn - HUIT Há»c Vá»¥')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Cá»•ng Giáº£ng viÃªn - HUIT Há»c Vá»¥</title>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/lich-giang-day']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">calendar_month</span>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/lich-coi-thi']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">event_upcoming</span>
<span class="truncate">Lá»‹ch coi thi</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/diem-danh']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">check_circle</span>
<span class="truncate">Äiá»ƒm danh sinh viÃªn</span>
</a>
</div>
<div class="space-y-1">
<div class="px-3 py-1.5 text-[10.5px] font-bold tracking-wider text-slate-500 uppercase">Cá» Váº¤N &amp; DUYá»†T ÄÆ N</div>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/lop-co-van-hoc-tap']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">supervisor_account</span>
<span class="truncate">Lá»›p cá»‘ váº¥n há»c táº­p</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/duyet-don']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">rule_folder</span>
<span class="truncate">Duyá»‡t Ä‘Æ¡n tá»«</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/ket-qua-khao-sat']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">poll</span>
<span class="truncate">Káº¿t quáº£ kháº£o sÃ¡t</span>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Cá»•ng Giáº£ng viÃªn</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Há»c ká»³ 1 â€¢ 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chá»§ / Cá»•ng Giáº£ng viÃªn</div>
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
<div class="max-w-[1120px] mx-auto space-y-7">
<!-- Welcome & Context Header -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-1 border-b border-slate-200/60">
<div>
<h1 class="text-[28px] font-bold text-[#1E293B] tracking-tight flex items-center gap-2">
          Xin chÃ o, TS. Tráº§n HoÃ ng Nam <span>ðŸ‘‹</span>
</h1>
<div class="flex items-center gap-2 mt-1 text-sm text-slate-500 font-normal">
<span class="material-symbols-outlined text-base text-[#4776E6]">calendar_today</span>
<span>Thá»© NÄƒm, 15/10/2026</span>
<span class="text-slate-300">â€¢</span>
<span class="inline-flex items-center gap-1.5 font-semibold text-[#4776E6]">
<span class="w-2 h-2 rounded-full bg-[#4776E6] animate-ping inline-block"></span>
            3 ca dáº¡y Ä‘ang chá»
          </span>
<span class="text-slate-300">â€¢</span>
<span>Há»c ká»³ 1 (2024â€“2025)</span>
</div>
</div>
<!-- Action CTAs -->
<div class="flex items-center gap-3 shrink-0">
<button class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-lg shadow-sm transition-all active:scale-95">
<span class="material-symbols-outlined text-lg text-slate-500">how_to_reg</span>
          Äiá»ƒm quÃ¡ trÃ¬nh nhanh
        </button>
<button class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#4776E6] hover:bg-[#3b63c4] text-white text-sm font-semibold rounded-lg shadow-sm transition-all active:scale-95">
<span class="material-symbols-outlined text-lg">edit_note</span>
          Nháº­p Ä‘iá»ƒm há»c pháº§n
        </button>
</div>
</div>
<!-- ATTENTION PANEL: CÃ´ng viá»‡c há»c vá»¥ cáº§n xá»­ lÃ½ -->
<div class="bg-[#FFFBEB] border border-amber-200 rounded-xl p-5 shadow-sm">
<div class="flex items-center justify-between pb-3 border-b border-amber-200/80 mb-3">
<div class="flex items-center gap-2.5">
<div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm">
<span class="material-symbols-outlined text-base">priority_high</span>
</div>
<h2 class="text-base font-bold text-slate-800">CÃ´ng viá»‡c há»c vá»¥ cáº§n xá»­ lÃ½</h2>
<span class="bg-amber-100 text-amber-800 text-xs font-semibold px-2 py-0.5 rounded-full border border-amber-300/50">3 viá»‡c cáº§n xá»­ lÃ½</span>
</div>
<a class="text-xs font-semibold text-[#4776E6] hover:underline flex items-center gap-1" href="#">
          Xá»­ lÃ½ táº¥t cáº£
          <span class="material-symbols-outlined text-sm">arrow_forward</span>
</a>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-3">
<!-- Task 1 -->
<div class="bg-white rounded-lg p-3 border border-amber-200/80 flex items-start justify-between gap-2 shadow-sm">
<div class="flex flex-col">
<span class="text-xs font-semibold text-slate-800 line-clamp-1">Ná»™p Ä‘iá»ƒm cuá»‘i ká»³ HP Láº­p trÃ¬nh Java</span>
<span class="text-[11px] text-slate-500 mt-0.5">Deadline: HÃ´m nay 15/10/2026</span>
</div>
<span class="bg-red-100 text-red-700 text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0">Kháº©n cáº¥p</span>
</div>
<!-- Task 2 -->
<div class="bg-white rounded-lg p-3 border border-amber-200/80 flex items-start justify-between gap-2 shadow-sm">
<div class="flex flex-col">
<span class="text-xs font-semibold text-slate-800 line-clamp-1">PhÃª duyá»‡t Ä‘Æ¡n xin nghá»‰ há»c cá»§a SV</span>
<span class="text-[11px] text-slate-500 mt-0.5">3 Ä‘Æ¡n Ä‘ang chá» duyá»‡t</span>
</div>
<span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0">Cáº§n xá»­ lÃ½</span>
</div>
<!-- Task 3 -->
<div class="bg-white rounded-lg p-3 border border-amber-200/80 flex items-start justify-between gap-2 shadow-sm">
<div class="flex flex-col">
<span class="text-xs font-semibold text-slate-800 line-clamp-1">Cáº­p nháº­t Ä‘á» cÆ°Æ¡ng CÃ´ng nghá»‡ pháº§n má»m</span>
<span class="text-[11px] text-slate-500 mt-0.5">Thá»i háº¡n: 20/10/2026</span>
</div>
<span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0">Cáº§n xá»­ lÃ½</span>
</div>
</div>
</div>
<!-- SECTION: Lá»ŠCH GIáº¢NG Dáº Y HÃ”M NAY (3 equal-width columns) -->
<section class="space-y-3.5">
<div class="flex items-center justify-between">
<div class="flex items-center gap-2.5">
<span class="material-symbols-outlined text-[#4776E6] text-xl">calendar_today</span>
<h2 class="text-[20px] font-bold text-[#1E293B]">Lá»‹ch giáº£ng dáº¡y hÃ´m nay</h2>
<span class="text-xs font-semibold bg-slate-200 text-slate-700 px-2 py-0.5 rounded-full">15/10/2026</span>
</div>
<a class="text-xs font-semibold text-[#4776E6] hover:underline flex items-center gap-0.5" href="#">
          Xem toÃ n bá»™ thá»i khÃ³a biá»ƒu
          <span class="material-symbols-outlined text-sm">arrow_forward</span>
</a>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-5">
<!-- Card 1: Ca 1 (Äang diá»…n ra) -->
<div class="bg-white border-2 border-emerald-400 rounded-xl p-5 shadow-sm relative flex flex-col justify-between">
<div>
<div class="flex items-center justify-between mb-2">
<span class="text-xs font-semibold text-[#4776E6] flex items-center gap-1">
<span class="material-symbols-outlined text-sm">schedule</span>
                Ca 1: 07:30 â€“ 09:10 (Tiáº¿t 1 - 4)
              </span>
<span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-800 text-[11px] font-bold px-2 py-0.5 rounded-full border border-emerald-200">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                Äang diá»…n ra
              </span>
</div>
<h3 class="text-base font-bold text-slate-900 mt-1 truncate">Láº­p trÃ¬nh hÆ°á»›ng Ä‘á»‘i tÆ°á»£ng</h3>
<div class="mt-3.5 space-y-1.5 text-xs text-slate-600 bg-slate-50 p-3 rounded-lg border border-slate-200">
<div class="flex justify-between">
<span class="text-slate-500">MÃ£ HP:</span>
<span class="font-medium text-slate-800">IT302</span>
</div>
<div class="flex justify-between">
<span class="text-slate-500">Lá»›p há»c:</span>
<span class="font-medium text-slate-800">D22CNPM01</span>
</div>
<div class="flex justify-between">
<span class="text-slate-500">PhÃ²ng:</span>
<span class="font-medium text-slate-800">B.204 (TÃ¢n PhÃº)</span>
</div>
<div class="flex justify-between pt-1 border-t border-slate-200/60">
<span class="text-slate-500">SÄ© sá»‘:</span>
<span class="font-bold text-emerald-700">42/42 SV</span>
</div>
</div>
</div>
<div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
<span class="text-[11px] text-slate-500">ÄÃ£ Ä‘iá»ƒm quÃ¡ trÃ¬nh 40/42</span>
<button class="px-3 py-1.5 bg-[#4776E6] hover:bg-[#3b63c4] text-white text-xs font-semibold rounded-lg shadow-sm transition-all active:scale-95 flex items-center gap-1">
<span class="material-symbols-outlined text-sm">fact_check</span>
              Äiá»ƒm quÃ¡ trÃ¬nh
            </button>
</div>
</div>
<!-- Card 2: Ca 2 (Sáº¯p báº¯t Ä‘áº§u) -->
<div class="bg-white border border-[#E2E8F0] rounded-xl p-5 shadow-sm relative flex flex-col justify-between">
<div>
<div class="flex items-center justify-between mb-2">
<span class="text-xs font-semibold text-slate-600 flex items-center gap-1">
<span class="material-symbols-outlined text-sm text-slate-500">schedule</span>
                Ca 2: 09:30 â€“ 11:10 (Tiáº¿t 5 - 8)
              </span>
<span class="inline-flex items-center gap-1 bg-blue-100 text-[#4776E6] text-[11px] font-bold px-2 py-0.5 rounded-full border border-blue-200">
                Sáº¯p báº¯t Ä‘áº§u
              </span>
</div>
<h3 class="text-base font-bold text-slate-900 mt-1 truncate">CÆ¡ sá»Ÿ dá»¯ liá»‡u</h3>
<div class="mt-3.5 space-y-1.5 text-xs text-slate-600 bg-slate-50 p-3 rounded-lg border border-slate-200">
<div class="flex justify-between">
<span class="text-slate-500">MÃ£ HP:</span>
<span class="font-medium text-slate-800">IT205</span>
</div>
<div class="flex justify-between">
<span class="text-slate-500">Lá»›p há»c:</span>
<span class="font-medium text-slate-800">D22HTTT02</span>
</div>
<div class="flex justify-between">
<span class="text-slate-500">PhÃ²ng:</span>
<span class="font-medium text-slate-800">A.301</span>
</div>
<div class="flex justify-between pt-1 border-t border-slate-200/60">
<span class="text-slate-500">SÄ© sá»‘:</span>
<span class="font-semibold text-slate-800">38/40 SV</span>
</div>
</div>
</div>
<div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
<span class="text-[11px] text-slate-500">PhÃ²ng mÃ¡y sáºµn sÃ ng</span>
<button class="px-3 py-1.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-lg transition-colors">
              Xem danh sÃ¡ch
            </button>
</div>
</div>
<!-- Card 3: Ca 3 (ChÆ°a báº¯t Ä‘áº§u) -->
<div class="bg-white border border-[#E2E8F0] rounded-xl p-5 shadow-sm relative flex flex-col justify-between">
<div>
<div class="flex items-center justify-between mb-2">
<span class="text-xs font-semibold text-slate-600 flex items-center gap-1">
<span class="material-symbols-outlined text-sm text-slate-500">schedule</span>
                Ca 3: 13:00 â€“ 14:40 (Tiáº¿t 9 - 12)
              </span>
<span class="inline-flex items-center gap-1 bg-slate-100 text-slate-600 text-[11px] font-bold px-2 py-0.5 rounded-full border border-slate-200">
                ChÆ°a báº¯t Ä‘áº§u
              </span>
</div>
<h3 class="text-base font-bold text-slate-900 mt-1 truncate">Nháº­p mÃ´n CNTT</h3>
<div class="mt-3.5 space-y-1.5 text-xs text-slate-600 bg-slate-50 p-3 rounded-lg border border-slate-200">
<div class="flex justify-between">
<span class="text-slate-500">MÃ£ HP:</span>
<span class="font-medium text-slate-800">IT101</span>
</div>
<div class="flex justify-between">
<span class="text-slate-500">Lá»›p há»c:</span>
<span class="font-medium text-slate-800">D23CNTT03</span>
</div>
<div class="flex justify-between">
<span class="text-slate-500">PhÃ²ng:</span>
<span class="font-medium text-slate-800">C.105</span>
</div>
<div class="flex justify-between pt-1 border-t border-slate-200/60">
<span class="text-slate-500">SÄ© sá»‘:</span>
<span class="font-semibold text-slate-800">45/45 SV</span>
</div>
</div>
</div>
<div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
<span class="text-[11px] text-slate-500">Buá»•i 8/15</span>
<button class="px-3 py-1.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-lg transition-colors">
              Xem danh sÃ¡ch
            </button>
</div>
</div>
</div>
</section>
<!-- 2-COLUMN MAIN CONTENT GRID (Left 8 cols, Right 4 cols) -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
<!-- LEFT COLUMN: Há»ŒC PHáº¦N & Lá»šP PHá»¤ TRÃCH (8 Cols) -->
<section class="lg:col-span-8 bg-white border border-[#E2E8F0] rounded-xl p-5 shadow-sm space-y-4">
<div class="flex items-center justify-between pb-3 border-b border-slate-200">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[#4776E6] text-xl">school</span>
<h2 class="text-base font-bold text-[#1E293B]">Há»c pháº§n &amp; Lá»›p phá»¥ trÃ¡ch (Há»c ká»³ 1)</h2>
</div>
<span class="text-xs font-semibold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-full border border-slate-200">
            4 há»c pháº§n hoáº¡t Ä‘á»™ng
          </span>
</div>
<!-- 2-column grid of course cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
<!-- Course 1 -->
<div class="p-4 rounded-xl border border-slate-200 hover:border-[#4776E6]/60 transition-all bg-white shadow-sm flex flex-col justify-between">
<div>
<div class="flex items-start justify-between gap-2">
<span class="text-[11px] font-semibold text-[#4776E6] bg-blue-50 border border-blue-200 px-2 py-0.5 rounded">IT302 â€¢ 3 TÃ­n chá»‰</span>
<span class="text-xs text-slate-500 font-medium">D22CNPM01</span>
</div>
<h3 class="text-sm font-bold text-slate-900 mt-2">Láº­p trÃ¬nh hÆ°á»›ng Ä‘á»‘i tÆ°á»£ng</h3>
<p class="text-xs text-slate-500 mt-0.5">42 sinh viÃªn</p>
<div class="mt-3">
<div class="flex justify-between text-[11px] mb-1 text-slate-500">
<span>Tiáº¿n Ä‘á»™ giáº£ng dáº¡y</span>
<span class="font-semibold text-slate-800">8/15 tuáº§n (53%)</span>
</div>
<div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
<div class="bg-[#4776E6] h-1.5 rounded-full" style="width: 53.3%"></div>
</div>
</div>
</div>
<div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
<a class="text-slate-600 hover:text-[#4776E6] font-medium transition-colors" href="#">Xem danh sÃ¡ch</a>
<a class="text-[#4776E6] font-semibold hover:underline" href="#">Quáº£n lÃ½ Ä‘iá»ƒm</a>
</div>
</div>
<!-- Course 2 -->
<div class="p-4 rounded-xl border border-slate-200 hover:border-[#4776E6]/60 transition-all bg-white shadow-sm flex flex-col justify-between">
<div>
<div class="flex items-start justify-between gap-2">
<span class="text-[11px] font-semibold text-[#4776E6] bg-blue-50 border border-blue-200 px-2 py-0.5 rounded">IT205 â€¢ 3 TÃ­n chá»‰</span>
<span class="text-xs text-slate-500 font-medium">D22HTTT02</span>
</div>
<h3 class="text-sm font-bold text-slate-900 mt-2">CÆ¡ sá»Ÿ dá»¯ liá»‡u</h3>
<p class="text-xs text-slate-500 mt-0.5">38 sinh viÃªn</p>
<div class="mt-3">
<div class="flex justify-between text-[11px] mb-1 text-slate-500">
<span>Tiáº¿n Ä‘á»™ giáº£ng dáº¡y</span>
<span class="font-semibold text-slate-800">8/15 tuáº§n (53%)</span>
</div>
<div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
<div class="bg-[#4776E6] h-1.5 rounded-full" style="width: 53.3%"></div>
</div>
</div>
</div>
<div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
<a class="text-slate-600 hover:text-[#4776E6] font-medium transition-colors" href="#">Xem danh sÃ¡ch</a>
<a class="text-[#4776E6] font-semibold hover:underline" href="#">Quáº£n lÃ½ Ä‘iá»ƒm</a>
</div>
</div>
<!-- Course 3 -->
<div class="p-4 rounded-xl border border-slate-200 hover:border-[#4776E6]/60 transition-all bg-white shadow-sm flex flex-col justify-between">
<div>
<div class="flex items-start justify-between gap-2">
<span class="text-[11px] font-semibold text-[#4776E6] bg-blue-50 border border-blue-200 px-2 py-0.5 rounded">IT101 â€¢ 2 TÃ­n chá»‰</span>
<span class="text-xs text-slate-500 font-medium">D23CNTT03</span>
</div>
<h3 class="text-sm font-bold text-slate-900 mt-2">Nháº­p mÃ´n CNTT</h3>
<p class="text-xs text-slate-500 mt-0.5">45 sinh viÃªn</p>
<div class="mt-3">
<div class="flex justify-between text-[11px] mb-1 text-slate-500">
<span>Tiáº¿n Ä‘á»™ giáº£ng dáº¡y</span>
<span class="font-semibold text-slate-800">9/15 tuáº§n (60%)</span>
</div>
<div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
<div class="bg-[#4776E6] h-1.5 rounded-full" style="width: 60%"></div>
</div>
</div>
</div>
<div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
<a class="text-slate-600 hover:text-[#4776E6] font-medium transition-colors" href="#">Xem danh sÃ¡ch</a>
<a class="text-[#4776E6] font-semibold hover:underline" href="#">Quáº£n lÃ½ Ä‘iá»ƒm</a>
</div>
</div>
<!-- Course 4 -->
<div class="p-4 rounded-xl border border-slate-200 hover:border-[#4776E6]/60 transition-all bg-white shadow-sm flex flex-col justify-between">
<div>
<div class="flex items-start justify-between gap-2">
<span class="text-[11px] font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 px-2 py-0.5 rounded">IT401 â€¢ 2 TÃ­n chá»‰</span>
<span class="text-xs text-slate-500 font-medium">HÄ Khoa CNTT</span>
</div>
<h3 class="text-sm font-bold text-slate-900 mt-2">Äá»“ Ã¡n chuyÃªn ngÃ nh CNTT</h3>
<p class="text-xs text-slate-500 mt-0.5">12 nhÃ³m SV â€¢ 28 sinh viÃªn</p>
<div class="mt-3">
<div class="flex justify-between text-[11px] mb-1 text-slate-500">
<span>Tiáº¿n Ä‘á»™ nghiá»‡m thu</span>
<span class="font-semibold text-slate-800">Giai Ä‘oáº¡n 2 (BÃ¡o cÃ¡o sÆ¡ bá»™)</span>
</div>
<div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
<div class="bg-indigo-600 h-1.5 rounded-full" style="width: 45%"></div>
</div>
</div>
</div>
<div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
<a class="text-slate-600 hover:text-[#4776E6] font-medium transition-colors" href="#">Danh sÃ¡ch Ä‘á» tÃ i</a>
<a class="text-[#4776E6] font-semibold hover:underline" href="#">ÄÃ¡nh giÃ¡ tiáº¿n Ä‘á»™</a>
</div>
</div>
</div>
</section>
<!-- RIGHT COLUMN: NOTICES & DOCUMENTS (4 Cols) -->
<div class="lg:col-span-4 space-y-5">
<!-- Card: ThÃ´ng bÃ¡o Há»c vá»¥ má»›i nháº¥t -->
<div class="bg-white border border-[#E2E8F0] rounded-xl p-5 shadow-sm space-y-3.5">
<div class="flex items-center justify-between pb-2.5 border-b border-slate-200">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[#4776E6] text-xl">campaign</span>
<h2 class="text-sm font-bold text-[#1E293B]">ThÃ´ng bÃ¡o Há»c vá»¥ má»›i nháº¥t</h2>
</div>
<a class="text-xs text-[#4776E6] hover:underline font-semibold" href="#">Xem táº¥t cáº£</a>
</div>
<div class="space-y-2.5">
<!-- Notice 1: Kháº©n -->
<div class="p-2.5 rounded-lg border-l-4 border-l-red-500 border border-slate-200 bg-red-50/30 hover:bg-red-50/60 transition-colors">
<div class="flex items-center justify-between mb-1">
<span class="bg-red-100 text-red-800 text-[10px] font-bold px-1.5 py-0.5 rounded">Kháº©n</span>
<span class="text-[11px] text-slate-500">14/10/2026</span>
</div>
<h4 class="text-xs font-semibold text-slate-900 hover:text-[#4776E6] cursor-pointer line-clamp-2">
                Háº¡n chÃ³t nháº­p Ä‘iá»ƒm giá»¯a ká»³ Ä‘á»£t 1 nÄƒm há»c 2026-2027
              </h4>
</div>
<!-- Notice 2: ÄÃ o táº¡o -->
<div class="p-2.5 rounded-lg border-l-4 border-l-blue-500 border border-slate-200 bg-blue-50/20 hover:bg-blue-50/50 transition-colors">
<div class="flex items-center justify-between mb-1">
<span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-1.5 py-0.5 rounded">ÄÃ o táº¡o</span>
<span class="text-[11px] text-slate-500">12/10/2026</span>
</div>
<h4 class="text-xs font-semibold text-slate-900 hover:text-[#4776E6] cursor-pointer line-clamp-2">
                Káº¿ hoáº¡ch Ä‘Äƒng kÃ½ dáº¡y bÃ¹ vÃ  báº£o trÃ¬ phÃ²ng Lab
              </h4>
</div>
<!-- Notice 3: Kháº£o thÃ­ -->
<div class="p-2.5 rounded-lg border-l-4 border-l-amber-500 border border-slate-200 bg-amber-50/20 hover:bg-amber-50/50 transition-colors">
<div class="flex items-center justify-between mb-1">
<span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-1.5 py-0.5 rounded">Kháº£o thÃ­</span>
<span class="text-[11px] text-slate-500">10/10/2026</span>
</div>
<h4 class="text-xs font-semibold text-slate-900 hover:text-[#4776E6] cursor-pointer line-clamp-2">
                PhÃ¢n cÃ´ng coi thi vÃ  cháº¥m thi Ä‘á»£t bá»• sung
              </h4>
</div>
</div>
</div>
<!-- Card: Biá»ƒu máº«u & Quy Ä‘á»‹nh Ä‘Ã o táº¡o -->
<div class="bg-white border border-[#E2E8F0] rounded-xl p-5 shadow-sm">
<div class="flex items-center gap-2 pb-2.5 border-b border-slate-200 mb-3">
<span class="material-symbols-outlined text-slate-500 text-lg">folder_open</span>
<h2 class="text-sm font-bold text-[#1E293B]">Biá»ƒu máº«u &amp; Quy Ä‘á»‹nh Ä‘Ã o táº¡o</h2>
</div>
<div class="space-y-2 text-xs">
<a class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-colors" href="#">
<div class="flex items-center gap-2 text-slate-700 hover:text-[#4776E6]">
<span class="material-symbols-outlined text-base text-red-500">picture_as_pdf</span>
<span class="font-medium">Máº«u Ä‘á» cÆ°Æ¡ng chi tiáº¿t HP 2024</span>
</div>
<span class="text-[10px] font-semibold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">PDF</span>
</a>
<a class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-colors" href="#">
<div class="flex items-center gap-2 text-slate-700 hover:text-[#4776E6]">
<span class="material-symbols-outlined text-base text-blue-500">description</span>
<span class="font-medium">Quy cháº¿ Ä‘Ã o táº¡o tÃ­n chá»‰ sá»­a Ä‘á»•i</span>
</div>
<span class="text-[10px] font-semibold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">DOCX</span>
</a>
</div>
</div>
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

@endsection

