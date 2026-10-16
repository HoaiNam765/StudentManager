<!doctype html>
<html @yield('html_attributes')>
<head>
@yield('head')
<link rel="stylesheet" href="{{ asset('fe-preview/shared.css') }}">
</head>
<body @yield('body_attributes')>
@yield('content')
</body>
</html>
