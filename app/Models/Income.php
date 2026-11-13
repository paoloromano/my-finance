<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    public const CATEGORIES = [
        'piva' => 'Partita IVA',
        'dipendente' => 'Lavoro dipendente',
        'collaborazione' => 'Collaborazione',
        'occasionale' => 'Prestazione occasionale',
        'altro' => 'Altro reddito da lavoro',
    ];

    public const DEFAULT_SOURCES = ['PS Company', 'Abifin', 'Visiospot'];

    protected $fillable = [
        'received_on',
        'category',
        'source',
        'description',
        'document_number',
        'gross_amount',
        'withholding_amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'received_on' => 'date',
            'gross_amount' => 'decimal:2',
            'withholding_amount' => 'decimal:2',
        ];
    }

    public function getNetAmountAttribute(): float
    {
        return (float) $this->gross_amount - (float) $this->withholding_amount;
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function scopeInYear(Builder $query, ?int $year): Builder
    {
        return $year ? $query->whereYear('received_on', $year) : $query;
    }
}
