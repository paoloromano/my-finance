<?php

namespace App\Support;

use App\Models\FamilyExpense;
use App\Models\Setting;

/**
 * Calcoli derivati dai parametri (stessa logica della versione JS).
 */
final class FinanceSummary
{
    public function __construct(
        public readonly array $settings,
        public readonly float $revenue,
        public readonly float $taxAccrual,
        public readonly float $marginAfterTransfer,
        public readonly float $familyCosts,
        public readonly float $familyMargin,
    ) {}

    public static function make(): self
    {
        $s = Setting::values();
        $revenue = $s['ps'] + $s['abifin'] + $s['visio'];
        $tax = $revenue * $s['tax_rate'] / 100;
        $familyCosts = (float) FamilyExpense::query()->sum('amount');

        return new self(
            settings: $s,
            revenue: $revenue,
            taxAccrual: $tax,
            marginAfterTransfer: $revenue - $tax - $s['personal_transfer'],
            familyCosts: $familyCosts,
            familyMargin: $s['family_income'] - $familyCosts,
        );
    }

    /** @return array<string, float> */
    public function revenueSources(): array
    {
        return [
            'PS Company' => $this->settings['ps'],
            'Abifin' => $this->settings['abifin'],
            'Visiospot' => $this->settings['visio'],
        ];
    }
}
