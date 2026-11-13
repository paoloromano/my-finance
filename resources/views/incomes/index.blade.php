@extends('layouts.app')

@section('title', 'Entrate lavoro')

@section('actions')
  <a class="btn btn-primary" href="{{ route('incomes.create') }}">＋ Nuova entrata</a>
@endsection

@section('content')
  <form method="GET" class="card mb-4 grid items-end gap-3 py-4 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_1fr_auto]">
    <x-select name="year" label="Anno" :options="['all' => 'Tutti'] + $years->mapWithKeys(fn ($y) => [$y => $y])->all()"
              :value="$year ?? 'all'" onchange="this.form.submit()" />
    <x-select name="category" label="Tipologia" :options="\App\Models\Income::CATEGORIES" :value="$category"
              placeholder="Tutte" onchange="this.form.submit()" />
    <x-select name="source" label="Committente" :options="array_combine($sources, $sources)" :value="$source"
              placeholder="Tutti" onchange="this.form.submit()" />
    <a class="btn" href="{{ route('incomes.index') }}">Azzera filtri</a>
  </form>

  @php
    $gross = (float) $totals->gross;
    $wh = (float) $totals->withholding;
    $piva = (float) ($byCategory['piva'] ?? 0);
  @endphp
  <div class="mb-4 grid grid-cols-2 gap-3 lg:grid-cols-4 lg:gap-4">
    <x-kpi label="Totale lordo" :value="eur($gross, 0)" tone="blue" :foot="$totals->n.' entrate · '.($year ?? 'tutti gli anni')" />
    <x-kpi label="Partita IVA" :value="eur($piva, 0)" foot="Fatture / incassi P.IVA" />
    <x-kpi label="Altri redditi da lavoro" :value="eur($gross - $piva, 0)" foot="Dipendente, collaborazioni, occasionali…" />
    <x-kpi label="Netto dopo ritenute" :value="eur($gross - $wh, 0)" tone="positive" :foot="'Ritenute: '.eur($wh, 0)" />
  </div>

  @if ($bySource->isNotEmpty())
    <div class="card mb-4">
      <x-section-head title="Per committente"><span class="pill">Lordo</span></x-section-head>
      <div class="grid gap-4">
        @foreach ($bySource as $row)
          <div class="grid grid-cols-[120px_1fr_90px] items-center gap-3 text-xs">
            <span class="truncate">{{ $row->source }}</span>
            <div class="h-2 overflow-hidden rounded-full bg-slate-100">
              <div class="h-full rounded-full bg-indigo-500" style="width: {{ $gross > 0 ? max(1, $row->gross / $gross * 100) : 0 }}%"></div>
            </div>
            <span class="text-right font-bold tabular-nums">{{ eur($row->gross, 0) }}</span>
          </div>
        @endforeach
      </div>
    </div>
  @endif

  <div class="card">
    <x-section-head title="Registro entrate"><span class="pill">{{ $incomes->total() }} righe</span></x-section-head>
    <div class="overflow-x-auto">
      <table class="table-base">
        <thead>
          <tr><th>Data</th><th>Committente</th><th>Tipologia</th><th>Descrizione</th><th>Doc.</th><th class="text-right!">Lordo</th><th class="text-right!">Ritenuta</th><th class="text-right!">Netto</th><th></th></tr>
        </thead>
        <tbody>
          @forelse ($incomes as $income)
            <tr>
              <td>{{ $income->received_on->format('d/m/Y') }}</td>
              <td class="font-semibold">{{ $income->source }}</td>
              <td><span @class(['pill', 'bg-emerald-50! text-emerald-700!' => $income->category !== 'piva'])>{{ $income->category_label }}</span></td>
              <td class="min-w-40 whitespace-normal!">{{ $income->description }}</td>
              <td>{{ $income->document_number }}</td>
              <td class="text-right tabular-nums">{{ eur($income->gross_amount) }}</td>
              <td class="text-right text-slate-500 tabular-nums">{{ (float) $income->withholding_amount ? eur($income->withholding_amount) : '—' }}</td>
              <td class="text-right font-semibold text-emerald-600 tabular-nums">{{ eur($income->net_amount) }}</td>
              <td>
                <div class="flex justify-end gap-1.5">
                  <a class="btn btn-sm" href="{{ route('incomes.edit', $income) }}">Modifica</a>
                  <form method="POST" action="{{ route('incomes.destroy', $income) }}" data-confirm="Eliminare questa entrata?">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger" type="submit">Elimina</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="9" class="py-9 text-center text-slate-500">Nessuna entrata registrata. <a href="{{ route('incomes.create') }}" class="text-indigo-600 hover:underline">Aggiungi la prima</a>.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-4">{{ $incomes->links() }}</div>
  </div>
@endsection
