<?php

namespace App\Support;

/**
 * Valori iniziali (gli stessi della prima versione JS).
 */
final class FinanceDefaults
{
    public const SETTINGS = [
        'ps' => 1602,
        'abifin' => 1280,
        'visio' => 1770,
        'tax_rate' => 21,
        'personal_transfer' => 3000,
        'tax_reserve' => 6000,
        'family_income' => 2000,
    ];

    /** Chiavi usate nel backup JSON della versione JS (camelCase). */
    public const LEGACY_SETTING_KEYS = [
        'ps' => 'ps',
        'abifin' => 'abifin',
        'visio' => 'visio',
        'taxRate' => 'tax_rate',
        'personalTransfer' => 'personal_transfer',
        'taxReserve' => 'tax_reserve',
        'familyIncome' => 'family_income',
    ];

    public const DEADLINES = [
        ['due_on' => '2026-12-28', 'label' => 'F24 piano 2 · rata 1', 'amount' => 619.87, 'category' => 'F24 personale'],
        ['due_on' => '2027-02-01', 'label' => 'F24 piano 1 · rata 7', 'amount' => 648.16, 'category' => 'F24 personale'],
        ['due_on' => '2027-03-31', 'label' => 'F24 piano 2 · rata 2', 'amount' => 628.79, 'category' => 'F24 personale'],
        ['due_on' => '2027-04-30', 'label' => 'F24 piano 1 · rata 8', 'amount' => 653.38, 'category' => 'F24 personale'],
        ['due_on' => '2027-06-30', 'label' => 'F24 piano 2 · rata 3', 'amount' => 634.20, 'category' => 'F24 personale'],
        ['due_on' => '2027-08-20', 'label' => 'F24 piano 1 · rata 9', 'amount' => 658.79, 'category' => 'F24 personale'],
        ['due_on' => '2027-09-30', 'label' => 'F24 piano 2 · rata 4', 'amount' => 639.67, 'category' => 'F24 personale'],
        ['due_on' => '2027-11-02', 'label' => 'F24 piano 1 · rata 10', 'amount' => 664.19, 'category' => 'F24 personale'],
        ['due_on' => '2027-12-31', 'label' => 'F24 piano 2 · rata 5', 'amount' => 645.14, 'category' => 'F24 personale'],
    ];

    public const FAMILY_EXPENSES = [
        ['label' => 'Mutuo', 'amount' => 755],
        ['label' => 'Assicurazione casa', 'amount' => 60.83],
        ['label' => 'Internet', 'amount' => 22.99],
        ['label' => 'PAC Riccardo', 'amount' => 100],
        ['label' => 'Allarme', 'amount' => 5.58],
        ['label' => 'Alimentari e casa', 'amount' => 433.33],
        ['label' => 'Elettricità', 'amount' => 85],
        ['label' => 'AIMAG', 'amount' => 35],
        ['label' => 'Condominio', 'amount' => 190],
    ];
}
