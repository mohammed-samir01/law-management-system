<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class PwaController extends Controller
{
    private function guard(): void
    {
        $office = auth()->user()?->office;
        abort_unless($office && $office->hasAddon('client-pwa'), 404);
    }

    public function manifest(): Response
    {
        $this->guard();

        $name = auth()->user()->office?->name ?? config('app.name');

        $manifest = [
            'name'             => $name,
            'short_name'       => mb_substr($name, 0, 12),
            'start_url'        => route('portal.dashboard'),
            'scope'            => '/portal/',
            'display'          => 'standalone',
            'background_color' => '#f0f4f8',
            'theme_color'      => '#1E3A5F',
            'lang'             => 'ar',
            'dir'              => 'rtl',
            'icons'            => [
                ['src' => route('portal.pwa.icon', ['size' => 192]), 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any maskable'],
                ['src' => route('portal.pwa.icon', ['size' => 512]), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any maskable'],
            ],
        ];

        return response($manifest, 200)
            ->header('Content-Type', 'application/manifest+json');
    }

    public function serviceWorker(): Response
    {
        $this->guard();

        $offline = route('portal.dashboard');

        $js = <<<JS
        const CACHE = 'mizan-portal-v1';
        const OFFLINE_URL = '{$offline}';

        self.addEventListener('install', (e) => {
            e.waitUntil(caches.open(CACHE).then((c) => c.add(OFFLINE_URL)));
            self.skipWaiting();
        });

        self.addEventListener('activate', (e) => {
            e.waitUntil(caches.keys().then((keys) =>
                Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k)))
            ));
            self.clients.claim();
        });

        self.addEventListener('fetch', (e) => {
            if (e.request.method !== 'GET') return;
            e.respondWith(
                fetch(e.request)
                    .then((res) => {
                        const copy = res.clone();
                        caches.open(CACHE).then((c) => c.put(e.request, copy));
                        return res;
                    })
                    .catch(() => caches.match(e.request).then((r) => r || caches.match(OFFLINE_URL)))
            );
        });
        JS;

        return response($js, 200)
            ->header('Content-Type', 'application/javascript')
            ->header('Service-Worker-Allowed', '/portal/');
    }

    // ── Lawyer app (lawyer-pwa addon), scoped to /mobile/lawyer ──────────────

    private function guardLawyer(): void
    {
        $office = auth()->user()?->office;
        abort_unless($office && $office->hasAddon('lawyer-pwa'), 404);
    }

    public function lawyerManifest(): Response
    {
        $this->guardLawyer();

        $name = auth()->user()->office?->name ?? config('app.name');

        return response([
            'name'             => $name,
            'short_name'       => mb_substr($name, 0, 12),
            'start_url'        => route('mobile.lawyer.dashboard'),
            'scope'            => '/mobile/',
            'display'          => 'standalone',
            'background_color' => '#1E3A5F',
            'theme_color'      => '#1E3A5F',
            'lang'             => 'ar',
            'dir'              => 'rtl',
            'icons'            => [
                ['src' => route('mobile.lawyer.pwa.icon', ['size' => 192]), 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any maskable'],
                ['src' => route('mobile.lawyer.pwa.icon', ['size' => 512]), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any maskable'],
            ],
        ], 200)->header('Content-Type', 'application/manifest+json');
    }

    public function lawyerServiceWorker(): Response
    {
        $this->guardLawyer();

        $offline = route('mobile.lawyer.dashboard');
        $js = <<<JS
        const CACHE = 'mizan-lawyer-v1';
        const OFFLINE_URL = '{$offline}';
        self.addEventListener('install', (e) => { e.waitUntil(caches.open(CACHE).then((c) => c.add(OFFLINE_URL))); self.skipWaiting(); });
        self.addEventListener('activate', (e) => { e.waitUntil(caches.keys().then((ks) => Promise.all(ks.filter((k) => k !== CACHE).map((k) => caches.delete(k))))); self.clients.claim(); });
        self.addEventListener('fetch', (e) => {
            if (e.request.method !== 'GET') return;
            e.respondWith(fetch(e.request).then((res) => { const c = res.clone(); caches.open(CACHE).then((x) => x.put(e.request, c)); return res; })
                .catch(() => caches.match(e.request).then((r) => r || caches.match(OFFLINE_URL))));
        });
        JS;

        return response($js, 200)
            ->header('Content-Type', 'application/javascript')
            ->header('Service-Worker-Allowed', '/mobile/');
    }

    public function lawyerIcon(int $size): Response
    {
        $this->guardLawyer();
        return $this->pngIcon($size);
    }

    public function icon(int $size): Response
    {
        $this->guard();
        return $this->pngIcon($size);
    }

    private function pngIcon(int $size): Response
    {
        $size = in_array($size, [192, 512], true) ? $size : 192;
        $name = auth()->user()->office?->name ?? config('app.name');
        $letter = mb_strtoupper(mb_substr(trim($name), 0, 1)) ?: 'M';

        $img = imagecreatetruecolor($size, $size);
        $bg  = imagecolorallocate($img, 0x1E, 0x3A, 0x5F);
        $fg  = imagecolorallocate($img, 0xFF, 0xFF, 0xFF);
        imagefilledrectangle($img, 0, 0, $size, $size, $bg);

        // Centered letter using the largest built-in font, scaled up.
        $font  = 5;
        $scale = (int) ($size / 12);
        $tmpW  = imagefontwidth($font);
        $tmpH  = imagefontheight($font);
        $tmp   = imagecreatetruecolor($tmpW, $tmpH);
        $tbg   = imagecolorallocate($tmp, 0x1E, 0x3A, 0x5F);
        $tfg   = imagecolorallocate($tmp, 0xFF, 0xFF, 0xFF);
        imagefilledrectangle($tmp, 0, 0, $tmpW, $tmpH, $tbg);
        imagestring($tmp, $font, 0, 0, $letter, $tfg);

        $dstW = $tmpW * $scale;
        $dstH = $tmpH * $scale;
        imagecopyresized($img, $tmp, (int) (($size - $dstW) / 2), (int) (($size - $dstH) / 2), 0, 0, $dstW, $dstH, $tmpW, $tmpH);
        imagedestroy($tmp);

        ob_start();
        imagepng($img);
        $png = ob_get_clean();
        imagedestroy($img);

        return response($png, 200)->header('Content-Type', 'image/png');
    }
}
