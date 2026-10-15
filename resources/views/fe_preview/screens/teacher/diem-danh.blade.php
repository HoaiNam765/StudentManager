@extends('fe_preview.layouts.teacher')
@section('page_title', 'Äiá»ƒm danh sinh viÃªn theo buá»•i - HUIT Há»c Vá»¥')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'selection:bg-blue-100', 'selection:text-blue-900']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Äiá»ƒm danh sinh viÃªn theo buá»•i - HUIT Há»c Vá»¥</title>
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
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/lich-coi-thi']) }}">
<span class="material-symbols-outlined text-[19px] text-slate-500 group-hover:text-slate-600">event_upcoming</span>
<span class="truncate">Lá»‹ch coi thi</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-semibold bg-blue-50 text-primary border-r-4 border-primary transition-colors" href="{{ route('fe.preview.show', ['screen' => 'teacher/diem-danh']) }}">
<span class="material-symbols-outlined text-[19px] text-primary">check_circle</span>
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
<h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">Äiá»ƒm danh sinh viÃªn theo buá»•i</h1>
<span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-primary border border-blue-100">
              Há»c ká»³ 1 â€¢ 2026-2027
            </span>
</div>
<div class="text-xs text-slate-500 hidden sm:block">Trang chá»§ / Cá»•ng Giáº£ng viÃªn / Äiá»ƒm danh</div>
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
<div class="space-y-4 max-w-4xl mx-auto">
<!-- Controls: Select Class & Session -->
<div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs space-y-3">
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
<div>
<label class="block text-xs font-semibold text-slate-700 mb-1" for="class-select">Lá»›p há»c pháº§n</label>
<select aria-label="241_71ITEC3201_01 - Láº­p trÃ¬nh Java (Thá»© Hai, Ca 1)" class="w-full h-10 px-3 rounded-lg border border-slate-300 text-xs font-medium text-slate-800 bg-slate-50" id="class-select">
<option>241_71ITEC3201_01 - Láº­p trÃ¬nh Java (Thá»© Hai, Ca 1)</option>
<option>241_71ITEC2202_02 - CÆ¡ sá»Ÿ dá»¯ liá»‡u nÃ¢ng cao (Thá»© NÄƒm, Ca 2)</option>
</select>
</div>
<div>
<label class="block text-xs font-semibold text-slate-700 mb-1" for="session-select">Buá»•i Ä‘iá»ƒm danh</label>
<select aria-label="Buá»•i 7 â€¢ HÃ´m nay (12/10/2026) - Äang má»Ÿ" class="w-full h-10 px-3 rounded-lg border border-slate-300 text-xs font-medium text-slate-800 bg-slate-50" id="session-select">
<option value="current">Buá»•i 7 â€¢ HÃ´m nay (12/10/2026) - Äang má»Ÿ</option>
<option value="locked">Buá»•i 6 â€¢ 05/10/2026 (ÄÃ£ chá»‘t khÃ³a sá»•)</option>
<option value="locked">Buá»•i 5 â€¢ 28/09/2026 (ÄÃ£ chá»‘t khÃ³a sá»•)</option>
</select>
</div>
</div>
<!-- Quick Actions Bar & Live Stats -->
<div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-slate-100">
<div class="flex items-center gap-2 text-xs font-bold">
<span class="px-2 py-1 rounded bg-emerald-100 text-emerald-800">CÃ³ máº·t: <span id="count-present">38</span></span>
<span class="px-2 py-1 rounded bg-blue-100 text-blue-800">CÃ³ phÃ©p: <span id="count-excused">2</span></span>
<span class="px-2 py-1 rounded bg-red-100 text-red-800">Váº¯ng: <span id="count-absent">1</span></span>
<span class="text-slate-500">/ 41 SV</span>
</div>
<div class="flex items-center gap-2">
<button class="h-9 px-3 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-300 font-bold text-xs flex items-center gap-1 transition-colors" onclick="markAllPresent()" type="button">
<span class="material-symbols-outlined text-[16px]">done_all</span>
<span>Táº¥t cáº£ cÃ³ máº·t</span>
</button>
<button class="h-9 px-4 rounded-lg bg-primary hover:bg-primary-hover text-white font-bold text-xs flex items-center gap-1 shadow-xs transition-colors" onclick="saveAttendance()" type="button">
<span class="material-symbols-outlined text-[16px]">save</span>
<span>LÆ°u Ä‘iá»ƒm danh</span>
</button>
</div>
</div>
</div>
<!-- Student Attendance List (Touch-optimized 44px+ buttons) -->
<div class="bg-white rounded-xl border border-slate-200 shadow-xs divide-y divide-slate-100 overflow-hidden">
<!-- Student 1 -->
<div class="p-3.5 sm:p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50 transition-colors">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-full bg-blue-100 text-primary font-bold text-xs flex items-center justify-center shrink-0 border border-blue-200">
          01
        </div>
<div>
<div class="font-bold text-slate-900 text-sm">Nguyá»…n VÄƒn An</div>
<div class="text-xs text-slate-500 font-mono">2001210123 â€¢ Lá»›p 12DHTH01</div>
</div>
</div>
<!-- One-touch 3 buttons -->
<div class="flex items-center gap-1.5 self-end sm:self-auto w-full sm:w-auto">
<button class="flex-1 sm:flex-none min-h-[44px] px-3.5 rounded-lg font-bold text-xs border border-emerald-500 bg-emerald-600 text-white flex items-center justify-center gap-1 transition-all" onclick="setStatus(this, 'present')" type="button">
<span class="material-symbols-outlined text-[18px]">check</span>
<span>CÃ³ máº·t</span>
</button>
<button class="flex-1 sm:flex-none min-h-[44px] px-3 rounded-lg font-bold text-xs border border-slate-300 bg-white text-slate-600 hover:bg-blue-50 flex items-center justify-center gap-1 transition-all" onclick="setStatus(this, 'excused')" type="button">
<span>CÃ³ phÃ©p</span>
</button>
<button class="flex-1 sm:flex-none min-h-[44px] px-3 rounded-lg font-bold text-xs border border-slate-300 bg-white text-slate-600 hover:bg-red-50 flex items-center justify-center gap-1 transition-all" onclick="setStatus(this, 'absent')" type="button">
<span>Váº¯ng</span>
</button>
</div>
</div>
<!-- Student 2 -->
<div class="p-3.5 sm:p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50 transition-colors">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-full bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0 border border-slate-200">
          02
        </div>
<div>
<div class="font-bold text-slate-900 text-sm">Tráº§n Thá»‹ BÃ­ch</div>
<div class="text-xs text-slate-500 font-mono">2001210124 â€¢ Lá»›p 12DHTH01</div>
</div>
</div>
<div class="flex items-center gap-1.5 self-end sm:self-auto w-full sm:w-auto">
<button class="flex-1 sm:flex-none min-h-[44px] px-3.5 rounded-lg font-bold text-xs border border-emerald-500 bg-emerald-600 text-white flex items-center justify-center gap-1 transition-all" onclick="setStatus(this, 'present')" type="button">
<span class="material-symbols-outlined text-[18px]">check</span>
<span>CÃ³ máº·t</span>
</button>
<button class="flex-1 sm:flex-none min-h-[44px] px-3 rounded-lg font-bold text-xs border border-slate-300 bg-white text-slate-600 hover:bg-blue-50 flex items-center justify-center gap-1 transition-all" onclick="setStatus(this, 'excused')" type="button">
<span>CÃ³ phÃ©p</span>
</button>
<button class="flex-1 sm:flex-none min-h-[44px] px-3 rounded-lg font-bold text-xs border border-slate-300 bg-white text-slate-600 hover:bg-red-50 flex items-center justify-center gap-1 transition-all" onclick="setStatus(this, 'absent')" type="button">
<span>Váº¯ng</span>
</button>
</div>
</div>
<!-- Student 3 (Absent with excuse) -->
<div class="p-3.5 sm:p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50 transition-colors bg-blue-50/20">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 font-bold text-xs flex items-center justify-center shrink-0 border border-blue-200">
          03
        </div>
<div>
<div class="font-bold text-slate-900 text-sm">LÃª HoÃ ng CÆ°á»ng</div>
<div class="text-xs text-blue-600 font-medium">CÃ³ Ä‘Æ¡n xin nghá»‰ phÃ©p qua há»‡ thá»‘ng</div>
</div>
</div>
<div class="flex items-center gap-1.5 self-end sm:self-auto w-full sm:w-auto">
<button class="flex-1 sm:flex-none min-h-[44px] px-3.5 rounded-lg font-bold text-xs border border-slate-300 bg-white text-slate-600 hover:bg-emerald-50 flex items-center justify-center gap-1 transition-all" onclick="setStatus(this, 'present')" type="button">
<span class="material-symbols-outlined text-[18px]">check</span>
<span>CÃ³ máº·t</span>
</button>
<button class="flex-1 sm:flex-none min-h-[44px] px-3 rounded-lg font-bold text-xs border border-blue-500 bg-blue-600 text-white flex items-center justify-center gap-1 transition-all" onclick="setStatus(this, 'excused')" type="button">
<span>CÃ³ phÃ©p</span>
</button>
<button class="flex-1 sm:flex-none min-h-[44px] px-3 rounded-lg font-bold text-xs border border-slate-300 bg-white text-slate-600 hover:bg-red-50 flex items-center justify-center gap-1 transition-all" onclick="setStatus(this, 'absent')" type="button">
<span>Váº¯ng</span>
</button>
</div>
</div>
</div>
<!-- Locked Session Warning Dialog -->
<div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden" id="lockedModal">
<div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
<div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center mx-auto">
<span class="material-symbols-outlined text-[28px]">lock_clock</span>
</div>
<div class="text-center space-y-1">
<h3 class="text-base font-bold text-slate-900">XÃ¡c nháº­n sá»­a buá»•i há»c Ä‘Ã£ chá»‘t sá»•?</h3>
<p class="text-xs text-slate-600 leading-relaxed">
          Buá»•i há»c ngÃ y <strong>05/10/2026</strong> Ä‘Ã£ Ä‘Æ°á»£c chá»‘t sá»• chuyÃªn cáº§n. Viá»‡c Ä‘iá»u chá»‰nh dá»¯ liá»‡u sáº½ <strong>ghi láº¡i nháº­t kÃ½ kiá»ƒm toÃ¡n</strong> cá»§a giáº£ng viÃªn vÃ  gá»­i thÃ´ng bÃ¡o tá»›i PhÃ²ng ÄÃ o táº¡o.
        </p>
</div>
<div class="pt-2 flex items-center gap-3">
<button class="flex-1 h-10 rounded-lg border border-slate-300 hover:bg-slate-50 text-xs font-semibold text-slate-700 transition-colors" onclick="closeLockedModal()" type="button">
          Há»§y bá»
        </button>
<button class="flex-1 h-10 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition-colors" onclick="confirmUnlock()" type="button">
          Má»Ÿ khÃ³a &amp; Chá»‰nh sá»­a
        </button>
</div>
</div>
</div>
</div>
<script>
  function setStatus(btn, status) {
    const parent = btn.parentElement;
    const buttons = parent.querySelectorAll('button');
    buttons.forEach(b => {
      b.className = 'flex-1 sm:flex-none min-h-[44px] px-3 rounded-lg font-bold text-xs border border-slate-300 bg-white text-slate-600 hover:bg-slate-50 flex items-center justify-center gap-1 transition-all';
    });
    if (status === 'present') {
      btn.className = 'flex-1 sm:flex-none min-h-[44px] px-3.5 rounded-lg font-bold text-xs border border-emerald-500 bg-emerald-600 text-white flex items-center justify-center gap-1 transition-all';
    } else if (status === 'excused') {
      btn.className = 'flex-1 sm:flex-none min-h-[44px] px-3 rounded-lg font-bold text-xs border border-blue-500 bg-blue-600 text-white flex items-center justify-center gap-1 transition-all';
    } else {
      btn.className = 'flex-1 sm:flex-none min-h-[44px] px-3 rounded-lg font-bold text-xs border border-red-500 bg-red-600 text-white flex items-center justify-center gap-1 transition-all';
    }
  }

  function markAllPresent() {
    document.querySelectorAll('.divide-y > div').forEach(row => {
      const btn = row.querySelector('button');
      if (btn) setStatus(btn, 'present');
    });
    alert('ÄÃ£ Ä‘Ã¡nh dáº¥u toÃ n bá»™ sinh viÃªn cÃ³ máº·t!');
  }

  function saveAttendance() {
    const sel = document.getElementById('session-select');
    if (sel.value === 'locked') {
      document.getElementById('lockedModal').classList.remove('hidden');
    } else {
      alert('ÄÃ£ lÆ°u thÃ nh cÃ´ng dá»¯ liá»‡u Ä‘iá»ƒm danh buá»•i há»c!');
    }
  }

  function closeLockedModal() {
    document.getElementById('lockedModal').classList.add('hidden');
  }

  function confirmUnlock() {
    closeLockedModal();
    alert('ÄÃ£ má»Ÿ khÃ³a phiÃªn Ä‘iá»ƒm danh! Má»i thao tÃ¡c cáº­p nháº­t Ä‘Ã£ Ä‘Æ°á»£c ghi log.');
  }
</script>
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

