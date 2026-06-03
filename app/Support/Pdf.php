<?php

namespace App\Support;

use Illuminate\Support\Facades\View;
use Mpdf\Mpdf;

/**
 * Central PDF renderer using mPDF — which natively shapes Arabic letters and
 * applies RTL bidi (unlike DomPDF). Renders a Blade view to PDF bytes.
 *
 * Uses mPDF's bundled "XB Riyaz" Arabic font (parses cleanly and shapes
 * correctly). The Blade views' Amiri @font-face blocks are stripped before
 * rendering — Amiri's OpenType tables aren't supported by mPDF's font parser,
 * and unmatched font-family names fall back to the default font below.
 */
class Pdf
{
    public static function make(string $view, array $data = []): string
    {
        $html = View::make($view, $data)->render();

        // Drop @font-face blocks (Amiri) — mPDF can't parse them; we use xbriyaz.
        $html = preg_replace('/@font-face\s*\{[^}]*\}/i', '', $html);

        $tmp = storage_path('app/mpdf-tmp');
        if (! is_dir($tmp)) {
            @mkdir($tmp, 0775, true);
        }

        $mpdf = new Mpdf([
            'mode'             => 'utf-8',
            'format'           => 'A4',
            'directionality'   => 'rtl',
            'default_font'     => 'xbriyaz',
            'default_font_size' => 11,
            'tempDir'          => $tmp,
            'autoScriptToLang' => true,
            'autoLangToFont'   => true,
            'margin_top'       => 12,
            'margin_bottom'    => 12,
            'margin_left'      => 12,
            'margin_right'     => 12,
        ]);

        $mpdf->WriteHTML($html);

        return $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);
    }
}
