@extends('fe_preview.layouts.teacher')
@section('page_title', 'Äá» cÆ°Æ¡ng chi tiáº¿t há»c pháº§n - HUIT Há»c Vá»¥')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Äá» cÆ°Æ¡ng chi tiáº¿t há»c pháº§n - HUIT Há»c Vá»¥</title>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/de-cuong-hoc-phan']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">menu_book</span>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Äá» cÆ°Æ¡ng chi tiáº¿t há»c pháº§n</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Há»c ká»³ 1 â€¢ 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chá»§ / Äá» cÆ°Æ¡ng chi tiáº¿t há»c pháº§n</div>
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
<!-- ===================================================================== -->
<!-- Cá»˜T TRÃI (340px): DANH SÃCH Há»ŒC PHáº¦N BIÃŠN SOáº N Äá»€ CÆ¯Æ NG               -->
<!-- ===================================================================== -->
<section class="w-[350px] flex-shrink-0 flex flex-col gap-4">
<!-- Header cá»™t trÃ¡i + NÃºt táº¡o má»›i -->
<div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant shadow-sm flex flex-col gap-3">
<div class="flex items-center justify-between">
<h2 class="font-headline-sm text-headline-sm text-on-surface flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[20px]">assignment</span>
<span>Há»c pháº§n biÃªn soáº¡n</span>
</h2>
<span class="px-2 py-0.5 bg-surface-container text-primary font-bold text-xs rounded-full">4 mÃ´n</span>
</div>
<!-- NÃºt Táº¡o phiÃªn báº£n má»›i -->
<button class="w-full h-10 border border-primary text-primary hover:bg-primary/5 rounded-lg flex items-center justify-center gap-2 font-label-md text-label-md transition-all active:scale-[0.98]">
<span class="material-symbols-outlined text-[18px]">add_circle</span>
<span>+ Táº¡o phiÃªn báº£n Ä‘á» cÆ°Æ¡ng má»›i</span>
</button>
<!-- Ã” tÃ¬m kiáº¿m mÃ£/tÃªn -->
<div class="relative">
<span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-[18px] text-outline">filter_list</span>
<input aria-label="TÃ¬m theo mÃ£ hoáº·c tÃªn..." class="w-full h-9 pl-9 pr-3 rounded-lg border border-outline-variant bg-surface-container-low text-on-surface font-body-sm text-body-sm placeholder:text-outline focus:border-primary focus:bg-surface-container-lowest" placeholder="TÃ¬m theo mÃ£ hoáº·c tÃªn..." type="text"/>
</div>
<!-- Bá»™ lá»c tráº¡ng thÃ¡i tabs/chips -->
<div class="flex flex-wrap gap-1.5 pt-1">
<button class="px-2.5 py-1 rounded-full bg-primary text-white font-label-sm text-[11px] shadow-sm">Táº¥t cáº£ (4)</button>
<button class="px-2.5 py-1 rounded-full bg-surface-container-low hover:bg-surface-container text-secondary font-label-sm text-[11px] border border-outline-variant/60">ÄÃ£ duyá»‡t (2)</button>
<button class="px-2.5 py-1 rounded-full bg-surface-container-low hover:bg-surface-container text-secondary font-label-sm text-[11px] border border-outline-variant/60">Báº£n nhÃ¡p (1)</button>
<button class="px-2.5 py-1 rounded-full bg-surface-container-low hover:bg-surface-container text-secondary font-label-sm text-[11px] border border-outline-variant/60">Chá» duyá»‡t (1)</button>
</div>
</div>
<!-- Danh sÃ¡ch 4 tháº» há»c pháº§n -->
<div class="flex flex-col gap-3 overflow-y-auto pr-0.5">
<!-- THáºº 1: Láº­p trÃ¬nh Web -->
<div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant shadow-sm hover:border-primary/50 transition-all cursor-pointer">
<div class="flex items-start justify-between gap-2">
<span class="font-label-sm text-xs font-bold text-outline">71ITEC3201</span>
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                ÄÃ£ duyá»‡t
              </span>
</div>
<h3 class="font-headline-sm text-[14px] text-on-surface font-semibold mt-1">Láº­p trÃ¬nh Web</h3>
<div class="flex items-center gap-2 mt-2 text-secondary font-body-sm text-body-sm">
<span class="bg-surface-container-low px-1.5 py-0.5 rounded text-[11px]">3 TÃ­n chá»‰</span>
<span>â€¢</span>
<span class="text-[11px]">PhiÃªn báº£n: <strong class="text-on-surface">v2.1</strong></span>
</div>
<div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-outline text-[11px] font-body-sm">
<span>Cáº­p nháº­t: 15/06/2026</span>
<span class="material-symbols-outlined text-[16px] text-outline-variant">chevron_right</span>
</div>
</div>
<!-- THáºº 2 [ACTIVE / ÄANG CHá»ŒN]: CÃ´ng nghá»‡ pháº§n má»m -->
<div class="bg-blue-50/70 p-4 rounded-xl border-2 border-primary shadow-sm transition-all cursor-pointer relative overflow-hidden">
<div class="absolute left-0 top-0 bottom-0 w-1.5 bg-primary"></div>
<div class="flex items-start justify-between gap-2">
<div class="flex items-center gap-1.5">
<span class="font-label-sm text-xs font-bold text-primary">71ITEC3202</span>
<span class="bg-primary text-white text-[9px] font-bold px-1.5 py-0.2 rounded uppercase tracking-wider">Äang xem</span>
</div>
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-300">
<span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                Báº£n nhÃ¡p
              </span>
</div>
<h3 class="font-headline-sm text-[15px] text-primary font-bold mt-1.5 leading-snug">CÃ´ng nghá»‡ pháº§n má»m</h3>
<div class="flex items-center gap-2 mt-2 text-on-secondary-container font-body-sm text-body-sm">
<span class="bg-primary-fixed text-on-primary-fixed px-1.5 py-0.5 rounded font-semibold text-[11px]">3 TÃ­n chá»‰ (2 LT + 1 TH)</span>
<span>â€¢</span>
<span class="text-[11px]">PhiÃªn báº£n: <strong class="text-primary">v2.2</strong></span>
</div>
<div class="mt-2.5 pt-2 border-t border-primary/20 flex items-center justify-between text-secondary text-[11px] font-body-sm">
<span class="flex items-center gap-1">
<span class="material-symbols-outlined text-[14px] text-amber-700">update</span>
                Cáº­p nháº­t: 10/10/2026
              </span>
<span class="text-primary font-semibold flex items-center">
                Chi tiáº¿t <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
</span>
</div>
</div>
<!-- THáºº 3: Kiáº¿n trÃºc pháº§n má»m -->
<div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant shadow-sm hover:border-primary/50 transition-all cursor-pointer">
<div class="flex items-start justify-between gap-2">
<span class="font-label-sm text-xs font-bold text-outline">71ITEC4105</span>
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                ÄÃ£ duyá»‡t
              </span>
</div>
<h3 class="font-headline-sm text-[14px] text-on-surface font-semibold mt-1">Kiáº¿n trÃºc pháº§n má»m</h3>
<div class="flex items-center gap-2 mt-2 text-secondary font-body-sm text-body-sm">
<span class="bg-surface-container-low px-1.5 py-0.5 rounded text-[11px]">3 TÃ­n chá»‰</span>
<span>â€¢</span>
<span class="text-[11px]">PhiÃªn báº£n: <strong class="text-on-surface">v1.0</strong></span>
</div>
<div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-outline text-[11px] font-body-sm">
<span>Cáº­p nháº­t: 20/01/2026</span>
<span class="material-symbols-outlined text-[16px] text-outline-variant">chevron_right</span>
</div>
</div>
<!-- THáºº 4: DevOps vÃ  Äiá»‡n toÃ¡n Ä‘Ã¡m mÃ¢y -->
<div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant shadow-sm hover:border-primary/50 transition-all cursor-pointer">
<div class="flex items-start justify-between gap-2">
<span class="font-label-sm text-xs font-bold text-outline">71ITEC4208</span>
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-orange-50 text-orange-700 border border-orange-200">
<span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                Chá» duyá»‡t
              </span>
</div>
<h3 class="font-headline-sm text-[14px] text-on-surface font-semibold mt-1">DevOps vÃ  Äiá»‡n toÃ¡n Ä‘Ã¡m mÃ¢y</h3>
<div class="flex items-center gap-2 mt-2 text-secondary font-body-sm text-body-sm">
<span class="bg-surface-container-low px-1.5 py-0.5 rounded text-[11px]">3 TÃ­n chá»‰</span>
<span>â€¢</span>
<span class="text-[11px]">PhiÃªn báº£n: <strong class="text-on-surface">v1.1</strong></span>
</div>
<div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-outline text-[11px] font-body-sm">
<span>Cáº­p nháº­t: 05/10/2026</span>
<span class="material-symbols-outlined text-[16px] text-outline-variant">chevron_right</span>
</div>
</div>
</div>
</section>
<!-- ===================================================================== -->
<!-- Cá»˜T PHáº¢I (flex-1): Ná»˜I DUNG CHI TIáº¾T Äá»€ CÆ¯Æ NG Há»ŒC PHáº¦N                 -->
<!-- ===================================================================== -->
<section class="flex-1 flex flex-col gap-5 overflow-hidden">
<!-- CARD HEADER CHI TIáº¾T & ACTION BUTTONS -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-sm">
<div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 pb-4 border-b border-slate-100">
<div>
<div class="flex items-center gap-2 flex-wrap mb-1">
<span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-300 flex items-center gap-1">
<span class="w-2 h-2 rounded-full bg-amber-500"></span>
                  Báº£n nhÃ¡p - Äang biÃªn soáº¡n
                </span>
<span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-surface-container text-primary border border-outline-variant/50">
                  PhiÃªn báº£n: v2.2 (NiÃªn giÃ¡m 2026 - 2027)
                </span>
<span class="text-secondary font-body-sm text-body-sm">MÃ£ HP: <strong class="text-on-surface font-semibold">71ITEC3202</strong></span>
</div>
<h1 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">
                CÃ”NG NGHá»† PHáº¦N Má»€M <span class="text-secondary font-normal text-headline-sm">(SOFTWARE ENGINEERING)</span>
</h1>
<div class="flex flex-wrap items-center gap-y-1 gap-x-4 mt-2 text-secondary font-body-sm text-body-sm">
<span>Sá»‘ tÃ­n chá»‰: <strong class="text-on-surface font-semibold">3 TC (2 LT + 1 TH)</strong></span>
<span>â€¢</span>
<span>Khoa: <strong class="text-on-surface font-semibold">CÃ´ng nghá»‡ thÃ´ng tin</strong></span>
<span>â€¢</span>
<span>Bá»™ mÃ´n: <strong class="text-on-surface font-semibold">Ká»¹ thuáº­t pháº§n má»m</strong></span>
<span>â€¢</span>
<span>Giáº£ng viÃªn biÃªn soáº¡n: <strong class="text-on-surface font-semibold">TS. Nguyá»…n VÄƒn DÅ©ng</strong></span>
</div>
</div>
<!-- Cá»¤M NÃšT HÃ€NH Äá»˜NG TRÃŠN CÃ™NG -->
<div class="flex flex-wrap items-center gap-2">
<!-- NÃºt Ghost: Lá»‹ch sá»­ phiÃªn báº£n -->
<button class="h-9 px-3 rounded-lg text-secondary hover:bg-surface-container-low hover:text-on-surface border border-outline-variant font-label-md text-label-md flex items-center gap-1.5 transition-colors">
<span class="material-symbols-outlined text-[18px]">history</span>
<span>Lá»‹ch sá»­ (3)</span>
</button>
<!-- NÃºt Outline: Xuáº¥t PDF/Word -->
<button class="h-9 px-3 rounded-lg text-on-surface hover:bg-surface-container-low border border-outline-variant font-label-md text-label-md flex items-center gap-1.5 transition-colors">
<span class="material-symbols-outlined text-[18px]">download</span>
<span>Xuáº¥t PDF/Word</span>
</button>
<!-- NÃºt Secondary: Gá»­i TrÆ°á»Ÿng bá»™ mÃ´n phÃª duyá»‡t -->
<button class="h-9 px-3.5 rounded-lg bg-white border border-primary text-primary hover:bg-primary/5 font-label-md text-label-md flex items-center gap-1.5 transition-all shadow-sm">
<span class="material-symbols-outlined text-[18px]">send</span>
<span>Gá»­i duyá»‡t</span>
</button>
<!-- NÃºt Primary: Chá»‰nh sá»­a Ä‘á» cÆ°Æ¡ng -->
<button class="h-9 px-4 rounded-lg bg-primary hover:bg-[#15429e] text-white font-label-md text-label-md flex items-center gap-1.5 shadow transition-all active:scale-95">
<span class="material-symbols-outlined text-[18px]">edit</span>
<span>Chá»‰nh sá»­a</span>
</button>
</div>
</div>
<!-- BANNER Cáº¢NH BÃO Báº¢N NHÃP -->
<div class="mt-4 p-3.5 bg-amber-50/80 border border-amber-300 rounded-lg flex items-start gap-3">
<span class="material-symbols-outlined text-amber-700 text-[22px] flex-shrink-0 mt-0.5">warning</span>
<div class="text-xs font-body-sm leading-relaxed text-amber-950">
<strong class="font-semibold text-amber-900">LÆ°u Ã½ há»c vá»¥:</strong> Báº¡n Ä‘ang thao tÃ¡c trÃªn <strong>PhiÃªn báº£n nhÃ¡p v2.2</strong>. Sau khi chá»‰nh sá»­a hoÃ n táº¥t, hÃ£y báº¥m <strong>"Gá»­i TrÆ°á»Ÿng bá»™ mÃ´n phÃª duyá»‡t"</strong> Ä‘á»ƒ tháº©m Ä‘á»‹nh chuyÃªn mÃ´n trÆ°á»›c khi cÃ´ng bá»‘ Ã¡p dá»¥ng chÃ­nh thá»©c cho sinh viÃªn HK1 (2026 - 2027).
            </div>
</div>
</div>
<!-- =================================================================== -->
<!-- KHá»I 5 PHáº¦N Cáº¤U TRÃšC Äá»€ CÆ¯Æ NG CHI TIáº¾T HUIT                        -->
<!-- =================================================================== -->
<div class="space-y-5 pb-8">
<!-- ----------------------------------------------------------------- -->
<!-- PHáº¦N 1: Má»¤C TIÃŠU Há»ŒC PHáº¦N (COURSE OBJECTIVES)                     -->
<!-- ----------------------------------------------------------------- -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-sm">
<div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
<h3 class="font-headline-sm text-headline-sm text-on-surface flex items-center gap-2">
<span class="w-6 h-6 rounded bg-primary/10 text-primary flex items-center justify-center font-bold text-xs">1</span>
<span>Má»¥c tiÃªu há»c pháº§n (Course Objectives)</span>
</h3>
<span class="text-secondary font-body-sm text-xs">4 Má»¥c tiÃªu Ä‘Ã o táº¡o</span>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
<!-- CO1 -->
<div class="p-3.5 rounded-lg border border-outline-variant bg-surface-container-low/30 hover:border-primary/40 transition-colors">
<div class="flex items-center gap-2 mb-1.5">
<span class="px-2 py-0.5 rounded bg-primary text-white font-label-sm text-[11px] font-bold">CO1</span>
<span class="font-label-md text-label-md font-semibold text-on-surface">Kiáº¿n thá»©c ná»n táº£ng &amp; Quy trÃ¬nh</span>
</div>
<p class="font-body-md text-[13px] text-on-surface-variant leading-relaxed">
                  Náº¯m vá»¯ng cÃ¡c mÃ´ hÃ¬nh quy trÃ¬nh phÃ¡t triá»ƒn pháº§n má»m hiá»‡n Ä‘áº¡i (Agile, Scrum, Waterfall) vÃ  nguyÃªn lÃ½ quáº£n lÃ½ vÃ²ng Ä‘á»i sáº£n pháº©m (SDLC).
                </p>
</div>
<!-- CO2 -->
<div class="p-3.5 rounded-lg border border-outline-variant bg-surface-container-low/30 hover:border-primary/40 transition-colors">
<div class="flex items-center gap-2 mb-1.5">
<span class="px-2 py-0.5 rounded bg-primary text-white font-label-sm text-[11px] font-bold">CO2</span>
<span class="font-label-md text-label-md font-semibold text-on-surface">Ká»¹ nghá»‡ yÃªu cáº§u &amp; Äáº·c táº£</span>
</div>
<p class="font-body-md text-[13px] text-on-surface-variant leading-relaxed">
                  PhÃ¢n tÃ­ch yÃªu cáº§u nghiá»‡p vá»¥ thá»±c táº¿, mÃ´ hÃ¬nh hÃ³a luá»“ng dá»¯ liá»‡u há»‡ thá»‘ng báº±ng ngÃ´n ngá»¯ UML chuáº©n vÃ  xÃ¢y dá»±ng tÃ i liá»‡u Ä‘áº·c táº£ yÃªu cáº§u SRS.
                </p>
</div>
<!-- CO3 -->
<div class="p-3.5 rounded-lg border border-outline-variant bg-surface-container-low/30 hover:border-primary/40 transition-colors">
<div class="flex items-center gap-2 mb-1.5">
<span class="px-2 py-0.5 rounded bg-primary text-white font-label-sm text-[11px] font-bold">CO3</span>
<span class="font-label-md text-label-md font-semibold text-on-surface">Thiáº¿t káº¿ há»‡ thá»‘ng &amp; Kiá»ƒm thá»­</span>
</div>
<p class="font-body-md text-[13px] text-on-surface-variant leading-relaxed">
                  Thiáº¿t káº¿ kiáº¿n trÃºc pháº§n má»m, Ã¡p dá»¥ng máº«u thiáº¿t káº¿ (Design Patterns) phá»• biáº¿n, láº­p trÃ¬nh hÆ°á»›ng Ä‘á»‘i tÆ°á»£ng vÃ  thá»±c thi kiá»ƒm thá»­ pháº§n má»m tá»± Ä‘á»™ng (Unit Test).
                </p>
</div>
<!-- CO4 -->
<div class="p-3.5 rounded-lg border border-outline-variant bg-surface-container-low/30 hover:border-primary/40 transition-colors">
<div class="flex items-center gap-2 mb-1.5">
<span class="px-2 py-0.5 rounded bg-primary text-white font-label-sm text-[11px] font-bold">CO4</span>
<span class="font-label-md text-label-md font-semibold text-on-surface">Ká»¹ nÄƒng má»m &amp; Äáº¡o Ä‘á»©c nghá» nghiá»‡p</span>
</div>
<p class="font-body-md text-[13px] text-on-surface-variant leading-relaxed">
                  Ká»¹ nÄƒng lÃ m viá»‡c nhÃ³m hiá»‡u quáº£, giao tiáº¿p vÄƒn báº£n ká»¹ thuáº­t, thuyáº¿t trÃ¬nh giáº£i phÃ¡p vÃ  nÄƒng lá»±c quáº£n lÃ½ dá»± Ã¡n cÃ´ng nghá»‡ theo chuáº©n tiáº¿n Ä‘á»™ Agile.
                </p>
</div>
</div>
</div>
<!-- ----------------------------------------------------------------- -->
<!-- PHáº¦N 2: CHUáº¨N Äáº¦U RA Há»ŒC PHáº¦N (CLOs) & MA TRáº¬N ÃNH Xáº              -->
<!-- ----------------------------------------------------------------- -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-sm">
<div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
<h3 class="font-headline-sm text-headline-sm text-on-surface flex items-center gap-2">
<span class="w-6 h-6 rounded bg-primary/10 text-primary flex items-center justify-center font-bold text-xs">2</span>
<span>Chuáº©n Ä‘áº§u ra há»c pháº§n (CLOs) &amp; Ma tráº­n Ã¡nh xáº¡ PLOs</span>
</h3>
<div class="flex items-center gap-2">
<span class="text-xs bg-emerald-50 text-emerald-800 border border-emerald-200 px-2 py-0.5 rounded font-medium">Chuáº©n ABET / CDIO</span>
</div>
</div>
<!-- Báº£ng CLOs -->
<div class="overflow-x-auto rounded-lg border border-outline-variant custom-scrollbar">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-slate-50 border-b border-outline-variant text-[12px] font-semibold text-secondary uppercase font-label-md">
<th class="py-3 px-3.5 w-20 text-center">MÃ£ CLO</th>
<th class="py-3 px-4 min-w-[280px]">MÃ´ táº£ Chuáº©n Ä‘áº§u ra (Course Learning Outcomes)</th>
<th class="py-3 px-3 text-center w-28">Ãnh xáº¡ PLO</th>
<th class="py-3 px-3 text-center w-28">Má»©c Bloom</th>
<th class="py-3 px-4 min-w-[180px]">PhÆ°Æ¡ng phÃ¡p giáº£ng dáº¡y</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 text-[13px] font-body-md text-on-surface">
<tr class="hover:bg-slate-50/70 transition-colors">
<td class="py-3 px-3.5 text-center font-bold text-primary font-label-md">CLO1</td>
<td class="py-3 px-4">Giáº£i thÃ­ch Ä‘Æ°á»£c cÃ¡c nguyÃªn lÃ½ ká»¹ nghá»‡ pháº§n má»m vÃ  so sÃ¡nh Æ°u nhÆ°á»£c Ä‘iá»ƒm cá»§a cÃ¡c mÃ´ hÃ¬nh phÃ¡t triá»ƒn pháº§n má»m.</td>
<td class="py-3 px-3 text-center">
<span class="px-2 py-0.5 bg-blue-50 text-primary font-bold text-xs rounded border border-blue-200">PLO1, PLO2</span>
</td>
<td class="py-3 px-3 text-center">
<span class="px-2 py-0.5 bg-amber-50 text-amber-800 font-semibold text-xs rounded border border-amber-200">K3 (Apply)</span>
</td>
<td class="py-3 px-4 text-secondary">Thuyáº¿t giáº£ng, Tháº£o luáº­n nhÃ³m tÃ¬nh huá»‘ng</td>
</tr>
<tr class="hover:bg-slate-50/70 transition-colors">
<td class="py-3 px-3.5 text-center font-bold text-primary font-label-md">CLO2</td>
<td class="py-3 px-4">Thu tháº­p, phÃ¢n tÃ­ch vÃ  mÃ´ hÃ¬nh hÃ³a yÃªu cáº§u há»‡ thá»‘ng báº±ng chuáº©n biá»ƒu Ä‘á»“ Use Case, Activity, Class diagram.</td>
<td class="py-3 px-3 text-center">
<span class="px-2 py-0.5 bg-blue-50 text-primary font-bold text-xs rounded border border-blue-200">PLO3</span>
</td>
<td class="py-3 px-3 text-center">
<span class="px-2 py-0.5 bg-purple-50 text-purple-800 font-semibold text-xs rounded border border-purple-200">K4 (Analyze)</span>
</td>
<td class="py-3 px-4 text-secondary">Thá»±c hÃ nh Case study, HÆ°á»›ng dáº«n Ä‘á»“ Ã¡n</td>
</tr>
<tr class="hover:bg-slate-50/70 transition-colors">
<td class="py-3 px-3.5 text-center font-bold text-primary font-label-md">CLO3</td>
<td class="py-3 px-4">Ãp dá»¥ng kiáº¿n trÃºc Model-View-Controller (MVC) vÃ  viáº¿t mÃ£ kiá»ƒm thá»­ Ä‘Æ¡n vá»‹ (Unit Test) cho má»™t mÃ´ Ä‘un nghiá»‡p vá»¥.</td>
<td class="py-3 px-3 text-center">
<span class="px-2 py-0.5 bg-blue-50 text-primary font-bold text-xs rounded border border-blue-200">PLO3, PLO5</span>
</td>
<td class="py-3 px-3 text-center">
<span class="px-2 py-0.5 bg-purple-50 text-purple-800 font-semibold text-xs rounded border border-purple-200">K4 (Analyze)</span>
</td>
<td class="py-3 px-4 text-secondary">Láº­p trÃ¬nh thá»±c hÃ nh táº¡i phÃ²ng Lab</td>
</tr>
<tr class="hover:bg-slate-50/70 transition-colors">
<td class="py-3 px-3.5 text-center font-bold text-primary font-label-md">CLO4</td>
<td class="py-3 px-4">Phá»‘i há»£p nhÃ³m xÃ¢y dá»±ng Ä‘á»“ Ã¡n há»c pháº§n, sá»­ dá»¥ng cÃ´ng cá»¥ Git/GitHub vÃ  thuyáº¿t trÃ¬nh báº£o vá»‡ sáº£n pháº©m trÆ°á»›c lá»›p.</td>
<td class="py-3 px-3 text-center">
<span class="px-2 py-0.5 bg-blue-50 text-primary font-bold text-xs rounded border border-blue-200">PLO7, PLO9</span>
</td>
<td class="py-3 px-3 text-center">
<span class="px-2 py-0.5 bg-amber-50 text-amber-800 font-semibold text-xs rounded border border-amber-200">K3 (Apply)</span>
</td>
<td class="py-3 px-4 text-secondary">LÃ m viá»‡c nhÃ³m theo Sprint Scrum</td>
</tr>
</tbody>
</table>
</div>
</div>
<!-- ----------------------------------------------------------------- -->
<!-- PHáº¦N 3: Káº¾ HOáº CH GIáº¢NG Dáº Y CHI TIáº¾T 15 TUáº¦N                       -->
<!-- ----------------------------------------------------------------- -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-sm">
<div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
<h3 class="font-headline-sm text-headline-sm text-on-surface flex items-center gap-2">
<span class="w-6 h-6 rounded bg-primary/10 text-primary flex items-center justify-center font-bold text-xs">3</span>
<span>Káº¿ hoáº¡ch giáº£ng dáº¡y chi tiáº¿t (15 Tuáº§n chuáº©n ÄÃ o táº¡o HUIT)</span>
</h3>
<div class="flex items-center gap-3 text-xs text-secondary font-body-sm">
<span>Tá»•ng: <strong class="text-on-surface">30 tiáº¿t LT</strong></span>
<span>â€¢</span>
<span><strong class="text-on-surface">30 tiáº¿t TH</strong></span>
</div>
</div>
<div class="overflow-x-auto rounded-lg border border-outline-variant custom-scrollbar">
<table class="w-full text-left border-collapse text-[13px]">
<thead>
<tr class="bg-slate-50 border-b border-outline-variant text-[12px] font-semibold text-secondary uppercase font-label-md">
<th class="py-3 px-3.5 w-16 text-center">Tuáº§n</th>
<th class="py-3 px-4 min-w-[260px]">Ná»™i dung bÃ i há»c / Hoáº¡t Ä‘á»™ng giáº£ng dáº¡y</th>
<th class="py-3 px-3 text-center w-20">LT/TH</th>
<th class="py-3 px-3 text-center w-24">CLOs</th>
<th class="py-3 px-4 min-w-[220px]">Hoáº¡t Ä‘á»™ng há»c táº­p &amp; ÄÃ¡nh giÃ¡</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 font-body-md text-on-surface">
<!-- Má»‘c Tuáº§n 1 -->
<tr class="hover:bg-slate-50 transition-colors">
<td class="py-3 px-3.5 text-center font-bold text-primary font-label-md bg-blue-50/40">Tuáº§n 1</td>
<td class="py-3 px-4">
<div class="font-semibold text-on-surface">Giá»›i thiá»‡u Tá»•ng quan CÃ´ng nghá»‡ pháº§n má»m</div>
<div class="text-xs text-secondary mt-0.5">VÃ²ng Ä‘á»i pháº§n má»m (SDLC), mÃ´ hÃ¬nh Waterfall, Agile Manifesto vÃ  khung lÃ m viá»‡c Scrum.</div>
</td>
<td class="py-3 px-3 text-center font-semibold text-secondary text-xs">2 LT / 0 TH</td>
<td class="py-3 px-3 text-center"><span class="px-1.5 py-0.5 bg-slate-100 rounded text-xs font-semibold">CLO1</span></td>
<td class="py-3 px-4 text-secondary text-xs">Nghe giáº£ng, thÃ nh láº­p nhÃ³m Ä‘á»“ Ã¡n (4-5 SV/nhÃ³m), chá»n Ä‘á» tÃ i.</td>
</tr>
<!-- Má»‘c Tuáº§n 4 -->
<tr class="hover:bg-slate-50 transition-colors">
<td class="py-3 px-3.5 text-center font-bold text-primary font-label-md bg-blue-50/40">Tuáº§n 4</td>
<td class="py-3 px-4">
<div class="font-semibold text-on-surface">Ká»¹ nghá»‡ yÃªu cáº§u &amp; Äáº·c táº£ pháº§n má»m (SRS)</div>
<div class="text-xs text-secondary mt-0.5">Ká»¹ thuáº­t thu tháº­p yÃªu cáº§u, viáº¿t User Story theo máº«u INVEST, phÃ¢n biá»‡t chá»©c nÄƒng vÃ  phi chá»©c nÄƒng.</div>
</td>
<td class="py-3 px-3 text-center font-semibold text-secondary text-xs">2 LT / 2 TH</td>
<td class="py-3 px-3 text-center"><span class="px-1.5 py-0.5 bg-slate-100 rounded text-xs font-semibold">CLO2</span></td>
<td class="py-3 px-4 text-secondary text-xs">Thá»±c hÃ nh viáº¿t tÃ i liá»‡u SRS cho Ä‘á»“ Ã¡n nhÃ³m; Ná»™p báº£n phÃ¡c tháº£o Sprint 1.</td>
</tr>
<!-- Má»‘c Tuáº§n 8 -->
<tr class="hover:bg-slate-50 transition-colors">
<td class="py-3 px-3.5 text-center font-bold text-primary font-label-md bg-blue-50/40">Tuáº§n 8</td>
<td class="py-3 px-4">
<div class="font-semibold text-on-surface">Thiáº¿t káº¿ kiáº¿n trÃºc, Thiáº¿t káº¿ chi tiáº¿t &amp; MÃ´ hÃ¬nh hÃ³a UML</div>
<div class="text-xs text-secondary mt-0.5">Thiáº¿t káº¿ CSDL quan há»‡, thiáº¿t káº¿ giao diá»‡n (UI/UX wireframe), Ã¡p dá»¥ng mÃ´ hÃ¬nh phÃ¢n lá»›p.</div>
</td>
<td class="py-3 px-3 text-center font-semibold text-secondary text-xs">2 LT / 2 TH</td>
<td class="py-3 px-3 text-center"><span class="px-1.5 py-0.5 bg-slate-100 rounded text-xs font-semibold">CLO2, CLO3</span></td>
<td class="py-3 px-4 text-secondary text-xs">BÃ¡o cÃ¡o tiáº¿n Ä‘á»™ Giá»¯a ká»³ (Checkpoint 1); ÄÃ¡nh giÃ¡ sÆ¡ bá»™ Ä‘iá»ƒm nhÃ³m.</td>
</tr>
<!-- Má»‘c Tuáº§n 11 -->
<tr class="hover:bg-slate-50 transition-colors">
<td class="py-3 px-3.5 text-center font-bold text-primary font-label-md bg-blue-50/40">Tuáº§n 11</td>
<td class="py-3 px-4">
<div class="font-semibold text-on-surface">Kiá»ƒm thá»­ pháº§n má»m &amp; Äáº£m báº£o cháº¥t lÆ°á»£ng (QA/QC)</div>
<div class="text-xs text-secondary mt-0.5">NguyÃªn lÃ½ kiá»ƒm thá»­ White-box vÃ  Black-box, viáº¿t Test Cases, kiá»ƒm thá»­ tá»± Ä‘á»™ng vá»›i JUnit/Jest.</div>
</td>
<td class="py-3 px-3 text-center font-semibold text-secondary text-xs">2 LT / 2 TH</td>
<td class="py-3 px-3 text-center"><span class="px-1.5 py-0.5 bg-slate-100 rounded text-xs font-semibold">CLO3</span></td>
<td class="py-3 px-4 text-secondary text-xs">Thá»±c hÃ nh viáº¿t Unit Test táº¡i phÃ²ng mÃ¡y; Ghi nháº­n tá»· lá»‡ bao phá»§ mÃ£ (Code Coverage).</td>
</tr>
<!-- Má»‘c Tuáº§n 15 -->
<tr class="hover:bg-slate-50 transition-colors bg-blue-50/20">
<td class="py-3 px-3.5 text-center font-bold text-primary font-label-md bg-blue-50/60">Tuáº§n 15</td>
<td class="py-3 px-4">
<div class="font-semibold text-on-surface">BÃ¡o cÃ¡o Ä‘á»“ Ã¡n há»c pháº§n &amp; ÄÃ¡nh giÃ¡ tá»•ng káº¿t</div>
<div class="text-xs text-secondary mt-0.5">Demo sáº£n pháº©m pháº§n má»m hoÃ n chá»‰nh, báº£o vá»‡ giáº£i phÃ¡p kiáº¿n trÃºc vÃ  tráº£ lá»i cÃ¢u há»i váº¥n Ä‘Ã¡p.</div>
</td>
<td class="py-3 px-3 text-center font-semibold text-secondary text-xs">0 LT / 4 TH</td>
<td class="py-3 px-3 text-center"><span class="px-1.5 py-0.5 bg-blue-100 text-primary font-bold rounded text-xs">CLO1-4</span></td>
<td class="py-3 px-4 text-primary font-medium text-xs">Váº¥n Ä‘Ã¡p há»™i Ä‘á»“ng giáº£ng viÃªn; Cháº¥m Ä‘iá»ƒm Äá»“ Ã¡n káº¿t thÃºc há»c pháº§n.</td>
</tr>
</tbody>
</table>
</div>
<div class="mt-3 flex items-center justify-between text-xs text-secondary">
<span>* Hiá»ƒn thá»‹ cÃ¡c tuáº§n má»‘c trá»ng yáº¿u. Báº¥m Ä‘á»ƒ xem toÃ n bá»™ 15 tuáº§n chi tiáº¿t.</span>
<button class="text-primary hover:underline font-semibold flex items-center gap-1">
<span>Xem chi tiáº¿t Ä‘áº§y Ä‘á»§ 15 tuáº§n</span>
<span class="material-symbols-outlined text-[16px]">expand_more</span>
</button>
</div>
</div>
<!-- ----------------------------------------------------------------- -->
<!-- PHáº¦N 4: PHÆ¯Æ NG PHÃP ÄÃNH GIÃ & TRá»ŒNG Sá» ÄIá»‚M                      -->
<!-- ----------------------------------------------------------------- -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-sm">
<div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
<h3 class="font-headline-sm text-headline-sm text-on-surface flex items-center gap-2">
<span class="w-6 h-6 rounded bg-primary/10 text-primary flex items-center justify-center font-bold text-xs">4</span>
<span>PhÆ°Æ¡ng phÃ¡p Ä‘Ã¡nh giÃ¡ &amp; Trá»ng sá»‘ Ä‘iá»ƒm (Chuáº©n HUIT 100%)</span>
</h3>
<span class="text-xs bg-primary/10 text-primary px-2.5 py-0.5 rounded-full font-bold">Thang Ä‘iá»ƒm 10</span>
</div>
<div class="overflow-hidden rounded-lg border border-outline-variant">
<table class="w-full text-left border-collapse text-[13px]">
<thead>
<tr class="bg-slate-50 border-b border-outline-variant text-[12px] font-semibold text-secondary uppercase font-label-md">
<th class="py-3 px-4">ThÃ nh pháº§n Ä‘Ã¡nh giÃ¡</th>
<th class="py-3 px-4">HÃ¬nh thá»©c / CÃ´ng cá»¥ Ä‘Ã¡nh giÃ¡</th>
<th class="py-3 px-3 text-center w-28">CLOs Ä‘Ã¡nh giÃ¡</th>
<th class="py-3 px-4 text-center w-28">Trá»ng sá»‘</th>
<th class="py-3 px-4 text-right w-36">TiÃªu chÃ­ Ä‘áº¡t</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 font-body-md text-on-surface">
<!-- ThÃ nh pháº§n 1 -->
<tr class="hover:bg-slate-50/70 transition-colors">
<td class="py-3 px-4">
<div class="font-semibold">QuÃ¡ trÃ¬nh há»c táº­p &amp; Ã thá»©c há»c táº­p</div>
<div class="text-xs text-secondary">ÄÃ¡nh giÃ¡ quÃ¡ trÃ¬nh táº¡i lá»›p</div>
</td>
<td class="py-3 px-4 text-secondary">Äiá»ƒm quÃ¡ trÃ¬nh qua Cá»•ng SV, phÃ¡t biá»ƒu xÃ¢y dá»±ng bÃ i, tháº£o luáº­n nhÃ³m</td>
<td class="py-3 px-3 text-center">
<span class="px-1.5 py-0.5 bg-slate-100 text-on-surface font-semibold text-xs rounded">CLO4</span>
</td>
<td class="py-3 px-4 text-center font-bold text-primary font-label-md">10%</td>
<td class="py-3 px-4 text-right text-xs text-secondary">Tham gia â‰¥ 80% sá»‘ tiáº¿t</td>
</tr>
<!-- ThÃ nh pháº§n 2 -->
<tr class="hover:bg-slate-50/70 transition-colors">
<td class="py-3 px-4">
<div class="font-semibold">ÄÃ¡nh giÃ¡ giá»¯a ká»³ / BÃ i táº­p lá»›n nhÃ³m</div>
<div class="text-xs text-secondary">Äá»“ Ã¡n thá»±c hÃ nh theo Sprint</div>
</td>
<td class="py-3 px-4 text-secondary">BÃ¡o cÃ¡o Sprint 1 &amp; 2, ná»™p tÃ i liá»‡u SRS + Thiáº¿t káº¿ kiáº¿n trÃºc UML, mÃ£ nguá»“n Git</td>
<td class="py-3 px-3 text-center">
<span class="px-1.5 py-0.5 bg-slate-100 text-on-surface font-semibold text-xs rounded">CLO1, CLO2</span>
</td>
<td class="py-3 px-4 text-center font-bold text-primary font-label-md">30%</td>
<td class="py-3 px-4 text-right text-xs text-secondary">Äiá»ƒm thÃ nh pháº§n â‰¥ 5.0</td>
</tr>
<!-- ThÃ nh pháº§n 3 -->
<tr class="hover:bg-slate-50/70 transition-colors">
<td class="py-3 px-4">
<div class="font-semibold">Thi káº¿t thÃºc há»c pháº§n</div>
<div class="text-xs text-secondary">ÄÃ¡nh giÃ¡ chung cuá»™c</div>
</td>
<td class="py-3 px-4 text-secondary">BÃ¡o cÃ¡o sáº£n pháº©m pháº§n má»m hoÃ n chá»‰nh &amp; Váº¥n Ä‘Ã¡p trá»±c tiáº¿p trÆ°á»›c giáº£ng viÃªn</td>
<td class="py-3 px-3 text-center">
<span class="px-1.5 py-0.5 bg-slate-100 text-on-surface font-semibold text-xs rounded">CLO1 - CLO4</span>
</td>
<td class="py-3 px-4 text-center font-bold text-primary font-label-md">60%</td>
<td class="py-3 px-4 text-right text-xs text-secondary">Äiá»ƒm thi â‰¥ 4.0 (chuáº©n HUIT)</td>
</tr>
<!-- DÃ’NG Tá»”NG Káº¾T Ná»”I Báº¬T -->
<tr class="bg-primary/5 font-semibold font-label-md border-t-2 border-primary/20">
<td class="py-3 px-4 text-primary font-bold" colspan="3">
                      Tá»”NG Cá»˜NG TRá»ŒNG Sá» ÄÃNH GIÃ Há»ŒC PHáº¦N
                    </td>
<td class="py-3 px-4 text-center text-primary font-extrabold text-[15px]">
                      100%
                    </td>
<td class="py-3 px-4 text-right text-xs text-primary font-bold">
                      Chuáº©n Ä‘áº¡t: Äiá»ƒm HP â‰¥ 5.0
                    </td>
</tr>
</tbody>
</table>
</div>
</div>
<!-- ----------------------------------------------------------------- -->
<!-- PHáº¦N 5: GIÃO TRÃŒNH & TÃ€I LIá»†U Há»ŒC Táº¬P                             -->
<!-- ----------------------------------------------------------------- -->
<div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-sm">
<div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
<h3 class="font-headline-sm text-headline-sm text-on-surface flex items-center gap-2">
<span class="w-6 h-6 rounded bg-primary/10 text-primary flex items-center justify-center font-bold text-xs">5</span>
<span>GiÃ¡o trÃ¬nh &amp; TÃ i liá»‡u há»c táº­p (Textbooks &amp; References)</span>
</h3>
<span class="text-secondary font-body-sm text-xs">ThÆ° viá»‡n HUIT sá»‘ hÃ³a</span>
</div>
<div class="space-y-3">
<!-- GiÃ¡o trÃ¬nh chÃ­nh -->
<div class="p-4 rounded-lg border border-primary/30 bg-blue-50/40 flex items-start gap-3">
<div class="w-9 h-9 rounded-lg bg-primary text-white flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined text-[20px]">menu_book</span>
</div>
<div class="flex-1">
<div class="flex items-center gap-2">
<span class="px-2 py-0.5 rounded bg-primary text-white font-label-sm text-[10px] font-bold uppercase tracking-wider">GiÃ¡o trÃ¬nh chÃ­nh</span>
<span class="text-xs text-secondary font-mono">ISBN: 978-0133943030</span>
</div>
<h4 class="font-headline-sm text-[14px] text-on-surface font-bold mt-1">
                    Ian Sommerville (2020), Software Engineering (10th Edition), Pearson Education.
                  </h4>
<p class="font-body-sm text-xs text-secondary mt-1">
                    Vá»‹ trÃ­ táº¡i Trung tÃ¢m ThÃ´ng tin - ThÆ° viá»‡n HUIT: MÃ£ xáº¿p giÃ¡ <strong class="text-on-surface">005.1 SOM-I</strong> (SÃ¡ch in &amp; E-book báº£n quyá»n).
                  </p>
</div>
</div>
<!-- TÃ i liá»‡u tham kháº£o 1 & 2 -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1">
<!-- TLTK 1 -->
<div class="p-3.5 rounded-lg border border-outline-variant bg-surface-container-low/20">
<div class="flex items-center gap-1.5 text-xs text-outline mb-1 font-semibold">
<span class="material-symbols-outlined text-[16px] text-secondary">auto_stories</span>
<span>TÃ i liá»‡u tham kháº£o 1</span>
</div>
<div class="font-body-md text-xs font-semibold text-on-surface">
                    Roger S. Pressman, Bruce R. Maxim (2019)
                  </div>
<p class="text-xs text-secondary mt-0.5">
<em>Software Engineering: A Practitioner's Approach (9th Edition)</em>, McGraw-Hill Education.
                  </p>
</div>
<!-- TLTK 2 -->
<div class="p-3.5 rounded-lg border border-outline-variant bg-surface-container-low/20">
<div class="flex items-center gap-1.5 text-xs text-outline mb-1 font-semibold">
<span class="material-symbols-outlined text-[16px] text-secondary">integration_instructions</span>
<span>TÃ i liá»‡u thá»±c hÃ nh ná»™i bá»™</span>
</div>
<div class="font-body-md text-xs font-semibold text-on-surface">
                    Bá»™ mÃ´n CÃ´ng nghá»‡ pháº§n má»m - Khoa CNTT HUIT (2024)
                  </div>
<p class="text-xs text-secondary mt-0.5">
<em>Táº­p bÃ i giáº£ng vÃ  HÆ°á»›ng dáº«n thá»±c hÃ nh CÃ´ng nghá»‡ pháº§n má»m trÃªn ná»n táº£ng GitHub &amp; Jira</em>, LÆ°u hÃ nh ná»™i bá»™ ÄH CÃ´ng ThÆ°Æ¡ng.
                  </p>
</div>
</div>
</div>
</div>
</div>
</section>
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

