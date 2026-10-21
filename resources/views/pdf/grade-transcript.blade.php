<!doctype html><html lang="{{ $lang }}"><head><meta charset="utf-8"><style>body{font-family:DejaVu Sans,sans-serif;font-size:10px}table{border-collapse:collapse;width:100%}th,td{border:1px solid #666;padding:5px}</style></head><body>
<h2>{{ $lang === 'en' ? 'Academic Transcript' : 'Bảng điểm cá nhân' }}</h2>
<p>{{ $report['student']['student_code'] }} — {{ $report['student']['full_name'] }}</p>
<table><thead><tr><th>Học kỳ</th><th>Mã HP</th><th>Học phần</th><th>TC</th><th>Hệ 10</th><th>Chữ</th><th>Hệ 4</th></tr></thead><tbody>
@foreach ($report['rows'] as $row)
<tr><td>{{ $row['term_id'] }}</td><td>{{ $row['subject_code'] }}</td><td>{{ $lang === 'en' ? ($row['subject_name_en'] ?: $row['subject_name']) : $row['subject_name'] }}</td><td>{{ $row['credits'] }}</td><td>{{ $row['score_10'] }}</td><td>{{ $row['letter'] }}</td><td>{{ $row['score_4'] }}</td></tr>
@endforeach
</tbody></table><p>{{ $lang === 'en' ? 'Verification QR will be available in phase P2.' : 'Mã QR xác thực sẽ được bổ sung ở giai đoạn P2.' }}</p>
</body></html>
