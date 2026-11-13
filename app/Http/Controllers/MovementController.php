<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MovementController extends Controller
{
    public function index(Request $request): View
    {
        $area = $request->input('area');
        $type = $request->input('type');

        $filtered = Movement::query()
            ->when(array_key_exists((string) $area, Movement::AREAS), fn ($q) => $q->where('area', $area))
            ->when(array_key_exists((string) $type, Movement::TYPES), fn ($q) => $q->where('type', $type));

        $income = (float) (clone $filtered)->where('type', 'income')->sum('amount');
        $expense = (float) (clone $filtered)->where('type', 'expense')->sum('amount');

        return view('movements.index', [
            'movements' => (clone $filtered)->latest('occurred_on')->latest('id')->paginate(30)->withQueryString(),
            'income' => $income,
            'expense' => $expense,
            'area' => $area,
            'type' => $type,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Movement::create($this->validated($request, 'movement'));

        return back()->with('status', 'Movimento registrato');
    }

    public function edit(Movement $movement): View
    {
        return view('movements.edit', ['movement' => $movement]);
    }

    public function update(Request $request, Movement $movement): RedirectResponse
    {
        $movement->update($this->validated($request));

        return redirect()->route('movements.index')->with('status', 'Movimento aggiornato');
    }

    public function destroy(Movement $movement): RedirectResponse
    {
        $movement->delete();

        return back()->with('status', 'Movimento eliminato');
    }

    private function validated(Request $request, string $bag = 'default'): array
    {
        return $request->validateWithBag($bag, [
            'occurred_on' => ['required', 'date'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'type' => ['required', Rule::in(array_keys(Movement::TYPES))],
            'area' => ['required', Rule::in(array_keys(Movement::AREAS))],
            'note' => ['nullable', 'string', 'max:255'],
        ], [], [
            'occurred_on' => 'data',
            'description' => 'descrizione',
            'amount' => 'importo',
        ]);
    }
}
