@extends('layouts.app')
@section('title', 'FE Preview — 88 Stitch screens')
@section('content')
<main class="mx-auto max-w-6xl px-6 py-10">
    <h1 class="text-3xl font-bold">StitchFE — Local mock routes</h1>
    <p class="mt-2 text-sm text-slate-600">Chỉ để kiểm tra UI. Không dùng các route này để xử lý nghiệp vụ hay phân quyền.</p>
    <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($screens as $screen)
        <a class="rounded-xl border bg-white p-4 hover:border-blue-500 hover:text-blue-700" href="{{ route($screen['route']) }}">
            <span class="text-xs uppercase text-slate-500">{{ $screen['portal'] }}</span>
            <div class="mt-1 font-semibold">{{ $screen['key'] }}</div>
            <div class="text-xs text-slate-500">{{ $screen['route'] }}</div>
        </a>
        @endforeach
    </div>
</main>
@endsection
