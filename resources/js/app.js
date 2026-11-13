// Apertura/chiusura dei <dialog> tramite attributi data-*:
//   <button data-open-dialog="id">  apre il dialog con quell'id
//   <button data-close-dialog>      chiude il dialog che lo contiene
document.addEventListener('click', (event) => {
    const opener = event.target.closest('[data-open-dialog]');
    if (opener) {
        const dialog = document.getElementById(opener.dataset.openDialog);
        if (dialog) {
            event.preventDefault();
            dialog.showModal();
            dialog.querySelector('[autofocus]')?.focus();
        }
        return;
    }

    const closer = event.target.closest('[data-close-dialog]');
    if (closer) {
        event.preventDefault();
        closer.closest('dialog')?.close();
    }
});

// Conferma prima di inviare form distruttivi: <form data-confirm="Messaggio">
document.addEventListener('submit', (event) => {
    const message = event.target.dataset?.confirm;
    if (message && !window.confirm(message)) {
        event.preventDefault();
    }
});

// Riapre automaticamente un dialog dopo un errore di validazione: <dialog data-open-on-load>
document.querySelectorAll('dialog[data-open-on-load]').forEach((dialog) => dialog.showModal());
