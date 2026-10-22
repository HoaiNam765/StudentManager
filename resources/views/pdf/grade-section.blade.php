<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sổ điểm {{ $report['section']['code'] }}</title>
    <style>
        @page { margin: 24px 28px; }
        body { font-family: "{{ $font }}", sans-serif; font-size: 10px; color: #111; }
        .header { width: 100%; }
        .header td { vertical-align: top; text-align: center; }
        .school { font-weight: bold; text-transform: uppercase; }
        h1 { text-align: center; font-size: 15px; margin: 16px 0 4px; }
        .sub { text-align: center; margin-bottom: 10px; }
        table.grid { border-collapse: collapse; width: 100%; }
        table.grid th, table.grid td { border: 1px solid #555; padding: 3px; }
        table.grid th { background: #e8e8e8; }
        .num { text-align: center; }
        .stats td { padding: 2px 12px 2px 0; }
        .sign { width: 100%; margin-top: 24px; }
        .sign td { text-align: center; width: 33%; vertical-align: top; }
    </style>
</head>
<body>
    @php($subject = $report['section']['subject'] ?? [])
    @php($stats = $report['statistics'])
    <table class="header">
        <tr>
            <td style="width: 50%"><div class="school">{{ $school }}</div><div>PHÒNG ĐÀO TẠO</div></td>
            <td><div><strong>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</strong></div><div>Độc lập – Tự do – Hạnh phúc</div></td>
        </tr>
    </table>

    <h1>BẢNG ĐIỂM LỚP HỌC PHẦN</h1>
    <div class="sub">
        {{ $subject['code'] ?? '' }} – {{ $subject['name'] ?? '' }} ({{ $subject['credits'] ?? '' }} TC)
        · Lớp {{ $report['section']['code'] }} · {{ $report['section']['term'] }} · Trạng thái: {{ $report['section']['workflow'] }}
    </div>

    <table class="grid">
        <thead>
            <tr>
                <th>STT</th><th>MSSV</th><th>Họ và tên</th>
                @foreach ($report['components'] as $component)
                    <th>{{ $component['name'] }}<br>({{ str_replace('.', ',', (string) $component['weight']) }}%)</th>
                @endforeach
                <th>Hệ 10</th><th>Chữ</th><th>Hệ 4</th><th>Ghi chú</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($report['rows'] as $i => $row)
                <tr>
                    <td class="num">{{ $i + 1 }}</td>
                    <td>{{ $row['student_code'] }}</td>
                    <td>{{ $row['student_name'] }}</td>
                    @foreach ($row['scores'] as $score)
                        <td class="num">{{ $score }}</td>
                    @endforeach
                    <td class="num">{{ $row['score10'] }}</td>
                    <td class="num">{{ $row['letter'] }}</td>
                    <td class="num">{{ $row['point4'] }}</td>
                    <td>{{ $row['special_label'] ?? ($row['status'] === 'incomplete' ? 'Chưa đủ dữ liệu' : ($row['is_passed'] === false ? 'Không đạt' : '')) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="stats">
        <tr>
            <td>Sĩ số: <strong>{{ $stats['students'] }}</strong></td>
            <td>Có điểm: <strong>{{ $stats['graded'] }}</strong></td>
            <td>Điểm trung bình: <strong>{{ $stats['mean'] ?? '–' }}</strong></td>
            <td>Tỷ lệ đạt: <strong>{{ $stats['pass_rate'] === null ? '–' : $stats['pass_rate'].'%' }}</strong></td>
            <td>Phân bố: {{ collect($stats['distribution'])->map(fn ($n, $letter) => $letter.': '.$n)->implode(' · ') ?: '–' }}</td>
        </tr>
    </table>

    <table class="sign">
        <tr>
            <td><strong>Giảng viên</strong><br><em>(ký, ghi rõ họ tên)</em></td>
            <td><strong>Lãnh đạo khoa</strong><br><em>(ký, ghi rõ họ tên)</em></td>
            <td><em>In lúc {{ $printed_at }}</em><br><strong>Phòng Đào tạo</strong><br><em>(ký, ghi rõ họ tên)</em></td>
        </tr>
    </table>
</body>
</html>
