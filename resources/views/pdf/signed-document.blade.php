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
    body {
        font-family: 'tajawal', sans-serif;
        font-size: 14px; color: #1e293b; background: #fff;
    }
    .page { padding: 50px 45px; }
    .header { border-bottom: 3px solid #1E3A5F; padding-bottom: 16px; margin-bottom: 28px; }
    .office-name { font-size: 20px; font-weight: bold; color: #1E3A5F; }
    .doc-type { font-size: 13px; color: #64748b; margin-top: 4px; }
    .doc-title { font-size: 18px; font-weight: bold; color: #1E3A5F; text-align: center; margin: 20px 0 28px; }
    .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 28px; background: #f8fafc; }
    .meta-table td { padding: 10px 14px; border: 1px solid #e2e8f0; }
    .meta-table .label { font-weight: bold; color: #475569; width: 35%; }
    .sig-box { margin-top: 30px; border: 1px solid #e2e8f0; padding: 18px; text-align: center; }
    .sig-box .caption { font-size: 12px; color: #64748b; margin-bottom: 10px; }
    .sig-img { max-height: 140px; max-width: 360px; }
    .verified { margin-top: 24px; padding: 12px; background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; font-size: 12px; text-align: center; }
    .footer { margin-top: 36px; font-size: 11px; color: #94a3b8; text-align: center; }
</style>
</head>
<body>
<div class="page">
    <div class="header">
        <div class="office-name">{{ $document->office?->name ?? config('app.name') }}</div>
        <div class="doc-type">شهادة توقيع إلكتروني — Electronic Signature Certificate</div>
    </div>

    <div class="doc-title">{{ $document->getTranslation('title', 'ar') ?: $document->getTranslation('title', 'en') ?: 'مستند' }}</div>

    <table class="meta-table">
        <tr>
            <td class="label">الموقّع</td>
            <td>{{ $document->signingClient?->getTranslation('name', 'ar') ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">تاريخ التوقيع</td>
            <td>{{ $document->signed_at?->format('Y/m/d H:i') ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">عنوان IP</td>
            <td>{{ $document->signer_ip ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">رقم المستند</td>
            <td>#{{ $document->id }}</td>
        </tr>
    </table>

    <div class="sig-box">
        <div class="caption">التوقيع</div>
        @if($document->signature_data)
        <img class="sig-img" src="{{ $document->signature_data }}" alt="signature">
        @endif
    </div>

    <div class="verified">
        ✓ تم توقيع هذا المستند إلكترونياً عبر منصة {{ config('app.name') }} وتم التحقق من بياناته.
    </div>

    <div class="footer">
        وثيقة مُولّدة آلياً — {{ now()->format('Y/m/d H:i') }}
    </div>
</div>
</body>
</html>
