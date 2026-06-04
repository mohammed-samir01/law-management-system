<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<style>
    @php $amiriPath = str_replace('\\', '/', public_path('fonts/Amiri-Regular.ttf')); $amiriBoldPath = str_replace('\\', '/', public_path('fonts/Amiri-Bold.ttf')); @endphp
    @if(file_exists(public_path('fonts/Amiri-Regular.ttf')))
    @font-face { font-family: 'Amiri'; font-style: normal; font-weight: normal; src: url('{{ $amiriPath }}') format('truetype'); }
    @font-face { font-family: 'Amiri'; font-style: normal; font-weight: bold; src: url('{{ $amiriBoldPath }}') format('truetype'); }
    @endif
    * { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { direction: rtl; text-align: right; }
    body { font-family: 'tajawal', sans-serif; font-size: 14px; color: #1e293b; }
    .page { padding: 50px 45px; }
    .header { border-bottom: 3px solid #1E3A5F; padding-bottom: 16px; margin-bottom: 24px; }
    .office-name { font-size: 20px; font-weight: bold; color: #1E3A5F; }
    .report-title { font-size: 15px; color: #64748b; margin-top: 4px; }
    table { width: 100%; border-collapse: collapse; margin-top: 14px; }
    td { padding: 11px 14px; border: 1px solid #e2e8f0; }
    .label { color: #475569; }
    .value { text-align: left; font-weight: bold; direction: ltr; }
    .footer { margin-top: 30px; font-size: 11px; color: #94a3b8; text-align: center; }
</style>
</head>
<body>
<div class="page">
    <div class="header">
        <div class="office-name">{{ $officeName }}</div>
        <div class="report-title">{{ $report['title'] ?? 'تقرير' }}</div>
    </div>

    <table>
        @forelse($report['rows'] ?? [] as $row)
        <tr>
            <td class="label">{{ $row['label'] }}</td>
            <td class="value">{{ $row['value'] }}</td>
        </tr>
        @empty
        <tr><td colspan="2" style="text-align:center; color:#94a3b8;">لا توجد بيانات</td></tr>
        @endforelse
    </table>

    <div class="footer">تقرير مُولّد آلياً — {{ now()->format('Y/m/d H:i') }} — {{ config('app.name') }}</div>
</div>
</body>
</html>
