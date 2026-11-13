@props(['name', 'label' => null, 'type' => 'text', 'value' => null, 'bag' => 'default'])
@php
    $key = trim(str_replace(['[', ']'], ['.', ''], $name), '.');
    $id = $attributes->get('id', 'f-'.str_replace('.', '-', $key));
    $error = $errors->getBag($bag)->first($key);
@endphp
<div {{ $attributes->only('class') }}>
  @if ($label)
    <label for="{{ $id }}" class="label">{{ $label }}</label>
  @endif
  <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($key, $value) }}"
         {{ $attributes->except(['class', 'id'])->class(['input', 'input-error' => $error]) }}>
  @if ($error)
    <p class="mt-1.5 text-[11px] text-rose-600">{{ $error }}</p>
  @endif
</div>
