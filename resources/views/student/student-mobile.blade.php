@extends('layouts.app')

@section('title', 'HUIT Học Vụ - Sinh viên (di động)')

@section('content')
<div class="min-h-screen bg-background pb-20">
    <div class="mx-auto w-full max-w-md">
        <!-- Header -->
        <header class="sticky top-0 z-30 h-14 bg-white border-b border-[#E2E8F0] px-4 flex items-center justify-between">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-9 h-9 rounded-lg bg-primary flex items-center justify-center text-white shrink-0">
                    <span class="material-symbols-outlined text-[20px]" aria-hidden="true">school</span>
                </div>
                <div class="min-w-0">
                    <div class="text-sm font-bold text-on-surface leading-tight truncate">HUIT Học Vụ</div>
                    <div class="text-[11px] text-on-surface-variant leading-tight">Học kỳ 1 • 2024-2025</div>
                </div>
            </div>
            @include('partials.logout-form', ['class' => 'p-2 text-on-surface-variant hover:text-error transition-colors rounded-lg'])
        </header>

        <main class="p-4 space-y-4">
            <!-- Greeting -->
            <section class="bg-white border border-[#E2E8F0] rounded-xl p-4 shadow-xs">
                <h1 class="text-xl font-bold text-on-surface tracking-tight">Xin chào, {{ $student['name'] }}</h1>
                <p class="text-xs text-on-surface-variant mt-1">MSSV {{ $student['student_id'] }} • Lớp {{ $student['class'] }}</p>
            </section>

            <!-- Summary -->
            <section class="grid grid-cols-2 gap-3" aria-label="Tóm tắt học tập">
                <div class="bg-white border border-[#E2E8F0] rounded-xl p-4 shadow-xs">
                    <div class="text-[11px] text-on-surface-variant font-medium">GPA (thang 4)</div>
                    <div class="text-2xl font-bold text-primary mt-1">{{ number_format($student['gpa_4'], 2) }}</div>
                    <div class="text-[11px] text-on-surface-variant mt-0.5">Xếp loại {{ $student['classification'] }}</div>
                </div>
                <div class="bg-white border border-[#E2E8F0] rounded-xl p-4 shadow-xs">
                    <div class="text-[11px] text-on-surface-variant font-medium">Tín chỉ tích lũy</div>
                    <div class="text-2xl font-bold text-on-surface mt-1">{{ $student['credits_earned'] }}<span class="text-sm font-medium text-on-surface-variant">/{{ $student['credits_total'] }}</span></div>
                    <div class="h-1.5 mt-2 rounded-full bg-surface-container overflow-hidden" role="progressbar" aria-valuenow="{{ $student['credits_pct'] }}" aria-valuemin="0" aria-valuemax="100" aria-label="Tiến độ tín chỉ">
                        <div class="h-full bg-primary rounded-full" style="width: {{ $student['credits_pct'] }}%"></div>
                    </div>
                </div>
            </section>

            <!-- Today's classes -->
            <section class="bg-white border border-[#E2E8F0] rounded-xl p-4 shadow-xs">
                <h2 class="text-sm font-semibold text-on-surface mb-3">Lịch học hôm nay</h2>
                <ul class="space-y-3">
                    @foreach ($todayClasses as $class)
                        <li class="flex gap-3">
                            <div class="w-1 rounded-full {{ $class['status'] === 'ongoing' ? 'bg-primary' : 'bg-outline-variant' }} shrink-0"></div>
                            <div class="min-w-0">
                                <div class="text-[11px] text-on-surface-variant font-medium">{{ $class['time'] }} • {{ $class['status_label'] }}</div>
                                <div class="text-sm font-semibold text-on-surface break-words">{{ $class['course_name'] }}</div>
                                <div class="text-xs text-on-surface-variant">{{ $class['room'] }} • {{ $class['lecturer'] }}</div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>

            <!-- Recent grades -->
            <section class="bg-white border border-[#E2E8F0] rounded-xl p-4 shadow-xs">
                <h2 class="text-sm font-semibold text-on-surface mb-3">Điểm gần đây</h2>
                <ul class="divide-y divide-surface-container">
                    @foreach ($recentGrades as $grade)
                        <li class="py-2.5 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-sm font-medium text-on-surface break-words">{{ $grade['course_name'] }}</div>
                                <div class="text-[11px] text-on-surface-variant">{{ $grade['credits'] }} tín chỉ • {{ $grade['semester'] }}</div>
                            </div>
                            <div class="text-right shrink-0">
                                <div class="text-sm font-bold text-primary">{{ number_format($grade['score'], 1) }}</div>
                                <div class="text-[11px] text-on-surface-variant">{{ $grade['letter'] }}</div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>
        </main>

        <!-- Bottom navigation -->
        <nav class="fixed bottom-0 inset-x-0 z-30 bg-white border-t border-[#E2E8F0]" aria-label="Điều hướng chính">
            <div class="mx-auto max-w-md grid grid-cols-4">
                <a href="{{ route('student.mobile') }}" aria-current="page" class="flex flex-col items-center gap-0.5 py-2 text-primary">
                    <span class="material-symbols-outlined text-[22px]" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">dashboard</span>
                    <span class="text-[11px] font-semibold">Tổng quan</span>
                </a>
                <a href="#" class="flex flex-col items-center gap-0.5 py-2 text-on-surface-variant">
                    <span class="material-symbols-outlined text-[22px]" aria-hidden="true">calendar_month</span>
                    <span class="text-[11px]">Lịch học</span>
                </a>
                <a href="#" class="flex flex-col items-center gap-0.5 py-2 text-on-surface-variant">
                    <span class="material-symbols-outlined text-[22px]" aria-hidden="true">grade</span>
                    <span class="text-[11px]">Điểm</span>
                </a>
                <a href="{{ route('account.settings') }}" class="flex flex-col items-center gap-0.5 py-2 text-on-surface-variant">
                    <span class="material-symbols-outlined text-[22px]" aria-hidden="true">person</span>
                    <span class="text-[11px]">Tài khoản</span>
                </a>
            </div>
        </nav>
    </div>
</div>
@endsection
