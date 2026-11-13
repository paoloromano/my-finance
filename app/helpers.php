<?php

if (! function_exists('eur')) {
    function eur(float|int|string|null $value, int $decimals = 2): string
    {
        return number_format((float) $value, $decimals, ',', '.').' €';
    }
}

if (! function_exists('pct')) {
    function pct(float|int|string|null $value): string
    {
        return rtrim(rtrim(number_format((float) $value, 2, ',', '.'), '0'), ',').'%';
    }
}
