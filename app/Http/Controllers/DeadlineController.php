<?php

namespace App\Http\Controllers;

use App\Models\Deadline;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeadlineController extends Controller
{
    public function index(Request $request): View
    {
        $showPaid = $request->boolean('pagate');

        $deadlines = Deadline::query()
            ->when(! $showPaid, fn ($q) => $q->whereNull('paid_on'))
            ->orderBy('due_on')
            ->get();

        $open = Deadline::query()->whereNull('paid_on');

        return view('deadlines.index', [
            'deadlines' => $deadlines,
            'showPaid' => $showPaid,
            'openTotal' => (float) (clone $open)->sum('amount'),
            'openCount' => (clone $open)->count(),
            'overdueCount' => (clone $open)->whereDate('due_on', '<', today())->count(),
            'next30' => (float) (clone $open)->whereBetween('due_on', [today()->toDateString(), today()->addDays(30)->toDateString()])->sum('amount'),
            'categories' => Deadline::query()->whereNotNull('category')->distinct()->orderBy('category')->pluck('category'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Deadline::create($this->validated($request, 'deadline'));

        return back()->with('status', 'Scadenza aggiunta');
    }

    public function edit(Deadline $deadline): View
    {
        return view('deadlines.edit', [
            'deadline' => $deadline,
            'categories' => Deadline::query()->whereNotNull('category')->distinct()->orderBy('category')->pluck('category'),
        ]);
    }

    public function update(Request $request, Deadline $deadline): RedirectResponse
    {
        $deadline->update($this->validated($request));

        return redirect()->route('deadlines.index')->with('status', 'Scadenza aggiornata');
    }

    public function togglePaid(Deadline $deadline): RedirectResponse
    {
        $deadline->update(['paid_on' => $deadline->isPaid() ? null : today()]);

        return back()->with('status', $deadline->isPaid() ? 'Segnata come pagata' : 'Segnata come da pagare');
    }

    public function destroy(Deadline $deadline): RedirectResponse
    {
        $deadline->delete();

        return back()->with('status', 'Scadenza eliminata');
    }

    private function validated(Request $request, string $bag = 'default'): array
    {
        return $request->validateWithBag($bag, [
            'due_on' => ['required', 'date'],
            'label' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'category' => ['nullable', 'string', 'max:255'],
        ], [], [
            'due_on' => 'data scadenza',
            'label' => 'descrizione',
            'amount' => 'importo',
        ]);
    }
}
