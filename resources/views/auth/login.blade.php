<!DOCTYPE html><html class="light" lang="vi"><head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>Đăng nhập - Trường Đại học Công Thương TP. Hồ Chí Minh - Cổng Thông tin Học vụ</title>
<link href="https://fonts.googleapis.com" rel="preconnect">
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          "colors": {
            "primary-fixed": "#dae2ff",
            "outline-variant": "#c3c6d5",
            "on-primary-fixed": "#001849",
            "primary-container": "#3f6fde",
            "on-tertiary-fixed": "#001e2c",
            "tertiary-fixed": "#c4e7ff",
            "error": "#ba1a1a",
            "surface": "#f8f9ff",
            "on-surface-variant": "#434653",
            "inverse-primary": "#b2c5ff",
            "surface-variant": "#d3e4fe",
            "on-primary": "#ffffff",
            "on-secondary-fixed": "#131b2e",
            "on-primary-fixed-variant": "#003fa3",
            "primary": "#1c55c4",
            "on-tertiary-fixed-variant": "#004c69",
            "on-error-container": "#93000a",
            "surface-container-highest": "#d3e4fe",
            "on-tertiary-container": "#fcfcff",
            "surface-tint": "#2057c6",
            "on-secondary": "#ffffff",
            "tertiary": "#006387",
            "on-surface": "#0b1c30",
            "on-secondary-container": "#5c647a",
            "background": "#f8f9ff",
            "surface-dim": "#cbdbf5",
            "on-tertiary": "#ffffff",
            "on-secondary-fixed-variant": "#3f465c",
            "secondary-fixed": "#dae2fd",
            "inverse-on-surface": "#eaf1ff",
            "on-primary-container": "#fefcff",
            "on-background": "#0b1c30",
            "outline": "#737685",
            "surface-container-high": "#dce9ff",
            "primary-fixed-dim": "#b2c5ff",
            "error-container": "#ffdad6",
            "tertiary-container": "#007da9",
            "inverse-surface": "#213145",
            "secondary-fixed-dim": "#bec6e0",
            "on-error": "#ffffff",
            "surface-container": "#e5eeff",
            "secondary": "#565e74",
            "surface-container-low": "#eff4ff",
            "surface-bright": "#f8f9ff",
            "secondary-container": "#dae2fd",
            "surface-container-lowest": "#ffffff",
            "tertiary-fixed-dim": "#7bd0ff"
          },
          "borderRadius": {
            "DEFAULT": "0.25rem",
            "lg": "0.5rem",
            "xl": "0.75rem",
            "full": "9999px"
          },
          "spacing": {
            "gutter-mobile": "1rem",
            "space-xl": "2rem",
            "space-md": "1rem",
            "space-lg": "1.5rem",
            "gutter": "1.5rem",
            "margin-mobile": "1rem",
            "margin": "2rem",
            "space-sm": "0.5rem",
            "space-xs": "0.25rem"
          },
          "fontFamily": {
            "label-md": ["Inter"],
            "headline-md": ["Inter"],
            "body-sm": ["Inter"],
            "headline-xl-mobile": ["Inter"],
            "label-lg": ["Inter"],
            "headline-xl": ["Inter"],
            "display-lg": ["Inter"],
            "headline-sm": ["Inter"],
            "display-lg-mobile": ["Inter"],
            "label-sm": ["Inter"],
            "body-md": ["Inter"],
            "headline-lg": ["Inter"],
            "body-lg": ["Inter"]
          },
          "fontSize": {
            "label-md": ["12px", { "lineHeight": "16px", "letterSpacing": "0.02em", "fontWeight": "600" }],
            "headline-md": ["20px", { "lineHeight": "28px", "letterSpacing": "-0.005em", "fontWeight": "600" }],
            "body-sm": ["12px", { "lineHeight": "18px", "fontWeight": "400" }],
            "headline-xl-mobile": ["24px", { "lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "600" }],
            "label-lg": ["14px", { "lineHeight": "20px", "fontWeight": "600" }],
            "headline-xl": ["30px", { "lineHeight": "38px", "letterSpacing": "-0.015em", "fontWeight": "600" }],
            "display-lg": ["36px", { "lineHeight": "44px", "letterSpacing": "-0.02em", "fontWeight": "700" }],
            "headline-sm": ["16px", { "lineHeight": "24px", "fontWeight": "600" }],
            "display-lg-mobile": ["28px", { "lineHeight": "36px", "letterSpacing": "-0.015em", "fontWeight": "700" }],
            "label-sm": ["11px", { "lineHeight": "14px", "letterSpacing": "0.03em", "fontWeight": "500" }],
            "body-md": ["14px", { "lineHeight": "20px", "fontWeight": "400" }],
            "headline-lg": ["24px", { "lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "600" }],
            "body-lg": ["16px", { "lineHeight": "24px", "fontWeight": "400" }]
          }
        }
      }
    }
  </script>
<style>
    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
      display: inline-block;
      vertical-align: middle;
      line-height: 1;
    }
    .custom-shadow {
      box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.04), 0 1px 2px -1px rgba(15, 23, 42, 0.02);
    }
  </style>
</head>
<body class="bg-surface text-on-surface antialiased min-h-screen flex flex-col font-body-md text-body-md selection:bg-primary-container selection:text-on-primary-container">
<!-- TopNavBar (Shared Component Anchor) -->
<header class="bg-surface border-b border-outline-variant shadow-sm w-full top-0 left-0 transition-all duration-150 ease-in-out">
<div class="flex justify-between items-center w-full px-space-md lg:px-space-xl py-space-sm h-16">
<!-- Brand Anchor -->
<div class="flex items-center gap-space-sm">
<div class="w-9 h-9 rounded-lg bg-primary flex items-center justify-center text-on-primary shadow-sm">
<span class="material-symbols-outlined text-[20px]" data-icon="school">school</span>
</div>
<div class="flex flex-col">
<span class="text-headline-sm font-headline-sm text-primary tracking-tight leading-tight">Trường Đại học Công Thương TP. Hồ Chí Minh - Cổng Thông tin Học vụ</span>
<span class="text-label-sm font-label-sm text-on-surface-variant hidden sm:inline-block">
            Cổng quản lý và đào tạo tín chỉ chính quy
          </span>
</div>
</div>
<!-- Navigation & Trailing Action -->
<div class="flex items-center gap-space-lg">
<nav class="hidden md:flex items-center gap-space-md">
<a class="text-on-surface-variant hover:text-on-surface text-label-md font-label-md transition-colors duration-150" href="#">Trang chủ</a>
<a class="text-primary font-semibold border-b-2 border-primary pb-1 text-label-md font-label-md transition-colors duration-150" href="#">Hướng dẫn đăng nhập</a>
<a class="text-on-surface-variant hover:text-on-surface text-label-md font-label-md transition-colors duration-150" href="#">Hỗ trợ kỹ thuật</a>
</nav>
<div class="flex items-center gap-space-xs text-on-surface-variant text-label-md font-label-md border-l border-outline-variant pl-space-md">
<span class="material-symbols-outlined text-[18px] text-primary" data-icon="language">language</span>
<span class="hover:text-primary transition-colors duration-150 cursor-pointer">Ngôn ngữ: Tiếng Việt</span>
</div>
</div>
</div>
</header>
<!-- Main Workspace (Centered Login Card) -->
<main class="flex-1 flex items-center justify-center p-space-md md:p-space-xl">
<div class="w-full max-w-4xl bg-surface-container-lowest rounded-2xl border border-outline-variant overflow-hidden custom-shadow grid grid-cols-1 md:grid-cols-12 min-h-[580px]">
<!-- LEFT BRANDED PANEL (Col-span 5, ~42% width) -->
<div class="md:col-span-5 bg-[#4776E6] text-white p-space-lg md:p-10 flex flex-col justify-between relative overflow-hidden">
<!-- Subtle Academic Geometry Pattern Backdrop -->
<div class="absolute -right-12 -top-12 w-48 h-48 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
<div class="absolute -left-12 -bottom-12 w-48 h-48 bg-black/10 rounded-full blur-2xl pointer-events-none"></div>
<!-- Top & Middle Content -->
<div class="relative z-10">
<!-- University Badge Motif -->
<div class="bg-white/10 border border-white/20 rounded-xl p-3 inline-flex items-center justify-center w-fit mb-6 shadow-sm">
<span class="material-symbols-outlined text-3xl text-white" data-icon="auto_stories">auto_stories</span>
</div>
<!-- Titles -->
<h1 class="text-headline-xl font-headline-xl text-white tracking-tight leading-snug">
            Hệ thống Quản lý Học vụ
          </h1>
<p class="text-blue-100 text-body-md font-body-md mt-2 leading-relaxed">
            Cổng thông tin học vụ
          </p>
<!-- Integration Highlight Pill -->
<div class="mt-8 bg-white/10 border border-white/15 rounded-xl p-4 backdrop-blur-sm">
<div class="flex items-center gap-2 mb-2">
<span class="material-symbols-outlined text-white text-[18px]" data-icon="verified_user">verified_user</span>
<span class="text-label-md font-label-md text-white font-semibold">Cổng tích hợp xác thực</span>
</div>
<p class="text-blue-100 text-body-sm font-body-sm leading-normal">Dành cho Sinh viên, Giảng viên &amp; Cán bộ quản lý đào tạo Trường Đại học Công Thương.</p>
</div>
<!-- Feature Bullets -->
<ul class="mt-6 space-y-2.5 text-body-sm font-body-sm text-blue-100">
<li class="flex items-center gap-2">
<span class="material-symbols-outlined text-emerald-300 text-[16px]" data-icon="check_circle">check_circle</span>
<span class="">Đăng ký học phần &amp; Tra cứu thời khóa biểu</span>
</li>
<li class="flex items-center gap-2">
<span class="material-symbols-outlined text-emerald-300 text-[16px]" data-icon="check_circle">check_circle</span>
<span class="">Theo dõi tiến độ tích lũy &amp; Bảng điểm học tập</span>
</li>
<li class="flex items-center gap-2">
<span class="material-symbols-outlined text-emerald-300 text-[16px]" data-icon="check_circle">check_circle</span>
<span class="">Quản lý học phí và các thủ tục hành chính</span>
</li>
</ul>
</div>
<!-- Bottom University Identifier -->
<div class="relative z-10 pt-6 mt-6 border-t border-white/20 flex items-center justify-between text-blue-100 text-label-md font-label-md">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[18px]" data-icon="account_balance">account_balance</span>
<span class="font-medium tracking-wide">Trường Đại học Công Thương</span>
</div>
<span class="text-label-sm font-label-sm text-blue-200">HUIT</span>
</div>
</div>
<!-- RIGHT LOGIN FORM PANEL (Col-span 7, ~58% width) -->
<div class="md:col-span-7 bg-surface-container-lowest p-space-lg md:p-12 flex flex-col justify-center">
<div class="w-full max-w-md mx-auto">
<!-- Heading -->
<div class="mb-8">
<h2 class="text-display-lg-mobile md:text-headline-xl font-headline-xl text-on-surface tracking-tight">
              Đăng nhập
            </h2>
<p class="text-body-md font-body-md text-on-surface-variant mt-1.5">
              Nhập thông tin tài khoản của bạn để tiếp tục
            </p>
</div>
<!-- Form Element -->
<form class="space-y-5" method="POST" action="{{ route('login.store') }}">
@csrf
@if (session('status'))
<p class="rounded-lg bg-emerald-50 border border-emerald-200 px-3.5 py-2.5 text-body-md text-emerald-800" role="status">{{ session('status') }}</p>
@endif
@if ($errors->any())
<p class="rounded-lg bg-error-container border border-error/30 px-3.5 py-2.5 text-body-md text-on-error-container" role="alert">{{ $errors->first('login') ?: $errors->first() }}</p>
@endif
<!-- Username Input -->
<div>
<label class="block text-body-md font-label-lg text-on-surface mb-1.5" for="login">
                Tên đăng nhập
              </label>
<div class="relative">
<div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-outline">
<span class="material-symbols-outlined text-[20px]" data-icon="person">person</span>
</div>
<input autocomplete="username" class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg pl-10 pr-3.5 py-2.5 text-body-md font-body-md text-on-surface placeholder:text-outline focus:border-[#4776E6] focus:ring-2 focus:ring-[#4776E6]/20 transition-all outline-none" id="login" name="login" value="{{ old('login') }}" placeholder="Nhập tên đăng nhập / email" required autofocus type="text">
</div>
<p class="text-label-sm font-label-sm text-outline mt-1">Ví dụ: tên đăng nhập hoặc canbo@huit.edu.vn</p>
</div>
<!-- Password Input -->
<div>
<div class="flex items-center justify-between mb-1.5">
<label class="text-body-md font-label-lg text-on-surface" for="password">
                  Mật khẩu
                </label>
</div>
<div class="relative">
<div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-outline">
<span class="material-symbols-outlined text-[20px]" data-icon="lock">lock</span>
</div>
<input autocomplete="current-password" class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg pl-10 pr-10 py-2.5 text-body-md font-body-md text-on-surface placeholder:text-outline focus:border-[#4776E6] focus:ring-2 focus:ring-[#4776E6]/20 transition-all outline-none" id="password" name="password" placeholder="••••••••" required="" type="password">
<button aria-label="Hiện hoặc ẩn mật khẩu" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-outline hover:text-on-surface transition-colors cursor-pointer" id="togglePassword" type="button">
<span class="material-symbols-outlined text-[20px]" data-icon="visibility" id="eyeIcon">visibility</span>
</button>
</div>
</div>
<!-- Options Row (Remember me & Forgot password) -->
<div class="flex items-center justify-between pt-1">
<label class="flex items-center gap-2 cursor-pointer select-none">
<input class="w-4 h-4 rounded border-outline-variant text-[#4776E6] focus:ring-[#4776E6]/20 focus:ring-offset-0 cursor-pointer" name="remember" value="1" type="checkbox" @checked(old('remember'))>
<span class="text-body-sm font-body-sm text-on-surface-variant">Ghi nhớ đăng nhập</span>
</label>
<a class="text-body-sm font-label-md text-[#4776E6] hover:underline cursor-pointer" href="#">
                Quên mật khẩu?
              </a>
</div>
<!-- Submit Button -->
<button class="w-full h-11 bg-[#4776E6] hover:bg-[#3b63c7] text-white font-label-lg text-label-lg rounded-xl shadow-sm transition-colors flex items-center justify-center gap-2 cursor-pointer active:bg-[#3154AF] mt-6" type="submit">
<span class="">Đăng nhập</span>
<span class="material-symbols-outlined text-[18px]" data-icon="arrow_forward">arrow_forward</span>
</button>
</form>
<!-- Security & Advisory Note -->
<div class="mt-8 pt-6 border-t border-outline-variant/60">
<div class="flex items-start gap-2 justify-center text-center">
<span class="material-symbols-outlined text-tertiary text-[18px] shrink-0 mt-0.5" data-icon="info">info</span>
<p class="text-label-md font-label-md text-on-surface-variant">
                Hệ thống chỉ dành cho cán bộ, giảng viên và sinh viên của trường.
              </p>
</div>
<p class="text-label-sm font-label-sm text-outline text-center mt-2">
              Hỗ trợ kỹ thuật: <a class="text-primary hover:underline" href="mailto:support@huit.edu.vn">support@huit.edu.vn</a> | Hotline: <span class="font-medium text-on-surface-variant">024.3754.7461</span>
</p>
</div>
</div>
</div>
</div>
</main>
<!-- Footer (Shared Component Anchor) -->
<footer class="bg-surface-container-lowest border-t border-outline-variant w-full bottom-0 left-0 transition-opacity duration-150 ease-in-out">
<div class="flex flex-col md:flex-row justify-between items-center w-full px-space-md lg:px-space-xl py-space-sm gap-space-xs">
<span class="text-label-sm font-label-sm text-on-surface-variant text-center md:text-left">© 2024 Trường Đại học Công Thương TP. Hồ Chí Minh. Hệ thống Quản lý Học vụ. Bảo lưu mọi quyền.</span>
<div class="flex items-center gap-space-md">
<a class="text-label-sm font-label-sm text-on-surface-variant hover:text-primary underline transition-colors duration-150" href="#">Quy chế đào tạo</a>
<a class="text-label-sm font-label-sm text-on-surface-variant hover:text-primary underline transition-colors duration-150" href="#">Chính sách bảo mật</a>
<a class="text-label-sm font-label-sm text-on-surface-variant hover:text-primary underline transition-colors duration-150" href="#">Liên hệ hỗ trợ</a>
</div>
</div>
</footer>
<!-- Micro-interactions Script -->
<script>
    const togglePasswordBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    if (togglePasswordBtn && passwordInput && eyeIcon) {
      togglePasswordBtn.addEventListener('click', function () {
        const isPassword = passwordInput.getAttribute('type') === 'password';
        passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
        eyeIcon.textContent = isPassword ? 'visibility_off' : 'visibility';
        eyeIcon.setAttribute('data-icon', isPassword ? 'visibility_off' : 'visibility');
      });
    }
  </script>


</body></html>