<!doctype html>
<html lang="it" class="h-full bg-nav">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="theme-color" content="#101827">
  <meta name="robots" content="noindex,nofollow">
  <title>Accesso · MyFinance</title>
  @fonts
  @vite(['resources/css/app.css'])
</head>
<body class="grid h-full place-items-center p-4 font-sans text-slate-800 antialiased">
  <div class="card w-full max-w-sm p-7">
    <div class="mb-6 flex items-center gap-3">
      <div class="grid size-9 place-items-center rounded-xl bg-indigo-600 font-display text-xl font-extrabold text-white">M</div>
      <div>
        <div class="font-display text-base font-bold leading-tight">my-finance</div>
        <div class="text-[9px] tracking-[.15em] text-slate-500">AREA RISERVATA</div>
      </div>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
      @csrf
      @error('session')
        <p class="rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800">{{ $message }}</p>
      @enderror
      <x-input name="email" label="Email" type="email" required autofocus autocomplete="username" />
      <div>
        <label for="password" class="label">Password</label>
        <input id="password" name="password" type="password" required autocomplete="current-password" class="input">
      </div>
      <label class="flex items-center gap-2 text-xs text-slate-500">
        <input type="checkbox" name="remember" value="1" class="size-4 accent-indigo-600"> Ricordami
      </label>
      <button class="btn btn-primary w-full py-3" type="submit">Accedi</button>
    </form>
  </div>
</body>
</html>
