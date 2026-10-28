@extends('layouts.portal')

@section('title', 'Quản lý Điểm & Duyệt công bố - Quản lý Đào tạo')
@section('page-title', 'Quản lý Điểm học phần')

@section('page')
<div class="space-y-6 pb-16">
    {{-- Header & Thông báo --}}
    <div class="bg-surface-container-lowest p-5 rounded-2xl border border-outline-variant/60 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-headline-sm sm:text-headline-md font-bold text-on-surface">Quản lý Quy trình Duyệt &amp; Công bố Điểm</h1>
            <p class="text-body-sm text-on-surface-variant mt-0.5">Vòng đời nộp điểm, kiểm soát chất lượng, công bố điểm và giải quyết điều chỉnh điểm theo quy chế</p>
        </div>

        {{-- Bộ lọc Học kỳ nhanh --}}
        <form method="GET" action="{{ route('admin.pages.grades') }}" class="flex items-center gap-2">
            <input type="hidden" name="tab" value="{{ $activeTab }}">
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
    </div>

    @if (session('success'))
        <div class="rounded-xl p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm flex items-center gap-2.5 shadow-xs">
            <span class="material-symbols-outlined text-emerald-600 text-[20px]" aria-hidden="true">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div role="alert" class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <p class="font-semibold">Không thể thực hiện thao tác:</p><ul class="list-disc ml-5 mt-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- 4 Thẻ Bento KPI --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-surface-container-lowest border border-outline-variant/60 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-label-md text-on-surface-variant font-medium">Tổng số lớp học phần</span>
                    <div class="text-3xl font-extrabold tracking-tight text-on-surface mt-1">
                        {{ $totalSectionsCount }}
                    </div>
                </div>
                <div class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]" aria-hidden="true">class</span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-outline-variant/30 text-xs text-on-surface-variant">
                <span>Học kỳ được chọn hiện tại</span>
            </div>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant/60 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-label-md text-on-surface-variant font-medium">Sổ điểm chờ duyệt</span>
                    <div class="text-3xl font-extrabold tracking-tight text-blue-700 mt-1">
                        {{ $pendingApprovalCount }}
                    </div>
                </div>
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]" aria-hidden="true">pending_actions</span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-outline-variant/30 text-xs text-on-surface-variant">
                <span>Cần Khoa / Phòng Đào tạo duyệt</span>
            </div>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant/60 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-label-md text-on-surface-variant font-medium">Sổ điểm đã công bố</span>
                    <div class="text-3xl font-extrabold tracking-tight text-emerald-700 mt-1">
                        {{ $publishedCount }}
                    </div>
                </div>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]" aria-hidden="true">verified</span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-outline-variant/30 text-xs text-on-surface-variant">
                <span>Sinh viên đã xem được điểm</span>
            </div>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant/60 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-label-md text-on-surface-variant font-medium">Yêu cầu sửa điểm chờ</span>
                    <div class="text-3xl font-extrabold tracking-tight text-amber-700 mt-1">
                        {{ $pendingChangesCount }}
                    </div>
                </div>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]" aria-hidden="true">edit_note</span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-outline-variant/30 text-xs text-on-surface-variant">
                <span>Điều chỉnh điểm sau công bố</span>
            </div>
        </div>
    </div>

    {{-- Tabs chuyển đổi giữa Quy trình Duyệt điểm & Điều chỉnh điểm sau công bố --}}
    <div class="border-b border-outline-variant/60 flex items-center gap-6">
        <a href="{{ route('admin.pages.grades', ['term_id' => $selectedTermId, 'tab' => 'workflow']) }}"
           class="py-3 px-1 text-sm font-semibold border-b-2 flex items-center gap-2 transition-colors {{ $activeTab === 'workflow' ? 'border-primary text-primary' : 'border-transparent text-on-surface-variant hover:text-on-surface' }}">
            <span class="material-symbols-outlined text-[20px]" aria-hidden="true">rule</span>
            <span>Quy trình Duyệt &amp; Công bố Điểm (#124)</span>
        </a>
        <a href="{{ route('admin.pages.grades', ['term_id' => $selectedTermId, 'tab' => 'adjustments']) }}"
           class="py-3 px-1 text-sm font-semibold border-b-2 flex items-center gap-2 transition-colors {{ $activeTab === 'adjustments' ? 'border-primary text-primary' : 'border-transparent text-on-surface-variant hover:text-on-surface' }}">
            <span class="material-symbols-outlined text-[20px]" aria-hidden="true">edit_calendar</span>
            <span>Điều chỉnh điểm sau công bố (#126)</span>
            @if ($pendingChangesCount > 0)
                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                    {{ $pendingChangesCount }}
                </span>
            @endif
        </a>
    </div>

    <div class="flex flex-wrap items-center gap-2 text-xs">
        <a href="{{ route('admin.pages.grade-gpa') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-primary hover:bg-primary/5"><span class="material-symbols-outlined text-[17px]">monitoring</span> Tra cứu GPA sinh viên (#125)</a>
        <span class="inline-flex items-center gap-1.5 px-3 py-2 text-on-surface-variant"><span class="material-symbols-outlined text-[17px]">description</span> Sổ điểm PDF/Excel: chọn một lớp bên dưới (#127)</span>
    </div>

    @if ($activeTab === 'workflow')
        {{-- ========================================================================= --}}
        {{-- TAB 1: QUY TRÌNH DUYỆT & CÔNG BỐ ĐIỂM (#124)                              --}}
        {{-- ========================================================================= --}}
        <section class="bg-surface-container-lowest rounded-2xl border border-outline-variant/60 shadow-xs overflow-hidden space-y-4">
            {{-- Bộ lọc chi tiết --}}
            <form method="GET" action="{{ route('admin.pages.grades') }}" class="p-4 bg-surface-container-low/50 border-b border-outline-variant/40 flex flex-wrap items-center gap-3 text-xs">
                <input type="hidden" name="tab" value="workflow">
                <input type="hidden" name="term_id" value="{{ $selectedTermId }}">

                <div class="flex items-center gap-2">
                    <span class="font-medium text-on-surface-variant">Khoa:</span>
                    <select name="faculty_id" onchange="this.form.submit()" class="h-9 pl-2.5 pr-8 bg-surface-container-lowest border border-outline-variant rounded-lg font-medium text-on-surface outline-none">
                        <option value="">Tất cả các Khoa</option>
                        @foreach ($faculties as $fac)
                            <option value="{{ $fac->id }}" @selected($selectedFacultyId == $fac->id)>
                                {{ $fac->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <span class="font-medium text-on-surface-variant">Trạng thái:</span>
                    <select name="status" onchange="this.form.submit()" class="h-9 pl-2.5 pr-8 bg-surface-container-lowest border border-outline-variant rounded-lg font-medium text-on-surface outline-none">
                        <option value="all" @selected($selectedStatus === 'all')>Tất cả trạng thái</option>
                        <option value="draft" @selected($selectedStatus === 'draft')>Bản nháp (Chưa nộp)</option>
                        <option value="submitted" @selected($selectedStatus === 'submitted')>Đã nộp (Chờ duyệt)</option>
                        <option value="faculty_approved" @selected($selectedStatus === 'faculty_approved')>Khoa đã duyệt</option>
                        <option value="confirmed" @selected($selectedStatus === 'confirmed')>Đã xác nhận</option>
                        <option value="published" @selected($selectedStatus === 'published')>Đã công bố</option>
                        <option value="returned" @selected($selectedStatus === 'returned')>Bị trả lại</option>
                        <option value="locked" @selected($selectedStatus === 'locked')>Đã khóa</option>
                    </select>
                </div>
            </form>

            @if ($sections->isEmpty())
                <div class="p-12 text-center text-on-surface-variant">
                    <span class="material-symbols-outlined text-outline text-[48px] mb-2" aria-hidden="true">folder_off</span>
                    <p class="text-sm font-medium">Không tìm thấy lớp học phần nào phù hợp với bộ lọc.</p>
                </div>
            @else
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-surface-container-low border-b border-outline-variant/60 text-on-surface-variant font-semibold">
                                <th scope="col" class="py-3 px-3 w-12 text-center">STT</th>
                                <th scope="col" class="py-3 px-3 min-w-[110px]">Mã Lớp HP</th>
                                <th scope="col" class="py-3 px-4 min-w-[180px]">Học phần</th>
                                <th scope="col" class="py-3 px-3 min-w-[120px]">Khoa / Bộ môn</th>
                                <th scope="col" class="py-3 px-3 min-w-[130px]">Giảng viên</th>
                                <th scope="col" class="py-3 px-3 text-center min-w-[130px]">Trạng thái</th>
                                <th scope="col" class="py-3 px-3 text-center min-w-[140px]">Lịch / Ngày công bố</th>
                                <th scope="col" class="py-3 px-4 text-center min-w-[160px]">Thao tác duyệt</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/30 text-on-surface">
                            @foreach ($sections as $idx => $sec)
                                @php
                                    $wf = $workflows->get($sec->id);
                                    $wfStatus = $wf?->status instanceof \App\Modules\Grade\Enums\GradePublicationStatus
                                        ? $wf->status->value
                                        : ($wf?->status ?? 'draft');
                                    $statusBadges = [
                                        'draft' => ['label' => 'Bản nháp', 'class' => 'bg-amber-50 text-amber-800 border-amber-200'],
                                        'submitted' => ['label' => 'Chờ duyệt', 'class' => 'bg-blue-50 text-blue-800 border-blue-200'],
                                        'faculty_approved' => ['label' => 'Khoa đã duyệt', 'class' => 'bg-purple-50 text-purple-800 border-purple-200'],
                                        'confirmed' => ['label' => 'Đã xác nhận', 'class' => 'bg-indigo-50 text-indigo-800 border-indigo-200'],
                                        'published' => ['label' => 'Đã công bố', 'class' => 'bg-emerald-50 text-emerald-800 border-emerald-200'],
                                        'locked' => ['label' => 'Đã khóa', 'class' => 'bg-slate-100 text-slate-800 border-slate-300'],
                                        'returned' => ['label' => 'Bị trả lại', 'class' => 'bg-red-50 text-red-800 border-red-200'],
                                    ];
                                    $b = $statusBadges[$wfStatus] ?? ['label' => ucfirst($wfStatus), 'class' => 'bg-slate-100 text-slate-700 border-slate-200'];
                                    $instructorsStr = $sec->instructors->map(fn($i) => $i->lecturer?->name)->filter()->implode(', ') ?: '—';
                                @endphp
                                <tr class="hover:bg-surface-container-low/40 transition-colors">
                                    <td class="py-3 px-3 text-center font-mono text-outline">{{ $sections->firstItem() + $idx }}</td>
                                    <td class="py-3 px-3 font-mono font-medium text-primary">{{ $sec->code }}</td>
                                    <td class="py-3 px-4 font-medium">
                                        {{ $sec->subject?->name }}
                                        <div class="text-[11px] text-on-surface-variant font-mono">{{ $sec->subject?->credits }} TC • {{ $sec->subject?->code }}</div>
                                    </td>
                                    <td class="py-3 px-3 text-on-surface-variant">
                                        {{ $sec->subject?->department?->faculty?->code ?? 'CNTT' }}
                                    </td>
                                    <td class="py-3 px-3 text-on-surface-variant truncate max-w-[140px]" title="{{ $instructorsStr }}">
                                        {{ $instructorsStr }}
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold border {{ $b['class'] }}">
                                            {{ $b['label'] }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-center text-[11px] text-on-surface-variant">
                                        @if ($wf?->published_at)
                                            <span class="text-emerald-700 font-medium">{{ \Carbon\Carbon::parse($wf->published_at)->format('d/m/Y H:i') }}</span>
                                        @elseif ($wf?->scheduled_at)
                                            <span class="text-indigo-700 font-medium">Hẹn: {{ \Carbon\Carbon::parse($wf->scheduled_at)->format('d/m/Y H:i') }}</span>
                                        @else
                                            <span class="text-outline">—</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                            <a href="{{ route('admin.pages.grade-section', ['courseSection' => $sec->id]) }}" class="px-2 py-1 rounded-md border border-primary/20 text-primary hover:bg-primary/5 font-semibold text-[11px]" title="Chi tiết sổ điểm, lịch sử & báo cáo">Chi tiết</a>
                                            <a href="{{ route('admin.course-sections.gradebook.export', ['courseSection' => $sec->id, 'format' => 'pdf']) }}" class="p-1.5 rounded-lg text-primary hover:bg-primary/5" title="Xuất sổ điểm PDF"><span class="material-symbols-outlined text-[18px]">picture_as_pdf</span></a>
                                            {{-- Xuất sổ điểm --}}
                                            <a href="{{ route('admin.course-sections.gradebook.export', ['courseSection' => $sec->id, 'format' => 'xlsx']) }}" class="p-1.5 rounded-lg text-emerald-700 hover:bg-emerald-50" title="Xuất sổ điểm Excel">
                                                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">table_view</span>
                                            </a>

                                            {{-- Duyệt / Xác nhận / Công bố theo từng trạng thái --}}
                                            @if ($canApprove)
                                                {{-- Nếu đang submitted -> Khoa duyệt hoặc Xác nhận --}}
                                                @if ($wfStatus === 'submitted')
                                                    <form method="POST" action="{{ route('admin.pages.grade-workflow.change', ['courseSection' => $sec->id]) }}">
                                                        @csrf
                                                        <input type="hidden" name="status" value="faculty_approved">
                                                        <button type="submit" class="px-2 py-1 rounded-md text-[11px] font-semibold bg-purple-50 text-purple-700 hover:bg-purple-100 border border-purple-200">
                                                            Khoa duyệt
                                                        </button>
                                                    </form>
                                                @endif

                                                {{-- Nếu faculty_approved -> Xác nhận --}}
                                                @if ($wfStatus === 'faculty_approved')
                                                    <form method="POST" action="{{ route('admin.pages.grade-workflow.change', ['courseSection' => $sec->id]) }}">
                                                        @csrf
                                                        <input type="hidden" name="status" value="confirmed">
                                                        <button type="submit" class="px-2 py-1 rounded-md text-[11px] font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200">
                                                            Xác nhận
                                                        </button>
                                                    </form>
                                                @endif

                                                {{-- Nếu confirmed -> Công bố ngay --}}
                                                @if ($wfStatus === 'confirmed')
                                                    <form method="POST" action="{{ route('admin.pages.grade-workflow.change', ['courseSection' => $sec->id]) }}" onsubmit="return confirm('Công bố điểm chính thức cho lớp học phần này? Điểm sẽ hiển thị cho sinh viên.')">
                                                        @csrf
                                                        <input type="hidden" name="status" value="published">
                                                        <button type="submit" class="px-2 py-1 rounded-md text-[11px] font-semibold bg-emerald-600 text-white hover:bg-emerald-700">
                                                            Công bố
                                                        </button>
                                                    </form>
                                                @endif

                                                {{-- Nếu published -> Khóa sổ --}}
                                                @if ($wfStatus === 'published')
                                                    <form method="POST" action="{{ route('admin.pages.grade-workflow.change', ['courseSection' => $sec->id]) }}" onsubmit="return confirm('Khóa sổ điểm lớp học phần này? Sau khi khóa sẽ không thể thay đổi.')">
                                                        @csrf
                                                        <input type="hidden" name="status" value="locked">
                                                        <button type="submit" class="px-2 py-1 rounded-md text-[11px] font-semibold bg-slate-700 text-white hover:bg-slate-800">
                                                            Khóa sổ
                                                        </button>
                                                    </form>
                                                @endif

                                                {{-- Nút Trả lại điểm nếu chưa published/locked --}}
                                                @if (in_array($wfStatus, ['submitted', 'faculty_approved', 'confirmed']))
                                                    <button type="button" onclick="openReturnModal({{ $sec->id }}, '{{ $sec->code }}')" class="px-2 py-1 rounded-md text-[11px] font-semibold bg-red-50 text-red-700 hover:bg-red-100 border border-red-200" title="Trả lại kèm lý do">
                                                        Trả lại
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-outline-variant/40">
                    {{ $sections->links() }}
                </div>
            @endif
        </section>

    @else
        {{-- ========================================================================= --}}
        {{-- TAB 2: ĐIỀU CHỈNH ĐIỂM SAU CÔNG BỐ (#126)                                 --}}
        {{-- ========================================================================= --}}
        <section class="bg-surface-container-lowest rounded-2xl border border-outline-variant/60 shadow-xs overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-outline-variant/40 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[22px]" aria-hidden="true">history_edu</span>
                        Danh sách Yêu cầu Điều chỉnh Điểm sau Công bố
                    </h3>
                    <p class="text-xs text-on-surface-variant mt-0.5">Mọi thay đổi sau công bố đều được lưu vết lịch sử (versioning) và tự động cập nhật lại GPA</p>
                </div>
            </div>

            @if ($changeRequests->isEmpty())
                <div class="p-12 text-center text-on-surface-variant">
                    <span class="material-symbols-outlined text-outline text-[48px] mb-2" aria-hidden="true">check_circle</span>
                    <p class="text-sm font-medium">Hiện không có yêu cầu điều chỉnh điểm nào.</p>
                </div>
            @else
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-surface-container-low border-b border-outline-variant/60 text-on-surface-variant font-semibold">
                                <th scope="col" class="py-3 px-3 w-12 text-center">STT</th>
                                <th scope="col" class="py-3 px-3 min-w-[90px]">Mã YC</th>
                                <th scope="col" class="py-3 px-3 min-w-[150px]">Sinh viên</th>
                                <th scope="col" class="py-3 px-3 min-w-[160px]">Lớp HP</th>
                                <th scope="col" class="py-3 px-3 min-w-[130px]">Người đề xuất</th>
                                <th scope="col" class="py-3 px-4 min-w-[160px]">Lý do đề xuất</th>
                                <th scope="col" class="py-3 px-3 text-center min-w-[120px]">Trạng thái</th>
                                <th scope="col" class="py-3 px-4 text-center min-w-[140px]">Quyết định</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/30 text-on-surface">
                            @foreach ($changeRequests as $idx => $req)
                                @php
                                    $reqStatus = $req->status instanceof \BackedEnum ? $req->status->value : (string) $req->status;
                                    $reqBadges = [
                                        'pending_faculty' => ['label' => 'Chờ Khoa duyệt', 'class' => 'bg-purple-50 text-purple-700 border-purple-200'],
                                        'pending_confirmation' => ['label' => 'Chờ P.ĐT duyệt', 'class' => 'bg-blue-50 text-blue-700 border-blue-200'],
                                        'pending_acad' => ['label' => 'Chờ P.ĐT duyệt', 'class' => 'bg-blue-50 text-blue-700 border-blue-200'],
                                        'approved' => ['label' => 'Đã phê duyệt', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                                        'rejected' => ['label' => 'Đã từ chối', 'class' => 'bg-red-50 text-red-700 border-red-200'],
                                    ];
                                    $rb = $reqBadges[$reqStatus] ?? ['label' => $reqStatus, 'class' => 'bg-slate-100 text-slate-700 border-slate-200'];
                                @endphp
                                <tr class="hover:bg-surface-container-low/40 transition-colors">
                                    <td class="py-3 px-3 text-center font-mono text-outline">{{ $changeRequests->firstItem() + $idx }}</td>
                                    <td class="py-3 px-3 font-mono font-medium text-primary">#{{ $req->id }}</td>
                                    <td class="py-3 px-3">
                                        <div class="font-medium text-on-surface">{{ $req->enrollment?->student?->full_name ?? '—' }}</div>
                                        <div class="font-mono text-outline text-[11px]">{{ $req->enrollment?->student?->student_code }}</div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="font-medium text-on-surface">{{ $req->enrollment?->section?->code }}</div>
                                        <div class="text-[11px] text-on-surface-variant truncate">{{ $req->enrollment?->section?->subject?->name }}</div>
                                    </td>
                                    <td class="py-3 px-3 text-on-surface-variant">
                                        {{ $req->creator?->name ?? 'Giảng viên' }}
                                    </td>
                                    <td class="py-3 px-4 max-w-xs">
                                        <p class="truncate" title="{{ $req->reason }}">{{ $req->reason }}</p>
                                        @if ($req->evidence_path)
                                            <a href="{{ route('admin.pages.grade-evidence', ['gradeChangeRequest' => $req->id]) }}" class="inline-flex items-center gap-1 text-[11px] text-primary hover:underline mt-0.5">
                                                <span class="material-symbols-outlined text-[14px]">attachment</span>
                                                Minh chứng đính kèm
                                            </a>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold border {{ $rb['class'] }}">
                                            {{ $rb['label'] }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <a href="{{ route('admin.pages.grade-history', ['enrollment' => $req->enrollment_id]) }}" class="text-primary hover:underline text-[11px] inline-block mb-1">Lịch sử điểm</a>
                                        @if (in_array($reqStatus, ['pending_faculty', 'pending_confirmation', 'pending_acad']) && $canApprove)
                                            <div class="flex items-center justify-center gap-1.5">
                                                <form method="POST" action="{{ route('admin.pages.grade-changes.approve', ['gradeChangeRequest' => $req->id]) }}" onsubmit="return confirm('Phê duyệt điều chỉnh điểm này? Hệ thống sẽ ghi nhận lịch sử và tính lại GPA cho sinh viên.')">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-600 text-white hover:bg-emerald-700 shadow-2xs">
                                                        Duyệt
                                                    </button>
                                                </form>
                                                <button type="button" onclick="openRejectModal({{ $req->id }})" class="px-2.5 py-1 rounded-md text-[11px] font-semibold bg-red-50 text-red-700 hover:bg-red-100 border border-red-200">
                                                    Từ chối
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-outline text-[11px]">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-outline-variant/40">
                    {{ $changeRequests->links() }}
                </div>
            @endif
        </section>
    @endif
</div>

{{-- Modal Trả lại điểm kèm lý do (Section) --}}
<div id="return-modal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest rounded-2xl max-w-md w-full border border-outline-variant shadow-lg p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-red-600 text-[22px]" aria-hidden="true">reply</span>
                Trả lại bảng điểm lớp học phần
            </h3>
            <button type="button" onclick="document.getElementById('return-modal').classList.add('hidden')" class="text-outline hover:text-on-surface">
                <span class="material-symbols-outlined text-[20px]" aria-hidden="true">close</span>
            </button>
        </div>

        <form id="return-form" method="POST" action="" class="space-y-4">
            @csrf
            <input type="hidden" name="status" value="returned">
            <p class="text-xs text-on-surface-variant">
                Lớp học phần: <strong id="return-section-code" class="text-primary font-mono">—</strong>. Vui lòng ghi rõ lý do để giảng viên nắm rõ và chỉnh sửa:
            </p>
            <div>
                <textarea name="reason" rows="3" required placeholder="Nhập lý do trả điểm (ví dụ: Thiếu điểm quá trình, điểm thành phần chưa khớp...)" class="w-full text-xs p-3 rounded-xl border border-outline-variant bg-surface-container-low text-on-surface focus:ring-2 focus:ring-primary focus:border-primary outline-none"></textarea>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('return-modal').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-medium text-on-surface-variant hover:bg-surface-container-low">Hủy</button>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-red-600 text-white hover:bg-red-700">Xác nhận trả lại</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Từ chối yêu cầu điều chỉnh điểm --}}
<div id="reject-modal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest rounded-2xl max-w-md w-full border border-outline-variant shadow-lg p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-red-600 text-[22px]" aria-hidden="true">cancel</span>
                Từ chối yêu cầu điều chỉnh điểm
            </h3>
            <button type="button" onclick="document.getElementById('reject-modal').classList.add('hidden')" class="text-outline hover:text-on-surface">
                <span class="material-symbols-outlined text-[20px]" aria-hidden="true">close</span>
            </button>
        </div>

        <form id="reject-form" method="POST" action="" class="space-y-4">
            @csrf
            <p class="text-xs text-on-surface-variant">
                Nhập lý do từ chối yêu cầu điều chỉnh điểm này:
            </p>
            <div>
                <textarea name="reason" rows="3" required placeholder="Lý do từ chối..." class="w-full text-xs p-3 rounded-xl border border-outline-variant bg-surface-container-low text-on-surface focus:ring-2 focus:ring-primary focus:border-primary outline-none"></textarea>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('reject-modal').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-medium text-on-surface-variant hover:bg-surface-container-low">Hủy</button>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-red-600 text-white hover:bg-red-700">Từ chối yêu cầu</button>
            </div>
        </form>
    </div>
</div>

<script>
function openReturnModal(sectionId, sectionCode) {
    document.getElementById('return-section-code').textContent = sectionCode;
    const url = "{{ route('admin.pages.grade-workflow.change', ['courseSection' => '__ID__']) }}".replace('__ID__', sectionId);
    document.getElementById('return-form').action = url;
    document.getElementById('return-modal').classList.remove('hidden');
}

function openRejectModal(requestId) {
    const url = "{{ route('admin.pages.grade-changes.reject', ['gradeChangeRequest' => '__ID__']) }}".replace('__ID__', requestId);
    document.getElementById('reject-form').action = url;
    document.getElementById('reject-modal').classList.remove('hidden');
}
</script>
@endsection
