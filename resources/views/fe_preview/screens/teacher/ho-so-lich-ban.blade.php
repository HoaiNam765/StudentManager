@extends('fe_preview.layouts.teacher')
@section('page_title', 'Há»“ sÆ¡ cÃ¡ nhÃ¢n vÃ  ÄÄƒng kÃ½ lá»‹ch báº­n - HUIT Há»c Vá»¥')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Há»“ sÆ¡ cÃ¡ nhÃ¢n vÃ  ÄÄƒng kÃ½ lá»‹ch báº­n - HUIT Há»c Vá»¥</title>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/ho-so-lich-ban']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">event_busy</span>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Há»“ sÆ¡ cÃ¡ nhÃ¢n vÃ  ÄÄƒng kÃ½ lá»‹ch báº­n</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Há»c ká»³ 1 â€¢ 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chá»§ / Há»“ sÆ¡ cÃ¡ nhÃ¢n vÃ  ÄÄƒng kÃ½ lá»‹ch báº­n</div>
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
<div class="max-w-[1400px] mx-auto space-y-5">
<!-- 1. TAB ÄIá»€U HÆ¯á»šNG TRANG CHÃNH -->
<div class="flex items-center justify-between border-b border-outline-variant/60 pb-1">
<div class="flex items-center gap-8">
<button class="pb-3 px-1 font-headline-sm text-headline-sm font-medium text-on-surface-variant hover:text-on-surface transition-colors flex items-center gap-2">
<span class="material-symbols-outlined text-[20px]">badge</span>
<span>Há»“ sÆ¡ giáº£ng viÃªn</span>
</button>
<button class="pb-3 px-1 font-headline-sm text-headline-sm font-bold text-primary border-b-2 border-primary transition-colors flex items-center gap-2.5">
<span class="material-symbols-outlined text-[20px]">edit_calendar</span>
<span>ÄÄƒng kÃ½ lá»‹ch báº­n giáº£ng dáº¡y</span>
<span class="bg-emerald-100 text-emerald-800 text-label-sm font-semibold px-2 py-0.5 rounded-full border border-emerald-300">
                Äang má»Ÿ Ä‘á»£t
              </span>
</button>
</div>
<div class="flex items-center gap-2 text-on-surface-variant text-body-sm font-body-sm pb-2">
<span class="material-symbols-outlined text-[18px] text-primary">verified</span>
<span>Quy cháº¿ Ä‘Ã o táº¡o theo há»c cháº¿ tÃ­n chá»‰ HUIT v4.2</span>
</div>
</div>
<!-- 2. THáºº TÃ“M Táº®T Há»’ SÆ  GIáº¢NG VIÃŠN (COLLAPSIBLE PROFILE SUMMARY CARD) -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant/80 p-5 shadow-sm transition-all duration-200" id="profile-card">
<div class="flex items-start justify-between">
<div class="flex items-start gap-5">
<!-- Avatar with Teacher Badge -->
<div class="relative flex-shrink-0">
<div class="w-20 h-20 rounded-xl bg-surface-container border border-outline-variant flex items-center justify-center overflow-hidden shadow-inner">
<img alt="" class="w-full h-full object-cover" data-alt="Professional portrait photograph of a Vietnamese male university senior lecturer wearing glasses and a formal navy blue blazer with white collared shirt in a bright modern academic university office setting, soft natural lighting, high key corporate education aesthetic." src="https://lh3.googleusercontent.com/aida-public/AB6AXuDUKb4sf4D_b1RXo1MEwSWW6zmfgAgbR3gp8XSR2Q6cTa2f-ExLa3tfhsdNn162WAJokgF4H-ZqddsQsXpJd8-WP1qFcEa6wGndPdYtg1s4dpXkJpsJSP7ZcmnkwvxmM_jBNYHHRHR-9vsw3dz0b786PPkeleAq0TKUhctCYwS2cyKiU2ek70JATYTr8PkoHeUTSWnSABN_4MRMTo_y9cmpisGl3qD0ndQdIjwVwAyMRbrWJ3rbsVWLGB9OtyKN22bK5-7zQyHtacY"/>
</div>
<div class="absolute -bottom-2 -right-1 bg-primary text-white text-[10px] font-bold px-1.5 py-0.5 rounded-md flex items-center shadow-xs" title="Giáº£ng viÃªn cÆ¡ há»¯u HUIT">
<span class="material-symbols-outlined text-[12px] mr-0.5">verified_user</span>GV
                </div>
</div>
<!-- General Info Grid -->
<div class="space-y-1.5">
<div class="flex items-center gap-3 flex-wrap">
<h1 class="font-headline-md text-headline-md font-bold text-on-surface">TS. NGUYá»„N VÄ‚N DÅ¨NG</h1>
<span class="font-label-md text-label-md bg-surface-container px-2.5 py-0.5 rounded-md text-on-surface border border-outline-variant">MÃ£ GV: GV-2018042</span>
<span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 text-label-sm px-2.5 py-0.5 rounded-full border border-emerald-200 font-semibold">
<span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Äang cÃ´ng tÃ¡c (BiÃªn cháº¿)
                  </span>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-1 text-body-md font-body-md text-on-surface-variant">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[16px] text-outline">school</span>
<span><strong>Há»c hÃ m / Há»c vá»‹:</strong> Tiáº¿n sÄ© (2018) - Giáº£ng viÃªn chÃ­nh</span>
</div>
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[16px] text-outline">domain</span>
<span><strong>ÄÆ¡n vá»‹:</strong> Bá»™ mÃ´n CN Pháº§n má»m - Khoa CNTT</span>
</div>
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[16px] text-outline">mail</span>
<span><strong>Email:</strong> nguoidung028@example.test</span>
</div>
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[16px] text-outline">call</span>
<span><strong>Äiá»‡n thoáº¡i:</strong> 0918 234 567</span>
</div>
</div>
<!-- Expertise Tags -->
<div class="pt-2 flex items-center gap-2 flex-wrap" id="profile-extra">
<span class="font-label-sm text-label-sm font-semibold text-on-surface-variant">ChuyÃªn mÃ´n chÃ­nh:</span>
<span class="px-2.5 py-0.5 rounded bg-surface-container text-on-secondary-fixed text-label-sm font-medium border border-outline-variant/60">Kiáº¿n trÃºc pháº§n má»m</span>
<span class="px-2.5 py-0.5 rounded bg-surface-container text-on-secondary-fixed text-label-sm font-medium border border-outline-variant/60">Láº­p trÃ¬nh Web doanh nghiá»‡p</span>
<span class="px-2.5 py-0.5 rounded bg-surface-container text-on-secondary-fixed text-label-sm font-medium border border-outline-variant/60">DevOps &amp; CI/CD</span>
<span class="px-2.5 py-0.5 rounded bg-surface-container text-on-secondary-fixed text-label-sm font-medium border border-outline-variant/60">Äáº£m báº£o cháº¥t lÆ°á»£ng pháº§n má»m</span>
</div>
</div>
</div>
<!-- Collapse Button -->
<button class="text-on-surface-variant hover:text-primary p-1.5 rounded-lg hover:bg-surface-container transition-colors flex items-center gap-1 font-label-md text-label-md" onclick="document.getElementById('profile-extra').classList.toggle('hidden')">
<span>Thu gá»n</span>
<span class="material-symbols-outlined text-[18px]">expand_less</span>
</button>
</div>
</div>
<!-- 3. THANH THÃ”NG TIN & RÃ€NG BUá»˜C Há»ŒC Ká»² (KPI STATS BANNER) -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
<!-- Tháº» 1: ThÃ´ng tin Ä‘á»£t -->
<div class="bg-surface-container-lowest border border-outline-variant/80 rounded-xl p-4 shadow-sm flex items-start gap-3.5">
<div class="w-10 h-10 rounded-lg bg-primary-fixed text-primary flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined text-[22px]">event_available</span>
</div>
<div class="min-w-0">
<div class="flex items-center gap-2">
<span class="font-label-md text-label-md text-on-surface-variant">Háº¡n chÃ³t Ä‘Äƒng kÃ½ lá»‹ch</span>
<span class="bg-blue-100 text-primary text-[10px] font-bold px-1.5 py-0.2 rounded">Cá»•ng Ä‘ang má»Ÿ</span>
</div>
<p class="font-headline-sm text-headline-sm font-bold text-on-surface mt-0.5">20/08/2026</p>
<p class="font-body-sm text-body-sm text-amber-700 font-medium">Há»c ká»³ 1 (2026 - 2027) â€¢ CÃ²n 4 ngÃ y</p>
</div>
</div>
<!-- Tháº» 2: Quy cháº¿ Ä‘Äƒng kÃ½ -->
<div class="bg-surface-container-lowest border border-outline-variant/80 rounded-xl p-4 shadow-sm flex items-start gap-3.5">
<div class="w-10 h-10 rounded-lg bg-surface-container-high text-on-tertiary-fixed-variant flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined text-[22px]">rule</span>
</div>
<div class="min-w-0">
<span class="font-label-md text-label-md text-on-surface-variant">Quy cháº¿ xáº¿p thá»i khÃ³a biá»ƒu</span>
<p class="font-headline-sm text-headline-sm font-bold text-on-surface mt-0.5">Tá»‘i Ä‘a 10 tiáº¿t báº­n / tuáº§n</p>
<p class="font-body-sm text-body-sm text-on-surface-variant">Äá»ƒ PhÃ²ng ÄÃ o táº¡o bá»‘ trÃ­ lá»‹ch giáº£ng dáº¡y tá»‘i Æ°u.</p>
</div>
</div>
<!-- Tháº» 3: Tiáº¿n Ä‘á»™ sá»‘ tiáº¿t báº­n -->
<div class="bg-surface-container-lowest border border-outline-variant/80 rounded-xl p-4 shadow-sm flex items-start gap-3.5">
<div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined text-[22px]">pending_actions</span>
</div>
<div class="flex-1 min-w-0">
<div class="flex items-center justify-between">
<span class="font-label-md text-label-md text-on-surface-variant">Tiáº¿t báº­n Ä‘Ã£ Ä‘Äƒng kÃ½</span>
<span class="font-label-sm text-label-sm font-bold text-emerald-700 bg-emerald-50 px-2 py-0.2 rounded-full border border-emerald-200">
                  Há»£p lá»‡ (CÃ²n 4 tiáº¿t)
                </span>
</div>
<div class="flex items-baseline gap-2 mt-0.5">
<span class="font-headline-lg text-headline-lg font-bold text-amber-700">6 / 10</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">tiáº¿t (60% háº¡n má»©c)</span>
</div>
<!-- Progress Bar -->
<div class="w-full bg-surface-container h-2 rounded-full mt-1.5 overflow-hidden">
<div class="bg-amber-500 h-full rounded-full transition-all duration-300" style="width: 60%"></div>
</div>
</div>
</div>
</div>
<!-- 4. BANNER Cáº¢NH BÃO XUNG Äá»˜T Lá»ŠCH Dáº Y (CONFLICT ALERT BOX) -->
<div class="bg-red-50 border border-red-200 text-red-900 p-4 rounded-xl flex items-start gap-3.5 shadow-xs">
<span class="material-symbols-outlined text-[24px] text-error flex-shrink-0 mt-0.5">warning</span>
<div class="flex-1 text-body-md font-body-md">
<span class="font-bold text-error">PhÃ¡t hiá»‡n xung Ä‘á»™t lá»‹ch dáº¡y táº¡i Thá»© 4, Tiáº¿t 1 - 4:</span>
<span>Báº¡n Ä‘Ã£ Ä‘Æ°á»£c xáº¿p lá»‹ch giáº£ng dáº¡y lá»›p chÃ­nh thá»©c <strong>[241_71ITEC3201_01 - Láº­p trÃ¬nh Web]</strong> táº¡i phÃ²ng mÃ¡y <strong>A2.301</strong>. Há»‡ thá»‘ng tá»± Ä‘á»™ng khÃ³a vÃ  khÃ´ng cho phÃ©p chá»n báº­n vÃ o khung giá» Ä‘Ã£ phÃ¡t sinh lá»›p há»c. Giáº£ng viÃªn vui lÃ²ng liÃªn há»‡ TrÆ°á»Ÿng bá»™ mÃ´n CNPM náº¿u cÃ³ nguyá»‡n vá»ng Ä‘iá»u chá»‰nh giá» giáº£ng.</span>
</div>
<button class="text-error hover:text-red-950 text-sm font-bold p-1">
<span class="material-symbols-outlined text-[18px]">close</span>
</button>
</div>
<!-- 5. Bá» Cá»¤C 2 Cá»˜T CHÃNH (70% GRID TUáº¦N - 30% PANEL GHI CHÃš) -->
<div class="grid grid-cols-12 gap-5 items-start">
<!-- Cá»˜T TRÃI (70% ~ 8.5/12 COLS): Báº¢NG LÆ¯á»šI TUáº¦N ÄÄ‚NG KÃ Lá»ŠCH Báº¬N -->
<div class="col-span-12 xl:col-span-8 bg-surface-container-lowest border border-outline-variant/80 rounded-xl p-5 shadow-sm space-y-4">
<!-- Header Grid & Legend Bar -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pb-2 border-b border-outline-variant/50">
<div>
<h2 class="font-headline-sm text-headline-sm font-bold text-on-surface flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[20px]">grid_on</span>
                  LÆ°á»›i Thá»i khÃ³a biá»ƒu &amp; Khung giá» báº­n trong tuáº§n
                </h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Nháº¥p trá»±c tiáº¿p vÃ o Ã´ Ä‘á»ƒ Ä‘Ã¡nh dáº¥u báº­n hoáº·c chá»n khung giá» tá»« báº£ng Ä‘iá»u khiá»ƒn bÃªn pháº£i.</p>
</div>
<!-- Legend (ChÃº thÃ­ch) -->
<div class="flex items-center gap-3 bg-surface-container-low px-3 py-1.5 rounded-lg border border-outline-variant/60 text-label-sm font-label-sm flex-wrap">
<div class="flex items-center gap-1.5">
<span class="w-3.5 h-3.5 bg-white border border-outline-variant rounded"></span>
<span class="text-on-surface-variant">Sáºµn sÃ ng dáº¡y</span>
</div>
<div class="flex items-center gap-1.5">
<span class="w-3.5 h-3.5 bg-amber-100 border border-amber-400 rounded"></span>
<span class="text-amber-900 font-semibold">ÄÃ£ Ä‘Äƒng kÃ½ báº­n</span>
</div>
<div class="flex items-center gap-1.5">
<span class="w-3.5 h-3.5 bg-primary-fixed border border-primary rounded"></span>
<span class="text-primary font-semibold">Lá»›p Ä‘ang dáº¡y (KhÃ³a)</span>
</div>
</div>
</div>
<!-- Weekly Schedule Grid Table (Condensed Institutional Style) -->
<div class="overflow-x-auto custom-scroll border border-outline-variant/70 rounded-lg">
<table class="w-full border-collapse text-left text-body-sm">
<thead>
<tr class="bg-surface-container text-on-surface font-label-md text-label-md border-b border-outline-variant">
<th class="py-2.5 px-3 w-28 text-center border-r border-outline-variant font-bold">Ca &amp; Tiáº¿t há»c</th>
<th class="py-2.5 px-2 text-center border-r border-outline-variant w-36">Thá»© 2</th>
<th class="py-2.5 px-2 text-center border-r border-outline-variant w-36">Thá»© 3</th>
<th class="py-2.5 px-2 text-center border-r border-outline-variant w-36">Thá»© 4</th>
<th class="py-2.5 px-2 text-center border-r border-outline-variant w-36">Thá»© 5</th>
<th class="py-2.5 px-2 text-center border-r border-outline-variant w-36">Thá»© 6</th>
<th class="py-2.5 px-2 text-center border-r border-outline-variant w-36">Thá»© 7</th>
<th class="py-2.5 px-2 text-center w-28 text-on-surface-variant">Chá»§ Nháº­t</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant/50">
<!-- CA SÃNG HEADER ROW -->
<tr class="bg-surface-container-low/70">
<td class="py-1 px-3 text-label-sm font-bold text-primary uppercase tracking-wider bg-surface-container-low flex items-center gap-1.5" colspan="8">
<span class="material-symbols-outlined text-[15px]">wb_sunny</span>
                      Ca SÃ¡ng (Tiáº¿t 1 - Tiáº¿t 5 | 07:00 - 11:00)
                    </td>
</tr>
<!-- TIáº¾T 1-3 SÃNG -->
<tr class="h-16 hover:bg-slate-50/50">
<td class="p-2 border-r border-outline-variant bg-surface-container-lowest text-center">
<span class="font-semibold block text-on-surface">Tiáº¿t 1 - 3</span>
<span class="text-[10px] text-on-surface-variant block">07:00 - 09:30</span>
</td>
<!-- T2: Trá»‘ng -->
<td class="p-1.5 border-r border-outline-variant text-center align-middle group cursor-pointer hover:bg-blue-50/40 transition-colors">
<span class="text-xs text-outline opacity-0 group-hover:opacity-100 font-medium transition-opacity">+ Chá»n báº­n</span>
</td>
<!-- T3: Báº¬N Há»ŒP KHOA (TIáº¾T 1-3) -->
<td class="p-2 border-r border-outline-variant bg-amber-50 border-l-2 border-l-amber-500 align-top shadow-xs" rowspan="2">
<div class="h-full flex flex-col justify-between">
<div>
<div class="flex items-center justify-between text-amber-900 font-bold text-label-sm">
<span class="flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]">event_busy</span>
                              [Báº¬N] 3 tiáº¿t
                            </span>
<button class="text-amber-800 hover:text-red-700 hover:bg-amber-200/60 rounded p-0.5 transition-colors" title="XÃ³a lá»‹ch báº­n">
<span class="material-symbols-outlined text-[14px]">close</span>
</button>
</div>
<p class="font-body-sm text-[11px] text-amber-950 font-medium mt-1 leading-snug">
                            Há»p Há»™i Ä‘á»“ng Khoa Ä‘á»‹nh ká»³
                          </p>
</div>
<span class="text-[10px] text-amber-800 font-semibold bg-amber-200/50 px-1 py-0.5 rounded self-start mt-2">CÃ´ng tÃ¡c Khoa</span>
</div>
</td>
<!-- T4: ÄANG Dáº Y Lá»šP (TIáº¾T 1-4 KHÃ“A) -->
<td class="p-2 border-r border-outline-variant bg-primary-fixed/40 border-l-2 border-l-primary align-top shadow-xs" rowspan="2">
<div class="h-full flex flex-col justify-between">
<div>
<div class="flex items-center justify-between text-primary font-bold text-label-sm">
<span class="flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]">lock</span>
                              Äang dáº¡y (4 tiáº¿t)
                            </span>
</div>
<p class="font-body-sm text-[11px] text-on-primary-fixed font-bold mt-1 leading-tight">
                            241_71ITEC3201_01
                          </p>
<p class="text-[11px] text-on-surface font-medium leading-tight">Láº­p trÃ¬nh Web</p>
<p class="text-[10px] text-primary font-semibold mt-0.5">PhÃ²ng: A2.301</p>
</div>
<span class="text-[9px] text-primary bg-white/80 border border-primary/30 px-1 py-0.5 rounded font-bold self-start mt-1">ÄÃ£ chá»‘t lá»‹ch TKB</span>
</div>
</td>
<!-- T5: Trá»‘ng -->
<td class="p-1.5 border-r border-outline-variant text-center align-middle group cursor-pointer hover:bg-blue-50/40">
<span class="text-xs text-outline opacity-0 group-hover:opacity-100 font-medium">+ Chá»n báº­n</span>
</td>
<!-- T6: Trá»‘ng -->
<td class="p-1.5 border-r border-outline-variant text-center align-middle group cursor-pointer hover:bg-blue-50/40">
<span class="text-xs text-outline opacity-0 group-hover:opacity-100 font-medium">+ Chá»n báº­n</span>
</td>
<!-- T7: Trá»‘ng -->
<td class="p-1.5 border-r border-outline-variant text-center align-middle group cursor-pointer hover:bg-blue-50/40">
<span class="text-xs text-outline opacity-0 group-hover:opacity-100 font-medium">+ Chá»n báº­n</span>
</td>
<!-- CN: Trá»‘ng -->
<td class="p-1.5 text-center align-middle bg-slate-50/50">
<span class="text-[11px] text-outline">Nghá»‰</span>
</td>
</tr>
<!-- TIáº¾T 4-5 SÃNG -->
<tr class="h-14 hover:bg-slate-50/50">
<td class="p-2 border-r border-outline-variant bg-surface-container-lowest text-center">
<span class="font-semibold block text-on-surface">Tiáº¿t 4 - 5</span>
<span class="text-[10px] text-on-surface-variant block">09:30 - 11:00</span>
</td>
<!-- T2: Trá»‘ng -->
<td class="p-1.5 border-r border-outline-variant text-center align-middle group cursor-pointer hover:bg-blue-50/40">
<span class="text-xs text-outline opacity-0 group-hover:opacity-100 font-medium">+ Chá»n báº­n</span>
</td>
<!-- (T3 vÃ  T4 do rowspan phá»§ xuá»‘ng) -->
<!-- T5: Trá»‘ng -->
<td class="p-1.5 border-r border-outline-variant text-center align-middle group cursor-pointer hover:bg-blue-50/40">
<span class="text-xs text-outline opacity-0 group-hover:opacity-100 font-medium">+ Chá»n báº­n</span>
</td>
<!-- T6: Trá»‘ng -->
<td class="p-1.5 border-r border-outline-variant text-center align-middle group cursor-pointer hover:bg-blue-50/40">
<span class="text-xs text-outline opacity-0 group-hover:opacity-100 font-medium">+ Chá»n báº­n</span>
</td>
<!-- T7: Trá»‘ng -->
<td class="p-1.5 border-r border-outline-variant text-center align-middle group cursor-pointer hover:bg-blue-50/40">
<span class="text-xs text-outline opacity-0 group-hover:opacity-100 font-medium">+ Chá»n báº­n</span>
</td>
<!-- CN: Trá»‘ng -->
<td class="p-1.5 text-center align-middle bg-slate-50/50">
<span class="text-[11px] text-outline">Nghá»‰</span>
</td>
</tr>
<!-- CA CHIá»€U HEADER ROW -->
<tr class="bg-surface-container-low/70">
<td class="py-1 px-3 text-label-sm font-bold text-amber-800 uppercase tracking-wider bg-surface-container-low flex items-center gap-1.5" colspan="8">
<span class="material-symbols-outlined text-[15px]">wb_twilight</span>
                      Ca Chiá»u (Tiáº¿t 6 - Tiáº¿t 10 | 12:30 - 16:30)
                    </td>
</tr>
<!-- TIáº¾T 6-8 CHIá»€U -->
<tr class="h-16 hover:bg-slate-50/50">
<td class="p-2 border-r border-outline-variant bg-surface-container-lowest text-center">
<span class="font-semibold block text-on-surface">Tiáº¿t 6 - 8</span>
<span class="text-[10px] text-on-surface-variant block">12:30 - 15:00</span>
</td>
<!-- T2: Trá»‘ng -->
<td class="p-1.5 border-r border-outline-variant text-center align-middle group cursor-pointer hover:bg-blue-50/40">
<span class="text-xs text-outline opacity-0 group-hover:opacity-100 font-medium">+ Chá»n báº­n</span>
</td>
<!-- T3: Trá»‘ng -->
<td class="p-1.5 border-r border-outline-variant text-center align-middle group cursor-pointer hover:bg-blue-50/40">
<span class="text-xs text-outline opacity-0 group-hover:opacity-100 font-medium">+ Chá»n báº­n</span>
</td>
<!-- T4: Trá»‘ng -->
<td class="p-1.5 border-r border-outline-variant text-center align-middle group cursor-pointer hover:bg-blue-50/40">
<span class="text-xs text-outline opacity-0 group-hover:opacity-100 font-medium">+ Chá»n báº­n</span>
</td>
<!-- T5: Báº¬N CÃ”NG TÃC NCKH (TIáº¾T 7-9) -->
<td class="p-2 border-r border-outline-variant bg-amber-50 border-l-2 border-l-amber-500 align-top shadow-xs" rowspan="2">
<div class="h-full flex flex-col justify-between">
<div>
<div class="flex items-center justify-between text-amber-900 font-bold text-label-sm">
<span class="flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]">event_busy</span>
                              [Báº¬N] 3 tiáº¿t
                            </span>
<button class="text-amber-800 hover:text-red-700 hover:bg-amber-200/60 rounded p-0.5 transition-colors" title="XÃ³a lá»‹ch báº­n">
<span class="material-symbols-outlined text-[14px]">close</span>
</button>
</div>
<p class="font-body-sm text-[11px] text-amber-950 font-medium mt-1 leading-snug">
                            CÃ´ng tÃ¡c NCKH &amp; Äá» tÃ i
                          </p>
</div>
<span class="text-[10px] text-amber-800 font-semibold bg-amber-200/50 px-1 py-0.5 rounded self-start mt-2">NghiÃªn cá»©u KH</span>
</div>
</td>
<!-- T6: Trá»‘ng -->
<td class="p-1.5 border-r border-outline-variant text-center align-middle group cursor-pointer hover:bg-blue-50/40">
<span class="text-xs text-outline opacity-0 group-hover:opacity-100 font-medium">+ Chá»n báº­n</span>
</td>
<!-- T7: Trá»‘ng -->
<td class="p-1.5 border-r border-outline-variant text-center align-middle group cursor-pointer hover:bg-blue-50/40">
<span class="text-xs text-outline opacity-0 group-hover:opacity-100 font-medium">+ Chá»n báº­n</span>
</td>
<!-- CN: Trá»‘ng -->
<td class="p-1.5 text-center align-middle bg-slate-50/50">
<span class="text-[11px] text-outline">Nghá»‰</span>
</td>
</tr>
<!-- TIáº¾T 9-10 CHIá»€U -->
<tr class="h-14 hover:bg-slate-50/50">
<td class="p-2 border-r border-outline-variant bg-surface-container-lowest text-center">
<span class="font-semibold block text-on-surface">Tiáº¿t 9 - 10</span>
<span class="text-[10px] text-on-surface-variant block">15:00 - 16:30</span>
</td>
<td class="p-1.5 border-r border-outline-variant text-center align-middle group cursor-pointer hover:bg-blue-50/40">
<span class="text-xs text-outline opacity-0 group-hover:opacity-100 font-medium">+ Chá»n báº­n</span>
</td>
<td class="p-1.5 border-r border-outline-variant text-center align-middle group cursor-pointer hover:bg-blue-50/40">
<span class="text-xs text-outline opacity-0 group-hover:opacity-100 font-medium">+ Chá»n báº­n</span>
</td>
<td class="p-1.5 border-r border-outline-variant text-center align-middle group cursor-pointer hover:bg-blue-50/40">
<span class="text-xs text-outline opacity-0 group-hover:opacity-100 font-medium">+ Chá»n báº­n</span>
</td>
<!-- T5 báº­n phá»§ xuá»‘ng -->
<td class="p-1.5 border-r border-outline-variant text-center align-middle group cursor-pointer hover:bg-blue-50/40">
<span class="text-xs text-outline opacity-0 group-hover:opacity-100 font-medium">+ Chá»n báº­n</span>
</td>
<td class="p-1.5 border-r border-outline-variant text-center align-middle group cursor-pointer hover:bg-blue-50/40">
<span class="text-xs text-outline opacity-0 group-hover:opacity-100 font-medium">+ Chá»n báº­n</span>
</td>
<td class="p-1.5 text-center align-middle bg-slate-50/50">
<span class="text-[11px] text-outline">Nghá»‰</span>
</td>
</tr>
<!-- CA Tá»I HEADER ROW -->
<tr class="bg-surface-container-low/70">
<td class="py-1 px-3 text-label-sm font-bold text-secondary uppercase tracking-wider bg-surface-container-low flex items-center gap-1.5" colspan="8">
<span class="material-symbols-outlined text-[15px]">dark_mode</span>
                      Ca Tá»‘i (Tiáº¿t 11 - Tiáº¿t 12 | 17:30 - 19:00)
                    </td>
</tr>
<!-- TIáº¾T 11-12 Tá»I -->
<tr class="h-14 hover:bg-slate-50/50">
<td class="p-2 border-r border-outline-variant bg-surface-container-lowest text-center">
<span class="font-semibold block text-on-surface">Tiáº¿t 11 - 12</span>
<span class="text-[10px] text-on-surface-variant block">17:30 - 19:00</span>
</td>
<td class="p-1.5 border-r border-outline-variant text-center align-middle group cursor-pointer hover:bg-blue-50/40">
<span class="text-xs text-outline opacity-0 group-hover:opacity-100 font-medium">+ Chá»n báº­n</span>
</td>
<td class="p-1.5 border-r border-outline-variant text-center align-middle group cursor-pointer hover:bg-blue-50/40">
<span class="text-xs text-outline opacity-0 group-hover:opacity-100 font-medium">+ Chá»n báº­n</span>
</td>
<td class="p-1.5 border-r border-outline-variant text-center align-middle group cursor-pointer hover:bg-blue-50/40">
<span class="text-xs text-outline opacity-0 group-hover:opacity-100 font-medium">+ Chá»n báº­n</span>
</td>
<td class="p-1.5 border-r border-outline-variant text-center align-middle group cursor-pointer hover:bg-blue-50/40">
<span class="text-xs text-outline opacity-0 group-hover:opacity-100 font-medium">+ Chá»n báº­n</span>
</td>
<td class="p-1.5 border-r border-outline-variant text-center align-middle group cursor-pointer hover:bg-blue-50/40">
<span class="text-xs text-outline opacity-0 group-hover:opacity-100 font-medium">+ Chá»n báº­n</span>
</td>
<td class="p-1.5 border-r border-outline-variant text-center align-middle group cursor-pointer hover:bg-blue-50/40">
<span class="text-xs text-outline opacity-0 group-hover:opacity-100 font-medium">+ Chá»n báº­n</span>
</td>
<td class="p-1.5 text-center align-middle bg-slate-50/50">
<span class="text-[11px] text-outline">Nghá»‰</span>
</td>
</tr>
</tbody>
</table>
</div>
<div class="flex items-center justify-between text-body-sm text-on-surface-variant pt-1">
<span class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-[16px] text-primary">info</span>
                Sau khi Ä‘Äƒng kÃ½, lá»‹ch báº­n sáº½ Ä‘Æ°á»£c tá»± Ä‘á»™ng Ä‘á»“ng bá»™ vÃ o há»‡ thá»‘ng TKB cá»§a Khoa CÃ´ng nghá»‡ thÃ´ng tin.
              </span>
<span class="text-label-sm font-semibold text-outline">Cáº­p nháº­t láº§n cuá»‘i: 10:15 - HÃ´m nay</span>
</div>
</div>
<!-- Cá»˜T PHáº¢I (30% ~ 3.5/12 COLS): PANEL GHI CHÃš & THIáº¾T Láº¬P Lá»ŠCH Báº¬N -->
<div class="col-span-12 xl:col-span-4 space-y-4">
<!-- THáºº FORM THÃŠM GHI CHÃš KHUNG GIá»œ Báº¬N -->
<div class="bg-surface-container-lowest border border-outline-variant/80 rounded-xl p-5 shadow-sm space-y-4">
<div class="border-b border-outline-variant/60 pb-3">
<h3 class="font-headline-sm text-headline-sm font-bold text-on-surface flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[20px]">add_circle</span>
                  ThÃªm khung giá» báº­n má»›i
                </h3>
<p class="font-body-sm text-body-sm text-on-surface-variant">Chá»n thá»i gian vÃ  nháº­p lÃ½ do Ä‘á»ƒ Ä‘Äƒng kÃ½ thÃªm</p>
</div>
<!-- Form Controls -->
<div class="space-y-3.5">
<!-- Chá»n Thá»© -->
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1">Thá»© trong tuáº§n</label>
<select aria-label="Thá»© 2" class="w-full bg-surface border border-outline-variant rounded-lg px-3 py-2 text-body-md text-on-surface focus:border-primary focus:ring-1 focus:ring-primary">
<option>Thá»© 2</option>
<option>Thá»© 3</option>
<option>Thá»© 4 (CÃ³ lá»‹ch dáº¡y)</option>
<option selected="">Thá»© 6</option>
<option>Thá»© 7</option>
</select>
</div>
<!-- Chá»n Khung Tiáº¿t -->
<div class="grid grid-cols-2 gap-2">
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1">Tá»« tiáº¿t</label>
<select aria-label="Tiáº¿t 1 (07:00)" class="w-full bg-surface border border-outline-variant rounded-lg px-3 py-2 text-body-md text-on-surface focus:border-primary focus:ring-1 focus:ring-primary">
<option>Tiáº¿t 1 (07:00)</option>
<option>Tiáº¿t 2 (07:45)</option>
<option>Tiáº¿t 3 (08:45)</option>
<option selected="">Tiáº¿t 6 (12:30)</option>
<option>Tiáº¿t 7 (13:15)</option>
</select>
</div>
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1">Äáº¿n tiáº¿t</label>
<select aria-label="Tiáº¿t 3 (09:30)" class="w-full bg-surface border border-outline-variant rounded-lg px-3 py-2 text-body-md text-on-surface focus:border-primary focus:ring-1 focus:ring-primary">
<option>Tiáº¿t 3 (09:30)</option>
<option>Tiáº¿t 5 (11:00)</option>
<option selected="">Tiáº¿t 8 (15:00)</option>
<option>Tiáº¿t 10 (16:30)</option>
</select>
</div>
</div>
<!-- Loáº¡i lÃ½ do báº­n -->
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1">Loáº¡i lÃ½ do báº­n</label>
<select aria-label="Há»p Khoa / TrÆ°á»ng / ÄoÃ n thá»ƒ" class="w-full bg-surface border border-outline-variant rounded-lg px-3 py-2 text-body-md text-on-surface focus:border-primary focus:ring-1 focus:ring-primary">
<option>Há»p Khoa / TrÆ°á»ng / ÄoÃ n thá»ƒ</option>
<option selected="">NghiÃªn cá»©u khoa há»c &amp; Äá» tÃ i</option>
<option>CÃ´ng tÃ¡c chuyÃªn mÃ´n / Há»™i Ä‘á»“ng</option>
<option>LÃ½ do cÃ¡ nhÃ¢n (BÃ¡c sÄ©, gia Ä‘Ã¬nh)</option>
</select>
</div>
<!-- Ghi chÃº chi tiáº¿t Textarea -->
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1">Ghi chÃº diá»…n giáº£i cá»¥ thá»ƒ</label>
<textarea class="w-full bg-surface border border-outline-variant rounded-lg p-2.5 text-body-md text-on-surface placeholder:text-outline-variant focus:border-primary focus:ring-1 focus:ring-primary resize-none" placeholder="Nháº­p diá»…n giáº£i cá»¥ thá»ƒ lÃ½ do báº­n Ä‘á»ƒ phÃ²ng ÄÃ o táº¡o Ä‘á»‘i chiáº¿u..." rows="2"></textarea>
</div>
<!-- Submit Button -->
<button class="w-full bg-primary hover:bg-primary-container text-white rounded-lg py-2.5 px-4 font-label-lg text-label-lg font-semibold flex items-center justify-center gap-2 transition-colors shadow-sm active:scale-[0.99]">
<span class="material-symbols-outlined text-[18px]">playlist_add</span>
<span>Ãp dá»¥ng vÃ o báº£ng thá»i khÃ³a biá»ƒu</span>
</button>
</div>
</div>
<!-- DANH SÃCH CÃC TIáº¾T Báº¬N ÄÃƒ GHI NHáº¬N -->
<div class="bg-surface-container-lowest border border-outline-variant/80 rounded-xl p-5 shadow-sm space-y-3">
<div class="flex items-center justify-between border-b border-outline-variant/60 pb-2">
<h4 class="font-headline-sm text-headline-sm font-bold text-on-surface flex items-center gap-1.5">
<span class="material-symbols-outlined text-amber-700 text-[18px]">fact_check</span>
                  Tiáº¿t báº­n Ä‘Ã£ Ä‘Äƒng kÃ½ (2 má»¥c)
                </h4>
<span class="font-label-md text-label-md font-bold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full">6 tiáº¿t</span>
</div>
<!-- List Items -->
<div class="space-y-2.5">
<!-- Item 1: Thá»© 3 -->
<div class="p-3 bg-surface-container-low rounded-lg border border-outline-variant/80 flex items-start justify-between gap-2">
<div class="space-y-1">
<div class="flex items-center gap-2">
<span class="font-label-md text-label-md font-bold text-on-surface bg-white px-2 py-0.5 rounded border border-outline-variant">Thá»© 3</span>
<span class="font-label-sm text-label-sm font-semibold text-amber-800">Tiáº¿t 1 - 3 (07:00 - 09:30)</span>
</div>
<p class="font-body-md text-body-md font-medium text-on-surface">Há»p Há»™i Ä‘á»“ng Khoa Ä‘á»‹nh ká»³</p>
<span class="text-[11px] text-on-surface-variant block">Loáº¡i: Há»p Khoa / TrÆ°á»ng</span>
</div>
<button class="text-on-surface-variant hover:text-error p-1 rounded hover:bg-white transition-colors" title="Há»§y tiáº¿t báº­n nÃ y">
<span class="material-symbols-outlined text-[18px]">delete</span>
</button>
</div>
<!-- Item 2: Thá»© 5 -->
<div class="p-3 bg-surface-container-low rounded-lg border border-outline-variant/80 flex items-start justify-between gap-2">
<div class="space-y-1">
<div class="flex items-center gap-2">
<span class="font-label-md text-label-md font-bold text-on-surface bg-white px-2 py-0.5 rounded border border-outline-variant">Thá»© 5</span>
<span class="font-label-sm text-label-sm font-semibold text-amber-800">Tiáº¿t 7 - 9 (13:15 - 15:45)</span>
</div>
<p class="font-body-md text-body-md font-medium text-on-surface">CÃ´ng tÃ¡c NCKH &amp; Äá» tÃ i cáº¥p Bá»™</p>
<span class="text-[11px] text-on-surface-variant block">Loáº¡i: NghiÃªn cá»©u khoa há»c</span>
</div>
<button class="text-on-surface-variant hover:text-error p-1 rounded hover:bg-white transition-colors" title="Há»§y tiáº¿t báº­n nÃ y">
<span class="material-symbols-outlined text-[18px]">delete</span>
</button>
</div>
</div>
<!-- HÃ€NH Äá»˜NG CHÃNH á»ž CHÃ‚N TRANG (PRIMARY / SECONDARY ACTIONS) -->
<div class="pt-3 border-t border-outline-variant/60 space-y-2">
<button class="w-full bg-primary hover:bg-primary-container text-white rounded-lg py-2.5 px-4 font-label-lg text-label-lg font-bold flex items-center justify-center gap-2 shadow-sm transition-all active:scale-95">
<span class="material-symbols-outlined text-[20px]">save</span>
<span>LÆ°u lá»‹ch báº­n há»c ká»³</span>
</button>
<div class="grid grid-cols-2 gap-2">
<button class="bg-white hover:bg-slate-50 text-on-surface border border-outline-variant rounded-lg py-2 px-3 font-label-md text-label-md font-medium flex items-center justify-center gap-1.5 transition-colors">
<span class="material-symbols-outlined text-[18px]">undo</span>
<span>HoÃ n tÃ¡c thay Ä‘á»•i</span>
</button>
<button class="bg-white hover:bg-primary-fixed/30 text-primary border border-primary/40 rounded-lg py-2 px-3 font-label-md text-label-md font-semibold flex items-center justify-center gap-1.5 transition-colors">
<span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
<span>Xuáº¥t lá»‹ch báº­n PDF</span>
</button>
</div>
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

