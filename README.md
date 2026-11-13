# MyBusiness

Dashboard locale per la gestione delle finanze professionali, personali e familiari.

## Avvio rapido su Windows

1. Estrai la cartella `my-business` in `C:\Users\utente\Documents\wwwpaolo\`.
2. Apri la cartella `my-business`.
3. Fai doppio clic su `index.html` oppure aprilo con Chrome/Edge.

Non serve installare Node.js, database o dipendenze. La prima apertura funziona offline, tranne il font esterno (con fallback locale).

## Funzioni incluse

- Dashboard con indicatori di ricavi, fondo fiscale e margine teorico.
- Gestione parametri PS Company, Abifin, Visiospot, percentuale fiscale e trasferimento personale.
- Registro di entrate e uscite.
- Budget familiare modificabile.
- Scadenze F24 personali inserite sulla base dei dati condivisi.
- Esportazione/importazione backup JSON.
- Persistenza locale tramite `localStorage`.

## Importante

I dati vengono conservati nel browser del dispositivo. Esporta periodicamente un backup dalla sezione Impostazioni. L'app non si collega a banche o servizi fiscali e non calcola il debito fiscale definitivo. Le percentuali e le scadenze sono strumenti di pianificazione da verificare con il commercialista.

## Struttura

- `index.html` — pagina principale
- `styles.css` — interfaccia e layout responsive
- `src/app.js` — logica, dati iniziali e persistenza
- `README.md` — istruzioni
