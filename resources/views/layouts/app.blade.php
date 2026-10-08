<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="vi" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hệ thống Quản lý Học vụ - Trường ĐH Công Thương TP.HCM (HUIT)')</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    @include('partials.icon-font')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-[#F7F9FC] text-[#0B1C30] font-sans antialiased min-h-screen">
    @yield('content')
    @stack('scripts')
</body>
</html>