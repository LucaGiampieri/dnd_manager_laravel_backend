<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'name',
        'code',
        'value_in_copper_pieces',
        'sort_order',
        'coin_weight_kg',
        'is_common',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'value_in_copper_pieces' => 'integer',
            'sort_order' => 'integer',
            'coin_weight_kg' => 'decimal:4',
            'is_common' => 'boolean',
        ];
    }
}
