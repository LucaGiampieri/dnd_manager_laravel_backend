<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    protected $fillable = [
        'name',
        'sort_order',
        'space_side_meters',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'space_side_meters' => 'decimal:3',
        ];
    }
}
