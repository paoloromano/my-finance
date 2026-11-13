<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deadline extends Model
{
    protected $fillable = ['due_on', 'label', 'amount', 'category', 'paid_on'];

    protected function casts(): array
    {
        return [
            'due_on' => 'date',
            'paid_on' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function isPaid(): bool
    {
        return $this->paid_on !== null;
    }

    public function isOverdue(): bool
    {
        return ! $this->isPaid() && $this->due_on->isBefore(today());
    }
}
