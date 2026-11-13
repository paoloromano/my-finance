@props(['title'])
<div class="mb-4 flex flex-wrap items-center justify-between gap-2">
  <h2 class="font-display text-[15px] font-bold">{{ $title }}</h2>
  {{ $slot }}
</div>
