@extends('fe_preview.layouts.teacher')
@section('page_title', 'Giáº£ng viÃªn - HUIT Há»c Vá»¥')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Giáº£ng viÃªn - HUIT Há»c Vá»¥</title>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/nhap-diem']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">edit_note</span>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Giáº£ng viÃªn</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Há»c ká»³ 1 â€¢ 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chá»§ / Giáº£ng viÃªn</div>
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
<!-- Top AppBar / Navigation Bar -->
<header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between sticky top-0 z-20 shadow-xs">
<!-- Breadcrumb -->
<nav class="flex items-center gap-2 text-xs font-medium text-slate-500">
<a class="hover:text-slate-800 transition-colors" href="#">Cá»•ng Giáº£ng viÃªn</a>
<span class="material-symbols-outlined text-[14px]">chevron_right</span>
<a class="hover:text-slate-800 transition-colors" href="#">Quáº£n lÃ½ Äiá»ƒm</a>
<span class="material-symbols-outlined text-[14px]">chevron_right</span>
<span class="text-[#1c55c4] font-semibold">Nháº­p Ä‘iá»ƒm há»c pháº§n</span>
</nav>
<!-- Right Appbar utility actions -->
<div class="flex items-center gap-3">
<div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-medium">
<span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
          Cá»•ng ná»™p Ä‘iá»ƒm trá»±c tuyáº¿n Ä‘ang má»Ÿ
        </div>
<div class="h-4 w-px bg-slate-200"></div>
<button class="relative p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors" title="ThÃ´ng bÃ¡o há»c vá»¥">
<span class="material-symbols-outlined text-[20px]">notifications</span>
<span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full"></span>
</button>
<button class="p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors" title="Trá»£ giÃºp &amp; Quy cháº¿">
<span class="material-symbols-outlined text-[20px]">help</span>
</button>
</div>
</header>
<!-- Content Container -->
<div class="p-8 max-w-[1400px] w-full mx-auto space-y-6">
<!-- ===================================================================== -->
<!-- CARD HERO: THÃ”NG TIN Lá»šP Há»ŒC PHáº¦N & TÃC Vá»¤ QUáº¢N TRá»Š -->
<!-- ===================================================================== -->
<section class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
<!-- Chi tiáº¿t lá»›p há»c pháº§n -->
<div class="space-y-3">
<div class="flex flex-wrap items-center gap-2.5">
<span class="px-2.5 py-1 rounded-md bg-blue-50 text-[#1c55c4] font-bold text-xs border border-blue-100 tracking-wide font-mono">
                241_71ITEC3201_01
              </span>
<h1 class="text-xl font-bold text-slate-900 tracking-tight">
                Láº­p trÃ¬nh Web
              </h1>
<span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                3 TÃ­n chá»‰ (LT: 2 â€¢ TH: 1)
              </span>
<!-- Tráº¡ng thÃ¡i sá»• Ä‘iá»ƒm -->
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200 text-xs font-medium">
<span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                Äang nháº­p Ä‘iá»ƒm â€¢ Báº£n nhÃ¡p tá»± Ä‘á»™ng lÆ°u lÃºc 14:35
              </span>
</div>
<!-- DÃ²ng meta chi tiáº¿t: Há»c ká»³, SÄ© sá»‘, Trá»ng sá»‘ -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-y-2 gap-x-6 text-xs text-slate-600">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[16px] text-slate-500">calendar_today</span>
<span>Há»c ká»³: <strong class="text-slate-800">Há»c ká»³ 1 (NÄƒm há»c 2026-2027)</strong></span>
</div>
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[16px] text-slate-500">groups</span>
<span>SÄ© sá»‘: <strong class="text-slate-800">60 Sinh viÃªn</strong> (12DHTH01, 12DHTH02)</span>
</div>
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[16px] text-amber-700">schedule</span>
<span class="text-amber-800 font-semibold bg-amber-50/80 px-2 py-0.5 rounded border border-amber-200/60">
                  Háº¡n cuá»‘i: 30/11/2026 (CÃ²n 12 ngÃ y)
                </span>
</div>
</div>
<!-- CÃ´ng thá»©c trá»ng sá»‘ Ä‘Ã¡nh giÃ¡ -->
<div class="inline-flex flex-wrap items-center gap-2 text-xs bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-lg text-slate-700">
<span class="font-semibold text-slate-900 flex items-center gap-1">
<span class="material-symbols-outlined text-[15px] text-[#1c55c4]">calculate</span>
                Trá»ng sá»‘ thÃ nh pháº§n:
              </span>
<span class="px-1.5 py-0.5 bg-white rounded border border-slate-200 font-medium">QuÃ¡ trÃ¬nh há»c táº­p: 10%</span>
<span>+</span>
<span class="px-1.5 py-0.5 bg-white rounded border border-slate-200 font-medium">BÃ i táº­p: 20%</span>
<span>+</span>
<span class="px-1.5 py-0.5 bg-white rounded border border-slate-200 font-medium">Giá»¯a ká»³: 20%</span>
<span>+</span>
<span class="px-1.5 py-0.5 bg-white rounded border border-slate-200 font-medium">Cuá»‘i ká»³: 50%</span>
<span class="text-[#1c55c4] font-bold">= 100% (Chuáº©n HUIT)</span>
</div>
</div>
<!-- Cá»¥m nÃºt tÃ¡c vá»¥ chÃ­nh -->
<div class="flex flex-wrap lg:flex-nowrap items-center gap-2.5 shrink-0">
<!-- Nháº­p file Excel -->
<button class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-lg text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 hover:text-slate-900 transition-colors shadow-2xs">
<span class="material-symbols-outlined text-[18px] text-emerald-700">upload_file</span>
              Nháº­p tá»« Excel
            </button>
<!-- Táº£i file máº«u -->
<button class="inline-flex items-center gap-1.5 px-3 py-2.5 rounded-lg text-xs font-medium text-slate-600 bg-white border border-slate-300 hover:bg-slate-50 hover:text-slate-800 transition-colors shadow-2xs" title="Táº£i máº«u Ä‘á»‹nh dáº¡ng chuáº©n HUIT">
<span class="material-symbols-outlined text-[18px] text-slate-500">download</span>
              Táº£i file máº«u (.xlsx)
            </button>
<!-- LÆ°u nhÃ¡p -->
<button class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition-colors shadow-2xs">
<span class="material-symbols-outlined text-[18px] text-slate-500">save</span>
              LÆ°u nhÃ¡p
            </button>
<!-- Ná»™p Ä‘iá»ƒm cho P.ÄÃ o táº¡o (NÃºt ná»•i báº­t kÃ­ch hoáº¡t Modal) -->
<button class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-xs font-semibold text-white bg-[#1c55c4] hover:bg-[#1542a0] transition-colors shadow-sm ring-2 ring-blue-200">
<span class="material-symbols-outlined text-[18px]">send</span>
              Ná»™p Ä‘iá»ƒm cho PhÃ²ng ÄÃ o táº¡o
            </button>
</div>
</div>
<!-- Cáº£nh bÃ¡o lá»—i tháº©m Ä‘á»‹nh (Validation Banner) -->
<div class="mt-4 p-3 rounded-lg bg-rose-50 border border-rose-200 flex items-start gap-3">
<span class="material-symbols-outlined text-rose-700 text-[20px] shrink-0 mt-0.5">error</span>
<div class="text-xs text-rose-800 flex-1">
<span class="font-bold">PhÃ¡t hiá»‡n 01 lá»—i nháº­p liá»‡u cáº§n xá»­ lÃ½:</span> Táº¡i dÃ²ng STT 3 sinh viÃªn <strong>LÃª HoÃ ng CÆ°á»ng (MSSV: 2001210189)</strong>, Ä‘iá»ƒm thi Cuá»‘i ká»³ Ä‘ang nháº­p <strong>10.5</strong> vÆ°á»£t quÃ¡ thang Ä‘iá»ƒm quy Ä‘á»‹nh (0.0 - 10.0). Vui lÃ²ng Ä‘iá»u chá»‰nh trÆ°á»›c khi ná»™p Ä‘iá»ƒm chÃ­nh thá»©c.
          </div>
<button class="text-xs font-semibold text-rose-700 hover:underline shrink-0">Äi Ä‘áº¿n Ã´ lá»—i</button>
</div>
</section>
<!-- ===================================================================== -->
<!-- Báº¢NG Dá»® LIá»†U NHáº¬P ÄIá»‚M TRá»°C TIáº¾P (SPREADSHEET GRID) -->
<!-- ===================================================================== -->
<section class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
<!-- Header Ä‘iá»u khiá»ƒn báº£ng tÃ­nh -->
<div class="px-6 py-4 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4 bg-slate-50/50">
<div class="flex items-center gap-3">
<h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
<span class="material-symbols-outlined text-[18px] text-[#1c55c4]">table_chart</span>
              Báº£ng Ä‘iá»ƒm quÃ¡ trÃ¬nh &amp; thi káº¿t thÃºc há»c pháº§n
            </h2>
<span class="text-xs px-2.5 py-0.5 rounded-full bg-blue-50 text-[#1c55c4] font-semibold border border-blue-200">
              60 Sinh viÃªn
            </span>
</div>
<!-- Bá»™ tÃ¬m kiáº¿m & lá»c nhanh -->
<div class="flex items-center gap-3 text-xs">
<div class="relative w-64">
<span class="material-symbols-outlined absolute left-2.5 top-2 text-slate-500 text-[18px]">search</span>
<input aria-label="TÃ¬m theo MSSV, Há» vÃ  tÃªn..." class="w-full pl-8 pr-3 py-1.5 rounded-lg border border-slate-300 text-xs focus:outline-none focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] bg-white" placeholder="TÃ¬m theo MSSV, Há» vÃ  tÃªn..." type="text"/>
</div>
<select aria-label="Táº¥t cáº£ lá»›p hÃ nh chÃ­nh" class="px-3 py-1.5 rounded-lg border border-slate-300 text-xs bg-white text-slate-700 focus:outline-none focus:ring-1 focus:ring-[#1c55c4]">
<option>Táº¥t cáº£ lá»›p hÃ nh chÃ­nh</option>
<option>12DHTH01</option>
<option>12DHTH02</option>
</select>
<div class="text-slate-500">|</div>
<span class="text-slate-500 font-medium">PhÃ­m táº¯t: DÃ¹ng <strong>Enter</strong> hoáº·c <strong>Tab</strong> Ä‘á»ƒ chuyá»ƒn Ã´ nhanh</span>
</div>
</div>
<!-- VÃ¹ng cuá»™n ngang báº£ng tÃ­nh (overflow-x-auto) -->
<div class="overflow-x-auto w-full">
<table class="w-full border-collapse text-left text-xs min-w-[1180px]">
<thead>
<tr class="bg-slate-100/90 text-slate-700 font-semibold border-b border-slate-200 text-[11px] uppercase tracking-wider">
<!-- Cá»™t cá»‘ Ä‘á»‹nh STT -->
<th class="py-3 px-3 text-center w-12 sticky left-0 bg-slate-100 z-10 border-r border-slate-200">STT</th>
<!-- Cá»™t cá»‘ Ä‘á»‹nh MSSV -->
<th class="py-3 px-3 w-28 sticky left-12 bg-slate-100 z-10 border-r border-slate-200">MSSV</th>
<!-- Cá»™t cá»‘ Ä‘á»‹nh Há» vÃ  tÃªn -->
<th class="py-3 px-4 w-44 sticky left-40 bg-slate-100 z-10 border-r border-slate-200 shadow-[2px_0_4px_rgba(0,0,0,0.03)]">Há» vÃ  tÃªn</th>
<th class="py-3 px-3 w-24 text-center">Lá»›p HC</th>
<th class="py-3 px-2 w-28 text-center bg-blue-50/50 text-[#1c55c4]">QuÃ¡ trÃ¬nh há»c táº­p<br/><span class="text-[10px] font-normal text-slate-500">(10%)</span></th>
<th class="py-3 px-2 w-28 text-center bg-blue-50/50 text-[#1c55c4]">BÃ i táº­p<br/><span class="text-[10px] font-normal text-slate-500">(20%)</span></th>
<th class="py-3 px-2 w-28 text-center bg-blue-50/50 text-[#1c55c4]">Giá»¯a ká»³<br/><span class="text-[10px] font-normal text-slate-500">(20%)</span></th>
<th class="py-3 px-2 w-32 text-center bg-amber-50/50 text-amber-900">Cuá»‘i ká»³<br/><span class="text-[10px] font-normal text-slate-500">(50%)</span></th>
<th class="py-3 px-3 w-24 text-center bg-slate-200/50 font-bold">Äiá»ƒm TK (10)</th>
<th class="py-3 px-2 w-20 text-center font-bold">Äiá»ƒm chá»¯</th>
<th class="py-3 px-3 w-28 text-center">ÄÃ¡nh giÃ¡</th>
<th class="py-3 px-3 min-w-[140px]">Ghi chÃº</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-200 bg-white">
<!-- DÃ²ng 1: Nguyá»…n VÄƒn An -->
<tr class="hover:bg-blue-50/30 transition-colors">
<td class="py-2.5 px-3 text-center text-slate-500 font-medium sticky left-0 bg-white z-10 border-r border-slate-200">1</td>
<td class="py-2.5 px-3 font-mono font-semibold text-[#1c55c4] sticky left-12 bg-white z-10 border-r border-slate-200">2001210123</td>
<td class="py-2.5 px-4 font-medium text-slate-900 sticky left-40 bg-white z-10 border-r border-slate-200 shadow-[2px_0_4px_rgba(0,0,0,0.03)] whitespace-nowrap">Nguyá»…n VÄƒn An</td>
<td class="py-2.5 px-3 text-center text-slate-600 font-mono">12DHTH01</td>
<!-- Ã” nháº­p QuÃ¡ trÃ¬nh há»c táº­p -->
<td class="py-1.5 px-2 text-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-semibold rounded border border-slate-300 focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] text-slate-800 bg-white" type="text" value="9.0"/>
</td>
<!-- Ã” nháº­p BÃ i táº­p -->
<td class="py-1.5 px-2 text-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-semibold rounded border border-slate-300 focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] text-slate-800 bg-white" type="text" value="8.0"/>
</td>
<!-- Ã” nháº­p Giá»¯a ká»³ -->
<td class="py-1.5 px-2 text-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-semibold rounded border border-slate-300 focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] text-slate-800 bg-white" type="text" value="8.0"/>
</td>
<!-- Ã” nháº­p Cuá»‘i ká»³ -->
<td class="py-1.5 px-2 text-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-semibold rounded border border-slate-300 focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] text-slate-800 bg-white" type="text" value="8.5"/>
</td>
<!-- Äiá»ƒm TK há»‡ 10 -->
<td class="py-2.5 px-3 text-center font-bold text-slate-900 bg-slate-50/60">8.4</td>
<!-- Äiá»ƒm chá»¯ -->
<td class="py-2.5 px-2 text-center font-bold text-blue-700">B+</td>
<!-- ÄÃ¡nh giÃ¡ -->
<td class="py-2.5 px-3 text-center">
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
<span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                    Äáº¡t
                  </span>
</td>
<td class="py-2.5 px-3 text-slate-500 italic text-[11px]">â€”</td>
</tr>
<!-- DÃ²ng 2: LÃª Thá»‹ Thu Tháº£o -->
<tr class="hover:bg-blue-50/30 transition-colors">
<td class="py-2.5 px-3 text-center text-slate-500 font-medium sticky left-0 bg-white z-10 border-r border-slate-200">2</td>
<td class="py-2.5 px-3 font-mono font-semibold text-[#1c55c4] sticky left-12 bg-white z-10 border-r border-slate-200">2001210045</td>
<td class="py-2.5 px-4 font-medium text-slate-900 sticky left-40 bg-white z-10 border-r border-slate-200 shadow-[2px_0_4px_rgba(0,0,0,0.03)] whitespace-nowrap">LÃª Thá»‹ Thu Tháº£o</td>
<td class="py-2.5 px-3 text-center text-slate-600 font-mono">12DHTH01</td>
<td class="py-1.5 px-2 text-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-semibold rounded border border-slate-300 focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] text-slate-800 bg-white" type="text" value="10.0"/>
</td>
<td class="py-1.5 px-2 text-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-semibold rounded border border-slate-300 focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] text-slate-800 bg-white" type="text" value="9.0"/>
</td>
<td class="py-1.5 px-2 text-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-semibold rounded border border-slate-300 focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] text-slate-800 bg-white" type="text" value="8.5"/>
</td>
<td class="py-1.5 px-2 text-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-semibold rounded border border-slate-300 focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] text-slate-800 bg-white" type="text" value="9.0"/>
</td>
<td class="py-2.5 px-3 text-center font-bold text-slate-900 bg-slate-50/60">9.0</td>
<td class="py-2.5 px-2 text-center font-bold text-emerald-700">A</td>
<td class="py-2.5 px-3 text-center">
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
<span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                    Äáº¡t
                  </span>
</td>
<td class="py-2.5 px-3 text-slate-500 text-[11px]">Sinh viÃªn xuáº¥t sáº¯c</td>
</tr>
<!-- DÃ²ng 3: DÃ’NG PHÃT HIá»†N Lá»–I NHáº¬P LIá»†U (Error Validation Demo) -->
<tr class="bg-rose-50/40 border-l-4 border-l-rose-500 hover:bg-rose-50/60 transition-colors">
<td class="py-2.5 px-3 text-center text-rose-700 font-bold sticky left-0 bg-rose-50/90 z-10 border-r border-slate-200">3</td>
<td class="py-2.5 px-3 font-mono font-semibold text-rose-700 sticky left-12 bg-rose-50/90 z-10 border-r border-slate-200">2001210189</td>
<td class="py-2.5 px-4 font-bold text-slate-900 sticky left-40 bg-rose-50/90 z-10 border-r border-slate-200 shadow-[2px_0_4px_rgba(0,0,0,0.03)] whitespace-nowrap">
                  LÃª HoÃ ng CÆ°á»ng
                  <span class="inline-block ml-1 text-rose-700 font-normal text-[10px]">(Váº¯ng 2 buá»•i)</span>
</td>
<td class="py-2.5 px-3 text-center text-slate-600 font-mono">12DHTH02</td>
<td class="py-1.5 px-2 text-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-semibold rounded border border-slate-300 focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] text-slate-800 bg-white" type="text" value="7.0"/>
</td>
<td class="py-1.5 px-2 text-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-semibold rounded border border-slate-300 focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] text-slate-800 bg-white" type="text" value="6.5"/>
</td>
<td class="py-1.5 px-2 text-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-semibold rounded border border-slate-300 focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] text-slate-800 bg-white" type="text" value="7.0"/>
</td>
<!-- Ã” NHáº¬P Lá»–I: 10.5 -->
<td class="py-1.5 px-2 text-center relative">
<div class="flex flex-col items-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-bold rounded border-2 border-rose-500 bg-rose-100/90 text-rose-800 ring-2 ring-rose-200 focus:outline-none" type="text" value="10.5"/>
<span class="text-[10px] text-rose-700 font-bold whitespace-nowrap mt-0.5 flex items-center gap-0.5">
<span class="material-symbols-outlined text-[12px]">warning</span>
                      Äiá»ƒm pháº£i tá»« 0 Ä‘áº¿n 10!
                    </span>
</div>
</td>
<td class="py-2.5 px-3 text-center font-bold text-rose-700 bg-rose-100/40">---</td>
<td class="py-2.5 px-2 text-center font-bold text-rose-700">---</td>
<td class="py-2.5 px-3 text-center">
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-100 text-rose-700 border border-rose-300">
<span class="w-1 h-1 rounded-full bg-rose-500"></span>
                    Cáº£nh bÃ¡o lá»—i
                  </span>
</td>
<td class="py-2.5 px-3 text-rose-700 font-medium text-[11px]">Sai thang Ä‘iá»ƒm cuá»‘i ká»³</td>
</tr>
<!-- DÃ²ng 4: Tráº§n Quá»‘c Báº£o -->
<tr class="hover:bg-blue-50/30 transition-colors">
<td class="py-2.5 px-3 text-center text-slate-500 font-medium sticky left-0 bg-white z-10 border-r border-slate-200">4</td>
<td class="py-2.5 px-3 font-mono font-semibold text-[#1c55c4] sticky left-12 bg-white z-10 border-r border-slate-200">2001210012</td>
<td class="py-2.5 px-4 font-medium text-slate-900 sticky left-40 bg-white z-10 border-r border-slate-200 shadow-[2px_0_4px_rgba(0,0,0,0.03)] whitespace-nowrap">Tráº§n Quá»‘c Báº£o</td>
<td class="py-2.5 px-3 text-center text-slate-600 font-mono">12DHTH01</td>
<td class="py-1.5 px-2 text-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-semibold rounded border border-slate-300 focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] text-slate-800 bg-white" type="text" value="8.5"/>
</td>
<td class="py-1.5 px-2 text-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-semibold rounded border border-slate-300 focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] text-slate-800 bg-white" type="text" value="7.5"/>
</td>
<td class="py-1.5 px-2 text-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-semibold rounded border border-slate-300 focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] text-slate-800 bg-white" type="text" value="7.0"/>
</td>
<td class="py-1.5 px-2 text-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-semibold rounded border border-slate-300 focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] text-slate-800 bg-white" type="text" value="7.5"/>
</td>
<td class="py-2.5 px-3 text-center font-bold text-slate-900 bg-slate-50/60">7.5</td>
<td class="py-2.5 px-2 text-center font-bold text-blue-700">B</td>
<td class="py-2.5 px-3 text-center">
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
<span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                    Äáº¡t
                  </span>
</td>
<td class="py-2.5 px-3 text-slate-500 italic text-[11px]">â€”</td>
</tr>
<!-- DÃ²ng 5: Äá»— Quá»‘c DÅ©ng -->
<tr class="hover:bg-blue-50/30 transition-colors">
<td class="py-2.5 px-3 text-center text-slate-500 font-medium sticky left-0 bg-white z-10 border-r border-slate-200">5</td>
<td class="py-2.5 px-3 font-mono font-semibold text-[#1c55c4] sticky left-12 bg-white z-10 border-r border-slate-200">2001210356</td>
<td class="py-2.5 px-4 font-medium text-slate-900 sticky left-40 bg-white z-10 border-r border-slate-200 shadow-[2px_0_4px_rgba(0,0,0,0.03)] whitespace-nowrap">Äá»— Quá»‘c DÅ©ng</td>
<td class="py-2.5 px-3 text-center text-slate-600 font-mono">12DHTH02</td>
<td class="py-1.5 px-2 text-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-semibold rounded border border-slate-300 focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] text-slate-800 bg-white" type="text" value="9.0"/>
</td>
<td class="py-1.5 px-2 text-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-semibold rounded border border-slate-300 focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] text-slate-800 bg-white" type="text" value="8.0"/>
</td>
<td class="py-1.5 px-2 text-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-semibold rounded border border-slate-300 focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] text-slate-800 bg-white" type="text" value="8.0"/>
</td>
<td class="py-1.5 px-2 text-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-semibold rounded border border-slate-300 focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] text-slate-800 bg-white" type="text" value="8.0"/>
</td>
<td class="py-2.5 px-3 text-center font-bold text-slate-900 bg-slate-50/60">8.1</td>
<td class="py-2.5 px-2 text-center font-bold text-blue-700">B+</td>
<td class="py-2.5 px-3 text-center">
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
<span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                    Äáº¡t
                  </span>
</td>
<td class="py-2.5 px-3 text-slate-500 italic text-[11px]">â€”</td>
</tr>
<!-- DÃ²ng 6: VÅ© Mai HÆ°Æ¡ng -->
<tr class="hover:bg-blue-50/30 transition-colors">
<td class="py-2.5 px-3 text-center text-slate-500 font-medium sticky left-0 bg-white z-10 border-r border-slate-200">6</td>
<td class="py-2.5 px-3 font-mono font-semibold text-[#1c55c4] sticky left-12 bg-white z-10 border-r border-slate-200">2001210411</td>
<td class="py-2.5 px-4 font-medium text-slate-900 sticky left-40 bg-white z-10 border-r border-slate-200 shadow-[2px_0_4px_rgba(0,0,0,0.03)] whitespace-nowrap">VÅ© Mai HÆ°Æ¡ng</td>
<td class="py-2.5 px-3 text-center text-slate-600 font-mono">12DHTH01</td>
<td class="py-1.5 px-2 text-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-semibold rounded border border-slate-300 focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] text-slate-800 bg-white" type="text" value="10.0"/>
</td>
<td class="py-1.5 px-2 text-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-semibold rounded border border-slate-300 focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] text-slate-800 bg-white" type="text" value="9.5"/>
</td>
<td class="py-1.5 px-2 text-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-semibold rounded border border-slate-300 focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] text-slate-800 bg-white" type="text" value="9.0"/>
</td>
<td class="py-1.5 px-2 text-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-semibold rounded border border-slate-300 focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] text-slate-800 bg-white" type="text" value="9.5"/>
</td>
<td class="py-2.5 px-3 text-center font-bold text-slate-900 bg-slate-50/60">9.5</td>
<td class="py-2.5 px-2 text-center font-bold text-emerald-700">A</td>
<td class="py-2.5 px-3 text-center">
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
<span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                    Äáº¡t
                  </span>
</td>
<td class="py-2.5 px-3 text-slate-500 text-[11px]">BÃ¡o cÃ¡o Ä‘á» tÃ i tá»‘t</td>
</tr>
<!-- DÃ²ng 7: HoÃ ng Minh KhÃ´i -->
<tr class="hover:bg-blue-50/30 transition-colors">
<td class="py-2.5 px-3 text-center text-slate-500 font-medium sticky left-0 bg-white z-10 border-r border-slate-200">7</td>
<td class="py-2.5 px-3 font-mono font-semibold text-[#1c55c4] sticky left-12 bg-white z-10 border-r border-slate-200">2001210098</td>
<td class="py-2.5 px-4 font-medium text-slate-900 sticky left-40 bg-white z-10 border-r border-slate-200 shadow-[2px_0_4px_rgba(0,0,0,0.03)] whitespace-nowrap">HoÃ ng Minh KhÃ´i</td>
<td class="py-2.5 px-3 text-center text-slate-600 font-mono">12DHTH02</td>
<td class="py-1.5 px-2 text-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-semibold rounded border border-slate-300 focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] text-slate-800 bg-white" type="text" value="8.0"/>
</td>
<td class="py-1.5 px-2 text-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-semibold rounded border border-slate-300 focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] text-slate-800 bg-white" type="text" value="6.0"/>
</td>
<td class="py-1.5 px-2 text-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-semibold rounded border border-slate-300 focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] text-slate-800 bg-white" type="text" value="6.5"/>
</td>
<td class="py-1.5 px-2 text-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-semibold rounded border border-slate-300 focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] text-slate-800 bg-white" type="text" value="5.5"/>
</td>
<td class="py-2.5 px-3 text-center font-bold text-slate-900 bg-slate-50/60">6.1</td>
<td class="py-2.5 px-2 text-center font-bold text-amber-700">C</td>
<td class="py-2.5 px-3 text-center">
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
<span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                    Äáº¡t
                  </span>
</td>
<td class="py-2.5 px-3 text-slate-500 italic text-[11px]">â€”</td>
</tr>
<!-- DÃ²ng 8: LÃ½ ThÃ nh Long -->
<tr class="hover:bg-blue-50/30 transition-colors">
<td class="py-2.5 px-3 text-center text-slate-500 font-medium sticky left-0 bg-white z-10 border-r border-slate-200">8</td>
<td class="py-2.5 px-3 font-mono font-semibold text-[#1c55c4] sticky left-12 bg-white z-10 border-r border-slate-200">2001210512</td>
<td class="py-2.5 px-4 font-medium text-slate-900 sticky left-40 bg-white z-10 border-r border-slate-200 shadow-[2px_0_4px_rgba(0,0,0,0.03)] whitespace-nowrap">LÃ½ ThÃ nh Long</td>
<td class="py-2.5 px-3 text-center text-slate-600 font-mono">12DHTH01</td>
<td class="py-1.5 px-2 text-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-semibold rounded border border-slate-300 focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] text-slate-800 bg-white" type="text" value="9.0"/>
</td>
<td class="py-1.5 px-2 text-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-semibold rounded border border-slate-300 focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] text-slate-800 bg-white" type="text" value="7.5"/>
</td>
<td class="py-1.5 px-2 text-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-semibold rounded border border-slate-300 focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] text-slate-800 bg-white" type="text" value="7.0"/>
</td>
<td class="py-1.5 px-2 text-center">
<input aria-label="Ã” nháº­p liá»‡u" class="w-16 h-8 text-center font-semibold rounded border border-slate-300 focus:ring-1 focus:ring-[#1c55c4] focus:border-[#1c55c4] text-slate-800 bg-white" type="text" value="8.0"/>
</td>
<td class="py-2.5 px-3 text-center font-bold text-slate-900 bg-slate-50/60">7.8</td>
<td class="py-2.5 px-2 text-center font-bold text-blue-700">B</td>
<td class="py-2.5 px-3 text-center">
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
<span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                    Äáº¡t
                  </span>
</td>
<td class="py-2.5 px-3 text-slate-500 italic text-[11px]">â€”</td>
</tr>
</tbody>
</table>
</div>
<!-- DÃ’NG CHÃ‚N Báº¢NG: TÃ“M Táº®T THá»NG KÃŠ ÄIá»‚M Há»ŒC PHáº¦N -->
<div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex flex-wrap items-center justify-between gap-4 text-xs">
<!-- Chá»‰ sá»‘ tÃ³m táº¯t -->
<div class="flex flex-wrap items-center gap-6">
<div class="flex items-center gap-1.5">
<span class="text-slate-500">SÄ© sá»‘ lá»›p:</span>
<span class="font-bold text-slate-900">60 SV</span>
</div>
<div class="h-3.5 w-px bg-slate-300"></div>
<div class="flex items-center gap-1.5">
<span class="text-slate-500">ÄÃ£ nháº­p Ä‘iá»ƒm:</span>
<span class="font-bold text-emerald-700">59 / 60 SV (98.3%)</span>
</div>
<div class="h-3.5 w-px bg-slate-300"></div>
<div class="flex items-center gap-1.5">
<span class="text-slate-500">Äiá»ƒm TB toÃ n lá»›p:</span>
<span class="font-bold text-[#1c55c4]">7.45 / 10.0</span>
</div>
<div class="h-3.5 w-px bg-slate-300"></div>
<div class="flex items-center gap-1.5">
<span class="text-slate-500">Tá»‰ lá»‡ Äáº¡t:</span>
<span class="font-bold text-emerald-700">96.6% (58 SV)</span>
</div>
<div class="h-3.5 w-px bg-slate-300"></div>
<div class="flex items-center gap-1.5">
<span class="text-slate-500">Cáº£nh bÃ¡o lá»—i:</span>
<span class="font-bold text-rose-700">01 SV</span>
</div>
</div>
<!-- PhÃ¢n trang danh sÃ¡ch -->
<div class="flex items-center gap-2">
<span class="text-slate-500">Hiá»ƒn thá»‹ 1 - 8 trong 60 sinh viÃªn</span>
<div class="inline-flex rounded-md shadow-2xs">
<button class="px-2.5 py-1 text-slate-500 bg-white border border-slate-300 rounded-l-md hover:bg-slate-50" disabled="">
<span class="material-symbols-outlined text-[16px]">chevron_left</span>
</button>
<button class="px-3 py-1 font-semibold text-white bg-[#1c55c4] border border-[#1c55c4]">1</button>
<button class="px-3 py-1 text-slate-700 bg-white border-y border-slate-300 hover:bg-slate-50">2</button>
<button class="px-3 py-1 text-slate-700 bg-white border-y border-slate-300 hover:bg-slate-50">3</button>
<span class="px-2 py-1 bg-white border-y border-slate-300 text-slate-500">...</span>
<button class="px-3 py-1 text-slate-700 bg-white border-y border-slate-300 hover:bg-slate-50">8</button>
<button class="px-2.5 py-1 text-slate-600 bg-white border border-slate-300 rounded-r-md hover:bg-slate-50">
<span class="material-symbols-outlined text-[16px]">chevron_right</span>
</button>
</div>
</div>
</div>
</section>
<!-- Footer báº£n quyá»n HUIT -->
<footer class="pt-2 pb-6 text-center text-xs text-slate-500">
<p>Â© 2026 TrÆ°á»ng Äáº¡i há»c CÃ´ng ThÆ°Æ¡ng TP. Há»“ ChÃ­ Minh (HUIT) â€¢ Cá»•ng ThÃ´ng tin Giáº£ng viÃªn &amp; Quáº£n lÃ½ Há»c vá»¥.</p>
<p class="mt-1 text-slate-500">Há»— trá»£ ká»¹ thuáº­t nháº­p Ä‘iá»ƒm: PhÃ²ng ÄÃ o táº¡o (028.38161673 - nguoidung025@example.test)</p>
</footer>
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

