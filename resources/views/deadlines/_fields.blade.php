@php($bag = $bag ?? 'default')
<div class="grid gap-3 sm:grid-cols-2">
  <x-input name="due_on" label="Data scadenza" type="date" :value="$deadline?->due_on?->format('Y-m-d')" :bag="$bag" required :id="$bag.'-due_on'" />
  <x-input name="amount" label="Importo (€)" type="number" step="0.01" min="0" :value="$deadline?->amount" :bag="$bag" required :id="$bag.'-amount'" />
</div>
<x-input name="label" label="Descrizione" :value="$deadline?->label" :bag="$bag" required maxlength="255" placeholder="Es. F24 piano 1 · rata 11" :id="$bag.'-label'" />
<x-input name="category" label="Categoria" :value="$deadline?->category" :bag="$bag" maxlength="255" list="deadline-categories" placeholder="Es. F24 personale" :id="$bag.'-category'" />
<datalist id="deadline-categories">
  @foreach ($categories as $c)
    <option value="{{ $c }}"></option>
  @endforeach
</datalist>
