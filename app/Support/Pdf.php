<?php

namespace App\Support;

use Illuminate\Support\Facades\View;
use Mpdf\Mpdf;

/**
 * Central PDF renderer using mPDF — which natively shapes Arabic letters and
 * applies RTL bidi (unlike DomPDF). Renders a Blade view to PDF bytes.
 *
 * Uses the modern "Tajawal" Arabic font (same family as the web UI) for a
 * clean, professional look. The TTFs live in resources/fonts and are
 * registered with mPDF below. The Blade views' @font-face blocks are stripped
 * before rendering — mPDF resolves fonts via its own registry, not @font-face.
 */
class Pdf
{
    public static function make(string $view, array $data = []): string
    {
        $html = View::make($view, $data)->render();

        // Drop @font-face blocks — mPDF resolves fonts via its registry below.
        $html = preg_replace('/@font-face\s*\{[^}]*\}/i', '', $html);

        $tmp = storage_path('app/mpdf-tmp');
        if (! is_dir($tmp)) {
            @mkdir($tmp, 0775, true);
        }

        // Register the bundled Tajawal font alongside mPDF's defaults.
        $defaultFontDir  = (new \Mpdf\Config\ConfigVariables())->getDefaults()['fontDir'];
        $defaultFontData = (new \Mpdf\Config\FontVariables())->getDefaults()['fontdata'];

        $mpdf = new Mpdf([
            'mode'             => 'utf-8',
            'format'           => 'A4',
            'directionality'   => 'rtl',
            'default_font'     => 'tajawal',
            'default_font_size' => 11,
            'tempDir'          => $tmp,
            'fontDir'          => array_merge($defaultFontDir, [resource_path('fonts')]),
            'fontdata'         => $defaultFontData + [
                // Tajawal + an "amiri" alias so the templates' font-family:'Amiri'
                // declarations resolve to Tajawal instead of falling back to DejaVu.
                'tajawal' => [
                    'R'          => 'Tajawal-Regular.ttf',
                    'B'          => 'Tajawal-Bold.ttf',
                    'useOTL'     => 0xFF,
                    'useKashida' => 75,
                ],
                'amiri' => [
                    'R'          => 'Tajawal-Regular.ttf',
                    'B'          => 'Tajawal-Bold.ttf',
                    'useOTL'     => 0xFF,
                    'useKashida' => 75,
                ],
            ],
            // Off, so our Tajawal font (not mPDF's bundled xbriyaz) shapes the Arabic.
            'autoScriptToLang' => false,
            'autoLangToFont'   => false,
            'margin_top'       => 12,
            'margin_bottom'    => 12,
            'margin_left'      => 12,
            'margin_right'     => 12,
        ]);

        $mpdf->WriteHTML($html);

        return $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);
    }
}
