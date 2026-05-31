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
    .header { border-bottom: 3px solid #1E3A5F; padding-bottom: 18px; margin-bottom: 26px; }
    .header-table { width: 100%; border-collapse: collapse; }
    .header-table td { vertical-align: middle; padding: 0; }
    .office-name   { font-size: 20px; font-weight: bold; color: #1E3A5F; }
    .office-phone  { font-size: 12px; color: #64748b; margin-top: 4px; }
    .invoice-title { font-size: 18px; font-weight: bold; color: #1E3A5F; }
    .invoice-num   { font-size: 13px; color: #64748b; margin-top: 4px; }
    .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 26px; }
    .meta-table td { padding: 0 8px; vertical-align: top; width: 50%; font-size: 13px; }
    .meta-label { font-size: 10px; color: #64748b; text-transform: uppercase; margin-bottom: 6px; font-weight: bold; display: block; }
    .meta-value { font-size: 13px; margin-bottom: 3px; }
    table.lines { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    table.lines th {
        background: #1E3A5F; color: #fff;
        padding: 10px; text-align: right;
        font-size: 12px; direction: rtl;
    }
    table.lines td {
        padding: 10px; border-bottom: 1px solid #e2e8f0;
        font-size: 13px; text-align: right; direction: rtl;
    }
    .totals-wrap { overflow: hidden; margin-top: 10px; }
    table.totals { width: 280px; border-collapse: collapse; float: left; }
    table.totals td { padding: 5px 10px; font-size: 13px; text-align: right; direction: rtl; }
    table.totals .grand td {
        font-size: 15px; font-weight: bold; color: #1E3A5F;
        border-top: 2px solid #1E3A5F; padding-top: 8px;
    }
    .badge { padding: 3px 10px; border-radius: 3px; font-size: 11px; font-weight: bold; }
    .badge-paid    { background: #dcfce7; color: #15803d; }
    .badge-sent    { background: #dbeafe; color: #1d4ed8; }
    .badge-draft   { background: #f1f5f9; color: #64748b; }
    .badge-overdue { background: #fee2e2; color: #b91c1c; }
    .notes-box { margin-top: 26px; padding: 14px; background: #f8fafc; border-right: 3px solid #1E3A5F; }
    .notes-label { font-size: 11px; color: #64748b; margin-bottom: 5px; display: block; }
    .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 12px; }
</style>
</head>
<body>
<div class="page">

    <div class="header">
        <table class="header-table">
            <tr>
                <td style="text-align:right; direction:rtl;">
                    <div class="office-name">{{ $invoice->office?->getTranslation('name','ar') ?? config('app.name') }}</div>
                    @if($invoice->office?->phone)
                        <div class="office-phone">{{ $invoice->office->phone }}</div>
                    @endif
                </td>
                <td style="text-align:left; direction:rtl; width:35%;">
                    <div class="invoice-title">فاتورة</div>
                    <div class="invoice-num">{{ $invoice->invoice_number }}</div>
                    <div style="margin-top:6px;">
                        <span class="badge badge-{{ $invoice->status }}">{{ $invoice->status_label }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <table class="meta-table">
        <tr>
            <td>
                <span class="meta-label">بيانات العميل</span>
                <div class="meta-value"><strong>{{ $invoice->client?->getTranslation('name','ar') ?? '—' }}</strong></div>
                @if($invoice->client?->email)<div class="meta-value">{{ $invoice->client->email }}</div>@endif
                @if($invoice->client?->phone)<div class="meta-value">{{ $invoice->client->phone }}</div>@endif
            </td>
            <td style="text-align:left; direction:ltr;">
                <span class="meta-label">بيانات الفاتورة</span>
                <div class="meta-value">تاريخ الإصدار: {{ $invoice->created_at?->format('Y/m/d') }}</div>
                @if($invoice->due_date)
                    <div class="meta-value">تاريخ الاستحقاق: {{ $invoice->due_date->format('Y/m/d') }}</div>
                @endif
                @if($invoice->legalCase)
                    <div class="meta-value">القضية: {{ $invoice->legalCase->case_number }}</div>
                @endif
            </td>
        </tr>
    </table>

    <table class="lines">
        <thead>
            <tr>
                <th>الوصف</th>
                <th style="width:160px;">المبلغ</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>أتعاب قانونية — {{ $invoice->legalCase?->case_number ?? 'خدمات قانونية' }}</td>
                <td>{{ number_format($invoice->amount, 2) }} {{ $invoice->currency }}</td>
            </tr>
        </tbody>
    </table>

    <div class="totals-wrap">
        <table class="totals">
            <tr>
                <td>المبلغ الأساسي</td>
                <td>{{ number_format($invoice->amount, 2) }} {{ $invoice->currency }}</td>
            </tr>
            @if($invoice->tax_amount > 0)
            <tr>
                <td>الضريبة (15%)</td>
                <td>{{ number_format($invoice->tax_amount, 2) }} {{ $invoice->currency }}</td>
            </tr>
            @endif
            <tr class="grand">
                <td>الإجمالي</td>
                <td>{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}</td>
            </tr>
        </table>
    </div>

    @if($invoice->getTranslation('notes','ar'))
    <div class="notes-box" style="margin-top: 60px;">
        <span class="notes-label">ملاحظات</span>
        <p>{{ $invoice->getTranslation('notes','ar') }}</p>
    </div>
    @endif

    <div class="footer">
        {{ config('app.name') }} — {{ $invoice->office?->getTranslation('name','ar') ?? '' }}
    </div>

</div>
</body>
</html>
