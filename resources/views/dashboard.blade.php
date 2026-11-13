@extends('layouts.app')

@section('title', 'Panoramica')

@section('content')
  @php($s = $summary->settings)

  <div class="mb-4 grid grid-cols-2 gap-3 lg:grid-cols-4 lg:gap-4">
    <x-kpi label="Ricavi professionali medi" :value="eur($summary->revenue, 0)" tone="blue" foot="PS Company · Abifin · Visiospot" />
    <x-kpi label="Fondo Fisco & INPS" :value="eur($s['tax_reserve'], 0)" foot="Saldo configurato" />
    <x-kpi label="Accantonamento mensile" :value="eur($summary->taxAccrual, 0)" :foot="'Aliquota simulata '.pct($s['tax_rate'])" />
    <x-kpi label="Margine dopo prelievo" :value="eur($summary->marginAfterTransfer, 0)" :tone="$summary->marginAfterTransfer >= 0 ? 'positive' : 'negative'" foot="Prima di altri costi e riserve" />
  </div>

  <div class="grid gap-4 lg:grid-cols-[1.35fr_1fr]">
    <div class="card">
      <x-section-head title="Entrate professionali"><span class="pill">Mese tipo</span></x-section-head>
      <div class="grid gap-4">
        @foreach ($summary->revenueSources() as $name => $value)
          <div class="grid grid-cols-[100px_1fr_80px] items-center gap-3 text-xs">
            <span>{{ $name }}</span>
            <div class="h-2 overflow-hidden rounded-full bg-slate-100">
              <div class="h-full rounded-full bg-indigo-500" style="width: {{ $summary->revenue > 0 ? max(1, $value / $summary->revenue * 100) : 0 }}%"></div>
            </div>
            <span class="text-right font-bold tabular-nums">{{ eur($value, 0) }}</span>
          </div>
        @endforeach
      </div>
      <div class="mt-3">
        <div class="metric-line"><span>Ricavi annui teorici</span><b>{{ eur($summary->revenue * 12, 0) }}</b></div>
        <div class="metric-line"><span>Trasferimento personale</span><b>{{ eur($s['personal_transfer'], 0) }}</b></div>
        <div class="metric-line">
          <span>Entrate lavoro registrate {{ $year }}</span>
          <a href="{{ route('incomes.index') }}" class="font-bold text-indigo-600 hover:underline">{{ eur($incomeYear, 0) }}</a>
        </div>
        <div class="metric-line"><span>· di cui Partita IVA</span><b>{{ eur($incomeYearPiva, 0) }}</b></div>
      </div>
    </div>

    <div class="card">
      <x-section-head title="Bilancio familiare"><span class="pill">Mensile</span></x-section-head>
      <div class="metric-line"><span>Entrate conto comune</span><b>{{ eur($s['family_income'], 0) }}</b></div>
      <div class="metric-line"><span>Spese note</span><b>{{ eur($summary->familyCosts, 0) }}</b></div>
      <div class="metric-line">
        <span>Margine teorico</span>
        <b @class(['text-emerald-600' => $summary->familyMargin >= 0, 'text-rose-600' => $summary->familyMargin < 0])>{{ eur($summary->familyMargin, 0) }}</b>
      </div>
      <p class="mt-2 text-[11px] text-slate-500">Escluse spese variabili per Riccardo e imprevisti.</p>

      <div class="mt-5">
        <x-section-head title="Prossime scadenze">
          <a href="{{ route('deadlines.index') }}" class="btn btn-sm">Tutte →</a>
        </x-section-head>
        @forelse ($nextDeadlines as $d)
          <div class="metric-line">
            <span>
              <span @class(['font-semibold', 'text-rose-600' => $d->isOverdue()])>{{ $d->due_on->format('d/m/Y') }}</span>
              <span class="text-slate-500">· {{ $d->label }}</span>
            </span>
            <b class="tabular-nums">{{ eur($d->amount) }}</b>
          </div>
        @empty
          <p class="text-xs text-slate-500">Nessuna scadenza aperta.</p>
        @endforelse
      </div>
    </div>
  </div>

  <div class="card mt-4">
    <x-section-head title="Ultimi movimenti">
      <a href="{{ route('movements.index') }}" class="btn">Tutti i movimenti →</a>
    </x-section-head>
    <div class="overflow-x-auto">
      <table class="table-base">
        <thead><tr><th>Data</th><th>Descrizione</th><th>Area</th><th class="text-right!">Importo</th></tr></thead>
        <tbody>
          @forelse ($latestMovements as $m)
            <tr>
              <td>{{ $m->occurred_on->format('d/m/Y') }}</td>
              <td>{{ $m->description }}</td>
              <td><span class="pill">{{ $m->area_label }}</span></td>
              <td class="text-right"><x-money :amount="$m->amount" :type="$m->type" :decimals="0" /></td>
            </tr>
          @empty
            <tr><td colspan="4" class="py-9 text-center text-slate-500">Nessun movimento registrato</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <x-notice>Nota: i valori iniziali sono quelli comunicati in chat. Le simulazioni non sono una liquidazione fiscale; verificare saldo e acconti con il commercialista.</x-notice>
@endsection
