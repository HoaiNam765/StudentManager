{{--
    Duyệt và công bố điểm (FR-GRD-004, 005, 011; mock admin/quan-ly-diem). Danh sách lớp dựng ở máy chủ trong phạm vi
    GRD.view; nút bước tiếp theo theo vai trò gọi API qua resources/js/pages/admin-grade-workflows.js. Yêu cầu điều chỉnh
    điểm sau công bố duyệt ở trang riêng (admin.pages.grade-changes).
--}}
@extends('layouts.portal')

@section('title', 'Duyệt & công bố điểm - Quản lý Đào tạo')
@section('page-title', 'Duyệt & công bố điểm')

@section('page')
@php $field = 'h-10 pl-3.5 pr-9 bg-surface-container-low border border-outline-variant rounded-xl text-sm text-on-surface focus:ring-2 focus:ring-primary focus:border-primary outline-none'; @endphp
<div data-page="admin-grade-workflows" class="space-y-6 pb-16">
    <div class="bg-surface-container-lowest p-5 rounded-2xl border border-outline-variant/60 shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-headline-sm sm:text-headline-md font-bold text-on-surface">Duyệt &amp; công bố điểm học phần</h1>
            <p class="text-body-sm text-on-surface-variant mt-0.5">Nộp → khoa duyệt → Phòng Đào tạo xác nhận → công bố → khóa sổ. Trả lại phải ghi lý do.</p>
        </div>
        <form method="GET" action="{{ route('admin.pages.grades') }}" class="flex flex-wrap items-center gap-2">
            <select name="term_id" onchange="this.form.submit()" aria-label="Học kỳ" class="{{ $field }}">
                @foreach ($terms as $term)
                    <option value="{{ $term->id }}" @selected($selectedTermId == $term->id)>{{ $term->name }} – {{ $term->academicYear?->name }}</option>
                @endforeach
            </select>
            <select name="faculty_id" onchange="this.form.submit()" aria-label="Khoa" class="{{ $field }}">
                <option value="">Tất cả khoa trong phạm vi</option>
                @foreach ($faculties as $faculty)
                    <option value="{{ $faculty->id }}" @selected($selectedFacultyId == $faculty->id)>{{ $faculty->name }}</option>
                @endforeach
            </select>
            <select name="status" onchange="this.form.submit()" aria-label="Trạng thái bảng điểm" class="{{ $field }}">
                <option value="">Mọi trạng thái</option>
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" @selected($selectedStatus === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div data-region="alert" hidden></div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ([
            ['Lớp cần bảng điểm', $kpi['total'], 'class', 'text-on-surface', 'bg-primary/10 text-primary', 'Đang học / kết thúc / đã khóa trong học kỳ'],
            ['Đang chờ duyệt', $kpi['pending'], 'pending_actions', 'text-blue-700', 'bg-blue-50 text-blue-700', 'Đã nộp, khoa đã duyệt, đã xác nhận'],
            ['Đã công bố / khóa', $kpi['published'], 'verified', 'text-emerald-700', 'bg-emerald-50 text-emerald-700', 'Sinh viên đã xem được điểm'],
            ['Yêu cầu sửa điểm chờ bạn', $kpi['changes'], 'edit_note', 'text-amber-700', 'bg-amber-50 text-amber-700', 'Điều chỉnh điểm sau công bố'],
        ] as [$label, $value, $icon, $color, $badge, $hint])
            <div class="bg-surface-container-lowest border border-outline-variant/60 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-label-md text-on-surface-variant font-medium">{{ $label }}</span>
                        <div class="text-3xl font-extrabold tracking-tight mt-1 {{ $color }}">{{ $value }}</div>
                    </div>
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center {{ $badge }}"><span class="material-symbols-outlined text-[20px]" aria-hidden="true">{{ $icon }}</span></div>
                </div>
                <div class="mt-4 pt-3 border-t border-outline-variant/30 text-xs text-on-surface-variant">
                    {{ $hint }}
                    @if ($icon === 'edit_note' && Route::has('admin.pages.grade-changes'))
                        · <a href="{{ route('admin.pages.grade-changes') }}" class="text-primary font-semibold hover:underline">Xem và duyệt</a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <section class="bg-surface-container-lowest rounded-2xl border border-outline-variant/60 shadow-xs overflow-hidden">
        @if ($sections->isEmpty())
            <div class="p-12 text-center text-on-surface-variant">
                <span class="material-symbols-outlined text-outline text-[48px]" aria-hidden="true">fact_check</span>
                <p class="text-sm font-medium mt-2">Không có lớp học phần nào phù hợp bộ lọc trong phạm vi của bạn.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse min-w-[960px]">
                    <thead>
                        <tr class="bg-surface-container-low border-b border-outline-variant/60 text-on-surface-variant font-semibold">
                            <th scope="col" class="py-3 px-3">Lớp học phần</th>
                            <th scope="col" class="py-3 px-3">Học phần</th>
                            <th scope="col" class="py-3 px-3">Giảng viên</th>
                            <th scope="col" class="py-3 px-3">Trạng thái bảng điểm</th>
                            <th scope="col" class="py-3 px-3">Sổ điểm</th>
                            <th scope="col" class="py-3 px-3">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/30 text-on-surface">
                        @foreach ($sections as $section)
                            @php
                                $workflow = $workflows->get($section->id);
                                $status = $workflow?->status ?? \App\Modules\Grade\Enums\GradePublicationStatus::Draft;
                                $badge = match ($status->value) {
                                    'submitted' => 'bg-blue-50 text-blue-800 border-blue-200',
                                    'faculty_approved' => 'bg-purple-50 text-purple-800 border-purple-200',
                                    'confirmed' => 'bg-indigo-50 text-indigo-800 border-indigo-200',
                                    'published' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                    'locked' => 'bg-slate-100 text-slate-800 border-slate-300',
                                    'returned' => 'bg-red-50 text-red-800 border-red-200',
                                    default => 'bg-amber-50 text-amber-800 border-amber-200',
                                };
                            @endphp
                            <tr class="hover:bg-surface-container-low/40 align-top">
                                <td class="py-3 px-3 font-mono font-semibold text-primary">{{ $section->code }}</td>
                                <td class="py-3 px-3">{{ $section->subject?->name }} <span class="text-outline">({{ $section->subject?->credits }} TC)</span></td>
                                <td class="py-3 px-3">{{ $section->instructors->map(fn ($i) => $i->lecturer?->full_name)->filter()->implode(', ') ?: '—' }}</td>
                                <td class="py-3 px-3">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full border font-semibold text-[11px] {{ $badge }}">{{ $status->label() }}</span>
                                    @if ($workflow?->scheduled_at)
                                        <div class="text-[11px] text-outline mt-1">Hẹn công bố {{ $workflow->scheduled_at->timezone(config('studentmanager.display_timezone'))->format('H:i d/m/Y') }}</div>
                                    @endif
                                    @if ($status->value === 'returned' && $workflow?->return_reason)
                                        <div class="text-[11px] text-red-800 mt-1">Lý do: {{ $workflow->return_reason }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <a href="{{ route('admin.course-sections.grade-report', ['courseSection' => $section->id, 'format' => 'pdf']) }}" class="inline-flex items-center gap-1 text-primary hover:underline" title="Sổ điểm PDF"><span class="material-symbols-outlined text-[17px]" aria-hidden="true">picture_as_pdf</span>PDF</a>
                                    <a href="{{ route('admin.course-sections.grade-report', ['courseSection' => $section->id, 'format' => 'xlsx']) }}" class="inline-flex items-center gap-1 text-emerald-700 hover:underline ml-2" title="Sổ điểm Excel"><span class="material-symbols-outlined text-[17px]" aria-hidden="true">table_view</span>Excel</a>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="flex flex-wrap gap-1.5">
                                        @forelse ($actions[$section->id] ?? [] as $action)
                                            <button type="button" data-action="step" data-section="{{ $section->id }}" data-code="{{ $section->code }}" data-status="{{ $action['status'] }}" data-label="{{ $action['label'] }}"
                                                    class="px-2.5 py-1 rounded-md text-[11px] font-semibold border {{ $action['status'] === 'returned' ? 'bg-red-50 text-red-700 border-red-200 hover:bg-red-100' : 'bg-primary text-white border-primary hover:bg-primary-container' }}">
                                                {{ $action['label'] }}
                                            </button>
                                        @empty
                                            <span class="text-[11px] text-outline">{{ $status->value === 'draft' || $status->value === 'returned' ? 'Chờ giảng viên nộp' : '—' }}</span>
                                        @endforelse
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-outline-variant/40">{{ $sections->links() }}</div>
        @endif
    </section>
</div>
@endsection
