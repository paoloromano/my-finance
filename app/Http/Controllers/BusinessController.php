<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Support\FinanceSummary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessController extends Controller
{
    public function show(): View
    {
        return view('business', ['summary' => FinanceSummary::make()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ps' => ['required', 'numeric', 'min:0'],
            'abifin' => ['required', 'numeric', 'min:0'],
            'visio' => ['required', 'numeric', 'min:0'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'personal_transfer' => ['required', 'numeric', 'min:0'],
            'tax_reserve' => ['required', 'numeric'],
        ]);

        Setting::put($data);

        return back()->with('status', 'Impostazioni salvate');
    }
}
