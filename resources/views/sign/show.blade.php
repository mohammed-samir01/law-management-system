<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>{{ __('addons.esign_page_title') }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Tajawal', system-ui, sans-serif; background: #f1f5f9; color: #1e293b; padding: 16px; }
        .card { max-width: 560px; margin: 24px auto; background: #fff; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,.08); overflow: hidden; }
        .head { background: #1E3A5F; color: #fff; padding: 22px 20px; }
        .head h1 { font-size: 18px; }
        .head p { font-size: 13px; opacity: .85; margin-top: 4px; }
        .body { padding: 20px; }
        .doc-title { font-weight: 700; font-size: 16px; margin-bottom: 6px; }
        .instructions { font-size: 13px; color: #64748b; margin-bottom: 18px; }
        .pad-label { font-size: 13px; font-weight: 600; margin-bottom: 8px; }
        canvas { width: 100%; height: 200px; border: 2px dashed #cbd5e1; border-radius: 12px; touch-action: none; background: #fff; }
        .row { display: flex; gap: 10px; margin-top: 16px; }
        button { flex: 1; padding: 13px; border: none; border-radius: 12px; font-size: 14px; font-weight: 700; cursor: pointer; font-family: inherit; }
        .clear { background: #f1f5f9; color: #475569; }
        .submit { background: #16A34A; color: #fff; }
        .submit:disabled { opacity: .5; }
    </style>
</head>
<body>
    <div class="card">
        <div class="head">
            <h1>{{ __('addons.esign_page_title') }}</h1>
            <p>{{ $document->office?->name ?? config('app.name') }}</p>
        </div>
        <div class="body">
            <div class="doc-title">{{ $document->getTranslation('title', 'ar') ?: $document->getTranslation('title', 'en') }}</div>
            <p class="instructions">{{ __('addons.esign_instructions') }}</p>

            <div class="pad-label">{{ __('addons.esign_sign_here') }}</div>
            <canvas id="pad"></canvas>

            <form method="POST" action="{{ route('documents.sign.store', $token) }}" id="form">
                @csrf
                <input type="hidden" name="signature" id="signature">
                <div class="row">
                    <button type="button" class="clear" id="clear">{{ __('addons.esign_clear') }}</button>
                    <button type="submit" class="submit" id="submit" disabled>{{ __('addons.esign_submit') }}</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const canvas = document.getElementById('pad');
        const ctx = canvas.getContext('2d');
        let drawing = false, dirty = false;

        function resize() {
            const ratio = window.devicePixelRatio || 1;
            const rect = canvas.getBoundingClientRect();
            canvas.width = rect.width * ratio;
            canvas.height = rect.height * ratio;
            ctx.scale(ratio, ratio);
            ctx.lineWidth = 2.5; ctx.lineCap = 'round'; ctx.strokeStyle = '#1e293b';
        }
        resize();

        function pos(e) {
            const rect = canvas.getBoundingClientRect();
            const t = e.touches ? e.touches[0] : e;
            return { x: t.clientX - rect.left, y: t.clientY - rect.top };
        }
        function start(e) { drawing = true; const p = pos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); e.preventDefault(); }
        function move(e) { if (!drawing) return; const p = pos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); dirty = true; document.getElementById('submit').disabled = false; e.preventDefault(); }
        function end() { drawing = false; }

        canvas.addEventListener('mousedown', start);
        canvas.addEventListener('mousemove', move);
        canvas.addEventListener('mouseup', end);
        canvas.addEventListener('touchstart', start);
        canvas.addEventListener('touchmove', move);
        canvas.addEventListener('touchend', end);

        document.getElementById('clear').addEventListener('click', function () {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            dirty = false; document.getElementById('submit').disabled = true;
        });

        document.getElementById('form').addEventListener('submit', function (e) {
            if (!dirty) { e.preventDefault(); return; }
            document.getElementById('signature').value = canvas.toDataURL('image/png');
        });
    </script>
</body>
</html>
