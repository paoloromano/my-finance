@props(['label', 'value', 'foot' => null, 'tone' => null])
@php
    $toneClass = match ($tone) {
        'positive' => 'text-emerald-600',
        'negative' => 'text-rose-600',
        'blue' => 'text-indigo-600',
        default => 'text-slate-800',
    };
@endphp
<div {{ $attributes->merge(['class' => 'card']) }}>
  <div class="text-[11px] font-semibold text-slate-500">{{ $label }}</div>
  <div class="mt-2.5 mb-1 font-display text-xl font-extrabold tracking-tight md:text-[25px] {{ $toneClass }}">{{ $value }}</div>
  @if ($foot)
    <div class="text-[11px] text-slate-500">{{ $foot }}</div>
  @endif
</div>
