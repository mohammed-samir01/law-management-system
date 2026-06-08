<?php

if (! function_exists('fmt_price')) {
    /**
     * Convert a price from EGP (base) to the visitor's currency and format it.
     *
     * @param  float       $egpAmount   Amount in EGP
     * @param  string|null $forceCurrency  Override currency code
     * @return array{amount: string, symbol: string, symbol_en: string, code: string}
     */
    function fmt_price(float $egpAmount, ?string $forceCurrency = null): array
    {
        $code       = $forceCurrency ?? session('visitor_currency', config('currencies.fallback', 'USD'));
        $currencies = config('currencies.currencies', []);
        $def        = $currencies[$code] ?? $currencies[config('currencies.fallback')];

        $converted = $egpAmount / $def['rate'];
        $formatted = number_format($converted, $def['decimals']);

        return [
            'amount'     => $formatted,
            'symbol'     => $def['symbol'],
            'symbol_en'  => $def['symbol_en'],
            'code'       => $code,
        ];
    }
}
