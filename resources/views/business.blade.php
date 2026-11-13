@extends('layouts.app')

@section('title', 'Partita IVA')

@section('content')
  @php($s = $summary->settings)

  <form method="POST" action="{{ route('business.update') }}" class="card mb-4">
    @csrf
    @method('PUT')
    <x-section-head title="Ricavi ricorrenti medi"><span class="pill">Modifica i valori</span></x-section-head>
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
      <x-input name="ps" label="PS Company (€ / mese)" type="number" step="0.01" min="0" :value="$s['ps']" required />
      <x-input name="abifin" label="Abifin (€ / mese)" type="number" step="0.01" min="0" :value="$s['abifin']" required />
      <x-input name="visio" label="Visiospot (€ / mese)" type="number" step="0.01" min="0" :value="$s['visio']" required />
      <x-input name="tax_rate" label="Aliquota accantonamento %" type="number" step="0.01" min="0" max="100" :value="$s['tax_rate']" required />
      <x-input name="personal_transfer" label="Trasferimento personale €" type="number" step="0.01" min="0" :value="$s['personal_transfer']" required />
      <x-input name="tax_reserve" label="Fondo fiscale attuale €" type="number" step="0.01" :value="$s['tax_reserve']" required />
    </div>
    <div class="mt-4"><button type="submit" class="btn btn-primary">Salva impostazioni</button></div>
  </form>

  <div class="mb-4 grid grid-cols-2 gap-3 lg:grid-cols-4 lg:gap-4">
    <x-kpi label="Ricavi medi mensili" :value="eur($summary->revenue, 0)" tone="blue" />
    <x-kpi label="Accantonamento teorico" :value="eur($summary->taxAccrual, 0)" />
    <x-kpi label="Dopo accantonamento e trasferimento" :value="eur($summary->marginAfterTransfer, 0)" :tone="$summary->marginAfterTransfer >= 0 ? 'positive' : 'negative'" />
    <x-kpi label="Ricavi annui teorici" :value="eur($summary->revenue * 12, 0)" />
  </div>

  <div class="card">
    <x-section-head title="Regole operative" />
    <div class="metric-line"><span>Accantonamento su incassi</span><b>{{ pct($s['tax_rate']) }} (provvisorio)</b></div>
    <div class="metric-line"><span>Trasferimento personale</span><b>{{ eur($s['personal_transfer'], 0) }} / mese</b></div>
    <div class="metric-line"><span>Arretrati Abifin da considerare nel 2026</span><b>8.960 €</b></div>
    <x-notice class="mb-0">Gli arretrati Abifin sono già sette mensilità da 1.280 €. Il possibile nuovo bando da circa 10.000 € è escluso finché non sarà certo. Non sommare due volte gli stessi compensi.</x-notice>
  </div>
@endsection
