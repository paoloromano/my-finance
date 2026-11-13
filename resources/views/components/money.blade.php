@props(['amount', 'decimals' => 2, 'sign' => false, 'type' => null])
@php
    $isIncome = $type === 'income';
    $class = $type ? ($isIncome ? 'text-emerald-600' : 'text-rose-600') : '';
@endphp
<span {{ $attributes->class([$class, 'tabular-nums']) }}>@if ($type){{ $isIncome ? '+' : '−' }} @endif{{ eur($amount, $decimals) }}</span>
