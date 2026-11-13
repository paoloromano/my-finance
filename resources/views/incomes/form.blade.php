@extends('layouts.app')

@section('title', $income->exists ? 'Modifica entrata' : 'Nuova entrata')

@section('actions')
  <a class="btn" href="{{ route('incomes.index') }}">← Torna al registro</a>
@endsection

@section('content')
  <form method="POST" class="card max-w-4xl space-y-4"
        action="{{ $income->exists ? route('incomes.update', $income) : route('incomes.store') }}">
    @csrf
    @if ($income->exists)
      @method('PUT')
    @endif

    @if ($errors->any())
      <p class="text-xs font-medium text-rose-600">Controlla i campi evidenziati.</p>
    @endif

    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
      <x-input name="received_on" label="Data incasso *" type="date" :value="$income->received_on?->format('Y-m-d')" required />
      <x-select name="category" label="Tipologia *" :options="\App\Models\Income::CATEGORIES" :value="$income->category" required />
      <x-input name="source" label="Committente / datore *" :value="$income->source" list="sources" required maxlength="255" placeholder="Es. PS Company" />
      <x-input name="gross_amount" label="Importo lordo (€) *" type="number" step="0.01" min="0.01" :value="$income->gross_amount" required />
      <x-input name="withholding_amount" label="Ritenuta d'acconto (€)" type="number" step="0.01" min="0" :value="$income->withholding_amount" />
      <x-input name="document_number" label="N. fattura / documento" :value="$income->document_number" maxlength="50" placeholder="Es. 12/2026" />
    </div>
    <datalist id="sources">
      @foreach ($sources as $s)
        <option value="{{ $s }}"></option>
      @endforeach
    </datalist>

    <x-input name="description" label="Descrizione" :value="$income->description" maxlength="255" placeholder="Es. Compenso ottobre" />

    <div>
      <label for="notes" class="label">Note</label>
      <textarea id="notes" name="notes" rows="3" maxlength="2000" @class(['input resize-y', 'input-error' => $errors->has('notes')])>{{ old('notes', $income->notes) }}</textarea>
      @error('notes')<p class="mt-1.5 text-[11px] text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="flex flex-wrap gap-2 pt-2">
      <button class="btn btn-primary" type="submit">{{ $income->exists ? 'Salva modifiche' : 'Salva entrata' }}</button>
      @unless ($income->exists)
        <button class="btn" type="submit" name="add_another" value="1">Salva e inserisci un'altra</button>
      @endunless
      <a class="btn" href="{{ route('incomes.index') }}">Annulla</a>
    </div>
  </form>
@endsection
