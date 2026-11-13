<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    public const TYPES = [
        'income' => 'Entrata',
        'expense' => 'Uscita',
    ];

    public const AREAS = [
        'business' => 'Partita IVA',
        'personal' => 'Personale',
        'family' => 'Famiglia',
        'tax' => 'Fisco & INPS',
    ];

    protected $fillable = ['occurred_on', 'description', 'amount', 'type', 'area', 'note'];

    protected function casts(): array
    {
        return [
            'occurred_on' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function isIncome(): bool
    {
        return $this->type === 'income';
    }

    public function getAreaLabelAttribute(): string
    {
        return self::AREAS[$this->area] ?? $this->area;
    }
}
