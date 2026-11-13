@props(['name', 'label' => null, 'options' => [], 'value' => null, 'placeholder' => null, 'bag' => 'default'])
@php
    $key = trim(str_replace(['[', ']'], ['.', ''], $name), '.');
    $id = $attributes->get('id', 'f-'.str_replace('.', '-', $key));
    $error = $errors->getBag($bag)->first($key);
    $selected = (string) old($key, $value);
@endphp
<div {{ $attributes->only('class') }}>
  @if ($label)
    <label for="{{ $id }}" class="label">{{ $label }}</label>
  @endif
  <select id="{{ $id }}" name="{{ $name }}" {{ $attributes->except(['class', 'id'])->class(['input', 'input-error' => $error]) }}>
    @if ($placeholder !== null)
      <option value="">{{ $placeholder }}</option>
    @endif
    @foreach ($options as $optValue => $optLabel)
      <option value="{{ $optValue }}" @selected($selected === (string) $optValue)>{{ $optLabel }}</option>
    @endforeach
  </select>
  @if ($error)
    <p class="mt-1.5 text-[11px] text-rose-600">{{ $error }}</p>
  @endif
</div>
