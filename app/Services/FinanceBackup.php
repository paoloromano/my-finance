<?php

namespace App\Services;

use App\Models\Deadline;
use App\Models\FamilyExpense;
use App\Models\Income;
use App\Models\Movement;
use App\Models\Setting;
use App\Support\FinanceDefaults;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FinanceBackup
{
    public const VERSION = 2;

    public function export(): array
    {
        return [
            'app' => 'my-finance',
            'version' => self::VERSION,
            'exported_at' => now()->toIso8601String(),
            'settings' => Setting::values(),
            'movements' => Movement::query()->orderBy('occurred_on')->get()
                ->map(fn (Movement $m) => [
                    'occurred_on' => $m->occurred_on->toDateString(),
                    'description' => $m->description,
                    'amount' => (float) $m->amount,
                    'type' => $m->type,
                    'area' => $m->area,
                    'note' => $m->note,
                ])->all(),
            'deadlines' => Deadline::query()->orderBy('due_on')->get()
                ->map(fn (Deadline $d) => [
                    'due_on' => $d->due_on->toDateString(),
                    'label' => $d->label,
                    'amount' => (float) $d->amount,
                    'category' => $d->category,
                    'paid_on' => $d->paid_on?->toDateString(),
                ])->all(),
            'family_expenses' => FamilyExpense::query()->orderBy('position')->get()
                ->map(fn (FamilyExpense $e) => ['label' => $e->label, 'amount' => (float) $e->amount])->all(),
            'incomes' => Income::query()->orderBy('received_on')->get()
                ->map(fn (Income $i) => [
                    'received_on' => $i->received_on->toDateString(),
                    'category' => $i->category,
                    'source' => $i->source,
                    'description' => $i->description,
                    'document_number' => $i->document_number,
                    'gross_amount' => (float) $i->gross_amount,
                    'withholding_amount' => (float) $i->withholding_amount,
                    'notes' => $i->notes,
                ])->all(),
        ];
    }

    /**
     * Importa un backup sostituendo i dati presenti.
     * Accetta sia il formato attuale sia quello della vecchia versione JS (localStorage).
     */
    public function import(array $data): void
    {
        $normalized = $this->isLegacy($data) ? $this->fromLegacy($data) : $data;

        $this->ensureValid($normalized);

        DB::transaction(function () use ($normalized) {
            Setting::query()->delete();
            Movement::query()->delete();
            Deadline::query()->delete();
            FamilyExpense::query()->delete();

            Setting::put(Arr::get($normalized, 'settings', []));

            foreach (Arr::get($normalized, 'movements', []) as $m) {
                Movement::create(Arr::only($m, ['occurred_on', 'description', 'amount', 'type', 'area', 'note']));
            }
            foreach (Arr::get($normalized, 'deadlines', []) as $d) {
                Deadline::create(Arr::only($d, ['due_on', 'label', 'amount', 'category', 'paid_on']));
            }
            foreach (array_values(Arr::get($normalized, 'family_expenses', [])) as $i => $e) {
                FamilyExpense::create(['label' => $e['label'], 'amount' => $e['amount'] ?? 0, 'position' => $i]);
            }

            // Le entrate lavoro si sostituiscono solo se il backup le contiene (i backup JS non le hanno).
            if (array_key_exists('incomes', $normalized)) {
                Income::query()->delete();
                foreach ($normalized['incomes'] as $income) {
                    $row = Arr::only($income, [
                        'received_on', 'category', 'source', 'description', 'document_number',
                        'gross_amount', 'withholding_amount', 'notes',
                    ]);
                    $row['withholding_amount'] ??= 0;
                    Income::create($row);
                }
            }
        });
    }

    /**
     * Ripristina parametri, scadenze e budget iniziali ed elimina i movimenti.
     * Le entrate lavoro non vengono toccate.
     */
    public function resetToDefaults(): void
    {
        DB::transaction(function () {
            Setting::query()->delete();
            Movement::query()->delete();
            Deadline::query()->delete();
            FamilyExpense::query()->delete();

            Setting::put(FinanceDefaults::SETTINGS);
            foreach (FinanceDefaults::DEADLINES as $d) {
                Deadline::create($d);
            }
            foreach (FinanceDefaults::FAMILY_EXPENSES as $i => $e) {
                FamilyExpense::create($e + ['position' => $i]);
            }
        });
    }

    private function isLegacy(array $data): bool
    {
        return isset($data['familyExpenses']) || isset($data['settings']['taxRate']);
    }

    private function fromLegacy(array $data): array
    {
        $settings = [];
        foreach (FinanceDefaults::LEGACY_SETTING_KEYS as $legacy => $key) {
            if (isset($data['settings'][$legacy])) {
                $settings[$key] = $data['settings'][$legacy];
            }
        }

        return [
            'settings' => $settings,
            'movements' => array_map(fn ($m) => [
                'occurred_on' => $m['date'] ?? null,
                'description' => $m['description'] ?? '',
                'amount' => $m['amount'] ?? 0,
                'type' => $m['type'] ?? 'income',
                'area' => $m['area'] ?? 'business',
                'note' => ($m['note'] ?? '') ?: null,
            ], $data['movements'] ?? []),
            'deadlines' => array_map(fn ($d) => [
                'due_on' => $d['date'] ?? null,
                'label' => $d['label'] ?? '',
                'amount' => $d['amount'] ?? 0,
                'category' => $d['category'] ?? null,
                'paid_on' => null,
            ], $data['deadlines'] ?? []),
            'family_expenses' => array_map(fn ($e) => [
                'label' => $e['label'] ?? '',
                'amount' => $e['amount'] ?? 0,
            ], $data['familyExpenses'] ?? []),
        ];
    }

    private function ensureValid(array $data): void
    {
        $validator = validator($data, [
            'settings' => ['array'],
            'settings.*' => ['numeric'],
            'movements' => ['array'],
            'movements.*.occurred_on' => ['required', 'date'],
            'movements.*.description' => ['required', 'string', 'max:255'],
            'movements.*.amount' => ['required', 'numeric', 'min:0'],
            'movements.*.type' => ['required', 'in:'.implode(',', array_keys(Movement::TYPES))],
            'movements.*.area' => ['required', 'in:'.implode(',', array_keys(Movement::AREAS))],
            'deadlines' => ['array'],
            'deadlines.*.due_on' => ['required', 'date'],
            'deadlines.*.label' => ['required', 'string', 'max:255'],
            'deadlines.*.amount' => ['required', 'numeric'],
            'deadlines.*.paid_on' => ['nullable', 'date'],
            'family_expenses' => ['array'],
            'family_expenses.*.label' => ['required', 'string', 'max:255'],
            'family_expenses.*.amount' => ['nullable', 'numeric'],
            'incomes' => ['array'],
            'incomes.*.received_on' => ['required', 'date'],
            'incomes.*.category' => ['required', 'in:'.implode(',', array_keys(Income::CATEGORIES))],
            'incomes.*.source' => ['required', 'string', 'max:255'],
            'incomes.*.gross_amount' => ['required', 'numeric', 'min:0'],
            'incomes.*.withholding_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages([
                'backup' => 'File di backup non valido: '.$validator->errors()->first(),
            ]);
        }
    }
}
