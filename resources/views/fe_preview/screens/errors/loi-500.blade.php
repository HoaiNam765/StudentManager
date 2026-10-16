@extends('fe_preview.layouts.base')
@section('page_title', '500 – Hệ thống gặp sự cố – Hệ thống Quản lý Học vụ')
@section('html_attributes')lang="vi"@endsection
@section('body_attributes')@endsection
@section('head')
<meta charset="utf-8"/><meta content="width=device-width,initial-scale=1" name="viewport"/><title>500 – Hệ thống gặp sự cố – Hệ thống Quản lý Học vụ</title><link href="{{ asset('fe-preview/shared.css') }}" rel="stylesheet"/><link href="https://fonts.googleapis.com" rel="preconnect"/><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/><style id="ui-overflow-guard">html,body{max-width:100%;} img,svg,video{max-width:100%;height:auto} :focus-visible{outline:3px solid #e49b15;outline-offset:2px}</style>
@endsection
@section('content')
<main style="max-width:620px;margin:12vh auto;padding:18px;text-align:center"><section class="card"><div style="font-size:72px;color:#4776e6;font-weight:700">500</div><h1>Hệ thống gặp sự cố</h1><p class="muted">Đã xảy ra lỗi ngoài dự kiến. Vui lòng thử lại sau.</p><a class="btn" href="{{ route('fe.preview.show', ['screen' => 'student/tong-quan']) }}">Về trang chủ</a></section></main>
@endsection
