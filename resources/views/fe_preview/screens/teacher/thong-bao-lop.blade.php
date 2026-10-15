@extends('fe_preview.layouts.teacher')
@section('page_title', 'Cá»•ng ThÃ´ng tin Giáº£ng viÃªn - HUIT Há»c Vá»¥')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Cá»•ng ThÃ´ng tin Giáº£ng viÃªn - HUIT Há»c Vá»¥</title>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/thong-bao-lop']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">campaign</span>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Cá»•ng ThÃ´ng tin Giáº£ng viÃªn</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Há»c ká»³ 1 â€¢ 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chá»§ / Cá»•ng ThÃ´ng tin Giáº£ng viÃªn</div>
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
<!-- Top Administrative Header Bar -->
<header class="h-16 bg-surface-container-lowest border-b border-outline-variant px-8 flex items-center justify-between sticky top-0 z-30 shadow-sm">
<!-- Breadcrumbs & Path Info -->
<nav class="flex items-center gap-2 font-body-sm text-body-sm text-on-surface-variant">
<span class="hover:text-primary transition-colors">Cá»•ng Giáº£ng viÃªn</span>
<span class="material-symbols-outlined text-[14px]">chevron_right</span>
<span class="hover:text-primary transition-colors">ThÃ´ng bÃ¡o lá»›p há»c pháº§n</span>
<span class="material-symbols-outlined text-[14px]">chevron_right</span>
<span class="text-primary font-semibold">Soáº¡n thÃ´ng bÃ¡o má»›i</span>
</nav>
<!-- Right Utility Controls -->
<div class="flex items-center gap-3">
<!-- Selector Há»c ká»³ -->
<div class="flex items-center gap-2 bg-surface-container-low border border-outline-variant px-3 py-1.5 rounded-lg">
<span class="material-symbols-outlined text-[18px] text-primary">calendar_month</span>
<span class="font-label-md text-label-md text-on-surface font-semibold">Há»c ká»³ 1 (NÄƒm há»c 2026-2027)</span>
<span class="material-symbols-outlined text-[16px] text-outline">arrow_drop_down</span>
</div>
<div class="h-6 w-px bg-outline-variant mx-1"></div>
<!-- Help Action -->
<button class="p-2 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low transition-colors" title="Trá»£ giÃºp nghiá»‡p vá»¥">
<span class="material-symbols-outlined text-[20px]">help_outline</span>
</button>
<!-- Notification Bell -->
<button class="relative p-2 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low transition-colors" title="ThÃ´ng bÃ¡o há»‡ thá»‘ng">
<span class="material-symbols-outlined text-[20px]">notifications</span>
<span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-error ring-2 ring-surface-container-lowest"></span>
</button>
</div>
</header>
<!-- Page Body Container (Max-Width 1440px constraint & Padded) -->
<div class="max-w-[1440px] w-full mx-auto px-8 py-6 space-y-6">
<!-- Page Header & Main Navigation Tabs -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-outline-variant pb-4">
<div>
<div class="flex items-center gap-3">
<h1 class="font-headline-xl text-headline-xl text-on-surface font-bold tracking-tight">Gá»­i thÃ´ng bÃ¡o cho lá»›p há»c pháº§n</h1>
<span class="bg-primary-fixed text-on-primary-fixed font-semibold px-2.5 py-0.5 rounded-full text-label-sm border border-outline-variant">
              Khoa CNTT
            </span>
</div>
<p class="font-body-md text-body-md text-on-surface-variant mt-1">
            Soáº¡n tháº£o vÃ  phÃ¡t hÃ nh thÃ´ng tin há»c vá»¥, thay Ä‘á»•i lá»‹ch há»c hoáº·c thÃ´ng bÃ¡o kháº©n trá»±c tiáº¿p Ä‘áº¿n sinh viÃªn.
          </p>
</div>
<!-- Tab Clusters -->
<div class="flex items-center gap-1 bg-surface-container-low p-1 rounded-xl border border-outline-variant">
<button class="flex items-center gap-2 px-4 py-2 rounded-lg bg-surface-container-lowest text-primary font-semibold text-label-md shadow-xs border border-outline-variant/60">
<span class="material-symbols-outlined text-[18px]">edit_note</span>
<span>Soáº¡n thÃ´ng bÃ¡o má»›i</span>
</button>
<button class="flex items-center gap-2 px-4 py-2 rounded-lg text-on-surface-variant hover:text-on-surface font-medium text-label-md transition-colors">
<span class="material-symbols-outlined text-[18px]">history</span>
<span>Lá»‹ch sá»­ thÃ´ng bÃ¡o Ä‘Ã£ gá»­i (12)</span>
</button>
</div>
</div>
<!-- ========================================================================= -->
<!-- 3. TWO-COLUMN WORKSPACE: LEFT FORM (58%) & RIGHT LIVE PREVIEW (42%)       -->
<!-- ========================================================================= -->
<div class="grid grid-cols-12 gap-6 items-start">
<!-- ========================================== -->
<!-- A) LEFT COLUMN: COMPOSE FORM (7 Cols ~ 58%) -->
<!-- ========================================== -->
<div class="col-span-12 lg:col-span-7 space-y-5">
<div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-sm space-y-6">
<!-- KHá»I 1: CHá»ŒN Lá»šP Há»ŒC PHáº¦N NHáº¬N TIN -->
<div class="space-y-3">
<div class="flex items-center justify-between">
<label class="font-headline-sm text-headline-sm text-on-surface flex items-center gap-2">
<span class="w-2 h-4 bg-primary rounded-full"></span>
<span>1. Chá»n lá»›p há»c pháº§n nháº­n thÃ´ng bÃ¡o</span>
</label>
<span class="text-label-sm font-label-sm text-on-surface-variant">Báº¯t buá»™c chá»n Ã­t nháº¥t 1 lá»›p</span>
</div>
<!-- Multi-select Class Cards Grid -->
<div class="grid grid-cols-1 gap-2.5">
<!-- Class Card 1 [CHECKED] -->
<label class="relative flex items-center justify-between p-3.5 rounded-xl border-2 border-primary bg-surface-container-low/60 cursor-pointer transition-all hover:bg-surface-container-low">
<div class="flex items-center gap-3">
<input aria-label="Chá»n dÃ²ng" checked="" class="w-4 h-4 text-primary rounded border-outline focus:ring-primary focus:ring-offset-1" type="checkbox"/>
<div>
<div class="flex items-center gap-2">
<span class="font-label-lg text-label-lg text-on-surface font-semibold">241_71ITEC3201_01 - Láº­p trÃ¬nh Web</span>
<span class="px-2 py-0.5 rounded-full bg-surface-container-high text-primary font-semibold text-label-sm">Äang má»Ÿ</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Tiáº¿t 7 - 9 (Thá»© 2) â€¢ PhÃ²ng Lab PM04 â€¢ SÄ© sá»‘: <strong class="text-on-surface">60 SV</strong></p>
</div>
</div>
<div class="flex items-center text-primary">
<span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
</div>
</label>
<!-- Class Card 2 [CHECKED] -->
<label class="relative flex items-center justify-between p-3.5 rounded-xl border-2 border-primary bg-surface-container-low/60 cursor-pointer transition-all hover:bg-surface-container-low">
<div class="flex items-center gap-3">
<input aria-label="Chá»n dÃ²ng" checked="" class="w-4 h-4 text-primary rounded border-outline focus:ring-primary focus:ring-offset-1" type="checkbox"/>
<div>
<div class="flex items-center gap-2">
<span class="font-label-lg text-label-lg text-on-surface font-semibold">241_71ITEC3201_02 - Láº­p trÃ¬nh Web</span>
<span class="px-2 py-0.5 rounded-full bg-surface-container-high text-primary font-semibold text-label-sm">Äang má»Ÿ</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Tiáº¿t 1 - 3 (Thá»© 2) â€¢ PhÃ²ng Lab PM04 â€¢ SÄ© sá»‘: <strong class="text-on-surface">58 SV</strong></p>
</div>
</div>
<div class="flex items-center text-primary">
<span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
</div>
</label>
<!-- Class Card 3 [UNCHECKED] -->
<label class="relative flex items-center justify-between p-3.5 rounded-xl border border-outline-variant bg-surface-container-lowest cursor-pointer transition-all hover:bg-surface-container-low/30">
<div class="flex items-center gap-3">
<input aria-label="Chá»n dÃ²ng" class="w-4 h-4 text-primary rounded border-outline focus:ring-primary focus:ring-offset-1" type="checkbox"/>
<div>
<div class="flex items-center gap-2">
<span class="font-label-lg text-label-lg text-on-surface font-semibold">241_71ITEC2104_02 - Cáº¥u trÃºc dá»¯ liá»‡u &amp; GT</span>
<span class="px-2 py-0.5 rounded-full bg-surface-container text-on-surface-variant font-medium text-label-sm">Lá»›p lÃ½ thuyáº¿t</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Tiáº¿t 4 - 6 (Thá»© 4) â€¢ Giáº£ng Ä‘Æ°á»ng B201 â€¢ SÄ© sá»‘: 58 SV</p>
</div>
</div>
<div class="text-outline-variant">
<span class="material-symbols-outlined text-[20px]">radio_button_unchecked</span>
</div>
</label>
</div>
<!-- Summary Badge -->
<div class="bg-surface-container-low text-primary px-3.5 py-2.5 rounded-lg text-body-md font-semibold flex items-center gap-2 border border-primary-fixed">
<span class="material-symbols-outlined text-[18px]">verified</span>
<span>ÄÃ£ chá»n: 2 lá»›p â€¢ Tá»•ng cá»™ng 118 sinh viÃªn sáº½ nháº­n thÃ´ng bÃ¡o</span>
</div>
</div>
<!-- KHá»I 2: Má»¨C Äá»˜ Æ¯U TIÃŠN THÃ”NG BÃO -->
<div class="space-y-2 pt-2 border-t border-outline-variant/60">
<label class="font-headline-sm text-headline-sm text-on-surface flex items-center gap-2">
<span class="w-2 h-4 bg-primary rounded-full"></span>
<span>2. Má»©c Ä‘á»™ Æ°u tiÃªn thÃ´ng bÃ¡o</span>
</label>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
<!-- Option Normal -->
<label class="flex items-center gap-3 p-3 rounded-xl border border-outline-variant hover:bg-surface-container-low/40 cursor-pointer">
<input aria-label="Chá»n dÃ²ng" class="w-4 h-4 text-primary border-outline focus:ring-primary" name="priority_level" type="radio"/>
<div>
<span class="font-label-lg text-label-lg text-on-surface font-semibold">Má»©c Ä‘á»™ ThÃ´ng thÆ°á»ng</span>
<p class="font-body-sm text-body-sm text-on-surface-variant">Hiá»ƒn thá»‹ theo luá»“ng bÃ i Ä‘Äƒng tiÃªu chuáº©n</p>
</div>
</label>
<!-- Option Urgent [SELECTED] -->
<label class="flex items-start gap-3 p-3 rounded-xl border-2 border-error bg-error-container/30 cursor-pointer">
<input aria-label="Chá»n dÃ²ng" checked="" class="w-4 h-4 text-error border-error focus:ring-error mt-0.5" name="priority_level" type="radio"/>
<div>
<div class="flex items-center gap-2">
<span class="font-label-lg text-label-lg text-error font-bold">Quan trá»ng / Kháº©n cáº¥p</span>
<span class="px-2 py-0.5 rounded-full bg-error text-on-error font-bold text-label-sm">Æ¯u tiÃªn cao</span>
</div>
<p class="font-body-sm text-body-sm text-on-error-container mt-0.5">Sáº½ ghim Ä‘áº§u trang vÃ  gá»­i email tá»©c thá»i Ä‘áº¿n sinh viÃªn</p>
</div>
</label>
</div>
</div>
<!-- KHá»I 3: TIÃŠU Äá»€ THÃ”NG BÃO -->
<div class="space-y-2 pt-2 border-t border-outline-variant/60">
<div class="flex justify-between items-center">
<label class="font-headline-sm text-headline-sm text-on-surface flex items-center gap-2" for="notice_title">
<span class="w-2 h-4 bg-primary rounded-full"></span>
<span>3. TiÃªu Ä‘á» thÃ´ng bÃ¡o</span>
</label>
<span class="text-label-sm font-label-sm text-on-surface-variant">88 / 150 kÃ½ tá»±</span>
</div>
<input class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant text-on-surface font-medium text-body-md focus:border-primary focus:ring-2 focus:ring-primary/20 shadow-xs transition-colors" id="notice_title" placeholder="Nháº­p tiÃªu Ä‘á» rÃµ rÃ ng, ngáº¯n gá»n cho sinh viÃªn..." type="text" value="ThÃ´ng bÃ¡o dá»i lá»‹ch há»c thá»±c hÃ nh Lab PM04 sang Thá»© 5 vÃ  gia háº¡n bÃ i táº­p lá»›n Ä‘á»“ Ã¡n"/>
</div>
<!-- KHá»I 4: Ná»˜I DUNG THÃ”NG BÃO (RICH TEXT EDITOR) -->
<div class="space-y-2 pt-2 border-t border-outline-variant/60">
<label class="font-headline-sm text-headline-sm text-on-surface flex items-center gap-2">
<span class="w-2 h-4 bg-primary rounded-full"></span>
<span>4. Ná»™i dung chi tiáº¿t thÃ´ng bÃ¡o</span>
</label>
<!-- Rich Text Toolbar -->
<div class="rounded-xl border border-outline-variant overflow-hidden shadow-xs focus-within:border-primary">
<div class="bg-surface-container-low px-3 py-2 border-b border-outline-variant flex flex-wrap items-center gap-1 text-on-surface-variant">
<button class="p-1.5 rounded hover:bg-surface-container hover:text-on-surface transition-colors" title="In Ä‘áº­m (Ctrl+B)" type="button">
<span class="material-symbols-outlined text-[18px]">format_bold</span>
</button>
<button class="p-1.5 rounded hover:bg-surface-container hover:text-on-surface transition-colors" title="In nghiÃªng (Ctrl+I)" type="button">
<span class="material-symbols-outlined text-[18px]">format_italic</span>
</button>
<button class="p-1.5 rounded hover:bg-surface-container hover:text-on-surface transition-colors" title="Gáº¡ch chÃ¢n (Ctrl+U)" type="button">
<span class="material-symbols-outlined text-[18px]">format_underlined</span>
</button>
<div class="h-4 w-px bg-outline-variant mx-1"></div>
<button class="p-1.5 rounded hover:bg-surface-container hover:text-on-surface transition-colors" title="Danh sÃ¡ch cháº¥m" type="button">
<span class="material-symbols-outlined text-[18px]">format_list_bulleted</span>
</button>
<button class="p-1.5 rounded hover:bg-surface-container hover:text-on-surface transition-colors" title="Danh sÃ¡ch sá»‘" type="button">
<span class="material-symbols-outlined text-[18px]">format_list_numbered</span>
</button>
<div class="h-4 w-px bg-outline-variant mx-1"></div>
<button class="p-1.5 rounded hover:bg-surface-container hover:text-on-surface transition-colors" title="ChÃ¨n liÃªn káº¿t" type="button">
<span class="material-symbols-outlined text-[18px]">link</span>
</button>
<button class="p-1.5 rounded hover:bg-surface-container hover:text-on-surface transition-colors" title="TrÃ­ch dáº«n" type="button">
<span class="material-symbols-outlined text-[18px]">format_quote</span>
</button>
<button class="p-1.5 rounded hover:bg-surface-container hover:text-on-surface transition-colors ml-auto" title="Má»Ÿ rá»™ng khung soáº¡n" type="button">
<span class="material-symbols-outlined text-[18px]">open_in_full</span>
</button>
</div>
<!-- Text Area Editor Content -->
<textarea class="w-full p-4 border-0 text-on-surface text-body-md focus:ring-0 leading-relaxed resize-y bg-surface-container-lowest" placeholder="Nháº­p ná»™i dung Ä‘áº§y Ä‘á»§ cá»§a thÃ´ng bÃ¡o..." rows="7">ChÃ o cÃ¡c em sinh viÃªn hai lá»›p Láº­p trÃ¬nh Web,

Do phÃ²ng mÃ¡y Lab PM04 báº£o trÃ¬ Ä‘á»‹nh ká»³ vÃ o Thá»© 2 ngÃ y 21/10, lá»‹ch há»c thá»±c hÃ nh cá»§a hai lá»›p sáº½ chuyá»ƒn sang Thá»© 5 ngÃ y 24/10 cÃ¹ng khung giá» (Tiáº¿t 7 - 9). Äá»“ng thá»i, háº¡n ná»™p BÃ¡o cÃ¡o Ä‘á»“ Ã¡n giá»¯a ká»³ Ä‘Æ°á»£c gia háº¡n thÃªm 02 ngÃ y Ä‘áº¿n 23:59 ngÃ y 26/10 trÃªn há»‡ thá»‘ng. Äá» nghá»‹ cÃ¡c em theo dÃµi vÃ  hoÃ n thÃ nh Ä‘Ãºng háº¡n.

Tháº§y Nam.</textarea>
</div>
</div>
<!-- KHá»I 5: Tá»†P ÄÃNH KÃˆM -->
<div class="space-y-3 pt-2 border-t border-outline-variant/60">
<div class="flex items-center justify-between">
<label class="font-headline-sm text-headline-sm text-on-surface flex items-center gap-2">
<span class="w-2 h-4 bg-primary rounded-full"></span>
<span>5. Tá»‡p Ä‘Ã­nh kÃ¨m há»c táº­p</span>
</label>
<span class="text-label-sm font-label-sm text-on-surface-variant">2 tá»‡p Ä‘Ã£ táº£i (Tá»‘i Ä‘a 25MB)</span>
</div>
<!-- Attached Files List -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
<!-- File 1: PDF -->
<div class="flex items-center justify-between p-2.5 rounded-xl border border-outline-variant bg-surface-bright shadow-xs">
<div class="flex items-center gap-2.5 min-w-0">
<div class="w-8 h-8 rounded-lg bg-error-container text-error flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined text-[20px]">picture_as_pdf</span>
</div>
<div class="flex flex-col min-w-0">
<span class="font-label-md text-label-md text-on-surface font-semibold truncate" title="De_cuong_thuc_hanh_buoi_04.pdf">
                        De_cuong_thuc_hanh_buoi_04.pdf
                      </span>
<span class="font-body-sm text-body-sm text-on-surface-variant">1.8 MB â€¢ PDF</span>
</div>
</div>
<button class="p-1 rounded-lg text-outline hover:text-error hover:bg-error-container/40 transition-colors ml-2" title="XÃ³a tá»‡p Ä‘Ã­nh kÃ¨m">
<span class="material-symbols-outlined text-[18px]">close</span>
</button>
</div>
<!-- File 2: DOCX -->
<div class="flex items-center justify-between p-2.5 rounded-xl border border-outline-variant bg-surface-bright shadow-xs">
<div class="flex items-center gap-2.5 min-w-0">
<div class="w-8 h-8 rounded-lg bg-primary-fixed text-primary flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined text-[20px]">description</span>
</div>
<div class="flex flex-col min-w-0">
<span class="font-label-md text-label-md text-on-surface font-semibold truncate" title="Huong_dan_nop_bai_giua_ky.docx">
                        Huong_dan_nop_bai_giua_ky.docx
                      </span>
<span class="font-body-sm text-body-sm text-on-surface-variant">420 KB â€¢ DOCX</span>
</div>
</div>
<button class="p-1 rounded-lg text-outline hover:text-error hover:bg-error-container/40 transition-colors ml-2" title="XÃ³a tá»‡p Ä‘Ã­nh kÃ¨m">
<span class="material-symbols-outlined text-[18px]">close</span>
</button>
</div>
</div>
<!-- Upload Drag-Drop Area -->
<div class="border-2 border-dashed border-outline-variant hover:border-primary rounded-xl p-4 text-center cursor-pointer bg-surface-container-low/40 hover:bg-surface-container-low transition-colors">
<div class="flex items-center justify-center gap-2 text-primary font-semibold text-label-md">
<span class="material-symbols-outlined text-[20px]">upload_file</span>
<span>+ Táº£i thÃªm tá»‡p Ä‘Ã­nh kÃ¨m (Tá»‘i Ä‘a 25MB)</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Há»— trá»£ cÃ¡c Ä‘á»‹nh dáº¡ng PDF, DOCX, XLSX, PPTX, ZIP</p>
</div>
</div>
<!-- KHá»I 6: TÃ™Y CHá»ŒN Háº¸N GIá»œ Gá»¬I -->
<div class="pt-2 border-t border-outline-variant/60 space-y-3">
<label class="font-headline-sm text-headline-sm text-on-surface flex items-center gap-2">
<span class="w-2 h-4 bg-primary rounded-full"></span>
<span>6. TÃ¹y chá»n phÃ¡t hÃ nh</span>
</label>
<div class="p-3.5 rounded-xl bg-surface-container-low/50 border border-outline-variant space-y-3">
<label class="flex items-center gap-2.5 cursor-pointer">
<input aria-label="Chá»n dÃ²ng" checked="" class="w-4 h-4 text-primary rounded border-outline focus:ring-primary" type="checkbox"/>
<span class="font-label-lg text-label-lg text-on-surface font-semibold">Háº¹n giá» gá»­i tá»± Ä‘á»™ng vÃ o thá»i Ä‘iá»ƒm xÃ¡c Ä‘á»‹nh</span>
</label>
<div class="flex items-center gap-3 pl-6">
<div class="relative flex-1 max-w-xs">
<span class="material-symbols-outlined absolute left-3 top-2.5 text-primary text-[20px]">calendar_today</span>
<input aria-label="Ã” nháº­p liá»‡u" class="w-full pl-10 pr-3 py-2 rounded-lg border border-outline-variant text-on-surface font-medium text-body-md focus:border-primary focus:ring-2 focus:ring-primary/20 bg-surface-container-lowest" type="text" value="20/10/2026 07:00"/>
</div>
<span class="text-body-sm text-on-surface-variant">ThÃ´ng bÃ¡o sáº½ tá»± Ä‘á»™ng phÃ¡t vÃ o Ä‘Ãºng 07:00 sÃ¡ng</span>
</div>
</div>
</div>
<!-- KHá»I 7: THANH NÃšT HÃ€NH Äá»˜NG CHÃ‚N BIá»‚U MáºªU -->
<div class="pt-4 border-t border-outline-variant flex items-center justify-between gap-4">
<button class="border border-outline-variant bg-surface-container-lowest hover:bg-surface-container-low text-on-surface font-medium px-4 py-2.5 rounded-xl text-label-lg shadow-xs transition-colors flex items-center gap-2" type="button">
<span class="material-symbols-outlined text-[18px]">save</span>
<span>LÆ°u báº£n nhÃ¡p</span>
</button>
<button class="bg-primary hover:bg-primary-container text-white font-semibold px-6 py-3 rounded-xl shadow-md hover:shadow-lg transition-all flex items-center gap-2 text-label-lg active:scale-[0.99]" type="button">
<span class="material-symbols-outlined text-[20px]">send</span>
<span>Gá»¬I THÃ”NG BÃO CHO 118 SINH VIÃŠN</span>
</button>
</div>
</div>
</div>
<!-- ========================================== -->
<!-- B) RIGHT COLUMN: LIVE PREVIEW (5 Cols ~ 42%) -->
<!-- ========================================== -->
<div class="col-span-12 lg:col-span-5 space-y-5 sticky top-24">
<!-- Header Báº£n Xem TrÆ°á»›c -->
<div class="flex items-center justify-between px-1">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[22px]">visibility</span>
<h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Báº£n xem trÆ°á»›c trá»±c quan</h2>
</div>
<div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-surface-container-high border border-outline-variant text-label-sm font-semibold text-primary">
<span class="w-2 h-2 rounded-full bg-primary animate-ping"></span>
<span>Thá»i gian thá»±c</span>
</div>
</div>
<!-- Tháº» MÃ´ Phá»ng Giao Diá»‡n ThÃ´ng BÃ¡o Cá»•ng Sinh ViÃªn HUIT -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
<!-- Student Portal Simulated Header Bar -->
<div class="bg-surface-container-low px-4 py-2.5 border-b border-outline-variant flex items-center justify-between">
<div class="flex items-center gap-2">
<span class="w-2.5 h-2.5 rounded-full bg-error"></span>
<span class="w-2.5 h-2.5 rounded-full bg-outline"></span>
<span class="w-2.5 h-2.5 rounded-full bg-outline-variant"></span>
<span class="ml-2 font-label-sm text-label-sm text-on-surface-variant font-medium">Cá»•ng Sinh viÃªn HUIT â€¢ GÃ³c nhÃ¬n SV</span>
</div>
<span class="font-label-sm text-label-sm text-outline">v2.4</span>
</div>
<!-- Content Card Body -->
<div class="p-6 space-y-4">
<!-- Urgent Badge Header -->
<div>
<span class="bg-error-container text-error font-bold px-3 py-1 rounded-full text-label-sm inline-flex items-center gap-1.5 border border-error/20">
<span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">priority_high</span>
<span>THÃ”NG BÃO QUAN TRá»ŒNG Tá»ª GIáº¢NG VIÃŠN</span>
</span>
</div>
<!-- Sender & Course Metadata -->
<div class="flex items-start gap-3.5 pb-3 border-b border-outline-variant/60">
<div class="w-11 h-11 rounded-full bg-primary text-white font-bold flex items-center justify-center text-label-lg flex-shrink-0 shadow-xs">
                  TN
                </div>
<div class="min-w-0 flex-1">
<div class="flex items-center justify-between">
<h3 class="font-label-lg text-label-lg text-on-surface font-bold truncate">TS. Tráº§n HoÃ ng Nam</h3>
<span class="text-label-sm font-label-sm text-on-surface-variant">Khoa CNTT</span>
</div>
<p class="font-body-sm text-body-sm text-primary font-medium truncate mt-0.5">
                    Há»c pháº§n: Láº­p trÃ¬nh Web (Lá»›p HP: 241_71ITEC3201_01 &amp; 02)
                  </p>
<p class="font-body-sm text-body-sm text-outline flex items-center gap-1 mt-0.5">
<span class="material-symbols-outlined text-[14px]">schedule</span>
<span>Dá»± kiáº¿n gá»­i: 07:00 Thá»© Hai, ngÃ y 20/10/2026</span>
</p>
</div>
</div>
<!-- Main Article Title -->
<div>
<h4 class="font-headline-md text-headline-md text-on-surface font-bold leading-snug">
                  ThÃ´ng bÃ¡o dá»i lá»‹ch há»c thá»±c hÃ nh Lab PM04 sang Thá»© 5 vÃ  gia háº¡n bÃ i táº­p lá»›n Ä‘á»“ Ã¡n
                </h4>
</div>
<!-- Simulated Message Body -->
<div class="text-body-md text-on-surface space-y-2.5 leading-relaxed bg-surface-bright p-4 rounded-xl border border-outline-variant/40">
<p>ChÃ o cÃ¡c em sinh viÃªn hai lá»›p Láº­p trÃ¬nh Web,</p>
<p>
                  Do phÃ²ng mÃ¡y Lab PM04 báº£o trÃ¬ Ä‘á»‹nh ká»³ vÃ o Thá»© 2 ngÃ y 21/10, lá»‹ch há»c thá»±c hÃ nh cá»§a hai lá»›p sáº½ chuyá»ƒn sang <strong>Thá»© 5 ngÃ y 24/10</strong> cÃ¹ng khung giá» (Tiáº¿t 7 - 9).
                </p>
<p>
                  Äá»“ng thá»i, háº¡n ná»™p BÃ¡o cÃ¡o Ä‘á»“ Ã¡n giá»¯a ká»³ Ä‘Æ°á»£c <strong>gia háº¡n thÃªm 02 ngÃ y Ä‘áº¿n 23:59 ngÃ y 26/10</strong> trÃªn há»‡ thá»‘ng. Äá» nghá»‹ cÃ¡c em theo dÃµi vÃ  hoÃ n thÃ nh Ä‘Ãºng háº¡n.
                </p>
<p class="pt-1 font-medium text-on-surface">Tháº§y Nam.</p>
</div>
<!-- Attachments in Preview -->
<div class="space-y-2 pt-1">
<span class="text-label-sm font-label-sm text-on-surface-variant font-semibold uppercase tracking-wider block">
                  Tá»‡p Ä‘Ã­nh kÃ¨m dÃ nh cho sinh viÃªn:
                </span>
<div class="space-y-2">
<a class="flex items-center justify-between p-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest hover:bg-surface-container-low transition-colors group" href="#">
<div class="flex items-center gap-2 min-w-0">
<span class="material-symbols-outlined text-error text-[20px]">picture_as_pdf</span>
<span class="font-label-md text-label-md text-on-surface truncate group-hover:text-primary transition-colors">
                        De_cuong_thuc_hanh_buoi_04.pdf
                      </span>
</div>
<span class="text-body-sm text-outline ml-2 whitespace-nowrap">1.8 MB</span>
</a>
<a class="flex items-center justify-between p-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest hover:bg-surface-container-low transition-colors group" href="#">
<div class="flex items-center gap-2 min-w-0">
<span class="material-symbols-outlined text-primary text-[20px]">description</span>
<span class="font-label-md text-label-md text-on-surface truncate group-hover:text-primary transition-colors">
                        Huong_dan_nop_bai_giua_ky.docx
                      </span>
</div>
<span class="text-body-sm text-outline ml-2 whitespace-nowrap">420 KB</span>
</a>
</div>
</div>
</div>
<!-- Footer of Student Card -->
<div class="bg-surface-container-low/60 px-6 py-3 border-t border-outline-variant flex items-center justify-between text-body-sm text-on-surface-variant">
<span>ÄÆ°á»£c hiá»ƒn thá»‹ trá»±c quan theo thá»i gian thá»±c</span>
<span class="text-primary font-semibold">118 ngÆ°á»i nháº­n</span>
</div>
</div>
<!-- Confirm Schedule Dispatch Floating Card Preview -->
<div class="bg-surface-container-lowest rounded-xl border-2 border-primary-container shadow-md p-5 space-y-3">
<div class="flex items-center gap-2.5">
<div class="w-8 h-8 rounded-full bg-primary-fixed text-primary flex items-center justify-center">
<span class="material-symbols-outlined text-[18px]">event_available</span>
</div>
<h3 class="font-label-lg text-label-lg text-on-surface font-bold uppercase tracking-tight">
                XÃC NHáº¬N LÃŠN Lá»ŠCH Gá»¬I THÃ”NG BÃO
              </h3>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
              ThÃ´ng bÃ¡o sáº½ Ä‘Æ°á»£c tá»± Ä‘á»™ng gá»­i Ä‘áº¿n <strong>118 sinh viÃªn</strong> thuá»™c <strong>02 lá»›p há»c pháº§n</strong> Láº­p trÃ¬nh Web vÃ o lÃºc <strong>07:00 ngÃ y 20/10/2026</strong> qua Cá»•ng sinh viÃªn vÃ  email thÃ´ng bÃ¡o.
            </p>
<div class="flex items-center justify-end gap-2.5 pt-2">
<button class="px-3.5 py-1.5 rounded-lg border border-outline-variant text-on-surface-variant hover:text-on-surface text-label-md font-medium transition-colors" type="button">
                Quay láº¡i
              </button>
<button class="px-4 py-2 rounded-lg bg-primary hover:bg-primary-container text-white text-label-md font-semibold shadow-xs flex items-center gap-1.5 transition-colors" type="button">
<span class="material-symbols-outlined text-[18px]">schedule_send</span>
<span>XÃ¡c nháº­n lÃªn lá»‹ch (20/10)</span>
</button>
</div>
</div>
</div>
</div>
</div>
<!-- Institutional Footer Bar -->
<footer class="mt-auto border-t border-outline-variant bg-surface-container-lowest py-4 px-8 text-body-sm text-on-surface-variant flex flex-col md:flex-row items-center justify-between gap-2">
<div class="flex items-center gap-2">
<span class="font-semibold text-on-surface">TrÆ°á»ng Äáº¡i há»c CÃ´ng ThÆ°Æ¡ng TP. Há»“ ChÃ­ Minh (HUIT)</span>
<span>â€¢</span>
<span>Há»‡ thá»‘ng Quáº£n lÃ½ Há»c vá»¥ &amp; Giáº£ng dáº¡y</span>
</div>
<div class="flex items-center gap-4 text-outline">
<span>Há»— trá»£ ká»¹ thuáº­t: hotro@huit.edu.vn</span>
<span>â€¢</span>
<span>Hotline: (028) 3816 1673</span>
</div>
</footer>
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

