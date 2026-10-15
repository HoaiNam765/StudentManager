@extends('fe_preview.layouts.guest')
@section('page_title', 'Đăng nhập một lần SSO - HUIT Học Vụ')
@section('html_attributes')lang="vi" class="['light']"@endsection
@section('body_attributes')class="['bg-[#f8f9ff]', 'text-slate-800', 'antialiased', 'min-h-screen', 'flex', 'flex-col']"@endsection
@section('head')

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Đăng nhập một lần SSO - HUIT Học Vụ</title>
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
          colors: { primary: { DEFAULT: '#1c55c4', hover: '#1645a5', tint: '#eef2fd' } }
        }
      }
    }
  </script>
<style>
    .material-symbols-outlined { font-size: 20px; line-height: 1; }
    body { font-family: 'Inter', sans-serif; }
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

<header class="h-16 bg-white border-b border-slate-200/80 px-6 flex items-center justify-between shadow-xs">
<div class="flex items-center gap-3 min-w-0 flex-1">
<div class="w-9 h-9 rounded-xl bg-primary text-white flex items-center justify-center font-bold">
<span class="material-symbols-outlined text-[20px]">verified</span>
</div>
<div>
<div class="font-bold text-sm text-slate-900">Trường Đại học Công Thương TP.HCM</div>
<div class="text-xs text-slate-500">Cổng tra cứu &amp; Xác thực văn bản điện tử</div>
</div>
</div>
<a class="text-xs font-semibold text-primary hover:underline" href="{{ route('fe.preview.show', ['screen' => 'public/dang-nhap']) }}">Đăng nhập hệ thống</a>
</header>
<main class="flex-1 p-4 sm:p-8 max-w-4xl w-full mx-auto">
    {CONTENT}
  </main>
<footer class="py-4 text-center text-xs text-slate-500 border-t border-slate-200 bg-white">
    © 2026 Trường Đại học Công Thương TP.HCM (HUIT) • Hỗ trợ: 028 3816 1673
  </footer>

@endsection
