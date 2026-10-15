@extends('fe_preview.layouts.teacher')
@section('page_title', 'Cá»•ng Giáº£ng viÃªn HUIT - HUIT Há»c Vá»¥')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Cá»•ng Giáº£ng viÃªn HUIT - HUIT Há»c Vá»¥</title>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/lop-hoc-phan']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">class</span>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Cá»•ng Giáº£ng viÃªn HUIT</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Há»c ká»³ 1 â€¢ 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chá»§ / Cá»•ng Giáº£ng viÃªn HUIT</div>
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
<!-- ================= SECTION: 4 KPI CARDS ================= -->
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
<!-- KPI 1 -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/80 shadow-sm flex flex-col justify-between hover:border-primary/50 transition-colors">
<div class="flex items-start justify-between">
<div>
<p class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider font-semibold">Sá»‘ lá»›p phá»¥ trÃ¡ch</p>
<h3 class="font-headline-lg text-headline-lg font-bold text-on-surface mt-1">4 <span class="font-body-md text-body-md font-normal text-on-surface-variant">lá»›p há»c pháº§n</span></h3>
</div>
<div class="w-10 h-10 rounded-lg bg-surface-container-low text-primary flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined text-[22px]" data-icon="school">school</span>
</div>
</div>
<div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-green-100 text-green-800 text-xs font-semibold">
              Äang giáº£ng dáº¡y
            </span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Há»c ká»³ 1</span>
</div>
</div>
<!-- KPI 2 -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/80 shadow-sm flex flex-col justify-between hover:border-primary/50 transition-colors">
<div class="flex items-start justify-between">
<div>
<p class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider font-semibold">Tá»•ng sinh viÃªn</p>
<h3 class="font-headline-lg text-headline-lg font-bold text-on-surface mt-1">235 <span class="font-body-md text-body-md font-normal text-on-surface-variant">sinh viÃªn</span></h3>
</div>
<div class="w-10 h-10 rounded-lg bg-surface-container-low text-primary flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined text-[22px]" data-icon="groups">groups</span>
</div>
</div>
<div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
<span class="font-body-sm text-body-sm text-on-surface-variant">Trung bÃ¬nh:</span>
<span class="font-label-md text-label-md font-semibold text-on-surface">58 SV/lá»›p</span>
</div>
</div>
<!-- KPI 3 -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/80 shadow-sm flex flex-col justify-between hover:border-primary/50 transition-colors">
<div class="flex items-start justify-between">
<div>
<p class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider font-semibold">Tá»•ng giá» quy chuáº©n</p>
<h3 class="font-headline-lg text-headline-lg font-bold text-on-surface mt-1">180 <span class="font-body-md text-body-md font-normal text-on-surface-variant">tiáº¿t</span></h3>
</div>
<div class="w-10 h-10 rounded-lg bg-surface-container-low text-primary flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined text-[22px]" data-icon="hourglass_bottom">hourglass_bottom</span>
</div>
</div>
<div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
<span class="font-body-sm text-body-sm text-on-surface-variant">TÃ­n chá»‰ Ä‘á»‹nh má»©c:</span>
<span class="font-label-md text-label-md font-semibold text-primary">14 TÃ­n chá»‰</span>
</div>
</div>
<!-- KPI 4 -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/80 shadow-sm flex flex-col justify-between hover:border-primary/50 transition-colors">
<div class="flex items-start justify-between">
<div>
<p class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider font-semibold">Tiáº¿n Ä‘á»™ há»c ká»³</p>
<h3 class="font-headline-lg text-headline-lg font-bold text-on-surface mt-1">Tuáº§n 08 <span class="font-body-md text-body-md font-normal text-on-surface-variant">/ 15 tuáº§n</span></h3>
</div>
<div class="w-10 h-10 rounded-lg bg-surface-container-low text-primary flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined text-[22px]" data-icon="timelapse">timelapse</span>
</div>
</div>
<div class="mt-4 pt-3 border-t border-slate-100">
<div class="flex items-center justify-between text-xs mb-1">
<span class="text-on-surface-variant">Tiáº¿n Ä‘á»™ giáº£ng dáº¡y</span>
<span class="font-semibold text-primary">53.3%</span>
</div>
<div class="w-full bg-surface-container-high h-2 rounded-full overflow-hidden">
<div class="bg-primary h-full rounded-full" style="width: 53.3%;"></div>
</div>
</div>
</div>
</section>
<!-- ================= SECTION 1: LÆ¯á»šI THáºº Lá»šP Há»ŒC PHáº¦N ÄANG Dáº Y ================= -->
<section class="space-y-3">
<div class="flex items-center justify-between">
<div>
<h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">Lá»›p há»c pháº§n Ä‘ang phá»¥ trÃ¡ch</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Chá»n má»™t lá»›p há»c pháº§n Ä‘á»ƒ xem danh sÃ¡ch sinh viÃªn, Ä‘iá»ƒm quÃ¡ trÃ¬nh vÃ  quáº£n lÃ½ há»c vá»¥</p>
</div>
<span class="font-label-md text-label-md text-on-surface-variant bg-surface-container-low px-3 py-1 rounded-full border border-outline-variant/60">
            4 Lá»›p trong há»c ká»³
          </span>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
<!-- CARD 1 (ACTIVE / ÄANG XEM CHI TIáº¾T) -->
<div class="bg-surface-container-lowest rounded-xl p-5 border-2 border-primary shadow-md relative flex flex-col justify-between ring-2 ring-primary/10">
<div>
<div class="flex items-center justify-between mb-2">
<span class="font-label-sm text-label-sm font-bold text-primary tracking-wide">241_71ITEC3201_01</span>
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-primary text-white font-label-sm text-label-sm font-semibold shadow-xs">
<span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                  Äang xem chi tiáº¿t
                </span>
</div>
<h3 class="font-headline-sm text-headline-sm font-bold text-on-surface mb-1">Láº­p trÃ¬nh Web</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant mb-3 font-medium">3 TÃ­n chá»‰ â€¢ 45 Tiáº¿t lÃ½ thuyáº¿t &amp; thá»±c hÃ nh</p>
<div class="space-y-2 py-2 border-t border-b border-slate-100 font-body-sm text-body-sm">
<div class="flex items-center gap-2 text-on-surface">
<span class="material-symbols-outlined text-[18px] text-primary" data-icon="schedule">schedule</span>
<span>Thá»© 2, Tiáº¿t 1 - 3 (07:00 - 09:30)</span>
</div>
<div class="flex items-center gap-2 text-on-surface">
<span class="material-symbols-outlined text-[18px] text-primary" data-icon="meeting_room">meeting_room</span>
<span>PhÃ²ng A2.301 (CÆ¡ sá»Ÿ TÃ¢n PhÃº)</span>
</div>
<div class="flex items-center justify-between pt-1">
<span class="text-on-surface-variant">SÄ© sá»‘: <strong class="text-on-surface">60 / 60 SV</strong> (100%)</span>
<span class="text-xs bg-slate-100 px-2 py-0.5 rounded font-medium text-slate-700">12DHTH01, 12DHTH02</span>
</div>
</div>
<!-- Tráº¡ng thÃ¡i ná»™p Ä‘iá»ƒm -->
<div class="mt-3">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-amber-50 text-amber-800 border border-amber-200 text-xs font-semibold">
<span class="material-symbols-outlined text-[14px] text-amber-700" data-icon="warning">warning</span>
                  ChÆ°a ná»™p Ä‘iá»ƒm - Háº¡n: 30/11
                </span>
</div>
</div>
<!-- TÃ¡c vá»¥ nhanh -->
<div class="mt-4 pt-3 flex items-center gap-2 border-t border-slate-100">
<button class="flex-1 py-1.5 px-2 text-center rounded-lg bg-primary text-white font-label-md text-label-md font-semibold hover:bg-primary-container transition-colors shadow-sm">
                Xem danh sÃ¡ch SV
              </button>
<button class="py-1.5 px-2 rounded-lg border border-outline-variant hover:bg-slate-50 text-on-surface font-label-md text-label-md transition-colors" title="Nháº­p Ä‘iá»ƒm há»c pháº§n">
                Nháº­p Ä‘iá»ƒm
              </button>
<button class="p-1.5 rounded-lg border border-outline-variant hover:bg-slate-50 text-on-surface-variant hover:text-on-surface transition-colors" title="Gá»­i thÃ´ng bÃ¡o Ä‘áº¿n lá»›p">
<span class="material-symbols-outlined text-[18px]" data-icon="mail">mail</span>
</button>
</div>
</div>
<!-- CARD 2 -->
<div class="bg-surface-container-lowest rounded-xl p-5 border border-outline-variant/80 shadow-sm hover:border-primary/50 transition-colors flex flex-col justify-between">
<div>
<div class="flex items-center justify-between mb-2">
<span class="font-label-sm text-label-sm font-bold text-secondary tracking-wide">241_71ITEC2104_02</span>
<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-label-sm text-label-sm">
                  Lá»›p chÃ­nh quy
                </span>
</div>
<h3 class="font-headline-sm text-headline-sm font-bold text-on-surface mb-1">Cáº¥u trÃºc dá»¯ liá»‡u &amp; Giáº£i thuáº­t</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant mb-3 font-medium">4 TÃ­n chá»‰ â€¢ 60 Tiáº¿t</p>
<div class="space-y-2 py-2 border-t border-b border-slate-100 font-body-sm text-body-sm">
<div class="flex items-center gap-2 text-on-surface">
<span class="material-symbols-outlined text-[18px] text-secondary" data-icon="schedule">schedule</span>
<span>Thá»© 3, Tiáº¿t 4 - 6 (09:45 - 12:15)</span>
</div>
<div class="flex items-center gap-2 text-on-surface">
<span class="material-symbols-outlined text-[18px] text-secondary" data-icon="meeting_room">meeting_room</span>
<span>PhÃ²ng B.204 (CÆ¡ sá»Ÿ TÃ¢n PhÃº)</span>
</div>
<div class="flex items-center justify-between pt-1">
<span class="text-on-surface-variant">SÄ© sá»‘: <strong class="text-on-surface">58 / 60 SV</strong> (96%)</span>
<span class="text-xs bg-slate-100 px-2 py-0.5 rounded font-medium text-slate-700">12DHTH03</span>
</div>
</div>
<!-- Tráº¡ng thÃ¡i ná»™p Ä‘iá»ƒm -->
<div class="mt-3">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-green-50 text-green-800 border border-green-200 text-xs font-semibold">
<span class="material-symbols-outlined text-[14px] text-green-600" data-icon="check_circle">check_circle</span>
                  ÄÃ£ nháº­p Ä‘iá»ƒm quÃ¡ trÃ¬nh
                </span>
</div>
</div>
<!-- TÃ¡c vá»¥ nhanh -->
<div class="mt-4 pt-3 flex items-center gap-2 border-t border-slate-100">
<button class="flex-1 py-1.5 px-2 text-center rounded-lg border border-outline-variant hover:border-primary hover:text-primary font-label-md text-label-md font-semibold text-on-surface transition-colors">
                Xem chi tiáº¿t lá»›p
              </button>
<button class="py-1.5 px-2 rounded-lg border border-outline-variant hover:bg-slate-50 text-on-surface font-label-md text-label-md transition-colors">
                Nháº­p Ä‘iá»ƒm
              </button>
<button class="p-1.5 rounded-lg border border-outline-variant hover:bg-slate-50 text-on-surface-variant hover:text-on-surface transition-colors" title="Gá»­i thÃ´ng bÃ¡o">
<span class="material-symbols-outlined text-[18px]" data-icon="mail">mail</span>
</button>
</div>
</div>
<!-- CARD 3 -->
<div class="bg-surface-container-lowest rounded-xl p-5 border border-outline-variant/80 shadow-sm hover:border-primary/50 transition-colors flex flex-col justify-between">
<div>
<div class="flex items-center justify-between mb-2">
<span class="font-label-sm text-label-sm font-bold text-secondary tracking-wide">241_71ITEC3208_01</span>
<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-label-sm text-label-sm">
                  Lá»›p chÃ­nh quy
                </span>
</div>
<h3 class="font-headline-sm text-headline-sm font-bold text-on-surface mb-1">Máº¡ng mÃ¡y tÃ­nh</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant mb-3 font-medium">3 TÃ­n chá»‰ â€¢ 45 Tiáº¿t</p>
<div class="space-y-2 py-2 border-t border-b border-slate-100 font-body-sm text-body-sm">
<div class="flex items-center gap-2 text-on-surface">
<span class="material-symbols-outlined text-[18px] text-secondary" data-icon="schedule">schedule</span>
<span>Thá»© 6, Tiáº¿t 1 - 3 (07:00 - 09:30)</span>
</div>
<div class="flex items-center gap-2 text-on-surface">
<span class="material-symbols-outlined text-[18px] text-secondary" data-icon="meeting_room">meeting_room</span>
<span>PhÃ²ng B.105 (CÆ¡ sá»Ÿ TÃ¢n PhÃº)</span>
</div>
<div class="flex items-center justify-between pt-1">
<span class="text-on-surface-variant">SÄ© sá»‘: <strong class="text-on-surface">55 / 60 SV</strong> (91%)</span>
<span class="text-xs bg-slate-100 px-2 py-0.5 rounded font-medium text-slate-700">12DHMM01</span>
</div>
</div>
<!-- Tráº¡ng thÃ¡i ná»™p Ä‘iá»ƒm -->
<div class="mt-3">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-amber-50 text-amber-800 border border-amber-200 text-xs font-semibold">
<span class="material-symbols-outlined text-[14px] text-amber-700" data-icon="warning">warning</span>
                  ChÆ°a ná»™p Ä‘iá»ƒm - Háº¡n: 30/11
                </span>
</div>
</div>
<!-- TÃ¡c vá»¥ nhanh -->
<div class="mt-4 pt-3 flex items-center gap-2 border-t border-slate-100">
<button class="flex-1 py-1.5 px-2 text-center rounded-lg border border-outline-variant hover:border-primary hover:text-primary font-label-md text-label-md font-semibold text-on-surface transition-colors">
                Xem chi tiáº¿t lá»›p
              </button>
<button class="py-1.5 px-2 rounded-lg border border-outline-variant hover:bg-slate-50 text-on-surface font-label-md text-label-md transition-colors">
                Nháº­p Ä‘iá»ƒm
              </button>
<button class="p-1.5 rounded-lg border border-outline-variant hover:bg-slate-50 text-on-surface-variant hover:text-on-surface transition-colors" title="Gá»­i thÃ´ng bÃ¡o">
<span class="material-symbols-outlined text-[18px]" data-icon="mail">mail</span>
</button>
</div>
</div>
<!-- CARD 4 -->
<div class="bg-surface-container-lowest rounded-xl p-5 border border-outline-variant/80 shadow-sm hover:border-primary/50 transition-colors flex flex-col justify-between">
<div>
<div class="flex items-center justify-between mb-2">
<span class="font-label-sm text-label-sm font-bold text-secondary tracking-wide">241_71ITEC4105_03</span>
<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 font-label-sm text-label-sm font-semibold">
                  KhÃ³a luáº­n TN
                </span>
</div>
<h3 class="font-headline-sm text-headline-sm font-bold text-on-surface mb-1">KhÃ³a luáº­n tá»‘t nghiá»‡p</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant mb-3 font-medium">4 TÃ­n chá»‰ â€¢ Äá»“ Ã¡n tá»‘t nghiá»‡p</p>
<div class="space-y-2 py-2 border-t border-b border-slate-100 font-body-sm text-body-sm">
<div class="flex items-center gap-2 text-on-surface">
<span class="material-symbols-outlined text-[18px] text-secondary" data-icon="schedule">schedule</span>
<span>Lá»‹ch há»p theo phÃ¢n cÃ´ng GV</span>
</div>
<div class="flex items-center gap-2 text-on-surface">
<span class="material-symbols-outlined text-[18px] text-secondary" data-icon="meeting_room">meeting_room</span>
<span>PhÃ²ng Lab PM04</span>
</div>
<div class="flex items-center justify-between pt-1">
<span class="text-on-surface-variant">SÄ© sá»‘ hÆ°á»›ng dáº«n: <strong class="text-on-surface">12 SV</strong></span>
<span class="text-xs bg-slate-100 px-2 py-0.5 rounded font-medium text-slate-700">KhÃ³a 11</span>
</div>
</div>
<!-- Tráº¡ng thÃ¡i ná»™p Ä‘iá»ƒm -->
<div class="mt-3">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-blue-50 text-blue-800 border border-blue-200 text-xs font-semibold">
<span class="material-symbols-outlined text-[14px] text-blue-600" data-icon="history_edu">history_edu</span>
                  Äang cháº¥m tiáº¿n Ä‘á»™ Ä‘á»£t 2
                </span>
</div>
</div>
<!-- TÃ¡c vá»¥ nhanh -->
<div class="mt-4 pt-3 flex items-center gap-2 border-t border-slate-100">
<button class="flex-1 py-1.5 px-2 text-center rounded-lg border border-outline-variant hover:border-primary hover:text-primary font-label-md text-label-md font-semibold text-on-surface transition-colors">
                Xem chi tiáº¿t Ä‘á» tÃ i
              </button>
<button class="py-1.5 px-2 rounded-lg border border-outline-variant hover:bg-slate-50 text-on-surface font-label-md text-label-md transition-colors">
                Nháº­p Ä‘iá»ƒm
              </button>
<button class="p-1.5 rounded-lg border border-outline-variant hover:bg-slate-50 text-on-surface-variant hover:text-on-surface transition-colors" title="Gá»­i thÃ´ng bÃ¡o">
<span class="material-symbols-outlined text-[18px]" data-icon="mail">mail</span>
</button>
</div>
</div>
</div>
</section>
<!-- ================= SECTION 2: CHI TIáº¾T Lá»šP Há»ŒC PHáº¦N & DANH SÃCH SINH VIÃŠN ================= -->
<section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
<!-- Panel Header -->
<div class="p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-lg bg-surface-container-low text-primary flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined text-[24px]" data-icon="format_list_bulleted">format_list_bulleted</span>
</div>
<div>
<div class="flex items-center gap-2">
<h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">
                  Danh sÃ¡ch sinh viÃªn lá»›p: 241_71ITEC3201_01 - Láº­p trÃ¬nh Web
                </h2>
<span class="px-2 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed text-xs font-semibold">SÄ© sá»‘: 60 SV</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant">Thá»© 2 (Tiáº¿t 1-3) â€¢ PhÃ²ng A2.301 â€¢ Giáº£ng viÃªn phá»¥ trÃ¡ch: TS. Tráº§n HoÃ ng Nam</p>
</div>
</div>
<!-- Quick Action Buttons -->
<div class="flex items-center gap-2 flex-wrap">
<button class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-outline-variant text-on-surface font-label-md text-label-md hover:bg-slate-50 transition-colors">
<span class="material-symbols-outlined text-[18px]" data-icon="print">print</span>
<span>In danh sÃ¡ch lá»›p (A4)</span>
</button>
<button class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-primary text-white font-label-md text-label-md font-semibold hover:bg-primary-container transition-colors shadow-sm">
<span class="material-symbols-outlined text-[18px]" data-icon="download">download</span>
<span>Xuáº¥t Excel (.xlsx)</span>
</button>
</div>
</div>
<!-- Filter & Search Toolbar -->
<div class="p-4 bg-surface-container-low/50 border-b border-slate-100 grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
<!-- Search Bar -->
<div class="md:col-span-5 relative">
<span class="material-symbols-outlined absolute left-3 top-2.5 text-on-surface-variant text-[20px]" data-icon="search">search</span>
<input aria-label="TÃ¬m kiáº¿m theo MSSV, Há» vÃ  tÃªn sinh viÃªn..." class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface font-body-md text-body-md placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary" placeholder="TÃ¬m kiáº¿m theo MSSV, Há» vÃ  tÃªn sinh viÃªn..." type="text"/>
</div>
<!-- Lá»›p hÃ nh chÃ­nh Filter -->
<div class="md:col-span-3">
<div class="relative">
<select aria-label="Lá»›p hÃ nh chÃ­nh: Táº¥t cáº£" class="w-full px-3 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:border-primary">
<option value="">Lá»›p hÃ nh chÃ­nh: Táº¥t cáº£</option>
<option value="12DHTH01">12DHTH01 (32 sinh viÃªn)</option>
<option value="12DHTH02">12DHTH02 (28 sinh viÃªn)</option>
</select>
</div>
</div>
<!-- Attendance / Status Filter -->
<div class="md:col-span-3">
<div class="relative">
<select aria-label="Tráº¡ng thÃ¡i Ä‘iá»ƒm quÃ¡ trÃ¬nh: Táº¥t cáº£" class="w-full px-3 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:border-primary">
<option value="">Tráº¡ng thÃ¡i Ä‘iá»ƒm quÃ¡ trÃ¬nh: Táº¥t cáº£</option>
<option value="normal">BÃ¬nh thÆ°á»ng (Äá»§ Ä‘iá»u kiá»‡n dá»± thi)</option>
<option value="warning">Cáº£nh bÃ¡o váº¯ng (&gt; 20% sá»‘ buá»•i)</option>
</select>
</div>
</div>
<!-- Refresh / Reset -->
<div class="md:col-span-1 flex justify-end">
<button class="p-2 border border-outline-variant rounded-lg bg-surface-container-lowest hover:bg-slate-50 text-on-surface-variant hover:text-on-surface" title="Äáº·t láº¡i bá»™ lá»c">
<span class="material-symbols-outlined text-[20px]" data-icon="refresh">refresh</span>
</button>
</div>
</div>
<!-- Academic Roster Data Table -->
<div class="overflow-x-auto custom-scrollbar">
<table class="w-full text-left border-collapse min-w-[1100px]">
<thead>
<tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-label-md text-label-md uppercase tracking-wider">
<th class="py-3 px-4 w-14 text-center sticky left-0 bg-slate-50 z-10 border-r border-slate-200">STT</th>
<th class="py-3 px-4 w-32 sticky left-14 bg-slate-50 z-10 border-r border-slate-200">MSSV</th>
<th class="py-3 px-4 min-w-[220px]">Há» vÃ  tÃªn sinh viÃªn</th>
<th class="py-3 px-4 w-28">Lá»›p HC</th>
<th class="py-3 px-4 w-28">NgÃ y sinh</th>
<th class="py-3 px-4 min-w-[200px]">Email HUIT</th>
<th class="py-3 px-4 w-28 text-center">Sá»‘ buá»•i váº¯ng</th>
<th class="py-3 px-4 w-44">Tráº¡ng thÃ¡i há»c vá»¥</th>
<th class="py-3 px-4 w-44 text-right">Thao tÃ¡c</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 font-body-md text-body-md text-on-surface">
<!-- Row 1 -->
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center font-medium sticky left-0 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">1</td>
<td class="py-3 px-4 font-mono font-semibold text-primary sticky left-14 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">2001210123</td>
<td class="py-3 px-4">
<div class="flex items-center gap-3">
<img alt="" class="w-8 h-8 rounded-full object-cover ring-1 ring-slate-200" data-alt="ChÃ¢n dung tháº» sinh viÃªn nam thanh niÃªn Viá»‡t Nam má»‰m cÆ°á»i nghiÃªm tÃºc, phong cÃ¡ch áº£nh há»“ sÆ¡ giÃ¡o dá»¥c Ä‘áº¡i há»c, Ã¡o sÆ¡ mi tráº¯ng, phÃ´ng ná»n xanh nháº¡t hiá»‡n Ä‘áº¡i trÆ°á»ng Äáº¡i há»c HUIT." src="https://lh3.googleusercontent.com/aida-public/AB6AXuDlX_Llk9V_SbXl0azq4_fs6jBfn9YWE7euBWyAfpkWL0VIaJ2w3pLGkfQ5_6NTDE8pmNn4kbz179P3VfQhhiW_IIVUSfW-z-G4ObllQGxUev_Sq0hhm84_PWHTji0sV2wGslLQmRDr2JGS34tpK4rSp04VTuEEfkGxj3iMUl8Cf_SOGlgQuz3CHiEPxZSU39N3ylZSeNgWpPCGvCPMpJh-ehavPl7zMH_-Je9Kybzx1GDpvzQvdcvD_cqajt9Kdj8rEXQ4UmpeePM"/>
<div>
<span class="font-semibold block">Nguyá»…n VÄƒn An</span>
<span class="text-xs text-on-surface-variant">NhÃ³m 01 (Thá»±c hÃ nh)</span>
</div>
</div>
</td>
<td class="py-3 px-4 font-medium">12DHTH01</td>
<td class="py-3 px-4 text-on-surface-variant">14/05/2003</td>
<td class="py-3 px-4 text-on-surface-variant font-mono text-xs">2001210123@huit.edu.vn</td>
<td class="py-3 px-4 text-center font-semibold text-slate-700">0 / 15</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-label-sm text-label-sm font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    BÃ¬nh thÆ°á»ng
                  </span>
</td>
<td class="py-3 px-4 text-right">
<div class="inline-flex items-center gap-1">
<button class="px-2.5 py-1 rounded border border-outline-variant hover:bg-primary hover:text-white hover:border-primary text-xs font-medium transition-colors" title="Äiá»ƒm quÃ¡ trÃ¬nh buá»•i hÃ´m nay">Äiá»ƒm quÃ¡ trÃ¬nh</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Ghi chÃº há»c vá»¥">
<span class="material-symbols-outlined text-[18px]" data-icon="edit_note">edit_note</span>
</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Xem há»“ sÆ¡">
<span class="material-symbols-outlined text-[18px]" data-icon="visibility">visibility</span>
</button>
</div>
</td>
</tr>
<!-- Row 2 -->
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center font-medium sticky left-0 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">2</td>
<td class="py-3 px-4 font-mono font-semibold text-primary sticky left-14 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">2001210045</td>
<td class="py-3 px-4">
<div class="flex items-center gap-3">
<img alt="" class="w-8 h-8 rounded-full object-cover ring-1 ring-slate-200" data-alt="ChÃ¢n dung tháº» sinh viÃªn ná»¯ Viá»‡t Nam tÃ³c dÃ i gá»n gÃ ng, Ä‘eo kÃ­nh cáº­n, ná»¥ cÆ°á»i thanh lá»‹ch tá»± tin, Ã¡o Ä‘á»“ng phá»¥c Ä‘áº¡i há»c, phong cÃ¡ch chuáº©n má»±c áº£nh há»“ sÆ¡ trÆ°á»ng HUIT." src="https://lh3.googleusercontent.com/aida-public/AB6AXuAdcF8aZGCsvGOnekCbE6cDneGDF2FfrYfYQXlDRjVbElOu81clJX0sOX86JweAkvJN6e2s9GI9qs-Ilcv2VbGLk4HA-dHB2VDbzSGWwY36a1zVfunYyATPZaiovuBeJY1CZ1Zmn9KE8aT5jMP7cKFR20tu3evTDpa0RIrRSiF7siwO9fPR-0efp1f9g8haqO-gnVbL2xwfAR4QpZ5jrGCqJ9mtAQM__m4T1J1IW5BMikBMuAJ1NwuFSx4VFnRA8zoVTJiJg3cQqAI"/>
<div>
<span class="font-semibold block">Tráº§n Thá»‹ BÃ­ch</span>
<span class="text-xs text-on-surface-variant">NhÃ³m 01 (Thá»±c hÃ nh)</span>
</div>
</div>
</td>
<td class="py-3 px-4 font-medium">12DHTH01</td>
<td class="py-3 px-4 text-on-surface-variant">22/08/2003</td>
<td class="py-3 px-4 text-on-surface-variant font-mono text-xs">2001210045@huit.edu.vn</td>
<td class="py-3 px-4 text-center font-semibold text-slate-700">1 / 15</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-label-sm text-label-sm font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    BÃ¬nh thÆ°á»ng
                  </span>
</td>
<td class="py-3 px-4 text-right">
<div class="inline-flex items-center gap-1">
<button class="px-2.5 py-1 rounded border border-outline-variant hover:bg-primary hover:text-white hover:border-primary text-xs font-medium transition-colors" title="Äiá»ƒm quÃ¡ trÃ¬nh buá»•i hÃ´m nay">Äiá»ƒm quÃ¡ trÃ¬nh</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Ghi chÃº há»c vá»¥">
<span class="material-symbols-outlined text-[18px]" data-icon="edit_note">edit_note</span>
</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Xem há»“ sÆ¡">
<span class="material-symbols-outlined text-[18px]" data-icon="visibility">visibility</span>
</button>
</div>
</td>
</tr>
<!-- Row 3 (Cáº£nh bÃ¡o váº¯ng) -->
<tr class="hover:bg-slate-50/80 bg-red-50/30 transition-colors">
<td class="py-3 px-4 text-center font-medium sticky left-0 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">3</td>
<td class="py-3 px-4 font-mono font-semibold text-primary sticky left-14 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">2001210189</td>
<td class="py-3 px-4">
<div class="flex items-center gap-3">
<img alt="" class="w-8 h-8 rounded-full object-cover ring-1 ring-slate-200" data-alt="ChÃ¢n dung tháº» sinh viÃªn nam thanh niÃªn Viá»‡t Nam hiá»‡n Ä‘áº¡i, tháº§n thÃ¡i táº­p trung, gÃ³c chá»¥p tháº³ng trang trá»ng, phÃ´ng ná»n trÆ°á»ng há»c tÃ´ng xanh dÆ°Æ¡ng cÃ´ng nghá»‡ cao." src="https://lh3.googleusercontent.com/aida-public/AB6AXuBucS_M8gwoH4-6q4irZH5cMG--lImWzkFH-klpjcqDBa9kKPApKgccr0Qrqv5sAiK9olU-C-fttk5wEAsC8YxxuIAxuytVKDjMfA7_j_6Tspo8qwHK3G5a6erlxF7tl-5pIpvDkVVj9m2nty9_UzGnr_GWKMArQDVUqV6E5lC13hs-yPhhBrvM_kSrg7D7SFmuz35t2mL_rlWSu0U215UOUXH7_eDEtUvCLc5Cfc6IBAEq2Nm8GddZ4csiwpL2y5b9Sj8hAo9blYs"/>
<div>
<span class="font-semibold block text-red-950">LÃª HoÃ ng CÆ°á»ng</span>
<span class="text-xs text-red-700 font-medium">Váº¯ng 3 buá»•i liÃªn tiáº¿p</span>
</div>
</div>
</td>
<td class="py-3 px-4 font-medium">12DHTH02</td>
<td class="py-3 px-4 text-on-surface-variant">05/11/2003</td>
<td class="py-3 px-4 text-on-surface-variant font-mono text-xs">2001210189@huit.edu.vn</td>
<td class="py-3 px-4 text-center font-bold text-red-700">3 / 15</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 border border-rose-300 font-label-sm text-label-sm font-bold">
<span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                    Cáº£nh bÃ¡o váº¯ng &gt; 20%
                  </span>
</td>
<td class="py-3 px-4 text-right">
<div class="inline-flex items-center gap-1">
<button class="px-2.5 py-1 rounded border border-rose-300 text-rose-700 hover:bg-rose-600 hover:text-white text-xs font-semibold transition-colors" title="Äiá»ƒm quÃ¡ trÃ¬nh buá»•i hÃ´m nay">Äiá»ƒm quÃ¡ trÃ¬nh</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Ghi chÃº há»c vá»¥">
<span class="material-symbols-outlined text-[18px]" data-icon="edit_note">edit_note</span>
</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Xem há»“ sÆ¡">
<span class="material-symbols-outlined text-[18px]" data-icon="visibility">visibility</span>
</button>
</div>
</td>
</tr>
<!-- Row 4 -->
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center font-medium sticky left-0 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">4</td>
<td class="py-3 px-4 font-mono font-semibold text-primary sticky left-14 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">2001210012</td>
<td class="py-3 px-4">
<div class="flex items-center gap-3">
<img alt="" class="w-8 h-8 rounded-full object-cover ring-1 ring-slate-200" data-alt="ChÃ¢n dung sinh viÃªn ná»¯ Viá»‡t Nam tÃ³c ngáº¯n cÃ¡ tÃ­nh, má»‰m cÆ°á»i nháº¹ nhÃ ng tá»± tin, Ã¡nh sÃ¡ng studio tráº¯ng má»m, Ä‘á»“ng phá»¥c há»c viá»‡n cÃ´ng nghá»‡ HUIT chuyÃªn nghiá»‡p." src="https://lh3.googleusercontent.com/aida-public/AB6AXuA1a--_nN7xz2L-WRNUqUd_09u348gjJm63VDQAnkkRfteOp06kB7IqsJtiPDBtcaRkun0PwSfS8HaOSeWqYQahjQZ5T61e8M1cnQ_wy3gOvjCV0rNhh85rlLZ1mNXl_lsOpB1cQEkvadmf7wd6zSgKMcgPl1Z5JPBwGhdwiqPkpAX4f5nh70JVzMMF1yXwwte2oUJzIBDC-1A-xWoN8Tk3KVmQ4cirpB8U-rDBwUlOxZ_OOr1BNYyVLZvoh5DM0OsOd1EeLz8UbL4"/>
<div>
<span class="font-semibold block">Pháº¡m Thá»‹ Dung</span>
<span class="text-xs text-on-surface-variant">Lá»›p trÆ°á»Ÿng 12DHTH01</span>
</div>
</div>
</td>
<td class="py-3 px-4 font-medium">12DHTH01</td>
<td class="py-3 px-4 text-on-surface-variant">19/02/2003</td>
<td class="py-3 px-4 text-on-surface-variant font-mono text-xs">2001210012@huit.edu.vn</td>
<td class="py-3 px-4 text-center font-semibold text-slate-700">0 / 15</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-label-sm text-label-sm font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    BÃ¬nh thÆ°á»ng
                  </span>
</td>
<td class="py-3 px-4 text-right">
<div class="inline-flex items-center gap-1">
<button class="px-2.5 py-1 rounded border border-outline-variant hover:bg-primary hover:text-white hover:border-primary text-xs font-medium transition-colors">Äiá»ƒm quÃ¡ trÃ¬nh</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Ghi chÃº há»c vá»¥">
<span class="material-symbols-outlined text-[18px]" data-icon="edit_note">edit_note</span>
</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Xem há»“ sÆ¡">
<span class="material-symbols-outlined text-[18px]" data-icon="visibility">visibility</span>
</button>
</div>
</td>
</tr>
<!-- Row 5 -->
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center font-medium sticky left-0 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">5</td>
<td class="py-3 px-4 font-mono font-semibold text-primary sticky left-14 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">2001210356</td>
<td class="py-3 px-4">
<div class="flex items-center gap-3">
<img alt="" class="w-8 h-8 rounded-full object-cover ring-1 ring-slate-200" data-alt="ChÃ¢n dung tháº» sinh viÃªn nam ngÆ°á»i Viá»‡t Nam thÃ´ng minh, kÃ­nh cáº­n trÃ²n, Ã¡o sÆ¡ mi cá»Ÿi cÃºc trÃªn, gÃ³c chá»¥p chuyÃªn nghiá»‡p hiá»‡n Ä‘áº¡i Ã¡nh sÃ¡ng nháº¹." src="https://lh3.googleusercontent.com/aida-public/AB6AXuAbMzJ_mZWlKgNbLMHAuOQoQY3yueIHVgcFrZFY4YTGPUSOup1aiAxNKZjtyiANxyQrrNMaoHmWdKSrjZ1UvMmZaBCV04HXIThks0FRurIjd5w2FF1uHFN0O1JL18TE7lAcO6KMGyScS5_hac_PP-MWSgyiRg9A-PYTaeZiKSFXMgqvmo-Ep7F0xPwj-1WtsozCLzCIiksB9A5gTGSP8y7aUl0R2LRKWGc225qI7Yqwz36gJK4WKtZZtkvdRaD3xuVWoZM0aAZ9k0w"/>
<div>
<span class="font-semibold block">Äá»— Quá»‘c DÅ©ng</span>
<span class="text-xs text-on-surface-variant">NhÃ³m 02 (Thá»±c hÃ nh)</span>
</div>
</div>
</td>
<td class="py-3 px-4 font-medium">12DHTH02</td>
<td class="py-3 px-4 text-on-surface-variant">30/09/2003</td>
<td class="py-3 px-4 text-on-surface-variant font-mono text-xs">2001210356@huit.edu.vn</td>
<td class="py-3 px-4 text-center font-semibold text-slate-700">1 / 15</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-label-sm text-label-sm font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    BÃ¬nh thÆ°á»ng
                  </span>
</td>
<td class="py-3 px-4 text-right">
<div class="inline-flex items-center gap-1">
<button class="px-2.5 py-1 rounded border border-outline-variant hover:bg-primary hover:text-white hover:border-primary text-xs font-medium transition-colors">Äiá»ƒm quÃ¡ trÃ¬nh</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Ghi chÃº há»c vá»¥">
<span class="material-symbols-outlined text-[18px]" data-icon="edit_note">edit_note</span>
</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Xem há»“ sÆ¡">
<span class="material-symbols-outlined text-[18px]" data-icon="visibility">visibility</span>
</button>
</div>
</td>
</tr>
<!-- Row 6 -->
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center font-medium sticky left-0 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">6</td>
<td class="py-3 px-4 font-mono font-semibold text-primary sticky left-14 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">2001210411</td>
<td class="py-3 px-4">
<div class="flex items-center gap-3">
<img alt="" class="w-8 h-8 rounded-full object-cover ring-1 ring-slate-200" data-alt="ChÃ¢n dung tháº» sinh viÃªn ná»¯ sinh Viá»‡t Nam duyÃªn dÃ¡ng, Ã¡nh máº¯t tá»± tin, tÃ³c cá»™t cao thanh lá»‹ch, phÃ´ng ná»n xanh dÆ°Æ¡ng Ä‘áº·c trÆ°ng cá»§a Ä‘áº¡i há»c." src="https://lh3.googleusercontent.com/aida-public/AB6AXuBpritCM7Jy6FjrTqgg3qGCslvfw8LIRbMq7PyuQCwSn4Yyp5OKq-LQK0Rw_9EHPTyfnO8LlDWa-xCKC4PCKj2jeDIOf1GiVqlKKCtMVpunz4IEROJuzkdQbBn-75Fhn9_weBI-VHAIdRg-UGrggOP7TH47FLB3r-DpMWtfkgHz-y-PqezHHqvBoU1O6WJ_PLMl0W1rF52rfguOW77HcrToBInt4R5HDrFpNSGV9Sriy2OXCxtfBLEs8gdF_nw3dEBrd5pnGzCgVQA"/>
<div>
<span class="font-semibold block">VÅ© Mai HÆ°Æ¡ng</span>
<span class="text-xs text-on-surface-variant">NhÃ³m 02 (Thá»±c hÃ nh)</span>
</div>
</div>
</td>
<td class="py-3 px-4 font-medium">12DHTH01</td>
<td class="py-3 px-4 text-on-surface-variant">11/04/2003</td>
<td class="py-3 px-4 text-on-surface-variant font-mono text-xs">2001210411@huit.edu.vn</td>
<td class="py-3 px-4 text-center font-semibold text-slate-700">0 / 15</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-label-sm text-label-sm font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    BÃ¬nh thÆ°á»ng
                  </span>
</td>
<td class="py-3 px-4 text-right">
<div class="inline-flex items-center gap-1">
<button class="px-2.5 py-1 rounded border border-outline-variant hover:bg-primary hover:text-white hover:border-primary text-xs font-medium transition-colors">Äiá»ƒm quÃ¡ trÃ¬nh</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Ghi chÃº há»c vá»¥">
<span class="material-symbols-outlined text-[18px]" data-icon="edit_note">edit_note</span>
</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Xem há»“ sÆ¡">
<span class="material-symbols-outlined text-[18px]" data-icon="visibility">visibility</span>
</button>
</div>
</td>
</tr>
<!-- Row 7 -->
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center font-medium sticky left-0 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">7</td>
<td class="py-3 px-4 font-mono font-semibold text-primary sticky left-14 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">2001210098</td>
<td class="py-3 px-4">
<div class="flex items-center gap-3">
<img alt="" class="w-8 h-8 rounded-full object-cover ring-1 ring-slate-200" data-alt="ChÃ¢n dung tháº» sinh viÃªn nam sinh viÃªn ngÃ nh pháº§n má»m Viá»‡t Nam tráº» trung, biá»ƒu cáº£m táº­p trung, ná»n studio xanh ghi thanh lá»‹ch cháº¥t lÆ°á»£ng cao." src="https://lh3.googleusercontent.com/aida-public/AB6AXuBbfIL59yakz06R_ZnUYoITFv1jPNzbXSWwNKoz1U86DUpAgGTV5y5eErV5qAjIwgYUUJW2kJLm2LYfx50LRZfhtEIorET6KWQopLmn4QdsGMKkX6TSUDYjv9X0IXJW_58VbwHDV-hBQ7N-fsr4BH_JYlJVp7QlDSfbE5pAEPr-bICARmkkKU1KE8tDuDmE0zPvWYxNZRhPn4yVju0Nltj7AMtc-w4nuHAT4SY-WwsfmBvWXb4dkC_8Nx2RoFMNLj5uiIGa6z2XDC4"/>
<div>
<span class="font-semibold block">HoÃ ng Minh KhÃ´i</span>
<span class="text-xs text-on-surface-variant">NhÃ³m 02 (Thá»±c hÃ nh)</span>
</div>
</div>
</td>
<td class="py-3 px-4 font-medium">12DHTH02</td>
<td class="py-3 px-4 text-on-surface-variant">17/12/2003</td>
<td class="py-3 px-4 text-on-surface-variant font-mono text-xs">2001210098@huit.edu.vn</td>
<td class="py-3 px-4 text-center font-semibold text-slate-700">2 / 15</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 font-label-sm text-label-sm font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    Cáº§n lÆ°u Ã½
                  </span>
</td>
<td class="py-3 px-4 text-right">
<div class="inline-flex items-center gap-1">
<button class="px-2.5 py-1 rounded border border-outline-variant hover:bg-primary hover:text-white hover:border-primary text-xs font-medium transition-colors">Äiá»ƒm quÃ¡ trÃ¬nh</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Ghi chÃº há»c vá»¥">
<span class="material-symbols-outlined text-[18px]" data-icon="edit_note">edit_note</span>
</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Xem há»“ sÆ¡">
<span class="material-symbols-outlined text-[18px]" data-icon="visibility">visibility</span>
</button>
</div>
</td>
</tr>
<!-- Row 8 -->
<tr class="hover:bg-slate-50/80 transition-colors">
<td class="py-3 px-4 text-center font-medium sticky left-0 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">8</td>
<td class="py-3 px-4 font-mono font-semibold text-primary sticky left-14 bg-surface-container-lowest hover:bg-slate-50/80 z-10 border-r border-slate-100">2001210512</td>
<td class="py-3 px-4">
<div class="flex items-center gap-3">
<img alt="" class="w-8 h-8 rounded-full object-cover ring-1 ring-slate-200" data-alt="ChÃ¢n dung tháº» sinh viÃªn nam thanh niÃªn Viá»‡t Nam ná»¥ cÆ°á»i tÆ°Æ¡i táº¯n, Ä‘á»“ng phá»¥c Ã¡o polo xanh thÃªu logo trÆ°á»ng HUIT, Ã¡nh sÃ¡ng cÃ¢n Ä‘á»‘i tá»± nhiÃªn." src="https://lh3.googleusercontent.com/aida-public/AB6AXuAhtEaKXJd8jRv43CwqwahDfrYa5nUd_MewZMOflKKJdhDsovAM_W-AZR9aiti2Hz9w1Ee6JycDbsUVCoBvA_qcwf5qi_-v9EU7M26Yifj4aikAV6s7gWeDDFNF-nMScvuOAlVWvD-vcDFDb2bqzzJlAZ0uD_bW6KWugBldn04IAZ_zqRuSGu0lHVQEMgzjkmvZvdoSvJGTDkAffEmIS6MqC3fqXKAlaucoGjbYuVFwzlAWiEaHzy_6WxtdIUZYj-nn3wRlV7iYvfY"/>
<div>
<span class="font-semibold block">LÃ½ ThÃ nh Long</span>
<span class="text-xs text-on-surface-variant">NhÃ³m 01 (Thá»±c hÃ nh)</span>
</div>
</div>
</td>
<td class="py-3 px-4 font-medium">12DHTH01</td>
<td class="py-3 px-4 text-on-surface-variant">08/07/2003</td>
<td class="py-3 px-4 text-on-surface-variant font-mono text-xs">2001210512@huit.edu.vn</td>
<td class="py-3 px-4 text-center font-semibold text-slate-700">0 / 15</td>
<td class="py-3 px-4">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-label-sm text-label-sm font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    BÃ¬nh thÆ°á»ng
                  </span>
</td>
<td class="py-3 px-4 text-right">
<div class="inline-flex items-center gap-1">
<button class="px-2.5 py-1 rounded border border-outline-variant hover:bg-primary hover:text-white hover:border-primary text-xs font-medium transition-colors">Äiá»ƒm quÃ¡ trÃ¬nh</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Ghi chÃº há»c vá»¥">
<span class="material-symbols-outlined text-[18px]" data-icon="edit_note">edit_note</span>
</button>
<button class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-slate-100" title="Xem há»“ sÆ¡">
<span class="material-symbols-outlined text-[18px]" data-icon="visibility">visibility</span>
</button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<!-- Pagination Controls -->
<div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-on-surface-variant font-body-sm text-body-sm bg-surface-container-lowest">
<div class="flex items-center gap-2">
<span>Hiá»ƒn thá»‹</span>
<select aria-label="8 sinh viÃªn" class="px-2 py-1 bg-surface-container-low border border-outline-variant rounded font-medium text-on-surface text-xs focus:outline-none">
<option value="8">8 sinh viÃªn</option>
<option value="15">15 sinh viÃªn</option>
<option value="30">30 sinh viÃªn</option>
<option value="60">Táº¥t cáº£ 60 sinh viÃªn</option>
</select>
<span>trong tá»•ng sá»‘ <strong>60</strong> sinh viÃªn cá»§a lá»›p há»c pháº§n</span>
</div>
<div class="flex items-center gap-1">
<button class="p-1.5 rounded border border-outline-variant hover:bg-slate-50 disabled:opacity-40" disabled="">
<span class="material-symbols-outlined text-[18px]" data-icon="first_page">first_page</span>
</button>
<button class="p-1.5 rounded border border-outline-variant hover:bg-slate-50 disabled:opacity-40" disabled="">
<span class="material-symbols-outlined text-[18px]" data-icon="chevron_left">chevron_left</span>
</button>
<button class="w-8 h-8 rounded bg-primary text-white font-semibold text-xs shadow-xs">1</button>
<button class="w-8 h-8 rounded border border-outline-variant hover:bg-slate-50 font-medium text-xs text-on-surface">2</button>
<button class="w-8 h-8 rounded border border-outline-variant hover:bg-slate-50 font-medium text-xs text-on-surface">3</button>
<span class="px-1 text-slate-500">...</span>
<button class="w-8 h-8 rounded border border-outline-variant hover:bg-slate-50 font-medium text-xs text-on-surface">8</button>
<button class="p-1.5 rounded border border-outline-variant hover:bg-slate-50 text-on-surface">
<span class="material-symbols-outlined text-[18px]" data-icon="chevron_right">chevron_right</span>
</button>
<button class="p-1.5 rounded border border-outline-variant hover:bg-slate-50 text-on-surface">
<span class="material-symbols-outlined text-[18px]" data-icon="last_page">last_page</span>
</button>
</div>
</div>
</section>
<!-- Institutional Footer -->
<footer class="pt-6 pb-8 border-t border-outline-variant/60 text-center text-on-surface-variant space-y-1">
<p class="font-label-md text-label-md font-semibold text-on-surface">
          TrÆ°á»ng Äáº¡i há»c CÃ´ng ThÆ°Æ¡ng TP. Há»“ ChÃ­ Minh (HUIT) â€¢ Cá»•ng ThÃ´ng tin Giáº£ng viÃªn &amp; Quáº£n lÃ½ ÄÃ o táº¡o
        </p>
<p class="font-body-sm text-body-sm">
          Äá»‹a chá»‰: 140 LÃª Trá»ng Táº¥n, P. TÃ¢y Tháº¡nh, Q. TÃ¢n PhÃº, TP. Há»“ ChÃ­ Minh | PhÃ²ng Quáº£n lÃ½ ÄÃ o táº¡o: (028) 3816 1673 - daotao@huit.edu.vn
        </p>
<p class="font-label-sm text-label-sm text-slate-500">
          PhiÃªn báº£n há»‡ thá»‘ng: 4.8.2-ERP â€¢ Báº£n quyá»n Â© 2026 HUIT. Táº¥t cáº£ cÃ¡c quyá»n Ä‘Æ°á»£c báº£o lÆ°u.
        </p>
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

