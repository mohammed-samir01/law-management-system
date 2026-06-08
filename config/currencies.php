<?php

return [

    /*
     * Base currency stored in the database.
     */
    'base' => 'EGP',

    /*
     * Country → currency mapping (ISO 3166-1 alpha-2 → ISO 4217).
     */
    'country_map' => [
        'EG' => 'EGP',
        'SA' => 'SAR',
        'AE' => 'AED',
        'KW' => 'KWD',
        'QA' => 'QAR',
        'BH' => 'BHD',
        'OM' => 'OMR',
        'JO' => 'JOD',
        'LB' => 'USD',
        'IQ' => 'USD',
        'LY' => 'USD',
        'MA' => 'USD',
        'TN' => 'USD',
        'DZ' => 'USD',
        'SD' => 'USD',
    ],

    /*
     * Currency definitions.
     * rate = how many EGP = 1 unit of this currency (divide EGP price by rate)
     */
    'currencies' => [
        'EGP' => ['symbol' => 'ج.م', 'symbol_en' => 'EGP', 'rate' => 1,      'decimals' => 0],
        'SAR' => ['symbol' => 'ر.س', 'symbol_en' => 'SAR', 'rate' => 13.5,   'decimals' => 0],
        'AED' => ['symbol' => 'د.إ', 'symbol_en' => 'AED', 'rate' => 13.6,   'decimals' => 0],
        'KWD' => ['symbol' => 'د.ك', 'symbol_en' => 'KWD', 'rate' => 162.0,  'decimals' => 2],
        'QAR' => ['symbol' => 'ر.ق', 'symbol_en' => 'QAR', 'rate' => 13.7,   'decimals' => 0],
        'BHD' => ['symbol' => 'د.ب', 'symbol_en' => 'BHD', 'rate' => 132.0,  'decimals' => 2],
        'OMR' => ['symbol' => 'ر.ع', 'symbol_en' => 'OMR', 'rate' => 130.0,  'decimals' => 2],
        'JOD' => ['symbol' => 'د.أ', 'symbol_en' => 'JOD', 'rate' => 70.0,   'decimals' => 2],
        'USD' => ['symbol' => '$',   'symbol_en' => 'USD', 'rate' => 50.0,   'decimals' => 0],
    ],

    /*
     * Fallback currency for countries not in the map.
     */
    'fallback' => 'USD',
];
