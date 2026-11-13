@extends('layouts.app')

@section('title', 'Movimenti')

@section('content')
  <div class="mb-4 grid grid-cols-2 gap-3 lg:grid-cols-4 lg:gap-4">
    <x-kpi label="Entrate registrate" :value="eur($income, 0)" tone="positive" />
    <x-kpi label="Uscite registrate" :value="eur($expense, 0)" tone="negative" />
    <x-kpi label="Saldo movimenti" :value="eur($income - $expense, 0)" :tone="$income >= $expense ? 'positive' : 'negative'" />
    <x-kpi label="Movimenti" :value="$movements->total()" />
  </div>

  <div class="card">
    <x-section-head title="Registro movimenti">
      <div class="flex flex-wrap items-center gap-2">
        <form method="GET" class="flex gap-2">
          <select name="type" class="input py-2! text-xs!" onchange="this.form.submit()" aria-label="Tipo">
            <option value="">Tutti i tipi</option>
            @foreach (\App\Models\Movement::TYPES as $k => $v)
              <option value="{{ $k }}" @selected($type === $k)>{{ $v }}</option>
            @endforeach
          </select>
          <select name="area" class="input py-2! text-xs!" onchange="this.form.submit()" aria-label="Area">
            <option value="">Tutte le aree</option>
            @foreach (\App\Models\Movement::AREAS as $k => $v)
              <option value="{{ $k }}" @selected($area === $k)>{{ $v }}</option>
            @endforeach
          </select>
        </form>
        <button type="button" class="btn btn-primary" data-open-dialog="movement-dialog">＋ Aggiungi</button>
      </div>
    </x-section-head>

    <div class="overflow-x-auto">
      <table class="table-base">
        <thead><tr><th>Data</th><th>Descrizione</th><th>Area</th><th>Tipo</th><th class="text-right!">Importo</th><th></th></tr></thead>
        <tbody>
          @forelse ($movements as $m)
            <tr>
              <td>{{ $m->occurred_on->format('d/m/Y') }}</td>
              <td class="whitespace-normal!">
                {{ $m->description }}
                @if ($m->note)<div class="text-[11px] text-slate-500">{{ $m->note }}</div>@endif
              </td>
              <td><span class="pill">{{ $m->area_label }}</span></td>
              <td>{{ \App\Models\Movement::TYPES[$m->type] ?? $m->type }}</td>
              <td class="text-right"><x-money :amount="$m->amount" :type="$m->type" /></td>
              <td>
                <div class="flex justify-end gap-1.5">
                  <a href="{{ route('movements.edit', $m) }}" class="btn btn-sm">Modifica</a>
                  <form method="POST" action="{{ route('movements.destroy', $m) }}" data-confirm="Eliminare questo movimento?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Elimina</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="py-9 text-center text-slate-500">Nessun movimento presente</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-4">{{ $movements->links() }}</div>
  </div>
@endsection
