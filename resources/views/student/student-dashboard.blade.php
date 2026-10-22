{{--
    Tổng quan cổng sinh viên (FR-RPT-009; mock student/tong-quan). Dữ liệu thật của chính sinh viên từ
    App\Modules\Report\Services\PortalOverview::student() ($overview); tài khoản chưa gắn hồ sơ sinh viên thì báo rõ.
--}}
@extends('layouts.portal')

@section('title', 'Cổng Sinh viên - ' . config('studentmanager.school.short_name'))
@section('page-title', 'Tổng quan Sinh viên')

@section('page')
@php
    $card = 'bg-white border border-[#E2E8F0] rounded-xl shadow-xs';
    $fmt = fn ($v, $d = 2) => $v === null ? '—' : number_format((float) $v, $d, ',', '.');
@endphp
<div class="space-y-6">
    <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Cổng thông tin Sinh viên</p>
    @if ($overview === null)
        <div class="{{ $card }} p-8 text-center space-y-2">
            <span class="material-symbols-outlined text-outline text-[44px]" aria-hidden="true">person_off</span>
            <h1 class="text-lg font-bold text-on-surface">Tài khoản chưa gắn với hồ sơ sinh viên</h1>
            <p class="text-sm text-on-surface-variant">Liên hệ Phòng Đào tạo để được gắn hồ sơ. Đăng nhập bằng MSSV nếu bạn đã nhập học.</p>
        </div>
    @else
        @php
            $p = $overview['profile'];
            $c = $overview['credits'];
        @endphp
        <div class="{{ $card }} p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <div class="space-y-1.5">
                <h1 class="text-[26px] sm:text-[30px] font-bold text-on-surface tracking-tight leading-tight">Xin chào, {{ $p['name'] }} 👋</h1>
                <p class="text-xs text-on-surface-variant">
                    MSSV: <strong class="text-primary font-semibold">{{ $p['code'] }}</strong> • Ngành: <strong class="text-on-surface">{{ $p['major'] ?? '—' }}</strong>
                    • Khóa: <strong>{{ $p['cohort'] }}</strong> • Lớp: <strong>{{ $p['class'] ?? '—' }}</strong>
                    @if ($overview['term']) • <span>{{ $overview['term'] }}</span> @endif
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                @foreach ([['student.pages.registration', 'how_to_reg', 'Đăng ký học phần', true], ['student.pages.grades', 'grade', 'Kết quả học tập', false], ['student.pages.registration-result', 'fact_check', 'Kết quả đăng ký', false]] as [$route, $icon, $label, $primary])
                    @if (Route::has($route))
                        <a href="{{ route($route) }}" class="{{ $primary ? 'bg-primary hover:bg-primary-container text-white' : 'bg-white border border-outline-variant hover:bg-surface-container-low text-on-surface' }} text-xs font-semibold px-4 py-2.5 rounded-lg flex items-center gap-1.5 shadow-xs">
                            <span class="material-symbols-outlined text-[16px] {{ $primary ? '' : 'text-primary' }}" aria-hidden="true">{{ $icon }}</span>
                            <span>{{ $label }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="{{ $card }} p-5 space-y-2">
                <span class="text-xs text-on-surface-variant font-medium">Tín chỉ tích lũy</span>
                <div class="flex items-baseline gap-1">
                    <span class="text-[22px] font-bold text-on-surface leading-tight">{{ $c['earned'] }}</span>
                    <span class="text-xs text-on-surface-variant font-medium">/ {{ $c['total'] ?: '—' }} TC</span>
                </div>
                <div class="w-full h-1.5 rounded-full bg-surface-container-high overflow-hidden"><div class="h-full rounded-full bg-primary" style="width: {{ $c['pct'] }}%"></div></div>
                <div class="text-[11px] text-on-surface-variant flex justify-between"><span>Đã tích lũy: <strong class="text-primary">{{ $fmt($c['pct'], 1) }}%</strong></span><span>Còn thiếu: <strong>{{ $c['remaining'] }} TC</strong></span></div>
            </div>
            <div class="{{ $card }} p-5 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-on-surface-variant font-medium">GPA tích lũy</span>
                    @if ($overview['classification'])
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">Xếp loại: {{ $overview['classification'] }}</span>
                    @endif
                </div>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-[22px] font-bold text-primary leading-tight">{{ $fmt($overview['cumulative_gpa']) }}</span>
                    <span class="text-xs text-on-surface-variant font-medium">/ 4,0</span>
                </div>
                @php $lastTerm = collect($overview['gpa_by_term'])->last(); @endphp
                <div class="text-[11px] text-on-surface-variant pt-1 border-t border-[#F1F5F9]">
                    {{ $lastTerm ? 'GPA '.$lastTerm['term'].': '.$fmt($lastTerm['gpa']) : 'Chưa có điểm đã công bố' }}
                </div>
            </div>
            <div class="{{ $card }} p-5 space-y-2">
                <span class="text-xs text-on-surface-variant font-medium">Học kỳ này</span>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-[22px] font-bold text-on-surface leading-tight">{{ $overview['current']['courses'] }}</span>
                    <span class="text-xs text-on-surface-variant font-medium">học phần</span>
                </div>
                <div class="text-[11px] text-on-surface-variant pt-1 border-t border-[#F1F5F9]">{{ $overview['current']['credits'] }} tín chỉ đang học</div>
            </div>
            <div class="{{ $card }} p-5 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-on-surface-variant font-medium">Thông báo chưa đọc</span>
                    <span class="material-symbols-outlined text-primary text-[18px]" aria-hidden="true">notifications</span>
                </div>
                <div class="text-[22px] font-bold text-on-surface leading-tight">{{ $overview['notifications']['unread'] }}</div>
                <div class="text-[11px] text-on-surface-variant pt-1 border-t border-[#F1F5F9]">
                    @if (Route::has('notifications.page'))<a href="{{ route('notifications.page') }}" class="text-primary font-semibold hover:underline">Mở trung tâm thông báo</a>@endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-8 space-y-6">
                <section class="{{ $card }} p-6 space-y-4">
                    <div class="flex items-center gap-2 border-b border-[#F1F5F9] pb-3">
                        <span class="material-symbols-outlined text-primary text-[22px]" aria-hidden="true">calendar_today</span>
                        <h2 class="text-[20px] font-bold text-on-surface leading-tight">Lịch học hôm nay ({{ now(config('studentmanager.display_timezone'))->translatedFormat('l, d/m/Y') }})</h2>
                    </div>
                    <div class="space-y-3">
                        @forelse ($overview['today_schedule'] as $cls)
                            <div class="p-4 rounded-xl border border-outline-variant/60 bg-surface-container-low/30 space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded text-xs font-bold bg-secondary-container text-on-secondary-fixed">{{ $cls['time'] }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800">{{ $cls['status_label'] }}</span>
                                </div>
                                <h3 class="text-sm font-bold text-on-surface">{{ $cls['subject'] }} <span class="font-normal text-on-surface-variant">({{ $cls['section'] }})</span></h3>
                                <div class="text-xs text-on-surface-variant">Phòng: <strong class="text-primary">{{ $cls['room'] ?? 'Trực tuyến' }}</strong> • Giảng viên: <strong class="text-on-surface">{{ $cls['lecturer'] ?? '—' }}</strong></div>
                            </div>
                        @empty
                            <p class="text-sm text-on-surface-variant">Hôm nay không có buổi học (hoặc thời khóa biểu học kỳ chưa công bố).</p>
                        @endforelse
                    </div>
                </section>

                <section class="{{ $card }} p-6 space-y-4">
                    <div class="flex items-center gap-2 border-b border-[#F1F5F9] pb-3">
                        <span class="material-symbols-outlined text-primary text-[22px]" aria-hidden="true">trending_up</span>
                        <h2 class="text-[18px] font-bold text-on-surface">Tiến độ chương trình đào tạo ({{ $c['earned'] }} / {{ $c['total'] ?: '—' }} TC)</h2>
                    </div>
                    <div class="space-y-2">
                        <div class="w-full h-3 rounded-full bg-[#E2E8F0] overflow-hidden flex">
                            <div class="h-full bg-primary" style="width: {{ $c['pct'] }}%" title="Đã tích lũy"></div>
                            <div class="h-full bg-primary-container" style="width: {{ $c['in_progress_pct'] }}%" title="Đang học"></div>
                        </div>
                        <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-on-surface-variant">
                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-primary"></span> Đã tích lũy: <strong class="text-on-surface">{{ $c['earned'] }} TC</strong></span>
                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-primary-container"></span> Đang học: <strong class="text-on-surface">{{ $c['in_progress'] }} TC</strong></span>
                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-outline-variant"></span> Còn thiếu: <strong class="text-on-surface">{{ $c['remaining'] }} TC</strong></span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                        @forelse ($overview['blocks'] as $block)
                            <div class="p-3 rounded-lg border border-[#E2E8F0] bg-surface-container-low/30 space-y-1">
                                <div class="flex justify-between text-xs font-semibold text-on-surface"><span>{{ $block['name'] }}</span><span class="{{ $block['pct'] >= 100 ? 'text-emerald-700' : 'text-primary' }}">{{ $fmt($block['pct'], 1) }}%</span></div>
                                <div class="w-full h-1.5 rounded-full bg-surface-container-high overflow-hidden"><div class="h-full rounded-full {{ $block['pct'] >= 100 ? 'bg-emerald-500' : 'bg-primary' }}" style="width: {{ $block['pct'] }}%"></div></div>
                                <div class="text-[11px] text-on-surface-variant">{{ $block['earned'] }} / {{ $block['required'] }} tín chỉ</div>
                            </div>
                        @empty
                            <p class="text-sm text-on-surface-variant">Chưa có chương trình đào tạo áp dụng.</p>
                        @endforelse
                    </div>
                </section>
            </div>

            <div class="lg:col-span-4 space-y-6">
                <section class="{{ $card }} p-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-[#F1F5F9] pb-3">
                        <h3 class="text-sm font-bold text-on-surface flex items-center gap-2"><span class="material-symbols-outlined text-primary text-[20px]" aria-hidden="true">verified</span>Kết quả mới công bố</h3>
                        @if (Route::has('student.pages.grades'))<a href="{{ route('student.pages.grades') }}" class="text-xs text-primary hover:underline font-semibold">Bảng điểm →</a>@endif
                    </div>
                    <div class="space-y-2.5 text-xs">
                        @forelse ($overview['recent_results'] as $g)
                            <div class="p-2.5 rounded-lg border border-[#E2E8F0] bg-surface-container-low/30 flex items-center justify-between gap-2">
                                <div>
                                    <div class="font-semibold text-on-surface">{{ $g['subject'] }}</div>
                                    <div class="text-[11px] text-on-surface-variant">{{ $g['credits'] }} tín chỉ • {{ $g['term'] }}</div>
                                </div>
                                <span class="px-2 py-0.5 rounded font-bold border {{ $g['passed'] === false ? 'text-red-700 bg-red-50 border-red-200' : 'text-emerald-700 bg-emerald-50 border-emerald-200' }}">{{ $g['score'] ?? '' }} {{ $g['letter'] ? '('.$g['letter'].')' : '' }}</span>
                            </div>
                        @empty
                            <p class="text-on-surface-variant">Chưa có điểm đã công bố.</p>
                        @endforelse
                    </div>
                </section>

                <section class="{{ $card }} p-5 space-y-3">
                    <h3 class="text-sm font-bold text-on-surface flex items-center gap-2 border-b border-[#F1F5F9] pb-3"><span class="material-symbols-outlined text-primary text-[20px]" aria-hidden="true">event_upcoming</span>Hạn chót sắp tới</h3>
                    @forelse ($overview['deadlines'] as $d)
                        <div class="text-xs flex items-start justify-between gap-2">
                            <span class="text-on-surface">{{ $d['label'] }} <span class="text-on-surface-variant">({{ $d['term'] }})</span></span>
                            <strong class="text-primary shrink-0">{{ \Carbon\CarbonImmutable::parse($d['date'])->format('d/m') }}{{ $d['time'] ? ' '.$d['time'] : '' }}</strong>
                        </div>
                    @empty
                        <p class="text-xs text-on-surface-variant">Không có mốc nào trong {{ config('studentmanager.dashboard.deadline_days') }} ngày tới.</p>
                    @endforelse
                </section>

                <section class="{{ $card }} p-5 space-y-3">
                    <h3 class="text-sm font-bold text-on-surface flex items-center gap-2 border-b border-[#F1F5F9] pb-3"><span class="material-symbols-outlined text-primary text-[20px]" aria-hidden="true">campaign</span>Thông báo mới</h3>
                    @forelse ($overview['notifications']['latest'] as $n)
                        <a href="{{ route('notifications.page') }}" class="block p-2.5 rounded-lg border border-[#E2E8F0] hover:bg-surface-container-low space-y-1">
                            <div class="text-[10px] text-on-surface-variant">{{ $n['sent_at'] ? \Carbon\CarbonImmutable::parse($n['sent_at'])->timezone(config('studentmanager.display_timezone'))->format('H:i d/m/Y') : '' }}</div>
                            <h4 class="text-xs font-semibold text-on-surface leading-snug line-clamp-2">{{ $n['title'] }}</h4>
                        </a>
                    @empty
                        <p class="text-xs text-on-surface-variant">Không có thông báo chưa đọc.</p>
                    @endforelse
                </section>
            </div>
        </div>
    @endif
</div>
@endsection
