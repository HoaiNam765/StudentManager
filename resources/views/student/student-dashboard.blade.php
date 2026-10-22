@extends('layouts.portal')

@section('title', 'Cổng Sinh viên - ' . config('studentmanager.school.short_name'))
@section('page-title', 'Tổng quan Sinh viên')

@section('page')
<div class="space-y-6">
    <!-- Welcome Banner Card -->
    <div class="bg-white border border-[#E2E8F0] rounded-xl p-6 shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-5 relative overflow-hidden">
        <div class="space-y-1.5 relative z-10">
            <h1 class="text-[26px] sm:text-[30px] font-bold text-on-surface tracking-tight leading-tight">
                Xin chào, {{ auth()->user()->name ?? ($student['name'] ?? 'Sinh viên') }} 👋
            </h1>
            <p class="text-xs text-on-surface-variant">
                MSSV: <strong class="text-primary font-semibold">{{ auth()->user()->username ?? ($student['student_id'] ?? '2001210123') }}</strong> • Ngành: <strong class="text-on-surface">{{ $student['major'] ?? 'Công nghệ Thông tin' }}</strong> • Khóa: <strong>{{ $student['cohort'] ?? '12 (2021–2025)' }}</strong> • Lớp: <strong>{{ $student['class'] ?? '12DHTT01' }}</strong>
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5 relative z-10 shrink-0">
            @if (\Illuminate\Support\Facades\Route::has('student.pages.registration'))
                <a href="{{ route('student.pages.registration') }}" class="bg-primary hover:bg-primary-container text-white text-xs font-semibold px-4 py-2.5 rounded-lg flex items-center gap-1.5 shadow-sm transition-all active:scale-95">
                    <span class="material-symbols-outlined text-[16px]" aria-hidden="true">how_to_reg</span>
                    <span>Đăng ký học phần</span>
                </a>
            @endif
            @if (\Illuminate\Support\Facades\Route::has('student.pages.grades'))
                <a href="{{ route('student.pages.grades') }}" class="bg-white border border-outline-variant hover:bg-surface-container-low text-on-surface text-xs font-semibold px-3.5 py-2.5 rounded-lg flex items-center gap-1.5 shadow-xs transition-colors">
                    <span class="material-symbols-outlined text-primary text-[16px]" aria-hidden="true">grade</span>
                    <span>Xem kết quả học tập</span>
                </a>
            @endif
            @if (\Illuminate\Support\Facades\Route::has('student.pages.registration-result'))
                <a href="{{ route('student.pages.registration-result') }}" class="bg-white border border-outline-variant hover:bg-surface-container-low text-on-surface text-xs font-semibold px-3.5 py-2.5 rounded-lg flex items-center gap-1.5 shadow-xs transition-colors">
                    <span class="material-symbols-outlined text-primary text-[16px]" aria-hidden="true">fact_check</span>
                    <span>Kết quả đăng ký</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Overview 4 Stat Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Stat 1: Tiến độ tín chỉ -->
        <div class="bg-white border border-[#E2E8F0] rounded-xl p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-on-surface-variant font-medium">Tiến độ tín chỉ</span>
                <div class="w-7 h-7 rounded-lg bg-primary-fixed flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-[16px]" aria-hidden="true">school</span>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex items-baseline gap-1.5">
                    <span class="text-[22px] font-bold text-on-surface leading-tight">{{ $student['credits_earned'] ?? 84 }}</span>
                    <span class="text-xs text-on-surface-variant font-medium">/ {{ $student['credits_total'] ?? 132 }} TC</span>
                </div>
                <div class="w-full h-2 rounded-full bg-surface-container-high overflow-hidden">
                    <div class="h-full rounded-full bg-primary" style="width: {{ $student['credits_pct'] ?? 63.6 }}%"></div>
                </div>
                <div class="text-[11px] text-on-surface-variant flex items-center justify-between pt-0.5">
                    <span>Đã tích lũy: <strong class="text-primary">{{ $student['credits_pct'] ?? 63.6 }}%</strong></span>
                    <span>Còn lại: <strong>{{ $student['credits_remaining'] ?? 48 }} TC</strong></span>
                </div>
            </div>
        </div>

        <!-- Stat 2: GPA -->
        <div class="bg-white border border-[#E2E8F0] rounded-xl p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-on-surface-variant font-medium">Điểm trung bình (GPA)</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">
                    Xếp loại: {{ $student['classification'] ?? 'Giỏi' }}
                </span>
            </div>
            <div class="space-y-1.5">
                <div class="flex items-baseline gap-1.5">
                    <span class="text-[22px] font-bold text-primary leading-tight">{{ number_format($student['gpa_4'] ?? 3.42, 2) }}</span>
                    <span class="text-xs text-on-surface-variant font-medium">/ 4.0</span>
                </div>
                <div class="text-[11px] text-on-surface-variant pt-1 border-t border-[#F1F5F9] flex items-center justify-between">
                    <span>ĐTB hệ 10: <strong class="text-on-surface">{{ number_format($student['gpa_10'] ?? 8.35, 2) }}</strong></span>
                    <span class="text-emerald-700 font-medium flex items-center gap-0.5">
                        <span class="material-symbols-outlined text-[14px]" aria-hidden="true">trending_up</span> +0.08
                    </span>
                </div>
            </div>
        </div>

        <!-- Stat 3: Lớp học HK1 -->
        <div class="bg-white border border-[#E2E8F0] rounded-xl p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-on-surface-variant font-medium">Lớp học hiện tại</span>
                <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-[16px]" aria-hidden="true">menu_book</span>
                </div>
            </div>
            <div class="space-y-1.5">
                <div class="flex items-baseline gap-1.5">
                    <span class="text-[22px] font-bold text-on-surface leading-tight">{{ $student['current_courses_count'] ?? 6 }}</span>
                    <span class="text-xs text-on-surface-variant font-medium">Học phần</span>
                </div>
                <div class="text-[11px] text-on-surface-variant pt-1 border-t border-[#F1F5F9] flex items-center justify-between">
                    <span>{{ $student['current_credits'] ?? 18 }} tín chỉ</span>
                    <span class="font-medium text-primary">Đang theo học</span>
                </div>
            </div>
        </div>

        <!-- Stat 4: Học phí -->
        <div class="bg-white border border-[#E2E8F0] rounded-xl p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-on-surface-variant font-medium">Tình trạng học phí</span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500" aria-hidden="true"></span> {{ $student['tuition_status'] ?? 'Đã hoàn tất' }}
                </span>
            </div>
            <div class="space-y-1.5">
                <div class="flex items-baseline gap-1">
                    <span class="text-[18px] font-bold text-emerald-700 leading-tight">{{ number_format($student['tuition_debt'] ?? 0) }} VNĐ</span>
                    <span class="text-xs text-on-surface-variant font-normal">(Không nợ)</span>
                </div>
                <div class="text-[11px] text-on-surface-variant pt-1 border-t border-[#F1F5F9] flex items-center justify-between">
                    <span class="text-on-surface-variant font-medium">Học kỳ 1</span>
                    <span class="text-emerald-700 font-semibold">Đã đóng đủ</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 12-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left 8 Columns -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Thời khóa biểu hôm nay -->
            <div class="bg-white border border-[#E2E8F0] rounded-xl shadow-xs p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-[#F1F5F9] pb-3">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[22px]" aria-hidden="true">calendar_today</span>
                        <h2 class="text-[20px] font-bold text-on-surface leading-tight">Thời khóa biểu hôm nay ({{ now()->translatedFormat('l, d/m/Y') }})</h2>
                    </div>
                </div>

                <div class="space-y-3">
                    @if (isset($todayClasses) && count($todayClasses) > 0)
                        @foreach ($todayClasses as $idx => $cls)
                            <div class="p-4 rounded-xl border {{ $cls['status'] === 'ongoing' ? 'border-primary/30 bg-[#EEF2FD]/50' : 'border-outline-variant/60 bg-surface-container-low/30' }} flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded text-xs font-bold {{ $cls['status'] === 'ongoing' ? 'bg-primary text-white' : 'bg-secondary-container text-on-secondary-fixed' }}">{{ $cls['time'] }}</span>
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $cls['status'] === 'ongoing' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">{{ $cls['status_label'] }}</span>
                                    </div>
                                    <h3 class="text-sm font-bold text-on-surface">{{ $cls['course_name'] }}</h3>
                                    <div class="flex flex-wrap items-center gap-x-3 text-xs text-on-surface-variant">
                                        <span>Phòng: <strong class="text-primary font-semibold">{{ $cls['room'] }}</strong></span>
                                        <span>•</span>
                                        <span>Giảng viên: <strong class="text-on-surface">{{ $cls['lecturer'] }}</strong></span>
                                    </div>
                                </div>
                                <span class="self-start sm:self-center px-3 py-1.5 rounded-lg border border-primary/20 bg-white text-primary text-xs font-semibold shadow-xs">
                                    Tiết học
                                </span>
                            </div>
                        @endforeach
                    @else
                        <div class="p-4 rounded-xl border border-primary/30 bg-[#EEF2FD]/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded text-xs font-bold bg-primary text-white">07:30 - 09:10</span>
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">Đang diễn ra</span>
                                </div>
                                <h3 class="text-sm font-bold text-on-surface">Lập trình Web nâng cao</h3>
                                <div class="flex flex-wrap items-center gap-x-3 text-xs text-on-surface-variant">
                                    <span>Phòng: <strong class="text-primary font-semibold">A3.04</strong></span>
                                    <span>•</span>
                                    <span>Giảng viên: <strong class="text-on-surface">TS. Trần Hoàng Nam</strong></span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tiến độ Chương trình đào tạo -->
            <div class="bg-white border border-[#E2E8F0] rounded-xl shadow-xs p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-[#F1F5F9] pb-3">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[22px]" aria-hidden="true">trending_up</span>
                        <h2 class="text-[18px] font-bold text-on-surface">Tiến độ Chương trình đào tạo ({{ $student['credits_earned'] ?? 84 }} / {{ $student['credits_total'] ?? 132 }} TC - {{ $student['credits_pct'] ?? 63.6 }}%)</h2>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="w-full h-3 rounded-full bg-[#E2E8F0] overflow-hidden flex">
                        <div class="h-full bg-primary" style="width: 63.6%" title="Đã tích lũy: 63.6%"></div>
                        <div class="h-full bg-primary-container" style="width: 13.6%" title="Đang học HK1: 13.6%"></div>
                        <div class="h-full bg-outline-variant/60" style="width: 22.8%" title="Còn lại: 22.8%"></div>
                    </div>
                    <div class="flex flex-wrap items-center justify-between text-xs text-on-surface-variant pt-1">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-primary" aria-hidden="true"></span> Đã tích lũy: <strong class="text-on-surface">{{ $student['credits_earned'] ?? 84 }} TC ({{ $student['credits_pct'] ?? 63.6 }}%)</strong>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-primary-container" aria-hidden="true"></span> Đang học HK1: <strong class="text-on-surface">18 TC (13.6%)</strong>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-outline-variant" aria-hidden="true"></span> Còn lại: <strong class="text-on-surface">{{ $student['credits_remaining'] ?? 48 }} TC (22.8%)</strong>
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                    <div class="p-3 rounded-lg border border-[#E2E8F0] bg-surface-container-low/30 space-y-1">
                        <div class="flex justify-between text-xs font-semibold text-on-surface">
                            <span>Đại cương & Cơ sở nhóm ngành</span>
                            <span class="text-emerald-700">100%</span>
                        </div>
                        <div class="w-full h-1.5 rounded-full bg-surface-container-high overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full" style="width: 100%"></div>
                        </div>
                        <div class="text-[11px] text-on-surface-variant">42 / 42 Tín chỉ hoàn thành</div>
                    </div>
                    <div class="p-3 rounded-lg border border-[#E2E8F0] bg-surface-container-low/30 space-y-1">
                        <div class="flex justify-between text-xs font-semibold text-on-surface">
                            <span>Cơ sở ngành</span>
                            <span class="text-primary">84.2%</span>
                        </div>
                        <div class="w-full h-1.5 rounded-full bg-surface-container-high overflow-hidden">
                            <div class="h-full bg-primary rounded-full" style="width: 84.2%"></div>
                        </div>
                        <div class="text-[11px] text-on-surface-variant">32 / 38 Tín chỉ hoàn thành</div>
                    </div>
                    <div class="p-3 rounded-lg border border-[#E2E8F0] bg-surface-container-low/30 space-y-1">
                        <div class="flex justify-between text-xs font-semibold text-on-surface">
                            <span>Chuyên ngành bắt buộc & tự chọn</span>
                            <span class="text-amber-600">52.1%</span>
                        </div>
                        <div class="w-full h-1.5 rounded-full bg-surface-container-high overflow-hidden">
                            <div class="h-full bg-amber-500 rounded-full" style="width: 52.1%"></div>
                        </div>
                        <div class="text-[11px] text-on-surface-variant">25 / 48 Tín chỉ hoàn thành</div>
                    </div>
                    <div class="p-3 rounded-lg border border-[#E2E8F0] bg-surface-container-low/30 space-y-1">
                        <div class="flex justify-between text-xs font-semibold text-on-surface">
                            <span>Khóa luận / Đồ án tốt nghiệp</span>
                            <span class="text-outline">0%</span>
                        </div>
                        <div class="w-full h-1.5 rounded-full bg-surface-container-high overflow-hidden">
                            <div class="h-full bg-outline rounded-full" style="width: 0%"></div>
                        </div>
                        <div class="text-[11px] text-on-surface-variant">0 / 10 Tín chỉ (Chưa đăng ký)</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right 4 Columns -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Kết quả mới cập nhật -->
            <div class="bg-white border border-[#E2E8F0] rounded-xl shadow-xs p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-[#F1F5F9] pb-3">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[20px]" aria-hidden="true">verified</span>
                        <h3 class="text-sm font-bold text-on-surface">Kết quả mới cập nhật</h3>
                    </div>
                    @if (\Illuminate\Support\Facades\Route::has('student.pages.grades'))
                        <a href="{{ route('student.pages.grades') }}" class="text-xs text-primary hover:underline font-semibold">Bảng điểm đầy đủ →</a>
                    @endif
                </div>
                <div class="space-y-2.5 text-xs">
                    @if (isset($recentGrades) && count($recentGrades) > 0)
                        @foreach ($recentGrades as $g)
                            <div class="p-2.5 rounded-lg border border-[#E2E8F0] bg-surface-container-low/30 flex items-center justify-between">
                                <div>
                                    <div class="font-semibold text-on-surface">{{ $g['course_name'] }}</div>
                                    <div class="text-[11px] text-on-surface-variant">{{ $g['credits'] }} tín chỉ • {{ $g['semester'] }}</div>
                                </div>
                                <div class="text-right">
                                    <span class="px-2 py-0.5 rounded font-bold text-emerald-700 bg-emerald-50 border border-emerald-200">{{ $g['score'] }} ({{ $g['letter'] }})</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="p-2.5 rounded-lg border border-[#E2E8F0] bg-surface-container-low/30 flex items-center justify-between">
                            <div>
                                <div class="font-semibold text-on-surface">Lập trình Web API</div>
                                <div class="text-[11px] text-on-surface-variant">3 tín chỉ • HK trước</div>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-0.5 rounded font-bold text-emerald-700 bg-emerald-50 border border-emerald-200">9.5 (A+)</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Thông báo sinh viên -->
            <div class="bg-white border border-[#E2E8F0] rounded-xl shadow-xs p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-[#F1F5F9] pb-3">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[20px]" aria-hidden="true">campaign</span>
                        <h3 class="text-sm font-bold text-on-surface">Thông báo mới</h3>
                    </div>
                    <span class="text-xs text-on-surface-variant font-semibold">Học vụ</span>
                </div>
                <div class="space-y-3">
                    <div class="block p-2.5 rounded-lg border border-[#E2E8F0] hover:bg-surface-container-low transition-colors space-y-1">
                        <div class="flex items-center justify-between text-[10px]">
                            <span class="px-1.5 py-0.5 rounded font-bold bg-[#EEF2FD] text-primary">PHÒNG ĐÀO TẠO</span>
                            <span class="text-on-surface-variant">Hôm nay</span>
                        </div>
                        <h4 class="text-xs font-semibold text-on-surface leading-snug line-clamp-2">
                            Kế hoạch thi giữa kỳ Học kỳ 1 năm học {{ now()->year }}–{{ now()->year + 1 }}
                        </h4>
                    </div>
                    <div class="block p-2.5 rounded-lg border border-[#E2E8F0] hover:bg-surface-container-low transition-colors space-y-1">
                        <div class="flex items-center justify-between text-[10px]">
                            <span class="px-1.5 py-0.5 rounded font-bold bg-amber-200/70 text-amber-900">CỔNG ĐĂNG KÝ</span>
                            <span class="text-on-surface-variant">Đang mở</span>
                        </div>
                        <h4 class="text-xs font-semibold text-on-surface leading-snug line-clamp-2">
                            Mở cổng đăng ký học phần bổ sung cho toàn bộ sinh viên các khóa
                        </h4>
                    </div>
                    <div class="block p-2.5 rounded-lg border border-[#E2E8F0] hover:bg-surface-container-low transition-colors space-y-1">
                        <div class="flex items-center justify-between text-[10px]">
                            <span class="px-1.5 py-0.5 rounded font-bold bg-emerald-100 text-emerald-800">PHÒNG CTSV</span>
                            <span class="text-on-surface-variant">Đợt 1</span>
                        </div>
                        <h4 class="text-xs font-semibold text-on-surface leading-snug line-clamp-2">
                            Danh sách sinh viên nhận học bổng khuyến khích học tập
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
