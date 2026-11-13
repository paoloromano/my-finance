# MyFinance

Dashboard per la gestione delle finanze professionali, personali e familiari.
Laravel 13 + Tailwind CSS 4 (Vite), database SQLite.

URL di sviluppo: **http://my-finance.test** (Laravel Herd, cartella `wwwpaolo` parcheggiata).

## Sezioni

| Sezione | URL | Cosa fa |
|---|---|---|
| Panoramica | `/` | Indicatori su ricavi, accantonamento, margine; bilancio familiare; prossime scadenze; ultimi movimenti |
| Entrate lavoro | `/entrate-lavoro` | Data entry dei redditi da lavoro (P.IVA, dipendente, collaborazioni, occasionali, altro) con ritenute, filtri e totali |
| Partita IVA | `/partita-iva` | Parametri: ricavi medi per committente, aliquota di accantonamento, trasferimento personale, fondo fiscale |
| Movimenti | `/movimenti` | Registro entrate/uscite per area (P.IVA, personale, famiglia, fisco) |
| Famiglia | `/famiglia` | Entrate del conto comune e voci di spesa mensili |
| Scadenze | `/scadenze` | Rate e scadenze, con segna come pagata |
| Impostazioni | `/impostazioni` | Backup JSON (esporta/importa) e ripristino dei dati iniziali |

L'importazione accetta anche il file `my-business-backup.json` esportato dalla vecchia versione JS (localStorage).

## Accesso

Nessuna registrazione pubblica. Un solo utente amministratore, creato da terminale:

```bash
php artisan app:create-admin
```

Rilanciato con la stessa email aggiorna la password.

## Setup da zero

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed      # crea tabelle e carica parametri/scadenze/budget iniziali
php artisan app:create-admin
npm run build                   # oppure: npm run dev durante lo sviluppo
```

## Sviluppo

- `npm run dev` — Vite con hot reload (necessario per vedere subito le modifiche a classi Tailwind / CSS / JS)
- `npm run build` — build di produzione in `public/build` (non versionata)
- `php artisan test` — test

## File principali

- `app/Http/Controllers/` — un controller per sezione
- `app/Models/` — `Income`, `Movement`, `Deadline`, `FamilyExpense`, `Setting`
- `app/Support/FinanceDefaults.php` — valori iniziali
- `app/Support/FinanceSummary.php` — calcoli (ricavi, accantonamento, margini)
- `app/Services/FinanceBackup.php` — esporta / importa / ripristina
- `resources/views/` — layout, componenti (`components/`), pagine
- `resources/css/app.css` — Tailwind e classi base (`card`, `btn`, `input`…)

## Importante

Le simulazioni non sono una liquidazione fiscale; verificare saldo e acconti con il commercialista.
