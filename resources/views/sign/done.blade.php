<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('addons.esign_page_title') }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Tajawal', system-ui, sans-serif; background: #f1f5f9; color: #1e293b; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 16px; }
        .card { max-width: 420px; width: 100%; background: #fff; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,.08); padding: 36px 24px; text-align: center; }
        .check { width: 72px; height: 72px; margin: 0 auto 20px; border-radius: 50%; background: #f0fdf4; display: flex; align-items: center; justify-content: center; }
        .check svg { width: 40px; height: 40px; color: #16A34A; }
        h1 { font-size: 18px; margin-bottom: 8px; }
        p { font-size: 14px; color: #64748b; }
    </style>
</head>
<body>
    <div class="card">
        <div class="check">
            <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        </div>
        <h1>{{ __('addons.esign_success') }}</h1>
        <p>{{ $document->getTranslation('title', 'ar') ?: $document->getTranslation('title', 'en') }}</p>
    </div>
</body>
</html>
