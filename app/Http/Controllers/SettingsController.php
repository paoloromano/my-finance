<?php

namespace App\Http\Controllers;

use App\Services\FinanceBackup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SettingsController extends Controller
{
    public function __construct(private readonly FinanceBackup $backup) {}

    public function show(): View
    {
        return view('settings');
    }

    public function export(): StreamedResponse
    {
        $json = json_encode($this->backup->export(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return response()->streamDownload(
            fn () => print ($json),
            'my-finance-backup-'.now()->format('Y-m-d').'.json',
            ['Content-Type' => 'application/json'],
        );
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'backup' => ['required', 'file', 'max:5120'],
        ], [], ['backup' => 'file di backup']);

        $data = json_decode((string) file_get_contents($request->file('backup')->getRealPath()), true);

        if (! is_array($data)) {
            return back()->withErrors(['backup' => 'Il file non è un JSON valido.']);
        }

        $this->backup->import($data);

        return back()->with('status', 'Backup importato');
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->validate(['confirm' => ['accepted']], ['confirm.accepted' => 'Conferma il ripristino spuntando la casella.']);

        $this->backup->resetToDefaults();

        return back()->with('status', 'Dati iniziali ripristinati');
    }
}
