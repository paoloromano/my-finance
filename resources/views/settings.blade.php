@extends('layouts.app')

@section('title', 'Impostazioni')

@section('content')
  <div class="grid items-start gap-4 lg:grid-cols-2">
    <div class="card">
      <x-section-head title="Backup dei dati" />
      <p class="text-xs text-slate-500">
        Esporta un file JSON con parametri, movimenti, scadenze, budget familiare ed entrate lavoro.
        Conservalo in un luogo sicuro.
      </p>
      <div class="mt-4">
        <a href="{{ route('settings.export') }}" class="btn btn-primary">⬇ Esporta backup</a>
      </div>

      <form method="POST" action="{{ route('settings.import') }}" enctype="multipart/form-data" class="mt-6 border-t border-slate-100 pt-5"
            data-confirm="L'importazione sostituisce i dati attuali. Continuare?">
        @csrf
        <label for="backup" class="label">Importa backup (.json)</label>
        <input id="backup" name="backup" type="file" accept="application/json,.json" required
               class="block w-full text-xs text-slate-600 file:mr-3 file:cursor-pointer file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100">
        @error('backup')<p class="mt-1.5 text-[11px] text-rose-600">{{ $message }}</p>@enderror
        <p class="mt-2 text-[11px] text-slate-500">
          Accetta anche il file <code>my-business-backup.json</code> esportato dalla vecchia versione nel browser.
          Sostituisce parametri, movimenti, scadenze e budget; le entrate lavoro vengono sostituite solo se presenti nel file.
        </p>
        <div class="mt-4"><button type="submit" class="btn">Importa</button></div>
      </form>
    </div>

    <div class="card">
      <x-section-head title="Ripristino dati iniziali" />
      <p class="text-xs text-slate-500">
        Riporta parametri, scadenze e budget familiare ai valori iniziali ed <b>elimina tutti i movimenti</b>.
        Le entrate lavoro non vengono toccate. Esporta prima un backup.
      </p>
      <form method="POST" action="{{ route('settings.reset') }}" class="mt-4" data-confirm="Ripristinare i dati iniziali? I movimenti registrati saranno eliminati.">
        @csrf
        <label class="flex items-center gap-2 text-xs text-slate-600">
          <input type="checkbox" name="confirm" value="1" class="size-4 accent-rose-600"> Ho capito, i movimenti verranno eliminati
        </label>
        @error('confirm')<p class="mt-1.5 text-[11px] text-rose-600">{{ $message }}</p>@enderror
        <div class="mt-4"><button type="submit" class="btn btn-danger">Ripristina dati iniziali</button></div>
      </form>
    </div>
  </div>

  <x-notice>L'applicazione non è collegata a Revolut, Generali, Fatture in Cloud o Agenzia delle Entrate. I dati sono salvati nel database locale dell'app.</x-notice>
@endsection
