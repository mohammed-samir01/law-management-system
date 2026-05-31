<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<style>
@php $amiriPath = str_replace('\\', '/', public_path('fonts/Amiri-Regular.ttf')); $amiriBoldPath = str_replace('\\', '/', public_path('fonts/Amiri-Bold.ttf')); @endphp
@if(file_exists(public_path('fonts/Amiri-Regular.ttf')))
    @font-face {
        font-family: 'Amiri';
        font-style: normal;
        font-weight: normal;
        src: url('{{ $amiriPath }}') format('truetype');
    }
    @font-face {
        font-family: 'Amiri';
        font-style: normal;
        font-weight: bold;
        src: url('{{ $amiriBoldPath }}') format('truetype');
    }
@endif

    * { box-sizing: border-box; margin: 0; padding: 0; }
    html, body {
        direction: rtl;
        text-align: right;
    }
    body {
        font-family: {{ file_exists(public_path('fonts/Amiri-Regular.ttf')) ? "'Amiri'" : "'DejaVu Sans'" }}, sans-serif;
        font-size: 14px;
        color: #1e293b;
        background: #fff;
    }
    .page { padding: 40px; }
    .header { border-bottom: 3px solid #1E3A5F; padding-bottom: 16px; margin-bottom: 22px; }
    .header-table { width: 100%; border-collapse: collapse; }
    .header-table td { vertical-align: middle; padding: 0; }
    .office-name  { font-size: 20px; font-weight: bold; color: #1E3A5F; }
    .report-title { font-size: 15px; font-weight: bold; color: #1E3A5F; }
    .report-sub   { font-size: 12px; color: #64748b; margin-top: 3px; }
    h2 {
        font-size: 14px; color: #1E3A5F;
        margin: 20px 0 8px;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 5px;
        direction: rtl; text-align: right;
    }
    .info-table { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
    .info-table td { padding: 6px 10px; font-size: 13px; width: 25%; vertical-align: top; }
    .info-label { font-size: 10px; color: #64748b; display: block; margin-bottom: 3px; }
    .info-value { font-size: 13px; font-weight: bold; }
    table.data { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
    table.data th {
        background: #1E3A5F; color: #fff;
        padding: 8px 10px; text-align: right;
        font-size: 12px; direction: rtl;
    }
    table.data td {
        padding: 7px 10px; border-bottom: 1px solid #e2e8f0;
        font-size: 13px; text-align: right; direction: rtl;
    }
    table.data tr:nth-child(even) td { background: #f8fafc; }
    .badge { padding: 2px 8px; border-radius: 3px; font-size: 11px; font-weight: bold; }
    .badge-active    { background: #dcfce7; color: #15803d; }
    .badge-new       { background: #dbeafe; color: #1d4ed8; }
    .badge-pending   { background: #fef9c3; color: #a16207; }
    .badge-closed    { background: #fee2e2; color: #b91c1c; }
    .badge-adjourned { background: #e0e7ff; color: #4338ca; }
    .badge-archived  { background: #f1f5f9; color: #64748b; }
    .footer {
        margin-top: 40px; text-align: center;
        font-size: 10px; color: #94a3b8;
        border-top: 1px solid #e2e8f0; padding-top: 12px;
    }
</style>
</head>
<body>
<div class="page">

    <div class="header">
        <table class="header-table">
            <tr>
                <td style="text-align:right; direction:rtl;">
                    <div class="office-name">{{ $case->office?->getTranslation('name','ar') ?? config('app.name') }}</div>
                </td>
                <td style="text-align:left; direction:rtl; width:35%;">
                    <div class="report-title">تقرير القضية</div>
                    <div class="report-sub">{{ $case->case_number }}</div>
                </td>
            </tr>
        </table>
    </div>

    <h2>معلومات القضية</h2>
    <table class="info-table">
        <tr>
            <td>
                <span class="info-label">رقم القضية</span>
                <span class="info-value">{{ $case->case_number }}</span>
            </td>
            <td>
                <span class="info-label">العنوان</span>
                <span class="info-value">{{ $case->getTranslation('title','ar') ?: $case->getTranslation('title','en') }}</span>
            </td>
            <td>
                <span class="info-label">الحالة</span>
                <span class="badge badge-{{ $case->status }}">{{ $case->status_label }}</span>
            </td>
            <td>
                <span class="info-label">النوع</span>
                <span class="info-value">{{ $case->type }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="info-label">المحكمة</span>
                <span class="info-value">{{ $case->court ?? '—' }}</span>
            </td>
            <td>
                <span class="info-label">القاضي</span>
                <span class="info-value">{{ $case->judge ?? '—' }}</span>
            </td>
            <td>
                <span class="info-label">العميل</span>
                <span class="info-value">{{ $case->client?->getTranslation('name','ar') ?? '—' }}</span>
            </td>
            <td>
                <span class="info-label">تاريخ الفتح</span>
                <span class="info-value">{{ $case->created_at?->format('Y/m/d') }}</span>
            </td>
        </tr>
    </table>

    @if($case->hearings->isNotEmpty())
    <h2>الجلسات ({{ $case->hearings->count() }})</h2>
    <table class="data">
        <thead>
            <tr><th>الموعد</th><th>المكان</th><th>القاضي</th><th>الحالة</th></tr>
        </thead>
        <tbody>
            @foreach($case->hearings as $hearing)
            <tr>
                <td>{{ $hearing->scheduled_at?->format('Y/m/d H:i') }}</td>
                <td>{{ $hearing->location ?? '—' }}</td>
                <td>{{ $hearing->judge ?? '—' }}</td>
                <td>{{ $hearing->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($case->documents->isNotEmpty())
    <h2>الوثائق ({{ $case->documents->count() }})</h2>
    <table class="data">
        <thead>
            <tr><th>العنوان</th><th>النوع</th><th>تاريخ الرفع</th></tr>
        </thead>
        <tbody>
            @foreach($case->documents as $doc)
            <tr>
                <td>{{ $doc->getTranslation('title','ar') ?: $doc->getTranslation('title','en') }}</td>
                <td>{{ $doc->type }}</td>
                <td>{{ $doc->created_at?->format('Y/m/d') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($case->lawyers->isNotEmpty())
    <h2>فريق القضية</h2>
    <table class="data">
        <thead>
            <tr><th>الاسم</th><th>الدور</th></tr>
        </thead>
        <tbody>
            @foreach($case->lawyers as $lawyer)
            <tr>
                <td>{{ $lawyer->name }}</td>
                <td>{{ $lawyer->pivot->role ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        تقرير صادر بتاريخ {{ now()->format('Y/m/d H:i') }} — {{ config('app.name') }}
    </div>

</div>
</body>
</html>
