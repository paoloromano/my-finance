@extends('layouts.app')

@section('title', 'Modifica scadenza')

@section('actions')
  <a href="{{ route('deadlines.index') }}" class="btn">← Torna alle scadenze</a>
@endsection

@section('content')
  <form method="POST" action="{{ route('deadlines.update', $deadline) }}" class="card max-w-xl space-y-4">
    @csrf
    @method('PUT')
    @include('deadlines._fields', ['deadline' => $deadline, 'bag' => 'default'])
    <div class="flex gap-2 pt-2">
      <button type="submit" class="btn btn-primary">Salva modifiche</button>
      <a href="{{ route('deadlines.index') }}" class="btn">Annulla</a>
    </div>
  </form>
@endsection
