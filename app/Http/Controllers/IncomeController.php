<?php

namespace App\Http\Controllers;

use App\Models\Income;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class IncomeController extends Controller
{
    public function index(Request $request): View
    {
        $years = Income::query()
            ->pluck('received_on')
            ->map(fn ($d) => (int) $d->format('Y'))
            ->push((int) now()->year)
            ->unique()
            ->sortDesc()
            ->values();

        $year = $request->filled('year') ? (int) $request->input('year') : (int) now()->year;
        if ($request->input('year') === 'all') {
            $year = null;
        }
        $category = $request->input('category');
        $source = $request->input('source');

        $filtered = Income::query()
            ->inYear($year)
            ->when($category, fn ($q) => $q->where('category', $category))
            ->when($source, fn ($q) => $q->where('source', $source));

        $incomes = (clone $filtered)
            ->orderByDesc('received_on')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $totals = (clone $filtered)
            ->selectRaw('COALESCE(SUM(gross_amount),0) as gross, COALESCE(SUM(withholding_amount),0) as withholding, COUNT(*) as n')
            ->first();

        $byCategory = (clone $filtered)
            ->selectRaw('category, SUM(gross_amount) as gross')
            ->groupBy('category')
            ->pluck('gross', 'category');

        $bySource = (clone $filtered)
            ->selectRaw('source, SUM(gross_amount) as gross, COUNT(*) as n')
            ->groupBy('source')
            ->orderByDesc('gross')
            ->get();

        return view('incomes.index', [
            'incomes' => $incomes,
            'years' => $years,
            'year' => $year,
            'category' => $category,
            'source' => $source,
            'sources' => $this->sources(),
            'totals' => $totals,
            'byCategory' => $byCategory,
            'bySource' => $bySource,
        ]);
    }

    public function create(): View
    {
        return view('incomes.form', [
            'income' => new Income(['received_on' => now(), 'category' => 'piva', 'withholding_amount' => 0]),
            'sources' => $this->sources(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Income::create($this->validated($request));

        $target = $request->boolean('add_another') ? route('incomes.create') : route('incomes.index');

        return redirect($target)->with('status', 'Entrata registrata');
    }

    public function edit(Income $income): View
    {
        return view('incomes.form', [
            'income' => $income,
            'sources' => $this->sources(),
        ]);
    }

    public function update(Request $request, Income $income): RedirectResponse
    {
        $income->update($this->validated($request));

        return redirect()->route('incomes.index')->with('status', 'Entrata aggiornata');
    }

    public function destroy(Income $income): RedirectResponse
    {
        $income->delete();

        return back()->with('status', 'Entrata eliminata');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'received_on' => ['required', 'date'],
            'category' => ['required', Rule::in(array_keys(Income::CATEGORIES))],
            'source' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'document_number' => ['nullable', 'string', 'max:50'],
            'gross_amount' => ['required', 'numeric', 'min:0.01', 'max:9999999999'],
            'withholding_amount' => ['nullable', 'numeric', 'min:0', 'lte:gross_amount'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ], [], [
            'received_on' => 'data incasso',
            'category' => 'tipologia',
            'source' => 'committente',
            'gross_amount' => 'importo lordo',
            'withholding_amount' => 'ritenuta',
        ]);

        $data['withholding_amount'] ??= 0;

        return $data;
    }

    private function sources(): array
    {
        return collect(Income::DEFAULT_SOURCES)
            ->merge(Income::query()->distinct()->orderBy('source')->pluck('source'))
            ->unique()
            ->values()
            ->all();
    }
}
