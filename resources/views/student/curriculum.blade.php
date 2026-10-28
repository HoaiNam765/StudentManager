@extends('layouts.portal')
@section('title', 'Chương trình đào tạo - Cổng Sinh viên')
@section('page-title', 'Chương trình đào tạo')
@section('page')

<div class="flex flex-col gap-6">
<!-- SECTION 1: BANNER TỔNG QUAN CHƯƠNG TRÌNH & THẺ TIẾN ĐỘ TÍCH LŨY -->
<section class="bg-surface-container-lowest rounded-xl border border-slate-200 p-6 shadow-sm">
<!-- Header Banner & Action Buttons -->
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-6 border-b border-slate-100">
<div>
<div class="flex items-center gap-2.5">
<span class="px-2.5 py-0.5 rounded-full bg-surface-container text-primary font-label-sm text-label-sm font-semibold">Khóa {{ $student['cohort'] }}</span>
<span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-secondary font-label-sm text-label-sm font-medium">Hệ: {{ $profile?->trainingType?->name ?? '—' }}</span>
<span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-secondary font-label-sm text-label-sm font-medium">Mã CTĐT: {{ $curriculum?->code ?? 'Chưa được gán' }}</span>
</div>
<h1 class="font-headline-xl text-headline-xl text-on-surface font-bold tracking-tight mt-2">
              Chương trình đào tạo: {{ $curriculum?->name ?? 'Chưa được gán' }}
            </h1>
<p class="font-body-md text-body-md text-secondary mt-1">
              {{ $student['major'] }} • Thời gian đào tạo chuẩn: {{ $curriculum?->standard_terms ?? '—' }} học kỳ
            </p>
</div>
<!-- CTAs: Download PDF & Plan -->
<div class="flex items-center gap-3 flex-shrink-0">
<button type="button" onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-surface-container-lowest border border-slate-300 rounded-lg font-label-md text-label-md font-semibold text-on-surface hover:bg-slate-50 transition-colors shadow-sm">
<span class="material-symbols-outlined text-[18px] text-secondary" data-icon="file_download">file_download</span>
<span>In / lưu khung chương trình (PDF)</span>
</button>
<button type="button" onclick="document.getElementById('curriculum-search').focus()" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg font-label-md text-label-md font-semibold hover:bg-[#15439e] transition-colors shadow-sm">
<span class="material-symbols-outlined text-[18px]" data-icon="view_timeline">view_timeline</span>
<span>Tra cứu học phần</span>
</button>
</div>
</div>
<!-- Academic Metrics Grid -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 pt-6">
<!-- Column 1: Multi-Segmented Credit Progress (7 cols) -->
<div class="lg:col-span-7 flex flex-col justify-between">
<div>
<div class="flex items-baseline justify-between mb-2">
<span class="font-label-lg text-label-lg text-secondary font-semibold">Tiến độ tích lũy tín chỉ toàn khóa</span>
<div class="flex items-baseline gap-1.5">
<span class="font-display-lg text-display-lg text-primary font-bold tracking-tight">{{ $student['credits_earned'] ?? '—' }}</span>
<span class="font-headline-md text-headline-md text-secondary font-semibold">/ {{ $student['credits_total'] ?? '—' }}</span>
<span class="font-body-md text-body-md text-secondary ml-1">Tín chỉ</span>
<span class="ml-2 font-label-md text-label-md px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold">Đạt {{ $student['credits_pct'] }}%</span>
</div>
</div>
<!-- Multi-segmented Progress Bar -->
<div class="w-full h-4 bg-slate-100 rounded-full overflow-hidden flex shadow-inner my-3">
<!-- Segment 1: Đã tích lũy {{ $student['credits_pct'] }}% ({{ $student['credits_earned'] ?? '—' }} TC) -->
<div class="h-full bg-emerald-500 transition-all duration-500" style="width: {{ $student['credits_pct'] }}%;" title="Đã tích lũy: {{ $student['credits_earned'] ?? '—' }} TC ({{ $student['credits_pct'] }}%)"></div>
<!-- Segment 2: Đang học {{ $student['current_pct'] }}% ({{ $student['current_credits'] }} TC) -->
<div class="h-full bg-amber-400 transition-all duration-500" style="width: {{ $student['current_pct'] }}%;" title="Đang học: {{ $student['current_credits'] }} TC ({{ $student['current_pct'] }}%)"></div>
<!-- Segment 3: Chưa học {{ $student['remaining_pct'] }}% ({{ $student['credits_remaining'] ?? '—' }} TC) -->
<div class="h-full bg-slate-200 transition-all duration-500" style="width: {{ $student['remaining_pct'] }}%;" title="Chưa học: {{ $student['credits_remaining'] ?? '—' }} TC ({{ $student['remaining_pct'] }}%)"></div>
</div>
<!-- Color Legend & Breakdown details -->
<div class="grid grid-cols-3 gap-2 pt-2">
<div class="flex items-center gap-2">
<span class="w-3 h-3 rounded-sm bg-emerald-500 flex-shrink-0"></span>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-secondary">Đã tích lũy</span>
<span class="font-label-md text-label-md font-bold text-on-surface">{{ $student['credits_earned'] ?? '—' }} TC ({{ $student['credits_pct'] }}%)</span>
</div>
</div>
<div class="flex items-center gap-2">
<span class="w-3 h-3 rounded-sm bg-amber-400 flex-shrink-0"></span>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-secondary">Đang học</span>
<span class="font-label-md text-label-md font-bold text-on-surface">{{ $student['current_credits'] }} TC ({{ $student['current_pct'] }}%)</span>
</div>
</div>
<div class="flex items-center gap-2">
<span class="w-3 h-3 rounded-sm bg-slate-300 flex-shrink-0"></span>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-secondary">Chưa học</span>
<span class="font-label-md text-label-md font-bold text-on-surface">{{ $student['credits_remaining'] ?? '—' }} TC ({{ $student['remaining_pct'] }}%)</span>
</div>
</div>
</div>
</div>
<!-- Graduation Requirement Hint -->
<p class="font-body-sm text-body-sm text-secondary pt-3 flex items-center gap-1.5">
<span class="material-symbols-outlined text-[16px] text-primary" data-icon="info">info</span>
<span>Yêu cầu tốt nghiệp chuẩn HUIT: Tích lũy tối thiểu {{ $student['credits_total'] ?? '—' }} tín chỉ theo đúng khung CTĐT và đạt các chuẩn đầu ra.</span>
</p>
</div>
<!-- Column 2: Academic Benchmarks & Standards (5 cols) -->
<div class="lg:col-span-5 bg-surface-container-low rounded-xl p-4 border border-slate-200/80 flex flex-col justify-between">
<div>
<span class="font-label-md text-label-md text-secondary font-semibold uppercase tracking-wider block mb-3">Chỉ số học tập &amp; Chuẩn đầu ra</span>
<div class="grid grid-cols-2 gap-3">
<!-- GPA Tích lũy -->
<div class="bg-surface-container-lowest p-3 rounded-lg border border-slate-200">
<span class="font-body-sm text-body-sm text-secondary block">GPA Tích lũy (Hệ 4)</span>
<div class="flex items-baseline gap-1 mt-1">
<span class="font-headline-lg text-headline-lg font-bold text-primary">{{ $student['gpa_4'] === null ? '—' : number_format($student['gpa_4'], 2) }}</span>
<span class="font-body-sm text-body-sm text-secondary">/ 4.0</span>
</div>
<span class="inline-block mt-1 font-label-sm text-label-sm px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-semibold">Xếp loại: {{ $student['classification'] }}</span>
</div>
<!-- Điểm rèn luyện -->
<div class="bg-surface-container-lowest p-3 rounded-lg border border-slate-200">
<span class="font-body-sm text-body-sm text-secondary block">Điểm rèn luyện HK gần nhất</span>
<div class="flex items-baseline gap-1 mt-1">
<span class="font-headline-lg text-headline-lg font-bold text-on-surface">—</span>
<span class="font-body-sm text-body-sm text-secondary">/ 100</span>
</div>
<span class="inline-block mt-1 font-label-sm text-label-sm px-2 py-0.5 rounded-full bg-blue-50 text-primary font-semibold">Chưa có dữ liệu</span>
</div>
</div>
</div>
<!-- Standard Exit Credentials -->
<div class="flex flex-col gap-2 pt-3 border-t border-slate-200/80 mt-3">
<div class="flex items-center justify-between text-body-sm">
<span class="text-secondary flex items-center gap-1.5">
<span class="material-symbols-outlined text-[16px] text-emerald-700" data-icon="verified">verified</span>
<span>Chuẩn Ngoại ngữ (B1 CEFR / VSTEP):</span>
</span>
<span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">Chưa có kết quả xét</span>
</div>
<div class="flex items-center justify-between text-body-sm">
<span class="text-secondary flex items-center gap-1.5">
<span class="material-symbols-outlined text-[16px] text-emerald-700" data-icon="verified">verified</span>
<span>Chuẩn CNTT Cơ bản (Thông tư 03):</span>
</span>
<span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">Chưa có kết quả xét</span>
</div>
</div>
</div>
</div>
<!-- Alert Thông Báo Cảnh Báo Thông Minh (Amber/Warning) -->
<div class="mt-6 p-4 rounded-xl bg-amber-50 border border-amber-200 flex items-start gap-3">
<span class="material-symbols-outlined text-amber-700 text-[22px] flex-shrink-0 mt-0.5" data-icon="warning">warning</span>
<div class="flex flex-col">
<span class="font-label-lg text-label-lg font-bold text-amber-900">
              Học phần còn thiếu để xét tốt nghiệp đúng hạn:
            </span>
<p class="font-body-md text-body-md text-amber-800 mt-0.5">
              Bạn còn <strong class="font-semibold text-amber-950">{{ $student['credits_remaining'] ?? '—' }} tín chỉ chưa tích lũy</strong> Hãy chú ý điều kiện tiên quyết và kế hoạch mở lớp cho các học kỳ tiếp theo.
            </p>
</div>
</div>
</section>
<!-- SECTION 2: THANH CÔNG CỤ TÌM KIẾM & BỘ LỌC HỌC PHẦN -->
<section class="bg-surface-container-lowest rounded-xl border border-slate-200 p-4 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
<!-- Search and Filter Dropdowns -->
<div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
<!-- Chế độ xem: theo khối kiến thức / theo học kỳ -->
<div id="curriculum-view-toggle" role="group" aria-label="Chế độ xem chương trình đào tạo" class="flex w-full sm:inline-flex sm:w-auto rounded-lg border border-slate-300 overflow-hidden shadow-sm no-print">
<button type="button" data-curriculum-view="blocks" aria-pressed="true" onclick="setCurriculumView('blocks')" class="inline-flex flex-1 sm:flex-none items-center justify-center gap-1.5 px-3 py-2 text-center sm:whitespace-nowrap font-label-md text-label-md font-semibold transition-colors bg-primary text-white">
<span class="material-symbols-outlined text-[16px]" data-icon="account_tree">account_tree</span>
<span>Theo khối kiến thức</span>
</button>
<button type="button" data-curriculum-view="terms" aria-pressed="false" onclick="setCurriculumView('terms')" class="inline-flex flex-1 sm:flex-none items-center justify-center gap-1.5 px-3 py-2 text-center sm:whitespace-nowrap font-label-md text-label-md font-semibold transition-colors border-l border-slate-300 bg-surface-container-lowest text-secondary hover:bg-slate-50">
<span class="material-symbols-outlined text-[16px]" data-icon="calendar_month">calendar_month</span>
<span>Theo học kỳ</span>
</button>
</div>
<!-- Input Tìm kiếm -->
<div class="relative w-full sm:w-80">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-outline" data-icon="search">search</span>
<input id="curriculum-search" oninput="filterCurriculum()" aria-label="Tìm theo mã học phần, tên học phần..." class="w-full pl-9 pr-3 py-2 bg-surface-container-lowest border border-slate-300 rounded-lg font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm" placeholder="Tìm theo mã học phần, tên học phần..." type="text"/>
</div>
<!-- Dropdown Trạng thái -->
<div class="relative">
<select id="curriculum-status" onchange="filterCurriculum()" aria-label="Tất cả trạng thái" class="appearance-none bg-surface-container-lowest border border-slate-300 rounded-lg pl-3 pr-8 py-2 font-body-sm text-body-sm text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm cursor-pointer">
<option value="all">Tất cả trạng thái</option>
<option value="completed">Đã hoàn thành</option>
<option value="in_progress">Đang học</option>
<option value="unregistered">Chưa học</option>
</select>
<span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-[18px] text-outline pointer-events-none">expand_more</span>
</div>
<!-- Dropdown Khối kiến thức (chỉ chế độ khối) -->
<div id="curriculum-block-filter" class="relative">
<select id="curriculum-block" onchange="filterCurriculum()" aria-label="Tất cả khối kiến thức" class="appearance-none bg-surface-container-lowest border border-slate-300 rounded-lg pl-3 pr-8 py-2 font-body-sm text-body-sm text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm cursor-pointer">
<option value="all">Tất cả khối kiến thức</option>
@foreach ($curriculum?->blocks ?? [] as $block)<option value="{{ $block->id }}">{{ $block->name }}</option>@endforeach
</select>
<span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-[18px] text-outline pointer-events-none">expand_more</span>
</div>
</div>
<!-- Expansion Controls -->
<div class="flex items-center gap-2 flex-shrink-0 w-full sm:w-auto justify-end">
<button type="button" onclick="document.querySelectorAll('[data-block-table]').forEach(el => el.hidden=true)" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-300 font-label-md text-label-md text-secondary hover:bg-slate-50 transition-colors">
<span class="material-symbols-outlined text-[16px]" data-icon="unfold_less">unfold_less</span>
<span>Thu gọn tất cả</span>
</button>
<button type="button" onclick="document.querySelectorAll('[data-block-table]').forEach(el => el.hidden=false)" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-300 font-label-md text-label-md text-secondary hover:bg-slate-50 transition-colors">
<span class="material-symbols-outlined text-[16px]" data-icon="unfold_more">unfold_more</span>
<span>Mở rộng tất cả</span>
</button>
</div>
</section>
<!-- SECTION 3: DANH SÁCH 5 KHỐI KIẾN THỨC DẠNG ACCORDION -->
<div id="curriculum-blocks">
<div class="flex flex-col gap-4">
@forelse ($curriculum?->blocks ?? [] as $block)
<!-- ==================== KHỐI 1 ==================== -->
<div class="bg-surface-container-lowest rounded-xl border border-slate-200 overflow-hidden shadow-sm transition-all duration-200">
<!-- Accordion Header -->
<div onclick="this.nextElementSibling.hidden = !this.nextElementSibling.hidden" role="button" tabindex="0" onkeydown="if(event.key === 'Enter' || event.key === ' '){event.preventDefault();this.click()}" class="p-5 flex items-center justify-between cursor-pointer hover:bg-slate-50/70 border-b border-slate-100">
<div class="flex items-center gap-3">
<span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-label-md">{{ $loop->iteration }}</span>
<div>
<div class="flex items-center gap-2">
<h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">{{ $block->name }}</h2>
<span class="font-body-sm text-body-sm text-secondary">(Yêu cầu: {{ $block->required_credits }} TC)</span>
</div>
<p class="font-body-sm text-body-sm text-secondary mt-0.5">{{ $block->block_type->label() }}</p>
</div>
</div>
<div class="flex items-center gap-3">
<!-- Status Badge -->
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 font-label-md text-label-md font-semibold">
<span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                {{ collect($curriculumRows)->where('block_id', $block->id)->where('passed', true)->sum('credits') }} / {{ $block->required_credits }} TC đã đạt
              </span>
<span class="material-symbols-outlined text-outline text-[22px]">keyboard_arrow_up</span>
</div>
</div>
<!-- Academic Table -->
<div data-block-table class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-slate-50 border-b border-slate-200 text-secondary font-label-md text-label-md uppercase tracking-wider">
<th class="py-3 px-4 w-12 text-center">STT</th>
<th class="py-3 px-4 w-28">Mã HP</th>
<th class="py-3 px-4">Tên học phần</th>
<th class="py-3 px-4 w-20 text-center">Số TC</th>
<th class="py-3 px-4 w-32">Điều kiện</th>
<th class="py-3 px-4 w-24 text-center">Điểm số</th>
<th class="py-3 px-4 w-24 text-center">Điểm chữ</th>
<th class="py-3 px-4 w-36 text-center">Trạng thái</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 font-body-sm text-body-sm">
@foreach (collect($curriculumRows)->where('block_id', $block->id) as $row)
<tr class="hover:bg-slate-50/80 transition-colors" data-curriculum-row data-block="{{ $block->id }}" data-status="{{ $row['passed'] ? 'completed' : ($row['studying'] ? 'in_progress' : 'unregistered') }}">
<td class="py-3 px-4 text-center text-secondary">{{ $loop->iteration }}</td>
<td class="py-3 px-4 font-mono font-medium text-primary">{{ $row['code'] }}</td>
<td class="py-3 px-4 font-medium text-on-surface">{{ $row['name'] }}</td>
<td class="py-3 px-4 text-center font-semibold">{{ $row['credits'] }}</td>
<td class="py-3 px-4 text-secondary">{{ $row['type'] }} • HK {{ $row['term'] }}</td>
<td class="py-3 px-4 text-center font-bold text-on-surface">{{ $row['score'] === null ? '—' : number_format($row['score'], 1) }}</td>
<td class="py-3 px-4 text-center font-bold text-emerald-700">{{ $row['letter'] ?? '—' }}</td>
<td class="py-3 px-4 text-center"><span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">{{ $row['passed'] ? 'Đã hoàn thành' : ($row['studying'] ? 'Đang học' : 'Chưa đạt') }}</span></td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div>

@empty<p class="p-5 text-secondary">Chưa được gán chương trình đào tạo.</p>@endforelse
</div>
</div>
<!-- SECTION 3b: CHƯƠNG TRÌNH KHUNG THEO HỌC KỲ -->
@php
  $termRows = collect($curriculumRows);
  // Tín chỉ tối thiểu của một nhóm tự chọn chỉ tính vào tổng của học kỳ đầu tiên nhóm xuất hiện
  $groupFirstTerm = $termRows->whereNotNull('group_id')->groupBy('group_id')->map(fn ($group) => $group->min('term'));
  $termCount = $curriculum === null ? 0 : max($curriculum->standard_terms ?? 0, (int) $termRows->max('term'));
@endphp
<div id="curriculum-terms" hidden>
<div class="flex flex-col gap-4">
@forelse ($termCount > 0 ? range(1, $termCount) : [] as $termNo)
@php
  $inTerm = $termRows->where('term', $termNo);
  $compulsory = $inTerm->whereNull('group_id');
  $groups = $inTerm->whereNotNull('group_id')->groupBy('group_id');
  $compulsoryCredits = $compulsory->sum('credits');
  $electiveCredits = $groups->filter(fn ($group, $groupId) => $groupFirstTerm->get($groupId) === $termNo)
      ->sum(fn ($group) => $group->first()['group_min_credits'] ?? 0);
  $parts = [
      ['label' => 'Học phần bắt buộc', 'credits' => $compulsoryCredits, 'groups' => $compulsory->isEmpty() ? collect() : collect([0 => $compulsory])],
      ['label' => 'Học phần tự chọn', 'credits' => $electiveCredits, 'groups' => $groups],
  ];
  $stt = 0;
@endphp
<div class="bg-surface-container-lowest rounded-xl border border-slate-200 overflow-hidden shadow-sm transition-all duration-200" data-term="{{ $termNo }}">
<!-- Accordion Header -->
<div onclick="this.nextElementSibling.hidden = !this.nextElementSibling.hidden" role="button" tabindex="0" onkeydown="if(event.key === 'Enter' || event.key === ' '){event.preventDefault();this.click()}" class="p-5 flex items-center justify-between cursor-pointer hover:bg-slate-50/70 border-b border-slate-100">
<div class="flex items-center gap-3">
<span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-label-md">{{ $termNo }}</span>
<div>
<div class="flex items-center gap-2">
<h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">Học kỳ {{ $termNo }}</h2>
<span class="font-body-sm text-body-sm text-secondary" data-term-total>(Tổng: {{ $compulsoryCredits + $electiveCredits }} TC)</span>
</div>
<p class="font-body-sm text-body-sm text-secondary mt-0.5">Bắt buộc: {{ $compulsoryCredits }} TC • Tự chọn: {{ $electiveCredits }} TC</p>
</div>
</div>
<div class="flex items-center gap-3">
<!-- Status Badge -->
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 font-label-md text-label-md font-semibold">
<span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                {{ $inTerm->where('passed', true)->sum('credits') }} / {{ $compulsoryCredits + $electiveCredits }} TC đã đạt
              </span>
<span class="material-symbols-outlined text-outline text-[22px]">keyboard_arrow_up</span>
</div>
</div>
<!-- Academic Table -->
<div data-block-table class="overflow-x-auto">
@if ($inTerm->isEmpty())
<p class="p-5 font-body-sm text-body-sm text-secondary">Chưa có học phần dự kiến trong học kỳ này.</p>
@else
<table class="w-full text-left border-collapse curriculum-term-table">
<thead>
<tr class="bg-slate-50 border-b border-slate-200 text-secondary font-label-md text-label-md uppercase tracking-wider">
<th class="py-3 px-4 w-12 text-center">STT</th>
<th class="py-3 px-4 w-28">Mã HP</th>
<th class="py-3 px-4 curriculum-name">Tên học phần</th>
<th class="py-3 px-4 w-36">Điều kiện</th>
<th class="py-3 px-4 w-20 text-center">Số TC</th>
<th class="py-3 px-4 w-20 text-center">Số tiết LT</th>
<th class="py-3 px-4 w-20 text-center">Số tiết TH</th>
<th class="py-3 px-4 w-40">Nhóm tự chọn</th>
<th class="py-3 px-4 w-24 text-center">TC bắt buộc của nhóm</th>
<th class="py-3 px-4 w-20 text-center">Đạt</th>
</tr>
</thead>
@foreach ($parts as $part)
@continue($part['groups']->isEmpty())
<tbody data-term-section class="divide-y divide-slate-100 font-body-sm text-body-sm">
<tr data-section-head class="bg-slate-50/60">
<td colspan="10" class="py-2 px-4 font-label-md text-label-md font-semibold text-on-surface">{{ $part['label'] }} <span class="font-normal text-secondary">({{ $part['credits'] }} TC)</span></td>
</tr>
@foreach ($part['groups'] as $groupId => $groupRows)
@if ($groupId)
<tr data-group-head data-group="{{ $groupId }}">
<td colspan="10" class="py-2 px-4 text-secondary">
<span class="font-semibold text-on-surface">Nhóm tự chọn: {{ $groupRows->first()['group_name'] }}</span>
                — chọn tối thiểu {{ $groupRows->first()['group_min_credits'] }} TC
                @if ($groupFirstTerm->get($groupId) !== $termNo)(đã tính vào tổng học kỳ {{ $groupFirstTerm->get($groupId) }})@endif
</td>
</tr>
@endif
@foreach ($groupRows as $row)
<tr class="hover:bg-slate-50/80 transition-colors" data-curriculum-row data-status="{{ $row['passed'] ? 'completed' : ($row['studying'] ? 'in_progress' : 'unregistered') }}" @if ($groupId) data-group="{{ $groupId }}" @endif>
<td class="py-3 px-4 text-center text-secondary">{{ ++$stt }}</td>
<td class="py-3 px-4 font-mono font-medium text-primary">{{ $row['code'] }}</td>
<td class="py-3 px-4 font-medium text-on-surface curriculum-name">{{ $row['name'] }}</td>
<td class="py-3 px-4 text-secondary">
@forelse ($row['requirements'] as $requirement)
<span class="block"><span class="font-mono">{{ $requirement['code'] }}</span> ({{ mb_strtolower($requirement['type']) }})</span>
@empty
—
@endforelse
</td>
<td class="py-3 px-4 text-center font-semibold">{{ $row['credits'] }}</td>
<td class="py-3 px-4 text-center">{{ $row['theory_periods'] ?? '—' }}</td>
<td class="py-3 px-4 text-center">{{ $row['practice_periods'] ?? '—' }}</td>
<td class="py-3 px-4 text-secondary">{{ $row['group_name'] }}</td>
<td class="py-3 px-4 text-center">{{ $row['group_min_credits'] }}</td>
<td class="py-3 px-4 text-center">
@if ($row['passed'])
<span class="font-bold text-emerald-700 text-[18px]" title="Đã đạt" aria-label="Đã đạt">✓</span>
@elseif ($row['studying'])
<span class="inline-block px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 font-label-sm text-label-sm font-semibold whitespace-nowrap">Đang học</span>
@endif
</td>
</tr>
@endforeach
@endforeach
</tbody>
@endforeach
<tbody data-term-empty hidden><tr><td colspan="10" class="py-4 px-4 text-center text-secondary">Không có học phần nào trong học kỳ này khớp bộ lọc.</td></tr></tbody>
</table>
@endif
</div>
</div>
@empty<p class="p-5 text-secondary">Chưa được gán chương trình đào tạo.</p>@endforelse
@if ($termCount > 0)
<p class="font-body-sm text-body-sm text-secondary flex items-start gap-1.5 px-1">
<span class="material-symbols-outlined text-[16px] text-primary" data-icon="info">info</span>
<span>Học phần tự chọn: sinh viên học đủ số tín chỉ bắt buộc của từng nhóm, không lấy học phần nhóm này thay cho nhóm khác.</span>
</p>
@endif
</div>
</div>
</div>
<style>
@media screen {
 .curriculum-term-table .curriculum-name { min-width: 14rem; }
 .curriculum-term-table td:nth-child(2) { white-space: nowrap; }
}
@media print {
 [data-block-table] { overflow: visible !important; }
 .curriculum-term-table { font-size: 10px; }
 .curriculum-term-table th, .curriculum-term-table td { padding: 4px 6px !important; }
 .curriculum-term-table tr { break-inside: avoid; }
}
</style>
<script>
function filterCurriculum() {
 const fold = s => s.toLocaleLowerCase('vi').normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/đ/g,'d');
 const q=fold(document.getElementById('curriculum-search').value), status=document.getElementById('curriculum-status').value, block=document.getElementById('curriculum-block').value;
 document.querySelectorAll('[data-curriculum-row]').forEach(row => row.hidden = !fold(row.textContent).includes(q) || (status!=='all' && row.dataset.status!==status) || (block!=='all' && row.dataset.block !== undefined && row.dataset.block!==block));
 // Chế độ học kỳ: ẩn tiêu đề mục / nhóm tự chọn khi không còn dòng nào khớp
 document.querySelectorAll('[data-term-section]').forEach(body => {
  body.querySelector('[data-section-head]').hidden = !body.querySelector('[data-curriculum-row]:not([hidden])');
  body.querySelectorAll('[data-group-head]').forEach(head => head.hidden = !body.querySelector('[data-curriculum-row][data-group="' + head.dataset.group + '"]:not([hidden])'));
 });
 // Học kỳ không còn dòng nào khớp: báo thay vì để bảng trống
 document.querySelectorAll('.curriculum-term-table').forEach(table => {
  const empty = table.querySelector('[data-term-empty]');
  if (empty) empty.hidden = !!table.querySelector('[data-curriculum-row]:not([hidden])');
 });
}
function setCurriculumView(view) {
 const terms = view === 'terms';
 document.getElementById('curriculum-blocks').hidden = terms;
 document.getElementById('curriculum-terms').hidden = !terms;
 document.getElementById('curriculum-block-filter').hidden = terms;
 if (terms) document.getElementById('curriculum-block').value = 'all';
 document.querySelectorAll('[data-curriculum-view]').forEach(button => {
  const active = button.dataset.curriculumView === view;
  button.setAttribute('aria-pressed', active ? 'true' : 'false');
  ['bg-primary', 'text-white'].forEach(c => button.classList.toggle(c, active));
  ['bg-surface-container-lowest', 'text-secondary', 'hover:bg-slate-50'].forEach(c => button.classList.toggle(c, !active));
 });
 try { localStorage.setItem('curriculum-view', view); } catch (e) {}
 filterCurriculum();
}
(function () {
 let saved = null;
 try { saved = localStorage.getItem('curriculum-view'); } catch (e) {}
 if (saved === 'terms') setCurriculumView('terms');
})();
</script>
@endsection
