@extends('layouts.app')

@section('title', 'Scadenze')

@section('actions')
  <button type="button" class="btn btn-primary" data-open-dialog="deadline-dialog">＋ Nuova scadenza</button>
@endsection

@section('content')
  <div class="mb-4 grid grid-cols-2 gap-3 lg:grid-cols-4 lg:gap-4">
    <x-kpi label="Da pagare" :value="eur($openTotal, 0)" tone="blue" :foot="$openCount.' scadenze aperte'" />
    <x-kpi label="Prossimi 30 giorni" :value="eur($next30, 0)" />
    <x-kpi label="Scadute non pagate" :value="$overdueCount" :tone="$overdueCount > 0 ? 'negative' : 'positive'" />
    <x-kpi label="Rata media" :value="eur($openCount ? $openTotal / $openCount : 0, 0)" />
  </div>

  <div class="card">
    <x-section-head title="Scadenze e rate">
      <a href="{{ route('deadlines.index', $showPaid ? [] : ['pagate' => 1]) }}" class="btn btn-sm">
        {{ $showPaid ? 'Nascondi pagate' : 'Mostra anche pagate' }}
      </a>
    </x-section-head>
    <div class="overflow-x-auto">
      <table class="table-base">
        <thead><tr><th>Scadenza</th><th>Descrizione</th><th>Categoria</th><th class="text-right!">Importo</th><th>Stato</th><th></th></tr></thead>
        <tbody>
          @forelse ($deadlines as $d)
            <tr @class(['opacity-50' => $d->isPaid()])>
              <td @class(['font-semibold text-rose-600' => $d->isOverdue()])>{{ $d->due_on->format('d/m/Y') }}</td>
              <td class="whitespace-normal!">{{ $d->label }}</td>
              <td>{{ $d->category }}</td>
              <td class="text-right font-semibold tabular-nums">{{ eur($d->amount) }}</td>
              <td>
                @if ($d->isPaid())
                  <span class="pill bg-emerald-50! text-emerald-700!">Pagata {{ $d->paid_on->format('d/m') }}</span>
                @elseif ($d->isOverdue())
                  <span class="pill bg-rose-50! text-rose-700!">Scaduta</span>
                @else
                  <span class="pill bg-amber-50! text-amber-700!">Da pagare</span>
                @endif
              </td>
              <td>
                <div class="flex justify-end gap-1.5">
                  <form method="POST" action="{{ route('deadlines.toggle', $d) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-sm">{{ $d->isPaid() ? 'Annulla pagamento' : '✓ Pagata' }}</button>
                  </form>
                  <a href="{{ route('deadlines.edit', $d) }}" class="btn btn-sm">Modifica</a>
                  <form method="POST" action="{{ route('deadlines.destroy', $d) }}" data-confirm="Eliminare questa scadenza?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Elimina</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="py-9 text-center text-slate-500">Nessuna scadenza</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <x-notice class="mb-0">Le rate F24 personali vanno sostenute con il trasferimento personale, non con il fondo Fisco & INPS dell'attività. Controllare gli F24 prima del pagamento.</x-notice>
  </div>

  <dialog id="deadline-dialog" class="m-auto w-[min(520px,calc(100%-30px))] rounded-2xl p-6 shadow-2xl backdrop:bg-slate-950/65"
          @if ($errors->deadline->any()) data-open-on-load @endif>
    <form method="POST" action="{{ route('deadlines.store') }}" class="space-y-4">
      @csrf
      <div class="flex items-center justify-between">
        <h2 class="font-display text-lg font-bold">Nuova scadenza</h2>
        <button type="button" class="cursor-pointer text-lg text-slate-500" data-close-dialog aria-label="Chiudi">✕</button>
      </div>
      @include('deadlines._fields', ['deadline' => null, 'bag' => 'deadline'])
      <div class="flex justify-end gap-2 pt-2">
        <button type="button" class="btn" data-close-dialog>Annulla</button>
        <button type="submit" class="btn btn-primary">Salva scadenza</button>
      </div>
    </form>
  </dialog>
@endsection
