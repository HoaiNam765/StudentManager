<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Phiếu đăng ký học phần {{ $student['student_code'] }}</title>
    <style>
        @page { margin: 28px 32px; }
        body { font-family: "{{ $font }}", sans-serif; font-size: 11px; color: #111; }
        .header { width: 100%; }
        .header td { vertical-align: top; text-align: center; }
        .school { font-weight: bold; text-transform: uppercase; }
        h1 { text-align: center; font-size: 16px; margin: 18px 0 4px; }
        .sub { text-align: center; margin-bottom: 14px; }
        .info td { padding: 2px 8px 2px 0; }
        table.grid { border-collapse: collapse; width: 100%; margin-top: 8px; }
        table.grid th, table.grid td { border: 1px solid #555; padding: 4px; }
        table.grid th { background: #e8e8e8; }
        .num { text-align: center; }
        .muted { color: #666; }
        .sign { width: 100%; margin-top: 28px; }
        .sign td { text-align: center; width: 50%; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td style="width: 50%">
                <div class="school">{{ $school }}</div>
                <div>PHÒNG ĐÀO TẠO</div>
            </td>
            <td>
                <div><strong>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</strong></div>
                <div>Độc lập – Tự do – Hạnh phúc</div>
            </td>
        </tr>
    </table>

    <h1>PHIẾU ĐĂNG KÝ HỌC PHẦN</h1>
    <div class="sub">{{ $term['name'] }} – Năm học {{ $term['academic_year'] }}</div>

    <table class="info">
        <tr><td>Họ và tên:</td><td><strong>{{ $student['full_name'] }}</strong></td><td>MSSV:</td><td><strong>{{ $student['student_code'] }}</strong></td></tr>
        <tr><td>Ngành:</td><td>{{ $student['major'] }}</td><td>Khóa:</td><td>{{ $student['cohort'] }}</td></tr>
    </table>

    <table class="grid">
        <thead>
            <tr>
                <th>STT</th><th>Mã HP</th><th>Tên học phần</th><th>TC</th><th>Lớp học phần</th><th>Loại</th><th>Lịch học</th><th>Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $i => $item)
                <tr>
                    <td class="num">{{ $i + 1 }}</td>
                    <td>{{ $item['subject']['code'] }}</td>
                    <td>{{ $item['subject']['name'] }}</td>
                    <td class="num">{{ $item['subject']['credits'] }}</td>
                    <td>{{ $item['section'] }}</td>
                    <td>{{ $item['type_label'] }}</td>
                    <td>{!! collect($item['schedule'])->map(fn ($s) => e($s))->implode('<br>') ?: '<span class="muted">Chưa xếp lịch</span>' !!}</td>
                    <td>{{ $item['status_label'] }}{{ $item['counts_for_gpa'] ? '' : ' (không tính GPA)' }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="num muted">Chưa đăng ký học phần nào trong học kỳ này.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" style="text-align: right">Tổng số tín chỉ đăng ký</th>
                <th class="num">{{ $total_credits }}</th>
                <th colspan="4" style="text-align: left; font-weight: normal">
                    @if ($credit_limits)
                        Khung cho phép: {{ $credit_limits['min'] }}–{{ $credit_limits['max'] }} tín chỉ ({{ $credit_limits['basis'] }})
                    @endif
                </th>
            </tr>
        </tfoot>
    </table>

    <table class="sign">
        <tr>
            <td></td>
            <td><em>In lúc {{ $printed_at }}</em><br><strong>Sinh viên</strong><br><br><br><br>{{ $student['full_name'] }}</td>
        </tr>
    </table>
</body>
</html>
