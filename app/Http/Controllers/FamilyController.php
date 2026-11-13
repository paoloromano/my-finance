<?php

namespace App\Http\Controllers;

use App\Models\FamilyExpense;
use App\Models\Setting;
use App\Support\FinanceSummary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FamilyController extends Controller
{
    public function show(): View
    {
        return view('family', [
            'summary' => FinanceSummary::make(),
            'expenses' => FamilyExpense::query()->orderBy('position')->orderBy('id')->get(),
        ]);
    }

    public function updateIncome(Request $request): RedirectResponse
    {
        $data = $request->validate(['family_income' => ['required', 'numeric', 'min:0']]);

        Setting::put($data);

        return back()->with('status', 'Entrate salvate');
    }

    public function updateExpenses(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'expenses' => ['array'],
            'expenses.*.label' => ['required', 'string', 'max:255'],
            'expenses.*.amount' => ['required', 'numeric', 'min:0'],
        ], [], ['expenses.*.label' => 'voce', 'expenses.*.amount' => 'importo']);

        foreach ($data['expenses'] ?? [] as $id => $row) {
            FamilyExpense::query()->whereKey($id)->update([
                'label' => $row['label'],
                'amount' => $row['amount'],
            ]);
        }

        return back()->with('status', 'Spese salvate');
    }

    public function storeExpense(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
        ], [], ['label' => 'voce', 'amount' => 'importo']);

        FamilyExpense::create($data + ['position' => (int) FamilyExpense::query()->max('position') + 1]);

        return back()->with('status', 'Voce aggiunta');
    }

    public function destroyExpense(FamilyExpense $expense): RedirectResponse
    {
        $expense->delete();

        return back()->with('status', 'Voce eliminata');
    }
}
