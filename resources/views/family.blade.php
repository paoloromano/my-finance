@extends('layouts.app')

@section('title', 'Famiglia')

@section('content')
  @php($s = $summary->settings)

  <div class="mb-4 grid grid-cols-2 gap-3 lg:grid-cols-3 lg:gap-4">
    <x-kpi label="Entrate conto comune" :value="eur($s['family_income'], 0)" tone="blue" />
    <x-kpi label="Spese note" :value="eur($summary->familyCosts, 0)" />
    <x-kpi label="Margine teorico" :value="eur($summary->familyMargin, 0)" :tone="$summary->familyMargin >= 0 ? 'positive' : 'negative'" foot="Escluse spese variabili e imprevisti" />
  </div>

  <div class="grid items-start gap-4 lg:grid-cols-[1fr_1.6fr]">
    <div class="space-y-4">
      <form method="POST" action="{{ route('family.income') }}" class="card">
        @csrf
        @method('PUT')
        <x-section-head title="Entrate conto comune" />
        <x-input name="family_income" label="Contributo complessivo mensile (€)" type="number" step="0.01" min="0" :value="$s['family_income']" required />
        <div class="mt-4"><button type="submit" class="btn btn-primary">Salva</button></div>
      </form>

      <form method="POST" action="{{ route('family.expenses.store') }}" class="card">
        @csrf
        <x-section-head title="Aggiungi voce di spesa" />
        <div class="grid grid-cols-[1fr_120px] gap-3">
          <x-input name="label" label="Voce" :value="null" required maxlength="255" placeholder="Es. Palestra" />
          <x-input name="amount" label="Importo €" type="number" step="0.01" min="0" :value="null" required />
        </div>
        <div class="mt-4"><button type="submit" class="btn btn-primary">＋ Aggiungi</button></div>
      </form>
    </div>

    <div class="card">
      <x-section-head title="Spese familiari mensili"><span class="pill">Modificabili</span></x-section-head>
      @if ($expenses->isEmpty())
        <p class="py-6 text-center text-xs text-slate-500">Nessuna voce. Aggiungine una dal riquadro a fianco.</p>
      @else
        <form method="POST" action="{{ route('family.expenses.update') }}" id="expenses-form">
          @csrf
          @method('PUT')
        </form>
        <div class="divide-y divide-slate-100">
          @foreach ($expenses as $e)
            <div class="flex items-start gap-2 py-2">
              <x-input class="flex-1" :name="'expenses['.$e->id.'][label]'" :value="$e->label" form="expenses-form" required maxlength="255" aria-label="Voce" />
              <x-input class="w-32" :name="'expenses['.$e->id.'][amount]'" type="number" step="0.01" min="0" :value="$e->amount" form="expenses-form" required aria-label="Importo" />
              <form method="POST" action="{{ route('family.expenses.destroy', $e) }}" data-confirm="Eliminare la voce «{{ $e->label }}»?">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger mt-0.5 px-3" aria-label="Elimina {{ $e->label }}">✕</button>
              </form>
            </div>
          @endforeach
        </div>
        <div class="mt-3 flex items-center justify-between border-t border-slate-200 pt-4">
          <button type="submit" form="expenses-form" class="btn btn-primary">Salva spese</button>
          <span class="text-sm">Totale <b class="tabular-nums">{{ eur($summary->familyCosts) }}</b></span>
        </div>
      @endif
    </div>
  </div>
@endsection
