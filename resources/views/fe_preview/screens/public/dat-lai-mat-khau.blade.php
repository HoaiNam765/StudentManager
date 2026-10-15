@extends('fe_preview.layouts.guest')
@section('page_title', 'Đặt lại mật khẩu – Hệ thống Quản lý Học vụ')
@section('html_attributes')lang="vi"@endsection
@section('body_attributes')@endsection
@section('head')
<meta charset="utf-8"/><meta content="width=device-width,initial-scale=1" name="viewport"/><title>Đặt lại mật khẩu – Hệ thống Quản lý Học vụ</title><link href="{{ asset('ui/shared.css') }}" rel="stylesheet"/><link href="https://fonts.googleapis.com" rel="preconnect"/><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/><style id="ui-overflow-guard">html,body{max-width:100%;} img,svg,video{max-width:100%;height:auto} :focus-visible{outline:3px solid #e49b15;outline-offset:2px}</style>
@endsection
@section('content')
<main style="max-width:480px;margin:8vh auto;padding:20px"><div class="brand">Trường Đại học Công Thương</div><section class="card"><h1>Đặt lại mật khẩu</h1><p class="muted">Nhập mật khẩu mới cho tài khoản của bạn.</p><form onsubmit="event.preventDefault();document.getElementById('msg').hidden=false"><label class="field">Mật khẩu mới<input name="password" required="" type="password"/></label><label class="field">Xác nhận mật khẩu mới<input name="confirm" required="" type="password"/></label><button type="submit">Tiếp tục</button><p hidden="" id="msg" role="status">Đây là bản thiết kế giao diện; chức năng gửi email sẽ được kết nối ở Laravel.</p></form><p><a href="{{ route('fe.preview.show', ['screen' => 'public/dang-nhap']) }}">Quay lại đăng nhập</a></p></section></main>
@endsection
