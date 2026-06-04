<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { direction: rtl; text-align: right; }
    body {
        font-family: 'tajawal', sans-serif;
        font-size: 14px;
        color: #1e293b;
        background: #fff;
    }
    .page { padding: 50px 45px; }
    .header {
        border-bottom: 3px solid #1E3A5F;
        padding-bottom: 16px;
        margin-bottom: 28px;
    }
    .header-table { width: 100%; border-collapse: collapse; }
    .header-table td { vertical-align: middle; padding: 0; }
    .office-name { font-size: 20px; font-weight: bold; color: #1E3A5F; }
    .doc-type    { font-size: 13px; color: #64748b; margin-top: 4px; }
    .doc-title   { font-size: 18px; font-weight: bold; color: #1E3A5F; text-align: center; margin: 20px 0 28px; }
    .meta-table  { width: 100%; border-collapse: collapse; margin-bottom: 28px; background: #f8fafc; border-radius: 6px; }
    .meta-table td { padding: 7px 12px; font-size: 12px; width: 25%; vertical-align: top; }
    .meta-label  { font-size: 10px; color: #94a3b8; display: block; margin-bottom: 2px; }
    .meta-value  { font-weight: bold; font-size: 12px; }
    .content-box {
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 24px 28px;
        white-space: pre-wrap;
        line-height: 2;
        font-size: 14px;
        min-height: 300px;
    }
    .footer {
        margin-top: 40px;
        text-align: center;
        font-size: 10px;
        color: #94a3b8;
        border-top: 1px solid #e2e8f0;
        padding-top: 12px;
    }
</style>
</head>
<body>
<div class="page">

    <div class="header">
        <table class="header-table">
            <tr>
                <td style="text-align:right; direction:rtl;">
                    <div class="office-name">{{ $document->office?->getTranslation('name','ar') ?? config('app.name') }}</div>
                    <div class="doc-type">{{ match($document->type ?? '') {
                        'contract'          => 'عقد',
                        'pleading'          => 'مذكرة',
                        'verdict'           => 'حكم',
                        'power_of_attorney' => 'توكيل',
                        'evidence'          => 'دليل',
                        default             => 'وثيقة',
                    } }}</div>
                </td>
                <td style="text-align:left; direction:rtl; width:30%;">
                    <div style="font-size:11px; color:#64748b;">الإصدار: {{ $document->version ?? 1 }}</div>
                    <div style="font-size:11px; color:#64748b;">التاريخ: {{ $document->created_at?->format('Y/m/d') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="doc-title">
        {{ $document->getTranslation('title', 'ar') ?: $document->getTranslation('title', 'en') }}
    </div>

    <table class="meta-table">
        <tr>
            <td>
                <span class="meta-label">الحالة</span>
                <span class="meta-value">{{ match($document->status ?? '') {
                    'draft'    => 'مسودة',
                    'final'    => 'نهائي',
                    'approved' => 'معتمد',
                    'archived' => 'مؤرشف',
                    default    => $document->status ?? '—',
                } }}</span>
            </td>
            <td>
                <span class="meta-label">الفئة</span>
                <span class="meta-value">{{ match($document->category ?? '') {
                    'legal'     => 'قانوني',
                    'financial' => 'مالي',
                    'personal'  => 'شخصي',
                    'court'     => 'محكمة',
                    'contract'  => 'عقود',
                    default     => '—',
                } }}</span>
            </td>
            <td>
                <span class="meta-label">القضية</span>
                <span class="meta-value">{{ $document->documentable?->case_number ?? '—' }}</span>
            </td>
            <td>
                <span class="meta-label">أُنشئ بواسطة</span>
                <span class="meta-value">{{ $document->uploadedBy?->name ?? '—' }}</span>
            </td>
        </tr>
    </table>

    @if($document->getTranslation('content', 'ar'))
        <div class="content-box">{{ $document->getTranslation('content', 'ar') }}</div>
    @else
        <div class="content-box" style="color:#94a3b8; text-align:center; padding-top:60px;">
            لا يوجد محتوى نصي — الوثيقة مرفقة كملف
        </div>
    @endif

    <div class="footer">
        صدر بتاريخ {{ now()->format('Y/m/d H:i') }} — {{ config('app.name') }}
    </div>

</div>
</body>
</html>
