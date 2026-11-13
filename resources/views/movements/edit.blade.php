@extends('layouts.app')

@section('title', 'Modifica movimento')

@section('actions')
  <a href="{{ route('movements.index') }}" class="btn">← Torna ai movimenti</a>
@endsection

@section('content')
  <form method="POST" action="{{ route('movements.update', $movement) }}" class="card max-w-xl space-y-4">
    @csrf
    @method('PUT')
    @include('movements._fields', ['movement' => $movement, 'bag' => 'default'])
    <div class="flex gap-2 pt-2">
      <button type="submit" class="btn btn-primary">Salva modifiche</button>
      <a href="{{ route('movements.index') }}" class="btn">Annulla</a>
    </div>
  </form>
@endsection
