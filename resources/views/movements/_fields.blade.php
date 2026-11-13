@php($bag = $bag ?? 'default')
<x-input name="description" label="Descrizione" :value="$movement?->description" :bag="$bag" required maxlength="255" placeholder="Es. Incasso PS Company" :id="$bag.'-description'" autofocus />
<div class="grid grid-cols-2 gap-3">
  <x-input name="amount" label="Importo (€)" type="number" step="0.01" min="0.01" :value="$movement?->amount" :bag="$bag" required :id="$bag.'-amount'" />
  <x-input name="occurred_on" label="Data" type="date" :value="($movement?->occurred_on ?? now())->format('Y-m-d')" :bag="$bag" required :id="$bag.'-occurred_on'" />
  <x-select name="type" label="Tipo" :options="\App\Models\Movement::TYPES" :value="$movement?->type ?? 'income'" :bag="$bag" :id="$bag.'-type'" />
  <x-select name="area" label="Area" :options="\App\Models\Movement::AREAS" :value="$movement?->area ?? 'business'" :bag="$bag" :id="$bag.'-area'" />
</div>
<x-input name="note" label="Note" :value="$movement?->note" :bag="$bag" maxlength="255" placeholder="Facoltative" :id="$bag.'-note'" />
