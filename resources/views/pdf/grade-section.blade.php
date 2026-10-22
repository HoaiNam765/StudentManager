<!doctype html><html lang="vi"><head><meta charset="utf-8"><style>body{font-family:DejaVu Sans,sans-serif;font-size:10px}table{border-collapse:collapse;width:100%}th,td{border:1px solid #666;padding:5px}</style></head><body>
<h2>Sổ điểm lớp học phần: {{ $report['section']['code'] }}</h2>
<table><thead><tr><th>MSSV</th><th>Họ và tên</th><th>Điểm 10</th><th>Chữ</th><th>Điểm 4</th><th>Đạt</th></tr></thead><tbody>
@foreach ($report['rows'] as $row)
<tr><td>{{ $row['student_code'] }}</td><td>{{ $row['student_name'] }}</td><td>{{ $row['score_10'] }}</td><td>{{ $row['letter'] }}</td><td>{{ $row['score_4'] }}</td><td>{{ $row['passed'] === null ? '' : ($row['passed'] ? 'Đạt' : 'Không đạt') }}</td></tr>
@endforeach
</tbody></table>
<p>Số sinh viên có điểm: {{ $report['statistics']['graded'] }} | Trung bình: {{ $report['statistics']['mean'] ?? '-' }} | Tỷ lệ đạt: {{ $report['statistics']['pass_rate'] ?? '-' }}%</p>
<p>Phổ điểm: @foreach($report['statistics']['distribution'] as $letter => $count) {{ $letter }}: {{ $count }}; @endforeach</p>
<p style="text-align:right;margin-top:50px">Ngày ..... tháng ..... năm ........<br><strong>Giảng viên / Đơn vị xác nhận</strong><br><em>(Ký, ghi rõ họ tên)</em></p>
</body></html>
