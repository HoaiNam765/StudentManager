@extends('layouts.portal')

@section('title', 'Kết quả học tập & Điểm - Cổng Sinh viên')
@section('page-title', 'Kết quả học tập')

@section('page')
<div class="space-y-6 pb-12">
    @if (! $student)
        <div class="rounded-2xl p-6 bg-amber-50 border border-amber-200 text-amber-900 flex items-start gap-3 shadow-xs">
            <span class="material-symbols-outlined text-amber-600 text-[28px] shrink-0" aria-hidden="true">warning</span>
            <div>
                <h3 class="font-bold text-base">Chưa liên kết hồ sơ sinh viên</h3>
                <p class="text-sm mt-1 text-amber-800">
                    Tài khoản của bạn hiện chưa được gắn với hồ sơ sinh viên chính thức trong hệ thống đào tạo. Nếu bạn là sinh viên, vui lòng liên hệ Phòng Đào tạo để được kiểm tra.
                </p>
            </div>
        </div>
    @else
        {{-- Thanh chọn Học kỳ & Nút Xuất PDF --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-surface-container-lowest p-5 rounded-2xl border border-outline-variant/60 shadow-xs">
            <div>
                <h1 class="text-headline-sm sm:text-headline-md font-bold text-on-surface">Kết quả học tập học phần</h1>
                <p class="text-body-sm text-on-surface-variant mt-0.5">Dữ liệu kết quả chính thức từ Hệ thống Đào tạo &amp; Khảo thí (HUIT)</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <form method="GET" action="{{ route('student.pages.grades') }}" class="flex items-center gap-2">
                    <div class="relative">
                        <select name="term_id" onchange="this.form.submit()" aria-label="Chọn học kỳ" class="h-10 pl-3.5 pr-9 bg-surface-container-low border border-outline-variant rounded-xl font-label-md text-on-surface focus:ring-2 focus:ring-primary focus:border-primary outline-none appearance-none cursor-pointer">
                            @foreach ($terms as $term)
                                <option value="{{ $term->id }}" @selected($selectedTermId == $term->id)>
                                    {{ $term->name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant text-[18px]" aria-hidden="true">arrow_drop_down</span>
                    </div>
                </form>

                <a href="{{ route('student.transcript.mine', ['term_id' => $selectedTermId]) }}" target="_blank" class="h-10 px-4 rounded-xl bg-primary hover:bg-primary-container text-white font-label-md flex items-center gap-2 shadow-xs transition-colors">
                    <span class="material-symbols-outlined text-[18px]" aria-hidden="true">picture_as_pdf</span>
                    <span>Tải bảng điểm (PDF)</span>
                </a>
            </div>
        </div>

        {{-- 4 Thẻ Bento KPI Học tập --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Card 1: GPA Học kỳ --}}
            <div class="bg-surface-container-lowest border border-outline-variant/60 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-label-md text-on-surface-variant font-medium">GPA Học kỳ này</span>
                        <div class="flex items-baseline gap-2 mt-1">
                            <span class="text-3xl font-extrabold tracking-tight text-primary">
                                {{ $termGpa?->gpa !== null ? number_format($termGpa->gpa, 2) : '—' }}
                            </span>
                            <span class="text-body-sm text-outline">/ 4.00</span>
                        </div>
                    </div>
                    @if ($termGpa?->classification)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            {{ $termGpa->classification }}
                        </span>
                    @endif
                </div>
                <div class="mt-4 pt-3 border-t border-outline-variant/30 flex items-center justify-between text-xs text-on-surface-variant">
                    <span>Tín chỉ đạt kỳ: <strong class="text-on-surface font-semibold">{{ $termGpa?->passed_credits ?? 0 }} TC</strong></span>
                    <span>Tính GPA: <strong class="text-on-surface font-semibold">{{ $termGpa?->total_credits ?? 0 }} TC</strong></span>
                </div>
            </div>

            {{-- Card 2: GPA Tích lũy toàn khóa --}}
            <div class="bg-surface-container-lowest border border-outline-variant/60 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-label-md text-on-surface-variant font-medium">GPA Tích lũy toàn khóa</span>
                        <div class="flex items-baseline gap-2 mt-1">
                            <span class="text-3xl font-extrabold tracking-tight text-on-surface">
                                {{ $cumulativeGpa?->gpa !== null ? number_format($cumulativeGpa->gpa, 2) : '—' }}
                            </span>
                            <span class="text-body-sm text-outline">/ 4.00</span>
                        </div>
                    </div>
                    @if ($cumulativeGpa?->classification)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-primary border border-blue-200">
                            {{ $cumulativeGpa->classification }}
                        </span>
                    @endif
                </div>
                <div class="mt-4 pt-3 border-t border-outline-variant/30 flex items-center justify-between text-xs text-on-surface-variant">
                    <span>Tổng TC tích lũy: <strong class="text-on-surface font-semibold">{{ $cumulativeGpa?->passed_credits ?? 0 }} TC</strong></span>
                    <span>Khóa học: <strong class="text-on-surface font-semibold">{{ $student->cohort?->code ?? 'K' . now()->year }}</strong></span>
                </div>
            </div>

            {{-- Card 3: Tín chỉ đạt / Đăng ký trong kỳ --}}
            <div class="bg-surface-container-lowest border border-outline-variant/60 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-label-md text-on-surface-variant font-medium">Số môn đã công bố</span>
                        <div class="flex items-baseline gap-2 mt-1">
                            <span class="text-3xl font-extrabold tracking-tight text-emerald-700">
                                {{ $courseGrades->count() }}
                            </span>
                            <span class="text-body-sm text-outline">môn</span>
                        </div>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]" aria-hidden="true">task_alt</span>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-outline-variant/30 flex items-center justify-between text-xs text-on-surface-variant">
                    <span>Đạt: <strong class="text-emerald-700 font-semibold">{{ $courseGrades->filter(fn($c) => $c['result']?->passed)->count() }}</strong></span>
                    <span>Không đạt: <strong class="text-error font-semibold">{{ $courseGrades->filter(fn($c) => $c['result'] && ! $c['result']->passed)->count() }}</strong></span>
                </div>
            </div>

            {{-- Card 4: Thông tin sinh viên --}}
            <div class="bg-surface-container-lowest border border-outline-variant/60 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
                <div class="flex items-start justify-between">
                    <div class="min-w-0">
                        <span class="text-label-md text-on-surface-variant font-medium">Hồ sơ sinh viên</span>
                        <h4 class="text-sm font-bold text-on-surface truncate mt-1">{{ $student->full_name }}</h4>
                        <p class="text-xs text-on-surface-variant font-mono">MSSV: {{ $student->student_code }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[20px]" aria-hidden="true">badge</span>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-outline-variant/30 flex items-center justify-between text-xs text-on-surface-variant">
                    <span>Lớp: <strong class="text-on-surface font-semibold">{{ $student->adminClass?->code ?? '—' }}</strong></span>
                    <span>Ngành: <strong class="text-on-surface font-semibold">{{ $student->major?->name ?? 'CNTT' }}</strong></span>
                </div>
            </div>
        </div>

        {{-- Bảng chi tiết điểm học phần --}}
        <section class="bg-surface-container-lowest border border-outline-variant/60 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-outline-variant/40 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="material-symbols-outlined text-primary text-[22px]" aria-hidden="true">table_chart</span>
                    <h2 class="text-base font-bold text-on-surface">Bảng điểm chi tiết học kỳ</h2>
                </div>
                <span class="text-xs text-on-surface-variant bg-surface-container-low px-2.5 py-1 rounded-lg">
                    Chỉ hiển thị điểm đã công bố chính thức
                </span>
            </div>

            @if ($courseGrades->isEmpty())
                <div class="p-12 text-center text-on-surface-variant">
                    <span class="material-symbols-outlined text-outline text-[48px] mb-2" aria-hidden="true">school</span>
                    <p class="text-sm font-medium">Chưa có kết quả điểm học phần nào được công bố trong học kỳ này.</p>
                    <p class="text-xs text-outline mt-1">Khi giảng viên hoàn tất nộp điểm và Phòng Đào tạo duyệt công bố, điểm sẽ hiển thị tại đây.</p>
                </div>
            @else
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-surface-container-low border-b border-outline-variant/60 text-on-surface-variant font-semibold">
                                <th scope="col" class="py-3 px-4 w-12 text-center">STT</th>
                                <th scope="col" class="py-3 px-4 min-w-[100px]">Mã HP</th>
                                <th scope="col" class="py-3 px-4 min-w-[200px]">Tên học phần</th>
                                <th scope="col" class="py-3 px-3 text-center">Số TC</th>
                                <th scope="col" class="py-3 px-4 min-w-[180px]">Điểm thành phần</th>
                                <th scope="col" class="py-3 px-3 text-center">Điểm hệ 10</th>
                                <th scope="col" class="py-3 px-3 text-center">Điểm chữ</th>
                                <th scope="col" class="py-3 px-3 text-center">Điểm hệ 4</th>
                                <th scope="col" class="py-3 px-4 text-center">Kết quả</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/30 text-on-surface">
                            @foreach ($courseGrades as $index => $item)
                                @php
                                    $res = $item['result'];
                                    $passed = $res?->passed;
                                @endphp
                                <tr class="hover:bg-surface-container-low/50 transition-colors">
                                    <td class="py-3 px-4 text-center font-mono text-outline">{{ $index + 1 }}</td>
                                    <td class="py-3 px-4 font-mono font-medium text-primary">{{ $item['subject']?->code }}</td>
                                    <td class="py-3 px-4 font-medium">
                                        {{ $item['subject']?->name }}
                                        @if (! $item['subject']?->counts_toward_gpa)
                                            <span class="ml-1 inline-block px-1.5 py-0.5 rounded text-[10px] bg-slate-100 text-slate-600 border border-slate-200" title="Học phần điều kiện không tính GPA">Điều kiện</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-center font-semibold">{{ $item['subject']?->credits }}</td>
                                    <td class="py-3 px-4">
                                        @if ($item['components']->isNotEmpty())
                                            <div class="flex flex-wrap gap-1.5">
                                                @foreach ($item['components'] as $comp)
                                                    <span class="inline-flex items-center gap-1 bg-surface-container px-2 py-0.5 rounded text-[11px]" title="{{ $comp->component?->name }} ({{ $comp->component?->weight }}%)">
                                                        <span class="text-outline-variant font-medium">{{ $comp->component?->name }}:</span>
                                                        <strong class="font-semibold">{{ $comp->score !== null ? number_format($comp->score, 1) : '—' }}</strong>
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-outline text-[11px]">—</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-center font-bold text-sm">
                                        {{ $res?->score_10 !== null ? number_format($res->score_10, 1) : '—' }}
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        @if ($res?->letter)
                                            <span class="inline-block px-2 py-0.5 rounded font-bold text-xs {{ $res->letter === 'F' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-primary/10 text-primary' }}">
                                                {{ $res->letter }}
                                            </span>
                                        @else
                                            <span class="text-outline">—</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-center font-bold">
                                        {{ $res?->score_4 !== null ? number_format($res->score_4, 2) : '—' }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if ($passed === true)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Đạt
                                            </span>
                                        @elseif ($passed === false)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                Học lại
                                            </span>
                                        @else
                                            <span class="text-outline">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        {{-- Ghi chú quy chế tính điểm & GPA --}}
        <div class="rounded-2xl p-4 sm:p-5 bg-surface-container-low border border-outline-variant/40 text-xs text-on-surface-variant space-y-2">
            <h3 class="font-bold text-on-surface flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px] text-primary" aria-hidden="true">info</span>
                Quy chế tính điểm &amp; xếp loại học lực (HUIT):
            </h3>
            <ul class="list-disc list-inside space-y-1 pl-1 text-[11.5px] leading-relaxed">
                <li>Điểm học phần được tính theo trọng số các thành phần đánh giá (Quá trình, Giữa kỳ, Cuối kỳ) và làm tròn đến 1 chữ số thập phân.</li>
                <li>Thang điểm 4: A (4.0), B+ (3.5), B (3.0), C+ (2.5), C (2.0), D+ (1.5), D (1.0), F (0.0). Điểm F phải đăng ký học lại.</li>
                <li>Học phần có điểm thành phần bắt buộc dưới điểm sàn quy định sẽ tự động xếp điểm F (Không đạt).</li>
                <li>Các học phần điều kiện (Giáo dục thể chất, Giáo dục Quốc phòng - An ninh, Tuần sinh hoạt công dân) không tính vào điểm trung bình tích lũy GPA.</li>
            </ul>
        </div>
    @endif
</div>
@endsection
