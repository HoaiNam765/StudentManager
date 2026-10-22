@extends('layouts.portal')

@section('title', 'Quản lý & Nhập điểm - Cổng Giảng viên')
@section('page-title', 'Nhập điểm lớp học phần')

@section('page')
<div class="space-y-6 pb-16" @if ($selectedSection) data-page="teacher-gradebook" @endif>
    {{-- Thanh chọn Lớp học phần & Trạng thái --}}
    <div class="bg-surface-container-lowest p-5 rounded-2xl border border-outline-variant/60 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-headline-sm sm:text-headline-md font-bold text-on-surface">Quản lý Sổ điểm Lớp học phần</h1>
            <p class="text-body-sm text-on-surface-variant mt-0.5">Nhập điểm thành phần, quản lý tiến độ nộp và công bố điểm theo quy chế HUIT</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <form method="GET" action="{{ route('teacher.pages.gradebook') }}" class="flex items-center gap-2">
                <div class="relative">
                    <select name="course_section_id" onchange="this.form.submit()" aria-label="Chọn lớp học phần" class="h-10 pl-3.5 pr-9 bg-surface-container-low border border-outline-variant rounded-xl font-label-md text-on-surface focus:ring-2 focus:ring-primary focus:border-primary outline-none appearance-none cursor-pointer max-w-xs sm:max-w-sm truncate">
                        @forelse ($sections as $sec)
                            <option value="{{ $sec->id }}" @selected($selectedSection?->id == $sec->id)>
                                {{ $sec->code }} — {{ $sec->subject?->name }} ({{ $sec->term?->name }} {{ $sec->term?->academicYear?->name }})
                            </option>
                        @empty
                            <option value="">(Chưa có lớp học phần được phân công)</option>
                        @endforelse
                    </select>
                    <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant text-[18px]" aria-hidden="true">arrow_drop_down</span>
                </div>
            </form>
        </div>
    </div>

    <div data-region="alert" hidden></div>

    @if (! $selectedSection)
        <div class="bg-surface-container-lowest border border-outline-variant/60 rounded-2xl p-12 text-center text-on-surface-variant shadow-xs">
            <span class="material-symbols-outlined text-outline text-[48px] mb-2" aria-hidden="true">fact_check</span>
            <h3 class="font-bold text-base text-on-surface">Chưa có lớp học phần nào để hiển thị sổ điểm</h3>
            <p class="text-sm mt-1 text-on-surface-variant max-w-md mx-auto">
                Hiện tại tài khoản chưa được phân công lớp học phần nào, hoặc chưa có lớp học phần nào đang hoạt động.
            </p>
        </div>
    @else
        @php
            $status = $workflow?->status instanceof \App\Modules\Grade\Enums\GradePublicationStatus
                ? $workflow->status->value
                : ($workflow?->status ?? 'draft');
            $statusLabels = [
                'draft' => ['label' => 'Bản nháp • Đang nhập điểm', 'class' => 'bg-amber-50 text-amber-800 border-amber-200', 'dot' => 'bg-amber-500'],
                'submitted' => ['label' => 'Đã nộp • Chờ duyệt', 'class' => 'bg-blue-50 text-blue-800 border-blue-200', 'dot' => 'bg-blue-500'],
                'faculty_approved' => ['label' => 'Khoa đã duyệt • Chờ P.ĐT', 'class' => 'bg-purple-50 text-purple-800 border-purple-200', 'dot' => 'bg-purple-500'],
                'confirmed' => ['label' => 'P.ĐT đã xác nhận • Chờ công bố', 'class' => 'bg-indigo-50 text-indigo-800 border-indigo-200', 'dot' => 'bg-indigo-500'],
                'published' => ['label' => 'Đã công bố điểm chính thức', 'class' => 'bg-emerald-50 text-emerald-800 border-emerald-200', 'dot' => 'bg-emerald-500'],
                'locked' => ['label' => 'Đã khóa sổ điểm', 'class' => 'bg-slate-100 text-slate-800 border-slate-300', 'dot' => 'bg-slate-500'],
                'returned' => ['label' => 'Bị trả lại • Cần chỉnh sửa', 'class' => 'bg-red-50 text-red-800 border-red-200', 'dot' => 'bg-red-500'],
            ];
            $curStatus = $statusLabels[$status] ?? ['label' => ucfirst($status), 'class' => 'bg-slate-100 text-slate-700 border-slate-200', 'dot' => 'bg-slate-400'];
        @endphp

        {{-- Hero Card: Chi tiết Lớp học phần & Cụm nút Thao tác --}}
        <section class="bg-surface-container-lowest rounded-2xl border border-outline-variant/60 shadow-xs p-5 sm:p-6 space-y-5">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2.5">
                        <span class="px-2.5 py-1 rounded-md bg-primary-fixed text-on-primary-fixed font-bold text-xs font-mono border border-primary/20">
                            {{ $selectedSection->code }}
                        </span>
                        <h2 class="text-xl font-bold text-on-surface tracking-tight">
                            {{ $selectedSection->subject?->name }}
                        </h2>
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-surface-container text-on-surface-variant border border-outline-variant/60">
                            {{ $selectedSection->subject?->credits }} Tín chỉ
                        </span>

                        {{-- Huy hiệu trạng thái sổ điểm --}}
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border {{ $curStatus['class'] }}">
                            <span class="w-2 h-2 rounded-full {{ $curStatus['dot'] }}"></span>
                            {{ $curStatus['label'] }}
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center gap-x-6 gap-y-1.5 text-xs text-on-surface-variant pt-1">
                        <span class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px]" aria-hidden="true">calendar_today</span>
                            Học kỳ: <strong class="text-on-surface">{{ $selectedSection->term?->name }} {{ $selectedSection->term?->academicYear?->name }}</strong>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px]" aria-hidden="true">groups</span>
                            Sĩ số: <strong class="text-on-surface">{{ count($gradebookData['students'] ?? []) }} Sinh viên</strong>
                        </span>
                    </div>

                    @if ($workflow?->return_reason)
                        <div class="mt-2 p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-900 flex items-start gap-2">
                            <span class="material-symbols-outlined text-red-600 text-[18px] shrink-0" aria-hidden="true">info</span>
                            <div>
                                <span class="font-bold">Lý do trả về từ cấp duyệt:</span>
                                <p class="mt-0.5">{{ $workflow->return_reason }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Cụm nút Thao tác --}}
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    {{-- Tải file mẫu --}}
                    <a href="{{ route('teacher.gradebook.template', ['courseSection' => $selectedSection->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-medium bg-surface-container-low hover:bg-surface-container text-on-surface border border-outline-variant/60 transition-colors shadow-2xs" title="Tải mẫu Excel nhập điểm">
                        <span class="material-symbols-outlined text-[18px]" aria-hidden="true">download</span>
                        <span>Mẫu Excel</span>
                    </a>

                    {{-- Nhập Excel (nếu được sửa) --}}
                    @if ($canEdit)
                        <button type="button" onclick="document.getElementById('import-modal').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200 transition-colors shadow-2xs">
                            <span class="material-symbols-outlined text-[18px]" aria-hidden="true">upload_file</span>
                            <span>Nhập Excel</span>
                        </button>
                    @endif

                    {{-- Xuất sổ điểm (XLSX / PDF) --}}
                    <div class="relative inline-block text-left" id="export-dropdown-wrapper">
                        <button type="button" onclick="document.getElementById('export-menu').classList.toggle('hidden')" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-medium bg-surface-container-low hover:bg-surface-container text-on-surface border border-outline-variant/60 transition-colors shadow-2xs">
                            <span class="material-symbols-outlined text-[18px]" aria-hidden="true">print</span>
                            <span>Xuất sổ điểm</span>
                            <span class="material-symbols-outlined text-[16px]" aria-hidden="true">arrow_drop_down</span>
                        </button>
                        <div id="export-menu" class="hidden absolute right-0 mt-2 w-44 rounded-xl bg-surface-container-lowest border border-outline-variant shadow-md z-20 py-1">
                            <a href="{{ route('teacher.grade-report', ['courseSection' => $selectedSection->id, 'format' => 'xlsx']) }}" class="flex items-center gap-2 px-3.5 py-2 text-xs text-on-surface hover:bg-surface-container-low">
                                <span class="material-symbols-outlined text-[18px] text-emerald-600" aria-hidden="true">table_view</span>
                                <span>Xuất Excel (.xlsx)</span>
                            </a>
                            <a href="{{ route('teacher.grade-report', ['courseSection' => $selectedSection->id, 'format' => 'pdf']) }}" class="flex items-center gap-2 px-3.5 py-2 text-xs text-on-surface hover:bg-surface-container-low">
                                <span class="material-symbols-outlined text-[18px] text-red-600" aria-hidden="true">picture_as_pdf</span>
                                <span>Xuất PDF</span>
                            </a>
                        </div>
                    </div>

                    {{-- Nộp điểm --}}
                    @if ($canEdit)
                        <button type="button" data-action="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-primary hover:bg-primary-container transition-colors shadow-xs">
                            <span class="material-symbols-outlined text-[18px]" aria-hidden="true">send</span>
                            <span>Nộp điểm</span>
                        </button>
                    @endif
                </div>
            </div>

            {{-- Thanh thành phần trọng số điểm --}}
            <div class="p-3.5 bg-surface-container-low rounded-xl border border-outline-variant/40 flex flex-wrap items-center gap-2 text-xs text-on-surface">
                <span class="font-bold flex items-center gap-1 text-primary">
                    <span class="material-symbols-outlined text-[16px]" aria-hidden="true">calculate</span>
                    Cơ cấu điểm:
                </span>
                @forelse ($gradebookData['components'] ?? [] as $c)
                    <span class="px-2.5 py-1 bg-surface-container-lowest rounded-lg border border-outline-variant/60 font-medium">
                        {{ $c['name'] }}: <strong class="text-primary">{{ (float) $c['weight'] }}%</strong>
                        @if (! empty($c['min_score']))
                            <span class="text-error text-[11px]">(Sàn: {{ (float) $c['min_score'] }})</span>
                        @endif
                    </span>
                    @if (! $loop->last) <span class="text-outline">+</span> @endif
                @empty
                    <span class="text-outline">Chưa thiết lập cơ cấu điểm cho lớp này.</span>
                    @if ($canEdit)
                        <button type="button" data-action="initialize" class="ml-2 px-3 py-1 rounded-lg bg-primary text-white font-semibold hover:bg-primary-container">Khởi tạo theo cơ cấu của học phần</button>
                    @endif
                @endforelse
            </div>
        </section>

        {{-- Bảng Nhập điểm Sổ lớp --}}
        <section class="bg-surface-container-lowest rounded-2xl border border-outline-variant/60 shadow-xs overflow-hidden">
            <form id="draft-form" data-form="draft" novalidate>
                <div class="p-4 sm:p-5 border-b border-outline-variant/40 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-primary text-[22px]" aria-hidden="true">table_chart</span>
                        <h3 class="text-base font-bold text-on-surface">Danh sách sinh viên &amp; Bảng điểm</h3>
                    </div>

                    @if ($canEdit)
                        <div class="flex items-center gap-2">
                            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold bg-primary text-white hover:bg-primary-container transition-colors shadow-xs">
                                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">save</span>
                                <span>Lưu nháp điểm</span>
                            </button>
                        </div>
                    @else
                        <span class="text-xs text-on-surface-variant bg-surface-container-low px-3 py-1 rounded-lg">
                            Chế độ chỉ xem (Sổ điểm ở trạng thái: {{ $curStatus['label'] }})
                        </span>
                    @endif
                </div>

                @if (empty($gradebookData['students']))
                    <div class="p-12 text-center text-on-surface-variant">
                        <span class="material-symbols-outlined text-outline text-[48px] mb-2" aria-hidden="true">group_off</span>
                        <p class="text-sm font-medium">Lớp học phần này hiện chưa có sinh viên nào đăng ký.</p>
                    </div>
                @else
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-surface-container-low border-b border-outline-variant/60 text-on-surface-variant font-semibold">
                                    <th scope="col" class="py-3 px-3 w-12 text-center">STT</th>
                                    <th scope="col" class="py-3 px-3 min-w-[100px]">MSSV</th>
                                    <th scope="col" class="py-3 px-4 min-w-[180px]">Họ và tên</th>
                                    @foreach ($gradebookData['components'] ?? [] as $comp)
                                        <th scope="col" class="py-3 px-3 text-center min-w-[100px]">
                                            <div>{{ $comp['name'] }}</div>
                                            <div class="text-[10.5px] font-normal text-outline">({{ (float) $comp['weight'] }}%)</div>
                                        </th>
                                    @endforeach
                                    <th scope="col" class="py-3 px-3 text-center min-w-[70px]">Tổng kết</th>
                                    <th scope="col" class="py-3 px-3 text-center min-w-[60px]">Điểm chữ</th>
                                    <th scope="col" class="py-3 px-3 text-center min-w-[70px]">Hệ 4</th>
                                    <th scope="col" class="py-3 px-3 text-center min-w-[90px]">Kết quả</th>
                                    <th scope="col" class="py-3 px-3 text-center min-w-[150px]">Trạng thái đặc biệt</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant/30 text-on-surface">
                                @foreach ($gradebookData['students'] as $idx => $st)
                                    @php
                                        $scoresByComp = collect($st['scores'] ?? [])->keyBy('component_id');
                                        $final = $results->get($st['enrollment_id']);
                                        $special = $final?->special_status;
                                    @endphp
                                    <tr class="hover:bg-surface-container-low/40 transition-colors">
                                        <td class="py-3 px-3 text-center font-mono text-outline">{{ $idx + 1 }}</td>
                                        <td class="py-3 px-3 font-mono font-medium text-primary">{{ $st['student']['student_code'] ?? '—' }}</td>
                                        <td class="py-3 px-4 font-medium">{{ $st['student']['full_name'] ?? '—' }}</td>

                                        @foreach ($gradebookData['components'] ?? [] as $comp)
                                            @php
                                                $scoreItem = $scoresByComp->get($comp['id']);
                                                $scoreVal = $scoreItem['score'] ?? null;
                                            @endphp
                                            <td class="py-2 px-2 text-center">
                                                @if ($canEdit)
                                                    <input type="number" step="{{ config('grades.score_step') }}" min="{{ config('grades.min_score') }}" max="{{ config('grades.max_score') }}"
                                                           data-enrollment="{{ $st['enrollment_id'] }}" data-component="{{ $comp['id'] }}"
                                                           data-original="{{ $scoreVal !== null ? (float) $scoreVal : '' }}"
                                                           aria-label="{{ $comp['name'] }} {{ $st['student']['student_code'] ?? '' }}"
                                                           value="{{ $scoreVal !== null ? (float) $scoreVal : '' }}"
                                                           class="w-16 h-8 text-center bg-surface-container-lowest border border-outline-variant rounded-lg font-mono text-xs focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-colors">
                                                @else
                                                    <span class="font-mono font-semibold {{ $scoreVal !== null ? 'text-on-surface' : 'text-outline' }}">
                                                        {{ $scoreVal !== null ? number_format((float) $scoreVal, 1) : '—' }}
                                                    </span>
                                                @endif
                                            </td>
                                        @endforeach

                                        <td class="py-3 px-3 text-center font-bold text-sm">
                                            {{ $final?->score10 !== null ? number_format((float) $final->score10, 1, ',', '') : '—' }}
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            @if (! empty($final?->letter))
                                                <span class="inline-block px-1.5 py-0.5 rounded font-bold text-[11px] {{ $final->letter === 'F' ? 'bg-red-50 text-red-700' : 'bg-primary/10 text-primary' }}">
                                                    {{ $final->letter }}
                                                </span>
                                            @else
                                                <span class="text-outline">—</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3 text-center font-mono font-semibold">
                                            {{ $final?->point4 !== null ? number_format((float) $final->point4, 1, ',', '') : '—' }}
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            @if ($final?->is_passed === true)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    Đạt
                                                </span>
                                            @elseif ($final?->is_passed === false)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-red-50 text-red-700 border border-red-200">
                                                    Không đạt
                                                </span>
                                            @elseif ($final?->status === 'incomplete')
                                                <span class="text-[11px] text-amber-800">Chưa đủ dữ liệu</span>
                                            @else
                                                <span class="text-outline">—</span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-2 text-center">
                                            @if ($canEdit)
                                                <select data-special="{{ $st['enrollment_id'] }}" data-original="{{ $special }}" aria-label="Trạng thái đặc biệt {{ $st['student']['student_code'] ?? '' }}"
                                                        class="h-8 max-w-[150px] px-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-[11px]">
                                                    <option value="">—</option>
                                                    @foreach ($specialStatuses as $code => $label)
                                                        <option value="{{ $code }}" @selected($special === $code)>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <span class="text-[11px] text-on-surface-variant">{{ $special ? ($specialStatuses[$special] ?? $special) : '—' }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </form>
        </section>

        {{-- Modal Nhập Excel --}}
        <div id="import-modal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-surface-container-lowest rounded-2xl max-w-md w-full border border-outline-variant shadow-lg p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-emerald-600 text-[22px]" aria-hidden="true">upload_file</span>
                        Nhập điểm từ file Excel
                    </h3>
                    <button type="button" onclick="document.getElementById('import-modal').classList.add('hidden')" class="text-outline hover:text-on-surface">
                        <span class="material-symbols-outlined text-[20px]" aria-hidden="true">close</span>
                    </button>
                </div>

                <form data-form="import" class="space-y-4" novalidate>
                    <p class="text-xs text-on-surface-variant">
                        Chọn file <strong>.xlsx</strong> đúng định dạng tải từ nút <em>Mẫu Excel</em>. Điểm sau khi nhập sẽ được lưu tự động vào bản nháp.
                    </p>
                    <div>
                        <input type="file" name="file" accept=".xlsx" required class="block w-full text-xs text-on-surface border border-outline-variant rounded-xl cursor-pointer bg-surface-container-low file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-container">
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" onclick="document.getElementById('import-modal').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-medium text-on-surface-variant hover:bg-surface-container-low">Hủy</button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-primary text-white hover:bg-primary-container">Tải lên &amp; Lưu nháp</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
