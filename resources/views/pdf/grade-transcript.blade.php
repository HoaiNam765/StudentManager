@php($en = $lang === 'en')
<!doctype html>
<html lang="{{ $lang }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $en ? 'Academic Transcript' : 'Bảng điểm' }} {{ $report['student']['student_code'] }}</title>
    <style>
        @page { margin: 28px 32px; }
        body { font-family: "{{ $font }}", sans-serif; font-size: 10.5px; color: #111; }
        .header { width: 100%; }
        .header td { vertical-align: top; text-align: center; }
        .school { font-weight: bold; text-transform: uppercase; }
        h1 { text-align: center; font-size: 16px; margin: 18px 0 10px; }
        .info td { padding: 2px 8px 2px 0; }
        h2 { font-size: 12px; margin: 14px 0 4px; }
        table.grid { border-collapse: collapse; width: 100%; }
        table.grid th, table.grid td { border: 1px solid #555; padding: 3px 4px; }
        table.grid th { background: #e8e8e8; }
        .num { text-align: center; }
        .muted { color: #666; }
        .sum td { padding-top: 3px; }
        .sign { width: 100%; margin-top: 28px; }
        .sign td { text-align: center; width: 50%; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td style="width: 50%"><div class="school">{{ $school }}</div><div>{{ $en ? 'ACADEMIC AFFAIRS OFFICE' : 'PHÒNG ĐÀO TẠO' }}</div></td>
            <td>
                @if ($en)
                    <div><strong>SOCIALIST REPUBLIC OF VIETNAM</strong></div><div>Independence – Freedom – Happiness</div>
                @else
                    <div><strong>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</strong></div><div>Độc lập – Tự do – Hạnh phúc</div>
                @endif
            </td>
        </tr>
    </table>

    <h1>{{ $en ? 'ACADEMIC TRANSCRIPT' : 'BẢNG ĐIỂM HỌC TẬP' }}</h1>
    <table class="info">
        <tr>
            <td>{{ $en ? 'Full name:' : 'Họ và tên:' }}</td><td><strong>{{ $report['student']['full_name'] }}</strong></td>
            <td>{{ $en ? 'Student ID:' : 'MSSV:' }}</td><td><strong>{{ $report['student']['student_code'] }}</strong></td>
            <td>{{ $en ? 'Cohort:' : 'Khóa:' }}</td><td>{{ $report['student']['cohort'] }}</td>
        </tr>
    </table>

    @forelse ($report['terms'] as $term)
        <h2>{{ $term['name'] }}</h2>
        <table class="grid">
            <thead>
                <tr>
                    <th>{{ $en ? 'No.' : 'STT' }}</th><th>{{ $en ? 'Code' : 'Mã HP' }}</th><th>{{ $en ? 'Course' : 'Tên học phần' }}</th>
                    <th>{{ $en ? 'Credits' : 'TC' }}</th><th>{{ $en ? '10-point' : 'Hệ 10' }}</th><th>{{ $en ? 'Letter' : 'Chữ' }}</th><th>{{ $en ? '4-point' : 'Hệ 4' }}</th>
                    <th>{{ $en ? 'Result' : 'Kết quả' }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($term['rows'] as $i => $row)
                    <tr>
                        <td class="num">{{ $i + 1 }}</td>
                        <td>{{ $row['subject_code'] }}</td>
                        <td>{{ $en ? ($row['subject_name_en'] ?: $row['subject_name']) : $row['subject_name'] }}{{ $row['counts_toward_gpa'] ? '' : ($en ? ' (not in GPA)' : ' (không tính GPA)') }}</td>
                        <td class="num">{{ $row['credits'] }}</td>
                        <td class="num">{{ $row['score10'] }}</td>
                        <td class="num">{{ $row['letter'] }}</td>
                        <td class="num">{{ $row['point4'] }}</td>
                        <td class="num">
                            @if ($row['special_status']) {{ $row['special_label'] }}
                            @elseif ($row['is_passed'] === null) –
                            @else {{ $row['is_passed'] ? ($en ? 'Passed' : 'Đạt') : ($en ? 'Failed' : 'Không đạt') }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($term['gpa'])
            <table class="sum">
                <tr>
                    <td>{{ $en ? 'Term GPA:' : 'GPA học kỳ:' }} <strong>{{ $term['gpa']['gpa'] ?? '–' }}</strong></td>
                    <td>&nbsp; {{ $en ? 'Credits earned:' : 'Tín chỉ đạt:' }} {{ $term['gpa']['credits_earned'] }}</td>
                    <td>&nbsp; {{ $en ? 'Standing:' : 'Xếp loại:' }} {{ $term['gpa']['classification'] ?? '–' }}</td>
                </tr>
            </table>
        @endif
    @empty
        <p class="muted">{{ $en ? 'No published grades yet.' : 'Chưa có điểm đã công bố.' }}</p>
    @endforelse

    @if ($report['cumulative'] && ! $report['term_id'])
        <h2>{{ $en ? 'Cumulative' : 'Tích lũy' }}</h2>
        <table class="sum">
            <tr>
                <td>{{ $en ? 'Cumulative GPA:' : 'GPA tích lũy:' }} <strong>{{ $report['cumulative']['gpa'] ?? '–' }}</strong></td>
                <td>&nbsp; {{ $en ? 'Credits earned:' : 'Tín chỉ tích lũy:' }} {{ $report['cumulative']['credits_earned'] }}</td>
                <td>&nbsp; {{ $en ? 'Standing:' : 'Xếp loại học lực:' }} {{ $report['cumulative']['classification'] ?? '–' }}</td>
            </tr>
        </table>
    @endif

    <table class="sign">
        <tr>
            <td class="muted">{{ $en ? 'Verification QR code: available in phase P2.' : 'Mã QR xác thực: bổ sung ở giai đoạn P2.' }}</td>
            <td><em>{{ $en ? 'Printed at' : 'In lúc' }} {{ $printed_at }}</em><br><strong>{{ $en ? 'ACADEMIC AFFAIRS OFFICE' : 'PHÒNG ĐÀO TẠO' }}</strong></td>
        </tr>
    </table>
</body>
</html>
