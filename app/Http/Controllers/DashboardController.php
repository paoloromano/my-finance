<?php

namespace App\Http\Controllers;

use App\Models\Deadline;
use App\Models\Income;
use App\Models\Movement;
use App\Support\FinanceSummary;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $year = (int) now()->year;

        return view('dashboard', [
            'summary' => FinanceSummary::make(),
            'latestMovements' => Movement::query()->latest('occurred_on')->latest('id')->limit(6)->get(),
            'nextDeadlines' => Deadline::query()->whereNull('paid_on')->orderBy('due_on')->limit(4)->get(),
            'year' => $year,
            'incomeYear' => (float) Income::query()->inYear($year)->sum('gross_amount'),
            'incomeYearPiva' => (float) Income::query()->inYear($year)->where('category', 'piva')->sum('gross_amount'),
        ]);
    }
}
