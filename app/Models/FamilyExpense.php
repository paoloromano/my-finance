<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyExpense extends Model
{
    protected $fillable = ['label', 'amount', 'position'];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }
}
