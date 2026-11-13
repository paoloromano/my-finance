@php
    $nav = [
        ['home', 'home', '◫', 'Panoramica'],
        ['incomes.index', 'incomes.*', '€', 'Entrate lavoro'],
        ['business', 'business*', '▤', 'Partita IVA'],
        ['movements.index', 'movements.*', '↕', 'Movimenti'],
        ['family', 'family*', '⌂', 'Famiglia'],
        ['deadlines.index', 'deadlines.*', '◷', 'Scadenze'],
        ['settings', 'settings*', '⚙', 'Impostazioni'],
    ];
    $title = trim($__env->yieldContent('title', 'Panoramica'));
@endphp
<!doctype html>
<html lang="it" class="h-full bg-slate-100">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="theme-color" content="#101827">
  <meta name="robots" content="noindex,nofollow">
  <title>{{ $title }} · MyFinance</title>
  @fonts
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans text-slate-800 antialiased">
  <div class="min-h-full md:grid md:grid-cols-[248px_1fr]">
    {{-- Sidebar --}}
    <aside class="sticky top-0 z-10 flex flex-col bg-nav px-3 py-3 text-white md:h-screen md:px-4 md:py-6">
      <div class="flex items-center gap-3 px-2.5 pb-3 md:pb-9">
        <div class="grid size-9 place-items-center rounded-xl bg-indigo-600 font-display text-xl font-extrabold">M</div>
        <div>
          <div class="font-display text-base font-bold leading-tight">my-finance</div>
          <div class="text-[9px] tracking-[.15em] text-slate-400">PERSONAL FINANCE</div>
        </div>
      </div>
      <nav class="-mx-1 flex gap-1 overflow-x-auto md:mx-0 md:flex-col md:overflow-visible">
        @foreach ($nav as [$route, $pattern, $icon, $label])
          @php $active = request()->routeIs($pattern); @endphp
          <a href="{{ route($route) }}"
             @class([
                 'flex shrink-0 items-center gap-3 rounded-lg px-3.5 py-2.5 text-sm transition md:py-3',
                 'bg-nav-hover text-white shadow-[inset_3px_0_#6681ff]' => $active,
                 'text-slate-400 hover:bg-nav-hover hover:text-white' => ! $active,
             ])
             @if ($active) aria-current="page" @endif>
            <span class="w-4 text-center text-base">{{ $icon }}</span>
            <span class="text-[13px]">{{ $label }}</span>
          </a>
        @endforeach
      </nav>
      <div class="mt-auto hidden items-center justify-between gap-2 px-2.5 pt-4 md:flex">
        <div class="flex items-center gap-2 text-xs text-slate-300">
          <span class="size-1.5 rounded-full bg-emerald-400"></span>{{ auth()->user()->name }}
        </div>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="cursor-pointer rounded-lg border border-slate-700 px-2.5 py-1.5 text-[11px] font-semibold text-slate-400 hover:bg-nav-hover hover:text-white">Esci</button>
        </form>
      </div>
    </aside>

    {{-- Contenuto --}}
    <main class="mx-auto w-full max-w-[1600px] px-4 py-6 md:px-[clamp(20px,4vw,54px)] md:py-7">
      <header class="mb-7 flex flex-wrap items-center justify-between gap-3">
        <div>
          <div class="text-[10px] font-bold tracking-[.17em] text-slate-500">AREA PERSONALE</div>
          <h1 class="mt-1 font-display text-2xl font-extrabold md:text-[28px]">{{ $title }}</h1>
        </div>
        <div class="flex items-center gap-3">
          <span class="hidden text-xs text-slate-500 lg:inline">{{ now()->locale('it')->isoFormat('ddd D MMM YYYY') }}</span>
          @hasSection('actions')
            @yield('actions')
          @else
            <button type="button" class="btn btn-primary" data-open-dialog="movement-dialog">＋ Nuovo movimento</button>
          @endif
          <form method="POST" action="{{ route('logout') }}" class="md:hidden">
            @csrf
            <button type="submit" class="btn">Esci</button>
          </form>
        </div>
      </header>

      @if (session('status'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-medium text-emerald-700" role="status">{{ session('status') }}</div>
      @endif
      @error('session')
        <x-notice>{{ $message }}</x-notice>
      @enderror

      @yield('content')

      <footer class="pt-8 pb-2 text-center text-[10px] text-slate-400">MyFinance · Dati salvati sul server</footer>
    </main>
  </div>

  {{-- Nuovo movimento (disponibile da ogni pagina) --}}
  <dialog id="movement-dialog" class="m-auto w-[min(520px,calc(100%-30px))] rounded-2xl p-6 shadow-2xl backdrop:bg-slate-950/65"
          @if ($errors->movement->any()) data-open-on-load @endif>
    <form method="POST" action="{{ route('movements.store') }}" class="space-y-4">
      @csrf
      <div class="flex items-center justify-between">
        <h2 class="font-display text-lg font-bold">Nuovo movimento</h2>
        <button type="button" class="cursor-pointer text-lg text-slate-500" data-close-dialog aria-label="Chiudi">✕</button>
      </div>
      @include('movements._fields', ['movement' => null, 'bag' => 'movement'])
      <div class="flex justify-end gap-2 pt-2">
        <button type="button" class="btn" data-close-dialog>Annulla</button>
        <button type="submit" class="btn btn-primary">Salva movimento</button>
      </div>
    </form>
  </dialog>
</body>
</html>
